<?php
/**
 * Wikipedia Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Wikipedia Dynamic Element
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.5.1
 */
class Page_Generator_Pro_Shortcode_Wikipedia extends Page_Generator_Pro_Shortcode_Base {

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
	 * Returns this shortcode / block's programmatic name.
	 *
	 * @since   2.5.1
	 */
	public function get_name() {

		return 'wikipedia';

	}

	/**
	 * Returns this shortcode / block's Title, Icon, Categories, Keywords
	 * and properties for registering on generation and requiring CSS/JS.
	 *
	 * @since   2.5.1
	 */
	public function get_overview() {

		return array(
			'title'                       => __( 'Wikipedia', 'page-generator-pro' ),
			'description'                 => __( 'Displays content from Wikipedia based on the given Term(s).', 'page-generator-pro' ),
			'icon'                        => $this->base->plugin->url . '/_modules/dashboard/feather/wikipedia.svg',
			'category'                    => $this->base->plugin->name,
			'keywords'                    => array(
				__( 'Wikipedia', 'page-generator-pro' ),
				__( 'Wiki', 'page-generator-pro' ),
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
			'render_callback'             => array( 'shortcode_wikipedia', 'render' ),

			// Gutenberg: Block Icon in Editor.
			'gutenberg_icon'              => file_get_contents( $this->base->plugin->folder . '/_modules/dashboard/feather/wikipedia.svg' ), /* phpcs:ignore */
		);

	}

	/**
	 * Returns this block's Attributes and default values for Gutenberg.
	 *
	 * @since   3.6.3
	 */
	public function get_attributes() {

		return array(
			'term'                 => array(
				'type'      => 'array',
				'delimiter' => ';',
			),
			'use_similar_page'     => array(
				'type'    => 'boolean',
				'default' => $this->get_default_value( 'use_similar_page' ),
			),
			'language'             => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'language' ) ? '' : $this->get_default_value( 'language' ) ),
			),
			'sections'             => array(
				'type'      => 'array',
				'delimiter' => ';',
			),
			'elements'             => array(
				'type'      => 'array',
				'delimiter' => ',',
				'default'   => $this->get_default_value( 'elements' ),
			),
			'remove_links'         => array(
				'type'    => 'remove_links',
				'default' => $this->get_default_value( 'remove_links' ),
			),
			'paragraphs'           => array(
				'type'    => 'number',
				'default' => $this->get_default_value( 'paragraphs' ),
			),
			'source_link'          => array(
				'type'    => 'source_link',
				'default' => $this->get_default_value( 'source_link' ),
			),
			'source_link_text'     => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'source_link_text' ) ? '' : $this->get_default_value( 'source_link_text' ) ),
			),
			'apply_synonyms'       => array(
				'type'    => 'toggle',
				'default' => $this->get_default_value( 'apply_synonyms' ),
			),

			// Preview.
			'is_gutenberg_example' => array(
				'type'    => 'boolean',
				'default' => false,
			),
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

		return array(
			'term'             => array(
				'label'       => __( 'Term(s) / URL(s)', 'page-generator-pro' ),
				'type'        => 'text_multiple',
				'data'        => array(
					'delimiter' => ';',
				),
				'class'       => 'wpzinc-selectize-freeform',
				'description' => __( 'Specify one or more terms or Wikipedia URLs to search for on Wikipedia, in order. Contents will be used from the first term / URL that produces a matching Wikipedia Page', 'page-generator-pro' ),
			),
			'use_similar_page' => array(
				'label'         => __( 'Use Similar Page', 'page-generator-pro' ),
				'type'          => 'toggle',
				'default_value' => $this->get_default_value( 'use_similar_page' ),
				'description'   => __( 'If enabled, a similar Wikipedia Article will be used where a Term specified above could not be found, and Wikipedia provides alternate Articles when viewing said Term. Refer to the Documentation for more information.', 'page-generator-pro' ),
			),
			'language'         => array(
				'label'         => __( 'Language', 'page-generator-pro' ),
				'type'          => 'select',
				'values'        => $this->base->get_class( 'wikipedia' )->get_languages(),
				'default_value' => $this->get_default_value( 'language' ),
			),
			'sections'         => array(
				'label'       => __( 'Sections', 'page-generator-pro' ),
				'type'        => 'text_multiple',
				'data'        => array(
					'delimiter' => ';',
				),
				'class'       => 'wpzinc-selectize-freeform',
				'description' => __( 'Optional; specify one or more Wikipedia top level Table of Content sections to pull content from.  If no sections are specified, the summary (text before the Table of Contents) will be used.', 'page-generator-pro' ),
			),

			'elements'         => array(
				'label'         => __( 'Elements', 'page-generator-pro' ),
				'type'          => 'select_multiple',
				'default_value' => $this->get_default_value( 'elements' ),
				'values'        => $this->base->get_class( 'wikipedia' )->get_supported_elements(),
				'class'         => 'wpzinc-selectize-drag-drop',
				'description'   => __( 'Specify the HTML elements to return from the Wikipedia Article. If no elements are specified, paragraphs will be returned', 'page-generator-pro' ),
			),
			'remove_links'     => array(
				'label'         => __( 'Remove Links?', 'page-generator-pro' ),
				'type'          => 'toggle',
				'default_value' => $this->get_default_value( 'remove_links' ),
				'description'   => __( 'If enabled, any links found in the Wikipedia Article will be removed.', 'page-generator-pro' ),
			),
			'paragraphs'       => array(
				'label'         => __( 'Limit', 'page-generator-pro' ),
				'type'          => 'number',
				'min'           => 0,
				'max'           => 999,
				'step'          => 1,
				'default_value' => $this->get_default_value( 'paragraphs' ),
				'description'   => __( 'The maximum number of elements to output after all above sections have been fetched and combined.', 'page-generator-pro' ),
			),
			'source_link'      => array(
				'label'         => __( 'Output Source Link?', 'page-generator-pro' ),
				'type'          => 'toggle',
				'default_value' => $this->get_default_value( 'source_link' ),
				'description'   => __( 'If enabled, a nofollow link to the Wikipedia article will be placed at the end of the content.', 'page-generator-pro' ),
			),
			'source_link_text' => array(
				'label'         => __( 'Source Link Text', 'page-generator-pro' ),
				'type'          => 'text',
				'default_value' => $this->get_default_value( 'source_link_text' ),
				'description'   => __( 'The text to display which links to the Wikipedia Source Link, if enabled.', 'page-generator-pro' ),
			),
			'apply_synonyms'   => array(
				'label'         => __( 'Spin?', 'page-generator-pro' ),
				'type'          => 'toggle',
				'default_value' => $this->get_default_value( 'apply_synonyms' ),
				'description'   => __( 'If enabled, the Wikipedia content will be spun to produce a unique variation.', 'page-generator-pro' ),
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
				'label'  => __( 'Search Parameters', 'page-generator-pro' ),
				'class'  => 'search',
				'fields' => array(
					'term',
					'use_similar_page',
					'language',
				),
			),

			'output'  => array(
				'label'       => __( 'Output', 'page-generator-pro' ),
				'class'       => 'wikipedia',
				'description' => __( 'Defines the output of Wikipedia Content.', 'page-generator-pro' ),
				'fields'      => array(
					'sections',
					'elements',
					'remove_links',
					'paragraphs',
					'source_link',
					'source_link_text',
					'apply_synonyms',
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
			'term'             => array(),
			'use_similar_page' => 0,
			'language'         => 'en',

			'sections'         => array(),
			'elements'         => array( 'paragraphs' ),
			'remove_links'     => 1,               // Removes <a> links.
			'paragraphs'       => 0,               // Number of elements.
			'source_link'      => 0,
			'source_link_text' => __( 'Source', 'page-generator-pro' ),
			'apply_synonyms'   => 0,
		);

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

		// Iterate through terms until we find a page.
		$errors = array();
		foreach ( $atts['term'] as $term ) {
			// Skip empty term.
			if ( empty( $term ) ) {
				continue;
			}

			// Get elements from Wikipedia Page.
			$elements = $this->base->get_class( 'wikipedia' )->get_page_sections(
				$term,
				$atts['use_similar_page'],
				$atts['sections'],
				$atts['language'],
				$atts['elements'],
				$atts['remove_links']
			);

			// Collect errors.
			if ( is_wp_error( $elements ) ) {
				$errors[] = sprintf(
					/* translators: %1$s: Search Term, %2$s: Error message */
					__( 'Term: %1$s: Response: %2$s', 'page-generator-pro' ),
					$term,
					$elements->get_error_message()
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

		// If a paragraph limit has been specified, apply it now.
		if ( isset( $atts['paragraphs'] ) && is_numeric( $atts['paragraphs'] ) && $atts['paragraphs'] > 0 ) {
			$elements = array_slice( $elements, 0, absint( $atts['paragraphs'] ) );
		}

		// Convert array of content elements into string.
		$content = implode( '', $elements );

		// Apply synonyms for spintax, if enabled.
		if ( $atts['apply_synonyms'] ) {
			$result = $this->base->get_class( 'spintax' )->add_spintax( $content );

			// Only assign the spintax to the content, and process it, if there was no error.
			if ( ! is_wp_error( $result ) ) {
				$content = $this->base->get_class( 'spintax' )->process( $result );
			}
		}

		// Add Source Link, if required.
		if ( $atts['source_link'] ) {
			$source_link = $this->base->get_class( 'wikipedia' )->get_url();
			if ( ! empty( $source_link ) ) {
				$content .= '<small><a href="' . $source_link . '" target="_blank" rel="nofollow noopener">' . $atts['source_link_text'] . '</a></small>';
			}
		}

		// Build HTML.
		$html = '<div class="' . $this->base->plugin->name . '-wikipedia">' . $content . '</div>';

		/**
		 * Filter the Wikipedia Shortcode HTML output, before returning.
		 *
		 * @since   1.0.0
		 *
		 * @param   string  $html       HTML Output.
		 * @param   array   $atts       Shortcode Attributes.
		 * @param   string  $build      Wikipedia Content.
		 * @param   array   $elements   Wikipedia Elements in Wikipedia Article based on $atts.
		 */
		$html = apply_filters( 'page_generator_pro_shortcode_wikipedia', $html, $atts, $content, $elements );

		// Return.
		return $html;

	}

}
