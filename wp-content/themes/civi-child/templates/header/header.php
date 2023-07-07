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
			<?php if (is_user_logged_in()) { ?>
				<?php echo Civi_Templates::notification(); ?>
				<?php } else {  ?>
				<div class="account logged-out" id="reg-btn"><a id="reg-clck" href="#popup-form" class="btn-login-register" data-form="ux-register"><?php echo __( "Register" , "civichild" ); ?></a></div>
			<?php } ?>
			
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
