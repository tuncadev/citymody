<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use \Elementor\Group_Control_Border;
use Elementor\Icons_Manager;

defined('ABSPATH') || exit;

class Widget_Button extends Base
{

	public function get_name()
	{
		return 'civi-button';
	}

	public function get_title()
	{
		return esc_html__('Advanced Button', 'civi');
	}

	public function get_icon_part()
	{
		return 'eicon-button';
	}

    public function get_style_depends()
    {
        return ['civi-el-widget-button'];
    }

	protected function register_controls()
	{
		$this->register_button_content();
		$this->register_button_wrap_style();
		$this->register_button_icon_style();
	}

	private function register_button_content()
	{
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__('Button', 'civi')
			]
		);

		$this->add_control(
			'text',
			[
				'label' => esc_html__('Text', 'civi'),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('Button', 'civi'),
				'placeholder' => esc_html__('Button', 'civi'),
			]
		);

		$this->add_control(
            'open_lr_form',
            [
                'label' => esc_html__('Open Login/Register Popup', 'civi-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
            ]
        );

		$this->add_control(
			'link',
			[
				'label' => esc_html__('Link', 'civi'),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__('https://your-link.com', 'civi'),
				'default' => [
					'url' => '#',
				],
				'condition' => [
					'open_lr_form' => '',
				],
			]
		);


		$this->add_control(
			'type',
			[
				'label' => esc_html__('Type', 'civi'),
				'type' => Controls_Manager::SELECT,
				'default' => 'classic',
				'separator'     => 'before',
				'options' => Widget_Utils::get_button_style(),
			]
		);

		$this->add_control(
			'shape',
			[
				'label' => esc_html__('Shape', 'civi'),
				'type' => Controls_Manager::SELECT,
				'default' => 'rounded',
				'options' => Widget_Utils::get_button_shape(),
				'condition' => [
					'type[value]!' => 'link',
				],
			]
		);

		$this->add_control(
			'size',
			[
				'label' => esc_html__('Size', 'civi'),
				'type' => Controls_Manager::SELECT,
				'default' => 'md',
				'options' =>  Widget_Utils::get_button_size(),
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => esc_html__('Icon', 'civi'),
				'type' => Controls_Manager::ICONS,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label' => esc_html__('Icon Position', 'civi'),
				'type' => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left' => esc_html__('Before', 'civi'),
					'right' => esc_html__('After', 'civi'),
				],
				'condition' => [
					'icon[value]!' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_button_wrap_style()
	{
		$this->start_controls_section(
			'section_wrap_style',
			[
				'label' => esc_html__('Button', 'civi'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '{{WRAPPER}} .civi-button',
			]
		);

		$this->add_control(
			'button_gradient_background',
			[
				'label' => esc_html__('Use Gradient Background', 'civi'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Show', 'civi'),
				'label_off' => esc_html__('Hide', 'civi'),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'type!' => 'link',
				],
			]
		);

		$this->start_controls_tabs('tabs_button_style');

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__('Normal', 'civi'),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__('Text Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .civi-button' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_border_color',
			[
				'label' => esc_html__('Border Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .civi-button' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'type!' => 'link',
				],
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label' => esc_html__('Background Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .civi-button' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'type!' => ['link', 'outline'],
					'button_gradient_background' => ''
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_background_gradient_color',
				'types' => ['gradient', 'classic'],
				'selector' => '{{WRAPPER}} .civi-button',
				'condition' => [
					'type!' => ['link', 'outline'],
					'button_gradient_background' => 'yes'
				],
			]
		);


		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__('Hover', 'civi'),
			]
		);


		$this->add_control(
			'button_text_color_hover',
			[
				'label' => esc_html__('Text Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .civi-button:hover' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_border_color_hover',
			[
				'label' => esc_html__('Border Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .civi-button:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'type!' => 'link',
				],
			]
		);

		$this->add_control(
			'button_background_color_hover',
			[
				'label' => esc_html__('Background Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .civi-button:hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'type!' => ['link'],
					'button_gradient_background' => ''
				],
			]
		);


		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_background_gradient_color_hover',
				'types' => ['gradient', 'classic'],
				'selector' => '{{WRAPPER}} .civi-button:hover',
				'condition' => [
					'type!' => ['link'],
					'button_gradient_background' => 'yes'
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label' => esc_html__('Hover Animation', 'civi'),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} .civi-button',
				'separator' => 'before',
			]
		);


		$this->add_control(
			'border_radius',
			[
				'label' => esc_html__('Border Radius', 'civi'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .civi-button' => 'border-radius: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__('Alignment', 'civi'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => esc_html__('Left', 'civi'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'civi'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'civi'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => '',
				'prefix_class' => 'elementor%s-align-',
			]
		);

		$this->add_responsive_control(
			'button_width',
			[
				'label'      => esc_html__('Width', 'civi'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .civi-button' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .civi-button',
			]
		);

		$this->add_responsive_control(
			'text_padding',
			[
				'label' => esc_html__('Padding', 'civi'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .civi-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
	}

	private function register_button_icon_style()
	{
		$this->start_controls_section(
			'section_icon_style',
			[
				'label' => esc_html__('Icon', 'civi'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'icon_indent',
			[
				'label' => esc_html__('Icon Spacing', 'civi'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .button-icon-right i' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .button-icon-right svg' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .button-icon-left i' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .button-icon-left svg' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => esc_html__('Size', 'civi'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', 'rem'],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 200,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .civi-button-icon' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs('tabs_icon_style');

		$this->start_controls_tab(
			'tab_icon_normal',
			[
				'label' => esc_html__('Normal', 'civi'),
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__('Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .civi-button-icon' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();


		$this->start_controls_tab(
			'tab_icon_hover',
			[
				'label' => esc_html__('Hover', 'civi'),
			]
		);

		$this->add_control(
			'icon_color_hover',
			[
				'label' => esc_html__('Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .civi-button-icon:hover' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		if( $settings['open_lr_form'] == 'yes' && is_user_logged_in() ){
			return;
		}

		$wrapper_classes = array(
			'civi-button',
			"button-{$settings['size']}",
			"button-{$settings['shape']}",
		);

		if ($settings['type'] !== '') {
			$wrapper_classes[] = "button-{$settings['type']}";
		}

		if ($settings['hover_animation']) {
			$wrapper_classes[] = "elementor-animation-{$settings['hover_animation']}";
		}

		if (!empty($settings['icon']) && !empty($settings['icon']['value'])) {
			$wrapper_classes[] = "button-icon-{$settings['icon_align']}";
		}

		if( $settings['open_lr_form'] == 'yes' ){
			$wrapper_classes[] = 'btn-login';
		}

		$this->add_render_attribute('wrapper', 'class', $wrapper_classes);

		$this->add_inline_editing_attributes('text', 'none');

		if (!empty($settings['link']['url'])) {
			$this->add_link_attributes('wrapper', $settings['link']);
		} elseif( $settings['open_lr_form'] == 'yes' ) {
			$this->add_render_attribute('wrapper', 'href', '#popup-form');
		}
?>
<?php
	if( $settings['open_lr_form'] == 'yes' ){
		echo '<div class="logged-out">';
	}
?>
		<a <?php echo $this->get_render_attribute_string('wrapper') ?>>
			<?php if (!empty($settings['icon']) && !empty($settings['icon']['value']) && ($settings['icon_align'] === 'left')) : ?>
				<span class="civi-button-icon"><?php Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']); ?></span>
			<?php endif; ?>
			<span <?php echo $this->get_render_attribute_string('text') ?>><?php esc_html_e($settings['text']); ?></span>
			<?php if (!empty($settings['icon']) && !empty($settings['icon']['value']) && ($settings['icon_align'] === 'right')) : ?>
				<span class="civi-button-icon"><?php Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']); ?></span>
			<?php endif; ?>
		</a>
<?php
	if( $settings['open_lr_form'] == 'yes' ){
		echo '</div>';
	}
?>
	<?php
	}

	protected function content_template()
	{
		// @formatter:off
	?>
		<# var wrapper_classes=[ 'civi-button' , 'button-' + settings.size, 'button-' + settings.shape, ]; if (settings.type !=='' ) { wrapper_classes.push('button-' + settings.type); } if ( settings.hover_animation ) { wrapper_classes.push('elementor-animation-' + settings.hover_animation); } if ((settings.icon !=='' ) && (settings.icon.value !=='' )) { wrapper_classes.push('button-icon-' + settings.icon_align); } var iconHTML=elementor.helpers.renderIcon( view, settings.icon, { 'aria-hidden' : true }, 'i' , 'object' ); view.addRenderAttribute('wrapper', 'class' , wrapper_classes); view.addRenderAttribute('text', 'class' , 'civi-button-text' ); view.addInlineEditingAttributes( 'text' , 'none' ); #>
			<a href="{{ settings.link.url }}" {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
				<# if ((iconHTML.rendered) && (settings.icon_align==='left' )) { #>
					<span class="civi-button-icon">{{{ iconHTML.value }}}</span>
					<# } #>
						<span {{{ view.getRenderAttributeString( 'text' ) }}}>{{{ settings.text }}}</span>
						<# if ((iconHTML.rendered) && (settings.icon_align==='right' )) { #>
							<span class="civi-button-icon">{{{ iconHTML.value }}}</span>
							<# } #>
			</a>
	<?php
		// @formatter:off
	}
}
