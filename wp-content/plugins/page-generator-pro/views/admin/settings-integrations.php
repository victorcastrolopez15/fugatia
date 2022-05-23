<div class="postbox">
    <h3 class="hndle"><?php _e( 'Integrations', 'page-generator-pro' ); ?></h3>

    <!-- Airtable -->
    <div class="wpzinc-option">
        <div class="left">
            <label for="airtable_api_key"><?php _e( 'Airtable API Key', 'page-generator-pro' ); ?></label>
        </div>
        <div class="right">
            <input type="text" name="<?php echo $this->base->plugin->name; ?>-integrations[airtable_api_key]" id="airtable_api_key" value="<?php echo $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-integrations', 'airtable_api_key' ); ?>" class="widefat" />
            <p class="description">
                <?php 
                echo sprintf( 
                    /* translators: Documentation Link */
                    __( 'To use an Airtable base as a Keyword Source, enter your API key here. %s to read the step by step documentation to do this.', 'page-generator-pro' ),
                    '<a href="' . $this->base->plugin->documentation_url . '/settings-integration/#airtable" target="_blank" rel="noopener">' . __( 'Click here', 'page-generator-pro' ) . '</a>'
                );
                ?>
            </p>
        </div>
    </div>

    <!-- OpenWeatherMap -->
    <div class="wpzinc-option">
        <div class="left">
            <label for="open_weather_map_api_key"><?php _e( 'OpenWeatherMap API Key', 'page-generator-pro' ); ?></label>
        </div>
        <div class="right">
            <input type="text" name="<?php echo $this->base->plugin->name; ?>-integrations[open_weather_map_api_key]" id="open_weather_map_api_key" value="<?php echo $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-integrations', 'open_weather_map_api_key' ); ?>" class="widefat" />
            <p class="description">
                <?php 
                echo sprintf( 
                    /* translators: Documentation Link */
                    __( 'If you reach an API limit when attempting to use the OpenWeatherMap Dynamic Element, you\'ll need to use your own free API key.  %s to read the step by step documentation to do this.', 'page-generator-pro' ),
                    '<a href="' . $this->base->plugin->documentation_url . '/settings-integration/#openweathermap" target="_blank" rel="noopener">' . __( 'Click here', 'page-generator-pro' ) . '</a>'
                );
                ?>
            </p>
        </div>
    </div>

    <!-- Pexels -->
    <div class="wpzinc-option">
        <div class="left">
            <label for="pexels_api_key"><?php _e( 'Pexels API Key', 'page-generator-pro' ); ?></label>
        </div>
        <div class="right">
            <input type="text" name="<?php echo $this->base->plugin->name; ?>-integrations[pexels_api_key]" id="pexels_api_key" value="<?php echo $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-integrations', 'pexels_api_key' ); ?>" class="widefat" />
            <p class="description">
                <?php 
                echo sprintf( 
                    /* translators: Documentation Link */
                    __( 'If you reach an API limit when attempting to import images from Pexels, you\'ll need to use your own free Pexels API key.  %s to read the step by step documentation to do this.', 'page-generator-pro' ),
                    '<a href="' . $this->base->plugin->documentation_url . '/settings-integration/#pexels" target="_blank" rel="noopener">' . __( 'Click here', 'page-generator-pro' ) . '</a>'
                );
                ?>
            </p>
        </div>
    </div>

    <!-- Pixabay -->
    <div class="wpzinc-option">
        <div class="left">
            <label for="pixabay_api_key"><?php _e( 'Pixabay API Key', 'page-generator-pro' ); ?></label>
        </div>
        <div class="right">
            <input type="text" name="<?php echo $this->base->plugin->name; ?>-integrations[pixabay_api_key]" id="pixabay_api_key" value="<?php echo $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-integrations', 'pixabay_api_key' ); ?>" class="widefat" />
            <p class="description">
                <?php 
                echo sprintf( 
                    /* translators: Documentation Link */
                    __( 'If you reach an API limit when attempting to import images from Pixabay, you\'ll need to use your own free Pixabay API key.  %s to read the step by step documentation to do this.', 'page-generator-pro' ),
                    '<a href="' . $this->base->plugin->documentation_url . '/settings-integration/#pixabay" target="_blank" rel="noopener">' . __( 'Click here', 'page-generator-pro' ) . '</a>'
                );
                ?>
            </p>
        </div>
    </div>

    <!-- Yelp -->
    <div class="wpzinc-option">
        <div class="left">
            <label for="yelp_api_key"><?php _e( 'Yelp API Key', 'page-generator-pro' ); ?></label>
        </div>
        <div class="right">
            <input type="text" name="<?php echo $this->base->plugin->name; ?>-integrations[yelp_api_key]" id="yelp_api_key" value="<?php echo $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-integrations', 'yelp_api_key' ); ?>" class="widefat" />
            <p class="description">
                <?php 
                echo sprintf( 
                    /* translators: Documentation Link */
                    __( 'If you reach an API limit when attempting to use the Yelp Dynamic Element, you\'ll need to use your own free Yelp API key.  %s to read the step by step documentation to do this.', 'page-generator-pro' ),
                    '<a href="' . $this->base->plugin->documentation_url . '/settings-integration/#yelp" target="_blank" rel="noopener">' . __( 'Click here', 'page-generator-pro' ) . '</a>'
                );
                ?>
            </p>
        </div>
    </div>

    <!-- Google -->
    <div class="wpzinc-option">
        <div class="left">
            <label for="youtube_data_api_key"><?php _e( 'YouTube Data API Key', 'page-generator-pro' ); ?></label>
        </div>
        <div class="right">
            <input type="text" name="<?php echo $this->base->plugin->name; ?>-integrations[youtube_data_api_key]" id="youtube_data_api_key" value="<?php echo $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-integrations', 'youtube_data_api_key' ); ?>" class="widefat" />
            <p class="description">
                <?php
                echo sprintf( 
                    /* translators: Documentation Link */
                    __( 'If you reach an API limit, or your YouTube Dynamic Element does not render, you\'ll need to use your own API key.  %s to read the step by step documentation to do this.', 'page-generator-pro' ),
                    '<a href="' . $this->base->plugin->documentation_url . '/settings-integration/#youtube" target="_blank" rel="noopener">' . __( 'Click here', 'page-generator-pro' ) . '</a>'
                );
                ?>
            </p>
        </div>
    </div>
</div>