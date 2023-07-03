<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Css_Filter;
use Elementor\Repeater;
use Elementor\Utils;

defined('ABSPATH') || exit;

class Widget_Team_Member_Carousel extends Static_Carousel
{

	public function get_name()
	{
		return 'civi-team-member-carousel';
	}

	public function get_title()
	{
		return esc_html__('Team Member Carousel', 'civi');
	}

	public function get_icon_part()
	{
		return 'eicon-posts-carousel';
	}

	public function get_keywords()
	{
		return ['team', 'member', 'avatar', 'carousel'];
	}

	public function get_style_depends()
	{
		return ['civi-el-widget-team-member-carousel'];
	}

	public function get_script_depends()
	{
		return ['civi-group-widget-carousel'];
	}

	protected function register_controls()
	{
		$this->add_layout_section();

		parent::register_controls();

		$this->add_image_style_section();

		$this->update_controls();
	}

	private function update_controls()
	{
		$this->update_control('slides', [
			'title_field' => '{{{ name }}}',
		]);
	}

	private function add_layout_section()
	{
		$this->start_controls_section('layout_section', [
			'label' => esc_html__('Layout', 'civi'),
		]);

		$this->add_control('hover_effect', [
			'label'        => esc_html__('Hover Effect', 'civi'),
			'type'         => Controls_Manager::SELECT,
			'options'      => [
				''         => esc_html__('None', 'civi'),
				'zoom-in'  => esc_html__('Zoom In', 'civi'),
				'zoom-out' => esc_html__('Zoom Out', 'civi'),
			],
			'default'      => '',
			'prefix_class' => 'civi-animation-',
		]);

		$this->end_controls_section();
	}

	private function add_image_style_section()
	{
		$this->start_controls_section('images_style_section', [
			'label' => esc_html__('Images', 'civi'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->start_controls_tabs('images_effects');

		$this->start_controls_tab('images_effects_normal_tab', [
			'label' => esc_html__('Normal', 'civi'),
		]);

		$this->add_group_control(Group_Control_Css_Filter::get_type(), [
			'name'     => 'css_filters',
			'selector' => '{{WRAPPER}} .civi-image img',
		]);

		$this->add_control('images_opacity', [
			'label'     => esc_html__('Opacity', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'max'  => 1,
					'min'  => 0.10,
					'step' => 0.01,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .civi-image img' => 'opacity: {{SIZE}};',
			],
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('images_effects_hover_tab', [
			'label' => esc_html__('Hover', 'civi'),
		]);

		$this->add_group_control(Group_Control_Css_Filter::get_type(), [
			'name'     => 'css_filters_hover',
			'selector' => '{{WRAPPER}} .civi-box:hover .civi-image img',
		]);

		$this->add_control('images_opacity_hover', [
			'label'     => esc_html__('Opacity', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'max'  => 1,
					'min'  => 0.10,
					'step' => 0.01,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .civi-box:hover .civi-image img' => 'opacity: {{SIZE}};',
			],
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function add_repeater_controls(Repeater $repeater)
	{
		$repeater->add_control('name', [
			'label'   => esc_html__('Name', 'civi'),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__('John Doe', 'civi'),
		]);

		$repeater->add_control('content', [
			'label' => esc_html__('Content', 'civi'),
			'type'  => Controls_Manager::TEXTAREA,
		]);

		$repeater->add_control('image', [
			'label' => esc_html__('Image', 'civi'),
			'type'  => Controls_Manager::MEDIA,
		]);

		$repeater->add_control('position', [
			'label'   => esc_html__('Position', 'civi'),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__('CEO', 'civi'),
		]);

		$repeater->add_control('profile', [
			'label'         => esc_html__('Profile', 'civi'),
			'type'          => Controls_Manager::URL,
			'placeholder'   => esc_html__('https://your-link.com', 'civi'),
			'show_external' => true,
			'default'       => [
				'url'         => '',
				'is_external' => true,
				'nofollow'    => true,
			],
		]);

		$repeater->add_control('socials', [
			'label'       => esc_html__('Socials', 'civi'),
			'description' => esc_html__('One social per line. Icon class & url separator with |. For eg: fab fa-facebook|https://facebook.com', 'civi'),
			'type'        => Controls_Manager::TEXTAREA,
		]);
	}

	protected function get_repeater_defaults()
	{
		$placeholder_image_src = Utils::get_placeholder_image_src();

		return [
			[
				'name'     => esc_html__('John Doe', 'civi'),
				'position' => esc_html__('CEO', 'civi'),
				'image'    => ['url' => $placeholder_image_src],
			],
			[
				'name'     => esc_html__('John Doe', 'civi'),
				'position' => esc_html__('CEO', 'civi'),
				'image'    => ['url' => $placeholder_image_src],
			],
			[
				'name'     => esc_html__('John Doe', 'civi'),
				'position' => esc_html__('CEO', 'civi'),
				'image'    => ['url' => $placeholder_image_src],
			],
		];
	}

	protected function print_slide()
	{
		$slide = $this->get_current_slide();
		$current_key   = $this->get_current_key();
		$item_link_key = $current_key . '_link';

		if (!empty($slide['profile']['url'])) {
			$this->add_link_attributes($item_link_key, $slide['profile']);
		}
?>
		<div class="civi-team-member civi-box">
			<div class="item">
				<?php if ($slide['image']['url']) : ?>
					<div class="photo civi-image">
						<?php echo \Civi_Image::get_elementor_attachment([
							'settings'       => $slide,
							'image_size_key' => 'image_size',
						]); ?>

						<div class="overlay"></div>

						<?php $this->print_socials(); ?>
					</div>
				<?php endif; ?>

				<div class="info">
					<h3 class="name">
						<?php if (!empty($slide['profile']['url'])) { ?>
							<a <?php $this->print_render_attribute_string($item_link_key); ?>><?php esc_html_e($slide['name']); ?></a>
						<?php } else {
							esc_html_e($slide['name']);
						} ?>
					</h3>

					<?php if (!empty($slide['position'])) : ?>
						<div class="position"><?php esc_html_e($slide['position']); ?></div>
					<?php endif; ?>

					<?php if (!empty($slide['content'])) : ?>
						<div class="description">
							<?php echo wp_kses($slide['content'], 'civi-default'); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php
	}

	private function print_socials()
	{
		$slide = $this->get_current_slide();

		if (empty($slide['socials'])) {
			return;
		}

		$socials = explode("\n", str_replace("\r", "", $slide['socials']));
	?>
		<div class="social-networks">
			<div class="inner">
				<?php
				foreach ($socials as $social) {
					$item = explode('|', $social);
					if (empty($item) || count($item) < 2) {
						continue;
					}
					$icon_class = $item[0];
					$url        = $item[1];
				?>
					<a href="<?php esc_url($url); ?>" target="_blank" rel="nofollow">
						<span class="link-icon <?php echo esc_attr($icon_class); ?>"></span>
					</a>
				<?php
				}
				?>
			</div>
		</div>
	<?php
	}

	protected function render()
	{
		$this->add_render_attribute('wrapper', 'class', 'civi-team-member-carousel');
	?>
		<div <?php $this->print_attributes_string('wrapper'); ?>>
			<?php $this->print_slider(); ?>
		</div>
<?php
	}
}
