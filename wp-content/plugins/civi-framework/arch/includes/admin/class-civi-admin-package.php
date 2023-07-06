<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('Civi_Admin_Package')) {
    /**
     * Class Civi_Admin_Package
     */
    class Civi_Admin_Package
    {
        /**
         * Modify package slug
         * @param $existing_slug
         * @return string
         */
        public function modify_package_slug($existing_slug)
        {
            $package_url_slug = civi_get_option('package_url_slug');
            if ($package_url_slug) {
                return $package_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Register custom column titles
         * @param $columns
         * @return array
         */
        public function register_custom_column_titles($columns)
        {
            $columns['cb'] = "<input type=\"checkbox\" />";
            $columns['title'] = esc_html__('Name', 'civi-framework');
            $columns['price'] = esc_html__('Price', 'civi-framework');
            $columns['featured'] = '<span data-tip="' .  esc_html__('Featured?', 'civi-framework') . '" class="tips dashicons dashicons-star-filled"></span>';
            $new_columns = array();
            $custom_order = array('cb', 'title', 'price', 'featured', 'date');
            foreach ($custom_order as $colname) {
                $new_columns[$colname] = $columns[$colname];
            }
            return $new_columns;
        }

        /**
         * Display custom column
         * @param $column
         */
        public function display_custom_column($column)
        {
            global $post;
            switch ($column) {
                case 'price':
                    $package_free = get_post_meta($post->ID, CIVI_METABOX_PREFIX . 'package_free', true);
                    if ($package_free == 1) {
                        esc_html_e('Free', 'civi-framework');
                    } else {
                        $package_price = get_post_meta($post->ID, CIVI_METABOX_PREFIX . 'package_price', true);
                        if ($package_price > 0) {
                            echo civi_get_format_money($package_price, '', 2);
                        } else {
                            esc_html_e('Free', 'civi-framework');
                        }
                    }

                    break;
                case 'featured':
                    $featured = get_post_meta($post->ID, CIVI_METABOX_PREFIX . 'package_featured', true);
                    if ($featured == 1) {
                        echo '<i data-tip="' .  esc_html__('Featured', 'civi-framework') . '" class="tips accent-color dashicons dashicons-star-filled"></i>';
                    } else {
                        echo '<i data-tip="' .  esc_html__('Not Featured', 'civi-framework') . '" class="tips dashicons dashicons-star-empty"></i>';
                    }
                    break;
            }
        }

        /**
         * @param $actions
         * @param $post
         * @return mixed
         */
        public function modify_list_row_actions($actions, $post)
        {
            // Check for your post type.
            if ($post->post_type == 'package') {
                unset($actions['view']);
            }
            return $actions;
        }
    }
}
