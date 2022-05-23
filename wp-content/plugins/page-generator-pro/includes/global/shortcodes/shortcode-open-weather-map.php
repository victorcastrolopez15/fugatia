<?php
/**
 * Open Weather Map Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Open Weather Map Dynamic Element
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.5.1
 */
class Page_Generator_Pro_Shortcode_Open_Weather_Map extends Page_Generator_Pro_Shortcode_Base {

	/**
	 * Holds the base object.
	 *
	 * @since   2.5.1
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Constructor
	 *
	 * @since   2.5.1
	 *
	 * @param   object $base    Base Plugin Class.
	 */
	public function __construct( $base ) {

		// Store base class.
		$this->base = $base;

		add_filter( 'page_generator_pro_shortcode_add_shortcodes', array( $this, 'add_shortcode' ) );

	}

	/**
	 * Registers this shortcode / block in Page Generator Pro
	 *
	 * @since   2.5.1
	 *
	 * @param   array $shortcodes     Shortcodes.
	 * @return  array                   Shortcodes
	 */
	public function add_shortcode( $shortcodes ) {

		// Add this shortcode to the array of registered shortcodes.
		$shortcodes[ $this->get_name() ] = array_merge(
			$this->get_overview(),
			array(
				'name'           => $this->get_name(),
				'fields'         => $this->get_fields(),
				'attributes'     => $this->get_attributes(),
				'supports'       => $this->get_supports(),
				'tabs'           => $this->get_tabs(),
				'default_values' => $this->get_default_values(),
			)
		);

		// Return.
		return $shortcodes;

	}

	/**
	 * Returns this shortcode / block's programmatic name.
	 *
	 * @since   2.5.1
	 */
	public function get_name() {

		return 'open-weather-map';

	}

	/**
	 * Returns this shortcode / block's Title, Icon, Categories, Keywords
	 * and properties for registering on generation and requiring CSS/JS.
	 *
	 * @since   2.5.1
	 */
	public function get_overview() {

		return array(
			'title'                       => __( 'Open Weather Map', 'page-generator-pro' ),
			'description'                 => __( 'Displays the weather forecast', 'page-generator-pro' ),
			'icon'                        => $this->base->plugin->url . '/_modules/dashboard/feather/sun.svg',
			'category'                    => $this->base->plugin->name,
			'keywords'                    => array(
				__( 'Open Weather Map', 'page-generator-pro' ),
				__( 'Weather', 'page-generator-pro' ),
				__( 'Map', 'page-generator-pro' ),
			),

			// TinyMCE / QuickTags Modal Width and Height.
			'modal'                       => array(
				'width'  => 600,
				'height' => 290,
			),

			// Register when Generation is running only.
			'register_on_generation_only' => true,

			// Requires CSS and/or JS for output.
			'requires_css'                => false,
			'requires_js'                 => false,

			// Function to call when rendering the shortcode on the frontend.
			'render_callback'             => array( 'shortcode_open_weather_map', 'render' ),

			// Gutenberg: Block Icon in Editor.
			'gutenberg_icon'              => file_get_contents( $this->base->plugin->folder . '/_modules/dashboard/feather/sun.svg' ), /* phpcs:ignore */
		);

	}

	/**
	 * Returns this block's Attributes and default values for Gutenberg.
	 *
	 * @since   3.6.3
	 */
	public function get_attributes() {

		return array(
			'forecast_type'        => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'forecast_type' ) ? '' : $this->get_default_value( 'forecast_type' ) ),
			),
			'location'             => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'location' ) ? '' : $this->get_default_value( 'location' ) ),
			),
			'country_code'         => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'country_code' ) ? '' : $this->get_default_value( 'country_code' ) ),
			),
			'units'                => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'units' ) ? '' : $this->get_default_value( 'units' ) ),
			),

			// Preview.
			'is_gutenberg_example' => array(
				'type'    => 'boolean',
				'default' => false,
			),
		);

	}

	/**
	 * Returns this block's supported built-in Attributes for Gutenberg.
	 *
	 * @since   3.6.3
	 *
	 * @return  array   Supports
	 */
	public function get_supports() {

		return array(
			'className' => true,
		);

	}

	/**
	 * Returns this shortcode / block's Fields
	 *
	 * @since   2.5.1
	 */
	public function get_fields() {

		// Don't populate fields for performance if the request is for the frontend web site.
		// Populate fields for admin, CLI and cron requests so that Generate via Browser, Server and CLI
		// will see fields for this shortcode, which is required for correct operation with e.g. Elementor
		// registered shortcodes/elements.
		if ( ! $this->base->is_admin_or_frontend_editor() && ! $this->base->is_cli() && ! $this->base->is_cron() ) {
			return false;
		}

		// Fetch Keywords.
		$keywords = $this->base->get_class( 'keywords' )->get_keywords_and_columns( true );

		return array(
			'forecast_type' => array(
				'label'         => __( 'Forecast Type', 'page-generator-pro' ),
				'type'          => 'select',
				'values'        => $this->base->get_class( 'open_weather_map' )->get_forecast_types(),
				'default_value' => $this->get_default_value( 'forecast_type' ),
			),
			'location'      => array(
				'label'       => __( 'Location', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'placeholder' => __( 'e.g. Birmingham', 'page-generator-pro' ),
			),
			'country_code'  => array(
				'label'         => __( 'Country Code', 'page-generator-pro' ),
				'type'          => 'select',
				'values'        => $this->base->get_class( 'common' )->get_countries(),
				'default_value' => $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-general', 'country_code', 'US' ),
			),
			'units'         => array(
				'label'         => __( 'Units', 'page-generator-pro' ),
				'type'          => 'select',
				'values'        => $this->base->get_class( 'open_weather_map' )->get_temperature_units(),
				'default_value' => $this->get_default_value( 'units' ),
			),
		);

	}

	/**
	 * Returns this shortcode / block's UI Tabs
	 *
	 * @since   2.5.1
	 */
	public function get_tabs() {

		if ( ! $this->base->is_admin_or_frontend_editor() ) {
			return false;
		}

		return array(
			'general' => array(
				'label'  => __( 'General', 'page-generator-pro' ),
				'fields' => array(
					'forecast_type',
					'location',
					'country_code',
					'units',
				),
			),
		);

	}

	/**
	 * Returns this shortcode / block's Default Values
	 *
	 * @since   2.5.1
	 */
	public function get_default_values() {

		return array(
			'forecast_type' => 13,
			'location'      => '',
			'country_code'  => '',
			'units'         => 'imperial',
		);

	}

	/**
	 * Returns the given shortcode / block's field's Default Value
	 *
	 * @since   2.5.1
	 *
	 * @param   string $field  Field.
	 * @return  string          Value
	 */
	public function get_default_value( $field ) {

		$defaults = $this->get_default_values();
		if ( isset( $defaults[ $field ] ) ) {
			return $defaults[ $field ];
		}

		return '';
	}

	/**
	 * Returns this shortcode / block's output
	 *
	 * @since   2.5.1
	 *
	 * @param  array $atts   Shortcode Attributes.
	 * @return string          Output
	 */
	public function render( $atts ) {

		// Parse attributes.
		$atts = $this->parse_atts( $atts );

		// If an Open Weather Map API key has been specified, use it instead of the class default.
		$open_weather_map_api_key = $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-integrations', 'open_weather_map_api_key' );
		if ( ! empty( $open_weather_map_api_key ) ) {
			$this->base->get_class( 'open_weather_map' )->set_api_key( $open_weather_map_api_key );
		}

		// Get City ID.
		$city_id = $this->base->get_class( 'open_weather_map' )->get_city_id(
			$atts['location'],
			$atts['country_code']
		);

		// Try fetching the City ID by ZIP Code if the City search didn't work.
		if ( is_wp_error( $city_id ) ) {
			$city_id = $this->base->get_class( 'open_weather_map' )->get_city_id_by_zip_code(
				$atts['location'],
				$atts['country_code']
			);
		}

		// Bail if errors occured.
		if ( is_wp_error( $city_id ) ) {
			if ( defined( 'PAGE_GENERATOR_PRO_DEBUG' ) && PAGE_GENERATOR_PRO_DEBUG === true ) {
				return $city_id->get_error_message();
			}

			return '';
		}

		// Generate random ID for the map.
		$weather_id = md5( wp_rand() );

		// Build HTML.
		$html = '<div id="page-generator-pro-open-weather-map-widget-' . $weather_id . '" class="page-generator-pro-open-weather-map"></div>
<script type="text/javascript">
window.myWidgetParam ? window.myWidgetParam : window.myWidgetParam = [];
window.myWidgetParam.push({
    id: ' . $atts['forecast_type'] . ',
    cityid: \'' . $city_id . '\',
    appid: \'' . $this->base->get_class( 'open_weather_map' )->api_key . '\',
    units: \'' . $atts['units'] . '\',
    containerid: \'page-generator-pro-open-weather-map-widget-' . $weather_id . '\'
});
(function() {
    var script = document.createElement(\'script\');
    script.async = true;
    script.charset = "utf-8";
    script.src = "//openweathermap.org/themes/openweathermap/assets/vendor/owm/js/weather-widget-generator.js";
    var s = document.getElementsByTagName(\'script\')[0];
    s.parentNode.insertBefore(script, s);
})();
</script>';

		/**
		 * Filter the Open Weather Maps HTML output, before returning.
		 *
		 * @since   2.4.8
		 *
		 * @param   string  $html   HTML Output.
		 * @param   array   $atts   Shortcode Attributes.
		 */
		$html = apply_filters( 'page_generator_pro_shortcode_open_weather_map', $html, $atts );

		// Return.
		return $html;

	}

}
