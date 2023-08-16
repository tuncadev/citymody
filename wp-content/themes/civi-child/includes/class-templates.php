<?php

if (!defined("ABSPATH")) {
	exit();
}

if (!class_exists("Civi_Templates")) {
	/**
	 *  Class Civi_Templates
	 */
	class Civi_Templates
	{
		public static function site_logo($type = "")
		{
			$logo = "";
			$logo_retina = "";

			if ($type == "dark") {
				$logo_dark = Civi_Helper::get_setting("logo_dark");
				$logo_dark_retina = Civi_Helper::get_setting(
					"logo_dark_retina"
				);

				if ($logo_dark) {
					$logo = $logo_dark;
				}

				if ($logo_dark_retina) {
					$logo_retina = $logo_dark_retina;
				}
			}

			if ($type == "light") {
				$logo_light = Civi_Helper::get_setting("logo_light");
				$logo_light_retina = Civi_Helper::get_setting(
					"logo_light_retina"
				);

				if ($logo_light) {
					$logo = $logo_light;
				}

				if ($logo_light_retina) {
					$logo_retina = $logo_light_retina;
				}
			}

			$site_name = get_bloginfo("name", "display");

			ob_start();
?>
			<?php if (!empty($logo)) : ?>
				<div class="site-logo">
					<a href="<?php echo esc_url(home_url("/")); ?>" title="<?php echo esc_attr(
																				$site_name
																			); ?>"><img src="<?php echo esc_url(
																									$logo
																								); ?>" data-retina="<?php echo esc_attr(
																														$logo_retina
																													); ?>" alt="<?php echo esc_attr($site_name); ?>"></a>
				</div>
			<?php else : ?>
				<div class="site-logo">
					<?php $blog_info = get_bloginfo("name"); ?>
					<?php if (!empty($blog_info)) : ?>
						<h1 class="site-title"><a href="<?php echo esc_url(
															home_url("/")
														); ?>" title="<?php echo esc_attr(
																			$site_name
																		); ?>"><?php bloginfo("name"); ?></a></h1>
						<p><?php bloginfo("description"); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		<?php return ob_get_clean();
		}


		public static function style_logo()
		{
			$id = get_the_ID();
			$header_style = '';
			if (!empty($id)) {
				$header_style = get_post_meta($id, 'civi-header_style', true);
			}
			if ($header_style == 'light') {
				$header_logo = Civi_Templates::site_logo('light');
			} else {
				$header_logo = Civi_Templates::site_logo('dark');
			}
			return $header_logo;
		}

		public static function main_menu()
		{
			$show_main_menu = Civi_Helper::get_setting("show_main_menu");

			if (!$show_main_menu) {
				return;
			}

			ob_start();
		?>
			<div class="site-menu main-menu desktop-menu default-menu">
				<?php
				$args = array();

				$defaults = array(
					'menu_class' => 'menu',
					'container' => '',
					'theme_location' => 'main_menu',
				);

				$args = wp_parse_args($args, $defaults);

				if (has_nav_menu('main_menu') && class_exists('Civi_Walker_Nav_Menu')) {
					$args['walker'] = new Civi_Walker_Nav_Menu;
				}

				if (has_nav_menu('main_menu')) {
					wp_nav_menu($args);
				}
				?>
			</div>
		<?php return ob_get_clean();
		}

		public static function site_menu()
		{
			if (!class_exists("Civi_Framework")) {
				return;
			}

			ob_start();
		?>
			<div class="site-menu desktop-menu default-menu">
				<?php
				$args = array();

				$defaults = array(
					'menu_class' => 'menu',
					'container' => '',
					'theme_location' => 'primary',
				);

				$args = wp_parse_args($args, $defaults);

				if (has_nav_menu('primary') && class_exists('Civi_Walker_Nav_Menu')) {
					$args['walker'] = new Civi_Walker_Nav_Menu;
				}

				wp_nav_menu($args);
				?>
			</div>
		<?php return ob_get_clean();
		}

		public static function mobile_menu()
		{

			ob_start();
		?>
			<div class="bg-overlay"></div>

			<div class="site-menu area-menu mobile-menu default-menu">

				<div class="inner-menu custom-scrollbar">

					<a href="#" class="btn-close">
						<i class="far fa-times"></i>
					</a>

					<?php if (!class_exists("Civi_Framework")) : ?>
						<?php echo self::site_logo("dark"); ?>
					<?php endif; ?>

					<?php if (class_exists("Civi_Framework")) : ?>
						<div class="top-mb-menu">
							<?php echo self::account(); ?>
						</div>
					<?php if (!is_user_logged_in()) { ?>
					<div class="account logged-out" id="reg-mb-btn"><a href="#popup-form" class="btn-login-register"><?php echo __("Register", "civichild"); ?></a></div>
					<?php } ?>
					<?php endif; ?>

					<?php
					$args = [
						"menu_class" => "menu",
						"container" => "",
						"theme_location" => "mobile_menu",
					];
					wp_nav_menu($args);
					?>

					<?php echo self::add_jobs(); ?>
				</div>
			</div>
		<?php return ob_get_clean();
		}

		public static function canvas_menu()
		{
			$show_canvas_menu = Civi_Helper::get_setting("show_canvas_menu");

			ob_start();
		?>
			<div class="mb-menu canvas-menu canvas-left <?php if (!$show_canvas_menu) {
															echo "d-hidden";
														} ?>">
				<a href="#" class="icon-menu">
					<i class="far fa-bars"></i>
				</a>

				<?php echo self::mobile_menu(); ?>
			</div>
		<?php return ob_get_clean();
		}

		public static function search_icon($search_type = "icon", $ajax = false)
		{
			$ajax_class = "";
			if ($ajax) {
				$ajax_class = "civi-ajax-search";
			}

			$show_search_icon = Civi_Helper::get_setting("show_search_icon");
			if (!$show_search_icon) {
				return;
			}

			ob_start();
		?>
			<div class="block-search search-<?php echo esc_attr($search_type); ?>
			<?php echo esc_attr($ajax_class); ?>">
				<div class="icon-search">
					<i class="far fa-search"></i>
				</div>
			</div>
			<?php return ob_get_clean();
		}

		public static function post_categories()
		{
			ob_start();

			$count_posts = wp_count_posts();
			$category_id = "";
			$blog_sidebar = Civi_Helper::get_setting("blog_sidebar");
			$sidebar = !empty($_GET["sidebar"])
				? Civi_Helper::civi_clean(wp_unslash($_GET["sidebar"]))
				: $blog_sidebar;

			if (is_category()) {
				$cate = get_category(get_query_var("cat"));
				$category_id = $cate->cat_ID;
			}
			$categories = get_categories([
				"orderby" => "count",
				"order" => "DESC",
				"number" => 5,
				"parent" => 0,
				"hide_empty" => true,
				"hierarchical" => true,
			]);

			if ($categories) : ?>
				<div class="civi-categories">
					<ul class="list-categories">
						<li class="<?php if (!is_front_page() && is_home()) :
										echo esc_attr("active");
									endif; ?>">
							<a href="<?php echo get_post_type_archive_link("post"); ?>">
								<span class="entry-name"><?php esc_html_e("All", "civichild"); ?></span>
							</a>
						</li>
						<?php foreach ($categories as $category) {
							$category_link = get_category_link($category->term_id); ?>
							<li class="<?php if ($category_id == $category->term_id) :
											echo esc_attr("active");
										endif; ?>">
								<a href="<?php echo esc_url($category_link); ?>">
									<span class="entry-name"><?php esc_html_e($category->name); ?></span>
								</a>
							</li>
						<?php
						} ?>
					</ul>
				</div>
			<?php endif;

			return ob_get_clean();
		}

		public static function account()
		{
			$getset = isset($_GET["wishlist"]) ? "wishlist" : "";
			$favActive = isset($_GET["wishlist"]) ? "active" : "";
			$en_IDS = [
				"candidate_dashboard" => 15370,
				"candidate_profile" => 15375,
				"my_jobs" => 15381,
				"candidate_reviews" => '',
				"candidate_company" => '',
				"candidate_messages" => '',
				"candidate_meetings" => '',
				"candidate_settings" => 15379,
				"candidate_logout" => '',
				];
		
			$language = pll_current_language( 'slug' );
			$show_login = Civi_Helper::get_setting("show_login");

			if (
				!class_exists("Civi_Framework") ||
				(!$show_login)
			) {
				return;
			}

			ob_start();
			?>
			<?php if (is_user_logged_in()) {

				
				$accent_color 	   	   = Civi_Helper::get_setting('accent_color');
				$secondary_color 	   = Civi_Helper::get_setting('secondary_color');
				$current_user = wp_get_current_user();
				$candidate_fn = __('Welcome', 'civi-framework');
				$candidate_first_name = $candidate_fn . ", " . $current_user->first_name;
				$user_name = $current_user->display_name;
				$user_link = get_edit_user_link($current_user->ID);
				$avatar_url = get_avatar_url($current_user->ID);
				$author_avatar_image_url = get_the_author_meta(
					"author_avatar_image_url",
					$current_user->ID
				);
				$author_avatar_image_id = get_the_author_meta(
					"author_avatar_image_id",
					$current_user->ID
				);
				if (!empty($author_avatar_image_url)) {
					$avatar_url = $author_avatar_image_url;
				}
				$current_user = wp_get_current_user();
				$key_employer = [
					"dashboard" => esc_html__('Dashboard', 'civi-framework'),
					"jobs_dashboard" => esc_html__('Jobs', 'civi-framework'),
					"applicants" => esc_html__('Applicants', 'civi-framework'),
					"candidates" => esc_html__('Candidates', 'civi-framework'),
					"user_package" => esc_html__('Package', 'civi-framework'),
					"messages" => esc_html__('Messages', 'civi-framework'),
					"meetings" => esc_html__('Meetings', 'civi-framework'),
					"company" => esc_html__('Company', 'civi-framework'),
					"settings" => esc_html__('Settings', 'civi-framework'),
					"logout" => esc_html__('Logout', 'civi-framework'),
				];

				$key_candidate = [
					/*"candidate_membership" => esc_html__('Premium', 'civi-framework'),*/
					"candidate_dashboard" => esc_html__('Dashboard', 'civi-framework'),
					"candidate_profile" => esc_html__('Profile', 'civi-framework'),
					"my_jobs" => esc_html__('My jobs', 'civi-framework'),
					"my_favorites" => esc_html__('My Favorites', 'civi-framework'),
					"candidate_reviews" => esc_html__('My Reviews', 'civi-framework'),
					"candidate_company" => esc_html__('My Following', 'civi-framework'),
					"candidate_messages" => esc_html__('Messages', 'civi-framework'),
					"candidate_meetings" => esc_html__('Meetings', 'civi-framework'),
					"candidate_settings" => esc_html__('Settings', 'civi-framework'),
					"candidate_logout" => esc_html__('Logout', 'civi-framework'),
				];
			?>
				<div class="account logged-in">
					<?php if ($avatar_url) : ?>
						<div class="user-show">
							<a class="avatar" href="#">
								<img src="<?php echo esc_url(
												$avatar_url
											); ?>" title="<?php echo esc_attr(
																$candidate_first_name . "-".  $current_user->ID
															); ?>" alt="<?php echo esc_attr($candidate_first_name); ?>">
								<span><?php	 esc_html_e($candidate_first_name); ?></span>
								<i class="far fa-chevron-down"></i>
							</a>
						</div>
					<?php endif; ?>
					<?php if (
						in_array("civi_user_candidate", (array)$current_user->roles) ||
						in_array("civi_user_employer", (array)$current_user->roles)
					) : ?>
						<div class="user-control civi-nav-dashboard" data-secondary="<?php echo $secondary_color; ?>" data-accent="<?php echo $accent_color; ?>">
							<div class="inner-control nav-dashboard">
								<ul class="list-nav-dashboard">

									<?php if (in_array("civi_user_employer", (array)$current_user->roles)) :
										foreach ($key_employer as $key => $value) {
											$show_employer = civi_get_option("show_employer_" . $key, "1");
											$image_employer = civi_get_option("image_employer_" . $key);
											$id = civi_get_option("civi_" . $key . "_page_id");
											
									?>
											<?php if ($show_employer) : ?>
												<li class="nav-item <?php if (is_page($id) && $key !== "logout") :
																		echo esc_attr("active");
																	endif; ?>">
													<?php if ($key === "logout") { ?>
														<a href="<?php echo wp_logout_url(home_url()); ?>">
														<?php } else { ?>
															<a href="<?php echo get_permalink($id); ?>" class="civi-icon-items">
															<?php } ?>
															<?php if (!empty($image_employer["url"])) : ?>
																<span class="image">
																	<?php if (civi_get_option('type_icon_employer') === 'svg') { ?>
																		<object class="civi-svg" type="image/svg+xml" data="<?php echo esc_url($image_employer['url']) ?>"></object>
																	<?php } else { ?>
																		<img src="<?php echo esc_url($image_employer['url']) ?>" alt="<?php echo $value; ?>" />
																	<?php } ?>
																</span>
															<?php endif; ?>
															<span><?php esc_html_e($value) ?></span>
															<?php if ($key === "messages") { ?>
																<?php civi_get_total_unread_message(); ?>
															<?php } ?>
															</a>
												</li>
											<?php endif; ?>
										<?php
										} ?>
										<?php
									elseif (in_array("civi_user_candidate", (array)$current_user->roles)) : ?>
									<?php	foreach ($key_candidate as $key => $value) :

											$show_candidate = civi_get_option('show_' . $key, '1');
											$span_premium = $key === "candidate_membership" ? "premium_menu" : "";
	
				
											if (!$show_candidate) {
												continue;
											}
											$nID = '';
											$id = civi_get_option("civi_" . $key . "_page_id");
											$image_candidate = civi_get_option("image_" . $key, "");

											$class_active =
												is_page($id) && $key !== "candidate_logout" && !isset($_GET["wishlist"]) ? "active" : "";
											if($language != "tr") { 
												$nID = $en_IDS[$key];
												if($nID === '') { $id = $id; } else { $id = $nID ; }
											} 
											$link_url = '';
											$link_url = $key === "candidate_logout" ? wp_logout_url(home_url()) : get_permalink($id);

											$html_icon = '';
											if (!empty($image_candidate['url'])) {
												if (civi_get_option("type_icon_candidate") === "svg") {
													$html_icon =
														'<object class="civi-svg" type="image/svg+xml" data="' .
														esc_url($image_candidate["url"]) .
														'"></object>';
												} else {
													$html_icon =
														'<img src="' .
														esc_url($image_candidate["url"]) .
														'" alt="' .
														$value .
														'"/>';
												}
											}
											$language = pll_current_language( 'slug' );
										
										?>
											<?php if($key != "my_favorites") { ?>
											<li class="nav-item <?php esc_html_e($class_active); ?> <?php echo $span_premium; ?>">
													<a href="<?php echo esc_url($link_url); ?>" data-title="<?php echo $value; ?>">
															<?php if (!empty($image_candidate["url"])) { ?>
																	<span class="image">
																		<?php echo $html_icon; ?>
															</span>
															<?php } ?>
															<span><?php echo $value; ?></span>
															<?php if ($key === "candidate_messages") { ?>
																	<?php civi_get_total_unread_message();?>
															<?php } ?>
													</a>
											</li>
										<?php } else { ?>
											<li class="nav-item <?php esc_html_e($favActive); ?>">
												<a href="https://www.citymody.com/dashboard/candidates/my-jobs/?wishlist" data-title="<?php echo $value; ?>" style="padding:5px 14px;">
													<span class="image" style="margin-right: 16px;">
														<img src="https://www.citymody.com/wp-content/uploads/2023/08/favorites.svg" width="20" style="opacity: 0.95" />
													</span>
													<span><?php echo $value; ?></span>
												</a>
											</li>
										<?php } ?>
										<?php endforeach; ?>
									<?php endif; ?>
								</ul>
							</div>
						</div>
					<?php endif; ?>
				</div>
			<?php
			} else {
			?>
				<div class="account logged-out">
					<?php if ($show_login) : ?>
						<a href="#popup-form" class="btn-login">
							<?php esc_html_e("Login", "civichild"); ?></a>
					<?php endif; ?>
				</div>
			<?php
			} ?>
		<?php return ob_get_clean();
		}

		public static function notification()
		{
			$show_icon_noti = Civi_Helper::get_setting("show_icon_noti");
			if (!class_exists("Civi_Framework") || !$show_icon_noti || !is_user_logged_in()) {
				return;
			}

			ob_start();

			civi_get_template('dashboard/notification/notification.php');

			return ob_get_clean();
		}


		public static function add_jobs()
		{
			global $current_user;
			$show_add_jobs_button = Civi_Helper::get_setting(
				"show_add_jobs_button"
			);

			if (!class_exists("Civi_Framework") || !$show_add_jobs_button) {
				return;
			}
			$add_jobs = $add_jobs_not = $update_profile = '#';
			if (Civi_Helper::civi_get_option("civi_add_jobs_page_id")) {
				$add_jobs = get_page_link(Civi_Helper::civi_get_option("civi_add_jobs_page_id"));
			}

			if (Civi_Helper::civi_get_option("civi_add_jobs_not_page_id")) {
				$add_jobs_not = get_page_link(Civi_Helper::civi_get_option("civi_add_jobs_not_page_id"));
			}

			if (Civi_Helper::civi_get_option("civi_update_profile_page_id")) {
				$update_profile = get_page_link(Civi_Helper::civi_get_option("civi_update_profile_page_id"));
			}

			$enable_login_to_submit = Civi_Helper::civi_get_option(
				"enable_login_to_submit",
				"1"
			);

			ob_start();
		?>
			<?php if ($enable_login_to_submit == "1" && !is_user_logged_in()) { ?>
				<a href="<?php echo esc_url($add_jobs_not); ?>" class="civi-button add-job">
					<?php esc_html_e("Post a job", "civichild"); ?>
				</a>
			<?php } else { ?>
				<?php if (in_array('civi_user_candidate', (array)$current_user->roles)) { ?>
					<a href="<?php echo esc_url($update_profile); ?>" class="add-job civi-button">
						<?php esc_html_e("Update Profile", "civichild"); ?>
					</a>
				<?php } else { ?>
					<a href="<?php echo esc_url($add_jobs); ?>" class="add-job civi-button">
						<?php esc_html_e("Post a job", "civichild"); ?>
					</a>
				<?php } ?>
			<?php } ?>
		<?php return ob_get_clean();
		}

		//Top Bar
		public static function top_bar()
		{
			$top_bar_text = Civi_Helper::get_setting("top_bar_text");
			$top_bar_phone = Civi_Helper::get_setting("top_bar_phone");
			$top_bar_email = Civi_Helper::get_setting("top_bar_email");
			ob_start(); ?>
			<div class="col-lg-7 left-top-bar">
				<div class="top-bar-text"><i class="fal fa-exclamation-circle"></i><?php echo wp_kses_post($top_bar_text) ?></div>
			</div>
			<div class="col-lg-5 right-top-bar">
				<span class="top-bar-phone"><i class="fal fa-phone-alt"></i><?php esc_html_e($top_bar_phone); ?></span>
				<span class="top-bar-email"><i class="fal fa-envelope"></i><?php esc_html_e($top_bar_email); ?></span>
			</div>
			<?php return ob_get_clean();
		}


		public static function page_title()
		{
			ob_start();

			get_template_part("templates/page/page-title");

			return ob_get_clean();
		}

		public static function post_thumbnail()
		{
			ob_start();

			get_template_part("templates/post/post-thumbnail");

			return ob_get_clean();
		}

		/**
		 * Render comments
		 * *******************************************************
		 */
		public static function render_comments($comment, $args, $depth)
		{
			self::civi_get_template("post/comment", [
				"comment" => $comment,
				"args" => $args,
				"depth" => $depth,
			]);
		}

		/**
		 * Get template
		 * *******************************************************
		 */
		public static function civi_get_template($slug, $args = [])
		{
			if ($args && is_array($args)) {
				extract($args);
			}
			$located = locate_template(["templates/{$slug}.php"]);

			if (!file_exists($located)) {
				_doing_it_wrong(
					__FUNCTION__,
					sprintf("<code>%s</code> does not exist.", $slug),
					"1.0"
				);
				return;
			}
			include $located;
		}

		/**
		 * Display navigation to next/previous set of posts when applicable.
		 */
		public static function pagination()
		{
			global $wp_query, $wp_rewrite;

			// Don't print empty markup if there's only one page.
			if ($wp_query->max_num_pages < 2) {
				return;
			}

			$paged = get_query_var("paged")
				? intval(get_query_var("paged"))
				: 1;
			$pagenum_link = wp_kses(
				get_pagenum_link(),
				Civi_Helper::civi_kses_allowed_html()
			);
			$query_args = [];
			$url_parts = explode("?", $pagenum_link);

			if (isset($url_parts[1])) {
				wp_parse_str($url_parts[1], $query_args);
			}

			$pagenum_link = esc_url(
				remove_query_arg(array_keys($query_args), $pagenum_link)
			);
			$pagenum_link = trailingslashit($pagenum_link) . "%_%";

			$format =
				$wp_rewrite->using_index_permalinks() &&
				!strpos($pagenum_link, "index.php")
				? "index.php/"
				: "";
			$format .= $wp_rewrite->using_permalinks()
				? user_trailingslashit(
					$wp_rewrite->pagination_base . "/%#%",
					"paged"
				)
				: "?paged=%#%";

			// Set up paginated links.
			$links = paginate_links([
				"format" => $format,
				"total" => $wp_query->max_num_pages,
				"current" => $paged,
				"add_args" => array_map("urlencode", $query_args),
				"prev_text" => '<i class="far fa-angle-left"></i>',
				"next_text" => '<i class="far fa-angle-right"></i>',
				"type" => "list",
				"end_size" => 3,
				"mid_size" => 3,
			]);

			if ($links) { ?>

				<div class="posts-pagination">
					<?php echo wp_kses($links, Civi_Helper::civi_kses_allowed_html()); ?>
				</div><!-- .pagination -->

<?php }
		}
	}
}
