<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Civi_Mega_Menu' ) ) {
    class Civi_Mega_Menu {

        protected static $instance = null;

        static function instance() {
            if ( null === self::$instance ) {
                self::$instance = new self();
            }

            return self:: $instance;
        }

        public function initialize() {
            add_action( 'init', array( $this, 'register_mega_menu') );
            add_post_type_support( 'civi_mega_menu', 'elementor' );
        }

        /**
         * Register Mega_Menu Post Type
         */
        function register_mega_menu() {

            $labels = array(
                'name'               => _x( 'Mega Menus', 'Post Type General Name', 'civi' ),
                'singular_name'      => _x( 'Mega Menus', 'Post Type Singular Name', 'civi' ),
                'menu_name'          => esc_html__( 'Mega Menu', 'civi' ),
                'name_admin_bar'     => esc_html__( 'Mega Menu', 'civi' ),
                'parent_item_colon'  => esc_html__( 'Parent Menu:', 'civi' ),
                'all_items'          => esc_html__( 'Mega Menus', 'civi' ),
                'add_new_item'       => esc_html__( 'Add New Menu', 'civi' ),
                'add_new'            => esc_html__( 'Add New', 'civi' ),
                'new_item'           => esc_html__( 'New Menu', 'civi' ),
                'edit_item'          => esc_html__( 'Edit Menu', 'civi' ),
                'update_item'        => esc_html__( 'Update Menu', 'civi' ),
                'view_item'          => esc_html__( 'View Menu', 'civi' ),
                'search_items'       => esc_html__( 'Search Menu', 'civi' ),
                'not_found'          => esc_html__( 'Not found', 'civi' ),
                'not_found_in_trash' => esc_html__( 'Not found in Trash', 'civi' ),
            );

            $args = array(
                'label'       => esc_html__( 'Mega Menus', 'civi' ),
                'description' => esc_html__( 'Mega Menus', 'civi' ),
                'labels'      => $labels,
                'supports'    => array(
                    'title',
                    'editor',
                    'revisions',
                ),
                'hierarchical'        => false,
                'public'              => true,
                'menu_position'       => 14,
                'menu_icon'           => 'dashicons-menu-alt',
                'show_in_admin_bar'   => true,
                'show_in_nav_menus'   => true,
                'can_export'          => true,
                'has_archive'         => false,
                'exclude_from_search' => true,
                'publicly_queryable'  => false,
                'rewrite'             => false,
                'capability_type'     => 'page',
                'publicly_queryable'  => true, // Enable TRUE for Elementor Editing
            );

            register_post_type( 'civi_mega_menu', $args );

        }
    }

    Civi_Mega_Menu:: instance()->initialize();
}
