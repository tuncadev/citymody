<?php
global $wpdb;

wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'company-review');
wp_localize_script(
    CIVI_PLUGIN_PREFIX . 'company-review',
    'civi_company_review_vars',
    array(
        'ajax_url'  => CIVI_AJAX_URL,
    )
);
$rating = $total_reviews = $total_stars = 0;
$no_avatar_src = '';
$current_user = wp_get_current_user();
$user_id      = $current_user->ID;
$company_id     = get_the_ID();
$company_rating = get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_rating', true);

$comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $company_id AND meta.meta_key = 'company_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 OR comment.user_id = $user_id )";
$get_comments   = $wpdb->get_results($comments_query);
$my_review      = $wpdb->get_row("SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $company_id AND comment.user_id = $user_id AND meta.meta_key = 'company_rating' AND meta.comment_id = comment.comment_ID ORDER BY comment.comment_ID DESC");
$my_review_company_salary_rating = $my_review_company_company_rating  = $my_review_company_skill_rating = $my_review_company_work_rating = '';
$salary_rating_class = $company_rating_class = $skill_rating_class = $work_rating_class = '';

if (!is_null($get_comments)) {

    $company_salary_rating = $company_company_rating = $company_skill_rating = $company_work_rating = array();
    foreach ($get_comments as $comment) {
        if (intval(get_comment_meta($comment->comment_ID, 'company_salary_rating', true)) != 0) {
            $company_salary_rating[]         = intval(get_comment_meta($comment->comment_ID, 'company_salary_rating', true));
        }
        if (intval(get_comment_meta($comment->comment_ID, 'company_company_rating', true)) != 0) {
            $company_company_rating[]         = intval(get_comment_meta($comment->comment_ID, 'company_company_rating', true));
        }
        if (intval(get_comment_meta($comment->comment_ID, 'company_skill_rating', true)) != 0) {
            $company_skill_rating[]         = intval(get_comment_meta($comment->comment_ID, 'company_skill_rating', true));
        }
        if (intval(get_comment_meta($comment->comment_ID, 'company_work_rating', true)) != 0) {
            $company_work_rating[]         = intval(get_comment_meta($comment->comment_ID, 'company_work_rating', true));
        }

        if ($comment->comment_approved == 1) {
            if (!empty($comment->meta_value) && $comment->meta_value != 0.00) {
                $total_reviews++;
            }
            if ($comment->meta_value > 0) {
                $total_stars += $comment->meta_value;
            }
        }

        if (isset($my_review) ? $comment->comment_ID : 0) {
            if ($comment->comment_ID == $my_review->comment_ID) {
                $my_review_company_salary_rating = intval(get_comment_meta($comment->comment_ID, 'company_salary_rating', true));
                $my_review_company_company_rating = intval(get_comment_meta($comment->comment_ID, 'company_company_rating', true));
                $my_review_company_skill_rating = intval(get_comment_meta($comment->comment_ID, 'company_skill_rating', true));
                $my_review_company_work_rating = intval(get_comment_meta($comment->comment_ID, 'company_work_rating', true));
            }
        }
    }

    if ($total_reviews != 0) {
        $rating = number_format($total_stars / $total_reviews, 1);
    }

    if (!empty($company_salary_rating)) {
        $salary_rating = array_sum($company_salary_rating) / count($company_salary_rating);
        $salary_rating = number_format((float)$salary_rating, 2, '.', '');
        $salary_rating_percent = ($salary_rating / 5) * 100;
        if ($salary_rating_percent >= 0 && $salary_rating_percent <= 30) {
            $salary_rating_class = 'low';
        } else if ($salary_rating_percent >= 31 && $salary_rating_percent <= 70) {
            $salary_rating_class = 'mid';
        } else if ($salary_rating_percent >= 71 && $salary_rating_percent <= 100) {
            $salary_rating_class = 'high';
        }
    } else {
        $salary_rating = 0;
        $salary_rating_percent = 0;
    }

    if (!empty($company_company_rating)) {
        $company_rating = array_sum($company_company_rating) / count($company_company_rating);
        $company_rating = number_format((float)$company_rating, 2, '.', '');
        $company_rating_percent = ($company_rating / 5) * 100;
        if ($company_rating_percent >= 0 && $company_rating_percent <= 30) {
            $company_rating_class = 'low';
        } else if ($company_rating_percent >= 31 && $company_rating_percent <= 70) {
            $company_rating_class = 'mid';
        } else if ($company_rating_percent >= 71 && $company_rating_percent <= 100) {
            $company_rating_class = 'high';
        }
    } else {
        $company_rating = 0;
        $company_rating_percent = 0;
    }

    if (!empty($company_skill_rating)) {
        $skill_rating = array_sum($company_skill_rating) / count($company_skill_rating);
        $skill_rating = number_format((float)$skill_rating, 2, '.', '');
        $skill_rating_percent = ($skill_rating / 5) * 100;
        if ($skill_rating_percent >= 0 && $skill_rating_percent <= 30) {
            $skill_rating_class = 'low';
        } else if ($skill_rating_percent >= 31 && $skill_rating_percent <= 70) {
            $skill_rating_class = 'mid';
        } else if ($skill_rating_percent >= 71 && $skill_rating_percent <= 100) {
            $skill_rating_class = 'high';
        }
    } else {
        $skill_rating = 0;
        $skill_rating_percent = 0;
    }

    if (!empty($company_work_rating)) {
        $work_rating = array_sum($company_work_rating) / count($company_work_rating);
        $work_rating = number_format((float)$work_rating, 2, '.', '');
        $work_rating_percent = ($work_rating / 5) * 100;
        if ($work_rating_percent >= 0 && $work_rating_percent <= 30) {
            $work_rating_class = 'low';
        } else if ($work_rating_percent >= 31 && $work_rating_percent <= 70) {
            $work_rating_class = 'mid';
        } else if ($work_rating_percent >= 71 && $work_rating_percent <= 100) {
            $work_rating_class = 'high';
        }
    } else {
        $work_rating = 0;
        $work_rating_percent = 0;
    }
}

?>
<div class="company-review-details block-archive-inner" id="company-review-details">
    <h3 class="title-company"><?php esc_html_e('Review', 'civi-framework'); ?></h3>
    <div class="entry-heading">
        <span class="rating-count">
            <span><?php esc_html_e($rating); ?></span>
            <i class="fas fa-star"></i>
        </span>
        <span class="review-count"><?php printf(_n('Base on %s reviews', 'Base on %s reviews', $total_reviews, 'civi-framework'), $total_reviews); ?></span>
    </div>
    <div class="entry-overview">
        <div class="rating-bars">
            <div class="rating-bars-item">
                <div class="rating-bars-name">
                    <?php esc_html_e('Salary & Benefits', 'civi-framework'); ?>
                    <div class="tip" data-tip-content="<?php esc_html_e('Salary review every 6 months based on the work performance', 'civi-framework'); ?>">
                        <div class="tip-content"><?php esc_html_e('Salary review every 6 months based on the work performance', 'civi-framework'); ?></div>
                    </div>
                </div>
                <div class="rating-bars-inner">
                    <span class="rating-bars-rating <?php echo esc_attr($salary_rating_class); ?>" data-rating="<?php echo esc_attr($salary_rating); ?>">
                        <span class="rating-bars-rating-inner" style="width: <?php echo esc_attr($salary_rating_percent); ?>%;"></span>
                    </span>
                    <span class="value-rating"><?php echo esc_attr($salary_rating); ?></span>
                </div>
            </div>
            <div class="rating-bars-item">
                <div class="rating-bars-name">
                    <?php esc_html_e('Company Culture', 'civi-framework'); ?>
                    <div class="tip" data-tip-content="<?php esc_html_e('Company trip once a year and Team building once a month', 'civi-framework'); ?>">
                        <div class="tip-content"><?php esc_html_e('Company trip once a year and Team building once a month', 'civi-framework'); ?></div>
                    </div>
                </div>
                <div class="rating-bars-inner">
                    <span class="rating-bars-rating <?php echo esc_attr($company_rating_class); ?>" data-rating="<?php echo esc_attr($company_rating); ?>">
                        <span class="rating-bars-rating-inner" style="width: <?php echo esc_attr($company_rating_percent); ?>%;"></span>
                    </span>
                    <span class="value-rating"><?php echo esc_attr($company_rating); ?></span>
                </div>
            </div>
            <div class="rating-bars-item">
                <div class="rating-bars-name">
                    <?php esc_html_e('Skill Development', 'civi-framework'); ?>
                    <div class="tip" data-tip-content="<?php esc_html_e('Well trained and dedicated to being able to catch the pace smoothly.', 'civi-framework'); ?>">
                        <div class="tip-content"><?php esc_html_e('Well trained and dedicated to being able to catch the pace smoothly.', 'civi-framework'); ?></div>
                    </div>
                </div>
                <span class="rating-bars-inner">
                    <span class="rating-bars-rating <?php echo esc_attr($skill_rating_class); ?>" data-rating="<?php echo esc_attr($skill_rating); ?>">
                        <span class="rating-bars-rating-inner" style="width: <?php echo esc_attr($skill_rating_percent); ?>%;"></span>
                    </span>
                    <span class="value-rating"><?php echo esc_attr($skill_rating); ?></span>
                </span>
            </div>
            <div class="rating-bars-item">
                <div class="rating-bars-name">
                    <?php esc_html_e('Work Satisfaction', 'civi-framework'); ?>
                    <div class="tip" data-tip-content="<?php esc_html_e('Our office is located with creative, open workspaces and a high-quality engaging environment.', 'civi-framework'); ?>">
                        <div class="tip-content"><?php esc_html_e('Our office is located with creative, open workspaces and a high-quality engaging environment.', 'civi-framework'); ?></div>
                    </div>
                </div>
                <div class="rating-bars-inner">
                    <span class="rating-bars-rating <?php echo esc_attr($work_rating_class); ?>" data-rating="<?php echo esc_attr($work_rating); ?>">
                        <span class="rating-bars-rating-inner" style="width: <?php echo esc_attr($work_rating_percent); ?>%;"></span>
                    </span>
                    <span class="value-rating"><?php echo esc_attr($work_rating); ?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="entry-detail">
        <ul class="reviews-list">
            <?php if (!is_null($get_comments)) {
                foreach ($get_comments as $comment) {
                    $comment_id        = $comment->comment_ID;
                    $author_avatar_url = get_avatar_url($comment->user_id, ['size' => '50']);
                    $author_avatar_image_url = get_the_author_meta('author_avatar_image_url', $comment->user_id);
                    if (!empty($author_avatar_image_url)) {
                        $author_avatar_url = $author_avatar_image_url;
                    }
                    $user_link = get_author_posts_url($comment->user_id);

                    $comment_thumb = get_comment_meta($comment->comment_ID, 'comment_thumb', true);

            ?>
                    <li class="author-review">
                        <div class="entry-head">
                            <div class="entry-avatar">
                                <figure>
                                    <?php
                                    if (!empty($author_avatar_url)) {
                                    ?>
                                        <a href="<?php echo esc_url($user_link); ?>">
                                            <img src="<?php echo esc_url($author_avatar_url); ?>" alt="<?php the_author_meta('display_name', $comment->user_id); ?>">
                                        </a>
                                    <?php
                                    } else {
                                    ?>
                                        <a href="<?php echo esc_url($user_link); ?>">
                                            <img src="<?php echo esc_url($no_avatar_src); ?>" alt="<?php the_author_meta('display_name', $comment->user_id); ?>"></a>
                                    <?php
                                    }
                                    ?>
                                </figure>
                            </div>
                            <div class="entry-info">
                                <div class="entry-name">
                                    <h4 class="author-name"><a href="<?php echo esc_url($user_link); ?>"><?php the_author_meta('display_name', $comment->user_id); ?></a></h4>
                                    <span class="review-date"><?php echo civi_get_comment_time($comment->comment_ID); ?></span>
                                </div>
                                <?php if ($comment->meta_value > 0) : ?>
                                    <div class="author-rating">
                                        <span class="star <?php if ($comment->meta_value >= 1) : echo 'checked';
                                                            endif; ?>">
                                            <i class="fas fa-star"></i>
                                        </span>
                                        <span class="star <?php if ($comment->meta_value >= 2) : echo 'checked';
                                                            endif; ?>">
                                            <i class="fas fa-star"></i>
                                        </span>
                                        <span class="star <?php if ($comment->meta_value >= 3) : echo 'checked';
                                                            endif; ?>">
                                            <i class="fas fa-star"></i>
                                        </span>
                                        <span class="star <?php if ($comment->meta_value >= 4) : echo 'checked';
                                                            endif; ?>">
                                            <i class="fas fa-star"></i>
                                        </span>
                                        <span class="star <?php if ($comment->meta_value == 5) : echo 'checked';
                                                            endif; ?>">
                                            <i class="fas fa-star"></i>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="entry-comment civi-review-details">
                            <p class="review-content civi-review"><?php echo wp_kses_post($comment->comment_content); ?></p>
                            <div class="toggle-review">
                                <a href="#" class="show-more-review"><?php esc_html_e('Show more', 'civi-framework'); ?><i class="fas fa-angle-down"></i></a>
                                <a href="#" class="hide-all-review"><?php esc_html_e('Hide less', 'civi-framework'); ?><i class="fas fa-angle-up"></i></a>
                            </div>
                            <?php
                            if ($comment_thumb) :
                            ?>
                                <ul>
                                    <?php
                                    foreach ($comment_thumb as $key => $value) :
                                        $image_attributes = wp_get_attachment_image_src($value, 'full');
                                    ?>
                                        <li><a href="<?php echo $image_attributes[0]; ?>" target="_Blank"><img src="<?php echo $image_attributes[0]; ?>" /></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>

                        <?php if (is_user_logged_in()) { ?>
                            <div class="entry-nav">
                                <div class="reply">
                                    <a href="#">
                                        <i class="far fa-comment-alt-lines medium"></i>
                                        <span><?php esc_html_e('Reply', 'civi-framework'); ?></span>
                                    </a>
                                </div>

                                <?php if ($comment->comment_approved == 0) { ?>
                                    <span class="waiting-for-approval"> <?php esc_html_e('Waiting for approval', 'civi-framework'); ?> </span>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        <?php
                        $args = array(
                            'status' => 'approve',
                            'number' => '',
                            'order'  => 'ASC',
                            'parent' => $comment->comment_ID
                        );
                        $child_comments = get_comments($args);
                        ?>
                        <?php if ($child_comments) : ?>
                            <ol class="children">
                                <?php foreach ($child_comments as $child_comment) { ?>
                                    <?php
                                    $child_avatar_url       = get_avatar_url($child_comment->user_id, ['size' => '50']);
                                    $child_link             = get_author_posts_url($child_comment->user_id);
                                    $child_avatar_image_url = get_the_author_meta('author_avatar_image_url', $child_comment->user_id);
                                    if (isset($child_avatar_image_url)) {
                                        $child_avatar_url = $child_avatar_image_url;
                                    }
                                    if (empty($child_avatar_url)) {
                                        $child_avatar_url = CIVI_PLUGIN_URL . 'assets/images/default-user-image.png';
                                    }
                                    ?>
                                    <li class="author-review">
                                        <div class="entry-head">
                                            <div class="entry-avatar">
                                                <figure>
                                                    <?php
                                                    if (!empty($child_avatar_url)) {
                                                    ?>
                                                        <a href="<?php echo esc_url($child_link); ?>">
                                                            <img src="<?php echo esc_url($child_avatar_url); ?>" alt="<?php the_author_meta('display_name', $comment->user_id); ?>">
                                                        </a>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <a href="<?php echo esc_url($child_link); ?>">
                                                            <img src="<?php echo esc_url($no_avatar_src); ?>" alt="<?php the_author_meta('display_name', $comment->user_id); ?>"></a>
                                                    <?php
                                                    }
                                                    ?>
                                                </figure>
                                            </div>
                                            <div class="entry-info">
                                                <div class="entry-name">
                                                    <h4 class="author-name"><a href="<?php echo esc_url($child_link); ?>"><?php the_author_meta('display_name', $child_comment->user_id); ?></a></h4>
                                                </div>
                                                <span class="review-date"><?php echo civi_get_comment_time($child_comment->comment_ID); ?></span>
                                            </div>
                                        </div>

                                        <div class="entry-comment civi-review-details">
                                            <p class="review-content civi-review"><?php esc_html_e($child_comment->comment_content); ?></p>
                                            <div class="toggle-review">
                                                <a href="#" class="show-more-review"><?php esc_html_e('Show more', 'civi-framework'); ?><i class="fas fa-angle-down"></i></a>
                                                <a href="#" class="hide-all-review"><?php esc_html_e('Hide less', 'civi-framework'); ?><i class="fas fa-angle-up"></i></a>
                                            </div>
                                        </div>

                                        <?php if ($child_comment->comment_approved == 0) { ?>
                                            <div class="entry-nav">
                                                <span class="waiting-for-approval"> <?php esc_html_e('Waiting for approval', 'civi-framework'); ?> </span>
                                            </div>
                                        <?php } ?>
                                    </li>
                                <?php } ?>
                            </ol>
                        <?php endif; ?>

                        <div class="form-reply" data-id="<?php echo esc_attr($comment->comment_ID); ?>"></div>
                    </li>
            <?php
                }
            }
            ?>
        </ul>
        <?php
        $post_id = get_the_ID();
        $post_author_id = get_post_field('post_author', $post_id);
        $current_user = wp_get_current_user();
        $userID = $current_user->ID;

        if (!is_user_logged_in()) { ?>
            <div class="add-new-review">
                <div class="login-for-review account logged-out">
                    <a href="#popup-form" class="btn-login"><?php esc_html_e('Login', 'civi-framework'); ?></a>
                    <span><?php esc_html_e('to review', 'civi-framework'); ?></span>
                </div>
            </div>
        <?php
        } else { ?>
            <?php if ($post_author_id != $userID) { ?>
                <div class="add-new-review">
                    <h4 class="review-title"><?php esc_html_e('Write a Review', 'civi-framework'); ?></h4>
                    <?php
                    $user_name = $current_user->display_name;
                    $avatar_url = get_avatar_url($current_user->ID);
                    $author_avatar_image_url = get_the_author_meta('author_avatar_image_url', $current_user->ID);
                    if (!empty($author_avatar_image_url)) {
                        $avatar_url = $author_avatar_image_url;
                    }
                    if (is_null($my_review)) {
                    ?>
                        <form method="post" class="reviewForm" enctype="multipart/form-data" action="#">
                            <div class="form-group star-rating">
                                <div class="rate-title">
                                    <span><?php esc_html_e('Salary & Benefits', 'civi-framework'); ?></span>
                                    <div class="tip" data-tip-content="<?php esc_html_e('Salary review every 6 months based on the work performance', 'civi-framework'); ?>">
                                        <div class="tip-content"><?php esc_html_e('Salary review every 6 months based on the work performance', 'civi-framework'); ?></div>
                                    </div>
                                </div>
                                <fieldset class="rate">
                                    <input type="radio" id="rating_salary5" name="rating_salary" value="5" /><label for="rating_salary5" title="5 stars"></label>
                                    <input type="radio" id="rating_salary4" name="rating_salary" value="4" /><label for="rating_salary4" title="4 stars"></label>
                                    <input type="radio" id="rating_salary3" name="rating_salary" value="3" /><label for="rating_salary3" title="3 stars"></label>
                                    <input type="radio" id="rating_salary2" name="rating_salary" value="2" /><label for="rating_salary2" title="2 stars"></label>
                                    <input type="radio" id="rating_salary1" name="rating_salary" value="1" /><label for="rating_salary1" title="1 star"></label>
                                </fieldset>
                            </div>
                            <div class="form-group star-rating">
                                <div class="rate-title">
                                    <span><?php esc_html_e('Company Culture', 'civi-framework'); ?></span>
                                    <div class="tip" data-tip-content="<?php esc_html_e('Company trip once a year and Team building once a month', 'civi-framework'); ?>">
                                        <div class="tip-content"><?php esc_html_e('Company trip once a year and Team building once a month', 'civi-framework'); ?></div>
                                    </div>
                                </div>
                                <fieldset class="rate">
                                    <input type="radio" id="rating_company5" name="rating_company" value="5" /><label for="rating_company5" title="5 stars"></label>
                                    <input type="radio" id="rating_company4" name="rating_company" value="4" /><label for="rating_company4" title="4 stars"></label>
                                    <input type="radio" id="rating_company3" name="rating_company" value="3" /><label for="rating_company3" title="3 stars"></label>
                                    <input type="radio" id="rating_company2" name="rating_company" value="2" /><label for="rating_company2" title="2 stars"></label>
                                    <input type="radio" id="rating_company1" name="rating_company" value="1" /><label for="rating_company1" title="1 star"></label>
                                </fieldset>
                            </div>
                            <div class="form-group star-rating">
                                <div class="rate-title">
                                    <span><?php esc_html_e('Skill Development', 'civi-framework'); ?></span>
                                    <div class="tip" data-tip-content="<?php esc_html_e('Well trained and dedicated to being able to catch the pace smoothly.', 'civi-framework'); ?>">
                                        <div class="tip-content"><?php esc_html_e('Well trained and dedicated to being able to catch the pace smoothly.', 'civi-framework'); ?></div>
                                    </div>
                                </div>
                                <fieldset class="rate">
                                    <input type="radio" id="rating_skill5" name="rating_skill" value="5" /><label for="rating_skill5" title="5 stars"></label>
                                    <input type="radio" id="rating_skill4" name="rating_skill" value="4" /><label for="rating_skill4" title="4 stars"></label>
                                    <input type="radio" id="rating_skill3" name="rating_skill" value="3" /><label for="rating_skill3" title="3 stars"></label>
                                    <input type="radio" id="rating_skill2" name="rating_skill" value="2" /><label for="rating_skill2" title="2 stars"></label>
                                    <input type="radio" id="rating_skill1" name="rating_skill" value="1" /><label for="rating_skill1" title="1 star"></label>
                                </fieldset>
                            </div>
                            <div class="form-group star-rating">
                                <div class="rate-title">
                                    <span><?php esc_html_e('Work Satisfaction', 'civi-framework'); ?></span>
                                    <div class="tip" data-tip-content="<?php esc_html_e('Our office is located with creative, open workspaces and a high-quality engaging environment.', 'civi-framework'); ?>">
                                        <div class="tip-content"><?php esc_html_e('Our office is located with creative, open workspaces and a high-quality engaging environment.', 'civi-framework'); ?></div>
                                    </div>
                                </div>
                                <fieldset class="rate">
                                    <input type="radio" id="rating_work5" name="rating_work" value="5" /><label for="rating_work5" title="5 stars"></label>
                                    <input type="radio" id="rating_work4" name="rating_work" value="4" /><label for="rating_work4" title="4 stars"></label>
                                    <input type="radio" id="rating_work3" name="rating_work" value="3" /><label for="rating_work3" title="3 stars"></label>
                                    <input type="radio" id="rating_work2" name="rating_work" value="2" /><label for="rating_work2" title="2 stars"></label>
                                    <input type="radio" id="rating_work1" name="rating_work" value="1" /><label for="rating_work1" title="1 star"></label>
                                </fieldset>
                            </div>
                            <div class="form-group form-media">
                                <label for="file">
                                    <input class="uploadImage" type="file" name="files[]" accept="image/*, application/pdf" id="file" multiple="">
                                    <span class="name"><?php esc_attr_e('Add Photos', 'civi-framework'); ?></span>
                                    <span class="fileList"></span>
                                </label>
                            </div>
                            <div class="form-group custom-area">
                                <textarea class="form-control" name="message" placeholder="<?php esc_attr_e('Your review...', 'civi-framework'); ?>"></textarea>
                                <?php if (isset($avatar_url)) : ?>
                                    <div class="current-user-avatar">
                                        <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($user_name); ?>">
                                    </div>
                                <?php endif; ?>
                            </div>
                            <button type="submit" class="civi-submit-company-rating civi-button button-icon-right">
                                <span><?php esc_html_e('Submit Review', 'civi-framework'); ?></span></button>
                            <?php wp_nonce_field('civi_submit_review_ajax_nonce', 'civi_security_submit_review'); ?>
                            <input type="hidden" name="action" value="civi_company_submit_review_ajax">
                            <input type="hidden" name="company_id" value="<?php the_ID(); ?>">
                        </form>
                    <?php
                    } else {
                    ?>
                        <form method="post" class="reviewForm" enctype="multipart/form-data" action="#">
                            <div class="form-group star-rating">
                                <div class="rate-title">
                                    <span><?php esc_html_e('Salary & Benefits', 'civi-framework'); ?></span>
                                    <div class="tip" data-tip-content="<?php esc_html_e('Salary review every 6 months based on the work performance', 'civi-framework'); ?>">
                                        <div class="tip-content"><?php esc_html_e('Salary review every 6 months based on the work performance', 'civi-framework'); ?></div>
                                    </div>
                                </div>
                                <fieldset class="rate">
                                    <input type="radio" id="rating_salary5" name="rating_salary" <?php if ($my_review_company_salary_rating === 5) {
                                                                                                        echo 'checked';
                                                                                                    } ?> value="5" /><label for="rating_salary5" title="5 stars"></label>
                                    <input type="radio" id="rating_salary4" name="rating_salary" <?php if ($my_review_company_salary_rating === 4) {
                                                                                                        echo 'checked';
                                                                                                    } ?> value="4" /><label for="rating_salary4" title="4 stars"></label>
                                    <input type="radio" id="rating_salary3" name="rating_salary" <?php if ($my_review_company_salary_rating === 3) {
                                                                                                        echo 'checked';
                                                                                                    } ?> value="3" /><label for="rating_salary3" title="3 stars"></label>
                                    <input type="radio" id="rating_salary2" name="rating_salary" <?php if ($my_review_company_salary_rating === 2) {
                                                                                                        echo 'checked';
                                                                                                    } ?> value="2" /><label for="rating_salary2" title="2 stars"></label>
                                    <input type="radio" id="rating_salary1" name="rating_salary" <?php if ($my_review_company_salary_rating === 1) {
                                                                                                        echo 'checked';
                                                                                                    } ?> value="1" /><label for="rating_salary1" title="1 star"></label>
                                </fieldset>
                            </div>
                            <div class="form-group star-rating">
                                <div class="rate-title">
                                    <span><?php esc_html_e('Company Culture', 'civi-framework'); ?></span>
                                    <div class="tip" data-tip-content="<?php esc_html_e('Company trip once a year and Team building once a month', 'civi-framework'); ?>">
                                        <div class="tip-content"><?php esc_html_e('Company trip once a year and Team building once a month', 'civi-framework'); ?></div>
                                    </div>
                                </div>
                                <fieldset class="rate">
                                    <input type="radio" id="rating_company5" name="rating_company" <?php if ($my_review_company_company_rating === 5) {
                                                                                                        echo 'checked';
                                                                                                    } ?> value="5" /><label for="rating_company5" title="5 stars"></label>
                                    <input type="radio" id="rating_company4" name="rating_company" <?php if ($my_review_company_company_rating === 4) {
                                                                                                        echo 'checked';
                                                                                                    } ?> value="4" /><label for="rating_company4" title="4 stars"></label>
                                    <input type="radio" id="rating_company3" name="rating_company" <?php if ($my_review_company_company_rating === 3) {
                                                                                                        echo 'checked';
                                                                                                    } ?> value="3" /><label for="rating_company3" title="3 stars"></label>
                                    <input type="radio" id="rating_company2" name="rating_company" <?php if ($my_review_company_company_rating === 2) {
                                                                                                        echo 'checked';
                                                                                                    } ?> value="2" /><label for="rating_company2" title="2 stars"></label>
                                    <input type="radio" id="rating_company1" name="rating_company" <?php if ($my_review_company_company_rating === 1) {
                                                                                                        echo 'checked';
                                                                                                    } ?> value="1" /><label for="rating_company1" title="1 star"></label>
                                </fieldset>
                            </div>
                            <div class="form-group star-rating">
                                <div class="rate-title">
                                    <span><?php esc_html_e('Skill Development', 'civi-framework'); ?></span>
                                    <div class="tip" data-tip-content="<?php esc_html_e('Well trained and dedicated to being able to catch the pace smoothly.', 'civi-framework'); ?>">
                                        <div class="tip-content"><?php esc_html_e('Well trained and dedicated to being able to catch the pace smoothly.', 'civi-framework'); ?></div>
                                    </div>
                                </div>
                                <fieldset class="rate">
                                    <input type="radio" id="rating_skill5" name="rating_skill" <?php if ($my_review_company_skill_rating === 5) {
                                                                                                    echo 'checked';
                                                                                                } ?> value="5" /><label for="rating_skill5" title="5 stars"></label>
                                    <input type="radio" id="rating_skill4" name="rating_skill" <?php if ($my_review_company_skill_rating === 4) {
                                                                                                    echo 'checked';
                                                                                                } ?> value="4" /><label for="rating_skill4" title="4 stars"></label>
                                    <input type="radio" id="rating_skill3" name="rating_skill" <?php if ($my_review_company_skill_rating === 3) {
                                                                                                    echo 'checked';
                                                                                                } ?> value="3" /><label for="rating_skill3" title="3 stars"></label>
                                    <input type="radio" id="rating_skill2" name="rating_skill" <?php if ($my_review_company_skill_rating === 2) {
                                                                                                    echo 'checked';
                                                                                                } ?> value="2" /><label for="rating_skill2" title="2 stars"></label>
                                    <input type="radio" id="rating_skill1" name="rating_skill" <?php if ($my_review_company_skill_rating === 1) {
                                                                                                    echo 'checked';
                                                                                                } ?> value="1" /><label for="rating_skill1" title="1 star"></label>
                                </fieldset>
                            </div>
                            <div class="form-group star-rating">
                                <div class="rate-title">
                                    <span><?php esc_html_e('Work Satisfaction', 'civi-framework'); ?></span>
                                    <div class="tip" data-tip-content="<?php esc_html_e('Our office is located with creative, open workspaces and a high-quality engaging environment.', 'civi-framework'); ?>">
                                        <div class="tip-content"><?php esc_html_e('Our office is located with creative, open workspaces and a high-quality engaging environment.', 'civi-framework'); ?></div>
                                    </div>
                                </div>
                                <fieldset class="rate">
                                    <input type="radio" id="rating_work5" name="rating_work" <?php if ($my_review_company_work_rating === 5) {
                                                                                                    echo 'checked';
                                                                                                } ?> value="5" /><label for="rating_work5" title="5 stars"></label>
                                    <input type="radio" id="rating_work4" name="rating_work" <?php if ($my_review_company_work_rating === 4) {
                                                                                                    echo 'checked';
                                                                                                } ?> value="4" /><label for="rating_work4" title="4 stars"></label>
                                    <input type="radio" id="rating_work3" name="rating_work" <?php if ($my_review_company_work_rating === 3) {
                                                                                                    echo 'checked';
                                                                                                } ?> value="3" /><label for="rating_work3" title="3 stars"></label>
                                    <input type="radio" id="rating_work2" name="rating_work" <?php if ($my_review_company_work_rating === 2) {
                                                                                                    echo 'checked';
                                                                                                } ?> value="2" /><label for="rating_work2" title="2 stars"></label>
                                    <input type="radio" id="rating_work1" name="rating_work" <?php if ($my_review_company_work_rating === 1) {
                                                                                                    echo 'checked';
                                                                                                } ?> value="1" /><label for="rating_work1" title="1 star"></label>
                                </fieldset>
                            </div>
                            <div class="form-group form-media">
                                <label for="file">
                                    <input class="uploadImage" type="file" name="files[]" accept="image/*, application/pdf" id="file" multiple="">
                                    <span class="name"><?php esc_attr_e('Add Photos', 'civi-framework'); ?></span>
                                    <span class="fileList"></span>
                                </label>
                            </div>
                            <div class="form-group custom-area">
                                <textarea class="form-control" rows="6" name="message" placeholder="<?php esc_attr_e('Your review...', 'civi-framework'); ?>"><?php echo wp_kses_post($my_review->comment_content); ?></textarea>
                                <?php if (isset($avatar_url)) : ?>
                                    <div class="current-user-avatar">
                                        <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($user_name); ?>">
                                    </div>
                                <?php endif; ?>
                            </div>
                            <button type="submit" class="civi-submit-company-rating civi-button button-icon-right">
                                <span><?php esc_html_e('Update Review', 'civi-framework'); ?></span></button>
                            <?php wp_nonce_field('civi_submit_review_ajax_nonce', 'civi_security_submit_review'); ?>
                            <input type="hidden" name="action" value="civi_company_submit_review_ajax">
                            <input type="hidden" name="company_id" value="<?php the_ID(); ?>">
                        </form>
                    <?php
                    } ?>
                </div>
        <?php }
        } ?>
    </div>

    <div class="duplicate-form-reply hide none">
        <div class="entry-head">
            <h4 class="review-title"><?php esc_html_e('Reply', 'civi-framework'); ?></h4>
            <a href="#" class="cancel-reply">
                <i class="far fa-times"></i>
                <span><?php esc_html_e('Cancel reply', 'civi-framework'); ?></span>
            </a>
        </div>
        <?php
        $current_user = wp_get_current_user();
        $user_name    = $current_user->display_name;
        $avatar_url   = get_avatar_url($current_user->ID);
        $author_avatar_image_url = get_the_author_meta('author_avatar_image_url', $current_user->ID);
        if (!empty($author_avatar_image_url)) {
            $avatar_url = $author_avatar_image_url;
        }
        ?>
        <form method="post" class="repreviewForm" action="#">
            <div class="form-group custom-area">
                <textarea class="form-control" rows="5" name="message" placeholder="<?php esc_attr_e('Add a comment...', 'civi-framework'); ?>"></textarea>
            </div>
            <button type="submit" class="civi-submit-company-reply civi-button"><?php esc_html_e('Send', 'civi-framework'); ?></button>
            <?php wp_nonce_field('civi_submit_reply_ajax_nonce', 'civi_security_submit_reply'); ?>
            <input type="hidden" name="action" value="civi_company_submit_reply_ajax">
            <input type="hidden" name="company_id" value="<?php the_ID(); ?>">
            <input type="hidden" name="comment_id" value="">
        </form>
    </div>
</div>