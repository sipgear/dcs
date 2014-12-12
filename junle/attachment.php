<?php


get_header(); // Loads the header.php template. 
	do_action( 'before_content' ); // supreme_before_content
	
	$supreme2_theme_settings = get_option(supreme_prefix().'_theme_settings');
	if ( current_theme_supports( 'breadcrumb-trail' ) && $supreme2_theme_settings['supreme_show_breadcrumb'] == 1 ) breadcrumb_trail( array( 'separator' => '&raquo;' ) ); ?>

	<section id="content">

		<?php do_action( 'open_content' ); // supreme_open_content ?>

		<div class="hfeed">

			<?php if ( have_posts() ) :

				while ( have_posts() ) : the_post();

					do_action( 'before_entry' ); // supreme_before_entry ?>

					<article id="post-<?php the_ID(); ?>" class="<?php supreme_entry_class(); ?>">

						<?php do_action( 'open_entry' ); // supreme_open_entry 

						do_action('entry-title');
						
						apply_filters( 'tmpl-entry',supreme_sidebar_entry() ); // Loads the sidebar-entry ?>

						<div class="entry-content">
							<?php if ( wp_attachment_is_image( get_the_ID() ) ) : ?>

								<p class="attachment-image">
									<?php echo wp_get_attachment_image( get_the_ID(), 'full', false, array( 'class' => 'aligncenter' ) ); ?>
								</p><!-- .attachment-image -->

							<?php else : ?>

								<p>
								<?php supreme_attachment(); // Function for handling non-image attachments. ?>
								</p>

								<p class="download">
									<a href="<?php echo wp_get_attachment_url(); ?>" title="<?php the_title_attribute(); ?>" rel="enclosure" type="<?php echo get_post_mime_type(); ?>"><?php printf( __( 'Download "%s";', THEME_DOMAIN ), the_title( '<span class="fn">', '</span>', false) ); ?></a>
								</p><!-- .download -->

							<?php endif;

							the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', THEME_DOMAIN ) );
							wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', THEME_DOMAIN ), 'after' => '</p>' ) );
							
							if ( wp_attachment_is_image( get_the_ID() ) ) {
								$gallery = do_shortcode( sprintf( '[gallery id="%1$s" exclude="%2$s" columns="5" numberposts="16" orderby="rand"]', $post->post_parent, get_the_ID() ) );
							if ( !empty( $gallery ) )
								echo '<h3>' . __( 'Gallery', THEME_DOMAIN ) . '</h3>' . $gallery;
							}
							?>
						</div><!-- .entry-content -->

						<?php do_action( 'close_entry' ); // supreme_close_entry ?>

					</article><!-- .hentry -->

					<?php do_action( 'after_entry' ); // supreme_after_entry 
					
					apply_filters('tmpl_after-singular',supreme_sidebar_after_singular()); // Loads the sidebar-after-singular. 

					do_action( 'after_singular' ); // supreme_after_singular

					comments_template( '/comments.php', true ); // Loads the comments.php template.

				endwhile;

			endif; ?>

		</div><!-- .hfeed -->

		<?php do_action( 'close_content' ); // supreme_close_content 

		 apply_filters('supreme_attach_loop_navigation',supreme_loop_navigation($post)); ?>

	</section><!-- #content -->

	<?php do_action( 'after_content' ); // supreme_after_content
get_sidebar();
get_footer(); // Loads the footer.php template. ?>