<tr class="form-field term-description-wrap">
    <th scope="row"><label for="description"><?php _e( 'Description' ); ?></label></th>
    <td>
        <?php
        wp_editor( htmlspecialchars_decode( $term->description ), 'html-tag-description', array(
            'textarea_name' => 'description',
            'textarea_rows' => 10,
            'editor_class'  => 'i18n-multilingual',
        ) );
        ?>
        <p class="description"><?php esc_html_e( 'The description is not prominent by default; however, some themes may show it.' ); ?></p>
    </td>
    <script>
        // Remove the non-html field
        jQuery('textarea#description').closest('.form-field').remove();
    </script>
</tr>