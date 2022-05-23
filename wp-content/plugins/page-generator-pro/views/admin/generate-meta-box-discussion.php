<div class="wpzinc-option">
	<div class="left">
		<label for="comments"><?php _e( 'Allow comments?', 'page-generator-pro' ); ?></label>
	</div>
	<div class="right">
		<input type="checkbox" id="comments" name="<?php echo $this->base->plugin->name; ?>[comments]" value="1"<?php checked( $this->settings['comments'], 1 ); ?> />
		
		<p class="description">
			<?php _e( 'If checked, a comments form will be displayed on every generated Page/Post.  It is your Theme\'s responsibility to honor this setting.', 'page-generator-pro' ); ?>
		</p>
	</div>
</div>
<div class="wpzinc-option">
	<div class="left">
		<label for="comments_generate"><?php _e( 'Generate Comments?', 'page-generator-pro' ); ?></label>
	</div>
	<div class="right">
		<input type="checkbox" id="comments_generate" name="<?php echo $this->base->plugin->name; ?>[comments_generate][enabled]" value="1" data-conditional="comments-generate" <?php checked( $this->settings['comments_generate']['enabled'], 1 ); ?> />
		
		<p class="description">
			<?php _e( 'If checked, options are displayed to generate comments with every generated Page/Post.', 'page-generator-pro' ); ?>
		</p>
	</div>
</div>

<div id="comments-generate">
	<div class="wpzinc-option">
		<div class="left">
			<label for="comments_generate_limit"><?php _e( 'No. Comments', 'page-generator-pro' ); ?></label>
		</div>
		<div class="right">
			<input type="number" id="comments_generate_limit" name="<?php echo $this->base->plugin->name; ?>[comments_generate][limit]" value="<?php echo $this->settings['comments_generate']['limit']; ?>" min="0" max="50" step="1" />
			
			<p class="description">
				<?php _e( 'The number of Comments to generate for each Page/Post generated. If zero or blank, a random number of Comments will be generated.', 'page-generator-pro' ); ?>
			</p>
		</div>
	</div>

	<div class="wpzinc-option">
		<div class="left">
			<label for="comments_generate_date_option"><?php _e( 'Date', 'page-generator-pro' ); ?></label>
		</div>
		<div class="right">
			<select name="<?php echo $this->base->plugin->name; ?>[comments_generate][date_option]" id="comments_generate_date_option" size="1" class="widefat">
				<?php
				if ( is_array( $date_options ) && count( $date_options ) > 0 ) {
					foreach ( $date_options as $date_option => $label ) {
						?>
						<option value="<?php echo $date_option; ?>"<?php selected( $this->settings['comments_generate']['date_option'], $date_option ); ?>>
							<?php echo $label; ?>
						</option>
						<?php
					}
				}
				?>
			</select>
		</div>
	</div>

	<div class="wpzinc-option specific">
		<div class="left">
			<label for="comments_generate_date_specific"><?php _e( 'Specific Date', 'page-generator-pro' ); ?></label>
		</div>
		<div class="right">
			<input type="date" name="<?php echo $this->base->plugin->name; ?>[comments_generate][date_specific]" id="comments_generate_date_specific" value="<?php echo $this->settings['comments_generate']['date_specific']; ?>" class="widefat" />
		
			<p class="description">
				<?php _e( 'Each generated comment will use this date as the comment date.', 'page-generator-pro' ); ?>
			</p>
		</div>
	</div>

	<div class="wpzinc-option random">
		<div class="left">
			<label for="comments_generate_date_min"><?php _e( 'Start', 'page-generator-pro' ); ?></label>
		</div>
		<div class="right">
			<input type="date" name="<?php echo $this->base->plugin->name; ?>[comments_generate][date_min]" id="comments_generate_date_min" value="<?php echo $this->settings['comments_generate']['date_min']; ?>" />
		</div>
	</div>
	<div class="wpzinc-option random">
		<div class="left">
			<label for="comments_generate_date_max"><?php _e( 'End', 'page-generator-pro' ); ?></label>
		</div>
		<div class="right">
			<input type="date" name="<?php echo $this->base->plugin->name; ?>[comments_generate][date_max]" id="comments_generate_date_max" value="<?php echo $this->settings['comments_generate']['date_max']; ?>" />
		
			<p class="description">
				<?php _e( 'Each generated comment will use a date and time between the above minimum and maximum dates.', 'page-generator-pro' ); ?>
			</p>
		</div>
	</div>

	<div class="wpzinc-option">
		<div class="left">
			<label for="comments_generate_firstname"><?php _e( 'First Name', 'page-generator-pro' ); ?></label>
		</div>
		<div class="right">
			<input type="text" id="comments_generate_firstname" name="<?php echo $this->base->plugin->name; ?>[comments_generate][firstname]" value="<?php echo $this->settings['comments_generate']['firstname']; ?>" class="widefat" />
			
			<p class="description">
				<?php _e( 'The Author\'s First Name for each Generated Comment. Supports Keywords and Spintax. We recommend using a Keyword comprising of all First Names, and using {keyword:random_different} to generate a random first name for each generated Comment.', 'page-generator-pro' ); ?>
			</p>
		</div>
	</div>

	<div class="wpzinc-option">
		<div class="left">
			<label for="comments_generate_firstname"><?php _e( 'Surname', 'page-generator-pro' ); ?></label>
		</div>
		<div class="right">
			<input type="text" id="comments_generate_surname" name="<?php echo $this->base->plugin->name; ?>[comments_generate][surname]" value="<?php echo $this->settings['comments_generate']['surname']; ?>" class="widefat" />
			
			<p class="description">
				<?php _e( 'The Author\'s Surname for each Generated Comment. Supports Keywords and Spintax. We recommend using a Keyword comprising of all Surnames, and using {keyword:random_different} to generate a random surname for each generated Comment.', 'page-generator-pro' ); ?>
			</p>
		</div>
	</div>

	<div class="wpzinc-option">
		<div class="left">
			<label for="comments_generate_comment"><?php _e( 'Comment', 'page-generator-pro' ); ?></label>
		</div>
		<div class="right">
			<textarea id="comments_generate_comment" name="<?php echo $this->base->plugin->name; ?>[comments_generate][comment]" class="widefat"><?php echo $this->settings['comments_generate']['comment']; ?></textarea>
			
			<p class="description">
				<?php _e( 'The Comment Text for each Generated Comment. Supports Keywords and Spintax.', 'page-generator-pro' ); ?>
			</p>
		</div>
	</div>
</div>
<div class="wpzinc-option">
	<div class="left">
		<label for="trackbacks"><?php _e( 'Allow track / pingbacks?', 'page-generator-pro' ); ?></label>
	</div>
	<div class="right">
		<input type="checkbox" id="trackbacks" name="<?php echo $this->base->plugin->name; ?>[trackbacks]" value="1"<?php checked( $this->settings['trackbacks'], 1 ); ?> />
	</div>
</div>