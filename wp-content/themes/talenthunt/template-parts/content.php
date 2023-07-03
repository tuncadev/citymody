<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package talenthunt
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php
		add_image_size( 'single-feature', 1024, 768, true );
		if ( 'post' === get_post_type() ) : 
			if ( has_post_thumbnail() ) {
				$img_class="has-featured-img";
				$img_url = wp_get_attachment_url(get_post_thumbnail_id());
					if( !empty($img_url) && ( !is_single() ) ){
						$class = is_single() ? '' : ''; 
						echo '<div class="post_image">';
							echo '<a href="'.esc_url(get_the_permalink()).'">';
								$width = is_single() ? '' : '780'; 
								echo '<img src="'.the_post_thumbnail().'"  />';  // WPCS: XSS OK
							echo '</a>';
						echo '</div>';
					}   
			}else{
				$img_class="";
			}
		if ( is_single() ) :
			echo '<div class="blog-post-single-content">';
				if( !empty($img_url) ){
					echo '<img src="'.the_post_thumbnail().'" class="" />';  // WPCS: XSS OK
				}
				echo the_content();
			echo '</div>';
		else: ?>
			<div class="description blog-post-content-wrapper <?php echo esc_html($img_class); ?>">
				<div class="post-title-wrapper">	
						<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
					?>
					<div class="post-meta">
						<?php  talenthunt_kaya_posted_on(); ?>
					</div>
				</div>
				<div class="post-desc">
					<?php 
					if ( ! has_excerpt() ) {
						//echo talenthunt_kaya_content_display_words(50000);
						echo  the_content();
					}else{
						echo  the_excerpt(); 	
					}

					wp_link_pages( array(
						'before'      => '<div class="page-links">' . esc_html__( 'Pages:', 'talenthunt' ),
						'after'       => '</div>',
						'link_before' => '<span class="page-numbers">',
						'link_after'  => '</span>',
					) ); ?>
				</div>
			</div>
		<?php endif;  ?>				
		<?php
	endif; ?>
</article><!-- #post-## -->
