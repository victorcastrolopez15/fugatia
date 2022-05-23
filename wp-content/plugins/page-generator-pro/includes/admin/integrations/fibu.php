<?php
/**
 * Featured Image by URL Integration Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Registers Featured Image by URL as a Plugin integration:
 * - Registering as a Featured Image source on Content Groups
 * - Copy / don't copy metadata to generated Pages, depending on if the integration is active
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.9.3
 */
class Page_Generator_Pro_FIBU extends Page_Generator_Pro_Integration {

	/**
	 * Holds the base object.
	 *
	 * @since   2.9.3
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Constructor
	 *
	 * @since   2.9.3
	 *
	 * @param   object $base    Base Plugin Class.
	 */
	public function __construct( $base ) {

		// Store base class.
		$this->base = $base;

		// Set Plugin.
		$this->plugin_folder_filename = 'featured-image-by-url/featured-image-by-url.php';

		// Add FIBU Defaults.
		add_filter( 'page_generator_pro_groups_get_defaults', array( $this, 'get_defaults' ) );

		// Add FIBU as a Featured Image Source.
		add_filter( 'page_generator_pro_common_get_featured_image_sources', array( $this, 'add_featured_image_source' ) );

		// Output FIBU settings in the Content Groups UI.
		add_action( 'page_generator_pro_groups_ui_output_meta_box_featured_image', array( $this, 'output_meta_box_featured_image' ), 10, 2 );

		// Copy FIBU settings to Generated Page.
		add_filter( 'page_generator_pro_generate_featured_image_fibu', array( $this, 'copy_featured_image_data' ), 10, 6 );

	}

	/**
	 * Add FIBU Default Settings to Content Groups
	 *
	 * @since   2.9.3
	 *
	 * @param   array $defaults   Default Settings.
	 * @return  array               Default Settings
	 */
	public function get_defaults( $defaults ) {

		// Bail if Plugin isn't active.
		if ( ! $this->is_active() ) {
			return $defaults;
		}

		return array_merge(
			$defaults,
			array(
				'featured_image_fibu_url' => '',
				'featured_image_fibu_alt' => '',
			)
		);

	}

	/**
	 * Add FIBU as a Featured Image Source to the Content Groups UI
	 *
	 * @since   2.9.3
	 *
	 * @param   array $sources    Featured Image Sources.
	 * @return  array               Featured Image Sources
	 */
	public function add_featured_image_source( $sources ) {

		// Bail if Plugin isn't active.
		if ( ! $this->is_active() ) {
			return $sources;
		}

		$sources['fibu'] = __( 'Featured Image by URL', 'page-generator-pro' );

		return $sources;

	}

	/**
	 * Output FIBU settings in the Content Groups UI
	 *
	 * @since   2.9.3
	 *
	 * @param   WP_Post $post       WordPress Post.
	 * @param   array   $settings   Content Group Settings.
	 */
	public function output_meta_box_featured_image( $post, $settings ) { // phpcs:ignore

		// Bail if Plugin isn't active.
		if ( ! $this->is_active() ) {
			return;
		}

		// Load view.
		include $this->base->plugin->folder . 'views/admin/generate-meta-box-featured-image-fibu.php';

	}

	/**
	 * Defines the Featured Image for the given generated Post ID, if
	 * the Group Settings specify a Featured Image and (if overwriting)
	 * the Featured Image should be overwritten
	 *
	 * @since   2.9.3
	 *
	 * @param   mixed $image_id   Image ID.
	 * @param   int   $post_id    Generated Post ID.
	 * @param   int   $group_id   Group ID.
	 * @param   int   $index      Generation Index.
	 * @param   array $settings   Group Settings.
	 * @param   array $post_args  wp_insert_post() / wp_update_post() arguments.
	 * @return  mixed               WP_Error | false | Image ID
	 */
	public function copy_featured_image_data( $image_id, $post_id, $group_id, $index, $settings, $post_args ) { // phpcs:ignore

		// Update Post Meta on Generated Page.
		update_post_meta( $post_id, '_knawatfibu_url', $settings['featured_image_fibu_url'] );
		update_post_meta( $post_id, '_knawatfibu_alt', $settings['featured_image_fibu_alt'] );

		// Don't return an image ID, as FIBU will use the Post Meta on the Generated Page to output the Featured Image.
		return false;

	}

}
