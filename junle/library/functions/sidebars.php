<?php
/**
 * Sets up the default framework sidebars if the theme supports them.  By default, the framework registers 
 * seven sidebars.  Themes may choose to use one or more of these sidebars.  A theme must register support 
 * for 'supreme-core-sidebars' to use them and register each sidebar ID within an array for the second 
 * parameter of add_theme_support().
 *
 * @package HybridCore
 * @subpackage Functions
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2012, Justin Tadlock
 * @link http://themehybrid.com/supreme-core
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Register widget areas. */
add_action( 'widgets_init', 'supreme_register_sidebars' );

/*
 Name :supreme_register_sidebars
 Description : Registers the supreme supported sidebars 

 */
function supreme_register_sidebars() {
	unregister_widget('WP_Widget_Text');
	/* Get the theme-supported sidebars. */
	$supported_sidebars = get_theme_support( 'supreme-core-sidebars' );

	/* If the theme doesn't add support for any sidebars, return. */
	if ( !is_array( $supported_sidebars[0] ) )
		return;

	/* Get the available core framework sidebars. */
	$core_sidebars = supreme_get_sidebars();

	/* Loop through the supported sidebars. */
	foreach ( $supported_sidebars[0] as $sidebar ) {

		/* Make sure the given sidebar is one of the core sidebars. */
		if ( isset( $core_sidebars[$sidebar] ) ) {

			/* Set up some default sidebar arguments. */
			$defaults = array(
				'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
				'after_widget' => 	'</div></div>',
				'before_title' => 	'<h3 class="widget-title">',
				'after_title' => 	'</h3>'
			);

			/* Parse the sidebar arguments and defaults. */
			$args = wp_parse_args( $core_sidebars[$sidebar], $defaults );

			/* If no 'id' was given, use the $sidebar variable and sanitize it. */
			$args['id'] = ( isset( $args['id'] ) ? sanitize_key( $args['id'] ) : sanitize_key( $sidebar ) );

			/* Register the sidebar. */
			
			register_sidebar($args);
		}
	}

	
	if(is_plugin_active('woocommerce/woocommerce.php')){
		$args = array(
			'name'          => __( 'WooCommerce Sidebar', THEME_DOMAIN ),
			'id'            => 'supreme_woocommerce',
			'description'   => apply_filters('supreme_woo_commerce_sidebar_description',__('This sidebar is specially for woocommerce product pages, whichever widgets you drop here will be shown in woocommerce product pages.',THEME_DOMAIN)),
			'class'         => '',
			'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>' );
		register_sidebar( $args );
	}
}

/**
 Name : supreme_get_sidebars
 Description : get the sidebar of supreme
 */
function supreme_get_sidebars() {

	/* Set up an array of sidebars. */
	global $theme_sidebars;
	if(empty($theme_sidebars))
	{
		$theme_sidebars = array(''); 
	}
	$sidebars = array(
		'header' => array(
			'name' =>	apply_filters('supreme_header_right_title',_x( 'Header Right', 'sidebar', THEME_DOMAIN )),
			'description' =>	apply_filters('supreme_header_right_description',__( "Displays within the site's header area on right side.", THEME_DOMAIN )),
		),
		'mega_menu' => array(
			'name' =>	_x( 'Mega Menu Navigation', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( 'Load only jquery mega menu in Secondary Navigation menu.', THEME_DOMAIN),
		),
		'secondary_navigation_right' => array(
			'name' =>	_x( 'Secondary Navigation Right', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( 'Widgets loaded here are visible near the Secondary Navigation', THEME_DOMAIN ),
		),
		'home-page-banner' => array(
			'name' =>	apply_filters('supreme_home_page_banner_title',_x( 'Home Page Slider', 'sidebar', THEME_DOMAIN )),
			'description' =>	__( "Widgets placed here appears on the Home Page created with the help of Front Page template", THEME_DOMAIN ),
		),	
		
		'home-page-content' => array(
			'name' =>	_x( apply_filters('home_page_content_name',__('Home Page Content',THEME_DOMAIN)), 'sidebar', THEME_DOMAIN ),
			'description' =>	apply_filters('supreme_home_page_widget_area_description',__('Widget placed here are visible on Home Page created with the help of Front Page Template.',THEME_DOMAIN)),
		),	
		'front-page-sidebar' => array(
		'name' =>  _x( 'Front Page Sidebar', 'sidebar', THEME_DOMAIN ),
		'description' => __( 'A home page sidebar widget area. use to display different widgets on home page.', THEME_DOMAIN )
		),
		
		
		'post-listing-sidebar' => array(
		'name' =>  _x( 'Post Listing Page Sidebar', 'sidebar', THEME_DOMAIN ),
		'description' => __( 'Widgets placed here will be visible on your blog/post listing page as sidebar items.', THEME_DOMAIN )
		),
		
		'post-detail-sidebar' => array(
		'name' =>  _x( 'Post Detail Page Sidebar', 'sidebar', THEME_DOMAIN ),
		'description' => __( 'Widgets placed here will be visible on your blog/post detail page as sidebar items.', THEME_DOMAIN )
		),
		
		'after-content' => array(
			'name' =>	_x( 'At Page End - All Pages', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( "Widgets placed here appears at the end of the page before the footer.", THEME_DOMAIN ),
		),
		'before-content' => array(
			'name' =>	_x( 'Before Content', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( "Widgets placed here appears before the all page's main content area.", THEME_DOMAIN ),
		),
		'after-singular' => array(
			'name' =>	_x( 'Below Detail Page Content', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( 'Loads on detail page of post, pages, attachement. Visible before the comments area.', THEME_DOMAIN ),
		),
		'primary-sidebar' => array(
			'name' => 	_x( 'Primary Sidebar', 'sidebar', THEME_DOMAIN ),
			'description' => 	__( 'This is the default sidebar area.If you do not place any widget for other relevant page sidebar areas than widgets placed here will be applied as their sidebar items.', THEME_DOMAIN )
		),
		
		
		
		
		
		'entry' => array(
			'name' =>	_x( 'Before Listing Entry', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( 'widgets placed here will appear directly above each post,page listing.', THEME_DOMAIN),
		),
		
		'subsidiary' => array(
			'name' => 	_x( 'Subsidiary With 1 Column', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( 'Displays widgets in a single column below the "After Content" area on all post,pages,attachments.', THEME_DOMAIN),
		),

		'subsidiary-2c' => array(
			'name' =>	_x( 'Subsidiary With 2 Column', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( 'Displays widgets in two columns below the "After Content" area on all post,pages,attachments.', THEME_DOMAIN),
		),
		
		'subsidiary-3c' => array(
			'name' =>	_x( 'Subsidiary With 3 Column', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( 'Displays widgets in three columns below the "After Content" area on all post,pages,attachments.', THEME_DOMAIN),
		),

		'after-header' => array(
			'name' =>	_x( 'After Header', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( 'A 1-column widget area loaded after the header of the site.', THEME_DOMAIN ),
		),		
		'contact_page_widget' => array(
			'name' =>	_x( 'Contact Page Content Area', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( 'Google Map Widget is one of the suitable widget for this area.', THEME_DOMAIN ),
		),
		
		'contact_page_sidebar' => array(
			'name' =>	_x( 'Contact Page Sidebar', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( 'Widgets placed here are visible on contact page as sidebar items.', THEME_DOMAIN ),
		),
		
		'footer' => array(
			'name' =>	_x( apply_filters('footer_name',__('Footer',THEME_DOMAIN)), 'sidebar', THEME_DOMAIN),
			'description' =>	_x( apply_filters('footer_description',__( 'Display the widgets in footer.',THEME_DOMAIN ))),
		),
	
	);
	$sidebars = array_merge($sidebars,$theme_sidebars);
	/* Return the sidebars. */
	
return $sidebars;
}

?>