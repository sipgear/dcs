<?php


get_header(); // Loads the header.php template. ?>

	<?php $supreme2_theme_settings = get_option(supreme_prefix().'_theme_settings');
	if ( current_theme_supports( 'breadcrumb-trail' ) && $supreme2_theme_settings['supreme_show_breadcrumb'] == 1 ) breadcrumb_trail( array( 'separator' => '&raquo;' ) ); 

	do_action( 'before_content' ); // supreme_before_content ?>

	<section id="content">

		<?php do_action( 'open_content' ); // supreme_open_content ?>

		<div class="hfeed">
			
			<?php apply_filters('tmpl_before-content',supreme_sidebar_before_content() ); // Loads the sidebar-before-content.

			if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); 

					do_action( 'before_entry' ); // supreme_before_entry ?>

					<div id="post-<?php the_ID(); ?>" class="<?php supreme_entry_class(); ?>">

						<?php do_action( 'open_entry' ); // supreme_open_entry 
					
							 do_action('entry-title'); ?>

						<div class="entry-content">
							<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', THEME_DOMAIN ) );
							wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', THEME_DOMAIN ), 'after' => '</p>' ) );
							do_action('entry-edit-link'); ?>
						</div><!-- .entry-content -->
						
						<?php apply_filters('supreme_author_biograply',supreme_author_biography_($post));	// show author biography below post

						do_action( 'close_entry' ); // supreme_close_entry ?>

					</div><!-- .hentry -->

					<?php do_action( 'after_entry' ); // supreme_after_entry 

					apply_filters('tmpl_after-singular',supreme_sidebar_after_singular()); // Loads the sidebar-after-singular.

					do_action( 'after_singular' ); // supreme_after_singular 

					
						// If comments are open or we have at least one comment, load the comments template.
						$theme_options = get_option(supreme_prefix().'_theme_settings');
						$enable_comments_on_page = $theme_options['enable_comments_on_page'];
						if ( $enable_comments_on_page) {
							comments_template( '/comments.php', true ); // Loads the comments.php template. 
						}
					endwhile; 

			endif; 
			
			apply_filters('tmpl_after-content',supreme_sidebar_after_content()); // afetr-content-sidebar use remove filter to dont display it ?>

		</div><!-- .hfeed -->

		<?php do_action( 'close_content' ); // supreme_close_content ?>

	</section><!-- #content -->

	<?php do_action( 'after_content' ); // supreme_after_content
	global $post;
	if(is_plugin_active('woocommerce/woocommerce.php')){
		if($post->ID == get_option('woocommerce_cart_page_id') || $post->ID == get_option('woocommerce_checkout_page_id') || $post->ID == get_option('woocommerce_pay_page_id') || $post->ID == get_option('woocommerce_thanks_page_id') || $post->ID == get_option('woocommerce_myaccount_page_id') || $post->ID == get_option('woocommerce_edit_address_page_id') || $post->ID == get_option('woocommerce_view_order_page_id') || $post->ID == get_option('woocommerce_change_password_page_id') || $post->ID == get_option('woocommerce_logout_page_id') || $post->ID == get_option('woocommerce_lost_password_page_id') )
		{
			apply_filters( 'tmpl-woo_sidebar',supreme_woocommerce_sidebar() );
		}
		else
		{
				get_sidebar();
		}
	}else{
			get_sidebar();
	}
	get_footer(); // Loads the footer.php template. ?>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	