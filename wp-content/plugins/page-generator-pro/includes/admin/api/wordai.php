<?php
/**
 * WordAI API Class.
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Generate spintax using wordai.com
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.2.9
 */
class Page_Generator_Pro_WordAI extends Page_Generator_Pro_API {

	/**
	 * Holds the base object.
	 *
	 * @since   2.2.9
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Holds the API endpoint
	 *
	 * @since   2.2.9
	 *
	 * @var     string
	 */
	public $api_endpoint = 'https://wai.wordai.com/api/';

	/**
	 * Holds the account URL where users can obtain their API key
	 *
	 * @since   3.3.7
	 *
	 * @var     string
	 */
	public $account_url = 'https://wai.wordai.com/api';

	/**
	 * Holds the referal URL to use for users wanting to sign up
	 *
	 * @since   2.2.9
	 *
	 * @var     string
	 */
	public $referral_url = 'https://wordai.com/?ref=17haci';

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
	 * Holds the user's email address
	 *
	 * @since   2.2.9
	 *
	 * @var     string
	 */
	public $email_address;

	/**
	 * Constructor.
	 *
	 * @since   2.2.9
	 *
	 * @param   object $base    Base Plugin Class.
	 */
	public function __construct( $base ) {

		// Store base class.
		$this->base = $base;

	}

	/**
	 * Sets the credentials to use for API calls
	 *
	 * @since   2.2.9
	 *
	 * @param   string $email_address  Email Address.
	 * @param   string $api_key        API Key.
	 */
	public function set_credentials( $email_address, $api_key ) {

		$this->email_address = $email_address;
		$this->set_api_key( $api_key );

	}

	/**
	 * Returns the valid values for the uniqueness parameter
	 *
	 * @since   3.3.7
	 *
	 * @return  array   Uniqueness Options
	 */
	public function get_confidence_levels() {

		return array(
			1 => __( 'More Conservative', 'page-generator-pro' ),
			2 => __( 'Regular', 'page-generator-pro' ),
			3 => __( 'More Adventurous', 'page-generator-pro' ),
		);

	}

	/**
	 * Returns account details
	 *
	 * @since   3.1.6
	 *
	 * @return  mixed   WP_Error | object
	 */
	public function account() {

		return $this->response(
			$this->post(
				'account',
				array(
					'email' => $this->email_address,
					'key'   => $this->api_key,
				)
			)
		);

	}

	/**
	 * Determines if the WordAI account has enough credits to perform
	 * spintax
	 *
	 * @since   3.1.6
	 *
	 * @return  bool    Account has reached Turing Limit
	 */
	public function has_reached_limit() {

		$result = $this->account();

		// Bail if an error.
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		// Get Turing Usage and Limit.
		if ( $result->{'Turing Usage'} >= $result->{'Turing Limit'} ) {
			return true;
		}

		return false;

	}

	/**
	 * Returns a spintax version of the given non-spintax text, that can be later processed.
	 *
	 * @since   2.2.9
	 *
	 * @param   string $text               Original non-spintax Text.
	 * @param   array  $params             Spin Parameters.
	 *     int    $uniqueness                  How unique the spintax should be, 1 to 3. Higher is less accurate but more unique.
	 * @param   mixed  $protected_words    Protected Words not to spin (false | array). Not supported by WordAI.
	 * @return  mixed   WP_Error | string   Error | Text with Spintax
	 */
	public function text_with_spintax( $text, $params, $protected_words = false ) { // phpcs:ignore

		// Build params.
		$params = array(
			'input'           => htmlentities( $text ),
			'rewrite_num'     => $params['rewrite_num'], // 1 to 10.
			'uniqueness'      => $params['uniqueness'], // 1 to 3.
			'return_rewrites' => false, // Return spintax.
			'email'           => $this->email_address,
			'key'             => $this->api_key,
			'output'          => 'json',
		);

		// Perform request.
		$result = $this->response(
			$this->post( 'rewrite', $params )
		);

		// Bail if an error.
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		// Return text with spintax.
		return $result->text;

	}

	/**
	 * Performs all POST requests using cURL, as wp_remote_post()
	 * results in 403 forbidden errors.
	 *
	 * @since  2.2.9
	 *
	 * @param  string $endpoint   Endpoint.
	 * @param  array  $params     Request Parameters.
	 * @return mixed                WP_Error | object
	 */
	public function post( $endpoint, $params = array() ) {

		// Don't use wp_remote_post(), as it returns a 403 forbidden response.
		$ch = curl_init(); // phpcs:ignore
		curl_setopt_array( // phpcs:ignore
			$ch,
			array(
				CURLOPT_URL            => $this->api_endpoint . $endpoint,
				CURLOPT_POST           => true,
				CURLOPT_POSTFIELDS     => http_build_query( $params ),
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HEADER         => false,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_MAXREDIRS      => 10,
				CURLOPT_CONNECTTIMEOUT => $this->get_timeout(),
				CURLOPT_TIMEOUT        => $this->get_timeout(),
				CURLOPT_SSL_VERIFYPEER => 0,
			)
		);

		// Execute.
		$response  = curl_exec( $ch ); // phpcs:ignore
		$http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE ); // phpcs:ignore
		$error     = curl_error( $ch ); // phpcs:ignore
		curl_close( $ch ); // phpcs:ignore

		// If our error string isn't empty, something went wrong.
		if ( ! empty( $error ) ) {
			return new WP_Error( 'page_generator_pro_wordai_error', $error );
		}

		// Decode JSON.
		return json_decode( $response );

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

		// Bail if the status is an error.
		if ( ! isset( $response->status ) ) {
			return new WP_Error( 'page_generator_pro_wordai_error', __( 'WordAI API: Unable to determine success or failure of request.', 'page-generator-pro' ) );
		}
		if ( $response->status !== 'Success' ) {
			return new WP_Error(
				'page_generator_pro_wordai_error',
				sprintf(
				/* translators: %1$s: Status, %2$s: Error message */
					__( 'WordAI API: %1$s: %2$s', 'page-generator-pro' ),
					$response->status,
					$response->error
				)
			);
		}

		return $response;

	}

}
