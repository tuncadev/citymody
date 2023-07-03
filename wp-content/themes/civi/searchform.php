<?php

/**
 * Search form
 *
 * @package Civi
 */

$post_type    = 'post';
$jobs_holder = esc_html__('Search posts...', 'civi');

?>
<form role="search" method="get" class="custom-form-search" action="<?php echo esc_url(home_url('/')); ?>">
	<div>
		<label class="screen-reader-text"><?php esc_html_e('Search for:', 'civi'); ?></label>
		<input type="text" class="ip-search" name="s" placeholder="<?php echo esc_attr($jobs_holder); ?>" />
		<input type="hidden" name="post_type" value="<?php echo esc_attr($post_type); ?>" />
		<button type="submit" class="search-submit">
			<span><?php esc_html_e('Search', 'civi'); ?></span>
			<i class="far fa-search large"></i>
		</button>
	</div>
</form>