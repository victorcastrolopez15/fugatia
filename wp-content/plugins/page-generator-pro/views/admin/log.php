<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php echo $this->base->plugin->displayName; ?>

        <span>
            <?php _e( 'Logs', 'page-generator-pro' ); ?>
        </span>
    </h1>

    <?php
    // Search Subtitle
    if ( isset( $_REQUEST['s'] ) && ! empty( $_REQUEST['s'] ) ) {
        ?>
        <span class="subtitle left"><?php _e( 'Search results for', 'page-generator-pro' ); ?> &#8220;<?php echo esc_html( urldecode( $_REQUEST['s'] ) ); ?>&#8221;</span>
        <?php
    }
    ?>

	<form action="admin.php?page=<?php echo esc_attr( $_REQUEST['page'] ); ?>" method="post" id="posts-filter">
		<?php   
		// Output Search Box
        $table->search_box( __( 'Search' ), 'page-generator-pro' );

        // Output Table
		$table->display(); 
		?>	
	</form>
</div><!-- /.wrap -->