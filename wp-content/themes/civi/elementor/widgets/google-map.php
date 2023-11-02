<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;

defined('ABSPATH') || exit;

class Widget_Google_Map extends Base
{

	public function get_name()
	{
		return 'civi-google-map';
	}

	public function get_title()
	{
		return esc_html__('Advanced Google Map', 'civi');
	}

	public function get_icon_part()
	{
		return 'eicon-google-maps';
	}

	public function get_keywords()
	{
		return ['google', 'map', 'location'];
	}

	public function get_script_depends()
	{
		return ['gmap3', 'gmap-api', 'civi-widget-google-map'];
	}

    public function get_style_depends()
    {
        return ['civi-el-widget-google-map'];
    }

	protected function register_controls()
	{
		$this->add_map_options_section();

		$this->add_map_markers_section();

		$this->add_map_markers_style_section();
	}

	private function add_map_options_section()
	{
		$this->start_controls_section('map_options_section', [
			'label' => esc_html__('Map', 'civi'),
		]);

		$this->add_control('height', [
			'label'   => esc_html__('Height', 'civi'),
			'type'    => Controls_Manager::SLIDER,
			'default' => [
				'size' => 480,
			],
			'range'   => [
				'px' => [
					'min' => 40,
					'max' => 1440,
				],
			],
		]);

		$this->add_control('style', [
			'label'   => esc_html__('Map Style', 'civi'),
			'type'    => Controls_Manager::SELECT,
			'options' => $this->get_map_style(),
			'default' => 'ultra_light_with_labels',
		]);

		$this->add_control('type', [
			'label'   => esc_html__('Map Type', 'civi'),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'roadmap'   => esc_html__('Roadmap', 'civi'),
				'satellite' => esc_html__('Satellite', 'civi'),
				'hybrid'    => esc_html__('Hybrid', 'civi'),
				'terrain'   => esc_html__('Terrain', 'civi'),
			],
			'default' => 'roadmap',
		]);

		$this->add_control('zoom_level', [
			'label'       => esc_html__('Zoom Level', 'civi'),
			'placeholder' => '12',
			'type'        => Controls_Manager::NUMBER,
			'min'         => 1,
			'max'         => 17,
			'step'        => 1,
			'default'     => 12,
		]);

		$this->add_control('scrollwheel', [
			'label'   => esc_html__('Mouse Scroll Wheel Zoom', 'civi'),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		]);

		$this->add_control('draggable', [
			'label'   => esc_html__('Draggable', 'civi'),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		]);

		$this->add_control('map_controls_heading', [
			'label'     => esc_html__('Controls', 'civi'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_control('zoom_control', [
			'label' => esc_html__('Zoom Control', 'civi'),
			'type'  => Controls_Manager::SWITCHER,
		]);

		$this->add_control('scale_control', [
			'label' => esc_html__('Scale Control', 'civi'),
			'type'  => Controls_Manager::SWITCHER,
		]);

		$this->add_control('fullscreen_control', [
			'label' => esc_html__('Fullscreen Control', 'civi'),
			'type'  => Controls_Manager::SWITCHER,
		]);

		$this->add_control('street_view_control', [
			'label' => esc_html__('Street View Control', 'civi'),
			'type'  => Controls_Manager::SWITCHER,
		]);

		$this->end_controls_section();
	}

	private function add_map_markers_section()
	{
		$this->start_controls_section('markers_section', [
			'label' => esc_html__('Markers', 'civi'),
		]);

		$this->add_control('marker_style', [
			'label'       => esc_html__('Marker Style', 'civi'),
			'description' => esc_html__('Select default style for all markers', 'civi'),
			'type'        => Controls_Manager::SELECT,
			'options'     => [
				'icon'   => esc_html__('Icon', 'civi'),
				'signal' => esc_html__('Signal', 'civi'),
			],
			'default'     => 'icon',
		]);

		$this->add_control('marker_icon', [
			'label'     => esc_html__('Marker Icon', 'civi'),
			'type'      => Controls_Manager::MEDIA,
			'default'   => [
				'url' => $this->get_default_marker_icon(),
			],
			'condition' => [
				'marker_style' => 'icon',
			],
			'classes'   => 'civi-control-media-auto',
		]);

		$repeater = new Repeater();

		$repeater->start_controls_tabs('marker_repeater_tabs');

		$repeater->start_controls_tab('marker_content_tab', [
			'label' => esc_html__('Marker', 'civi'),
		]);

		$repeater->add_control('address', [
			'label'       => esc_html__('Address or Coordinate', 'civi'),
			'type'        => Controls_Manager::TEXT,
			'label_block' => true,
		]);

		$repeater->add_control('style', [
			'label'   => esc_html__('Style', 'civi'),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				''       => esc_html__('Default', 'civi'),
				'icon'   => esc_html__('Icon', 'civi'),
				'signal' => esc_html__('Signal', 'civi'),
			],
			'default' => '',
		]);

		$repeater->add_control('signal_color', [
			'label'     => esc_html__('Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} {{CURRENT_ITEM}} .style-signal .animated-dot' => 'color: {{VALUE}};',
			],
			'condition' => [
				'style!' => 'icon',
			],
		]);

		$repeater->add_control('icon', [
			'label'     => esc_html__('Icon', 'civi'),
			'type'      => Controls_Manager::MEDIA,
			'condition' => [
				'style!' => 'signal',
			],
			'classes'   => 'civi-control-media-auto',
		]);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab('marker_popup_tab', [
			'label' => esc_html__('Popup', 'civi'),
		]);

		$repeater->add_control('overlay_display', [
			'label'   => esc_html__('Display', 'civi'),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				''       => esc_html__('Hover', 'civi'),
				'always' => esc_html__('Always', 'civi'),
			],
			'default' => '',
		]);

		$repeater->add_control('title', [
			'label'       => esc_html__('Title', 'civi'),
			'label_block' => true,
			'type'        => Controls_Manager::TEXT,
		]);

		$repeater->add_control('content', [
			'label' => esc_html__('Content', 'civi'),
			'type'  => Controls_Manager::TEXTAREA,
		]);

		$repeater->add_control('image', [
			'label'       => esc_html__('Image', 'civi'),
			'description' => esc_html__('Choose an image that displays on left info box', 'civi'),
			'type'        => Controls_Manager::MEDIA,
			'classes'     => 'civi-control-media-auto',
		]);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control('markers', [
			'label'       => esc_html__('Markers', 'civi'),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => [
				[
					'address' => '40.7590615,-73.969231',
				],
			],
			'title_field' => '{{{ title }}}',
		]);

		$this->add_group_control(Group_Control_Image_Size::get_type(), [
			'label'     => esc_html__('Popup Image Size', 'civi'),
			'name'      => 'popup_image_size',
			'default'   => 'full',
			'separator' => 'before',
		]);

		$this->end_controls_section();
	}

	private function add_map_markers_style_section()
	{
		$this->start_controls_section('markers_popup_style_section', [
			'label' => esc_html__('Markers Popup', 'civi'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control('markers_popup_wrapper_heading', [
			'label' => esc_html__('Wrapper', 'civi'),
			'type'  => Controls_Manager::HEADING,
		]);

		$this->add_responsive_control('markers_popup_padding', [
			'label'      => esc_html__('Padding', 'civi'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .civi-map-overlay-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('markers_popup_width', [
			'label'      => esc_html__('Width', 'civi'),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
			],
			'size_units' => ['%', 'px'],
			'range'      => [
				'%'  => [
					'min' => 5,
					'max' => 50,
				],
				'px' => [
					'min' => 300,
					'max' => 1000,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .civi-map-overlay-content' => 'width: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_control('markers_popup_image_heading', [
			'label'     => esc_html__('Image', 'civi'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_responsive_control('markers_popup_image_width', [
			'label'      => esc_html__('Width', 'civi'),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
			],
			'size_units' => ['%', 'px'],
			'range'      => [
				'%'  => [
					'min' => 5,
					'max' => 50,
				],
				'px' => [
					'min' => 30,
					'max' => 200,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .map-marker-image img' => 'width: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('markers_popup_image_spacing', [
			'label'      => esc_html__('Spacing', 'civi'),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
			],
			'size_units' => ['px', '%', 'em'],
			'range'      => [
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .map-marker-image' => 'margin-right: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_control('markers_popup_title_heading', [
			'label'     => esc_html__('Title', 'civi'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'markers_popup_title',
			'selector' => '{{WRAPPER}} .map-marker-title',
		]);

		$this->add_control('markers_popup_title_color', [
			'label'     => esc_html__('Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .map-marker-title' => 'color: {{VALUE}};',
			],
		]);

		$this->add_control('markers_popup_description_heading', [
			'label'     => esc_html__('Content', 'civi'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'markers_popup_description',
			'selector' => '{{WRAPPER}} .map-marker-description',
		]);

		$this->add_control('markers_popup_description_color', [
			'label'     => esc_html__('Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .map-marker-description' => 'color: {{VALUE}};',
			],
		]);

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('wrapper', 'class', [
			'civi-google-map',
			'marker-style-' . $settings['marker_style'],
		]);

		$this->add_render_attribute('map', 'class', 'map');
		$this->add_render_attribute('map', 'data-height', $settings['height']['size']);

		$map_options = $this->get_map_js_options($settings);
?>
		<div <?php $this->print_attributes_string('wrapper'); ?>>
			<div <?php $this->print_attributes_string('map'); ?>></div>
			<div class="map-options hide">
				<?php echo wp_json_encode($map_options, JSON_HEX_QUOT | JSON_HEX_TAG); ?>
			</div>
		</div>
<?php
	}

	private function get_map_js_options($settings)
	{
		$snippet = $this->get_map_style_snippet($settings['style']);

		$scrollwheel = !empty($settings['scrollwheel']) && 'yes' === $settings['scrollwheel'] ? true : false;
		$draggable   = !empty($settings['draggable']) && 'yes' === $settings['draggable'] ? true : false;

		$zoom_control        = !empty($settings['zoom_control']) && 'yes' === $settings['zoom_control'] ? true : false;
		$scale_control       = !empty($settings['scale_control']) && 'yes' === $settings['scale_control'] ? true : false;
		$street_view_control = !empty($settings['street_view_control']) && 'yes' === $settings['street_view_control'] ? true : false;
		$fullscreen_control  = !empty($settings['fullscreen_control']) && 'yes' === $settings['fullscreen_control'] ? true : false;

		$map_options = array(
			'settings' => array(
				'zoom'              => $settings['zoom_level'],
				'mapTypeControl'    => false,
				'mapTypeId'         => $settings['type'],
				'styles'            => $snippet,
				'zoomControl'       => $zoom_control,
				'scaleControl'      => $scale_control,
				'streetViewControl' => $street_view_control,
				'fullscreenControl' => $fullscreen_control,
				'scrollwheel'       => $scrollwheel,
				'draggable'         => $draggable,
			),
			'marker'   => array(),
			'overlay'  => array(),
		);

		$loop_count = 1;

		foreach ($settings['markers'] as $marker) {
			$position = trim($marker['address'], ' ');
			$position = explode(',', $position);

			$overlay_value = $this->get_overlay_value($settings, $marker, $position);

			$map_options['marker'][] = array(
				'position' => $position,
			);

			$map_options['overlay'][] = $overlay_value;

			if (1 === $loop_count) {
				$map_options['settings']['center'] = $position;
			}

			$loop_count++;
		}

		return $map_options;
	}

	private function get_overlay_value($settings, $marker, $position)
	{
		$marker_id         = $marker['_id'];
		$item_key          = 'marker_overlay_' . $marker_id;
		$item_marker_style = !empty($marker['style']) ? $marker['style'] : $settings['marker_style'];

		$this->add_render_attribute($item_key, 'class', [
			'civi-map-overlay',
			"civi-map-overlay-{$marker['overlay_display']}",
			"elementor-repeater-item-{$marker_id}",
		]);

		$icon = '';
		if (!empty($marker['icon']['url'])) {
			$icon = '<img src="' . esc_url($marker['icon']['url']) . '"  />';
		} else {
			if (!empty($settings['marker_icon']['url'])) {
				$icon = '<img src="' . esc_url($settings['marker_icon']['url']) . '"  />';
			}
		}

		$signal_template = '<div class="animated-dot"><div class="middle-dot"></div><div class="signal signal-1"></div><div class="signal signal-2"></div></div>';
		$icon_template   = '<div class="gmap-marker-icon">' . $icon . '</div>';
		$info_template   = '';

		if ($marker['title'] !== '' || $marker['content'] !== '') {
			$info_template .= '<div class="civi-map-overlay-template"><div class="civi-map-overlay-content"><div class="civi-map-overlay-info">';

			if (isset($marker['image'])) {
				$size  = \Civi_Image::elementor_parse_image_size($settings, '80x9999', 'popup_image_size');
				$image = \Civi_Image::get_attachment_by_id(array(
					'id'   => $marker['image']['id'],
					'size' => $size,
				));

				$info_template .= '<div class="map-marker-image">' . $image . '</div>';
			}

			$info_template .= '<div class="map-marker-content">';
			$info_template .= '<h5 class="map-marker-title">' . $marker['title'] . '</h5>';
			$info_template .= '<div class="map-marker-description">' . $marker['content'] . '</div>';
			$info_template .= '</div></div></div></div>';
		}

		if ('signal' === $item_marker_style) {
			$template = $signal_template . $info_template;
		} else {
			$template = $icon_template . $info_template;
		}

		$template = '<div ' . $this->get_render_attribute_string($item_key) . '><div class="gmap-info-wrapper style-' . $item_marker_style . '">' . $template . '</div></div>';

		$overlay_value = array(
			'position' => $position,
			'content'  => $template,
		);

		return $overlay_value;
	}

	private function get_map_style()
	{
		return [
			'no_label_bright_colors'  => esc_html__('No Label Bright Colors', 'civi'),
			'grayscale'               => esc_html__('Grayscale', 'civi'),
			'subtle_grayscale'        => esc_html__('Subtle Grayscale', 'civi'),
			'apple_paps_esque'        => esc_html__('Apple Maps-esque', 'civi'),
			'pale_dawn'               => esc_html__('Pale Dawn', 'civi'),
			'midnight_commander'      => esc_html__('Midnight Commander', 'civi'),
			'blue_water'              => esc_html__('Blue Water', 'civi'),
			'retro'                   => esc_html__('Retro', 'civi'),
			'paper'                   => esc_html__('Paper', 'civi'),
			'ultra_light_with_labels' => esc_html__('Ultra Light with Labels', 'civi'),
			'shades_of_grey'          => esc_html__('Shades Of Grey', 'civi'),
			'easy_light'              => esc_html__('Easy Light', 'civi'),
			'wy'                      => esc_html__('WY', 'civi'),
		];
	}

	private function get_map_style_snippet($map_style)
	{
		switch ($map_style) {
			case 'no_label_bright_colors':
				$snippet = '[{"featureType":"all","elementType":"all","stylers":[{"saturation":"32"},{"lightness":"-3"},{"visibility":"on"},{"weight":"1.18"}]},{"featureType":"administrative","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"landscape.man_made","elementType":"all","stylers":[{"saturation":"-70"},{"lightness":"14"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"saturation":"100"},{"lightness":"-14"}]},{"featureType":"water","elementType":"labels","stylers":[{"visibility":"off"},{"lightness":"12"}]}]';
				break;
			case 'grayscale':
				$snippet = '[{"featureType":"all","elementType":"all","stylers":[{"saturation":-100},{"gamma":0.5}]}]';
				break;
			case 'subtle_grayscale':
				$snippet = '[{"featureType":"landscape","stylers":[{"saturation":-100},{"lightness":65},{"visibility":"on"}]},{"featureType":"poi","stylers":[{"saturation":-100},{"lightness":51},{"visibility":"simplified"}]},{"featureType":"road.highway","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"road.arterial","stylers":[{"saturation":-100},{"lightness":30},{"visibility":"on"}]},{"featureType":"road.local","stylers":[{"saturation":-100},{"lightness":40},{"visibility":"on"}]},{"featureType":"transit","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"administrative.province","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"labels","stylers":[{"visibility":"on"},{"lightness":-25},{"saturation":-100}]},{"featureType":"water","elementType":"geometry","stylers":[{"hue":"#ffff00"},{"lightness":-25},{"saturation":-97}]}]';
				break;
			case 'apple_paps_esque':
				$snippet = '[{"featureType":"landscape.man_made","elementType":"geometry","stylers":[{"color":"#f7f1df"}]},{"featureType":"landscape.natural","elementType":"geometry","stylers":[{"color":"#d0e3b4"}]},{"featureType":"landscape.natural.terrain","elementType":"geometry","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"poi.business","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi.medical","elementType":"geometry","stylers":[{"color":"#fbd3da"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#bde6ab"}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffe15f"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#efd151"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"road.local","elementType":"geometry.fill","stylers":[{"color":"black"}]},{"featureType":"transit.station.airport","elementType":"geometry.fill","stylers":[{"color":"#cfb2db"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#a2daf2"}]}]';
				break;
			case 'pale_dawn':
				$snippet = '[{"featureType":"administrative","elementType":"all","stylers":[{"visibility":"on"},{"lightness":33}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2e5d4"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#c5dac6"}]},{"featureType":"poi.park","elementType":"labels","stylers":[{"visibility":"on"},{"lightness":20}]},{"featureType":"road","elementType":"all","stylers":[{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#c5c6c6"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#e4d7c6"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#fbfaf7"}]},{"featureType":"water","elementType":"all","stylers":[{"visibility":"on"},{"color":"#acbcc9"}]}]';
				break;
			case 'paper':
				$snippet = '[{"featureType":"administrative","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"visibility":"simplified"},{"hue":"#0066ff"},{"saturation":74},{"lightness":100}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"off"},{"weight":0.6},{"saturation":-85},{"lightness":61}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"visibility":"on"}]},{"featureType":"road.arterial","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road.local","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"water","elementType":"all","stylers":[{"visibility":"simplified"},{"color":"#5f94ff"},{"lightness":26},{"gamma":5.86}]}]';
				break;
			case 'retro':
				$snippet = '[{"featureType":"administrative","stylers":[{"visibility":"off"}]},{"featureType":"poi","stylers":[{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"simplified"}]},{"featureType":"water","stylers":[{"visibility":"simplified"}]},{"featureType":"transit","stylers":[{"visibility":"simplified"}]},{"featureType":"landscape","stylers":[{"visibility":"simplified"}]},{"featureType":"road.highway","stylers":[{"visibility":"off"}]},{"featureType":"road.local","stylers":[{"visibility":"on"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"visibility":"on"}]},{"featureType":"water","stylers":[{"color":"#84afa3"},{"lightness":52}]},{"stylers":[{"saturation":-17},{"gamma":0.36}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"color":"#3f518c"}]}]';
				break;
			case 'shades_of_grey':
				$snippet = '[{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]}]';
				break;
			case 'midnight_commander':
				$snippet = '[{"featureType":"all","elementType":"labels.text.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"color":"#000000"},{"lightness":13}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#144b53"},{"lightness":14},{"weight":1.4}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#08304b"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#0c4152"},{"lightness":5}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#0b434f"},{"lightness":25}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#000000"}]},{"featureType":"road.arterial","elementType":"geometry.stroke","stylers":[{"color":"#0b3d51"},{"lightness":16}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"}]},{"featureType":"transit","elementType":"all","stylers":[{"color":"#146474"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#021019"}]}]';
				break;
			case 'blue_water':
				$snippet = '[{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#46bcec"},{"visibility":"on"}]}]';
				break;
			case 'ultra_light_with_labels':
				$snippet = '[{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}]';
				break;
			case 'easy_light':
				$snippet = '[{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#6195a0"}]},{"featureType":"administrative.province","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"lightness":"0"},{"saturation":"0"},{"color":"#f5f5f2"},{"gamma":"1"}]},{"featureType":"landscape.man_made","elementType":"all","stylers":[{"lightness":"-3"},{"gamma":"1.00"}]},{"featureType":"landscape.man_made","elementType":"geometry.fill","stylers":[{"color":"#f8f8f8"}]},{"featureType":"landscape.natural.terrain","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"poi.attraction","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"poi.business","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"poi.government","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"poi.medical","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#e6f2da"},{"visibility":"on"}]},{"featureType":"poi.park","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"poi.place_of_worship","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"poi.school","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"poi.sports_complex","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45},{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#f9d6b5"},{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"labels.text","stylers":[{"color":"#4e4e4e"}]},{"featureType":"road.arterial","elementType":"labels.text.fill","stylers":[{"color":"#787878"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"road.local","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"transit.line","elementType":"geometry.fill","stylers":[{"color":"#e5e5e5"}]},{"featureType":"transit.station.airport","elementType":"labels.icon","stylers":[{"hue":"#0a00ff"},{"saturation":"-77"},{"gamma":"0.57"},{"lightness":"0"},{"visibility":"off"}]},{"featureType":"transit.station.bus","elementType":"labels.icon","stylers":[{"visibility":"on"},{"hue":"#008eff"}]},{"featureType":"transit.station.rail","elementType":"labels.text.fill","stylers":[{"color":"#43321e"}]},{"featureType":"transit.station.rail","elementType":"labels.icon","stylers":[{"hue":"#005dff"},{"lightness":"4"},{"gamma":"0.75"},{"saturation":"-68"},{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#eaf6f8"},{"visibility":"on"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#cbeefa"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"lightness":"-49"},{"saturation":"-53"},{"gamma":"0.79"}]},{"featureType":"water","elementType":"labels.icon","stylers":[{"visibility":"off"}]}]';
				break;
			case 'wy':
				$snippet = '[{"featureType":"all","elementType":"geometry.fill","stylers":[{"weight":"2.00"}]},{"featureType":"all","elementType":"geometry.stroke","stylers":[{"color":"#9c9c9c"}]},{"featureType":"all","elementType":"labels.text","stylers":[{"visibility":"on"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"landscape","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"landscape.man_made","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"color":"#eeeeee"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#7b7b7b"}]},{"featureType":"road","elementType":"labels.text.stroke","stylers":[{"color":"#ffffff"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#46bcec"},{"visibility":"on"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#c8d7d4"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#070707"}]},{"featureType":"water","elementType":"labels.text.stroke","stylers":[{"color":"#ffffff"}]}]';
				break;
			default:
				$snippet = '';
				break;
		}

		$snippet = json_decode($snippet);

		return $snippet;
	}

	private function get_default_marker_icon()
	{
		$icon_url = CIVI_ELEMENTOR_ASSETS . '/images/map-marker.png';

		return $icon_url;
	}
}
