<?php
/**
 * Betheme Integration Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Registers Betheme as a Plugin integration:
 * - Register metaboxes on Content Groups
 * - Register as an overwrite section in Content Groups
 * - Copy / don't copy metadata to generated Pages, depending on if the integration is active
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 3.3.7
 */
class Page_Generator_Pro_Betheme extends Page_Generator_Pro_Integration {

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

		// Set Theme Name.
		$this->theme_name = 'Betheme';

		// Set Meta Keys used by this Theme.
		$this->meta_keys = array(
			'/^mfn-(.*)/i',
		);

		add_action( 'wp_loaded', array( $this, 'register_betheme_support' ) );

		// Remove Page Builder data from Group Settings if overwriting content is disabled, and an existing generated page already exists.
		add_filter( 'page_generator_pro_generate_remove_content_from_settings_ignored_keys', array( $this, 'remove_post_meta_from_content_group' ), 10, 2 );

		// Remove Theme data from Group Settings if Theme isn't active on Generation.
		add_filter( 'page_generator_pro_groups_get_settings_remove_orphaned_settings', array( $this, 'remove_orphaned_settings' ) );

	}

	/**
	 * Allows Betheme's Muffin Builder Meta Box to be output on Page Generator Pro's Groups
	 *
	 * @since   2.1.2
	 */
	public function register_betheme_support() {

		// Bail if Betheme isn't loaded.
		if ( ! class_exists( 'Mfn_Post_Type' ) ) {
			return;
		}

		// Load class.
		include_once $this->base->plugin->folder . '/includes/admin/integrations/pagebuilders-betheme.php';

		// Bail if class didn't load.
		if ( ! class_exists( 'Mfn_Post_Type_Page_Generator_Pro' ) ) {
			return;
		}

		// Invoke class.
		$mfn_post_type_page_generator_pro = new Mfn_Post_Type_Page_Generator_Pro();

	}

	/**
	 * Removes orphaned Betheme metadata in the Group Settings during Generation,
	 * if Betheme is not active
	 *
	 * @since   3.3.7
	 *
	 * @param   array $settings   Group Settings.
	 * @return  array               Group Settings
	 */
	public function remove_orphaned_settings( $settings ) {

		// Don't remove settings if the Plugin is active.
		if ( $this->is_theme_active() ) {
			return $settings;
		}

		// Remove Betheme Meta Keys from the Group Settings during Generation.
		return $this->remove_orphaned_settings_metadata( $settings, $this->meta_keys );

	}

}
