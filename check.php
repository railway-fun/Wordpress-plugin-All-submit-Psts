<?php
echo '======= Start script ' . date( 'Y-m-d H:i:s' ) . ' ======' . "\n";
require_once( dirname( __FILE__ ) . '/wp-load.php' );
$current_time = current_time( 'mysql' );
$published_ids = array();

$sql = "
SELECT	ID
FROM	$wpdb->posts
WHERE	post_status = 'future'
AND		post_date < '$current_time'
";

$post_ids = $wpdb->get_col( $sql );

if ( $post_ids ) {
	echo count( $post_ids ) . ' future posts found.' . "\n";
	foreach ( $post_ids as $post_id ) {
		//$ret = wp_update_post( array( 'ID' => $post_id, 'post_status' => 'publish' ) );
		$ret = wp_publish_post($post_id);
		if ( $ret ) {
			$published_ids[] = $post_id;
		}
	}
	echo count( $published_ids ) . '( ' . implode( ', ', $published_ids ) . ' ) posts published.' . "\n";
} else {
	echo 'no future posts found.' . "\n";
}
echo '========= End script ' . current_time( 'mysql' ) . ' ======' . "\n";
?>
