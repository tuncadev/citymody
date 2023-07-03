<article <?php post_class(); ?>>

	<div class="inner-post-wrap">

		<?php
		$elementor_page = get_post_meta(get_the_ID(), '_elementor_edit_mode', true);
		if (empty($elementor_page)) {
		?>

			<!-- list categories -->
			<?php if (!empty(get_the_category_list())) { ?>
				<?php echo get_the_category_list(); ?>
			<?php } ?>

			<!-- post title -->
			<div class="post-title">
				<h1 class="entry-title"><?php the_title(); ?></h1>
			</div>

			<!-- post meta -->
			<?php
			if ('post' === get_post_type()) {
				get_template_part('templates/post/content', 'meta');
			}
			?>

			<!-- post content -->
			<div class="post-content">
				<?php
				the_content();
				wp_link_pages(array(
					'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__('Pages:', 'civi') . '</span>',
					'after'       => '</div>',
					'link_before' => '<span class="page-link">',
					'link_after'  => '</span>',
				));
				?>
			</div>

			<?php get_template_part('templates/post/tags'); ?>

			<?php get_template_part('templates/post/author-bio'); ?>

			<?php get_template_part('templates/post/related'); ?>

		<?php } else { ?>

			<!-- post title -->
			<div class="post-title">
				<h1 class="entry-title"><?php the_title(); ?></h1>
			</div>

			<!-- post content -->
			<div class="post-content elementor-content">
				<?php
				the_content();
				wp_link_pages(array(
					'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__('Pages:', 'civi') . '</span>',
					'after'       => '</div>',
					'link_before' => '<span class="page-link">',
					'link_after'  => '</span>',
				));
				?>
			</div>

		<?php } ?>

	</div>

</article>