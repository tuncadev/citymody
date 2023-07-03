<?php if ('yes' === $settings['show_overlay_title'] || !empty($settings['overlay_meta_data'])) : ?>
	<div class="post-overlay-background"></div>

	<div class="post-overlay-content">
		<div class="post-overlay-content-inner">
			<div class="post-overlay-info">
				<?php if (!empty($settings['overlay_meta_data'])) : ?>
					<div class="post-overlay-meta">
						<?php foreach ($settings['overlay_meta_data'] as $data) : ?>

							<?php if ('date' === $data['meta']) : ?>
								<div class="post-overlay-date"><?php echo get_the_date(); ?></div>
							<?php elseif ('author' === $data['meta']) : ?>
								<div class="post-overlay-author">
									<?php esc_html_e('by', 'civi'); ?>
									<a href="<?php echo get_author_posts_url(get_the_author_meta('ID'), get_the_author_meta('user_nicename')); ?>">
										<?php the_author(); ?>
									</a>
								</div>
							<?php elseif ('comments' === $data['meta']) : ?>
								<div class="post-overlay-comments">
									<?php
									$comment_count = get_comments_number();
									$comment_count .= $comment_count > 1 ? esc_html__(' Comments', 'civi') : esc_html__(' Comment', 'civi');
									?>
									<?php esc_html_e($comment_count); ?>
								</div>
							<?php endif; ?>

						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ('yes' === $settings['show_overlay_title']) : ?>
					<h3 class="post-overlay-title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h3>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php endif; ?>