<div class="glf-theme-options-wrapper wrap">
	<h2 class="screen-reader-text"><?php esc_html_e($page_title) ?></h2>
	<?php do_action("glf/{$option_name}-theme-option-form/before") ?>
	<div class="area-theme-options inside">
		<form action="#" method="post" enctype="multipart/form-data">
			<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce($option_name); ?>" />
			<input type="hidden" id="_current_page" name="_current_page" value="<?php echo esc_attr($page); ?>" />
			<div class="glf-theme-options-title">
				<h1><?php esc_html_e($page_title) ?></h1>

				<div class="glf-theme-options-action">
					<button class="button button-primary glf-theme-options-save-options" type="submit" name="glf_save_option"><?php esc_html_e('Save Options', 'civi-framework'); ?></button>
				</div>
			</div>
			<div class="glf-meta-box-wrap">
				<?php glf_get_template('templates/theme-option-section', array('list_section' => $list_section)) ?>
				<div class="glf-fields">
					<div class="glf-fields-wrapper">
