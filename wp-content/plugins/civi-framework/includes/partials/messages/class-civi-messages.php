<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Civi_Messages')) {
    /**
     * Class Civi_Messages
     */
    class Civi_Messages
    {
        /**
         * Messages send
         */
        public function civi_send_messages()
        {
            $title_message = isset($_REQUEST['title_message']) ? civi_clean(wp_unslash($_REQUEST['title_message'])) : '';
            $content_message = isset($_REQUEST['content_message']) ? civi_clean(wp_unslash($_REQUEST['content_message'])) : '';
            $creator_message = isset($_REQUEST['creator_message']) ? civi_clean(wp_unslash($_REQUEST['creator_message'])) : '';
            $recipient_message = isset($_REQUEST['recipient_message']) ? civi_clean(wp_unslash($_REQUEST['recipient_message'])) : '';

            $reply_message = get_post_field('post_author', $recipient_message);
            if ($title_message == '' || $content_message == '') {
                echo json_encode(array('success' => false, 'message' => esc_html__('Please fill all form fields', 'civi-framework')));
            } else {
                $new_messages = array(
                    'post_type' => 'messages',
                    'post_status' => 'pending',
                );

                if (isset($title_message)) {
                    $new_messages['post_title'] = $title_message;
                }

                if (isset($content_message)) {
                    $new_messages['post_excerpt'] = $content_message;
                }

                if (!empty($new_messages['post_title'])) {
                    $messages_id = wp_insert_post($new_messages, true);
                }

                civi_get_data_ajax_notification($recipient_message,'add-message');

                if (isset($messages_id)) {
                    update_post_meta($messages_id, CIVI_METABOX_PREFIX . 'creator_message', $creator_message);
                    update_post_meta($messages_id, CIVI_METABOX_PREFIX . 'recipient_message', $recipient_message);
                    update_post_meta($messages_id, CIVI_METABOX_PREFIX . 'reply_message', $reply_message);
                }

                echo json_encode(array('success' => true, 'message' => esc_html__('You have sent the message successfully', 'civi-framework')));
            }

            wp_die();
        }


        /**
         * Messages write
         */
        public function civi_write_messages()
        {
            $content_message = isset($_REQUEST['content_message']) ? civi_clean(wp_unslash($_REQUEST['content_message'])) : '';
            $post_creator = isset($_REQUEST['post_creator']) ? civi_clean(wp_unslash($_REQUEST['post_creator'])) : '';

            if ($content_message == '') {
                echo json_encode(array('success' => false, 'message' => esc_html__('Please enter the content', 'civi-framework')));
            } else {

                global $current_user;
                $user_id = $current_user->ID;
                $title_message = sprintf(esc_html__('Reply: %s', 'civi-framework'), get_the_title($post_creator));
                $new_messages = array(
                    'post_type' => 'messages',
                    'post_status' => 'publish',
                );

                if (isset($title_message)) {
                    $new_messages['post_title'] = $title_message;
                }

                if (isset($content_message)) {
                    $new_messages['post_excerpt'] = $content_message;
                }

                if (!empty($new_messages['post_title'])) {
                    $message_id = wp_insert_post($new_messages, true);
                }

                if (isset($message_id)) {
                    update_post_meta($message_id, CIVI_METABOX_PREFIX . 'post_message_reply', $post_creator);
                    update_post_meta($message_id, CIVI_METABOX_PREFIX . 'creator_message_user', $user_id);
                }

                $data = array(
                    'ID' => $post_creator,
                    'post_type' => 'messages',
                    'post_status' => 'publish',
                );
                wp_update_post($data);

                //Notification
                $creator_athour_mess = get_post_field('post_author', $post_creator);
                $post_recipient = get_post_meta($post_creator, CIVI_METABOX_PREFIX . 'recipient_message',true);
                $recipient_athour_mess = get_post_field('post_author', $post_recipient);
                if (intval($creator_athour_mess) == $user_id) {
                    civi_get_data_ajax_notification($post_recipient,'add-message');
                }
                if (intval($recipient_athour_mess) == $user_id) {
                    civi_get_data_ajax_notification($post_creator,'add-message');
                }

                ob_start();
                civi_get_template('dashboard/messages/content/body.php', array(
                    'message_id' => $post_creator,
                ));
                $messages_html = ob_get_clean();

                echo json_encode(array('success' => true, 'messages_html' => $messages_html));
            }

            wp_die();
        }

        /**
         * Messages list user
         */
        public function civi_messages_list_user()
        {
            $message_id = isset($_REQUEST['message_id']) ? civi_clean(wp_unslash($_REQUEST['message_id'])) : '';

            $data = array(
                'ID' => $message_id,
                'post_type' => 'messages',
                'post_status' => 'publish',
            );
            wp_update_post($data);

            ob_start();

            civi_get_template('dashboard/messages/content.php', array(
                'message_id' => $message_id,
            ));

            $mess_content_list = ob_get_clean();

            echo json_encode(array('success' => true, 'mess_content_list' => $mess_content_list));

            wp_die();
        }

        /**
         * Messages refresh
         */
        public function civi_refresh_messages()
        {
            $message_id = isset($_REQUEST['message_id']) ? civi_clean(wp_unslash($_REQUEST['message_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? civi_clean(wp_unslash($_REQUEST['action_click'])) : '';

            // Data reply
            $args_reply = array(
                'post_type' => 'messages',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'order' => 'ASC',
                'meta_query' => array(
                    array(
                        'key' => CIVI_METABOX_PREFIX . 'post_message_reply',
                        'value' => $message_id,
                        'compare' => '=='
                    )
                ),
            );
            $data_reply = new WP_Query($args_reply);
            $mess_reply_id = array();
            if ($data_reply->have_posts()) {
                while ($data_reply->have_posts()) : $data_reply->the_post();
                    $mess_reply_id[] = get_the_ID();
                endwhile;
            }

            // Delete mess
            if (!empty($message_id) && $action_click == 'delete') {
                wp_delete_post($message_id, true);

                foreach ($mess_reply_id as $reply_id) {
                    wp_delete_post($reply_id, true);
                }
            }

            // Data Frist
            $data_frist = civi_get_data_list_message(true);
            $frist_id = array();
            if ($data_frist->have_posts()) {
                while ($data_frist->have_posts()) : $data_frist->the_post();
                    $frist_id[] = get_the_ID();
                endwhile;
            }
            $frist_id = !empty($frist_id) ? $frist_id[0] : '';

            //Unread
            $data_list_unread = civi_get_data_list_message(false,true);
            $badge = $data_list_unread->found_posts;

            // Content mess
            ob_start();
            $data_list = civi_get_data_list_message(false);
            $total_post = $data_list->found_posts;
            if ($total_post > 0) { ?>
                <div class="bg-overlay"></div>
                <div class="mess-list">
                    <?php civi_get_template('dashboard/messages/tab.php'); ?>
                </div>
                <div class="mess-content">
                    <?php civi_get_template('dashboard/messages/content.php', array(
                        'message_id' => $frist_id,
                    )); ?>
                </div>
            <?php } else {
                civi_get_template('dashboard/messages/empty.php');
            } ?>
            <div class="civi-loading-effect"><span class="civi-dual-ring"></span></div>
            <?php $mess_content = ob_get_clean();

            echo json_encode(array('success' => true, 'mess_content' => $mess_content , 'badge' => $badge));

            wp_die();
        }
    }
}
