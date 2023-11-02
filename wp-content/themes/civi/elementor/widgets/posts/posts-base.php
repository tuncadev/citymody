<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
//use ElementorPro\Modules\QueryControl\Module as Module_Query;

defined('ABSPATH') || exit;

abstract class Posts_Base extends Base
{

	/**
	 * @var \WP_Query
	 */
	private $_query      = null;
	private $_query_args = null;

	abstract protected function get_post_type();

	abstract protected function get_post_category();

	public function query_posts()
	{
		$settings          = $this->get_settings_for_display();
		$post_type         = $this->get_post_type();
		$this->_query      = Module_Query_Base::instance()->get_query($settings, $post_type);
		$this->_query_args = Module_Query_Base::instance()->get_query_args();
	}

	protected function get_query()
	{
		return $this->_query;
	}

	protected function get_query_args()
	{
		return $this->_query_args;
	}

	protected function register_controls()
	{
		$this->register_query_section();
	}

	// protected function get_query_author_object() {
	// 	return Module_Query::QUERY_OBJECT_AUTHOR;
	// }

	protected function get_query_orderby_options()
	{
		$options = [
			'date'           => esc_html__('Date', 'civi'),
			'ID'             => esc_html__('Post ID', 'civi'),
			'author'         => esc_html__('Author', 'civi'),
			'title'          => esc_html__('Title', 'civi'),
			'modified'       => esc_html__('Last modified date', 'civi'),
			'parent'         => esc_html__('Post/page parent ID', 'civi'),
			'comment_count'  => esc_html__('Number of comments', 'civi'),
			'menu_order'     => esc_html__('Menu order/Page Order', 'civi'),
			'meta_value'     => esc_html__('Meta value', 'civi'),
			'meta_value_num' => esc_html__('Meta value number', 'civi'),
			'rand'           => esc_html__('Random order', 'civi'),
		];

		return $options;
	}

	protected function register_query_section()
	{
		$this->start_controls_section('query_section', [
			'label' => esc_html__('Query', 'civi'),
		]);

		$this->add_control('query_source', [
			'label'   => esc_html__('Source', 'civi'),
			'type'    => Controls_Manager::SELECT,
			'options' => array(
				'custom_query'  => esc_html__('Custom Query', 'civi'),
				'current_query' => esc_html__('Current Query', 'civi'),
			),
			'default' => 'custom_query',
		]);

		$this->start_controls_tabs('query_args_tabs', [
			'condition' => [
				'query_source!' => ['current_query'],
			],
		]);

		$this->start_controls_tab('query_include_tab', [
			'label' => esc_html__('Include', 'civi'),
		]);

		$this->add_control('query_include', [
			'label'       => esc_html__('Include By', 'civi'),
			'label_block' => true,
			'type'        => Controls_Manager::SELECT2,
			'multiple'    => true,
			'options'     => [
				'terms'   => esc_html__('Term', 'civi'),
				'authors' => esc_html__('Author', 'civi'),
			],
			'condition'   => [
				'query_source!' => ['current_query'],
			],
		]);

		// $this->add_control( 'query_include_term_ids', [
		// 	'type'         => Module_Query::QUERY_CONTROL_ID,
		// 	'options'      => [],
		// 	'label_block'  => true,
		// 	'multiple'     => true,
		// 	'autocomplete' => [
		// 		'object'  => Module_Query::QUERY_OBJECT_CPT_TAX,
		// 		'display' => 'detailed',
		// 		'query'   => [
		// 			'post_type' => $this->get_post_type(),
		// 		],
		// 	],
		// 	'condition'    => [
		// 		'query_include' => 'terms',
		// 		'query_source!' => [ 'current_query' ],
		// 	],
		// ] );

		// $this->add_control( 'query_include_authors', [
		// 	'label'        => esc_html__( 'Author', 'civi' ),
		// 	'label_block'  => true,
		// 	'type'         => Module_Query::QUERY_CONTROL_ID,
		// 	'multiple'     => true,
		// 	'default'      => [],
		// 	'options'      => [],
		// 	'autocomplete' => [
		// 		'object' => $this->get_query_author_object(),
		// 	],
		// 	'condition'    => [
		// 		'query_include' => 'authors',
		// 		'query_source!' => [ 'current_query' ],
		// 	],
		// ] );

		$this->end_controls_tab();

		$this->start_controls_tab('query_exclude_tab', [
			'label' => esc_html__('Exclude', 'civi'),
		]);

		$this->add_control('query_exclude', [
			'label'       => esc_html__('Exclude By', 'civi'),
			'label_block' => true,
			'type'        => Controls_Manager::SELECT2,
			'multiple'    => true,
			'options'     => [
				'terms'   => esc_html__('Term', 'civi'),
				'authors' => esc_html__('Author', 'civi'),
			],
			'condition'   => [
				'query_source!' => ['current_query'],
			],
		]);

		// $this->add_control( 'query_exclude_term_ids', [
		// 	'type'         => Module_Query::QUERY_CONTROL_ID,
		// 	'options'      => [],
		// 	'label_block'  => true,
		// 	'multiple'     => true,
		// 	'autocomplete' => [
		// 		'object'  => Module_Query::QUERY_OBJECT_CPT_TAX,
		// 		'display' => 'detailed',
		// 		'query'   => [
		// 			'post_type' => $this->get_post_type(),
		// 		],
		// 	],
		// 	'condition'    => [
		// 		'query_exclude' => 'terms',
		// 		'query_source!' => [ 'current_query' ],
		// 	],
		// ] );

		// $this->add_control( 'query_exclude_authors', [
		// 	'label'        => esc_html__( 'Author', 'civi' ),
		// 	'label_block'  => true,
		// 	'type'         => Module_Query::QUERY_CONTROL_ID,
		// 	'multiple'     => true,
		// 	'default'      => [],
		// 	'options'      => [],
		// 	'autocomplete' => [
		// 		'object' => $this->get_query_author_object(),
		// 	],
		// 	'condition'    => [
		// 		'query_exclude' => 'authors',
		// 		'query_source!' => [ 'current_query' ],
		// 	],
		// ] );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control('query_number', [
			'label'       => esc_html__('Items per page', 'civi'),
			'description' => esc_html__('Number of items to show per page. Input "-1" to show all posts. Leave blank to use global setting.', 'civi'),
			'type'        => Controls_Manager::NUMBER,
			'min'         => -1,
			'max'         => 100,
			'step'        => 1,
			'condition'   => [
				'query_source!' => ['current_query'],
			],
			'separator'   => 'before',
		]);

		$this->add_control('query_orderby', [
			'label'       => esc_html__('Order by', 'civi'),
			'description' => esc_html__('Select order type. If "Meta value" or "Meta value Number" is chosen then meta key is required.', 'civi'),
			'type'        => Controls_Manager::SELECT,
			'options'     => $this->get_query_orderby_options(),
			'default'     => 'date',
			'condition'   => [
				'query_source!' => ['current_query'],
			],
		]);

		$this->add_control('query_sort_meta_key', [
			'label'     => esc_html__('Meta key', 'civi'),
			'type'      => Controls_Manager::TEXT,
			'condition' => [
				'query_orderby' => [
					'meta_value',
					'meta_value_num',
				],
				'query_source!' => ['current_query'],
			],
		]);

		$this->add_control('query_order', [
			'label'     => esc_html__('Sort order', 'civi'),
			'type'      => Controls_Manager::SELECT,
			'options'   => array(
				'DESC' => esc_html__('Descending', 'civi'),
				'ASC'  => esc_html__('Ascending', 'civi'),
			),
			'default'   => 'DESC',
			'condition' => [
				'query_source!' => ['current_query'],
			],
		]);

		$this->end_controls_section();
	}

	protected function add_pagination_section()
	{
		$this->start_controls_section('pagination_section', [
			'label' => esc_html__('Pagination', 'civi'),
		]);

		$this->add_control('pagination_type', [
			'label'   => esc_html__('Pagination', 'civi'),
			'type'    => Controls_Manager::SELECT,
			'options' => array(
				''              => esc_html__('None', 'civi'),
				'numbers'       => esc_html__('Numbers', 'civi'),
				'navigation'    => esc_html__('Navigation', 'civi'),
				'load-more'     => esc_html__('Button', 'civi'),
				'load-more-alt' => esc_html__('Custom Button', 'civi'),
				'infinite'      => esc_html__('Infinite Scroll', 'civi'),
			),
			'default' => '',
		]);

		$this->add_control('pagination_custom_button_id', [
			'label'       => esc_html__('Custom Button ID', 'civi'),
			'description' => esc_html__('Input id of custom button to load more posts when click. For e.g: #product-load-more-btn', 'civi'),
			'type'        => Controls_Manager::TEXT,
			'condition'   => [
				'pagination_type' => 'load-more-alt',
			],
		]);

		$this->end_controls_section();
	}

	protected function add_pagination_style_section()
	{
		$this->start_controls_section('pagination_style_section', [
			'label'     => esc_html__('Pagination', 'civi'),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [
				'pagination_type!' => '',
			],
		]);

		$this->add_responsive_control('pagination_alignment', [
			'label'     => esc_html__('Alignment', 'civi'),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => Widget_Utils::get_control_options_horizontal_alignment(),
			'default'   => 'center',
			'selectors' => [
				'{{WRAPPER}} .civi-grid-pagination' => 'text-align: {{VALUE}};',
			],
		]);

		$this->add_responsive_control('pagination_spacing', [
			'label'       => esc_html__('Spacing', 'civi'),
			'type'        => Controls_Manager::SLIDER,
			'placeholder' => '70',
			'range'       => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'selectors'   => [
				'{{WRAPPER}} .civi-grid-pagination' => 'padding-top: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'      => 'pagination_typography',
			'selector'  => '{{WRAPPER}} .nav-link',
			'condition' => [
				'pagination_type' => 'navigation',
			],
		]);

		$this->start_controls_tabs('pagination_style_tabs');

		$this->start_controls_tab('pagination_style_normal_tab', [
			'label' => esc_html__('Normal', 'civi'),
		]);

		$this->add_control('pagination_link_color', [
			'label'     => esc_html__('Link Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .navigation-buttons' => 'color: {{VALUE}};',
				'{{WRAPPER}} .page-pagination'    => 'color: {{VALUE}};',
			],
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('pagination_style_hover_tab', [
			'label' => esc_html__('Hover', 'civi'),
		]);

		$this->add_control('pagination_link_hover_color', [
			'label'     => esc_html__('Link Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .nav-link:hover'           => 'color: {{VALUE}};',
				'{{WRAPPER}} .page-pagination .current' => 'color: {{VALUE}};',
				'{{WRAPPER}} .page-pagination a:hover'  => 'color: {{VALUE}};',
			],
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control('pagination_loading_heading', [
			'label'     => esc_html__('Loading Icon', 'civi'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_control('pagination_loading_color', [
			'label'     => esc_html__('Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .civi-infinite-loader .sk-wrap' => 'color: {{VALUE}};',
			],
			'condition' => [
				'pagination_type!' => 'numbers',
			],
		]);

		$this->end_controls_section();
	}

	protected function add_filter_section()
	{
		$this->start_controls_section('filter_section', [
			'label' => esc_html__('Filter', 'civi'),
		]);

		$this->add_control('filter_enable', [
			'label' => esc_html__('Show Filter', 'civi'),
			'type'  => Controls_Manager::SWITCHER,
		]);

		$this->add_control('filter_style', [
			'label'   => esc_html__('Style', 'civi'),
			'type'    => Controls_Manager::SELECT,
			'options' => array(
				'01' => '01',
			),
			'default' => '01',
		]);

		$this->add_control('filter_counter', [
			'label'        => esc_html__('Show Counter', 'civi'),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => '1',
			'condition'    => [
				'filter_style!' => '',
			],
		]);

		$this->add_control('filter_in_grid', [
			'label'        => esc_html__('In Grid', 'civi'),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => '1',
			'condition'    => [
				'filter_style!' => '',
			],
		]);

		$this->end_controls_section();
	}

	protected function add_filter_style_section()
	{
		$this->start_controls_section('filter_style_section', [
			'label'     => esc_html__('Filter', 'civi'),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [
				'filter_enable' => 'yes',
			],
		]);

		$this->add_responsive_control('filter_spacing', [
			'label'      => esc_html__('Spacing', 'civi'),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => ['px'],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .civi-grid-filter' => 'padding-bottom: {{SIZE}}{{UNIT}}',
			],
		]);

		$this->add_responsive_control('filter_alignment', [
			'label'     => esc_html__('Alignment', 'civi'),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => Widget_Utils::get_control_options_horizontal_alignment(),
			'default'   => 'center',
			'selectors' => [
				'{{WRAPPER}} .civi-grid-filter' => 'text-align: {{VALUE}};',
			],
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'filter_link_typography',
			'label'    => esc_html__('Link Typography', 'civi'),
			'selector' => '{{WRAPPER}} .btn-filter .filter-text',
		]);

		$this->start_controls_tabs('filter_link_tabs');

		$this->start_controls_tab('filter_link_normal', [
			'label' => esc_html__('Normal', 'civi'),
		]);

		$this->add_control('filter_link_color', [
			'label'     => esc_html__('Link Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .btn-filter' => 'color: {{VALUE}};',
			],
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('filter_link_hover', [
			'label' => esc_html__('Hover', 'civi'),
		]);

		$this->add_control('filter_link_hover_color', [
			'label'     => esc_html__('Link Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .btn-filter.current, {{WRAPPER}} .btn-filter:hover' => 'color: {{VALUE}};',
			],
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control('filter_counter_style_heading', [
			'label'     => esc_html__('Filter Counter', 'civi'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => [
				'filter_counter' => '1',
			],
		]);

		$this->add_control('filter_counter_text_color', [
			'label'     => esc_html__('Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .btn-filter .filter-counter' => 'color: {{VALUE}};',
			],
			'condition' => [
				'filter_counter' => '1',
			],
		]);

		$this->add_control('filter_counter_background_color', [
			'label'     => esc_html__('Background', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .btn-filter .filter-counter'        => 'background: {{VALUE}};',
				'{{WRAPPER}} .btn-filter .filter-counter:before' => 'border-top-color: {{VALUE}};',
			],
			'condition' => [
				'filter_counter' => '1',
			],
		]);

		$this->end_controls_section();
	}

	protected function add_sorting_section()
	{
		$this->start_controls_section('result_count_sorting_section', [
			'label' => esc_html__('Result Count & Sorting', 'civi'),
		]);

		$this->add_control('show_result_count', [
			'label'        => esc_html__('Show Result Count', 'civi'),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => '1',
		]);

		$this->add_control('show_ordering', [
			'label'        => esc_html__('Show Order', 'civi'),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => '1',
		]);

		$this->end_controls_section();
	}

	protected function get_sort_options()
	{
		return [
			''           => esc_html__('Default', 'civi'),
			'popularity' => esc_html__('Popularity', 'civi'),
			'date'       => esc_html__('Latest', 'civi'),
			'price'      => esc_html__('Price: low to high', 'civi'),
			'price-desc' => esc_html__('Price: high to low', 'civi'),
		];
	}

	/**
	 * Check if layout is grid|masonry
	 *
	 * @return bool
	 */
	protected function is_grid()
	{
		$settings = $this->get_settings_for_display();
		if (
			!empty($settings['layout']) &&
			in_array($settings['layout'], array(
				'grid',
				'masonry',
			), true)
		) {
			return true;
		}

		return false;
	}

	protected function get_grid_options(array $settings)
	{
		$grid_options = [
			'type'  => $settings['layout'],
		];

		// Columns.
		if (!empty($settings['grid_columns'])) {
			$grid_options['columns'] = $settings['grid_columns'];
		}

		if (!empty($settings['grid_columns_tablet'])) {
			$grid_options['columnsTablet'] = $settings['grid_columns_tablet'];
		} else {
			$grid_options['columnsTablet'] = '2';
		}

		if (!empty($settings['grid_columns_mobile'])) {
			$grid_options['columnsMobile'] = $settings['grid_columns_mobile'];
		} else {
			$grid_options['columnsMobile'] = '1';
		}

		// Gutter
		if (!empty($settings['grid_gutter'])) {
			$grid_options['gutter'] = $settings['grid_gutter'];
		}

		if (!empty($settings['grid_gutter_tablet'])) {
			$grid_options['gutterTablet'] = $settings['grid_gutter_tablet'];
		}

		if (!empty($settings['grid_gutter_mobile'])) {
			$grid_options['gutterMobile'] = $settings['grid_gutter_mobile'];
		}

		// Zigzag height.
		if (!empty($settings['zigzag_height'])) {
			$grid_options['zigzagHeight'] = $settings['zigzag_height'];
		}

		if (!empty($settings['zigzag_height_tablet'])) {
			$grid_options['zigzagHeightTablet'] = $settings['zigzag_height_tablet'];
		}

		if (!empty($settings['zigzag_height_mobile'])) {
			$grid_options['zigzagHeightMobile'] = $settings['zigzag_height_mobile'];
		}

		if (!empty($settings['zigzag_reversed']) && 'yes' === $settings['zigzag_reversed']) {
			$grid_options['zigzagReversed'] = 1;
		}

		return $grid_options;
	}

	protected function print_pagination($query, $settings)
	{
		$number          = !empty($settings['query_number']) ? $settings['query_number'] : get_option('posts_per_page');
		$pagination_type = $settings['pagination_type'];

		if ($pagination_type !== '' && $query->found_posts > $number) {
?>
			<div class="civi-grid-pagination">
				<div class="pagination-wrapper">

					<?php if (in_array($pagination_type, array(
						'load-more',
						'load-more-alt',
						'infinite',
						'navigation',
					), true)) { ?>
						<div class="inner">
							<div class="civi-infinite-loader">
								<?php get_template_part('template-parts/preloader/style', 'circle'); ?>
							</div>
						</div>

						<div class="inner">
							<?php if ($pagination_type === 'load-more') { ?>
								<a href="#" class="civi-load-more-button civi-button style-border icon-right">
									<span class="button-text"><?php echo esc_html__('Load More', 'civi'); ?></span>
									<span class="button-icon fal fa-redo"></span>
								</a>
							<?php } elseif ($pagination_type === 'navigation') { ?>
								<?php $this->print_pagination_type_navigation(); ?>
							<?php } ?>
						</div>
					<?php } elseif ($pagination_type === 'numbers') { ?>
						<?php \Civi_Templates::pagination($query); ?>
					<?php } ?>

				</div>
			</div>
			<div class="civi-grid-messages" style="display: none;">
				<?php esc_html_e('All items displayed.', 'civi'); ?>
			</div>
		<?php
		}
	}

	protected function print_pagination_type_navigation()
	{
		?>
		<div class="navigation-buttons">
			<div class="nav-link prev-link disabled" data-action="prev">
				<?php esc_html_e('Prev Projects', 'civi'); ?>
			</div>
			<div class="nav-line"></div>
			<div class="nav-link next-link" data-action="next">
				<?php esc_html_e('Next Projects', 'civi'); ?>
			</div>
		</div>
	<?php
	}

	protected function print_filter($total = 0, $list = '')
	{
		$settings  = $this->get_settings_for_display();
		$category  = $this->get_post_category();
		$post_type = $this->get_post_type();

		if (empty($settings['filter_enable']) || 'yes' !== $settings['filter_enable']) {
			return;
		}

		$this->add_render_attribute('filter', 'class', 'civi-grid-filter');

		if ('1' === $settings['filter_counter']) {
			$this->add_render_attribute('filter', 'class', 'show-filter-counter');
		}

		if ('1' === $settings['filter_counter']) {
			$this->add_render_attribute('filter', 'data-filter-counter', true);
		}

		$current_cat = '';

		$btn_filter_class     = 'btn-filter';
		$btn_filter_all_class = $btn_filter_class;

		if ('' === $current_cat) {
			$btn_filter_all_class .= ' current';
		}
	?>
		<div <?php $this->print_render_attribute_string('filter') ?>>
			<?php ob_start(); ?>
			<div class="civi-grid-filter-buttons">
				<a href="<?php echo esc_url(get_post_type_archive_link($post_type)); ?>" class="<?php echo esc_attr($btn_filter_all_class); ?>" data-filter="*" data-filter-count="<?php echo esc_attr($total); ?>">
					<span class="filter-text"><?php esc_html_e('All', 'civi'); ?></span>
				</a>
				<?php
				if ($list === '') {
					$_categories = get_terms(array(
						'taxonomy'   => $category,
						'hide_empty' => true,
					));

					foreach ($_categories as $term) {
						$current_filter_class = $btn_filter_class;

						if ($term->term_id === $current_cat) {
							$current_filter_class .= ' current';
						}

						$term_link = get_term_link($term);
						printf(
							'<a href="%s" class="%s" data-filter="%s" data-filter-count="%s"><span class="filter-text">%s</span></a>',
							esc_url($term_link),
							esc_attr($current_filter_class),
							esc_attr("{$category}:{$term->slug}"),
							$term->count,
							$term->name
						);
					}
				} else {
					$list = explode(', ', $list);
					foreach ($list as $item) {
						$value = explode(':', $item);

						$term = get_term_by('slug', $value[1], $value[0]);

						if ($term === false) {
							continue;
						}

						$term_link = get_term_link($term);

						printf(
							'<a href="%s" class="btn-filter" data-filter="%s" data-filter-count="%s"><span class="filter-text">%s</span></a>',
							esc_url($term_link),
							esc_attr("{$value[0]}:{$value[1]}"),
							$term->count,
							$value[1]
						);
					}
				}
				?>
			</div>
			<?php
			$output = ob_get_clean();

			if ('1' === $settings['filter_in_grid']) {
				printf('<div class="container"><div class="row"><div class="col-md-12">%1$s</div></div></div>', $output);
			} else {
				echo '' . $output;
			}
			?>
		</div>
<?php
	}
}
