<?php
/**
 * Media Library Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Media Library Dynamic Element
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.5.1
 */
class Page_Generator_Pro_Shortcode_Media_Library extends Page_Generator_Pro_Shortcode_Base {

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

		return 'media-library';

	}

	/**
	 * Returns this shortcode / block's Title, Icon, Categories, Keywords
	 * and properties for registering on generation and requiring CSS/JS.
	 *
	 * @since   2.5.1
	 */
	public function get_overview() {

		return array(
			'title'                       => __( 'Media Library', 'page-generator-pro' ),
			'description'                 => __( 'Displays an image from the Media Library', 'page-generator-pro' ),
			'icon'                        => $this->base->plugin->url . '/assets/images/icons/admin-media.svg',
			'category'                    => $this->base->plugin->name,
			'keywords'                    => array(
				__( 'Media Library', 'page-generator-pro' ),
				__( 'Media', 'page-generator-pro' ),
				__( 'Library', 'page-generator-pro' ),
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
			'render_callback'             => array( 'shortcode_media_library', 'render' ),

			// Gutenberg: Block Icon in Editor.
			'gutenberg_icon'              => file_get_contents( $this->base->plugin->folder . '/assets/images/icons/admin-media.svg' ), /* phpcs:ignore */
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
			'operator'             => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'operator' ) ? '' : $this->get_default_value( 'operator' ) ),
			),
			'ids'                  => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'ids' ) ? '' : $this->get_default_value( 'ids' ) ),
			),
			'min_id'               => array(
				'type'    => 'number',
				'default' => $this->get_default_value( 'min_id' ),
			),
			'max_id'               => array(
				'type'    => 'number',
				'default' => $this->get_default_value( 'max_id' ),
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
			'output_title'         => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'output_title' ) ? '' : $this->get_default_value( 'output_title' ) ),
			),
			'output_caption'       => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'output_caption' ) ? '' : $this->get_default_value( 'output_caption' ) ),
			),
			'output_alt_tag'       => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'output_alt_tag' ) ? '' : $this->get_default_value( 'output_alt_tag' ) ),
			),
			'output_description'   => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'output_description' ) ? '' : $this->get_default_value( 'output_description' ) ),
			),
			'output_filename'      => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'output_filename' ) ? '' : $this->get_default_value( 'output_filename' ) ),
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
			// Search Parameters.
			'title'              => array(
				'label'       => __( 'Title', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'description' => __( 'Fetch an image at random with a partial or full match to the given Title.', 'page-generator-pro' ),
			),
			'caption'            => array(
				'label'       => __( 'Caption', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'description' => __( 'Fetch an image at random with a partial or full match to the given Caption.', 'page-generator-pro' ),
			),
			'alt'                => array(
				'label'       => __( 'Alt Text', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'description' => __( 'Fetch an image at random with a partial or full match to the given Alt Text.', 'page-generator-pro' ),
			),
			'description'        => array(
				'label'       => __( 'Description', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'description' => __( 'Fetch an image at random with a partial or full match to the given Description.', 'page-generator-pro' ),
			),
			'filename'           => array(
				'label'       => __( 'Filename', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'description' => __( 'Fetch an image at random with a partial or full match to the given Filename.', 'page-generator-pro' ),
			),
			'operator'           => array(
				'label'         => __( 'Operator', 'page-generator-pro' ),
				'type'          => 'select',
				'values'        => $keywords,
				'description'   => __( 'Determines whether images should match all or any of the Title, Caption, Alt Text and Descriptions specified above.', 'page-generator-pro' ),
				'values'        => $this->base->get_class( 'common' )->get_operator_options(),
				'default_value' => $this->get_default_value( 'operator' ),
			),
			'ids'                => array(
				'label'       => __( 'Image IDs', 'page-generator-pro' ),
				'type'        => 'text',
				'description' => __( 'Comma separated list of Media Library Image ID(s) to use.  If multiple image IDs are specified, one will be chosen at random for each generated Page.', 'page-generator-pro' ),
			),
			'min_id'             => array(
				'label'       => __( 'Minimum Image ID', 'page-generator-pro' ),
				'type'        => 'number',
				'min'         => 0,
				'max'         => 9999999,
				'step'        => 1,
				'description' => __( 'Fetch an image whose ID matches or is greater than the given value.', 'page-generator-pro' ),
			),
			'max_id'             => array(
				'label'       => __( 'Maximum Image ID', 'page-generator-pro' ),
				'type'        => 'number',
				'min'         => 0,
				'max'         => 9999999,
				'step'        => 1,
				'description' => __( 'Fetch an image whose ID matches or is less than the given value.', 'page-generator-pro' ),
			),

			// Output.
			'copy'               => array(
				'label'         => __( 'Create as Copy', 'page-generator-pro' ),
				'type'          => 'toggle',
				'description'   => __( 'Store the found image as a new copy in the Media Library.', 'page-generator-pro' ),
				'default_value' => $this->get_default_value( 'copy' ),
			),
			'size'               => array(
				'label'         => __( 'Image Size', 'page-generator-pro' ),
				'type'          => 'select',
				'description'   => __( 'The image size to output.', 'page-generator-pro' ),
				'values'        => $this->base->get_class( 'common' )->get_media_library_image_size_options(),
				'default_value' => $this->get_default_value( 'size' ),
			),
			'output_title'       => array(
				'label'       => __( 'Title', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'description' => __( 'Define the title for the image.', 'page-generator-pro' ),
			),
			'output_caption'     => array(
				'label'       => __( 'Caption', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'description' => __( 'Define the caption for the image.', 'page-generator-pro' ),
			),
			'output_alt_tag'     => array(
				'label'       => __( 'Alt Tag', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'description' => __( 'Define the alt text for the image.', 'page-generator-pro' ),
			),
			'output_description' => array(
				'label'       => __( 'Description', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'description' => __( 'Define the description for the image.', 'page-generator-pro' ),
			),
			'output_filename'    => array(
				'label'       => __( 'Filename', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'description' => __( 'Define the filename for the image, excluding the extension.', 'page-generator-pro' ),
			),

			// Link.
			'link_href'          => array(
				'label'       => __( 'Link', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'description' => __( 'Define the link for the image. Leave blank for no link.', 'page-generator-pro' ),
			),
			'link_title'         => array(
				'label'       => __( 'Link Title', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'description' => __( 'Define the link title for the image.', 'page-generator-pro' ),
			),
			'link_rel'           => array(
				'label'       => __( 'Link Rel', 'page-generator-pro' ),
				'type'        => 'autocomplete',
				'values'      => $keywords,
				'description' => __( 'Define the link rel attribute for the image.', 'page-generator-pro' ),
			),
			'link_target'        => array(
				'label'         => __( 'Link Target', 'page-generator-pro' ),
				'type'          => 'select',
				'description'   => __( 'Define the link target for the image.', 'page-generator-pro' ),
				'values'        => $this->base->get_class( 'common' )->get_link_target_options(),
				'default_value' => $this->get_default_value( 'link_target' ),
			),

			// EXIF.
			'exif_latitude'      => array(
				'label'  => __( 'Latitude', 'page-generator-pro' ),
				'type'   => 'autocomplete',
				'values' => $keywords,
			),
			'exif_longitude'     => array(
				'label'  => __( 'Longitude', 'page-generator-pro' ),
				'type'   => 'autocomplete',
				'values' => $keywords,
			),
			'exif_comments'      => array(
				'label'  => __( 'Comments', 'page-generator-pro' ),
				'type'   => 'autocomplete',
				'values' => $keywords,
			),
			'exif_description'   => array(
				'label'  => __( 'Description', 'page-generator-pro' ),
				'type'   => 'autocomplete',
				'values' => $keywords,
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
				'description' => __( 'Defines search query parameters to fetch an image at random from the Media Library.', 'page-generator-pro' ),
				'class'       => 'search',
				'fields'      => array(
					'title',
					'caption',
					'alt',
					'description',
					'filename',
					'operator',
					'ids',
					'min_id',
					'max_id',
				),
			),
			'output'            => array(
				'label'       => __( 'Output', 'page-generator-pro' ),
				'description' => __( 'Defines output parameters for the Media Library image.', 'page-generator-pro' ),
				'class'       => 'image',
				'fields'      => array(
					'copy',
					'size',
					'output_title',
					'output_alt_tag',
					'output_caption',
					'output_description',
					'output_filename',
				),
			),
			'link'              => array(
				'label'       => __( 'Link', 'page-generator-pro' ),
				'description' => __( 'Defines parameters for linking the Media Library image.', 'page-generator-pro' ),
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
	 * @since   2.5.1
	 */
	public function get_default_values() {

		return array(
			// Search Parameters.
			'title'              => false,
			'caption'            => false,
			'alt'                => false,
			'description'        => false,
			'filename'           => false,
			'operator'           => 'OR',
			'ids'                => false,
			'min_id'             => 0,
			'max_id'             => 0,

			// Output.
			'size'               => 'medium',
			'copy'               => false,
			'output_title'       => false,
			'output_description' => false,
			'output_caption'     => false,
			'output_alt_tag'     => false,
			'output_filename'    => false,

			// Link.
			'link_href'          => false,
			'link_title'         => false,
			'link_rel'           => false,
			'link_target'        => '_self',

			// EXIF.
			'exif_description'   => false,
			'exif_comments'      => false,
			'exif_latitude'      => false,
			'exif_longitude'     => false,
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

		// Get Random Image ID matching attributes.
		$image_id = $this->base->get_class( 'media_library' )->get_random_image_id( $atts );
		if ( ! $image_id ) {
			if ( defined( 'PAGE_GENERATOR_PRO_DEBUG' ) && PAGE_GENERATOR_PRO_DEBUG === true ) {
				return __( 'Media Library: No image could be found.', 'page-generator-pro' );
			}

			return '';
		}

		// If we're copying the image to a new Media Library attachment, do this now.
		if ( $atts['copy'] ) {
			// Get image.
			$image = wp_get_attachment_image_src( $image_id, 'full' );
			if ( ! $image ) {
				if ( defined( 'PAGE_GENERATOR_PRO_DEBUG' ) && PAGE_GENERATOR_PRO_DEBUG === true ) {
					return __( 'Media Library: Could not copy image.', 'page-generator-pro' );
				}

				return '';
			}

			// Copy to new image.
			$image_id = $this->base->get_class( 'import' )->import_remote_image(
				$image[0],
				0,
				$this->base->get_class( 'shortcode' )->get_group_id(),
				$this->base->get_class( 'shortcode' )->get_index(),
				( ! $atts['output_filename'] ? false : $atts['output_filename'] ), // filename.
				( ! $atts['output_title'] ? '' : $atts['output_title'] ), // title.
				( ! $atts['output_caption'] ? '' : $atts['output_caption'] ), // caption.
				( ! $atts['output_alt_tag'] ? '' : $atts['output_alt_tag'] ), // alt_tag.
				( ! $atts['output_description'] ? '' : $atts['output_description'] ) // description.
			);

			// Bail if an error occured.
			if ( is_wp_error( $image_id ) ) {
				if ( defined( 'PAGE_GENERATOR_PRO_DEBUG' ) && PAGE_GENERATOR_PRO_DEBUG === true ) {
					/* translators: Error message */
					return sprintf( __( 'Media Library: %s', 'page-generator-pro' ), $image_id->get_error_message() );
				}

				return '';
			}
		}

		// Cast image ID.
		$image_id = absint( $image_id );

		// Store EXIF Data in Image.
		$exif = $this->base->get_class( 'exif' )->write(
			$image_id,
			$atts['exif_description'],
			$atts['exif_comments'],
			$atts['exif_latitude'],
			$atts['exif_longitude']
		);
		if ( is_wp_error( $exif ) ) {
			if ( defined( 'PAGE_GENERATOR_PRO_DEBUG' ) && PAGE_GENERATOR_PRO_DEBUG === true ) {
				/* translators: Error message */
				return sprintf( __( 'Media Library: %s', 'page-generator-pro' ), $exif->get_error_message() );
			}

			// Allow processing to continue as we can output an image; we just couldn't store the EXIF data.
		}

		// Get image HTML.
		$html = $this->base->get_class( 'media_library' )->get_image_html_tag_by_id(
			$image_id,
			array(
				'size'    => $atts['size'],
				'alt_tag' => $atts['output_alt_tag'],
			)
		);

		// If a link is specified, wrap the image in the link now.
		if ( ! empty( $atts['link_href'] ) ) {
			$link = '<a href="' . $atts['link_href'] . '"';

			// Add title, if specified.
			if ( ! empty( $atts['link_title'] ) ) {
				$link .= ' title="' . $atts['link_title'] . '"';
			}

			// Add rel attribute, if specified.
			if ( ! empty( $atts['link_rel'] ) ) {
				$link .= ' rel="' . $atts['link_rel'] . '"';
			}

			// Add target, if specified.
			if ( ! empty( $atts['link_target'] ) ) {
				$link .= ' target="' . $atts['link_target'] . '"';
			}

			$link .= '>';

			$html = $link . $html . '</a>';
		}

		/**
		 * Filter the Media Library HTML output, before returning.
		 *
		 * @since   1.0.0
		 *
		 * @param   string  $html   HTML Output.
		 * @param   array   $atts   Shortcode Attributes.
		 */
		$html = apply_filters( 'page_generator_pro_shortcode_media_library', $html, $atts );

		// Return.
		return $html;

	}

}
