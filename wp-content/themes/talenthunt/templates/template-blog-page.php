<?php
/*
Template Name: Blog Page
*/
get_header();
	$choose_sidebar = get_theme_mod('blog_page_sidebar_position') ? get_theme_mod('blog_page_sidebar_position') : 'right';
	if( $choose_sidebar == 'right' ){
		$content_postion = 'two_third';
		$sidebar_position = 'one_third_last';	
	}elseif($choose_sidebar == 'left'){
		$content_postion = 'two_third_last';
		$sidebar_position = 'one_third';
	}else{
		$content_postion = 'fullwidth';
		$sidebar_position = '';
	}
	?>
	<div class="<?php echo esc_html($content_postion); ?> mid-content"> <!-- Middle content align -->
		<div class="blog-page-content-wrapper">
			<?php
			$temp = $wp_query; $wp_query= null;
			$post_limit = get_theme_mod('blog_page_post_limit') ? get_theme_mod('blog_page_post_limit') : '10';

			$wp_query = new WP_Query(); $wp_query->query('posts_per_page='.$post_limit . '&paged='.$paged);
			while ($wp_query->have_posts()) : $wp_query->the_post(); 		
				get_template_part( 'template-parts/content', get_post_format() );
			endwhile;
			talenthunt_kaya_pagination(); // Pagination
			wp_reset_postdata(); ?>
		</div>
	</div> <!-- End -->
	<?php if( $choose_sidebar != 'none' ){ ?>
	<div class="<?php echo esc_html($sidebar_position); ?>"> <!-- sidebar -->
		<?php get_sidebar(); ?>
	</div>
	<?php } ?>
<?php get_footer(); ?>