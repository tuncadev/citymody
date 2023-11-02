<?php

/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 */

get_header();
?>
<div class="main-content content-page">
	<div class="container">
		<div class="site-layout">
			<div class="area-404 align-center">
				<h2><?php esc_html_e('Hmm, that didn’t work', 'civi'); ?></h2>
				<p><?php esc_html_e("Sorry, we couldn't find the page you were looking for. The page you are looking for may be moved, removed, renamed or never found.", 'civi'); ?></p>
				<img src="https://www.citymody.com/wp-content/uploads/2023/07/404.png" alt="<?php esc_attr_e('404', 'civi'); ?>">
				<a class="civi-button button-outline-accent button-icon-right" href="<?php echo esc_url(home_url()); ?>">
					<?php esc_html_e('Go to home page', 'civi') ?>
					<i class="fas fa-chevron-right"></i>
				</a>
			</div>
		</div>
	</div>
</div>

<?php
get_footer();
