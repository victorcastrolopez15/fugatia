<?php
/**
 * Search Exclude Integration Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Registers Search Exclude as a Plugin integration:
 * - Copy metadata to generated Pages
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 3.0.8
 */
class Page_Generator_Pro_Search_Exclude extends Page_Generator_Pro_Integration {

	/**
	 * Holds the base object.
	 *
	 * @since   3.0.8
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Constructor
	 *
	 * @since   3.0.8
	 *
	 * @param   object $base    Base Plugin Class.
	 */
	public function __construct( $base ) {

		// Store base class.
		$this->base = $base;

		// Set Plugin.
		$this->plugin_folder_filename = 'search-exclude/search-exclude.php';

		// Copy Search Exclude settings to Generated Page.
		add_action( 'page_generator_pro_generate_set_post_meta', array( $this, 'copy_settings_to_generated_page' ), 10, 2 );

	}

	/**
	 * Copies Search Exclude settings from the Content Group to the Generated Content.
	 *
	 * @since   3.0.8
	 *
	 * @param   int $post_id        Generated Page ID.
	 * @param   int $group_id       Group ID.
	 */
	public function copy_settings_to_generated_page( $post_id, $group_id ) {

		// Bail if Search Exclude isn't active.
		if ( ! $this->is_active() ) {
			return;
		}

		// Get Search Exclude settings.
		$excluded = get_option( 'sep_exclude' );
		if ( ! is_array( $excluded ) ) {
			$excluded = array();
		}

		// Determine if the Content Group is set to exclude.
		$exclude = ( in_array( $group_id, $excluded ) === false ? false : true ); // phpcs:ignore
		if ( ! $exclude ) {
			// Exclude is disabled, remove the Generated Page from the array.
			$excluded = array_diff( $excluded, array( $post_id ) );
		} else {
			// Exclude is enabled, add the Generated Page to the array.
			$excluded = array_unique( array_merge( $excluded, array( $post_id ) ) );
		}

		// Save.
		update_option( 'sep_exclude', $excluded );

	}

}
