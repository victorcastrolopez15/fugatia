<?php
/**
 * Genesis Integration Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Registers Genesis Theme as a Plugin integration:
 * - Enable Genesis on Content Groups
 * - Register as an overwrite section in Content Groups
 * - Copy / don't copy metadata to generated Pages, depending on if the integration is active
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 3.0.5
 */
class Page_Generator_Pro_Genesis extends Page_Generator_Pro_Integration {

	/**
	 * Holds the base object.
	 *
	 * @since   3.0.5
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Constructor
	 *
	 * @since   3.0.5
	 *
	 * @param   object $base    Base Plugin Class.
	 */
	public function __construct( $base ) {

		// Store base class.
		$this->base = $base;

		// Set Meta Keys used by this Plugin.
		$this->meta_keys = array(
			'/^_genesis_(.*)/i',
		);

		// Set Overwrite Setting's Key used by this Plugin.
		$this->overwrite_section = 'genesis';

		// Register Post Type Support for Content Groups.
		add_action( 'init', array( $this, 'register_post_type_support' ) );

		// Add Overwrite Section if Genesis enabled.
		add_filter( 'page_generator_pro_common_get_content_overwrite_sections', array( $this, 'add_overwrite_section' ) );

		// Ignore Genesis meta keys if overwriting is disabled for Genesis.
		add_filter( 'page_generator_pro_generate_set_post_meta_ignored_keys', array( $this, 'prevent_post_meta_copy_to_generated_content' ), 10, 4 );

		// Remove Plugin data from Group Settings if Plugin isn't active on Generation.
		add_filter( 'page_generator_pro_groups_get_settings_remove_orphaned_settings', array( $this, 'remove_orphaned_settings' ) );

	}

	/**
	 * Register Genesis Meta Boxes as supported for the Content Groups Post Type
	 *
	 * @since   3.0.5
	 */
	public function register_post_type_support() {

		// Bail if Genesis isn't active.
		if ( ! $this->is_active() ) {
			return;
		}

		// Add Support.
		add_post_type_support(
			'page-generator-pro',
			array(
				'genesis-seo',
				'genesis-scripts',
				'genesis-layouts',
				'genesis-breadcrumbs-toggle',
				'genesis-footer-widgets-toggle',
				'genesis-title-toggle',
			)
		);

	}

	/**
	 * Defines available content overwrite sections.
	 *
	 * @since   3.0.5
	 *
	 * @param   array $sections    Content Overwrite Sections.
	 * @return  array                Content Overwrite Sections
	 */
	public function add_overwrite_section( $sections ) {

		// Bail if Genesis isn't active.
		if ( ! $this->is_active() ) {
			return $sections;
		}

		// Add Genesis.
		$sections['genesis'] = __( 'Genesis Framework', 'page-generator-pro' );

		// Return.
		return $sections;

	}

	/**
	 * Removes orphaned metadata in the Group Settings during Generation,
	 * if Genesis is not active
	 *
	 * @since   3.3.7
	 *
	 * @param   array $settings   Group Settings.
	 * @return  array               Group Settings
	 */
	public function remove_orphaned_settings( $settings ) {

		// Don't remove settings if Genesis is active.
		if ( $this->is_active() ) {
			return $settings;
		}

		// Remove Genesis Meta Keys from the Group Settings during Generation.
		return $this->remove_orphaned_settings_metadata( $settings, $this->meta_keys );

	}

	/**
	 * Checks if Genesis is active
	 *
	 * @since   3.0.5
	 *
	 * @return  bool    Is Active
	 */
	public function is_active() {

		return defined( 'GENESIS_LOADED_FRAMEWORK' );

	}

}
