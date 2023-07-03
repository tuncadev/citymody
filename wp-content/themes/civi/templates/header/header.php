<div class="container">
	<div class="row">

		<div class="left-header">
			<?php echo Civi_Templates::canvas_menu(); ?>
			<?php echo Civi_Templates::style_logo(); ?>

			<div class="d-none d-xl-block">
				<?php echo Civi_Templates::main_menu(); ?>
			</div>
		</div>

		<div class="right-header">
			<?php echo Civi_Templates::notification(); ?>

			<div class="d-none d-xl-block">
				<?php echo Civi_Templates::account(); ?>
			</div>

			<div class="d-xl-none">
				<?php echo Civi_Templates::search_icon('icon', true); ?>
			</div>

			<div class="d-none d-xl-block">
				<?php echo Civi_Templates::add_jobs(); ?>
			</div>
		</div>

	</div>
</div><!-- .container -->
