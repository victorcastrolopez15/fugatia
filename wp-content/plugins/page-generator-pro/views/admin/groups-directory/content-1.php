<h1><?php _e( 'Add New Directory Structure', 'page-generator-pro' ); ?></h1>
<p>
	<?php _e( 'This will generate the necessary Keywords and Content Groups to produce the following directory structure:', 'page-generator-pro' ); ?>
</p>
<div class="wpzinc-horizontal-selection options-2">
	<?php
	foreach ( $structures as $structure => $properties ) {
		?>
		<label for="structure_<?php echo $structure; ?>">
			<span><strong><?php echo $properties['title']; ?></strong></span>
			<input type="radio" name="structure" id="structure_<?php echo $structure; ?>" value="<?php echo $structure; ?>" <?php checked( $this->configuration['structure'], $structure ); ?> />
			<span class="description"><?php echo $properties['description']; ?></span>
		</label>
		<?php	
	}
	?>
</div>

<h1><?php _e( 'Services', 'page-generator-pro' ); ?></h1>
<p>
	<?php _e( 'Choose an existing Keyword to use as the list of services.  If this doesn\'t exist, select the <i>New Keyword</i> option and define the services below.', 'page-generator-pro' ); ?>
</p>
<div>
	<select name="service_keyword" size="1" data-conditional="services" data-conditional-display="false">
	    <option value=""><?php _e( '--- New Keyword ---', 'page-generator-pro' ); ?></option>
	    <?php
	    if ( is_array( $keywords ) && count( $keywords ) ) {
		    foreach ( $keywords as $keyword ) {
		        ?>
		        <option value="<?php echo $keyword; ?>"<?php selected( $this->configuration['service_keyword'], $keyword ); ?>><?php echo $keyword; ?></option>
		        <?php
		    }
		}
	    ?>
	</select>
</div>
<div id="services">
	<textarea name="services" rows="10" class="widefat" placeholder="<?php _e( 'One Service per line', 'page-generator-pro' ); ?>"><?php echo $this->configuration['services']; ?></textarea>
</div>

<h1><?php _e( 'Locations', 'page-generator-pro' ); ?></h1>
<p>
	<?php _e( 'Use the below to define the Locations keyword.', 'page-generator-pro' ); ?>
</p>

<div class="wpzinc-horizontal-selection options-2">
	<label for="method_radius">
		<span><strong><?php _e( 'Radius', 'page-generator-pro' ); ?></strong></span>
		<input type="radio" name="method" id="method_radius" value="radius" class="wpzinc-conditional" data-container="#wpzinc-onboarding-content" <?php checked( $this->configuration['method'], 'radius' ); ?> />
		<span class="description"><?php _e( 'Your business offers its services within a fixed radius from its address.' ); ?></span>
	</label>
	<label for="method_area">
		<span><strong><?php _e( 'Area', 'page-generator-pro' ); ?></strong></span>
		<input type="radio" name="method" id="method_area" value="area" class="wpzinc-conditional" data-container="#wpzinc-onboarding-content" <?php checked( $this->configuration['method'], 'area' ); ?> />
		<span class="description"><?php _e( 'Your business offers its services in specific Regions, States or Counties.' ); ?></span>
	</label>
</div>

<div>
	<label for="country_code"><?php _e( 'Country', 'page-generator-pro' ); ?> <span class="required">*</span></label>
	<select name="country_code" id="country_code" size="1">
		<?php
		foreach ( $countries as $country_code => $country_name ) {
			?>
			<option value="<?php echo $country_code; ?>"<?php selected( $this->configuration['country_code'], $country_code ); ?>><?php echo $country_name; ?></option>
			<?php
		}
		?>
	</select>
	<p class="description">
		<?php _e( 'Define the Country to fetch Locations from.', 'page-generator-pro' ); ?>
	</p>
</div>
<div>
	<div class="radius">
		<label for="radius"><?php _e( 'Radius', 'page-generator-pro' ); ?> <span class="required">*</span></label>
	    <input type="number" name="radius" min="0.1" max="99999" step="0.1" value="<?php echo $this->configuration['radius'] ?>" class="widefat" />
		<p class="description">
			<?php _e( 'Enter the number of miles from your Business Address that you serve.', 'page-generator-pro' ); ?>
		</p>
	</div>
	<div class="radius">
		<label for="zipcode"><?php _e( 'ZIP / Postal Code', 'page-generator-pro' ); ?> <span class="required">*</span></label>
		<input type="text" name="zipcode" id="zipcode" value="<?php echo $this->configuration['zipcode']; ?>" class="widefat" />
		<p class="description">
			<?php _e( 'Enter the ZIP / Postal Code to use as the starting point.', 'page-generator-pro' ); ?>
		</p>
	</div>
</div>
<div>
	<div class="area">
		<div>
		    <label for="region_id"><?php _e( 'Regions / States', 'page-generator-pro' ); ?></label>
			<select name="region_id[]" multiple="multiple" class="wpzinc-selectize" data-action="page_generator_pro_georocket" data-api-call="get_regions" data-country-code="country_code" data-value-field="id" data-output-fields="region_name,country_code" data-nonce="<?php echo wp_create_nonce( 'generate_locations' ); ?>">
		    </select>
		    <p class="description">
				<?php _e( 'Start typing the Regions / States that your business serves. Multiple Regions / States can be specified. If you serve in specific Counties, use the Counties option below.', 'page-generator-pro' ); ?>
			</p>
		</div>

		<div>
			<label for="county_id"><?php _e( 'Counties', 'page-generator-pro' ); ?></label>
			<select name="county_id[]" multiple="multiple" class="wpzinc-selectize-api" data-action="page_generator_pro_georocket" data-api-call="get_counties" data-api-search-field="county_name" data-api-fields="region_id[]" data-country-code="country_code" data-value-field="id" data-output-fields="county_name,region_name" data-nonce="<?php echo wp_create_nonce( 'generate_locations' ); ?>">
			</select>
			<p class="description">
				<?php _e( 'Start typing the Counties that your business serves. Multiple Counties can be specified. If you serve in Regions / States, use the Regions / States option above.', 'page-generator-pro' ); ?>
			</p>
		</div>
	</div>
</div>
