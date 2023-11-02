<?php

/**
 * This file define demos for the theme.
 */

function civi_import_list_demos()
{
	return [
		"01" => [
			"name" => esc_html__("Civi", "civi"),
			"description" => esc_html__(
				"After importing this demo, your site will have all data like wp.getcivi.com",
				"civi"
			),
			"preview_image_url" =>
			CIVI_THEME_URI . "/assets/import/01/screenshot.png",
			"media_package_url" =>
			"https://data.uxper.co/civi/civi-media-01.zip",
		],
	];
}
add_filter("civi_import_demos", "civi_import_list_demos");
