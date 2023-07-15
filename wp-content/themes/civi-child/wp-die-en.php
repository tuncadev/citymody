<?php get_header(); ?>

<div class="unicorn"></div>

		<div class="container">

			<h1><?php echo __("Oops!", "civichild"); ?></h1>

			<div class="warning">
				<h2><?php echo $message; ?></h2>

				<?php if ( isset( $r['back_link'] ) && $r['back_link'] ) : ?>
					<a href="javascript:history.back()"><?php echo __("Please go back to the previous page", "civichild"); ?></a>
				<?php endif; ?>
			</div>

			<div class="footer"><p class="copyright"><a href="https://www.citymody.com/"><?php echo __("Copyright @2023 Citymody. All rights reserved", "civichild"); ?></a></p></div>

		</div>
<?php get_footer(); ?>