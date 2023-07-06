<?php

/**
 * Abstract Widget Class
 */
abstract class Civi_Widget extends WP_Widget
{

	public $widget_cssclass;
	public $widget_description;
	public $widget_id;
	public $widget_name;
	public $settings;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$widget_ops = array(
			'classname'   => $this->widget_cssclass,
			'description' => $this->widget_description
		);

		parent::__construct($this->widget_id, $this->widget_name, $widget_ops);

		add_action('save_post', array($this, 'civi_widget_cache'));
		add_action('deleted_post', array($this, 'civi_widget_cache'));
		add_action('switch_theme', array($this, 'civi_widget_cache'));

		add_action('load-widgets.php', array($this, 'enqueue_resources'));
	}

	public function enqueue_resources()
	{
		// base
		wp_enqueue_style(CIVI_PLUGIN_PREFIX . 'widget', CIVI_PLUGIN_URL . '/modules/widgets/assets/css/widget.css', array(), false, 'all');
		wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'widget', CIVI_PLUGIN_URL . '/modules/widgets/assets/js/widget.js', array(), false, true);
	}

	/**
	 * get_cached_widget function.
	 */
	function get_cached_widget($args)
	{
		$cache = wp_cache_get($this->widget_id, 'widget');
		if (!is_array($cache)) {
			$cache = array();
		}

		if (isset($cache[$args['widget_id']])) {
			echo wp_kses_post($cache[$args['widget_id']]);
			return true;
		}

		return false;
	}

	/**
	 * Cache the widget
	 * @param string $content
	 */
	public function cache_widget($args, $content)
	{
		$cache = wp_cache_get($this->widget_id, 'widget');

		$cache[$args['widget_id']] = $content;

		wp_cache_set($this->widget_id, $cache, 'widget');
	}

	/**
	 * Flush the cache
	 *
	 * @return void
	 */
	public function civi_widget_cache()
	{
		wp_cache_delete($this->widget_id, 'widget');
	}

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update($new_instance, $old_instance)
	{

		$instance = $old_instance;

		if (!$this->settings) {
			return $instance;
		}

		foreach ($this->settings as $key => $setting) {

			if (isset($new_instance[$key])) {
				if (current_user_can('unfiltered_html')) {
					$instance[$key] =  $new_instance[$key];
				} else {
					$instance[$key] = stripslashes(wp_filter_post_kses(addslashes($new_instance[$key])));
				}
			} elseif ('checkbox' === $setting['type']) {
				$instance[$key] = 0;
			}
		}
		$this->civi_widget_cache();

		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @param array $instance
	 */
	public function form($instance)
	{

		if (!$this->settings) {
			return;
		}

		foreach ($this->settings as $key => $setting) {

			$std = isset($setting['std']) ? $setting['std'] : '';

			$value   = isset($instance[$key]) ? $instance[$key] : $std;

			switch ($setting['type']) {
				case "text":
?>
					<p>
						<label for="<?php echo esc_attr($this->get_field_id($key)); ?>"><?php esc_html_e($setting['label']); ?></label>
						<input class="widefat" id="<?php echo esc_attr($this->get_field_id($key)); ?>" name="<?php echo esc_attr($this->get_field_name($key)); ?>" type="text" value="<?php echo esc_attr($value); ?>" />
					</p>
				<?php
					break;

				case "number":
					$number_step = isset($setting['step']) ? $setting['step'] : '1';
					$number_min = isset($setting['min']) ? ' min="' . esc_attr($setting['min']) . '"' : '';
					$number_max = isset($setting['max']) ? ' max="' . esc_attr($setting['max']) . '"' : '';
				?>
					<p>
						<label for="<?php echo esc_attr($this->get_field_id($key)); ?>"><?php esc_html_e($setting['label']); ?></label>
						<input class="widefat" id="<?php echo esc_attr($this->get_field_id($key)); ?>" name="<?php echo esc_attr($this->get_field_name($key)); ?>" type="number" step="<?php echo esc_attr($number_step); ?>" <?php echo sprintf('%s', $number_min); ?><?php echo sprintf('%s', $number_max); ?> value="<?php echo esc_attr($value); ?>" />
					</p>
				<?php
					break;

				case "select":
				?>
					<p>
						<label for="<?php echo esc_attr($this->get_field_id($key)); ?>"><?php esc_html_e($setting['label']); ?></label>
						<select class="widefat widget-select2" id="<?php echo esc_attr($this->get_field_id($key)); ?>" name="<?php echo esc_attr($this->get_field_name($key)); ?>">
							<?php foreach ($setting['options'] as $option_key => $option_value) : ?>
								<option value="<?php echo esc_attr($option_key); ?>" <?php selected($option_key, $value); ?>><?php esc_html_e($option_value); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
				<?php
					break;

				case "select-tax":
					$types = get_terms(
						array(
							'taxonomy' => 'product_cat',
							'hide_empty' => false,
						)
					);
				?>
					<p>
						<label for="<?php echo esc_attr($this->get_field_id($key)); ?>"><?php esc_html_e($setting['label']); ?></label>
						<input name="<?php echo esc_attr($this->get_field_name($key)); ?>" type="hidden" value="<?php echo esc_attr($value); ?>" />
						<select multiple class="widefat widget-select2" id="<?php echo esc_attr($this->get_field_id($key)); ?>" data-value="<?php echo esc_attr($value) ?>">
							<?php foreach ($types as $type) { ?>
								<option value="<?php echo esc_attr($type->term_id); ?>"><?php esc_html_e($type->name); ?></option>
							<?php } ?>
						</select>
					</p>
				<?php
					break;

				case "checkbox":
				?>
					<p>
						<input id="<?php echo esc_attr($this->get_field_id($key)); ?>" name="<?php echo esc_attr($this->get_field_name($key)); ?>" type="checkbox" value="1" <?php checked($value, 1); ?> />
						<label for="<?php echo esc_attr($this->get_field_id($key)); ?>"><?php esc_html_e($setting['label']); ?></label>
					</p>
				<?php
					break;

				case "multi-select":
				?>
					<p>
						<label for="<?php echo esc_attr($this->get_field_id($key)); ?>"><?php esc_html_e($setting['label']); ?></label>
						<input name="<?php echo esc_attr($this->get_field_name($key)); ?>" type="hidden" value="<?php echo esc_attr($value); ?>" />
						<select multiple="multiple" class="widefat civi-select2" id="<?php echo esc_attr($this->get_field_id($key)); ?>" data-value="<?php echo esc_attr($value) ?>">
							<?php foreach ($setting['options'] as $option_key => $option_value) : ?>
								<option value="<?php echo esc_attr($option_key); ?>"><?php esc_html_e($option_value); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
				<?php
					break;

				case "icon":
				?>
					<div>
						<label for="<?php echo esc_attr($this->get_field_id($key)); ?>"><?php esc_html_e($setting['label']); ?> </label>
						<div>
							<input style="width: 145px" type="text" class="input-icon" id="<?php echo esc_attr($this->get_field_id($key)); ?>" name="<?php echo esc_attr($this->get_field_name($key)); ?>" value="<?php echo esc_attr($value); ?>">
							<input style="float: right" title="<?php echo esc_html__('Click to browse icon', 'civi-framework') ?>" class="browse-icon button-secondary" type="button" value="<?php echo esc_html__('Browse...', 'civi-framework') ?>" />
							<span style="vertical-align: top;width: 30px; height: 30px" class="icon-preview"><i class="fa <?php echo esc_attr($value); ?>"></i></span>
						</div>
					</div>
				<?php
					break;

				case "image":
				?>
					<div style="margin: 13px 0">
						<label for="<?php echo esc_attr($this->get_field_id($key)); ?>"><?php esc_html_e($setting['label']); ?> </label>
						<div class="widget-image-field">
							<div class="show-image" style="text-align: center;margin-top: 10px">
								<img class="entry-image" style="max-width: 100%" src="<?php echo esc_url($value); ?>" alt="Logo">
							</div>
							<input type="text" class="input-icon is_none" id="<?php echo esc_attr($this->get_field_id($key)); ?>" name="<?php echo esc_attr($this->get_field_name($key)); ?>" value="<?php echo esc_attr($value); ?>">
							<button style="margin-top: 10px" title="<?php echo esc_html__('Click to browse image', 'civi-framework') ?>" class="browse-image button-secondary" type="button"><?php echo esc_html__('Upload image', 'civi-framework') ?></button>
						</div>
					</div>
					<script type="text/javascript">
						jQuery(document).ready(function() {
							jQuery('.browse-image').on('click', function() {
								var parent = jQuery(this).parent();
								wp.media.editor.send.attachment = function(props, attachment, $parent = parent) {
									$parent.find('.input-icon').val(attachment.url).trigger('change');
									$parent.find('.show-image .entry-image').attr('src', attachment.url);
									$parent.find('.show-image').show();
								}
								wp.media.editor.open(this);
								return false;
							});
						});
					</script>

				<?php
					break;
				case "text-area":
				?>
					<p>
						<label for="<?php echo esc_attr($this->get_field_id($key)); ?>"><?php esc_html_e($setting['label']); ?></label>
						<textarea class="widefat" rows="8" cols="40" id="<?php echo esc_attr($this->get_field_id($key)); ?>" name="<?php echo esc_attr($this->get_field_name($key)); ?>"><?php echo esc_textarea($value); ?></textarea>
					</p>
<?php
					break;
			}
		}
	}
}
