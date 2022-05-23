<div id="wpzinc-onboarding">
	<div class="wrap">
		<?php
		// Output Progress Bar / Bullets
		include( 'progress.php' );

		// Output Success and/or Error Notices, if any exist
	    $this->base->get_class( 'notices' )->output_notices();
	    ?>

	    <div class="js-notices"></div>
	</div>
	
	<form action="admin.php?page=<?php echo esc_attr( $this->base->plugin->name ); ?>-groups-directory&step=<?php echo esc_attr( $this->step ); ?>" method="POST" id="wpzinc-onboarding-form">
		<div id="wpzinc-onboarding-content">
			<?php include( 'content-' . $this->step . '.php' ); ?>
		</div>

		<div id="wpzinc-onboarding-footer">
			<?php
			if ( isset( $back_button_label ) ) {
				?>
				<div class="left">
					<a href="<?php echo $back_button_url; ?>" class="button"><?php echo $back_button_label; ?></a>
				</div>
				<?php
			}

			if ( isset( $next_button_label ) ) {
				?>
				<div class="right">
					<input type="hidden" name="configuration" value='<?php echo json_encode( $this->configuration, JSON_HEX_APOS ); ?>' />
					<?php wp_nonce_field( $this->base->plugin->name, $this->base->plugin->name . '_nonce' ); ?>
					<button class="button button-primary button-large"><?php echo $next_button_label; ?></button>
				</div>
				<?php
			}
			?>
		</div>
	</form>
</div>