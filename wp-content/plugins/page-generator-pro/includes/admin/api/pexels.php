<?php
/**
 * Pexels API Class.
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Fetch images from Pexels based on given criteria.
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.2.9
 */
class Page_Generator_Pro_Pexels extends Page_Generator_Pro_API {

	/**
	 * Holds the API endpoint
	 *
	 * @since   2.2.9
	 *
	 * @var     string
	 */
	public $api_endpoint = 'https://api.pexels.com/';

	/**
	 * Holds the API Key
	 *
	 * @since   2.2.9
	 *
	 * @var     string
	 */
	public $api_key = '563492ad6f9170000100000113545ff30aa14515888b11a213970c6f';

	/**
	 * Returns an array of image orientations supported
	 * by the API.
	 *
	 * @since   2.2.9
	 *
	 * @return  array   Supported Image Orientations
	 */
	public function get_image_orientations() {

		return array(
			0           => __( 'Any', 'page-generator-pro' ),
			'portrait'  => __( 'Portrait', 'page-generator-pro' ),
			'landscape' => __( 'Landscape', 'page-generator-pro' ),
		);

	}

	/**
	 * Searches photos based on the given query
	 *
	 * @since   2.2.9
	 *
	 * @param   string $query          Search Term(s).
	 * @param   string $size           Image Size (original, large, large2x, medium, small, tiny).
	 * @param   mixed  $orientation    Image Orientation (false, portrait, landscape).
	 * @param   int    $per_page       Number of Images to Return.
	 * @param   int    $page           Pagination Page Offset.
	 * @return  mixed                   WP_Error | array
	 */
	public function photos_search( $query = false, $size = 'original', $orientation = false, $per_page = 80, $page = 1 ) {

		// Perform search.
		$results = $this->search( $query, $size, $orientation, $per_page, $page );

		// Bail if an error occured.
		if ( is_wp_error( $results ) ) {
			return new WP_Error(
				'page_generator_pro_pexels_error',
				sprintf(
					/* translators: Error message */
					__( 'Pexels: photos_search(): %s', 'page-generator-pro' ),
					$results->get_error_message()
				)
			);
		}

		// Determine whether to fetch by orientation or size.
		$photo_type = ( $orientation !== false && $orientation !== '0' ? $orientation : $size );

		// Parse results.
		$images = array();
		foreach ( $results->photos as $photo ) {
			// Creator.
			if ( isset( $photo->photographer ) ) {
				/* translators: Photographer's Name */
				$creator = sprintf( __( '%s on Pexels', 'page-generator-pro' ), $photo->photographer );
			} else {
				$creator = false;
			}

			$images[] = array(
				// original, large, large2x, medium, small, portrait, landscape, tiny.
				'url'             => $photo->src->{ $photo_type },
				'title'           => $photo->photographer,

				// Credits.
				'source'          => $photo->url,
				'creator'         => $creator,
				'creator_url'     => ( isset( $photo->photographer_url ) ? $photo->photographer_url : false ),
				'license'         => false,
				'license_version' => false,
				'license_url'     => false,
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
	 * @param   string $size           Image Size (original, large, large2x, medium, small, tiny).
	 * @param   mixed  $orientation    Image Orientation (false, portrait, landscape).
	 * @param   int    $per_page       Number of Images to Return.
	 * @param   int    $page           Pagination Page Offset.
	 * @return  mixed                   WP_Error | array
	 */
	public function page_count( $query = false, $size = 'original', $orientation = false, $per_page = 80, $page = 1 ) {

		// Perform search.
		$results = $this->search( $query, $size, $orientation, $per_page, $page );

		// Bail if an error occured.
		if ( is_wp_error( $results ) ) {
			return new WP_Error(
				'page_generator_pro_pexels_error',
				sprintf(
					/* translators: Error message */
					__( 'Pexels: page_count(): %s', 'page-generator-pro' ),
					$results->get_error_message()
				)
			);
		}

		// If total results exceeds 8,000, reduce it as this results in a page count too high
		// that fails when calling photos_search(), even though there might be results.
		if ( $results->total_results > 8000 ) {
			$total_results = 8000;
		} else {
			$total_results = $results->total_results;
		}

		return (int) ceil( $total_results / $results->per_page );

	}

	/**
	 * Searches photos based on the given query
	 *
	 * @since   2.2.9
	 *
	 * @param   string $query          Search Term(s).
	 * @param   string $size           Image Size (original, large, large2x, medium, small, tiny).
	 * @param   mixed  $orientation    Image Orientation (false, portrait, landscape).
	 * @param   int    $per_page       Number of Images to Return.
	 * @param   int    $page           Pagination Page Offset.
	 * @return  mixed                   WP_Error | array
	 */
	private function search( $query = false, $size = 'original', $orientation = false, $per_page = 80, $page = 1 ) {

		// Set HTTP headers.
		$this->set_headers(
			array(
				'Authorization' => $this->api_key,
			)
		);

		// Build array of arguments  .
		$args = array(
			'query'    => $query,
			'per_page' => $per_page,
			'page'     => $page,
		);

		/**
		 * Filters the API arguments to send to the Pexels /search endpoint
		 *
		 * @since   2.2.9
		 *
		 * @param   array   $args           API arguments.
		 * @param   string  $query          Search Term(s).
		 * @param   string  $size           Image Size (original, large, large2x, medium, small, portrait, landscape, tiny).
		 * @param   int     $per_page       Number of Images to Return.
		 * @param   int     $page           Pagination Page Offset.
		 */
		$args = apply_filters( 'page_generator_pro_pexels_photos_search_args', $args, $query, $size, $orientation, $per_page, $page );

		// Run the query.
		$results = $this->get( 'v1/search', $args );

		// Bail if an error occured.
		if ( is_wp_error( $results ) ) {
			return $results;
		}

		// Bail if an error is in the response.
		if ( isset( $results->error ) ) {
			return new WP_Error(
				'page_generator_pro_pexels_error',
				$results->error
			);
		}

		// Bail if no results were found.
		if ( ! $results->total_results ) {
			return new WP_Error(
				'page_generator_pro_pexels_error',
				__( 'No results were found for the given search criteria.', 'page-generator-pro' )
			);
		}

		// Return results.
		return $results;

	}

}
