<div class="postbox">
    <h3 class="hndle"><?php _e( 'Generate Locations', 'page-generator-pro' ); ?></h3>

    <div class="wpzinc-option">
        <p class="description">
            <?php _e( 'Specifies default settings for the Keywords &gt; Generate Locations screen.  These can always be overriden when using Generate Locations.', 'page-generator-pro' ); ?>
        </p>
    </div>

    <div class="wpzinc-option">
        <div class="left">
            <label for="method"><?php _e( 'Method', 'page-generator-pro' ); ?></label>
        </div>
        <div class="right">
            <?php
            $setting = $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-georocket', 'method', 'radius' );
            ?>
            <select name="<?php echo $this->base->plugin->name; ?>-georocket[method]" id="method" size="1">
                <?php
                foreach ( $methods as $method => $label ) {
                    ?>
                    <option value="<?php echo $method; ?>"<?php selected( $setting, $method ); ?>>
                        <?php echo $label; ?>
                    </option>
                    <?php
                }
                ?>
            </select>
        
            <p class="description">
                <?php _e( 'The default method to select for the Method dropdown.', 'page-generator-pro' ); ?><br />
            </p>
        </div>
    </div>

    <div class="wpzinc-option">
        <div class="left">
            <label for="radius"><?php _e( 'Radius', 'page-generator-pro' ); ?></label>
        </div>
        <div class="right">
            <?php
            $setting = $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-georocket', 'radius', '' );
            ?>
            <input type="number" id="radius" name="<?php echo $this->base->plugin->name; ?>-georocket[radius]" min="0.1" max="99999" step="0.1" value="<?php echo $setting; ?>" class="widefat" />
          
            <p class="description">
                <?php _e( 'The default radius distance value, in miles.', 'page-generator-pro' ); ?><br />
            </p>
        </div>
    </div>
</div>