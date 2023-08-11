<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


wp_enqueue_style('lightboxgallery');
wp_enqueue_style('lightboxgallery_style');
wp_enqueue_script('lightboxgallery');


$candidate_id = get_the_ID();
$candidate_galleries     = get_post_meta(get_the_ID(), CIVI_METABOX_PREFIX . 'candidate_galleries', true);
$attach_id         = get_post_thumbnail_id();
$show = 3;
$candidate_first_name = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_first_name', true);
$candidate_last_name = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_last_name', true);
?>

<?php if (!empty($candidate_galleries)) : ?>

       

	<div class="gal_container">
		 <h4 class="gal_title"><?php esc_html_e('Photos', 'civi-framework') ?></h4>
      <div class="lightboxgallery-gallery clearfix">
                    <?php
                    $civi_candidate_galleries = explode('|', $candidate_galleries);
                    $count = count($civi_candidate_galleries);
					$i = 0;
                    foreach ($civi_candidate_galleries as $key => $image) :
                        if ($image) {
                            $image_full_src = wp_get_attachment_image_src($image, 'full');
							$image_thumbnail_src = wp_get_attachment_image_src($image, 'thumbnail');
                            if (isset($image_full_src[0])) {
                                $thumb_src      = $image_full_src[0];
                            }
							 if (isset($image_thumbnail_src[0])) {
                                $thumb      = $image_thumbnail_src[0];
                            }
                        }
						
                        if (!empty($thumb_src)) {
							$i = $i + 1;
                            ?>
						<a class="lightboxgallery-gallery-item" target="_blank" href="<?php echo esc_url($thumb_src); ?>" data-title="<?php echo $candidate_first_name . "-" . $i; ?>" data-alt="<?php echo $candidate_first_name . "-" . $i; ?>" data-desc="Citymody lightbox gallery for <?php echo $candidate_first_name . " " . $candidate_last_name; ?>">
							<div>
								<img src="<?php echo esc_url($thumb); ?>" title="<?php echo $candidate_first_name . " * " . $i; ?>" alt="<?php echo $candidate_first_name . "-" . $i; ?>">
								<div class="lightboxgallery-gallery-item-content">
						  			<span class="lightboxgallery-gallery-item-title"><?php echo $candidate_first_name . " " . $candidate_last_name; ?></span>
								</div>
							</div>
						</a>
									<?php } ?>
                    <?php endforeach; ?>
            </div>
          </div>
				
<?php endif; ?>

