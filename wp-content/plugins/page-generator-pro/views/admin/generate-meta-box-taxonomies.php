<?php
// Check if hierarchal or tag based
switch ( $taxonomy->hierarchical ) {

	case true:
		// Category based taxonomy
		$terms = get_terms( $taxonomy->name, array( 'hide_empty' => 0 ) );
		?>
		<div class="wpzinc-option taxonomy post-type-conditional <?php echo trim( $post_types_string ); ?>">
			<div class="left">
				<strong><?php echo $taxonomy->labels->name; ?></strong>
			</div>
			<div class="right">
				<a href="#" class="button button-small deselect-all" data-list="#taxonomy-list-<?php echo $taxonomy->name; ?>">
					<?php _e( 'Deselect All', 'page-generator-pro' ); ?>
				</a>
			</div>
			
			<div class="full tax-selection">
				<div class="tabs-panel">
					<ul id="taxonomy-list-<?php echo $taxonomy->name; ?>" class="categorychecklist form-no-clear">				                    			
						<?php
						foreach ( $terms as $term_key => $term ) {
                            ?>
                            <li>
								<label class="selectit">
									<input type="checkbox" name="<?php echo $this->base->plugin->name; ?>[tax][<?php echo $taxonomy->name; ?>][<?php echo $term->term_id; ?>]" value="1"<?php echo ( isset( $this->settings['tax'][ $taxonomy->name ][ $term->term_id ] ) ? ' checked' : '' ); ?> />
									<?php echo $term->name; ?>      
								</label>
							</li>
                            <?php
						}	
						?>
					</ul>
				</div>
				<input type="search" name="search" data-list="#taxonomy-list-<?php echo $taxonomy->name; ?>" placeholder="<?php _e( 'Search', 'page-generator-pro' ); ?>" class="widefat" />
			</div>

			<div class="full">
				<input type="text" name="<?php echo $this->base->plugin->name; ?>[tax][<?php echo $taxonomy->name; ?>][0]" value="<?php echo ( isset( $this->settings['tax'][ $taxonomy->name ][0] ) ? $this->settings['tax'][ $taxonomy->name ][0] : '' ); ?>" class="widefat" placeholder="<?php _e( 'Enter new taxonomy terms to create here.', 'page-generator-pro' ); ?>" />
			</div>
		</div>
		<?php
		break;

	case false:
		// Tag based taxonomy
		?>
		<div class="wpzinc-option taxonomy post-type-conditional <?php echo trim( $post_types_string ); ?>">
			<div class="full">
				<strong><?php echo $taxonomy->labels->name; ?></strong>
			</div>
			
			<div class="full">
				<input type="text" name="<?php echo $this->base->plugin->name; ?>[tax][<?php echo $taxonomy->name; ?>]" value="<?php echo ( isset( $this->settings['tax'][ $taxonomy->name ] ) ? $this->settings['tax'][ $taxonomy->name ] : '' ); ?>" class="widefat" />
			</div>
		</div>
		<?php
		break;
}