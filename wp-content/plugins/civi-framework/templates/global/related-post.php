<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$enable_city_post = civi_get_option('enable_city_post', '0');
if ($enable_city_post == '0') {
    return;
}

$current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));

$related = get_posts(array(
    'post_type' => 'post',
    'numberposts'  => 12,
    'meta_query' => array(
        array(
            'key' => CIVI_METABOX_PREFIX . 'post_city',
            'value' => $current_term->slug,
            'compare' => '=',
        )
    )
));

$slick_attributes = array(
    '"slidesToShow": 4',
    '"slidesToScroll": 4',
    '"autoplay": true',
    '"infinite": true',
    '"autoplaySpeed": 5000',
    '"arrows": false',
    '"responsive": [{ "breakpoint": 376, "settings": {"slidesToShow": 2} },{ "breakpoint": 479, "settings": {"slidesToShow": 2} },{ "breakpoint": 650, "settings": {"slidesToShow": 2} },{ "breakpoint": 768, "settings": {"slidesToShow": 3} },{ "breakpoint": 1200, "settings": {"slidesToShow": 4} } ]'
);
$wrapper_attributes[] = "data-slick='{" . implode(', ', $slick_attributes) . "}'";

?>

<?php if ($related) : ?>
    <div class="related-post city-related-post pd-top-50 pd-bottom-40">

        <div class="container">

            <div class="list-posts slick-carousel" <?php echo implode(' ', $wrapper_attributes); ?>>
                <?php
                foreach ($related as $related_post) {
                    $postid    = $related_post->ID;
                    $size      = 'medium';
                    $categores = wp_get_post_categories($postid);
                    $size      = '480x520';
                    $attach_id = get_post_thumbnail_id($postid);
                    $thumb_url = civi_image_resize($attach_id, $size);
                ?>

                    <article id="post-<?php echo esc_attr($postid); ?>" class="post">
                        <div class="inner-post-wrap">

                            <!-- post thumbnail -->
                            <?php if (has_post_thumbnail($postid)) : ?>
                                <div class="entry-post-thumbnail">
                                    <a href="<?php echo get_the_permalink($postid); ?>">
                                        <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php the_title_attribute($postid); ?>">
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="entry-post-detail">

                                <!-- list categories -->
                                <?php if ($categores) : ?>
                                    <ul class="post-categories">
                                        <?php
                                        foreach ($categores as $category) {
                                            $cate = get_category($category);
                                        ?>
                                            <li><a href="<?php echo get_category_link($cate); ?>"><?php esc_html_e($cate->name); ?></a></li>
                                        <?php } ?>
                                    </ul>
                                <?php endif; ?>

                                <!-- post title -->
                                <h3 class="post-title"><a href="<?php echo get_the_permalink($postid); ?>" rel="bookmark"><?php echo get_the_title($postid); ?></a></h3>

                                <?php if (is_sticky($postid)) { ?>
                                    <span class="is-sticky"><?php esc_html_e('Featured', 'civi'); ?></span>
                                <?php } ?>

                            </div>

                        </div>
                    </article><!-- #post-## -->

                <?php
                }
                ?>
            </div>

        </div>
    </div>
<?php endif; ?>