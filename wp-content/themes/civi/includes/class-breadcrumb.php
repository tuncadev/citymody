<?php

// Exit if accessed directly
if (!defined("ABSPATH")) {
	exit();
}

if (!class_exists("Civi_Breadcrumb")) {
	/**
	 *  Class Civi_Breadcrumb
	 */
	class Civi_Breadcrumb
	{
		public static function breadcrumb($args = "")
		{
			$default = [
				"type" => "list",
				"home_label" => esc_html__("Home", "civi"),
				"search_label" => esc_html__('Search Result of "%s"', "civi"),
				"404_label" => esc_html__("404 Not Found", "civi"),
				"category_label" => _x("%s", "category label", "civi"),
				"tag_label" => _x("%s", "tag label", "civi"),
				"taxonomy_label" => _x("%s", "taxonomy label", "civi"),
				"author_label" => _x("%s", "author label", "civi"),
				"attachment_label" => _x("%s", "attachment label", "civi"),
				"year_label" => _x("%s", "year label", "civi"),
				"month_label" => _x("%s", "month label", "civi"),
				"day_label" => _x("%s", "day label", "civi"),
				"post_type_label" => _x("%s", "post type label", "civi"),
				"joint_string" => esc_html__(" &gt; ", "civi"),
				"navi_element" => "",
				"elm_class" => "civi_breadcrumb",
				"elm_id" => "",
				"li_class" => "",
				"class_prefix" => "",
				"current_class" => "current",
				"indent" => 0,
				"echo" => true,
			];
			$default = apply_filters("civi_breadcrumb_default", $default);
			$args = wp_parse_args($args, $default);

			$elm = in_array($args["navi_element"], ["nav", "div", ""])
				? $args["navi_element"]
				: "div";
			$args["elm_id"] = is_array($args["elm_id"])
				? $default["elm_id"]
				: $args["elm_id"];
			$args["elm_id"] = preg_replace("/[^\w_-]+/", "", $args["elm_id"]);
			$args["elm_id"] = preg_replace("/^[\d_-]+/", "", $args["elm_id"]);

			$args["class_prefix"] = is_array($args["class_prefix"])
				? $default["class_prefix"]
				: $args["class_prefix"];
			$args["class_prefix"] = preg_replace(
				"/[^\w_-]+/",
				"",
				$args["class_prefix"]
			);
			$args["class_prefix"] = preg_replace(
				"/^[\d_-]+/",
				"",
				$args["class_prefix"]
			);

			$args["elm_class"] = self::sanitize_attr_classes(
				$args["elm_class"],
				$args["class_prefix"]
			);
			$args["li_class"] = self::sanitize_attr_classes(
				$args["li_class"],
				$args["class_prefix"]
			);
			$args["current_class"] = self::sanitize_attr_classes(
				$args["current_class"],
				$args["class_prefix"]
			);
			$args["current_class"] = $args["current_class"]
				? $args["current_class"]
				: $args["class_prefix"] . $default["current_class"];
			$args["echo"] = self::uniform_boolean(
				$args["echo"],
				$default["echo"]
			);

			$tabs = str_repeat("\t", (int) $args["indent"]);

			$breadcrumb_arr = self::get_breadcrumb_array($args);

			$elm_attrs = "";
			if ($args["elm_id"]) {
				$elm_attrs = ' id="' . $args["elm_id"] . '"';
			}
			if ($args["elm_class"]) {
				$elm_attrs .= ' class="' . $args["elm_class"] . '"';
			}

			$output = "";
			$elm_tabs = "";

			$output = "";
			if ($elm) {
				$elm_tabs = "\t";
				$output = $tabs . "<" . $elm;
				if ($elm_attrs) {
					$output .= $elm_attrs . ">\n";
				}
			}

			if ($args["type"] == "string") {
				$output = [];
				$cnt = 1;
				foreach ($breadcrumb_arr as $ancestor) {
					if ($cnt == count($breadcrumb_arr)) {
						$output[] =
							'<strong class="' .
							$args["current_class"] .
							'">' .
							apply_filters("the_title", $ancestor["title"]) .
							"</strong>";
					} else {
						$output[] =
							'<a href="' .
							$ancestor["link"] .
							'">' .
							apply_filters("the_title", $ancestor["title"]) .
							"</a>";
					}
					$cnt++;
				}
				$output =
					$elm_tabs .
					$tabs .
					implode(esc_html($args["joint_string"]), $output);
				$output = apply_filters(
					"civi_breadcrumb_after",
					$output,
					$args
				);
			} else {
				$output .= $elm_tabs . $tabs . "<ul";
				if (!$elm && $elm_attrs) {
					$output .= $elm_attrs;
				}
				$output .= ">\n";

				$output = apply_filters(
					"civi_breadcrumb_before",
					$output,
					$args
				);

				$cnt = 1;
				foreach ($breadcrumb_arr as $ancestor) {
					$classes = [];
					$classes[] = $args["class_prefix"] . "level-" . $cnt;
					if ($cnt == 1) {
						$classes[] = $args["class_prefix"] . "top";
					} else {
						$classes[] = $args["class_prefix"] . "sub";
					}
					if ($cnt == count($breadcrumb_arr)) {
						$classes[] = $args["class_prefix"] . "tail";
						$output .=
							$elm_tabs .
							$tabs .
							'	<li class="' .
							implode(" ", $classes);
						if ($args["li_class"]) {
							$output .= " " . $args["li_class"];
						}
						$output .= " " . $args["current_class"];
						$output .=
							'">' .
							apply_filters("the_title", $ancestor["title"]) .
							"</li>" .
							"\n";
					} else {
						$output .=
							$elm_tabs .
							$tabs .
							'	<li class="' .
							implode(" ", $classes);
						if ($args["li_class"]) {
							$output .= " " . $args["li_class"];
						}
						$output .=
							'"><a href="' .
							$ancestor["link"] .
							'">' .
							apply_filters("the_title", $ancestor["title"]) .
							"</a></li>" .
							"\n";
					}
					$cnt++;
				}
				$output = apply_filters(
					"civi_breadcrumb_after",
					$output,
					$args
				);

				$output .= $elm_tabs . $tabs . "</ul>" . "\n";
			}

			if ($elm) {
				$output .= $tabs . "</" . $elm . ">\n";
			}

			$output = apply_filters("civi_breadcrumb", $output, $args);

			if ($args["echo"]) {
				echo wp_kses_post($output);
			} else {
				return $output;
			}
		}

		public static function get_breadcrumb_array($args)
		{
			global $post;

			$breadcrumb_arr = [];
			$breadcrumb_arr[] = [
				"title" => $args["home_label"],
				"link" => home_url() . "/",
			];
			$breadcrumb_arr = self::add_posts_page_array($breadcrumb_arr);
			if (is_404()) {
				$breadcrumb_arr[] = [
					"title" => $args["404_label"],
					"link" => false,
				];
			} elseif (is_search()) {
				$breadcrumb_arr[] = [
					"title" => sprintf(
						$args["search_label"],
						get_search_query()
					),
					"link" => false,
				];
			} elseif (is_tax()) {
				$taxonomy = get_query_var("taxonomy");
				$term = get_term_by("slug", get_query_var("term"), $taxonomy);
				if (is_taxonomy_hierarchical($taxonomy) && $term->parent != 0) {
					$ancestors = array_reverse(
						get_ancestors($term->term_id, $taxonomy)
					);
					foreach ($ancestors as $ancestor_id) {
						$ancestor = get_term($ancestor_id, $taxonomy);
						$breadcrumb_arr[] = [
							"title" => $ancestor->name,
							"link" => get_term_link($ancestor, $term->slug),
						];
					}
				}
				$breadcrumb_arr[] = [
					"title" => sprintf($args["taxonomy_label"], $term->name),
					"link" => get_term_link($term->term_id, $term->slug),
				];
			} elseif (is_attachment()) {
				if ($post->post_parent) {
					if ($parent_post = get_post($post->post_parent)) {
						$singular_breadcrumb_arr = self::get_singular_breadcrumb_array(
							$parent_post,
							$args
						);
						$breadcrumb_arr = array_merge(
							$breadcrumb_arr,
							$singular_breadcrumb_arr
						);
					}
				}
				$breadcrumb_arr[] = [
					"title" => $parent_post->post_title,
					"link" => get_permalink($parent_post->ID),
				];
				$breadcrumb_arr[] = [
					"title" => sprintf(
						$args["attachment_label"],
						$post->post_title
					),
					"link" => get_permalink($post->ID),
				];
			} elseif (is_singular() && !is_front_page()) {
				$singular_breadcrumb_arr = self::get_singular_breadcrumb_array(
					$post,
					$args
				);
				$breadcrumb_arr = array_merge(
					$breadcrumb_arr,
					$singular_breadcrumb_arr
				);
				$breadcrumb_arr[] = [
					"title" => $post->post_title,
					"link" => get_permalink($post->ID),
				];
			} elseif (is_category()) {
				global $cat;

				$category = get_category($cat);
				if ($category->parent != 0) {
					$ancestors = array_reverse(
						get_ancestors($category->term_id, "category")
					);
					foreach ($ancestors as $ancestor_id) {
						$ancestor = get_category($ancestor_id);
						$breadcrumb_arr[] = [
							"title" => $ancestor->name,
							"link" => get_category_link($ancestor->term_id),
						];
					}
				}
				$breadcrumb_arr[] = [
					"title" => sprintf(
						$args["category_label"],
						$category->name
					),
					"link" => get_category_link($cat),
				];
			} elseif (is_tag()) {
				global $tag_id;
				$tag = get_tag($tag_id);
				$breadcrumb_arr[] = [
					"title" => sprintf($args["tag_label"], $tag->name),
					"link" => get_tag_link($tag_id),
				];
			} elseif (is_author()) {
				$author = get_query_var("author");
				$breadcrumb_arr[] = [
					"title" => sprintf(
						$args["author_label"],
						get_the_author_meta(
							"display_name",
							get_query_var("author")
						)
					),
					"link" => get_author_posts_url($author),
				];
			} elseif (is_day()) {
				if ($m = get_query_var("m")) {
					$year = substr($m, 0, 4);
					$month = substr($m, 4, 2);
					$day = substr($m, 6, 2);
				} else {
					$year = get_query_var("year");
					$month = get_query_var("monthnum");
					$day = get_query_var("day");
				}
				$month_title = self::get_month_title($month);
				$breadcrumb_arr[] = [
					"title" => sprintf($args["year_label"], $year),
					"link" => get_year_link($year),
				];
				$breadcrumb_arr[] = [
					"title" => sprintf($args["month_label"], $month_title),
					"link" => get_month_link($year, $month),
				];
				$breadcrumb_arr[] = [
					"title" => sprintf($args["day_label"], $day),
					"link" => get_day_link($year, $month, $day),
				];
			} elseif (is_month()) {
				if ($m = get_query_var("m")) {
					$year = substr($m, 0, 4);
					$month = substr($m, 4, 2);
				} else {
					$year = get_query_var("year");
					$month = get_query_var("monthnum");
				}
				$month_title = self::get_month_title($month);
				$breadcrumb_arr[] = [
					"title" => sprintf($args["year_label"], $year),
					"link" => get_year_link($year),
				];
				$breadcrumb_arr[] = [
					"title" => sprintf($args["month_label"], $month_title),
					"link" => get_month_link($year, $month),
				];
			} elseif (is_year()) {
				if ($m = get_query_var("m")) {
					$year = substr($m, 0, 4);
				} else {
					$year = get_query_var("year");
				}
				$breadcrumb_arr[] = [
					"title" => sprintf($args["year_label"], $year),
					"link" => get_year_link($year),
				];
			} elseif (is_post_type_archive()) {
				$post_type = get_post_type_object(get_query_var("post_type"));
				$breadcrumb_arr[] = [
					"title" => sprintf(
						$args["post_type_label"],
						$post_type->label
					),
					"link" => get_post_type_archive_link($post_type->name),
				];
			}

			return apply_filters("civi_breadcrumb_arr", $breadcrumb_arr, $args);
		}

		public static function get_singular_breadcrumb_array($post, $args)
		{
			$breadcrumb_arr = [];
			$post_type = get_post_type_object($post->post_type);

			if ($post_type && $post_type->has_archive) {
				$breadcrumb_arr[] = [
					"title" => sprintf(
						$args["post_type_label"],
						$post_type->label
					),
					"link" => get_post_type_archive_link($post_type->name),
				];
			}

			if (is_post_type_hierarchical($post_type->name)) {
				$ancestors = array_reverse(get_post_ancestors($post));
				if (count($ancestors)) {
					$ancestor_posts = get_posts(
						"post_type=" .
							$post_type->name .
							"&include=" .
							implode(",", $ancestors)
					);
					foreach ($ancestors as $ancestor) {
						foreach ($ancestor_posts as $ancestor_post) {
							if ($ancestor == $ancestor_post->ID) {
								$breadcrumb_arr[] = [
									"title" => apply_filters(
										"the_title",
										$ancestor_post->post_title
									),
									"link" => get_permalink($ancestor_post->ID),
								];
							}
						}
					}
				}
			} else {
				$post_type_taxonomies = get_object_taxonomies(
					$post_type->name,
					false
				);
				if (
					is_array($post_type_taxonomies) &&
					count($post_type_taxonomies)
				) {
					foreach ($post_type_taxonomies as $tax_slug => $taxonomy) {
						if ($taxonomy->hierarchical) {
							if (!$taxonomy->show_in_nav_menus) {
								continue;
							}
							$terms = get_the_terms($post->ID, $tax_slug);
							if ($terms) {
								$term = array_shift($terms);
								if ($term->parent != 0) {
									$ancestors = array_reverse(
										get_ancestors($term->term_id, $tax_slug)
									);
									foreach ($ancestors as $ancestor_id) {
										$ancestor = get_term(
											$ancestor_id,
											$tax_slug
										);
										$breadcrumb_arr[] = [
											"title" => $ancestor->name,
											"link" => get_term_link(
												$ancestor,
												$tax_slug
											),
										];
									}
								}
								$breadcrumb_arr[] = [
									"title" => $term->name,
									"link" => get_term_link($term, $tax_slug),
								];
								break;
							}
						}
					}
				}
			}

			return $breadcrumb_arr;
		}

		public static function add_posts_page_array($breadcrumb_arr)
		{
			if (is_page() || is_front_page()) {
				return $breadcrumb_arr;
			} elseif (is_category()) {
				$tax = get_taxonomy("category");
				if (
					count($tax->object_type) != 1 ||
					$tax->object_type[0] != "post"
				) {
					return $breadcrumb_arr;
				}
			} elseif (is_tag()) {
				$tax = get_taxonomy("post_tag");
				if (
					count($tax->object_type) != 1 ||
					$tax->object_type[0] != "post"
				) {
					return $breadcrumb_arr;
				}
			} elseif (is_tax()) {
				$tax = get_taxonomy(get_query_var("taxonomy"));
				if (
					count($tax->object_type) != 1 ||
					$tax->object_type[0] != "post"
				) {
					return $breadcrumb_arr;
				}
			} elseif (is_home() && !get_query_var("pagename")) {
				return $breadcrumb_arr;
			} else {
				$post_type = get_query_var("post_type")
					? get_query_var("post_type")
					: "post";
				if ($post_type != "post") {
					return $breadcrumb_arr;
				}
			}
			if (
				get_option("show_on_front") == "page" &&
				($posts_page_id = get_option("page_for_posts"))
			) {
				$posts_page = get_post($posts_page_id);
				$breadcrumb_arr[] = [
					"title" => $posts_page->post_title,
					"link" => get_permalink($posts_page->ID),
				];
			}

			return $breadcrumb_arr;
		}

		public static function sanitize_attr_classes($classes, $prefix = "")
		{
			if (!is_array($classes)) {
				$classes = preg_replace("/[^\s\w_-]+/", "", $classes);
				$classes = preg_split("/[\s]+/", $classes);
			}

			foreach ($classes as $key => $class) {
				if (is_array($class)) {
					unset($classes[$key]);
				} else {
					$class = preg_replace("/[^\w_-]+/", "", $class);
					$class = preg_replace("/^[\d_-]+/", "", $class);
					if ($class) {
						$classes[$key] = $prefix . $class;
					}
				}
			}
			$classes = implode(" ", $classes);

			return $classes;
		}

		public static function uniform_boolean($arg, $default = true)
		{
			if (is_numeric($arg)) {
				$arg = (int) $arg;
			}
			if (is_string($arg)) {
				$arg = strtolower($arg);
				if ($arg == "false") {
					$arg = false;
				} elseif ($arg == "true") {
					$arg = true;
				} else {
					$arg = $default;
				}
			}

			return $arg;
		}

		public static function get_month_title($monthnum = 0)
		{
			global $wp_locale;
			$monthnum = (int) $monthnum;
			$date_format = get_option("date_format");
			if (
				in_array($date_format, [
					"DATE_COOKIE",
					"DATE_RFC822",
					"DATE_RFC850",
					"DATE_RFC1036",
					"DATE_RFC1123",
					"DATE_RFC2822",
					"DATE_RSS",
				])
			) {
				$month_format = "M";
			} elseif (
				in_array($date_format, [
					"DATE_ATOM",
					"DATE_ISO8601",
					"DATE_RFC3339",
					"DATE_W3C",
				])
			) {
				$month_format = "m";
			} else {
				preg_match(
					"/(^|[^\\\\]+)(F|m|M|n)/",
					str_replace("\\\\", "", get_option("date_format")),
					$m
				);
				if (isset($m[2])) {
					$month_format = $m[2];
				} else {
					$month_format = "F";
				}
			}

			switch ($month_format) {
				case "F":
					$month = $wp_locale->get_month($monthnum);
					break;
				case "M":
					$month = $wp_locale->get_month_abbrev(
						$wp_locale->get_month($monthnum)
					);
					break;
				default:
					$month = $monthnum;
			}

			return $month;
		}
	}

	new Civi_Breadcrumb();
}
