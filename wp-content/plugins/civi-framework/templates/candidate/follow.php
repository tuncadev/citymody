<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $current_user;
wp_get_current_user();
wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'candidate-follow');

$key = false;
$user_id = $current_user->ID;
$follow_candidate = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'follow_candidate', true);
$id = get_the_ID();

if (!empty($candidate_id)) {
    $id = $candidate_id;
}

if (!empty($follow_candidate)) {
    $key = array_search($id, $follow_candidate);
}

$css_class = '';
if ($key !== false) {
    $css_class = 'added';
}
$candidate_link = get_permalink($id);
$link_URL = get_stylesheet_directory_uri() . "/project-offer?offer=" . $id;

?>
<?php if ( is_user_logged_in() && in_array('civi_user_employer', (array)$current_user->roles))  { ?>
<div class="icons_wrapper">
	<div>
	<a href="#"
       class="addfav civi-add-to-follow-candidate add-follow-candidate <?php echo esc_attr($css_class); ?>"
       data-candidate-id="<?php echo intval($id) ?>">
        <?php if ($key !== false) { ?>
            <i class="fa-regular fa-heart" style="color:#2876BB;"></i>
        <?php } else { ?>
            <i class="fa-regular fa-heart" style="color:#2876BB; font-weight: 700;"></i>
        <?php } ?>
    </a>
			</div>
	<div>
	<a class="addfav" href="<?php echo $link_URL; ?>">
		<i class="fa-regular fa-envelope" style="color:#2876BB;"></i>
	</a>
		</div>
		<div>
	<a class="addfav"  href="javascript:void(0)"  onclick="copy('<?php echo $candidate_link; ?>')">
		<i class="fa-regular fa-share-from-square" style="color:#2876BB; padding-left: 3px"></i>
	</a>
</div>
</div>

<?php } elseif ( is_user_logged_in() && in_array('civi_user_candidate', (array)$current_user->roles) ) { ?>
	<div class="logged-out">
	<a href="#popup-form" class="addfav btn-login notice-employer  tooltip" data-notice="<?php esc_attr_e('Please login role Employer to view', 'civi-framework') ?>" data-title="<?php esc_attr_e('Send Offer', 'civi-framework') ?>">
			<i class="fa-regular fa-envelope" style="color:#2876BB;"></i>
		</a>
	</div>	
<div>
	<a class="addfav tooltip"  href="javascript:void(0)"  onclick="copy('<?php echo $candidate_link; ?>')" data-title="<?php esc_attr_e('Share Link', 'civi-framework') ?>">
		<i class="fa-regular fa-share-from-square" style="color:#2876BB; padding-left: 3px"></i>
	</a>
	</div>
<?php } elseif ( !is_user_logged_in() ) { ?>
<div class="logged-out">
	<a href="#popup-form" class="addfav btn-login notice-employer add-follow-candidate <?php echo esc_attr($css_class); ?>" data-candidate-id="<?php echo intval($id) ?>">
       <i class="fa-regular fa-heart" style="color:#2876BB;"></i>
    </a>
</div>
<div class="logged-out" id="rmOverflow">
	<a href="#popup-form" class="addfav btn-login notice-employer">
		<i class="fa-regular fa-envelope" style="color:#2876BB;"></i>
	</a>
	</div>
	<div>
	<a  href="javascript:void(0)"  onclick="copy('<?php echo $candidate_link; ?>')" class="addfav" >
		<i class="fa-regular fa-share-from-square" style="color:#2876BB; padding-left: 3px"></i>
	</a>
</div>
<?php } ?>

<script>
	
	const checkbox_hourly = document.getElementById('hourly_budget');
	const checkbox_daily = document.getElementById('daily_budget');
	const checkbox_project = document.getElementById('project_based_payment');
	
	const box_hourly = document.getElementById('box_hourly');
	const box_daily = document.getElementById('box_daily');
	const box_project = document.getElementById('box_project');
	
	checkbox_hourly.addEventListener('click', function handleClick() {
		if (checkbox_hourly.checked) {
			box_hourly.style.display = 'block';
		} else {
			box_hourly.style.display = 'none';
		}
	});
	checkbox_daily.addEventListener('click', function handleClick() {
		if (checkbox_daily.checked) {
			box_daily.style.display = 'block';
		} else {
			box_daily.style.display = 'none';
		}
	});
	checkbox_project.addEventListener('click', function handleClick() {
		if (checkbox_project.checked) {
			box_project.style.display = 'block';
		} else {
			box_project.style.display = 'none';
		}
	});
</script>