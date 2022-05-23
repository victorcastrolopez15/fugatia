<?php
/**
 * Wikipedia Image Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Wikipedia Image Dynamic Element
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 3.1.7
 */
class Page_Generator_Pro_Shortcode_Wikipedia_Image extends Page_Generator_Pro_Shortcode_Base {

	/**
	 * Holds the base object.
	 *
	 * @since   3.1.7
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Constructor
	 *
	 * @since   3.1.7
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
	 * @since   3.1.7
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
	 * @since   3.1.7
	 */
	public function get_name() {

		return 'wikipedia-image';

	}

	/**
	 * Returns this shortcode / block's Title, Icon, Categories, Keywords
	 * and properties for registering on generation and requiring CSS/JS.
	 *
	 * @since   3.1.7
	 */
	public function get_overview() {

		return array(
			'title'                       => __( 'Wikipedia Image', 'page-generator-pro' ),
			'description'                 => __( 'Displays an image from Wikipedia, based on the given search parameters.', 'page-generator-pro' ),
			'icon'                        => $this->base->plugin->url . '/assets/images/icons/wikipedia-image.svg',
			'category'                    => $this->base->plugin->name,
			'keywords'                    => array(
				__( 'Wikipedia Image', 'page-generator-pro' ),
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
			'render_callback'             => array( 'shortcode_wikipedia_image', 'render' ),
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
				'type'      => 'array',
				'delimiter' => ';',
			),
			'language'             => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'language' ) ? '' : $this->get_default_value( 'language' ) ),
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
	 * @since   3.1.7
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
				'label'       => __( 'Term(s) / URL(s)', 'page-generator-pro' ),
				'type'        => 'text_multiple',
				'data'        => array(
					'delimiter' => ';',
				),
				'class'       => 'wpzinc-selectize-freeform',
				'description' => __( 'Specify one or more terms or Wikipedia URLs to search for on Wikipedia, in order. An image will be used at random from the first term / URL that produces a matching Wikipedia Page', 'page-generator-pro' ),
			),
			'language'         => array(
				'label'         => __( 'Language', 'page-generator-pro' ),
				'type'          => 'select',
				'values'        => $this->base->get_class( 'wikipedia' )->get_languages(),
				'default_value' => $this->get_default_value( 'language' ),
			),

			// Output.
			'copy'             => array(
				'label'         => __( 'Save to Library?', 'page-generator-pro' ),
				'type'          => 'toggle',
				'class'         => 'wpzinc-conditional',
				'data'          => array(
					// .components-panel is Gutenberg.
					// .creative-commons is TinyMCE.
					'container' => '.components-panel, .wikipedia-image',
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
	 * @since   3.1.7
	 */
	public function get_tabs() {

		if ( ! $this->base->is_admin_or_frontend_editor() ) {
			return false;
		}

		return array(
			'search-parameters' => array(
				'label'       => __( 'Search Parameters', 'page-generator-pro' ),
				'description' => __( 'Defines search query parameters to fetch an image at random from Wikipedia.', 'page-generator-pro' ),
				'class'       => 'search',
				'fields'      => array(
					'term',
					'language',
				),
			),
			'output'            => array(
				'label'       => __( 'Output', 'page-generator-pro' ),
				'description' => __( 'Defines output parameters for the Wikipedia image.', 'page-generator-pro' ),
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
				'description' => __( 'Defines parameters for linking the Wikipedia image.', 'page-generator-pro' ),
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
	 * @since   3.1.7
	 */
	public function get_default_values() {

		return array(
			// Search Parameters.
			'term'             => array(),
			'language'         => 'en',

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
	 * @since   3.1.7
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
	 * @since   3.1.7
	 *
	 * @param  array $atts   Shortcode Attributes.
	 * @return string          Output
	 */
	public function render( $atts ) {

		// Parse attributes.
		$atts = $this->parse_atts( $atts );

		// Iterate through terms until we find a page.
		$errors = array();
		foreach ( $atts['term'] as $term ) {
			// Skip empty Terms.
			if ( empty( $term ) ) {
				continue;
			}

			// Run images query.
			$images = $this->base->get_class( 'wikipedia' )->get_images( $term, $atts['language'] );

			// Collect errors.
			if ( is_wp_error( $images ) ) {
				$errors[] = sprintf(
					/* translators: %1$s: Search Term, %2$s: Error message */
					__( 'Term: %1$s: Response: %2$s', 'page-generator-pro' ),
					$term,
					$images->get_error_message()
				);
				continue;
			}

			// If here, we managed to fetch elements.
			// Unset errors and break the loop.
			unset( $errors );
			break;
		}

		// If errors exist, bail.
		if ( isset( $errors ) && count( $errors ) > 0 ) {
			if ( defined( 'PAGE_GENERATOR_PRO_DEBUG' ) && PAGE_GENERATOR_PRO_DEBUG === true ) {
				/* translators: Error message */
				return sprintf( __( 'Wikipedia:<br />%s', 'page-generator-pro' ), implode( '<br />', $errors ) );
			}

			return '';
		}

		// If no images exist, bail.
		if ( ! isset( $images ) ) {
			if ( defined( 'PAGE_GENERATOR_PRO_DEBUG' ) && PAGE_GENERATOR_PRO_DEBUG === true ) {
				/* translators: Error message */
				return sprintf( __( 'Wikipedia:<br />%s', 'page-generator-pro' ), __( 'The term parameter is missing.', 'page-generator-pro' ) );
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

		// Get HTML image tag, with the image matching the given WordPress registered image size.
		$html = $this->get_image_html( $image_id, $atts, $image );

		/**
		 * Filter the Wikipedia Image HTML output, before returning.
		 *
		 * @since   3.1.7
		 *
		 * @param   string  $html       HTML Output.
		 * @param   array   $atts       Shortcode Attributes.
		 * @param   mixed   $image_id   WordPress Media Library Image ID (false = not imported into Media Library as copy=0).
		 * @param   array   $images     Wikipedia Image Results.
		 * @param   array   $image      Wikipedia Image chosen at random and imported into the Media Library.
		 */
		$html = apply_filters( 'page_generator_pro_shortcode_wikipedia_image', $html, $atts, $image_id, $images, $image );

		// Return.
		return $html;

	}

}
