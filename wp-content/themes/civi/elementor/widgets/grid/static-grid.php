<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;
use Elementor\Repeater;

defined('ABSPATH') || exit;

abstract class Static_Grid extends Grid_Base
{

	private $current_item = null;
	private $current_key  = null;

	abstract protected function add_repeater_controls(Repeater $repeater);

	abstract protected function get_repeater_defaults();

	abstract protected function print_grid_item();

	protected function register_controls()
	{
		$this->start_controls_section('items_section', [
			'label' => esc_html__('Items', 'civi'),
		]);

		$repeater = new Repeater();

		$this->add_repeater_controls($repeater);

		$this->add_control('items', [
			'label'     => esc_html__('Items', 'civi'),
			'type'      => Controls_Manager::REPEATER,
			'fields'    => $repeater->get_controls(),
			'default'   => $this->get_repeater_defaults(),
			'separator' => 'after',
		]);

		$this->end_controls_section();

		parent::register_controls();
	}

	protected function get_current_item()
	{
		return $this->current_item;
	}

	protected function get_current_key()
	{
		return $this->current_key;
	}

	protected function print_grid_items(array $settings)
	{
		foreach ($settings['items'] as $item) :
			$item_id = $item['_id'];
			$item_key = 'item_' . $item_id;

			$this->current_key  = $item_key;
			$this->current_item = $item;

			$this->add_render_attribute($item_key, [
				'class' => [
					'grid-item',
					'elementor-repeater-item-' . $item_id,
				],
			]);
?>
			<div <?php $this->print_attributes_string($item_key); ?>>
				<?php $this->print_grid_item(); ?>
			</div>
<?php endforeach;
	}
}
