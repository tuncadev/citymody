<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('GLF_Field_Selectize')) {
    class GLF_Field_Selectize extends GLF_Field
    {
        public function enqueue()
        {
            wp_enqueue_style('selectize', GLF_BASE_URL . 'assets/libs/selectize/css/selectize.css', array(), '0.12.3');
            wp_enqueue_style('selectize_default', GLF_BASE_URL . 'assets/libs/selectize/css/selectize.default.css', array(), '0.12.3');
            wp_enqueue_script('selectize', GLF_BASE_URL . 'assets/libs/selectize/js/selectize.js', array(), '0.12.3', true);
            wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'selectize', GLF_BASE_URL . 'fields/selectize/assets/selectize.js', array(), GLF_VER, true);
        }

        function render_content($content_args = '')
        {
            $create_link = '';
            if (isset($this->params['data'])) {
                switch ($this->params['data']) {
                    case 'sidebar':
                        $this->params['options'] = glf_get_sidebars();
                        $create_link = admin_url('widgets.php');
                        break;
                    case 'menu':
                        $this->params['options'] = glf_get_menus();
                        break;
                    case 'taxonomy':
                        $this->params['options'] = glf_get_taxonomies(isset($this->params['data_args']) ? $this->params['data_args'] : array());
                        break;
                    default:
                        if (isset($this->params['data_args']) && !isset($this->params['data_args']['post_type'])) {
                            $this->params['data_args']['post_type'] = $this->params['data'];
                        }
                        $this->params['options'] = glf_get_posts(isset($this->params['data_args']) ? $this->params['data_args'] : array('post_type' => $this->params['data']));
                        break;
                }
            }

            if (!isset($this->params['options']) || !is_array($this->params['options'])) {
                $this->params['options'] = array();
            }
            $field_value = $this->get_value();
            $is_deselect = isset($this->params['allow_clear']) ? $this->params['allow_clear'] : false;
            $multiple = isset($this->params['multiple']) ? $this->params['multiple'] : false;
            $is_edit_link = isset($this->params['edit_link']) ? $this->params['edit_link'] : false;
?>
            <div class="glf-field-selectize-inner">
                <?php if (isset($this->params['tags']) && $this->params['tags']) : ?>
                    <input data-field-control="" type="text" name="<?php echo esc_attr($this->get_name()) ?>" class="glf-selectize" value="<?php echo esc_attr($field_value); ?>" data-tags="true" <?php if (isset($this->params['drag']) && $this->params['drag']) : ?> data-drag="true" <?php endif; ?> />
                <?php else : ?>
                    <select data-field-control="" <?php if ($multiple) : ?> multiple="multiple" name="<?php echo esc_attr($this->get_name()) ?>[]" <?php else : ?> name="<?php echo esc_attr($this->get_name()) ?>" <?php endif; ?> <?php if ($is_deselect) : ?> data-allow-clear="true" <?php endif; ?> <?php if (isset($this->params['placeholder'])) : ?> data-placeholder="<?php echo esc_attr($this->params['placeholder']) ?>" <?php endif; ?> data-value='<?php echo esc_attr(is_array($field_value) ? json_encode($field_value) : $field_value) ?>' <?php if (isset($this->params['drag']) && $this->params['drag']) : ?> data-drag="true" <?php endif; ?> class="glf-selectize">
                        <?php foreach ($this->params['options'] as $key => $value) : ?>
                            <?php if (is_array($value)) : ?>
                                <optgroup label="<?php echo esc_attr($key); ?>">
                                    <?php foreach ($value as $opt_key => $opt_value) : ?>
                                        <option value="<?php echo esc_attr($opt_key); ?>"><?php esc_html_e($opt_value); ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php else :; ?>
                                <option value="<?php echo esc_attr($key); ?>"><?php esc_html_e($value); ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($this->params['options']) && is_array($this->params['options']) && (sizeof($this->params['options']) > 0)) : ?>
                        <?php if ($is_edit_link) : ?>
                            <a target="_blank" href="#" class="glf-selectize-edit-link button button-primary" data-link="<?php echo admin_url('post.php'); ?>"><?php esc_html_e('Edit', 'civi-framework') ?></a>
                        <?php endif; ?>
                        <?php if (!empty($create_link)) : ?>
                            <a class="glf-selectize-create-link button button-primary" href="<?php echo esc_attr($create_link) ?>" target="_blank" title="<?php esc_attr_e('Add New', 'civi-framework') ?>"><?php esc_html_e('Add New', 'civi-framework'); ?></a>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
<?php
        }

        /**
         * Get default value
         *
         * @return array | string
         */
        function get_default()
        {
            $default = '';
            if (isset($this->params['multiple']) && $this->params['multiple']) {
                $default = array();
            }
            $field_default = isset($this->params['default']) ? $this->params['default'] : $default;
            return $this->is_clone() ? array($field_default) : $field_default;
        }
    }
}
