<div class="wpzinc-option">
	<div class="full">
		<?php 
		// Nonce field
		wp_nonce_field( 'save_generate', $this->base->plugin->name . '_nonce' ); 
		?>
		
		<span class="test">
			<a href="<?php echo admin_url( 'edit.php?post_type=' . $this->base->get_class( 'post_type' )->post_type_name . '&' . $this->base->plugin->name . '-action=test&id=' . $group_id . '&type=' . $this->settings['group_type'] ); ?>" class="button button-primary"><?php _e( 'Test', 'page-generator-pro' ); ?></a>
		</span>

		<span class="generate">
			<a href="<?php echo admin_url( 'admin.php?page=' . $this->base->plugin->name . '-generate&id=' . $group_id . '&type=' . $this->settings['group_type'] ); ?>" class="button button-primary"><?php _e( 'Generate via Browser', 'page-generator-pro' ); ?></a>
		</span>
	</div>
</div>
<?php
// Delete Generated Content, if any exist
if ( $this->settings['generated_pages_count'] > 0 ) {
	?>
	<div class="wpzinc-option">
		<div class="full">
			<?php
			if ( $this->settings['group_type'] == 'content' ) {
				?>
				<span class="trash_generated_content">
					<a href="<?php echo admin_url( 'edit.php?post_type=' . $this->base->get_class( 'post_type' )->post_type_name . '&' . $this->base->plugin->name . '-action=trash-generated-content&id=' . $group_id . '&type=' . $this->settings['group_type'] ); ?>" class="button wpzinc-button-red trash-generated-content" data-group-id="<?php echo $group_id; ?>" data-limit="<?php echo $limit; ?>" data-total="<?php echo $this->settings['generated_pages_count']; ?>"><?php _e( 'Trash Generated Content', 'page-generator-pro' ); ?></a>
				</span>
				<?php
			}
			?>
			<span class="delete_generated_content">
				<a href="<?php echo admin_url( 'edit.php?post_type=' . $this->base->get_class( 'post_type' )->post_type_name . '&' . $this->base->plugin->name . '-action=delete-generated-content&id=' . $group_id . '&type=' . $this->settings['group_type'] ); ?>" class="button wpzinc-button-red delete-generated-content" data-group-id="<?php echo $group_id; ?>" data-limit="<?php echo $limit; ?>" data-total="<?php echo $this->settings['generated_pages_count']; ?>"><?php _e( 'Delete Generated Content', 'page-generator-pro' ); ?></a>
			</span>
		</div>
	</div>
	<?php	
}