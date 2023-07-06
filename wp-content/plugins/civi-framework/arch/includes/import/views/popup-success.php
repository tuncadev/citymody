<?php

/**
 * Success content for popup after importing
 *
 * @package Civi_Framework
 */

if (!defined('ABSPATH')) {
	exit();
}
?>
<div class="animated fadeInRight" id="import-success">
	<h4 class="civi-popup__title"><?php esc_html_e('All done!', 'civi-framework'); ?></h4>
	<p class="civi-popup__subtitle"><?php esc_html_e('Import is successful! Now customization is as easy as pie. Enjoy it!', 'civi-framework'); ?></p>
	<div class="civi-popup__footer">
		<div class="civi-popup__buttons">
			<a href="#" class="civi-popup__close-button"><?php esc_html_e('Close', 'civi-framework'); ?></a>
			<a href="<?php echo esc_url(site_url('/')); ?>" target="_blank" class="civi-popup__next-button"><?php esc_html_e('View your website', 'civi-framework'); ?></a>
		</div>
	</div>
</div>