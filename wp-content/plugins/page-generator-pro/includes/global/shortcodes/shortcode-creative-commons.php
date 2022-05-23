<?php
/**
 * Creative Commons Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Creative Commons Dynamic Element
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.6.9
 */
class Page_Generator_Pro_Shortcode_Creative_Commons extends Page_Generator_Pro_Shortcode_Base {

	/**
	 * Holds the base object.
	 *
	 * @since   2.6.9
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Constructor
	 *
	 * @since   2.6.9
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
	 * @since   2.6.9
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
	 * @since   2.6.9
	 *
	 * @return  string
	 */
	public function get_name() {

		return 'creative-commons';

	}

	/**
	 * Returns this shortcode / block's Title, Icon, Categories, Keywords
	 * and properties for registering on generation and requiring CSS/JS.
	 *
	 * @since   2.6.9
	 *
	 * @return  array
	 */
	public function get_overview() {

		return array(
			'title'                       => __( 'Creative Commons', 'page-generator-pro' ),
			'description'                 => __( 'Displays an image from Creative Commons, based on the given search parameters.', 'page-generator-pro' ),
			'icon'                        => $this->base->plugin->url . '/assets/images/icons/creative-commons.svg',
			'category'                    => $this->base->plugin->name,
			'keywords'                    => array(
				__( 'Creative Commons', 'page-generator-pro' ),
				__( 'Dynamic Image', 'page-generator-pro' ),
				__( 'Image', 'page-generator-pro' ),
			),

			// Shortcode: TinyMCE / QuickTags Modal Width and Height.
			'modal'                       => array(
				'width'  => 800,
				'height' => 600,
			),

			// Register when Generation is running only.
			'register_on_generation_only' => true,

			// Requires CSS and/or JS for output.
			'requires_css'                => false,
			'requires_js'                 => false,

			// Function to call when rendering as a block or a shortcode on the frontend web site.
			'render_callback'             => array( 'shortcode_creative_commons', 'render' ),

			// Gutenberg: Block Icon in Editor.
			'gutenberg_icon'              => file_get_contents( $this->base->plugin->folder . '/assets/images/icons/creative-commons.svg' ), /* phpcs:ignore */
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
			'orientation'          => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'orientation' ) ? '' : $this->get_default_value( 'orientation' ) ),
			),
			'licenses'             => array(
				'type'      => 'array',
				'delimiter' => ',',
			),
			'sources'              => array(
				'type'      => 'array',
				'delimiter' => ',',
			),

			// Output.
			'copy'                 => array(
				'type'    => 'boolean',
				'default' => $this->get_default_value( 'copy' ),
			),
			'size'                 => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'size' ) ? '' : $this->get_default_value( 'size' ) ),
			),
			'title'                => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'title' ) ? '' : $this->get_default_value( 'title' ) ),
			),
			'caption'              => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'caption' ) ? '' : $this->get_default_value( 'caption' ) ),
			),
			'alt_tag'              => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'alt_tag' ) ? '' : $this->get_default_value( 'alt_tag' ) ),
			),
			'description'          => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'description' ) ? '' : $this->get_default_value( 'description' ) ),
			),
			'filename'             => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'filename' ) ? '' : $this->get_default_value( 'filename' ) ),
			),
			'attribution'          => array(
				'type'    => 'boolean',
				'default' => $this->get_default_value( 'attribution' ),
			),

			// Link.
			'link_href'            => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'link_href' ) ? '' : $this->get_default_value( 'link_href' ) ),
			),
			'link_title'           => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'link_title' ) ? '' : $this->get_default_value( 'link_title' ) ),
			),
			'link_rel'             => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'link_rel' ) ? '' : $this->get_default_value( 'link_rel' ) ),
			),
			'link_target'          => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'link_target' ) ? '' : $this->get_default_value( 'link_target' ) ),
			),

			// EXIF.
			'exif_latitude'        => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'exif_latitude' ) ? '' : $this->get_default_value( 'exif_latitude' ) ),
			),
			'exif_longitude'       => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'exif_longitude' ) ? '' : $this->get_default_value( 'exif_longitude' ) ),
			),
			'exif_comments'        => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'exif_comments' ) ? '' : $this->get_default_value( 'exif_comments' ) ),
			),
			'exif_description'     => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'exif_description' ) ? '' : $this->get_default_value( 'exif_description' ) ),
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
	 * @since   2.6.9
	 *
	 * @return  array
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
			// Search Parameters.
			'term'             => array(
				'label'       => __( 'Term', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'placeholder' => __( 'e.g. building', 'page-generator-pro' ),
				'description' => __( 'The search term to use.  For example, "laptop" would return an image of a laptop.', 'page-generator-pro' ),
			),
			'orientation'      => array(
				'label'         => __( 'Image Orientation', 'page-generator-pro' ),
				'type'          => 'select',
				'values'        => $this->base->get_class( 'creative_commons' )->get_image_orientations(),
				'default_value' => $this->get_default_value( 'orientation' ),
				'description'   => __( 'The image orientation to output.', 'page-generator-pro' ),
			),
			'sources'          => array(
				'label'       => __( 'Sources', 'page-generator-pro' ),
				'type'        => 'select_multiple',
				'values'      => $this->base->get_class( 'creative_commons' )->get_sources(),
				'class'       => 'wpzinc-selectize-drag-drop',
				'description' => __( 'The sources to search. If none are selected, all sources are searched. Specifying sources may result in slower generation times.', 'page-generator-pro' ),
			),
			'licenses'         => array(
				'label'       => __( 'Licenses', 'page-generator-pro' ),
				'type'        => 'select_multiple',
				'values'      => $this->base->get_class( 'creative_commons' )->get_licenses(),
				'class'       => 'wpzinc-selectize-drag-drop',
				'description' => __( 'The image licenses to include. If none are selected, all licenses are included.  Specifying licenses may result in slower generation times.', 'page-generator-pro' ),
			),

			// Output.
			'copy'             => array(
				'label'         => __( 'Save to Library?', 'page-generator-pro' ),
				'type'          => 'toggle',
				'class'         => 'wpzinc-conditional',
				'data'          => array(
					// .components-panel is Gutenberg.
					// .creative-commons is TinyMCE.
					'container' => '.components-panel, .creative-commons',
				),
				'description'   => __( 'If enabled, stores the found image in the Media Library. Additional attributes, such as Caption, Filename and EXIF metadata can then be set.', 'page-generator-pro' ),
				'default_value' => $this->get_default_value( 'copy' ),
			),
			'size'             => array(
				'label'         => __( 'Image Size', 'page-generator-pro' ),
				'type'          => 'select',
				'values'        => $this->base->get_class( 'common' )->get_media_library_image_size_options(),
				'default_value' => $this->get_default_value( 'size' ),
				'description'   => __( 'The image size to output.', 'page-generator-pro' ),
				'condition'     => array(
					'key'        => 'copy',
					'value'      => 1,
					'comparison' => '==',
				),
			),
			'title'            => array(
				'label'       => __( 'Title', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'placeholder' => __( 'e.g. building', 'page-generator-pro' ),
				'description' => __( 'Define the title for the image.', 'page-generator-pro' ),
			),
			'caption'          => array(
				'label'       => __( 'Caption', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'placeholder' => __( 'e.g. building', 'page-generator-pro' ),
				'description' => __( 'Define the caption for the image.', 'page-generator-pro' ),
				'condition'   => array(
					'key'        => 'copy',
					'value'      => 1,
					'comparison' => '==',
				),
			),
			'alt_tag'          => array(
				'label'       => __( 'Alt Tag', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'placeholder' => __( 'e.g. building', 'page-generator-pro' ),
				'description' => __( 'Define the alt text for the image.', 'page-generator-pro' ),
			),
			'description'      => array(
				'label'       => __( 'Description', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'placeholder' => __( 'e.g. building', 'page-generator-pro' ),
				'description' => __( 'Define the description for the image.', 'page-generator-pro' ),
				'condition'   => array(
					'key'        => 'copy',
					'value'      => 1,
					'comparison' => '==',
				),
			),
			'filename'         => array(
				'label'       => __( 'Filename', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'placeholder' => __( 'e.g. building', 'page-generator-pro' ),
				'description' => __( 'Define the filename for the image, excluding the extension.', 'page-generator-pro' ),
				'condition'   => array(
					'key'        => 'copy',
					'value'      => 1,
					'comparison' => '==',
				),
			),
			'attribution'      => array(
				'label'       => __( 'Show Attribution?', 'page-generator-pro' ),
				'type'        => 'toggle',
				'description' => __( 'If enabled, outputs the credits/attribution below the image.', 'page-generator-pro' ),
			),

			// Link.
			'link_href'        => array(
				'label'       => __( 'Link', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'description' => __( 'Define the link for the image. Leave blank for no link.', 'page-generator-pro' ),
			),
			'link_title'       => array(
				'label'       => __( 'Link Title', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'description' => __( 'Define the link title for the image.', 'page-generator-pro' ),
			),
			'link_rel'         => array(
				'label'       => __( 'Link Rel', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'description' => __( 'Define the link rel attribute for the image.', 'page-generator-pro' ),
			),
			'link_target'      => array(
				'label'         => __( 'Link Target', 'page-generator-pro' ),
				'type'          => 'select',
				'description'   => __( 'Define the link target for the image.', 'page-generator-pro' ),
				'values'        => $this->base->get_class( 'common' )->get_link_target_options(),
				'default_value' => $this->get_default_value( 'link_target' ),
			),

			// EXIF.
			'exif_latitude'    => array(
				'label'     => __( 'Latitude', 'page-generator-pro' ),
				'type'      => 'autocomplete',
				'values'    => $keywords,
				'condition' => array(
					'key'        => 'copy',
					'value'      => 1,
					'comparison' => '==',
				),
			),
			'exif_longitude'   => array(
				'label'     => __( 'Longitude', 'page-generator-pro' ),
				'type'      => 'autocomplete',
				'values'    => $keywords,
				'condition' => array(
					'key'        => 'copy',
					'value'      => 1,
					'comparison' => '==',
				),
			),
			'exif_comments'    => array(
				'label'       => __( 'Comments', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'placeholder' => __( 'e.g. building', 'page-generator-pro' ),
				'condition'   => array(
					'key'        => 'copy',
					'value'      => 1,
					'comparison' => '==',
				),
			),
			'exif_description' => array(
				'label'       => __( 'Description', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'placeholder' => __( 'e.g. building', 'page-generator-pro' ),
				'condition'   => array(
					'key'        => 'copy',
					'value'      => 1,
					'comparison' => '==',
				),
			),
		);

	}

	/**
	 * Returns this shortcode / block's UI Tabs
	 *
	 * @since   2.6.9
	 *
	 * @return  array
	 */
	public function get_tabs() {

		if ( ! $this->base->is_admin_or_frontend_editor() ) {
			return false;
		}

		return array(
			'search-parameters' => array(
				'label'       => __( 'Search Parameters', 'page-generator-pro' ),
				'description' => __( 'Defines search query parameters to fetch an image at random from Creative Commons.', 'page-generator-pro' ),
				'class'       => 'search',
				'fields'      => array(
					'term',
					'orientation',
					'sources',
					'licenses',
				),
			),
			'output'            => array(
				'label'       => __( 'Output', 'page-generator-pro' ),
				'description' => __( 'Defines output parameters for the Creative Commons image.', 'page-generator-pro' ),
				'class'       => 'image',
				'fields'      => array(
					'copy',
					'size',
					'title',
					'alt_tag',
					'caption',
					'description',
					'filename',
					'attribution',
				),
			),
			'link'              => array(
				'label'       => __( 'Link', 'page-generator-pro' ),
				'description' => __( 'Defines parameters for linking the Creative Commons image.', 'page-generator-pro' ),
				'class'       => 'link',
				'fields'      => array(
					'link_href',
					'link_title',
					'link_rel',
					'link_target',
				),
			),
			'exif'              => array(
				'label'       => __( 'EXIF', 'page-generator-pro' ),
				'description' => __( 'Defines EXIF metadata to store in the image.  Available when Output > Save to Library is enabled.', 'page-generator-pro' ),
				'class'       => 'aperture',
				'fields'      => array(
					'exif_latitude',
					'exif_longitude',
					'exif_comments',
					'exif_description',
				),
			),
		);

	}

	/**
	 * Returns this shortcode / block's Default Values
	 *
	 * @since   2.6.9
	 *
	 * @return  array
	 */
	public function get_default_values() {

		return array(
			// Search Parameters.
			'term'             => false,
			'orientation'      => false,
			'licenses'         => array(),
			'sources'          => array(),

			// Output.
			'copy'             => true,
			'size'             => 'large',
			'title'            => false,
			'caption'          => false,
			'alt_tag'          => false,
			'description'      => false,
			'filename'         => false,
			'attribution'      => false,

			// Link.
			'link_href'        => false,
			'link_title'       => false,
			'link_rel'         => false,
			'link_target'      => '_self',

			// EXIF.
			'exif_description' => false,
			'exif_comments'    => false,
			'exif_latitude'    => false,
			'exif_longitude'   => false,
		);

	}

	/**
	 * Returns the given shortcode / block's field's Default Value
	 *
	 * @since   2.6.9
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
	 * @since   2.6.9
	 *
	 * @param  array $atts   Shortcode Attributes.
	 * @return string          Output
	 */
	public function render( $atts ) {

		// Parse attributes.
		$atts = $this->parse_atts( $atts );

		// Define the number of images to return to then choose one at random from.
		$per_page = 500;

		// Run query to fetch total number of pages of results that are available.
		$page_count = $this->base->get_class( 'creative_commons' )->page_count(
			$atts['term'],
			$atts['orientation'],
			$atts['licenses'],
			$atts['sources'],
			$per_page
		);
		if ( is_wp_error( $page_count ) ) {
			// Couldn't fetch an image, so don't show an image at all.
			if ( defined( 'PAGE_GENERATOR_PRO_DEBUG' ) && PAGE_GENERATOR_PRO_DEBUG === true ) {
				return $page_count->get_error_message();
			}

			return '';
		}

		// Pick a page index at random from the resultset.
		if ( $page_count === 1 ) {
			$page_index = 1;
		} else {
			$page_index = wp_rand( 1, $page_count );
		}

		// Run images query, using the random page index.
		$images = $this->base->get_class( 'creative_commons' )->photos_search(
			$atts['term'],
			$atts['orientation'],
			$atts['licenses'],
			$atts['sources'],
			$per_page,
			$page_index
		);
		if ( is_wp_error( $images ) ) {
			// Couldn't fetch an image, so don't show an image at all.
			if ( defined( 'PAGE_GENERATOR_PRO_DEBUG' ) && PAGE_GENERATOR_PRO_DEBUG === true ) {
				return $images->get_error_message();
			}

			return '';
		}

		// Pick an image at random from the resultset.
		$image = $this->choose_random_image( $images );

		// Import the image.
		$image_id = false;
		if ( $atts['copy'] ) {
			$image_id = $this->import( $image, $atts );

			// Bail if an error occured.
			if ( is_wp_error( $image_id ) ) {
				if ( defined( 'PAGE_GENERATOR_PRO_DEBUG' ) && PAGE_GENERATOR_PRO_DEBUG === true ) {
					return $image_id->get_error_message();
				}

				return '';
			}
		}

		// Get HTML image tag.
		$html = $this->get_image_html( $image_id, $atts, $image );

		/**
		 * Filter the Creative Commons HTML output, before returning.
		 *
		 * @since   2.6.9
		 *
		 * @param   string  $html       HTML Output.
		 * @param   array   $atts       Shortcode Attributes.
		 * @param   mixed   $image_id   WordPress Media Library Image ID (false = not imported into Media Library as copy=0).
		 * @param   array   $images     Creative Commons Image Results.
		 * @param   array   $image      Creative Commons Image chosen at random and imported into the Media Library.
		 */
		$html = apply_filters( 'page_generator_pro_shortcode_creative_commons', $html, $atts, $image_id, $images, $image );

		// Return.
		return $html;

	}

}
