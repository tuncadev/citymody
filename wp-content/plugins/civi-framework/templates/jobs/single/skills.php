<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if($job_id){
	$jobs_id = $job_id;
} else {
	$jobs_id = get_the_ID();
}
$jobs_skills = get_the_terms($jobs_id, 'jobs-skills');
?>
<?php if (is_array($jobs_skills)) { ?>
    <div class="block-archive-inner jobs-skills-details">
        <h4 class="title-jobs"><?php esc_html_e('Skills', 'civi-framework') ?></h4>
        <div class="skills-warpper">
            <?php foreach ($jobs_skills as $skills) {
                if ($skills->term_id !== '') {
                    $skills_link = get_term_link($skills, 'jobs-skills');
                    ?>
                    <a class="label label-skills" href="<?php echo esc_url($skills_link); ?>">
                        <?php echo $skills->name; ?>
                    </a>
                <?php }
            } ?>
        </div>
    </div>
<?php } ?>
