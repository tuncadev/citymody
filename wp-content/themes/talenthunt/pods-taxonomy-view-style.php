<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package talenthunt
 */
global $kaya_options, $kaya_shortlist_options;
$cpt_slug_name = kaya_get_post_type();
if( !is_author() ){
	$columns = !empty($kaya_options->taxonomy_columns) ? $kaya_options->taxonomy_columns : '4';
}else{
	$columns = '3';
}
$image_cropping_type = !empty($kaya_options->choose_image_sizes) ? $kaya_options->choose_image_sizes : 'wp_image_sizes';
if( $image_cropping_type == 'wp_image_sizes' ){
	$image_sizes = !empty($kaya_options->choose_image_sizes) ? $kaya_options->choose_image_sizes : 'full';
}else{
	$image_size_width = !empty($kaya_options->taxonomy_gallery_width) ? $kaya_options->taxonomy_gallery_width : '380';
	$image_size_height = !empty($kaya_options->taxonomy_gallery_height) ? $kaya_options->taxonomy_gallery_height : '600';
	$image_sizes = array( $image_size_width, $image_size_height );
}

// Session for shortlist data
if(isset($_SESSION['shortlist'])) {
	if ( in_array(get_the_ID(), $_SESSION['shortlist']) ) {
		$selected = 'item_selected';
	}else{
		$selected = '';
	}
}else{
	$selected = '';
}
		$img_url = wp_get_attachment_url(get_post_thumbnail_id());
	echo '<li class="column'.$columns.' item" id="'.get_the_ID().'">';
	if( function_exists('gray_scale_grid_view') ){
		if(!empty($kaya_options->gray_scale_mode) ){
			echo gray_scale_grid_view();
		}
		else{
			echo normal_grid_style();
		}
	}
		echo '<div class="grid-view-container taxonomy-style">';
			//echo '<strong>'.get_the_title().'</strong>';
			echo '<a href="'.get_the_permalink().'">';
				echo '<div class="grid-view-image">';
				echo kaya_pod_featured_img( $image_sizes, $image_cropping_type );
				echo '<div class="overlay-hd">';
				echo '</div>';
				if( function_exists('kaya_general_info_section') ){
					echo '<div class="title-meta-data-wrapper">';
					kaya_general_info_section($cpt_slug_name);
					echo '</div>';
				}
				echo '</div>';
				echo '</a>';
			echo '<div class="shortlist-wrap">';
			do_action('kaya_pods_cpt_shortlist_icons'); // Shortlist Icons
			echo '</div>';
			
		echo '</div>';
		
	echo '</li>';
		
?>