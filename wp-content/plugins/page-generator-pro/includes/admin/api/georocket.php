<?php
/**
 * GeoRocket API Class.
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Fetch location data for Keywords from georocket.net
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 1.7.8
 */
class Page_Generator_Pro_Georocket extends Page_Generator_Pro_API {

	/**
	 * Holds the API endpoint
	 *
	 * @since   1.7.8
	 *
	 * @var     string
	 */
	public $api_endpoint = 'https://www.wpzinc.com';

	/**
	 * Holds the flag determining if the request data should be encoded
	 * into a JSON string
	 *
	 * If false, data is encoded using http_build_query()
	 *
	 * @since   2.8.9
	 *
	 * @var     bool
	 */
	public $is_json_request = false;

	/**
	 * Returns Countries
	 *
	 * @since   1.7.8
	 *
	 * @param   array $args   Georocket API compatible arguments, including license_key.
	 * @return  mixed           WP_Error | array
	 */
	public function get_countries( $args ) {

		return $this->response(
			$this->post(
				'?georocket_api=1',
				array(
					'endpoint' => 'countries',
					'params'   => $this->sanitize_arguments( $args ),
				)
			)
		);

	}

	/**
	 * Returns Regions
	 *
	 * @since   1.7.8
	 *
	 * @param   array $args   Georocket API compatible arguments, including license_key.
	 * @return  mixed           WP_Error | array
	 */
	public function get_regions( $args ) {

		return $this->response(
			$this->post(
				'?georocket_api=1',
				array(
					'endpoint' => 'regions',
					'params'   => $this->sanitize_arguments( $args ),
				)
			)
		);

	}

	/**
	 * Returns Counties
	 *
	 * @since   1.7.8
	 *
	 * @param   array $args   Georocket API compatible arguments, including license_key.
	 * @return  mixed           WP_Error | array
	 */
	public function get_counties( $args ) {

		return $this->response(
			$this->post(
				'?georocket_api=1',
				array(
					'endpoint' => 'counties',
					'params'   => $this->sanitize_arguments( $args ),
				)
			)
		);

	}

	/**
	 * Returns Cities
	 *
	 * @since   1.7.8
	 *
	 * @param   array $args   Georocket API compatible arguments, including license_key.
	 * @return  mixed           WP_Error | array
	 */
	public function get_cities( $args ) {

		return $this->response(
			$this->post(
				'?georocket_api=1',
				array(
					'endpoint' => 'cities',
					'params'   => $this->sanitize_arguments( $args ),
				)
			)
		);

	}

	/**
	 * Returns ZIP Codes
	 *
	 * @since   1.7.8
	 *
	 * @param   array $args   Georocket API compatible arguments, including license_key.
	 * @return  mixed           WP_Error | array
	 */
	public function get_zipcodes( $args ) {

		return $this->response(
			$this->post(
				'?georocket_api=1',
				array(
					'endpoint' => 'zipcodes',
					'params'   => $this->sanitize_arguments( $args ),
				)
			)
		);

	}

	/**
	 * Returns Zipcode Districts
	 *
	 * @since   2.2.0
	 *
	 * @param   array $args   Georocket API compatible arguments, including license_key.
	 * @return  mixed           WP_Error | array
	 */
	public function get_zipcode_districts( $args ) {

		// Remove any arguments that are false.
		$args = $this->sanitize_arguments( $args );

		// Call API and return results.
		return $this->response(
			$this->post(
				'?georocket_api=1',
				array(
					'endpoint' => 'zipcode_districts',
					'params'   => $this->sanitize_arguments( $args ),
				)
			)
		);

	}

	/**
	 * Returns Street Names
	 *
	 * @since   2.2.0
	 *
	 * @param   array $args   Georocket API compatible arguments, including license_key.
	 * @return  mixed           WP_Error | array
	 */
	public function get_street_names( $args ) {

		return $this->response(
			$this->post(
				'?georocket_api=1',
				array(
					'endpoint' => 'street_names',
					'params'   => $this->sanitize_arguments( $args ),
				)
			)
		);

	}

	/**
	 * Returns a Latitude and Longitude for the given Location
	 *
	 * @since   1.7.8
	 *
	 * @param   string $location       Location.
	 * @param   string $license_key    Plugin License Key.
	 * @return  mixed                   WP_Error | array
	 */
	public function get_geocode( $location, $license_key ) {

		return $this->response(
			$this->post(
				'?georocket_api=1',
				array(
					'endpoint' => 'geocode',
					'params'   => $this->sanitize_arguments(
						array(
							'license_key' => $license_key,
							'location'    => $location,
						)
					),
				)
			)
		);

	}

	/**
	 * Inspects the response from the API call, returning an error
	 * or data
	 *
	 * @since   2.8.9
	 *
	 * @param   mixed $response   Response (WP_Error | object).
	 * @return  mixed               WP_Error | object
	 */
	private function response( $response ) {

		// If the response is an error, return it.
		if ( is_wp_error( $response ) ) {
			return new WP_Error(
				'page_generator_pro_georocket_error',
				sprintf(
					/* translators: Error message */
					__( 'GeoRocket: %s', 'page-generator-pro' ),
					$response->get_error_message()
				)
			);
		}

		// If the response's success flag is false, return the data as an error.
		if ( ! $response->success ) {
			return new WP_Error(
				'page_generator_pro_georocket_error',
				sprintf(
					/* translators: Error message */
					__( 'GeoRocket: %s', 'page-generator-pro' ),
					$response->data
				)
			);
		}

		// Return successful response data.
		return $response->data;

	}

}
