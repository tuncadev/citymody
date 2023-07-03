<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$custom_field_candidate = civi_render_custom_field('candidate');
if (count($custom_field_candidate) > 0) :
    $tabs_array = array();
    foreach ($custom_field_candidate as $field) {
        if ((!in_array($field['section'], $tabs_array)) && !empty($field['section'])) {
            $tabs_array[] = $field['section'];
        }
    }
    foreach ($tabs_array as $value) {
        $tabs_id = str_replace(" ", "-", $value); ?>
        <div class="block-archive-inner candidate-single-field additional-<?php echo $tabs_id;?>">
					<?php if($tabs_id === "Physical-Attributes") { ?>
		<span style="width: 100%;">
			<h4 class="title-candidate-att" >Physical-Attributes</h4>
			<hr>
		</span>
			
		<?php } ?>	
            <?php civi_custom_field_single_candidate($value,true);?>
        </div>
    <?php } ?>
<?php endif; ?>
