<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $candidate_data;
wp_enqueue_style('lity');
wp_enqueue_script('lity');
$candidate_id = get_the_ID();
$candidate_audios = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_audio_list', false);
$candidate_audios = !empty($candidate_audios) ? $candidate_audios[0] : '';

if (empty($candidate_audios[0][CIVI_METABOX_PREFIX . 'candidate_audio_title'])) {
    return;
}
?>

<?php if (!empty($candidate_audios)) : ?>
<div class="row row_canAudio">
	<?php foreach($candidate_audios as $index => $candidate_audio) : ?>
	<div class="form-group col-md-12 col_canAudio">
		<?php $candidate_audio_url = $candidate_audio[CIVI_METABOX_PREFIX . 'candidate_audio_url']; ?>
		<?php $candidate_audio_title = $candidate_audio[CIVI_METABOX_PREFIX . 'candidate_audio_title']; ?>
		<div class="block-archive-inner candidate-video-details">
			<h4 class="title-candidate"><?php esc_html_e('Audio', 'civi-framework') ?><?php echo  " " . $index +1 . " - "; ?><?php echo $candidate_audio_title; ?></h4>
			<div class="entry-candidate-element">
				<div class="entry-thumb-wrap">
					<?php if (wp_oembed_get($candidate_audio_url)) : ?>
						   <div class="embed-responsive embed-responsive-16by9 embed-responsive-full can_audio">
								<?php echo wp_oembed_get($candidate_audio_url, array('wmode' => 'transparent')); ?>
							</div>	
					<?php else : ?>
						<?php echo $candidate_audio_url; ?>
						<div class="embed-responsive embed-responsive-16by9 embed-responsive-full">
							<?php echo wp_kses_post($candidate_audio_url); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
</div>
<?php endif; ?>