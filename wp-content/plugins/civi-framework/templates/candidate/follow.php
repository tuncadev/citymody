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
?>
<?php if ( is_user_logged_in() && in_array('civi_user_employer', (array)$current_user->roles))  { ?>
<div class="icons_wrapper">
	<a href="#"
       class="addfav civi-add-to-follow-candidate add-follow-candidate <?php echo esc_attr($css_class); ?>"
       data-candidate-id="<?php echo intval($id) ?>">
        <?php if ($key !== false) { ?>
            <i class="fa-regular fa-heart" style="color:#2876BB;"></i>
        <?php } else { ?>
            <i class="fa-regular fa-heart" style="color:#2876BB; font-weight: 700;"></i>
        <?php } ?>
    </a>
	<a class="addfav">
		<i class="fa-regular fa-envelope" style="color:#2876BB;"></i>
	</a>
	<a class="addfav"  href="javascript:void(0)"  onclick="copy('<?php echo $candidate_link; ?>')">
		<i class="fa-regular fa-share-from-square" style="color:#2876BB; padding-left: 3px"></i>
	</a>

</div>

<?php } elseif ( is_user_logged_in() && in_array('civi_user_candidate', (array)$current_user->roles) ) { ?>
	<a class="addfav"  href="javascript:void(0)"  onclick="copy('<?php echo $candidate_link; ?>')">
		<i class="fa-regular fa-share-from-square" style="color:#2876BB; padding-left: 3px"></i>
	</a>
<?php } elseif ( !is_user_logged_in() ) { ?>
<div class="logged-out">
	<a href="#popup-form" class="addfav btn-login notice-employer add-follow-candidate <?php echo esc_attr($css_class); ?>" data-candidate-id="<?php echo intval($id) ?>">
        <?php if ($key !== false) { ?>
            <i class="fa-regular fa-heart" style="color:#2876BB;"></i>
        <?php } else { ?>
            <i class="fa-regular fa-heart" style="color:#2876BB;"></i>
        <?php } ?>
    </a>
		<a href="#popup-form" class="addfav btn-login notice-employer">
		<i class="fa-regular fa-envelope" style="color:#2876BB;"></i>
	</a>
	<a  href="javascript:void(0)"  onclick="copy('<?php echo $candidate_link; ?>')" class="addfav" >
		<i class="fa-regular fa-share-from-square" style="color:#2876BB; padding-left: 3px"></i>
	</a>

</div>
<?php } ?>