 <div class="wpzinc-option sidebar">
	<div class="left">
		<label for="method"><?php _e( 'Method', 'page-generator-pro' ); ?></label>
	</div>
	<div class="right">
		<select name="<?php echo $this->base->plugin->name; ?>[method]" id="method" size="1" class="widefat">
			<?php
			if ( is_array( $methods ) && count( $methods ) > 0 ) {
				foreach ( $methods as $method => $label ) {
					?>
					<option value="<?php echo $method; ?>"<?php selected( $this->settings['method'], $method ); ?>>
						<?php echo $label; ?>
					</option>
					<?php
				}
			}
			?>
		</select>
	</div>
	<p class="description">
		<strong><?php _e( 'All:', 'page-generator-pro' ); ?></strong>
		<?php
		echo sprintf( 
			/* translators: Post Type, Plural (e.g. Posts, Pages) */
			__( 'Generates %s for all possible combinations of terms across all keywords used.', 'page-generator-pro' ),
			$labels['plural']
		);
		?>
	</p>
	<p class="description">
		<strong><?php _e( 'Sequential:', 'page-generator-pro' ); ?></strong>
		<?php _e( 'Honors the order of terms in each keyword used. Once all terms have been used in a keyword, the generator stops.', 'page-generator-pro' ); ?>
	</p>
	<p class="description">
		<strong><?php _e( 'Random:', 'page-generator-pro' ); ?></strong>
		<?php
		/* translators: Post Type, Singular (e.g. Post, Page) */
		echo sprintf( __( 'For each %s generated, selects a term at random from each keyword used.', 'page-generator-pro' ), $labels['singular'] );
		?>
	</p>
</div>

<div class="wpzinc-option sidebar">
	<div class="left">
		<label for="apply_synonyms"><?php _e( 'Spin Content?', 'page-generator-pro' ); ?></label>
	</div>
	<div class="right">
		<select name="<?php echo $this->base->plugin->name; ?>[apply_synonyms]" id="apply_synonyms" size="1" class="widefat">
			<option value="1"<?php selected( $this->settings['apply_synonyms'], 1 ); ?>><?php _e( 'Yes', 'page-generator-pro' ); ?></option>
			<option value="0"<?php selected( $this->settings['apply_synonyms'], 0 ); ?>><?php _e( 'No', 'page-generator-pro' ); ?></option>
		</select>
	</div>

	<p class="description">
		<?php
		/* translators: Post Type, Singular (e.g. Post, Page) */
		echo sprintf( __( 'If enabled, the Visual Editor\'s content will be spun for each generated %s. If you have already defined spintax, do not use this option.', 'page-generator-pro' ), $labels['singular'] );
		?>
	</p>
</div>

<div class="wpzinc-option sidebar">
	<div class="left">
		<label for="overwrite"><?php _e( 'Overwrite', 'page-generator-pro' ); ?></label>
	</div>
	<div class="right">
		<select name="<?php echo $this->base->plugin->name; ?>[overwrite]" id="overwrite" size="1" class="widefat">
			<?php
			if ( is_array( $overwrite_methods ) && count( $overwrite_methods ) > 0 ) {
				foreach ( $overwrite_methods as $method => $label ) {
					?>
					<option value="<?php echo $method; ?>"<?php selected( $this->settings['overwrite'], $method ); ?>>
						<?php echo $label; ?>
					</option>
					<?php
				}
			}
			?>
		</select>
	</div>
	<p class="description">
		<?php 
		echo sprintf( 
			/* translators: Documentation Link */
			__( 'See the %s to understand each available option.', 'page-generator-pro' ),
			'<a href="' . $overwrite_documentation_url . '" rel="noopener" target="_blank">' . __( 'Documentation', 'page-generator-pro' ) . '</a>'
		);
		?>
	</p>
</div>

<?php
if ( isset( $overwrite_sections ) ) {
	?>
	<div class="wpzinc-option sidebar overwrite-sections overwrite overwrite_any overwrite_preserve_date overwrite_any_preserve_date">
		<div class="full">
			<label><?php _e( 'Overwrite Sections', 'page-generator-pro' ); ?></label>
		</div>
		<div class="full">		
			<ul class="checklist">                    			
				<?php
				foreach ( $overwrite_sections as $key => $label ) {
		            ?>
		            <li>
						<label>
							<input type="checkbox" name="<?php echo $this->base->plugin->name; ?>[overwrite_sections][<?php echo $key; ?>]" value="1"<?php echo ( isset( $this->settings['overwrite_sections'][ $key ] ) ? ' checked' : '' ); ?> />
							<?php echo $label; ?>
							<br />  
						</label>
					</li>
		            <?php
				}	
				?>
			</ul>
		</div>
		<p class="description">
			<?php
			echo sprintf( 
				/* translators: %1$s: Post Type, Singular, %2$s: Post Type, Singular */
				__( 'If generation would overwrite an existing %1$s, choose which items of the existing %2$s to overwrite.', 'page-generator-pro' ),
				$labels['singular'],
				$labels['singular']
			);
			?>
		</p>
	</div>
	<?php
}
?>

<div class="wpzinc-option sidebar">
	<div class="left">
		<label for="number_of_posts">
			<?php
			/* translators: Post Type, Plural */
			echo sprintf( __( 'No. %s', 'page-generator-pro' ), $labels['plural'] );
			?>
		</label>
	</div>
	<div class="right">
		<input type="number" name="<?php echo $this->base->plugin->name; ?>[numberOfPosts]" id="number_of_posts" value="<?php echo $this->settings['numberOfPosts']; ?>" step="1" min="0" class="widefat" />
	</div>
	<p class="description">
		<?php
		echo sprintf(
			/* translators: %1$s: Post Type, Plural, %2$s: Post Type, Plural */
			__( 'The number of %1$s to generate. If zero or blank, all %2$s will be generated.', 'page-generator-pro' ),
			$labels['plural'],
			$labels['plural']
		);
		?>
	</p>
</div>

<div class="wpzinc-option sidebar">
	<div class="left">
		<label for="resume_index"><?php _e( 'Resume Index', 'page-generator-pro' ); ?></label>
	</div>
	<div class="right">
		<input type="number" name="<?php echo $this->base->plugin->name; ?>[resumeIndex]" id="resume_index" value="<?php echo $this->settings['resumeIndex']; ?>" step="1" min="0" class="widefat" />
	</div>
	<div class="full">
		<a href="#" class="alignright wpzinc-populate-field-value" data-field="#resume_index" data-value="<?php echo $this->settings['last_index_generated']; ?>">
			<?php _e( 'Use Last Generated Index', 'page-generator-pro' ); ?>
		</a>
	</div>

	<p class="description">
		<?php _e( 'Optional: If generation did not fully complete (e.g. 50 / 100 only), or you specified a limit, you can set the Resume Index = 50.', 'page-generator-pro' ); ?>
	</p>
</div>