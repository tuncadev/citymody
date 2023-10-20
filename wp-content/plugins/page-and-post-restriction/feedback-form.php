<?php

function papr_display_feedback_form() {
	if ( 'plugins.php' != basename( $_SERVER['PHP_SELF'] ) ) {
		return;
	}
    wp_enqueue_style( 'papr_admin_plugin_feedback_style', plugins_url( '/includes/css/papr_feedback_style.min.css', __FILE__ ) );
	?>

    <div id="papr_feedback_modal" class="mo_papr_modal">

        <div class="mo_papr_feedback_content">
            <h3 style="margin: 2%; text-align:center;">
                <b>Your feedback</b>
                <span class="papr_close">&times;</span>
            </h3>
            <hr class="mo_papr_feedback_hr">

            <form name="f" method="post" id="papr_feedback">
				<?php wp_nonce_field("papr_feedback");?>
                <input type="hidden" name="option" value="papr_feedback"/>
                <div>
                    <h4 style="margin: 2%;">Please tell us what went wrong.<br></h4>

                    <div style="text-align: left;padding:2% 10%;">
                        <input type="radio" name="papr_reason" value="Missing Features" id="papr_feature"/>
                        <label for="papr_feature" class="mo_papr_feedback_option" > Does not have the features I'm looking for</label>
                        <br>

                        <input type="radio" name="papr_reason" value="Costly" id="papr_costly" class="mo_papr_feedback_radio" />
                        <label for="papr_costly" class="mo_papr_feedback_option">Do not want to upgrade - Too costly</label>
                        <br>

                        <input type="radio" name="papr_reason" value="Confusing" id="papr_confusing" class="mo_papr_feedback_radio"/>
                        <label for="papr_confusing" class="mo_papr_feedback_option">Confusing Interface</label>
                        <br>

                        <input type="radio" name="papr_reason" value="Bugs" id="papr_bugs" class="mo_papr_feedback_radio"/>
                        <label for="papr_bugs" class="mo_papr_feedback_option">Bugs in the plugin</label>
                        <br>

                        <input type="radio" name="papr_reason" value="other" id="papr_other" class="mo_papr_feedback_radio"/>
                        <label for="papr_other" class="mo_papr_feedback_option">Other Reasons</label>
                        <br><br>

                        <textarea id="papr_query_feedback" name="papr_query_feedback" rows="4" style="width: 100%"
                                  placeholder="Tell us what happened!"></textarea>
                    </div>

                    <hr class="mo_papr_feedback_hr">

                    <div>Thank you for your valuable time</div>
                    <br>

                    <div class="mo_papr_feedback_footer">
                        <input type="submit" name="miniorange_feedback_submit"
                               class="papr-btn-cstm rounded" value="Send"/>
                        <input type="button" name="miniorange_skip_feedback"
                               class="papr-btn-cstm rounded" value="Skip" onclick="document.getElementById('papr_feedback_form_close').submit();"/>
                    </div>
                </div>
            </form>

            <form name="f" method="post" action="" id="papr_feedback_form_close">
				<?php wp_nonce_field("papr_skip_feedback");?>
                <input type="hidden" name="option" value="papr_skip_feedback"/>
            </form>
        </div>
    </div>

    <script>
        jQuery('a[aria-label="Deactivate Page Restriction WordPress (WP) - Protect WP Pages/Post"]').click(function () {

            var mo_modal = document.getElementById('papr_feedback_modal');

            var span = document.getElementsByClassName("papr_close")[0];

            mo_modal.style.display = "block";
            document.querySelector("#papr_query_feedback").focus();
            span.onclick = function () {
                mo_modal.style.display = "none";
                jQuery('#papr_feedback_form_close').submit();
            };

            window.onclick = function (event) {
                if (event.target === mo_modal) {
                    mo_modal.style.display = "none";
                }
            };
            return false;

        });
    </script>
    <?php
}

?>