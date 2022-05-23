<?php
/**
 * Yelp Image Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Yelp Dynamic Element
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.5.1
 */
class Page_Generator_Pro_Shortcode_Yelp extends Page_Generator_Pro_Shortcode_Base {

	/**
	 * Holds the base object.
	 *
	 * @since   2.5.1
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Flag denoting if we've output the required Yelp logo
	 * if a Yelp shortcode was used.
	 *
	 * This prevents outputting it multiple times when
	 * the shortcode is used more than once
	 *
	 * @since   2.5.1
	 *
	 * @var     bool
	 */
	public $yelp_logo_output = false;

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

		return 'yelp';

	}

	/**
	 * Returns this shortcode / block's Title, Icon, Categories, Keywords
	 * and properties for registering on generation and requiring CSS/JS.
	 *
	 * @since   2.5.1
	 */
	public function get_overview() {

		return array(
			'title'                       => __( 'Yelp', 'page-generator-pro' ),
			'description'                 => __( 'Displays business listings from Yelp based on the given search parameters.', 'page-generator-pro' ),
			'icon'                        => $this->base->plugin->url . '/_modules/dashboard/feather/yelp.svg',
			'category'                    => $this->base->plugin->name,
			'keywords'                    => array(
				__( 'Yelp', 'page-generator-pro' ),
			),

			// TinyMCE / QuickTags Modal Width and Height.
			'modal'                       => array(
				'width'  => 990,
				'height' => 610,
			),

			// Register when Generation is running only.
			'register_on_generation_only' => true,

			// Requires CSS and/or JS for output.
			'requires_css'                => true,
			'requires_js'                 => false,

			// Function to call when rendering the shortcode on the frontend.
			'render_callback'             => array( 'shortcode_yelp', 'render' ),

			// Gutenberg: Block Icon in Editor.
			'gutenberg_icon'              => file_get_contents( $this->base->plugin->folder . '/_modules/dashboard/feather/yelp.svg' ), /* phpcs:ignore */
		);

	}

	/**
	 * Returns this block's Attributes and default values for Gutenberg.
	 *
	 * @since   3.6.3
	 */
	public function get_attributes() {

		return array(
			// Search Parameters.
			'term'                 => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'term' ) ? '' : $this->get_default_value( 'term' ) ),
			),
			'location'             => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'location' ) ? '' : $this->get_default_value( 'location' ) ),
			),
			'radius'               => array(
				'type'    => 'number',
				'default' => $this->get_default_value( 'radius' ),
			),
			'minimum_rating'       => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'minimum_rating' ) ? '' : $this->get_default_value( 'minimum_rating' ) ),
			),
			'locale'               => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'locale' ) ? '' : $this->get_default_value( 'locale' ) ),
			),
			'price'                => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'price' ) ? '' : $this->get_default_value( 'price' ) ),
			),
			'limit'                => array(
				'type'    => 'number',
				'default' => $this->get_default_value( 'limit' ),
			),
			'sort_by'              => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'sort_by' ) ? '' : $this->get_default_value( 'sort_by' ) ),
			),

			// Output.
			'output_type'          => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'output_type' ) ? '' : $this->get_default_value( 'output_type' ) ),
			),
			'columns'              => array(
				'type'    => 'number',
				'default' => $this->get_default_value( 'columns' ),
			),
			'link'                 => array(
				'type'    => 'boolean',
				'default' => $this->get_default_value( 'link' ),
			),
			'image_width'          => array(
				'type'    => 'number',
				'default' => $this->get_default_value( 'image_width' ),
			),
			'image_alt_tag'        => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'image_alt_tag' ) ? '' : $this->get_default_value( 'image_alt_tag' ) ),
			),
			'display_order'        => array(
				'type'      => 'array',
				'delimiter' => ',',
				'default'   => $this->get_default_value( 'display_order' ),
			),
			'display_alignment'    => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'display_alignment' ) ? '' : $this->get_default_value( 'display_alignment' ) ),
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
			'term'              => array(
				'label'       => __( 'Term', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'placeholder' => __( 'e.g. restaurants', 'page-generator-pro' ),
			),
			'location'          => array(
				'label'       => __( 'Location', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'placeholder' => __( 'e.g. Birmingham, UK', 'page-generator-pro' ),
			),
			'radius'            => array(
				'label'         => __( 'Radius', 'page-generator-pro' ),
				'type'          => 'number',
				'min'           => 1,
				'max'           => 20,
				'step'          => 1,
				'default_value' => $this->get_default_value( 'radius' ),
				'description'   => __( 'The maximum radius, in miles, from the Location to search Business Listings for.', 'page-generator-pro' ),
			),
			'minimum_rating'    => array(
				'label'         => __( 'Minimum Rating', 'page-generator-pro' ),
				'type'          => 'select',
				'values'        => $this->base->get_class( 'yelp' )->get_rating_options(),
				'default_value' => $this->get_default_value( 'minimum_rating' ),
				'description'   => __( 'The minimum rating a business listing must have to be displayed.', 'page-generator-pro' ),
			),
			'locale'            => array(
				'label'         => __( 'Language', 'page-generator-pro' ),
				'type'          => 'select',
				'values'        => $this->base->get_class( 'yelp' )->get_locales(),
				'default_value' => $this->get_default_value( 'locale' ),
			),
			'price'             => array(
				'label'         => __( 'Price', 'page-generator-pro' ),
				'type'          => 'select',
				'values'        => $this->base->get_class( 'yelp' )->get_price_options(),
				'default_value' => $this->get_default_value( 'price' ),
			),
			'limit'             => array(
				'label'         => __( 'Number of Listings', 'page-generator-pro' ),
				'type'          => 'number',
				'min'           => 1,
				'max'           => 50,
				'step'          => 1,
				'default_value' => $this->get_default_value( 'limit' ),
			),
			'sort_by'           => array(
				'label'         => __( 'Sort Listings', 'page-generator-pro' ),
				'type'          => 'select',
				'values'        => $this->base->get_class( 'yelp' )->get_sort_by_options(),
				'default_value' => $this->get_default_value( 'sort_by' ),
			),

			'output_type'       => array(
				'label'         => __( 'Output Type', 'page-generator-pro' ),
				'type'          => 'select',
				'class'         => 'wpzinc-conditional',
				'data'          => array(
					// .components-panel is Gutenberg.
					// .yelp is TinyMCE.
					'container' => '.components-panel, .yelp',
				),
				'values'        => array(
					'table' => __( 'Table', 'page-generator-pro' ),
					'list'  => __( 'Grid/List', 'page-generator-pro' ),
				),
				'default_value' => $this->get_default_value( 'output_type' ),
			),
			'columns'           => array(
				'label'         => __( 'Number of Columns', 'page-generator-pro' ),
				'type'          => 'number',
				'min'           => 1,
				'max'           => 4,
				'step'          => 1,
				'condition'     => array(
					'key'        => 'output_type',
					'value'      => array( 'list' ),
					'comparison' => '==',
				),
				'default_value' => $this->get_default_value( 'columns' ),
			),
			'link'              => array(
				'label'         => __( 'Link Results', 'page-generator-pro' ),
				'type'          => 'toggle',
				'default_value' => $this->get_default_value( 'link' ),
				'description'   => __( 'If enabled, each Business\' listing will be linked to the listing on Yelp when clicked.', 'page-generator-pro' ),
			),
			'image_width'       => array(
				'label'         => __( 'Max. Image Width', 'page-generator-pro' ),
				'type'          => 'number',
				'min'           => 0,
				'max'           => 9999,
				'step'          => 1,
				'default_value' => $this->get_default_value( 'image_width' ),
				'description'   => __( 'The maximum width of each Business\' Image / Logo, in pixels. Zero = the full size image will be displayed.', 'page-generator-pro' ),
			),
			'image_alt_tag'     => array(
				'label'         => __( 'Image Alt Tag', 'page-generator-pro' ),
				'type'          => 'text',
				'default_value' => $this->get_default_value( 'image_alt_tag' ),
			),
			'display_order'     => array(
				'label'         => __( 'Display Order', 'page-generator-pro' ),
				'type'          => 'select_multiple',
				'values'        => array(
					'business_name'  => __( 'Business Name', 'page-generator-pro' ),
					'image'          => __( 'Image', 'page-generator-pro' ),
					'rating'         => __( 'Rating', 'page-generator-pro' ),
					'categories'     => __( 'Categories', 'page-generator-pro' ),
					'phone'          => __( 'Phone: International Format', 'page-generator-pro' ),
					'phone_local'    => __( 'Phone: Local Format', 'page-generator-pro' ),
					'address'        => __( 'Address: Full', 'page-generator-pro' ),
					'address1'       => __( 'Address: Line 1', 'page-generator-pro' ),
					'address2'       => __( 'Address: Line 2', 'page-generator-pro' ),
					'address3'       => __( 'Address: Line 3', 'page-generator-pro' ),
					'city'           => __( 'Address: City', 'page-generator-pro' ),
					'zip_code'       => __( 'Address: ZIP Code', 'page-generator-pro' ),
					'state'          => __( 'Address: State Code', 'page-generator-pro' ),
					'country'        => __( 'Address: Country Code', 'page-generator-pro' ),
					'distance_km'    => __( 'Distance: KM', 'page-generator-pro' ),
					'distance_miles' => __( 'Distance: Miles', 'page-generator-pro' ),
				),
				'default_value' => $this->get_default_value( 'display_order' ),
				'class'         => 'wpzinc-selectize-drag-drop',
				'description'   => __( 'Defines the content to display for each individual Business Listing, and the order to display it in.', 'page-generator-pro' ),
			),
			'display_alignment' => array(
				'label'       => __( 'Display Alignment', 'page-generator-pro' ),
				'type'        => 'select',
				'values'      => array(
					'vertical'   => __( 'Vertical', 'page-generator-pro' ),
					'horizontal' => __( 'Horizontal', 'page-generator-pro' ),
				),
				'condition'   => array(
					'key'        => 'output_type',
					'value'      => array( 'list', 'foobar' ),
					'comparison' => '==',
				),
				'description' => __( 'Defines the content display alignment for each individual Business Listing.', 'page-generator-pro' ),
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
			'search-parameters' => array(
				'label'       => __( 'Search Parameters', 'page-generator-pro' ),
				'description' => __( 'Defines search query parameters to fetch business listings from Yelp.', 'page-generator-pro' ),
				'class'       => 'search',
				'fields'      => array(
					'term',
					'location',
					'radius',
					'minimum_rating',
					'locale',
					'price',
					'limit',
					'sort_by',
				),
			),
			'output'            => array(
				'label'       => __( 'Output', 'page-generator-pro' ),
				'description' => __( 'Defines what to output for each Yelp business listing.', 'page-generator-pro' ),
				'class'       => 'yelp',
				'fields'      => array(
					'output_type',
					'columns',
					'link',
					'image_alt_tag',
					'image_width',
					'display_order',
					'display_alignment',
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
			// Search Parameters.
			'term'              => '',
			'location'          => '',
			'radius'            => 0,
			'minimum_rating'    => 0,
			'locale'            => 'en_US', // get_locale() may return 'en' which is not valid for Yelp.
			'price'             => 0,
			'limit'             => 5,
			'sort_by'           => '',

			// Output.
			'output_type'       => 'list',
			'columns'           => 1,
			'link'              => false,
			'image_alt_tag'     => '%business_name%',
			'image_width'       => 0,
			'display_order'     => array(
				'business_name',
				'image',
				'rating',
				'categories',
				'phone',
				'address',
			),
			'display_alignment' => 'vertical',

			// Kept for backward compat.
			'image'             => 1,
			'rating'            => 1,
			'categories'        => 1,
			'phone'             => 1,
			'address'           => 1,
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

		// For backward compat, if there's no display order defined but individual items
		// are enabled (e.g. image=1, rating=1 etc), use those attributes to build the display order.
		if ( ! isset( $atts['display_order'] ) || empty( $atts['display_order'] ) ) {
			// Always display the business name.
			$atts['display_order'] = 'business_name';

			foreach ( array( 'image', 'rating', 'categories', 'phone', 'address' ) as $display_item ) {
				if ( ! isset( $atts[ $display_item ] ) ) {
					continue;
				}
				if ( ! $atts[ $display_item ] ) {
					continue;
				}

				$atts['display_order'] .= ',' . $display_item;
			}

			// If we only have a business name, discard it so we fallback to the defaults below which will display everything.
			if ( $atts['display_order'] === 'business_name' ) {
				unset( $atts['display_order'] );
			}
		}

		// Parse attributes.
		$atts = $this->parse_atts( $atts );

		// Copy shortcode attributes to Yelp API arguments, removing some unused keys.
		$args     = array();
		$api_keys = array(
			'term',
			'location',
			'radius',
			'minimum_rating',
			'locale',
			'price',
			'limit',
			'sort_by',
		);
		foreach ( $api_keys as $api_key ) {
			if ( ! isset( $atts[ $api_key ] ) ) {
				continue;
			}
			if ( ! $atts[ $api_key ] ) {
				continue;
			}

			$args[ $api_key ] = $atts[ $api_key ];
		}

		// If a Yelp API key has been specified, use it instead of the class default.
		$yelp_api_key = $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-integrations', 'yelp_api_key' );
		if ( ! empty( $yelp_api_key ) ) {
			$this->base->get_class( 'yelp' )->set_api_key( $yelp_api_key );
		}

		// Send request to Yelp API.
		$results = $this->base->get_class( 'yelp' )->businesses_search( $args );

		// Check for errors from the Yelp API.
		if ( is_wp_error( $results ) ) {
			if ( defined( 'PAGE_GENERATOR_PRO_DEBUG' ) && PAGE_GENERATOR_PRO_DEBUG === true ) {
				return $results->get_error_message();
			}

			// Just return a blank string.
			return '';
		}

		// Define CSS classes for the container.
		$css = array(
			'page-generator-pro-' . $this->get_name(),
			'page-generator-pro-' . $this->get_name() . '-columns-' . $atts['columns'],
			'page-generator-pro-' . $this->get_name() . '-' . str_replace( '_', '-', $atts['output_type'] ),
			'page-generator-pro-' . $this->get_name() . '-' . str_replace( '_', '-', $atts['display_alignment'] ),
		);

		// Start HTML.
		$html = '<div class="' . implode( ' ', $css ) . '">';

		// Build HTML based on the output type.
		switch ( $atts['output_type'] ) {
			case 'table':
				$html .= $this->get_table_output( $results, $atts );
				break;

			default:
				$html .= $this->get_list_output( $results, $atts );
				break;
		}

		/**
		 * Filter the Yelp Shortcode HTML output, before returning.
		 *
		 * @since   1.0.0
		 *
		 * @param   string  $html   HTML Output.
		 * @param   array   $atts   Shortcode Attributes.
		 */
		$html = apply_filters( 'page_generator_pro_shortcode_yelp', $html, $atts );

		// Add Yelp logo, if we haven't yet output it.
		// This is required to meet the display requirements below, which is why this is done after filtering.
		// http://www.yelp.co.uk/developers/getting_started/display_requirements.
		if ( ! $this->yelp_logo_output ) {
			$html                  .= '<a href="https://www.yelp.com" rel="nofollow noreferrer noopener" target="_blank"><img src="https://s3-media1.ak.yelpcdn.com/assets/2/www/img/55e2efe681ed/developers/yelp_logo_50x25.png" /></a>';
			$this->yelp_logo_output = true;
		}

		$html .= '</div>';

		// Return.
		return $html;

	}

	/**
	 * Returns HTML for Yelp Business Listings in list format
	 *
	 * @since   2.8.3
	 *
	 * @param   array $results    Business Listings.
	 * @param   array $atts       Shortcode Attributes.
	 * @return  string              HTML
	 */
	private function get_list_output( $results, $atts ) {

		$html = '';

		// Iterate through results, building HTML.
		foreach ( $results as $count => $business ) {

			$html .= '<div class="business">';

			// Iterate through the display order for the business listing's attributes.
			foreach ( $atts['display_order'] as $index => $display_item ) {
				// If Display Alignment is horizontal, and we're on the second item,
				// wrap the remaining items in a div.
				if ( $atts['display_alignment'] === 'horizontal' && $index === 1 ) {
					$html .= '<div class="item">';
				}

				// Get Business Listing Attribute.
				$html .= $this->get_business_listing_attribute_output( $atts, $business, $display_item, 'div' );
			}

			// If Display Alignment is horizontal, and we output at least two attributes
			// for the business, close the wrap on the remaining items.
			if ( $atts['display_alignment'] === 'horizontal' && $index > 0 ) {
				$html .= '</div>';
			}

			$html .= '</div>';

			// Check if limit reached.
			if ( ( $count + 1 ) == $atts['limit'] ) { // phpcs:ignore
				break;
			}
		}

		return $html;

	}

	/**
	 * Returns HTML for Yelp Business Listings in table format
	 *
	 * @since   2.8.3
	 *
	 * @param   array $results    Business Listings.
	 * @param   array $atts       Shortcode Attributes.
	 * @return  string              HTML
	 */
	private function get_table_output( $results, $atts ) {

		// Build table headers.
		$html = '<table>
            <thead>
                <tr>';

		foreach ( $atts['display_order'] as $display_item ) {
			switch ( $display_item ) {
				/**
				 * Business Name
				 */
				case 'business_name':
					$html .= '<th>' . __( 'Business Name', 'page-generator-pro' ) . '</th>';
					break;

				/**
				 * Image
				 */
				case 'image':
					// Deliberately ignored; the image is displayed with the Business Name.
					break;

				/**
				 * Rating
				 */
				case 'rating':
					$html .= '<th>' . __( 'Rating', 'page-generator-pro' ) . '</th>';
					break;

				/**
				 * Categories
				 */
				case 'categories':
					$html .= '<th>' . __( 'Categories', 'page-generator-pro' ) . '</th>';
					break;

				/**
				 * Phone
				 * Phone (Local Format)
				 */
				case 'phone':
				case 'phone_local':
					$html .= '<th>' . __( 'Phone Number', 'page-generator-pro' ) . '</th>';
					break;

				/**
				 * Address
				 */
				case 'address':
					$html .= '<th>' . __( 'Address', 'page-generator-pro' ) . '</th>';
					break;

				/**
				 * Address Line 1
				 */
				case 'address1':
					$html .= '<th>' . __( 'Address Line 1', 'page-generator-pro' ) . '</th>';
					break;

				/**
				 * Address Line 2
				 */
				case 'address2':
					$html .= '<th>' . __( 'Address Line 2', 'page-generator-pro' ) . '</th>';
					break;

				/**
				 * Address Line 3
				 */
				case 'address3':
					$html .= '<th>' . __( 'Address Line 3', 'page-generator-pro' ) . '</th>';
					break;

				/**
				 * City
				 */
				case 'city':
					$html .= '<th>' . __( 'City', 'page-generator-pro' ) . '</th>';
					break;

				/**
				 * ZIP Code
				 */
				case 'zip_code':
					$html .= '<th>' . __( 'ZIP Code', 'page-generator-pro' ) . '</th>';
					break;

				/**
				 * State
				 */
				case 'state':
					$html .= '<th>' . __( 'State', 'page-generator-pro' ) . '</th>';
					break;

				/**
				 * Country
				 */
				case 'country':
					$html .= '<th>' . __( 'Country', 'page-generator-pro' ) . '</th>';
					break;

				/**
				 * Distance
				 */
				case 'distance_km':
				case 'distance_miles':
					$html .= '<th>' . __( 'Distance', 'page-generator-pro' ) . '</th>';
					break;

			}
		}

		$html .= '
                </tr>
            </thead>
            <tbody>';

		// Iterate through results, building HTML.
		foreach ( $results as $count => $business ) {

			$html .= '<tr class="business">';

			// Iterate through the display order for the business listing's attributes.
			foreach ( $atts['display_order'] as $display_item ) {
				// Get Business Listing Attribute.
				$html .= $this->get_business_listing_attribute_output( $atts, $business, $display_item, 'td' );
			}

			// Close row.
			$html .= '</tr>';

			// Check if limit reached.
			if ( ( $count + 1 ) == $atts['limit'] ) { // phpcs:ignore
				break;
			}
		}

		// Close table.
		$html .= '</tbody>
            </table>';

		// Return.
		return $html;

	}

	/**
	 * Returns HTML for the given Business' Display Item and HTML Tag - for example,
	 * the Business Name in a <div> or the Business Image in a <td>
	 *
	 * @since   2.9.6
	 *
	 * @param   array  $atts           Attributes.
	 * @param   array  $business       Business.
	 * @param   string $display_item   Display Item.
	 * @param   string $html_tag       HTML Tag (div,td).
	 * @return  string                  HTML
	 */
	private function get_business_listing_attribute_output( $atts, $business, $display_item, $html_tag ) {

		switch ( $display_item ) {
			/**
			 * Business Name
			 */
			case 'business_name':
				$html = '<' . $html_tag . ' class="name">';

				// Add link if required.
				if ( $atts['link'] ) {
					$html .= '<a href="' . $business->url . '" target="_blank" rel="nofollow noopener">';
				}

				$html .= $business->name;

				// Add image if this is a table cell and the image needs to be output.
				if ( $html_tag === 'td' && in_array( 'image', $atts['display_order'], true ) ) {
					// Define image width if specified.
					$image_width = ( ( $atts['image_width'] ) ? ' width="' . $atts['image_width'] . '"' : '' );
					$html       .= '<img src="' . $business->image_url . '"' . $image_width . ' alt="' . $this->replace_yelp_variables( $atts['image_alt_tag'], $business ) . '" />';
				}

				// Close link if required.
				if ( $atts['link'] ) {
					$html .= '</a>';
				}

				$html .= '</' . $html_tag . '>';

				return $html;

			/**
			 * Image
			 */
			case 'image':
				// Ignore for table cell.
				if ( $html_tag === 'td' ) {
					return '';
				}

				// Define image width if specified.
				$image_width = ( ( $atts['image_width'] ) ? ' width="' . $atts['image_width'] . '"' : '' );

				$html = '<' . $html_tag . ' class="image">';

				// Add link if required.
				if ( $atts['link'] ) {
					$html .= '<a href="' . $business->url . '" target="_blank" rel="nofollow noopener">';
				}

				$html .= '<img src="' . $business->image_url . '"' . $image_width . ' alt="' . $this->replace_yelp_variables( $atts['image_alt_tag'], $business ) . '" />';

				// Close link if required.
				if ( $atts['link'] ) {
					$html .= '</a>';
				}

				$html .= '</' . $html_tag . '>';

				return $html;

			/**
			 * Rating
			 */
			case 'rating':
				$html = '<' . $html_tag . ' class="rating">';

				// Add link if required.
				if ( $atts['link'] ) {
					$html .= '<a href="' . $business->url . '" target="_blank" rel="nofollow noopener">';
				}

				$html .= '
                	<div class="rating-stars rating-stars-' . str_replace( '.', '-', $business->rating ) . '"></div>
                    <div class="rating-text">' . $business->review_count . ' ' . ( $business->review_count === 1 ? __( 'review', 'page-generator-pro' ) : __( 'reviews', 'page-generator-pro' ) ) . '</div>';

				// Close link if required.
				if ( $atts['link'] ) {
					$html .= '</a>';
				}

				$html .= '</' . $html_tag . '>';

				return $html;

			/**
			 * Categories
			 */
			case 'categories':
				$html = '<' . $html_tag . ' class="categories">';

				$total_categories = count( $business->categories );
				foreach ( $business->categories as $category_count => $category ) {
					$html .= $category->title;
					if ( ( $category_count + 1 ) != $total_categories ) { // phpcs:ignore
						$html .= ', ';
					}
				}

				$html .= '</' . $html_tag . '>';

				return $html;

			/**
			 * Phone
			 */
			case 'phone':
				return '<' . $html_tag . ' class="phone">' . $business->phone . '</' . $html_tag . '>';

			/**
			 * Phone (Local Format)
			 */
			case 'phone_local':
				return '<' . $html_tag . ' class="phone-local">' . $business->display_phone . '</' . $html_tag . '>';

			/**
			 * Address
			 */
			case 'address':
				$html = '<' . $html_tag . ' class="address">';

				// Address.
				$total_address_lines = count( $business->location->display_address );
				foreach ( $business->location->display_address as $address_count => $address ) {
					$html .= $address;
					if ( ( $address_count + 1 ) != $total_address_lines ) { // phpcs:ignore
						$html .= ', ';
					}
				}

				$html .= '</' . $html_tag . '>';

				return $html;

			/**
			 * Address Lines
			 */
			case 'address1':
			case 'address2':
			case 'address3':
			case 'city':
			case 'zip_code':
			case 'country':
			case 'state':
				return '<' . $html_tag . ' class="' . $display_item . '">' . $business->location->{ $display_item } . '</' . $html_tag . '>';

			/**
			 * Distance (KM)
			 */
			case 'distance_km':
				return '<' . $html_tag . ' class="distance-km">' . round( ( $business->distance / 1000 ), 2 ) . ' km</' . $html_tag . '>';

			/**
			 * Distance (Miles)
			 */
			case 'distance_miles':
				return '<' . $html_tag . ' class="distance-miles">' . round( ( ( $business->distance / 1000 ) / 1.6 ), 2 ) . ' miles</' . $html_tag . '>';

			default:
				return '';
		}

	}

	/**
	 * Replaces Post variables with the Post's data.
	 *
	 * @since   2.6.3
	 *
	 * @param   string $text       Text.
	 * @param   object $business   Yelp Business Listing.
	 * @return  string              Text
	 */
	private function replace_yelp_variables( $text, $business ) {

		// Build categories.
		$categories = array();
		if ( isset( $business->categories ) && is_array( $business->categories ) && count( $business->categories ) > 0 ) {
			foreach ( $business->categories as $category ) {
				$categories[] = $category->title;
			}
		}

		// Define search and replacements.
		$searches = array(
			'%business_name%',
			'%business_address1%',
			'%business_address2%',
			'%business_address3%',
			'%business_city%',
			'%business_zip_code%',
			'%business_country%',
			'%business_state%',
			'%business_display_address%',
			'%business_phone%',
			'%business_display_phone%',
			'%business_distance%',
			'%business_categories%',
		);

		$replacements = array(
			$business->name,
			( isset( $business->location->address1 ) ? $business->location->address1 : '' ),
			( isset( $business->location->address2 ) ? $business->location->address2 : '' ),
			( isset( $business->location->address3 ) ? $business->location->address3 : '' ),
			( isset( $business->location->city ) ? $business->location->city : '' ),
			( isset( $business->location->zip_code ) ? $business->location->zip_code : '' ),
			( isset( $business->location->country ) ? $business->location->country : '' ),
			( isset( $business->location->state ) ? $business->location->state : '' ),
			implode( ', ', $business->location->display_address ),
			$business->phone,
			$business->display_phone,
			$business->distance,
			implode( ', ', $categories ),
		);

		// Perform search and replace.
		$text = str_ireplace( $searches, $replacements, $text );

		// Return.
		return $text;

	}

}
