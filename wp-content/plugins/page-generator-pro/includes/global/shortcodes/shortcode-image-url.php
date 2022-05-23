<?php
/**
 * Image URL Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Image URL Dynamic Element
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.8.8
 */
class Page_Generator_Pro_Shortcode_Image_URL extends Page_Generator_Pro_Shortcode_Base {

	/**
	 * Holds the base object.
	 *
	 * @since   2.8.8
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Constructor
	 *
	 * @since   2.8.8
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
	 * @since   2.8.8
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
	 * @since   2.8.8
	 */
	public function get_name() {

		return 'image-url';

	}

	/**
	 * Returns this shortcode / block's Title, Icon, Categories, Keywords
	 * and properties for registering on generation and requiring CSS/JS.
	 *
	 * @since   2.8.8
	 */
	public function get_overview() {

		return array(
			'title'                       => __( 'Image URL', 'page-generator-pro' ),
			'description'                 => __( 'Displays an image from an image URL.', 'page-generator-pro' ),
			'icon'                        => $this->base->plugin->url . '_modules/dashboard/feather/image.svg',
			'category'                    => $this->base->plugin->name,
			'keywords'                    => array(
				__( 'Image URL', 'page-generator-pro' ),
				__( 'Dynamic Image', 'page-generator-pro' ),
				__( 'Image', 'page-generator-pro' ),
			),

			// TinyMCE / QuickTags Modal Width and Height.
			'modal'                       => array(
				'width'  => 800,
				'height' => 600,
			),

			// Register when Generation is running only.
			'register_on_generation_only' => true,

			// Requires CSS and/or JS for output.
			'requires_css'                => false,
			'requires_js'                 => false,

			// Function to call when rendering the shortcode on the frontend.
			'render_callback'             => array( 'shortcode_image_url', 'render' ),

			// Gutenberg: Block Icon in Editor.
			'gutenberg_icon'              => file_get_contents( $this->base->plugin->folder . '_modules/dashboard/feather/image.svg' ), /* phpcs:ignore */
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
			'url'                  => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'url' ) ? '' : $this->get_default_value( 'url' ) ),
			),

			// Output.
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
	 * @since   2.8.8
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
			'url'              => array(
				'label'       => __( 'URL', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'placeholder' => __( 'e.g. http://placehold.it/300x300', 'page-generator-pro' ),
				'description' => __( 'The image URL to use.', 'page-generator-pro' ),
			),

			// Output.
			'size'             => array(
				'label'         => __( 'Image Size', 'page-generator-pro' ),
				'type'          => 'select',
				'values'        => $this->base->get_class( 'common' )->get_media_library_image_size_options(),
				'default_value' => $this->get_default_value( 'size' ),
				'description'   => __( 'The image size to output.', 'page-generator-pro' ),
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
			),
			'filename'         => array(
				'label'       => __( 'Filename', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'placeholder' => __( 'e.g. building', 'page-generator-pro' ),
				'description' => __( 'Define the filename for the image, excluding the extension.', 'page-generator-pro' ),
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
				'label'  => __( 'Latitude', 'page-generator-pro' ),
				'type'   => 'autocomplete',
				'values' => $keywords,
			),
			'exif_longitude'   => array(
				'label'  => __( 'Longitude', 'page-generator-pro' ),
				'type'   => 'autocomplete',
				'values' => $keywords,
			),
			'exif_comments'    => array(
				'label'       => __( 'Comments', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'placeholder' => __( 'e.g. building', 'page-generator-pro' ),
			),
			'exif_description' => array(
				'label'       => __( 'Description', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'placeholder' => __( 'e.g. building', 'page-generator-pro' ),
			),
		);

	}

	/**
	 * Returns this shortcode / block's UI Tabs
	 *
	 * @since   2.8.8
	 */
	public function get_tabs() {

		if ( ! $this->base->is_admin_or_frontend_editor() ) {
			return false;
		}

		return array(
			'search-parameters' => array(
				'label'       => __( 'Search Parameters', 'page-generator-pro' ),
				'description' => __( 'Defines the remote image URL.', 'page-generator-pro' ),
				'class'       => 'search',
				'fields'      => array(
					'url',
				),
			),
			'output'            => array(
				'label'       => __( 'Output', 'page-generator-pro' ),
				'description' => __( 'Defines output parameters for the remote image.', 'page-generator-pro' ),
				'class'       => 'image',
				'fields'      => array(
					'size',
					'title',
					'alt_tag',
					'caption',
					'description',
					'filename',
				),
			),
			'link'              => array(
				'label'       => __( 'Link', 'page-generator-pro' ),
				'description' => __( 'Defines parameters for linking the remote image.', 'page-generator-pro' ),
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
				'description' => __( 'Defines EXIF metadata to store in the image.', 'page-generator-pro' ),
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
	 * @since   2.8.8
	 */
	public function get_default_values() {

		return array(
			// Search Parameters.
			'url'              => '',

			// Output.
			'size'             => 'large',
			'title'            => false,
			'caption'          => false,
			'alt_tag'          => false,
			'description'      => false,
			'filename'         => false,

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
	 * @since   2.8.8
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
	 * @since   2.8.8
	 *
	 * @param  array $atts   Shortcode Attributes.
	 * @return string          Output
	 */
	public function render( $atts ) {

		// Parse attributes.
		$atts = $this->parse_atts( $atts );

		// Import the image.
		$image_id = $this->base->get_class( 'import' )->import_remote_image(
			$atts['url'],
			0,
			$this->base->get_class( 'shortcode' )->get_group_id(),
			$this->base->get_class( 'shortcode' )->get_index(),
			$atts['filename'],
			( ! $atts['title'] ? '' : $atts['title'] ), // title.
			( ! $atts['caption'] ? '' : $atts['caption'] ), // caption.
			( ! $atts['alt_tag'] ? '' : $atts['alt_tag'] ), // alt_tag.
			( ! $atts['description'] ? '' : $atts['description'] ) // description.
		);

		// Bail if an error occured.
		if ( is_wp_error( $image_id ) ) {
			if ( defined( 'PAGE_GENERATOR_PRO_DEBUG' ) && PAGE_GENERATOR_PRO_DEBUG === true ) {
				/* translators: Error message */
				return sprintf( __( 'Remote Image: %s', 'page-generator-pro' ), $image_id->get_error_message() );
			}

			return '';
		}

		// Store EXIF Data in Image.
		$this->base->get_class( 'exif' )->write(
			$image_id,
			$atts['exif_description'],
			$atts['exif_comments'],
			$atts['exif_latitude'],
			$atts['exif_longitude']
		);

		// Get HTML image tag, with the image matching the given WordPress registered image size.
		$html = $this->get_image_html( $image_id, $atts );

		/**
		 * Filter the Image URL HTML output, before returning.
		 *
		 * @since   1.0.0
		 *
		 * @param   string  $html       HTML Output.
		 * @param   array   $atts       Shortcode Attributes.
		 * @param   int     $image_id   WordPress Media Library Image ID.
		 */
		$html = apply_filters( 'page_generator_pro_shortcode_image_url', $html, $atts, $image_id );

		// Return.
		return $html;

	}

}
