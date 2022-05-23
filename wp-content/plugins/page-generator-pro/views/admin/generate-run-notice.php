<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php echo $this->base->plugin->displayName; ?>

        <span>
        	<?php _e( 'Generate', 'page-generator-pro' ); ?>
        </span>
    </h1>

    <?php
    // Output Success and/or Error Notices, if any exist
    $this->base->get_class( 'notices' )->output_notices();
    ?>

    <div class="wrap-inner">
    	<!-- Return Button -->
	    <a href="<?php echo $return_url; ?>" class="button button-primary">
	        <?php _e( 'Return to Group', 'page-generator-pro' ); ?>
	    </a>
	</div>
</div>