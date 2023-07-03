<?php
//==============================================================================
// LOGO WIDGET
//==============================================================================
if (!class_exists('Civi_Widget_Products')) {
    class Civi_Widget_Products extends Civi_Widget
    {
        public function __construct()
        {
            $this->widget_cssclass = 'widget-products';
            $this->widget_description = esc_html__("Products", 'civi-framework');
            $this->widget_id = 'civi_products';
            $this->widget_name = esc_html__('Civi - Products', 'civi-framework');
            $this->settings = array(
                'title' => array(
                    'type'  => 'text',
                    'std'   => esc_html__('Products', 'civi-framework'),
                    'label' => esc_html__('Title', 'civi-framework')
                ),
                'number_products' => array(
                    'type'  => 'number',
                    'std'   => '6',
                    'label' => esc_html__('Number of products to show', 'civi-framework')
                ),
                'filter_products' => array(
                    'type' => 'select',
                    'std'  => 'featured',
                    'label'   => esc_html__('Filter', 'civi-framework'),
                    'options' => array(
                        'new_items'    => esc_html__('Newest', 'civi-framework'),
                        'featured'     => esc_html__('Featured', 'civi-framework'),
                        'on_sale'      => esc_html__('Sale', 'civi-framework'),
                        'best_sellers' => esc_html__('Best Sellers', 'civi-framework'),
                    )
                ),
                'sort_by' => array(
                    'type'    => 'select',
                    'label'   => esc_html__('Sort By', 'civi-framework'),
                    'std'     => 'date',
                    'options' => array(
                        'date'  => esc_html__('Date', 'civi-framework'),
                        'title' => esc_html__('Title', 'civi-framework'),
                        'rand'  => esc_html__('Random', 'civi-framework'),
                    )
                ),
            );
            parent::__construct();
        }

        function widget($args, $instance)
        {
            if ($this->get_cached_widget($args))
                return;
            extract($args, EXTR_SKIP);

            $title           = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
            $number_products = empty($instance['number_products']) ? '' : apply_filters('widget_number', $instance['number_products']);
            $filter_products = empty($instance['filter_products']) ? '' : apply_filters('widget_filter', $instance['filter_products']);
            $sort_by         = empty($instance['sort_by']) ? '' : apply_filters('widget_number', $instance['sort_by']);

            ob_start();
            echo wp_kses_post($args['before_widget']);
?>

            <?php
            $tax_query = array();
            $meta_query = array();

            $arr = array(
                'post_type'     => 'product',
                'posts_per_page' => $number_products,
                'order_by'      => $sort_by,
                'tax_query'     => $tax_query,
                'meta_query'    => $meta_query,
            );

            if ($filter_products) {

                if ($filter_products == 'featured') {
                    $tax_query[] = array(
                        'taxonomy' => 'product_visibility',
                        'field'    => 'name',
                        'terms'    => 'featured',
                        'operator' => 'IN',
                    );
                }

                if ($filter_products == 'on_sale') {
                    $meta_query[] = array(
                        array(
                            'key'           => '_sale_price',
                            'value'         => 0,
                            'compare'       => '>',
                            'type'          => 'numeric'
                        ),
                    );
                }

                if ($filter_products == 'new_items') {
                    $arr['orderby']  = 'meta_value_num';
                    $arr['order']    = 'DESC';
                    $arr['meta_key'] = '_stock';
                }

                if ($filter_products == 'best_sellers') {
                    $arr['orderby']  = 'meta_value_num';
                    $arr['order']    = 'DESC';
                    $arr['meta_key'] = 'total_sales';
                }
            };

            $loop = new WP_Query($arr);
            if ($loop->have_posts()) {

            ?>
                <?php if (!empty($title)) { ?>
                    <h3 class="widget-title"><?php esc_html_e($title); ?></h3>
                <?php } ?>

                <?php
                $wrapper_attributes = array();
                $slick_attributes = array(
                    '"dots": true',
                    '"arrows": true',
                    '"slidesToShow": 1',
                    '"slidesToScroll": 1',
                    '"autoplay": true',
                    '"autoplaySpeed": 5000',
                    '"fade" : true',
                    '"infinite" : true',
                    '"cssEase" : "linear"',
                );
                $wrapper_attributes[] = "data-slick='{" . implode(', ', $slick_attributes) . "}'";
                ?>
                <div class="slick-carousel" <?php echo implode(' ', $wrapper_attributes); ?>>
                    <?php
                    while ($loop->have_posts()) : $loop->the_post();
                        global $product;
                    ?>
                        <?php wc_get_template_part('content', 'product'); ?>
                    <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            <?php } ?>

<?php
            echo wp_kses_post($args['after_widget']);
            $content = ob_get_clean();
            echo $content;

            $this->cache_widget($args, $content);
        }
    }
}
