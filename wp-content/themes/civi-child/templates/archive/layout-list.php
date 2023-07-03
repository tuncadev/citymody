<?php

/**
 * Template part for displaying blog list.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 */

$image_size      = Civi_Helper::get_setting('blog_image_size');
$excerpt         = get_the_excerpt();
$categories = get_the_category();
$numbers_excerpt = 25;
$attach_id       = get_post_thumbnail_id($post->ID);
$thumb_url       = Civi_Helper::civi_image_resize($attach_id, $image_size);
$no_image_src    = CIVI_IMAGES . 'no-image.jpg';
if (class_exists('Civi_Framework')) {
	$default_image   = civi_get_option('default_jobs_image', '');
} else {
	$default_image = '';
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

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="inner-post-wrap">

		<!-- post thumbnail -->

		<div class="entry-post-thumbnail">
			<a href="<?php the_permalink(); ?>">
				<img src="<?php echo esc_url($cur_url); ?>" alt="<?php the_title_attribute(); ?>">
			</a>
		</div>

		<div class="entry-post-detail">

			<!-- post date -->
			<div class="post-date">
				<?php if ($categories) : ?>
					<ul class="post-categories">
						<?php foreach ($categories as $cat) : ?>
							<li><a href="<?php echo get_category_link($cat); ?>"><?php esc_html_e($cat->name); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<span><?php esc_html_e(get_the_date('F j, Y')) ?></span>
			</div>

			<!-- post title -->
			<div class="entry-title">
				<?php
				the_title('<h3 class="post-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h3>');
				?>

				<?php if (is_sticky($post->ID)) { ?>
					<span class="is-sticky"><?php esc_html_e('Featured', 'civi'); ?></span>
				<?php } ?>
			</div>

			<!-- post excerpt -->
			<?php if (!empty($excerpt)) { ?>
				<div class="post-excerpt">
					<p><?php echo wp_trim_words(get_the_excerpt($post->ID), 20); ?></p>
				</div>
			<?php } ?>

			<!-- button readmore -->
			<div class="btn-readmore">
				<a href="<?php the_permalink(); ?>">
					<?php esc_html_e('Read More', 'civi'); ?>
				</a>
			</div>

		</div>
	</div>
</article><!-- #post-## -->