<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="entry-my-page my-jobs">
    <div class="entry-title">
        <h4><?php esc_html_e('Membership Details', 'civi-framework'); ?></h4>
    </div>
    <div class="tab-dashboard">
		<div class="tab-content">
			<?php 
				$string_with_shortcodes = "[ihc-account-page-subscriptions-table]";
				$content = preg_replace('/<!--(.|\s)*?-->/', 'nonetbl', do_shortcode($string_with_shortcodes));
				
				$string_with_shortcodes = "[ihc-account-page-subscriptions-table]";
			if (strcmp($content, strip_tags($content)) == 0)
				{
				echo do_shortcode("[ihc-select-level]");
				}
				else
				{
				echo do_shortcode($string_with_shortcodes);
				} 
					
			?>
			
			<?php //$shortcode_content = do_shortcode( 'ihc-account-page-subscriptions-table' );  ?>
			<?php //echo remove_html_comments($shortcode_content); ?>
			<?php //if ($clean_return === '') { echo "Empty"; } else { echo "Not empty"; } ?>
			<?php // echo $clean_return; ?>
		</div>
	</div>
</div>
