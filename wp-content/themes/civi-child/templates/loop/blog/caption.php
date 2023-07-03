<div class="post-caption">

	<div class="top-blog">
		<?php if (!empty($settings['show_caption_meta'])) : ?>
			<?php $meta = $settings['show_caption_meta']; ?>
			<div class="post-meta">
				<div class="inner">
					<?php if (in_array('author', $meta, true)) : ?>
						<div class="post-author">
							<span><?php esc_html_e('by', 'civi'); ?></span>
							<?php the_author_posts_link(); ?>
						</div>
					<?php endif; ?>

					<?php if (in_array('date', $meta, true)) : ?>
						<div class="post-time">
							<span>
								<?php printf('<span>%1$s</span>', esc_html(get_the_time(get_option('date_format')))); ?>
							</span>
						</div>
					<?php endif; ?>

					<?php if (in_array('comments', $meta, true)) : ?>
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
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
		<?php if ('yes' === $settings['show_caption_category']) : ?>
			<!-- list categories -->
			<?php echo get_the_category_list(); ?>
		<?php endif; ?>
	</div>

	<h3 class="post-title">
		<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	</h3>

	<?php if ('yes' === $settings['show_caption_excerpt']) : ?>
		<?php
		if (empty($settings['excerpt_length'])) {
			$settings['excerpt_length'] = 10;
		}
		?>
		<div class="post-excerpt">
			<p><?php echo wp_trim_words(get_the_excerpt($post->ID), $settings['excerpt_length']); ?></p>
		</div>
	<?php endif; ?>

	<?php if ('yes' === $settings['show_caption_read_more']) : ?>
		<div class="post-footer">
			<?php if ('yes' === $settings['show_caption_read_more']) : ?>
				<?php
				$read_more_text = !empty($settings['read_more_text']) ? $settings['read_more_text'] : esc_html__('Read more', 'civi');
				?>

				<!-- button readmore -->
				<div class="btn-readmore civi-button button-border-bottom">
					<a href="<?php the_permalink(); ?>">
						<?php esc_html_e($read_more_text); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

</div>