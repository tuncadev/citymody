<?php
/*
Template Name: Full Width Page
*/
get_header(); ?>
	<div class="fullwidth mid-content"> <!-- Middle content align -->
		<?php 
		while ( have_posts() ) : the_post();
			get_template_part( 'template-parts/content', 'page' );
			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop. ?>
	</div> <!-- End -->
<?php get_footer(); ?>