<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
	<div id="comment-<?php comment_ID(); ?>" class="comment-body">
		<div class="inner-comment">

			<div class="comment-author">
				<?php if (get_avatar($comment, $args['avatar_size'])) : ?>
					<div class="entry-avatar">
						<?php echo get_avatar($comment, $args['avatar_size']); ?>
					</div>
				<?php endif; ?>

				<div class="entry-detail">
					<div class="author-name"><?php printf('%s', get_comment_author_link()) ?></div>
					<div class="comment-time">
						<span><?php printf(_x('%s ago', '%s = human-readable time difference', 'civi'), human_time_diff(get_comment_time('U'), current_time('timestamp'))); ?></span>
					</div>
				</div>
			</div>

			<?php if (get_comment_text()) : ?>
				<div class="comment-content">
					<?php comment_text(); ?>
				</div>
			<?php endif; ?>

			<div class="comment-meta">
				<?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth'], 'reply_text' => '<i class="fas fa-comment-alt-lines"></i><span>' . esc_html__('Reply', 'civi') . '</span>'))) ?>
				<?php edit_comment_link('<i class="fas fa-comment-alt-lines"></i><span>' . esc_html__('Edit', 'civi') . '</span>'); ?>
				<?php if ($comment->comment_approved == '0') : ?>
					<em><?php esc_html_e('Your comment is awaiting moderation.', 'civi'); ?></em>
				<?php endif; ?>
			</div>

		</div>
	</div>
</li>