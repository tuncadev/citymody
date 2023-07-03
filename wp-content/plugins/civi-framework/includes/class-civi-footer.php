<?php
defined('ABSPATH') || exit;

if (!class_exists('Civi_Footer')) {

    class Civi_Footer
    {

        protected static $instance = null;

        public static function instance()
        {
            if (null === self::$instance) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function initialize()
        {
            add_action('init', array($this, 'register_footer'));
            add_post_type_support('civi_footer', 'elementor');
        }

        /**
         * Register Footer Post Type
         */
        function register_footer()
        {
            $labels = array(
                'name' => __('Footer', 'civi-framework'),
                'singular_name' => __('Footer', 'civi-framework'),
                'add_new' => __('Add New', 'civi-framework'),
                'add_new_item' => __('Add New', 'civi-framework'),
                'edit_item' => __('Edit Footer', 'civi-framework'),
                'new_item' => __('Add New Footer', 'civi-framework'),
                'view_item' => __('View Footer', 'civi-framework'),
                'search_items' => __('Search Footer', 'civi-framework'),
                'not_found' => __('No items found', 'civi-framework'),
                'not_found_in_trash' => __('No items found in trash', 'civi-framework'),
            );

            $args = array(
                'menu_icon' => 'dashicons-arrow-down-alt',
                'label' => esc_html__('Footer', 'civi'),
                'description' => esc_html__('Footer', 'civi'),
                'labels' => $labels,
                'supports' => array(
                    'title',
                    'editor',
                    'revisions',
                ),
                'hierarchical' => false,
                'public' => true,
                'menu_position' => 15,
                'show_in_admin_bar' => true,
                'show_in_nav_menus' => true,
                'can_export' => true,
                'has_archive' => false,
                'exclude_from_search' => true,
                'publicly_queryable' => false,
                'rewrite' => false,
                'capability_type' => 'page',
                'publicly_queryable' => true, // Enable TRUE for Elementor Editing
            );
            register_post_type('civi_footer', $args);
        }
    }

    Civi_Footer::instance()->initialize();
}
