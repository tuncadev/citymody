<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('Civi_Admin_Messages')) {
    /**
     * Class Civi_Admin_Messages
     */
    class Civi_Admin_Messages
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
            $columns['messages_content'] = esc_html__('Content', 'civi-framework');
            $columns['author'] = 'Author';
            $columns['date'] = esc_html__('Date', 'civi-framework');
            $new_columns = array();
            $custom_order = array('cb', 'thumb', 'title','messages_content','author','date');
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
            $columns['messages_content'] = 'messages_content';
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
                    $creator_message = get_post_meta($post_id, CIVI_METABOX_PREFIX . 'creator_message', true);
                    $avatar = get_the_author_meta( 'author_avatar_image_url', $creator_message);
                    if(!empty($avatar)){
                        echo '<img src = " '.$avatar. '" alt=""/>' ;
                    } else {
                        echo '&ndash;';
                    }
                    break;
                case 'author':
                    echo '<a href="' . esc_url(add_query_arg('author', $post->post_author)) . '">' . get_the_author() . '</a>';
                    break;
                case 'messages_content':
                    $messages_content = get_the_excerpt($post_id);
                    echo $messages_content;
                    break;
            }
        }

        /**
         * messages_filter
         * @param $query
         */
        public function messages_filter($query)
        {
            global $pagenow;
            $post_type = 'messages';
            $q_vars    = &$query->query_vars;
            $filter_arr = array();
            if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type) {
                $messages_user = isset($_GET['messages_user']) ? civi_clean(wp_unslash($_GET['messages_user'])) : '';
                if ($messages_user !== '') {
                    $user = get_user_by('login', $messages_user);
                    $user_id = -1;
                    if ($user) {
                        $user_id = $user->ID;
                    }
                    $filter_arr[] = array(
                        'key' => CIVI_METABOX_PREFIX . 'messages_user_id',
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
            if ($post->post_type == 'messages') {
                unset($actions['view']);
            }
            return $actions;
        }
    }
}
