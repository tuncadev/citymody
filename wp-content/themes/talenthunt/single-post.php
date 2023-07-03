<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package talenthunt
 */

get_header(); 
$choose_sidebar = get_theme_mod('blog_single_page_sidebar_position') ? get_theme_mod('blog_single_page_sidebar_position') : 'right';
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
		<?php
		
		while ( have_posts() ) : the_post();

			get_template_part( 'template-parts/content', get_post_format() );

			wp_link_pages( array(
						'before'      => '<div class="page-links">' . esc_html__( 'Pages:', 'talenthunt' ),
						'after'       => '</div>',
						'link_before' => '<span class="page-numbers">',
						'link_after'  => '</span>',
					) );
			

			echo '<div class="single-post-tags">';
				the_tags();
			echo '</div>';


			echo '<div class="post-next-prev-buttons">';
				the_post_navigation();
			echo '</div>';	

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>
	</div> <!-- End -->
	<?php if( $choose_sidebar != 'none' ){ ?>
	<div class="<?php echo esc_html($sidebar_position); ?>"> <!-- sidebar -->
		<?php get_sidebar(); ?>
	</div>
	<?php } ?>
<?php get_footer(); ?>