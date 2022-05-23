<?php
/**
 * YouTube API Class.
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Fetch videos from YouTube based on given criteria.
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 1.2.0
 */
class Page_Generator_Pro_Youtube extends Page_Generator_Pro_API {

	/**
	 * Holds the base object.
	 *
	 * @since   1.9.8
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Holds the API endpoint
	 *
	 * @since   1.2.0
	 *
	 * @var     string
	 */
	public $api_endpoint = 'https://www.googleapis.com/youtube/v3';

	/**
	 * Holds the API Key
	 *
	 * @since   1.2.0
	 *
	 * @var     string
	 */
	public $api_key = 'AIzaSyC4IwPk9Iyp1uALNkj5WTblmQCO9Dr7ZCo';

	/**
	 * Constructor.
	 *
	 * @since   1.9.8
	 *
	 * @param   object $base    Base Plugin Class.
	 */
	public function __construct( $base ) {

		// Store base class.
		$this->base = $base;

	}

	/**
	 * Search for YouTube Videos for the given keyword and optional
	 * latitude / longitude.
	 *
	 * @since   1.2.0
	 *
	 * @param   string $keyword    Search Terms.
	 * @param   array  $lat_lng    Latitude and Longitude.
	 * @return  mixed               WP_Error | array
	 */
	public function search( $keyword, $lat_lng = false ) {

		// Build array of arguments.
		$args = array(
			'key'        => $this->api_key,
			'type'       => 'video',
			'q'          => $keyword,
			'part'       => 'snippet',
			'maxResults' => 50,
		);

		// If a latitude and longitude is supplied, add it to the query.
		if ( $lat_lng !== false ) {
			$args['location']       = $lat_lng['latitude'] . ',' . $lat_lng['longitude'];
			$args['locationRadius'] = '10mi';
		}

		// Send request.
		$results = $this->response(
			$this->get( 'search?' . http_build_query( $args ) )
		);

		// Bail if an error occured.
		if ( is_wp_error( $results ) ) {
			return $results;
		}

		// Parse results.
		$videos = array();
		foreach ( $results->items as $video ) {
			$videos[] = array(
				'id'      => $video->id->videoId,
				'url'     => 'https://youtube.com/watch?v=' . $video->id->videoId,
				'title'   => $video->snippet->title,
				'caption' => $video->snippet->description,
			);
		}

		// Return array of videos.
		return $videos;

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
			// Inspect the error data to see if a more detailed reason for the error exists.
			$error_data = $response->get_error_data();

			if ( ! empty( $error_data ) ) {
				$error_data = json_decode( $error_data, true );

				return new WP_Error(
					'page_generator_pro_youtube_error',
					sprintf(
						/* translators: Error message */
						__( 'YouTube: %s', 'page-generator-pro' ),
						$error_data['error']['message']
					)
				);
			}

			// Return standard WP_Error.
			return new WP_Error(
				'page_generator_pro_youtube_error',
				sprintf(
					/* translators: Error message */
					__( 'YouTube: %s', 'page-generator-pro' ),
					$response->get_error_message()
				)
			);
		}

		// Bail if an error exists.
		if ( isset( $response->error ) ) {
			return new WP_Error(
				'page_generator_pro_youtube_error',
				$response->error->code . ': ' . $response->error->message
			);
		}

		// Bail if no results.
		if ( ! count( $response->items ) ) {
			return new WP_Error(
				'page_generator_pro_youtube_error',
				__( 'YouTube: No results found', 'page-generator-pro' )
			);
		}

		// Return successful response data.
		return $response;

	}

}
