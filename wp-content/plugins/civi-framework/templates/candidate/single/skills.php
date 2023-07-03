<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
/* 
$candidate_id = get_the_ID();

$candidate_skills = get_the_terms($candidate_id, 'candidate_skills');

if ($candidate_skills == false || is_wp_error($candidate_skills)) {
    return;
}

?>

<div class="block-archive-inner candidate-single-field">
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

<?php */ ?>