<?php
/**
 * Groups Directory Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Provides a UI for creating multiple Content Groups and Keywords
 * in a directory structure, such as County > City > Service.
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 3.2.9
 */
class Page_Generator_Pro_Groups_Directory {

	/**
	 * Holds the base object.
	 *
	 * @since   3.2.9
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * The current step
	 *
	 * @var     int
	 */
	public $step = 1;

	/**
	 * The current configuration
	 *
	 * @var     array
	 */
	public $configuration = array();

	/**
	 * Constructor.
	 *
	 * @since   3.2.9
	 *
	 * @param   object $base    Base Plugin Class.
	 */
	public function __construct( $base ) {

		// Store base class.
		$this->base = $base;

		add_action( 'admin_init', array( $this, 'maybe_load' ) );

	}

	/**
	 * Loads the Groups Directory screen if the request URL is for this class
	 *
	 * @since   3.2.9
	 */
	public function maybe_load() {

		// Bail if this isn't a request for the Groups Directory screen.
		if ( ! $this->is_groups_directory_request() ) {
			return;
		}

		// Define current screen.
		set_current_screen( $this->base->plugin->name . '-groups-directory' );

		// Process posted form data.
		$result = $this->process_form();

		// If an error occured in processing, show it on screen.
		if ( is_wp_error( $result ) ) {
			$this->base->get_class( 'notices' )->add_error_notice( $result->get_error_message() );
		}

		// Output custom HTML for the Groups Directory screen.
		$this->output_header();
		$this->output_content();
		$this->output_footer();
		exit;

	}

	/**
	 * Process posted form data, if any exists
	 *
	 * @since   3.2.9
	 *
	 * @return  mixed   WP_Error | bool
	 */
	private function process_form() {

		// Assume we're on the current step.
		$this->step = ( isset( $_REQUEST['step'] ) ? absint( $_REQUEST['step'] ) : 1 ); // phpcs:ignore

		// Decode the current configuration.
		$this->configuration = ( isset( $_REQUEST['configuration'] ) ? json_decode( stripslashes( $_POST['configuration'] ), true ) : array( // phpcs:ignore
			'structure'       => 'county_city_service',
			'service_keyword' => '',
			'services'        => '',
			'method'          => 'radius',
			'radius'          => 10,
			'region_id'       => array(), // Region IDs.
			'county_id'       => array(), // County IDs.
			'zipcode'         => '',
			'country_code'    => $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-general', 'country_code', 'US' ),
		) );

		// Run security checks.
		if ( ! isset( $_POST[ $this->base->plugin->name . '_nonce' ] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_POST[ $this->base->plugin->name . '_nonce' ], 'page-generator-pro' ) ) {
			return new WP_Error( 'page_generator_pro_groups_directory_process_form', __( 'Invalid nonce specified.', 'page-generator-pro' ) );
		}

		// Depending on the step we're on, check the form data.
		switch ( $this->step ) {
			/**
			 * Setup
			 */
			case 1:
				// Add to configuration.
				$this->configuration = array_merge(
					$this->configuration,
					array(
						'structure'           => sanitize_text_field( $_POST['structure'] ),
						'service_keyword'     => sanitize_text_field( $_POST['service_keyword'] ),
						'service_keyword_id'  => '',
						'services'            => $_POST['services'],
						'location_keyword'    => '',
						'location_keyword_id' => '',
						'method'              => sanitize_text_field( $_POST['method'] ),
						'radius'              => sanitize_text_field( $_POST['radius'] ),
						'region_id'           => ( isset( $_POST['region_id'] ) ? $_POST['region_id'] : false ),
						'county_id'           => ( isset( $_POST['county_id'] ) ? $_POST['county_id'] : false ),
						'zipcode'             => sanitize_text_field( $_POST['zipcode'] ),
						'country_code'        => sanitize_text_field( $_POST['country_code'] ),
					)
				);

				// Check required fields are completed.
				if ( empty( $this->configuration['service_keyword'] ) && empty( $this->configuration['services'] ) ) {
					return new WP_Error(
						'page_generator_pro_groups_directory_process_form_error',
						__( 'A service keyword must be chosen, or service terms entered', 'page-generator-pro' )
					);
				}
				if ( $this->configuration['method'] === 'radius' ) {
					if ( empty( $this->configuration['radius'] ) ) {
						return new WP_Error(
							'page_generator_pro_groups_directory_process_form_error',
							__( 'The radius field is required', 'page-generator-pro' )
						);
					}
					if ( empty( $this->configuration['zipcode'] ) ) {
						return new WP_Error(
							'page_generator_pro_groups_directory_process_form_error',
							__( 'The ZIP Code field is required', 'page-generator-pro' )
						);
					}
				}

				// Setup Location Keyword.
				$result = $this->setup_location_keyword();

				// Bail if an error occured.
				if ( is_wp_error( $result ) ) {
					return $result;
				}

				$this->configuration['location_keyword']    = $result['keyword'];
				$this->configuration['location_keyword_id'] = $result['id'];

				// Setup Service Keyword.
				if ( ! $this->configuration['service_keyword'] ) {
					$result = $this->setup_service_keyword();

					// Bail if an error occured.
					if ( is_wp_error( $result ) ) {
						return $result;
					}

					$this->configuration['service_keyword']    = $result['keyword'];
					$this->configuration['service_keyword_id'] = $result['id'];
				} else {
					// Get Keyword ID.
					$keyword                                   = $this->base->get_class( 'keywords' )->get_by( 'keyword', $this->configuration['service_keyword'] );
					$this->configuration['service_keyword_id'] = $keyword['keywordID'];
				}

				// Setup Content Groups.
				$content_group_ids = $this->setup_content_groups();

				// Bail if an error occured.
				if ( is_wp_error( $content_group_ids ) ) {
					return $content_group_ids;
				}

				$this->configuration['content_group_ids'] = $content_group_ids;
				break;
		}

		// If here, form validation/processing was successful.
		// Increment the step so that the next section is displayed.
		$this->step++;

	}

	/**
	 * Outputs the <head> and opening <body> tag for the standalone Groups Directory screen
	 *
	 * @since   3.2.9
	 */
	private function output_header() {

		// Remove scripts.
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );

		// Enqueue scripts.
		do_action( 'admin_enqueue_scripts' );

		// Load header view.
		include_once $this->base->plugin->folder . '/views/admin/groups-directory/header.php';

	}

	/**
	 * Outputs the HTML for the <body> section for the standalone Groups Directory screen,
	 * and defines any form option data that might be needed.
	 *
	 * @since   3.2.9
	 */
	private function output_content() {

		// Load form data.
		switch ( $this->step ) {
			/**
			 * Setup
			 */
			case 1:
				$structures        = $this->get_structures();
				$keywords          = $this->base->get_class( 'keywords' )->get_keywords_names();
				$countries         = $this->base->get_class( 'common' )->get_countries();
				$back_button_url   = 'edit.php?post_type=page-generator-pro';
				$back_button_label = __( 'Cancel', 'page-generator-pro' );
				$next_button_label = __( 'Create Keywords and Content Groups', 'page-generator-pro' );
				break;

			/**
			 * Done
			 */
			case 2:
				// Define UI.
				$back_button_url   = 'edit.php?post_type=page-generator-pro';
				$back_button_label = __( 'Finish', 'page-generator-pro' );
				break;
		}

		// Load content view.
		include_once $this->base->plugin->folder . '/views/admin/groups-directory/content.php';

	}

	/**
	 * Outputs the closing </body> and </html> tags, and runs some WordPress actions, for the standalone Groups Directory screen
	 *
	 * @since   3.2.9
	 */
	private function output_footer() {

		do_action( 'admin_footer', '' );
		do_action( 'admin_print_footer_scripts' );

		// Load footer view.
		include_once $this->base->plugin->folder . '/views/admin/groups-directory/footer.php';

	}

	/**
	 * Determines if the request is for the Groups Directory screen
	 *
	 * @since   3.2.9
	 *
	 * @return  bool    Is Groups Directory Request
	 */
	private function is_groups_directory_request() {

		// Don't load if this is an AJAX call.
		if ( wp_doing_ajax() || wp_doing_cron() ) {
			return false;
		}

		// Bail if we're not on the Groups Directory screen.
		if ( ! isset( $_GET['page'] ) ) { // phpcs:ignore
			return false;
		}
		if ( sanitize_text_field( $_GET['page'] ) !== $this->base->plugin->name . '-groups-directory' ) { // phpcs:ignore
			return false;
		}

		return true;

	}

	/**
	 * Returns directory structures supported the Groups Directory functionality
	 *
	 * @since   3.3.0
	 *
	 * @return  array   Supported Structures
	 */
	private function get_structures() {

		return array(
			'region_county_city_service' => array(
				'title'       => __( 'Region > County > City > Service', 'page-generator-pro' ),
				'description' => __( 'Services cover multiple States/Regions', 'page-generator-pro' ),
			),
			'county_city_service'        => array(
				'title'       => __( 'County > City > Service', 'page-generator-pro' ),
				'description' => __( 'Services cover multiple Counties within a single State/Region (or Region Pages are not required)', 'page-generator-pro' ),
			),
			'city_service'               => array(
				'title'       => __( 'City > Service', 'page-generator-pro' ),
				'description' => __( 'Services cover multiple Cities within a single County (or County Pages are not required)', 'page-generator-pro' ),
			),
			'service_city'               => array(
				'title'       => __( 'Service > City', 'page-generator-pro' ),
				'description' => __( 'Cities cover multiple Services within a single County (or County Pages are not required)', 'page-generator-pro' ),
			),
		);

	}

	/**
	 * Creates the Service Keyword based on the supplied configuration
	 *
	 * @since   3.2.9
	 *
	 * @return  mixed   WP_Error | array
	 */
	private function setup_service_keyword() {

		// Get unique keyword name that can be used.
		$keyword = $this->base->get_class( 'keywords' )->get_unique_name( 'service' );

		// Create keyword.
		$result = $this->base->get_class( 'keywords' )->save(
			array(
				'keyword' => $keyword,
				'data'    => $this->configuration['services'],
			)
		);

		// Bail if an error occured.
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		// Return ID and Keyword Name.
		return array(
			'id'      => $result,
			'keyword' => $keyword,
		);

	}

	/**
	 * Creates the Location Keyword based on the supplied configuration
	 *
	 * @since   3.2.9
	 *
	 * @return  mixed   WP_Error | array
	 */
	private function setup_location_keyword() {

		// Define columns.
		$columns = array(
			'city_name',
			'city_latitude',
			'city_longitude',
			'county_name',
			'region_name',
		);

		// Define GeoRocket arguments.
		$args = array(
			'license_key'  => $this->base->licensing->get_license_key(),
			'country_code' => $this->configuration['country_code'],
			'api_call'     => 'get_cities',
			'fields'       => implode( ',', $columns ),
		);
		switch ( $this->configuration['method'] ) {
			case 'radius':
				$args['location'] = $this->configuration['zipcode'] . ', ' . $this->configuration['country_code'];
				$args['radius']   = $this->configuration['radius'];
				break;

			case 'area':
				if ( is_array( $this->configuration['region_id'] ) && ! empty( $this->configuration['region_id'][0] ) ) {
					$args['region_id'] = $this->configuration['region_id'];
				}
				if ( is_array( $this->configuration['county_id'] ) && ! empty( $this->configuration['county_id'][0] ) ) {
					$args['county_id'] = $this->configuration['county_id'];
				}
				break;
		}

		// Define database argument, if stored in a constant.
		if ( defined( 'PAGE_GENERATOR_PRO_GEOROCKET_DB' ) ) {
			$args['database'] = PAGE_GENERATOR_PRO_GEOROCKET_DB;
		}

		// Run looped request to fetch locations in batches.
		$locations    = array();
		$current_page = 0;
		$last_page    = 0;
		do {
			// Increment pagination.
			$args['page'] = $current_page + 1;

			// Run query.
			$terms = $this->base->get_class( 'georocket' )->get_cities( $args );

			// Bail if an error occured.
			if ( is_wp_error( $terms ) ) {
				return $terms;
			}

			// Bail if no results were found.
			if ( ! is_array( $terms->data ) || count( $terms->data ) === 0 ) {
				return new WP_Error( 'page_generator_pro_groups_directory_setup_location_keyword', __( 'No results were found for the given criteria.', 'page-generator-pro' ) );
			}

			// Convert to flat array.
			foreach ( $terms->data as $i => $term ) {
				// Remove fields we don't use.
				unset( $term->id );

				// Implode into a string and store in locations array.
				$locations[] = implode( ', ', (array) $term );
			}

			// Update current and last page.
			$current_page = $terms->meta->current_page;
			$last_page    = $terms->meta->last_page;
		} while ( $current_page < $last_page );

		// Get unique keyword name that can be used.
		$keyword = $this->base->get_class( 'keywords' )->get_unique_name( 'location' );

		// Create Keyword.
		$result = $this->base->get_class( 'keywords' )->save(
			array(
				'keyword'   => $keyword,
				'data'      => implode( "\n", array_values( array_unique( $locations ) ) ),
				'delimiter' => ',',
				'columns'   => implode( ',', $columns ),
			)
		);

		// Bail if an error occured.
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		// Return ID and Keyword Name.
		return array(
			'id'      => $result,
			'keyword' => $keyword,
		);

	}

	/**
	 * Creates Content Groups for the Directory Structure
	 *
	 * @since   3.2.9
	 *
	 * @return  mixed   WP_Error | array
	 */
	private function setup_content_groups() {

		// Depending on the configuration, create the necessary Content Groups.
		switch ( $this->configuration['structure'] ) {

			/**
			 * Region > County > City > Service
			 */
			case 'region_county_city_service':
				// Region Group.
				$region_group_id = $this->setup_region_content_group();
				if ( is_wp_error( $region_group_id ) ) {
					return $region_group_id;
				}

				// County Group.
				$county_group_id = $this->setup_county_content_group(
					$region_group_id,
					'{' . $this->configuration['location_keyword'] . '(region_name)}'
				);
				if ( is_wp_error( $county_group_id ) ) {
					return $county_group_id;
				}

				// City Group.
				$city_group_id = $this->setup_city_content_group(
					'Your content about the services offered in {' . $this->configuration['location_keyword'] . '(city_name)} goes here.',
					$county_group_id,
					'{' . $this->configuration['location_keyword'] . '(region_name)}/{' . $this->configuration['location_keyword'] . '(county_name)}'
				);
				if ( is_wp_error( $city_group_id ) ) {
					return $city_group_id;
				}

				// Service Group.
				$service_group_id = $this->setup_service_content_group(
					'Your content about {' . $this->configuration['service_keyword'] . '} in {' . $this->configuration['location_keyword'] . '(city_name)}, {' . $this->configuration['location_keyword'] . '(county_name)} goes here.',
					$city_group_id,
					'{' . $this->configuration['location_keyword'] . '(region_name)}/{' . $this->configuration['location_keyword'] . '(county_name)}/{' . $this->configuration['location_keyword'] . '(city_name)}'
				);
				if ( is_wp_error( $service_group_id ) ) {
					return $service_group_id;
				}

				// Related Links: Region Group.
				$result = $this->append_related_links_to_group_content(
					$region_group_id,
					$county_group_id,
					'{' . $this->configuration['location_keyword'] . '(region_name):url}',
					'Counties Served in {' . $this->configuration['location_keyword'] . '(region_name)}'
				);
				if ( is_wp_error( $result ) ) {
					return $result;
				}

				// Related Links: County Group.
				$result = $this->append_related_links_to_group_content(
					$county_group_id,
					$city_group_id,
					'{' . $this->configuration['location_keyword'] . '(region_name):url}/{' . $this->configuration['location_keyword'] . '(county_name):url}',
					'Cities Served in {' . $this->configuration['location_keyword'] . '(county_name)}'
				);
				if ( is_wp_error( $result ) ) {
					return $result;
				}

				// Related Links: City Group.
				$result = $this->append_related_links_to_group_content(
					$city_group_id,
					$service_group_id,
					'{' . $this->configuration['location_keyword'] . '(region_name):url}/{' . $this->configuration['location_keyword'] . '(county_name):url}/{' . $this->configuration['location_keyword'] . '(city_name):url}',
					'Services in {' . $this->configuration['location_keyword'] . '(city_name)}'
				);
				if ( is_wp_error( $result ) ) {
					return $result;
				}

				// Related Links: Service Group.
				$result = $this->append_related_links_to_group_content(
					$service_group_id,
					$service_group_id,
					'{' . $this->configuration['location_keyword'] . '(region_name):url}/{' . $this->configuration['location_keyword'] . '(county_name):url}/{' . $this->configuration['location_keyword'] . '(city_name):url}',
					'Other Services in {' . $this->configuration['location_keyword'] . '(city_name)}'
				);
				if ( is_wp_error( $result ) ) {
					return $result;
				}

				// Return created Content Group IDs.
				return array(
					'region_group_id'  => $region_group_id,
					'county_group_id'  => $county_group_id,
					'city_group_id'    => $city_group_id,
					'service_group_id' => $service_group_id,
				);

			/**
			 * County > City > Service.
			 */
			case 'county_city_service':
				// County Group.
				$county_group_id = $this->setup_county_content_group();
				if ( is_wp_error( $county_group_id ) ) {
					return $county_group_id;
				}

				// City Group.
				$city_group_id = $this->setup_city_content_group(
					'Your content about the services offered in {' . $this->configuration['location_keyword'] . '(city_name)} goes here.',
					$county_group_id,
					'{' . $this->configuration['location_keyword'] . '(county_name)}'
				);
				if ( is_wp_error( $city_group_id ) ) {
					return $city_group_id;
				}

				// Service Group.
				$service_group_id = $this->setup_service_content_group(
					'Your content about {' . $this->configuration['service_keyword'] . '} in {' . $this->configuration['location_keyword'] . '(city_name)}, {' . $this->configuration['location_keyword'] . '(county_name)} goes here.',
					$city_group_id,
					'{' . $this->configuration['location_keyword'] . '(county_name)}/{' . $this->configuration['location_keyword'] . '(city_name)}'
				);
				if ( is_wp_error( $service_group_id ) ) {
					return $service_group_id;
				}

				// Related Links: County Group.
				$result = $this->append_related_links_to_group_content(
					$county_group_id,
					$city_group_id,
					'{' . $this->configuration['location_keyword'] . '(county_name):url}',
					'Cities Served in {' . $this->configuration['location_keyword'] . '(county_name)}'
				);
				if ( is_wp_error( $result ) ) {
					return $result;
				}

				// Related Links: City Group.
				$result = $this->append_related_links_to_group_content(
					$city_group_id,
					$service_group_id,
					'{' . $this->configuration['location_keyword'] . '(county_name):url}/{' . $this->configuration['location_keyword'] . '(city_name):url}',
					'Services in {' . $this->configuration['location_keyword'] . '(city_name)}'
				);
				if ( is_wp_error( $result ) ) {
					return $result;
				}

				// Related Links: Service Group.
				$result = $this->append_related_links_to_group_content(
					$service_group_id,
					$service_group_id,
					'{' . $this->configuration['location_keyword'] . '(county_name):url}/{' . $this->configuration['location_keyword'] . '(city_name):url}',
					'Other Services in {' . $this->configuration['location_keyword'] . '(city_name)}'
				);
				if ( is_wp_error( $result ) ) {
					return $result;
				}

				// Return created Content Group IDs.
				return array(
					'county_group_id'  => $county_group_id,
					'city_group_id'    => $city_group_id,
					'service_group_id' => $service_group_id,
				);

			/**
			 * City > Service
			 */
			case 'city_service':
				// City Group.
				$city_group_id = $this->setup_city_content_group(
					'Your content about the services offered in {' . $this->configuration['location_keyword'] . '(city_name)} goes here.'
				);
				if ( is_wp_error( $city_group_id ) ) {
					return $city_group_id;
				}

				// Service Group.
				$service_group_id = $this->setup_service_content_group(
					'Your content about {' . $this->configuration['service_keyword'] . '} in {' . $this->configuration['location_keyword'] . '(city_name)}, {' . $this->configuration['location_keyword'] . '(county_name)} goes here.',
					$city_group_id,
					'{' . $this->configuration['location_keyword'] . '(city_name)}'
				);
				if ( is_wp_error( $service_group_id ) ) {
					return $service_group_id;
				}

				// Related Links: City Group.
				$result = $this->append_related_links_to_group_content(
					$city_group_id,
					$service_group_id,
					'{' . $this->configuration['location_keyword'] . '(city_name):url}',
					'Services in {' . $this->configuration['location_keyword'] . '(city_name)}'
				);
				if ( is_wp_error( $result ) ) {
					return $result;
				}

				// Related Links: Service Group.
				$result = $this->append_related_links_to_group_content(
					$service_group_id,
					$service_group_id,
					'{' . $this->configuration['location_keyword'] . '(city_name):url}',
					'Other Services in {' . $this->configuration['location_keyword'] . '(city_name)}'
				);
				if ( is_wp_error( $result ) ) {
					return $result;
				}

				// Return created Content Group IDs.
				return array(
					'city_group_id'    => $city_group_id,
					'service_group_id' => $service_group_id,
				);

			/**
			 * Service > City
			 */
			case 'service_city':
				// Service Group.
				$service_group_id = $this->setup_service_content_group(
					'Your content about {' . $this->configuration['service_keyword'] . '} goes here.'
				);
				if ( is_wp_error( $service_group_id ) ) {
					return $service_group_id;
				}

				// City Group.
				$city_group_id = $this->setup_city_content_group(
					'Your content about {' . $this->configuration['service_keyword'] . '} in {' . $this->configuration['location_keyword'] . '(city_name)} goes here.',
					$service_group_id,
					'{' . $this->configuration['service_keyword'] . '}'
				);
				if ( is_wp_error( $city_group_id ) ) {
					return $city_group_id;
				}

				// Related Links: Service Group.
				$result = $this->append_related_links_to_group_content(
					$service_group_id,
					$city_group_id,
					'{' . $this->configuration['service_keyword'] . '}',
					'Cities offering {' . $this->configuration['service_keyword'] . '}'
				);
				if ( is_wp_error( $result ) ) {
					return $result;
				}

				// Return created Content Group IDs.
				return array(
					'service_group_id' => $service_group_id,
					'city_group_id'    => $city_group_id,
				);

		}

	}

	/**
	 * Creates the Region Content Group
	 *
	 * @since   3.3.0
	 *
	 * @param   int    $parent_group_id    Parent Group ID (optional).
	 * @param   string $parent_slug        Parent Slug (optional).
	 * @return  mixed                       WP_Error | int
	 */
	private function setup_region_content_group( $parent_group_id = 0, $parent_slug = '' ) {

		return $this->base->get_class( 'groups' )->create(
			array(
				'title'       => '{' . $this->configuration['location_keyword'] . '(region_name)}',
				'content'     => 'Your content about the services offered in {' . $this->configuration['location_keyword'] . '(region_name)} goes here.',
				'permalink'   => '{' . $this->configuration['location_keyword'] . '(region_name)}',
				'pageParent'  => array(
					'page' => $parent_slug,
				),
				'description' => __( 'Regions', 'page-generator-pro' ),
			),
			$parent_group_id
		);

	}

	/**
	 * Creates the County Content Group
	 *
	 * @since   3.3.0
	 *
	 * @param   int    $parent_group_id    Parent Group ID (optional).
	 * @param   string $parent_slug        Parent Slug (optional).
	 * @return  mixed                       WP_Error | int
	 */
	private function setup_county_content_group( $parent_group_id = 0, $parent_slug = '' ) {

		return $this->base->get_class( 'groups' )->create(
			array(
				'title'       => '{' . $this->configuration['location_keyword'] . '(county_name)}',
				'content'     => 'Your content about the services offered in {' . $this->configuration['location_keyword'] . '(county_name)} goes here.',
				'permalink'   => '{' . $this->configuration['location_keyword'] . '(county_name)}',
				'pageParent'  => array(
					'page' => $parent_slug,
				),
				'description' => __( 'Counties', 'page-generator-pro' ),
			),
			$parent_group_id
		);

	}

	/**
	 * Creates the City Content Group
	 *
	 * @since   3.3.0
	 *
	 * @param   string $content            Content.
	 * @param   int    $parent_group_id    Parent Group ID (optional).
	 * @param   string $parent_slug        Parent Slug (optional).
	 * @return  mixed                       WP_Error | int
	 */
	private function setup_city_content_group( $content, $parent_group_id = 0, $parent_slug = '' ) {

		return $this->base->get_class( 'groups' )->create(
			array(
				'title'       => '{' . $this->configuration['location_keyword'] . '(city_name)}',
				'content'     => $content,
				'permalink'   => '{' . $this->configuration['location_keyword'] . '(city_name)}',
				'latitude'    => '{' . $this->configuration['location_keyword'] . '(city_latitude)}',
				'longitude'   => '{' . $this->configuration['location_keyword'] . '(city_longitude)}',
				'pageParent'  => array(
					'page' => $parent_slug,
				),
				'description' => __( 'Cities', 'page-generator-pro' ),
			),
			$parent_group_id
		);

	}

	/**
	 * Creates the Service Content Group
	 *
	 * @since   3.3.0
	 *
	 * @param   string $content            Content.
	 * @param   int    $parent_group_id    Parent Group ID (optional).
	 * @param   string $parent_slug        Parent Slug (optional).
	 * @return  mixed                       WP_Error | int
	 */
	private function setup_service_content_group( $content, $parent_group_id = 0, $parent_slug = '' ) {

		return $this->base->get_class( 'groups' )->create(
			array(
				'title'       => '{' . $this->configuration['service_keyword'] . '}',
				'content'     => $content,
				'permalink'   => '{' . $this->configuration['service_keyword'] . '}',
				'pageParent'  => array(
					'page' => $parent_slug,
				),
				'description' => __( 'Services', 'page-generator-pro' ),
			),
			$parent_group_id
		);

	}

	/**
	 * Appends the Related Links Shortcode to the given Content Group's Content
	 *
	 * @since   3.3.0
	 *
	 * @param   int    $group_id                   Group ID to append Related Links Shortcode on.
	 * @param   int    $related_links_group_id     Related Links Group ID Parameter.
	 * @param   string $related_links_parent_slug  Related Links Parent Slug Parameter.
	 * @param   string $related_links_heading      Related Links Heading Parameter.
	 * @return  mixed                               WP_Error | int
	 */
	private function append_related_links_to_group_content( $group_id, $related_links_group_id, $related_links_parent_slug, $related_links_heading ) {

		// Get existing Group Content.
		$post = get_post( $group_id );

		// Append Heading and Related Links Shortcode to Group Content.
		$content = $post->post_content . "\n" . $this->get_related_links_heading( $related_links_heading ) . "\n" . $this->get_related_links_shortcode( $related_links_group_id, $related_links_parent_slug );

		// Update Group Content, appending Related Links.
		return wp_update_post(
			array(
				'ID'           => $group_id,
				'post_content' => $content,
			),
			true
		);

	}

	/**
	 * Returns the given heading text, wrapped in a <h2>
	 *
	 * @since   3.3.0
	 *
	 * @param   string $heading    Heading.
	 * @return  string              Heading wrapped in <h2> tags
	 */
	private function get_related_links_heading( $heading ) {

		$html = '<h2>' . $heading . '</h2>';

		/**
		 * Defines the header to display before the Related Links shortcode
		 * when Content Groups are generated through the Groups Directory
		 * functionality.
		 *
		 * @since   3.3.0
		 *
		 * @param   string  $html   HTML Markup for Heading
		 * @param   string  $heading    Heading Text without HTML
		 */
		$html = apply_filters( 'page_generator_pro_groups_directory_get_related_links_heading', $html, $heading );

		// Return.
		return $html;

	}

	/**
	 * Returns the Related Links shortcode for the given Group ID and Parent Slug
	 *
	 * @since   3.3.0
	 *
	 * @param   int    $group_id       Group ID.
	 * @param   string $parent_slug    Parent Slug.
	 * @return  string                  Related Links Shortcode
	 */
	private function get_related_links_shortcode( $group_id = 0, $parent_slug = '' ) {

		// Define shortcode.
		$shortcode = '[page-generator-pro-related-links group_id="' . $group_id . '" post_type="page" post_status="publish" post_parent="' . $parent_slug . '" output_type="list_links" limit="0" columns="3" orderby="name" order="asc"]';

		/**
		 * Defines the Related Links Shortcode to use when a Content Group
		 * is generated through the Groups Directory functionality.
		 *
		 * @since   3.3.0
		 *
		 * @param   string  $shortcode      Shortcode.
		 * @param   int     $group_id       Group ID.
		 * @param   string  $parent_slug    Parent Slug.
		 */
		$shortcode = apply_filters( 'page_generator_pro_groups_directory_get_related_links_shortcode', $shortcode, $group_id, $parent_slug );

		// Return.
		return $shortcode;

	}

}
