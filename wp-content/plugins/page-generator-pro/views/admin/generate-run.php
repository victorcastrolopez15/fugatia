<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php echo $this->base->plugin->displayName; ?>

        <span>
            <?php
            echo sprintf(
                /* translators: Title */
                __( 'Generating &quot;%s&quot;', 'page-generator-pro' ),
                $settings['title']
            );
            ?>
        </span>
    </h1>

    <hr class="wp-header-end" />

    <div class="wrap-inner">
        <p>
        	<?php
            echo sprintf(
                /* translators: Documentation Link */
                __( 'Please be patient while content is generated. This can take a while if you have a lot of Pages to generate, and/or you are using Page Generator Shortcodes. %s to understand why.', 'page-generator-pro' ),
                '<a href="' . $this->base->plugin->documentation_url . '/generate/#page-generation" target="_blank">' . __( 'Read the Documentation', 'page-generator-pro' ) . '</a>'
            );
            ?>
            <br />

        	<?php _e( 'Do not navigate away from this page until this script is done or all items will not be generated. You will be notified via this page when the process is completed.', 'page-generator-pro' ); ?>
        </p>

        <!-- Progress Bar -->
        <div id="progress-bar"></div>
        <div id="progress">
            <span id="progress-number"><?php echo $settings['resumeIndex']; ?></span>
            <span> / <?php echo ( $settings['numberOfPosts'] + $settings['resumeIndex'] ); ?></span>
        </div>

        <!-- Status Updates -->
        <div id="log">
            <ul></ul>
        </div>

        <p>
            <!-- Cancel Button -->
            <a href="post.php?post=<?php echo $id; ?>&amp;action=edit" class="button wpzinc-button-red page-generator-pro-generate-cancel-button">
                <?php _e( 'Stop Generation', 'page-generator-pro' ); ?>
            </a>

            <!-- Return Button (display when generation routine finishes -->
            <a href="<?php echo $return_url; ?>" class="button button-primary page-generator-pro-generate-return-button">
                <?php _e( 'Return to Group', 'page-generator-pro' ); ?>
            </a>
        </p>
    </div>
</div>