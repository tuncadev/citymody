<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
$classes = array('block-search', 'search-form-wrapper', 'canvas-search');
wp_enqueue_script('jquery-ui-autocomplete');
if (!class_exists("Civi_Framework")) {
	return;
}
?>
<div class="<?php echo join(' ', $classes); ?>">
	<div class="bg-overlay"></div>
	<form action="<?php echo esc_url(home_url('/')); ?>" method="get" class="form-search-canvas">
		<a href="#" class="btn-close"><i class="far fa-times"></i></a>
		<div class="jobs-search-inner">
			<?php $jobs_skills = array();
			$taxonomy_kills = get_categories(
				array(
					'taxonomy' => 'jobs-skills',
					'orderby' => 'name',
					'order' => 'ASC',
					'hide_empty' => false,
					'parent' => 0
				)
			);
			if (!empty($taxonomy_kills)) {
				foreach ($taxonomy_kills as $term) {
					$jobs_skills[] = $term->name;
				}
			}
			$jobs_keyword = json_encode($jobs_skills);
			?>
			<div class="form-group">
				<input class="jobs-search-canvas archive-search-control" data-key='<?php esc_html_e($jobs_keyword) ?>' id="jobs_filter_search-canvas" type="text" name="s" placeholder="<?php esc_attr_e('Jobs title or keywords', 'civi') ?>" autocomplete="off">
				<span class="btn-filter-search"><i class="far fa-search"></i></span>
			</div>
			<div class="form-group">
				<div class="select2-field">
					<select name="location" class="civi-select2">
						<?php echo '<option value="">' . esc_html__('All location', 'civi') . '</option>'; ?>
						<?php civi_get_taxonomy('jobs-location', false, false); ?>
					</select>
				</div>
				<i class="fas fa-map-marker-alt"></i>
			</div>
			<div class="form-group">
				<button type="submit" class="btn-jobs-search civi-button">
					<?php esc_html_e('Search', 'civi') ?>
				</button>
			</div>
		</div>
		<input type="hidden" name="post_type" class="post-type" value="jobs">
	</form>
</div>
