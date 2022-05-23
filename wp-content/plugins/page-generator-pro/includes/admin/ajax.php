<?php
/**
 * AJAX Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Registers AJAX endpoints for various features, such as Generate Locations,
 * Generate Spintax from Content and Generation/Trash/Delete Content.
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 1.0.0
 */
class Page_Generator_Pro_AJAX {

	/**
	 * Holds the base object.
	 *
	 * @since   1.9.8
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Constructor
	 *
	 * @since   1.0.0
	 *
	 * @param   object $base    Base Plugin Class.
	 */
	public function __construct( $base ) {

		// Store base class.
		$this->base = $base;

		// Georocket API.
		add_action( 'wp_ajax_page_generator_pro_georocket', array( $this, 'georocket' ) );

		// Keywords: Get Terms.
		add_action( 'wp_ajax_page_generator_pro_keywords_get_terms', array( $this, 'keywords_get_terms' ) );

		// Keywords: Generate Locations.
		add_action( 'wp_ajax_page_generator_pro_keywords_generate_locations', array( $this, 'keywords_generate_locations' ) );

		// Page Attributes: Parent.
		add_action( 'wp_ajax_page_generator_pro_search_pages', array( $this, 'search_pages' ) );

		// Generate: Authors.
		add_action( 'wp_ajax_page_generator_pro_search_authors', array( $this, 'search_authors' ) );

		// Research.
		add_action( 'wp_ajax_page_generator_pro_research', array( $this, 'research' ) );
		add_action( 'wp_ajax_page_generator_pro_research_get_status', array( $this, 'research_get_status' ) );

		// TinyMCE.
		add_action( 'wp_ajax_page_generator_pro_tinymce_spintax_generate', array( $this, 'spintax_generate' ) );
		add_action( 'wp_ajax_page_generator_pro_output_tinymce_modal', array( $this, 'output_tinymce_modal' ) );

		// Generate: Content.
		add_action( 'wp_ajax_page_generator_pro_generate_content', array( $this, 'generate_content' ) );
		add_action( 'wp_ajax_page_generator_pro_generate_content_trash_generated_content', array( $this, 'trash_generated_content' ) );
		add_action( 'wp_ajax_page_generator_pro_generate_content_delete_generated_content', array( $this, 'delete_generated_content' ) );
		add_action( 'wp_ajax_page_generator_pro_generate_content_after', array( $this, 'after_generated_content' ) );

		// Generate: Terms.
		add_action( 'wp_ajax_page_generator_pro_generate_term', array( $this, 'generate_term' ) );
		add_action( 'wp_ajax_page_generator_pro_generate_term_delete_generated_term', array( $this, 'delete_generated_terms' ) );
		add_action( 'wp_ajax_page_generator_pro_generate_term_after', array( $this, 'after_generated_terms' ) );

	}

	/**
	 * Returns the maximum number of generated items to delete in a single AJAX
	 * request, to prevent timeouts or server errors.
	 *
	 * @since   2.7.6
	 *
	 * @return  int     Limit
	 */
	public function get_trash_delete_per_request_item_limit() {

		$limit = 100;

		/**
		 * The maximum number of generated items to trash or delete in a single AJAX
		 * request, to prevent timeouts or server errors.
		 *
		 * If there are more items to delete than the limit specified, the Plugin
		 * will send synchronous requests until all items are deleted.
		 *
		 * @since   2.7.6
		 */
		$limit = apply_filters( 'page_generator_pro_ajax_delete_generated_count_number_of_items', $limit );

		// Return.
		return absint( $limit );

	}

	/**
	 * Calls the Georocket API, returning results that are compatible
	 * with selectize.js
	 *
	 * @since   1.7.8
	 */
	public function georocket() {

		// Verify nonce.
		check_ajax_referer( 'generate_locations', 'nonce' );

		// If any required parameters are missing, bail.
		$required_params = array(
			'api_call',
			'country_code',
		);
		foreach ( $required_params as $required_param ) {
			if ( ! isset( $_POST[ $required_param ] ) ) {
				wp_send_json_error(
					array(
						'message' => sprintf(
							/* translators: API parameter */
							__( 'The %s parameter is missing from the POST data.', 'page-generator-pro' ),
							$required_param
						),
						'args'    => false,
						'request' => $_POST,
					)
				);
			}
		}

		// Get sanitized data.
		$api_call = sanitize_text_field( $_POST['api_call'] );

		// Define an array of all possible arguments.
		$args = array(
			// Plugin License Key.
			'license_key'  => $this->base->licensing->get_license_key(),

			'country_code' => sanitize_text_field( $_POST['country_code'] ),
		);

		// If a search field and query is specified, add it now
		// e.g. city_name: birmingham.
		if ( isset( $_POST['api_search_field'] ) && isset( $_POST['query'] ) ) {
			$args[ sanitize_text_field( $_POST['api_search_field'] ) ] = sanitize_text_field( $_POST['query'] );
		}

		// If region IDs are specified, add them now.
		if ( isset( $_POST['region_id'] ) ) {
			$args['region_id'] = $_POST['region_id'];
		}

		// If county IDs are specified, add them now.
		if ( isset( $_POST['county_id'] ) ) {
			$args['county_id'] = $_POST['county_id'];
		}

		// Define database argument, if stored in a constant.
		if ( defined( 'PAGE_GENERATOR_PRO_GEOROCKET_DB' ) ) {
			$args['database'] = PAGE_GENERATOR_PRO_GEOROCKET_DB;
		}

		// Depending on the search key, run API call.
		switch ( $api_call ) {
			case 'get_cities':
				// API call to cities endpoint.
				$terms = $this->base->get_class( 'georocket' )->get_cities( $args );
				break;

			case 'get_counties':
				// API call to counties endpoint.
				$terms = $this->base->get_class( 'georocket' )->get_counties( $args );
				break;

			case 'get_regions':
				// API call to regions endpoint.
				$terms = $this->base->get_class( 'georocket' )->get_regions( $args );
				break;

			default:
				// Invalid key.
				wp_send_json_error(
					array(
						'message' => __( 'api_call parameter is invalid, and should match one of get_cities, get_counties or get_regions', 'page-generator-pro' ),
						'args'    => $args,
						'request' => $_POST,
					)
				);
				break;
		}

		// Bail if an error occured.
		if ( is_wp_error( $terms ) ) {
			wp_send_json_error(
				array(
					'message' => $terms->get_error_message(),
					'args'    => $args,
					'request' => $_POST,
				)
			);
		}

		// Bail if no results were found.
		if ( ! is_array( $terms->data ) || count( $terms->data ) === 0 ) {
			wp_send_json_error(
				array(
					'message' => __( 'No results were found for the given criteria.', 'page-generator-pro' ),
					'args'    => $args,
					'request' => $_POST,
				)
			);
		}

		// Send results.
		wp_send_json_success(
			array(
				'data'    => $terms->data,
				'args'    => $args,
				'request' => $_POST,
			)
		);

	}

	/**
	 * Keywords: Get Terms
	 *
	 * @since   3.0.9
	 */
	public function keywords_get_terms() {

		// Verify nonce.
		check_ajax_referer( 'save_keyword', 'nonce' );

		// Get vars.
		$id     = absint( $_REQUEST['id'] );
		$draw   = absint( $_REQUEST['draw'] );
		$offset = absint( $_REQUEST['start'] );
		$limit  = absint( $_REQUEST['length'] );
		$search = sanitize_text_field( $_REQUEST['search']['value'] );

		// Run query.
		$terms = $this->base->get_class( 'keywords' )->get_terms( $id, $offset, $limit, $search, false );

		// Return error.
		if ( ! $terms ) {
			echo wp_json_encode(
				array(
					'error' => __( 'Keyword not found.', 'page-generator-pro' ),
				)
			);
			die();
		}

		// Return Terms in JSON string compatible with DataTables.
		echo wp_json_encode(
			array(
				'draw'            => $draw,
				'recordsTotal'    => $terms['total'],
				'recordsFiltered' => $terms['filtered'],
				'data'            => $terms['data'],
			)
		);
		die();

	}

	/**
	 * Passes on the Keywords: Generate Locations form submission request
	 * to the GeoRocket API
	 *
	 * @since   1.8.2
	 */
	public function keywords_generate_locations() {

		// Verify nonce.
		check_ajax_referer( 'generate_locations', 'nonce' );

		// Check that a keyword ID or name was supplied.
		if ( ( ! isset( $_POST['keyword_id'] ) || empty( $_POST['keyword_id'] ) ) && ( ! isset( $_POST['keyword'] ) || empty( $_POST['keyword'] ) ) ) {
			wp_send_json_error( __( 'Please specify a keyword.', 'page-generator-pro' ) );
		}

		// If a Keyword ID has been specified, store it now for later use.
		$keyword_id = false;
		if ( isset( $_POST['keyword_id'] ) && ! empty( $_POST['keyword_id'] ) ) {
			$keyword_id = absint( $_POST['keyword_id'] );
		}

		// If no Keyword ID is specified, check that the new keyword we want to create doesn't already exist.
		if ( ! $keyword_id ) {
			// Check if the keyword already exists.
			$keyword_exists = $this->base->get_class( 'keywords' )->exists( $_POST['keyword'] );
			if ( $keyword_exists ) {
				wp_send_json_error( __( 'The keyword already exists.  Please specify a different keyword name.', 'page-generator-pro' ) );
			}
		}

		// Setup Georocket, and define an array of all possible arguments.
		$args = array(
			// Plugin License Key.
			'license_key'                 => $this->base->licensing->get_license_key(),

			// Location and Radius.
			'location'                    => false,
			'radius'                      => false,

			// Street Name Restraints.
			'street_name'                 => false,
			'street_name_not'             => false,

			// Zipcode District Restraints.
			'zipcode_district'            => false,
			'zipcode_district_not'        => false,

			// City Restraints.
			'population_min'              => false,
			'population_max'              => false,
			'median_household_income_min' => false,
			'median_household_income_max' => false,
			'city_id'                     => false,
			'city_name'                   => false,
			'city_name_not'               => false,

			// County Restraints.
			'county_id'                   => false,
			'county_name'                 => false,
			'county_name_not'             => false,

			// Region Restraints.
			'region_id'                   => false,
			'region_name'                 => false,
			'region_name_not'             => false,

			// Country Restraints.
			'country_id'                  => false,
			'country_code'                => sanitize_text_field( $_POST['country_code'] ),
			'country_name'                => false,

			// Fields.
			'fields'                      => implode( ',', $_POST['output_type'] ),

			// Pagination.
			'per_page'                    => 10000,
			'page'                        => ( isset( $_POST['page'] ) ? absint( $_POST['page'] ) : false ),                      // For non-zipcode requests.
			'start_id'                    => ( isset( $_POST['start_id'] ) ? sanitize_text_field( $_POST['start_id'] ) : false ), // For zipcode requests.
		);

		// Arguments will be either location/radius or city/county/region/country.
		switch ( sanitize_text_field( $_POST['method'] ) ) {
			case 'radius':
				$args['location'] = sanitize_text_field( $_POST['location'] ) . ', ' . sanitize_text_field( $_POST['country_code'] );

				// Build arguments.
				$keys = array(
					'radius',
					'population_min',
					'population_max',
					'median_household_income_min',
					'median_household_income_max',
				);
				$args = $this->keywords_generate_locations_build_args( $args, $keys, $_POST );
				break;

			case 'area':
				$keys = array(
					'population_min',
					'population_max',

					'median_household_income_min',
					'median_household_income_max',

					'city_name',
					'city_id',

					'county_name',
					'county_id',

					'region_name',
					'region_id',
				);
				$args = $this->keywords_generate_locations_build_args( $args, $keys, $_POST );
				break;
		}

		// Define exclusions now, if they exist.
		$exclusions = false;
		if ( isset( $_POST['exclusions'] ) && ! empty( $_POST['exclusions'] ) ) {
			$exclusions = explode( ',', sanitize_text_field( $_POST['exclusions'] ) );
		}

		// Define database argument, if stored in a constant.
		if ( defined( 'PAGE_GENERATOR_PRO_GEOROCKET_DB' ) ) {
			$args['database'] = PAGE_GENERATOR_PRO_GEOROCKET_DB;
		}

		// Make Georocket API call, depending on the level of detail required for the output.
		if ( count( array_intersect( array_keys( $this->base->get_class( 'common' )->get_locations_output_types_street_names() ), $_POST['output_type'] ) ) > 0 ) {
			// API call to street names endpoint.
			if ( $exclusions !== false ) {
				$args['street_name_not'] = $exclusions;
			}
			$result = $this->base->get_class( 'georocket' )->get_street_names( $args );
		} elseif ( count( array_intersect( array_keys( $this->base->get_class( 'common' )->get_locations_output_types_zipcode_districts() ), $_POST['output_type'] ) ) > 0 ) {
			// API call to zipcode district endpoint.
			if ( $exclusions !== false ) {
				$args['zipcode_district_not'] = $exclusions;
			}
			$result = $this->base->get_class( 'georocket' )->get_zipcode_districts( $args );
		} elseif ( count( array_intersect( array_keys( $this->base->get_class( 'common' )->get_locations_output_types_zipcodes() ), $_POST['output_type'] ) ) > 0 ) {
			// API call to zipcodes endpoint.
			if ( $exclusions !== false ) {
				$args['city_name_not'] = $exclusions;
			}
			$result = $this->base->get_class( 'georocket' )->get_zipcodes( $args );
		} elseif ( count( array_intersect( array_keys( $this->base->get_class( 'common' )->get_locations_output_types_cities() ), $_POST['output_type'] ) ) > 0 ) {
			// API call to cities endpoint.
			if ( $exclusions !== false ) {
				$args['city_name_not'] = $exclusions;
			}
			$result = $this->base->get_class( 'georocket' )->get_cities( $args );
		} elseif ( count( array_intersect( array_keys( $this->base->get_class( 'common' )->get_locations_output_types_counties() ), $_POST['output_type'] ) ) > 0 ) {
			// API call to counties endpoint.
			if ( $exclusions !== false ) {
				$args['county_name_not'] = $exclusions;
			}
			$result = $this->base->get_class( 'georocket' )->get_counties( $args );
		} elseif ( count( array_intersect( array_keys( $this->base->get_class( 'common' )->get_locations_output_types_regions() ), $_POST['output_type'] ) ) > 0 ) {
			// API call to regions endpoint.
			if ( $exclusions !== false ) {
				$args['region_name_not'] = $exclusions;
			}
			$result = $this->base->get_class( 'georocket' )->get_regions( $args );
		}

		// Bail if an error occured.
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
		}

		// Bail if no results were found.
		if ( ! is_array( $result->data ) || count( $result->data ) === 0 ) {
			wp_send_json_error( __( 'No results were found for the given criteria.', 'page-generator-pro' ) );
		}

		// Build single Keyword.
		$keyword = array(
			'keyword'   => sanitize_text_field( $_POST['keyword'] ),
			'data'      => '',
			'delimiter' => ( count( $_POST['output_type'] ) > 1 ? ',' : '' ),
			'columns'   => ( count( $_POST['output_type'] ) > 1 ? implode( ',', $_POST['output_type'] ) : '' ),
		);

		// Build the keyword data based on the output type formatting.
		$formatted_terms = array();
		foreach ( $result->data as $i => $term ) {
			// Define array to build output order for this term.
			$formatted_terms[ $i ] = array();

			// Build array.
			foreach ( $_POST['output_type'] as $output_type ) {
				// Skip if this Output Type isn't in the API resultset.
				if ( ! isset( $term->{ $output_type } ) ) {
					continue;
				}

				// Depending on the output type, we might need to use quotes and convert newlines to <br />.
				switch ( $output_type ) {
					/**
					 * Wikipedia Summaries
					 * - Some include newlines, which break Terms over multiple lines
					 */
					case 'city_wikipedia_summary':
					case 'city_wikipedia_summary_local':
					case 'county_wikipedia_summary':
					case 'region_wikipedia_summary':
						$formatted_terms[ $i ][] = '\"' . trim( str_replace( "\n", '<br />', $term->{ $output_type } ) ) . '\"';
						break;

					/**
					 * Wikipedia URLs
					 * - Some include a comma, which would fail column + term count when saving as a Keyword
					 */
					case 'city_wikipedia_url':
					case 'county_wikipedia_url':
					case 'region_wikipedia_url':
						$formatted_terms[ $i ][] = '\"' . trim( $term->{ $output_type } ) . '\"';
						break;

					/**
					 * If a comma is included in the value, encapsulate the value
					 */
					default:
						$value                   = ( strpos( $term->{ $output_type }, ',' ) !== false ? '"' . $term->{ $output_type } . '"' : $term->{ $output_type } );
						$formatted_terms[ $i ][] = $value;
						break;
				}
			}

			// Implode into a string.
			$formatted_terms[ $i ] = implode( ', ', $formatted_terms[ $i ] );
		}

		// Remove duplicates.
		// This should never occur, but it's a good fallback just in case.
		$formatted_terms = array_values( array_unique( $formatted_terms ) );

		// Add Terms to keyword data.
		$keyword['data'] = implode( "\n", $formatted_terms );

		// Save Keyword, returning Keyword ID or WP_Error.
		$keyword_result = $this->base->get_class( 'keywords' )->save( $keyword, $keyword_id, true );

		// Bail if an error occured.
		if ( is_wp_error( $keyword_result ) ) {
			wp_send_json_error( $keyword_result->get_error_message() );
		}

		// Return the Keyword ID along with the GeoRocket Links and Meta.
		wp_send_json_success(
			array(
				'keyword_id'  => $keyword_result,
				'keyword_url' => admin_url( 'admin.php?page=page-generator-pro-keywords&cmd=form&id=' . $keyword_result ),
				'links'       => $result->links,
				'meta'        => $result->meta,
			)
		);
	}

	/**
	 * Appends the given Post Data to the arguments array, based on the
	 * specific Post Data Keys to add.
	 *
	 * If a Post Data Key doesn't exist, is empty or false, we don't add
	 * it to the arguments
	 *
	 * @since   2.2.3
	 *
	 * @param   array $args       Arguments.
	 * @param   array $keys       Argument Keys to possibly add to $args.
	 * @param   array $post_data  POST Data.
	 * @return  array               Arguments
	 */
	private function keywords_generate_locations_build_args( $args, $keys, $post_data ) {

		// Verify nonce.
		// This is already performed in keywords_generate_locations(), but this prevents phpcs errors.
		check_ajax_referer( 'generate_locations', 'nonce' );

		foreach ( $keys as $key ) {
			if ( ! isset( $post_data[ $key ] ) ) {
				continue;
			}
			if ( empty( $post_data[ $key ] ) ) {
				continue;
			}
			if ( is_array( $post_data[ $key ] ) && count( $post_data[ $key ] ) === 0 ) {
				continue;
			}
			if ( ! is_array( $post_data[ $key ] ) && ! $post_data[ $key ] ) {
				continue;
			}

			if ( is_array( $post_data[ $key ] ) ) {
				if ( count( $post_data[ $key ] ) === 1 ) {
					$args[ $key ] = sanitize_text_field( $post_data[ $key ][0] );
				} else {
					$args[ $key ] = $_POST[ $key ];
				}
			} else {
				$args[ $key ] = sanitize_text_field( $post_data[ $key ] );
			}
		}

		return $args;

	}

	/**
	 * Searches for Pages, Posts or Custom Post Types for the given freeform text
	 *
	 * @since   2.1.8
	 */
	public function search_pages() {

		// Verify nonce.
		check_ajax_referer( 'search_pages', 'nonce' );

		// Parse args.
		parse_str( sanitize_text_field( $_REQUEST['args'] ), $args );

		// Build WP_Query args.
		$query = array(
			'post_status'            => 'publish',
			'post_type'              => ( isset( $args['post_type'] ) ? $args['post_type'] : 'page' ),
			's'                      => sanitize_text_field( $_REQUEST['query'] ),
			'order'                  => 'ASC',
			'orderby'                => 'relevance',

			// Performance.
			'posts_per_page'         => 10,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'fields'                 => 'id=>parent',
		);

		// Add Exclusion.
		if ( isset( $args['exclude_tree'] ) ) {
			$query['post__not_in'] = array( absint( $args['exclude_tree'] ) );
		}

		// Get results.
		$posts = new WP_Query( $query );

		// If an error occured, bail.
		if ( is_wp_error( $posts ) ) {
			return wp_send_json_error( $posts->get_error_message() );
		}

		// Build array.
		$posts_array = array();
		if ( ! empty( $posts->posts ) ) {
			foreach ( $posts->posts as $post ) {
				$posts_array[] = array(
					'ID'         => $post->ID,
					'post_title' => ( $post->post_parent > 0 ? get_the_title( $post->post_parent ) . ': ' : '' ) . get_the_title( $post->ID ),
				);
			}
		}

		// Done.
		wp_send_json_success( $posts_array );

	}

	/**
	 * Searches for Authors for the given freeform text
	 *
	 * @since   1.8.3
	 */
	public function search_authors() {

		// Verify nonce.
		check_ajax_referer( 'search_authors', 'nonce' );

		// Get vars.
		$query = sanitize_text_field( $_REQUEST['query'] );

		// Get results.
		$users = new WP_User_Query(
			array(
				'search' => '*' . $query . '*',
			)
		);

		// If an error occured, bail.
		if ( is_wp_error( $users ) ) {
			return wp_send_json_error( $users->get_error_message() );
		}

		// Build array.
		$users_array = array();
		$results     = $users->get_results();
		if ( ! empty( $results ) ) {
			foreach ( $results as $user ) {
				$users_array[] = array(
					'id'         => $user->ID,
					'user_login' => $user->user_login,
				);
			}
		}

		// Done.
		wp_send_json_success( $users_array );

	}

	/**
	 * Research (produce) content for a given topic.
	 *
	 * @since   2.8.9
	 */
	public function research() {

		// Verify nonce.
		check_ajax_referer( 'page-generator-pro-research', 'nonce' );

		// Bail if no content.
		if ( ! isset( $_REQUEST['topic'] ) ) {
			wp_send_json_error( __( 'No topic was defined.', 'page-generator-pro' ) );
		}

		$topic = trim( sanitize_text_field( $_REQUEST['topic'] ) );
		if ( empty( $topic ) ) {
			wp_send_json_error( __( 'No topic was defined.', 'page-generator-pro' ) );
		}

		// Send request to create content.
		$result = $this->base->get_class( 'research' )->research( $topic );

		// Return success or error.
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
		}

		// Return result.
		wp_send_json_success( $result );

	}

	/**
	 * Gets the status on a research (produce) request.
	 *
	 * @since   2.8.9
	 */
	public function research_get_status() {

		// Verify nonce.
		check_ajax_referer( 'page-generator-pro-research', 'nonce' );

		// Bail if no ID.
		if ( ! isset( $_REQUEST['id'] ) ) {
			wp_send_json_error( __( 'No ID was defined.', 'page-generator-pro' ) );
		}

		$id = trim( sanitize_text_field( $_REQUEST['id'] ) );
		if ( empty( $id ) ) {
			wp_send_json_error( __( 'No ID was defined.', 'page-generator-pro' ) );
		}

		// Send request.
		$result = $this->base->get_class( 'research' )->get_status( $id );

		// Return success or error.
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
		}

		// Return result.
		wp_send_json_success( $result );

	}

	/**
	 * Adds spintax to words in the given content.
	 *
	 * @since   1.7.9
	 */
	public function spintax_generate() {

		// Verify nonce.
		check_ajax_referer( 'page-generator-pro-spintax-generate', 'nonce' );

		// Bail if no content.
		if ( ! isset( $_REQUEST['content'] ) ) {
			wp_send_json_error( __( 'No content was selected.', 'page-generator-pro' ) );
		}

		// Strip slashes.
		$content = trim( stripslashes( $_REQUEST['content'] ) );

		// If no content exists, bail.
		if ( empty( $content ) ) {
			wp_send_json_error( __( 'No content was selected.', 'page-generator-pro' ) );
		}

		// Add spintax to content.
		$content = $this->base->get_class( 'spintax' )->add_spintax( $content );

		// Return success or error.
		if ( is_wp_error( $content ) ) {
			wp_send_json_error( $content->get_error_message() );
		}

		// Return content.
		wp_send_json_success( $content );

	}

	/**
	 * Loads the view for a shortcode's modal in TinyMCE.
	 *
	 * @since   2.5.1
	 */
	public function output_tinymce_modal() {

		// Verify nonce.
		check_ajax_referer( 'page_generator_pro_tinymce', 'nonce' );

		// Fetch shortcode.
		$shortcode = $this->base->get_class( 'shortcode' )->get_shortcode( sanitize_text_field( $_REQUEST['shortcode'] ) );

		// If the shortcode is not registered, return a view in the modal to tell the user.
		if ( ! $shortcode ) {
			require_once $this->base->plugin->folder . '/views/admin/tinymce-modal-missing.php';
			die();
		}

		// If we have less than two tabs defined in the shortcode properties, output a basic modal.
		if ( count( $shortcode['tabs'] ) < 2 ) {
			require_once $this->base->plugin->folder . '/views/admin/tinymce-modal.php';
			die();
		}

		// Output tabbed view.
		require_once $this->base->plugin->folder . '/views/admin/tinymce-modal-tabbed.php';
		die();

	}

	/**
	 * Generates a Page, Post or CPT
	 *
	 * @since   1.6.1
	 */
	public function generate_content() {

		// Validate.
		$group = $this->generate_validation( 'page-generator-pro-generate-browser' );

		// Run.
		$result = $this->base->get_class( 'generate' )->generate_content(
			$group['group_id'],
			$group['current_index'],
			$group['test_mode'],
			'browser',
			$group['last_generated_post_date_time']
		);

		// Return.
		$this->generate_return( $result );

	}

	/**
	 * Generates a Term
	 *
	 * @since   1.6.1
	 */
	public function generate_term() {

		// Validate.
		$group = $this->generate_validation( 'page-generator-pro-generate-browser' );

		// Run.
		$result = $this->base->get_class( 'generate' )->generate_term( $group['group_id'], $group['current_index'], $group['test_mode'], 'browser' );

		// Return.
		$this->generate_return( $result );

	}

	/**
	 * Trashes Generated Content
	 *
	 * @since   1.9.1
	 */
	public function trash_generated_content() {

		// Validate.
		$group = $this->generate_validation( 'page-generator-pro-trash-generated-content' );

		// Run.
		$result = $this->base->get_class( 'generate' )->trash_content( $group['group_id'], $this->get_trash_delete_per_request_item_limit() );
		if ( is_wp_error( $result ) ) {
			$this->generate_return( $result );
			die();
		}

		// Determine if there are more Posts in this Content Group that need deleting.
		$remaining_posts = $this->base->get_class( 'generate' )->get_generated_content_post_ids( $group['group_id'] );
		if ( is_wp_error( $remaining_posts ) ) {
			// Error will say there are no more Generated Posts to delete for this Content Group.
			$result = array(
				'has_more' => false,
			);
		} else {
			$result = array(
				'has_more' => true,
			);
		}

		// Return.
		$this->generate_return( $result );

	}

	/**
	 * Deletes Generated Content
	 *
	 * @since   1.8.4
	 */
	public function delete_generated_content() {

		// Validate.
		$group = $this->generate_validation( 'page-generator-pro-delete-generated-content' );

		// Run.
		$result = $this->base->get_class( 'generate' )->delete_content( $group['group_id'], $this->get_trash_delete_per_request_item_limit() );
		if ( is_wp_error( $result ) ) {
			$this->generate_return( $result );
			die();
		}

		// Determine if there are more Posts in this Content Group that need deleting.
		$remaining_posts = $this->base->get_class( 'generate' )->get_generated_content_post_ids( $group['group_id'] );
		if ( is_wp_error( $remaining_posts ) ) {
			// Error will say there are no more Generated Posts to delete for this Content Group.
			$result = array(
				'has_more' => false,
			);

			// Reset the Last Index Generated.
			$this->base->get_class( 'groups' )->update_last_index_generated( $group['group_id'], 0 );
		} else {
			$result = array(
				'has_more' => true,
			);
		}

		// Return.
		$this->generate_return( $result );

	}

	/**
	 * Removes the generating flag on the Group, as Generation has finished.
	 *
	 * @since   1.9.9
	 */
	public function after_generated_content() {

		// Validate.
		$group = $this->generate_validation();

		/**
		 * Runs any actions after Generate Content has finished.
		 *
		 * @since   3.0.7
		 *
		 * @param   int     $group_id   Group ID.
		 * @param   bool    $test_mode  Test Mode.
		 * @param   string  $system     System.
		 */
		do_action( 'page_generator_pro_generate_content_after', $group['group_id'], false, 'browser' );

		// Run.
		$result = $this->base->get_class( 'groups' )->stop_generation( $group['group_id'] );

		// Return.
		$this->generate_return( $result );

	}

	/**
	 * Deletes Generated Terms
	 *
	 * @since   1.9.5
	 */
	public function delete_generated_terms() {

		// Validate.
		$group = $this->generate_validation( 'page-generator-pro-delete-generated-content' );

		// Run.
		$result = $this->base->get_class( 'generate' )->delete_terms( $group['group_id'], $this->get_trash_delete_per_request_item_limit() );
		if ( is_wp_error( $result ) ) {
			$this->generate_return( $result );
			die();
		}

		// Determine if there are more Terms in this Term Group that need deleting.
		$remaining_terms = $this->base->get_class( 'generate' )->get_generated_term_ids( $group['group_id'] );
		if ( is_wp_error( $remaining_terms ) ) {
			// Error will say there are no more Generated Terms to delete for this Term Group.
			$result = array(
				'has_more' => false,
			);

			// Reset the Last Index Generated.
			$this->base->get_class( 'groups_terms' )->update_last_index_generated( $group['group_id'], 0 );
		} else {
			$result = array(
				'has_more' => true,
			);
		}

		// Return.
		$this->generate_return( $result );

	}

	/**
	 * Removes the generating flag on the Group, as Generation has finished.
	 *
	 * @since   1.9.9
	 */
	public function after_generated_terms() {

		// Validate.
		$group = $this->generate_validation();

		/**
		 * Runs any actions after Generate Terms has finished.
		 *
		 * @since   3.0.7
		 *
		 * @param   int     $group_id   Group ID.
		 * @param   bool    $test_mode  Test Mode.
		 * @param   string  $system     System.
		 */
		do_action( 'page_generator_pro_generate_terms_after', $group['group_id'], false, 'browser' );

		// Run.
		$result = $this->base->get_class( 'groups_terms' )->stop_generation( $group['group_id'] );

		// Return.
		$this->generate_return( $result );

	}

	/**
	 * Runs validation when AJAX calls are made to generate content or terms,
	 * returning the Group ID and Current Index.
	 *
	 * @since   1.6.1
	 *
	 * @param   mixed $action     Nonce Action.
	 * @return  array               Group ID and Current Index
	 */
	private function generate_validation( $action = false ) {

		// Validate nonce.
		if ( $action ) {
			check_ajax_referer( $action, 'nonce' );
		}

		// Sanitize inputs.
		if ( ! isset( $_POST['id'] ) ) {
			wp_send_json_error( __( 'No group ID was specified.', 'page-generator-pro' ) );
			die();
		}

		return array(
			'group_id'                      => absint( $_POST['id'] ),
			'current_index'                 => ( isset( $_POST['current_index'] ) ? absint( $_POST['current_index'] ) : 0 ),
			'last_generated_post_date_time' => ( isset( $_POST['last_generated_post_date_time'] ) ? sanitize_text_field( $_POST['last_generated_post_date_time'] ) : false ),
			'test_mode'                     => ( isset( $_POST['test_mode'] ) ? true : false ),
		);

	}

	/**
	 * Returns the generation result as a JSON error or success
	 *
	 * @since   1.6.1
	 *
	 * @param   mixed $result     WP_Error | array.
	 */
	private function generate_return( $result ) {

		// Return error or success JSON.
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_code() . ': ' . $result->get_error_message() );
		}

		// If here, run routine worked.
		wp_send_json_success( $result );

	}

}
