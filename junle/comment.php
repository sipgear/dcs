<?php


	global $post, $comment;
?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
	<?php do_action( 'before_comment' ); // supreme_before_comment ?>
	
		<div id="comment-<?php comment_ID(); ?>" class="comment-wrap">
		<?php do_action( 'open_comment' ); // supreme_open_comment ?>
			<div class="comment-header comment-author vcard">
				<?php
				   echo supreme_avatar();
				echo apply_atomic_shortcode( 'comment_meta', '<div class="comment-meta">[comment-author] [comment-published] [comment-permalink before=" . "] [comment-edit-link before=" . "]</div>' ); ?>
			
			</div><!-- .comment-meta -->

			<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'twentytwelve' ); ?></p>
			<?php endif; ?>

			<div class="comment-content comment">
				<?php if ( '0' == $comment->comment_approved ) : ?>
					<?php echo apply_atomic_shortcode( 'comment_moderation', '<p class="alert moderation">' . __( 'Your comment is awaiting moderation.', THEME_DOMAIN ) . '</p>' ); ?>
				<?php endif; ?>

				<?php comment_text( $comment->comment_ID ); ?>
			</div><!-- .comment-content -->

			<?php echo apply_atomic_shortcode( 'comment_meta', '<div class="templatic_comment">[comment-reply-link]</div>' ); ?>
				<?php do_action( 'close_comment' ); // supreme_close_comment ?>
		</div><!-- #comment-## -->
		<?php do_action( 'after_comment' ); // supreme_after_comment ?>


