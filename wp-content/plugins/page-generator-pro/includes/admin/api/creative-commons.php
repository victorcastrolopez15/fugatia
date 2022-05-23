<?php
/**
 * Creative Commons API Class.
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Fetch images from Creative Commons based on given criteria.
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.6.9
 */
class Page_Generator_Pro_Creative_Commons extends Page_Generator_Pro_API {

	/**
	 * Holds the API endpoint
	 *
	 * @since   2.6.9
	 *
	 * @var     string
	 */
	public $api_endpoint = 'https://api.openverse.engineering';

	/**
	 * Returns an array of aspect ratios (orientations) supported
	 * by the API.
	 *
	 * @since   2.6.9
	 *
	 * @return  array   Supported Image Orientations
	 */
	public function get_image_orientations() {

		return array(
			0        => __( 'Any', 'page-generator-pro' ),
			'tall'   => __( 'Portrait', 'page-generator-pro' ),
			'wide'   => __( 'Landscape', 'page-generator-pro' ),
			'square' => __( 'Square', 'page-generator-pro' ),
		);

	}

	/**
	 * Returns an array of licenses supported by the API
	 *
	 * @since   2.9.9
	 *
	 * @return  array   Supported Licenses
	 */
	public function get_licenses() {

		return array(
			'BY-ND'    => 'BY-ND',
			'BY-NC'    => 'BY-NC',
			'PDM'      => 'PDM',
			'BY-NC-ND' => 'BY-NC-ND',
			'CC0'      => 'CC0',
			'BY-SA'    => 'BY-SA',
			'BY'       => 'BY',
			'BY-NC-SA' => 'BY-NC-SA',
			'BY-ND'    => 'BY-ND',
		);

	}

	/**
	 * Returns an array of sources supported by the API
	 *
	 * @since   2.9.9
	 *
	 * @return  array   Supported Sources
	 */
	public function get_sources() {

		return array(
			'500px'                                       => '500px',
			'animaldiversity'                             => 'animaldiversity',
			'behance'                                     => 'behance',
			'bio_diversity'                               => 'bio_diversity',
			'brooklynmuseum'                              => 'brooklynmuseum',
			'CAPL'                                        => 'CAPL',
			'clevelandmuseum'                             => 'clevelandmuseum',
			'deviantart'                                  => 'deviantart',
			'digitaltmuseum'                              => 'digitaltmuseum',
			'eol'                                         => 'eol',
			'europeana'                                   => 'europeana',
			'flickr'                                      => 'flickr',
			'floraon'                                     => 'floraon',
			'geographorguk'                               => 'geographorguk',
			'iha'                                         => 'iha',
			'mccordmuseum'                                => 'mccordmuseum',
			'met'                                         => 'met',
			'museumsvictoria'                             => 'museumsvictoria',
			'nasa'                                        => 'nasa',
			'nypl'                                        => 'nypl',
			'phylopic'                                    => 'phylopic',
			'rawpixel'                                    => 'rawpixel',
			'rijksmuseum'                                 => 'rijksmuseum',
			'sciencemuseum'                               => 'sciencemuseum',
			'sketchfab'                                   => 'sketchfab',
			'smithsonian_african_american_history_museum' => 'smithsonian_african_american_history_museum',
			'smithsonian_african_art_museum'              => 'smithsonian_african_art_museum',
			'smithsonian_air_and_space_museum'            => 'smithsonian_air_and_space_museum',
			'smithsonian_american_art_museum'             => 'smithsonian_american_art_museum',
			'smithsonian_american_history_museum'         => 'smithsonian_american_history_museum',
			'smithsonian_american_indian_museum'          => 'smithsonian_american_indian_museum',
			'smithsonian_anacostia_museum'                => 'smithsonian_anacostia_museum',
			'smithsonian_cooper_hewitt_museum'            => 'smithsonian_cooper_hewitt_museum',
			'smithsonian_freer_gallery_of_art'            => 'smithsonian_freer_gallery_of_art',
			'smithsonian_gardens'                         => 'smithsonian_gardens',
			'smithsonian_hirshhorn_museum'                => 'smithsonian_hirshhorn_museum',
			'smithsonian_institution_archives'            => 'smithsonian_institution_archives',
			'smithsonian_libraries'                       => 'smithsonian_libraries',
			'smithsonian_national_museum_of_natural_history' => 'smithsonian_national_museum_of_natural_history',
			'smithsonian_portrait_gallery'                => 'smithsonian_portrait_gallery',
			'smithsonian_postal_museum'                   => 'smithsonian_postal_museum',
			'smithsonian_zoo_and_conservation'            => 'smithsonian_zoo_and_conservation',
			'spacex'                                      => 'spacex',
			'statensmuseum'                               => 'statensmuseum',
			'svgsilh'                                     => 'svgsilh',
			'thingiverse'                                 => 'thingiverse',
			'thorvaldsensmuseum'                          => 'thorvaldsensmuseum',
			'wellcome_collection'                         => 'wellcome_collection',
			'wikimedia'                                   => 'wikimedia',
			'woc_tech'                                    => 'woc_tech',
			'wordpress'                                   => 'wordpress',
			'WoRMS'                                       => 'WoRMS',
		);

	}

	/**
	 * Searches photos based on the given query
	 *
	 * @since   2.6.9
	 *
	 * @param   string $query          Search Term(s).
	 * @param   mixed  $orientation    Image Orientation (false, tall, wide, square).
	 * @param   mixed  $licenses       Licenses (false, array of get_licenses()).
	 * @param   mixed  $sources        Source (false, array of get_sources()).
	 * @param   int    $per_page       Number of Images to Return.
	 * @param   int    $page           Pagination Page Offset.
	 * @return  mixed                   WP_Error | array
	 */
	public function photos_search( $query = false, $orientation = false, $licenses = false, $sources = false, $per_page = 500, $page = 1 ) {

		// Perform search.
		$results = $this->search( $query, $orientation, $licenses, $sources, $per_page, $page );

		// Bail if an error occured.
		if ( is_wp_error( $results ) ) {
			return new WP_Error(
				'page_generator_pro_creative_commons_photos_search_error',
				sprintf(
					/* translators: Error message */
					__( 'Creative Commons: photos_search(): %s', 'page-generator-pro' ),
					$results->get_error_message()
				)
			);
		}

		// Parse results.
		$images = array();
		foreach ( $results->results as $photo ) {
			$images[] = array(
				'url'             => $photo->url,
				'title'           => ( isset( $photo->title ) ? $photo->title : false ),

				// Credits.
				'creator'         => ( isset( $photo->creator ) ? $photo->creator : false ),
				'creator_url'     => ( isset( $photo->creator_url ) ? $photo->creator_url : false ),
				'source'          => ( isset( $photo->source ) ? $photo->source : false ),
				'license'         => ( isset( $photo->license ) ? $photo->license : false ),
				'license_version' => ( isset( $photo->license_version ) ? $photo->license_version : false ),
				'license_url'     => ( isset( $photo->license_url ) ? $photo->license_url : false ),
			);
		}

		// Return array of images.
		return $images;

	}

	/**
	 * Returns the total number of pages found for the search parameters
	 *
	 * @since   2.8.4
	 *
	 * @param   string $query          Search Term(s).
	 * @param   mixed  $orientation    Image Orientation (false, tall, wide, square).
	 * @param   mixed  $licenses       Licenses (false, array of get_licenses()).
	 * @param   mixed  $sources        Source (false, array of get_sources()).
	 * @param   int    $per_page       Number of Images to Return.
	 * @param   int    $page           Pagination Page Offset.
	 * @return  mixed                   WP_Error | array
	 */
	public function page_count( $query = false, $orientation = false, $licenses = false, $sources = false, $per_page = 500, $page = 1 ) {

		// Perform search.
		$results = $this->search( $query, $orientation, $licenses, $sources, $per_page, $page );

		// Bail if an error occured.
		if ( is_wp_error( $results ) ) {
			return new WP_Error(
				'page_generator_pro_creative_commons_page_count_error',
				sprintf(
					/* translators: Error message */
					__( 'Creative Commons: page_count(): %s', 'page-generator-pro' ),
					$results->get_error_message()
				)
			);
		}

		// Return the total number of pages, ensuring per_page * number of found pages doesn't exceed 1,000
		// as fetching an image from an index greater than 1,000 silently fails.
		// This is undocumented in the Creative Commons API.
		if ( $per_page * $results->page_count > 1000 ) {
			return ceil( 1000 / $per_page );
		}

		// If the page count is zero, this means there's one page of results.
		// Return 1.
		if ( $results->page_count === 0 ) {
			return 1;
		}

		// Return the total number of pages.
		return (int) $results->page_count;

	}

	/**
	 * Performs an image search
	 *
	 * @since   2.8.4
	 *
	 * @param   string $query          Search Term(s).
	 * @param   mixed  $orientation    Image Orientation (false, tall, wide, square).
	 * @param   mixed  $licenses       Licenses (false, array of get_licenses()).
	 * @param   mixed  $sources        Source (false, array of get_sources()).
	 * @param   int    $per_page       Number of Images to Return.
	 * @param   int    $page           Pagination Page Offset.
	 * @return  mixed                   WP_Error | array
	 */
	private function search( $query = false, $orientation = false, $licenses = false, $sources = false, $per_page = 500, $page = 1 ) { // phpcs:ignore

		// Set HTTP headers.
		$this->set_headers(
			array(
				'Content-Type' => 'application/json',
			)
		);

		// Build array of arguments   .
		$args = array(
			'q'            => $query,
			'license_type' => implode( ',', array( 'all', 'all-cc', 'commercial', 'modification' ) ),
			'page_size'    => $per_page,
			'page'         => $page,
			'aspect_ratio' => ( $orientation ? $orientation : 'tall,wide,square' ),
		);

		// Add optional arguments.
		if ( $licenses ) {
			$args['license'] = implode( ',', ( ! is_array( $licenses ) ? array( $licenses ) : $licenses ) );
		}
		if ( is_array( $sources ) ) {
			$args['source'] = implode( ',', $sources );

			// If sources is empty, unset it.
			if ( empty( $args['source'] ) ) {
				unset( $args['source'] );
			}
		}

		/**
		 * Filters the API arguments to send to the Creative Commons /images endpoint
		 *
		 * @since   2.6.9
		 *
		 * @param   array   $args           API arguments.
		 * @param   string  $query          Search Term(s).
		 * @param   mixed   $orientation    Image Orientation (false, tall, wide, square).
		 * @param   int     $per_page       Number of Images to Return.
		 * @param   int     $page           Pagination Page Offset.
		 */
		$args = apply_filters( 'page_generator_pro_creative_commons_photos_search_args', $args, $query, $orientation, $per_page, $page );

		// Run the query.
		$results = $this->get( 'v1/images', $args );

		// Bail if an error occured.
		if ( is_wp_error( $results ) ) {
			return $results;
		}

		// Bail if a generic error occured.
		if ( isset( $results->detail ) ) {
			return new WP_Error(
				'page_generator_pro_creative_commons_photos_search',
				$results->detail
			);
		}

		// Bail if no results were found.
		if ( ! isset( $results->result_count ) || ! $results->result_count ) {
			return new WP_Error(
				'page_generator_pro_creative_commons_photos_search',
				__( 'No results were found for the given search criteria.', 'page-generator-pro' )
			);
		}

		// Return results.
		return $results;

	}

}
