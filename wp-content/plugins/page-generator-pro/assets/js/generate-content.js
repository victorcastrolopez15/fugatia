/**
 * Generate Content
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

jQuery( document ).ready(
	function( $ ) {

		/**
		 * Content Groups: Row Actions
		 * Content Groups: Actions Meta Box
		 */
		$( 'body.page-generator-pro td.has-row-actions span a, body.page-generator-pro td.generated_count span a, body.page-generator-pro td.status span a, #page-generator-pro-actions span a, #page-generator-pro-actions-bottom span a, #page-generator-pro-actions-gutenberg-bottom span a' ).click(
			function( e ) {

				var action = $( this ).parent( 'span' ).attr( 'class' ),
				result     = true,
				group_id   = page_generator_pro_get_url_param( $( this ).attr( 'href' ), 'id' ),
				type       = page_generator_pro_get_url_param( $( this ).attr( 'href' ), 'type' );

				// Check if a confirmation message exists for display.
				var confirmation_message = page_generator_pro_generate_content.messages[ action + '_confirm' ];

				// Let the request through if we're not asking for a confirmation.
				if ( typeof confirmation_message === 'undefined' ) {
					return true;
				}

				// Perform string replacements.
				confirmation_message = confirmation_message.replace( '%s', $( this ).data( 'total' ) );

				// Show confirmation dialog.
				result = confirm( confirmation_message );

				// If the user cancels, bail.
				if ( ! result ) {
					e.preventDefault();
					return false;
				}

				// Depending on the action, either use AJAX or let the request go through.
				switch ( action ) {

					/**
					 * Test
					 */
					case 'test':
						// AJAX.
						e.preventDefault();
						page_generator_pro_generate_content_test( group_id, type );
						break;

					/**
					 * Generate
					 */
					case 'generate':
						// Allow the request to go through.
						break;

					/**
					 * Generate via Server
					 */
					case 'generate_server':
						// Allow the request to go through.
						break;

					/**
					 * Cancel Generation
					 */
					case 'cancel_generation':
						// Allow the request to go through.
						break;

					/**
					 * Trash
					 */
					case 'trash_generated_content':
						// AJAX.
						e.preventDefault();
						page_generator_pro_generate_content_trash_generated_content( group_id, type, 0, $( this ).data( 'limit' ), $( this ).data( 'total' ) );
						break;

					/**
					 * Delete
					 */
					case 'delete_generated_content':
						// AJAX.
						e.preventDefault();
						page_generator_pro_generate_content_delete_generated_content( group_id, type, 0, $( this ).data( 'limit' ), $( this ).data( 'total' ) );
						break;

				}

			}
		);

		/**
		 * Content Groups: Row Actions: Close
		 * Content Groups: Actions Meta Box: Gutenberg: Close
		 */
		$( 'body' ).on(
			'click',
			'#page-generator-pro-progress button.close',
			function( e ) {

				e.preventDefault();

				page_generator_pro_hide_overlay_and_progress();

				return false;

			}
		);

		/**
		 * Repeater Rows: Reinitialize autocomplete
		 */
		$( 'body' ).on(
			'click',
			'.wpzinc-add-table-row',
			function( e ) {

				e.preventDefault();

				setTimeout(
					function() {
						wp_zinc_autocomplete_initialize();
					},
					500
				);

			}
		);

		/**
		 * Repeater Rows: Make sortable
		 * - Generate: Custom Fields: Sort
		 */
		if ( $( '.is-sortable' ).length > 0) {
			$( '.is-sortable' ).each(
				function() {
					$( this ).sortable();
				}
			);
		}

		/**
		 * Generate: Deselect All Taxonomy Terms
		 */
		$( document ).on(
			'click',
			'a.deselect-all',
			function( e ) {

				e.preventDefault();

				$( 'input[type="checkbox"]', $( $( this ).data( 'list' ) ) ).prop( 'checked', false );

			}
		);

		/**
		 * Generate: Show/hide metaboxes and metabox settings based on the chosen Post Type at Publish > Post Type
		 */
		$( 'select[name="page-generator-pro[type]"]' ).on(
			'change.page-generator-pro',
			function( e ) {

				// Get Post Type.
				var post_type = $( this ).val();

				// Show/Hide Metaboxes if any are conditionally displayed based on the post_type.
				for ( var metabox in page_generator_pro_generate_content.post_type_conditional_metaboxes ) {
					// Hide the Metabox.
					$( '#' + metabox ).hide();

					// Hide any conditional options / inputs within the metabox.
					$( '#' + metabox + ' div.wpzinc-option.post-type-conditional' ).hide();
					$( '#' + metabox + ' input.post-type-conditional' ).hide();

					// Show the Metabox if it belongs to the post_type.
					if ( page_generator_pro_generate_content.post_type_conditional_metaboxes[ metabox ].indexOf( post_type ) != -1 ) {
						$( '#' + metabox ).show();

						// Show any conditional options / inputs within the metabox.
						$( '#' + metabox + ' div.wpzinc-option.post-type-conditional.' + post_type ).show();
						$( '#' + metabox + ' input.post-type-conditional.' + post_type ).show();
					}
				}

			}
		);
		$( 'select[name="page-generator-pro[type]"]' ).trigger( 'change.page-generator-pro' );

		/**
		 * Generate: Status
		 */
		$( 'select[name="page-generator-pro[status]"], select[name="page-generator-pro[date_option]"]' ).on(
			'change.page-generator-pro',
			function() {

				var status  = $( 'select[name="page-generator-pro[status]"]' ).val(),
				date_option = $( 'select[name="page-generator-pro[date_option]"]' ).val();

				// Hide Status and Date options.
				$( 'div.specific, div.random, div.future' ).hide();

				// Don't show Schedule Increment when Status = Scheduled and Date = Random Date.
				if ( status == 'future' && date_option == 'random' ) {
					$( 'div.' + status ).hide();
				} else {
					$( 'div.' + status ).show();
				}

				// Show Date options for the selected Date.
				$( 'div.' + date_option ).show();

			}
		);
		$( 'select[name="page-generator-pro[status]"]' ).trigger( 'change.page-generator-pro' );

		/**
		 * Comments: Generate: Date
		 */
		$( 'select[name="page-generator-pro[comments_generate][date_option]"]' ).on(
			'change.page-generator-pro',
			function() {
				var date_option = $( this ).val();

				// Hide options.
				$( 'div.specific', $( this ).closest( '.postbox' ) ).hide();
				$( 'div.random', $( this ).closest( '.postbox' ) ).hide();

				// Show options matching the chosen date option.
				$( 'div.' + date_option, $( this ).closest( '.postbox' ) ).show();
			}
		);
		$( 'select[name="page-generator-pro[comments_generate][date_option]"]' ).trigger( 'change.page-generator-pro' );

		/**
		 * Generate: Overwrite
		 */
		$( 'select[name="page-generator-pro[overwrite]"]' ).on(
			'change.page-generator-pro',
			function() {
				var overwrite = $( this ).val();

				// Hide options.
				$( 'div.overwrite-sections' ).hide();

				// Show options matching the chosen post type.
				$( 'div.' + overwrite ).show();
			}
		);
		$( 'select[name="page-generator-pro[overwrite]"]' ).trigger( 'change.page-generator-pro' );

		/**
		 * Generate: Featured Image
		 */
		$( 'select[name="page-generator-pro[featured_image_source]"]' ).on(
			'change.page-generator-pro',
			function() {

				var source = $( this ).val();

				// Hide all Featured Image options.
				$( '.featured_image', $( this ).closest( '.postbox' ) ).hide();

				// Show Featured Image source options, if a source is selected.
				if ( source.length > 0 ) {
					$( '.featured_image.' + source, $( this ).closest( '.postbox' ) ).show();
				}

			}
		);
		$( 'select[name="page-generator-pro[featured_image_source]"]' ).trigger( 'change.page-generator-pro' );

		/**
		 * Generate Terms: Taxonomy Toggle
		 */
		$( 'select[name="tax"]' ).on(
			'change.page-generator-pro',
			function( e ) {

				var taxonomy = $( this ).val();

				// Show or hide the Parent Term depending on whether the chosen Taxonomy is hierarchical or not.
				if ( page_generator_pro_generate_content.taxonomy_is_hierarchical[ taxonomy ] === true ) {
					// Show.
					$( '.term-parent' ).show();
				} else {
					// Hide.
					$( '.term-parent' ).hide();
				}

			}
		);
		$( 'select[name="tax"]' ).trigger( 'change.page-generator-pro' );

		/**
		 * Generate via Browser: Submit
		 * Generate via Server: Submit
		 */
		$( 'body.post-type-page-generator-pro form input[type=submit], body.taxonomy-page-generator-tax form input[type=submit]' ).click(
			function( e ) {

				// Prevent WordPress from throwing a dialog warning that changes will be lost.
				$( window ).off( 'beforeunload.edit-post' );

				var action = $( this ).attr( 'name' ),
				result     = true;

				// Check if a confirmation message exists for display.
				var confirmation_message = page_generator_pro_generate_content.messages[ action + '_confirm' ];

				// Let the request through if we're not asking for a confirmation.
				if ( typeof confirmation_message === 'undefined' ) {
					return true;
				}

				// Show confirmation dialog.
				result = confirm( confirmation_message );

				if ( ! result ) {
					e.preventDefault();
					return false;
				}

			}
		);

	}
);

/**
 * Returns the value of the given URL parameter
 *
 * @since 	1.8.7
 *
 * @param 	string 	url 	URL.
 * @param 	string 	name 	Parameter Name.
 * @return 	string 			Parameter Value
 */
function page_generator_pro_get_url_param( url, name ) {

	name        = name.replace( /[\[]/, '\\[' ).replace( /[\]]/, '\\]' );
	var regex   = new RegExp( '[\\?&]' + name + '=([^&#]*)' );
	var results = regex.exec( url );
	return results === null ? '' : decodeURIComponent( results[1].replace( /\+/g, ' ' ) );

}

/**
 * Performs an asynchronous request to generate a Test Page
 * when the user clicks and confirms the Test Button when editing
 * a Content Group.
 *
 * @since 	1.8.4
 *
 * @param 	int 	group_id 	Group ID.
 * @param 	string 	type 		Type (content|term)
 */
function page_generator_pro_generate_content_test( group_id, type ) {

	// Show overlay and progress.
	wpzinc_modal_open(
		page_generator_pro_generate_content.titles.test,
		page_generator_pro_generate_content.messages.test
	);

	// Perform AJAX query.
	jQuery.ajax(
		{
			url: 		ajaxurl,
			type: 		'POST',
			async:    	true,
			data: 		{
				id: 		group_id,
				action: 	'page_generator_pro_generate_' + type,
				nonce:  	page_generator_pro_generate_content.nonces.generate_content,
				test_mode: 	true,
			},
			error: function( xhr, textStatus, errorThrown ) {

				// Show error message and exit.
				return wpzinc_modal_show_error_message( xhr.status + ' ' + xhr.statusText );

			},
			success: function( result ) {

				if ( ! result.success ) {
					// Show error message and exit.
					return wpzinc_modal_show_error_message( result.data );
				}

				// Build success message.
				message    = page_generator_pro_generate_content.messages.test_success.replace( '%s', '<a href="' + result.data.url + '" rel="noopener" target="_blank">' + result.data.url + '</a>' );
				var length = result.data.keywords_terms.length;
				for ( i = 0; i < length; i++ ) {
					message += '<br />{' + result.data.keywords_terms[ i ] + '}: ' + result.data.keywords_terms[ i ];
				}

				// Show success message and exit.
				return wpzinc_modal_show_success_message( message );

			}
		}
	);

}

/**
 * Performs an asynchronous request to Trash Generated Content
 * when the user clicks and confirms the Trash Generated Content Button
 * when editing a Content Group.
 *
 * @since 	1.8.4
 *
 * @param 	int 	group_id 		Group ID.
 * @param 	string 	type 			Type (content|term).
 * @param 	string 	request_count 	Request Count.
 * @param 	int 	limit 			Number of Items to Trash in this request.
 * @param 	int 	total 			Total Items to Trash.
 */
function page_generator_pro_generate_content_trash_generated_content( group_id, type, request_count, limit, total ) {

	// Define item start and end that will be trashed in this request.
	// These are used in the UI only, and not sent in the AJAX request.
	var start = ( Number( request_count ) * Number( limit ) ) + 1,
		end   = ( Number( request_count ) * Number( limit ) ) + Number( limit );

	// If the final item index exceeds the total number of items, set it to the total .
	if ( end > total ) {
		end = total;
	}

	// Show overlay and progress.
	wpzinc_modal_open(
		page_generator_pro_generate_content.titles.trash_generated_content,
		page_generator_pro_generate_content.messages.trash_generated_content + ' ' + start + ' to ' + end + ' of ' + total
	);

	// Perform AJAX query.
	jQuery.ajax(
		{
			url: 		ajaxurl,
			type: 		'POST',
			async:    	true,
			data: 		{
				id: 			group_id,
				action: 		'page_generator_pro_generate_' + type + '_trash_generated_' + type,
				nonce:  		page_generator_pro_generate_content.nonces.trash_generated_content
			},
			error: function( xhr, textStatus, errorThrown ) {

				// Show error message and exit.
				return wpzinc_modal_show_error_message( xhr.status + ' ' + xhr.statusText );

			},
			success: function( result ) {

				if ( ! result.success ) {
					// Show error message and exit.
					return wpzinc_modal_show_error_message( result.data );
				}

				// If there is a has_more flag, continue the requests.
				if ( result.data.has_more ) {
					// Run the next request.
					page_generator_pro_generate_content_trash_generated_content( group_id, type, ( request_count + 1 ), limit, total );
				} else {
					// No more requests to be made.

					// Determine if Trash and Delete are within a div.wpzinc-option.
					// If so, we're editing a Content Group and will need to delete div.wpzinc-option as it will be blank after
					// the buttons are removed.
					if ( jQuery( 'div.wpzinc-option div.full span.trash_generated_content' ).length > 0 ) {
						jQuery( 'span.trash_generated_content' ).closest( 'div.wpzinc-option' ).remove();
					} else {
						// Hide View, Trash and Delete Links in WP_List_Table.
						jQuery( 'span.generated_count span.count[data-group-id="' + group_id + '"]' ).text( '0' );
						jQuery( 'span.view a[data-group-id="' + group_id + '"]' ).remove();
						jQuery( 'span.trash_generated_content a[data-group-id="' + group_id + '"]' ).remove();
						jQuery( 'span.delete_generated_content a[data-group-id="' + group_id + '"]' ).remove();

						// Reset Generated Items Count.
						jQuery( 'span.view a[data-group-id="' + group_id + '"]' ).remove();
					}

					// Show success message and exit.
					return wpzinc_modal_show_success_message_and_exit(
						page_generator_pro_generate_content.messages.trash_generated_content_success
					);
				}

			}
		}
	);

}


/**
 * Performs an asynchronous request to Delete Generated Content
 * when the user clicks and confirms the Delete Generated Content Button
 * when editing a Content Group.
 *
 * @since 	1.8.4
 *
 * @param 	int 	group_id 		Group ID.
 * @param 	string 	type 			Type (content|term).
 * @param 	string 	request_count 	Request Count.
 * @param 	int 	limit 			Number of Items to Delete in this request.
 * @param 	int 	total 			Total Items to Delete.
 */
function page_generator_pro_generate_content_delete_generated_content( group_id, type, request_count, limit, total ) {

	// Define item start and end that will be deleted in this request.
	// These are used in the UI only, and not sent in the AJAX request.
	var start = ( Number( request_count ) * Number( limit ) ) + 1,
		end   = Number( start ) + Number( limit );

	// If the final item index exceeds the total number of items, set it to the total.
	if ( end > total ) {
		end = total;
	}

	// Show overlay and progress.
	wpzinc_modal_open(
		page_generator_pro_generate_content.titles.delete_generated_content,
		page_generator_pro_generate_content.messages.delete_generated_content + ' ' + start + ' to ' + end + ' of ' + total
	);

	// Perform AJAX query.
	jQuery.ajax(
		{
			url: 		ajaxurl,
			type: 		'POST',
			async:    	true,
			data: 		{
				id: 			group_id,
				action: 		'page_generator_pro_generate_' + type + '_delete_generated_' + type,
				nonce:  		page_generator_pro_generate_content.nonces.delete_generated_content
			},
			error: function( xhr, textStatus, errorThrown ) {

				// Show error message and exit.
				return wpzinc_modal_show_error_message( xhr.status + ' ' + xhr.statusText );

			},
			success: function( result ) {

				if ( ! result.success ) {
					// Show error message and exit.
					return wpzinc_modal_show_error_message( result.data );
				}

				// If there is a has_more flag, continue the requests.
				if ( result.data.has_more ) {
					// Run the next request.
					page_generator_pro_generate_content_delete_generated_content( group_id, type, ( request_count + 1 ), limit, total );
				} else {
					// No more requests to be made.

					// Determine if Trash and Delete are within a div.wpzinc-option.
					// If so, we're editing a Content Group and will need to delete div.wpzinc-option as it will be blank after
					// the buttons are removed.
					if ( jQuery( 'div.wpzinc-option div.full span.delete_generated_content' ).length > 0 ) {
						jQuery( 'span.delete_generated_content' ).closest( 'div.wpzinc-option' ).remove();
					} else {
						// Hide View, Trash and Delete Links in WP_List_Table.
						jQuery( 'span.generated_count span.count[data-group-id="' + group_id + '"]' ).text( '0' );
						jQuery( 'span.last_index_generated span.count[data-group-id="' + group_id + '"]' ).text( '0' );
						jQuery( 'span.view a[data-group-id="' + group_id + '"]' ).remove();
						jQuery( 'span.trash_generated_content a[data-group-id="' + group_id + '"]' ).remove();
						jQuery( 'span.delete_generated_content a[data-group-id="' + group_id + '"]' ).remove();

						// Reset Generated Items Count.
						jQuery( 'span.view a[data-group-id="' + group_id + '"]' ).remove();
					}

					// Show success message and exit.
					return wpzinc_modal_show_success_message_and_exit(
						page_generator_pro_generate_content.messages.delete_generated_content_success
					);
				}

			}
		}
	);

}
