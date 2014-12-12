<?php

ob_start();
error_reporting(0);
if(file_exists(trailingslashit ( get_template_directory() ) . 'library/supreme.php'))
	require_once( trailingslashit ( get_template_directory() ) . 'library/supreme.php' ); // contain all classes and core function pf the framework
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
define('THEME_DOMAIN','templatic'); // for localization
load_theme_textdomain(THEME_DOMAIN);
load_textdomain( THEME_DOMAIN, get_stylesheet_directory().'/languages/en_US.mo');
define('TEMPLATE_URI',trailingslashit(get_template_directory_uri()));
define('TEMPLATE_DIR',trailingslashit(get_template_directory()));

if(class_exists('Supreme')){
	$theme = new Supreme(); /* Part of the framework. */
}else{
	echo '<div id="message" class="error"><p><strong>Library</strong> forder is missing.</p></div>';
}
$page= @$_REQUEST['page'];
if(is_admin() && ($pagenow =='themes.php' || $pagenow =='post.php' || $pagenow =='edit.php'|| $pagenow =='admin-ajax.php' || trim($page) == trim('tmpl_theme_update'))){
	require_once('wp_theme_update.php');	
	new WPUpdatesStarUpdater( 'http://templatic.com/updates/api/index.php', basename(get_template_directory()) );
}

/*------------------------
  Theme setup function.  This function adds support for theme features and defines the default theme
  actions and filters.
 -----------------------------*/
add_action( 'after_setup_theme', 'supreme_theme_setup' );
function supreme_theme_setup() {	
	/* Get action/filter hook prefix. */
	if(class_exists('Supreme')){
		$prefix = supreme_prefix(); // Part of the framework, cannot be changed or prefixed.
	}
	
	if(file_exists(get_template_directory().'/library/functions/functions.php')){
		require_once(get_template_directory().'/library/functions/functions.php'); // framework functions file 
	}
	
	/* Add framework menus. */
	add_theme_support( 'supreme-core-menus', array( // Add core menus.
		'primary',
		'secondary',
		'subsidiary'
		) );

	/* Register aditional menus */
	
	/* if(!strstr($_SERVER['REQUEST_URI'],'/wp-admin/') && isset($_REQUEST['adv_search']) && $_REQUEST['adv_search'] == 1){
		add_action('posts_where','templatic_searching_filter_where');
	} */
	/* Add framework sidebars */
	
	/* add sidebar support in theme , want to remove from child theme as remove theme support from child theme's functions file */
	add_theme_support( 'supreme-core-sidebars', array( // Add sidebars or widget areas.
				'mega_menu',
				'innerpage_mega',
				'home-page-full-slider',
				'home-page-content',
				'home_after_content2',
				'home_after_content3',
				'home_after_content4',
				'home_after_content5',
				'secondary_navigation_right',
				'before-content',
				'entry',
				'after-content',
				'front-page-sidebar',
				'post-listing-sidebar',
				'post-detail-sidebar',
				'primary-sidebar',
				'after-singular',
				'subsidiary',
				'contact_page_widget',
				'contact_page_sidebar',
				'supreme_woocommerce',
				'footer',
				'footer2',
				) );

	/* add theme support for menu */
	/* Add framework menus. */
	add_theme_support( 'supreme-core-menus', array( // Add core menus.
				'secondary',
				'innerpagemenu',
	) );


	add_theme_support( 'post-formats', array(
		'aside',
		'audio',
		'gallery',
		'image',
		'link',
		'quote',
		'video'
		) );
	add_post_type_support( 'post', 'post-formats' ); // support post format
	add_theme_support( 'supreme_banner_slider' ); // work with home page banner slider
	add_theme_support( 'supreme-show-commentsonlist' ); // to show comments counting on listing
	add_theme_support( 'supreme-core-widgets' ); // to support widgest 
	add_theme_support( 'supreme-core-shortcodes' ); // to support shortcodes
	add_theme_support( 'supreme-core-template-hierarchy' ); // This is important. Do not remove. */
	add_theme_support( 'newsletter_title_abodediv' ); //To display newslatter widget title above div. */

	/* Add theme support for framework layout extension. */
	add_theme_support( 'theme-layouts', array( // Add theme layout options.
		'1c',
		'2c-l',
		'2c-r',
		) );

	/* Add theme support for other framework extensions */

	add_theme_support( 'custom-header' );
	add_theme_support( 'post-stylesheets' );
	add_theme_support( 'loop-pagination' );
	add_theme_support( 'get-the-image' );
	add_theme_support( 'breadcrumb-trail' );
	add_theme_support( 'supreme-core-theme-settings', array( 'footer' ) );
	if(class_exists('Supreme')){
		if(is_it_frontend()): 
			show_admin_bar(false);
		 endif;
	}
	/* Add theme support for WordPress features. */
	add_theme_support( 'automatic-feed-links' );
	
	//To remove tevolution sharing option settings
	add_theme_support( 'remove_tevolution_sharing_opts' );

	/* Add theme support for WordPress background feature */

	add_theme_support( 'custom-background', array (
		'default-color' => '',
		'default-image' => '',
		'wp-head-callback' => 'supreme_custom_background_callback',
		'admin-head-callback' => '',
		'admin-preview-callback' => ''
	));
	
	/* Modify excerpt more */
	add_filter('excerpt_length', 'supreme_excerpt_length');
	add_filter('excerpt_more', 'new_excerpt_more');
	
	/* Wraps <blockquote> around quote posts. */
	add_filter( 'the_content', 'supreme_quote_post_content' );
	
	add_filter( 'embed_defaults', 'supreme_embed_defaults' ); // Set default widths to use when inserting media files
	
	add_filter( 'sidebars_widgets', 'supreme_disable_sidebars' );
	
	/* Add aditional layouts */
	add_filter( 'theme_layouts_strings', 'supreme_theme_layouts' );
	
	###### ACTIONS ######
	/* Load resources into the theme. */
	add_action( 'wp_enqueue_scripts', 'supreme_resources' );
	
	/* Register new image sizes. */
	add_action( 'init', 'supreme_register_image_sizes' );

	add_action( 'init', 'supreme_support_woo' );

	/* Assign specific layouts to pages based on set conditions and disable certain sidebars based on layout choices. */
	add_action( 'template_redirect', 'supreme_layouts' );
	
	
	/* adding customizing taxture settings for background */
	if(function_exists('templatic_texture_settings')){
		add_action('wp_head','templatic_texture_settings');
	}
	
	/* Register additional widget areas. */
	add_action( 'widgets_init', 'supreme_register_sidebars', 11 ); // Number 11 indicates custom sidebars should be registered after Hybrid Core Sidebars
	
	/* WooCommerce Functions. */
	if ( function_exists( 'is_woocommerce' ) ) {
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
	}
	if(class_exists('Supreme')){
		/* Set content width. */
		supreme_set_content_width( 600 );
	}
	
	/****** Theme related files ******/
	add_theme_support('excerpt_in_popular_post');
	add_theme_support('theme-functions');
	add_theme_support('theme-widgets');
	add_theme_support('theme-post_type');
	add_theme_support('theme_widget_categories');
	/* for theme functions support */
	
	if(current_theme_supports('theme-functions')){
		include(get_template_directory()."/functions/theme-functions.php");
	}
	if(current_theme_supports('theme-widgets')){
		include(get_template_directory()."/functions/theme-widgets.php");
	}
	
	if(current_theme_supports('theme-post_type')){
		include(get_template_directory()."/functions/theme-post_type.php");
	}
	if ( get_header_textcolor()=='blank') { ?><style type="text/css"> #site-title,#site-title1,#site-description { text-indent: -99999px; } </style><?php }
	add_action('templ_after_categories_description','remove_meta_tag');
	add_image_size( 'taxonomy-thumbnail', 270, 170, true );
	add_image_size( 'portfolio-thumbnail', 258, 200, true );
	add_image_size( 'blog-thumbnail', 358, 260, true );
	add_image_size( 'blogdetail-thumbnail', 850, 420, true );
	remove_action('listing_post_title_before_image','listing_post_title_before_image');
	add_action('listing_post_title_after_image','listing_post_title_after_image');
	remove_action('templ_before_post_title','remove_shareing_buttons_option',20);
	add_action('templ_before_post_title','star_remove_shareing_buttons_option',20);
	$taxnow = 'room_category';
	if(isset($taxnow) && $taxnow== 'room_category'){
		add_action($taxnow.'_edit_form_fields','booking_room_category_custom_fields_EditFields');
		add_action($taxnow.'_add_form_fields','booking_room_category_custom_fields_AddFieldsAction');
		add_action('edited_term','booking_room_category_custom_fields_AlterFields');
		add_action('created_'.$taxnow,'booking_room_category_custom_fields_AlterFields');		
	}
	
	$taxnow = 'room_tag';
	if(isset($taxnow) && $taxnow== 'room_tag'){
		add_action($taxnow.'_edit_form_fields','booking_room_category_custom_fields_EditFields');
		add_action($taxnow.'_add_form_fields','booking_room_category_custom_fields_AddFieldsAction');
		add_action('edited_term','booking_room_category_custom_fields_AlterFields');
		add_action('created_'.$taxnow,'booking_room_category_custom_fields_AlterFields');		
	}
	
	$taxnow_house = 'house_category';
	if(isset($taxnow_house) && $taxnow_house== 'house_category'){
		add_action($taxnow_house.'_edit_form_fields','booking_room_category_custom_fields_EditFields');
		add_action($taxnow_house.'_add_form_fields','booking_room_category_custom_fields_AddFieldsAction');
		add_action('edited_term','booking_room_category_custom_fields_AlterFields');
		add_action('created_'.$taxnow_house,'booking_room_category_custom_fields_AlterFields');		
	}
	
	$taxnow_house = 'house_tag';
	if(isset($taxnow_house) && $taxnow_house== 'house_tag'){
		add_action($taxnow_house.'_edit_form_fields','booking_room_category_custom_fields_EditFields');
		add_action($taxnow_house.'_add_form_fields','booking_room_category_custom_fields_AddFieldsAction');
		add_action('edited_term','booking_room_category_custom_fields_AlterFields');
		add_action('created_'.$taxnow_house,'booking_room_category_custom_fields_AlterFields');		
	}
	
	$taxnow_post = 'category';
	if(isset($taxnow_post) && $taxnow_post== 'category'){
		add_action($taxnow_post.'_edit_form_fields','booking_room_category_custom_fields_EditFields');
		add_action($taxnow_post.'_add_form_fields','booking_room_category_custom_fields_AddFieldsAction');
		add_action('edited_term','booking_room_category_custom_fields_AlterFields');
		add_action('created_'.$taxnow_post,'booking_room_category_custom_fields_AlterFields');		
	}
	$taxnow_post = 'post_tag';
	if(isset($taxnow_post) && $taxnow_post== 'post_tag'){
		add_action($taxnow_post.'_edit_form_fields','booking_room_category_custom_fields_EditFields');
		add_action($taxnow_post.'_add_form_fields','booking_room_category_custom_fields_AddFieldsAction');
		add_action('edited_term','booking_room_category_custom_fields_AlterFields');
		add_action('created_'.$taxnow_post,'booking_room_category_custom_fields_AlterFields');		
	}
	add_action('admin_init','add_metabox_for_page_images');
	add_action('testimonial_script','onepager_widget_testimonial_script',20,3);
	add_action('tmpl_after_testimonial_title','onepager_testimonial_email',10,2);
	
	add_filter('popular_post_thumb_image','theme_crop_popular_post_thumb_image',12);
	remove_filter('popular_post_thumb_image','crop_popular_post_thumb_image',10);
}
function theme_crop_popular_post_thumb_image()
{
	return get_the_image(array('echo' => false, 'size'=> 'blog-thumbnail','height' => 260,'width'=>358,'default_image'=>get_template_directory_uri()."/images/noimage.jpg"));
} 
function star_remove_shareing_buttons_option()
{
	if(is_single())
	{
		remove_action('tmpl_detail_page_custom_fields_collection','detail_fields_colletion');
		add_action('templ_before_post_content','detail_fields_colletion');
		add_action('templ_after_post_image','booking_detail_after_content'); 
		remove_action('tmpl_before_comments','single_post_categories_tags'); 
		add_action('tmpl_before_comments','star_single_post_categories_tags'); 
		remove_action('for_comments','single_post_comment');
		add_action('for_comments','booking_single_post_comment');
	}
	if(is_archive() || is_tax() || is_tag()){
		remove_action('tmpl_detail_page_custom_fields_collection','detail_fields_colletion');			
		add_action('templ_after_post_content','booking_detail_after_content'); 
		remove_action('templ_the_taxonomies','category_post_categories_tags');
		add_action('templ_the_taxonomies','star_single_post_categories_tags'); 	
	}
	
	remove_action('templ_post_info','send_friend_inquiry_email');
	add_action('templ_after_post_content','send_friend_inquiry_email');
	
}
if(!function_exists('remove_meta_tag')){
	function remove_meta_tag(){
		if(is_tax())
		{
			global $post;
			remove_action('tmpl_category_page_image','tmpl_category_page_image');
			add_action('tmpl_category_page_image','star_taxonomy_page_image');
			remove_action('templ_post_info','post_info');
			if ( ($post->post_type == 'house' || $post->post_type == 'room')) {
				add_action('templ_post_info','theme_tax_post_info');
			}
		}
	}
}

if(!function_exists('listing_post_title_after_image')){
	function listing_post_title_after_image($instance)
	{
		if(!empty($instance['show_title'])) :
			printf( '<h2><a href="%s" title="%s">%s</a></h2>', get_permalink(), the_title_attribute('echo=0'), the_title_attribute('echo=0') );
		endif;
	}
}
function star_taxonomy_page_image()
{
	if ( current_theme_supports( 'get-the-image' ) && theme_get_settings('supreme_display_image') ) {
		global $post;
		$post_id = 	$post->ID;
		
		if(function_exists('get_templ_image'))
			$thumb_img=get_templ_image($post->ID,$size='taxonomy-thumbnail');
		?>
		<!-- Grid View image-->
		<a href="<?php the_permalink();?>" class="post_img img gridimg">
		<?php if($thumb_img!=""):?>
			<img src="<?php echo $thumb_img; ?>"  alt="<?php echo $img_alt; ?>" />
		<?php else:
			if(theme_get_settings('supreme_display_noimage')){
		?>    
				<img src="<?php echo CUSTOM_FIELDS_URLPATH; ?>/images/img_not_available.png" alt="" height="150" width="310"  />
		<?php 
			}
			endif;?>
		</a>
		<!--Finish Grid View Image -->
		<?php	
	}

}


/*
Name : supreme_support_woo
Description : to update option , is theme is support woocommerce or not 
*/

function supreme_support_woo(){

    $currrent_theme_name = wp_get_theme();
	$templatic_woocommerce_themes = get_option('templatic_woocommerce_themes');
	$templatic_woocommerce_ = str_replace(',','',get_option('templatic_woocommerce_themes'));
	if(!strstr(trim($templatic_woocommerce_) ,trim($currrent_theme_name))):
		update_option('templatic_woocommerce_themes',$templatic_woocommerce_themes.",".$currrent_theme_name);
	endif;		
}

/*
Name : supreme_resources
Description : load js files for supreme
*/
function supreme_resources() {

	wp_enqueue_script( 'supremesupreme-scripts', trailingslashit ( get_template_directory_uri() ) . 'js/_supreme.js', array( 'jquery' ), '20120606', true );

	/* for Gravity Forms */
	
	if( class_exists( 'RGForms' ) && class_exists( 'RGFormsModel' )) {
		wp_enqueue_style( 'supreme-gravity-forms', trailingslashit (get_template_directory_uri() ) . 'css/gravity-forms.css', false, '20120312', 'all' );
	}
	
	/* for WooCommerce */
	
	if( function_exists( 'is_woocommerce') ) {
		wp_dequeue_style( 'woocommerce_frontend_styles' );
		wp_dequeue_style( 'woocommerce_chosen_styles' );
	}
}

/**
 * This is a fix for when a user sets a custom background color with no custom background image.  What 
 * happens is the theme's background image hides the user-selected background color.  If a user selects a 
 * background image, we'll just use the WordPress custom background callback.
 * 
 * Thanks to Justin Tadlock for the code.
 *
 * @since 0.1
 * @link http://core.trac.wordpress.org/ticket/16919
 */
function supreme_custom_background_callback() {

	/* Get the background image. */
	$image = get_background_image();

	/* If there's an image, just call the normal WordPress callback. We won't do anything here. */
	if ( !empty( $image ) ) {
		_custom_background_cb();
		return;
	}

	/* Get the background color. */
	$color = get_background_color();

	/* If no background color, return. */
	if ( empty( $color ) )
		return;

	/* Use 'background' instead of 'background-color'. */
	$style = "background: #{$color};";

?>
<style type="text/css">body.custom-background { <?php echo trim( $style ); ?> }</style>
<?php
}

/*
Name : supreme_thumbnail_image_height
Description : Registers additional image size 'supreme-thumbnail'.
*/
function supreme_thumbnail_image_height() {
	return $thumbnail_height = apply_filters('supreme_thumbnail_image_height',170);

}

/*
Name : supreme_thumbnail_image_width
Description : Registers additional image size 'supreme-thumbnail'.
*/
function supreme_thumbnail_image_width() {
	return $thumbnail_width = apply_filters('supreme_thumbnail_image_width',220);

}

/*
Name : supreme_register_image_sizes
Description : Registers additional image size 'supreme-thumbnail'.
*/
function supreme_register_image_sizes() {
	$thumbnail_height = apply_filters('supreme_thumbnail_image_height',170);
	$thumbnail_width =  apply_filters('supreme_thumbnail_image_width',220);
	add_image_size( 'supreme-thumbnail', $thumbnail_width, $thumbnail_height, true );
}

/*
 Name : supreme_embed_defaults
 Description : Overwrites the default widths for embeds.  This is especially useful for making sure videos properly expand the full width on video pages. 
*/
function supreme_embed_defaults( $args ) {

	$args['width'] = 600;

	if ( current_theme_supports( 'theme-layouts' ) ) {

		$layout = theme_layouts_get_layout();

		if ( 'layout-3c-l' == $layout || 'layout-3c-r' == $layout || 'layout-3c-c' == $layout || 'layout-hl-2c-l' == $layout || 'layout-hl-2c-r' == $layout || 'layout-hr-2c-l' == $layout || 'layout-hr-2c-r' == $layout )
		
			$args['width'] = 280;
			
		elseif ( 'layout-1c' == $layout )
		
			$args['width'] = 920;

	}

	return $args;
}

/*
 Name : supreme_layouts
 Description: Conditional logic deciding the layout of certain pages.
*/
function supreme_layouts() {

	if ( current_theme_supports( 'theme-layouts' ) ) {

		$global_layout = theme_get_settings( 'supreme_global_layout' );
		$woocommerce_layout = supreme_get_settings( 'supreme_woocommerce_layout' );
		$layout = theme_layouts_get_layout();

		if ( !is_singular() && $global_layout !== 'layout_default' && function_exists( "supreme_{$global_layout}" ) ) {
			add_filter( 'get_theme_layout', 'supreme_' . $global_layout );
		} // end global layout control
		
		if ( is_singular() && $layout == 'layout-default' && $global_layout !== 'layout_default' && function_exists( "supreme_{$global_layout}" ) ) {
			add_filter( 'get_theme_layout', 'supreme_' . $global_layout );
		} // end singular layout control relative to global layout control
		
		if ( function_exists ( 'bbp_loaded' ) ) {
			if ( is_bbpress() && !is_singular() && $bbpress_layout !== 'layout_default' && function_exists( "supreme_{$bbpress_layout}" ) ) {
				add_filter( 'get_theme_layout', 'supreme_' . $bbpress_layout );
			}
			elseif ( is_bbpress() && is_singular() && $layout == 'layout-default' && $bbpress_layout !== 'layout_default' && function_exists( "supreme_{$bbpress_layout}" ) ) {
				add_filter( 'get_theme_layout', 'supreme_' . $bbpress_layout );
			}
		} // end bbpress layout control
		
		
		if ( function_exists ( 'is_woocommerce' ) ) {
			if ( is_woocommerce() && !is_singular() && $woocommerce_layout !== 'layout_default' && function_exists( "supreme_{$woocommerce_layout}" ) ) {
				add_filter( 'get_theme_layout', 'supreme_' . $woocommerce_layout );
			}
			elseif ( is_woocommerce() && is_singular() && $layout == 'layout-default' && $woocommerce_layout !== 'layout_default' && function_exists( "supreme_{$woocommerce_layout}" ) ) {
				add_filter( 'get_theme_layout', 'supreme_' . $woocommerce_layout );
			}
		} // end woocommerce layout control

	}
	
}



add_action('admin_init','supreme_wpup_changes',20);

function supreme_wpup_changes(){
	 remove_action( 'after_theme_row_supreme', 'wp_theme_update_row' ,10, 2 );
}
if(!function_exists('customAdmin')){
	function customAdmin() {
		if(file_exists(get_template_directory()."/library/admin/admin-style.php")){
			require_once(get_template_directory()."/library/admin/admin-style.php");
		}
	}
}
add_action('admin_head', 'customAdmin', 11);

// Returns the portion of haystack which goes until the last occurrence of needle
if(!function_exists('reverse_strrchr')){
	function reverse_strrchr($haystack, $needle, $trail) {
		return strrpos($haystack, $needle) ? substr($haystack, 0, strrpos($haystack, $needle) + $trail) : false;
	}
}
/* auto install for theme */
if(file_exists(get_template_directory()."/functions/auto_install/auto_install.php")){
		include_once(get_template_directory().'/functions/auto_install/auto_install.php');
	}
	
/*
Name : check_if_woocommerce_active
Desc : check if woocommerce is active or not 
*/
if(!function_exists('check_if_woocommerce_active')){
	function check_if_woocommerce_active(){
		$plugins = wp_get_active_and_valid_plugins();
		$flag ='';
		foreach($plugins as $plugins){
			if (strpos($plugins,'woocommerce.php') !== false) {
				$flag = 'true';
				break;
			}else{
				 $flag = 'false';
			}
		}
		return $flag;
	}
}

/* add theme support of woocommerce */
if(function_exists('check_if_woocommerce_active')){
	$is_woo_active = check_if_woocommerce_active();
	if($is_woo_active == 'true'){
		add_theme_support( 'woocommerce' );
	}
}
add_action('templ_inside_container_breadcrumb','custom_post_type_single_breadcrumb');
function custom_post_type_single_breadcrumb(){
	$supreme2_theme_settings = get_option(supreme_prefix().'_theme_settings');
	if ( current_theme_supports( 'breadcrumb-trail' ) && $supreme2_theme_settings['supreme_show_breadcrumb'] == 1 ) breadcrumb_trail( array( 'separator' => '&raquo;' ) ); 
}
function star_single_post_categories_tags()
{
	global $post;
	$taxonomies =  supreme_get_post_taxonomies($post);
	$cat_slug = $taxonomies[0];
	$tag_slug = $taxonomies[1];
	$cat_name = __("Categories:",THEME_DOMAIN);
	$tag_name = __("Tags:",THEME_DOMAIN);
	apply_filters('supreme_list_post_categories',supreme_get_categories($cat_name,$cat_slug,'',$tag_name,$tag_slug));// 1- category label, 2- category slug,3- class name of div, 3- tags label,4- tags slug

}
function booking_single_post_comment()
{
	global $post;
	if(function_exists('supreme_get_settings')){
		if(supreme_get_settings( 'enable_comments_on_'.$post->post_type ) == 1){?>
			<div id="comments"><?php comments_template(); ?></div>
		<?php }
	}
}


/* add div actions for contact form */

add_action('after_contact_form','after_contact_form_div');

function after_contact_form_div(){
	echo "<div class='fl_contact_frm'>";
}

add_action('after_contact_form_end','after_contact_form_end_div');

function after_contact_form_end_div(){
	echo "</div>";
}

/* actions for contact form */

add_action('after_contact_message_start','after_contact_message_start_div');
add_action('after_contact_message_end','after_contact_message_end_div');

function after_contact_message_start_div(){
	echo "<div class='fr_contact_msg'>";
}

function after_contact_message_end_div(){
	echo "</div>";
}
function onepager_widget_testimonial_script($transition,$fadin,$fadout)
{
	?>
		<script type="text/javascript">
		var $testimonials = jQuery.noConflict();
		$testimonials(document).ready(function() {
		  $testimonials('#testimonials')
		  	.after('<div id="nav">')
			.cycle({
				fx: '<?php echo $transition; ?>', // choose your transition type, ex: fade, scrollUp, scrollRight, shuffle
				 timeout: '<?php echo $fadin; ?>',
				 speed:'<?php echo $fadout; ?>',
				pager:  '#nav'
			 });
		});
		</script>
	<?php
}
function onepager_testimonial_email($instance,$slider_form)
{
	?>
	<p>
		<?php global $author_email;
			$author_email=$slider_form->get_field_name('s1_email');
					?>
		<label for="<?php echo $slider_form->get_field_id('s1_email'); ?>">
			<?php _e('Author Email 1',THEME_DOMAIN);?>
			<input type="text" class="widefat"  name="<?php echo $author_email; ?>[]" value="<?php echo esc_attr($instance['s1_email'][0]); ?>">
		</label>
	</p>
	
<?php 
	
}
add_action('tmpl_testimonial_field','add_image_link_',10,3);
function add_image_link_($j,$instance,$slider_link)
{
	$s1_email = ($instance['s1_email']);
	global $author_email;
	echo '<p>';
	echo '<label>Author Email '.$j;
	echo ' <input type="text" class="widefat"  name="'.$author_email.'[]" value="'.esc_attr($s1_email[($j - 1)]).'"></label>';
	echo '</label>';
	echo '</p>';
	
}
add_action('admin_init','submit_testimonial_widget',20);
function submit_testimonial_widget()
{
	remove_action('add_testimonial_submit','add_testimonial_submit_button',10,3);
}
add_action('add_testimonial_submit','tadd_slider_submit_button',10,3);
function tadd_slider_submit_button($instance,$text_author,$text_quotetext)
{
	global $author_email;
	?>
		<a href="javascript:void(0);" id="addtButton" class="addButton" type="button" onclick="add_testifields('<?php echo $text_quotetext; ?>','<?php echo $text_author; ?>','<?php echo $author_email;?>');">+ Add more</a>
	<?php
}
remove_action('admin_head','supreme_add_script_addnew_');
add_action('admin_footer','onepager_multitext_box');
function onepager_multitext_box()
{
	global $text_author,$text_quotetext,$author_email;
	?>
<script type="application/javascript">			
		var counter1 = 2;
		function add_testifields(name,title,author_email)
		{
			var newTextBoxDiv = jQuery(document.createElement('div')).attr("class", 'TextDiv' + counter1);
			newTextBoxDiv.html('<p><label>Quote text '+ counter1 +' </label>'+'<textarea  class="widefat" name="'+title+'[]" id="textbox' + counter1 + '" value="" ></textarea></p>');
					
			newTextBoxDiv.append('<p><label>Author name '+ counter1 + '</label>'+'<input type="text" class="widefat" name="'+name+'[]" id="textbox' + counter1 + '" value="" ></p>');	  
			
			newTextBoxDiv.append('<p><label>Author Email '+ counter1 + '</label>'+'<input type="text" class="widefat" name="'+author_email+'[]" id="textbox' + counter1 + '" value="" ></p>');
			
			newTextBoxDiv.appendTo(".tGroup");
				
		    counter1++;
		}
		function remove_tfields()
		{
		    if(counter1-1==1){
			   alert("you need one textbox required.");
			   return false;
		    }
		    counter1--;							
		    jQuery(".TextDiv" + counter1).remove();
		}
	</script>
<?php
}
add_action('tmpl_testimonial_add_extra_field','add_testimonial_author_email',10,2);
function add_testimonial_author_email($i,$instance)
{
	$s1_email = empty($instance['s1_email']) ? '' : apply_filters('widget_s1_email', $instance['s1_email']);
	if(!empty($s1_email[$i])){
		echo '<div class="testimonial_gravatar"><div  id="gravatar" >'.get_avatar( $s1_email[$i], '110', '' ).'</div></div>';
	}
}
add_action('init','remove_testimonial_quote_text');
function remove_testimonial_quote_text()
{
	remove_action('tmpl_testimonial_quote_text','add_testimonial_quote_text',10,2);
}
add_action('tmpl_testimonial_quote_text','onepage_testimonial_quote_text',10,2);
function onepage_testimonial_quote_text($c,$instance)
{
	$quote_text = empty($instance['quotetext']) ? '' : apply_filters('widget_quotetext', $instance['quotetext']);
	$author_text = empty($instance['author']) ? '' : apply_filters('widget_author', $instance['author']);
	?>
	<div class="quote">
	<?php
		echo  $quote_text[$c];
		if($author_text[$c]){?> <cite> - <?php echo $author_text[$c]; ?></cite><?php }
	?>
	</div>
	<?php
}
add_filter('load_popular_post_filter','add_meta_displays',12);
if(!function_exists('add_meta_displays')){
	function add_meta_displays(){
		global $post;
		$categories = '';
		$post_categories = '';
		if(theme_get_settings( 'display_author_name' )){
			if('post' == get_post_type($post->ID)){
				$categories = get_the_category( $post->ID );
			}else{
				$categories = get_the_terms( $post->ID, 'portfoliocategory' );
			}
			if(is_array($categories) && !empty($categories)){
				foreach($categories as $keys => $values){
					if('post' == get_post_type($post->ID)){
						$term_link = get_category_link( $values);
						$post_categories .= '<a href="'.$term_link.'">'.$values->name .'</a>, ';
					}else{
						$term_link = get_term_link( $values);
						if( !is_wp_error( $term_link ) ){
							$post_categories .= '<a href="'.$term_link.'">'.$values->name .'</a>, ';
						}
					}
					
				}
				$post_categories = rtrim($post_categories,', ');
			}else{
				if( @$categories !="" ){
					$post_categories = $categories;
				}
			}
			return $author = '<span class="author_meta">'.__('Posted by',THEME_DOMAIN).' <a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr( get_the_author_meta( 'display_name' ) ) . '">' . get_the_author_meta( 'display_name' ) . '</a> '.__('In',THEME_DOMAIN).' '.$post_categories.' | </span>';
			
		}
	}
}
add_action('wp_head','remove_shortcode_p_tag');
function remove_shortcode_p_tag()
{
	global $post;
	if(is_page())
	{
		global $post;
		if($post->ID == get_option('woocommerce_cart_page_id') || $post->ID == get_option('woocommerce_checkout_page_id') || $post->ID == get_option('woocommerce_pay_page_id') || $post->ID == get_option('woocommerce_thanks_page_id') || $post->ID == get_option('woocommerce_myaccount_page_id') || $post->ID == get_option('woocommerce_edit_address_page_id') || $post->ID == get_option('woocommerce_view_order_page_id') || $post->ID == get_option('woocommerce_change_password_page_id') || $post->ID == get_option('woocommerce_logout_page_id') || $post->ID == get_option('woocommerce_lost_password_page_id') )
		{
			remove_filter( 'the_content', 'wpautop',12 );
		}
	}
}
?>