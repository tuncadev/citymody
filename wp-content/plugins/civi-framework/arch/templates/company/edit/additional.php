<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $company_data, $company_meta_data, $hide_company_fields;
?>
<div class="company-fields-wrap">
    <div class="company-fields company-additional row">
        <?php
        $custom_field_company = civi_render_custom_field('company');
        if (count($custom_field_company) > 0) {
            foreach ($custom_field_company as $key => $field) {
                switch ($field['type']) {
                    case 'text':
                        ?>
                        <div class="form-group col-lg-6">
                            <label><?php esc_html_e($field['title']); ?></label>
                            <input type="text" id="<?php echo esc_attr($field['id']); ?>" class="form-control"
                                   name="<?php echo esc_attr($field['id']); ?>"
                                   value="<?php if (isset($company_meta_data[$field['id']])) {
                                       echo sanitize_text_field($company_meta_data[$field['id']][0]);
                                   } ?>" placeholder="<?php esc_attr_e('Your Value', 'civi-framework'); ?>">
                        </div>
                        <?php
                        break;
                    case 'url':
                        ?>
                        <div class="form-group col-lg-6">
                            <label><?php esc_html_e($field['title']); ?></label>
                            <input type="url" id="<?php echo esc_attr($field['id']); ?>" class="form-control"
                                   name="<?php echo esc_attr($field['id']); ?>"
                                   value="<?php if (isset($company_meta_data[$field['id']])) {
                                       echo sanitize_text_field($company_meta_data[$field['id']][0]);
                                   } ?>" placeholder="<?php esc_attr_e('Your Url', 'civi-framework'); ?>">
                        </div>
                        <?php
                        break;
                    case 'textarea':
                        ?>
                        <div class="form-group col-lg-6">
                            <label><?php esc_html_e($field['title']); ?></label>
                            <textarea name="<?php echo esc_attr($field['id']); ?>" rows="3"
                                      id="<?php echo esc_attr($field['id']); ?>" class="form-control">
                            <?php if (isset($company_meta_data[$field['id']])) {
                                echo sanitize_text_field($company_meta_data[$field['id']][0]);
                            } ?>
                        </textarea>
                        </div>
                        <?php
                        break;
                    case 'select':
                        ?>
                        <div class="form-group col-lg-6">
                            <label class="d-block"><?php esc_html_e($field['title']); ?></label>
                            <div class="select2-field">
								<select name="<?php echo esc_attr($field['id']); ?>"
										id="<?php echo esc_attr($field['id']); ?>" class="form-control civi-select2">
									<?php
									foreach ($field['options'] as $opt_value) : ?>
										<option value="<?php echo esc_attr($opt_value); ?>" <?php if (isset($company_meta_data[$field['id']]) && $company_meta_data[$field['id']][0] == $opt_value) {
											echo 'selected';
										} ?>><?php esc_html_e($opt_value); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
                        </div>
                        <?php
                        break;
                    case 'checkbox_list':
                        ?>
                        <div class="form-group col-lg-6">
                            <label><?php esc_html_e($field['title']); ?></label>
                            <div class="civi-field-<?php echo esc_attr($field['id']); ?>">
                                <?php
                                $company_field = get_post_meta($company_data->ID, $field['id'], true);
                                if (empty($company_field)) {
                                    $company_field = array();
                                }
                                foreach ($field['options'] as $opt_value) :
                                    if (in_array($opt_value, $company_field)) : ?>
                                        <div class="checkbox-inline inline"><input class="custom-checkbox"
                                                                                   type="checkbox"
                                                                                   name="<?php echo esc_attr($field['id']); ?>[]"
                                                                                   value="<?php echo esc_attr($opt_value); ?>"
                                                                                   checked><?php esc_html_e($opt_value); ?>
                                        </div>
                                    <?php else : ?>
                                        <div class="checkbox-inline inline"><input class="custom-checkbox"
                                                                                   type="checkbox"
                                                                                   name="<?php echo esc_attr($field['id']); ?>[]"
                                                                                   value="<?php echo esc_attr($opt_value); ?>"><?php esc_html_e($opt_value); ?>
                                        </div>
                                    <?php endif;
                                endforeach; ?>
                            </div>
                        </div>
                        <?php
                        break;
                }
            }
        }
        ?>
    </div>
</div>
