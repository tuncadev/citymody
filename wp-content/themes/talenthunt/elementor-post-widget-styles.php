<?php 
global $kaya_shortlist_options, $post;
$current_item_type = get_post_type( $post->ID );
$taxonomy_names = get_object_taxonomies($current_item_type, 'objects');
$taxonomy_data = array();
foreach ($taxonomy_names as $key => $tax_name) {
	$taxonomy_data[] = $tax_name->name;
}
//echo '<div class="talent_image_details_wrapper">';
	$image_sizes = ( $settings['image_size_size'] == 'custom' ) ? array_values($settings['image_size_custom_dimension']) : $settings['image_size_size'];
	echo '<div class="grid-view-container">';
	echo '<a href="'.get_the_permalink().'">';
	echo '<div class="grid-view-image">';
	echo kaya_pod_featured_img($image_sizes, $settings['image_size_size']);
	echo '<div class="overlay-hd">';
	echo '</div>';
	echo '<div class="title-meta-data-wrapper">';
	    echo '<div class="general-meta-fields-info-wrapper">';
			echo kaya_post_post_general_info($settings);								
		echo '</div>';
	echo '</div>';
	echo '</div>';	
	echo '</a>';
	echo '<div class="shortlist-wrap">';
	do_action('kaya_pods_cpt_shortlist_icons'); // Shortlist Icons
	echo '</div>';
	echo '</div>';
	//if( in_array($cpt_name, $kaya_shortlist_options['enable_cpt_shortlist']) ){
		
	//}
	
?>