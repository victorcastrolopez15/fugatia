<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php echo $this->base->plugin->displayName; ?>

        <span>
        	<?php _e( 'Edit Keyword', 'page-generator-pro' ); ?>
        </span>
    </h1>

    <?php
    // Button Links
    require_once( 'keywords-links.php' );
    ?>
    
    <div class="wrap-inner">
	    <div id="poststuff">
	    	<div id="post-body" class="metabox-holder columns-1">
	    		<!-- Content -->
	    		<div id="post-body-content">
	    			<!-- Form Start -->
	    			<form class="<?php echo $this->base->plugin->name; ?>" name="post" method="post" action="admin.php?page=page-generator-pro-keywords&amp;cmd=form&id=<?php echo absint( $_GET['id'] ); ?>" enctype="multipart/form-data">		
		    	    	<div id="normal-sortables" class="meta-box-sortables ui-sortable">                        
			                <div id="keyword-panel" class="postbox">
			                	<!-- Keyword ID we're editing -->
			                	<input type="hidden" name="keywordID" value="<?php echo $keyword['keywordID']; ?>" />
			                	
			                    <h3 class="hndle"><?php _e( 'Keyword', 'page-generator-pro' ); ?></h3>
			                    
			                    <div class="wpzinc-option">
			                    	<div class="left">
			                    		<label for="keyword"><?php _e( 'Keyword', 'page-generator-pro' ); ?></label>
			                    	</div>
			                    	<div class="right">
			                    		<input type="text" name="keyword" id="keyword" value="<?php echo ( isset( $_POST['keyword'] ) ? esc_attr( $_POST['keyword'] ) : $keyword['keyword'] ); ?>" class="widefat" />
			                    	
				                    	<p class="description">
				                    		<?php _e( 'A unique template tag name, which can then be used when generating content.', 'page-generator-pro' ); ?>
				                    	</p>
			                    	</div>
			                    </div>

			                    <div class="wpzinc-option">
			                    	<div class="left">
			                    		<label for="source"><?php _e( 'Source', 'page-generator-pro' ); ?></label>
			                    	</div>
			                    	<div class="right">
			                    		<div class="right">
			                    			<?php echo $sources[ $keyword['source'] ]['label']; ?>
			                    			<input type="hidden" name="source" value="<?php echo $keyword['source']; ?>" />
			                    			<p class="description">
			                    				<?php _e( 'Once a Keyword is created, the source cannot be changed. To change the source, delete this Keyword and create a new Keyword with the same name.', 'page-generator-pro' ); ?>
				                    		</p>
				                    	</div>
			                    	</div>
			                    </div>

			                    <?php
			                    foreach ( $sources[ $keyword['source'] ]['options'] as $option_name => $option ) {
			                    	// Data, Delimiter and Column are stored outside of options, but are submitted as options
			                    	$value = '';
			                    	if ( isset( $_POST[ $keyword['source'] ][ $option_name ] ) ) {
			                    		$value = $_POST[ $keyword['source'] ][ $option_name ];

			                    		// Sanitize value depending on whether it's for a textarea or not
				                    	if ( $option['type'] == 'textarea' ) {
				                    		// Prevents stripping of newlines
				                    		$value = sanitize_textarea_field( $value );
				                    	} else {
				                    		$value = sanitize_text_field( $value );
				                    	}
			                    	} elseif ( isset( $_POST[ $option_name ] ) ) {
			                    		$value = $_POST[ $option_name ];

			                    		// Sanitize value depending on whether it's for a textarea or not
				                    	if ( $option['type'] == 'textarea' ) {
				                    		// Prevents stripping of newlines
				                    		$value = sanitize_textarea_field( $value );
				                    	} else {
				                    		$value = sanitize_text_field( $value );
				                    	}
			                    	} elseif ( isset( $keyword['options'][ $option_name ] ) ) {
			                    		$value = $keyword['options'][ $option_name ];
			                    	} elseif ( isset( $keyword[ $option_name ] ) ) {
			                    		$value = $keyword[ $option_name ];
			                    	}
                    				?>
                    				<div class="wpzinc-option">
			                    		<div class="left">
				                    		<label for="<?php echo $option_name; ?>"><?php echo $option['label']; ?></label>
				                    	</div>
				                    	<div class="right"<?php echo ( $option['type'] == 'preview' ? ' style="overflow-x:hidden;"' : '' ); ?>>
				                    		<?php
				                    		// Output Form Field
				                    		switch ( $option['type'] ) {
				                    			case 'text':
				                    				?>
				                    				<input type="text" name="<?php echo $keyword['source']; ?>[<?php echo $option_name; ?>]" id="<?php echo $option_name; ?>" value="<?php echo $value; ?>" class="widefat" />
	                    							<?php
				                    				break;

				                    			case 'url':
				                    				?>
				                    				<input type="url" name="<?php echo $keyword['source']; ?>[<?php echo $option_name; ?>]" id="<?php echo $option_name; ?>" value="<?php echo $value; ?>" class="widefat" />
	                    							<?php
				                    				break;

				                    			case 'number':
				                    				?>
				                    				<input type="number" name="<?php echo $keyword['source']; ?>[<?php echo $option_name; ?>]" id="<?php echo $option_name; ?>" value="<?php echo $value; ?>" class="widefat" />
	                    							<?php
				                    				break;

				                    			case 'toggle':
				                    				?>
				                    				<input type="checkbox" name="<?php echo $keyword['source']; ?>[<?php echo $option_name; ?>]" id="<?php echo $option_name; ?>" value="1"<?php echo ( $value ? ' checked' : '' ); ?> />
	                    							<?php
				                    				break;

				                    			case 'textarea':
				                    				?>
				                    				<textarea name="<?php echo $keyword['source']; ?>[<?php echo $option_name; ?>]" id="<?php echo $option_name; ?>" rows="10" class="widefat no-wrap" style="height:300px"><?php echo $value; ?></textarea>
	                    							<?php
				                    				break;

				                    			case 'file':
				                    				?>
				                    				<input type="file" name="<?php echo $keyword['source']; ?>[<?php echo $option_name; ?>]" id="<?php echo $option_name; ?>" />
				                    				<?php
				                    				break;

				                    			case 'select':
				                    				?>
				                    				<select name="<?php echo $keyword['source']; ?>[<?php echo $option_name; ?>]" size="1">
						                    			<?php
						                    			foreach ( $option['values'] as $key => $label ) {
						                    				?>
						                    				<option value="<?php echo $key; ?>"<?php selected( $key, $value ); ?>><?php echo $label; ?></option>
						                    				<?php
						                    			}
						                    			?>
						                    		</select>
						                    		<?php
				                    				break;

				                    			case 'media_library':
				                    				$file = get_attached_file( $value );
				                    				?>
				                    				<div class="wpzinc-media-library-selector"
							                        	data-input-name="<?php echo $keyword['source']; ?>[<?php echo $option_name; ?>]"
							                        	data-file-type="<?php echo $option['file_type']; ?>">

							                        	<ul>
							                        		<?php
									                        if ( $file ) {
									                        	?>
								                        		<li class="wpzinc-media-library-attachment">
								                        			<div class="wpzinc-media-library-insert">
								                        				<input type="hidden" name="<?php echo $keyword['source']; ?>[<?php echo $option_name; ?>]" value="<?php echo $value; ?>" />
								                            			<?php echo basename( $file ); ?>
								                        			</div>
								                        			
								                        			<a href="#" class="wpzinc-media-library-remove" title="<?php _e( 'Remove', 'wp-to-social-pro' ); ?>"><?php _e( 'Remove', 'wp-to-social-pro' ); ?></a>
								                        		</li>
								                        		<?php
								                        	}
								                        	?>
							                        	</ul>

							                        	<button class="wpzinc-media-library-insert button button-secondary">
												        	<?php _e( 'Choose File from Media Library', 'page-generator-pro' ); ?>
												        </button>
							                        </div>
				                    				<?php
				                    				break;

				                    			case 'preview':
				                    				?>
			                    					<table class="page-generator-pro-keywords-table widefat striped" style="width:100%;" data-keyword-id="<?php echo $keyword['keywordID']; ?>">
				                    					<thead>
				                    						<tr>
				                    							<?php
				                    							foreach ( $keyword['columnsArr'] as $column ) {
				                    								?>
				                    								<th><?php echo $column; ?></th>
				                    								<?php
				                    							}
				                    							?>
				                    						</tr>
				                    					</thead>
				                    				</table>
				                    				<?php
				                    				break;
				                    		}

				                    		// Output Description
				                    		if ( isset( $option['description'] ) ) {
				                    			?>
				                    			<p class="description">
				                    				<?php
				                    				if ( is_array( $option['description'] ) ) {
				                    					echo implode( '<br />', $option['description'] );
				                    				} else {
				                    					echo $option['description'];
				                    				}
				                    				?>
				                    			</p>
				                    			<?php
				                    		}
				                    		?>
				                    	</div>
			                    	</div>
                    				<?php
			                    }
			                    ?>

			                    <?php
								// Upgrade Notice
								if ( class_exists( 'Page_Generator' ) ) {
								    require( $this->base->plugin->folder . 'views/admin/keywords-form-upgrade.php' );
								}
								?>

			                    <div class="wpzinc-option">
		                    		<?php wp_nonce_field( 'save_keyword', 'nonce' ); ?>
		                			<input type="submit" name="submit" value="<?php _e( 'Save', 'page-generator-pro' ); ?>" class="button button-primary" />
			                    </div>
			                </div>
						</div>
						<!-- /normal-sortables -->
				    </form>
				    <!-- /form end -->
	    		</div>
	    		<!-- /post-body-content -->
	    	</div>
		</div>  
	</div>     
</div>