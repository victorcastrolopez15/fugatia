<?php
/**
 * Outputs the licensing screen.
 *
 * @package LicensingUpdateManager
 * @author WP Zinc
 */

?>
<div class="wrap">
	<h1 class="wp-heading-inline">
		<?php echo esc_html( $this->base->plugin->displayName ); ?>

		<span>
			<?php esc_html_e( 'Licensing', $this->base->plugin->name ); /* phpcs:ignore */ ?>
		</span>
	</h1>

	<?php
	// Notices.
	if ( isset( $this->message ) ) {
		?>
		<div class="updated notice"><p><?php echo $this->message; /* phpcs:ignore */ ?></p></div>  
		<?php
	}
	if ( isset( $this->errorMessage ) ) { /* phpcs:ignore */
		?>
		<div class="error notice"><p><?php echo $this->errorMessage; /* phpcs:ignore */ ?></p></div>  
		<?php
	}
	?>


	<div class="wrap-inner">
		<div id="poststuff">
			<?php require_once 'licensing-inline.php'; ?>
		</div>
	</div><!-- /.wrap-inner -->
</div><!-- /.wrap -->
