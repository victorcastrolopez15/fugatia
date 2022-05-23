<?php
/**
 * Google Maps Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Google Maps Dynamic Element
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.5.1
 */
class Page_Generator_Pro_Shortcode_Google_Map extends Page_Generator_Pro_Shortcode_Base {

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
	 *
	 * @return array
	 */
	public function get_name() {

		return 'google-map';

	}

	/**
	 * Returns this shortcode / block's Title, Icon, Categories, Keywords
	 * and properties for registering on generation and requiring CSS/JS.
	 *
	 * @since   2.5.1
	 *
	 * @return array
	 */
	public function get_overview() {

		return array(
			'title'                       => __( 'Google Map', 'page-generator-pro' ),
			'description'                 => __( 'Displays a Google Map', 'page-generator-pro' ),
			'icon'                        => $this->base->plugin->url . '/_modules/dashboard/feather/map-pin.svg',
			'category'                    => $this->base->plugin->name,
			'keywords'                    => array(
				__( 'Google Map', 'page-generator-pro' ),
				__( 'Google', 'page-generator-pro' ),
				__( 'Map', 'page-generator-pro' ),
			),

			// TinyMCE / QuickTags Modal Width and Height.
			'modal'                       => array(
				'width'  => 600,
				'height' => 500,
			),

			// Register when Generation is running only.
			'register_on_generation_only' => true,

			// Requires CSS and/or JS for output.
			'requires_css'                => true,
			'requires_js'                 => false,

			// Function to call when rendering the shortcode on the frontend.
			'render_callback'             => array( 'shortcode_google_map', 'render' ),

			// Gutenberg: Block Icon in Editor.
			'gutenberg_icon'              => file_get_contents( $this->base->plugin->folder . '/_modules/dashboard/feather/map-pin.svg' ), /* phpcs:ignore */
		);

	}

	/**
	 * Returns this block's Attributes and default values for Gutenberg.
	 *
	 * @since   3.6.3
	 */
	public function get_attributes() {

		return array(
			// Block attributes.
			'map_mode'             => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'map_mode' ) ? '' : $this->get_default_value( 'map_mode' ) ),
			),
			'maptype'              => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'maptype' ) ? '' : $this->get_default_value( 'maptype' ) ),
			),

			// Map Mode: Any.
			'location'             => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'location' ) ? '' : $this->get_default_value( 'location' ) ),
			),

			// Map Mode: Directions.
			'destination'          => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'destination' ) ? '' : $this->get_default_value( 'destination' ) ),
			),
			'mode'                 => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'mode' ) ? '' : $this->get_default_value( 'mode' ) ),
			),

			// Map Mode: Streetview.
			'country_code'         => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'country_code' ) ? '' : $this->get_default_value( 'country_code' ) ),
			),

			// Map Mode: Search.
			'term'                 => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'term' ) ? '' : $this->get_default_value( 'term' ) ),
			),

			// Output.
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
	 *
	 * @return array
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
			'map_mode'     => array(
				'label'         => __( 'Map Mode', 'page-generator-pro' ),
				'type'          => 'select',
				'class'         => 'wpzinc-conditional',
				'data'          => array(
					// .components-panel is Gutenberg.
					// .wpzinc-tinymce-popup is TinyMCE.
					'container' => '.components-panel, .wpzinc-tinymce-popup',
				),
				'values'        => array(
					'place'      => __( 'Location', 'page-generator-pro' ),
					'view'       => __( 'Location without Marker', 'page-generator-pro' ),
					'search'     => __( 'Place(s)/Business(es) in Location', 'page-generator-pro' ),
					'directions' => __( 'Directions', 'page-generator-pro' ),
					'streetview' => __( 'Street View', 'page-generator-pro' ),
				),
				'default_value' => $this->get_default_value( 'map_mode' ),
			),
			'maptype'      => array(
				'label'         => __( 'Map Type', 'page-generator-pro' ),
				'type'          => 'select',
				'values'        => array(
					'roadmap'   => __( 'Road Map', 'page-generator-pro' ),
					'satellite' => __( 'Satellite', 'page-generator-pro' ),
				),
				'default_value' => $this->get_default_value( 'maptype' ),
				'condition'     => array(
					'key'        => 'map_mode',
					'value'      => array( 'place', 'view', 'search', 'directions' ),
					'comparison' => '==',
				),
			),

			// Map Mode: Any.
			'location'     => array(
				'label'  => __( 'Location / Origin', 'page-generator-pro' ),
				'type'   => 'autocomplete',
				'values' => $keywords,
			),

			// Map Mode: Directions.
			'destination'  => array(
				'label'       => __( 'Destination', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'description' => __( 'If Map Type = Directions, specify the Destination here.  The Location field above is used as the Origin / Starting Point.', 'page-generator-pro' ),
				'condition'   => array(
					'key'        => 'map_mode',
					'value'      => 'directions',
					'comparison' => '==',
				),
			),
			'mode'         => array(
				'label'       => __( 'Method of Travel', 'page-generator-pro' ),
				'type'        => 'select',
				'values'      => array(
					'driving'   => __( 'Driving', 'page-generator-pro' ),
					'walking'   => __( 'Walking', 'page-generator-pro' ),
					'bicycling' => __( 'Cycling', 'page-generator-pro' ),
					'transit'   => __( 'Public Transport/Transit', 'page-generator-pro' ),
					'flying'    => __( 'Flying', 'page-generator-pro' ),
				),
				'description' => __( 'Specify the method of travel to show between the Location and Destination on the Map.', 'page-generator-pro' ),
				'condition'   => array(
					'key'        => 'map_mode',
					'value'      => 'directions',
					'comparison' => '==',
				),
			),

			// Map Mode: Streetview.
			'country_code' => array(
				'label'         => __( 'Country Code', 'page-generator-pro' ),
				'type'          => 'select',
				'values'        => $this->base->get_class( 'common' )->get_countries(),
				'default_value' => $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-general', 'country_code', 'US' ),
				'condition'     => array(
					'key'        => 'map_mode',
					'value'      => 'streetview',
					'comparison' => '==',
				),
			),

			// Map Mode: Search.
			'term'         => array(
				'label'       => __( 'Term', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'description' => __( 'The businesses / landmarks to plot on the map.', 'page-generator-pro' ),
				'condition'   => array(
					'key'        => 'map_mode',
					'value'      => 'search',
					'comparison' => '==',
				),
			),

			// Output.
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
	 *
	 * @return array
	 */
	public function get_tabs() {

		if ( ! $this->base->is_admin_or_frontend_editor() ) {
			return false;
		}

		return array(
			'general' => array(
				'label'  => __( 'General', 'page-generator-pro' ),
				'fields' => array(
					'map_mode',
					'maptype',
					'location',
					'destination',
					'mode',
					'country_code',
					'term',
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
	 *
	 * @return array
	 */
	public function get_default_values() {

		return array(
			'map_mode'               => 'place',
			'maptype'                => 'roadmap',
			'location'               => '',

			// Directions.
			'destination'            => '',
			'mode'                   => 'driving',

			// Street View.
			'country_code'           => '',

			// Search.
			'term'                   => '',

			// Output.
			'height'                 => 250,
			'zoom'                   => 14,

			// Deprecated.
			'show_place_card_marker' => 1,
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

		// For backward compat, convert some attributes now.
		if ( array_key_exists( 'show_place_card_marker', $atts ) ) {
			if ( ! $atts['show_place_card_marker'] ) {
				$atts['map_mode'] = 'view';
			}
		}
		if ( array_key_exists( 'term', $atts ) ) {
			if ( ! empty( $atts['term'] ) ) {
				$atts['map_mode'] = 'search';
			}
		}
		switch ( $atts['maptype'] ) {
			case 'directions':
				$atts['map_mode'] = 'directions';
				$atts['mode']     = 'driving';
				$atts['maptype']  = 'roadmap';
				break;

			case 'streetview':
				$atts['map_mode'] = 'streetview';
				break;
		}

		// Build iframe arguments.
		$args = false;
		switch ( $atts['map_mode'] ) {

			/**
			 * Directions
			 */
			case 'directions':
				// Deliberately don't include zoom parameter as this can result in the map being too zoomed in
				// and directions not shown until the user zooms out.
				$args = array(
					'origin'      => $atts['location'],
					'destination' => $atts['destination'],
					'mode'        => $atts['mode'],
					'maptype'     => $atts['maptype'],
				);
				break;

			/**
			 * Street View
			 */
			case 'streetview':
				// Get latitude and longitude.
				$lat_lng = false;
				$result  = $this->base->get_class( 'georocket' )->get_geocode( $atts['location'] . ', ' . $atts['country_code'], $this->base->licensing->get_license_key() );

				// Bail if errors occured.
				if ( is_wp_error( $result ) ) {
					if ( defined( 'PAGE_GENERATOR_PRO_DEBUG' ) && PAGE_GENERATOR_PRO_DEBUG === true ) {
						return $result->get_error_message();
					}

					return '';
				}

				if ( ! $result->data ) {
					if ( defined( 'PAGE_GENERATOR_PRO_DEBUG' ) && PAGE_GENERATOR_PRO_DEBUG === true ) {
						return __( 'Google Maps: No Data in Geocode Response', 'page-generator-pro' );
					}

					return '';
				}

				// If here, we have a latitude and longitude.
				$args = array(
					'location' => $result->data->latitude . ',' . $result->data->longitude,
				);
				break;

			/**
			 * Search
			 * - Pins of matching places in location
			 */
			case 'search':
				$args = array(
					'q'       => $atts['term'] . ' in ' . $atts['location'],
					'zoom'    => $atts['zoom'],
					'maptype' => $atts['maptype'],
				);
				break;

			/**
			 * View
			 * - Map without Pin
			 */
			case 'view':
				// Get latitude and longitude.
				$lat_lng = false;
				$result  = $this->base->get_class( 'georocket' )->get_geocode( $atts['location'] . ', ' . $atts['country_code'], $this->base->licensing->get_license_key() );

				// Bail if errors occured.
				if ( is_wp_error( $result ) ) {
					if ( defined( 'PAGE_GENERATOR_PRO_DEBUG' ) && PAGE_GENERATOR_PRO_DEBUG === true ) {
						return $result->get_error_message();
					}

					return '';
				}

				if ( ! $result->data ) {
					if ( defined( 'PAGE_GENERATOR_PRO_DEBUG' ) && PAGE_GENERATOR_PRO_DEBUG === true ) {
						return __( 'Google Maps: No Data in Geocode Response', 'page-generator-pro' );
					}

					return '';
				}

				$args = array(
					'center'  => $result->data->latitude . ',' . $result->data->longitude,
					'zoom'    => $atts['zoom'],
					'maptype' => $atts['maptype'],
				);
				break;

			/**
			 * Place
			 * - Map with Pin
			 */
			case 'place':
			default:
				$args = array(
					'q'       => $atts['location'],
					'zoom'    => $atts['zoom'],
					'maptype' => $atts['maptype'],
				);
				break;

		}

		// Add API Key and build URL.
		// There is no billing/usage limit on this Google service, so use our API key.
		$args['key'] = 'AIzaSyCNTEOso0tZG6YMSJFoaJEY5Th1stEWrJI';
		$url         = 'https://www.google.com/maps/embed/v1/' . $atts['map_mode'] . '?' . http_build_query( $args );

		/**
		 * Filter the Google Maps iFrame URL, before output.
		 *
		 * @since   2.0.4
		 *
		 * @param   string  $url        URL with Arguments.
		 * @param   array   $atts       Shortcode Attributes.
		 * @param   array   $args       URL Arguments.
		 * @param   string  $map_mode   Map Mode.
		 */
		$url = apply_filters( 'page_generator_pro_shortcode_google_maps_url', $url, $atts, $args, $atts['map_mode'] );

		// Build HTML using the URL.
		$html = '<iframe class="page-generator-pro-map" width="100%" height="' . $atts['height'] . '" frameborder="0" style="border:0" src="' . $url . '" allowfullscreen></iframe>';

		/**
		 * Filter the Google Maps HTML output, before returning.
		 *
		 * @since   1.0.0
		 *
		 * @param   string  $html   HTML Output.
		 * @param   array   $atts   Shortcode Attributes.
		 */
		$html = apply_filters( 'page_generator_pro_shortcode_google_maps', $html, $atts );

		// Return.
		return $html;

	}

}
