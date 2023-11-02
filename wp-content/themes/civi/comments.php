<?php

/**
 * The template for displaying comments
 *
 * The area of the page that contains both current comments
 * and the comment form.
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if (post_password_required()) {
	return;
}
?>

<div id="comments" class="comments-area block-line">

	<?php if (have_comments()) : ?>
		<h2 class="comments-title">
			<?php
			$comments_number = get_comments_number();
			if ('1' === $comments_number) {
				/* translators: %s: post title */
				printf(_x('1 Comment', 'comments title', 'civi'), get_the_title());
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
		</h2>

		<?php the_comments_navigation(); ?>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 50,
					'callback' 	  => 'Civi_Templates::render_comments',
				)
			);
			?>
		</ol><!-- .comment-list -->

		<?php the_comments_navigation(); ?>

	<?php endif; // Check for have_comments().
	?>

	<?php
	// If comments are closed and there are comments, let's leave a little note, shall we?
	if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) :
	?>
		<p class="no-comments"><?php esc_html_e('Comments are closed.', 'civi'); ?></p>
	<?php endif; ?>

	<?php
	// Comment Form
	$args = array(
		'comment_field'  => '<p class="comment-form-comment form-row col-12"><textarea id="comment" class="input-text" name="comment" cols="45" rows="7" aria-required="true" placeholder="' . esc_attr__('Comment', 'civi') . '" ></textarea></p>',
		'fields'         => apply_filters(
			'comment_form_default_fields',
			array(
				'author' => '<p class="comment-form-author form-row col-12 col-md-6"><input id="author" class="input-text" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="52" placeholder="' . esc_attr__('Name', 'civi') . '" /></p>',
				'email'  => '<p class="comment-form-email form-row col-12 col-md-6"><input id="email" class="input-text" name="email" type="text" value="' . esc_attr($commenter['comment_author_email']) . '" size="52" placeholder="' . esc_attr__('Email', 'civi') . '" /></p>',
			)
		),
		'title_reply'  => esc_html__('Leave your thought here', 'civi'),
		'class_form'   => 'row',
		'class_submit' => 'btn-civi',
		'label_submit' => esc_html__('Submit', 'civi'),
	);

	comment_form($args);
	?>

</div><!-- .comments-area -->
