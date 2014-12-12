<?php


get_header(); // Loads the header.php template.

    do_action( 'before_content' ); // supreme_before_content 
	
	$supreme2_theme_settings = get_option(supreme_prefix().'_theme_settings');
	if ( current_theme_supports( 'breadcrumb-trail' ) && $supreme2_theme_settings['supreme_show_breadcrumb'] == 1 ) breadcrumb_trail( array( 'separator' => '&raquo;' ) ); ?>

	<section id="content">

		<?php do_action( 'open_content' ); // supreme_open_content 

		
		?>

		<div class="hfeed">

			<?php get_template_part( 'loop-meta' ); // Loads the loop-meta.php template. 
			?>
            <div class="twp_search_cont"><?php get_template_part('searchform'); ?></div>
            
			<?php
			apply_filters('tmpl_before-content',supreme_sidebar_before_content() ); // Loads the sidebar-before-content.
			
				
					get_template_part( 'loop' ); // Loads the loop.php template.
				
		
			apply_filters('tmpl_after-content',supreme_sidebar_after_content()); // afetr-content-sidebar use remove filter to dont display it ?>

		</div><!-- .hfeed -->

		<?php do_action( 'close_content' ); // supreme_close_content

		apply_filters('supreme_search_loop_navigation',supreme_loop_navigation($post)); // Loads the loop-navigation .; // Loads the loop-nav.php template. ?>

	</section><!-- #content -->

	<?php do_action( 'after_content' ); // supreme_after_content
	get_sidebar();
	get_footer(); // Loads the footer.php template. ?>