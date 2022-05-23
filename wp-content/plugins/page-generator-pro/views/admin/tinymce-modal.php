<!-- .wp-core-ui ensures styles are applied on frontend editors for e.g. buttons.css -->
<form class="wpzinc-tinymce-popup wp-core-ui">
	<input type="hidden" name="shortcode" value="page-generator-pro-<?php echo $shortcode['name']; ?>" />

    <?php
    // Output each Field
    foreach ( $shortcode['fields'] as $field_name => $field ) {
        include( 'tinymce-modal-field-row.php' );
    }
    ?>
</form>