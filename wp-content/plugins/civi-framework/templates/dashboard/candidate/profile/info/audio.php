<?php 
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $hide_candidate_fields, $candidate_data, $candidate_meta_data, $current_user;
$user_id = $current_user->ID;
$candidate_audio_list = get_post_meta($candidate_data->ID, CIVI_METABOX_PREFIX . 'candidate_audio_list', false);
$candidate_audio_list = !empty($candidate_audio_list) ? $candidate_audio_list[0] : '';
$candidate_audio_quantity = !empty($candidate_audio_list) ? count($candidate_audio_list) : '';

$candidate_id = civi_get_post_id_candidate();

$terms = get_the_terms( $candidate_id , 'candidate_categories' );
if($terms) {
$candidate_category = $terms[0]->name;

?>
<? if($candidate_category === "Müzik") : ?>

    <div class="awards-info block-from" id="audio_track">
        <h5><?php esc_html_e('Audio', 'civi-framework') ?></h5>

        <div class="sub-head profile_message"><span class="red_star">* </span><?php esc_html_e('You can add here a shake link to your audo track from', 'civi-framework') ?><span class="soundcloud"> soundcloud.com</span><?php esc_html_e('', 'civi-framework') ?></div>
		<div class="sub-head"><?php esc_html_e('We recommend at least one audio entry', 'civi-framework') ?></div>
        <div class="civi-candidate-warpper">
            <?php if (!empty($candidate_audio_list)) {
                foreach ($candidate_audio_list as $index => $candidate_audio) : ?>
                    <div class="row">
                        <div class="group-title col-md-12">
                            <i class="delete-group fas fa-times"></i>
                            <h6 class="project">
                                <?php esc_html_e('Audio', 'civi-framework') ?>
                                <span><?php echo $index + 1 ?></span>
                            </h6>
                            <i class="fas fa-angle-up"></i>
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php esc_html_e('Title', 'civi-framework') ?></label>
                            <input type="text" name="candidate_audio_title[]" placeholder="<?php esc_attr_e('Audio Title', 'civi-framework'); ?>" value="<?php echo esc_attr($candidate_audio[CIVI_METABOX_PREFIX . 'candidate_audio_title']) ?>">
                        </div>
                        <div class="form-group col-md-12">
                            <label><?php esc_html_e('Audio SoundCloud Link', 'civi-framework') ?></label>
                            <input type="text" name="candidate_audio_url[]" cols="30" rows="7" placeholder="<?php esc_attr_e('Audio SoundCloud Link', 'civi-framework'); ?>" value="<?php echo esc_attr($candidate_audio[CIVI_METABOX_PREFIX . 'candidate_audio_url']) ?>">
                        </div>
                    </div>
            <?php endforeach; ?>	
			<?php } else {  ?>
					<?php $index = 0; ?>
					 <div class="row">
                        <div class="group-title col-md-12">
                            <i class="delete-group fas fa-times"></i>
                            <h6 class="project">
                                <?php esc_html_e('Audio', 'civi-framework') ?>
                                <span><?php echo $index + 1 ?></span>
                            </h6>
                            <i class="fas fa-angle-up"></i>
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php esc_html_e('Title', 'civi-framework') ?></label>
                            <input type="text" name="candidate_audio_title[]" placeholder="<?php esc_attr_e('Audio Title', 'civi-framework'); ?>" value="">
                        </div>
                        <div class="form-group col-md-12">
                            <label><?php esc_html_e('Audio SoundCloud Link', 'civi-framework') ?></label>
                            <input type="text" name="candidate_audio_url[]" cols="30" rows="7" placeholder="<?php esc_attr_e('Audio SoundCloud Link', 'civi-framework'); ?>" value="">
                        </div>
                    </div>
			<?php } ?>
            <button type="button" class="btn-more profile-fields"><i class="far fa-angle-down"></i><?php esc_html_e('Add another Audio', 'civi-framework') ?></button>

            <template id="template-item-award" data-size="<?php echo esc_attr($candidate_audio_quantity) ?>">
                <div class="row">
                    <div class="group-title col-md-12">
                        <i class="delete-group fas fa-times"></i>
                        <h6 class="project">
                            <?php esc_html_e('Audio', 'civi-framework') ?>
                            <span></span>
                        </h6>
                        <i class="fas fa-angle-up"></i>
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php esc_html_e('Audio Title', 'civi-framework') ?></label>
                        <input type="text" name="candidate_audio_title[]" placeholder="<?php esc_attr_e('Audio Title', 'civi-framework'); ?>" value="">
                    </div>
                    <div class="form-group col-md-12">
                        <label><?php esc_html_e('Audio SoundCloud Link', 'civi-framework') ?></label>
                        <input type="text" name="candidate_audio_url[]" cols="30" rows="7" placeholder="<?php esc_attr_e('Audio SoundCloud Link', 'civi-framework'); ?>">
                    </div>
                </div>
            </template>
        </div>
    </div>


<?php endif; ?>
<script>
 function ShowAudioDiv() {
         var selectBox = document.getElementById("candidate_categories");
         var audioDiv = document.getElementById("audio_track");
				 console.log("VAL : " + selectBox.value);
         audioDiv.style.display = selectBox.value == "212" ? "block" : "none";
      }
</script>
<?php } ?>
