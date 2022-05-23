/**
 * Handles registration of TinyMCE buttons.
 *
 * @since   3.6.2
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

/**
 * Registers the given block as a TinyMCE Plugin, with a button in
 * the Visual Editor toolbar.
 *
 * @since 	3.6.2
 *
 * @param 	object 	block 	Block
 */
function pageGeneratorProTinyMCERegisterPlugin( block ) {

	( function( $ ) {

		tinymce.PluginManager.add(
			'page_generator_pro_' + block.name.replaceAll( '-', '_' ),
			function( editor, url ) {

				// Add Button to Visual Editor Toolbar.
				editor.addButton(
					'page_generator_pro_' + block.name.replaceAll( '-', '_' ),
					{
						title: 	block.title,
						image: 	block.icon,
						cmd: 	'page_generator_pro_' + block.name.replaceAll( '-', '_' ),
					}
				);

				// Load View when button clicked.
				editor.addCommand(
					'page_generator_pro_' + block.name.replaceAll( '-', '_' ),
					function() {

						// Open the TinyMCE Modal.
						editor.windowManager.open(
							{
								id: 	'wpzinc-tinymce-modal',
								title: 	block.title,
								width: 	block.modal.width,
								height: block.modal.height,

								// See dashboard submodule's tinymce-modal.js which handles
								// insert and cancel button clicks.
								buttons: [
									{
										text: 'Cancel',
										classes: 'cancel'
								},
									{
										text: 'Insert',
										subtype: 'primary',
										classes: 'insert'
								}
								]
							}
						);

						// Perform an AJAX call to load the modal's view.
						$.post(
							ajaxurl,
							{
								'action': 		'page_generator_pro_output_tinymce_modal',
								'nonce':  		page_generator_pro_tinymce.nonces.tinymce,
								'shortcode': 	block.name
							},
							function( response ) {

								// Inject HTML into modal.
								jQuery( '#wpzinc-tinymce-modal-body' ).html( response );

								// Initialize tabbed interface.
								wp_zinc_tabs_init();

								// Initialize selectize instances.
								page_generator_pro_reinit_selectize();

								// Initialize autocomplete instances.
								wp_zinc_autocomplete_initialize();

								// Initialize conditional fields.
								page_generator_pro_conditional_fields_initialize();
								jQuery( 'select.wpzinc-conditional, .wpzinc-conditional select' ).trigger( 'change' );

							}
						);

					}
				);

			}
		);

	} )( jQuery );

}
