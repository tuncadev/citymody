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
				<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M21.7042 19.316C21.5254 17.691 24.5704 12.5448 21.9617 9.08601C21.6917 8.71101 22.0667 7.92351 22.3179 7.39226C22.9279 6.10726 22.7979 5.05726 21.5792 4.42976C20.3604 3.80226 19.4192 4.37976 18.7042 5.42976C18.3667 5.92976 17.8192 6.67976 17.4317 6.64101C13.1442 6.36726 10.5267 11.716 9.08041 12.4498C7.37041 13.3173 3.45541 13.896 2.79416 16.2735C1.93291 19.346 4.65666 22.4085 10.2367 25.446C15.8167 28.4835 19.8617 29.1035 21.9492 26.696C23.5679 24.8373 21.9154 21.2298 21.7042 19.316Z" fill="#191919" />
					<path d="M18.3748 29.1702C18.01 29.1692 17.6456 29.1467 17.2835 29.1027C15.2048 28.8527 12.7435 27.9452 9.7585 26.3202C6.7735 24.6952 4.67225 23.1252 3.331 21.5089C1.821 19.6914 1.316 17.8377 1.831 16.0002C2.46225 13.7502 5.10725 12.8327 7.03975 12.1664C7.57981 11.994 8.10909 11.7895 8.62475 11.5539C8.87475 11.4289 9.466 10.7327 9.93975 10.1789C11.5223 8.32016 13.886 5.53766 17.2798 5.62516C17.5019 5.39094 17.7011 5.13603 17.8748 4.86391C18.9848 3.23891 20.4998 2.75391 22.0323 3.54141C22.8098 3.94141 24.481 5.15891 23.2173 7.82266C23.0819 8.08965 22.9686 8.36724 22.8785 8.65266C24.8473 11.4552 23.8135 14.9902 23.1285 17.3439C22.9223 18.0477 22.666 18.9227 22.6985 19.2064C22.7847 19.7692 22.9041 20.3264 23.056 20.8752C23.5823 22.9739 24.2373 25.5864 22.7048 27.3477C21.6535 28.5614 20.1998 29.1702 18.3748 29.1702ZM17.1248 7.62516C14.7385 7.62516 12.8573 9.83766 11.471 11.4689C10.7323 12.3439 10.1485 13.0252 9.536 13.3352C8.93867 13.6139 8.32408 13.8539 7.696 14.0539C6.0985 14.6052 4.10975 15.2927 3.761 16.5377C3.07725 18.9764 5.4185 21.6764 10.7198 24.5627C16.021 27.4489 19.5423 27.9377 21.1985 26.0389C22.046 25.0639 21.5323 23.0139 21.1185 21.3652C20.942 20.7278 20.8063 20.0798 20.7123 19.4252C20.6373 18.7427 20.8898 17.8764 21.2123 16.7814C21.8373 14.6427 22.6948 11.7139 21.1673 9.68766L21.156 9.67266C20.5385 8.81891 21.0685 7.70266 21.4185 6.96391C21.9885 5.76266 21.4898 5.50641 21.126 5.31891C20.8123 5.15766 20.2873 4.88766 19.536 5.98891C18.9673 6.82391 18.286 7.70766 17.3635 7.63391C17.2798 7.62516 17.1998 7.62516 17.1248 7.62516Z" fill="#191919" />
					<path d="M21.7042 19.316C21.5254 17.691 24.5704 12.5448 21.9617 9.08601C21.6917 8.71101 22.0667 7.92351 22.3179 7.39226C22.9279 6.10726 22.7979 5.05726 21.5792 4.42976C20.3604 3.80226 19.4192 4.37976 18.7042 5.42976C18.3667 5.92976 17.8192 6.67976 17.4317 6.64101C13.1442 6.36726 10.5267 11.716 9.08041 12.4498C7.37041 13.3173 3.45541 13.896 2.79416 16.2735C1.93291 19.346 4.65666 22.4085 10.2367 25.446C15.8167 28.4835 19.8617 29.1035 21.9492 26.696C23.5679 24.8373 21.9154 21.2298 21.7042 19.316Z" fill="#FFD75E" />
					<path d="M10.3053 18.8889C10.7028 18.9151 15.759 21.6839 16.1065 21.9589C16.454 22.2339 15.559 23.3851 14.019 23.4989C12.479 23.6126 10.2765 22.6939 9.83403 21.9351C9.39153 21.1764 9.90778 18.8626 10.3053 18.8889Z" fill="#ED0006" />
					<path d="M15.8232 21.8089C15.5807 21.8227 13.9344 23.5302 12.1982 22.6277C9.79315 21.3777 10.7394 19.2852 10.5732 19.0027C10.4069 18.7202 5.79565 17.0727 5.26815 17.9502C4.7844 18.7564 7.4194 21.6139 11.2369 23.5902C15.1232 25.6014 18.6632 26.3127 19.1244 25.4927C19.6707 24.5102 16.0644 21.7952 15.8232 21.8089Z" fill="white" />
					<path d="M2.48001 9.8367C2.99126 7.8892 5.09001 6.0042 6.98001 5.52795C7.45001 5.41045 7.11126 4.2867 6.58126 4.40295C4.50876 4.86545 2.28251 6.6017 1.26001 9.3567C1.00001 10.0442 2.38376 10.2055 2.48001 9.8367Z" fill="#191919" />
					<path d="M5.84292 9.59303C6.68542 8.36053 7.80417 8.07803 8.86667 7.96803C9.33667 7.92053 9.33667 6.71803 8.84542 6.75678C7.54792 6.86053 6.02917 7.11428 4.79292 8.82053C4.37542 9.39178 5.60292 9.94553 5.84292 9.59303Z" fill="#191919" />
					<path d="M27.699 14.6729C29.2715 16.0091 30.074 18.7841 29.679 20.7454C29.579 21.2316 30.7202 21.5954 30.8465 21.0529C31.3465 18.9279 30.7215 15.8654 28.6365 13.6979C28.1152 13.1566 27.4015 14.4204 27.699 14.6729Z" fill="#191919" />
					<path d="M26.4837 18.0432C27.17 19.4107 26.8675 20.5532 26.4362 21.5607C26.2462 22.0057 27.3337 22.542 27.5425 22.0832C28.135 20.7795 28.5875 19.427 27.6475 17.417C27.3425 16.762 26.2887 17.6532 26.4837 18.0432Z" fill="#191919" />
				</svg>
				<?php echo $settings['heading']; ?>
			</h2>
			<form action="#" method="POST" class="job-alerts-form">
				<?php if ($settings['show_job_alert_name']) : ?>
					<div class="field-input">
						<label for="name"><?php esc_html_e('Job alert name', 'civi_framework'); ?></label>
						<input type="text" id="name" name="name" placeholder="<?php esc_html_e('Enter job alert name', 'civi_framework'); ?>">
					</div>
				<?php endif; ?>
				<div class="field-input">
					<label for="email"><?php esc_html_e('Your email ', 'civi_framework'); ?><span>*</span></label>
					<input type="text" id="email" name="email" required placeholder="<?php esc_html_e('Your email', 'civi_framework'); ?>" value="<?php echo $mail; ?>">
				</div>
				<?php if ($settings['show_job_category']) : ?>
					<div class="field-select">
						<label for="category"><?php esc_html_e('Job category', 'civi_framework'); ?></label>
						<div class="form-select">
							<select name="category" id="category" class="civi-select2">
								<?php civi_get_taxonomy('jobs-categories', false, true); ?>
							</select>
						</div>
					</div>
				<?php endif; ?>
				<?php if ($settings['show_location']) : ?>
					<div class="field-select">
						<label for="location"><?php esc_html_e('Location', 'civi_framework'); ?></label>
						<div class="form-select">
							<select name="location" id="location" class="civi-select2">
								<?php civi_get_taxonomy('jobs-location', false, true); ?>
							</select>
						</div>
					</div>
				<?php endif; ?>
				<?php if ($settings['show_job_experience']) : ?>
					<div class="field-select">
						<label for="experience"><?php esc_html_e('Job experience', 'civi_framework'); ?></label>
						<div class="form-select">
							<select name="experience" id="experience" class="civi-select2">
								<?php civi_get_taxonomy('jobs-experience', false, true); ?>
							</select>
					</div>
					</div>
				<?php endif; ?>
				<?php if ($settings['show_job_skills']) : ?>
					<div class="field-input">
						<label for="skills"><?php esc_html_e('Job skills', 'civi_framework'); ?></label>
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
						<label for="type"><?php esc_html_e('Job type', 'civi_framework'); ?></label>
						<div class="form-select">
							<select data-placeholder="<?php esc_attr_e('Select an option', 'civi-framework'); ?>" multiple="multiple" class="civi-select2" name="types" id="type">
								<?php civi_get_taxonomy('jobs-type', false, false); ?>
							</select>
							<i class="fas fa-angle-down"></i>
						</div>
					</div>
				<?php endif; ?>
				<div class="field-select">
					<label for="frequency"><?php esc_html_e('Frequency', 'civi_framework'); ?></label>
					<div class="form-select">
						<select name="frequency" id="frequency" class="civi-select2">
							<option value="daily"><?php esc_html_e('Select an option', 'civi_framework'); ?></option>
							<option value="daily"><?php esc_html_e('Daily', 'civi_framework'); ?></option>
							<option value="weekly"><?php esc_html_e('Weekly', 'civi_framework'); ?></option>
							<option value="monthly"><?php esc_html_e('Monthly', 'civi_framework'); ?></option>
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
