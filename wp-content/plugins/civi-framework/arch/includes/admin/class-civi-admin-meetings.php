<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('Civi_Admin_Meetings')) {
    /**
     * Class Civi_Admin_Meetings
     */
    class Civi_Admin_Meetings
    {

        /**
         * Register custom columns
         * @param $columns
         * @return array
         */
        public function register_custom_column_titles($columns)
        {
            $columns['thumb'] = esc_html__('Image', 'civi-framework');
            $columns['cb'] = "<input type=\"checkbox\" />";
            $columns['title'] = esc_html__('Jobs Title', 'civi-framework');
            $columns['meeting_status'] = esc_html__('Status', 'civi-framework');
            $columns['meeting_with'] = esc_html__('Meeting with', 'civi-framework');
            $columns['meeting_date'] = esc_html__('Meeting Date', 'civi-framework');
            $columns['time_duration'] = esc_html__('Time Duration', 'civi-framework');
            $columns['date'] = esc_html__('Date', 'civi-framework');
            $new_columns = array();
            $custom_order = array('cb', 'thumb', 'title', 'meeting_status', 'meeting_with', 'meeting_date', 'time_duration', 'date');
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
            $columns['meeting_status'] = 'meeting_status';
            $columns['meeting_with'] = 'meeting_with';
            $columns['meeting_date'] = 'meeting_date';
            $columns['time_duration'] = 'time_duration';
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
         * Display custom column for meetings
         * @param $column
         */
        public function display_custom_column($column)
        {
            global $post;
            $post_id = $post->ID;
            switch ($column) {
                case 'thumb':
                    $candidate_id = get_post_meta($post_id, CIVI_METABOX_PREFIX . 'meeting_candidate_user_id', true);
                    $candidate_avatar = get_the_author_meta( 'author_avatar_image_url', $candidate_id );
                    if(!empty($candidate_avatar)){
                        echo '<img src = " '.$candidate_avatar. '" alt=""/>' ;
                    } else {
                        echo '&ndash;';
                    }
                    break;
                case 'meeting_status':
                    $meetings_status = get_post_meta($post_id, CIVI_METABOX_PREFIX . 'meeting_status', true);
                    if ($meetings_status == 'completed') {
                        echo '<span class="label civi-label-blue">' . esc_html__('Completed', 'civi-framework') . '</span>';
                    } else {
                        echo '<span class="label civi-label-yellow">' . esc_html__('Upcoming', 'civi-framework') . '</span>';
                    }
                    break;
                case 'meeting_with':
                    $meetings_author = get_post_meta($post_id, CIVI_METABOX_PREFIX . 'meeting_with', true);
                    echo $meetings_author;
                    break;
                case 'meeting_date':
                    $meetings_date = get_post_meta($post_id, CIVI_METABOX_PREFIX . 'meeting_date', true);
                    echo $meetings_date;
                    break;
                case 'time_duration':
                    $meetings_time_duration = get_post_meta($post_id, CIVI_METABOX_PREFIX . 'meeting_time_duration', true);
                    echo $meetings_time_duration;
                    break;
            }
        }

        /**
         * Modify meetings slug
         * @param $existing_slug
         * @return string
         */
        public function modify_meetings_slug($existing_slug)
        {
            $meetings_url_slug = civi_get_option('meetings_url_slug');
            if ($meetings_url_slug) {
                return $meetings_url_slug;
            }
            return $existing_slug;
        }
        /**
         * filter_restrict_manage_meetings
         */
        public function filter_restrict_manage_meetings()
        {
            global $typenow;
            $post_type = 'meetings';

            if ($typenow == $post_type) {
                //Meeting Status
                $values = array(
                    'upcoming' => esc_html__('Upcoming', 'civi-framework'),
                    'completed' => esc_html__('Completed', 'civi-framework'),
                );
?>
                <select name="meeting_status">
                    <option value=""><?php esc_html_e('All Status', 'civi-framework'); ?></option>
                    <?php $current_v = isset($_GET['meeting_status']) ? civi_clean(wp_unslash($_GET['meeting_status'])) : '';
                    foreach ($values as $value => $label) {
                        printf(
                            '<option value="%s"%s>%s</option>',
                            $value,
                            $value == $current_v ? ' selected="selected"' : '',
                            $label
                        );
                    }
                    ?>
                </select>
<?php }
        }

        /**
         * meetings_filter
         * @param $query
         */
        public function meetings_filter($query)
        {
            global $pagenow;
            $post_type = 'meetings';
            $q_vars    = &$query->query_vars;
            $filter_arr = array();
            if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type) {
                $meetings_user = isset($_GET['meetings_user']) ? civi_clean(wp_unslash($_GET['meetings_user'])) : '';
                if ($meetings_user !== '') {
                    $user = get_user_by('login', $meetings_user);
                    $user_id = -1;
                    if ($user) {
                        $user_id = $user->ID;
                    }
                    $filter_arr[] = array(
                        'key' => CIVI_METABOX_PREFIX . 'meetings_user_id',
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
            if ($post->post_type == 'meetings') {
                unset($actions['view']);
            }
            return $actions;
        }
    }
}
