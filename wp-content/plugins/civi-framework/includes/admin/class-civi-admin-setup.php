<?php
if (!defined('ABSPATH')) {
	exit;
}
if (!class_exists('Civi_Admin_Setup')) {
	/**
	 * Class Civi_Admin_Setup
	 */
	class Civi_Admin_Setup
	{
		/**
		 * admin_menu
		 */
		public function admin_menu()
		{
			add_menu_page(
				esc_html__('Civi', 'civi-framework'),
				esc_html__('Civi', 'civi-framework'),
				'manage_options',
				'civi_welcome',
				array($this, 'menu_welcome_page_callback'),
				CIVI_PLUGIN_URL . 'assets/images/icon.png',
				2
			);
			add_submenu_page(
				'civi_welcome',
				esc_html__('Welcome', 'civi-framework'),
				esc_html__('Welcome', 'civi-framework'),
				'manage_options',
				'civi_welcome',
				array($this, 'menu_welcome_page_callback')
			);
			add_submenu_page(
				'civi_welcome',
				esc_html__('System', 'civi-framework'),
				esc_html__('System', 'civi-framework'),
				'manage_options',
				'civi_system',
				array($this, 'system_page_callback')
			);
			add_submenu_page(
				'civi_welcome',
				esc_html__('Import', 'civi-framework'),
				esc_html__('Import', 'civi-framework'),
				'manage_options',
				'civi_import',
				array($this, 'import_page_callback')
			);

			if (defined('WP_DEBUG') && true === WP_DEBUG) {
				add_submenu_page(
					'civi_welcome',
					esc_html__('Export', 'civi-framework'),
					esc_html__('Export', 'civi-framework'),
					'manage_options',
					'civi_export',
					array($this, 'export_page_callback')
				);
			};

			add_submenu_page(
				'civi_welcome',
				esc_html__('Theme Options', 'civi-framework'),
				esc_html__('Theme Options', 'civi-framework'),
				'manage_options',
				'admin.php?page=civi-framework'
			);
			add_submenu_page(
				'civi_welcome',
				esc_html__('Setup Page', 'civi-framework'),
				esc_html__('Setup Page', 'civi-framework'),
				'manage_options',
				'civi_setup',
				array($this, 'setup_page')
			);

			add_menu_page(
				esc_html__('Civi Jobs', 'civi-framework'),
				esc_html__('Civi Jobs', 'civi-framework'),
				'manage_options',
				'civi_jobs',
				'',
				CIVI_PLUGIN_URL . 'assets/images/icon1.png',
				2,5
			);

			add_submenu_page(
				'civi_jobs',
				esc_html__('Jobs', 'civi-framework'),
				esc_html__('Jobs', 'civi-framework'),
				'manage_options',
				'edit.php?post_type=jobs'
			);
			add_submenu_page(
				'civi_jobs',
				esc_html__('Applicants', 'civi-framework'),
				esc_html__('Applicants', 'civi-framework'),
				'manage_options',
				'edit.php?post_type=applicants'
			);
			add_submenu_page(
				'civi_jobs',
				esc_html__('Job Alerts', 'civi-framework'),
				esc_html__('Job Alerts', 'civi-framework'),
				'manage_options',
				'edit.php?post_type=job_alerts'
			);

			add_menu_page(
				esc_html__('Civi Employer', 'civi-framework'),
				esc_html__('Civi Employer', 'civi-framework'),
				'manage_options',
				'civi_employer',
				'',
				CIVI_PLUGIN_URL . 'assets/images/icon2.png',
				7
			);

			add_submenu_page(
				'civi_employer',
				esc_html__('Companies', 'civi-framework'),
				esc_html__('Companies', 'civi-framework'),
				'manage_options',
				'edit.php?post_type=company'
			);

			add_submenu_page(
				'civi_employer',
				esc_html__('Package', 'civi-framework'),
				esc_html__('Package', 'civi-framework'),
				'manage_options',
				'edit.php?post_type=package'
			);

			add_submenu_page(
				'civi_employer',
				esc_html__('User Package', 'civi-framework'),
				esc_html__('User Package', 'civi-framework'),
				'manage_options',
				'edit.php?post_type=user_package'
			);

			add_submenu_page(
				'civi_employer',
				esc_html__('Invoice', 'civi-framework'),
				esc_html__('Invoice', 'civi-framework'),
				'manage_options',
				'edit.php?post_type=invoice'
			);

			add_menu_page(
				esc_html__('Civi Candidates', 'civi-framework'),
				esc_html__('Civi Candidates', 'civi-framework'),
				'manage_options',
				'edit.php?post_type=candidate',
				'',
				CIVI_PLUGIN_URL . 'assets/images/icon3.png',
				12
			);

			add_menu_page(
				esc_html__('Civi Extensions', 'civi-framework'),
				esc_html__('Civi Extensions', 'civi-framework'),
				'manage_options',
				'civi_extensions',
				'',
				CIVI_PLUGIN_URL . 'assets/images/icon4.png',
				13
			);

			add_submenu_page(
				'civi_extensions',
				esc_html__('Messages', 'civi-framework'),
				esc_html__('Messages', 'civi-framework'),
				'manage_options',
				'edit.php?post_type=messages'
			);

			add_submenu_page(
				'civi_extensions',
				esc_html__('Notification', 'civi-framework'),
				esc_html__('Notification', 'civi-framework'),
				'manage_options',
				'edit.php?post_type=notification'
			);

			add_submenu_page(
				'civi_extensions',
				esc_html__('Meetings', 'civi-framework'),
				esc_html__('Meetings', 'civi-framework'),
				'manage_options',
				'edit.php?post_type=meetings'
			);

			add_menu_page(
				esc_html__('Civi Builder', 'civi-framework'),
				esc_html__('Civi Builder', 'civi-framework'),
				'manage_options',
				'civi_builder',
				'',
				CIVI_PLUGIN_URL . 'assets/images/icon5.png',
				17
			);

			add_submenu_page(
				'civi_builder',
				esc_html__('Mega Menu', 'civi-framework'),
				esc_html__('Mega Menu', 'civi-framework'),
				'manage_options',
				'edit.php?post_type=civi_mega_menu'
			);

			add_submenu_page(
				'civi_builder',
				esc_html__('Footer', 'civi-framework'),
				esc_html__('Footer', 'civi-framework'),
				'manage_options',
				'edit.php?post_type=civi_footer'
			);
		}

		public function reorder_admin_menu() {
			// Remove default menu items
			remove_menu_page( 'edit-comments.php' );
			remove_menu_page( 'tools.php' );
			remove_menu_page( 'edit.php' ); // Remove posts
			remove_menu_page( 'edit.php?post_type=page' ); // Remove pages
			remove_menu_page( 'upload.php' );
			remove_menu_page( 'themes.php' );
			remove_menu_page( 'plugins.php' );
			remove_menu_page( 'users.php' );
			remove_menu_page( 'options-general.php' );

			// Reorder menu items
			add_menu_page( 'Posts', 'Posts', 'edit_posts', 'edit.php', '', 'dashicons-admin-post', 26 );
			add_menu_page( 'Media', 'Media', 'manage_options', 'upload.php', '', 'dashicons-admin-media', 27 );
			add_menu_page( 'Pages', 'Pages', 'edit_pages', 'edit.php?post_type=page', '', 'dashicons-admin-page', 28 );
			add_menu_page( 'Comments', 'Comments', 'manage_comments', 'edit-comments.php', '', 'dashicons-admin-comments', 29 );
			add_menu_page( 'Appearance', 'Appearance', 'edit_theme_options', 'themes.php', '', 'dashicons-admin-appearance', 30 );
			add_menu_page( 'Plugins', 'Plugins', 'activate_plugins', 'plugins.php', '', 'dashicons-admin-plugins', 31 );
			add_menu_page( 'Users', 'Users', 'promote_users', 'users.php', '', 'dashicons-admin-users', 32 );
			add_menu_page( 'Tools', 'Tools', 'manage_options', 'tools.php', '', 'dashicons-admin-tools', 33 );
			add_menu_page( 'Settings', 'Settings', 'manage_options', 'options-general.php', '', 'dashicons-admin-settings', 34 );
		}

		public function menu_welcome_page_callback()
		{
			if (isset($_POST['purchase_code'])) {
				$purchase_info = Civi_Updater::check_purchase_code(sanitize_key($_POST['purchase_code']));
				update_option('uxper_purchase_code', $_POST['purchase_code']);
			}
			$purchase_code = get_option('uxper_purchase_code');
			$purchase_class = '';
			$verified = '';
			$check_code = esc_html__('Not verified', 'civi-framework');
			if ($purchase_code) {
				$purchase_code_info = Civi_Updater::check_purchase_code($purchase_code);
				if ($purchase_code_info['status_code'] === 200) {
					$purchase_class = 'verified hidden-code';
					$verified = 'verified';
					$check_code = esc_html__('Verified', 'civi-framework');
				}
			}
?>

			<?php
			$update = Civi_Updater::check_theme_update();
			$new_version = isset($update['new_version']) ? $update['new_version'] : CIVI_THEME_VERSION;
			$get_info = Civi_Updater::get_info();
			if ($update) {
			?>
				<div class="alert-wrap alert-success about-wrap">
					<div class="msg-update">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
							<rect x="0" fill="none" width="24" height="24"></rect>
							<g>
								<path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm1 15h-2v-2h2v2zm0-4h-2l-.5-6h3l-.5 6z"></path>
							</g>
						</svg>

						<div class="inner-msg">
							<?php
							if (Civi_Updater::check_valid_update()) {

								printf(
									__(
										'There is a new version of %1$s available. <a href="%2$s" %3$s>View version %4$s details</a> or <a href="%5$s" %6$s>update now</a>.',
										'civi-framework'
									),
									CIVI_THEME_NAME,
									esc_url(add_query_arg(
										'action',
										'uxper_get_changelogs',
										admin_url('admin-ajax.php')
									)),
									sprintf(
										'class="thickbox" name="Changelogs" aria-label="%s"',
										esc_attr(sprintf(
											__('View %1$s version %2$s details'),
											CIVI_THEME_NAME,
											CIVI_THEME_VERSION
										))
									),
									$new_version,
									wp_nonce_url(
										self_admin_url('update.php?action=upgrade-theme&theme=') . CIVI_THEME_SLUG,
										'upgrade-theme_' . CIVI_THEME_SLUG
									),
									sprintf(
										'id="update-theme" aria-label="%s"',
										esc_attr(sprintf(__('Update %s now'), CIVI_THEME_NAME))
									)
								);
							} else {

								printf(
									__(
										'There is a new version of %1$s available. <strong>Please enter your purchase code to update the theme.</strong>',
										'civi-framework'
									),
									CIVI_THEME_NAME
								);
							}
							?>
						</div>
					</div>
				</div>
			<?php
			}
			?>

			<div class="civi-wrap wrap about-wrap purchase-wrap">
				<div class="entry-heading">
					<h4><?php esc_html_e('Purchase code', 'civi-framework'); ?><span class="check-code <?php esc_html_e($verified); ?>"><?php esc_html_e($check_code); ?></span>
					</h4>
				</div>

				<form action="" class="purchase-form <?php echo esc_attr($purchase_class); ?>" method="post">
					<span class="purchase-icon">
						<svg class="valid" fill="#000000" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" width="20px" height="20px">
							<path d="M 22.78125 0 C 21.605469 -0.00390625 20.40625 0.164063 19.21875 0.53125 C 12.902344 2.492188 9.289063 9.269531 11.25 15.59375 L 11.25 15.65625 C 11.507813 16.367188 12.199219 18.617188 12.625 20 L 9 20 C 7.355469 20 6 21.355469 6 23 L 6 47 C 6 48.644531 7.355469 50 9 50 L 41 50 C 42.644531 50 44 48.644531 44 47 L 44 23 C 44 21.355469 42.644531 20 41 20 L 14.75 20 C 14.441406 19.007813 13.511719 16.074219 13.125 15 L 13.15625 15 C 11.519531 9.722656 14.5 4.109375 19.78125 2.46875 C 25.050781 0.832031 30.695313 3.796875 32.34375 9.0625 C 32.34375 9.066406 32.34375 9.089844 32.34375 9.09375 C 32.570313 9.886719 33.65625 13.40625 33.65625 13.40625 C 33.746094 13.765625 34.027344 14.050781 34.386719 14.136719 C 34.75 14.226563 35.128906 14.109375 35.375 13.832031 C 35.621094 13.550781 35.695313 13.160156 35.5625 12.8125 C 35.5625 12.8125 34.433594 9.171875 34.25 8.53125 L 34.25 8.5 C 32.78125 3.761719 28.601563 0.542969 23.9375 0.0625 C 23.550781 0.0234375 23.171875 0 22.78125 0 Z M 9 22 L 41 22 C 41.554688 22 42 22.445313 42 23 L 42 47 C 42 47.554688 41.554688 48 41 48 L 9 48 C 8.445313 48 8 47.554688 8 47 L 8 23 C 8 22.445313 8.445313 22 9 22 Z M 25 30 C 23.300781 30 22 31.300781 22 33 C 22 33.898438 22.398438 34.6875 23 35.1875 L 23 38 C 23 39.101563 23.898438 40 25 40 C 26.101563 40 27 39.101563 27 38 L 27 35.1875 C 27.601563 34.6875 28 33.898438 28 33 C 28 31.300781 26.699219 30 25 30 Z" />
						</svg>

						<svg class="invalid" fill="#000000" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" width="20px" height="20px">
							<path d="M 25 3 C 18.363281 3 13 8.363281 13 15 L 13 20 L 9 20 C 7.355469 20 6 21.355469 6 23 L 6 47 C 6 48.644531 7.355469 50 9 50 L 41 50 C 42.644531 50 44 48.644531 44 47 L 44 23 C 44 21.355469 42.644531 20 41 20 L 37 20 L 37 15 C 37 8.363281 31.636719 3 25 3 Z M 25 5 C 30.566406 5 35 9.433594 35 15 L 35 20 L 15 20 L 15 15 C 15 9.433594 19.433594 5 25 5 Z M 9 22 L 41 22 C 41.554688 22 42 22.445313 42 23 L 42 47 C 42 47.554688 41.554688 48 41 48 L 9 48 C 8.445313 48 8 47.554688 8 47 L 8 23 C 8 22.445313 8.445313 22 9 22 Z M 25 30 C 23.300781 30 22 31.300781 22 33 C 22 33.898438 22.398438 34.6875 23 35.1875 L 23 38 C 23 39.101563 23.898438 40 25 40 C 26.101563 40 27 39.101563 27 38 L 27 35.1875 C 27.601563 34.6875 28 33.898438 28 33 C 28 31.300781 26.699219 30 25 30 Z" />
						</svg>
					</span>
					<input class="purchase-code" name="purchase_code" type="text" value="<?php echo esc_attr($purchase_code); ?>" placeholder="<?php esc_attr_e('Purchase code', 'civi-framework'); ?>" autocomplete="off" />
					<input type="submit" class="button action" value="Submit" />
				</form>
				<div class="purchase-desc">
					<?php
					if (isset($_POST['purchase_code'])) {
						$purchase_info = Civi_Updater::check_purchase_code(sanitize_key($_POST['purchase_code']));
						if ($purchase_info['status_code'] !== 200) {
							esc_html_e('The purchase code was invalid.', 'civi-framework');
						} else {
							esc_html_e('Success! The purchase code was valid.', 'civi-framework');
						}
					} else {
						if ($purchase_code) {
							$purchase_info = Civi_Updater::check_purchase_code($purchase_code);
							if ($purchase_info['status_code'] === 200) {
								esc_html_e('Please do not provide purchase code to anyone.', 'civi-framework');
							} else {
								esc_html_e('The purchase code was invalid. Please try again.', 'civi-framework');
							}
						} else {
							esc_html_e('Show us your ThemeForest purchase code to get the automatic update.', 'civi-framework');
						}
					}
					?>
				</div>
			</div>

			<div class="civi-wrap wrap about-wrap welcome-wrap">
				<div class="wrap-column wrap-column-2 col-started">
					<div class="panel-column column-content">
						<h3><?php esc_html_e('Welcome to Civi Theme', 'civi-framework'); ?></h3>
						<p><?php esc_html_e("We've assembled some links to get you started", 'civi-framework'); ?></p>
						<div class="entry-heading started">
							<h4><?php esc_html_e('Get Started', 'civi-framework'); ?></h4>
						</div>
						<div class="entry-detail">

							<a href="<?php echo esc_url(admin_url('admin.php?page=civi_import')); ?>" class="button button-primary"><?php esc_html_e('Install Sample Data', 'civi-framework'); ?></a>

							<p>
								<span><?php esc_html_e('or,', 'civi-framework') ?></span>
								<a href="<?php echo esc_url(admin_url('customize.php')); ?>"><?php esc_html_e('Customize your site', 'civi-framework'); ?></a>
							</p>
						</div>
						<div class="box-wrap">
							<div class="box-detail">
								<span class="entry-title"><?php esc_html_e('Current Version: ', 'civi-framework'); ?></span>
								<p><?php esc_html_e(CIVI_THEME_VERSION); ?></p>
							</div>
							<div class="box-detail">
								<span class="entry-title">
									<?php esc_html_e('Lastest Version: ', 'civi-framework'); ?>
									<?php
									if (Civi_Updater::check_valid_update() && $update) {

										printf(
											__(
												'<a class="button uxper-update" href="%1$s" %2$s>Update now</a>',
												'civi-framework'
											),
											wp_nonce_url(
												self_admin_url('update.php?action=upgrade-theme&theme=') . CIVI_THEME_SLUG,
												'upgrade-theme_' . CIVI_THEME_SLUG
											),
											sprintf(
												'id="update-theme" aria-label="%s"',
												esc_attr(sprintf(__('Update %s now'), CIVI_THEME_NAME))
											)
										);
									}
									?>
								</span>
								<p><?php esc_html_e($new_version); ?></p>
							</div>
						</div>
						<div class="entry-detail">
							<a class="entry-title" href="<?php echo esc_attr($get_info['docs']); ?>" target="_blank"><?php esc_html_e('Online Documentation', 'civi-framework'); ?>
								<i class="fas fa-external-link-alt"></i>
							</a>
							<a class="entry-title" href="<?php echo esc_attr($get_info['support']); ?>" target="_blank"><?php esc_html_e('Request Support', 'civi-framework'); ?>
								<i class="fas fa-external-link-alt"></i>
							</a>
						</div>
					</div>
					<div class="panel-column column-image">
						<img src="<?php echo CIVI_PLUGIN_URL . '/assets/images/img-welcome.jpg' ?>" alt="" />
					</div>
				</div>
			</div>

			<?php
			$civi_tgm_plugins = apply_filters('civi_tgm_plugins', array());
			$installed_plugins = class_exists('TGM_Plugin_Activation') ? TGM_Plugin_Activation::$instance->plugins : array();
			$required_plugins_count = 0;
			?>
			<div class="civi-wrap wrap about-wrap plugins-wrap">
				<div class="entry-heading">
					<h4><?php esc_html_e('Plugins', 'civi-framework'); ?></h4>
					<p><?php esc_html_e('Please install and activate plugins to use all functionality.', 'civi-framework'); ?></p>
				</div>

				<div class="wrap-content">
					<?php if (!empty($civi_tgm_plugins) && class_exists('TGM_Plugin_Activation')) : ?>
						<div class="grid columns-3">
							<?php foreach ($civi_tgm_plugins as $plugin) : ?>
								<?php
								$plugin_obj = $installed_plugins[$plugin['slug']];
								$css_class = '';
								if ($plugin['required']) {
									if (TGM_Plugin_Activation::$instance->is_plugin_active($plugin['slug'])) {
										$css_class .= 'plugin-activated';
									} else {
										$css_class .= 'plugin-deactivated';
									}
								}

								$thumb = isset($plugin['thumb']) ? esc_html($plugin['thumb']) : '';
								?>
								<div class="item <?php echo esc_attr($css_class); ?>">
									<div class="plugin-thumb">
										<img src="<?php echo esc_url($thumb); ?>" alt="<?php esc_html_e($plugin['name']); ?>">

										<div class="plugin-type">
											<span><?php echo $plugin['required'] ? esc_html__('Required', 'civi-framework') : esc_html__('Recommended', 'civi-framework'); ?></span>
										</div>
									</div>
									<div class="entry-detail">
										<div class="plugin-name">
											<span><?php esc_html_e($plugin['name']); ?></span>
											<sup><?php echo isset($plugin['version']) ? esc_html($plugin['version']) : ''; ?></sup>
										</div>

										<div class="plugin-action">
											<?php echo Civi_Plugins::get_plugin_action($plugin_obj); ?>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>

					<?php else : ?>

						<p><?php esc_html_e('This theme doesn\'t require any plugins.', 'civi-framework'); ?></p>

					<?php endif; ?>

				</div><!-- end .wrap-content -->
			</div>

			<div class="civi-wrap wrap about-wrap changelogs-wrap">
				<div class="entry-heading">
					<h4><?php esc_html_e('Changelogs', 'civi-framework'); ?></h4>
				</div>

				<div class="wrap-content">
					<table class="table-changelogs">
						<thead>
							<tr>
								<th><?php esc_html_e('Version', 'civi-framework'); ?></th>
								<th><?php esc_html_e('Description', 'civi-framework'); ?></th>
								<th><?php esc_html_e('Date', 'civi-framework'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php echo Civi_Updater::get_changelogs(true); ?>
						</tbody>
					</table>
				</div><!-- end .wrap-content -->
			</div>

		<?php
		}

		public function system_page_callback()
		{
			add_thickbox();
			function civi_core_let_to_num($size)
			{
				$l = substr($size, -1);
				$ret = substr($size, 0, -1);
				switch (strtoupper($l)) {
					case 'P':
						$ret *= 1024;
					case 'T':
						$ret *= 1024;
					case 'G':
						$ret *= 1024;
					case 'M':
						$ret *= 1024;
					case 'K':
						$ret *= 1024;
				}

				return $ret;
			}

		?>
			<div class="civi-system-page">
				<div class="about-wrap box">
					<div class="box-header">
						<span class="icon"><i class="lar la-lightbulb"></i></span>
						<?php esc_html_e('WordPress Environment', 'civi-framework'); ?>
					</div>
					<div class="box-body">
						<table class="wp-list-table widefat striped system" cellspacing="0">
							<tbody>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The URL of your site\'s homepage.', 'civi-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('Home URL', 'civi-framework'); ?></td>
									<td><?php form_option('home'); ?></td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The root URL of your site.', 'civi-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('Site URL', 'civi-framework'); ?></td>
									<td><?php form_option('siteurl'); ?></td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The version of WordPress installed on your site.', 'civi-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('WP Version', 'civi-framework'); ?></td>
									<td><?php bloginfo('version'); ?></td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('Whether or not you have WordPress Multisite enabled.', 'civi-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('WP Multisite', 'civi-framework'); ?></td>
									<td>
										<?php if (is_multisite()) {
											echo '&#10004;';
										} else {
											echo '&ndash;';
										} ?>
									</td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The maximum amount of memory (RAM) that your site can use at one time.', 'civi-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('WP Memory Limit', 'civi-framework'); ?></td>
									<td>
										<?php
										$memory = civi_core_let_to_num(WP_MEMORY_LIMIT);

										if (function_exists('memory_get_usage')) {
											$server_memory = civi_core_let_to_num(@ini_get('memory_limit'));
											$memory = max($memory, $server_memory);
										}

										if ($memory < 134217728) {
											echo '<mark class="error">' . sprintf(__('%s - We recommend setting memory to at least 128MB. See: <a href="%s" target="_blank">Increasing memory allocated to PHP</a>', 'civi-framework'), size_format($memory), 'http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP') . '</mark>';
										} else {
											echo '<mark class="yes">' . size_format($memory) . '</mark>';
										}
										?>
									</td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('Displays whether or not WordPress is in Debug Mode.', 'civi-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('WP Debug Mode', 'civi-framework'); ?></td>
									<td>
										<?php if (defined('WP_DEBUG') && WP_DEBUG) {
											echo '<mark class="yes">&#10004;</mark>';
										} else {
											echo '&ndash;';
										} ?>
									</td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The current language used by WordPress. Default = English', 'civi-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('Language', 'civi-framework'); ?></td>
									<td><?php echo get_locale() ?></td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The current theme name', 'civi-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('Theme Name', 'civi-framework'); ?></td>
									<td><?php echo CIVI_THEME_NAME; ?></td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The current theme version', 'civi-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('Theme Version', 'civi-framework'); ?></td>
									<td><?php echo CIVI_THEME_VERSION; ?></td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('Installed plugins', 'civi-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('Activated Plugins', 'civi-framework'); ?></td>
									<td>
										<?php
										$all_plugins = get_plugins();
										foreach ($all_plugins as $key => $val) {
											if (is_plugin_active($key)) {
												echo $val['Name'] . ' ' . $val['Version'] . ', ';
											}
										}
										?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="about-wrap box">
					<div class="box-header">
						<span class="icon"><i class="lar la-lightbulb"></i></span>
						<?php esc_html_e('Server Environment', 'civi-framework'); ?>
					</div>
					<div class="box-body">
						<table class="wp-list-table widefat striped system" cellspacing="0">
							<tbody>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('Information about the web server that is currently hosting your site.', 'civi-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('Server Info', 'civi-framework'); ?></td>
									<td><?php esc_html_e($_SERVER['SERVER_SOFTWARE']); ?></td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The version of PHP installed on your hosting server.', 'civi-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('PHP Version', 'civi-framework'); ?></td>
									<td><?php if (function_exists('phpversion')) {
											$php_version = esc_html(phpversion());

											if (version_compare($php_version, '5.6', '<')) {
												echo '<mark class="error">' . esc_html__('Civi framework requires PHP version 5.6 or greater. Please contact your hosting provider to upgrade PHP version.', 'civi-framework') . '</mark>';
											} else {
												echo $php_version;
											}
										}
										?></td>
								</tr>
								<?php if (function_exists('ini_get')) : ?>
									<tr>
										<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The largest filesize that can be contained in one post.', 'civi-framework') . '">[?]</a>'; ?></td>
										<td class="title"><?php _e('PHP Post Max Size', 'civi-framework'); ?></td>
										<td><?php echo size_format(civi_core_let_to_num(ini_get('post_max_size'))); ?></td>
									</tr>
									<tr>
										<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The amount of time (in seconds) that your site will spend on a single operation before timing out (to avoid server lockups)', 'civi-framework') . '">[?]</a>'; ?></td>
										<td class="title"><?php _e('PHP Time Limit', 'civi-framework'); ?></td>
										<td><?php
											$time_limit = ini_get('max_execution_time');

											if ($time_limit > 0 && $time_limit < 180) {
												echo '<mark class="error">' . sprintf(__('%s - We recommend setting max execution time to at least 180. See: <a href="%s" target="_blank">Increasing max execution to PHP</a>', 'civi-framework'), $time_limit, 'http://codex.wordpress.org/Common_WordPress_Errors#Maximum_execution_time_exceeded') . '</mark>';
											} else {
												echo '<mark class="yes">' . $time_limit . '</mark>';
											}
											?></td>
									</tr>
									<tr>
										<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The maximum number of variables your server can use for a single function to avoid overloads.', 'civi-framework') . '">[?]</a>'; ?></td>
										<td class="title"><?php _e('PHP Max Input Vars', 'civi-framework'); ?></td>
										<td><?php
											$max_input_vars = ini_get('max_input_vars');

											if ($max_input_vars < 5000) {
												echo '<mark class="error">' . sprintf(__('%s - Max input vars limitation will truncate POST data such as menus. Required >= 5000', 'civi-framework'), $max_input_vars) . '</mark>';
											} else {
												echo '<mark class="yes">' . $max_input_vars . '</mark>';
											}
											?></td>
									</tr>
								<?php endif; ?>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The version of MySQL installed on your hosting server.', 'civi-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('MySQL Version', 'civi-framework'); ?></td>
									<td>
										<?php
										global $wpdb;
										echo $wpdb->db_version();
										?>
									</td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The largest filesize that can be uploaded to your WordPress installation.', 'civi-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('Max Upload Size', 'civi-framework'); ?></td>
									<td><?php echo size_format(wp_max_upload_size()); ?></td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The default timezone for your server.', 'civi-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('Default Timezone is UTC', 'civi-framework'); ?></td>
									<td><?php
										$default_timezone = date_default_timezone_get();
										if ('UTC' !== $default_timezone) {
											echo '<mark class="error">&#10005; ' . sprintf(__('Default timezone is %s - it should be UTC', 'civi-framework'), $default_timezone) . '</mark>';
										} else {
											echo '<mark class="yes">&#10004;</mark>';
										} ?>
									</td>
								</tr>
								<?php
								$checks = array();
								// fsockopen/cURL
								$checks['fsockopen_curl']['name'] = 'fsockopen/cURL';
								$checks['fsockopen_curl']['help'] = '<a href="#" class="hint--right" aria-label="' . esc_attr__('Plugins may use it when communicating with remote services.', 'civi-framework') . '">[?]</a>';
								if (function_exists('fsockopen') || function_exists('curl_init')) {
									$checks['fsockopen_curl']['success'] = true;
								} else {
									$checks['fsockopen_curl']['success'] = false;
									$checks['fsockopen_curl']['note'] = __('Your server does not have fsockopen or cURL enabled. Please contact your hosting provider to enable it.', 'civi-framework') . '</mark>';
								}
								// DOMDocument
								$checks['dom_document']['name'] = 'DOMDocument';
								$checks['dom_document']['help'] = '<a href="#" class="hint--right" aria-label="' . esc_attr__('WordPress Importer use DOMDocument.', 'civi-framework') . '">[?]</a>';
								if (class_exists('DOMDocument')) {
									$checks['dom_document']['success'] = true;
								} else {
									$checks['dom_document']['success'] = false;
									$checks['dom_document']['note'] = sprintf(__('Your server does not have <a href="%s">the DOM extension</a> class enabled. Please contact your hosting provider to enable it.', 'civi-framework'), 'http://php.net/manual/en/intro.dom.php') . '</mark>';
								}
								// XMLReader
								$checks['xml_reader']['name'] = 'XMLReader';
								$checks['xml_reader']['help'] = '<a href="#" class="hint--right" aria-label="' . esc_attr__('WordPress Importer use XMLReader.', 'civi-framework') . '">[?]</a>';
								if (class_exists('XMLReader')) {
									$checks['xml_reader']['success'] = true;
								} else {
									$checks['xml_reader']['success'] = false;
									$checks['xml_reader']['note'] = sprintf(__('Your server does not have <a href="%s">the XMLReader extension</a> class enabled. Please contact your hosting provider to enable it.', 'civi-framework'), 'http://php.net/manual/en/intro.xmlreader.php') . '</mark>';
								}
								// WP Remote Get Check
								$checks['wp_remote_get']['name'] = __('Remote Get', 'civi-framework');
								$checks['wp_remote_get']['help'] = '<a href="#" class="hint--right" aria-label="' . esc_attr__('Retrieve the raw response from the HTTP request using the GET method.', 'civi-framework') . '">[?]</a>';
								$response = wp_remote_get(CIVI_PLUGIN_URL . 'assets/test.txt');

								if (!is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300) {
									$checks['wp_remote_get']['success'] = true;
								} else {
									$checks['wp_remote_get']['note'] = __(' WordPress function <a href="https://codex.wordpress.org/Function_Reference/wp_remote_get">wp_remote_get()</a> test failed. Please contact your hosting provider to enable it.', 'civi-framework');
									if (is_wp_error($response)) {
										$checks['wp_remote_get']['note'] .= ' ' . sprintf(__('Error: %s', 'civi-framework'), sanitize_text_field($response->get_error_message()));
									} else {
										$checks['wp_remote_get']['note'] .= ' ' . sprintf(__('Status code: %s', 'civi-framework'), sanitize_text_field($response['response']['code']));
									}
									$checks['wp_remote_get']['success'] = false;
								}
								foreach ($checks as $check) {
									$mark = !empty($check['success']) ? 'yes' : 'error';
								?>
									<tr>
										<td class="help"><?php echo isset($check['help']) ? $check['help'] : ''; ?></td>
										<td class="title"><?php esc_html_e($check['name']); ?></td>
										<td>
											<mark class="<?php echo $mark; ?>">
												<?php echo !empty($check['success']) ? '&#10004' : '&#10005'; ?><?php echo !empty($check['note']) ? wp_kses_data($check['note']) : ''; ?>
											</mark>
										</td>
									</tr>
								<?php
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php
		}

		public function import_page_callback()
		{
			$import_issues = Civi_Importer::get_import_issues();
			$ignore_import_issues = apply_filters('civi_ignore_import_issues', false);

		?>
			<div class="civi-wrap about-wrap">

				<?php
				/**
				 * Action: civi_page_import_before_content
				 */
				do_action('civi_page_import_before_content');
				?>

				<!-- Important Notes -->
				<?php require_once CIVI_PLUGIN_DIR . 'includes/import/views/box-import-notes.php'; ?>
				<!-- /Important Notes -->

				<?php if (!empty($import_issues) && !$ignore_import_issues) : ?>
					<!-- Issues -->
					<?php require_once CIVI_PLUGIN_DIR . 'includes/import/views/box-import-issues.php'; ?>
					<!-- /Issues -->
				<?php else : ?>
					<!-- Import Demos -->
					<?php require_once CIVI_PLUGIN_DIR . 'includes/import/views/box-import-demos.php'; ?>
					<!-- /Import Demos -->
				<?php endif; ?>

				<?php
				/**
				 * Action: civi_page_import_after_content
				 */
				do_action('civi_page_import_after_content');
				?>

			</div>
		<?php
		}

		public function export_page_callback()
		{
			$export_items = Civi_Exporter::get_export_items();
		?>
			<div class="about-wrap civi-box civi-box--gray civi-box--export">
				<div class="civi-box__body grid columns-3">

					<?php
					/**
					 * Action: civi_box_export_before_content
					 */
					do_action('civi_box_export_before_content');
					?>

					<?php if (!empty($export_items)) : ?>
						<?php foreach ($export_items as $item) : ?>
							<?php if (isset($item['name'], $item['action'], $item['icon'])) : ?>
								<!-- Export <?php esc_html_e($item['name']); ?>-->
								<div class="civi-export-item civi-export-item--<?php echo esc_attr(sanitize_title($item['name'])); ?>">
									<form action="<?php echo esc_url(admin_url('/admin-post.php')); ?>" method="POST" class="civi-export-item__form">
										<?php if (isset($item['description'])) : ?>
											<span class="civi-export-item__help hint--right" aria-label="<?php echo esc_attr($item['description']); ?>"><i class="fal fa-question-circle"></i></span>
										<?php endif; ?>

										<input type="hidden" name="_wpnonce" value="<?php echo esc_attr(wp_create_nonce($item['action'])); ?>">
										<input type="hidden" name="action" value="<?php echo esc_attr($item['action']); ?>">

										<p class="civi-export-item__name"><i class="<?php echo esc_attr($item['icon']); ?>"></i><?php esc_html_e($item['name']); ?>
										</p>

										<p class="civi-export-item__description"><?php esc_html_e($item['description']); ?></p>

										<div class="civi-export-item__icon<?php echo esc_attr(isset($item['input_file_name']) && $item['input_file_name'] ? ' civi-export-item__icon--has-file-name-input' : ''); ?>">

											<?php if (isset($item['input_file_name'], $item['default_file_name']) && $item['input_file_name']) : ?>
												<input type="text" name="<?php echo esc_attr(sanitize_title($item['name']) . '-file-name'); ?>" id="<?php echo esc_attr(sanitize_title($item['name']) . '-file-name'); ?>" class="civi-export-item__input" value="<?php echo esc_attr($item['default_file_name']); ?>">
											<?php endif; ?>
										</div>

										<div class="civi-export-item__footer">
											<?php if (isset($item['export_page_url']) && !empty($item['export_page_url'])) : ?>
												<a href="<?php echo esc_url($item['export_page_url']); ?>" class="button civi-export-item__button"><?php esc_html_e('Export', 'civi-framework'); ?>
													<i class="las la-download"></i></a>
											<?php else : ?>
												<button type="submit" name="export" class="button civi-export-item__button"><?php esc_html_e('Export', 'civi-framework'); ?>
													<i class="las la-download"></i></button>
											<?php endif; ?>
										</div>
									</form>
								</div>
								<!-- /Export <?php esc_html_e($item['name']); ?> -->
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endif; ?>

					<?php
					/**
					 * Action: civi_box_export_after_content
					 */
					do_action('civi_box_export_after_content');
					?>
				</div>
			</div>
		<?php
		}

		/**
		 * Redirect the setup page on first activation
		 */
		public function redirect()
		{
			// Bail if no activation redirect transient is set
			if (!get_transient('_civi_activation_redirect')) {
				return;
			}

			if (!current_user_can('manage_options')) {
				return;
			}

			// Delete the redirect transient
			delete_transient('_civi_activation_redirect');

			// Bail if activating from network, or bulk, or within an iFrame
			if (is_network_admin() || isset($_GET['activate-multi']) || defined('IFRAME_REQUEST')) {
				return;
			}

			if ((isset($_GET['action']) && 'upgrade-plugin' == $_GET['action']) && (isset($_GET['plugin']) && strstr($_GET['plugin'], 'civi-framework.php'))) {
				return;
			}

			wp_redirect(admin_url('admin.php?page=civi_setup'));
			exit;
		}

		/**
		 * Create page on first activation
		 * @param $title
		 * @param $content
		 * @param $option
		 */
		private function create_page($title, $content, $option)
		{
			$page_data = array(
				'post_status' => 'publish',
				'post_type' => 'page',
				'post_author' => 1,
				'post_name' => sanitize_title($title),
				'post_title' => $title,
				'post_content' => $content,
				'post_parent' => 0,
				'comment_status' => 'closed'
			);
			$page_id = wp_insert_post($page_data);
			if ($option) {
				$config = get_option(CIVI_OPTIONS_NAME);
				$config[$option] = $page_id;
				update_option(CIVI_OPTIONS_NAME, $config);
			}
		}

		/**
		 * Output page setup
		 */
		public function setup_page()
		{
			$step = !empty($_GET['step']) ? absint(wp_unslash($_GET['step'])) : 1;
			if (3 === $step && !empty($_POST)) {
				$create_pages = isset($_POST['civi-create-page']) ? civi_clean(wp_unslash($_POST['civi-create-page'])) : array();
				$page_titles = isset($_POST['civi-page-title']) ? civi_clean(wp_unslash($_POST['civi-page-title'])) : array();
				$pages_to_create = array(
					'dashboard_employer' => '[civi_dashboard]',
					'dashboard_candidate' => '[civi_candidates]',
					'meetings' => '[civi_meetings]',
					'candidate_meetings' => '[civi_candidate_meetings]',
					'employer_settings' => '[civi_settings]',
					'candidate_settings' => '[civi_candidate_settings]',
					'jobs_dashboard' => '[civi_jobs]',
					'jobs_submit' => '[civi_jobs_submit]',
					'jobs_performance' => '[civi_jobs_performance]',
					'applicants' => '[civi_applicants]',
					'candidates' => '[civi_candidates]',
					'user_package' => '[civi_user_package]',
					'company' => '[civi_company]',
					'my_jobs' => '[civi_my_jobs]',
					'messages' => '[civi_messages]',
					'package' => '[civi_package]',
					'payment' => '[civi_payment]',
					'candidate_company' => '[civi_candidate_company]',
					'payment_completed' => '[civi_payment_completed]',
					'candidate_reviews' => '[civi_candidate_my_review]',
					'candidate_profile' => '[civi_candidate_profile]',
					'candidate_membership' => '[civi_candidate_membership]',
				);
				foreach ($pages_to_create as $page => $content) {
					if (!isset($create_pages[$page]) || empty($page_titles[$page])) {
						continue;
					}
					$this->create_page(sanitize_text_field($page_titles[$page]), $content, 'civi_' . $page . '_page_id');
				}
			}
		?>
			<div class="civi-setup-wrap civi-wrap about-wrap setup-wrap">
				<h3><?php esc_html_e('Civi Setup', 'civi-framework'); ?></h3>
				<ul class="civi-setup-steps">
					<li class="<?php if ($step === 1) echo 'civi-setup-active-step'; ?>"><?php esc_html_e('1. Introduction', 'civi-framework'); ?></li>
					<li class="<?php if ($step === 2) echo 'civi-setup-active-step'; ?>"><?php esc_html_e('2. Page Setup', 'civi-framework'); ?></li>
					<li class="<?php if ($step === 3) echo 'civi-setup-active-step'; ?>"><?php esc_html_e('3. Done', 'civi-framework'); ?></li>
				</ul>

				<?php if (1 === $step) : ?>

					<h3><?php esc_html_e('Setup Wizard Introduction', 'civi-framework'); ?></h3>
					<p><?php _e('Thanks for installing <em>Civi</em>!', 'civi-framework'); ?></p>
					<p><?php esc_html_e('This setup wizard will help you get started by creating the pages for jobs submission, jobs management, profile management, listing jobs, jobs wishlist, jobs booking...', 'civi-framework'); ?></p>
					<p><?php printf(__('If you want to skip the wizard and setup the pages and shortcodes yourself manually, the process is still relatively simple. Refer to the %sdocumentation%s for help.', 'civi-framework'), '<a href="#"', '</a>'); ?></p>

					<p class="submit">
						<a href="<?php echo esc_url(add_query_arg('step', 2)); ?>" class="button button-primary"><?php esc_html_e('Continue to page setup', 'civi-framework'); ?></a>
						<a href="<?php echo esc_url(admin_url('admin.php?page=civi_setup&step=3')); ?>" class="button"><?php esc_html_e('Skip setup. I will setup the plugin manually (Not Recommended)', 'civi-framework'); ?></a>
					</p>

				<?php endif; ?>
				<?php if (2 === $step) : ?>

					<h3><?php esc_html_e('Page Setup', 'civi-framework'); ?></h3>

					<p><?php printf(__('<em>civi-framework</em> includes %1$sshortcodes%2$s which can be used within your %3$spages%2$s to output content. These can be created for you below. For more information on the civi-framework shortcodes view the %4$sshortcode documentation%2$s.', 'civi-framework'), '<a href="https://codex.wordpress.org/shortcode" title="What is a shortcode?" target="_blank" class="help-page-link">', '</a>', '<a href="http://codex.wordpress.org/Pages" target="_blank" class="help-page-link">', '<a href="#" target="_blank" class="help-page-link">'); ?></p>

					<form action="<?php echo esc_url(add_query_arg('step', 3)); ?>" method="post">
						<table class="civi-shortcodes widefat">
							<thead>
								<tr>
									<th>&nbsp;</th>
									<th><?php esc_html_e('Page Title', 'civi-framework'); ?></th>
									<th><?php esc_html_e('Page Description', 'civi-framework'); ?></th>
									<th><?php esc_html_e('Content Shortcode', 'civi-framework'); ?></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><input type="checkbox" checked="checked" name="civi-create-page[dashboard]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Dashboard Employer', 'Default page title (wizard)', 'civi-framework')); ?>" name="civi-page-title[dashboard]" /></td>
									<td>
										<p><?php esc_html_e('This page show dashboard.', 'civi-framework'); ?></p>
									</td>
									<td><code>[civi_dashboard]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="civi-create-page[jobs_performance]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Jobs Performance', 'Default page title (wizard)', 'civi-framework')); ?>" name="civi-page-title[jobs_performance]" /></td>
									<td>
										<p><?php esc_html_e('This page show jobs performance.', 'civi-framework'); ?></p>
									</td>
									<td><code>[civi_jobs_performance]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="civi-create-page[jobs]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Jobs', 'Default page title (wizard)', 'civi-framework')); ?>" name="civi-page-title[jobs]" /></td>
									<td>
										<p><?php esc_html_e('This page show all jobs.', 'civi-framework'); ?></p>
									</td>
									<td><code>[civi_jobs]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="civi-create-page[submit_jobs]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('New Jobs', 'Default page title (wizard)', 'civi-framework')); ?>" name="civi-page-title[submit_jobs]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to add jobs to your website via the front-end.', 'civi-framework'); ?></p>
									</td>
									<td><code>[civi_jobs_submit]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="civi-create-page[applicants]" />
									</td>
									<td><input type="text" value="<?php echo esc_attr(_x('Applicants', 'Default page title (wizard)', 'civi-framework')); ?>" name="civi-page-title[applicants]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Applicants" via the front-end.', 'civi-framework'); ?></p>
									</td>
									<td><code>[civi_applicants]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="civi-create-page[civi_candidates]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Candidates For Employer', 'Default page title (wizard)', 'civi-framework')); ?>" name="civi-page-title[civi_candidates]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Candidates For Employer" via the front-end.', 'civi-framework'); ?></p>
									</td>
									<td><code>[civi_candidates]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="civi-create-page[package]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('User Packages', 'Default page title (wizard)', 'civi-framework')); ?>" name="civi-page-title[package]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "User Package" via the front-end.', 'civi-framework'); ?></p>
									</td>
									<td><code>[civi_user_package]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="civi-create-page[messages]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Messages Employer', 'Default page title (wizard)', 'civi-framework')); ?>" name="civi-page-title[messages]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Messages Employer" via the front-end.', 'civi-framework'); ?></p>
									</td>
									<td><code>[civi_messages]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="civi-create-page[company]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Company', 'Default page title (wizard)', 'civi-framework')); ?>" name="civi-page-title[packages]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Company" via the front-end.', 'civi-framework'); ?></p>
									</td>
									<td><code>[civi_company]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="civi-create-page[submit_company]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('New Company', 'Default page title (wizard)', 'civi-framework')); ?>" name="civi-page-title[submit_company]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Company" via the front-end.', 'civi-framework'); ?></p>
									</td>
									<td><code>[civi_submit_company]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="civi-create-page[settings]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Settings Employer', 'Default page title (wizard)', 'civi-framework')); ?>" name="civi-page-title[settings]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Settings Employer" via the front-end.', 'civi-framework'); ?></p>
									</td>
									<td><code>[civi_settings]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="civi-create-page[meetings]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Meetings Employer', 'Default page title (wizard)', 'civi-framework')); ?>" name="civi-page-title[meetings]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Meetings Employer" via the front-end.', 'civi-framework'); ?></p>
									</td>
									<td><code>[civi_meetings]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="civi-create-page[package]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Packages', 'Default page title (wizard)', 'civi-framework')); ?>" name="civi-page-title[package]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Packages" via the front-end.', 'civi-framework'); ?></p>
									</td>
									<td><code>[civi_package]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="civi-create-page[payment]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Payment', 'Default page title (wizard)', 'civi-framework')); ?>" name="civi-page-title[payment]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Payment" via the front-end.', 'civi-framework'); ?></p>
									</td>
									<td><code>[civi-payment]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="civi-create-page[payment_completed]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Payment Completed', 'Default page title (wizard)', 'civi-framework')); ?>" name="civi-page-title[payment_completed]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Payment Completed" via the front-end.', 'civi-framework'); ?></p>
									</td>
									<td><code>[civi_payment_completed]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="civi-create-page[candidate_dashboard]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Dashboard Candidate', 'Default page title (wizard)', 'civi-framework')); ?>" name="civi-page-title[candidate_dashboard]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Dashboard Candidate" via the front-end.', 'civi-framework'); ?></p>
									</td>
									<td><code>[civi_candidate_dashboard]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="civi-create-page[candidate_settings]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Candidate Settings', 'Default page title (wizard)', 'civi-framework')); ?>" name="civi-page-title[candidate_settings]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Candidate Settings" via the front-end.', 'civi-framework'); ?></p>
									</td>
									<td><code>[civi_candidate_settings]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="civi-create-page[candidate_company]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Candidate Company', 'Default page title (wizard)', 'civi-framework')); ?>" name="civi-page-title[candidate_company]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Candidate Company" via the front-end.', 'civi-framework'); ?></p>
									</td>
									<td><code>[civi_candidate_company]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="civi-create-page[candidate_profile]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Candidate Profile', 'Default page title (wizard)', 'civi-framework')); ?>" name="civi-page-title[candidate_profile]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Candidate Profile" via the front-end.', 'civi-framework'); ?></p>
									</td>
									<td><code>[civi_candidate_profile]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="civi-create-page[my_review]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('My Review', 'Default page title (wizard)', 'civi-framework')); ?>" name="civi-page-title[my_review]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "My Review" via the front-end.', 'civi-framework'); ?></p>
									</td>
									<td><code>[civi_candidate_my_review]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="civi-create-page[candidate_meetings]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Candidate Meetings', 'Default page title (wizard)', 'civi-framework')); ?>" name="civi-page-title[candidate_meetings]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Candidate Meetings" via the front-end.', 'civi-framework'); ?></p>
									</td>
									<td><code>[civi_candidate_meetings]</code></td>
									<!--  Membersip -->
									<tr>
									<td><input type="checkbox" checked="checked" name="civi-create-page[candidate_membership]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Candidate Membership', 'Default page title (wizard)', 'civi-framework')); ?>" name="civi-page-title[candidate_membership]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Candidate Membership" via the front-end.', 'civi-framework'); ?></p>
									</td>
									<td><code>[civi_candidate_membership]</code></td>
								</tr>
								<!-- End Membership -->
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="4">
										<input type="submit" class="button button-primary" value="<?php esc_html_e('Create selected pages', 'civi-framework'); ?>" />
										<a href="<?php echo esc_url(add_query_arg('step', 3)); ?>" class="button"><?php esc_html_e('Skip this step', 'civi-framework'); ?></a>
									</th>
								</tr>
							</tfoot>
						</table>
					</form>

				<?php endif; ?>
				<?php if (3 === $step) : ?>

					<h3><?php esc_html_e('All Done!', 'civi-framework'); ?></h3>

					<p><?php esc_html_e('Looks like you\'re all set to start using the plugin. In case you\'re wondering where to go next:', 'civi-framework'); ?></p>

					<ul class="civi-next-steps">
						<li>
							<a href="<?php echo admin_url('themes.php?page=civi-framework'); ?>"><?php esc_html_e('Plugin settings', 'civi-framework'); ?></a>
						</li>
						<li>
							<a href="<?php echo admin_url('post-new.php?post_type=jobs'); ?>"><?php esc_html_e('Add a jobs the back-end', 'civi-framework'); ?></a>
						</li>
						<?php if ($permalink = civi_get_permalink('jobs')) : ?>
							<li>
								<a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('Show all jobs', 'civi-framework'); ?></a>
							</li>
						<?php endif; ?>
						<?php if ($permalink = civi_get_permalink('submit_jobs')) : ?>
							<li>
								<a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('Add a jobs via the front-end', 'civi-framework'); ?></a>
							</li>
						<?php endif; ?>
						<?php if ($permalink = civi_get_permalink('jobs_dashboard')) : ?>
							<li>
								<a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('View user jobs', 'civi-framework'); ?></a>
							</li>
						<?php endif; ?>
						<?php if ($permalink = civi_get_permalink('my_profile')) : ?>
							<li>
								<a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('View user profile', 'civi-framework'); ?></a>
							</li>
						<?php endif; ?>
						<?php if ($permalink = civi_get_permalink('my_booking')) : ?>
							<li>
								<a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('View my booking', 'civi-framework'); ?></a>
							</li>
						<?php endif; ?>
						<?php if ($permalink = civi_get_permalink('bookings')) : ?>
							<li>
								<a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('View user bookings', 'civi-framework'); ?></a>
							</li>
						<?php endif; ?>
						<?php if ($permalink = civi_get_permalink('packages')) : ?>
							<li>
								<a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('View packages', 'civi-framework'); ?></a>
							</li>
						<?php endif; ?>
						<?php if ($permalink = civi_get_permalink('payment')) : ?>
							<li>
								<a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('View payment', 'civi-framework'); ?></a>
							</li>
						<?php endif; ?>
						<?php if ($permalink = civi_get_permalink('country')) : ?>
							<li>
								<a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('View country detail', 'civi-framework'); ?></a>
							</li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>
			</div>
<?php
		}
	}
}
