<?php 
$project_title = $_GET['project_title'];
$new_post = array(
 'post_status' => 'publish',
 'post_type' => 'project'
			
);

$post_id = wp_insert_post( $new_post );

if( $post_id ){
	update_field('project_title', $project_title . " - " . $post_id, $post_id);
 echo "Post inserted successfully with the post ID of ".$post_id;
} else {
 echo "Error, post not inserted";
}


?>