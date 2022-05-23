<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php echo $this->base->plugin->displayName; ?>

        <span>
        	<?php _e( 'Import CSV or Spreadsheet', 'page-generator-pro' ); ?>
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
	    			<form name="post" method="post" action="admin.php?page=page-generator-pro-keywords&amp;cmd=form-import-file" enctype="multipart/form-data">		
		    			<div id="normal-sortables" class="meta-box-sortables ui-sortable">                        
			                <div id="keyword-panel" class="postbox">
			                    <h3 class="hndle"><?php _e( 'Import CSV or Spreadsheet', 'page-generator-pro' ); ?></h3>
		
			                    <div class="wpzinc-option">
			                    	<div class="left">
			                    		<strong><?php _e( 'File', 'page-generator-pro' ); ?></strong>
			                    	</div>
			                    	<div class="right">
			                    		<input type="file" name="file" />

			                    		<p class="description">
			                    			<?php _e( '.csv and .xlsx files are supported', 'page-generator-pro' ); ?>
			                    		</p>
			                    	</div>
			                    </div>

			                    <div class="wpzinc-option">
			                    	<div class="left">
			                    		<strong><?php _e( 'Import Method', 'page-generator-pro' ); ?></strong>
			                    	</div>
			                    	<div class="right">
			                    		<div class="wpzinc-horizontal-selection options-4">
				                    		<label for="columns_single_keyword">
				                    			<div class="image">
				                    				<img src="https://www.wpzinc.com/wp-content/uploads/2018/08/page-generator-pro-import-columns-single-keyword.png" />
				                    			</div>

				                    			<span><?php _e( 'Import Columns into a single Keyword', 'page-generator-pro' ); ?></span>

				                    			<input type="radio" name="method" id="columns_single_keyword" value="columns_single_keyword" class="wpzinc-conditional" data-container="#keyword-panel" checked />
				                    		</label>

				                    		<label for="columns_multiple_keywords">
				                    			<div class="image">
				                    				<img src="https://www.wpzinc.com/wp-content/uploads/2018/08/page-generator-pro-import-columns-multiple-keywords.png" />
				                    			</div>

				                    			<span><?php _e( 'Import Columns as multiple Keywords', 'page-generator-pro' ); ?></span>

				                    			<input type="radio" name="method" id="columns_multiple_keywords" value="columns_multiple_keywords" class="wpzinc-conditional" data-container="#keyword-panel" />
				                    		</label>

				                    		<label for="rows_single_keyword">
				                    			<div class="image">
				                    				<img src="https://www.wpzinc.com/wp-content/uploads/2018/08/page-generator-pro-import-keywords-rows-single-keyword.png" />
				                    			</div>

				                    			<span><?php _e( 'Import Rows into a single Keyword', 'page-generator-pro' ); ?></span>

				                    			<input type="radio" name="method" id="rows_single_keyword" value="rows_single_keyword" class="wpzinc-conditional" data-container="#keyword-panel" />
				                    		</label>

				                    		<label for="rows_multiple_keywords">
				                    			<div class="image">
				                    				<img src="https://www.wpzinc.com/wp-content/uploads/2018/08/page-generator-pro-import-rows-multiple-keywords.png" />
				                    			</div>

				                    			<span><?php _e( 'Import Rows as multiple Keywords', 'page-generator-pro' ); ?></span>

				                    			<input type="radio" name="method" id="rows_multiple_keywords" value="rows_multiple_keywords" class="wpzinc-conditional" data-container="#keyword-panel" />
				                    		</label>
				                    	</div>

				                    	<p class="description">
				                    		<?php _e( 'Choose an option above, based your CSV / spreadsheet file\'s structure and how you want to store the imported data.', 'page-generator-pro' ); ?>
				                    	</p>
			                    	</div>
			                    </div>

			                    <div class="wpzinc-option">
			                    	<div class="left">
			                    		<strong><?php _e( 'Delimiter', 'page-generator-pro' ); ?></strong>
			                    	</div>
			                    	<div class="right">
			                    		<input type="text" name="delimiter" value="," class="widefat" />
			                    		<p class="description">
			                    			<?php _e( 'For CSV Files, the delimiter used in the CSV file to separate cells.', 'page-generator-pro' ); ?>
			                    		</p>
			                    	</div>
			                    </div>

			                    <!-- HTML structure deliberate for compat. with conditional-fields.js -->
			                    <div>
				                    <div class="columns_single_keyword rows_single_keyword">
				                    	<div class="wpzinc-option">
					                    	<div class="left">
					                    		<strong><?php _e( 'Keyword Name', 'page-generator-pro' ); ?></strong>
					                    	</div>
					                    	<div class="right">
					                    		<input type="text" name="keyword" value="" class="widefat" />
					                    	</div>
					                    </div>
				                    </div>
				                </div>

			                    <div class="wpzinc-option">
		                    		<?php wp_nonce_field( 'import_file', $this->base->plugin->name . '_nonce' ); ?>
		                			<input type="submit" name="submit" value="<?php _e( 'Import', 'page-generator-pro' ); ?>" class="button button-primary" />
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