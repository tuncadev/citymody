<?php

namespace Civi_Elementor;

use Elementor\Plugin;

defined('ABSPATH') || exit;

class Widget_Init
{

	private static $_instance = null;

	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function initialize()
	{
		add_action('elementor/elements/categories_registered', [$this, 'add_elementor_widget_categories']);
		add_action('elementor/element/after_add_attributes', [$this, 'add_elementor_attribute']);

		// Registered Widgets.
		add_action('elementor/widgets/register', [$this, 'init_widgets']);
		//add_action( 'elementor/widgets/register', [ $this, 'remove_unwanted_widgets' ], 15 );

		add_action('elementor/frontend/after_register_scripts', [$this, 'after_register_scripts']);
		add_action('elementor/frontend/after_register_styles', [$this, 'after_register_styles']);

		add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_editor_scripts']);

		// Modify original widgets settings.
		require_once CIVI_ELEMENTOR_DIR . '/widgets/original/modify-base.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/original/section.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/original/column.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/original/accordion.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/original/animated-headline.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/original/counter.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/original/form.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/original/heading.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/original/icon-box.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/original/progress.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/original/countdown.php';
	}

	/**
	 * Register scripts for widgets.
	 */
	public function after_register_scripts()
	{
		// Fix Wordpress old version not registered this script.
		if (!wp_script_is('imagesloaded', 'registered')) {
			wp_register_script('imagesloaded', CIVI_THEME_URI . '/assets/libs/imagesloaded/imagesloaded.min.js', array('jquery'), null, true);
		}

		wp_register_script('circle-progress', CIVI_THEME_URI . '/assets/libs/circle-progress/circle-progress.min.js', array('jquery'), null, true);
		wp_register_script('civi-widget-circle-progress', CIVI_ELEMENTOR_URI . '/assets/js/widgets/widget-circle-progress.js', array(
			'jquery',
			'circle-progress',
		), null, true);

		wp_register_script('civi-swiper-wrapper', CIVI_THEME_URI . '/assets/js/swiper-wrapper.js', array('jquery'), CIVI_THEME_VER, true);
		wp_register_script('civi-group-widget-carousel', CIVI_ELEMENTOR_URI . '/assets/js/widgets/group-widget-carousel.js', array(
			'jquery',
			'civi-swiper',
			'civi-swiper-wrapper',
		), null, true);
		$civi_swiper_js = array(
			'prevText' => esc_html__('Prev', 'civi'),
			'nextText' => esc_html__('Next', 'civi'),
		);
		wp_localize_script('civi-swiper-wrapper', '$civiSwiper', $civi_swiper_js);

		wp_register_script('civi-grid-query', CIVI_ELEMENTOR_URI . '/assets/js/widgets/grid-query.min.js', array('jquery'), null, true);

		wp_register_script('civi-widget-modern-menu', CIVI_ELEMENTOR_URI . '/assets/js/widgets/widget-modern-menu.js', array('jquery'), null, true);
		wp_register_script('civi-widget-modern-tabs', CIVI_ELEMENTOR_URI . '/assets/js/widgets/widget-modern-tabs.js', array('jquery'), null, true);

		wp_register_script('civi-widget-grid-post', CIVI_ELEMENTOR_URI . '/assets/js/widgets/widget-grid-post.js', array('civi-grid-layout'), null, true);
		wp_register_script('civi-group-widget-grid', CIVI_ELEMENTOR_URI . '/assets/js/widgets/group-widget-grid.js', array('civi-grid-layout'), null, true);

		wp_register_script('civi-widget-google-map', CIVI_ELEMENTOR_URI . '/assets/js/widgets/widget-google-map.js', array('jquery'), null, true);

		wp_register_script('civi-widget-testimonial-carousel', CIVI_ELEMENTOR_URI . '/assets/js/widgets/widget-testimonial.js', array(
			'jquery',
		), null, true);

		wp_register_script('civi-widget-list', CIVI_ELEMENTOR_URI . '/assets/js/widgets/widget-list.js', array(
			'jquery',
		), null, true);

		wp_register_script('civi-social-networks', CIVI_ELEMENTOR_URI . '/assets/js/widgets/widget-social-networks.js', array(
			'jquery',
		), null, true);

		wp_register_script('civi-widget-flip-box', CIVI_ELEMENTOR_URI . '/assets/js/widgets/widget-flip-box.js', array(
			'jquery',
			'imagesloaded',
		), null, true);

		wp_register_script('typed', CIVI_ELEMENTOR_URI . '/assets/libs/typed/typed.min.js', array('jquery'), null, true);
		wp_register_script('civi-widget-fancy-heading', CIVI_ELEMENTOR_URI . '/assets/js/widgets/widget-fancy-heading.js', array(
			'jquery',
			'typed',
		), null, true);

		wp_register_script('civi-widget-accordion', CIVI_ELEMENTOR_URI . '/assets/js/widgets/widget-accordion.js', array(
			'jquery',
		), null, true);

		wp_register_script('civi-widget-gallery-justified-content', CIVI_ELEMENTOR_URI . '/assets/js/widgets/widget-gallery-justified-content.js', array(
			'justifiedGallery',
		), null, true);

		wp_register_script('countdown', CIVI_ELEMENTOR_URI . '/assets/libs/jquery.countdown/js/jquery.countdown.min.js', array('jquery'), CIVI_THEME_VER, true);
	}

	/**
	 * enqueue scripts in editor mode.
	 */
	public function enqueue_editor_scripts()
	{
		wp_enqueue_script('civi-widget-accordion', CIVI_ELEMENTOR_URI . '/assets/js/editor.js', array('jquery'), null, true);
	}

	/**
	 * Register styles for widgets.
	 */
	public function after_register_styles()
	{
		$style = [
			'accordion',
			'attribute-list',
			'banner',
			'blog',
			'circle-progress-chart',
			'client-logo',
			'contact-form-7',
			'fancy-heading',
			'flip-box',
			'google-map',
			'gradation',
			'heading',
			'icon',
			'icon-box',
			'number-box',
			'user-form',
			'job-search',
			'image-animation',
			'image-box',
			'image-carousel',
			'image-gallery',
			'image-layers',
			'image-rotate',
			'instagram',
			'list',
			'mailchimp-form',
			'modern-carousel',
			'modern-menu',
			'modern-slider',
			'modern-tabs',
			'popup-video',
			'pricing',
			'separator',
			'shapes',
			'social-networks',
			'table',
			'team-member',
			'team-member-carousel',
			'testimonial-carousel',
			'testimonial-grid',
			'timeline',
			'twitter',
			'view-demo'
		];

		foreach ($style as $key => $value) {
			wp_register_style('civi-el-widget-' . $value, CIVI_ELEMENTOR_URI  . '/assets/scss/' . $value . '.min.css');
		}
	}

	/**
	 * @param \Elementor\Elements_Manager $elements_manager
	 *
	 * Add category.
	 */
	function add_elementor_widget_categories($elements_manager)
	{
		$elements_manager->add_category('civi', [
			'title' => esc_html__('Civi', 'civi'),
			'icon'  => 'fa fa-plug',
		]);
	}

	/**
	 * @param \Elementor\Elements_Manager $element_base
	 *
	 * Add attribute.
	 */
	function add_elementor_attribute($element_base)
	{
		$settings = $element_base->get_settings_for_display();

		$_animation = !empty($settings['_animation']);
		$animation = !empty($settings['animation']);
		$has_animation = $_animation && 'none' !== $settings['_animation'] || $animation && 'none' !== $settings['animation'];

		if ($has_animation) {
			$is_static_render_mode = Plugin::$instance->frontend->is_static_render_mode();

			$civi_effect = array(
				'CiviSlideInDown',
				'CiviSlideInLeft',
				'CiviSlideInRight',
				'CiviSlideInUp',
				'CiviBottomToTop',
				'CiviSpin',
			);

			$civi_current_effect = $civi_animation = '';
			if (!empty($settings['animation'])) {
				$civi_animation = $settings['animation'];
			} elseif (!empty($settings['_animation'])) {
				$civi_animation = $settings['_animation'];
			}

			if (!empty($civi_animation)) {
				if ($civi_animation == 'CiviSlideInDown') {
					$civi_current_effect = 'civi-slide-in-down';
				} elseif ($civi_animation == 'CiviSlideInLeft') {
					$civi_current_effect = 'civi-slide-in-left';
				} elseif ($civi_animation == 'CiviSlideInRight') {
					$civi_current_effect = 'civi-slide-in-right';
				} elseif ($civi_animation == 'CiviSlideInUp') {
					$civi_current_effect = 'civi-slide-in-up';
				} elseif ($civi_animation == 'CiviBottomToTop') {
					$civi_current_effect = 'civi-bottom-to-top';
				} elseif ($civi_animation == 'CiviSpin') {
					$civi_current_effect = 'civi-spin';
				}

				if (!$is_static_render_mode && in_array($civi_animation, $civi_effect)) {
					// Hide the element until the animation begins
					$element_base->add_render_attribute('_wrapper', 'class', ['civi-elementor-loading', $civi_current_effect]);
				}
			}
		}
	}

	/**
	 * Init Widgets
	 *
	 * Include widgets files and register them
	 *
	 * @since  1.0.0
	 *
	 * @access public
	 */
	public function init_widgets()
	{

		// Include Widget files.
		require_once CIVI_ELEMENTOR_DIR . '/module-query.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/base.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/form/form-base.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/posts/posts-base.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/carousel/carousel-base.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/carousel/posts-carousel-base.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/carousel/static-carousel.php';

		require_once CIVI_ELEMENTOR_DIR . '/widgets/accordion.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/button.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/circle-progress-chart.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/google-map.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/heading.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/fancy-heading.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/icon.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/icon-box.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/number-box.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/user-form.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/job-search.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/image-box.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/image-rotate.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/image-animation.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/image-layers.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/image-gallery.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/banner.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/nav-menu.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/shapes.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/flip-box.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/instagram.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/attribute-list.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/gradation.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/timeline.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/list.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/pricing-table.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/twitter.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/team-member.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/social-networks.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/popup-video.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/separator.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/table.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/modern-tabs.php';

		require_once CIVI_ELEMENTOR_DIR . '/widgets/grid/grid-base.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/grid/static-grid.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/grid/client-logo.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/grid/view-demo.php';

		require_once CIVI_ELEMENTOR_DIR . '/widgets/posts/blog.php';

		require_once CIVI_ELEMENTOR_DIR . '/widgets/testimonial-grid.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/carousel/testimonial-carousel.php';

		require_once CIVI_ELEMENTOR_DIR . '/widgets/carousel/team-member-carousel.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/carousel/image-carousel.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/carousel/modern-carousel.php';
		require_once CIVI_ELEMENTOR_DIR . '/widgets/carousel/modern-slider.php';

		// Register Widgets.
		Plugin::instance()->widgets_manager->register(new Widget_Accordion());
		Plugin::instance()->widgets_manager->register(new Widget_Button());
		Plugin::instance()->widgets_manager->register(new Widget_Client_Logo());
		Plugin::instance()->widgets_manager->register(new Widget_Circle_Progress_Chart());
		Plugin::instance()->widgets_manager->register(new Widget_Google_Map());
		Plugin::instance()->widgets_manager->register(new Widget_Heading());
		Plugin::instance()->widgets_manager->register(new Widget_Icon());
		Plugin::instance()->widgets_manager->register(new Widget_Icon_Box());
		Plugin::instance()->widgets_manager->register(new Widget_Number_Box());
		Plugin::instance()->widgets_manager->register(new Widget_User_Form());
		Plugin::instance()->widgets_manager->register(new Widget_Job_Search());
		Plugin::instance()->widgets_manager->register(new Widget_Image_Box());
		Plugin::instance()->widgets_manager->register(new Widget_Image_Rotate());
		Plugin::instance()->widgets_manager->register(new Widget_Image_Animation());
		Plugin::instance()->widgets_manager->register(new Widget_Image_Layers());
		Plugin::instance()->widgets_manager->register(new Widget_Image_Gallery());
		Plugin::instance()->widgets_manager->register(new Widget_Image_Carousel());
		Plugin::instance()->widgets_manager->register(new Widget_Banner());
		Plugin::instance()->widgets_manager->register(new Widget_Nav_Menu());
		Plugin::instance()->widgets_manager->register(new Widget_Shapes());
		Plugin::instance()->widgets_manager->register(new Widget_Modern_Carousel());
		Plugin::instance()->widgets_manager->register(new Widget_Modern_Slider());
		Plugin::instance()->widgets_manager->register(new Widget_Instagram());
		Plugin::instance()->widgets_manager->register(new Widget_Flip_Box());
		Plugin::instance()->widgets_manager->register(new Widget_Blog());
		Plugin::instance()->widgets_manager->register(new Widget_Attribute_List());
		Plugin::instance()->widgets_manager->register(new Widget_List());
		Plugin::instance()->widgets_manager->register(new Widget_Fancy_Heading());
		Plugin::instance()->widgets_manager->register(new Widget_Gradation());
		Plugin::instance()->widgets_manager->register(new Widget_Timeline());
		Plugin::instance()->widgets_manager->register(new Widget_Pricing_Table());
		Plugin::instance()->widgets_manager->register(new Widget_Twitter());
		Plugin::instance()->widgets_manager->register(new Widget_Team_Member());
		Plugin::instance()->widgets_manager->register(new Widget_Team_Member_Carousel());
		Plugin::instance()->widgets_manager->register(new Widget_Testimonial_Carousel());
		Plugin::instance()->widgets_manager->register(new Widget_Testimonial_Grid());
		Plugin::instance()->widgets_manager->register(new Widget_Social_Networks());
		Plugin::instance()->widgets_manager->register(new Widget_Popup_Video());
		Plugin::instance()->widgets_manager->register(new Widget_Separator());
		Plugin::instance()->widgets_manager->register(new Widget_Table());
		Plugin::instance()->widgets_manager->register(new Widget_View_Demo());
		Plugin::instance()->widgets_manager->register(new Widget_Moderm_Tabs());

		/**
		 * Include & Register Dependency Widgets.
		 */

		if (function_exists('mc4wp_get_forms')) {
			require_once CIVI_ELEMENTOR_DIR . '/widgets/form/mailchimp-form.php';

			Plugin::instance()->widgets_manager->register(new Widget_Mailchimp_Form());
		}

		if (defined('WPCF7_VERSION')) {
			require_once CIVI_ELEMENTOR_DIR . '/widgets/form/contact-form-7.php';

			Plugin::instance()->widgets_manager->register(new Widget_Contact_Form_7());
		}
	}

	/**
	 * @param \Elementor\Widgets_Manager $widgets_manager
	 *
	 * Remove unwanted widgets
	 */
	function remove_unwanted_widgets($widgets_manager)
	{
		$elementor_widget_blacklist = array(
			'theme-site-logo',
		);

		foreach ($elementor_widget_blacklist as $widget_name) {
			$widgets_manager->unregister_widget_type($widget_name);
		}
	}
}

Widget_Init::instance()->initialize();
