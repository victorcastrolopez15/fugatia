<?php
/**
 * Oxygen Page Builder Integration Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Registers Oxygen Page Builder as a Plugin integration:
 * - Copy / don't copy metadata to generated Pages, depending on if the integration is active
 * - Encode/decode Page Builder data when generating Pages
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 3.3.7
 */
class Page_Generator_Pro_Oxygen extends Page_Generator_Pro_Integration {

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
			'oxygen/functions.php',
		);

		// Set Meta Keys used by this Plugin.
		$this->meta_keys = array(
			'/^ct_(.*)/i',
			'oxygen_lock_post_edit_mode',
		);

		add_filter( 'page_generator_pro_groups_get_post_meta_ct_builder_shortcodes', array( $this, 'oxygen_decode_meta' ) );
		add_filter( 'page_generator_pro_generate_set_post_meta_ct_builder_shortcodes', array( $this, 'oxygen_encode_meta' ) );

		// Remove Page Builder data from Group Settings if overwriting content is disabled, and an existing generated page already exists.
		add_filter( 'page_generator_pro_generate_remove_content_from_settings_ignored_keys', array( $this, 'remove_post_meta_from_content_group' ), 10, 2 );

		// Remove Plugin data from Group Settings if Plugin isn't active on Generation.
		add_filter( 'page_generator_pro_groups_get_settings_remove_orphaned_settings', array( $this, 'remove_orphaned_settings' ) );

	}

	/**
	 * Base64 decodes Oxygen's Page Builder metadata into an array, so that the Generate Routine
	 * can iterate through it, replacing Keywords, Shortcodes etc.
	 *
	 * Also calls oxygen_vsb_filter_shortcode_content_decode() to undo oxygen_vsb_filter_shortcode_content_encode(),
	 * which converts square brackets to _OXY_OPENING_BRACKET_ and _OXY_CLOSING_BRACKET_.
	 *
	 * @since   2.7.2
	 *
	 * @param   string $value  Oxygen Page Builder Data.
	 * @return  array           Oxygen Page Builder Data
	 */
	public function oxygen_decode_meta( $value ) {

		// Bail if Oxygen function doesn't exist.
		if ( ! function_exists( 'parse_shortcodes' ) ) {
			return $value;
		}

		$value = str_replace( '_OXY_OPENING_BRACKET_', '[', $value );
		$value = str_replace( '_OXY_CLOSING_BRACKET_', ']', $value );

		// Decode.
		return parse_shortcodes( $value, true, false );

	}

	/**
	 * Base64 encodes Oxygen Page Builder metadata into a string immediately before it's
	 * copied to the Generated Page.
	 *
	 * @since   2.9.5
	 *
	 * @param   array $value   Oxygen Page Builder Data.
	 * @return  string          Oxygen Page Builder Data
	 */
	public function oxygen_encode_meta( $value ) {

		// Bail if Oxygen function doesn't exist.
		if ( ! function_exists( 'parse_components_tree' ) ) {
			return $value;
		}

		// Convert shortcode to string.
		$value = parse_components_tree( $value['content'] );

		// Return.
		return $value;

	}

	/**
	 * Removes orphaned Oxygen metadata in the Group Settings during Generation,
	 * if Oxygen is not active
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

		// Remove Oxygen Meta Keys from the Group Settings during Generation.
		return $this->remove_orphaned_settings_metadata( $settings, $this->meta_keys );

	}

}
