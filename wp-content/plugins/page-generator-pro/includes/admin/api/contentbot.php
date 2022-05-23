<?php
/**
 * ContentBot.ai API Class.
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Generate articles based on keywords using ContentBot.ai
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 3.5.5
 */
class Page_Generator_Pro_ContentBot extends Page_Generator_Pro_API {

	/**
	 * Holds the base object.
	 *
	 * @since   3.5.5
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Holds the API endpoint
	 *
	 * @since   3.5.5
	 *
	 * @var     string
	 */
	public $api_endpoint = 'https://contentbot.us-3.evennode.com/api/v1';

	/**
	 * Holds the account URL where users can obtain their API key
	 *
	 * @since   3.5.5
	 *
	 * @var     string
	 */
	public $account_url = 'https://contentbot.ai/app/profile.php';

	/**
	 * Holds the referal URL to use for users wanting to sign up
	 * to the API service.
	 *
	 * @since   3.5.5
	 *
	 * @var     string
	 */
	public $referral_url = 'https://contentbot.ai?fpr=tim17';

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
	 * Returns content for the given topic.
	 *
	 * @since   3.5.5
	 *
	 * @param   string $topic              Topic.
	 * @param   string $tone               Tone (professional,friendly,bold,playful,first person,third person).
	 * @param   string $formality          Formality (default,more,less).
	 * @param   string $language_service   Language Service (google,deepl,watson).
	 * @param   string $source_lang        Original Text's Language (two-character language code).
	 * @param   string $target_lang        Target Language (two-character language code).
	 * @return  mixed   WP_Error | string   Error | Rewritten Text
	 */
	public function get_topic_content( $topic, $tone = 'professional', $formality = 'default', $language_service = 'google', $source_lang = 'en', $target_lang = 'en' ) {

		// Build params.
		$params = array(
			'hash'          => $this->api_key,
			'ptype'         => 'editor', // Always editor.
			'pcompletions'  => 1, // Always 1.
			'longformFlag'  => 1, // Always 1.
			'psubtype'      => 1, // Always 1.
			'wc'            => 75, // 15, 25, 50, 75 based on outputlength e.g. if 25, outputlength = 2.
			'outputlength'  => 4, // 1, 2, 3, 4 based on wc e.g. if 4, wc = 75.

			// Customisable params.
			'pdesc'         => $topic,
			'ptone'         => $tone,
			'planservice'   => $language_service,
			'psourcelan'    => $source_lang,
			'lang'          => $target_lang,
			'planformality' => $formality,
		);

		// Send request.
		$result = $this->response(
			$this->get( 'input', $params )
		);

		// Bail if an error.
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		// Return array of paragraphs.
		return explode( "\n\n", trim( $result->output[0]->text ) );

	}

	/**
	 * Returns supported tonalities for ContentBot API calls.
	 *
	 * @since   3.5.5
	 *
	 * @return  array    Tonalities
	 */
	public function get_tonalities() {

		return array(
			'professional' => __( 'Professional', 'page-generator-pro' ),
			'friendly'     => __( 'Friendly', 'page-generator-pro' ),
			'bold'         => __( 'Bold', 'page-generator-pro' ),
			'playful'      => __( 'Playful', 'page-generator-pro' ),
			'first person' => __( 'First Person', 'page-generator-pro' ),
			'third person' => __( 'Third Person', 'page-generator-pro' ),
		);

	}

	/**
	 * Returns supported formalities for ContentBot API calls.
	 *
	 * @since   3.5.5
	 *
	 * @return  array    Formalities
	 */
	public function get_formalities() {

		return array(
			'default'     => __( 'Default', 'page-generator-pro' ),
			'more formal' => __( 'More Formal', 'page-generator-pro' ),
			'less formal' => __( 'Less Formal', 'page-generator-pro' ),
		);

	}

	/**
	 * Returns supported languages for ContentBot API calls.
	 *
	 * @since   3.5.5
	 *
	 * @return  array    Languages
	 */
	public function get_languages() {

		return array(
			'en'    => 'English',
			'af'    => 'Afrikaans',
			'sq'    => 'Albanian',
			'am'    => 'Amharic',
			'ar'    => 'Arabic',
			'hy'    => 'Armenian',
			'az'    => 'Azerbaijani',
			'eu'    => 'Basque',
			'be'    => 'Belarusian',
			'bn'    => 'Bengali',
			'bs'    => 'Bosnian',
			'bg'    => 'Bulgarian',
			'ca'    => 'Catalan',
			'ceb'   => 'Cebuano',
			'ny'    => 'Chichewa',
			'zh'    => 'Chinese (Simplified)',
			'zh-TW' => 'Chinese (Traditional)',
			'co'    => 'Corsican',
			'hr'    => 'Croatian',
			'cs'    => 'Czech',
			'da'    => 'Danish',
			'nl'    => 'Dutch',
			'eo'    => 'Esperanto',
			'et'    => 'Estonian',
			'tl'    => 'Filipino',
			'fi'    => 'Finnish',
			'fr'    => 'French',
			'fy'    => 'Frisian',
			'gl'    => 'Galician',
			'ka'    => 'Georgian',
			'de'    => 'German',
			'el'    => 'Greek',
			'gu'    => 'Gujarati',
			'ht'    => 'Haitian Creole',
			'ha'    => 'Hausa',
			'haw'   => 'Hawaiian',
			'iw'    => 'Hebrew',
			'hi'    => 'Hindi',
			'hmn'   => 'Hmong',
			'hu'    => 'Hungarian',
			'is'    => 'Icelandic',
			'ig'    => 'Igbo',
			'id'    => 'Indonesian',
			'ga'    => 'Irish',
			'it'    => 'Italian',
			'ja'    => 'Japanese',
			'jw'    => 'Javanese',
			'kn'    => 'Kannada',
			'kk'    => 'Kazakh',
			'km'    => 'Khmer',
			'rw'    => 'Kinyarwanda',
			'ko'    => 'Korean',
			'ku'    => 'Kurdish (Kurmanji)',
			'ky'    => 'Kyrgyz',
			'lo'    => 'Lao',
			'la'    => 'Latin',
			'lv'    => 'Latvian',
			'lt'    => 'Lithuanian',
			'lb'    => 'Luxembourgish',
			'mk'    => 'Macedonian',
			'mg'    => 'Malagasy',
			'ms'    => 'Malay',
			'ml'    => 'Malayalam',
			'mt'    => 'Maltese',
			'mi'    => 'Maori',
			'mr'    => 'Marathi',
			'mn'    => 'Mongolian',
			'my'    => 'Myanmar (Burmese)',
			'ne'    => 'Nepali',
			'no'    => 'Norwegian',
			'or'    => 'Odia (Oriya)',
			'ps'    => 'Pashto',
			'fa'    => 'Persian',
			'pl'    => 'Polish',
			'pt'    => 'Portuguese',
			'pa'    => 'Punjabi',
			'ro'    => 'Romanian',
			'ru'    => 'Russian',
			'sm'    => 'Samoan',
			'gd'    => 'Scots Gaelic',
			'sr'    => 'Serbian',
			'st'    => 'Sesotho',
			'sn'    => 'Shona',
			'sd'    => 'Sindhi',
			'si'    => 'Sinhala',
			'sk'    => 'Slovak',
			'sl'    => 'Slovenian',
			'so'    => 'Somali',
			'es'    => 'Spanish',
			'su'    => 'Sundanese',
			'sw'    => 'Swahili',
			'sv'    => 'Swedish',
			'tg'    => 'Tajik',
			'ta'    => 'Tamil',
			'tt'    => 'Tatar',
			'te'    => 'Telugu',
			'th'    => 'Thai',
			'tr'    => 'Turkish',
			'tk'    => 'Turkmen',
			'uk'    => 'Ukrainian',
			'ur'    => 'Urdu',
			'ug'    => 'Uyghur',
			'uz'    => 'Uzbek',
			'vi'    => 'Vietnamese',
			'cy'    => 'Welsh',
			'xh'    => 'Xhosa',
			'yi'    => 'Yiddish',
			'yo'    => 'Yoruba',
			'zu'    => 'Zulu',
		);

	}

	/**
	 * Inspects the response from the API call, returning an error
	 * or data
	 *
	 * @since   3.5.5
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
					__( 'ContentBot: %s', 'page-generator-pro' ),
					$response->get_error_message()
				)
			);
		}

		return $response;

	}

}
