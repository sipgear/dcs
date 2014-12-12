<?php

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?php
if(is_plugin_active('all-in-one-seo-pack/all_in_one_seo_pack.php')){

	wp_title();
}else{
 	supreme_document_title(); 
}?></title>
<link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo get_template_directory_uri()."/library/css/admin_style.css"; ?>" type="text/css" media="all" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php 
	$supreme2_theme_settings = get_option(supreme_prefix().'_theme_settings');
	if ( isset($supreme2_theme_settings['customcss']) && $supreme2_theme_settings['customcss']==1 ) { ?>
		<link href="<?php echo get_template_directory_uri(); ?>/custom.css" rel="stylesheet" type="text/css" />
<?php } ?>
<?php 
	if(function_exists('supreme_get_favicon')){
		if(supreme_get_favicon()){ ?>
			<link rel="shortcut icon" href="<?php  echo supreme_get_favicon(); ?>" /><?php 	}
	}
wp_head(); // wp_head 
if(isset($supreme2_theme_settings['enable_sticky_header_menu']) && $supreme2_theme_settings['enable_sticky_header_menu']==1){
	include(get_template_directory().'/js/sticky_menu.php');
}
do_action('supreme_enqueue_script');
?>

<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->   

</head>

<body class="<?php supreme_body_class(); ?>">

	<?php do_action( 'open_body' ); // supreme_open_body 
	//if(wpmd_is_phone()){
	$theme_name = get_option('stylesheet');
	$nav_menu = get_option('theme_mods_'.strtolower($theme_name));

	?>
		<div class="supreme_wrapper">
		<div id="mobile_header" class="mobile_header">
			<div class="toggle_wrap clearfix">
				<div class="toggle_mobile_widget"><?php apply_filters('supreme-nav-right',dynamic_sidebar('secondary_navigation_right')); ?></div>
				   <div class="toggle_mobile_header"></div>
			</div>
			<div class="mobi-scroll">
			<?php
			apply_filters('tmpl_supreme_header_primary',supreme_header_primary_navigation()); // Loads the menu-primary template. 
			
			$flag = 0;
			echo '<div id="nav-secondary" class="nav_bg">';	
				if(is_home() || is_front_page()){
					if(isset($nav_menu['nav_menu_locations'])  && @$nav_menu['nav_menu_locations']['secondary'] != 0){
						apply_filters('tmpl_supreme_header_secondary',supreme_header_secondary_mobile_navigation()); // Loads the menu-secondary template.
						$flag = 1;
					}
				}else{
					if(isset($nav_menu['nav_menu_locations'])  && @$nav_menu['nav_menu_locations']['innerpagemenu'] != 0){
						apply_filters('tmpl_supreme_header_secondary',supreme_header_inner_secondary_mobile_navigation()); // Loads the menu-secondary template.
						$flag = 1;
					}
				}	
			echo '</div>';	
			
			if($flag == 1){
			}elseif((is_home() || is_front_page()) && is_active_sidebar('mega_menu')){
					if(function_exists('dynamic_sidebar')){
						dynamic_sidebar('mega_menu'); // jQuery mega menu
				}
			}elseif(is_active_sidebar('innerpage_mega')){
					if(function_exists('dynamic_sidebar')){
						dynamic_sidebar('innerpage_mega'); // jQuery mega menu
				}
			}else{
				if(is_home() || is_front_page()){
					add_action('wp_footer','header_menu_script');
					echo '<div class="nav_bg">
							<div id="menu-mobi-secondary1" class="menu-container">
								<nav role="navigation" class="wrap">
									<div class="menu">
										<ul class="" id="menu-mobi-secondary-items1">
											<li class=""><a href="#sec1">Home</a></li>
											<li class=""><a href="#sec2">About Us</a></li>
											<li class=""><a href="#sec3">Services</a></li>
											<li class=""><a href="#sec4">Portfolio</a></li>
											<li class=""><a href="#sec5">Blog</a></li>
											<li class=""><a href="#sec6">Contact Us</a></li>
										</ul>
									</div>';
					echo '		</nav>
							</div>
						</div>';
				}else{
					echo '<div class="nav_bg" id="nav-secondary1">
							<div class="menu-container" id="menu-secondary1">
								<nav role="navigation" class="wrap">
									<div class="menu">
										<ul class="" id="menu-secondary-items1">
											<li class=""><a href="'.home_url().'/#sec1">Home</a></li>
											<li class=""><a href="'.home_url().'/#sec2">About Us</a></li>
											<li class=""><a href="'.home_url().'/#sec3">Services</a></li>
											<li class=""><a href="'.home_url().'/#sec4">Portfolio</a></li>
											<li class=""><a href="'.home_url().'/#sec5">Blog</a></li>
											<li class=""><a href="'.home_url().'/#sec6">Contact Us</a></li>
										</ul>
									</div>';
					echo '		</nav>
							</div>
						</div>';
				}
			} 
			?>
			</div>
		</div>
 
        <div id="container" class="container-wrap">
        
        
		<?php if((is_home()  && (get_option('page_for_posts')=='0' || get_option('page_for_posts') == '')) || (is_front_page()) ): ?> 
		 <?php  
			if(is_active_sidebar('home-page-full-slider') && get_option( 'show_on_front' ) =='page') {?>
				<div id="<?php echo (get_option('menu_section1')) ? get_option('menu_section1') : ''; ?>" class="home_full_slider home_page_banner">
					<?php 
						if(get_option('menu_page_section1') > 0){
							echo '<div class="home_page_section">';
							$page_name = get_post_meta(get_option("menu_page_section1"),'_wp_page_template',true);
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
										$query = new WP_Query( array( 'page_id' => get_option("menu_page_section1") ) );
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
							dynamic_sidebar('home-page-full-slider'); 
						}
					?>
				</div>
			<?php } ?>
		<?php endif; ?>
        
     
        <div class="header_container <?php  if(is_active_sidebar('home-page-full-slider')) {?> header_full_slider <?php }?> clearfix">
        
        <div class="header_strip">
		<?php
        remove_action('pre_get_posts', 'home_page_feature_listing');	
        
        supreme_primary_navigation();

		do_action( 'after_menu_primary' ); // supreme_before_header
		
		do_action( 'before_header' ); // supreme_before_header ?>
		<?php 
			$header_image = get_header_image();
			if(function_exists('get_header_image_location')){
				$header_image_location = get_header_image_location(); // 0 = before secondary navigation menu, 1 = after secondary navigation menu
			}else{
				$header_image_location = 1;
			}
		?>
		<header id="header">
			<?php do_action( 'open_header' ); // supreme_open_header 
				$theme_options = get_option(supreme_prefix().'_theme_settings');
			?>
			<div class="header-wrap">
				<?php if($theme_options[ 'display_header_text' ] ==1){ ?>
				<div id="branding">
					<?php 
					
					$supreme_logo_url = $theme_options['supreme_logo_url'];
					if ( $supreme_logo_url ) : ?>	
		
						<div id="site-title">
							<a href="<?php echo home_url(); ?>/" title="<?php echo bloginfo( 'name' ); ?>" rel="Home">
								<img class="logo" src="<?php echo $supreme_logo_url; ?>" alt="<?php echo bloginfo( 'name' ); ?>" />
							</a>							
						</div>
						
					<?php else :
							supreme_site_title();
						  endif; 
					$theme_options = get_option(supreme_prefix().'_theme_settings');
					$supreme_site_description = $theme_options['supreme_site_description'];
					if ( $supreme_site_description == '' ||  $supreme_site_description == 0 )  : // If hide description setting is un-checked, display the site description. 
						    supreme_site_description(); 
					endif; ?>
				</div><!-- #branding -->
				<?php } ?>
				
  <?php 
		/* Secondary navigation menu for desk top */
		if(function_exists('onepager_supreme_secondary_navigation')){
			onepager_supreme_secondary_navigation(); 
		}
		if(function_exists('onepager_supreme_sticky_secondary_navigation')){
			onepager_supreme_sticky_secondary_navigation();
		}
		?>
			</div>

			<?php do_action( 'close_header' ); // supreme_close_header ?>
		<!-- #header -->
		</header>
        </div>
		</div>
        <?php
			$args = array(
			'post_type' => 'page',
			'meta_key' => '_wp_page_template',
			'meta_value' => 'page-templates/front-page.php'
			);
			$page_query = new WP_Query($args);
			$front_page_id = $page_query->post->ID;
			$div_class = '';
			if(is_front_page()){
				if(get_option( 'show_on_front' ) =='page'){
					$class="home_wrapper";
					if( $front_page_id != get_option('page_on_front') ){
						$div_class = " frontpage";
					}
				}else{
					$class="main";
					$div_class = '';
				}
			}else{
				$class="main";
				$div_class = '';
			}
		?>
		<div id="<?php echo $class; ?>" class="clearfix <?php echo $div_class;?>">

			<div class="wrap">

			<?php do_action( 'open_main' ); // supreme_open_main ?>