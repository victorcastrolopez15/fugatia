/**
 * Registers the TinyMCE Research Button.
 *
 * @since   2.8.9
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

var page_generator_pro_research_timeout;

/**
 * Generates content from a research provider for a given topic, inserting it
 * into a TinyMCE instance
 *
 * @since 	2.8.9
 */
( function() {

	tinymce.PluginManager.add(
		'page_generator_pro_research',
		function( editor, url ) {

			// Add Button to Visual Editor Toolbar.
			editor.addButton(
				'page_generator_pro_research',
				{
					title: 	'Research Content for a given Topic',
					image: 	url + '../../../../_modules/dashboard/feather/book.svg',
					cmd: 	'page_generator_pro_research',
				}
			);

			// Load View when button clicked.
			editor.addCommand(
				'page_generator_pro_research',
				function() {

					// Get topic.
					var topic = prompt( 'Enter the topic to research content for e.g. plumbing' );
					if ( topic === null ) {
						return;
					}

					// Show overlay and progress.
					wpzinc_modal_open( 'Researching...' );

					// Perform an AJAX call to load the modal's view.
					jQuery.post(
						ajaxurl,
						{
							'action': 'page_generator_pro_research',
							'nonce': page_generator_pro_tinymce.nonces.page_generator_pro_research,
							'topic': topic
						},
						function( response ) {

							// Bail if an error occured.
							if ( ! response.success ) {
								// Show error message.
								return wpzinc_modal_show_error_message( response.data );
							}

							// If completed, insert content into TinyMCE and exit now.
							// Some research tools, such as AI Writer, won't immediately return content.
							if ( response.data.completed ) {
								// Replace selected content and return.
								tinyMCE.activeEditor.selection.setContent( '<p>' + response.data.content.join( '</p><p>' ) + '</p>' );
								return wpzinc_modal_show_success_and_exit( 'Done!', '' );
							}

							// Update modal's message and wait for the next AJAX call.
							wpzinc_modal_update_message( response.data.message );

							// Update modal's message and wait for the next AJAX call.
							wpzinc_modal_update_message( response.data.message );

							// Get status, which will run at an interval.
							page_generator_pro_research_get_status( response.data.id, response.data.estimated_time );
						}
					);

				}
			);
		}
	);

} )();

/**
 * Polls the research request for every 1/2 of the given estimated_time
 * to determine if the research completed.
 *
 * @since 	2.8.9
 *
 * @param 	string 	id 				ID.
 * @param 	float 	estimated_time 	Estimated Time for research to complete.
 */
function page_generator_pro_research_get_status( id, estimated_time ) {

	// Wait the estimated time before sending a request to check on the research status.
	page_generator_pro_research_timeout = setInterval(
		function() {
			jQuery.ajax(
				{
					type: 'POST',
					url: ajaxurl,
					data: {
						'action': 	'page_generator_pro_research_get_status',
						'id': 		id,
						'nonce': 	page_generator_pro_tinymce.nonces.page_generator_pro_research,
					},
					success: function( response ) {
						// Bail if an error occured.
						if ( ! response.success ) {
							// Show error message.
							clearTimeout( page_generator_pro_research_timeout );
							return wpzinc_modal_show_error_message( response.data );
						}

						// If completed, insert content into TinyMCE and exit.
						if ( response.data.completed ) {
							// Replace selected content and return.
							clearTimeout( page_generator_pro_research_timeout );
							tinyMCE.activeEditor.selection.setContent( '<p>' + response.data.content.join( '</p><p>' ) + '</p>' );
							return wpzinc_modal_show_success_and_exit( 'Done!', '' );
						}

						// Update modal's message and wait for the next AJAX call.
						wpzinc_modal_update_message( response.data.message );
					}
				}
			);
		},
		10000 // Check every 10 seconds.
	);

}
