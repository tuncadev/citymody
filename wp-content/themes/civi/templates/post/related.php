<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
wp_enqueue_script('slick');

$post_id = get_the_ID();

$related = get_posts(array(
	'numberposts'  => 3,
	'category__in' => wp_get_post_categories($post_id),
	'post__not_in' => array($post_id)
));

$slick_attributes = array(
	'"slidesToShow": 3',
	'"slidesToScroll": 1',
	'"autoplay": true',
	'"infinite": true',
	'"autoplaySpeed": 5000',
	'"arrows": false',
	'"responsive": [{ "breakpoint": 376, "settings": {"slidesToShow": 2,"infinite": true, "swipeToSlide": true, "dots": true} },{ "breakpoint": 479, "settings": {"slidesToShow": 2,"infinite": true, "swipeToSlide": true, "dots": true} },{ "breakpoint": 650, "settings": {"slidesToShow": 2} },{ "breakpoint": 768, "settings": {"slidesToShow": 3} },{ "breakpoint": 1200, "settings": {"slidesToShow": 3} } ]'
);
$wrapper_attributes[] = "data-slick='{" . implode(', ', $slick_attributes) . "}'";

?>

<?php if ($related) : ?>
	<div class="related-post block-line">

		<div class="block-heading">
			<h3 class="entry-title"><?php esc_html_e('Related Articles', 'civi'); ?></h3>
		</div>

		<div class="list-posts slick-carousel" <?php echo implode(' ', $wrapper_attributes); ?>>
			<?php
			foreach ($related as $related_post) {
				$postid    = $related_post->ID;
				$size      = 'medium';
				$categores = wp_get_post_categories($postid);
				$size      = '480x520';
				$attach_id = get_post_thumbnail_id($postid);
				$thumb_url = Civi_Helper::civi_image_resize($attach_id, $size);

				$no_image_src    = CIVI_IMAGES . 'no-image.jpg';
				if (!class_exists("Civi_Framework")) {
					$default_image = '';
				} else {
					$default_image   = civi_get_option('default_jobs_image', '');
				}

				if ($thumb_url) {
					$cur_url = $thumb_url;
				} else {
					if ($default_image != '') {
						if (is_array($default_image) && $default_image['url'] != '') {
							$cur_url = $default_image['url'];
						}
					} else {
						$cur_url = $no_image_src;
					}
				}
			?>

				<article id="post-<?php echo esc_attr($postid); ?>" class="post">
					<div class="inner-post-wrap">

						<!-- post thumbnail -->

						<div class="entry-post-thumbnail">
							<a href="<?php echo get_the_permalink($postid); ?>">
								<img src="<?php echo esc_url($cur_url); ?>" alt="<?php the_title_attribute($postid); ?>">
							</a>
						</div>


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
<?php endif; ?>