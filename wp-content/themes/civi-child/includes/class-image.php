<?php
defined("ABSPATH") || exit();

class Civi_Image
{
	public static function get_attachment_info($attachment_id)
	{
		$attachment = get_post($attachment_id);
		$attachment_url = wp_get_attachment_url($attachment_id);

		if ($attachment === null) {
			return false;
		}

		$alt = get_post_meta($attachment->ID, "_wp_attachment_image_alt", true);

		if ("" === $alt) {
			$alt = $attachment->post_title;
		}

		return [
			"alt" => $alt,
			"caption" => $attachment->post_excerpt,
			"description" => $attachment->post_content,
			"href" => get_permalink($attachment->ID),
			"src" => $attachment_url,
			"title" => $attachment->post_title,
		];
	}

	/**
	 * Get post thumbnail in loop.
	 *
	 * @param array $args
	 *
	 * @return string HTML img tag.
	 */
	public static function get_the_post_thumbnail($args = [])
	{
		if (!empty($args["post_id"])) {
			$args["id"] = get_post_thumbnail_id($args["post_id"]);
		} else {
			$args["id"] = get_post_thumbnail_id(get_the_ID());
		}

		$attachment = self::get_attachment_by_id($args);

		return $attachment;
	}

	/**
	 * Print post thumbnail in loop.
	 *
	 * @param array $args
	 */
	public static function the_post_thumbnail($args = [])
	{
		$image = self::get_the_post_thumbnail($args);

		echo "{$image}";
	}

	/**
	 * Get post thumbnail url in loop.
	 *
	 * @param array $args
	 *
	 * @return string $attachment_url post thumbnail url
	 */
	public static function get_the_post_thumbnail_url($args = [])
	{
		if (isset($args["post_id"])) {
			$args["id"] = get_post_thumbnail_id($args["post_id"]);
		} else {
			$args["id"] = get_post_thumbnail_id(get_the_ID());
		}

		$attachment_url = self::get_attachment_url_by_id($args);

		return $attachment_url;
	}

	/**
	 * Print post thumbnail url in loop.
	 *
	 * @param array $args
	 */
	public static function the_post_thumbnail_url($args = [])
	{
		$url = self::get_the_post_thumbnail_url($args);

		echo esc_url($url);
	}

	public static function get_attachment_by_id($args = [])
	{
		$defaults = [
			"id" => "",
			"size" => "full",
			"width" => "",
			"height" => "",
			"crop" => true,
		];

		$args = wp_parse_args($args, $defaults);

		$image_full = self::get_attachment_info($args["id"]);

		if ($image_full === false) {
			return false;
		}

		$url = $image_full["src"];
		$cropped_image = self::get_image_cropped_url($url, $args);

		if ($cropped_image[0] === "") {
			return "";
		}

		$image_attributes = [
			"src" => $cropped_image[0],
			"alt" => $image_full["alt"],
		];

		if (isset($cropped_image[1])) {
			$image_attributes["width"] = $cropped_image[1];
		}

		$image = self::build_img_tag($image_attributes);

		// Wrap img with caption tags.
		if (
			isset($args["caption_enable"]) &&
			$args["caption_enable"] === true &&
			$image_full["caption"] !== ""
		) {
			$before = "<figure>";
			$after =
				'<figcaption class="wp-caption-text gallery-caption">' .
				$image_full["caption"] .
				"</figcaption></figure>";

			$image = $before . $image . $after;
		}

		return $image;
	}

	public static function the_attachment_by_id($args = [])
	{
		$attachment = self::get_attachment_by_id($args);

		echo "{$attachment}";
	}

	public static function get_attachment_url_by_id($args = [])
	{
		$id = $size = $width = $height = $crop = "";

		$defaults = [
			"id" => "",
			"size" => "full",
			"width" => "",
			"height" => "",
			"crop" => true,
			"details" => false,
		];

		$args = wp_parse_args($args, $defaults);
		extract($args);

		if ($id === "") {
			return "";
		}

		if ($details === false) {
			$url = wp_get_attachment_image_url($id, "full");
			$image_cropped = self::get_image_cropped_url($url, $args);

			return $image_cropped[0];
		} else {
			$image_full = self::get_attachment_info($id);
			$url = $image_full["src"];

			$image_cropped = self::get_image_cropped_url($url, $args);

			$full_details = $image_full;
			$full_details["cropped_image"] = $image_cropped[0];

			return $full_details;
		}
	}

	public static function the_attachment_url_by_id($args = [])
	{
		$url = self::get_attachment_url_by_id($args);

		echo esc_url($url);
	}

	/**
	 * @param string $url  Original image url.
	 * @param array  $args Array attributes.
	 *
	 * @return array|bool|string
	 */
	public static function get_image_cropped_url($url, $args = [])
	{
		extract($args);
		if ($url === false) {
			return [0 => ""];
		}

		if ($size === "full") {
			return [0 => $url];
		}

		if ($size !== "custom" && !preg_match("/(\d+)x(\d+)/", $size)) {
			$attachment_url = wp_get_attachment_image_url($args["id"], $size);

			if (!$attachment_url) {
				return [0 => $url];
			} else {
				return [0 => $attachment_url];
			}
		}

		if ($size !== "custom") {
			$_sizes = explode("x", $size);
			$width = $_sizes[0];
			$height = $_sizes[1];
		} else {
			if ($width === "") {
				$width = 9999;
			}

			if ($height === "") {
				$height = 9999;
			}
		}

		$width = (int) $width;
		$height = (int) $height;

		if ($width === 9999 || $height === 9999) {
			$crop = false;
		}

		if ($width !== "" && $height !== "" && function_exists("aq_resize")) {
			$crop_image = aq_resize($url, $width, $height, $crop, false);

			if (
				!empty($crop_image) &&
				is_array($crop_image) &&
				$crop_image[0] !== ""
			) {
				return $crop_image;
			}
		}

		return [0 => $url];
	}

	public static function elementor_parse_image_size(
		$settings = null,
		$default = "full",
		$image_size_key = "thumbnail"
	) {
		if (empty($settings)) {
			return $default;
		}

		if (
			isset($settings["thumbnail_default_size"]) &&
			"1" === $settings["thumbnail_default_size"]
		) {
			return $default;
		}

		if (isset($settings["{$image_size_key}_size"])) {
			if ($settings["{$image_size_key}_size"] === "custom") {
				$width =
					$settings["{$image_size_key}_custom_dimension"]["width"];
				$height =
					$settings["{$image_size_key}_custom_dimension"]["height"];

				if ($width === "") {
					$width = 9999;
				}

				if ($height === "") {
					$height = 9999;
				}

				return "{$width}x{$height}";
			} else {
				return $settings["{$image_size_key}_size"];
			}
		}

		return $default;
	}

	/**
	 * @param array $args
	 *
	 * @var array   $settings       Elementor settings or repeater item settings.
	 * @var string  $image_key      Name if image control.
	 * @var array   $size_settings  Elementor settings or custom array or null to use $settings.
	 * @var string  $image_size_key Name if image size control. Default same name with image key.
	 * @var array   $attributes     An array attributes that add to img tag.
	 *
	 * @return bool|string HTML img tag || false if errors.
	 */
	public static function get_elementor_attachment(array $args)
	{
		$defaults = [
			"settings" => [],
			"image_key" => "image",
			"size_settings" => [],
			"image_size_key" => "",
			"attributes" => [],
		];

		$args = wp_parse_args($args, $defaults);
		extract($args);

		if (empty($settings)) {
			return "";
		}

		if (empty($settings["{$image_key}"])) {
			return "";
		}

		$no_image_src = CIVI_IMAGES . "no-image.jpg";

		$image = $settings["{$image_key}"];

		// Default same name with $image_key
		if (empty($image_size_key)) {
			$image_size_key = $image_key;
		}

		// If image has no both id & url.
		if (empty($image["url"]) && empty($image["id"])) {
			return "";
		}

		// If image has id.
		if (!empty($image["id"])) {
			$attachment_args = [
				"id" => $image["id"],
			];

			// If not override. then use from $settings.
			if (empty($size_settings)) {
				$size_settings = $settings;
			}

			// Check if image has custom size.
			// Usage: `{name}_size` and `{name}_custom_dimension`, default `image_size` and `image_custom_dimension`.
			if (isset($size_settings["{$image_size_key}_size"])) {
				$image_size = $size_settings["{$image_size_key}_size"];

				// Get get image size.
				if ("custom" === $image_size) {
					$width =
						$size_settings["{$image_size_key}_custom_dimension"]["width"];
					$height =
						$size_settings["{$image_size_key}_custom_dimension"]["height"];

					if (empty($width)) {
						$width = 9999;

						$attachment_args["crop"] = false;
					}

					if (empty($height)) {
						$height = 9999;

						$attachment_args["crop"] = false;
					}

					$attachment_args["size"] = "{$width}x{$height}";
				} else {
					// WP Image Size like: full, thumbnail, large...
					$attachment_args["size"] = $image_size;
				}
			}

			$attachment = self::get_attachment_by_id($attachment_args);
		} else {
			$attributes["src"] = $image["url"];

			$attachment = self::build_img_tag($attributes);
		}

		return $attachment;
	}

	/**
	 * @param array $attributes
	 *
	 * @return string HTML img tag.
	 */
	public static function build_img_tag($attributes = [])
	{
		if (empty($attributes["src"])) {
			return "";
		}

		$attributes_str = "";

		if (!empty($attributes)) {
			foreach ($attributes as $attribute => $value) {
				$attributes_str .=
					" " . $attribute . '="' . esc_attr($value) . '"';
			}
		}

		$image = "<img " . $attributes_str . " />";

		return $image;
	}
}
