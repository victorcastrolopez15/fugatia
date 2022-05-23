<div class="wpzinc-option featured_image fibu">
    <div class="left">
        <label for="featured_image_fibu_url"><?php _e( 'URL', 'page-generator-pro' ); ?></label>
    </div>
    <div class="right">
        <input type="text" name="<?php echo $this->base->plugin->name; ?>[featured_image_fibu_url]" id="featured_image_fibu_url" value="<?php echo $settings['featured_image_fibu_url']; ?>" class="widefat" />
    </div>

    <p class="description">
        <?php _e( 'Enter an image URL. This can be a dynamic image URL.', 'page-generator-pro' ); ?>
    </p>
</div>

<div class="wpzinc-option featured_image fibu">
    <div class="left">
        <label for="featured_image_fibu_alt"><?php _e( 'Alt Text', 'page-generator-pro' ); ?></label>
    </div>
    <div class="right">
        <input type="text" name="<?php echo $this->base->plugin->name; ?>[featured_image_fibu_alt]" id="featured_image_fibu_alt" value="<?php echo $settings['featured_image_fibu_alt']; ?>" placeholder="<?php _e( 'e.g. building', 'page-generator-pro' ); ?>" class="widefat" />
    </div>
    <p class="description">
        <?php _e( 'The alt text.', 'page-generator-pro' ); ?>
    </p>
</div>