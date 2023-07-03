<?php 

/**
 * GET Plugin template
 * *******************************************************
 */
function glf_get_template($slug, $args = array())
{
	if ($args && is_array($args)) {
		extract($args);
	}
	$located = GLF_BASE_DIR . $slug . '.php';
	if ( !file_exists($located) ) {
		_doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $slug), '1.0');

		return;
	}
	include($located);
}

/**
 * Get GLOBAL term meta config
 * *******************************************************
 */
if (!function_exists('glf_get_term_meta_config')) {
	function &glf_get_term_meta_config() {
		if (!isset($GLOBALS['glf_register_term_meta'])) {
			$GLOBALS['glf_register_term_meta'] = apply_filters('glf_register_term_meta', array());
		}
		return $GLOBALS['glf_register_term_meta'];
	}
}

/**
 * Set GLOBAL Config Layout
 * *******************************************************
 */
if (!function_exists('glf_set_config_layout')) {
	function glf_set_config_layout($value)
	{
		$GLOBALS['glf_config_layout'] = $value;
	}
}

/**
 * Set GLOBAL Config Layout
 * *******************************************************
 */
if (!function_exists('glf_set_config_type')) {
	function glf_set_config_type($value)
	{
		$GLOBALS['glf_config_type'] = $value;
	}
}

/**
 * Get GLOBAL Config Layout
 * *******************************************************
 */
if (!function_exists('glf_get_config_type')) {
	function glf_get_config_type()
	{
		return isset($GLOBALS['glf_config_type']) ? $GLOBALS['glf_config_type'] : 'meta_box';
	}
}

/**
 * Get GLOBAL Config Layout
 * *******************************************************
 */
if (!function_exists('glf_get_config_layout')) {
	function glf_get_config_layout()
	{
		return isset($GLOBALS['glf_config_layout']) ? $GLOBALS['glf_config_layout'] : '';
	}
}

/**
 * Get GLOBAL meta box config
 * Change meta box config by filter: glf_meta_box_config
 * *******************************************************
 */
if (!function_exists('glf_get_meta_boxes_config')) {
	function &glf_get_meta_boxes_config() {
		if (!isset($GLOBALS['glf_meta_box_config'])) {
			$GLOBALS['glf_meta_box_config'] = apply_filters('glf_meta_box_config', array());
		}
		return $GLOBALS['glf_meta_box_config'];
	}
}

/**
 * Get GLOBAL options config
 * Change options config by filter: glf_option_config
 *
 * @since   1.0
 * @return  array
 */
if (!function_exists('glf_get_options_config')) {
	function &glf_get_options_config($page = '') {
		if (!isset($GLOBALS['glf_option_config'])) {
			$GLOBALS['glf_option_config'] = apply_filters('glf_option_config', array());
		}
		if ($page === '') {
			return $GLOBALS['glf_option_config'];
		}
		if (isset($GLOBALS['glf_option_config'][$page])) {
			return $GLOBALS['glf_option_config'][$page];
		}
		return array();

	}
}

/**
 * Determine whether we are in add New page/post/CPT or in edit page/post/CPT
 * *******************************************************
 */
if (!function_exists('glf_is_edit_page')) {
	function glf_is_edit_page($new_edit = null)
	{
		global $pagenow;
		//make sure we are on the backend
		if (!is_admin()) return false;


		if ($new_edit == "edit")
			return in_array($pagenow, array('post.php',));
		elseif ($new_edit == "new") //check for new post page
			return in_array($pagenow, array('post-new.php'));
		else //check for either new or edit
			return in_array($pagenow, array('post.php', 'post-new.php'));
	}
}

/**
 * Echo selected attribute in select field
 * *******************************************************
 */
if (!function_exists('glf_the_selected')) {
	function glf_the_selected($value, $current)
	{
		echo ((is_array($current) && in_array($value, $current)) || (!is_array($current) && ($value == $current))) ? 'selected' : '';
	}
}

/**
 * Get Field class name
 * *******************************************************
 */
if (!function_exists('glf_get_field_class_name')) {
	function glf_get_field_class_name($field_type)
	{
		$type = str_replace('_', ' ', $field_type);
		$type = ucwords($type);
		$type = str_replace(' ', '_', $type);

		return 'GLF_Field_' . $type;
	}
}

/**
 * Get Attachment ID from url
 * *******************************************************
 */
if (!function_exists('glf_get_attachment_id')) {
	function glf_get_attachment_id($url)
	{
		global $wpdb;
		$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $url));
		if (!empty($attachment)) {
			return $attachment[0];
		}

		return 0;
	}
}

/**
 * Get list posts
 * *******************************************************
 */
if (!function_exists('glf_get_posts')) {
	function glf_get_posts($params = array())
	{
		$args = array(
			'numberposts' => 20,
			'orderby' => 'post_title',
			'order'   => 'ASC',
		);
		if (!empty($params)) {
			$args = array_merge($args, $params);
		}
		$posts = get_posts($args);
		$ret_posts = array();
		foreach ($posts as $post) {
			$ret_posts[$post->ID] = $post->post_title;
		}

		return $ret_posts;
	}
}

/**
 * Get options config keys
 * *******************************************************
 */
if (!function_exists('glf_get_option_config_keys')) {
	function glf_get_option_config_keys($configs) {
		$field_keys = array();
		if (isset($configs['section'])) {
			foreach ($configs['section'] as $tabs) {
				if (isset($tabs['fields'])) {
					$field_keys = array_merge($field_keys, glf_get_config_field_keys($tabs['fields'], '', $tabs['id']));
				}
			}
		} else {

			if (isset($configs['fields'])) {
				$field_keys = array_merge($field_keys, glf_get_config_field_keys($configs['fields'], '', ''));
			}
		}
		return $field_keys;
	}
}

/**
 * Get config field keys
 * *******************************************************
 */
if (!function_exists('glf_get_config_field_keys')) {
	function glf_get_config_field_keys($fields, $parent_type = '', $section = '')
	{
		$field_keys = array();
		foreach ($fields as $field) {
			if (!isset($field['type'])) {
				continue;
			}

			switch ($field['type']) {
				case 'repeater':
					if (!isset($field['id'])) {
						break;
					}
					if (($parent_type === 'repeater') || !isset($field['fields'])) {
						break;
					}
					$field_keys[$field['id']] = array(
						'type' => $field['type'],
						'clone' => false,
						'section' => $section,
						'default' => isset($field['default']) ? $field['default'] : '',
					);
					$field_keys = array_merge($field_keys, glf_get_config_field_keys($field['fields'], $field['type'], $section));
					break;
				case 'row':
				case 'group':
					if (($parent_type === 'repeater') || !isset($field['fields'])) {
						break;
					}
					$field_keys = array_merge($field_keys, glf_get_config_field_keys($field['fields'], $field['type'], $section));
					break;
				default:
					if (!isset($field['id'])) {
						break;
					}
					$class_field = glf_get_field_class_name($field['type']);
					$field_obj = new $class_field($field, $parent_type);

					$field_keys[$field['id']] = array(
						'type' => $field['type'],
						'clone' => (isset($field['clone']) && $field['clone']) || ($parent_type === 'repeater'),
						'section' => $section,
						'default' => $field_obj->get_default(),
					);
					break;
			}
		}

		return $field_keys;
	}
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * *******************************************************
 */
if (!function_exists('glf_clean')) {
    function glf_clean( $var ) {
        if ( is_array( $var ) ) {
            return array_map( 'glf_clean', $var );
        } else {
            return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
        }
    }
}