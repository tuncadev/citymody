<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;

defined('ABSPATH') || exit;

abstract class Static_Carousel extends Carousel_Base
{

	private $current_slide = null;
	private $current_key   = null;

	abstract protected function add_repeater_controls(Repeater $repeater);

	abstract protected function get_repeater_defaults();

	abstract protected function print_slide();

	protected function register_controls()
	{
		$this->start_controls_section('slides_section', [
			'label' => esc_html__('Slides', 'civi'),
		]);

		$repeater = new Repeater();

		$this->add_repeater_controls($repeater);

		$this->add_control('slides', [
			'label'     => esc_html__('Slides', 'civi'),
			'type'      => Controls_Manager::REPEATER,
			'fields'    => $repeater->get_controls(),
			'default'   => $this->get_repeater_defaults(),
			'separator' => 'after',
		]);

		$this->add_group_control(Group_Control_Image_Size::get_type(), [
			'name'    => 'image_size',
			'default' => 'full',
		]);

		$this->end_controls_section();

		parent::register_controls();
	}

	protected function get_current_slide()
	{
		return $this->current_slide;
	}

	protected function get_current_key()
	{
		return $this->current_key;
	}

	protected function print_slides(array $settings)
	{
		foreach ($settings['slides'] as $slide) :
			$item_id = $slide['_id'];
			$item_key = 'item_' . $item_id;

			$this->current_key   = $item_key;
			$this->current_slide = $slide;

			$this->add_render_attribute($item_key, [
				'class' => [
					'swiper-slide',
					'elementor-repeater-item-' . $item_id,
				],
			]);
?>
			<div <?php $this->print_attributes_string($item_key); ?>>
				<?php $this->print_slide(); ?>
			</div>
<?php endforeach;
	}
}
