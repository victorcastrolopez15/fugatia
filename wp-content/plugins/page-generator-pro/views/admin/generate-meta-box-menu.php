 <div class="wpzinc-option sidebar">
	<div class="left">
		<label for="menu"><?php _e( 'Menu', 'page-generator-pro' ); ?></label>
	</div>
	<div class="right">
		<select name="<?php echo $this->base->plugin->name; ?>[menu]" id="menu" size="1" class="widefat">
			<option value="0"<?php selected( $this->settings['menu'], 0 ); ?>>
				<?php _e( '(none)', 'page-generator-pro' ); ?>
			</option>
			<?php
			if ( is_array( $menus ) && count( $menus ) > 0 ) {
				foreach ( $menus as $menu ) {
					?>
					<option value="<?php echo $menu->term_id; ?>"<?php selected( $this->settings['menu'], $menu->term_id ); ?>>
						<?php echo $menu->name; ?>
					</option>
					<?php
				}
			}
			?>
		</select>
	</div>
	<p class="description">
		<?php _e( 'If defined, generated Pages will be added to this WordPress Menu.', 'page-generator-pro' ); ?>
		<br />
		<?php
		echo sprintf(
			/* translators: Link to Appearance > Menus */
			__( 'To display a Menu in your Theme, see %s', 'page-generator-pro' ),
			'<a href="nav-menus.php">' . __( 'Appearance > Menus', 'page-generator-pro' ) . '</a>'
		);
		?>
		<br />
		<?php _e( 'In Test Mode, the generated Page will <strong>not</strong> be assigned to this Menu.', 'page-generator-pro' ); ?>
	</p>
</div>

<div class="wpzinc-option sidebar">
	<div class="left">
		<label for="menu_title"><?php _e( 'Menu Title', 'page-generator-pro' ); ?></label>
	</div>
	<div class="right">
		<input type="text" name="<?php echo $this->base->plugin->name; ?>[menu_title]" id="menu_title" value="<?php echo $this->settings['menu_title']; ?>" class="widefat" />
	</div>
	<p class="description">
		<?php _e( 'If defined, generated Pages will have the above title set in the Menu.', 'page-generator-pro' ); ?>
		<br />
		<?php _e( 'If empty, the generated Page title will be used.', 'page-generator-pro' ); ?>
		<br />
		<?php _e( 'Keywords and Spintax are supported.', 'page-generator-pro' ); ?>
	</p>
</div>

<div class="wpzinc-option sidebar">
	<div class="left">
		<label for="menu_parent"><?php _e( 'Menu Parent', 'page-generator-pro' ); ?></label>
	</div>
	<div class="right">
		<input type="text" name="<?php echo $this->base->plugin->name; ?>[menu_parent]" id="menu_parent" value="<?php echo $this->settings['menu_parent']; ?>" class="widefat" />
	</div>
	<p class="description">
		<?php _e( 'To make generated Menu items the child of an existing Menu item, enter the parent Menu Item Title or ID here.', 'page-generator-pro' ); ?>
		<br />
		<a href="<?php echo $this->base->plugin->documentation_url; ?>/generate-content/#fields--menu" rel="noopener" target="_blank">
    		<?php _e( 'How to find the Parent Menu ID', 'page-generator-pro' ); ?>
    	</a>
	</p>
</div>