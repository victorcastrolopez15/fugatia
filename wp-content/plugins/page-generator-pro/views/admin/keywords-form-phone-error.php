<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php echo $this->base->plugin->displayName; ?>

        <span>
        	<?php _e( 'Generate Phone Area Codes', 'page-generator-pro' ); ?>
        </span>
    </h1>

    <?php
    // Button Links
    require_once( 'keywords-links.php' );

    // Output Success and/or Error Notices, if any exist
    $this->base->get_class( 'notices' )->output_notices();
    ?>
</div>