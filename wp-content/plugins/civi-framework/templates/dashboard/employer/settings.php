<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!is_user_logged_in()) {
	civi_get_template('global/access-denied.php', array('type' => 'not_login'));
	return;
}
global $current_user;
wp_get_current_user();
$default_image           = CIVI_THEME_URI . '/assets/images/default-user-image.png';
$user_id                 = $current_user->ID;
$user_login              = $current_user->user_login;
$user_firstname          = get_the_author_meta('first_name', $user_id);
$user_lastname           = get_the_author_meta('last_name', $user_id);
$user_email              = get_the_author_meta('user_email', $user_id);
$author_mobile_number     = get_the_author_meta(CIVI_METABOX_PREFIX . 'author_mobile_number', $user_id);
$author_avatar_image_url = get_the_author_meta('author_avatar_image_url', $user_id);
$author_avatar_image_id  = get_the_author_meta('author_avatar_image_id', $user_id);
$user_demo = get_the_author_meta(CIVI_METABOX_PREFIX . 'user_demo', $user_id);

if (!$author_avatar_image_url) {
	$author_avatar_image_url = $default_image;
}
wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'settings');
wp_localize_script(
	CIVI_PLUGIN_PREFIX . 'settings',
	'civi_settings_vars',
	array(
		'ajax_url' => CIVI_AJAX_URL,
		'site_url' => get_site_url(),
	)
);
civi_get_avatar_enqueue();
?>
<div class="entry-my-page settings-dashboard">
	<div class="entry-title">
		<h4><?php esc_html_e('Profile Settings', 'civi-framework') ?></h4>
	</div>
	<div class="form-dashboard">
		<form class="block-from form-settings">
			<h6><?php esc_html_e('Personal info', 'civi-framework') ?></h6>
			<div class="civi-user-avatar">
				<div class="avatar civi-fields-avatar">
					<label><?php esc_html_e('Your photo', 'civi-framework'); ?></label>
					<div class="form-field">
						<div id="civi_avatar_errors" class="errors-log"></div>
						<div id="civi_avatar_container" class="file-upload-block preview">
							<div id="civi_avatar_view" data-image-id="<?php echo $author_avatar_image_id; ?>" data-image-url="<?php if (!empty($author_avatar_image_url)) {
																																	echo $author_avatar_image_url;
																																} ?>"></div>
							<div id="civi_add_avatar">
								<i class="far fa-arrow-from-bottom large"></i>
								<p id="civi_drop_avatar">
									<button type="button" id="civi_select_avatar"><?php esc_html_e('Upload', 'civi-framework') ?></button>
								</p>
							</div>
							<input type="hidden" class="avatar_url author_avatar_image_url form-control" name="author_avatar_image_url" value="<?php echo esc_attr($author_avatar_image_url); ?>" id="author_avatar_image_url">
							<input type="hidden" class="avatar_id author_avatar_image_id" name="author_avatar_image_id" value="<?php echo esc_attr($author_avatar_image_id); ?>" id="author_avatar_image_id" />
						</div>
					</div>
				</div>
				<p class="des-avatar"><?php esc_html_e('Update your photo manually, if the photo is not set the default Avatar will be the same as your login email account.', 'civi-framework') ?></p>
			</div>
			<div class="row">
				<div class="form-group col-md-6">
					<label for="user_firstname"><?php esc_html_e('First name', 'civi-framework') ?></label>
					<input type="text" id="user_firstname" name="user_firstname" value="<?php echo esc_attr($user_firstname); ?>">
				</div>
				<div class="form-group col-md-6">
					<label for="user_lastname"><?php esc_html_e('Last name', 'civi-framework') ?></label>
					<input type="text" id="user_lastname" name="user_lastname" value="<?php echo esc_attr($user_lastname); ?>">
				</div>
				<div class="form-group col-md-6">
					<label for="user_email"><?php esc_html_e('Email address', 'civi-framework') ?></label>
					<input type="email" id="user_email" name="user_email" value="<?php echo esc_attr($user_email); ?>">
				</div>
				<div class="form-group col-md-6">
					<label for="author_mobile_number"><?php esc_html_e('Phone number', 'civi-framework') ?></label>
					<input type="number" id="author_mobile_number" name="author_mobile_number" value="<?php echo esc_attr($author_mobile_number); ?>" placeholder="<?php esc_attr_e('Phone number', 'civi-framework'); ?>">
				</div>
			</div>
			<?php wp_nonce_field('civi_update_profile_ajax_nonce', 'civi_security_update_profile'); ?>
			<button type="submit" class="civi-button" id="civi_update_profile">
				<span><?php esc_html_e('Save changes', 'civi-framework'); ?></span>
				<span class="btn-loading"><i class="fal fa-spinner fa-spin large"></i></span>
			</button>
		</form>
		<form class="block-from form-password form-change-password">
			<h6><?php esc_html_e('Change password', 'civi-framework') ?></h6>
			<div class="row">
				<div class="form-group col-md-12">
					<label for="oldpass"><?php esc_html_e('Current password', 'civi-framework') ?></label>
					<input class="form-control" type="password" id="oldpass" name="oldpass" value="" placeholder="<?php esc_attr_e('Enter current password', 'civi-framework'); ?>">
					<span toggle="#oldpass" class="fa fa-fw fa-eye field-icon civi-toggle-password"></span>
				</div>
				<div class="form-group col-md-12">
					<label for="newpass"><?php esc_html_e('New password', 'civi-framework') ?></label>
					<input class="form-control" type="password" id="newpass" name="nnewpass" value="" placeholder="<?php esc_attr_e('Enter new password', 'civi-framework'); ?>">
					<span toggle="#newpass" class="fa fa-fw fa-eye field-icon civi-toggle-password"></span>
				</div>
				<div class="form-group col-md-12">
					<label for="confirmpass"><?php esc_html_e('Confirm new password', 'civi-framework') ?></label>
					<input class="form-control" type="password" id="confirmpass" name="confirmpass" value="" placeholder="<?php esc_attr_e('Enter confirm password', 'civi-framework'); ?>">
					<span toggle="#confirmpass" class="fa fa-fw fa-eye field-icon civi-toggle-password"></span>
				</div>
			</div>
			<?php wp_nonce_field('civi_change_password_ajax_nonce', 'civi_security_change_password'); ?>
			<div class="message"></div>
			<?php if ($user_demo == 'yes') : ?>
				<button class="civi-button btn-add-to-message" data-text="<?php echo esc_attr('This is a "Demo" account, so you can not change it', 'civi-framework'); ?>">
					<?php esc_html_e('Save changes', 'civi-framework'); ?>
				</button>
			<?php else : ?>
				<button type="submit" class="civi-button button-password" id="civi_change_pass">
					<span><?php esc_html_e('Save changes', 'civi-framework'); ?></span>
					<span class="btn-loading"><i class="fal fa-spinner fa-spin large"></i></span>
				</button>
			<?php endif; ?>
		</form>
		<?php if ($user_demo == 'yes') : ?>
			<a class="btn-add-to-message delete-account" data-text="<?php echo esc_attr('This is a "Demo" account so you not cant deactive it', 'civi-framework'); ?>" href="#"><?php esc_html_e('Deactive account', 'civi-framework') ?></a></li>
		<?php else :
			$nonce_url = wp_nonce_url(get_site_url() . '?action=civi_deactive_user&user_id=' . $user_id, 'deactive_' . $user_id); ?>
			<a href="<?php echo $nonce_url ?>" class="delete-account"><?php esc_html_e('Deactive account', 'civi-framework') ?></a>
		<?php endif; ?>
	</div>
</div>