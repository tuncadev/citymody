<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package talenthunt
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11"> 

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php
	 ?>
	<div id="kaya-page-content-wrapper" class="">

		<?php 
		// Header Section
		if( !function_exists('hfe_render_header') ){
			talenthunt_kaya_header(); // WPCS: xss ok.
		}else{
			$header_data = get_hfe_header_id();
			if( !empty($header_data) ){
				hfe_render_header(); // Header & Footer Plugin data
			}else{
				echo talenthunt_kaya_header(); // WPCS: xss ok.
			}
		}
		echo '<div class="main-wrapper ajax-search-results">';
		talenthunt_kaya_page_title(); ?>
		
		<?php	
		// Slider Functionality
		talenthunt_kaya_slider_shortcode(); ?>		<!-- Page title section -->
		
		<!-- Middle content alignment start here -->
		<div id="kaya-mid-content-wrapper">
			<div id="mid-content" class="site-content container">