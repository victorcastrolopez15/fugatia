<?php
/**
 * Spin Rewriter API Class.
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Generate spintax using spinrewriter.com
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.2.9
 */
class Page_Generator_Pro_Spin_Rewriter extends Page_Generator_Pro_API {

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
	public $api_endpoint = 'http://www.spinrewriter.com/action/api';

	/**
	 * Holds the account URL where users can obtain their API key
	 *
	 * @since   2.2.9
	 *
	 * @var     string
	 */
	public $account_url = 'https://www.spinrewriter.com/cp-api';

	/**
	 * Holds the referal URL to use for users wanting to sign up
	 * to Spin Rewriter's service.
	 *
	 * @since   2.2.9
	 *
	 * @var     string
	 */
	public $referral_url = 'https://www.spinrewriter.com/?ref=2c883';

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
	 * Returns the valid values for confidence levels,
	 * which can be used on API calls.
	 *
	 * @since   2.2.9
	 *
	 * @return  array   Confidence Levels
	 */
	public function get_confidence_levels() {

		$confidence_levels = array(
			'low'    => __( 'Low', 'page-generator-pro' ),
			'medium' => __( 'Medium', 'page-generator-pro' ),
			'high'   => __( 'High', 'page-generator-pro' ),
		);

		return $confidence_levels;

	}

	/**
	 * Returns the number of made and remaining API calls for the
	 * 24 hour period
	 *
	 * @since   2.2.9
	 *
	 * @return  mixed   WP_Error | array
	 */
	public function api_quota() {

		return $this->response(
			$this->post(
				array(
					'action'        => 'api_quota',
					'email_address' => $this->email_address,
					'api_key'       => $this->api_key,
				)
			)
		);

	}

	/**
	 * Returns a spintax version of the given non-spintax text, that can be later processed.
	 *
	 * @since   2.2.9
	 *
	 * @param   string $text               Original non-spintax Text.
	 * @param   array  $params             Spin Parameters.
	 *     string  $confidence_level           Confidence Level (low, medium, high).
	 *     bool    $auto_protected_terms       Don't spin capitalized words.
	 *     bool    $nested_spintax             Build Nested Spintax.
	 *     bool    $auto_sentences             Spin Sentences.
	 *     bool    $auto_paragraphs            Spin Paragraphs.
	 *     bool    $auto_new_paragraphs        Add Paragraphs.
	 *     bool    $auto_sentence_structure    Change Sentence Structure.
	 * @param   mixed  $protected_words    Protected Words not to spin (false | array).
	 * @return  mixed   WP_Error | string    Error | Text with Spintax
	 */
	public function text_with_spintax( $text, $params, $protected_words = false ) {

		// Build params.
		$params = array_merge(
			$params,
			array(
				'action'          => 'text_with_spintax',
				'text'            => $text,
				'protected_terms' => ( $protected_words !== false ? implode( "\n", $protected_words ) : '' ),
				'spintax_format'  => '{|}',
				'email_address'   => $this->email_address,
				'api_key'         => $this->api_key,
			)
		);

		// Convert boolean to true/false strings, as required by https://www.spinrewriter.com/cp-api.
		foreach ( $params as $key => $value ) {
			if ( ! $value || empty( $value ) ) {
				$params[ $key ] = 'false';
			}
			if ( $value == '1' ) { // phpcs:ignore
				$params[ $key ] = 'true';
			}
		}

		// Send request.
		$result = $this->response(
			$this->post( '', $params )
		);

		// Bail if an error.
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		// Clean up the response, which will have some erronous whitespaces in shortcodes.
		$spintax_content = str_replace( '=" ', '="', $result->response );
		$spintax_content = str_replace( '"%  ', '"%', $spintax_content );

		// Return text with spintax.
		return $spintax_content;

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

		// Bail if an error.
		if ( is_wp_error( $response ) ) {
			return new WP_Error(
				'page_generator_pro_spin_rewriter_error',
				sprintf(
					/* translators: Error message */
					__( 'SpinRewriter: %s', 'page-generator-pro' ),
					$response->get_error_message()
				)
			);
		}

		// Bail if the status is missing.
		if ( ! isset( $response->status ) ) {
			return new WP_Error(
				'page_generator_pro_spin_rewriter_error',
				__( 'Spin Rewriter: Unable to determine success or failure of request.', 'page-generator-pro' )
			);
		}

		// Bail if the status isn't OK.
		if ( $response->status !== 'OK' ) {
			return new WP_Error(
				'page_generator_pro_spin_rewriter_error',
				sprintf(
					/* translators: %1$s: Status, %2$s: Error message */
					__( 'Spin Rewriter: %1$s: %2$s', 'page-generator-pro' ),
					$response->status,
					$response->response
				)
			);
		}

		// Return data.
		return $response;

	}

}
