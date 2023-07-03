<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package talenthunt
 */

get_header(); ?>

	<div class="fullwidth mid-content">  <!-- Middle content align -->
		<?php
		if( (isset($_REQUEST['cpt_post_search']) && ( $_REQUEST['cpt_post_search'] == 'cpt_post_search' ) ) ){
			echo kaya_cpt_search_display();
		}else{
		echo '<div class="kaya-post-content-wrapper cpt-post-content-wrapper">';
		//if ( have_posts() ) : ?>
			<?php
			/* Start the Loop */
			//echo '<div class="kaya-post-content-wrapper">';
			//if( isset($_REQUEST['advance_search'] ) && ( $_REQUEST['advance_search'] == 'advance_search' ) ){
				echo '<ul class="column-extra">';
				//while ( have_posts() ) : the_post();
					/**
					 * Run the loop for the search to output the results.
					 * If you want to overload this in a child theme then include a file
					 * called content-search.php and that will be used instead.
					 */

					// POD CPT Search Start here
					
					if( function_exists('kaya_get_template_part') ){ 
						//	kaya_get_template_part( 'loop', 'content' );
							kaya_search_data();
						}else{
							get_template_part( 'template-parts/content', 'search' );
						}
					//}else{
					//	get_template_part( 'template-parts/content', 'search' );
					//} 
					
					// POD CPT Search Start here
					
				//endwhile;
				//if( isset($_REQUEST['advance_search'] ) && ( $_REQUEST['advance_search'] == 'advance_search' ) ){
					echo '</ul>';	
				//}
				//talenthunt_kaya_pagination();

			//else :

			//get_template_part( 'template-parts/content', 'none' );
		
		//echo '</div>';

		//endif;
		echo '</div>'; 
	}?>
		</div> <!-- End -->
			
<?php get_footer(); ?>