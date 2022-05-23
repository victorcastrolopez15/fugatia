<div class="wpzinc-option">
	<div class="left">
		<label for="keywords"><?php _e( 'Settings', 'page-generator-pro' ); ?></label>
	</div>
	<div class="right">
		<input type="checkbox" name="settings" value="1" checked />
	</div>
</div>

<?php
// Keywords
if ( isset( $keywords ) && is_array( $keywords ) ) {
	?>
	<div class="wpzinc-option">
		<div class="left">
			<label><?php _e( 'Keywords', 'page-generator-pro' ); ?></label><br />
			<a href="#" class="wpzinc-checkbox-toggle" data-target="keyword"><?php _e( 'Select / Deselect All', 'page-generator-pro' ); ?></a>
		</div>
		<div class="right">
			<div class="tax-selection">
				<div class="tabs-panel">
					<ul class="categorychecklist form-no-clear">				                    			
						<?php
						foreach ( $keywords as $keyword ) {
	                        ?>
	                        <li>
								<label class="selectit">
									<input type="checkbox" name="keywords[<?php echo $keyword->keywordID; ?>]" value="1" class="keyword" checked />
									<?php echo $keyword->keyword; ?>      
								</label>
							</li>
	                        <?php
						}	
						?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<?php
}

// Content Groups
if ( isset( $content_groups ) && is_array( $content_groups ) ) {
	?>
	<div class="wpzinc-option">
		<div class="left">
			<label><?php _e( 'Content Groups', 'page-generator-pro' ); ?></label><br />
			<a href="#" class="wpzinc-checkbox-toggle" data-target="content-group"><?php _e( 'Select / Deselect All', 'page-generator-pro' ); ?></a>
		</div>
		<div class="right">
			<div class="tax-selection">
				<div class="tabs-panel">
					<ul class="categorychecklist form-no-clear">				                    			
						<?php
						foreach ( $content_groups as $group_id => $group ) {
	                        ?>
	                        <li>
								<label class="selectit">
									<input type="checkbox" name="content_groups[<?php echo $group_id; ?>]" value="1" class="content-group" checked />
									<?php echo $group['title']; ?>      
								</label>
							</li>
	                        <?php
						}	
						?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<?php
}

// Term Groups
if ( isset( $term_groups ) && is_array( $term_groups ) ) {
	?>
	<div class="wpzinc-option">
		<div class="left">
			<label><?php _e( 'Term Groups', 'page-generator-pro' ); ?></label><br />
			<a href="#" class="wpzinc-checkbox-toggle" data-target="term-group"><?php _e( 'Select / Deselect All', 'page-generator-pro' ); ?></a>
		</div>
		<div class="right">
			<div class="tax-selection">
				<div class="tabs-panel">
					<ul class="categorychecklist form-no-clear">				                    			
						<?php
						foreach ( $term_groups as $group_id => $group ) {
	                        ?>
	                        <li>
								<label class="selectit">
									<input type="checkbox" name="term_groups[<?php echo $group_id; ?>]" value="1" class="term-group" checked />
									<?php echo $group['title']; ?>      
								</label>
							</li>
	                        <?php
						}					
						?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<?php
}