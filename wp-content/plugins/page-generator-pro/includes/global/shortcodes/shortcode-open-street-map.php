<?php
/**
 * Open Street Map Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Open Street Map Dynamic Element
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.5.1
 */
class Page_Generator_Pro_Shortcode_Open_Street_Map extends Page_Generator_Pro_Shortcode_Base {

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

		return 'open-street-map';

	}

	/**
	 * Returns this shortcode / block's Title, Icon, Categories, Keywords
	 * and properties for registering on generation and requiring CSS/JS.
	 *
	 * @since   2.5.1
	 */
	public function get_overview() {

		return array(
			'title'                       => __( 'Open Street Map', 'page-generator-pro' ),
			'description'                 => __( 'Displays an Open Street Map', 'page-generator-pro' ),
			'icon'                        => $this->base->plugin->url . '/_modules/dashboard/feather/map.svg',
			'category'                    => $this->base->plugin->name,
			'keywords'                    => array(
				__( 'Open Street Map', 'page-generator-pro' ),
				__( 'Map', 'page-generator-pro' ),
			),

			// TinyMCE / QuickTags Modal Width and Height.
			'modal'                       => array(
				'width'  => 600,
				'height' => 325,
			),

			// Register when Generation is running only.
			'register_on_generation_only' => true,

			// Requires CSS and/or JS for output.
			'requires_css'                => true,
			'requires_js'                 => true,

			// Function to call when rendering the shortcode on the frontend.
			'render_callback'             => array( 'shortcode_open_street_map', 'render' ),

			// Gutenberg: Block Icon in Editor.
			'gutenberg_icon'              => file_get_contents( $this->base->plugin->folder . '/_modules/dashboard/feather/map.svg' ), /* phpcs:ignore */
		);

	}

	/**
	 * Returns this block's Attributes and default values for Gutenberg.
	 *
	 * @since   3.6.3
	 */
	public function get_attributes() {

		return array(
			'location'             => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'location' ) ? '' : $this->get_default_value( 'location' ) ),
			),
			'country_code'         => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'country_code' ) ? '' : $this->get_default_value( 'country_code' ) ),
			),
			'height'               => array(
				'type'    => 'number',
				'default' => $this->get_default_value( 'height' ),
			),
			'zoom'                 => array(
				'type'    => 'number',
				'default' => $this->get_default_value( 'zoom' ),
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
			'location'     => array(
				'label'  => __( 'Location', 'page-generator-pro' ),
				'type'   => 'autocomplete',
				'values' => $keywords,
			),
			'country_code' => array(
				'label'         => __( 'Country Code', 'page-generator-pro' ),
				'type'          => 'select',
				'values'        => $this->base->get_class( 'common' )->get_countries(),
				'default_value' => $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-general', 'country_code', 'US' ),
			),
			'height'       => array(
				'label'         => __( 'Height (px)', 'page-generator-pro' ),
				'type'          => 'number',
				'min'           => 1,
				'max'           => 9999,
				'step'          => 1,
				'default_value' => $this->get_default_value( 'height' ),
			),
			'zoom'         => array(
				'label'         => __( 'Zoom Level', 'page-generator-pro' ),
				'type'          => 'number',
				'min'           => 1,
				'max'           => 20,
				'step'          => 1,
				'default_value' => $this->get_default_value( 'zoom' ),
				'description'   => __( 'A higher number means a higher zoom level, showing more detail. As a guide, 1 = World; 20 = Buildings', 'page-generator-pro' ),
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
					'location',
					'country_code',
					'height',
					'zoom',
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
			'location'     => '',
			'country_code' => '',
			'height'       => 250,
			'zoom'         => 14,
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

		// Get latitude and longitude.
		$lat_lng = false;
		$result  = $this->base->get_class( 'georocket' )->get_geocode( $atts['location'] . ', ' . $atts['country_code'], $this->base->licensing->get_license_key() );

		// Bail if errors occured.
		if ( is_wp_error( $result ) ) {
			if ( defined( 'PAGE_GENERATOR_PRO_DEBUG' ) && PAGE_GENERATOR_PRO_DEBUG === true ) {
				/* translators: Error message */
				return sprintf( __( 'Open Street Maps: %s', 'page-generator-pro' ), $result->get_error_message() );
			}

			return '';
		}

		// Generate random ID for the map.
		$map_id = md5( wp_rand() );

		/* translators: Copyright link */
		$copyright = sprintf( __( 'Map data &copy; %s', 'page-generator-pro' ), '<a href="https://www.openstreetmap.org/" rel="nofollow noreferrer noopener" target="_blank">OpenStreetMap</a>' );

		// Build HTML.
		$html  = '<div id="page-generator-pro-open-street-map-' . $map_id . '" class="page-generator-pro-map" style="height:' . $atts['height'] . 'px;"></div>';
		$html .= '<script type="text/javascript">
var map = L.map(\'page-generator-pro-open-street-map-' . $map_id . '\').setView([' . $result->data->latitude . ', ' . $result->data->longitude . '], ' . $atts['zoom'] . ');
L.tileLayer(\'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png\', {
    attribution: \'' . $copyright . '\'
}).addTo(map);
</script>';

		/**
		 * Filter the Open Street Maps HTML output, before returning.
		 *
		 * @since   2.2.6
		 *
		 * @param   string  $html   HTML Output.
		 * @param   array   $atts   Shortcode Attributes.
		 */
		$html = apply_filters( 'page_generator_pro_shortcode_open_street_map', $html, $atts );

		// Return.
		return $html;

	}

}
