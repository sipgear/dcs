<?php


/* Kill the page if trying to access this template directly. */
if ( 'comments.php' == basename( $_SERVER['SCRIPT_FILENAME'] ) )
	die( __( 'Please do not load this page directly. Thanks!', THEME_DOMAIN ) );

/* If a post password is required or no comments are given and comments/pings are closed, return. */
if ( post_password_required() || ( !have_comments() && !comments_open() && !pings_open() ) )
	return;
?>

<div id="comments-template">

	<div class="comments-wrap">

		<div id="comments">

			<?php if ( have_comments() ) : ?>

				<h3 id="comments-number" class="comments-header"><?php comments_number( __( 'No Comments', THEME_DOMAIN ), __( 'One Comment', THEME_DOMAIN ), __( '% Comments', THEME_DOMAIN ) ); ?></h3>

				<?php do_action( 'before_comment_list' );// supreme_before_comment_list ?>
				
				<?php if ( get_option( 'page_comments' ) ) : ?>
					<div class="comment-navigation comment-pagination">
						<span class="page-numbers"><?php printf( __( 'Page %1$s of %2$s', THEME_DOMAIN ), ( get_query_var( 'cpage' ) ? absint( get_query_var( 'cpage' ) ) : 1 ), get_comment_pages_count() ); ?></span>
						<?php paginate_comments_links(); ?>
					</div><!-- .comment-navigation -->
				<?php endif; ?>

				<ol class="comment-list">
					<?php wp_list_comments( supreme_list_comments_args() ); ?>
				</ol><!-- .comment-list -->

				<?php do_action( 'after_comment_list' ); // supreme_after_comment_list ?>
				
			<?php endif; ?>

			<?php if ( pings_open() && !comments_open() ) : ?>

				<p class="comments-closed pings-open">
					<?php printf( __( 'Comments are closed, but <a href="%1$s" title="Trackback URL for this post">trackbacks</a> and pingbacks are open.', THEME_DOMAIN ), get_trackback_url() ); ?>
				</p><!-- .comments-closed .pings-open -->

			<?php elseif ( !comments_open() ) : ?>

				<p class="comments-closed">
					<?php _e( 'Comments are closed.', THEME_DOMAIN ); ?>
				</p><!-- .comments-closed -->

			<?php endif; ?>

		</div><!-- #comments -->

		<?php 
		$comment_args = array( 'fields' => apply_filters( 'comment_form_default_fields', array(
						'author' => '<div class="form_row clearfix">' .
									'<input id="author" name="author" type="text" value="' .
									esc_attr( $commenter['comment_author'] ) . '" size="30"' . @$aria_req . ' PLACEHOLDER="'.__('Your name',THEME_DOMAIN).'"/>' .
									( $req ? ' <span class="required">*</span>' : '' ) .
									'</div><!-- #form-section-author .form-section -->',
						'email'  => '<div class="form_row clearfix">' .
									'<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . @$aria_req . ' PLACEHOLDER="'.__('Email Address',THEME_DOMAIN).'"/>' .
									( $req ? ' <span class="required">*</span>' : '' ) .
							'</div><!-- #form-section-email .form-section -->',
						'url'    => '<div class="form_row clearfix">' .
									'<input id="url" name="url" type="text" value="' . esc_attr(  $commenter['comment_author_url'] ) . '" size="30"' . @$aria_url . ' PLACEHOLDER="'.__('Website',THEME_DOMAIN).'"/>'.'</div>')),
						'comment_field' => '<div class="form_row clearfix">' .
									'<textarea id="comments" name="comment" cols="45" rows="8" aria-required="true" PLACEHOLDER="'.__('Comments',THEME_DOMAIN).'"></textarea>' .
									( $req ? ' <span class="required">*</span>' : '' ) .
									'</div><!-- #form-section-comment .form-section -->',
						'comment_notes_after' => '',
						'title_reply' => __( 'Add a comment', THEME_DOMAIN ),
					);
					if(get_option('default_comment_status') =='open'){
						comment_form($comment_args); } // Loads the comment form.  ?>

	</div><!-- .comments-wrap -->

</div><!-- #comments-template -->