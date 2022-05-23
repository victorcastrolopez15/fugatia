<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php echo $this->base->plugin->displayName; ?>

        <span>
            <?php _e( 'Settings', 'page-generator-pro' ); ?>
        </span>
    </h1>

    <?php
    // Output Success and/or Error Notices, if any exist
    $this->base->get_class( 'notices' )->output_notices();
    ?>

    <div class="wrap-inner">
        <!-- Tabs -->
        <h2 class="nav-tab-wrapper">
            <?php                               
            // Go through all registered settings panels
            foreach ( $panels as $key => $panel ) {
                ?>
                <a href="admin.php?page=page-generator-pro-settings&amp;tab=<?php echo $key; ?>" class="nav-tab<?php echo ( $tab == $key ? ' nav-tab-active' : '' ); ?>">
                    <?php
                    // Check if the icon is a URL
                    // If so, output the image instead of the dashicon
                    if ( filter_var( $panel['icon'], FILTER_VALIDATE_URL ) ) {
                        // Icon
                        ?>
                        <span style="background:url(<?php echo $panel['icon']; ?>) center no-repeat;" class="tab-icon"></span>
                        <?php
                    } else {
                        // Dashicon
                        ?>
                        <span class="dashicons <?php echo $panel['icon']; ?>"></span>
                        <?php
                    }
                    
                    echo $panel['label']; 
                    ?>
                </a>
                <?php
            }
            
            ?>
        </h2>
        
        <!-- Form Start -->
        <form name="post" method="post" action="<?php echo esc_attr( $_SERVER['REQUEST_URI'] ); ?>" id="<?php echo $this->base->plugin->name; ?>">    
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-1">
                    <!-- Content -->
                    <div id="post-body-content">
                        <div id="normal-sortables" class="meta-box-sortables ui-sortable publishing-defaults">  
                            <?php
                            // Load sub view
                            do_action( 'page_generator_pro_setting_panel-' . $tab );
                            ?>

                            <!-- Save -->
                            <div>
                                <?php wp_nonce_field( $this->base->plugin->name, $this->base->plugin->name . '_nonce' ); ?>
                                <input type="submit" name="submit" value="<?php _e( 'Save', 'page-generator-pro' ); ?>" class="button button-primary" />
                            </div>
                        </div>
                        <!-- /normal-sortables -->
                    </div>
                    <!-- /post-body-content -->
                </div>
            </div> 
        </form>  

    </div><!-- /.wrap-inner -->
</div><!-- /.wrap -->