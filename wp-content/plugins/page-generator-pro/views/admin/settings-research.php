<div class="postbox">
    <h3 class="hndle"><?php _e( 'Research', 'page-generator-pro' ); ?></h3>

    <div class="wpzinc-option">
        <p class="description">
            <?php
            _e( 'Specifies which provider to use to perform research to build content for a given topic.', 'page-generator-pro' );
            ?>
        </p>
    </div>

    <div class="wpzinc-option">
        <div class="left">
            <label for="provider"><?php _e( 'Service', 'page-generator-pro' ); ?></label>
        </div>
        <div class="right">
            <select name="<?php echo $this->base->plugin->name; ?>-research[provider]" id="provider" size="1">
                <?php
                foreach ( $providers as $provider => $label ) {
                    ?>
                    <option value="<?php echo $provider; ?>"<?php selected( $settings['provider'], $provider ); ?>><?php echo $label; ?></option>
                    <?php
                }
                ?>
            </select>
            <p class="description">
                <?php _e( 'The third party service to use for research.', 'page-generator-pro' ); ?>
            </p>
        </div>
    </div>

    <!-- AI Writer -->
    <div id="ai_writer">
        <div class="wpzinc-option">
            <div class="left">
                <label for="ai_writer_api_key"><?php _e( 'API Key', 'page-generator-pro' ); ?></label>
            </div>
            <div class="right">
                <input type="text" name="<?php echo $this->base->plugin->name; ?>-research[ai_writer_api_key]" id="ai_writer_api_key" value="<?php echo $settings['ai_writer_api_key']; ?>" class="widefat" />
                <p class="description">
                    <?php 
                    echo sprintf( 
                        /* translators: %1$s: AI Writer Account Link, %2$s: AI Writer Registration Link */
                        __( 'Enter your AI Writer API key, %1$s. Don\'t have an account? %2$s.', 'page-generator-pro' ),
                        '<a href="' . $this->base->get_class( 'ai_writer' )->get_account_url() . '" target="_blank" rel="noopener">' . __( 'which can be found here', 'page-generator-pro' ) . '</a>',
                    	'<a href="' . $this->base->get_class( 'ai_writer' )->get_registration_url() . '" target="_blank" rel="noopener">' . __( 'Register an account', 'page-generator-pro' ) . '</a>'
                    );
                    ?>
                </p>
            </div>
        </div>
    </div>

    <!-- ContentBot -->
    <div id="contentbot">
        <div class="wpzinc-option">
            <div class="left">
                <label for="contentbot_api_key"><?php _e( 'API Key', 'page-generator-pro' ); ?></label>
            </div>
            <div class="right">
                <input type="text" name="<?php echo $this->base->plugin->name; ?>-research[contentbot_api_key]" id="contentbot_api_key" value="<?php echo $settings['contentbot_api_key']; ?>" class="widefat" />
                <p class="description">
                    <?php 
                    echo sprintf( 
                        /* translators: %1$s: ContentBot Account Link, %2$s: ContentBot Registration Link */
                        __( 'Enter your ContentBot API key, %1$s. Don\'t have an account? %2$s.', 'page-generator-pro' ),
                        '<a href="' . $this->base->get_class( 'contentbot' )->get_account_url() . '" target="_blank" rel="noopener">' . __( 'which can be found here', 'page-generator-pro' ) . '</a>',
                    	'<a href="' . $this->base->get_class( 'contentbot' )->get_registration_url() . '" target="_blank" rel="noopener">' . __( 'Register an account', 'page-generator-pro' ) . '</a>'
                    );
                    ?>
                </p>
            </div>
        </div>
    </div>
</div>