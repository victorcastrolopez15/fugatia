<h1><?php _e( 'Done', 'page-generator-pro' ); ?></h1>

<p>
	<?php _e( 'The following has been setup.', 'page-generator-pro' ); ?>
</p>

<h2><?php _e( 'Keywords', 'page-generator-pro' ); ?></h2>
<table class="widefat striped">
	<tbody>
		<tr>
			<th><?php _e( 'Service Keyword', 'page-generator-pro' ); ?></th>
			<td>
				<a href="admin.php?page=page-generator-pro-keywords&cmd=form&id=<?php echo $this->configuration['service_keyword_id']; ?>" target="_blank">
					<?php _e( 'Edit', 'page-generator-pro' ); ?>
				</a>
			</td>
		</tr>
		<tr>
			<th><?php _e( 'Location Keyword', 'page-generator-pro' ); ?></th>
			<td>
				<a href="admin.php?page=page-generator-pro-keywords&cmd=form&id=<?php echo $this->configuration['location_keyword_id']; ?>" target="_blank">
					<?php _e( 'Edit', 'page-generator-pro' ); ?>
				</a>
			</td>
		</tr>
	</tbody>
</table>

<h2><?php _e( 'Content Groups', 'page-generator-pro' ); ?></h2>

<p>
	<?php _e( 'It is recommended that you edit each Content Group and write the content needed. The Content Groups will have a paragraph of text prefilled with the necessary Related Links shortcode for interlinking content.', 'page-generator-pro' ); ?>
</p>

<table class="widefat striped">
	<tbody>
		<?php
		foreach ( $this->configuration['content_group_ids'] as $content_group_type => $content_group_id ) {
			?>
			<tr>
				<th>
					<?php 
					switch ( $content_group_type ) {
						case 'region_group_id':
							_e( 'Region Content Group', 'page-generator-pro' ); 
							break;

						case 'county_group_id':
							_e( 'County Content Group', 'page-generator-pro' ); 
							break;

						case 'city_group_id':
							_e( 'City Content Group', 'page-generator-pro' ); 
							break;

						case 'service_group_id':
							_e( 'Service Content Group', 'page-generator-pro' ); 
							break;
					}
					?>
				</th>
				<td>
					<a href="post.php?post=<?php echo $content_group_id; ?>&action=edit" target="_blank">
						<?php _e( 'Edit', 'page-generator-pro' ); ?>
					</a>
				</td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>

<p>
	<?php _e( 'Click "Finish" to load the Content Groups screen.', 'page-generator-pro' ); ?>
</p>