<div class="postbox">
    <h3 class="hndle"><?php _e( 'Generate', 'page-generator-pro' ); ?></h3>

    <div class="wpzinc-option">
        <p class="description">
            <?php _e( 'Specifies default behaviour when Generating Content and Terms.', 'page-generator-pro' ); ?>
        </p>
    </div>

    <div class="wpzinc-option">
        <div class="left">
            <label for="log_enabled"><?php _e( 'Enable Logging?', 'page-generator-pro' ); ?></label>
        </div>
        <div class="right">
            <?php
            $setting = $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-generate', 'log_enabled', '0' );
            ?>
            <select name="<?php echo $this->base->plugin->name; ?>-generate[log_enabled]" id="log_enabled" size="1" data-conditional="log_settings">
                <option value="1"<?php selected( $setting, '1' ); ?>><?php _e( 'Yes', 'page-generator-pro' ); ?></option>
                <option value="0"<?php selected( $setting, '0' ); ?>><?php _e( 'No', 'page-generator-pro' ); ?></option>
            </select>
        
            <p class="description">
                <?php 
                echo sprintf(
                    /* translators: Documentation Link */
                    __( 'If enabled, the %s will detail results of Content and Term Generation.', 'page-generator-pro' ),
                    '<a href="' . $this->base->plugin->documentation_url . '/logs" target="_blank" rel="noopener">' . __( 'Plugin Logs', 'page-generator-pro' ) . '</a>'
                );
                ?>
            </p>
        </div>
    </div>

    <div id="log_settings" class="wpzinc-option">
        <div class="left">
            <label for="log_preserve_days"><?php _e( 'Preserve Logs', 'page-generator-pro' ); ?></label>
        </div>
        <div class="right">
            <input type="number" name="<?php echo $this->base->plugin->name; ?>-generate[log_preserve_days]" id="log_preserve_days" min="0" max="365" step="1" value="<?php echo $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-generate', 'log_preserve_days', '7' ); ?>" />
            <?php _e( 'days', 'page-generator-pro' ); ?>
            
            <p class="description">
                <?php _e( 'The number of days to preserve logs for. Zero means logs are kept indefinitely.', 'page-generator-pro' ); ?>
            </p>
        </div>
    </div>

    <div class="wpzinc-option">
        <div class="left">
            <label for="stop_on_error"><?php _e( 'Stop on Error', 'page-generator-pro' ); ?></label>
        </div>
        <div class="right">
            <?php
            $setting = $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-generate', 'stop_on_error', '0' );
            ?>
            <select name="<?php echo $this->base->plugin->name; ?>-generate[stop_on_error]" id="stop_on_error" size="1" data-conditional="stop_on_error_settings" data-conditional-value="0,-1">
                <option value="1"<?php selected( $setting, '1' ); ?>><?php _e( 'Stop', 'page-generator-pro' ); ?></option>
                <option value="0"<?php selected( $setting, '0' ); ?>><?php _e( 'Continue, attempting to regenerate the Content or Term again', 'page-generator-pro' ); ?></option>
                <option value="-1"<?php selected( $setting, '-1' ); ?>><?php _e( 'Continue, skipping the failed Content or Term', 'page-generator-pro' ); ?></option>
            </select>
        
            <p class="description">
                <?php _e( 'Whether to stop Content / Term Generation when an error occurs.', 'page-generator-pro' ); ?>
            </p>
        </div>
    </div>

    <div id="stop_on_error_settings">
        <div class="wpzinc-option">
            <div class="left">
                <label for="stop_on_error_pause"><?php _e( 'Pause before Continuing', 'page-generator-pro' ); ?></label>
            </div>
            <div class="right">
                <?php
                $setting = $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-generate', 'stop_on_error_pause', '5' );
                ?>
                <input type="number" id="stop_on_error_pause" name="<?php echo $this->base->plugin->name; ?>-generate[stop_on_error_pause]" value="<?php echo $setting; ?>" min="1" max="60" step="1" />
                
                <p class="description">
                    <?php _e( 'The number of seconds to pause generation before resuming when an error is detected, if Stop on Error is set to continue on errors.', 'page-generator-pro' ); ?>
                </p>
            </div>
        </div>
    </div>

    <div class="wpzinc-option">
        <div class="left">
            <label for="use_mu_plugin"><?php _e( 'Use Performance Addon?', 'page-generator-pro' ); ?></label>
        </div>
        <div class="right">
            <?php
            $setting = $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-generate', 'use_mu_plugin', '0' );
            ?>
            <select name="<?php echo $this->base->plugin->name; ?>-generate[use_mu_plugin]" id="use_mu_plugin" data-conditional="use_mu_plugin_settings" size="1">
                <option value="1"<?php selected( $setting, '1' ); ?>><?php _e( 'Yes', 'page-generator-pro' ); ?></option>
                <option value="0"<?php selected( $setting, '0' ); ?>><?php _e( 'No', 'page-generator-pro' ); ?></option>
            </select>
        
            <p class="description">
                <?php _e( 'Experimental: If enabled, uses the Performance Addon Must-Use Plugin.  This can improve generation times and reduce memory usage on sites with several Plugins.', 'page-generator-pro' ); ?>
            </p>
        </div>
    </div>

    <div id="use_mu_plugin_settings" class="wpzinc-option">
        <div class="left">
            <label for="log_preserve_days"><?php _e( 'Performance Addon: Load Plugins', 'page-generator-pro' ); ?></label>
        </div>
        <div class="right">
            <p class="description">
                <?php _e( 'If generation correctly generates data, there\'s no need to enable Plugins here - even if they\'re used in a Content Group. For example, most Custom Field and SEO data will generate without needing their Plugins to be activated here.', 'page-generator-pro' ); ?>
                <br />
                <?php _e( 'If generation <strong>does not</strong> correctly generate data, you may need to enable the applicable Plugin relating to that data below, so that it is loaded when using the Performance Addon.', 'page-generator-pro' ); ?>
             </p>

            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php _e( 'Plugin', 'page-generator-pro' ); ?></th>
                        <td><?php _e( 'Enabled', 'page-generator-pro' ); ?></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $use_mu_active_plugins = $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-generate', 'use_mu_active_plugins', array() );
                    $use_mu_required_plugins = $this->base->get_class( 'common' )->get_use_mu_plugin_required_plugins();

                    foreach ( get_plugins() as $uri => $plugin ) {
                        ?>
                        <tr>
                            <td>
                                <label for="use_mu_active_plugins_<?php echo $uri; ?>">
                                    <?php echo $plugin['Name']; ?>
                                </label>
                                <?php
                                if ( in_array( $uri, $use_mu_required_plugins ) ) {
                                    ?>
                                    <br /><small>This plugin is required when using the Performance Addon. It cannot be disabled.</small>
                                    <?php
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $enabled = false;
                                if ( in_array( $uri, $use_mu_required_plugins ) || in_array( $uri, $use_mu_active_plugins ) ) {
                                    $enabled = true;
                                }
                                ?>
                                <input type="checkbox" name="<?php echo $this->base->plugin->name; ?>-generate[use_mu_active_plugins][]" id="use_mu_active_plugins_<?php echo $uri; ?>" value="<?php echo $uri; ?>"<?php checked( $enabled, 1 ); ?> <?php echo ( in_array( $uri, $use_mu_required_plugins ) ? ' disabled' : '' ); ?> />
                                
                            </td> 
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>