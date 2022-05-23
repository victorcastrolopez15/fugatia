<?php
foreach ( $this->items as $count => $result ) {
    ?>
    <tr class="<?php echo $result['result'] . ( ( $count % 2 > 0 ) ? ' alternate' : '' ); ?>">
        <th scope="row" class="check-column">
            <input type="checkbox" name="ids[<?php echo $result['id']; ?>]" value="<?php echo $result['id']; ?>" />
        </th>
        <td class="group_id column-group_id<?php echo ( in_array( 'group_id', $hidden ) ? ' hidden' : '' ); ?>">
            <a href="<?php echo admin_url( 'admin.php?page=' . $this->base->plugin->name . '-logs&group_id=' . $result['group_id'] ); ?>" title="<?php _e( 'Filter Log by this Group', 'page-generator-pro' ); ?>"> 
                #<?php echo $result['group_id']; ?><br />
                <?php echo $result['group_name']; ?>
            </a>
        </td>
        <td class="group_id column-post_id<?php echo ( in_array( 'post_id', $hidden ) ? ' hidden' : '' ); ?>">
            <a href="<?php echo $result['url']; ?>" target="_blank" title="<?php _e( 'View Generated Item', 'page-generator-pro' ); ?>"><?php echo $result['url']; ?></a>
        </td>
        <td class="group_id column-system<?php echo ( in_array( 'system', $hidden ) ? ' hidden' : '' ); ?>">
            <?php echo $result['system']; ?>
        </td>
        <td class="group_id column-test_mode<?php echo ( in_array( 'test_mode', $hidden ) ? ' hidden' : '' ); ?>">
            <?php echo ( $result['test_mode'] ? __( 'Yes', 'page-generator-pro' ) : __( 'No', 'page-generator-pro' ) ); ?>
        </td>
        <td class="group_id column-generated<?php echo ( in_array( 'generated', $hidden ) ? ' hidden' : '' ); ?>">
            <?php echo ( $result['generated'] ? __( 'Yes', 'page-generator-pro' ) : __( 'No', 'page-generator-pro' ) ); ?>
        </td>
        <td class="group_id column-keywords_terms<?php echo ( in_array( 'keywords_terms', $hidden ) ? ' hidden' : '' ); ?>">
            <?php
            $keywords_terms = json_decode( $result['keywords_terms'] );
            foreach ( $keywords_terms as $keyword => $term ) {
                echo $keyword . ': ' . $term . '<br />';
            }
            ?>
        </td>
        <td class="group_id column-result<?php echo ( in_array( 'result', $hidden ) ? ' hidden' : '' ); ?>">
            <?php echo $result['result']; ?>
        </td>
        <td class="group_id column-message<?php echo ( in_array( 'message', $hidden ) ? ' hidden' : '' ); ?>">
            <?php echo $result['message']; ?>
        </td>
        <td class="group_id column-duration<?php echo ( in_array( 'duration', $hidden ) ? ' hidden' : '' ); ?>">
            <?php echo $result['duration']; ?>
        </td>
        <td class="group_id column-memory_usage<?php echo ( in_array( 'memory_usage', $hidden ) ? ' hidden' : '' ); ?>">
            <?php echo $result['memory_usage']; ?>
        </td>
        <td class="group_id column-memory_peak_usage<?php echo ( in_array( 'memory_peak_usage', $hidden ) ? ' hidden' : '' ); ?>">
            <?php echo $result['memory_peak_usage']; ?>
        </td>
        <td class="group_id column-generated_at<?php echo ( in_array( 'generated_at', $hidden ) ? ' hidden' : '' ); ?>">
            <?php echo date( 'jS F, Y H:i:s', strtotime( $result['generated_at'] ) ); ?>
        </td>
    </tr>
    <?php
}