<div class="wpzinc-option"> 
    <div class="left">
        <label for="store_keywords"><?php _e( 'Store Keywords?', 'page-generator-pro' ); ?></strong>
    </div>
    <div class="right">
        <input type="checkbox" id="store_keywords" name="<?php echo $this->base->plugin->name; ?>[store_keywords]" value="1"<?php checked( $this->settings['store_keywords'], 1 ); ?> />
    
        <p class="description">
            <?php _e( 'If checked, each generated Page/Post will store keyword and term key/value pairs in the Page/Post\'s Custom Fields. This is useful for subsequently querying Custom Field Metadata in e.g. Related Links.', 'page-generator-pro' ); ?>
        </p>
    </div>
</div>

<!-- Custom Fields -->
<div class="wpzinc-option">
    <div class="full">
        <table class="widefat">
            <thead>
                <tr>
                    <th><?php _e( 'Meta Key', 'page-generator-pro' ); ?></th>
                    <th><?php _e( 'Meta Value', 'page-generator-pro' ); ?></th>
                    <th><?php _e( 'Actions', 'page-generator-pro' ); ?></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="3">
                        <button class="wpzinc-add-table-row button" data-table-row-selector="custom-field-row">
                            <?php _e( 'Add Custom Field', 'page-generator-pro' ); ?>
                        </button>
                    </td>
                </tr>
            </tfoot>
            <tbody class="is-sortable">
            	<?php
            	// Existing Custom Fields
            	if ( is_array( $this->settings['meta'] ) && count( $this->settings['meta'] ) > 0 ) {
                    foreach ( $this->settings['meta']['key'] as $i => $key ) {
                        ?>
                        <tr class="custom-field-row">
                            <td>
                                <input type="text" name="<?php echo $this->base->plugin->name; ?>[meta][key][]" value="<?php echo $key; ?>" placeholder="<?php _e( 'Meta Key', 'page-generator-pro' ); ?>" class="widefat" />
                            </td>
                            <td>
                                <textarea name="<?php echo $this->base->plugin->name; ?>[meta][value][]" placeholder="<?php _e( 'Meta Value', 'page-generator-pro' ); ?>" class="widefat"><?php echo $this->settings['meta']['value'][ $i ]; ?></textarea>
                            </td>
                            <td>
                                <a href="#" class="move-row">
                                    <span class="dashicons dashicons-move "></span>
                                    <?php _e( 'Move', 'page-generator-pro' ); ?>
                                </a>

                                <a href="#" class="wpzinc-delete-table-row">
                                    <span class="dashicons dashicons-trash"></span>
                                    <?php _e( 'Delete', 'page-generator-pro' ); ?>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>

                <tr class="custom-field-row hidden">
                    <td>
                        <input type="text" name="<?php echo $this->base->plugin->name; ?>[meta][key][]" value="" placeholder="<?php _e( 'Meta Key', 'page-generator-pro' ); ?>" class="widefat" />
                    </td>
                    <td>
                        <textarea name="<?php echo $this->base->plugin->name; ?>[meta][value][]" placeholder="<?php _e( 'Meta Value', 'page-generator-pro' ); ?>" class="widefat"></textarea>
                    </td>
                    <td>
                        <a href="#" class="move-row">
                            <span class="dashicons dashicons-move "></span>
                            <?php _e( 'Move', 'page-generator-pro' ); ?>
                        </a>

                        <a href="#" class="wpzinc-delete-table-row">
                            <span class="dashicons dashicons-trash"></span>
                            <?php _e( 'Delete', 'page-generator-pro' ); ?>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>