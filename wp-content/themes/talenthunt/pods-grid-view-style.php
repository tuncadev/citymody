<?php
/**
* This loop data included "CPT Post Grid View & CPT Post Slider " widgets only
* If you want to customize this loop data
* Copy this file and add your theme root folder with same name(widget-loop.php)
*/
	global $kaya_shortlist_options;
	// post featured images with link
	$current_item_type = get_post_type( $post->ID );
	if( $instance['style'] == '1' ){
	echo '<div class="talent_image_details_wrapper">';
		echo '<a href="'.get_the_permalink().'" class="img_hover_effect">';
			$img_url = get_the_post_thumbnail_url();
			kaya_pod_featured_img($image_sizes, $instance['thumbnail_sizes']);
			// check this function to enabled shortlist icons or not

			foreach ($instance['cpt_post_type'] as $key => $cpt_name) {
					# code...
				if( $instance['disable_shortlist_icons'] != 'on' ){	
					if( !empty($kaya_shortlist_options['enable_cpt_shortlist']) ){
						if( in_array($cpt_name, $kaya_shortlist_options['enable_cpt_shortlist']) ){
							do_action('kaya_pods_cpt_shortlist_icons'); // Shortlist Icons
						}
					}
				}

				// start Post content wrapper
				if( $current_item_type == $cpt_name ){
					$meta_info = !empty($instance['disable_meta_details']) ? $instance['disable_meta_details'] : 'off';
				echo '<a href="'.get_the_permalink().'">';
				    echo '<div class="talenthunt_details">';
						echo kaya_general_info_section_widget($meta_info, $cpt_name, $instance['enbale_selected_cpt_fields'][$current_item_type], $instance['style']);
					echo '</div>';
				echo '</a>'; 	
				}
			}
				// post description limit words
			if( $instance['disable_post_content'] != 'on' ){
				echo '<p>'.wp_trim_words( get_the_content(), $instance ['post_content_limit'], null ).'</p>';
			} 
			echo '</a>'; 
			 // End Post content wrapper 
	//	echo '</a>'; 
	echo '</div>'; 
	// 
}elseif( $instance['style'] == '2' ){ // <---------------- Style-2 Project display style-------->
	echo '<div class="style2_image column6 ">';
		echo '<a href="'.get_the_permalink().'">';
			$img_url = get_the_post_thumbnail_url();
			kaya_pod_featured_img($image_sizes, $instance['thumbnail_sizes']);
			// check this function to enabled shortlist icons or not
			foreach ($instance['cpt_post_type'] as $key => $cpt_name) {
				# code...
				if( $instance['disable_shortlist_icons'] != 'on' ){
					if( !empty($kaya_shortlist_options['enable_cpt_shortlist']) ){
						if( in_array($cpt_name, $kaya_shortlist_options['enable_cpt_shortlist']) ){
							do_action('kaya_pods_cpt_shortlist_icons'); // Shortlist Icons
						}
					}
				}
		echo '</div>';
		// Project Details and title Section	
		echo '<div class="style2_details_wrapper">';
		//echo '<h4><a href="'.get_the_permalink().'">'.get_the_title().'</a></h4>';
			// start Post content wrapper
			if( $current_item_type == $cpt_name ){
				$meta_info = !empty($instance['disable_meta_details']) ? $instance['disable_meta_details'] : 'off';
				echo '<a href="'.get_the_permalink().'">';
					echo '<div class="talenthunt_details">';
						echo kaya_general_info_section_widget($meta_info, $cpt_name, $instance['enbale_selected_cpt_fields'][$current_item_type], $instance['style']);
					echo '</div>';
				echo '</a>'; 	
				}
				}
				// post description limit words
				if( $instance['disable_post_content'] != 'on' ){
					echo '<p>'.wp_trim_words( get_the_content(), $instance['post_content_limit'], null ).'</p>';
				}
				echo '</a>';
			 // End Post content wrapper
	//	echo '</a>';
	echo '</div>';			
	
}
?>