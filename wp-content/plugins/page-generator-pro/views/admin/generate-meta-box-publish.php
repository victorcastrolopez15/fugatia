<div class="wpzinc-option sidebar">
	<div class="left">
		<label for="type"><?php _e( 'Post Type', 'page-generator-pro' ); ?></label>
	</div>
	<div class="right">
		<select name="<?php echo $this->base->plugin->name; ?>[type]" id="type" size="1" class="widefat">
			<?php
			if ( is_array( $post_types ) && count( $post_types ) > 0 ) {
				foreach ( $post_types as $type => $post_type ) {
					?>
					<option value="<?php echo $type; ?>"<?php selected( $this->settings['type'], $type ); ?>>
						<?php echo $post_type->labels->singular_name; ?>
					</option>
					<?php
				}
			}
			?>
		</select>
	</div>
	<p class="description">
		<?php _e( 'The Post Type to create when generating content, such as a Page or Post', 'page-generator-pro' ); ?>
	</p>
</div>

<div class="wpzinc-option sidebar">
	<div class="left">
		<label for="status"><?php _e( 'Status', 'page-generator-pro' ); ?></label>
	</div>
	<div class="right">
		<select name="<?php echo $this->base->plugin->name; ?>[status]" id="status" size="1" class="widefat">
			<?php
			if ( is_array( $statuses ) && count( $statuses ) > 0 ) {
				foreach ( $statuses as $status => $label ) {
					?>
					<option value="<?php echo $status; ?>"<?php selected( $this->settings['status'], $status ); ?>>
						<?php echo $label; ?>
					</option>
					<?php
				}
			}
			?>
		</select>
	</div>
</div>

<div class="wpzinc-option sidebar">
	<div class="left">
		<label for="date_option"><?php _e( 'Date', 'page-generator-pro' ); ?></label>
	</div>
	<div class="right">
		<select name="<?php echo $this->base->plugin->name; ?>[date_option]" id="date_option" size="1" class="widefat">
			<?php
			if ( is_array( $date_options ) && count( $date_options ) > 0 ) {
				foreach ( $date_options as $date_option => $label ) {
					?>
					<option value="<?php echo $date_option; ?>"<?php selected( $this->settings['date_option'], $date_option ); ?>>
						<?php echo $label; ?>
					</option>
					<?php
				}
			}
			?>
		</select>
	</div>
</div>

<div class="wpzinc-option sidebar specific">
	<div class="full">
		<label for="date_specific"><?php _e( 'Specific Date', 'page-generator-pro' ); ?></label>
	</div>
	<div class="full">
		<input type="datetime-local" name="<?php echo $this->base->plugin->name; ?>[date_specific]" id="date_specific" value="<?php echo $this->settings['date_specific']; ?>" class="widefat" />
	</div>
</div>

<div class="wpzinc-option sidebar random">
	<div class="full">
		<label for="date_min"><?php _e( 'Start', 'page-generator-pro' ); ?></label>
	</div>
	<div class="full">
		<input type="date" name="<?php echo $this->base->plugin->name; ?>[date_min]" id="date_min" value="<?php echo $this->settings['date_min']; ?>" />
	</div>

	<div class="full">
		<label for="date_max"><?php _e( 'End', 'page-generator-pro' ); ?></label>
	</div>
	<div class="full">
		<input type="date" name="<?php echo $this->base->plugin->name; ?>[date_max]" id="date_max" value="<?php echo $this->settings['date_max']; ?>" />
	</div>

	<p class="description">
		<?php _e( 'Each generated page will use a date and time between the above minimum and maximum dates.', 'page-generator-pro' ); ?>
	</p>
</div>

<!-- Schedule Options -->
<div class="wpzinc-option sidebar future">
	<div class="full">
		<label for="schedule"><?php _e( 'Schedule Increment', 'page-generator-pro' ); ?></label>
	</div>
	<div class="full">
		<input type="number" class="small-text" name="<?php echo $this->base->plugin->name; ?>[schedule]" id="schedule" value="<?php echo $this->settings['schedule']; ?>" step="1" min="1" />
		<select name="<?php echo $this->base->plugin->name; ?>[scheduleUnit]" size="1">
			<?php
			if ( is_array( $schedule_units ) && count( $schedule_units ) > 0 ) {
				foreach ( $schedule_units as $unit => $label ) {
					?>
					<option value="<?php echo $unit; ?>"<?php selected( $this->settings['scheduleUnit'], $unit ); ?>>
						<?php echo $label; ?>
					</option>
					<?php
				}
			}
			?>
		</select>
	</div>

	<p class="description">
		<?php _e( 'The first generated Page’s date and time will be based on the Date setting (i.e. now or a specified date and time), plus the increment.', 'page-generator-pro' ); ?>
		<br />
		<?php _e( 'Second and subsequent Pages’ date and time will be based on the previous generated Page’s date and time, plus the increment.', 'page-generator-pro' ); ?>
	</p>
</div>