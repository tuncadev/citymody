<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Helper functions
 */
if (!class_exists('Civi_Helper')) {

	class Civi_Helper
	{

		/**
		 * The constructor.
		 */
		function __construct()
		{
			add_action('delete_attachment', array($this, 'civi_delete_resized_images'));

			add_filter('body_class', array($this, 'civi_body_class'));
		}

		/**
		 * Get Setting
		 */
		public static function get_setting($key)
		{
			$option = '';
			$option = get_option_customize($key);
			return $option;
		}

		/**
		 * Get Option
		 */
		public static function civi_get_option($key, $default = '')
		{
			$option = '';
			if (class_exists('Civi_Framework')) {
				$option = civi_get_option($key, $default);
			}
			return (isset($option)) ? $option : $default;
		}

		/**
		 * Clean Variable
		 */
		public static function civi_clean($var)
		{
			if (is_array($var)) {
				return array_map('civi_clean', $var);
			} else {
				return is_scalar($var) ? sanitize_text_field($var) : $var;
			}
		}

		/**
		 * Get Setting
		 */
		public static function civi_body_class($classes)
		{

			$enable_rtl_mode  = Civi_Helper::civi_get_option('enable_rtl_mode', 0);

			if (is_rtl() || $enable_rtl_mode) {
				$classes[] = 'rtl';
			}

			return $classes;
		}

		/**
		 * Check has shortcode
		 */
		public static function civi_page_shortcode($shortcode = NULL)
		{

			$post = get_post(get_the_ID());

			if (empty($post->post_content)) {
				return false;
			}

			$found = false;

			if ($post->post_content === $shortcode) {
				$found = true;
			}

			// return our final results
			return $found;
		}

		/**
		 * Send email
		 */
		public static function civi_send_email($email, $email_type, $args = array())
		{

			$message = Civi_Helper::civi_get_option($email_type, '');
			$subject = Civi_Helper::civi_get_option('subject_' . $email_type, '');

			if (function_exists('icl_translate')) {
				$message = icl_translate('civi', 'civi_email_' . $message, $message);
				$subject = icl_translate('civi-framework', 'civi_email_subject_' . $subject, $subject);
			}
			$message = wpautop($message);
			$args['website_url'] = get_option('siteurl');
			$args['website_name'] = get_option('blogname');
			$args['user_email'] = $email;
			$user = get_user_by('email', $email);
			$args['username'] = $user->user_login;

			foreach ($args as $key => $val) {
				$subject = str_replace('%' . $key, $val, $subject);
				$message = str_replace('%' . $key, $val, $message);
			}
			$headers = apply_filters('civi_contact_mail_header', array('Content-Type: text/html; charset=UTF-8'));
			@wp_mail(
				$email,
				$subject,
				$message,
				$headers
			);
		}

		/**
		 * Allowed_html
		 */
		public static function civi_kses_allowed_html()
		{
			$allowed_tags = array(
				'a' => array(
					'id'    => array(),
					'class' => array(),
					'href'  => array(),
					'rel'   => array(),
					'title' => array(),
				),
				'abbr' => array(
					'title' => array(),
				),
				'b' => array(),
				'blockquote' => array(
					'cite'  => array(),
				),
				'cite' => array(
					'title' => array(),
				),
				'code' => array(),
				'del' => array(
					'datetime' => array(),
					'title' => array(),
				),
				'dd' => array(),
				'div' => array(
					'class' => array(),
					'title' => array(),
					'style' => array(),
				),
				'dl' => array(),
				'dt' => array(),
				'em' => array(),
				'h1' => array(),
				'h2' => array(),
				'h3' => array(),
				'h4' => array(),
				'h5' => array(),
				'h6' => array(),
				'i' => array(
					'class' => array(),
				),
				'img' => array(
					'alt'    => array(),
					'class'  => array(),
					'height' => array(),
					'src'    => array(),
					'width'  => array(),
				),
				'li' => array(
					'class' => array(),
				),
				'ol' => array(
					'class' => array(),
				),
				'p' => array(
					'class' => array(),
				),
				'q' => array(
					'cite' => array(),
					'title' => array(),
				),
				'span' => array(
					'class' => array(),
					'title' => array(),
					'style' => array(),
				),
				'strike' => array(),
				'strong' => array(),
				'ul' => array(
					'class' => array(),
				),
			);

			return $allowed_tags;
		}

		public static function civi_image_captcha($captcha)
		{

			if (empty($captcha)) return;

			// Generate a 50x24 standard captcha image
			$im = imagecreatetruecolor(50, 40);

			// Accent color
			$bg = imagecolorallocate($im, 0, 116, 86);

			// White color
			$fg = imagecolorallocate($im, 255, 255, 255);

			// Give the image a blue background
			imagefill($im, 0, 0, $bg);

			// Print the captcha text in the image
			// with random position & size
			imagestring($im, 24, 8, 11, $captcha, $fg);

			ob_start();

			// Finally output the captcha as
			// PNG image the browser
			imagepng($im);

			$imgData = ob_get_clean();

			// Free memory
			imagedestroy($im);

			echo '<img src="data:image/png;base64,' . base64_encode($imgData) . '" />';
		}

		/**
		 * Image size
		 */
		public static function civi_image_resize($data, $image_size)
		{
			if (preg_match('/\d+x\d+/', $image_size)) {
				$image_sizes = explode('x', $image_size);
				$image_src  = self::civi_image_resize_id($data, $image_sizes[0], $image_sizes[1], true);
			} else {
				if (!in_array($image_size, array('full', 'thumbnail'))) {
					$image_size = 'full';
				}
				$image_src = wp_get_attachment_image_src($data, $image_size);
				if ($image_src && !empty($image_src[0])) {
					$image_src = $image_src[0];
				}
			}
			return $image_src;
		}

		/**
		 * Image resize by url
		 */
		public static function civi_image_resize_url($url, $width = NULL, $height = NULL, $crop = true, $retina = false)
		{

			global $wpdb;

			if (empty($url))
				return new WP_Error('no_image_url', esc_html__('No image URL has been entered.', 'civi'), $url);

			if (class_exists('Jetpack') && method_exists('Jetpack', 'get_active_modules') && in_array('photon', Jetpack::get_active_modules())) {
				$args_crop = array(
					'resize' => $width . ',' . $height,
					'crop' => '0,0,' . $width . 'px,' . $height . 'px'
				);
				$url = jetpack_photon_url($url, $args_crop);
			}

			// Get default size from database
			$width = ($width) ? $width : get_option('thumbnail_size_w');
			$height = ($height) ? $height : get_option('thumbnail_size_h');

			// Allow for different retina sizes
			$retina = $retina ? ($retina === true ? 2 : $retina) : 1;

			// Get the image file path
			$file_path = parse_url($url);
			$file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path['path'];

			// Check for Multisite
			if (is_multisite()) {
				global $blog_id;
				$blog_details = get_blog_details($blog_id);
				$file_path = str_replace($blog_details->path, '/', $file_path);
				//$file_path = str_replace($blog_details->path . 'files/', '/wp-content/blogs.dir/' . $blog_id . '/files/', $file_path);
			}

			// Destination width and height variables
			$dest_width = $width * $retina;
			$dest_height = $height * $retina;

			// File name suffix (appended to original file name)
			$suffix = "{$dest_width}x{$dest_height}";

			// Some additional info about the image
			$info = pathinfo($file_path);
			$dir = $info['dirname'];
			$ext = $name = '';
			if (!empty($info['extension'])) {
				$ext = $info['extension'];
				$name = wp_basename($file_path, ".$ext");
			}

			if ('bmp' == $ext) {
				return new WP_Error('bmp_mime_type', esc_html__('Image is BMP. Please use either JPG or PNG.', 'civi'), $url);
			}

			// Suffix applied to filename
			$suffix = "{$dest_width}x{$dest_height}";

			// Get the destination file name
			$dest_file_name = "{$dir}/{$name}-{$suffix}.{$ext}";

			if (!file_exists($dest_file_name)) {

				/*
	             *  Bail if this image isn't in the Media Library.
	             *  We only want to resize Media Library images, so we can be sure they get deleted correctly when appropriate.
	             */
				$query = $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE guid='%s'", $url);
				$get_attachment = $wpdb->get_results($query);
				// if (!$get_attachment)
				//     return array('url' => $url, 'width' => $width, 'height' => $height);

				// Load Wordpress Image Editor
				$editor = wp_get_image_editor($file_path);
				if (is_wp_error($editor))
					return array('url' => $url, 'width' => $width, 'height' => $height);

				// Get the original image size
				$size = $editor->get_size();
				$orig_width = $size['width'];
				$orig_height = $size['height'];

				$src_x = $src_y = 0;
				$src_w = $orig_width;
				$src_h = $orig_height;

				if ($crop) {

					$cmp_x = $orig_width / $dest_width;
					$cmp_y = $orig_height / $dest_height;

					// Calculate x or y coordinate, and width or height of source
					if ($cmp_x > $cmp_y) {
						$src_w = round($orig_width / $cmp_x * $cmp_y);
						$src_x = round(($orig_width - ($orig_width / $cmp_x * $cmp_y)) / 2);
					} else if ($cmp_y > $cmp_x) {
						$src_h = round($orig_height / $cmp_y * $cmp_x);
						$src_y = round(($orig_height - ($orig_height / $cmp_y * $cmp_x)) / 2);
					}
				}

				// Time to crop the image!
				$editor->crop($src_x, $src_y, $src_w, $src_h, $dest_width, $dest_height);

				// Now let's save the image
				$saved = $editor->save($dest_file_name);

				// Get resized image information
				$resized_url = str_replace(wp_basename($url), wp_basename($saved['path']), $url);
				$resized_width = $saved['width'];
				$resized_height = $saved['height'];
				$resized_type = $saved['mime-type'];

				// Add the resized dimensions to original image metadata (so we can delete our resized images when the original image is delete from the Media Library)
				if ($get_attachment) {
					$metadata = wp_get_attachment_metadata($get_attachment[0]->ID);
					if (isset($metadata['image_meta'])) {
						$metadata['image_meta']['resized_images'][] = $resized_width . 'x' . $resized_height;
						wp_update_attachment_metadata($get_attachment[0]->ID, $metadata);
					}
				}

				// Create the image array
				$image_array = array(
					'url' => $resized_url,
					'width' => $resized_width,
					'height' => $resized_height,
					'type' => $resized_type
				);
			} else {
				$image_array = array(
					'url' => str_replace(wp_basename($url), wp_basename($dest_file_name), $url),
					'width' => $dest_width,
					'height' => $dest_height,
					'type' => $ext
				);
			}

			// Return image array
			return $image_array;
		}

		/**
		 * Image resize by id
		 */
		public static function civi_image_resize_id($images_id, $width = NULL, $height = NULL, $crop = true, $retina = false)
		{
			$output = '';
			$image_src = wp_get_attachment_image_src($images_id, 'full');
			if ($image_src) {
				$resize = self::civi_image_resize_url($image_src[0], $width, $height, $crop, $retina);
				if ($resize != null && is_array($resize)) {
					$output = $resize['url'];
				}
			}
			return $output;
		}

		/**
		 * Delete resized images
		 */
		public static function civi_delete_resized_images($post_id)
		{
			// Get attachment image metadata
			$metadata = wp_get_attachment_metadata($post_id);
			if (!$metadata)
				return;

			// Do some bailing if we cannot continue
			if (!isset($metadata['file']) || !isset($metadata['image_meta']['resized_images']))
				return;
			$pathinfo = pathinfo($metadata['file']);
			$resized_images = $metadata['image_meta']['resized_images'];

			// Get Wordpress uploads directory (and bail if it doesn't exist)
			$wp_upload_dir = wp_upload_dir();
			$upload_dir = $wp_upload_dir['basedir'];
			if (!is_dir($upload_dir))
				return;

			// Delete the resized images
			foreach ($resized_images as $dims) {

				// Get the resized images filename
				$file = $upload_dir . '/' . $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-' . $dims . '.' . $pathinfo['extension'];

				// Delete the resized image
				@unlink($file);
			}
		}
	}
}
