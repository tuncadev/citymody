<?php

if (!defined("ABSPATH")) {
	exit();
}

if (!class_exists("Civi_Enqueue")) {
	/**
	 *  Class Civi_Enqueue
	 */
	class Civi_Enqueue
	{
		/**
		 * The constructor.
		 */
		function __construct()
		{
			add_action("wp_enqueue_scripts", [$this, "enqueue_styles"]);
			add_action("wp_enqueue_scripts", [$this, "enqueue_scripts"]);
		}

		/**
		 * Register the stylesheets for the public-facing side of the site.
		 */
		public function enqueue_styles()
		{
			/*
			 * Enqueue Third Party Styles
			 */

			if (!class_exists('Civi_Framework')) {
				wp_enqueue_style(
					'font-awesome-all',
					CIVI_THEME_URI . '/assets/fonts/font-awesome/css/fontawesome-all.min.css',
					array(),
					'5.10.0',
					'all'
				);

				wp_enqueue_style(
					'line-awesome',
					CIVI_THEME_URI . '/assets/fonts/line-awesome/css/line-awesome.min.css',
					array(),
					'1.1.0',
					'all'
				);
			}


			wp_enqueue_style(
				"slick",
				CIVI_THEME_URI . "/assets/libs/slick/slick.css",
				[],
				"1.8.1",
				"all"
			);

			wp_enqueue_style(
				"slick-theme",
				CIVI_THEME_URI . "/assets/libs/slick/slick-theme.css",
				[],
				"1.8.1",
				"all"
			);

			wp_register_style(
				"civi-swiper",
				CIVI_THEME_URI . "/assets/libs/swiper/css/swiper.min.css",
				[],
				"5.3.8",
				"all"

			);


			wp_enqueue_style('growl', CIVI_THEME_URI . '/assets/libs/growl/css/jquery.growl.min.css', array(), '1.3.3', 'all');

			/*
			 * Enqueue Theme Styles
			 */
			wp_enqueue_style(
				"civi-font-inter",
				CIVI_THEME_URI . "/assets/fonts/inter/font.min.css"
			);

			$enable_rtl_mode = Civi_Helper::civi_get_option(
				"enable_rtl_mode",
				0
			);
			if (is_rtl() || $enable_rtl_mode) {
				wp_enqueue_style(
					"civi_bootstrap-rtl",
					CIVI_THEME_URI .
						"/assets/libs/bootstrap-rtl/bootstrap.min.css",
					[]
				);
				wp_enqueue_style(
					"civi_minify-style",
					CIVI_THEME_URI . "/assets/scss/rtl.min.css",
					[]
				);
				wp_enqueue_style(
					"civi_custom-rtl-style",
					CIVI_THEME_URI . "/assets/scss/custom-rtl.css",
					[]
				);
			} else {
				wp_enqueue_style(
					"civi_minify-style",
					CIVI_THEME_URI . "/assets/scss/style.min.css",
					[]
				);
				$time = time();
				wp_enqueue_style(
					"civi_main-style",
					get_stylesheet_uri() . "?v=" . $time
				);
			}
		}

		/**
		 * Register the JavaScript for the admin area.
		 */
		public function enqueue_scripts()
		{
			/*
			 * Enqueue Third Party Scripts
			 */

			wp_enqueue_script(
				"waypoints",
				CIVI_THEME_URI . "/assets/libs/waypoints/jquery.waypoints.js",
				["jquery"],
				"4.0.1",
				true
			);

			wp_enqueue_script(
				"matchheight",
				CIVI_THEME_URI .
					"/assets/libs/matchHeight/jquery.matchHeight-min.js",
				["jquery"],
				"0.7.0",
				true
			);

			wp_enqueue_script(
				"imagesloaded",
				CIVI_THEME_URI .
					"/assets/libs/imagesloaded/imagesloaded.min.js",
				["jquery"],
				null,
				true
			);

			wp_enqueue_script('growl', CIVI_THEME_URI . '/assets/libs/growl/js/jquery.growl.min.js', array('jquery'), '1.3.3', true);

			wp_register_script(
				"isotope-masonry",
				CIVI_THEME_URI . "/assets/libs/isotope/js/isotope.pkgd.min.js",
				["jquery"],
				"3.0.6",
				true
			);

			wp_register_script(
				"packery-mode",
				CIVI_THEME_URI .
					"/assets/libs/packery-mode/packery-mode.pkgd.min.js",
				["jquery"],
				"3.0.6",
				true
			);

			wp_enqueue_script(
				"validate",
				CIVI_THEME_URI . "/assets/libs/validate/jquery.validate.min.js",
				["jquery"],
				"1.17.0",
				true
			);

			wp_register_script(
				"civi-grid-layout",
				CIVI_THEME_URI . "/assets/js/grid-layout.min.js",
				[
					"jquery",
					"imagesloaded",
					"matchheight",
					"isotope-masonry",
					"packery-mode",
				],
				CIVI_THEME_VER,
				true
			);

			/*
			 * Enqueue Theme Scripts
			 */
			wp_enqueue_script(
				"civi-swiper-wrapper",
				CIVI_THEME_URI . "/assets/js/swiper-wrapper.min.js",
				["jquery"],
				CIVI_THEME_VER,
				true
			);

			$civi_swiper_js = [
				"prevText" => esc_html__("Prev", "civi"),
				"nextText" => esc_html__("Next", "civi"),
			];
			wp_localize_script(
				"civi-swiper-wrapper",
				'$civiSwiper',
				$civi_swiper_js
			);

			wp_enqueue_script(
				"civi-main-js",
				CIVI_THEME_URI . "/assets/js/main.min.js",
				["jquery"],
				CIVI_THEME_VER,
				true
			);

			wp_register_script(
				"civi-swiper",
				CIVI_THEME_URI . "/assets/libs/swiper/js/swiper.min.js",
				["jquery"],
				"5.3.8",
				true
			);

			wp_register_script('civi-group-widget-carousel', CIVI_ELEMENTOR_URI . '/assets/js/widgets/group-widget-carousel.js', array(
				'jquery',
				'civi-swiper',
				'civi-swiper-wrapper',
			), null, true);

			if (!class_exists('Civi_Framework')) {
				wp_enqueue_script(
					"slick",
					CIVI_THEME_URI . "/assets/libs/slick/slick.min.js",
					["jquery"],
					"1.8.1",
					true
				);
			}


			$ajax_url = admin_url("admin-ajax.php");
			$current_lang = apply_filters("wpml_current_language", null);

			if ($current_lang) {
				$ajax_url = add_query_arg("lang", $current_lang, $ajax_url);
			}

			$google_id = Civi_Helper::civi_get_option(
				"google_login_api",
				"912412937100-rbi096jb7j0c9e8ee2ge8mm1hjda24fb.apps.googleusercontent.com"
			);
			$sticky_header = Civi_Helper::get_setting("sticky_header");
			$float_header = Civi_Helper::get_setting("float_header");

			wp_localize_script("civi-main-js", "theme_vars", [
				"ajax_url" => esc_url($ajax_url),
				"google_id" => $google_id,
				"send_user_info" => esc_html__(
					"Sending user info,please wait...",
					"civi"
				),
				"forget_password" => esc_html__(
					"Checking your email,please wait...",
					"civi"
				),
				"change_password" => esc_html__(
					"Checking your password,please wait...",
					"civi"
				),
				"notice_cookie_enable" => Civi_Helper::civi_get_option('enable_cookie'),
				"enable_search_box_dropdown" => Civi_Helper::civi_get_option('enable_search_box_dropdown'),
				"notice_cookie_confirm" => isset($_COOKIE["notice_cookie_confirm"]) ? "yes" : "no",
				"notice_cookie_messages" => Civi_Cookie::instance()->get_notice_cookie_messages(),
				"sticky_header" => $sticky_header,
				"float_header" => $float_header,
			]);

			/*
			 * The comment-reply script.
			 */
			if (
				is_singular() &&
				comments_open() &&
				get_option("thread_comments")
			) {
				wp_enqueue_script("comment-reply");
			}
		}
	}
}
