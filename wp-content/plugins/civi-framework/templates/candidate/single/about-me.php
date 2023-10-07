<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$content = get_the_content(); ?>
<?php
/* SKILLS SENCTION */
$candidate_id = get_the_ID();

$candidate_skills = get_the_terms($candidate_id, 'candidate_skills');

/* Physical  */
$candidate_height = get_the_terms($candidate_id, 'candidate_height') ; 
$candidate_weight = get_the_terms($candidate_id, 'candidate_weight'); 
$candidate_footsize = get_the_terms($candidate_id, 'candidate_footsize'); 
$candidate_haircolor = get_the_terms($candidate_id, 'candidate_hair-color'); 
$candidate_hairtype = get_the_terms($candidate_id, 'candidate_hair-type'); 
$candidate_eyecolor = get_the_terms($candidate_id, 'candidate_eye-color'); 
$candidate_skincolor = get_the_terms($candidate_id, 'candidate_skin-color'); 
$candidate_chestsize = get_the_terms($candidate_id, 'candidate_chest-size'); 
$candidate_waistsize = get_the_terms($candidate_id, 'candidate_waist-size'); 
$candidate_hipsize = get_the_terms($candidate_id, 'candidate_hip-size'); 
$candidate_bodytype = get_the_terms($candidate_id, 'candidate_body-type'); 
$candidate_salary          = !empty(get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_offer_salary')) ? get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_offer_salary')[0] : '';
$candidate_yoe             = get_the_terms($candidate_id, 'candidate_yoe');
$candidate_languages       = get_the_terms($candidate_id, 'candidate_languages');
$candidate_location        = get_the_terms($candidate_id, 'candidate_locations');
$candidate_gender          = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_gender', true);
$candidate_qualification   = get_the_terms($candidate_id, 'candidate_qualification');
$candidate_ages            = get_the_terms($candidate_id, 'candidate_ages');
$candidate_phone           = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_phone', true);
$candidate_email           = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_email', true);
$candidate_twitter         = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_twitter', true);
$candidate_facebook        = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_facebook', true);
$candidate_instagram       = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_instagram', true);
$candidate_linkedin        = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_linkedin', true);
$option_list_gender = array(
	'both' => esc_html__('Both', 'civi-framework'),
	'female' => esc_html__('Female', 'civi-framework'),
	'male' => esc_html__('Male', 'civi-framework'),
);
 ?>
 <?php 
if (isset($content) && !empty($content)) : ?>
    <div class="block-archive-inner candidate-overview-details">
        <h4 class="title-candidate"><?php esc_html_e('About me', 'civi-framework') ?></h4>
        <?php the_content(); ?>
    </div>
<?php endif; ?>
<?php if( $candidate_height || $candidate_weight || $candidate_footsize || $candidate_haircolor || $candidate_hairtype || $candidate_eyecolor || $candidate_skincolor || $candidate_chestsize || $candidate_waistsize || $candidate_hipsize || $candidate_bodytype) { ?>
	<div class="block-archive-inner candidate-single-field skills-aboutme">
		<h4 class="title-candidate"><?php esc_html_e('Physical Attributes', 'civi-framework') ?></h4>
		<div class="candidate-skills">
			<div class="row row_pattributes">
				<?php if($candidate_height) : ?>
				<?php foreach ($candidate_height as $height) { ?>
					<div class="form-group col-md-4 pattributes">
						<h3><?php esc_html_e('Height', 'civi-framework') ?></h3>
						<?php esc_html_e($height->name); ?>
					</div>
				<?php } ?>
				<?php endif; ?>
				<?php if($candidate_weight) : ?>
				<?php foreach ($candidate_weight as $weight) { ?>
					<div class="form-group col-md-4 pattributes">
						<h3><?php esc_html_e('Weight', 'civi-framework'); ?></h3>
						<?php esc_html_e($weight->name); ?>
					</div>
				<?php } ?>
				<?php endif; ?>
				<?php if($candidate_footsize) : ?>
				<?php foreach ($candidate_footsize as $footsize) { ?>
					<div class="form-group col-md-4 pattributes">
						<h3><?php esc_html_e('Foot Size', 'civi-framework'); ?></h3>
						<?php esc_html_e($footsize->name); ?>
					</div>
				<?php } ?>
				<?php endif; ?>
				<?php if($candidate_haircolor) : ?>
				<?php foreach ($candidate_haircolor as $haircolor) { ?>
					<div class="form-group col-md-4 pattributes">
						<h3><?php esc_html_e('Hair Color', 'civi-framework') ?></h3>
						<?php esc_html_e($haircolor->name); ?>
					</div>
				<?php } ?>
				<?php endif; ?>
				<?php if($candidate_hairtype) : ?>
				<?php foreach ($candidate_hairtype as $hairtype) { ?>
					<div class="form-group col-md-4 pattributes">
						<h3><?php esc_html_e('Hair Type', 'civi-framework'); ?></h3>
						<?php esc_html_e($hairtype->name); ?>
					</div>
				<?php } ?>
				<?php endif; ?>
				<?php if($candidate_eyecolor) : ?>
				<?php foreach ($candidate_eyecolor as $eyecolor) { ?>
					<div class="form-group col-md-4 pattributes">
						<h3><?php esc_html_e('Eye Color', 'civi-framework'); ?></h3>
						<?php esc_html_e($eyecolor->name); ?>
					</div>
				<?php } ?>
				<?php endif; ?>
				<?php if($candidate_skincolor) : ?>
				<?php foreach ($candidate_skincolor as $skincolor) { ?>
					<div class="form-group col-md-4 pattributes">
						<h3><?php esc_html_e('Skin Color', 'civi-framework') ?></h3>
						<?php esc_html_e($skincolor->name); ?>
					</div>
				<?php } ?>
				<?php endif; ?>
				<?php if($candidate_chestsize) : ?>
				<?php foreach ($candidate_chestsize as $chestsize) { ?>
					<div class="form-group col-md-4 pattributes">
						<h3><?php esc_html_e('Chest Size (cm)', 'civi-framework'); ?></h3>
						<?php esc_html_e($chestsize->name); ?>
					</div>
				<?php } ?>
				<?php endif; ?>
				<?php if($candidate_waistsize) : ?>
				<?php foreach ($candidate_waistsize as $waistsize) { ?>
					<div class="form-group col-md-4 pattributes">
						<h3><?php esc_html_e('Waist Size (cm)', 'civi-framework'); ?></h3>
						<?php esc_html_e($waistsize->name); ?>
					</div>
				<?php } ?>
				<?php endif; ?>
				<?php if($candidate_hipsize) : ?>
				<?php foreach ($candidate_hipsize as $hipsize) { ?>
					<div class="form-group col-md-4 pattributes">
						<h3><?php esc_html_e('Hip Size(cm)', 'civi-framework') ?></h3>
						<?php esc_html_e($hipsize->name); ?>
					</div>
				<?php } ?>
				<?php endif; ?>
				<?php if($candidate_bodytype) : ?>
				<?php foreach ($candidate_bodytype as $bodytype) { ?>
					<div class="form-group col-md-4 pattributes">
						<h3><?php esc_html_e('Body Type', 'civi-framework'); ?></h3>
						<?php esc_html_e($bodytype->name); ?>
					</div>
				<?php } ?>
				<?php endif; ?>
			</div>
		</div>
		
		<?php civi_custom_field_single_candidate('skills'); ?>
	</div>

	<div class="block-archive-inner candidate-single-field skills-aboutme">
		<div class="infotop">
			<h3 class="title-candidate"><?php esc_html_e('Information', 'civi-framework'); ?></h3>
		</div>
		<div class="infocontent">
		<?php if (!empty($candidate_salary)) : ?>
        <div class="info">
            <p class="title-info"><?php esc_html_e('Offered Salary', 'civi-framework'); ?></p>
            <div class="details-info salary">
                <?php civi_get_salary_candidate($candidate_id); ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if (is_array($candidate_yoe)) : ?>
        <div class="info">
            <p class="title-info"><?php esc_html_e('Experience time', 'civi-framework'); ?></p>
            <div class="list-cate">
                <?php foreach ($candidate_yoe as $yoe) {
                    $yoe_link = get_term_link($yoe, 'candidate_yoe'); ?>
                    <a href="<?php echo esc_url($yoe_link); ?>">
                        <?php esc_attr_e($yoe->name); ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if (is_array($candidate_languages)) : ?>
        <div class="info">
            <p class="title-info"><?php esc_html_e('Native Language', 'civi-framework'); ?></p>
            <div class="list-cate">
                <?php foreach ($candidate_languages as $language) {
                    esc_attr_e($language->name);
                } ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!empty($candidate_gender)) : ?>
        <div class="info">
            <p class="title-info"><?php esc_html_e('Gender', 'civi-framework'); ?></p>
            <p class="details-info"><?php esc_attr_e($option_list_gender[$candidate_gender]) ?></p>
        </div>
    <?php endif; ?>
    <?php if (is_array($candidate_qualification)) : ?>
        <div class="info">
            <p class="title-info"><?php esc_html_e('Qualification', 'civi-framework'); ?></p>
            <div class="list-cate">
                <?php foreach ($candidate_qualification as $qualification) {
                    echo esc_attr_e($qualification->name);
                } ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if (is_array($candidate_ages)) : ?>
        <div class="info">
            <p class="title-info"><?php esc_html_e('Age', 'civi-framework'); ?></p>
            <div class="list-cate">
                <?php foreach ($candidate_ages as $ages) {
                    echo esc_attr_e($ages->name);
                } ?>
            </div>
        </div>
    <?php endif; ?>

		</div>
   
		</div>
<?php } ?>