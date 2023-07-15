<?php

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Core\Schemes\Typography;
use Elementor\Plugin;

defined('ABSPATH') || exit;

Plugin::instance()->widgets_manager->register(new Widget_Job_Alerts());

class Widget_Job_Alerts extends Widget_Base
{
	public function get_post_type()
	{
		return 'jobs';
	}

	public function get_name()
	{
		return 'civi-job-alerts';
	}

	public function get_title()
	{
		return esc_html__('Job Alerts', 'civi-framework');
	}

	public function get_icon_part()
	{
		return 'eicon-mail';
	}

	public function get_keywords()
	{
		return ['title', 'text'];
	}

	public function get_script_depends()
	{
		return ['select2',CIVI_PLUGIN_PREFIX . 'select2',CIVI_PLUGIN_PREFIX . 'job-alerts'];
	}

	public function get_style_depends()
	{
		return ['select2',CIVI_PLUGIN_PREFIX . 'select2',CIVI_PLUGIN_PREFIX . 'job-alerts'];
	}

	protected function register_controls()
	{
		$this->add_form_section();
		$this->add_form_style_section();
	}

	private function add_form_section()
	{
		$this->start_controls_section('form_section', [
			'label' => esc_html__('Form', 'civi'),
		]);

		$this->add_control('heading', [
			'label'       => esc_html__('Heading', 'civi'),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => esc_html__('Enter form heading', 'civi'),
			'default'     => esc_html__('Create Job Alert', 'civi'),
		]);

		$this->add_control(
			'show_job_alert_name',
			[
				'label' => esc_html__('Show Job Alert Name', 'civi-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_job_skills',
			[
				'label' => esc_html__('Show Job Skills', 'civi-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_location',
			[
				'label' => esc_html__('Show Location', 'civi-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_job_category',
			[
				'label' => esc_html__('Show Job Category', 'civi-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_job_experience',
			[
				'label' => esc_html__('Show Job Experience', 'civi-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_job_type',
			[
				'label' => esc_html__('Show Job Type', 'civi-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control('submit_text', [
			'label'       => esc_html__('Submit Text', 'civi'),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => esc_html__('Enter Submit Text', 'civi'),
			'default'     => esc_html__('Create job alert', 'civi'),
		]);

		$this->end_controls_section();
	}

	private function add_form_style_section()
	{
		$this->start_controls_section('form_style_section', [
			'label'     => esc_html__('Form Style', 'civi'),
			'tab'       => Controls_Manager::TAB_STYLE,
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'form',
			'scheme'   => Typography::TYPOGRAPHY_1,
			'selector' => '{{WRAPPER}} .form-heading',
		]);

		$this->end_controls_section();
	}

	protected function render()
	{
		if (is_user_logged_in()) {
			$current_user = wp_get_current_user();
			$mail = $current_user->user_email;
		} else {
			$mail = '';
		}
		$settings = $this->get_settings_for_display();
?>
		<div class="job-alerts-wrapper">
			<h2 class="form-heading">
			<img src="https://www.citymody.com/wp-content/uploads/2023/07/bell-icon.png" style="margin-right: 30px;"/>
				<?php echo $settings['heading']; ?>
			</h2>
			<form action="#" method="POST" class="job-alerts-form">
				<?php if ($settings['show_job_alert_name']) : ?>
					<div class="field-input">
						<label for="name"><?php esc_html_e('Job alert name', 'civi-framework'); ?></label>
						<input type="text" id="name" name="name" placeholder="<?php esc_html_e('Enter job alert name', 'civi-framework'); ?>">
					</div>
				<?php endif; ?>
				<div class="field-input">
					<label for="email"><?php esc_html_e('Your email ', 'civi-framework'); ?><span>*</span></label>
					<input type="text" id="email" name="email" required placeholder="<?php esc_html_e('Your email', 'civi-framework'); ?>" value="<?php echo $mail; ?>">
				</div>
				<?php if ($settings['show_job_category']) : ?>
					<div class="field-select">
						<label for="category"><?php esc_html_e('Job category', 'civi-framework'); ?></label>
						<div class="form-select">
							<select name="category" id="category" class="civi-select2">
								<?php civi_get_taxonomy('jobs-categories', false, true); ?>
							</select>
						</div>
					</div>
				<?php endif; ?>
				<?php if ($settings['show_location']) : ?>
					<div class="field-select">
						<label for="location"><?php esc_html_e('Location', 'civi-framework'); ?></label>
						<div class="form-select">
							<select name="location" id="location" class="civi-select2">
								<?php civi_get_taxonomy('jobs-location', false, true); ?>
							</select>
						</div>
					</div>
				<?php endif; ?>
				<?php if ($settings['show_job_experience']) : ?>
					<div class="field-select">
						<label for="experience"><?php esc_html_e('Job experience', 'civi-framework'); ?></label>
						<div class="form-select">
							<select name="experience" id="experience" class="civi-select2">
								<?php civi_get_taxonomy('jobs-experience', false, true); ?>
							</select>
					</div>
					</div>
				<?php endif; ?>
				<?php if ($settings['show_job_skills']) : ?>
					<div class="field-input">
						<label for="skills"><?php esc_html_e('Job skills', 'civi-framework'); ?></label>
						<div class="form-select">
							<select data-placeholder="<?php esc_attr_e('Select skills', 'civi-framework'); ?>" multiple="multiple" class="civi-select2" name="skills">
								<?php civi_get_taxonomy('jobs-skills', false, false); ?>
							</select>
							<i class="fas fa-angle-down"></i>
						</div>
					</div>
				<?php endif; ?>
				<?php if ($settings['show_job_type']) : ?>
					<div class="field-select">
						<label for="type"><?php esc_html_e('Job type', 'civi-framework'); ?></label>
						<div class="form-select">
							<select data-placeholder="<?php esc_attr_e('Select an option', 'civi-framework'); ?>" multiple="multiple" class="civi-select2" name="types" id="type">
								<?php civi_get_taxonomy('jobs-type', false, false); ?>
							</select>
							<i class="fas fa-angle-down"></i>
						</div>
					</div>
				<?php endif; ?>
				<div class="field-select">
					<label for="frequency"><?php esc_html_e('Frequency', 'civi-framework'); ?></label>
					<div class="form-select">
						<select name="frequency" id="frequency" class="civi-select2">
							<option value="daily"><?php esc_html_e('Select an option', 'civi-framework'); ?></option>
							<option value="daily"><?php esc_html_e('Daily', 'civi-framework'); ?></option>
							<option value="weekly"><?php esc_html_e('Weekly', 'civi-framework'); ?></option>
							<option value="monthly"><?php esc_html_e('Monthly', 'civi-framework'); ?></option>
						</select>
					</div>
				</div>
				<div class="field-submit">
					<div class="notice"></div>
					<button class="civi-button">
						<span><?php echo $settings['submit_text']; ?></span>
						<span class="btn-loading"><i class="fal fa-spinner fa-spin large"></i></span>
					</button>
				</div>
			</form>
		</div>
<?php
	}
}
