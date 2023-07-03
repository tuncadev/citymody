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
 * @package julia
 */

get_header(); ?>
	<div class="two_third mid-content"> <!-- Middle content align -->
		<?php woocommerce_content(); ?>
	</div> <!-- End -->

	
	<div class="one_third_last">
	<aside id="sidebar">
	<?php
		if( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) {
		   dynamic_sidebar( 'woo_sidebar' ); 
		} else{

			get_sidebar(); 
		}
		             
    ?>
	</div>
	
<?php get_footer(); ?>

