<div class="post-meta">
	<div class="post-author">
		<span><?php esc_html_e('by', 'civi'); ?></span>
		<?php the_author_posts_link(); ?>
	</div>
	<div class="post-time">
		<span>
			<?php printf('<span>%1$s</span>', esc_html(get_the_time(get_option('date_format')))); ?>
		</span>
	</div>
	<div class="post-comment">
		<span>
			<?php
			$comments_number = get_comments_number();
			if ('1' === $comments_number) {
				/* translators: %s: post title */
				printf(_x('One Comment', 'comments title', 'civi'), get_the_title());
			} else {
				printf(
					/* translators: 1: number of comments, 2: post title */
					_nx(
						'%1$s Comment',
						'%1$s Comments',
						$comments_number,
						'comments title',
						'civi'
					),
					number_format_i18n($comments_number),
					get_the_title()
				);
			}
			?>
		</span>
	</div>
</div><!-- .entry-meta -->