<!-- post thumbnail -->
<?php
$size_image     = '';
$attach_id         = get_post_thumbnail_id();
$image_full_src = wp_get_attachment_image_src($attach_id, 'full');

if ($image_full_src) {
	$h_image    = $image_full_src[1];
	$w_image    = $image_full_src[2];
	$size_image = $h_image . 'x' . $w_image;
}

$no_image_src    = CIVI_IMAGES . 'no-image.jpg';
if (!class_exists("Civi_Framework")) {
	$default_image = '';
} else {
	$default_image   = civi_get_option('default_jobs_image', '');
}

if ($image_full_src) {
	$cur_url = $image_full_src[0];
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
<div class="post-thumbnail">
	<figure>
		<img src="<?php echo esc_url($cur_url); ?>" alt="<?php the_title_attribute(); ?>" title="<?php the_title_attribute(); ?>">
	</figure>
</div>
