<div class="wpzinc-option">    
    <div class="left">
        <label for="latitude"><?php _e( 'Latitude', 'page-generator-pro' ); ?></label>
    </div>
    <div class="right">
        <input type="text" name="<?php echo $this->base->plugin->name; ?>[latitude]" id="latitude" value="<?php echo $this->settings['latitude']; ?>" placeholder="<?php _e( 'Latitude', 'page-generator-pro' ); ?>" class="widefat" />
        
        <p class="description">
            <?php _e( 'Enter the Keyword that stores the Latitude.  This is used by the Related Links Shortcode for displaying Related Links by Radius.', 'page-generator-pro' ); ?>
        </p>
    </div>
</div>

<div class="wpzinc-option">    
    <div class="left">
        <label for="longitude"><?php _e( 'Longitude', 'page-generator-pro' ); ?></label>
    </div>
    <div class="right">
        <input type="text" name="<?php echo $this->base->plugin->name; ?>[longitude]" id="longitude" value="<?php echo $this->settings['longitude']; ?>" placeholder="<?php _e( 'Longitude', 'page-generator-pro' ); ?>" class="widefat" />
    
        <p class="description">
            <?php _e( 'Enter the Keyword that stores the Longitude.  This is used by the Related Links Shortcode for displaying Related Links by Radius.', 'page-generator-pro' ); ?>
        </p>
    </div>
</div>