<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('Civi_Profile')) {

	/**
	 * Class Civi_Profile
	 */
	class Civi_Profile
	{

		public function custom_user_profile_fields($user)
		{
		    ?>
			<h3><?php esc_html_e('Profile Info', 'civi-framework'); ?></h3>
			<table class="form-table">
				<tbody>
					<tr class="author-avatar-image-wrap">
						<th><label for="author_avatar_image_url"><?php echo esc_html__('Avatar', 'civi-framework'); ?></label></th>
						<td>
							<img class="show_author_avatar_image_url" src="<?php echo esc_attr(get_the_author_meta('author_avatar_image_url', $user->ID)); ?>" style="width: 96px;height: 96px; object-fit: cover;display: block;margin-bottom: 10px;">
							<input type="text" name="author_avatar_image_url" id="author_avatar_image_url" value="<?php echo esc_attr(get_the_author_meta('author_avatar_image_url', $user->ID)); ?>" style="display: block;margin-bottom: 10px;max-width: 350px;width: 100%;">
							<input type="hidden" name="author_avatar_image_id" id="author_avatar_image_id" value="<?php echo esc_attr(get_the_author_meta('author_avatar_image_id', $user->ID)); ?>">
							<input type='button' class="button-primary" value="Upload Image" id="uploadimage" />
						</td>
					</tr>
					<tr class="author-phone-number-wrap">
						<th><label for="<?php echo esc_attr(CIVI_METABOX_PREFIX . 'author_mobile_number'); ?>"><?php echo esc_html__('Phone', 'civi-framework'); ?></label></th>
						<td><input type="text" name="<?php echo esc_attr(CIVI_METABOX_PREFIX . 'author_mobile_number'); ?>" id="<?php echo esc_attr(CIVI_METABOX_PREFIX . 'author_mobile_number'); ?>" value="<?php echo esc_attr(get_the_author_meta(CIVI_METABOX_PREFIX . 'author_mobile_number', $user->ID)); ?>" class="regular-text"></td>
					</tr>
                    <tr class="author-user-demo-wrap">
                        <?php $user_selected = get_the_author_meta(CIVI_METABOX_PREFIX . 'user_demo', $user->ID); ?>
                        <th><label for="<?php echo esc_attr(CIVI_METABOX_PREFIX . 'user_demo'); ?>"><?php echo esc_html__('User Demo', 'civi-framework'); ?></label></th>
                        <td>
                            <select name="<?php echo esc_attr(CIVI_METABOX_PREFIX . 'user_demo'); ?>">
                                <option <?php if($user_selected == ''){?> selected <?php } ?> value=""><?php esc_html_e('No', 'civi-framework'); ?></option>
                                <option <?php if($user_selected == 'yes'){?> selected <?php } ?> value="yes"><?php esc_html_e('Yes', 'civi-framework'); ?></option>
                            </select>
                        </td>
                    </tr>
				</tbody>
			</table>
		<?php
		}

        public function user_package_available($user_id)
        {
            $package_id = get_the_author_meta(CIVI_METABOX_PREFIX . 'package_id', $user_id);
            if (empty($package_id)) {
                return 0;
            } else {
                $civi_package = new Civi_Package();
                $package_unlimited_time = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_unlimited_time', true);
                if ($package_unlimited_time == 0) {
                    $expired_date = $civi_package->get_expired_time($package_id, $user_id);
                    $today = time();
                    if ($today > $expired_date) {
                        return -1;
                    }
                }
                $package_num_places = get_the_author_meta(CIVI_METABOX_PREFIX . 'package_number_listings', $user_id);
                if ($package_num_places != -1 && $package_num_places < 1) {
                    return -2;
                }
            }
            return 1;
        }

		public function update_custom_user_profile_fields($user_id)
		{
			global $current_user;
			wp_get_current_user();

			if (current_user_can('edit_user', $user_id)) {

				$author_avatar_image_url = isset($_POST['author_avatar_image_url']) ? civi_clean(wp_unslash($_POST['author_avatar_image_url'])) : '';
				$author_avatar_image_id  = isset($_POST['author_avatar_image_id']) ? civi_clean(wp_unslash($_POST['author_avatar_image_id'])) : '';
				$author_mobile_number    = isset($_POST[CIVI_METABOX_PREFIX . 'author_mobile_number']) ? civi_clean(wp_unslash($_POST[CIVI_METABOX_PREFIX . 'author_mobile_number'])) : '';
                $user_demo    = isset($_POST[CIVI_METABOX_PREFIX . 'user_demo']) ? civi_clean(wp_unslash($_POST[CIVI_METABOX_PREFIX . 'user_demo'])) : '';

                update_user_meta($user_id, 'author_avatar_image_url', $author_avatar_image_url);
				update_user_meta($user_id, 'author_avatar_image_id', $author_avatar_image_id);
				update_user_meta($user_id, CIVI_METABOX_PREFIX . 'author_mobile_number', $author_mobile_number);
                update_user_meta($user_id, CIVI_METABOX_PREFIX . 'user_demo', $user_demo);
			}
		}

		function my_profile_upload_js()
		{
			wp_enqueue_media();
		?>
			<script type="text/javascript">
				jQuery(document).ready(function() {

					jQuery(document).find("input[id^='uploadimage']").on('click', function(e) {
						e.preventDefault();

						var button = jQuery(this),
							custom_uploader = wp.media({
								title: 'Insert image',
								library: {
									// uncomment the next line if you want to attach image to the current post
									// uploadedTo : wp.media.view.settings.post.id, 
									type: 'image'
								},
								button: {
									text: 'Use this image' // button label text
								},
								multiple: false // for multiple image selection set to true
							}).on('select', function() { // it also has "open" and "close" events 
								var attachment = custom_uploader.state().get('selection').first().toJSON();
								jQuery(button).removeClass('button').html('<img class="true_pre_image" src="' + attachment.url + '" style="max-width:95%;display:block;" />').next().val(attachment.id).next().show();
								jQuery('#author_avatar_image_url').val(attachment.url);
								jQuery('#author_avatar_image_id').val(attachment.id);
								jQuery('.show_author_avatar_image_url').attr('src', attachment.url);
							})
							.open();
					});
				});
			</script>
<?php
		}
	}
}
