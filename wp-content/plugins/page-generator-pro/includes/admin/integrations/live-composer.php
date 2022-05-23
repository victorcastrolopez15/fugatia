<?php
/**
 * Live Composer Integration Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Registers Live Composer as a Plugin integration:
 * - Enable Live Composer on Content Groups
 * - Copy / don't copy metadata to generated Pages, depending on if the integration is active
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 3.3.7
 */
class Page_Generator_Pro_Live_Composer extends Page_Generator_Pro_Integration {

	/**
	 * Holds the base object.
	 *
	 * @since   3.3.7
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Constructor
	 *
	 * @since   3.3.7
	 *
	 * @param   object $base    Base Plugin Class.
	 */
	public function __construct( $base ) {

		// Store base class.
		$this->base = $base;

		// Set Plugin.
		$this->plugin_folder_filename = array(
			'live-composer-page-builder/ds-live-composer.php',
		);

		// Set Meta Keys used by this Plugin.
		$this->meta_keys = array(
			'dslc_code',
			'dslc_content_for_search',
		);

		add_filter( 'dslc_can_edit_in_lc', array( $this, 'register_live_composer_page_builder_support' ), 10, 2 );
		add_filter( 'page_generator_pro_admin_body_class', array( $this, 'live_composer_body_class' ) );
		add_filter( 'page_generator_pro_screen_get_current_screen', array( $this, 'live_composer_set_current_screen' ), 10, 2 );
		add_filter( 'page_generator_pro_groups_get_post_meta_dslc_code', array( $this, 'live_composer_decode_meta' ) );
		add_filter( 'page_generator_pro_generate_set_post_meta_dslc_code', array( $this, 'live_composer_encode_meta' ) );

		// Remove Page Builder data from Group Settings if overwriting content is disabled, and an existing generated page already exists.
		add_filter( 'page_generator_pro_generate_remove_content_from_settings_ignored_keys', array( $this, 'remove_post_meta_from_content_group' ), 10, 2 );

		// Remove Plugin data from Group Settings if Plugin isn't active on Generation.
		add_filter( 'page_generator_pro_groups_get_settings_remove_orphaned_settings', array( $this, 'remove_orphaned_settings' ) );

	}

	/**
	 * Allows Live Composer to inject its Page Builder into Page Generator Pro's Groups,
	 *
	 * @since   1.6.8
	 *
	 * @param   bool   $can_edit   Can Edit Post Type.
	 * @param   string $post_type  Post Type.
	 * @return  bool                Can Edit Post Type
	 */
	public function register_live_composer_page_builder_support( $can_edit, $post_type ) {

		// Bail if we're not on a Page Generator Pro group.
		if ( $post_type !== 'page-generator-pro' ) {
			return $can_edit;
		}

		// Enable Live Composer on Page Generator Pro Groups.
		return true;

	}

	/**
	 * Registers Live Composer's screen name to ensure the wpzinc class is added to the <body> tag
	 *
	 * @since   2.5.8
	 *
	 * @param   array $screens    Screen Names.
	 * @return  array               Screen Names
	 */
	public function live_composer_body_class( $screens ) {

		$screens[] = 'livecomposer_editor';
		return $screens;

	}

	/**
	 * Tells the Screen class that we're editing a Content Group when editing it with Live Composer.
	 *
	 * @since   2.5.8
	 *
	 * @param   array  $result     Screen and Section.
	 * @param   string $screen_id  Screen.
	 * @return  array                   Screen and Section
	 */
	public function live_composer_set_current_screen( $result, $screen_id ) {

		// Bail if we're not on the Live Composer Editor screen.
		if ( $screen_id !== 'livecomposer_editor' ) {
			return $result;
		}

		// Check if we're editing a Content Group.
		if ( ! isset( $_REQUEST['page_id'] ) ) { // phpcs:ignore
			return $result;
		}
		if ( $this->base->plugin->name !== get_post_type( absint( $_REQUEST['page_id'] ) ) ) { // phpcs:ignore
			return $result;
		}

		// Return a modified screen array to tell the Screen class that we're editing a Content Group.
		return array(
			'screen'  => 'content_groups',
			'section' => 'edit',
		);

	}

	/**
	 * JSON decodes (or base64 decodes, for older versions) Live Composer's Page Builder metadata into an array,
	 * so that the Generate Routine can iterate through it, replacing Keywords, Shortcodes etc.
	 *
	 * Also calls dslc_decode_shortcodes() to undo dslc_encode_shortcodes(),
	 * which converts square brackets to %(% and %)%.
	 *
	 * @since   3.1.5
	 *
	 * @param   string $value  Live Composer Page Builder Data.
	 * @return  array           Live Composer Page Builder Data
	 */
	public function live_composer_decode_meta( $value ) {

		// Bail if Live Composer isn't active.
		if ( ! function_exists( 'dslc_decode_shortcodes' ) || ! function_exists( 'dslc_json_decode' ) ) {
			return $value;
		}

		// Decode shortcodes.
		$value = dslc_decode_shortcodes( $value );

		// Stripslashes.
		$value = stripslashes( $value );

		// Decode JSON string into object.
		$value = dslc_json_decode( $value );

		// Return.
		return $value;

	}

	/**
	 * JSON encodes Live Composer's Page Builder metadata (which was decoded in our live_composer_decode_meta() function above)
	 * into a string immediately before it's copied to the Generated Page.
	 *
	 * @since   3.1.5
	 *
	 * @param   array $value   Live Composer Page Builder Data.
	 * @return  string          Live Composer Page Builder Data
	 */
	public function live_composer_encode_meta( $value ) {

		// Bail if Live Composer isn't active.
		if ( ! function_exists( 'dslc_encode_shortcodes_in_array' ) ) {
			return $value;
		}

		// Bail if value is a string.
		if ( is_string( $value ) ) {
			return $value;
		}

		// Encode shortcodes.
		$value = dslc_encode_shortcodes_in_array( $value );

		// Encode into JSON string.
		$value = wp_json_encode( $value );

		// Add slashes with wp_slash() so update_post_meta() doesn't remove all slashes that we need.
		$value = wp_slash( $value );

		// Return.
		return $value;

	}

	/**
	 * Removes orphaned Live Composer metadata in the Group Settings during Generation,
	 * if Live Composer is not active
	 *
	 * @since   3.3.7
	 *
	 * @param   array $settings   Group Settings.
	 * @return  array               Group Settings
	 */
	public function remove_orphaned_settings( $settings ) {

		// Don't remove settings if the Plugin is active.
		if ( $this->is_active() ) {
			return $settings;
		}

		// Remove Live Composer Meta Keys from the Group Settings during Generation.
		return $this->remove_orphaned_settings_metadata( $settings, $this->meta_keys );

	}

}
