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

				<?php woocommerce_content(); ?>
<?php
			endif; 
			
			apply_filters('tmpl_after-content',supreme_sidebar_after_content()); // afetr-content-sidebar use remove filter to dont display it ?>

		</div><!-- .hfeed -->

		<?php do_action( 'close_content' ); // supreme_close_content ?>

	</section><!-- #content -->

	<?php do_action( 'after_content' ); // supreme_after_content
	apply_filters( 'tmpl-woo_sidebar',supreme_woocommerce_sidebar() ); // Loads the front page sidebar.
	get_footer(); // Loads the footer.php template. ?>