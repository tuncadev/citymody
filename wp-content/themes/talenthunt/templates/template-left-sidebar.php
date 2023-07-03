<?php
/*
Template Name: Page with Left Sidebar
*/
get_header(); ?>
    <div class="two_third_last mid-content"> <!-- Middle content align -->
        <?php 
        while ( have_posts() ) : the_post();
            get_template_part( 'template-parts/content', 'page' );
            // If comments are open or we have at least one comment, load up the comment template.
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;

        endwhile; // End of the loop. ?>
    </div> <!-- End -->
    <div class="one_third">
        <?php get_sidebar(); ?>
    </div>
<?php get_footer(); ?>