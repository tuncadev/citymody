<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $candidate_data;
wp_enqueue_style('lity');
wp_enqueue_script('lity');
$candidate_id = get_the_ID();
$candidate_videos = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_video_list', false);
$candidate_videos = !empty($candidate_videos) ? $candidate_videos[0] : '';

if (empty($candidate_videos[0][CIVI_METABOX_PREFIX . 'candidate_video_title'])) {
    return;
}
?>

<?php if (!empty($candidate_videos)) : ?>
<div class="row row_canVideo">
	<?php foreach($candidate_videos as $index => $candidate_video) : ?>
		<div class="form-group col-md-6 col_canVideo">
			<?php $candidate_video_url = $candidate_video[CIVI_METABOX_PREFIX . 'candidate_video_url']; ?>
			<div class="block-archive-inner candidate-video-details">
			<h4 class="title-candidate"><?php esc_html_e('Video', 'civi-framework') ?><?php echo  " " . $index +1; ?></h4>
				<div class="entry-candidate-element">
					<div class="entry-thumb-wrap">

						<?php if (wp_oembed_get($candidate_video_url)) : ?>
						   <div class="embed-responsive embed-responsive-16by9 embed-responsive-full">
								<?php echo wp_oembed_get($candidate_video_url, array('wmode' => 'transparent')); ?>
							</div>	
						<?php else : ?>
							<?php echo $candidate_video_url; ?>
							<div class="embed-responsive embed-responsive-16by9 embed-responsive-full">
								<?php echo wp_kses_post($candidate_video_url); ?>
							</div>
						<?php endif; ?>

					</div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>
<div class="block-archive-inner candidate-video-details">
	<?php civi_custom_field_single_candidate('info'); ?>
</div>					
<?php endif; ?>