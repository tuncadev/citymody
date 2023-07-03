<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$content = get_the_content();
if (isset($content) && !empty($content)) : ?>
    <div class="block-archive-inner candidate-overview-details">
        <h4 class="title-candidate"><?php esc_html_e('About me', 'civi-framework') ?></h4>
        <?php the_content(); ?>

    </div>

<?php endif;
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

/* ******* *//*
if ($candidate_skills == false || is_wp_error($candidate_skills)) {
    return;
}


?>

<div class="block-archive-inner candidate-single-field skills-aboutme">
    <h4 class="title-candidate"><?php esc_html_e('Skills', 'civi-framework') ?></h4>
    <div class="candidate-skills">
        <?php foreach ($candidate_skills as $skill) {
            $skill_link = get_term_link($skill, 'candidate_skills'); ?>
            <a href="<?php echo esc_url($skill_link); ?>" class="label label-skills">
                <?php esc_html_e($skill->name); ?>
            </a>
        <?php } ?>
    </div>
    <?php civi_custom_field_single_candidate('skills'); ?>
</div>
 */ ?>
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
<?php } ?>