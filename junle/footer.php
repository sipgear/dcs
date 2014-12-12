<?php

			$supreme2_theme_settings = get_option(supreme_prefix().'_theme_settings');	
		do_action( 'close_main' ); // supreme_close_main ?>
</div>
<!-- .wrap -->

<?php do_action( 'after_main' ); // supreme_after_main ?>
</div>
<!-- #container -->

<?php do_action( 'close_body' ); // supreme_close_body 
	apply_filters('tmpl_subsidiary_nav',supreme_subsidiary_navigation()); // Loads the menu-subsidiary.php template.
	do_action( 'before_footer' ); // supreme_before_footer ?>
	
<footer id="footer" class="clearfix">
							<div class="footer_widget_wrap clearfix">
														<?php do_action( 'open_footer' ); // supreme_open_footer
																					if(is_active_sidebar('footer')):
																					echo '<div class="footer_area1">';dynamic_sidebar('footer');echo '</div>';
																					endif;
																					if(is_active_sidebar('footer2')):
																					echo '<div class="footer_area2">';dynamic_sidebar('footer2');echo '</div>';
																					endif;
														?>
							</div>
	
	<div class="footer-wrap">
		<?php apply_filters('tmpl_supreme_footer_nav',			  supreme_footer_navigation()); // Loads the menu-footer. 
								if($supreme2_theme_settings['footer_insert']){
								?>
		<div class="footer-content"> <?php echo apply_atomic_shortcode( 'footer_content', __($supreme2_theme_settings['footer_insert'],THEME_DOMAIN) ); ?> </div>
		<!-- .footer-content -->
		<?php }else{ 
								if(!is_active_sidebar('footer')):
								?>
		<div class="footer-content"> <?php echo '<p class="copyright">&copy; 2011 <a href="http://templatic.com/demos/onepager">OnePager</a> '. __("All Rights Reserved.&nbsp;Designed by",THEME_DOMAIN). '<a href="http://templatic.com" class="footer-logo" alt="wordpress themes" title="wordpress themes"><img src="'.get_template_directory_uri().'/library/images/templatic-wordpress-themes.png" alt="wordpress themes" /></a></p>'; ?> </div>
		<!-- .footer-content -->
		<?php	endif; }	

						do_action( 'footer' ); // supreme_footer ?>
	</div><!-- .wrap -->
	
	<?php do_action( 'close_footer' ); // supreme_close_footer ?>

</footer><!-- #footer -->

<?php do_action( 'after_footer' ); // supreme_after_footer ?>
<?php wp_footer(); // wp_footer 
	do_action('before_body_end',10);
	?>
</div>
</div>
</body>
</html>