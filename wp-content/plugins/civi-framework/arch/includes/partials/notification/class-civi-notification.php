<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Civi_Notification')) {
    /**
     * Class Civi_Notification
     */
    class Civi_Notification
    {
        /**
         * Notification refresh
         */
        public function civi_refresh_notification()
        {
            $noti_id = isset($_REQUEST['noti_id']) ? civi_clean(wp_unslash($_REQUEST['noti_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? civi_clean(wp_unslash($_REQUEST['action_click'])) : '';

            if (!empty($noti_id) && $action_click == 'delete') {
                wp_delete_post($noti_id, true);
            }

            if ($action_click == 'clear') {
                $posts = civi_get_data_notification();
                foreach ($posts as $post) :
                    $id = $post->ID;
                    wp_delete_post($id, true);
                    wp_reset_postdata();
                endforeach;
            }

            $data_notification = civi_get_data_notification();
            if (!empty($data_notification)) {
                foreach ($data_notification as $index => $notification) {
                    $count = $index + 1;
                }
                if($count > 99){
                    $count = '99';
                }
            } else {
                $count = 0;
            }

            ob_start();
            civi_get_template('dashboard/notification/content.php', array(
                'data_notification' => $data_notification,
            ));

            $noti_content = ob_get_clean();

            echo json_encode(array('success' => true,'count' => $count, 'noti_content' => $noti_content));

            wp_die();
        }
    }
}
