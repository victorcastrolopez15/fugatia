<?php
// Output Template Options for Post Types
foreach ( $post_types_templates as $post_type => $templates ) {
	$template = ( isset( $this->settings['pageTemplate'][ $post_type ] ) ? $this->settings['pageTemplate'][ $post_type ] : '' );
	?>
	<div class="wpzinc-option post-type-conditional <?php echo $post_type; ?>">
		<div class="full">
	    	<select name="<?php echo $this->base->plugin->name; ?>[pageTemplate][<?php echo $post_type; ?>]" id="<?php echo $post_type; ?>_template" size="1" class="widefat">
	    		<option value="default"<?php selected( $template, 'default' ); ?>>
	    			<?php _e( 'Default Template', 'page-generator-pro' ); ?>
	    		</option>
	    		<?php page_template_dropdown( $template, $post_type ); ?>
			</select>
		</div>
	</div>
	<?php
}