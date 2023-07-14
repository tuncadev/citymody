<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

 

global $hide_candidate_fields, $candidate_data, $candidate_meta_data, $current_user;

$user_id = $current_user->ID;
$candidate_id = civi_get_post_id_candidate();
$candidate_hesapturu = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_hesapturu']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_hesapturu'][0] : '';
$candidate_namelast = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_namelast']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_namelast'][0] : '';
$candidate_kimlik = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_kimlik']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_kimlik'][0] : '';
$candidate_telefonu = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_telefonu']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_telefonu'][0] : '';
$candidate_iban = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_iban']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_iban'][0] : '';
$candidate_adres = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_adres']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_adres'][0] : '';

?>
<div id="tab-paymentinfo" class="tab-info">
    <div class="experience-info block-from">
	        <h5><?php esc_html_e('Bank and Payment Information', 'civi-framework') ?></h5>

        <div class="sub-head"><?php esc_html_e('Please fill in this form carefully in order to recieve paments from projects.', 'civi-framework') ?></div>
		<div class="civi-candidate-warpper">
			<div class="row">
				<div class="form-group col-md-6">
					<label for="candidate_namelast"><?php echo __("Full Name", "civi-framework"); ?></label>
					<input type="text" name="candidate_namelast" id="candidate_namelast" value="<?php echo esc_attr_e($candidate_namelast); ?>">
				</div>
				<div class="form-group col-md-6"></div>
				<div class="form-group col-md-6">
					<label for="candidate_kimlik"><?php echo __("TC Kimlik No", "civi-framework"); ?></label>
					<input type="text" name="candidate_kimlik" id="candidate_kimlik" maxLength="11"  value="<?php echo esc_attr_e($candidate_kimlik); ?>" maxlength="11" minlegth="11" onfocusout="myFunction(this, this.value)">
					<p id="form-error" style="color:red;"></p>
				</div>
				<div class="form-group col-md-6">
					<label for="telephone"><?php echo __("Phone Number", "civi-framework"); ?></label>
					<input type="tel" name="candidate_telefonu" id="input-telephone" class="form-control point-mark point-active valid" value="<?php echo esc_attr_e($candidate_telefonu); ?>" data-mask="(0999) 999 99 99" placeholder="Örn: (0232) 555 55 55">
				</div>	
			</div>
		</div>
		<div class="sub-head" style="border-top:1px solid #cccccc; padding-top: 20px;"><?php esc_html_e('Bank Information', 'civi-framework') ?></div>
		<div class="civi-candidate-warpper">
			<div class="row">
				<div class="form-group col-md-6">
					<label for="candidate_hesapturu"><?php echo __("Account Type", "civi-framework"); ?></label>
					<select name="candidate_hesapturu" id="candidate_hesapturu">
						<option <?php if( $candidate_hesapturu == "") { echo "selected"; } ?> value=""><?php echo esc_attr_e("Please select option", "civi-framework"); ?></option>
						<option <?php if( $candidate_hesapturu == "Individual account") { echo "selected"; } ?> value="Individual account"><?php echo esc_html_e("Individual account", "civi-framework"); ?></option>
						<option <?php if( $candidate_hesapturu == "Sole Proprietorship Account (Business)") { echo "selected"; } ?> value="Sole Proprietorship Account (Business)"><?php echo esc_html_e("Sole Proprietorship Account (Business)", "civi-framework"); ?></option>
					</select>
				</div>
				<div class="form-group col-md-6"></div>
				<div class="form-group col-md-6">
					<label for="iban"><?php echo __("IBAN Number", "civi-framework"); ?></label>
					<input type="text" name="candidate_iban" id="candidate_iban" placeholder="TR32 0010 0099 9990 1234 5678 90" data-mask="TR 99 9999 9999 9999 9999 9999 99" value="<?php echo esc_attr_e($candidate_iban); ?>">
				</div>	
				<div class="form-group col-md-6"></div>
				<div class="form-group col-md-6">
					<label for="candidate_adres"><?php echo __("Billing Address", "civi-framework"); ?></label>
					<input type="text" name="candidate_adres" id="candidate_adres" value="<?php echo esc_attr_e($candidate_adres); ?>">
				</div>
				<div class="form-group col-md-6"></div>
			</div>
		</div>
	</div>
			
</div>
<script>
	function myFunction(x,y) {
 	document.getElementById("form-error").innerHTML = "";
  	var maximum = x.maxLength;
  	var myValLength = y.length;
  	var myVar = document.getElementById("candidate_kimlik").value;
  	var myInt = parseInt(myVar, 10);  
 	
    if(isNaN(myInt)) { 
       	document.getElementById("form-error").innerHTML = "<?php echo esc_attr_e("Please only numbers","civi-framework"); ?>";
    	document.getElementById("form-error").style.display = "block";
    } else { 
    	document.getElementById("form-error").innerHTML = "";
        if(myValLength !== maximum) {
        	document.getElementById("form-error").innerHTML = "<?php echo esc_attr_e("Please enter 11 digit TC ID Number","civi-framework"); ?>";
    		document.getElementById("form-error").style.display = "block";
        } else {
        return true;
        }
    	
    }
}
</script>
<style>
	.dd:before {
  content: "TEST";
  color: #000000;
  position:absolute;
  margin-left: 7px;
  top:5px;
    height: auto;
  width: auto;

}
</style>
<script src="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.min.js"></script>