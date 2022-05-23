<?php
/**
 * Editor Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Registers Dynamic Elements as TinyMCE Plugins.
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 1.0.0
 */
class Page_Generator_Pro_Editor {

	/**
	 * Holds the base object.
	 *
	 * @since 1.2.1
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Holds the screen and section the user is viewing
	 *
	 * @since   2.6.2
	 *
	 * @var     array
	 */
	public $screen = array(
		'screen'  => false,
		'section' => false,
	);

	/**
	 * Holds the shortcodes to register as TinyMCE Plugins
	 *
	 * @since   2.6.2
	 *
	 * @var     array
	 */
	public $shortcodes = array();

	/**
	 * Constructor
	 *
	 * @since   1.0.0
	 *
	 * @param   object $base    Base Plugin Class.
	 */
	public function __construct( $base ) {

		// Store base class.
		$this->base = $base;

		// Add filters to register QuickTag Plugins.
		add_action( 'admin_enqueue_scripts', array( $this, 'register_quicktags' ) ); // WordPress Admin.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_quicktags' ) ); // Frontend Editors.

		// Add filters to register TinyMCE Plugins.
		// Low priority ensures this works with Frontend Page Builders.
		add_filter( 'mce_external_plugins', array( $this, 'register_tinymce_plugins' ), 99999 );
		add_filter( 'mce_buttons', array( $this, 'register_tinymce_buttons' ), 99999 );

	}

	/**
	 * Register QuickTags JS for the TinyMCE Text (non-Visual) Editor
	 *
	 * @since   3.0.0
	 */
	public function register_quicktags() {

		// Determine the screen that we're on.
		$screen = $this->base->get_class( 'screen' )->get_current_screen();

		// Bail if we're not registering TinyMCE Plugins.
		if ( ! $this->should_register_tinymce_plugins( $screen ) ) {
			return;
		}

		// Determine whether to load minified versions of JS.
		$minified = $this->base->dashboard->should_load_minified_js();

		// Register nonces.
		$nonces = array(
			'tinymce' => wp_create_nonce( 'page_generator_pro_tinymce' ),
		);

		// Depending on the screen we're on, define the shortcodes to register as TinyMCE Plugins.
		switch ( $screen['screen'] ) {
			case 'post':
				$shortcodes = $this->base->get_class( 'shortcode' )->get_shortcode_supported_outside_of_content_groups();
				break;

			case 'content_groups':
				// Get shortcodes.
				$shortcodes = $this->base->get_class( 'shortcode' )->get_shortcodes();

				// Get Plugins that aren't shortcodes, registering their JS scripts.
				foreach ( $this->get_tinymce_plugins( $minified ) as $plugin_name => $plugin_attributes ) {
					$plugins[ $plugin_name ] = $plugin_attributes['js'];

					// Register nonce in array if this plugin defines one.
					if ( array_key_exists( 'nonce', $plugin_attributes ) ) {
						$nonces[ $plugin_name ] = $plugin_attributes['nonce'];
					}
				}
				break;

			case 'content_terms':
				// Get Plugins that aren't shortcodes, registering their JS scripts.
				foreach ( $this->get_tinymce_plugins( $minified ) as $plugin_name => $plugin_attributes ) {
					$plugins[ $plugin_name ] = $plugin_attributes['js'];

					// Register nonce in array if this plugin defines one.
					if ( array_key_exists( 'nonce', $plugin_attributes ) ) {
						$nonces[ $plugin_name ] = $plugin_attributes['nonce'];
					}
				}
				break;
		}

		// If no shortcodes require registration, bail.
		if ( ! isset( $shortcodes ) ) {
			return;
		}

		// Enqueue Quicktag JS.
		wp_enqueue_script( $this->base->plugin->name . '-quicktags', $this->base->plugin->url . 'assets/js/' . ( $minified ? 'min/' : '' ) . 'quicktags' . ( $minified ? '-min' : '' ) . '.js', array( 'jquery', 'quicktags' ), $this->base->plugin->version, true );
		wp_localize_script( $this->base->plugin->name . '-quicktags', 'page_generator_pro_quicktags', $shortcodes );

		// Register JS variable with nonces for AJAX calls.
		wp_localize_script(
			$this->base->plugin->name . '-quicktags',
			'page_generator_pro_tinymce',
			array(
				'nonces' => $nonces,
			)
		);

		// Output Backbone View Template.
		add_action( 'wp_print_footer_scripts', array( $this, 'output_quicktags_modal' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'output_quicktags_modal' ) );

	}

	/**
	 * Register JS plugins for the TinyMCE Editor
	 *
	 * @since   1.0.0
	 *
	 * @param   array $plugins    JS Plugins.
	 * @return  array               JS Plugins
	 */
	public function register_tinymce_plugins( $plugins ) {

		// Determine the screen that we're on.
		$screen = $this->base->get_class( 'screen' )->get_current_screen();

		// Bail if we're not registering TinyMCE Plugins.
		if ( ! $this->should_register_tinymce_plugins( $screen ) ) {
			return $plugins;
		}

		// Determine whether to load minified versions of JS.
		$minified = $this->base->dashboard->should_load_minified_js();

		// Register nonces.
		$nonces = array(
			'tinymce' => wp_create_nonce( 'page_generator_pro_tinymce' ),
		);

		// Depending on the screen we're on, define the shortcodes to register as TinyMCE Plugins.
		switch ( $screen['screen'] ) {
			case 'post':
				$shortcodes = $this->base->get_class( 'shortcode' )->get_shortcode_supported_outside_of_content_groups();
				break;

			case 'content_groups':
				// Get shortcodes.
				$shortcodes = $this->base->get_class( 'shortcode' )->get_shortcodes();

				// Get Plugins that aren't shortcodes, registering their JS scripts.
				foreach ( $this->get_tinymce_plugins( $minified ) as $plugin_name => $plugin_attributes ) {
					$plugins[ $plugin_name ] = $plugin_attributes['js'];

					// Register nonce in array if this plugin defines one.
					if ( array_key_exists( 'nonce', $plugin_attributes ) ) {
						$nonces[ $plugin_name ] = $plugin_attributes['nonce'];
					}
				}
				break;

			case 'content_terms':
				// Get Plugins that aren't shortcodes, registering their JS scripts.
				foreach ( $this->get_tinymce_plugins( $minified ) as $plugin_name => $plugin_attributes ) {
					$plugins[ $plugin_name ] = $plugin_attributes['js'];

					// Register nonce in array if this plugin defines one.
					if ( array_key_exists( 'nonce', $plugin_attributes ) ) {
						$nonces[ $plugin_name ] = $plugin_attributes['nonce'];
					}
				}
				break;
		}

		// If no shortcodes require registration, bail.
		if ( ! isset( $shortcodes ) ) {
			return $plugins;
		}

		// Enqueue TinyMCE JS.
		wp_enqueue_script( $this->base->plugin->name . '-tinymce', $this->base->plugin->url . 'assets/js/' . ( $minified ? 'min/' : '' ) . 'tinymce' . ( $minified ? '-min' : '' ) . '.js', array( 'jquery' ), $this->base->plugin->version, true );

		// Register JS variable with nonces for AJAX calls.
		wp_localize_script(
			$this->base->plugin->name . '-tinymce',
			'page_generator_pro_tinymce',
			array(
				'nonces' => $nonces,
			)
		);
		wp_localize_script(
			$this->base->plugin->name . '-quicktags',
			'page_generator_pro_tinymce',
			array(
				'nonces' => $nonces,
			)
		);

		// Make shortcodes available as page_generator_pro_shortcodes JS variable.
		wp_localize_script( $this->base->plugin->name . '-tinymce', 'page_generator_pro_shortcodes', $shortcodes );

		// Register TinyMCE Plugins.
		foreach ( $shortcodes as $shortcode => $properties ) {
			$plugins[ 'page_generator_pro_' . str_replace( '-', '_', $shortcode ) ] = $this->base->plugin->url . 'assets/js/' . ( $minified ? 'min/' : '' ) . 'tinymce-' . $shortcode . ( $minified ? '-min' : '' ) . '.js';
		}

		/**
		 * Defines the TinyMCE Plugins to register
		 *
		 * @since   1.0.0
		 *
		 * @param   array   $plugins    TinyMCE Plugins.
		 * @param   array   $screen     Screen and Section.
		 * @param   array   $shortcodes Shortcodes.
		 */
		$plugins = apply_filters( 'page_generator_pro_editor_register_tinymce_plugins', $plugins, $screen, $shortcodes );

		// Return filtered results.
		return $plugins;

	}

	/**
	 * Registers buttons in the TinyMCE Editor
	 *
	 * @since   1.0.0
	 *
	 * @param   array $buttons    Buttons.
	 * @return  array               Buttons
	 */
	public function register_tinymce_buttons( $buttons ) {

		// Determine the screen that we're on.
		$screen = $this->base->get_class( 'screen' )->get_current_screen();

		// Bail if we're not registering TinyMCE Plugins.
		if ( ! $this->should_register_tinymce_plugins( $screen ) ) {
			return $buttons;
		}

		// Deliniate buttons before we add this Plugin's buttons.
		$buttons[] = '|';

		// Depending on the screen we're on, define the shortcodes to register as TinyMCE Plugins.
		switch ( $screen['screen'] ) {
			case 'post':
				$shortcodes = $this->base->get_class( 'shortcode' )->get_shortcode_supported_outside_of_content_groups();
				break;

			case 'content_groups':
			case 'content_terms':
				$shortcodes = $this->base->get_class( 'shortcode' )->get_shortcodes();

				// Get Plugins that aren't shortcodes, registering their buttons.
				foreach ( $this->get_tinymce_plugins() as $plugin_name => $plugin_attributes ) {
					// Don't register a button if this TinyMCE Plugin doesn't have one.
					if ( ! isset( $plugin_attributes['has_button'] ) ) {
						continue;
					}
					if ( ! $plugin_attributes['has_button'] ) {
						continue;
					}

					// Add Button.
					$buttons[] = $plugin_name;
				}
				break;
		}

		// Register TinyMCE Buttons.
		foreach ( $shortcodes as $shortcode => $properties ) {
			$buttons[] = 'page_generator_pro_' . str_replace( '-', '_', $shortcode );
		}

		/**
		 * Defines the TinyMCE Buttons to register
		 *
		 * @since   1.0.0
		 *
		 * @param   array   $plugins    TinyMCE Plugins.
		 * @param   array   $screen     Screen and Section.
		 * @param   array   $shortcodes Shortcodes.
		 */
		$buttons = apply_filters( 'page_generator_pro_editor_register_tinymce_buttons', $buttons, $screen, $shortcodes );

		// Return filtered results.
		return $buttons;

	}

	/**
	 * Outputs the backbone modal in the footer of the site, which is used by QuickTags.
	 *
	 * @since   3.6.9
	 */
	public function output_quicktags_modal() {

		?>
		<script type="text/template" id="tmpl-wpzinc-quicktags-modal">
			<div id="wpzinc-quicktags-modal">
				<div class="media-frame-title"><h1></h1></div>
				<div class="media-frame-content"></div>
				<div class="media-frame-toolbar">
					<div class="media-toolbar">
						<div class="media-toolbar-secondary">
							<button type="button" class="button button-large cancel"><?php esc_html_e( 'Cancel', 'page-generator-pro' ); ?></button>
						</div>
						<div class="media-toolbar-primary">
							<button type="button" class="button button-primary button-large insert"><?php esc_html_e( 'Insert', 'page-generator-pro' ); ?></button>
						</div>
					</div>
				</div>
			</div>
		</script>
		<?php

	}

	/**
	 * Returns an array of TinyMCE Plugins that aren't shortcodes/blocks,
	 * such as Autocomplete and Generate Spintax from Selected Text
	 *
	 * @since   2.8.9
	 *
	 * @param   bool $minified           Whether to load minified versions.
	 * @return  array                       TinyMCE Plugins
	 */
	private function get_tinymce_plugins( $minified = true ) {

		// Define Plugins.
		$plugins = array(
			'page_generator_pro_autocomplete_keywords' => array(
				'js'         => $this->base->plugin->url . '_modules/dashboard/js/' . ( $minified ? 'min/' : '' ) . 'autocomplete-tinymce' . ( $minified ? '-min' : '' ) . '.js',
				'has_button' => false,
			),
			'page_generator_pro_research'              => array(
				'js'         => $this->base->plugin->url . 'assets/js/' . ( $minified ? 'min/' : '' ) . 'tinymce-research' . ( $minified ? '-min' : '' ) . '.js',
				'has_button' => true,
				'nonce'      => wp_create_nonce( 'page-generator-pro-research' ),
			),
			'page_generator_pro_spintax_generate'      => array(
				'js'         => $this->base->plugin->url . 'assets/js/' . ( $minified ? 'min/' : '' ) . 'tinymce-spintax-generate' . ( $minified ? '-min' : '' ) . '.js',
				'has_button' => true,
				'nonce'      => wp_create_nonce( 'page-generator-pro-spintax-generate' ),
			),
		);

		/**
		 * Returns an array of TinyMCE Plugins that aren't shortcodes/blocks,
		 * such as Autocomplete and Generate Spintax from Selected Text
		 *
		 * @since   2.8.9
		 *
		 * @param  array   $plugins     TinyMCE Plugins
		 */
		$plugins = apply_filters( 'page_generator_pro_editor_get_tinymce_plugins', $plugins );

		// Return.
		return $plugins;

	}

	/**
	 * Determines whether TinyMCE Plugins should be registered, by checking if the
	 * user is editing a Content Group in the WordPress Admin or a Frontend Page Builder
	 *
	 * @since   2.5.7
	 *
	 * @param   array $screen     Screen and Section.
	 * @return  mixed               false
	 */
	private function should_register_tinymce_plugins( $screen ) {

		// Set a flag to denote whether we should register TinyMCE Plugins.
		$should_register_tinymce_plugins = false;

		// Depending on the screen we're on, define the Plugins that we should register.
		if ( $screen['screen'] === 'post' && $screen['section'] === 'edit' ) {
			// Only register Shortcodes where register_on_generation_only = false.
			$should_register_tinymce_plugins = true;
		} elseif ( $screen['screen'] === 'content_groups' && $screen['section'] === 'edit' ) {
			// Register all Shortcodes.
			$should_register_tinymce_plugins = true;
		} elseif ( $screen['screen'] === 'content_terms' && $screen['section'] === 'edit' ) {
			// Register all Shortcodes.
			$should_register_tinymce_plugins = true;
		}

		/**
		 * Set a flag to denote whether we should register TinyMCE Plugins
		 *
		 * @since   2.2.4
		 *
		 * @param   bool   $should_register_tinymce_plugins    Should Register TinyMCE Plugins.
		 */
		$should_register_tinymce_plugins = apply_filters( 'page_generator_pro_editor_should_register_tinymce_plugins', $should_register_tinymce_plugins );

		// Return.
		return $should_register_tinymce_plugins;

	}

}
