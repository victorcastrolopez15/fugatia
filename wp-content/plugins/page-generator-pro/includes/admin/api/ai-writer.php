<?php
/**
 * AI Writer API class.
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Generate articles based on keywords using ai-writer.com
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.3.1
 */
class Page_Generator_Pro_AI_Writer extends Page_Generator_Pro_API {

	/**
	 * Holds the base object.
	 *
	 * @since   2.8.9
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Holds the API endpoint
	 *
	 * @since   2.8.9
	 *
	 * @var     string
	 */
	public $api_endpoint = 'https://panel.ai-writer.com/aiw/apiendpoint2';

	/**
	 * Holds the account URL where users can obtain their API key
	 *
	 * @since   2.8.9
	 *
	 * @var     string
	 */
	public $account_url = 'https://panel.ai-writer.com/aiw/apidocumentationsite/';

	/**
	 * Holds the referal URL to use for users wanting to sign up
	 * to the API service.
	 *
	 * @since   2.8.9
	 *
	 * @var     string
	 */
	public $referral_url = 'https://ai-writer.com';

	/**
	 * Constructor.
	 *
	 * @since   2.3.1
	 *
	 * @param   object $base    Base Plugin Class.
	 */
	public function __construct( $base ) {

		// Store base class.
		$this->base = $base;

	}

	/**
	 * Returns subscription information
	 *
	 * @since   2.8.9
	 *
	 * @return  mixed               WP_Error | object
	 */
	public function get_subscription_info() {

		return $this->response(
			$this->get(
				'get_subscription_info',
				array(
					'api_key' => $this->api_key,
				)
			)
		);

	}

	/**
	 * Returns a list of all research requests
	 *
	 * @since   2.8.9
	 *
	 * @param   int $offset     List Offset.
	 * @param   int $limit      Limit (1 to 100).
	 * @return  mixed               WP_Error | object
	 */
	public function list_research_requests( $offset = 0, $limit = 100 ) {

		return $this->response(
			$this->get(
				'list_research_requests/' . $offset . '/' . $limit,
				array(
					'api_key' => $this->api_key,
				)
			)
		);

	}

	/**
	 * Submits a new research request for the given topic
	 *
	 * @since   2.8.9
	 *
	 * @param   string $topic          Topic.
	 * @return  mixed                   WP_Error | object
	 */
	public function put_research_request( $topic ) {

		return $this->response(
			$this->post(
				'put_research_request/' . rawurlencode( $topic ) . '?' . http_build_query(
					array(
						'api_key' => $this->api_key,
					)
				)
			)
		);

	}

	/**
	 * Returns the research request's result for the given ID
	 *
	 * @since   2.8.9
	 *
	 * @param   string $id             ID.
	 * @return  mixed                   WP_Error | object
	 */
	public function get_research_result( $id ) {

		$result = $this->response(
			$this->get(
				'get_research_result/' . rawurlencode( $id ) . '?' . http_build_query(
					array(
						'api_key' => $this->api_key,
					)
				)
			)
		);

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( $result->request->error_occurred ) {
			return new WP_Error(
				'page_generator_pro_ai_writer_error',
				sprintf(
					/* translators: %1$s: Resource ID, %2$s: Query */
					__( 'An error occured when attempting to perform research for resource ID %1$s, topic %2$s', 'page-generator-pro' ),
					$id,
					$result->request->query
				)
			);
		}

		return $result;

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
				'page_generator_pro_ai_writer_error',
				sprintf(
					/* translators: Error message */
					__( 'AI Writer: %s', 'page-generator-pro' ),
					$response->get_error_message()
				)
			);
		}

		// If the response wasn't successful, bail.
		if ( isset( $response->success ) && ! $response->success ) {
			return new WP_Error(
				'page_generator_pro_ai_writer_error',
				__( 'AI Writer: An error occured', 'page-generator-pro' )
			);
		}

		return $response;

	}

}
