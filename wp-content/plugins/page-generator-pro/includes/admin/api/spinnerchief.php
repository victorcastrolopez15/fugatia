<?php
/**
 * Spinnerchief API Class.
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Generate spintax using spinnerchief.com
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.3.1
 */
class Page_Generator_Pro_SpinnerChief extends Page_Generator_Pro_API {

	/**
	 * Holds the base object.
	 *
	 * @since   2.3.1
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Holds the API endpoint
	 *
	 * @since   2.3.1
	 *
	 * @var     string
	 */
	public $api_endpoint = 'http://api.spinnerchief.com:443';

	/**
	 * Holds the user's API key
	 *
	 * @since   2.3.1
	 *
	 * @var     string
	 */
	public $api_key = 'apifed603a55050401ca';

	/**
	 * Holds the account URL where users can obtain their API key
	 *
	 * @since   2.3.1
	 *
	 * @var     string
	 */
	public $account_url = 'http://account.spinnerchief.com/';

	/**
	 * Holds the referal URL to use for users wanting to sign up
	 * to SpinnerChief's service.
	 *
	 * @since   2.3.1
	 *
	 * @var     string
	 */
	public $referral_url = 'http://www.whitehatbox.com/Agents/SSS?code=0vbtYQiezQ69rR4wkFq6AQs9StMsnOWJZae2sjYH%2BH%2B0DfOPc1i%2BBw==';

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
	public $is_json_response = false;

	/**
	 * Holds the username
	 *
	 * @since   2.3.1
	 *
	 * @var     string
	 */
	public $username;

	/**
	 * Holds the password
	 *
	 * @since   2.3.1
	 *
	 * @var     string
	 */
	public $password;

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
	 * Sets the credentials to use for API calls
	 *
	 * @since   2.3.1
	 *
	 * @param   string $username       Username.
	 * @param   string $password       Password.
	 */
	public function set_credentials( $username, $password ) {

		$this->username = $username;
		$this->password = $password;

	}

	/**
	 * Returns a spintax version of the given non-spintax text, that can be later processed.
	 *
	 * @since   2.3.1
	 *
	 * @param   string $text               Original non-spintax Text.
	 * @param   array  $params             Spin Parameters.
	 *     int     $spinfreq                   Spin Frequency.
	 *     int     $wordquality                Word Quality.
	 *                                         0: Best Thesaurus.
	 *                                         1: Better Thesaurus.
	 *                                         2: Good Thesaurus.
	 *                                         3: All Thesaurus.
	 *                                         9: Everyone's Favourite.
	 *     string  $thesaurus                  Thesaurus Language to Use.
	 *     bool    $pos                        Use Part of Speech Analysis.
	 *     bool    $UseGrammarAI               Use Grammar Correction.
	 *     int     $replacetype                Replacement Method.
	 *                                         0：Replace phrase and word.
	 *                                         1：Only replace phrase.
	 *                                         2: Only replace word.
	 *                                         3: Replace phrase first, then replace word till the article passes copyscape.
	 *                                         4: Spin the article to most unique.
	 *                                         5: Spin the article to most readable.
	 * @param   mixed  $protected_words    Protected Words not to spin (false | array).
	 * @return  mixed   WP_Error | string    Error | Text with Spintax
	 */
	public function text_with_spintax( $text, $params = array(), $protected_words = false ) {

		// Build params.
		$params['tagprotect'] = '[],(),<- ->';
		$params['username']   = $this->username;
		$params['password']   = $this->password;
		$params['apikey']     = $this->api_key;

		if ( $protected_words !== false ) {
			$params['protectwords'] = implode( ',', $protected_words );
		}

		// Send request.
		$result = $this->response(
			$this->post( http_build_query( $params ), base64_encode( $text ) ) // phpcs:ignore
		);

		// Bail if an error.
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		// Cleanup the response.
		// Replace ) } with )}.
		$result = str_replace( ') }', ')}', $result );
		$result = str_replace( '> ', '>', $result );
		$result = str_replace( ' </', '</', $result );

		// Return text with spintax.
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
				'page_generator_pro_spinnerchief_error',
				sprintf(
					/* translators: Error message */
					__( 'SpinnerChief: %s', 'page-generator-pro' ),
					$response->get_error_message()
				)
			);
		}

		// Decode body.
		$data = base64_decode( $response ); // phpcs:ignore

		// If the response body starts with 'error=', there's an error.
		if ( strpos( $data, 'error=' ) !== false ) {
			// Contextualize error message to make it more understandable.
			if ( strpos( $data, 'query time reach limit' ) !== false ) {
				$message = __( 'You\'ve reached the daily limit for the number of spin requests that can be made to the SpinnerChief API.', 'page-generator-pro' );
			} else {
				$message = substr( $data, strpos( $data, 'error=' ) + 6 );
			}

			return new WP_Error(
				'page_generator_pro_spinnerchief_error',
				sprintf(
					/* translators: Error message */
					__( 'SpinnerChief: %s', 'page-generator-pro' ),
					$message
				)
			);
		}

		// Return data.
		return $data;

	}

}
