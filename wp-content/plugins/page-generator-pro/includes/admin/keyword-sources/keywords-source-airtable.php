<?php
/**
 * Airtable Keyword Source Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Registers Airtable as a Keyword source, enabling Airtable data to be used
 * for a Keyword.
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 3.3.4
 */
class Page_Generator_Pro_Keywords_Source_Airtable {

	/**
	 * Holds the base object.
	 *
	 * @since   3.3.4
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Constructor
	 *
	 * @since   3.3.4
	 *
	 * @param   object $base    Base Plugin Class.
	 */
	public function __construct( $base ) {

		// Store base class.
		$this->base = $base;

		// Register this Keyword Source.
		add_filter( 'page_generator_pro_keywords_register_sources', array( $this, 'register' ) );

		// Define parameters for the Keyword before saving.
		add_filter( 'page_generator_pro_keywords_save_airtable', array( $this, 'save' ) );

		// Refresh Keyword Terms before starting generation.
		add_filter( 'page_generator_pro_keywords_refresh_terms_airtable', array( $this, 'refresh_terms' ), 10, 2 );

	}

	/**
	 * Returns the programmatic name of the source
	 *
	 * @since   3.3.4
	 *
	 * @return  string
	 */
	public function get_name() {

		return 'airtable';

	}

	/**
	 * Returns the label of the source
	 *
	 * @since   3.3.4
	 *
	 * @return  string
	 */
	public function get_label() {

		return __( 'Airtable', 'page-generator-pro' );

	}

	/**
	 * Registers this Source with the Keyword Sources system, so it's available
	 * to Keywords
	 *
	 * @since   3.3.4
	 *
	 * @param   array $sources    Sources.
	 * @return  array               Sources
	 */
	public function register( $sources ) {

		// Don't register this source if no API Key has been specified in the Integration Settings .
		if ( ! $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-integrations', 'airtable_api_key', false ) ) {
			return $sources;
		}

		// Add Source.
		return array_merge(
			$sources,
			array(
				$this->get_name() => array(
					'name'    => $this->get_name(),
					'label'   => $this->get_label(),
					'options' => array(
						'base_id'    => array(
							'type'        => 'text',
							'label'       => __( 'Base ID', 'page-generator-pro' ),
							'description' =>
								sprintf(
									/* translators: Documentation URL */
									__( 'The base ID (spreadsheet) to use from Airtable. %s for instructions on fetching your Base ID', 'page-generator-pro' ),
									'<a href="' . $this->base->plugin->documentation_url . '/keywords/#adding---editing-keywords--source--airtable" target="_blank" rel="noopener">' . __( 'Click here', 'page-generator-pro' ) . '</a>'
								),
						),
						'table_name' => array(
							'type'        => 'text',
							'label'       => __( 'Table Name', 'page-generator-pro' ),
							'description' => sprintf(
								/* translators: Documentation URL */
								__( 'The table name within the above Base to use as the Keyword Data. %s for instructions on fetching your Table Name', 'page-generator-pro' ),
								'<a href="' . $this->base->plugin->documentation_url . '/keywords/#adding---editing-keywords--source--airtable" target="_blank" rel="noopener">' . __( 'Click here', 'page-generator-pro' ) . '</a>'
							),
						),
						'preview'    => array(
							'type'  => 'preview',
							'label' => __( 'Terms', 'page-generator-pro' ),
						),
					),
				),
			)
		);

	}

	/**
	 * Prepares Keyword Data for this Source, based on the supplied form data,
	 * immediately before it's saved to the Keywords table in the database
	 *
	 * @since   3.3.4
	 *
	 * @param   array $keyword        Keyword Parameters.
	 * @return  mixed                   WP_Error | Keyword Parameters
	 */
	public function save( $keyword ) {

		// Get Keyword Terms.
		$result = $this->get( $keyword );

		// Bail if an error occured.
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		// Merge delimiter, columns and data with Keyword.
		$keyword = array_merge(
			$keyword,
			array(
				'delimiter' => $result['delimiter'],
				'columns'   => ( is_array( $result['columns'] ) ? implode( ',', $result['columns'] ) : '' ),
				'data'      => implode( "\n", $result['data'] ),
			)
		);

		return $keyword;

	}

	/**
	 * Refresh the given Keyword's Columns and Terms by fetching them from Airtable
	 * immediately before starting generation.
	 *
	 * @since   3.3.4
	 *
	 * @param   string $terms      Terms.
	 * @param   array  $keyword    Keyword.
	 * @return  mixed               WP_Error | array (delimiter,columns,data)
	 */
	public function refresh_terms( $terms, $keyword ) { // phpcs:ignore

		return $this->get( $keyword );

	}

	/**
	 * Fetches Terms from the Airtable Base's Table, based on the Keyword settings
	 *
	 * @since   3.3.4
	 *
	 * @param   array $keyword    Keyword.
	 * @return  mixed               WP_Error | array (delimiter,columns,data)
	 */
	private function get( $keyword ) {

		// Fail if no API Key has been specified in the Integration Settings .
		if ( ! $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-integrations', 'airtable_api_key', false ) ) {
			return new WP_Error(
				'page_generator_pro_keywords_source_airtable_get',
				__( 'You must specify an API Key at Settings > Integrations before using the Airtable integration.', 'page-generator-pro' )
			);
		}

		// Get data.
		return $this->base->get_class( 'airtable' )->get_data(
			$this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-integrations', 'airtable_api_key' ),
			$keyword['options']['base_id'],
			$keyword['options']['table_name']
		);

	}

}
