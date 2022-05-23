/**
 * Registers Dynamic Elements / Shortcodes as Quicktags for the TinyMCE Text Editor
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Registers Dynamic Elements / Shortcodes as Quicktags for the TinyMCE Text Editor
 *
 * @since 	3.0.0
 *
 * @param 	array 	shortcodes 	Shortcodes.
 */
function pageGeneratorProQuickTagsRegister( shortcodes ) {

	( function( $ ) {

		for ( const shortcode in shortcodes ) {

			QTags.addButton(
				'page_generator_pro_' + shortcode,
				shortcodes[ shortcode ].title,
				function() {

					// Perform an AJAX call to load the modal's view.
					$.post(
						ajaxurl,
						{
							'action': 	'page_generator_pro_output_tinymce_modal',
							'nonce':  	page_generator_pro_tinymce.nonces.tinymce,
							'shortcode':shortcode
						},
						function( response ) {

							// Show Modal.
							wpZincQuickTagsModal.open();

							// Resize Modal so it's not full screen.
							$( 'div.wpzinc-quicktags-modal div.media-modal.wp-core-ui' ).css(
								{
									width: ( shortcodes[ shortcode ].modal.width ) + 'px',
									height: ( shortcodes[ shortcode ].modal.height + 20 ) + 'px' // Prevents a vertical scroll bar.
								}
							);

							// Set Title.
							$( '#wpzinc-quicktags-modal .media-frame-title h1' ).text( shortcodes[ shortcode ].title );

							// Inject HTML into modal.
							$( '#wpzinc-quicktags-modal .media-frame-content' ).html( response );

							// Resize HTML height so it fills the modal.
							$( 'div.wpzinc-quicktags-modal div.media-modal.wp-core-ui div.wpzinc-vertical-tabbed-ui' ).css(
								{
									height: ( shortcodes[ shortcode ].modal.height - 50 ) + 'px' // -50px is for the footer buttons.
								}
							);

							// Initialize tabbed interface.
							wp_zinc_tabs_init();

							// Initialize selectize instances.
							page_generator_pro_reinit_selectize();

							// Initialize autocomplete instances.
							wp_zinc_autocomplete_initialize();

							// Initialize conditional fields.
							page_generator_pro_conditional_fields_initialize();
							$( 'select.wpzinc-conditional, .wpzinc-conditional select' ).trigger( 'change' );

							// Trigger the change event stored in generate-content.js.
							$( 'form.wpzinc-tinymce-popup select[name="maptype"]' ).trigger( 'change.page-generator-pro' );
						}
					);

				}
			);

		}

	} )( jQuery );

}

pageGeneratorProQuickTagsRegister( page_generator_pro_quicktags );
