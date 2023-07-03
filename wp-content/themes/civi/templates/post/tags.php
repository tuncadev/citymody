<?php
$tags = get_the_tags($post->ID);
if (!empty($tags)) {
?>
	<div class="post-tags">
		<i class="far fa-tags normal"></i>

		<div class="entry-tag list-item">
			<?php
			foreach ($tags as $tag) {
				$tag_link = get_tag_link($tag->term_id);
			?>
				<a href="<?php echo esc_url($tag_link); ?>"><?php esc_html_e($tag->name); ?></a>
			<?php } ?>
		</div>
	</div>
<?php } ?>