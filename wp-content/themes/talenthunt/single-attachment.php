<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package talenthunt
 */

get_header(); ?>
	<div class="three_fourth mid-content"> <!-- Middle content align -->
		<?php
		while ( have_posts() ) : the_post();

			$img_url = wp_get_attachment_url(get_post_thumbnail_id());
			if( !empty($img_url) ){
				echo '<img src="'.talenthunt_kaya_image_sizes($img_url, '', '').'" alt="img" class="" />'; // WPCS: XSS OK
			}

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
	<div class="one_fourth_last">
		<?php get_sidebar(); ?>
	</div>
<?php get_footer(); ?>