<?php get_header(); ?>
<?php 
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
} ?>

		<div class="container">

			<h1><?php echo __("Hata!", "civichild"); ?></h1>

			<div class="warning">
				<h2><?php echo $message; ?></h2>

				<?php if ( isset( $r['back_link'] ) && $r['back_link'] ) : ?>
					<a href="javascript:history.back()"><?php echo __("Bir önceki sayfaya geri dön", "civichild"); ?></a>
				<?php endif; ?>
			</div>

			<div class="footer"><p class="copyright"><a href="https://www.citymody.com/"><?php echo __("Copyright @2023 Citymody. Tüm hakları saklıdır", "civichild"); ?></a></p></div>

		</div>
<?php get_footer(); ?>