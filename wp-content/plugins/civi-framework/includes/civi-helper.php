<?php

/**
 * Get Option
 */
if (!function_exists('civi_get_option')) {
    function civi_get_option($key, $default = '')
    {
        $option = get_option(CIVI_OPTIONS_NAME);
        return (isset($option[$key])) ? $option[$key] : $default;
    }
}

/**
 * Check nonce
 *
 * @param string $action Action name.
 * @param string $nonce Nonce.
 */
if (!function_exists('verify_nonce')) {
    function verify_nonce($action = '', $nonce = '')
    {

        if (!$nonce && isset($_REQUEST['_wpnonce'])) {
            $nonce = sanitize_text_field(wp_unslash($_REQUEST['_wpnonce']));
        }

        return wp_verify_nonce($nonce, $action);
    }
}

/**
 * Check theme support
 */
if (!function_exists('is_theme_support')) {
    function is_theme_support()
    {
        return current_theme_supports('civi');
    }
}

/**
 * Check has shortcode
 */
if (!function_exists('civi_page_shortcode')) {
    function civi_page_shortcode($shortcode = NULL)
    {

        $post = get_post(get_the_ID());

        $found = false;

        if (empty($post->post_content)) {
            return $found;
        }

        if (wp_strip_all_tags($post->post_content) === $shortcode) {
            $found = true;
        }

        // return our final results
        return $found;
    }
}

/**
 * Insert custom header script.
 *
 * @return void
 */
function civi_custom_header_js()
{
    if (civi_get_option('header_script', '') && !is_admin()) {
        echo civi_get_option('header_script', ''); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

add_action('wp_head', 'civi_custom_header_js', 99);

/**
 * Insert custom footer script.
 *
 * @return void
 */
function civi_footer_scripts()
{
    echo do_shortcode(civi_get_option('footer_script', ''));
}

add_action('wp_footer', 'civi_footer_scripts');

/**
 * Convert text to 1 line
 *
 * @param $str
 *
 * @return string
 */
if (!function_exists('text2line')) {
    function text2line($str)
    {
        return trim(preg_replace("/[\r\v\n\t]*/", '', $str));
    }
}

/**
 * Get template part (for templates like the shop-loop).
 *
 * @param mixed $slug
 * @param string $name (default: '')
 */
if (!function_exists('civi_get_template_part')) {
    function civi_get_template_part($slug, $name = '')
    {
        $template = '';
        if ($name) {
            $template = locate_template(array("{$slug}-{$name}.php", CIVI()->template_path() . "{$slug}-{$name}.php"));
        }

        // Get default slug-name.php
        if (!$template && $name && file_exists(CIVI_PLUGIN_DIR . "templates/{$slug}-{$name}.php")) {
            $template = CIVI_PLUGIN_DIR . "templates/{$slug}-{$name}.php";
        }

        if (!$template) {
            $template = locate_template(array("{$slug}.php", CIVI()->template_path() . "{$slug}.php"));
        }

        // Allow 3rd party plugins to filter template file from their plugin.
        $template = apply_filters('civi_get_template_part', $template, $slug, $name);

        if ($template) {
            load_template($template, false);
        }
    }
}

/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 */
if (!function_exists('civi_get_template')) {
    function civi_get_template($template_name, $args = array(), $template_path = '', $default_path = '')
    {
        if (!empty($args) && is_array($args)) {
            extract($args);
        }

        $located = civi_locate_template($template_name, $template_path, $default_path);

        if (!file_exists($located)) {
            _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $located), '2.1');
            return;
        }

        // Allow 3rd party plugin filter template file from their plugin.
        $located = apply_filters('civi_get_template', $located, $template_name, $args, $template_path, $default_path);

        do_action('civi_before_template_part', $template_name, $template_path, $located, $args);

        include($located);

        do_action('civi_after_template_part', $template_name, $template_path, $located, $args);
    }
}

/**
 * Like civi_get_template, but returns the HTML instead of outputting.
 */
if (!function_exists('civi_get_template_html')) {
    function civi_get_template_html($template_name, $args = array(), $template_path = '', $default_path = '')
    {
        ob_start();
        civi_get_template($template_name, $args, $template_path, $default_path);
        return ob_get_clean();
    }
}

/**
 * Send email
 */
if (!function_exists('civi_send_email')) {
    function civi_send_email($email, $email_type, $args = array())
    {
        $content = civi_get_option($email_type, '');
        $subject = civi_get_option('subject_' . $email_type, '');

        if (function_exists('icl_translate')) {
            $content = icl_translate('civi-framework', 'civi_email_' . $content, $content);
            $subject = icl_translate('civi-framework', 'civi_email_subject_' . $subject, $subject);
        }
        $content = wpautop($content);
        $args['website_url'] = get_option('siteurl');
        $args['website_name'] = get_option('blogname');
        $args['user_email'] = $email;
        $user = get_user_by('email', $email);
        if (!empty($user)) {
            $args['username'] = $user->user_login;
        }

        foreach ($args as $key => $val) {
            $subject = str_replace('%' . $key, $val, $subject);
            $content = str_replace('%' . $key, $val, $content);

        }

        ob_start();
        civi_get_template("mail/mail.php", array(
            'content' => $content,
        ));
        $message = ob_get_clean();

        $headers = apply_filters('civi_contact_mail_header', array('Content-Type: text/html; charset=UTF-8'));

        @wp_mail(
            $email,
            $subject,
            $message,
            $headers
        );
    }
}

/**
 * Get posts by user id
 */
if (!function_exists('get_posts_by_user')) {
    function get_posts_by_user($user_id, $post_type = 'post', $number = 6, $item = 4, $item_lg = 4, $item_md = 3, $item_sm = 2, $item_xs = 2)
    {
        $custom_jobs_image_size = civi_get_option('custom_jobs_image_size', '540x480');
        $archive_jobs_items_amount = civi_get_option('archive_jobs_items_amount', '12');
        $archive_class = array();
        $archive_class[] = 'grid';
        $archive_class[] = 'columns-' . $item;
        $archive_class[] = 'columns-lg-' . $item_lg;
        $archive_class[] = 'columns-md-' . $item_md;
        $archive_class[] = 'columns-sm-' . $item_sm;
        $archive_class[] = 'columns-xs-' . $item_xs;

        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => $number,
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'author' => $user_id,
            'orderby' => array(
                'menu_order' => 'ASC',
                'date' => 'DESC',
            ),
        );
        $posts = new WP_Query($args);
        $total_post = $posts->found_posts;
        $post_html = '';

        ob_start();

        if ($total_post > 0) {
            ?>
            <div class="area-jobs <?php echo join(' ', $archive_class); ?>"
                 data-item-amount='<?php echo esc_attr($number); ?>'>

                <?php while ($posts->have_posts()) : $posts->the_post(); ?>

                    <?php civi_get_template('content-jobs.php', array(
                        'custom_jobs_image_size' => $custom_jobs_image_size
                    )); ?>

                <?php endwhile; ?>

            </div>

        <?php } else { ?>

            <div class="item-not-found"><?php esc_html_e('No item found', 'civi-framework'); ?></div>

        <?php }

        $max_num_pages = $posts->max_num_pages;
        civi_get_template('global/pagination.php', array('max_num_pages' => $max_num_pages, 'type' => 'ajax-call'));

        wp_reset_postdata();

        $post_html = ob_get_clean();

        return $post_html;
    }
}

/**
 * Get total posts by user id
 */
if (!function_exists('get_total_posts_by_user')) {
    function get_total_posts_by_user($user_id, $post_type = 'post')
    {
        $args = array(
            'post_type' => $post_type,
            'post_status' => 'publish',
            'author' => $user_id,
        );
        $posts = new WP_Query($args);
        wp_reset_postdata();
        return $posts->found_posts;
    }
}

/**
 * Get total reviews by user id
 */
if (!function_exists('get_total_reviews_by_user')) {
    function get_total_reviews_by_user($user_id)
    {
        global $wpdb;
        $comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE meta.meta_key = 'jobs_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 OR comment.user_id = $user_id )";
        $get_comments = $wpdb->get_results($comments_query);

        $author_obj = get_user_by('id', $user_id);

        $comment_author = array();
        if (!is_null($get_comments)) {
            foreach ($get_comments as $comment) {
                $comment_id = $comment->comment_ID;
                $post_id = $comment->comment_post_ID;
                $comment_name = $comment->comment_author;
                $status = get_post_status($post_id);
                $post_author_id = get_post_field('post_author', $post_id);

                if ($author_obj->user_login == $comment_name && $status == 'publish') {
                    $comment_author[] = $comment_id;
                }
            }
        }
        $total_post = count($comment_author);

        return $total_post;
    }
}

/**
 * Get page id
 */
if (!function_exists('civi_get_page_id')) {
    function civi_get_page_id($page)
    {
        $page_id = civi_get_option('civi_' . $page . '_page_id');
        if ($page_id) {
            return absint(function_exists('pll_get_post') ? pll_get_post($page_id) : $page_id);
        } else {
            return 0;
        }
    }
}

/**
 * Get permalink
 */
if (!function_exists('civi_get_permalink')) {
    function civi_get_permalink($page)
    {
        if ($page_id = civi_get_page_id($page)) {
            return get_permalink($page_id);
        } else {
            return false;
        }
    }
}

/**
 * allow submit
 */
if (!function_exists('civi_allow_submit')) {
    function civi_allow_submit()
    {
        $enable_submit_jobs_via_frontend = civi_get_option('enable_submit_jobs_via_frontend', 1);
        $user_can_submit = civi_get_option('user_can_submit', 1);

        $allow_submit = true;
        if ($enable_submit_jobs_via_frontend != 1) {
            $allow_submit = false;
        } else {
            if ($user_can_submit != 1) {
                $allow_submit = false;
            }
        }
        return $allow_submit;
    }
}

/**
 * Total View Candidate
 */
if (!function_exists('civi_total_view_candidate')) {
    function civi_total_view_candidate($number_days = 7)
    {
        global $current_user;
        wp_get_current_user();
        $user_id = $current_user->ID;

        $args = array(
            'post_type' => 'candidate',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
            'author' => $user_id,
        );

        $data = new WP_Query($args);
        $total_post = $data->found_posts;
        $views_values = array();
        if ($total_post > 0) {
            while ($data->have_posts()) : $data->the_post();
                $id = get_the_ID();
                $views_date = get_post_meta($id, 'civi_view_candidate_date', true);
                $item = array();
                for ($i = $number_days; $i >= 0; $i--) {
                    $date = date("Y-m-d", strtotime("-" . $i . " day"));

                    if (isset($views_date[$date])) {
                        $item[] = $views_date[$date];
                    } else {
                        $item[] = 0;
                    }
                }
                array_push($views_values, $item);
            endwhile;
        }
        wp_reset_postdata();
        $results_value = array();
        for ($i = 0; $i <= $number_days; $i++) {
            $views_item = 0;
            foreach ($views_values as $views_value) {
                $views_item += $views_value[$i];
            }
            array_push($results_value, $views_item);
        }
        return $results_value;
    }
}

/**
 * Company Green Tick
 */
if (!function_exists('civi_company_green_tick')) {
    function civi_company_green_tick($company_id)
    {
        if (empty($company_id)) return;
        $company_green_tick = get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_green_tick', true);
        if ($company_green_tick == 1) : ?>
            <div class="civi-check-company tip active">
                <div class="tip-content">
                    <h4><?php esc_html_e('Conditions for a green tick:', 'civi-framework') ?></h4>
                    <ul class="list-check">
                        <li class="check-webs active">
                            <i class="fas fa-check"></i>
                            <?php esc_html_e('Website has been verified', 'civi-framework') ?>
                        </li>
                        <li class="check-phone active">
                            <i class="fas fa-check"></i>
                            <?php esc_html_e('Phone has been verified', 'civi-framework') ?>
                        </li>
                        <li class="check-location active">
                            <i class="fas fa-check"></i>
                            <?php esc_html_e('Location has been verified', 'civi-framework') ?>
                        </li>
                    </ul>
                </div>
            </div>
        <?php endif;
    }
}


/**
 * Actived Jobs
 */
if (!function_exists('civi_total_actived_jobs')) {
    function civi_total_actived_jobs()
    {

        global $current_user;
        $user_id = $current_user->ID;

        $args = array(
            'post_type' => 'jobs',
            'posts_per_page' => -1,
            'author' => $user_id,
        );

        $data = new WP_Query($args);
        $total_post = $data->found_posts;

        return $total_post;
    }
}

/**
 * Total Applications
 */
if (!function_exists('civi_total_applications_jobs')) {
    function civi_total_applications_jobs()
    {

        global $current_user;
        $user_id = $current_user->ID;
        $args_jobs = array(
            'post_type' => 'jobs',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
            'author' => $user_id,
            'orderby' => 'date',
        );
        $data_jobs = new WP_Query($args_jobs);
        $jobs_employer_id = array();
        if ($data_jobs->have_posts()) {
            while ($data_jobs->have_posts()) : $data_jobs->the_post();
                $jobs_employer_id[] = get_the_ID();
            endwhile;
        }
        $args = array(
            'post_type' => 'applicants',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => CIVI_METABOX_PREFIX . 'applicants_jobs_id',
                    'value' => $jobs_employer_id,
                    'compare' => 'IN'
                )
            ),
        );

        $data = new WP_Query($args);
        $total_post = $data->found_posts;

        if (!empty($jobs_employer_id)) {
            return $total_post;
        } else {
            return 0;
        }
    }
}

/**
 * Total meetings
 */
if (!function_exists('civi_total_meeting')) {
    function civi_total_meeting($user)
    {
        if (empty($user)) return;
        global $current_user;
        $user_id = $current_user->ID;
        if ($user == 'employer') {
            $args = array(
                'post_type' => 'meetings',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'author' => $user_id,
            );
        } elseif ($user == 'candidate') {
            $args_applicants = array(
                'post_type' => 'applicants',
                'ignore_sticky_posts' => 1,
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'author' => $user_id,
            );
            $data_applicants = new WP_Query($args_applicants);
            $applicants_id = array();
            if ($data_applicants->have_posts()) {
                while ($data_applicants->have_posts()) : $data_applicants->the_post();
                    $applicants_id[] = get_the_ID();
                endwhile;
            }
            $args = array(
                'post_type' => 'meetings',
                'ignore_sticky_posts' => 1,
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => CIVI_METABOX_PREFIX . 'meeting_applicants_id',
                        'value' => $applicants_id,
                        'compare' => 'IN'
                    ),
                    array(
                        'key' => CIVI_METABOX_PREFIX . 'meeting_status',
                        'value' => 'completed',
                        'compare' => '!='
                    )
                ),
            );
        }
        $data = new WP_Query($args);
        $total_post = $data->found_posts;

        if ($user == 'employer') {
            return $total_post;
        } elseif ($user == 'candidate') {
            if (!empty($applicants_id)) {
                return $total_post;
            } else {
                return 0;
            }
        }
    }
}

/**
 * Total View Jobs
 */
if (!function_exists('civi_total_view_jobs')) {
    function civi_total_view_jobs($number_days = 7)
    {
        global $current_user;
        wp_get_current_user();
        $user_id = $current_user->ID;

        $args = array(
            'post_type' => 'jobs',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
            'author' => $user_id,
        );

        $data = new WP_Query($args);
        $total_post = $data->found_posts;
        $views_values = array();
        if ($total_post > 0) {
            while ($data->have_posts()) : $data->the_post();
                $id = get_the_ID();
                $views_date = get_post_meta($id, 'civi_view_by_date', true);
                $item = array();
                for ($i = $number_days; $i >= 0; $i--) {
                    $date = date("Y-m-d", strtotime("-" . $i . " day"));

                    if (isset($views_date[$date])) {
                        $item[] = $views_date[$date];
                    } else {
                        $item[] = 0;
                    }
                }
                array_push($views_values, $item);
            endwhile;
        }
        wp_reset_postdata();
        $results_value = array();
        for ($i = 0; $i <= $number_days; $i++) {
            $views_item = 0;
            foreach ($views_values as $views_value) {
                $views_item += $views_value[$i];
            }
            array_push($results_value, $views_item);
        }
        return $results_value;
    }
}

/**
 * Total view jobs details
 */
if (!function_exists('civi_total_view_jobs_details')) {
    function civi_total_view_jobs_details($jobs_id)
    {

        if($jobs_id){
			$jobs_id = $jobs_id;
		} else {
			$jobs_id = get_the_ID();
		}
        $views_values = get_post_meta($jobs_id, 'civi_view_by_date', true);
        $views = 0;
		if( $views_values ){
			foreach ($views_values as $values) {
				$views += $values;
			}
		}
        if($views > 1){
            $text = esc_html('views','civi-framework');
        } else {
            $text = esc_html('view','civi-framework');
        }
        ?>
        <div class="jobs-view">
            <i class="fal fa-eye"></i>
            <span class="count"><?php echo sprintf('%1s (%2s)',$views,$text)?></span>
        </div>
        <?php
    }
}

/**
 * Total Applications Jobs ID
 */
if (!function_exists('civi_total_applications_jobs_id')) {
    function civi_total_applications_jobs_id($jobs_id)
    {

        $args = array(
            'post_type' => 'applicants',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => CIVI_METABOX_PREFIX . 'applicants_jobs_id',
                    'value' => $jobs_id,
                    'compare' => '='
                )
            ),
        );
        $data = new WP_Query($args);
        $total_post = $data->found_posts;

        return $total_post;
    }
}


/**
 * Total Jobs Apply
 */
if (!function_exists('civi_total_jobs_apply')) {
    function civi_total_jobs_apply($jobs_id, $number_days = 7)
    {

        if (empty($jobs_id)) return;
        $total_apply = array();
        for ($i = $number_days; $i >= 0; $i--) {
            $date = date("Y-m-d", strtotime("-" . $i . " day"));
            $args = array(
                'post_type' => 'applicants',
                'ignore_sticky_posts' => 1,
                'posts_per_page' => -1,
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => CIVI_METABOX_PREFIX . 'applicants_jobs_id',
                        'value' => $jobs_id,
                        'compare' => '='
                    ),
                    array(
                        'key' => CIVI_METABOX_PREFIX . 'applicants_date',
                        'value' => $date,
                        'compare' => '='
                    ),
                ),
            );
            $data = new WP_Query($args);
            $total_post = $data->found_posts;
            array_push($total_apply, $total_post);
        }
        return $total_apply;
    }
}


/**
 * Jobs Date
 */
if (!function_exists('civi_view_jobs_date')) {
    function civi_view_jobs_date($jobs_id, $number_days = 7)
    {

        if (empty($jobs_id)) return;
        $views_date = get_post_meta($jobs_id, 'civi_view_by_date', true);
        if (!is_array($views_date)) {
            $views_date = array();
        }

        $views_values = array();
        for ($i = $number_days; $i >= 0; $i--) {
            $date = date("Y-m-d", strtotime("-" . $i . " day"));
            if (isset($views_date[$date])) {
                $views_values[] = $views_date[$date];
            } else {
                $views_values[] = 0;
            }
        }

        return $views_values;
    }
}

/**
 * User Review
 */
if (!function_exists('civi_total_user_review')) {
    function civi_total_user_review()
    {

        global $current_user;
        wp_get_current_user();
        $user_id = $current_user->ID;

        global $wpdb;
        $comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE meta.meta_key = 'jobs_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 OR comment.user_id = $user_id )";

        $get_comments = $wpdb->get_results($comments_query);

        $comment_author = array();
        if (!is_null($get_comments)) {
            foreach ($get_comments as $comment) {
                $comment_id = $comment->comment_ID;
                $post_id = $comment->comment_post_ID;
                $comment_user_id = $comment->user_id;
                $post_author_id = get_post_field('post_author', $post_id);
                if ($post_author_id == $user_id) {
                    $comment_author[] = $comment_id;
                }
            }
        }
        $total_post = count($comment_author);

        add_user_meta($user_id, 'user_list_comment_id', $comment_author);

        return $total_post;
    }
}

if (!function_exists('civi_admin_taxonomy_terms')) {
    function civi_admin_taxonomy_terms($post_id, $taxonomy, $post_type)
    {

        $terms = get_the_terms($post_id, $taxonomy);

        if (!is_wp_error($terms) && $terms != false) {
            $results = array();
            foreach ($terms as $term) {
                $results[] = sprintf(
                    '<a href="%s">%s</a>',
                    esc_url(add_query_arg(array('post_type' => $post_type, $taxonomy => $term->slug), 'edit.php')),
                    esc_html(sanitize_term_field('name', $term->name, $term->term_id, $taxonomy, 'display'))
                );
            }
            return join(', ', $results);
        }

        return false;
    }
}

/**
 * civi_admin_taxonomy_terms
 */
if (!function_exists('civi_admin_taxonomy_terms')) {
    function civi_admin_taxonomy_terms($post_id, $taxonomy, $post_type)
    {

        $terms = get_the_terms($post_id, $taxonomy);

        if (!is_wp_error($terms) && $terms != false) {
            $results = array();
            foreach ($terms as $term) {
                $results[] = sprintf(
                    '<a href="%s">%s</a>',
                    esc_url(add_query_arg(array('post_type' => $post_type, $taxonomy => $term->slug), 'edit.php')),
                    esc_html(sanitize_term_field('name', $term->name, $term->term_id, $taxonomy, 'display'))
                );
            }
            return join(', ', $results);
        }

        return false;
    }
}

/**
 * Get format number
 */
if (!function_exists('civi_get_format_number')) {
    function civi_get_format_number($number, $decimals = 0)
    {
        $number = doubleval($number);
        if ($number) {
            $dec_point = civi_get_option('decimal_separator', '.');
            $thousands_sep = civi_get_option('thousand_separator', ',');
            return number_format($number, $decimals, $dec_point, $thousands_sep);
        } else {
            return 0;
        }
    }
}

/**
 * Custom Field Candidate
 */
if (!function_exists('civi_custom_field_candidate')) {
    function civi_custom_field_candidate($tabs,$newTab = false)
    {
        $custom_field_candidate = civi_render_custom_field('candidate');
        $candidate_id = civi_get_post_id_candidate();
        $candidate_data = get_post($candidate_id);

        $check_tabs = false;
        foreach ($custom_field_candidate as $field) {
            if ($field['tabs'] == $tabs) {
                $check_tabs = true;
            }
        }

        if(count($custom_field_candidate) > 0){
            if($newTab == true){ ?>
                <div class="row">
                    <?php foreach ($custom_field_candidate as $field) {
                        if ($field['section'] == $tabs) { ?>
                            <?php civi_get_template("dashboard/candidate/profile/additional/field.php",array(
                                'field' => $field,
                                'candidate_data' => $candidate_data
                            ));
                        }
                    } ?>
                </div>
            <?php } else {
                if ($check_tabs == true) : ?>
                    <div class="candidate-additional block-from">
                        <h6><?php esc_html_e('Additional Information', 'civi-framework') ?></h6>
                        <div class="row">
                            <?php foreach ($custom_field_candidate as $field) {
                                if ($field['tabs'] == $tabs) { ?>
                                    <?php civi_get_template("dashboard/candidate/profile/additional/field.php",array(
                                        'field' => $field,
                                        'candidate_data' => $candidate_data
                                    ));
                                }
                            } ?>
                        </div>
                    </div>
                <?php endif;
            }
        }
    }
}


/**
 * Custom Field Single Candidate
 */
if (!function_exists('civi_custom_field_single_candidate')) {
    function civi_custom_field_single_candidate($tabs,$newTab = false)
    {
        $custom_field_candidate = civi_render_custom_field('candidate');
        $candidate_id = civi_get_post_id_candidate();
        $candidate_data = get_post($candidate_id);

        $check_tabs = false;
        foreach ($custom_field_candidate as $field) {
            if ($field['tabs'] == $tabs) {
                $check_tabs = true;
            }
        }

        if(count($custom_field_candidate) > 0){
            if($newTab == true){ ?>
                <?php foreach ($custom_field_candidate as $field) {
                    if ($field['section'] == $tabs) { ?>
                        <?php civi_get_template("candidate/single/additional/field.php",array(
                            'field' => $field,
                            'candidate_data' => $candidate_data
                        ));
                    }
                } ?>
            <?php } else {
                if ($check_tabs == true) : ?>
                    <?php foreach ($custom_field_candidate as $field) {
                    if ($field['tabs'] == $tabs) { ?>
                        <?php civi_get_template("candidate/single/additional/field.php",array(
                            'field' => $field,
                            'candidate_data' => $candidate_data
                        ));
                    }} ?>
                <?php endif;
            }
        }
    }
}

/**
 * Get Data List Messages
 */
if (!function_exists('civi_get_data_list_message')) {
    function civi_get_data_list_message($first = false, $status_pending = false)
    {
        global $current_user;
        $user_id = $current_user->ID;

        $args = array(
            'post_type' => 'messages',
            'order' => 'DESC',
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => CIVI_METABOX_PREFIX . 'creator_message',
                    'value' => $user_id,
                    'compare' => '=='
                ),
                array(
                    'key' => CIVI_METABOX_PREFIX . 'reply_message',
                    'value' => $user_id,
                    'compare' => '=='
                )
            ),
        );

        if ($status_pending == true) {
            $args['post_status'] = 'pending';
        } else {
            $args['post_status'] = array('publish', 'pending');
        }

        if ($first == true) {
            $args['posts_per_page'] = 1;
        } else {
            $args['posts_per_page'] = -1;
        }

        $data = new WP_Query($args);

        return $data;
    }
}

/**
 * Get total unread message
 */
if (!function_exists('civi_get_total_unread_message')) {
    function civi_get_total_unread_message()
    {
        $data_list = civi_get_data_list_message(false, true);
        $total_unread = $data_list->found_posts;

        if ($total_unread > 0) { ?>
            <span class="badge"><?php esc_html_e($total_unread) ?></span>
        <?php } else {
            return;
        }
    }
}


/**
 * Get Data Notification
 */
if (!function_exists('civi_get_data_notification')) {
    function civi_get_data_notification()
    {
        global $current_user;
        $user_id = $current_user->ID;

        $args = array(
            'post_type' => 'notification',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => CIVI_METABOX_PREFIX . 'user_receive_noti',
                    'value' => $user_id,
                    'compare' => '=='
                ),
            ),
        );

        $data = get_posts($args);

        return $data;
    }
}

/**
 * Get Data Ajax Notification
 */
if (!function_exists('civi_get_data_ajax_notification')) {
    function civi_get_data_ajax_notification($post_current_id, $actions)
    {
        global $current_user;
        $user_id = $current_user->ID;

        $user_receive = get_post_field('post_author', $post_current_id);
        $link = get_the_permalink($post_current_id);
        $page_link = '#';

        //Action
        if (in_array("civi_user_employer", (array)$current_user->roles)
            || in_array("civi_user_candidate", (array)$current_user->roles)) {
            switch ($actions) {
                case 'add-apply':
                    $mess_noti = esc_html__('A new applicant on job', 'civi-framework');
                    $actions = esc_html__('Applicant', 'civi-framework');
                    $page_link = civi_get_permalink('applicants');
                    break;
                case 'add-message':
                    $mess_noti = esc_html__('A new message', 'civi-framework');
                    $actions = esc_html__('Message', 'civi-framework');
                    $page_link = civi_get_permalink('messages');
                    $link = '';
                    break;
                case 'add-wishlist':
                    $mess_noti = esc_html__('A new wishlist on job', 'civi-framework');
                    $actions = esc_html__('Wishlist', 'civi-framework');
                    $page_link = civi_get_permalink('my_jobs');
                    break;
                case 'add-invite':
                    $mess_noti = esc_html__('A new invite', 'civi-framework');
                    $actions = esc_html__('Invite', 'civi-framework');
                    $page_link = civi_get_permalink('my_jobs');
                    $link = '';
                    break;
                case 'add-follow-company':
                    $mess_noti = esc_html__('A new follow on company', 'civi-framework');
                    $actions = esc_html__('Follow Company', 'civi-framework');
                    $page_link = civi_get_permalink('candidates');
                    break;
                case 'add-review-company':
                    $mess_noti = esc_html__('A new review on company', 'civi-framework');
                    $page_link = '#';
                    $actions = esc_html__('Review Company', 'civi-framework');
                    break;
                case 'add-review-candidate':
                    $mess_noti = esc_html__('A new review', 'civi-framework');
                    $actions = esc_html__('Review Candidate', 'civi-framework');
                    $page_link = civi_get_permalink('candidate_reviews');
                    $link = '';
                    break;
                case 'add-follow-candidate':
                    $mess_noti = esc_html__('A new follow', 'civi-framework');
                    $actions = esc_html__('Follow Candidate', 'civi-framework');
                    $link = '';
                    $page_link = civi_get_permalink('candidate_company');
                    break;
                case 'add-meeting':
                    $mess_noti = esc_html__('A new meeting on job', 'civi-framework');
                    $actions = esc_html__('Meeting', 'civi-framework');
                    $jobs_id = get_post_meta($post_current_id, CIVI_METABOX_PREFIX . 'mee_jobs_id', true);
                    $link = get_the_permalink($jobs_id);
                    $user_receive = get_post_meta($post_current_id, CIVI_METABOX_PREFIX . 'user_receive_mee', true);
                    $page_link = civi_get_permalink('candidate_meetings');
                    break;
            }
        }

        //New
        $new_post = array(
            'post_type' => 'notification',
            'post_status' => 'publish',
        );
        $post_title = get_the_title($post_current_id);
        if (isset($post_title)) {
            $new_post['post_title'] = $post_title;
        }
        if (!empty($new_post['post_title'])) {
            $post_id = wp_insert_post($new_post, true);
        }
        if (isset($post_id)) {
            update_post_meta($post_id, CIVI_METABOX_PREFIX . 'user_send_noti', $user_id);
            update_post_meta($post_id, CIVI_METABOX_PREFIX . 'user_receive_noti', $user_receive);
            update_post_meta($post_id, CIVI_METABOX_PREFIX . 'link_post_noti', $link);
            update_post_meta($post_id, CIVI_METABOX_PREFIX . 'mess_noti', $mess_noti);
            update_post_meta($post_id, CIVI_METABOX_PREFIX . 'action_noti', $actions);
            update_post_meta($post_id, CIVI_METABOX_PREFIX . 'link_page_noti', $page_link);
        }
    }
}

/**
 * Get company founded
 */
if (!function_exists('civi_get_company_founded')) {
    function civi_get_company_founded($option = true)
    {
        global $company_meta_data;
        $founded_min = intval(civi_get_option('value_founded_min'));
        $founded_max = intval(civi_get_option('value_founded_max'));
        if (!empty($founded_min) && !empty($founded_min)) {
            if ($option) {
                for ($founded = $founded_min; $founded <= $founded_max; $founded++) { ?>
                    <option value="<?php echo $founded ?>" <?php if (isset($company_meta_data[CIVI_METABOX_PREFIX . 'company_founded'][0])) {
                        if ($company_meta_data[CIVI_METABOX_PREFIX . 'company_founded'][0] == $founded) {
                            echo 'selected';
                        }
                    } ?>><?php echo $founded ?></option>
                <?php } ?>
            <?php } else {
                $foundeds = array();
                for ($founded = $founded_min; $founded <= $founded_max; $founded++) {
                    $foundeds[] = $founded;
                };
                return array_combine($foundeds, $foundeds);
            };
        };
    }

    ;
}

/**
 * Get social network
 */
if (!function_exists('civi_get_social_network')) {
    function civi_get_social_network($id, $post_type)
    {
        $social_name = $social_icon = $social_name_field = $social_url_field = $value_icon = array();
        $civi_social_fields = civi_get_option('civi_social_fields');
        $civi_social_tabs = get_post_meta($id, CIVI_METABOX_PREFIX . $post_type . '_social_tabs');

        if (is_array($civi_social_tabs)) {
            foreach ($civi_social_tabs as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $k1 => $v1) {
                        $social_name_field[] = $v1[CIVI_METABOX_PREFIX . $post_type . '_social_name'];
                        $social_url_field[] = $v1[CIVI_METABOX_PREFIX . $post_type . '_social_url'];
                    }
                }
            }
        }

        if (is_array($civi_social_fields)) {
            foreach ($civi_social_fields as $key => $value) {
                $social_name[] = $value['social_name'];
                $social_icon[] = $value['social_icon'];
            }
        }

        $civi_social_field = array_combine($social_name, $social_icon);
        $icon_filter = array_filter(
            $civi_social_field,
            function ($key) use ($social_name_field) {
                if (in_array($key, $social_name_field)) {
                    return $social_name_field;
                }
            },
            ARRAY_FILTER_USE_KEY
        );
        ksort($icon_filter);
        $civi_social_fields = array_combine($social_name_field, $social_url_field);
        $url_filter = array_filter(
            $civi_social_fields,
            function ($key) use ($social_name_field) {
                if (in_array($key, $social_name_field)) {
                    return $social_name_field;
                }
            },
            ARRAY_FILTER_USE_KEY
        );
        ksort($url_filter);
        $value_icon = array_values($icon_filter);
        $value_url = array_values($url_filter);
        if (!empty($value_icon) && !empty($value_url)) {
            $civi_socials = array_combine($value_icon, $value_url);
            foreach ($civi_socials as $key => $value) {
                if (!empty($value)) {
                    echo '<li><a href="' . esc_url($value) . '">' . $key . '</a></li>';
                }
            }
        }
    }

    ;
}

/**
 * Image size
 */
if (!function_exists('civi_image_resize')) {
    function civi_image_resize($data, $image_size)
    {
        if (preg_match('/\d+x\d+/', $image_size)) {
            $image_sizes = explode('x', $image_size);
            $image_src = civi_image_resize_id($data, $image_sizes[0], $image_sizes[1], true);
        } else {
            if (!in_array($image_size, array('full', 'thumbnail'))) {
                $image_size = 'full';
            }
            $image_src = wp_get_attachment_image_src($data, $image_size);
            if ($image_src && !empty($image_src[0])) {
                $image_src = $image_src[0];
            }
        }
        return $image_src;
    }
}

/**
 * Image resize by url
 */
if (!function_exists('civi_image_resize_url')) {
    function civi_image_resize_url($url, $width = NULL, $height = NULL, $crop = true, $retina = false)
    {

        global $wpdb;

        if (empty($url))
            return new WP_Error('no_image_url', esc_html__('No image URL has been entered.', 'civi-framework'), $url);

        if (class_exists('Jetpack') && method_exists('Jetpack', 'get_active_modules') && in_array('photon', Jetpack::get_active_modules())) {
            $args_crop = array(
                'resize' => $width . ',' . $height,
                'crop' => '0,0,' . $width . 'px,' . $height . 'px'
            );
            $url = jetpack_photon_url($url, $args_crop);
        }

        // Get default size from database
        $width = ($width) ? $width : get_option('thumbnail_size_w');
        $height = ($height) ? $height : get_option('thumbnail_size_h');

        // Allow for different retina sizes
        $retina = $retina ? ($retina === true ? 2 : $retina) : 1;

        // Get the image file path
        $file_path = parse_url($url);
        $file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path['path'];

        // Check for Multisite
        if (is_multisite()) {
            global $blog_id;
            $blog_details = get_blog_details($blog_id);
            $file_path = str_replace($blog_details->path, '/', $file_path);
            //$file_path = str_replace( $blog_details->path . 'files/', '/wp-content/blogs.dir/' . $blog_id . '/files/', $file_path );
        }

        // Destination width and height variables
        $dest_width = $width * $retina;

        $dest_height = $height * $retina;

        // File name suffix (appended to original file name)
        $suffix = "{$dest_width}x{$dest_height}";

        // Some additional info about the image
        $info = pathinfo($file_path);
        $dir = $info['dirname'];
        $ext = $name = '';
        if (!empty($info['extension'])) {
            $ext = $info['extension'];
            $name = wp_basename($file_path, ".$ext");
        }

        if ('bmp' == $ext) {
            return new WP_Error('bmp_mime_type', esc_html__('Image is BMP. Please use either JPG or PNG.', 'civi-framework'), $url);
        }

        // Suffix applied to filename
        $suffix = "{$dest_width}x{$dest_height}";

        // Get the destination file name
        $dest_file_name = "{$dir}/{$name}-{$suffix}.{$ext}";

        if (!file_exists($dest_file_name)) {

            /*
             *  Bail if this image isn't in the Media Library.
             *  We only want to resize Media Library images, so we can be sure they get deleted correctly when appropriate.
             */
            $query = $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE guid='%s'", $url);
            $get_attachment = $wpdb->get_results($query);

            //if (!$get_attachment)
            //return array('url' => $url, 'width' => $width, 'height' => $height);

            // Load Wordpress Image Editor
            $editor = wp_get_image_editor($file_path);

            if (is_wp_error($editor))
                return array('url' => $url, 'width' => $width, 'height' => $height);

            // Get the original image size
            $size = $editor->get_size();
            $orig_width = $size['width'];
            $orig_height = $size['height'];

            $src_x = $src_y = 0;
            $src_w = $orig_width;
            $src_h = $orig_height;

            if ($crop) {

                $cmp_x = $orig_width / $dest_width;
                $cmp_y = $orig_height / $dest_height;

                // Calculate x or y coordinate, and width or height of source
                if ($cmp_x > $cmp_y) {
                    $src_w = round($orig_width / $cmp_x * $cmp_y);
                    $src_x = round(($orig_width - ($orig_width / $cmp_x * $cmp_y)) / 2);
                } else if ($cmp_y > $cmp_x) {
                    $src_h = round($orig_height / $cmp_y * $cmp_x);
                    $src_y = round(($orig_height - ($orig_height / $cmp_y * $cmp_x)) / 2);
                }
            }

            // Time to crop the image!
            $editor->crop($src_x, $src_y, $src_w, $src_h, $dest_width, $dest_height);

            // Now let's save the image
            $saved = $editor->save($dest_file_name);

            // Get resized image information
            $resized_url = str_replace(wp_basename($url), wp_basename($saved['path']), $url);
            $resized_width = $saved['width'];
            $resized_height = $saved['height'];
            $resized_type = $saved['mime-type'];

            // Add the resized dimensions to original image metadata (so we can delete our resized images when the original image is delete from the Media Library)
            if ($get_attachment) {
                if ($get_attachment[0]->ID) {
                    $metadata = wp_get_attachment_metadata($get_attachment[0]->ID);
                    if (isset($metadata['image_meta'])) {
                        $metadata['image_meta']['resized_images'][] = $resized_width . 'x' . $resized_height;
                        wp_update_attachment_metadata($get_attachment[0]->ID, $metadata);
                    }
                }
            }

            // Create the image array
            $image_array = array(
                'url' => $resized_url,
                'width' => $resized_width,
                'height' => $resized_height,
                'type' => $resized_type
            );
        } else {
            $image_array = array(
                'url' => str_replace(wp_basename($url), wp_basename($dest_file_name), $url),
                'width' => $dest_width,
                'height' => $dest_height,
                'type' => $ext
            );
        }

        // Return image array
        return $image_array;
    }
}

/*
 * Image resize by id
 */
if (!function_exists('civi_image_resize_id')) {
    function civi_image_resize_id($images_id, $width = NULL, $height = NULL, $crop = true, $retina = false)
    {
        $output = '';
        $image_src = wp_get_attachment_image_src($images_id, 'full');
        if (is_array($image_src)) {
            $resize = civi_image_resize_url($image_src[0], $width, $height, $crop, $retina);
            if ($resize != null && is_array($resize)) {
                $output = $resize['url'];
            }
        }
        return $output;
    }
}

/**
 * Get name company
 */
if (!function_exists('civi_select_post_company')) {
    function civi_select_post_company($type_option = false)
    {
        global $current_user, $jobs_meta_data;
        $user_id = $current_user->ID;
        $jobs_user_select_company = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'jobs_user_select_company', true);
        $meta_query_args = array(
            'post_type' => 'company',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        );
        if ($type_option) {
            $meta_query_args['author'] = $user_id;
        }
        $meta_query = new WP_Query($meta_query_args);
        $key_company = array("");
        $values_company = array("None");
        foreach ($meta_query->posts as $post) {
            $values_company[] = $post->post_title;
            $key_company[] = $post->ID;
        };
        if ($type_option) {
            echo '<option value="" data-url="">' . esc_html__('None', 'civi-framework') . '</option>';
            foreach ($meta_query->posts as $post) {
                $company_logo = get_post_meta($post->ID, CIVI_METABOX_PREFIX . 'company_logo', false);
                $company_logo_url = isset($company_logo[0]['url']) ? $company_logo[0]['url'] : ''; ?>
                <option <?php if ((isset($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_select_company']) && $jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_select_company'][0] == $post->ID) || (isset($jobs_user_select_company) && $jobs_user_select_company == $post->ID)) {
                    echo 'selected';
                } ?> value="<?php echo $post->ID; ?>"
                     data-url="<?php echo $company_logo_url ?>"><?php echo $post->post_title; ?>
                </option>
            <?php }
        } else {
            return array_combine($key_company, $values_company);
        }
    }
}

/**
 * Get posts company
 */
if (!function_exists('civi_posts_company')) {
    function civi_posts_company($company_id, $posts_per_page = -1)
    {
        if (empty($company_id)) return;
        $meta_query_args = array(
            'post_type' => 'jobs',
            'post_status' => 'publish',
            'posts_per_page' => $posts_per_page,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => CIVI_METABOX_PREFIX . 'jobs_select_company',
                    'value' => $company_id,
                    'compare' => '=='
                )
            ),
        );
        $meta_query = new WP_Query($meta_query_args);
        return $meta_query;
    }
}


/**
 * Get applicants status
 */
if (!function_exists('civi_applicants_status')) {
    function civi_applicants_status($id)
    {
        $applicants_status = get_post_meta($id, CIVI_METABOX_PREFIX . 'applicants_status');
        if (!empty($applicants_status)) {
            if ($applicants_status[0] == 'rejected') {
                echo '<span class="label label-close">' . esc_html__('Rejected', 'civi-framework') . '</span>';
            } elseif ($applicants_status[0] == 'approved') {
                echo '<span class="label label-open">' . esc_html__('Approved', 'civi-framework') . '</span>';
            } else {
                echo '<span class="label label-pending">' . esc_html__('Pending', 'civi-framework') . '</span>';
            }
        } else {
            echo '<span class="label label-pending">' . esc_html__('Pending', 'civi-framework') . '</span>';
        }
    }
}

/**
 * Get total post
 */
if (!function_exists('civi_total_post')) {
    function civi_total_post($post_type, $meta_key)
    {
        global $current_user;
        $user_id = $current_user->ID;
        if ($meta_key == 'my_wishlist') {
            $post_in = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'my_wishlist', true);
        } elseif ($meta_key == 'my_follow') {
            $post_in = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'my_follow', true);
        } elseif ($meta_key == 'my_invite') {
            $post_in = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'my_invite', true);
        } elseif ($meta_key == 'follow_candidate') {
            $post_in = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'follow_candidate', true);
        }

        $meta_query_args = array(
            'post_type' => $post_type,
            'post__in' => $post_in,
            'ignore_sticky_posts' => 1,
        );
        $meta_query = new WP_Query($meta_query_args);
        if (!empty($post_in) && $meta_query->found_posts > 0) {
            return $meta_query->found_posts;
        } else {
            return 0;
        }
    }
}

/**
 * Get total my apply
 */
if (!function_exists('civi_total_my_apply')) {
    function civi_total_my_apply()
    {
        global $current_user;
        $user_id = $current_user->ID;
        $args = array(
            'post_type' => 'applicants',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'author' => $user_id,
        );
        $data = new WP_Query($args);
        return $data->found_posts;
    }
}


/**
 * Get repeater social
 */
if (!function_exists('civi_get_repeater_social')) {
    function civi_get_repeater_social($social_selected, $type_option = false, $data = false)
    {
        $social_name = $social_url = array();
        $civi_social_fields = civi_get_option('civi_social_fields');
        if ($type_option) {
            echo '<option value="">' . esc_html__('None', 'civi-framework') . '</option>';
            foreach ($civi_social_fields as $social_fields) {
                if ($data) {
                    $selected = '';
                    if ($social_selected == $social_fields['social_name']) {
                        $selected = 'selected';
                    }
                    echo '<option value="' . $social_fields['social_name'] . '"' . $selected . '>' . $social_fields['social_name'] . '</option>';
                } else {
                    echo '<option value="' . $social_fields['social_name'] . '">' . $social_fields['social_name'] . '</option>';
                }
            }
        } else {
            foreach ($civi_social_fields as $social_fields) {
                $social_name[] = $social_fields['social_name'];
            };
            return array_combine($social_name, $social_name);
        }
    }
}


/**
 * Get select currency type
 */
if (!function_exists('civi_get_select_currency_type')) {
    function civi_get_select_currency_type($options_selected = false)
    {
        global $current_user, $jobs_meta_data;
        $user_id = $current_user->ID;
        $keys = $values = array();
        $options_currency_type = civi_get_option('currency_fields', true);
        $currency_type_default = civi_get_option('currency_type_default');
        $currency_sign_default = civi_get_option('currency_sign_default');
        $jobs_user_currency_type = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'jobs_user_currency_type', true);
        $jobs_currency_type = isset($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_currency_type']) ? $jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_currency_type'][0] : '';
        foreach ($options_currency_type as $key => $value) {
            $keys[] = $value['currency_sign'];
            $values[] = $value['currency_type'];
        }
        if ($options_selected) {
            echo '<option value="' . $currency_sign_default . '">' . $currency_type_default . '</option>';
            foreach ($options_currency_type as $key => $value) { ?>
                <option <?php if (!empty($options_currency_type) && ($jobs_user_currency_type == $value['currency_sign'] || $jobs_currency_type == $value['currency_sign'])) {
                    echo 'selected';
                } ?> value="<?php echo $value['currency_sign'] ?>"><?php echo $value['currency_type'] ?>
                </option>
            <?php }
        } else {
            $currency_default = array($currency_sign_default => $currency_type_default);
            $currency = array_combine($keys, $values);
            return array_merge($currency_default, $currency);
        }
    }
}


/**
 * Get Post ID Candidate
 */
if (!function_exists('civi_get_currency_type')) {
    function civi_get_post_id_candidate()
    {
        global $current_user;
        $user_id = $current_user->ID;;
        $args_candidate = array(
            'post_type' => 'candidate',
            'posts_per_page' => 1,
            'author' => $user_id,
        );
        $current_user_posts = get_posts($args_candidate);
        $candidate_id = !empty($current_user_posts) ? $current_user_posts[0]->ID : '';
        return $candidate_id;
    }
}

/**
 * Get currency type
 */
if (!function_exists('civi_get_currency_type')) {
    function civi_get_currency_type($currency = 1)
    {
        $jobs_id = get_the_ID();
        $jobs_currency_type = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_currency_type', true);
        if ($currency == 1) {
            $currency_type = $jobs_currency_type;
        } else {
            $array_key = civi_get_select_currency_type();
            $output_currency = array_filter($array_key, function ($k) {
                $jobs_id = get_the_ID();
                $jobs_currency_type = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_currency_type', true);
                return $k == $jobs_currency_type;
            }, ARRAY_FILTER_USE_KEY);
            $currency_type = $output_currency[$jobs_currency_type];
        }
        return $currency_type;
    }
}

/**
 * Get salary jobs
 */
if (!function_exists('civi_get_salary_jobs')) {
    function civi_get_salary_jobs($jobs_id)
    {
		$jobs_salary_active   = civi_get_option('enable_single_jobs_salary', '1');
		if( empty($jobs_salary_active) ){
			return;
		}
        $jobs_salary_show = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_salary_show', true);
        $jobs_salary_rate = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_salary_rate', true);
        if ($jobs_salary_rate == 'hours') {
            $jobs_salary_rate = esc_html__('/hours', 'civi-framework');
        } elseif ($jobs_salary_rate == 'days') {
            $jobs_salary_rate = esc_html__('/days', 'civi-framework');
        } elseif ($jobs_salary_rate == 'week') {
            $jobs_salary_rate = esc_html__('/week', 'civi-framework');
        } elseif ($jobs_salary_rate == 'month') {
            $jobs_salary_rate = esc_html__('/month', 'civi-framework');
        } elseif ($jobs_salary_rate == 'year') {
            $jobs_salary_rate = esc_html__('/year', 'civi-framework');
        } else {
			$jobs_salary_rate = '';
		}

        $jobs_currency_type = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_currency_type', true);
        $currency_sign_default = civi_get_option('currency_sign_default');

        $options_currency_type = civi_get_option('currency_fields', true);
        $keys = $values = array();
		if( is_array($options_currency_type) ){
			foreach ($options_currency_type as $key => $value) {
				$keys[] = $value['currency_sign'];
				$values[] = $value['currency_conversion'];
			}
		}
        $conversion_combine = array_combine($keys, $values);
        $conversion_filter = array_filter($conversion_combine, function ($k) {
            $jobs_id = get_the_ID();
            $jobs_currency_type = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_currency_type', true);
            return $k == $jobs_currency_type;
        }, ARRAY_FILTER_USE_KEY);
        if ($currency_sign_default == $jobs_currency_type) {
            $currency_conversion = 1;
        } else {
            $currency_conversion = intval(implode($conversion_filter));
            if ($currency_conversion == 0) {
                $currency_conversion = 1;
            }
        }

        $jobs_salary_minimum = civi_get_format_number(intval(get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_salary_minimum', true)) * $currency_conversion);
        $jobs_salary_maximum = civi_get_format_number(intval(get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_salary_maximum', true)) * $currency_conversion);
        $jobs_maximum_price = civi_get_format_number(intval(get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_maximum_price', true)) * $currency_conversion);
        $jobs_minimum_price = civi_get_format_number(intval(get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_minimum_price', true)) * $currency_conversion);

        $currency_position = civi_get_option('currency_position');
        $currency_leff = $currency_right = '';
        if ($currency_position == 'before') {
            $currency_leff = civi_get_currency_type();
        } else {
            $currency_right = civi_get_currency_type();
        }
        if ($jobs_salary_show == 'range') {
            $salary = sprintf("%1s%2s%s - %1s%2s%s%s", $currency_leff, $jobs_salary_minimum, $currency_right, $currency_leff, $jobs_salary_maximum, $currency_right, $jobs_salary_rate);
        } elseif ($jobs_salary_show == 'starting_amount') {
            $salary = esc_html_e('Min:', 'civi-framework') ?><?php echo $currency_leff . $jobs_minimum_price . $currency_right . $jobs_salary_rate ?>
        <?php } elseif ($jobs_salary_show == 'maximum_amount') {
            $salary = esc_html_e('Max:', 'civi-framework') ?><?php echo $currency_leff . $jobs_maximum_price . $currency_right . $jobs_salary_rate ?>
        <?php } else {
            $salary = esc_html_e('Negotiable Price', 'civi-framework') ?>
        <?php }
        return $salary;
    }
}

/**
 * Get salary candidate
 */
if (!function_exists('civi_get_salary_candidate')) {
    function civi_get_salary_candidate($candidate_id, $border_line = '/')
    {
        if (empty($candidate_id)) return;
        $offer_salary = !empty(get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_offer_salary')) ? get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_offer_salary')[0] : '';
        $salary_type = !empty(get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_salary_type')) ? get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_salary_type')[0] : '';
        $currency_type = !empty(get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_currency_type')) ? get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_currency_type')[0] : '';
        $currency_position = civi_get_option('currency_position');
        $currency_leff = $currency_right = '';
        if ($currency_position == 'before') {
            $currency_leff = $currency_type;
        } else {
            $currency_right = $currency_type;
        }
        ?>
        <?php if (!empty($offer_salary)) { ?>
        <div class="candidate-salary">
            <?php echo sprintf(__('<span>%1$s%2$s</span>%3$s%4$s%5$s'), $currency_leff, $offer_salary, $currency_right, $border_line, $salary_type); ?>
        </div>
    <?php }
    }
}

/**
 * Get expiration apply
 */
if (!function_exists('civi_get_expiration_apply')) {
    function civi_get_expiration_apply($jobs_id)
    {
        if (empty($jobs_id)) return;
        $public_date = get_the_date('Y-m-d', $jobs_id);
        $enable_jobs_expires = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'enable_jobs_expires', true);
        $current_date = date('Y-m-d');
        $jobs_days_single = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_days_closing', true);

        if ($enable_jobs_expires == '1') {
            $jobs_days_closing = '0';
        } else {
            if ($jobs_days_single) {
                $jobs_days_closing = $jobs_days_single;
            } else {
                $jobs_days_closing = civi_get_option('jobs_number_days', true);
            }
        }

        $expiration_date = date('Y-m-d', strtotime($public_date . '+' . $jobs_days_closing . ' days'));
        $seconds = strtotime($expiration_date) - strtotime($current_date);
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        $expiration_days = $dtF->diff($dtT)->format('%a');

        if ($expiration_date > $public_date && $expiration_date > $current_date) :
            return $expiration_days;
        else :
            $data = array(
                'ID' => $jobs_id,
                'post_type' => 'jobs',
                'post_status' => 'expired'
            );
            wp_update_post($data);
            update_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'enable_jobs_expires', 1);
            return 0;
        endif;
    }
}


/**
 * Get status apply
 */
if (!function_exists('civi_get_status_apply')) {
    function civi_get_status_apply($jobs_id)
    {
        if (empty($jobs_id)) return;
        global $current_user;
        $user_id = $current_user->ID;
        if (in_array('civi_user_candidate', (array)$current_user->roles)) {
            $args_candidate = array(
                'post_type' => 'candidate',
                'author' => $user_id,
            );
            $query = new WP_Query($args_candidate);
            $candidate_id = $query->post->ID;
        }
        $post_status = get_post_status($jobs_id);
        $key_apply = false;
        $jobs_select_apply = !empty(get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_select_apply')) ? get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_select_apply')[0] : '';
        $jobs_apply_external = !empty(get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_apply_external')) ? get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_apply_external')[0] : '';
        $my_apply = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'my_apply', true);
        if (!empty($my_apply)) {
            $key_apply = array_search($jobs_id, $my_apply);
        }

        $enable_apply_login = civi_get_option('enable_apply_login');
        if ($enable_apply_login == '1') {
            if ((is_user_logged_in() && in_array('civi_user_candidate', (array)$current_user->roles))) {
                if ($key_apply !== false) { ?>
                    <button class="civi-button button-disbale"><?php esc_html_e('Applied', 'civi-framework') ?></button>
                <?php } elseif ($post_status === "pause") { ?>
                    <button class="civi-button button-disbale"><?php esc_html_e('Pause', 'civi-framework') ?></button>
                <?php } elseif (civi_get_expiration_apply($jobs_id) == 0) { ?>
                    <button class="civi-button button-disbale"><?php esc_html_e('Expires', 'civi-framework') ?></button>
                <?php } else { ?>
                    <?php if ($jobs_select_apply == 'external') { ?>
                        <a href="<?php echo esc_url($jobs_apply_external) ?>" target="_blank"
                           class="civi-button"><?php esc_html_e('Apply now', 'civi-framework') ?></a>
                    <?php } else { ?>
											<?php 											
											if ( is_user_logged_in() ) { ?>
											<a href="#civi_form_apply_jobs" onClick=clickMe() 
                           class="civi-button civi-button-apply civi_form_apply_jobs"
                           data-jobs_id="<?php echo $jobs_id ?>"
                           data-candidate_id="<?php echo $candidate_id ?>"><?php esc_html_e('Apply now', 'civi-framework') ?></a>
											<?php } else { ?>
											<div class="account logged-out">
												<a href="#popup-form" class="btn-login"><?php esc_html_e('Apply now', 'civi-framework') ?></a>
											</div>
											<?php }	?>                     
                    <?php } ?>
                <?php } ?>
            <?php } else { ?>
                <div class="account logged-out">
                    <a href="#popup-form"
                       class="btn-login civi-button"><?php esc_html_e('Apply now', 'civi-framework') ?></a>
                </div>
            <?php } ?>
            <?php
        }

        if ($enable_apply_login !== '1') {
            if (is_user_logged_in()) {
                if ((in_array('civi_user_candidate', (array)$current_user->roles))) {
                    if ($key_apply !== false) { ?>
                        <button class="civi-button button-disbale"><?php esc_html_e('Applied', 'civi-framework') ?></button>
                    <?php } elseif ($post_status === "pause") { ?>
                        <button class="civi-button button-disbale"><?php esc_html_e('Pause', 'civi-framework') ?></button>
                    <?php } elseif (civi_get_expiration_apply($jobs_id) == 0) { ?>
                        <button class="civi-button button-disbale"><?php esc_html_e('Expires', 'civi-framework') ?></button>
                    <?php } else { ?>
                        <?php if ($jobs_select_apply == 'external') { ?>
                            <a href="<?php echo esc_url($jobs_apply_external) ?>" target="_blank"
                               class="civi-button"><?php esc_html_e('Apply now', 'civi-framework') ?></a>
                        <?php } else { ?>
													<?php 											
											if ( is_user_logged_in() ) { ?>
											<a href="#civi_form_apply_jobs" onClick=clickMe() 
                           class="civi-button civi-button-apply civi_form_apply_jobs"
                           data-jobs_id="<?php echo $jobs_id ?>"
                           data-candidate_id="<?php echo $candidate_id ?>"><?php esc_html_e('Apply now', 'civi-framework') ?></a>
											<?php } else { ?>
											<div class="account logged-out">
												<a href="#popup-form" class="btn-login"><?php esc_html_e('Apply now', 'civi-framework') ?></a>
											</div>
											<?php }	?>  
                        <?php } ?>
                    <?php } ?>
                <?php } else { ?>
                    <div class="account logged-out">
                        <a href="#popup-form"
                           class="btn-login civi-button"><?php esc_html_e('Apply now', 'civi-framework') ?></a>
                    </div>
                <?php }
            } else {
                if ($jobs_select_apply == 'external') { ?>
                    <a href="<?php echo esc_url($jobs_apply_external) ?>" target="_blank"
                       class="civi-button"><?php esc_html_e('Apply now', 'civi-framework') ?></a>
                <?php } else if ($jobs_select_apply == 'internal') { ?>
                    <div class="account logged-out">
                        <a href="#popup-form"
                           class="btn-login civi-button"><?php esc_html_e('Apply now', 'civi-framework') ?></a>
                    </div>
                <?php } else { ?>
                    <a href="#civi_form_apply_jobs"
                       class="civi-button civi-button-apply civi_form_apply_jobs"
                       data-jobs_id="<?php echo $jobs_id ?>"><?php esc_html_e('Apply now', 'civi-framework') ?></a>
                <?php }
            }
        }
    }
}

/**
 * Get Jobs Icon Status
 */
if (!function_exists('civi_get_icon_status')) {
    function civi_get_icon_status($jobs_id)
    {
        if (empty($jobs_id)) return;
        $jobs_meta_data = get_post_custom($jobs_id);
        $jobs_featured = isset($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_featured']) ? $jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_featured'][0] : '0';
        $enable_jobs_expires = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'enable_jobs_expires', true);
        $enable_status_urgent = civi_get_option('enable_status_urgent', '1');
        $number_status_urgent = civi_get_option('number_status_urgent', '3');
        ?>
        <?php if ($jobs_featured == '1' && civi_get_expiration_apply($jobs_id) != '0') : ?>
        <span class="tooltip featured" data-title="<?php esc_attr_e('Featured', 'civi-framework') ?>">
					<img src="<?php echo esc_attr(CIVI_PLUGIN_URL . 'assets/images/icon-featured.svg'); ?>"
                         alt="<?php echo esc_attr('featured', 'civi-framework') ?>">
				</span>
    <?php endif; ?>
        <?php if (civi_get_expiration_apply($jobs_id) == '0' && $enable_jobs_expires == '1') : ?>
        <span class="tooltip filled" data-title="<?php esc_attr_e('Filled', 'civi-framework') ?>">
					<img src="<?php echo esc_attr(CIVI_PLUGIN_URL . 'assets/images/icon-filled.svg'); ?>"
                         alt="<?php echo esc_attr('filled', 'civi-framework') ?>">
				</span>
    <?php endif; ?>
        <?php if (civi_get_expiration_apply($jobs_id) != '0' && $number_status_urgent > civi_get_expiration_apply($jobs_id) && $enable_status_urgent == '1' && $number_status_urgent !== '') : ?>
        <span class="tooltip urgent" data-title="<?php esc_attr_e('Urgent', 'civi-framework') ?>">
					<img src="<?php echo esc_attr(CIVI_PLUGIN_URL . 'assets/images/icon-urgent.svg'); ?>"
                         alt="<?php echo esc_attr('urgent', 'civi-framework') ?>">
				</span>
    <?php endif; ?>
    <?php }
}

/**
 * Get map enqueue
 */
if (!function_exists('civi_get_map_enqueue')) {
    function civi_get_map_enqueue()
    {
        $map_type = civi_get_option('map_type', 'mapbox');
        if ($map_type == 'google_map') {
            wp_enqueue_script('google-map');
        } else if ($map_type == 'mapbox') {
            wp_enqueue_style(CIVI_PLUGIN_PREFIX . 'mapbox-gl');
            wp_enqueue_style(CIVI_PLUGIN_PREFIX . 'mapbox-gl-geocoder');

            wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'mapbox-gl');
            wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'mapbox-gl-geocoder');
            wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'es6-promisel');
            wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'es6-promise');
        } else if ($map_type == 'openstreetmap') {
            wp_enqueue_style(CIVI_PLUGIN_PREFIX . 'mapbox-gl');
            wp_enqueue_style(CIVI_PLUGIN_PREFIX . 'leaflet');
            wp_enqueue_style(CIVI_PLUGIN_PREFIX . 'esri-leaflet');

            wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'mapbox-gl');
            wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'leaflet');
            wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'leaflet-src');
            wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'esri-leaflet');
            wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'esri-leaflet-geocoder');
        }
    }
}

/**
 * Get map type
 */
if (!function_exists('civi_get_map_type')) {
    function civi_get_map_type($lng, $lat, $form_submit)
    {

        $map_type = civi_get_option('map_type', 'mapbox');
        $map_zoom_level = civi_get_option('map_zoom_level', '3');
        $map_marker = CIVI_PLUGIN_URL . 'assets/images/map-marker-icon.png';
        civi_get_map_enqueue();

        if ($map_type == 'google_map') {
            wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'google-map-submit');
            wp_localize_script(
                CIVI_PLUGIN_PREFIX . 'google-map-submit',
                'civi_google_map_submit_vars',
                array(
                    'lng' => $lng,
                    'lat' => $lat,
                    'map_zoom' => $map_zoom_level,
                    'map_style' => json_encode(civi_get_option('googlemap_style')),
                    'map_type' => civi_get_option('googlemap_type', 'roadmap'),
                    'map_marker' => $map_marker,
                    'api_key' => civi_get_option('openstreetmap_api_key'),
                    'form_submit' => $form_submit,
                )
            );
        } else if ($map_type == 'openstreetmap') {
            wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'openstreet-map-submit');
            wp_localize_script(
                CIVI_PLUGIN_PREFIX . 'openstreet-map-submit',
                'civi_openstreet_map_submit_vars',
                array(
                    'lng' => $lng,
                    'lat' => $lat,
                    'map_zoom' => $map_zoom_level,
                    'map_style' => civi_get_option('openstreetmap_style', 'streets-v11'),
                    'map_marker' => $map_marker,
                    'api_key' => civi_get_option('openstreetmap_api_key'),
                    'form_submit' => $form_submit,
                )
            );
        } else {
            wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'map-box-submit');
            wp_localize_script(
                CIVI_PLUGIN_PREFIX . 'map-box-submit',
                'civi_map_box_submit_vars',
                array(
                    'lng' => $lng,
                    'lat' => $lat,
                    'map_zoom' => $map_zoom_level,
                    'map_style' => civi_get_option('mapbox_style', 'streets-v11'),
                    'map_marker' => $map_marker,
                    'api_key' => civi_get_option('mapbox_api_key'),
                    'form_submit' => $form_submit,
                )
            );
        }
    }
}

/**
 * Get single map type
 */
if (!function_exists('civi_get_single_map_type')) {
    function civi_get_single_map_type($lng, $lat)
    {

        $map_type = civi_get_option('map_type', 'mapbox');
        $map_zoom_level = civi_get_option('map_zoom_level', '3');
        $map_marker = CIVI_PLUGIN_URL . 'assets/images/map-marker-icon.png';
        civi_get_map_enqueue();

        if ($map_type == 'google_map') {
            wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'google-map-single');
            wp_localize_script(
                CIVI_PLUGIN_PREFIX . 'google-map-single',
                'civi_google_map_single_vars',
                array(
                    'lng' => $lng,
                    'lat' => $lat,
                    'map_zoom' => $map_zoom_level,
                    'map_style' => json_encode(civi_get_option('googlemap_style')),
                    'map_type' => civi_get_option('googlemap_type', 'roadmap'),
                    'api_key' => civi_get_option('openstreetmap_api_key'),
                    'map_marker' => $map_marker,
                )
            );
        } else if ($map_type == 'openstreetmap') {
            wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'openstreet-map-single');
            wp_localize_script(
                CIVI_PLUGIN_PREFIX . 'openstreet-map-single',
                'civi_openstreet_map_single_vars',
                array(
                    'lng' => $lng,
                    'lat' => $lat,
                    'map_zoom' => $map_zoom_level,
                    'map_style' => civi_get_option('openstreetmap_style', 'streets-v11'),
                    'api_key' => civi_get_option('openstreetmap_api_key'),
                    'map_marker' => $map_marker,
                )
            );
        } else {
            wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'map-box-single');
            wp_localize_script(
                CIVI_PLUGIN_PREFIX . 'map-box-single',
                'civi_map_box_single_vars',
                array(
                    'lng' => $lng,
                    'lat' => $lat,
                    'map_zoom' => $map_zoom_level,
                    'map_style' => civi_get_option('mapbox_style', 'streets-v11'),
                    'api_key' => civi_get_option('mapbox_api_key'),
                    'map_marker' => $map_marker,
                )
            );
        }
    }
}

/**
 * Get thumbnail enqueue
 */
if (!function_exists('civi_get_thumbnail_enqueue')) {
    function civi_get_thumbnail_enqueue()
    {
        wp_enqueue_script('plupload');
        wp_enqueue_script('jquery-validate');
        $thumbnail_upload_nonce = wp_create_nonce('civi_thumbnail_allow_upload');
        $thumbnail_type = civi_get_option('civi_image_type');
        $thumbnail_file_size = civi_get_option('civi_image_max_file_size', '1000kb');
        $thumbnail_url = CIVI_AJAX_URL . '?action=civi_thumbnail_upload_ajax&nonce=' . esc_attr($thumbnail_upload_nonce);
        $thumbnail_text = esc_html__('Click here', 'civi-framework');

        wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'thumbnail');
        wp_localize_script(
            CIVI_PLUGIN_PREFIX . 'thumbnail',
            'civi_thumbnail_vars',
            array(
                'ajax_url' => CIVI_AJAX_URL,
                'thumbnail_title' => esc_html__('Valid file formats', 'civi-framework'),
                'thumbnail_type' => $thumbnail_type,
                'thumbnail_file_size' => $thumbnail_file_size,
                'thumbnail_upload_nonce' => $thumbnail_upload_nonce,
                'thumbnail_url' => $thumbnail_url,
                'thumbnail_text' => $thumbnail_text,
            )
        );
    }
}

/**
 * Get avatar enqueue
 */
if (!function_exists('civi_get_avatar_enqueue')) {
    function civi_get_avatar_enqueue()
    {
        wp_enqueue_script('plupload');
        wp_enqueue_script('jquery-validate');
        $avatar_upload_nonce = wp_create_nonce('civi_avatar_allow_upload');
        $avatar_type = civi_get_option('civi_image_type');
        $avatar_file_size = civi_get_option('civi_image_max_file_size', '1000kb');
        $avatar_url = CIVI_AJAX_URL . '?action=civi_avatar_upload_ajax&nonce=' . esc_attr($avatar_upload_nonce);
        $avatar_text = esc_html__('Upload', 'civi-framework');

        wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'avatar');
        wp_localize_script(
            CIVI_PLUGIN_PREFIX . 'avatar',
            'civi_avatar_vars',
            array(
                'ajax_url' => CIVI_AJAX_URL,
                'avatar_title' => esc_html__('Valid file formats', 'civi-framework'),
                'avatar_type' => $avatar_type,
                'avatar_file_size' => $avatar_file_size,
                'avatar_upload_nonce' => $avatar_upload_nonce,
                'avatar_url' => $avatar_url,
                'avatar_text' => $avatar_text,
            )
        );
    }
}

/**
 * Get gallery enqueue
 */
if (!function_exists('civi_get_gallery_enqueue')) {
    function civi_get_gallery_enqueue()
    {
        wp_enqueue_script('plupload');
        wp_enqueue_script('jquery-ui-sortable');
        $gallery_upload_nonce = wp_create_nonce('civi_gallery_allow_upload');
        $gallery_type = civi_get_option('civi_image_type');
        $gallery_file_size = civi_get_option('civi_image_max_file_size', '1000kb');
        $gallery_max_images = civi_get_option('civi_max_gallery_images', 5);
        $gallery_url = CIVI_AJAX_URL . '?action=civi_gallery_upload_ajax&nonce=' . esc_attr($gallery_upload_nonce);

        wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'gallery');
        wp_localize_script(
            CIVI_PLUGIN_PREFIX . 'gallery',
            'civi_gallery_vars',
            array(
                'ajax_url' => CIVI_AJAX_URL,
                'gallery_title' => esc_html__('Valid file formats', 'civi-framework'),
                'gallery_type' => $gallery_type,
                'gallery_file_size' => $gallery_file_size,
                'gallery_max_images' => $gallery_max_images,
                'gallery_upload_nonce' => $gallery_upload_nonce,
                'gallery_url' => $gallery_url,
            )
        );
    }
}

/**
 * Format money
 */
if (!function_exists('civi_get_format_money')) {
    function civi_get_format_money($money, $price_unit = '', $decimals = 0, $small_sign = false, $is_currency_sign = true)
    {
        $formatted_price = $money;
        $money = doubleval($money);
        if ($money) {
            $dec_point = civi_get_option('decimal_separator', '.');
            $thousands_sep = civi_get_option('thousand_separator', ',');

            $price_unit = intval($price_unit);
            $formatted_price = number_format($money, $decimals, $dec_point, $thousands_sep);

            $currency_type = $currency_sign = '';
            if ($is_currency_sign == true) {
                $currency_sign = civi_get_option('currency_sign_default');
                $currency = !empty($currency_sign) ? $currency_sign : '';
            } else {
                $currency_type = civi_get_option('currency_type_default');
                $currency = !empty($currency_type) ? $currency_type : '';
            }

            if ($small_sign == true) {
                $currency = '<sup>' . $currency . '</sup>';
            }
            $currency_position = civi_get_option('currency_position', 'before');
            if ($currency_position == 'before') {
                return $currency . $formatted_price;
            } else {
                return $formatted_price . $currency;
            }
        } else {
            $currency = 0;
        }
        return $currency;
    }
}

/**
 * Get total reviews
 */
if (!function_exists('civi_get_total_reviews')) {
    function civi_get_total_reviews()
    {
        global $wpdb, $current_user;
        $user_id = $current_user->ID;
        $my_reviews = $wpdb->get_results("SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.user_id = $user_id AND meta.meta_key = 'company_rating' AND meta.comment_id = comment.comment_ID ORDER BY comment.comment_ID DESC LIMIT 999");
        $company_ids = array();
        foreach ($my_reviews as $my_review) {
            $company_ids[] = $my_review->comment_post_ID;
        }

        $args = array(
            'post_type' => 'company',
            'post__in' => $company_ids,
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
        );

        $data = new WP_Query($args);
        if (!empty($company_ids)) {
            $total_post = $data->found_posts;
        } else {
            $total_post = 0;
        }
        return $total_post;
    }
}

/**
 * Get total rating
 */
if (!function_exists('civi_get_total_rating')) {
    function civi_get_total_rating($post_type, $id)
    {
        global $wpdb;
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $rating = $total_reviews = $total_stars = 0;

        if ($post_type == 'company') {
            $comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $id AND meta.meta_key = 'company_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 OR comment.user_id = $user_id )";
        } elseif ($post_type == 'candidate') {
            $comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $id AND meta.meta_key = 'candidate_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 OR comment.user_id = $user_id )";
        }
        $get_comments = $wpdb->get_results($comments_query);
        if (!is_null($get_comments)) {
            foreach ($get_comments as $comment) {
                if ($comment->comment_approved == 1) {
                    if (!empty($comment->meta_value) && $comment->meta_value != 0.00) {
                        $total_reviews++;
                    }
                    if ($comment->meta_value > 0) {
                        $total_stars += $comment->meta_value;
                    }
                }
            }
            if ($total_reviews != 0) {
                $rating = number_format($total_stars / $total_reviews, 1);
            }
        }
        update_post_meta($id, 'civi-' . $post_type . '_rating', $rating);
        ?>

        <div class="civi-rating-warpper">
				<span class="rating-count">
					<i class="fas fa-star"></i>
					<span><?php esc_html_e($rating); ?>
					</span>
				</span>
            <span class="review-count"><?php printf(_n('(%s Review)', '(%s Reviews)', $total_reviews, 'civi-framework'), $total_reviews); ?></span>
        </div>

    <?php }
}


/**
 * Get comment time
 */
if (!function_exists('civi_get_comment_time')) {
    function civi_get_comment_time($comment_id = 0)
    {
        return sprintf(
            _x('%s ago', 'Human-readable time', 'civi-framework'),
            human_time_diff(
                get_comment_date('U', $comment_id),
                current_time('timestamp')
            )
        );
    }
}

/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @access public
 * @param string $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 */
if (!function_exists('civi_get_template')) {
    function civi_get_template($template_name, $args = array(), $template_path = '', $default_path = '')
    {
        if (!empty($args) && is_array($args)) {
            extract($args);
        }

        $located = civi_locate_template($template_name, $template_path, $default_path);

        if (!file_exists($located)) {
            _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $located), '2.1');
            return;
        }

        // Allow 3rd party plugin filter template file from their plugin.
        $located = apply_filters('civi_get_template', $located, $template_name, $args, $template_path, $default_path);

        do_action('civi_before_template_part', $template_name, $template_path, $located, $args);

        include($located);

        do_action('civi_after_template_part', $template_name, $template_path, $located, $args);
    }
}

/**
 * Locate a template and return the path for inclusion.
 */
if (!function_exists('civi_locate_template')) {
    function civi_locate_template($template_name, $template_path = '', $default_path = '')
    {
        if (!$template_path) {
            $template_path = CIVI()->template_path();
        }

        if (!$default_path) {
            $default_path = CIVI_PLUGIN_DIR . 'templates/';
        }

        // Look within passed path within the theme - this is priority.
        $template = locate_template(
            array(
                trailingslashit($template_path) . $template_name,
                $template_name
            )
        );

        // Get default template/
        if (!$template) {
            $template = $default_path . $template_name;
        }

        // Return what we found.
        return apply_filters('civi_locate_template', $template, $template_name, $template_path);
    }
}

/**
 * civi_get_jobs_by_category
 */
if (!function_exists('civi_get_jobs_by_category')) {
    function civi_get_jobs_by_category($total = 3, $show = 3, $category = 0)
    {
        $exclude = '';
        if (is_single()) {
            $exclude = get_the_ID();
        }
        $args = array(
            'posts_per_page' => $total,
            'post_type' => 'jobs',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'exclude' => $exclude,
            'orderby' => array(
                'menu_order' => 'ASC',
                'date' => 'DESC',
            ),
            'tax_query' => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'jobs-categories',
                    'field' => 'id',
                    'terms' => $category
                )
            ),
            'meta_query' => array(
                array(
                    'key' => CIVI_METABOX_PREFIX . 'enable_jobs_package_expires',
                    'value' => 0,
                    'compare' => '=='
                )
            ),
        );
        $job = get_posts($args);
        ob_start();
        ?>
        <?php foreach ($job as $jobs) { ?>
        <?php civi_get_template('content-jobs.php', array(
            'jobs_id' => $jobs->ID,
            'jobs_layout' => 'layout-list',
        )); ?>
    <?php } ?>
        <?php
        return ob_get_clean();
    }
}

/**
 * get_taxonomy
 */
if (!function_exists('civi_get_taxonomy')) {
    function civi_get_taxonomy($taxonomy_name, $value_as_slug = false, $show_default_none = true, $render_array = false)
    {
        global $current_user;
        $user_id = $current_user->ID;
        $taxonomy_terms = get_categories(
            array(
                'taxonomy' => $taxonomy_name,
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => false,
                'parent' => 0
            )
        );

        if ($render_array) {
            $list = array(
                '' => esc_html('Select an option', 'civi-framework')
            );
            foreach ($taxonomy_terms as $term) {
                $list[$term->term_id] = $term->name;
            }
            return $list;
        } else {
            if ($show_default_none) {
                echo '<option value="">' . esc_html__('Select an option', 'civi-framework') . '</option>';
            }
            if (!empty($taxonomy_terms)) {
                if ($value_as_slug) {
                    foreach ($taxonomy_terms as $term) {
                        echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
                    }
                } else {
                    foreach ($taxonomy_terms as $term) {
                        $jobs_user = get_user_meta($user_id, CIVI_METABOX_PREFIX . $taxonomy_name . '_user');
                        $jobs_user = !empty($jobs_user) ? $jobs_user[0] : '';
                        if (!empty($jobs_user)) { ?>
                            <?php if ($show_default_none) { ?>
                                <option <?php if (!empty($jobs_user) && $jobs_user == $term->term_id) {
                                    echo 'selected';
                                } ?> value="<?php echo $term->term_id ?>"><?php echo $term->name ?></option>';
                            <?php } else { ?>
                                <?php foreach ($jobs_user as $key => $value) { ?>
                                    <option <?php if ($value == $term->term_id) {
                                        echo 'selected';
                                    } ?> value="<?php echo $term->term_id ?>"><?php echo $term->name ?>
                                    </option>
                                <?php } ?>
                            <?php } ?>
                        <?php } else { ?>
							<option <?php if( isset($_GET[$taxonomy_name]) && $_GET[$taxonomy_name] == $term->slug ) { echo 'selected'; } ?> value="<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></option>
							<?php
                        }
                    }
                }
            }
        }
    }
}

/**
 * Get taxonomy slug by post id
 */
if (!function_exists('civi_get_taxonomy_slug_by_post_id')) {
    function civi_get_taxonomy_slug_by_post_id($post_id, $taxonomy_name)
    {
        $tax_terms = get_the_terms($post_id, $taxonomy_name);
        if (!empty($tax_terms)) {
            foreach ($tax_terms as $tax_term) {
                return $tax_term->slug;
            }
        }
        return null;
    }
}

/**
 * civi_get_taxonomy_slug
 */
if (!function_exists('civi_get_taxonomy_slug')) {
    function civi_get_taxonomy_slug($taxonomy_name, $target_term_slug = '', $prefix = '')
    {
        $taxonomy_terms = get_categories(
            array(
                'taxonomy' => $taxonomy_name,
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => false,
                'parent' => 0
            )
        );

        if (!empty($taxonomy_terms)) {
            foreach ($taxonomy_terms as $term) {
                if ($target_term_slug == $term->slug) {
                    echo '<option value="' . $term->slug . '" selected>' . $prefix . $term->name . '</option>';
                } else {
                    echo '<option value="' . $term->slug . '">' . $prefix . $term->name . '</option>';
                }
            }
        }
    }
}

/**
 * get_taxonomy_by_post_id
 */
if (!function_exists('civi_get_taxonomy_by_post_id')) {
    function civi_get_taxonomy_by_post_id($post_id, $taxonomy_name, $show_default_none = true, $is_target_by_name = false)
    {
        $taxonomy_terms = get_categories(
            array(
                'taxonomy' => $taxonomy_name,
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => false,
                'parent' => 0
            )
        );
        $target_by_name = array();
        $target_by_id = array();
        $tax_terms = get_the_terms($post_id, $taxonomy_name);
        if ($is_target_by_name) {
            if (!empty($tax_terms)) {
                foreach ($tax_terms as $tax_term) {
                    $target_by_name[] = $tax_term->name;
                }
            }
            if ($show_default_none) {
                if (empty($target_by_name)) {
                    echo '<option value="" selected>' . esc_html__('None', 'civi-framework') . '</option>';
                } else {
                    echo '<option value="">' . esc_html__('None', 'civi-framework') . '</option>';
                }
            }
            civi_get_taxonomy_target_by_name($taxonomy_terms, $target_by_name);
        } else {	
            if (!empty($tax_terms)) {
                foreach ($tax_terms as $tax_term) {
                    $target_by_id[] = $tax_term->term_id;
                }
            }
            if ($show_default_none) {
                if ($target_by_id == 0 || empty($target_by_id)) {
                    echo '<option value="" selected>' . esc_html__('Select an option', 'civi-framework') . '</option>';
                } else {
                    echo '<option value="">' . esc_html__('Select an option', 'civi-framework') . '</option>';
                }
            }
            civi_get_taxonomy_target_by_id($taxonomy_terms, $target_by_id);
        }
    }
}

/**
 * get_taxonomy_target_by_name
 */
if (!function_exists('civi_get_taxonomy_target_by_name')) {
    function civi_get_taxonomy_target_by_name($taxonomy_terms, $target_term_name, $prefix = "")
    {
        if (!empty($taxonomy_terms)) {
            foreach ($taxonomy_terms as $term) {
                if (in_array($term->name, $target_term_name)) {
                    echo '<option value="' . $term->slug . '" selected>' . $prefix . $term->name . '</option>';
                } else {
                    echo '<option value="' . $term->slug . '">' . $prefix . $term->name . '</option>';
                }
            }
        }
    }
}

/**
 * get_taxonomy_target_by_id
 */
if (!function_exists('civi_get_taxonomy_target_by_id')) {
    function civi_get_taxonomy_target_by_id($taxonomy_terms, $target_term_id, $prefix = "")
    {
        if (!empty($taxonomy_terms)) {
            foreach ($taxonomy_terms as $term) {
                if (in_array($term->term_id, $target_term_id)) {
                    echo '<option value="' . $term->term_id . '" selected>' . $prefix . $term->name . '</option>';
                } else {
                    echo '<option value="' . $term->term_id . '">' . $prefix . $term->name . '</option>';
                }
            }
        }
    }
}

if (!function_exists('civi_server_protocol')) {
    function civi_server_protocol()
    {
        if (is_ssl()) {
            return 'https://';
        }
        return 'http://';
    }
}


/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param string|array $var Data to sanitize.
 * @return string|array
 */
if (!function_exists('civi_clean')) {
    function civi_clean($var)
    {
        if (is_array($var)) {
            return array_map('civi_clean', $var);
        } else {
            return is_scalar($var) ? sanitize_text_field($var) : $var;
        }
    }
}

if (!function_exists('civi_clean_double_val')) {
    function civi_clean_double_val($string)
    {
        $string = preg_replace('/&#36;/', '', $string);
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        $string = preg_replace('/\D/', '', $string);
        return $string;
    }
}


if (!function_exists('civi_render_custom_field')) {
    function civi_render_custom_field($post_type)
    {
       if($post_type == 'company'){
           $form_fields = civi_get_option('custom_field_company');
       } elseif($post_type == 'candidate') {
           $form_fields = civi_get_option('custom_field_candidate');
       } else {
           $form_fields = civi_get_option('custom_field_jobs');
       }

        $meta_prefix = CIVI_METABOX_PREFIX;

        $configs = array();
        if ($form_fields && is_array($form_fields)) {
            foreach ($form_fields as $key => $field) {
                if (!empty($field['label'])) {
                    $type = $field['field_type'];
                    $config = array(
                        'title' => $field['label'],
                        'id' => $meta_prefix . sanitize_title($field['label']),
                        'type' => $type,
                    );
                    $first_opt = '';
                    switch ($type) {
                        case 'checkbox_list':
                        case 'select':
                            $options = array();
                            $options_arr = isset($field['select_choices']) ? $field['select_choices'] : '';
                            $options_arr = str_replace("\r\n", "\n", $options_arr);
                            $options_arr = str_replace("\r", "\n", $options_arr);
                            $options_arr = explode("\n", $options_arr);
                            $first_opt = !empty($options_arr) ? $options_arr[0] : '';
                            foreach ($options_arr as $opt_value) {
                                $options[$opt_value] = $opt_value;
                            }

                            $config['options'] = $options;
                            break;
                    }

                    if ($post_type == 'candidate') {
                        $config['tabs'] = $field['tabs'];
                        $config['section'] = $field['section'];
                    }

                    if (in_array($type, array('select'))) {
                        $config['default'] = $first_opt;
                    }
                    $configs[] = $config;
                }
            }
        }
        return $configs;
    }
}


//GET SEARCH FILTER ITEM
if (!function_exists('get_search_filter_submenu')) {
    function get_search_filter_submenu($taxonomy_name, $title, $load_children = true)
    {

        if (isset($_GET[$taxonomy_name . '_id'])) {
            $tax_selected_id_list = civi_clean(wp_unslash($_GET[$taxonomy_name . '_id']));
        } else {
            $tax_selected_id_list = array();
        }

        $class_list_wrapper = 'filter-control custom-scrollbar ' . $taxonomy_name;

        $submenu_arg = array(
            'taxonomy_name' => $taxonomy_name,
            'taxonomy_parent_id' => 0,
            'tax_selected_id_list' => $tax_selected_id_list,
            'class_list_wrapper' => $class_list_wrapper,
        );

        $class_wrapper = 'filter-' . $taxonomy_name;

        ?>

        <div class="<?php echo $class_wrapper ?>">
            <div class="entry-filter">
                <h4><?php echo esc_attr($title) ?></h4>
                <?php echo render_item_checkbox($submenu_arg, $load_children); ?>
            </div>
        </div>

        <?php

    }
}

//GET CHECKBOX ITEM FOR SUBMENU
if (!function_exists('render_item_checkbox')) {
    function render_item_checkbox($submenu_arg = array(), $load_children = true)
    {
        $taxonomy_name = $submenu_arg['taxonomy_name'];
        $taxonomy_parent_id = $submenu_arg['taxonomy_parent_id'];
        $tax_selected_id_list = $submenu_arg['tax_selected_id_list'];
        $class_list_wrapper = $submenu_arg['class_list_wrapper'];

        $taxonomy_object_list = get_categories(array(
            'taxonomy' => $taxonomy_name,
            'hide_empty' => 0,
            'orderby' => 'ID',
            'order' => 'ASC',
            'parent' => $taxonomy_parent_id,
        ));

        if (empty($taxonomy_object_list)) {
            return;
        }

        $list = '<ul class="' . $class_list_wrapper . '">';
        $list_item = '';

        foreach ($taxonomy_object_list as $term_object) {
            $check = '';
            if (in_array($term_object->term_id, $tax_selected_id_list)) {
                $check = 'checked';
            }

            $list_item = '<li>';
            $list_item .= '<input type="checkbox" class="custom-checkbox input-control" name="' . $taxonomy_name . '_id[]" value="' . $term_object->term_id . '" id="civi_' . $term_object->term_id . '"' . $check . '/>';

            $list_item .= '<label for="civi_' . esc_attr($term_object->term_id) . '">' . esc_html($term_object->name) . '</label>';

            if ($load_children) {
                $submenu_arg['class_list_wrapper'] = '';
                $submenu_arg['taxonomy_parent_id'] = $term_object->term_id;
                $list_item .= render_item_checkbox($submenu_arg);
            }

            $list_item .= '</li>';

            $list .= $list_item;
        }

        return $list .= '</ul>';
    }
}
