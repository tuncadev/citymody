<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $jobs_data, $jobs_meta_data, $hide_jobs_fields;
?>
<div class="row">
    <div class="form-group col-md-6">
        <label><?php esc_html_e('Show pay by', 'civi-framework'); ?></label>
        <div class="select2-field">
			<select id="select-salary-pay" name="jobs_salary_show" class="civi-select2">
				<option <?php if (isset($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_salary_show'][0])) {
					if ($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_salary_show'][0] == "range") {
						echo 'selected';
					}
				} ?> value="range"><?php esc_html_e('Range', 'civi-framework'); ?></option>
				<option <?php if (isset($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_salary_show'][0])) {
					if ($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_salary_show'][0] == "starting_amount") {
						echo 'selected';
					}
				} ?> value="starting_amount"><?php esc_html_e('Starting amount', 'civi-framework'); ?></option>
				<option <?php if (isset($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_salary_show'][0])) {
					if ($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_salary_show'][0] == "maximum_amount") {
						echo 'selected';
					}
				} ?> value="maximum_amount"><?php esc_html_e('Maximum amount', 'civi-framework'); ?></option>
				<option <?php if (isset($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_salary_show'][0])) {
					if ($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_salary_show'][0] == "agree") {
						echo 'selected';
					}
				} ?> value="agree"><?php esc_html_e('Negotiable Price', 'civi-framework'); ?></option>
			</select>
		</div>
    </div>
    <div class="form-group col-md-6">
        <label><?php esc_html_e('Currency Type', 'civi-framework'); ?></label>
        <div class="select2-field">
			<select name="jobs_currency_type" class="civi-select2">
				<?php civi_get_select_currency_type(true); ?>
			</select>
		</div>
    </div>
    <div class="civi-section-salary-select" id="range">
        <div class="form-group col-md-6">
            <label for="jobs_salary_minimum"><?php esc_html_e('Minimum', 'civi-framework'); ?></label>
            <input type="number" id="jobs_salary_minimum" name="jobs_salary_minimum"
                   value="<?php if (isset($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_salary_minimum'][0])) {
                       echo $jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_salary_minimum'][0];
                   } ?>">
        </div>
        <div class="form-group col-md-6">
            <label for="jobs_salary_maximum"><?php esc_html_e('Maximum', 'civi-framework'); ?></label>
            <input type="number" id="jobs_salary_maximum" name="jobs_salary_maximum"
                   value="<?php if (isset($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_salary_maximum'][0])) {
                       echo $jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_salary_maximum'][0];
                   } ?>">
        </div>
    </div>
    <div class="civi-section-salary-select col-md-6" id="starting_amount">
        <label for="jobs_minimum_price"><?php esc_html_e('Minimum', 'civi-framework'); ?></label>
        <input type="number" id="jobs_minimum_price" name="jobs_minimum_price"
               value="<?php if (isset($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_minimum_price'][0])) {
                   echo $jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_minimum_price'][0];
               } ?>">
    </div>
    <div class="civi-section-salary-select col-md-6" id="maximum_amount">
        <label for="jobs_maximum_price"><?php esc_html_e('Maximum', 'civi-framework'); ?></label>
        <input type="number" id="jobs_maximum_price" name="jobs_maximum_price"
               value="<?php if (isset($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_maximum_price'][0])) {
                   echo $jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_maximum_price'][0];
               } ?>">
    </div>
    <div class="form-group col-md-6">
        <label><?php esc_html_e('Rate', 'civi-framework'); ?></label>
        <div class="select2-field">
			<select name="jobs_salary_rate" class="civi-select2">
				<option value=""><?php esc_html_e('None', 'civi-framework'); ?></option>
				<option <?php if (isset($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_salary_rate'][0])) {
					if ($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_salary_rate'][0] == "hours") {
						echo 'selected';
					}
				} ?> value="hours"><?php esc_html_e('Per Hours', 'civi-framework'); ?></option>
				<option <?php if (isset($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_salary_rate'][0])) {
					if ($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_salary_rate'][0] == "days") {
						echo 'selected';
					}
				} ?> value="days"><?php esc_html_e('Per Days', 'civi-framework'); ?></option>
				<option <?php if (isset($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_salary_rate'][0])) {
					if ($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_salary_rate'][0] == "week") {
						echo 'selected';
					}
				} ?> value="week"><?php esc_html_e('Per Week', 'civi-framework'); ?></option>
				<option <?php if (isset($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_salary_rate'][0])) {
					if ($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_salary_rate'][0] == "month") {
						echo 'selected';
					}
				} ?> value="month"><?php esc_html_e('Per Month', 'civi-framework'); ?></option>
				<option <?php if (isset($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_salary_rate'][0])) {
					if ($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_salary_rate'][0] == "year") {
						echo 'selected';
					}
				} ?> value="year"><?php esc_html_e('Per Year', 'civi-framework'); ?></option>
			</select>
		</div>
    </div>
    <div class="form-group col-md-6 hidden">
        <label for="jobs_rate_convert_min"><?php esc_html_e('Maximum', 'civi-framework'); ?></label>
        <input type="number" id="jobs_rate_convert_min" name="jobs_rate_convert_min"
               value="<?php if (isset($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_maximum_price'][0])) {
                   echo $jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_maximum_price'][0];
               } ?>">
    </div>
</div>
