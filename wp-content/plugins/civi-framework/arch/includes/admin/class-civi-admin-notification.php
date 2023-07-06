<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('Civi_Admin_Notification')) {
    /**
     * Class Civi_Admin_Notification
     */
    class Civi_Admin_Notification
    {

        /**
         * Register custom columns
         * @param $columns
         * @return array
         */
        public function register_custom_column_titles($columns)
        {
            $columns['thumb'] = esc_html__('User', 'civi-framework');
            $columns['cb'] = "<input type=\"checkbox\" />";
            $columns['title'] = esc_html__('Title', 'civi-framework');
            $columns['action'] = esc_html__('Action', 'civi-framework');
            $columns['author'] = 'Author';
            $columns['date'] = esc_html__('Date', 'civi-framework');
            $new_columns = array();
            $custom_order = array('cb', 'thumb', 'title','action','author','date');
            foreach ($custom_order as $colname) {
                $new_columns[$colname] = $columns[$colname];
            }
            return $new_columns;
        }

        /**
         * sortable_columns
         * @param $columns
         * @return mixed
         */
        public function sortable_columns($columns)
        {
            $columns['title'] = 'title';
            $columns['action'] = 'action';
            $columns['author'] = 'author';
            $columns['date'] = 'date';
            return $columns;
        }

        /**
         * @param $vars
         * @return array
         */
        public function column_orderby($vars)
        {
            if (!is_admin())
                return $vars;
            return $vars;
        }
        /**
         * Display custom column for messages
         * @param $column
         */
        public function display_custom_column($column)
        {
            global $post;
            $post_id = $post->ID;
            switch ($column) {
                case 'thumb':
                    $user_send = get_post_meta($post_id, CIVI_METABOX_PREFIX . 'user_send_noti', true);
                    $avatar = get_the_author_meta( 'author_avatar_image_url', $user_send);
                    if(!empty($avatar)){
                        echo '<img src = " '.$avatar. '" alt=""/>' ;
                    } else {
                        echo '&ndash;';
                    }
                    break;
                case 'action':
                    $action = get_post_meta($post_id, CIVI_METABOX_PREFIX . 'action_noti', true);
                    $action = trim($action,"-");
                    echo $action;
                    break;
                case 'author':
                    echo '<a href="' . esc_url(add_query_arg('author', $post->post_author)) . '">' . get_the_author() . '</a>';
                    break;
            }
        }

        /**
         * notification_filter
         * @param $query
         */
        public function notification_filter($query)
        {
            global $pagenow;
            $post_type = 'notification';
            $q_vars    = &$query->query_vars;
            $filter_arr = array();
            if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type) {
                $notification_user = isset($_GET['notification_user']) ? civi_clean(wp_unslash($_GET['notification_user'])) : '';
                if ($notification_user !== '') {
                    $user = get_user_by('login', $notification_user);
                    $user_id = -1;
                    if ($user) {
                        $user_id = $user->ID;
                    }
                    $filter_arr[] = array(
                        'key' => CIVI_METABOX_PREFIX . 'notification_user_id',
                        'value' => $user_id,
                        'compare' => 'IN',
                    );
                }
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
            if ($post->post_type == 'notification') {
                unset($actions['view']);
            }
            return $actions;
        }
    }
}
