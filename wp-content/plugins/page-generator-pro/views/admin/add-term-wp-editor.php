<div class="form-field term-description-wrap">
	<label for="tag-description"><?php esc_html_e( 'Description' ); ?></label>
	<?php
	wp_editor( '', 'html-tag-description', array(
		'textarea_name' => 'description',
		'textarea_rows' => 7,
		'editor_class'  => 'i18n-multilingual',
	) );
	?>
	<p><?php esc_html_e( 'The description is not prominent by default; however, some themes may show it.' ); ?></p>

	<script>
		// Remove the non-html field
		jQuery('textarea#tag-description').closest('.form-field').remove();

		jQuery(function () {
			jQuery('#addtag').on('mousedown', '#submit', function () {
				tinyMCE.triggerSave();

				jQuery(document).bind('ajaxSuccess.pgp_add_term', function () {
					if (tinyMCE.activeEditor) {
						tinyMCE.activeEditor.setContent('');
					}
					jQuery(document).unbind('ajaxSuccess.pgp_add_term', false);
				});
			});
		});
	</script>
</div>