<?php
/**
 * Airtable API Class.
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Read data from an airtable.com base, to use in a Keyword.
 *
 * @package Page_Generator_Pro
 * @author  Tim Carr
 * @version 3.3.4
 */
class Page_Generator_Pro_Airtable extends Page_Generator_Pro_API {

	/**
	 * Holds the API endpoint
	 *
	 * @since   3.3.4
	 *
	 * @var     string
	 */
	public $api_endpoint = 'https://api.airtable.com/v0';

	/**
	 * Holds the flag determining if the request data should be encoded
	 * into a JSON string
	 *
	 * If false, data is encoded using http_build_query()
	 *
	 * @since   3.3.4
	 *
	 * @var     bool
	 */
	public $is_json_request = false;

	/**
	 * Returns all data from the given Base ID and Table Name
	 *
	 * @since   3.3.4
	 *
	 * @param   string $api_key        API Key.
	 * @param   string $base_id        Base ID.
	 * @param   string $table_name     Table Name .
	 * @param   mixed  $offset         Offset (if specified, fetches paginated records).
	 * @return  mixed                   WP_Error | array
	 */
	public function get_data( $api_key, $base_id, $table_name, $offset = false ) {

		// Get first page of results.
		$results = $this->get_data_offset( $api_key, $base_id, $table_name, $offset );

		// If an error occured, bail.
		if ( is_wp_error( $results ) ) {
			return $results;
		}

		// If no additional results i.e. less than 100 rows, return data now.
		if ( ! $results['offset'] ) {
			// Remove offset flag and return results.
			unset( $results['offset'] );
			return $results;
		}

		// Recurse through offsets until all data fetched.
		while ( $results['offset'] ) {
			// Store last query's results in a separate variable.
			$old_results = $results;

			// Get next page of results.
			$results = $this->get_data_offset( $api_key, $base_id, $table_name, $results['offset'] );

			// Merge old results data with new results.
			$results['data'] = array_merge( $old_results['data'], $results['data'] );
		}

		// Remove offset flag and return results.
		unset( $results['offset'] );
		return $results;

	}

	/**
	 * Returns a subset of 100 rows from the given Base ID and Table Name, based on
	 * the supplied offset.
	 *
	 * If no offset is supplied, the first 100 rows are returned.
	 *
	 * The limit on the number of rows is determined by Airtable.
	 *
	 * @since   3.3.4
	 *
	 * @param   string $api_key        API Key.
	 * @param   string $base_id        Base ID.
	 * @param   string $table_name     Table Name.
	 * @param   mixed  $offset         Offset (if specified, fetches paginated records).
	 * @return  mixed                   WP_Error | array
	 */
	private function get_data_offset( $api_key, $base_id, $table_name, $offset = false ) {

		// Set API Key.
		$this->set_api_key( $api_key );

		// Set Headers.
		$this->set_headers(
			array(
				'Authorization' => 'Bearer ' . $this->api_key,
			)
		);

		// Define URL.
		$url = $base_id . '/' . rawurlencode( $table_name );
		if ( $offset !== false ) {
			$url .= '?offset=' . $offset;
		}

		// Send Request.
		$response = $this->response(
			$this->get( $url )
		);

		// Return if the response is an error.
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// Build columns first, because a row only includes fields
		// that contain values.  Iterating through guarantees we get
		// all possible columns.
		foreach ( $response->records as $index => $record ) {
			foreach ( $record->fields as $field => $value ) {
				$columns[ $field ] = $field;
			}

			// Sort columns by name, to match the data.
			ksort( $columns );
		}

		// Iterate through records, building Terms that are compliant with Keywords.
		foreach ( $response->records as $index => $record ) {
			// Build blank array comprising of all possible columns.
			$data[ $index ] = array();
			foreach ( $columns as $column ) {
				$data[ $index ][ $column ] = '';
			}

			// Iterate through this row's columns (fields).
			foreach ( $record->fields as $field => $value ) {
				// If the value contains newlines, replace them with <br />, as Airtable
				// allows for long text / multiline text input on a single cell.
				if ( strpos( $value, "\n" ) !== false ) {
					$value = str_replace( "\n", '<br />', $value );
				}

				// Build row.
				$data[ $index ][ $field ] = $value;
			}

			// Sort row columns by name, so that the data order is always the same.
			ksort( $data[ $index ] );

			// If more than one column exists, implode the data and encapsulate in quotation marks.
			if ( count( $columns ) > 1 ) {
				$data[ $index ] = '"' . implode( '","', $data[ $index ] ) . '"';
			} else {
				$data[ $index ] = implode( ',', $data[ $index ] );
			}
		}

		// Return.
		return array(
			'delimiter' => ( count( $columns ) > 1 ? ',' : '' ),
			'columns'   => ( count( $columns ) > 1 ? $columns : '' ),
			'data'      => $data,
			'offset'    => ( isset( $response->offset ) ? $response->offset : false ),
		);

	}

	/**
	 * Inspects the response from the API call, returning an error
	 * or data
	 *
	 * @since   3.3.4
	 *
	 * @param   mixed $response   Response (WP_Error | object).
	 * @return  mixed               WP_Error | object
	 */
	private function response( $response ) {

		// If the response is an error, return it.
		if ( is_wp_error( $response ) ) {
			return new WP_Error(
				'page_generator_pro_airtable_error',
				sprintf(
					/* translators: Error message */
					__( 'Airtable: %s', 'page-generator-pro' ),
					$response->get_error_message()
				)
			);
		}

		// Return an error if an error is present in the response.
		if ( isset( $response->error ) ) {
			// Depending on the error, we might be able to return a more helpful error message.
			if ( is_string( $response->error ) ) {
				switch ( $response->error ) {
					case 'NOT_FOUND':
						$message = __( 'Base ID or Table Name not found.', 'page-generator-pro' );
						break;
					default:
						$message = $response->error;
						break;
				}
			} else {
				$message = $response->error->message;
			}

			// Return error object.
			return new WP_Error(
				'page_generator_pro_airtable_error',
				sprintf(
					/* translators: Error message */
					__( 'Airtable: %s', 'page-generator-pro' ),
					$message
				)
			);
		}

		// Return.
		return $response;

	}

}
