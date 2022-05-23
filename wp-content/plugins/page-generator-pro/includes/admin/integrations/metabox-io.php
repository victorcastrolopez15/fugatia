<?php
/**
 * Metabox.io Integration Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Registers Metabox.io as a Plugin integration:
 * - Register metabox(es) on Content Groups
 *
 * Themes that use this Plugin to register Meta Boxes + Custom Fields e.g. Construction Theme, Wize Law Theme.
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 3.3.7
 */
class Page_Generator_Pro_Metabox_IO extends Page_Generator_Pro_Integration {

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
			'/^mb_(.*)/i',
		);

		add_filter( 'rwmb_meta_boxes', array( $this, 'register_meta_box_io_support' ), 9999 );

		// Remove Plugin data from Group Settings if Plugin isn't active on Generation.
		add_filter( 'page_generator_pro_groups_get_settings_remove_orphaned_settings', array( $this, 'remove_orphaned_settings' ) );

	}

	/**
	 * Allows Metabox.io to register its metaboxes into Page Generator Pro (Themes that use this Plugin to register Meta Boxes +
	 * Custom Fields e.g. Construction Theme, Wize Law Theme)
	 *
	 * @since   2.6.3
	 *
	 * @param   array $meta_boxes     Meta Boxes.
	 * @return  array                   Meta Boxes
	 */
	public function register_meta_box_io_support( $meta_boxes ) {

		// Bail if no metaboxes are registered.
		if ( ! is_array( $meta_boxes ) ) {
			return $meta_boxes;
		}
		if ( ! count( $meta_boxes ) ) {
			return $meta_boxes;
		}

		// Get Post Types that Page Generator Pro can generate content for.
		$supported_post_types = array_keys( $this->base->get_class( 'common' )->get_post_types() );

		// Add Meta Boxes to Page Generator Pro.
		foreach ( $meta_boxes as $index => $meta_box ) {
			// Some themes use 'pages', others use 'post_types', so we check for both array keys.
			if ( isset( $meta_box['pages'] ) && is_array( $meta_box['pages'] ) && count( $meta_box['pages'] ) > 0 ) {
				foreach ( $meta_box['pages'] as $post_type ) {
					if ( in_array( $post_type, $supported_post_types, true ) ) {
						// The meta box is used on a Post Type that Page Generator Pro can generate content for.
						// Add the Content Group Post Type to the meta box so that the meta box's fields are displayed
						// when editing a Content Group.
						$meta_boxes[ $index ]['pages'][] = 'page-generator-pro';
						break;
					}
				}
				continue;
			}

			if ( isset( $meta_box['post_types'] ) && is_array( $meta_box['post_types'] ) && count( $meta_box['post_types'] ) > 0 ) {
				foreach ( $meta_box['post_types'] as $post_type ) {
					if ( in_array( $post_type, $supported_post_types, true ) ) {
						// The meta box is used on a Post Type that Page Generator Pro can generate content for.
						// Add the Content Group Post Type to the meta box so that the meta box's fields are displayed
						// when editing a Content Group.
						$meta_boxes[ $index ]['post_types'][] = 'page-generator-pro';
					}
				}
			}
		}

		return $meta_boxes;

	}

	/**
	 * Removes orphaned XX metadata in the Group Settings during Generation,
	 * if XX is not active
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

		// Remove XX Meta Keys from the Group Settings during Generation.
		return $this->remove_orphaned_settings_metadata( $settings, $this->meta_keys );

	}

	/**
	 * Checks if Metabox.io is active
	 *
	 * @since   3.3.7
	 *
	 * @return  bool    Plugin is Active
	 */
	public function is_active() {

		// We don't check for a Plugin activation status, as Metabox.io can be bundled within a Theme.
		if ( ! class_exists( 'RWMB_Core' ) ) {
			return false;
		}

		return true;
	}

}
