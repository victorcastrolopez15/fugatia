<?php
/**
 * Beaver Builder Integration Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Registers Beaver Builder as a Plugin integration:
 * - Display metaboxes on Content Groups
 * - Register as an overwrite section in Content Groups
 * - Copy / don't copy metadata to generated Pages, depending on if the integration is active
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 3.3.7
 */
class Page_Generator_Pro_Beaver_Builder extends Page_Generator_Pro_Integration {

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

		// Set Meta Keys used by this Plugin.
		$this->meta_keys = array(
			'/^_fl_builder_(.*)/i',
		);

		add_filter( 'fl_builder_post_types', array( $this, 'register_beaver_builder_support' ) );
		add_filter( 'page_generator_pro_generate_content_settings', array( $this, 'beaver_builder_remove_builder_data_on_generation' ), 10, 1 );

		// Remove Page Builder data from Group Settings if overwriting content is disabled, and an existing generated page already exists.
		add_filter( 'page_generator_pro_generate_remove_content_from_settings_ignored_keys', array( $this, 'remove_post_meta_from_content_group' ), 10, 2 );

		// Remove Plugin data from Group Settings if Plugin isn't active on Generation.
		add_filter( 'page_generator_pro_groups_get_settings_remove_orphaned_settings', array( $this, 'remove_orphaned_settings' ) );

	}

	/**
	 * Allows Beaver Builder to inject its Page Builder
	 * into Page Generator Pro's Groups
	 *
	 * @since   1.3.7
	 *
	 * @param   array $post_types     Post Types Supporting Beaver Builder.
	 * @return  array                   Post Types Supporting Beaver Builder
	 */
	public function register_beaver_builder_support( $post_types ) {

		$post_types[] = 'page-generator-pro';
		return $post_types;

	}

	/**
	 * Removes the Group's Beaver Builder Post Meta Data immediately prior to the generation routine
	 * running, as this information is also stored in the Post Content.  In turn, this prevents duplicated
	 * effort of shortcode processing across both the Post Content, _fl_builder_data and _fl_builder_draft Post Meta,
	 * which would result in e.g. duplicate Media Library Images if using the Media Library shortcode
	 *
	 * @since   2.8.5
	 *
	 * @param   array $settings       Group Settings.
	 */
	public function beaver_builder_remove_builder_data_on_generation( $settings ) {

		// Bail if Beaver Builder isn't active.
		if ( ! class_exists( 'FLBuilderLoader' ) ) {
			return $settings;
		}

		// Just return the Group settings if no Beaver Builder Data exists.
		if ( ! isset( $settings['post_meta'] ) ) {
			return $settings;
		}
		if ( ! isset( $settings['post_meta']['_fl_builder_data'] ) ) {
			return $settings;
		}

		// Remove Post Content and Beaver Builder Draft Data.
		$settings['content'] = '';
		unset( $settings['post_meta']['_fl_builder_draft'] );

		// Return.
		return $settings;

	}

	/**
	 * Removes orphaned Beaver Builder metadata in the Group Settings during Generation,
	 * if Beaver Builder is not active
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

		// Remove Beaver Builder Meta Keys from the Group Settings during Generation.
		return $this->remove_orphaned_settings_metadata( $settings, $this->meta_keys );

	}

	/**
	 * Checks if Beaver Builder is active
	 *
	 * @since   3.3.7
	 *
	 * @return  bool    Is Active
	 */
	public function is_active() {

		if ( ! class_exists( 'FLBuilderLoader' ) ) {
			return false;
		}

		return true;

	}

}
