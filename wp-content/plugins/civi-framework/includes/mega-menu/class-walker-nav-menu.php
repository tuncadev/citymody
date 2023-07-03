<?php

class Civi_Walker_Nav_Menu extends Walker_Nav_Menu
{

	private $mega_menu = false;

	public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output)
	{
		$id_field = $this->db_fields['id'];
		if (is_object($args[0])) {
			$args[0]->has_children = !empty($children_elements[$element->$id_field]);
		}

		return parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
	}

	public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
	{
		$indent          = '';
		$this->mega_menu = false;

		if ($depth) {
			$indent = str_repeat("\t", $depth);
		}

		$classes = array();
		if (!empty($item->classes)) {
			$classes = (array) $item->classes;
		}

		$classes[] = 'menu-item-' . $item->ID;

		$post_args = array(
			'post_type'   => 'nav_menu_item',
			'nopaging'    => true,
			'numberposts' => 1,
			'meta_key'    => '_menu_item_menu_item_parent',
			'meta_value'  => $item->ID,
		);

		if ($item->menu_item_parent === '0') {
			$classes[] = 'level-1';
		}

		$children = get_posts($post_args);

		foreach ($children as $child) {
			$obj = get_post_meta($child->ID, '_menu_item_object');

			if ($obj[0] === 'civi_mega_menu') {
				$classes[]       = apply_filters('civi_mega_menu_css_class', 'has-mega-menu', $item, $args, $depth);
				$this->mega_menu = true;
			}
		}

		$class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
		$class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

		$id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth);
		$id = $id ? ' id="' . esc_attr($id) . '"' : '';

		$output .= $indent . '<li' . $id . $class_names . '>';

		$atts           = array();
		$atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
		$atts['target'] = !empty($item->target) ? $item->target : '';
		$atts['rel']    = !empty($item->xfn) ? $item->xfn : '';
		$atts['href']   = !empty($item->url) ? $item->url : '';

		$atts       = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);
		$attributes = '';
		foreach ($atts as $attr => $value) {
			if (!empty($value)) {
				if ($attr === 'href') {
					$value = esc_url($value);
				} else {
					$value = esc_attr($value);
				}
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$item_output = $args->before;
		$item_output .= '<a' . $attributes . '><div class="menu-item-wrap">';
		$item_output .= '<span class="menu-item-title">' . $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after . '</span>';

		if ($args->has_children) {
			$item_output .= '<span class="toggle-sub-menu"> </span>';
		}

		$item_output .= '</div></a>';
		$item_output .= $args->after;

		if ('civi_mega_menu' === $item->object && defined('ELEMENTOR_VERSION')) {
			$mega_menu_content_class = apply_filters('civi_mega_menu_content_css_class', 'mega-menu-content', $item, $args, $depth);
			$output .= '<div class="' . esc_attr($mega_menu_content_class) . '"><div class="container"><div class="row"><div class="col-xs-12">' . \Elementor\Plugin::$instance->frontend->get_builder_content($item->object_id) . '</div></div></div></div>';
		} else {
			$output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
		}
	}

	function start_lvl(&$output, $depth = 0, $args = array())
	{
		$class = 'sub-menu children';

		if ($this->mega_menu) {
			$class .= ' mega-menu';
		} else {
			$class .= ' simple-menu';
		}

		$indent = str_repeat("\t", $depth);
		$output .= $indent . '<ul class="' . $class . '">';
	}
}
