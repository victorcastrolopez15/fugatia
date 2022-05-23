<?php
/**
 * ChimpRewriter API class.
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Generate spintax using chimprewriter.com
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.3.1
 */
class Page_Generator_Pro_ChimpRewriter extends Page_Generator_Pro_API {

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
	public $api_endpoint = 'http://api.chimprewriter.com/';

	/**
	 * Holds the account URL where users can obtain their API key
	 *
	 * @since   2.3.1
	 *
	 * @var     string
	 */
	public $account_url = 'http://account.chimprewriter.com/ChimpApi';

	/**
	 * Holds the referal URL to use for users wanting to sign up
	 * to the API service.
	 *
	 * @since   2.3.1
	 *
	 * @var     string
	 */
	public $referral_url = 'https://chimprewriter.com/api/?affiliate=wpzinc';

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
	 * @since   2.3.1
	 *
	 * @var     string
	 */
	public $email_address;

	/**
	 * Holds the Application ID, which can be any string up to 100 characters
	 *
	 * @since   2.3.1
	 *
	 * @var     string
	 */
	public $application_id = 'page-generator-pro';

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
	 * @param   string $email_address  Email Address.
	 * @param   string $api_key        API Key.
	 */
	public function set_credentials( $email_address, $api_key ) {

		$this->email_address = $email_address;
		$this->set_api_key( $api_key );

	}

	/**
	 * Returns the valid values for quality levels,
	 * which can be used on API calls.
	 *
	 * @since   2.3.1
	 *
	 * @return  array   Quality Levels
	 */
	public function get_confidence_levels() {

		$quality_levels = array(
			5 => __( 'Best', 'page-generator-pro' ),
			4 => __( 'Better', 'page-generator-pro' ),
			3 => __( 'Good', 'page-generator-pro' ),
			2 => __( 'Average', 'page-generator-pro' ),
			1 => __( 'Any', 'page-generator-pro' ),
		);

		return $quality_levels;

	}

	/**
	 * Returns the valid values for Part of Speech levels,
	 * which can be used on API calls.
	 *
	 * @since   2.3.1
	 *
	 * @return  array   Quality Levels
	 */
	public function get_part_of_speech_levels() {

		$part_of_speech_levels = array(
			3 => __( 'Full', 'page-generator-pro' ),
			2 => __( 'Loose', 'page-generator-pro' ),
			1 => __( 'Extremely Loose', 'page-generator-pro' ),
			0 => __( 'None', 'page-generator-pro' ),
		);

		return $part_of_speech_levels;

	}

	/**
	 * Returns a spintax version of the given non-spintax text, that can be later processed.
	 *
	 * @since   2.3.1
	 *
	 * @param   string $text               Original non-spintax Text.
	 * @param   array  $params             Spin Parameters.
	 *     int     $quality                    Synonym Replacement Quality (default: 4) (see get_quality_levels() for valid values).
	 *     int     $phrasequality              Phrase Replacement Quality (default: 3) (see get_quality_levels() for valid values).
	 *     int     $posmatch                   Required Part of Speech Match (default: 3) (see get_part_of_speech_levels() for valid values).
	 *     string  $language                   Two letter language code (en only at this time).
	 *     bool    $sentencerewrite            Rewrite Sentences (default: 0).
	 *     bool    $grammarcheck               Check Grammar (default: 0).
	 *     bool    $reorderparagraphs          Reorder Paragraphs (default: 0).
	 *     bool    $spinwithinspin             Spin within existing Spintax (default: 0).
	 *     bool    $spintidy                   Fix common type grammar mistakes (a/an) (default: 1).
	 *     int     $replacefrequency           nth words spun (default: 1).
	 *     int     $maxsyns                    Maximum Number of Synonyms to use for word/phrase (default: 10).
	 *     int     $excludeoriginal            Exclude Original word from result (default: 0).
	 *     int     $instantunique              Replace letters with similar looking chars for copyscape validation (default: 0).
	 *     int     $maxspindepth               Maximum Spin Level Deptch (default: 0 = no limit).
	 * @param   mixed  $protected_words    Protected Words not to spin (false | array).
	 * @return  mixed   WP_Error | string    Error | Text with Spintax
	 */
	public function chimprewrite( $text, $params = array(), $protected_words = false ) {

		return $this->response(
			$this->post(
				'ChimpRewrite',
				array_merge(
					$params,
					array(
						'text'           => $text,
						'protectedterms' => ( $protected_words !== false ? implode( ',', $protected_words ) : '' ),
						'tagprotect'     => '[|]{|}',
						'email'          => $this->email_address,
						'apikey'         => $this->api_key,
						'aid'            => $this->application_id,
					)
				)
			)
		);

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
				'page_generator_pro_chimprewriter_error',
				sprintf(
					/* translators: Error message */
					__( 'ChimpRewriter: %s', 'page-generator-pro' ),
					$response->get_error_message()
				)
			);
		}

		// Return an error if the status is missing.
		if ( ! isset( $response->status ) ) {
			return new WP_Error(
				'page_generator_pro_chimprewriter_error',
				__( 'ChimpRewriter: Unable to determine success or failure of request.', 'page-generator-pro' )
			);
		}

		// Return an error if the status isn't success.
		if ( $response->status !== 'success' ) {
			return new WP_Error(
				'page_generator_pro_chimprewriter_error',
				sprintf(
					/* translators: %1$s: Status result, %2$s: Error message */
					__( 'ChimpRewriter: %1$s: %2$s', 'page-generator-pro' ),
					$response->status,
					$response->output
				)
			);
		}

		// Get output.
		$output = trim( stripslashes( $response->output ) );

		// If output is empty, reorder paragraphs causes this.
		if ( empty( $output ) ) {
			return new WP_Error(
				'page_generator_pro_chimprewriter_error',
				__( 'ChimpRewriter could not spin the content. If the text is short, consider disabling the "Change Phrase and Sentence Structure" option.', 'page-generator-pro' )
			);
		}

		// Return output.
		return $output;

	}

}
