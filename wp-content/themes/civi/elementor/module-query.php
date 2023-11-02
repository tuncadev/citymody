<?php

namespace Civi_Elementor;

defined('ABSPATH') || exit;

class Module_Query_Base
{

	protected $post_type;
	protected $query_args;
	protected $widget_settings;

	private static $_instance = null;

	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**\
	 * @param string $control_name
	 *
	 * @return mixed|null
	 */
	private function get_widget_settings($control_name)
	{
		return isset($this->widget_settings[$control_name]) ? $this->widget_settings[$control_name] : null;
	}

	public function get_query(array $settings, $post_type = 'post')
	{
		$this->widget_settings = $settings;
		$this->post_type       = $post_type;
		$this->build_query_args($settings);

		if ('current_query' === $settings['query_source']) {
			global $wp_query;
			$query = $wp_query;
		} else {
			$query_args = $this->get_query_args();

			$query = new \WP_Query($query_args);
		}

		return $query;
	}

	public function get_query_args()
	{
		return $this->query_args;
	}

	protected function build_query_args($settings)
	{
		if ('current_query' === $settings['query_source']) {
			global $wp_query;

			$this->query_args = $wp_query->query_vars;

			$this->query_args['nopaging']    = false;
			$this->query_args['post_status'] = 'publish';

			$this->query_args['paged'] = get_query_var('paged') ? get_query_var('paged') : 1;
		} else {
			$number = !empty($settings['query_number']) ? $settings['query_number'] : get_option('posts_per_page');

			$this->query_args = array(
				'post_type'      => $this->post_type,
				'posts_per_page' => $number,
				'post_status'    => 'publish',
			);

			$this->set_sort_args();

			$this->set_terms_args();

			$this->set_author_args();

			if (get_query_var('paged')) {
				$this->query_args['paged'] = get_query_var('paged');
			} elseif (get_query_var('page')) {
				$this->query_args['paged'] = get_query_var('page');
			} else {
				$this->query_args['paged'] = 1;
			}
		}
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 */
	protected function set_query_arg($key, $value)
	{
		if (!isset($this->query_args[$key])) {
			$this->query_args[$key] = $value;
		}
	}

	protected function build_terms_query($control_id, $exclude = false)
	{
		$settings_terms = $this->get_widget_settings($control_id);

		if (empty($settings_terms)) {
			return;
		}

		$terms = [];

		// Switch to term_id in order to get all term children (sub-categories):
		foreach ($settings_terms as $id) {
			$term_data = get_term_by('term_taxonomy_id', $id);

			if (false !== $term_data) {
				$taxonomy             = $term_data->taxonomy;
				$terms[$taxonomy][] = $term_data->slug;
			}
		}

		$this->insert_tax_query($terms, $exclude);
	}

	protected function insert_tax_query($terms, $exclude)
	{
		$tax_query = [];
		foreach ($terms as $taxonomy => $ids) {
			$query = [
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => $ids,
			];

			if ($exclude) {
				$query['operator'] = 'NOT IN';
			}

			$tax_query[] = $query;
		}

		if (empty($tax_query)) {
			return;
		}

		if (empty($this->query_args['tax_query'])) {
			$this->query_args['tax_query'] = $tax_query;
		} else {
			$this->query_args['tax_query']['relation'] = 'AND';
			$this->query_args['tax_query'][]           = $tax_query;
		}
	}

	protected function set_terms_args()
	{
		$this->build_terms_query('query_include_term_ids');
		$this->build_terms_query('query_exclude_term_ids', true);
	}

	protected function set_author_args()
	{
		$include_authors = $this->get_widget_settings('query_include_authors');
		if (!empty($include_authors)) {
			$this->set_query_arg('author__in', $include_authors);
		}

		$exclude_authors = $this->get_widget_settings('query_exclude_authors');
		if (!empty($exclude_authors)) {
			//exclude only if not explicitly included
			if (empty($this->query_args['author__in'])) {
				$this->set_query_arg('author__not_in', $exclude_authors);
			}
		}
	}

	protected function set_sort_args()
	{
		$orderby = !empty($this->widget_settings['query_orderby']) ? $this->widget_settings['query_orderby'] : 'date';
		$order   = !empty($this->widget_settings['query_order']) ? $this->widget_settings['query_order'] : 'DESC';

		switch ($this->widget_settings['query_orderby']) {
			case 'meta_value':
			case 'meta_value_num':
				$this->query_args['meta_key'] = $this->widget_settings['query_sort_meta_key'];
				$this->query_args['orderby']  = $orderby;
				$this->query_args['order']    = $order;
				break;
			case 'woo_best_selling': // Woocommerce best selling items.
				$this->query_args['meta_key'] = 'total_sales';
				$this->query_args['orderby']  = 'meta_value_num';
				$this->query_args['order']    = 'DESC';
				break;
			case 'woo_featured': // Woocommerce featured items.
				$this->query_args['tax_query'][] = [
					'taxonomy'         => 'product_visibility',
					'terms'            => 'featured',
					'field'            => 'name',
					'operator'         => 'IN',
					'include_children' => false,
				];
				break;
			case 'woo_on_sale': // Woocommerce best selling items.
				$this->query_args['post__in'] = array_merge(array(0), wc_get_product_ids_on_sale());
				break;
			case 'woo_top_rated': // Woocommerce top rated items.
				$this->query_args['meta_key'] = '_wc_average_rating';
				$this->query_args['orderby']  = 'meta_value_num';
				$this->query_args['order']    = 'DESC';
				break;
			default:
				$this->query_args['orderby'] = $orderby;
				$this->query_args['order']   = $order;
				break;
		}
	}
}
