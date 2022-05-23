<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php echo $this->base->plugin->displayName; ?>

        <span>
        	<?php _e( 'Generate Locations', 'page-generator-pro' ); ?>
        </span>
    </h1>

    <?php
    // Button Links
    require_once( 'keywords-links.php' );
    ?>

    <!-- Container for JS notices -->
    <div class="js-notices"></div>

    <div class="wrap-inner">
	    <div id="poststuff">
	    	<div id="post-body" class="metabox-holder columns-1">
	    		<!-- Content -->
	    		<div id="post-body-content">
	    			<!-- Form Start -->
			        <form name="post" method="post" action="admin.php?page=page-generator-pro-keywords&amp;cmd=form-locations" enctype="multipart/form-data" id="keywords-generate-locations">		
		    		    <div id="normal-sortables" class="meta-box-sortables ui-sortable">                        
			                <div id="keyword-panel" class="postbox">
			                    <h3 class="hndle"><?php _e( 'Keyword', 'page-generator-pro' ); ?></h3>
			                    
		 						<div class="wpzinc-option">
			                    	<div class="left">
			                    		<strong><?php _e( 'Keyword', 'page-generator-pro' ); ?></strong>
			                    	</div>
			                    	<div class="right">
			                    		<input type="text" name="keyword" value="<?php echo ( isset( $_POST['keyword'] ) ? $_POST['keyword'] : '' ); ?>" class="widefat" required />
			                    		<input type="hidden" name="keyword_id" value="" />
			                    		
				                    	<p class="description">
				                    		<?php _e( 'A unique template tag name, which can then be used when generating content.', 'page-generator-pro' ); ?>
				                    	</p>
				                    </div>
			                    </div>

			                    <div class="wpzinc-option">
			                    	<div class="left">
			                    		<strong><?php _e( 'Output Type', 'page-generator-pro' ); ?></strong>
			                    	</div>

			                    	<div class="right">
			                    		<select name="output_type[]" multiple="multiple" class="wpzinc-selectize-drag-drop" data-controls="orderby">
			                    			<?php
			                    			foreach ( $output_types as $output_type => $label ) {
			                    				?>
			                    				<option value="<?php echo $output_type; ?>"<?php echo ( ( isset( $_POST['output_type'] ) && $_POST['output_type'] == $output_type ) ? ' selected' : '' ); ?>><?php echo $label; ?></option>
			                    				<?php
			                    			}
			                    			?>
			                    		</select>
			                    	
				                    	<p class="description">
				                    		<?php _e( 'Determine the data to store in this Keyword for each Location (for example, just the city, the city and zip code or city, county and region).', 'page-generator-pro' ); ?>
				                    	</p>
			                    	</div>
			                    </div>

			                    <div class="wpzinc-option">
			                    	<div class="left">
			                    		<strong><?php _e( 'Sort Terms By', 'page-generator-pro' ); ?></strong>
			                    	</div>

			                    	<div class="right">
			                    		<select name="orderby" size="1">
			                    			<?php
			                    			foreach ( $order_by_options as $order_by => $label ) {
			                    				?>
			                    				<option value="<?php echo $order_by; ?>"<?php echo ( ( isset( $_POST['orderby'] ) && $_POST['orderby'] == $order_by ) ? ' selected' : '' ); ?>><?php echo $label; ?></option>
			                    				<?php
			                    			}
			                    			?>
			                    		</select>
			                    		<select name="order" size="1">
			                    			<?php
			                    			foreach ( $order_options as $order => $label ) {
			                    				?>
			                    				<option value="<?php echo $order; ?>"<?php echo ( ( isset( $_POST['order'] ) && $_POST['order'] == $order ) ? ' selected' : '' ); ?>><?php echo $label; ?></option>
			                    				<?php
			                    			}
			                    			?>
			                    		</select>

				                    	<p class="description">
				                    		<?php _e( 'Define the order in which Generated Locations Terms are stored.', 'page-generator-pro' ); ?>
				                    	</p>
			                    	</div>
			                    </div>

			                    <div class="wpzinc-option">
			                    	<div class="left">
			                    		<strong><?php _e( 'Country', 'page-generator-pro' ); ?></strong>
			                    	</div>
			                    	<div class="right">
			                    		<select name="country_code" size="1">
			                    			<?php
			                    			$setting = ( isset( $_POST['country'] ) ? sanitize_text_field( $_POST['country'] ) : $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-general', 'country_code', 'US' ) );
			                    			foreach ( $countries as $country_code => $country_name ) {
			                    				?>
			                    				<option value="<?php echo $country_code; ?>"<?php selected( $setting, $country_code ); ?>><?php echo $country_name; ?></option>
			                    				<?php
			                    			}
			                    			?>
			                    		</select>
			                    	
				                    	<p class="description">
				                    		<?php _e( 'Limit Locations to within the given Country.', 'page-generator-pro' ); ?>
				                    	</p>
			                    	</div>
			                    </div>

			                    <div class="wpzinc-option">
			                    	<div class="left">
			                    		<strong><?php _e( 'Method', 'page-generator-pro' ); ?></strong>
			                    	</div>
			                    	<div class="right">
			                    		<select name="method" size="1" data-conditional="radius">
			                    			<?php
			                    			$setting = ( isset( $_POST['method'] ) ? sanitize_text_field( $_POST['method'] ) : $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-georocket', 'method', 'radius' ) );
			                    			foreach ( $methods as $method => $label ) {
			                    				?>
			                    				<option value="<?php echo $method; ?>"<?php selected( $setting, $method ); ?>><?php echo $label; ?></option>
			                    				<?php
			                    			}
			                    			?>
			                    		</select>
			                    	
				                    	<p class="description">
				                    		<?php _e( 'Determines how to build a list of location terms for this Keyword.', 'page-generator-pro' ); ?><br />
				                    		
				                    		<strong><?php _e( 'Radius', 'page-generator-pro' ); ?></strong>
				                    		<?php _e( 'The Keyword will be populated with Locations falling within the given radius from the given starting point.  This method is useful if, for example, your product or service targets a specific mileage radius from a central location.', 'page-generator-pro' ); ?>
				                    		<br />

											<strong><?php _e( 'Area', 'page-generator-pro' ); ?></strong>
				                    		<?php _e( 'The Keyword will be populated with Locations falling within the given City, County and/or Region.  This method is useful if, for example, your product or service targets a specific City, County or Region.', 'page-generator-pro' ); ?>
				                    	</p>
			                    	</div>
			                    </div>

			                    <div class="radius">
			                    	<div class="wpzinc-option">
				                    	<div class="left">
				                    		<strong><?php _e( 'Starting City / ZIP Code', 'page-generator-pro' ); ?></strong>
				                    	</div>
				                    		
				                    	<div class="right">
				                    		<input type="text" name="location" value="<?php echo ( isset( $_POST['location'] ) ? $_POST['location'] : '' ); ?>" class="widefat" />
				                    	
					                    	<p class="description">
					                    		<?php _e( 'Enter the city or zip code to use as the starting point to generate nearby cities / zip codes from.', 'page-generator-pro' ); ?>
					                    	</p>
				                    	</div>
				                    </div>

				                    <div class="wpzinc-option">
				                    	<div class="left">
				                    		<strong><?php _e( 'Radius', 'page-generator-pro' ); ?></strong>
				                    	</div>
				                    		
				                    	<div class="right">
				                    		<input type="number" name="radius" min="0.1" max="99999" step="0.1" value="<?php echo ( isset( $_POST['radius'] ) ? sanitize_text_field( $_POST['radius'] ) : $this->base->get_class( 'settings' )->get_setting( $this->base->plugin->name . '-georocket', 'radius', '' ) ); ?>" class="widefat" />
				                    	
					                    	<p class="description">
					                    		<?php _e( 'Enter the number of miles to fetch all nearby cities, counties, regions and/or ZIP codes from the Starting City / ZIP Code above.', 'page-generator-pro' ); ?>
					                    	</p>
				                    	</div>
				                    </div>
			                    </div>

			                    <div class="area">
			                    	<div class="wpzinc-option">
				                    	<div class="left">
				                    		<strong><?php _e( 'Restrict by Region(s)', 'page-generator-pro' ); ?></strong>
				                    	</div>
				                    		
				                    	<div class="right">
				                    		<select name="region_id[]" multiple="multiple" class="wpzinc-selectize" data-action="page_generator_pro_georocket" data-api-call="get_regions" data-country-code="country_code" data-value-field="id" data-output-fields="region_name,country_code" data-nonce="<?php echo wp_create_nonce( 'generate_locations' ); ?>">
				                    		</select>

					                    	<p class="description">
					                    		<?php _e( 'Limit Terms to the given Region / State Name(s). Begin typing to see valid Region / State Names.', 'page-generator-pro' ); ?>
					                    	</p>
				                    	</div>
				                    </div>

				                    <div class="wpzinc-option">
				                    	<div class="left">
				                    		<strong><?php _e( 'Restrict by County / Counties', 'page-generator-pro' ); ?></strong>
				                    	</div>
				                    		
				                    	<div class="right">
				                    		<select name="county_id[]" multiple="multiple" class="wpzinc-selectize-api" data-action="page_generator_pro_georocket" data-api-call="get_counties" data-api-search-field="county_name" data-api-fields="region_id[]" data-country-code="country_code" data-value-field="id" data-output-fields="county_name,region_name" data-nonce="<?php echo wp_create_nonce( 'generate_locations' ); ?>">
				                    		</select>

					                    	<p class="description">
					                    		<?php _e( 'Limit Terms to the given County Name(s). Begin typing to see valid County Names.', 'page-generator-pro' ); ?>
					                    	</p>
				                    	</div>
				                    </div>

				                    <div class="wpzinc-option">
				                    	<div class="left">
				                    		<strong><?php _e( 'Restrict by City / Cities', 'page-generator-pro' ); ?></strong>
				                    	</div>
				                    		
				                    	<div class="right">
				                    		<select name="city_id[]" multiple="multiple" class="wpzinc-selectize-api" data-action="page_generator_pro_georocket" data-api-call="get_cities" data-api-search-field="city_name" data-api-fields="region_id[],county_id[]" data-country-code="country_code" data-value-field="id" data-output-fields="city_name,county_name,region_name" data-nonce="<?php echo wp_create_nonce( 'generate_locations' ); ?>">
				                    		</select>

					                    	<p class="description">
					                    		<?php _e( 'Limit Terms to the given City Name(s). Begin typing to see valid City Names.  If you have specified Restrict by Region(s) and/or Counties above, the City results listed will be limited to those Regions and/or Counties.', 'page-generator-pro' ); ?>
					                    	</p>
				                    	</div>
				                    </div>

				                    <div class="wpzinc-option">
				                    	<div class="left">
				                    		<strong><?php _e( 'Exclusions', 'page-generator-pro' ); ?></strong>
				                    	</div>
				                    		
				                    	<div class="right">
				                    		<input type="text" name="exclusions" class="widefat wpzinc-selectize-freeform" />

					                    	<p class="description">
					                    		<?php _e( 'Optional: Define Cities, Counties or Regions to exclude from the results.', 'page-generator-pro' ); ?>
					                    	</p>
				                    	</div>
				                    </div>
				                </div>

				                <div class="wpzinc-option">
			                    	<div class="left">
			                    		<strong><?php _e( 'Restrict by City Population', 'page-generator-pro' ); ?></strong>
			                    	</div>
			                    		
			                    	<div class="right">
			                    		<input type="number" name="population_min" min="0" max="99999999" value="" placeholder="<?php _e( 'Min.', 'page-generator-pro' ); ?>" />
			                    		-
			                    		<input type="number" name="population_max" min="0" max="99999999" value="" placeholder="<?php _e( 'Max.', 'page-generator-pro' ); ?>" />
			
				                    	<p class="description">
				                    		<?php _e( 'Limit Terms to Cities within the given Population Limits.  Leave blank to specify no limit.', 'page-generator-pro' ); ?>
				                    	</p>
			                    	</div>
			                    </div>

			                    <div class="wpzinc-option">
			                    	<div class="left">
			                    		<strong><?php _e( 'Restrict by City Median Household Income', 'page-generator-pro' ); ?></strong>
			                    	</div>
			                    		
			                    	<div class="right">
			                    		<input type="number" name="median_household_income_min" min="0" max="99999999" value="" placeholder="<?php _e( 'Min.', 'page-generator-pro' ); ?>" />
			                    		-
			                    		<input type="number" name="median_household_income_max" min="0" max="99999999" value="" placeholder="<?php _e( 'Max.', 'page-generator-pro' ); ?>" />
			
				                    	<p class="description">
				                    		<?php _e( 'Limit Terms to Cities within the given Median Household Income Limits.  Leave blank to specify no limit.', 'page-generator-pro' ); ?>
				                    	</p>
			                    	</div>
			                    </div>

			                    <div class="wpzinc-option">
			                    	<?php wp_nonce_field( 'generate_locations', 'nonce' ); ?>
			                		<input type="submit" name="submit" value="<?php _e( 'Generate Keyword with Locations', 'page-generator-pro' ); ?>" class="button button-primary" />
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