<?php
/**
 * Divi Integration Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Registers Divi as a Plugin integration:
 * - Enable Divi on Content Groups
 * - Register as an overwrite section in Content Groups
 * - Register Dynamic Elements as Divi Modules
 * - Copy / don't copy metadata to generated Pages, depending on if the integration is active
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 3.0.7
 */
class Page_Generator_Pro_Divi extends Page_Generator_Pro_Integration {

	/**
	 * Holds the base object.
	 *
	 * @since   3.0.7
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Constructor
	 *
	 * @since   3.0.7
	 *
	 * @param   object $base    Base Plugin Class.
	 */
	public function __construct( $base ) {

		// Store base class.
		$this->base = $base;

		// Set Theme Name.
		$this->theme_name = 'Divi';

		// Set Plugin.
		$this->plugin_folder_filename = array(
			'divi-builder/divi-builder.php',
		);

		// Set Meta Keys used by this Plugin.
		$this->meta_keys = array(
			'/^_et_(.*)/i',
			'_global_colors_info',
		);

		// Adds Content Groups as a choice under third party Post Types.
		add_filter( 'et_builder_third_party_post_types', array( $this, 'register_support' ) );

		// Always enables Divi on Content Groups in the Backend, even if the user hasn't enabled it in Theme Options.
		add_filter( 'et_builder_post_types', array( $this, 'register_support' ) );

		// Always enables Divi on Content Groups in the Frontend, even if the user hasn't enabled it in Theme Options.
		add_filter( 'et_fb_post_types', array( $this, 'register_support' ) );

		// Make Layouts of any Post Type available to Content Groups.
		add_filter( 'et_pb_show_all_layouts_built_for_post_type', array( $this, 'register_layout_support' ) );

		// Show all Metabox options in Divi Settings.
		add_action( 'page_generator_pro_groups_ui_add_meta_boxes', array( $this, 'register_metabox_support' ) );

		// Set current screen to editing a Content Group when editing with Divi.
		add_filter( 'page_generator_pro_screen_get_current_screen_before', array( $this, 'set_current_screen' ) );

		// Register shortcodes as Divi Modules.
		add_action( 'init', array( $this, 'register_modules' ) );

		// Remove Page Builder data from Group Settings if overwriting content is disabled, and an existing generated page already exists.
		add_filter( 'page_generator_pro_generate_remove_content_from_settings_ignored_keys', array( $this, 'remove_post_meta_from_content_group' ), 10, 2 );

		// Remove Plugin data from Group Settings if Plugin isn't active on Generation.
		add_filter( 'page_generator_pro_groups_get_settings_remove_orphaned_settings', array( $this, 'remove_line_break_holders' ) );
		add_filter( 'page_generator_pro_groups_get_settings_remove_orphaned_settings', array( $this, 'remove_orphaned_settings' ) );

		// Convert Plugin Divi Modules to Text Modules with Shortcodes as content.
		add_filter( 'page_generator_pro_generate_content_settings', array( $this, 'convert_modules_to_text_modules' ), 10, 1 );

	}

	/**
	 * Allows The Divi Builder (and therefore Divi Theme 3.0+) to inject its Page Builder
	 * into Page Generator Pro's Groups
	 *
	 * @since   1.2.7
	 *
	 * @param   array $post_types     Post Types Supporting Divi.
	 * @return  array                   Post Types Supporting Divi
	 */
	public function register_support( $post_types ) {

		$post_types[] = 'page-generator-pro';
		return $post_types;

	}

	/**
	 * Allows The Divi Builder (and therefore Divi Theme 3.0+) to inject its Page Builder Layouts
	 * into the chosen Custom Post Types
	 *
	 * @since   1.4.1
	 *
	 * @param   mixed $post_types     string | array.
	 * @return  array                   Post Types to get Layouts from
	 */
	public function register_layout_support( $post_types ) {

		// Bail if we're not on Page Generator Pro.
		if ( $post_types !== 'page-generator-pro' ) {
			return $post_types;
		}

		// If $post_types isn't an array, make it one.
		if ( ! is_array( $post_types ) ) {
			$post_types = array( $post_types );
		}

		// Fetch Public Post Types.
		$public_post_types = $this->base->get_class( 'common' )->get_post_types();

		// Add all Public Post Types to $post_types.
		foreach ( $public_post_types as $public_post_type ) {
			// Add Custom Post Type to Divi, so the Page Builder displays.
			$post_types[] = $public_post_type->name;
		}

		// Remove duplicates.
		$post_types = array_unique( $post_types );

		// Return.
		return $post_types;

	}

	/**
	 * Allows The Divi Builder (and therefore Divi Theme 3.0+) to inject its Page Builder
	 * Meta Box into this Plugin's enabled Custom Post Types
	 *
	 * @since   1.4.1
	 *
	 * @param   obj $post_type_instance     Post Type Instance.
	 */
	public function register_metabox_support( $post_type_instance ) {

		// Don't need to do anything if we're not in the admin interface.
		if ( ! is_admin() ) {
			return;
		}

		// Don't add the meta box if Divi Builder isn't active.
		if ( ! function_exists( 'et_single_settings_meta_box' ) ) {
			return;
		}

		// Add Meta Box.
		// We don't use add_meta_box( 'et_settings_meta_box'... because we need to change
		// the Post Type = post, so that all settings display, without changing the global $post.
		add_meta_box(
			'et_settings_meta_box',
			__( 'Divi Settings', 'page-generator-pro' ),
			array( $this, 'output_metabox' ),
			$post_type_instance->post_type_name,
			'side',
			'high'
		);

		// Remove the bottom Actions Box, as clicking a button on it prompts Divi's 'leave site' JS.
		remove_meta_box( $this->base->get_class( 'post_type' )->post_type_name . '-actions-bottom', $this->base->get_class( 'post_type' )->post_type_name, 'side' );

	}

	/**
	 * Outputs the Divi Settings Metabox
	 *
	 * @since   1.6.4
	 */
	public function output_metabox() {

		// Trick Divi into outputting Post settings.
		global $post;
		$new_post            = $post;
		$new_post->post_type = 'post';

		// Call metabox function directly.
		et_single_settings_meta_box( $new_post );

	}

	/**
	 * Tells the Screen class that we're editing a Content Group when editing it with Divi.
	 *
	 * @since   3.1.4
	 *
	 * @param   array $result     Screen and Section.
	 * @return  array                   Screen and Section
	 */
	public function set_current_screen( $result ) {

		// Bail if this isn't a Divi AJAX request.
		if ( ! array_key_exists( 'et_post_type', $_REQUEST ) ) { // phpcs:ignore
			return $result;
		}
		if ( ! array_key_exists( 'et_post_id', $_REQUEST ) ) { // phpcs:ignore
			return $result;
		}
		if ( $this->base->plugin->name !== get_post_type( absint( $_REQUEST['et_post_id'] ) ) ) { // phpcs:ignore
			return $result;
		}

		// Return a modified screen array to tell the Screen class that we're editing a Content Group.
		return array(
			'screen'  => 'content_groups',
			'section' => 'edit',
		);

	}

	/**
	 * Registers Plugin Shortcodes as Modules, so that they can be used in the Divi Builder
	 *
	 * @since   3.0.7
	 */
	public function register_modules() {

		// Bail if Divi isn't loaded.
		if ( ! class_exists( 'ET_Builder_Module' ) ) {
			return;
		}

		// Determine the screen that we're on.
		$screen = $this->base->get_class( 'screen' )->get_current_screen();

		// Bail if we're not editing a Content Group.
		if ( $screen['screen'] !== 'content_groups' ) {
			return;
		}

		// Get shortcodes.
		$shortcodes = $this->base->get_class( 'shortcode' )->get_shortcodes();

		// Bail if no shortcodes are available.
		if ( ! is_array( $shortcodes ) || count( $shortcodes ) === 0 ) {
			return;
		}

		// Iterate through shortcodes, registering them.
		foreach ( $shortcodes as $shortcode => $shortcode_properties ) {
			// Skip if no tabs or fields.
			if ( ! $shortcode_properties['tabs'] ) {
				continue;
			}
			if ( ! $shortcode_properties['fields'] ) {
				continue;
			}

			// Register Module.
			$class_name = 'Page_Generator_Pro_Divi_Module_' . str_replace( '-', '_', $shortcode );
			$module     = new $class_name();
		}

	}

	/**
	 * Replace <!-- [et_pb_line_break_holder] --> with newlines in the Content Group's
	 * content, as Divi stores newlines with a shortcode, which prevents e.g. block
	 * spintax from processing.
	 *
	 * @since   3.6.8
	 *
	 * @param   array $settings   Group Settings.
	 * @return  array               Group Settings
	 */
	public function remove_line_break_holders( $settings ) {

		$settings['content'] = str_replace( '<!- [et_pb_line_break_holder] ->', "\n", $settings['content'] );
		$settings['content'] = str_replace( '<!-- [et_pb_line_break_holder] -->', "\n", $settings['content'] );
		return $settings;

	}

	/**
	 * Removes orphaned metadata in the Group Settings during Generation,
	 * if Divi is not active.
	 *
	 * @since   3.3.7
	 *
	 * @param   array $settings   Group Settings.
	 * @return  array               Group Settings
	 */
	public function remove_orphaned_settings( $settings ) {

		// Don't remove settings if Divi Theme is active.
		if ( $this->is_theme_active() ) {
			return $settings;
		}

		// Don't remove settings if Divi Plugin is active.
		if ( $this->is_active() ) {
			return $settings;
		}

		// Remove Divi Meta Keys from the Group Settings during Generation.
		return $this->remove_orphaned_settings_metadata( $settings, $this->meta_keys );

	}

	/**
	 * If the given Content Group's content contains Modules registered by this Plugin, converts them
	 * to Text Modules with their content set to the shortcode syntax, so subsequent generation routines can parse them.
	 *
	 * @since   3.0.7
	 *
	 * @param   array $settings       Group Settings.
	 * @return  array                   Group Settings
	 */
	public function convert_modules_to_text_modules( $settings ) {

		// Bail if no Divi Data exists.
		if ( ! isset( $settings['post_meta']['_et_builder_version'] ) ) {
			return $settings;
		}

		// Get shortcodes.
		$shortcodes = $this->base->get_class( 'shortcode' )->get_shortcodes();

		// Bail if no shortcodes are available.
		if ( ! is_array( $shortcodes ) || ! count( $shortcodes ) ) {
			return $settings;
		}

		// Iterate through Shortcodes.
		$content = $settings['content'];
		foreach ( $shortcodes as $shortcode_name => $shortcode_properties ) {
			$divi_module_name_start = '[page-generator-pro-divi-' . $shortcode_name;
			$divi_module_name_end   = '][/page-generator-pro-divi-' . $shortcode_name . ']';
			$shortcode_name         = $this->base->plugin->name . '-' . $shortcode_name;

			// Iterate through content, finding each instance of this module .
			$start = strpos( $content, $divi_module_name_start, 0 );
			while ( $start !== false ) {
				// Get module string and its attributes.
				$end                        = strpos( $content, $divi_module_name_end, $start + strlen( $divi_module_name_start ) ) + strlen( $divi_module_name_end );
				$divi_module_shortcode      = substr( $content, $start, ( $end - $start ) );
				$divi_module_shortcode_atts = shortcode_parse_atts( str_replace( $divi_module_name_start, '', str_replace( $divi_module_name_end, '', $divi_module_shortcode ) ) );

				// Merge attributes with shortcode's default attributes, so we're just left
				// with an array of shortcode compatible attributes that exclude Divi's e.g. _builder_version.
				$atts = shortcode_atts(
					$shortcode_properties['default_values'],
					$divi_module_shortcode_atts,
					$shortcode_name
				);

				// Build shortcode.
				$shortcode_atts = '';
				foreach ( $atts as $key => $value ) {
					if ( $value === false ) {
						$value = '0';
					}
					$shortcode_atts .= ' ' . $key . '="' . $value . '"';
				}
				$shortcode = '[' . $shortcode_name . $shortcode_atts . ']';

				// Inject Page Generator Pro Shortcode inside the Divi text module shortcode.
				$divi_text_module_shortcode = str_replace( $divi_module_name_start, '[et_pb_text', str_replace( $divi_module_name_end, '][/et_pb_text]', $divi_module_shortcode ) );
				$divi_text_module_shortcode = str_replace( '][', ']' . $shortcode . '[', $divi_text_module_shortcode );

				// Replace Divi module with Divi text module.
				$settings['content'] = str_replace( $divi_module_shortcode, $divi_text_module_shortcode, $settings['content'] );

				// Find the next instance of this module.
				$start = strpos( $content, $divi_module_name_start, $end );
			}
		}

		// Return.
		return $settings;

	}

}
