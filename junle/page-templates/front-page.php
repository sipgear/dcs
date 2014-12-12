<?php
/**
 * Template Name: Front Page Template
 *
 * This is the home template.  Technically, it is the "posts page" template.  It is used when a visitor is on the 
 * page assigned to show a site's latest blog posts.
 *
 * @package supreme
 * @subpackage Template
 */

get_header(); // Loads the header.php template. ?>
<?php do_action( 'before_content' ); // supreme_before_content ?>
<section id="content">
	<?php do_action( 'open_content' ); // supreme_open_content 
				if ( have_posts() ) : ?>
	<?php while ( have_posts() ) : the_post(); 

					do_action( 'before_entry' ); // supreme_before_entry ?>
	
	<div id="post-<?php the_ID(); ?>" class="<?php supreme_entry_class(); ?>">
		<?php do_action( 'open_entry' ); // supreme_open_entry  ?>
		<?php if($post->post_content !=''){ ?>
		<div class="entry-content">
			<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', THEME_DOMAIN ) );
							wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', THEME_DOMAIN ), 'after' => '</p>' ) );
						 ?>
		</div>
		<!-- .entry-content -->
		<?php } 
					
						do_action( 'close_entry' ); // supreme_close_entry ?>
	</div>
	<!-- .hentry -->
	<?php
				endwhile;
			endif; ?>
	<div class="hfeed">
		<?php get_template_part( 'loop-meta' ); // Loads the loop-meta.php template. 
			$theme_name = get_option('stylesheet');
			$nav_menu = get_option('theme_mods_'.strtolower($theme_name));	
			if(is_home() || is_front_page()){
				if(isset($nav_menu['nav_menu_locations'])  && $nav_menu['nav_menu_locations']['secondary'] != 0){
					$sec2_id = (get_option('menu_section2')) ? get_option('menu_section2') : '';
					$sec3_id = (get_option('menu_section3')) ? get_option('menu_section3') : '';
					$sec4_id = (get_option('menu_section4')) ? get_option('menu_section4') : '';
					$sec5_id = (get_option('menu_section5')) ? get_option('menu_section5') : '';
					$sec6_id = (get_option('menu_section6')) ? get_option('menu_section6') : '';
				}else{
					$sec2_id = (get_option('menu_section2')) ? get_option('menu_section2') : 'sec2';
					$sec3_id = (get_option('menu_section3')) ? get_option('menu_section3') : 'sec3';
					$sec4_id = (get_option('menu_section4')) ? get_option('menu_section4') : 'sec4';
					$sec5_id = (get_option('menu_section5')) ? get_option('menu_section5') : 'sec5';
					$sec6_id = (get_option('menu_section6')) ? get_option('menu_section6') : 'sec6';
				}
			}else{
				if(isset($nav_menu['nav_menu_locations'])  && $nav_menu['nav_menu_locations']['innerpagemenu'] != 0){
					$sec2_id = (get_option('menu_section2')) ? get_option('menu_section2') : '';
					$sec3_id = (get_option('menu_section3')) ? get_option('menu_section3') : '';
					$sec4_id = (get_option('menu_section4')) ? get_option('menu_section4') : '';
					$sec5_id = (get_option('menu_section5')) ? get_option('menu_section5') : '';
					$sec6_id = (get_option('menu_section6')) ? get_option('menu_section6') : '';
				}else{
					$sec2_id = (get_option('menu_section2')) ? get_option('menu_section2') : 'sec2';
					$sec3_id = (get_option('menu_section3')) ? get_option('menu_section3') : 'sec3';
					$sec4_id = (get_option('menu_section4')) ? get_option('menu_section4') : 'sec4';
					$sec5_id = (get_option('menu_section5')) ? get_option('menu_section5') : 'sec5';
					$sec6_id = (get_option('menu_section6')) ? get_option('menu_section6') : 'sec6';
				}
			}
			
		?>
		<?php if(is_active_sidebar('home-page-content') && get_option( 'show_on_front' ) =='page'){ ?>
		<div id="<?php echo $sec2_id; ?>" class="home_content content_one clearfix">
				<?php 
			if(get_option('menu_page_section2') > 0){
				echo '<div class="home_page_section clearfix">';
					$page_name = get_post_meta(get_option("menu_page_section2"),'_wp_page_template',true);
					if($page_name != "page-templates/front-page.php" ) {
						if( $page_name == "page-templates/advance-search.php" ){
							get_template_part("page-templates/advance-search");
						}elseif( $page_name == "page-templates/archives.php" ){
							get_template_part("page-templates/archives");
						}elseif( $page_name == "page-templates/contact-us.php" ){
							get_template_part("page-templates/contact-us");
						}elseif( $page_name == "page-templates/sitemap.php" ){
							get_template_part("page-templates/sitemap");
						}else{
							$query = new WP_Query( array( 'page_id' => get_option("menu_page_section2") ) );
							apply_filters('tmpl_before-content',supreme_sidebar_before_content() ); // Loads the sidebar-before-content.
							if ( $query->have_posts() ) : ?>
								<?php while ( $query->have_posts() ) : $query->the_post(); 
									global $post;
									setup_postdata( $post ); 
									do_action( 'before_entry' ); // supreme_before_entry ?>
									<div id="post-<?php the_ID(); ?>" class="<?php supreme_entry_class(); ?>">
										<?php do_action( 'open_entry' ); // supreme_open_entry 
											 do_action('entry-title'); ?>
										<div class="entry-content">
											<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', THEME_DOMAIN ) );?>
										</div><!-- .entry-content -->
										<?php apply_filters('supreme_author_biograply',supreme_author_biography_($post));	// show author biography below post
										do_action( 'close_entry' ); // supreme_close_entry ?>
									</div><!-- .hentry -->
									<?php do_action( 'after_entry' ); // supreme_after_entry 
									apply_filters('tmpl_after-singular',supreme_sidebar_after_singular()); // Loads the sidebar-after-singular.
									do_action( 'after_singular' ); // supreme_after_singular 
										// If comments are open or we have at least one comment, load the comments template.
									endwhile;  wp_reset_postdata();
							endif; 
							apply_filters('tmpl_after-content',supreme_sidebar_after_content()); // afetr-content-sidebar use remove filter to dont display it 
						}
					}	
				echo '</div>';
			}else{
				dynamic_sidebar('home-page-content'); 
			}
			?>
		</div>
		<?php } ?>
		<?php if(is_active_sidebar('home_after_content2') && get_option( 'show_on_front' ) =='page'){  ?>
		
		<div id="<?php echo $sec3_id; ?>" class="home_content content_two clearfix">
				<?php
				if(get_option('menu_page_section3') > 0){
					echo '<div class="home_page_section clearfix">';
						$page_name = get_post_meta(get_option("menu_page_section3"),'_wp_page_template',true);
						if($page_name != "page-templates/front-page.php" ) {
							if( $page_name == "page-templates/advance-search.php" ){
								get_template_part("page-templates/advance-search");
							}elseif( $page_name == "page-templates/archives.php" ){
								get_template_part("page-templates/archives");
							}elseif( $page_name == "page-templates/contact-us.php" ){
								get_template_part("page-templates/contact-us");
							}elseif( $page_name == "page-templates/sitemap.php" ){
								get_template_part("page-templates/sitemap");
							}else{
								$query = new WP_Query( array( 'page_id' => get_option("menu_page_section3") ) );
								apply_filters('tmpl_before-content',supreme_sidebar_before_content() ); // Loads the sidebar-before-content.
								if ( $query->have_posts() ) : ?>
									<?php while ( $query->have_posts() ) : $query->the_post(); 
										global $post;
										setup_postdata( $post ); 
										do_action( 'before_entry' ); // supreme_before_entry ?>
										<div id="post-<?php the_ID(); ?>" class="<?php supreme_entry_class(); ?>">
											<?php do_action( 'open_entry' ); // supreme_open_entry 
												 do_action('entry-title'); ?>
											<div class="entry-content">
												<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', THEME_DOMAIN ) );?>
											</div><!-- .entry-content -->
											<?php apply_filters('supreme_author_biograply',supreme_author_biography_($post));	// show author biography below post
											do_action( 'close_entry' ); // supreme_close_entry ?>
										</div><!-- .hentry -->
										<?php do_action( 'after_entry' ); // supreme_after_entry 
										apply_filters('tmpl_after-singular',supreme_sidebar_after_singular()); // Loads the sidebar-after-singular.
										do_action( 'after_singular' ); // supreme_after_singular 
											// If comments are open or we have at least one comment, load the comments template.
										endwhile;  wp_reset_postdata();
								endif; 
								apply_filters('tmpl_after-content',supreme_sidebar_after_content()); // afetr-content-sidebar use remove filter to dont display it 
							}
						}
						echo '</div>';
				}else{
					dynamic_sidebar('home_after_content2');
				}	
				?>
		</div>
		<?php } ?>
		<?php if(is_active_sidebar('home_after_content3') && get_option( 'show_on_front' ) =='page'){  ?>
		
		<div id="<?php echo $sec4_id; ?>" class="home_content content_three clearfix">
				<?php 	
					if(get_option('menu_page_section4') > 0){
						echo '<div class="home_page_section clearfix">';
							$page_name = get_post_meta(get_option("menu_page_section4"),'_wp_page_template',true);
							if($page_name != "page-templates/front-page.php" ) {
								if( $page_name == "page-templates/advance-search.php" ){
									get_template_part("page-templates/advance-search");
								}elseif( $page_name == "page-templates/archives.php" ){
									get_template_part("page-templates/archives");
								}elseif( $page_name == "page-templates/contact-us.php" ){
									get_template_part("page-templates/contact-us");
								}elseif( $page_name == "page-templates/sitemap.php" ){
									get_template_part("page-templates/sitemap");
								}else{
									$query = new WP_Query( array( 'page_id' => get_option("menu_page_section4") ) );
									apply_filters('tmpl_before-content',supreme_sidebar_before_content() ); // Loads the sidebar-before-content.
									if ( $query->have_posts() ) : ?>
										<?php while ( $query->have_posts() ) : $query->the_post(); 
											global $post;
											setup_postdata( $post ); 
											do_action( 'before_entry' ); // supreme_before_entry ?>
											<div id="post-<?php the_ID(); ?>" class="<?php supreme_entry_class(); ?>">
												<?php do_action( 'open_entry' ); // supreme_open_entry 
													 do_action('entry-title'); ?>
												<div class="entry-content">
													<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', THEME_DOMAIN ) );?>
												</div><!-- .entry-content -->
												<?php apply_filters('supreme_author_biograply',supreme_author_biography_($post));	// show author biography below post
												do_action( 'close_entry' ); // supreme_close_entry ?>
											</div><!-- .hentry -->
											<?php do_action( 'after_entry' ); // supreme_after_entry 
											apply_filters('tmpl_after-singular',supreme_sidebar_after_singular()); // Loads the sidebar-after-singular.
											do_action( 'after_singular' ); // supreme_after_singular 
												// If comments are open or we have at least one comment, load the comments template.
											endwhile;  wp_reset_postdata();
									endif; 
									apply_filters('tmpl_after-content',supreme_sidebar_after_content()); // afetr-content-sidebar use remove filter to dont display it 
								}
							}
						echo '</div>';	
					}else{
						dynamic_sidebar('home_after_content3'); 
					}?>
		</div>
		
		<?php } ?>
		<?php if(is_active_sidebar('home_after_content4') && get_option( 'show_on_front' ) =='page'){  ?>
		<div id="<?php echo $sec5_id; ?>"  class="home_content content_four clearfix">
			<?php 	
			if(get_option('menu_page_section5') > 0){
				echo '<div class="home_page_section clearfix">';
					$page_name = get_post_meta(get_option("menu_page_section5"),'_wp_page_template',true);
					if($page_name != "page-templates/front-page.php" ) {
						if( $page_name == "page-templates/advance-search.php" ){
							get_template_part("page-templates/advance-search");
						}elseif( $page_name == "page-templates/archives.php" ){
							get_template_part("page-templates/archives");
						}elseif( $page_name == "page-templates/contact-us.php" ){
							get_template_part("page-templates/contact-us");
						}elseif( $page_name == "page-templates/sitemap.php" ){
							get_template_part("page-templates/sitemap");
						}else{
							$query = new WP_Query( array( 'page_id' => get_option("menu_page_section5") ) );
							apply_filters('tmpl_before-content',supreme_sidebar_before_content() ); // Loads the sidebar-before-content.
							if ( $query->have_posts() ) : ?>
								<?php while ( $query->have_posts() ) : $query->the_post(); 
									global $post;
									setup_postdata( $post ); 
									do_action( 'before_entry' ); // supreme_before_entry ?>
									<div id="post-<?php the_ID(); ?>" class="<?php supreme_entry_class(); ?>">
										<?php do_action( 'open_entry' ); // supreme_open_entry 
											 do_action('entry-title'); ?>
										<div class="entry-content">
											<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', THEME_DOMAIN ) );?>
										</div><!-- .entry-content -->
										<?php apply_filters('supreme_author_biograply',supreme_author_biography_($post));	// show author biography below post
										do_action( 'close_entry' ); // supreme_close_entry ?>
									</div><!-- .hentry -->
									<?php do_action( 'after_entry' ); // supreme_after_entry 
									apply_filters('tmpl_after-singular',supreme_sidebar_after_singular()); // Loads the sidebar-after-singular.
									do_action( 'after_singular' ); // supreme_after_singular 
										// If comments are open or we have at least one comment, load the comments template.
									endwhile;  wp_reset_postdata();
							endif; 
							apply_filters('tmpl_after-content',supreme_sidebar_after_content()); // afetr-content-sidebar use remove filter to dont display it 
						}
					}
					echo '</div>';
			}else{
				dynamic_sidebar('home_after_content4'); 
			}
			 ?>
		</div>
		
		<?php } ?>
		<?php if(is_active_sidebar('home_after_content5') && get_option( 'show_on_front' ) =='page'){  ?>
		
		<div id="<?php echo $sec6_id; ?>" class="home_content content_five clearfix">
				<?php 	
					if(get_option('menu_page_section6') > 0){
						echo '<div class="home_page_section clearfix">';
							$page_name = get_post_meta(get_option("menu_page_section6"),'_wp_page_template',true);
							if($page_name != "page-templates/front-page.php" ) {
								if( $page_name == "page-templates/advance-search.php" ){
									get_template_part("page-templates/advance-search");
								}elseif( $page_name == "page-templates/archives.php" ){
									get_template_part("page-templates/archives");
								}elseif( $page_name == "page-templates/contact-us.php" ){
									get_template_part("page-templates/contact-us");
								}elseif( $page_name == "page-templates/sitemap.php" ){
									get_template_part("page-templates/sitemap");
								}else{
									$query = new WP_Query( array( 'page_id' => get_option("menu_page_section6") ) );
									apply_filters('tmpl_before-content',supreme_sidebar_before_content() ); // Loads the sidebar-before-content.
									if ( $query->have_posts() ) : ?>
										<?php while ( $query->have_posts() ) : $query->the_post(); 
											global $post;
											setup_postdata( $post ); 
											do_action( 'before_entry' ); // supreme_before_entry ?>
											<div id="post-<?php the_ID(); ?>" class="<?php supreme_entry_class(); ?>">
												<?php do_action( 'open_entry' ); // supreme_open_entry 
													 do_action('entry-title'); ?>
												<div class="entry-content">
													<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', THEME_DOMAIN ) );?>
												</div><!-- .entry-content -->
												<?php apply_filters('supreme_author_biograply',supreme_author_biography_($post));	// show author biography below post
												do_action( 'close_entry' ); // supreme_close_entry ?>
											</div><!-- .hentry -->
											<?php do_action( 'after_entry' ); // supreme_after_entry 
											apply_filters('tmpl_after-singular',supreme_sidebar_after_singular()); // Loads the sidebar-after-singular.
											do_action( 'after_singular' ); // supreme_after_singular 
												// If comments are open or we have at least one comment, load the comments template.
											endwhile;  wp_reset_postdata();
									endif; 
									apply_filters('tmpl_after-content',supreme_sidebar_after_content()); // afetr-content-sidebar use remove filter to dont display it 
								}
							}
						echo '</div>';	
					}else{
						dynamic_sidebar('home_after_content5'); 
					}
			?>
		</div>
		<?php } ?>
	</div>
	<!-- .hfeed -->
	
	<?php do_action( 'close_content' ); // supreme_close_content 

	apply_filters('supreme_custom_front_loop_navigation',supreme_loop_navigation($post)); // Loads the loop-navigation .
	?>
</section>
<!-- #content -->

<?php get_footer(); // Loads the footer.php template. ?>