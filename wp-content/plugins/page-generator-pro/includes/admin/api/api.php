<?php
/**
 * API Class.
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Used by other API classes to perform POST and GET requests.
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.2.9
 */
class Page_Generator_Pro_API {

	/**
	 * Holds the API endpoint
	 *
	 * @since   2.8.9
	 *
	 * @var     string
	 */
	public $api_endpoint;

	/**
	 * Holds the user's API key
	 *
	 * @since   2.8.9
	 *
	 * @var     string
	 */
	public $api_key;

	/**
	 * Holds the account URL where users can obtain their API key
	 *
	 * @since   2.8.9
	 *
	 * @var     string
	 */
	public $account_url;

	/**
	 * Holds the referal URL to use for users wanting to sign up
	 * to the API service.
	 *
	 * @since   2.8.9
	 *
	 * @var     string
	 */
	public $referral_url;

	/**
	 * Holds HTTP Headers to include in requests
	 *
	 * @since   2.2.9
	 *
	 * @var     array
	 */
	public $headers = array();

	/**
	 * Holds the user-agent string to use when making requests
	 *
	 * @since   2.2.9
	 *
	 * @var     array
	 */
	public $user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.131 Safari/537.36';

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
	public $is_json_request = true;

	/**
	 * Holds the flag determining if the response data should be an encoded
	 * JSON string
	 *
	 * If true, the body response data is JSON decoded and returned as an array
	 *
	 * If false, the body response data is returned
	 *
	 * @since   2.8.9
	 *
	 * @var     bool
	 */
	public $is_json_response = true;

	/**
	 * Returns the URL where the user can get their API key
	 *
	 * @since   2.8.9
	 *
	 * @return  string  Account URL
	 */
	public function get_account_url() {

		return $this->account_url;

	}

	/**
	 * Returns the URL where the user can register, if they
	 * do not yet have an account
	 *
	 * @since   2.8.9
	 *
	 * @return  string  Registration URL
	 */
	public function get_registration_url() {

		return $this->referral_url;

	}

	/**
	 * Overrides the default API Key with the supplied API Key
	 *
	 * @since   2.2.9
	 *
	 * @param   string $api_key        API Key.
	 */
	public function set_api_key( $api_key ) {

		$this->api_key = $api_key;

	}

	/**
	 * Sets headers to include in the request
	 *
	 * @since   2.2.9
	 *
	 * @param   array $headers     HTTP Headers.
	 */
	public function set_headers( $headers ) {

		$this->headers = $headers;

	}

	/**
	 * Sanitizes API arguments, by removing false or empty
	 * arguments in the array.
	 *
	 * @since   2.8.9
	 *
	 * @param   array $args   Arguments.
	 * @return  array           Sanitized Arguments
	 */
	public function sanitize_arguments( $args ) {

		foreach ( $args as $key => $value ) {
			if ( empty( $value ) || ! $value ) {
				unset( $args[ $key ] );
			}
		}

		return $args;

	}

	/**
	 * Performs a GET request
	 *
	 * @since   2.2.9
	 *
	 * @param   string $cmd            Command.
	 * @param   mixed  $params         Params (array|boolean|string).
	 * @return  mixed                   WP_Error | object
	 */
	public function get( $cmd, $params = false ) {

		return $this->request( $cmd, 'get', $params );

	}

	/**
	 * Performs a POST request
	 *
	 * @since  2.2.9
	 *
	 * @param   string $cmd            Command.
	 * @param   mixed  $params         Params (array|boolean|string).
	 * @return  mixed                   WP_Error | object
	 */
	public function post( $cmd, $params = false ) {

		return $this->request( $cmd, 'post', $params );

	}

	/**
	 * Performs a PUT request
	 *
	 * @since   2.8.9
	 *
	 * @param   string $cmd            Command.
	 * @param   mixed  $params         Params (array|boolean|string).
	 * @return  mixed                   WP_Error | object
	 */
	public function put( $cmd, $params = false ) {

		return $this->request( $cmd, 'put', $params );

	}

	/**
	 * Returns the maximum amount of time to wait for
	 * a response to the request before exiting
	 *
	 * @since   2.2.9
	 *
	 * @return  int     Timeout, in seconds
	 */
	public function get_timeout() {

		$timeout = 30;

		/**
		 * Defines the maximum time to allow the API request to run.
		 *
		 * @since   2.2.9
		 *
		 * @param   int     $timeout    Timeout, in seconds
		 */
		$timeout = apply_filters( 'page_generator_pro_api_get_timeout', $timeout );

		return $timeout;

	}

	/**
	 * Main function which handles sending requests to an API using WordPress functions
	 *
	 * @since   2.2.9
	 *
	 * @param   string $cmd            Command (required).
	 * @param   string $method         HTTP Method (optional).
	 * @param   mixed  $params         Params (array|boolean|string).
	 * @return  mixed                   WP_Error | object
	 */
	private function request( $cmd, $method = 'get', $params = array() ) {

		// Send request.
		switch ( $method ) {
			/**
			 * GET
			 */
			case 'get':
				$result = wp_remote_get(
					$this->set_url( $cmd ),
					array(
						'headers'    => $this->headers,
						'body'       => ( $params !== false ? $params : '' ),
						'timeout'    => $this->get_timeout(),
						'user-agent' => $this->user_agent,
						'sslverify'  => false,
					)
				);
				break;

			/**
			 * POST
			 */
			case 'post':
				$result = wp_remote_post(
					$this->set_url( $cmd ),
					array(
						'headers'    => $this->headers,
						'body'       => $this->build_params_string( $params ),
						'timeout'    => $this->get_timeout(),
						'user-agent' => $this->user_agent,
						'sslverify'  => false,
					)
				);
				break;

			/**
			 * PUT
			 */
			case 'put':
				$result = wp_remote_post(
					$this->set_url( $cmd ),
					array(
						'method'     => 'PUT',
						'headers'    => $this->headers,
						'body'       => $this->build_params_string( $params ),
						'timeout'    => $this->get_timeout(),
						'user-agent' => $this->user_agent,
						'sslverify'  => false,
					)
				);
				break;
		}

		// If an error occured, return it now.
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		// Fetch HTTP response code and body.
		$http_response_code = wp_remote_retrieve_response_code( $result );
		$body               = wp_remote_retrieve_body( $result );

		// Check the HTTP response code for possible errors.
		switch ( $http_response_code ) {
			case 400:
			case 403:
			case 500:
				return new WP_Error(
					'page_generator_pro_api_request_error',
					sprintf(
						/* translators: HTTP response code */
						__( 'HTTP Code %s.  Did you enter a valid API key in the Plugin Settings?', 'page-generator-pro' ),
						$http_response_code
					),
					$body
				);
		}

		// If the body is empty, return a generic error.
		if ( is_null( $body ) || empty( $body ) ) {
			return new WP_Error(
				'page_generator_pro_api_error',
				__( 'Response data empty', 'page-generator-pro' )
			);
		}

		// Just return the body if it's not a JSON response.
		if ( ! $this->is_json_response ) {
			return $body;
		}

		// Decode JSON.
		$body = json_decode( $body );

		// If decoding JSON results in a null string, something went wrong.
		if ( is_null( $body ) ) {
			return new WP_Error(
				'page_generator_pro_api_error',
				__( 'Response data not valid JSON', 'page-generator-pro' )
			);
		}

		// Return JSON response object.
		return $body;

	}

	/**
	 * Sets the full URL to request
	 *
	 * @since   2.2.9
	 *
	 * @param   string $cmd    Command.
	 * @return  string          Full URL
	 */
	private function set_url( $cmd ) {

		// If $cmd is empty, bail.
		if ( empty( $cmd ) ) {
			return $this->api_endpoint;
		}

		return $this->api_endpoint . '/' . $cmd;

	}

	/**
	 * Returns a string to send in the body of a request, depending on
	 * whether the data should be JSON encoded or not
	 *
	 * @since   2.8.9
	 *
	 * @param   mixed $params     Params.
	 * @return  string              JSON encoded or query string of $params
	 */
	private function build_params_string( $params = false ) {

		// If no parameters, return a blank string.
		if ( ! $params ) {
			return '';
		}

		// If $params are a string, just return it.
		if ( is_string( $params ) ) {
			return $params;
		}

		// If the request is JSON, encode the params.
		if ( $this->is_json_request ) {
			return wp_json_encode( $params );
		}

		// http_build_query() params.
		return http_build_query( $params );

	}

}
