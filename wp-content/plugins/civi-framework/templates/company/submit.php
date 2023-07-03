<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
global $current_user, $hide_company_fields, $hide_company_group_fields;
$custom_field_company = civi_render_custom_field('company');
$civi_company_page_id = civi_get_option('civi_company_page_id', 0);
wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'company-submit');
wp_enqueue_script('jquery-validate');
wp_localize_script(
	CIVI_PLUGIN_PREFIX . 'company-submit',
	'civi_submit_vars',
	array(
		'ajax_url' => CIVI_AJAX_URL,
		'not_found' => esc_html__("We didn't find any results, you can retry with other keyword.", 'civi-framework'),
		'not_company' => esc_html__('No company found', 'civi-framework'),
		'company_dashboard' => get_page_link($civi_company_page_id),
		'custom_field_company' => $custom_field_company,
	)
);
$form = 'submit-company';
$action = 'add_company';
$company_id = get_the_ID();

$hide_company_fields = civi_get_option('hide_company_fields', array());
if (!is_array($hide_company_fields)) {
	$hide_company_fields = array();
}
$hide_company_group_fields = civi_get_option('hide_company_group_fields', array());
if (!is_array($hide_company_group_fields)) {
	$hide_company_group_fields = array();
}
$layout = array('general', 'media', 'social', 'location', 'gallery', 'video');
?>

<div class="entry-my-page submit-company-dashboard">
	<form action="#" method="post" id="submit_company_form" class="form-dashboard" enctype="multipart/form-data" data-titleerror="<?php echo esc_html__('Please enter company name', 'civi-framework'); ?>" data-deserror="<?php echo esc_html__('Please enter company description', 'civi-framework'); ?>" data-caterror="<?php echo esc_html__('Please choose category', 'civi-framework'); ?>" data-sizeerror="<?php echo esc_html__('Please choose size', 'civi-framework'); ?>" data-emailerror="<?php echo esc_html__('Please choose email', 'civi-framework'); ?>">
		<div class="content-company">
			<div class="row">
				<div class="col-lg-8 col-md-7">
					<div class="submit-company-header civi-submit-header">
						<div class="entry-title">
							<h4><?php esc_html_e('Submit company', 'civi-framework') ?></h4>
						</div>
						<div class="button-warpper">
							<a href="<?php echo civi_get_permalink('company'); ?>" class="civi-button button-outline">
								<?php esc_html_e('Cancel', 'civi-framework') ?>
							</a>
							<button type="submit" class="btn-submit-company civi-button" name="submit_company">
								<span><?php esc_html_e('Publish', 'civi-framework'); ?></span>
								<span class="btn-loading"><i class="fal fa-spinner fa-spin large"></i></span>
							</button>
						</div>
					</div>
					<?php foreach ($layout as $value) {
						switch ($value) {
							case 'general':
								$name = esc_html__('Basic info', 'civi-framework');
								break;
							case 'media':
								$name = esc_html__('Media', 'civi-framework');
								break;
							case 'social':
								$name = esc_html__('Social network', 'civi-framework');
								break;
							case 'location':
								$name = esc_html__('Location', 'civi-framework');
								break;
							case 'gallery':
								$name = esc_html__('Gallery', 'civi-framework');
								break;
							case 'video':
								$name = esc_html__('Video', 'civi-framework');
								break;
						}
						if (!in_array($value, $hide_company_group_fields)) : ?>
							<div class="block-from" id="<?php echo 'company-submit-' . esc_attr($value); ?>">
								<h6><?php echo $name ?></h6>
								<?php civi_get_template('company/submit/' . $value . '.php'); ?>
							</div>
					<?php endif;
					} ?>

					<?php $custom_field_company = civi_render_custom_field('company');
					if (count($custom_field_company) > 0) : ?>
						<div class="block-from" id="company-submit-additional">
							<h6><?php echo esc_html__('Additional', 'civi-framework'); ?></h6>
							<?php civi_get_template('company/submit/additional.php'); ?>
						</div>
					<?php endif; ?>

					<?php wp_nonce_field('civi_submit_company_action', 'civi_submit_company_nonce_field'); ?>

					<input type="hidden" name="company_form" value="<?php echo esc_attr($form); ?>" />
					<input type="hidden" name="company_action" value="<?php echo esc_attr($action) ?>" />
					<input type="hidden" name="company_id" value="<?php echo esc_attr($company_id); ?>" />
				</div>
				<div class="col-lg-4 col-md-5">
					<div class="widget-area-init has-sticky">
						<div class="about-company-dashboard block-archive-sidebar">
							<h3 class="title-company-about">
								<?php esc_html_e('Preview', 'civi-framework') ?></h3>
							<div class="info-company">
								<div class="img-company"><i class="far fa-camera"></i></div>
								<div class="company-right">
									<div class="title-wapper">
										<h4 class="title-about" data-title="<?php esc_attr_e('Company name', 'civi-framework') ?>"><?php esc_html_e('Company name', 'civi-framework') ?></h4>
										<?php if ((!in_array('general', $hide_company_group_fields) && !in_array('fields_company_website', $hide_company_fields))
											|| (!in_array('general', $hide_company_group_fields) && !in_array('fields_company_phone', $hide_company_fields))
											|| (!in_array('location', $hide_company_group_fields) && !in_array('fields_company_location', $hide_company_fields))
										) : ?>
											<div class="civi-check-company tip">
												<div class="tip-content">
													<h4><?php esc_html_e('Conditions for a green tick:', 'civi-framework') ?></h4>
													<ul class="list-check">
														<?php if (!in_array('general', $hide_company_group_fields) && !in_array('fields_company_website', $hide_company_fields)) : ?>
															<li class="check-webs" data-verified="<?php esc_attr_e('Website has been verified', 'civi-framework') ?>" data-not-verified="<?php esc_attr_e('Website not been verified', 'civi-framework') ?>">
																<i class="fas fa-check"></i>
																<?php esc_html_e('Website not been verified', 'civi-framework') ?>
															</li>
														<?php endif; ?>
														<?php if (!in_array('general', $hide_company_group_fields) && !in_array('fields_company_phone', $hide_company_fields)) : ?>
															<li class="check-phone" data-verified="<?php esc_attr_e('Phone has been verified', 'civi-framework') ?>" data-not-verified="<?php esc_attr_e('Phone not been verified', 'civi-framework') ?>">
																<i class="fas fa-check"></i>
																<?php esc_html_e('Phone not been verified', 'civi-framework') ?>
															</li>
														<?php endif; ?>
														<?php if (!in_array('location', $hide_company_group_fields) && !in_array('fields_company_location', $hide_company_fields)) : ?>
															<li class="check-location" data-verified="<?php esc_attr_e('Location has been verified', 'civi-framework') ?>" data-not-verified="<?php esc_attr_e('Location not been verified', 'civi-framework') ?>">
																<i class="fas fa-check"></i>
																<?php esc_html_e('Location not been verified', 'civi-framework') ?>
															</li>
														<?php endif; ?>
													</ul>
												</div>
											</div>
										<?php endif; ?>
									</div>
									<i class="fas fa-map-marker-alt"></i><span class="location-about" data-location="<?php esc_attr_e('Location', 'civi-framework') ?>"><?php esc_html_e('Location', 'civi-framework') ?></span>
								</div>
							</div>
							<div class="des-about"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>