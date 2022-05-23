<?php
/**
 * BeTheme / Muffin Page Builder Integration Class
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Registers BeTheme's Muffin Page Builder fields as a Plugin integration:
 * - Register fields on Content Groups
 *
 * @package Page_Generator_Pro
 * @author  WP Zinc
 * @version 2.1.2
 */
class Mfn_Post_Type_Page_Generator_Pro extends Mfn_Post_Type {

	/**
	 * Constructor
	 *
	 * @since   2.1.2
	 */
	public function __construct() {

		parent::__construct();

		if ( is_admin() ) {
			$this->fields  = $this->set_fields();
			$this->builder = new Mfn_Builder_Admin();
		}

	}

	/**
	 * Set post type fields
	 *
	 * @since   2.1.2
	 */
	private function set_fields() {

		return array(
			'id'       => 'mfn-meta-page-generator-pro',
			'title'    => esc_html__( 'Page Options', 'page-generator-pro' ),
			'page'     => 'page-generator-pro',
			'context'  => 'normal',
			'priority' => 'default',
			'fields'   => array(

				// layout.
				array(
					'id'    => 'mfn-meta-info-layout',
					'type'  => 'info',
					'title' => '',
					'desc'  => __( 'Layout', 'page-generator-pro' ),
					'class' => 'mfn-info',
				),

				array(
					'id'       => 'mfn-post-hide-content',
					'type'     => 'switch',
					'title'    => __( 'Hide The Content', 'page-generator-pro' ),
					'sub_desc' => __( 'Hide the content from the WordPress editor', 'page-generator-pro' ),
					'desc'     => __( '<strong>Turn it ON if you build content using Content Builder</strong><br />Use the Content item if you want to display the Content from editor within the Content Builder', 'page-generator-pro' ),
					'options'  => array(
						'1' => 'On',
						'0' => 'Off',
					),
					'std'      => '0',
				),

				array(
					'id'      => 'mfn-post-layout',
					'type'    => 'radio_img',
					'title'   => __( 'Layout', 'page-generator-pro' ),
					'desc'    => __( '<b>Full width</b> sections works only <b>without</b> sidebars', 'page-generator-pro' ),
					'options' => array(
						'no-sidebar'    => array(
							'title' => 'Full width No sidebar',
							'img'   => MFN_OPTIONS_URI . 'img/1col.png',
						),
						'left-sidebar'  => array(
							'title' => 'Left Sidebar',
							'img'   => MFN_OPTIONS_URI . 'img/2cl.png',
						),
						'right-sidebar' => array(
							'title' => 'Right Sidebar',
							'img'   => MFN_OPTIONS_URI . 'img/2cr.png',
						),
						'both-sidebars' => array(
							'title' => 'Both Sidebars',
							'img'   => MFN_OPTIONS_URI . 'img/2sb.png',
						),
					),
					'std'     => mfn_opts_get( 'sidebar-layout' ),
				),

				array(
					'id'      => 'mfn-post-sidebar',
					'type'    => 'select',
					'title'   => __( 'Sidebar', 'page-generator-pro' ),
					'desc'    => __( 'Shows only if layout with sidebar is selected', 'page-generator-pro' ),
					'options' => mfn_opts_get( 'sidebars' ),
				),

				array(
					'id'      => 'mfn-post-sidebar2',
					'type'    => 'select',
					'title'   => __( 'Sidebar 2nd', 'page-generator-pro' ),
					'desc'    => __( 'Shows only if layout with both sidebars is selected', 'page-generator-pro' ),
					'options' => mfn_opts_get( 'sidebars' ),
				),

				// media.

				array(
					'id'    => 'mfn-meta-info-media',
					'type'  => 'info',
					'title' => '',
					'desc'  => __( 'Media', 'page-generator-pro' ),
					'class' => 'mfn-info',
				),

				array(
					'id'      => 'mfn-post-slider',
					'type'    => 'select',
					'title'   => __( 'Slider | Revolution Slider', 'page-generator-pro' ),
					'desc'    => __( 'Select one from the list of available <a target="_blank" href="admin.php?page=revslider">Revolution Sliders</a>', 'page-generator-pro' ),
					'options' => Mfn_Builder_Helper::get_sliders( 'rev' ),
				),

				array(
					'id'      => 'mfn-post-slider-layer',
					'type'    => 'select',
					'title'   => __( 'Slider | Layer Slider', 'page-generator-pro' ),
					'desc'    => __( 'Select one from the list of available <a target="_blank" href="admin.php?page=layerslider">Layer Sliders</a>', 'page-generator-pro' ),
					'options' => Mfn_Builder_Helper::get_sliders( 'layer' ),
				),

				array(
					'id'    => 'mfn-post-slider-shortcode',
					'type'  => 'text',
					'title' => __( 'Slider | Shortcode', 'page-generator-pro' ),
					'desc'  => __( 'Paste your slider shortcode here if you use slider other than Revolution or Layer', 'page-generator-pro' ),
				),

				array(
					'id'    => 'mfn-post-subheader-image',
					'type'  => 'upload',
					'title' => __( 'Subheader | Image', 'page-generator-pro' ),
				),

				// options.

				array(
					'id'    => 'mfn-meta-info-options',
					'type'  => 'info',
					'title' => '',
					'desc'  => __( 'Options', 'page-generator-pro' ),
					'class' => 'mfn-info',
				),

				array(
					'id'      => 'mfn-post-one-page',
					'type'    => 'switch',
					'title'   => __( 'One Page', 'page-generator-pro' ),
					'options' => array(
						'0' => 'Off',
						'1' => 'On',
					),
					'std'     => '0',
				),

				array(
					'id'      => 'mfn-post-hide-title',
					'type'    => 'switch',
					'title'   => __( 'Subheader | Hide', 'page-generator-pro' ),
					'options' => array(
						'1' => 'On',
						'0' => 'Off',
					),
					'std'     => '0',
				),

				array(
					'id'      => 'mfn-post-remove-padding',
					'type'    => 'switch',
					'title'   => __( 'Content | Remove Padding', 'page-generator-pro' ),
					'desc'    => __( 'Remove default Content Padding', 'page-generator-pro' ),
					'options' => array(
						'1' => 'On',
						'0' => 'Off',
					),
					'std'     => '0',
				),

				array(
					'id'      => 'mfn-post-custom-layout',
					'type'    => 'select',
					'title'   => __( 'Custom | Layout', 'page-generator-pro' ),
					'desc'    => __( 'Custom Layout overwrites Theme Options', 'page-generator-pro' ),
					'options' => $this->get_layouts(),
				),

				array(
					'id'      => 'mfn-post-menu',
					'type'    => 'select',
					'title'   => __( 'Custom | Menu', 'page-generator-pro' ),
					'desc'    => __( 'Do <b>not</b> work with Split Menu', 'page-generator-pro' ),
					'options' => $this->get_menus(),
				),

				// seo.

				array(
					'id'    => 'mfn-meta-info-seo',
					'type'  => 'info',
					'title' => '',
					'desc'  => __( 'SEO <span>below settings overriddes theme options</span>', 'page-generator-pro' ),
					'class' => 'mfn-info',
				),

				array(
					'id'    => 'mfn-meta-seo-title',
					'type'  => 'text',
					'title' => __( 'SEO | Title', 'page-generator-pro' ),
				),

				array(
					'id'    => 'mfn-meta-seo-description',
					'type'  => 'text',
					'title' => __( 'SEO | Description', 'page-generator-pro' ),
				),

				array(
					'id'    => 'mfn-meta-seo-keywords',
					'type'  => 'text',
					'title' => __( 'SEO | Keywords', 'page-generator-pro' ),
				),

				array(
					'id'       => 'mfn-meta-seo-og-image',
					'type'     => 'upload',
					'title'    => __( 'Open Graph | Image', 'page-generator-pro' ),
					'sub_desc' => __( 'e.g. Facebook share image', 'page-generator-pro' ),
				),

				// custom.

				array(
					'id'    => 'mfn-meta-info-custom',
					'type'  => 'info',
					'title' => '',
					'desc'  => __( 'Custom CSS', 'page-generator-pro' ),
					'class' => 'mfn-info',
				),

				array(
					'id'    => 'mfn-post-css',
					'type'  => 'textarea',
					'title' => __( 'Custom | CSS', 'page-generator-pro' ),
					'desc'  => __( 'Paste your custom CSS code for this page', 'page-generator-pro' ),
					'class' => 'full-width',
				),
			),
		);

	}

}
