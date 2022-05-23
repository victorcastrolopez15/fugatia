<div id="author" class="wpzinc-option">
	<div class="left">
		<label for="author"><?php _e( 'Author', 'page-generator-pro' ); ?></strong>
	</div>
	<div class="right">
        <select name="<?php echo $this->base->plugin->name; ?>[author]" id="author" class="wpzinc-selectize-search" data-action="page_generator_pro_search_authors" data-name-field="user_login" data-value-field="id" data-method="POST" data-output-fields="user_login" data-nonce="<?php echo wp_create_nonce( 'search_authors' ); ?>">
            <?php
            if ( ! empty( $this->settings['author'] ) && $this->settings['author'] !== false ) {
                ?>
                <option value="<?php echo $this->settings['author']; ?>" selected><?php echo $author->user_login; ?></option>
                <?php
            }
            ?>
        </select>
	</div>	
</div>

<div class="wpzinc-option">
	<div class="left">
		<label for="rotate_authors"><?php _e( 'Random?', 'page-generator-pro' ); ?></label>
	</div>
	<div class="right">
		<input type="checkbox" id="rotate_authors" name="<?php echo $this->base->plugin->name; ?>[rotateAuthors]" value="1"<?php checked( $this->settings['rotateAuthors'], 1 ); ?> data-conditional="author" data-conditional-display="false" />
	
    	<p class="description">
    		<?php _e( 'If checked, will choose a WordPress User at random for each Page/Post generated.', 'page-generator-pro' ); ?>
    	</p>
	</div>
</div>