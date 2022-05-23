<?php
/**
 * Related Links Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Related Links Dynamic Element
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.5.1
 */
class Page_Generator_Pro_Shortcode_Related_Links extends Page_Generator_Pro_Shortcode_Base {

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

		// Register shortcode.
		add_filter( 'page_generator_pro_shortcode_add_shortcodes', array( $this, 'add_shortcode' ) );
		add_filter( 'page_generator_pro_shortcode_add_shortcodes_outside_of_content_groups', array( $this, 'add_shortcode' ) );

		// Delete cache when Content Group generates a Page, not in Test Mode.
		add_action( 'page_generator_pro_generate_content_finished', array( $this, 'maybe_delete_cache' ), 10, 5 );

		// Delete cache when Generated Content is Trashed or Deleted.
		add_action( 'page_generator_pro_generate_trash_content_finished', array( $this, 'delete_cache' ) );
		add_action( 'page_generator_pro_generate_delete_content_finished', array( $this, 'delete_cache' ) );

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
	 * Deletes the Related Links cache if a Page has been generated and generation was
	 * not in Test Mode.
	 *
	 * @since   3.4.3
	 *
	 * @param   int   $post_id        Generated Post ID.
	 * @param   int   $group_id       Group ID.
	 * @param   array $settings       Group Settings.
	 * @param   int   $index          Keyword Index.
	 * @param   bool  $test_mode      Test Mode.
	 */
	public function maybe_delete_cache( $post_id, $group_id, $settings, $index, $test_mode ) { // phpcs:ignore

		// Don't delete cache if the generation request was in test mode.
		if ( $test_mode ) {
			return;
		}

		// Delete Related Links cache.
		$this->delete_cache();

	}

	/**
	 * Deletes the Related Links cache if generated Pages have been trashed or deleted.
	 *
	 * @since   3.4.3
	 */
	public function delete_cache() {

		$this->base->get_class( 'persistent_cache' )->delete( 'related-links' );

	}

	/**
	 * Returns this shortcode / block's programmatic name.
	 *
	 * @since   2.5.1
	 */
	public function get_name() {

		return 'related-links';

	}

	/**
	 * Returns this shortcode / block's Title, Icon, Categories, Keywords
	 * and properties for registering on generation and requiring CSS/JS.
	 *
	 * @since   2.5.1
	 */
	public function get_overview() {

		return array(
			'title'                       => __( 'Related Links', 'page-generator-pro' ),
			'description'                 => __( 'Displays a list of Related Pages, for internal linking.', 'page-generator-pro' ),
			'icon'                        => $this->base->plugin->url . '/_modules/dashboard/feather/list.svg',
			'category'                    => $this->base->plugin->name,
			'keywords'                    => array(
				__( 'Related Links', 'page-generator-pro' ),
				__( 'Interlinking', 'page-generator-pro' ),
				__( 'Site Links', 'page-generator-pro' ),
				__( 'Links', 'page-generator-pro' ),
			),

			// TinyMCE / QuickTags Modal Width and Height.
			'modal'                       => array(
				'width'  => 800,
				'height' => 600,
			),

			// Register when Generation is running only.
			'register_on_generation_only' => false,

			// Requires CSS and/or JS for output.
			'requires_css'                => true,
			'requires_js'                 => false,

			// Function to call when rendering the shortcode on the frontend.
			'render_callback'             => array( 'shortcode_related_links', 'render' ),

			// Gutenberg: Block Icon in Editor.
			'gutenberg_icon'              => file_get_contents( $this->base->plugin->folder . '/_modules/dashboard/feather/list.svg' ), /* phpcs:ignore */
		);

	}

	/**
	 * Returns this block's Attributes and default values for Gutenberg.
	 *
	 * @since   3.6.3
	 */
	public function get_attributes() {

		return array(
			// General.
			'group_id'               => array(
				'type'      => 'array',
				'delimiter' => ',',
			),
			'post_type'              => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'post_type' ) ? '' : $this->get_default_value( 'post_type' ) ),
			),
			'post_status'            => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'post_status' ) ? '' : $this->get_default_value( 'post_status' ) ),
			),
			'author'                 => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'author' ) ? '' : $this->get_default_value( 'author' ) ),
			),
			'post_parent'            => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'post_parent' ) ? '' : $this->get_default_value( 'post_parent' ) ),
			),
			'post_name'              => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'post_name' ) ? '' : $this->get_default_value( 'post_name' ) ),
			),

			// Output.
			'output_type'            => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'output_type' ) ? '' : $this->get_default_value( 'output_type' ) ),
			),
			'limit'                  => array(
				'type'    => 'number',
				'default' => $this->get_default_value( 'limit' ),
			),
			'columns'                => array(
				'type'    => 'number',
				'default' => $this->get_default_value( 'columns' ),
			),
			'delimiter'              => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'delimiter' ) ? '' : $this->get_default_value( 'delimiter' ) ),
			),
			'link_title'             => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'link_title' ) ? '' : $this->get_default_value( 'link_title' ) ),
			),
			'link_anchor_title'      => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'link_anchor_title' ) ? '' : $this->get_default_value( 'link_anchor_title' ) ),
			),
			'link_description'       => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'link_description' ) ? '' : $this->get_default_value( 'link_description' ) ),
			),
			'link_featured_image'    => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'link_featured_image' ) ? '' : $this->get_default_value( 'link_featured_image' ) ),
			),
			'link_display_order'     => array(
				'type'      => 'array',
				'delimiter' => ',',
				'default'   => $this->get_default_value( 'link_display_order' ),
			),
			'link_display_alignment' => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'link_display_alignment' ) ? '' : $this->get_default_value( 'link_display_alignment' ) ),
			),

			'parent_title'           => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'parent_title' ) ? '' : $this->get_default_value( 'parent_title' ) ),
			),
			'next_title'             => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'next_title' ) ? '' : $this->get_default_value( 'next_title' ) ),
			),
			'prev_title'             => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'prev_title' ) ? '' : $this->get_default_value( 'prev_title' ) ),
			),

			'orderby'                => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'orderby' ) ? '' : $this->get_default_value( 'orderby' ) ),
			),
			'order'                  => array(
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'order' ) ? '' : $this->get_default_value( 'order' ) ),
			),
			'radius'                 => array(
				'type'    => 'number',
				'default' => $this->get_default_value( 'radius' ),
			),

			// Taxonomy Conditions.
			'taxonomies'             => array(
				// repeater.
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'taxonomies' ) ? '' : $this->get_default_value( 'taxonomies' ) ),
			),

			// Custom Field Conditions.
			'custom_fields'          => array(
				// repeater.
				'type'    => 'string',
				'default' => ( ! $this->get_default_value( 'custom_fields' ) ? '' : $this->get_default_value( 'custom_fields' ) ),
			),

			// Preview.
			'is_gutenberg_example'   => array(
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

		// Define Keywords for autocomplete / select dropdowns if this is an admin or frontend editor request.
		// We always return fields, even for frontend requests, as this Dynamic Element can be used outside of Content Groups
		// in e.g. a standalone Gutenberg / TinyMCE / Elementor Page.
		$groups   = array();
		$keywords = array();
		if ( $this->base->is_admin_or_frontend_editor() ) {
			$groups   = $this->base->get_class( 'groups' )->get_all_ids_names();
			$keywords = $this->base->get_class( 'keywords' )->get_keywords_and_columns( true );
		}

		return array(
			// General.
			'group_id'               => array(
				'label'       => __( 'Groups', 'page-generator-pro' ),
				'type'        => 'select_multiple',
				'values'      => $groups,
				'class'       => 'wpzinc-selectize-drag-drop',
				'description' => __( 'If no Content Group is chosen, the Generated Page\'s Content Group will be used', 'page-generator-pro' ),
			),
			'post_type'              => array(
				'label'         => __( 'Post Type', 'page-generator-pro' ),
				'type'          => 'select',
				'values'        => array_merge(
					array(
						'' => __( '(all)', 'page-generator-pro' ),
					),
					$this->base->get_class( 'common' )->get_post_types_key_value_array()
				),
				'default_value' => $this->get_default_value( 'post_type' ),
			),
			'post_status'            => array(
				'label'         => __( 'Post Status', 'page-generator-pro' ),
				'type'          => 'select',
				'values'        => array_merge(
					array(
						'' => __( '(all)', 'page-generator-pro' ),
					),
					$this->base->get_class( 'common' )->get_post_statuses()
				),
				'default_value' => $this->get_default_value( 'post_status' ),
			),
			'author'                 => array(
				'label'         => __( 'Author', 'page-generator-pro' ),
				'type'          => 'autocomplete',
				'values'        => $keywords,
				'default_value' => $this->get_default_value( 'author' ),
			),
			'post_parent'            => array(
				'label'         => __( 'Post Parent', 'page-generator-pro' ),
				'type'          => 'autocomplete',
				'values'        => $keywords,
				'default_value' => $this->get_default_value( 'post_parent' ),
				'description'   => __( 'If specified, generated content that is a child of the given Parent will be displayed.  Supports ID, Slug or Keyword Slug', 'page-generator-pro' ),
			),
			'post_name'              => array(
				'label'         => __( 'Post Name / Slug', 'page-generator-pro' ),
				'type'          => 'autocomplete',
				'values'        => $keywords,
				'default_value' => $this->get_default_value( 'post_name' ),
				'description'   => __( 'If specified, generated content that fully matches the given Post Name / Slug will be displayed.  Supports multiple Post Names separated by a comma.  Supports Keyword Slugs.', 'page-generator-pro' ),
			),

			// Output.
			'output_type'            => array(
				'label'         => __( 'Output Type', 'page-generator-pro' ),
				'type'          => 'select',
				'class'         => 'wpzinc-conditional',
				'data'          => array(
					// .components-panel is Gutenberg.
					// .related-links is TinyMCE.
					'container' => '.components-panel, .related-links',
				),
				'values'        => array(
					'list_links'        => __( 'List of Links', 'page-generator-pro' ),
					'list_links_bullet' => __( 'List of Links, Bulleted', 'page-generator-pro' ),
					'list_links_number' => __( 'List of Links, Numbered', 'page-generator-pro' ),
					'list_links_comma'  => __( 'List of Links, Comma Separated', 'page-generator-pro' ),
					'prev_next'         => __( 'Parent, Next and Previous Links', 'page-generator-pro' ),
				),
				'default_value' => $this->get_default_value( 'output_type' ),
			),
			'limit'                  => array(
				'label'         => __( 'Number of Links', 'page-generator-pro' ),
				'type'          => 'number',
				'min'           => 0,
				'max'           => 9999999,
				'step'          => 1,
				'condition'     => array(
					'key'        => 'output_type',
					'value'      => array( 'list_links', 'list_links_bullet', 'list_links_number', 'list_links_comma' ),
					'comparison' => '==',
				),
				'default_value' => $this->get_default_value( 'limit' ),
			),
			'columns'                => array(
				'label'         => __( 'Number of Columns', 'page-generator-pro' ),
				'type'          => 'number',
				'min'           => 1,
				'max'           => 4,
				'step'          => 1,
				'condition'     => array(
					'key'        => 'output_type',
					'value'      => array( 'list_links', 'list_links_bullet', 'list_links_number' ),
					'comparison' => '==',
				),
				'default_value' => $this->get_default_value( 'columns' ),
			),
			'delimiter'              => array(
				'label'         => __( 'Delimiter', 'page-generator-pro' ),
				'type'          => 'text',
				'condition'     => array(
					'key'        => 'output_type',
					'value'      => array( 'list_links_comma' ),
					'comparison' => '==',
				),
				'data'          => array(
					'trim' => 0,
				),
				'default_value' => $this->get_default_value( 'delimiter' ),
				'description'   => __( 'The delimiter to separate each link with. Include any spacing before and/or after as necessary.', 'page-generator-pro' ),
			),
			'link_title'             => array(
				'label'         => __( 'Link Title', 'page-generator-pro' ),
				'type'          => 'text',
				'condition'     => array(
					'key'        => 'output_type',
					'value'      => array( 'list_links', 'list_links_bullet', 'list_links_number', 'list_links_comma' ),
					'comparison' => '==',
				),
				'default_value' => $this->get_default_value( 'link_title' ),
				'description'   => __( '%title% will be replaced by the Post Title. %parent_title will be replaced by the Post\'s Parent Title.', 'page-generator-pro' ),
			),
			'link_anchor_title'      => array(
				'label'         => __( 'Link Anchor Title', 'page-generator-pro' ),
				'type'          => 'text',
				'condition'     => array(
					'key'        => 'output_type',
					'value'      => array( 'list_links', 'list_links_bullet', 'list_links_number', 'list_links_comma' ),
					'comparison' => '==',
				),
				'default_value' => $this->get_default_value( 'link_anchor_title' ),
				'description'   => __( '%title% will be replaced by the Post Title. %parent_title will be replaced by the Post\'s Parent Title.', 'page-generator-pro' ),
			),
			'link_description'       => array(
				'label'         => __( 'Link Description', 'page-generator-pro' ),
				'type'          => 'text',
				'condition'     => array(
					'key'        => 'output_type',
					'value'      => array( 'list_links', 'list_links_bullet', 'list_links_number' ),
					'comparison' => '==',
				),
				'default_value' => $this->get_default_value( 'link_description' ),
				/* translators: %excerpt isn't a placeholder. It's a dynamic tag, and must not be translated */
				'description'   => __( '%excerpt will be replaced by the Post Excerpt. If blank, no Description is output.', 'page-generator-pro' ),
			),
			'link_featured_image'    => array(
				'label'         => __( 'Show Feat. Image?', 'page-generator-pro' ),
				'type'          => 'select',
				'values'        => array_merge(
					array(
						'' => __( 'No', 'page-generator-pro' ),
					),
					$this->base->get_class( 'common' )->get_media_library_image_size_options()
				),
				'condition'     => array(
					'key'        => 'output_type',
					'value'      => array( 'list_links', 'list_links_bullet', 'list_links_number' ),
					'comparison' => '==',
				),
				'default_value' => $this->get_default_value( 'link_featured_image' ),
				'description'   => __( 'Displays the Featured Image for the Post, at the specified size.', 'page-generator-pro' ),
			),
			'link_display_order'     => array(
				'label'         => __( 'Display Order', 'page-generator-pro' ),
				'type'          => 'select_multiple',
				'values'        => array(
					'link_title'       => __( 'Title', 'page-generator-pro' ),
					'featured_image'   => __( 'Featured Image', 'page-generator-pro' ),
					'link_description' => __( 'Description', 'page-generator-pro' ),
				),
				'condition'     => array(
					'key'        => 'output_type',
					'value'      => array( 'list_links', 'list_links_bullet', 'list_links_number' ),
					'comparison' => '==',
				),
				'default_value' => $this->get_default_value( 'link_display_order' ),
				'class'         => 'wpzinc-selectize-drag-drop',
				'description'   => __( 'Defines the content display order for each individual Related Link.', 'page-generator-pro' ),
			),
			'link_display_alignment' => array(
				'label'       => __( 'Display Alignment', 'page-generator-pro' ),
				'type'        => 'select',
				'values'      => array(
					'vertical'   => __( 'Vertical', 'page-generator-pro' ),
					'horizontal' => __( 'Horizontal', 'page-generator-pro' ),
				),
				'condition'   => array(
					'key'        => 'output_type',
					'value'      => array( 'list_links', 'list_links_bullet', 'list_links_number' ),
					'comparison' => '==',
				),
				'description' => __( 'Defines the content display alignment for each individual Related Link.', 'page-generator-pro' ),
			),

			'parent_title'           => array(
				'label'         => __( 'Parent Link: Title', 'page-generator-pro' ),
				'type'          => 'text',
				'condition'     => array(
					'key'        => 'output_type',
					'value'      => 'prev_next',
					'comparison' => '==',
				),
				'default_value' => $this->get_default_value( 'parent_title' ),
				'description'   => __( '%title% will be replaced by the Previous Link Post Title.  Leave blank to not display a Next Link.', 'page-generator-pro' ),
			),
			'next_title'             => array(
				'label'         => __( 'Next Link: Title', 'page-generator-pro' ),
				'type'          => 'text',
				'condition'     => array(
					'key'        => 'output_type',
					'value'      => 'prev_next',
					'comparison' => '==',
				),
				'default_value' => $this->get_default_value( 'next_title' ),
				'description'   => __( '%title% will be replaced by the Next Link Post Title.  Leave blank to not display a Next Link.', 'page-generator-pro' ),
			),
			'prev_title'             => array(
				'label'         => __( 'Previous Link: Title', 'page-generator-pro' ),
				'type'          => 'text',
				'condition'     => array(
					'key'        => 'output_type',
					'value'      => 'prev_next',
					'comparison' => '==',
				),
				'default_value' => $this->get_default_value( 'prev_title' ),
				'description'   => __( '%title% will be replaced by the Previous Post Title.  Leave blank to not display a Previous Link.', 'page-generator-pro' ),
			),

			'orderby'                => array(
				'label'         => __( 'Order Links By', 'page-generator-pro' ),
				'type'          => 'select',
				'values'        => $this->base->get_class( 'common' )->get_order_by_options(),
				'default_value' => $this->get_default_value( 'orderby' ),
				'description'   => __( 'Distance requires Geolocation Data be completed in both this Content Group and the Group(s) defined above.', 'page-generator-pro' ),
			),
			'order'                  => array(
				'label'         => __( 'Order', 'page-generator-pro' ),
				'type'          => 'select',
				'values'        => $this->base->get_class( 'common' )->get_order_options(),
				'default_value' => $this->get_default_value( 'order' ),
			),
			'radius'                 => array(
				'label'         => __( 'Radius (miles)', 'page-generator-pro' ),
				'type'          => 'number',
				'min'           => 0,
				'max'           => 9999999,
				'step'          => '0.1',
				'placeholder'   => __( 'e.g. 5', 'page-generator-pro' ),
				'default_value' => $this->get_default_value( 'radius' ),
				'description'   => __( 'When specified, links will only be displayed for Posts that have a Latitude and Longitude falling within the given distance. Both this Content Group and the Content Group(s) specified in the Groups option above must have Latitude and Longitude values specified in the Geolocation Data section.', 'page-generator-pro' ),
			),

			// Taxonomy Conditions.
			'taxonomies'             => array(
				'label'      => __( 'Taxonomies', 'page-generator-pro' ),
				'type'       => 'repeater',
				'sub_fields' => array(
					'taxonomy'      => array(
						'label'  => __( 'Taxonomy', 'page-generator-pro' ),
						'type'   => 'select',
						'class'  => 'taxonomy',
						'data'   => array(
							'shortcode' => '',
						),
						'values' => $this->base->get_class( 'common' )->get_taxonomies_key_value_array(),
					),
					'taxonomy_term' => array(
						'label'  => __( 'Term(s)', 'page-generator-pro' ),
						'type'   => 'autocomplete',
						'values' => $keywords,
						'data'   => array(
							'shortcode' => '{.taxonomy}',
						),
						'values' => $this->base->get_class( 'common' )->get_taxonomies_key_value_array(),
					),
				),
			),

			// Custom Field Conditions.
			'custom_fields'          => array(
				'label'      => __( 'Custom Fields', 'page-generator-pro' ),
				'type'       => 'repeater',
				'sub_fields' => array(
					'custom_field'       => array(
						'label'       => __( 'Meta Key', 'page-generator-pro' ),
						'type'        => 'text',
						'placeholder' => __( 'Meta Key', 'page-generator-pro' ),
						'class'       => 'custom-field',
						'data'        => array(
							'shortcode' => '',
						),
					),
					'custom_field_value' => array(
						'label'       => __( 'Meta Value', 'page-generator-pro' ),
						'type'        => 'text',
						'placeholder' => __( 'Meta Value', 'page-generator-pro' ),
						'type'        => 'autocomplete',
						'values'      => $keywords,
						'data'        => array(
							'shortcode'         => '{.custom-field}',
							'shortcode-prepend' => 'custom_field_',
						),
					),
				),
			),
		);

	}

	/**
	 * Returns this shortcode / block's UI Tabs
	 *
	 * @since   2.5.1
	 */
	public function get_tabs() {

		return array(
			'general'                 => array(
				'label'       => __( 'Search Parameters', 'page-generator-pro' ),
				'class'       => 'search',
				'description' => __( 'Defines search parameters to build the Related Links.', 'page-generator-pro' ),
				'fields'      => array(
					'group_id',

					// Post.
					'post_type',
					'post_status',
					'post_parent',
					'post_name',

					// Author.
					'author',

					// Radius.
					'radius',
				),
			),
			'output'                  => array(
				'label'       => __( 'Output', 'page-generator-pro' ),
				'class'       => 'link',
				'description' => __( 'Defines what to output for Related Links.', 'page-generator-pro' ),
				'fields'      => array(
					'output_type',

					// Output Type: List of Links.
					'limit',
					'columns',
					'delimiter',
					'link_title',
					'link_anchor_title',
					'link_description',
					'link_featured_image',
					'link_display_order',
					'link_display_alignment',

					// Output Type: Parent, Previous and Next.
					'parent_title',
					'next_title',
					'prev_title',
				),
			),
			'ordering'                => array(
				'label'       => __( 'Ordering', 'page-generator-pro' ),
				'class'       => 'order',
				'description' => __( 'Defines the order of the output for Related Links.', 'page-generator-pro' ),
				'fields'      => array(
					'orderby',
					'order',
				),
			),
			'taxonomy-conditions'     => array(
				'label'       => __( 'Taxonomies', 'page-generator-pro' ),
				'class'       => 'tag',
				'description' => __( 'When specified, links will only be displayed for Posts that belong to the given Taxonomy Term(s). Separate Terms with commas to specify multiple Terms. Term Names, IDs and Keywords are supported.', 'page-generator-pro' ),
				'fields'      => array(
					'taxonomies',
				),
			),
			'custom-field-conditions' => array(
				'label'       => __( 'Custom Fields', 'page-generator-pro' ),
				'class'       => 'database',
				'description' => __( 'When specified, links will only be displayed for Posts that match the given Custom Field Key/Value pairings. Keywords are supported.', 'page-generator-pro' ),
				'fields'      => array(
					'custom_fields',
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

		global $post;

		// Get the Group ID which generated this content.
		$group_id  = ( isset( $post ) ? absint( get_post_meta( $post->ID, '_page_generator_pro_group', true ) ) : '' );
		$post_type = ( isset( $post ) ? $post->post_type : '' );

		// Define default shortcode attributes.
		$defaults = array(
			// Output and Group ID.
			'group_id'               => array( $group_id ),
			'output_type'            => 'list_links',

			// Output Type: List of Links.
			'limit'                  => 0,
			'columns'                => 1,
			'delimiter'              => ', ',
			'link_title'             => '%title%',
			'link_anchor_title'      => '%title%',
			'link_description'       => '',
			'link_featured_image'    => '',
			'link_display_order'     => array(
				'link_title',
				'featured_image',
				'link_description',
			),
			'link_display_alignment' => 'vertical',

			// Output Type: Parent, Previous and Next.
			'parent_title'           => '',
			'next_title'             => '',
			'prev_title'             => '',

			// Post.
			'post_type'              => $post_type,
			'post_status'            => 'publish',
			'post_parent'            => '',
			'post_name'              => '',

			// Author.
			'author'                 => '',

			// Order.
			'orderby'                => 'name',
			'order'                  => 'ASC',

			// Radius.
			'radius'                 => 0,
		);

		// Define default taxonomy shortcode attributes.
		foreach ( $this->base->get_class( 'common' )->get_taxonomies() as $taxonomy ) {
			$defaults[ $taxonomy->name ] = '';
		}

		return $defaults;

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

		global $post;

		// Store original attributes.
		$original_atts = $atts;

		// If the cache contains a result for this specific Related Links configuration, use it now.
		$result = $this->base->get_class( 'persistent_cache' )->get( 'related-links', $original_atts );
		if ( $result !== false ) {
			return $result;
		}

		// Get defaults.
		$defaults = $this->get_default_values();

		// Define default custom field shortcode attributes.
		$custom_field_atts = $this->get_shortcode_atts_by_partial_key( 'custom_field_', $atts );
		if ( $custom_field_atts !== false ) {
			foreach ( $custom_field_atts as $att => $value ) {
				$defaults[ $att ] = '';
			}
		}

		/**
		 * Filter the Related Links Shortcode Default Attributes.
		 *
		 * @since   1.0.0
		 *
		 * @param   array       $defaults   Default Attributes.
		 * @param   array       $atts       Shortcode Attributes.
		 * @param   WP_Post     $post       WordPress Post.
		 */
		$defaults = apply_filters( 'page_generator_pro_shortcode_related_links_defaults', $defaults, $atts, $post );

		// Parse attributes.
		$atts = $this->parse_atts( $atts );

		// If certain attributes are empty, but require a value, set them now.
		if ( empty( $atts['group_id'] ) ) {
			$atts['group_id'] = array( $defaults['group_id'] );
		}
		if ( empty( $atts['post_type'] ) ) {
			$atts['post_type'] = $post->post_type;
		}

		// Cast Group IDs to integers.
		foreach ( $atts['group_id'] as $index => $group_id ) {
			$atts['group_id'][ $index ] = absint( $group_id );
		}

		// Start building the WP_Query arguments.
		$args = array(
			'post_type'              => $atts['post_type'],
			'post_status'            => $atts['post_status'],
			'posts_per_page'         => -1,
			'orderby'                => ( $atts['orderby'] === 'rand' ? 'none' : $atts['orderby'] ),
			'order'                  => $atts['order'],
			'meta_query'             => array(
				array(
					'key'     => '_page_generator_pro_group',
					'value'   => $atts['group_id'],
					'compare' => 'IN',
				),
			),

			// For performance, just return the Post ID and don't update meta or term caches.
			'fields'                 => 'ids',
			'cache_results'          => false,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		// Add Parent constraint to WP_Query, if defined.
		if ( $atts['post_parent'] !== false && ! empty( $atts['post_parent'] ) ) {
			// If Post Parent is a string, fetch Post Parent ID by Slug.
			if ( ! is_numeric( $atts['post_parent'] ) ) {
				// If spaces exist in the Post Parent attribute, convert it to a slug first.
				if ( strpos( $atts['post_parent'], ' ' ) !== false ) {
					// Convert to a slug, retaining forwardslashes.
					// This also converts special accented characters to non-accented versions.
					$atts['post_parent'] = $this->base->get_class( 'common' )->sanitize_slug( $atts['post_parent'] );
				}

				// Find parent by slug.
				$parent = get_page_by_path( $atts['post_parent'], OBJECT, $atts['post_type'] );
				if ( isset( $parent->ID ) ) {
					$args['post_parent'] = absint( $parent->ID );
				}
			} else {
				$args['post_parent'] = absint( $atts['post_parent'] );
			}
		}

		// Add Name constraint to WP_Query, if defined.
		if ( ! empty( $atts['post_name'] ) ) {
			$args['post_name__in'] = explode( ',', $atts['post_name'] );
		}

		// Add Author constraint to WP_Query, if defined.
		if ( ! empty( $atts['author'] ) ) {
			$args['author'] = absint( $atts['author'] );
		}

		// Add Taxonomy Term constraints to WP_Query, if defined.
		// Fetch Taxonomies for this Post Type.
		$taxonomies = $this->base->get_class( 'common' )->get_post_type_taxonomies( $args['post_type'] );
		if ( is_array( $taxonomies ) && count( $taxonomies ) > 0 ) {
			$args['tax_query'] = array();
			foreach ( $taxonomies as $taxonomy ) {
				// Skip if no constraints defined for this Taxonomy.
				if ( ! isset( $atts[ $taxonomy->name ] ) ) {
					continue;
				}
				if ( empty( $atts[ $taxonomy->name ] ) ) {
					continue;
				}

				// Build array of Terms, checking if they're numeric (i.e. all Term IDs).
				$terms             = array_map( 'trim', explode( ',', $atts[ $taxonomy->name ] ) );
				$terms_are_numeric = true;
				foreach ( $terms as $term ) {
					if ( ! is_numeric( $term ) ) {
						$terms_are_numeric = false;
						break;
					}
				}

				// Add constraint.
				$args['tax_query'][] = array(
					'taxonomy' => $taxonomy->name,
					'field'    => ( $terms_are_numeric ? 'term_id' : 'name' ),
					'terms'    => $terms,
				);
			}
		}

		// Add Custom Field constraints to WP_Query, if defined.
		// Define default custom field shortcode attributes.
		if ( $custom_field_atts !== false ) {
			if ( ! isset( $args['meta_query'] ) ) {
				$args['meta_query'] = array();
			}

			foreach ( $custom_field_atts as $custom_field => $meta_value ) {
				// Remove custom_field_ prefix, so we're left with the Meta Key.
				$meta_key = str_replace( 'custom_field_', '', $custom_field );

				// Add to WP_Query args.
				$args['meta_query'][] = array(
					'key'   => $meta_key,
					'value' => $meta_value,
				);
			}
		}

		// Add Radius constraints to WP_Query, if defined.
		if ( $atts['radius'] ) {
			$post_ids_within_radius = $this->base->get_class( 'geo' )->get_post_ids(
				$post->ID,
				$atts['radius'],
				( ( $atts['orderby'] === 'distance' ) ? $atts['order'] : false ),
				$atts['group_id']
			);

			// Only add constraint if results were found.
			if ( is_array( $post_ids_within_radius ) && count( $post_ids_within_radius ) > 0 ) {
				// Add Post IDs to fetch.
				$args['post__in'] = array_keys( $post_ids_within_radius );
			}
		}

		// If the Order By is distance, use post__in as Post IDs are already sorted by distance.
		if ( $args['orderby'] === 'distance' ) {
			$args['orderby'] = 'post__in';
			unset( $args['order'] ); // Must be done to ensure orderby => post__in works!
		}

		// Fetch other Pages / Posts generated in this Group based on the supplied arguments.
		$posts = new WP_Query( $args );

		// If no other Pages / Posts found, bail.
		if ( count( $posts->posts ) === 0 ) {
			// If this block is being previewed in Gutenberg, show a verbose message explaining
			// why no Related Links exist.
			if ( $this->base->get_class( 'common' )->is_rest_api_request() ) {
				return __( 'Related Links: No related links exist. Either no pages have been generated for the specified Groups, or no generated pages match the specified criteria. This message will not display on the frontend site.', 'page-generator-pro' );
			}

			return '';
		}

		// Define CSS classes for the list container.
		$css = array(
			'page-generator-pro-' . $this->get_name(),
			'page-generator-pro-' . $this->get_name() . '-columns-' . $atts['columns'],
			'page-generator-pro-' . $this->get_name() . '-' . str_replace( '_', '-', $atts['output_type'] ),
			'page-generator-pro-' . $this->get_name() . '-' . str_replace( '_', '-', $atts['link_display_alignment'] ),
		);

		// Start HTML.
		$html = '';

		// Build HTML based on the output type.
		$count = 0;
		switch ( $atts['output_type'] ) {
			/**
			 * Previous, Next and/or Parent Links
			 */
			case 'prev_next':
				// Start List Container.
				$html .= '<ul class="' . implode( ' ', $css ) . '">';

				// Get current Post index from the WP_Query results.
				$current_post_index = array_search( $post->ID, $posts->posts, true );

				// Add Parent Post Link.
				if ( ! empty( $atts['parent_title'] ) ) {
					$parent_post_id = wp_get_post_parent_id( $post->ID );
					if ( $parent_post_id ) {
						$distance = ( isset( $post_ids_within_radius[ $parent_post_id ] ) ? $post_ids_within_radius[ $parent_post_id ] : '' );
						$html    .= '<li class="parent">
                            <a href="' . get_permalink( $parent_post_id ) . '" title="' . get_the_title( $parent_post_id ) . '">' .
								$this->replace_post_variables( $atts['parent_title'], $parent_post_id, $distance ) . '
                            </a>
                        </li>';
					}
				}

				// Add Previous Post Link.
				if ( ! empty( $atts['prev_title'] ) && $current_post_index - 1 >= 0 ) {
					$previous_post_id = $posts->posts[ $current_post_index - 1 ];
					if ( $previous_post_id ) {
						$distance = ( isset( $post_ids_within_radius[ $previous_post_id ] ) ? $post_ids_within_radius[ $previous_post_id ] : '' );
						$html    .= '<li class="prev">
                            <a href="' . get_permalink( $previous_post_id ) . '" title="' . get_the_title( $previous_post_id ) . '">' .
								$this->replace_post_variables( $atts['prev_title'], $previous_post_id, $distance ) . '
                            </a>
                        </li>';
					}
				}

				// Add Next Post Link.
				if ( ! empty( $atts['next_title'] ) && $current_post_index + 1 <= count( $posts->posts ) - 1 ) {
					$next_post_id = $posts->posts[ $current_post_index + 1 ];
					if ( $next_post_id ) {
						$distance = ( isset( $post_ids_within_radius[ $next_post_id ] ) ? $post_ids_within_radius[ $next_post_id ] : '' );
						$html    .= '<li class="next">
                            <a href="' . get_permalink( $next_post_id ) . '" title="' . get_the_title( $next_post_id ) . '">' .
								$this->replace_post_variables( $atts['next_title'], $next_post_id, $distance ) . '
                            </a>
                        </li>';
					}
				}

				// End List Container.
				$html .= '</ul>';
				break;

			/**
			 * List of Links, Comma Separated
			 */
			case 'list_links_comma':
				// If order by is random, do this now.
				// It's more efficient and less resource intensive to do this now vs. in the WP_Query.
				if ( $atts['orderby'] === 'rand' ) {
					shuffle( $posts->posts );
				}

				// Start List Container.
				$html = '<span class="' . implode( ' ', $css ) . '">';

				foreach ( $posts->posts as $index => $post_id ) {
					// Skip if this Post is the current Post.
					if ( $post_id === $post->ID ) {
						continue;
					}

					// Get Distance, if available.
					$distance = ( isset( $post_ids_within_radius[ $post_id ] ) ? $post_ids_within_radius[ $post_id ] : '' );

					// If a Link Title and/or Anchor Title are specified, use it now.
					$title        = ( ! empty( $atts['link_title'] ) ? $this->replace_post_variables( $atts['link_title'], $post_id, $distance ) : get_the_title( $post_id ) );
					$anchor_title = ( ! empty( $atts['link_anchor_title'] ) ? $this->replace_post_variables( $atts['link_anchor_title'], $post_id, $distance ) : get_the_title( $post_id ) );

					// Append link to HTML.
					$html .= '<a href="' . get_permalink( $post_id ) . '" title="' . $anchor_title . '">' . $title . '</a>' . $atts['delimiter'];

					// Increment link count.
					$count++;

					// Exit loop if a link limit exists and has been reached.
					if ( $atts['limit'] > 0 && $count >= $atts['limit'] ) {
						break;
					}
				}

				// End List Container.
				$html  = trim( $html, $atts['delimiter'] );
				$html .= '</span>';
				break;

			/**
			 * List of Links
			 */
			default:
				// If order by is random, do this now.
				// It's more efficient and less resource intensive to do this now vs. in the WP_Query.
				if ( $atts['orderby'] === 'rand' ) {
					shuffle( $posts->posts );
				}

				// Start List Container.
				$html .= '<' . ( ( $atts['output_type'] === 'list_links_ordered' ) ? 'ol' : 'ul' ) . ' class="' . implode( ' ', $css ) . '">';

				foreach ( $posts->posts as $index => $post_id ) {
					// Skip if this Post is the current Post.
					if ( $post_id === $post->ID ) {
						continue;
					}

					// Define the HTML elements for this Related Link.
					$html_elements = array(
						'link_title'       => '',
						'featured_image'   => '',
						'link_description' => '',
					);

					// Get Distance, if available.
					$distance = ( isset( $post_ids_within_radius[ $post_id ] ) ? $post_ids_within_radius[ $post_id ] : '' );

					// Get Link Title.
					$title                       = ( ! empty( $atts['link_title'] ) ? $this->replace_post_variables( $atts['link_title'], $post_id, $distance ) : get_the_title( $post_id ) );
					$anchor_title                = ( ! empty( $atts['link_anchor_title'] ) ? $this->replace_post_variables( $atts['link_anchor_title'], $post_id, $distance ) : get_the_title( $post_id ) );
					$html_elements['link_title'] = '<a href="' . get_permalink( $post_id ) . '" title="' . $anchor_title . '">' . $title . '</a>';

					// Get Link Description.
					$html_elements['link_description'] = ( ! empty( $atts['link_description'] ) ? '<span class="page-generator-pro-related-links-description">' . $this->replace_post_variables( $atts['link_description'], $post_id, $distance ) . '</span>' : '' );

					// Get Featured Image.
					if ( $atts['link_featured_image'] && ! empty( get_the_post_thumbnail( $post_id ) ) ) {
						// For backward compat., use small size if link_featured_image=1.
						// 2.9.3+ will have the image size as the attribute value.
						$size = ( ! is_numeric( $atts['link_featured_image'] ) ? $atts['link_featured_image'] : 'small' );

						$html_elements['featured_image'] = '<a href="' . get_permalink( $post_id ) . '" title="' . get_the_title( $post_id ) . '">' .
							get_the_post_thumbnail( $post_id, $size ) .
						'</a>';
					}

					// Build this Related Link's HTML based on the order defined in the Display Order parameter.
					$html .= '<li>';
					foreach ( $atts['link_display_order'] as $link_display_item ) {
						$html .= $html_elements[ $link_display_item ];
					}
					$html .= '</li>';

					// Increment link count.
					$count++;

					// Exit loop if a link limit exists and has been reached.
					if ( $atts['limit'] > 0 && $count >= $atts['limit'] ) {
						break;
					}
				}

				// End List Container.
				$html .= '</ul>';
				break;
		}

		/**
		 * Filter the Related Links Shortcode HTML output, before returning.
		 *
		 * @since   1.0.0
		 *
		 * @param   string  $html   HTML Output.
		 * @param   array   $atts   Shortcode Attributes.
		 * @param   array   $posts  Related Posts.
		 * @param   WP_Post $post   WordPress Post the shortcode is used on.
		 */
		$html = apply_filters( 'page_generator_pro_shortcode_related_links', $html, $atts, $posts, $post );

		// Maybe change the CSS Prefix.
		// We do this here as this shortcode is rendered when viewing a Page, not when generating a Page.
		$html = $this->base->get_class( 'shortcode' )->maybe_change_css_prefix_content( $html );

		// Cache result, if it's not a dynamic radius based query (which will have the same parameters
		// for all Pages the shortcode is on, but will always output something different due to radius).
		if ( ! $atts['radius'] ) {
			$this->base->get_class( 'persistent_cache' )->set( 'related-links', $original_atts, $html );
		}

		// Return.
		return $html;

	}

	/**
	 * Replaces Post variables with the Post's data.
	 *
	 * @since   2.2.1
	 *
	 * @param   string $text       Text.
	 * @param   int    $post_id    Post ID.
	 * @param   string $distance   Distance.
	 * @return  string              Text
	 */
	private function replace_post_variables( $text, $post_id, $distance = '' ) {

		// Get Post.
		$post = get_post( $post_id );

		// Replace %title% with the Post's Title.
		$text = str_replace( '%title%', get_the_title( $post_id ), $text );
		$text = str_replace( '%title', get_the_title( $post_id ), $text ); // Backward compat.

		// Replace %excerpt% with the Post's Excerpt.
		$text = str_replace( '%excerpt%', $this->get_excerpt( $post ), $text );
		$text = str_replace( '%excerpt', $this->get_excerpt( $post ), $text ); // Backward compat.

		// Replace any %custom_field_NAME with Post's Custom Field Value.
		if ( preg_match_all( '/%custom_field_(.*?)%/', $text, $matches ) ) {
			foreach ( $matches[1] as $index => $custom_field ) {
				$text = str_replace( $matches[0][ $index ], get_post_meta( $post_id, $custom_field, true ), $text );
			}
		}

		// Replace %parent_title with the Post's Parent Title.
		$post_parent_id = wp_get_post_parent_id( $post_id );
		$text           = str_replace( '%parent_title%', get_the_title( $post_parent_id ), $text );
		$text           = str_replace( '%parent_title', get_the_title( $post_parent_id ), $text ); // Backward compat.

		// Replace any %parent_custom_field_NAME with Post's Parent Custom Field Value.
		if ( preg_match_all( '/%parent_custom_field_(.*?)%/', $text, $matches ) ) {
			foreach ( $matches[1] as $index => $custom_field ) {
				$text = str_replace( $matches[0][ $index ], get_post_meta( $post_parent_id, $custom_field, true ), $text );
			}
		}

		// If distance is blank, strip tags and return.
		if ( empty( $distance ) ) {
			$text = str_replace( '%distance_km%', '', $text );
			$text = str_replace( '%distance_km', '', $text );
			$text = str_replace( '%distance_miles%', '', $text );
			$text = str_replace( '%distance_miles', '', $text );
			$text = str_replace( '%distance%', '', $text );
			$text = str_replace( '%distance', '', $text );
			return $text;
		}

		// Replace Distance (Kilometres).
		$text = str_replace( '%distance_km%', round( ( $distance * 1.6 ), 2 ), $text );
		$text = str_replace( '%distance_km', round( ( $distance * 1.6 ), 2 ), $text );

		// Replace Distance (Miles).
		$text = str_replace( '%distance_miles%', round( $distance, 2 ), $text );
		$text = str_replace( '%distance_miles', round( $distance, 2 ), $text );

		// Replace Distance (Miles).
		// Backward compat.
		$text = str_replace( '%distance%', round( $distance, 2 ), $text );
		$text = str_replace( '%distance', round( $distance, 2 ), $text );

		// Return.
		return $text;

	}

	/**
	 * Safely generate an excerpt, stripping tags, shortcodes, falling back
	 * to the content if the Post Type doesn't have excerpt support, and applying filters so that
	 * third party plugins (such as translation plugins) can determine the final output.
	 *
	 * @since   2.5.2
	 *
	 * @param   WP_Post $post               WordPress Post.
	 * @param   bool    $fallback           Use Content if no Excerpt exists.
	 * @return  string                          Excerpt
	 */
	private function get_excerpt( $post, $fallback = true ) {

		// Fetch excerpt.
		if ( empty( $post->post_excerpt ) ) {
			if ( $fallback ) {
				$excerpt = $post->post_content;
			} else {
				$excerpt = $post->post_excerpt;
			}
		} else {
			$excerpt = apply_filters( 'get_the_excerpt', $post->post_excerpt, $post );
		}

		// Strip shortcodes.
		$excerpt = strip_shortcodes( $excerpt );

		// Strip HTML Tags.
		$excerpt = wp_strip_all_tags( $excerpt );

		// Decode excerpt to avoid encoding issues on status output.
		$excerpt = html_entity_decode( $excerpt );

		// Finally, trim the output.
		$excerpt = trim( $excerpt );

		/**
		 * Filters the dynamic {excerpt} replacement, when a Post's status is being built.
		 *
		 * @since   2.5.2
		 *
		 * @param   string      $excerpt    Post Excerpt.
		 * @param   WP_Post     $post       WordPress Post.
		 */
		$excerpt = apply_filters( 'page_generator_pro_shortcode_related_links_get_excerpt', $excerpt, $post );

		// Return.
		return $excerpt;

	}

	/**
	 * Returns an array of shortcode attributes and values where the given key is contained
	 * in the shortcode attribute key.
	 *
	 * @since   2.2.8
	 *
	 * @param   string $key    Key to search.
	 * @param   array  $atts   Shortcode Attributes.
	 * @return  mixed           false | array
	 */
	private function get_shortcode_atts_by_partial_key( $key, $atts ) {

		if ( ! is_array( $atts ) ) {
			return false;
		}

		$found_atts = array();
		foreach ( $atts as $att => $value ) {
			if ( strpos( $att, $key ) === false ) {
				continue;
			}

			$found_atts[ $att ] = $value;
		}

		// If no attributes found, bail.
		if ( count( $found_atts ) === 0 ) {
			return false;
		}

		return $found_atts;

	}

}
