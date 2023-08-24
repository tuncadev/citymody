<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $current_user;
wp_get_current_user();
wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'company-follow');

$key          = false;
$user_id      = $current_user->ID;
$my_follow = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'my_follow', true);
$id           = get_the_ID();

if (!empty($company_id)) {
    $id = $company_id;
}

if (!empty($my_follow)) {
    $key = array_search($id, $my_follow);
}

$css_class = '';
if ($key !== false) {
    $css_class = 'added';
}
?>
<?php if (is_user_logged_in() && in_array('civi_user_candidate', (array)$current_user->roles)) { ?>
    <a href="#" class="addfav civi-button button-outline-accent civi-add-to-follow add-follow-company <?php echo esc_attr($css_class); ?>" data-company-id="<?php echo intval($id) ?>">
        <?php if ($key !== false) {?>
		  <i class="fa-regular fa-heart" style="color:#2876BB; font-weight: 700;"></i>
		     <?php esc_html_e('Following', 'civi-framework') ?>
        <?php } else { ?>
		  <i class="fa-regular fa-heart" style="color:#2876BB;"></i>
		     <?php esc_html_e('Follow', 'civi-framework') ?>
        <?php } ?>
    </a>
<?php } else { ?>
	<div class="logged-out">
	<a href="#popup-form" class="addfav btn-login add-follow-company <?php echo esc_attr($css_class); ?>" data-candidate-id="<?php echo intval($id) ?>">
        <?php if ($key !== false) { ?>
            <i class="fa-regular fa-heart" style="color:#2876BB;"></i>
        <?php } else { ?>
            <i class="fa-regular fa-heart" style="color:#2876BB;"></i>
        <?php } ?>
    </a>	
</div>
<div>
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