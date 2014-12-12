<?php
/**
 * Admin functions :  used with other components of the framework admin. This file is for 
 * setting up any basic features and holding additional admin helper functions.
 */

/* Add the admin setup function to the 'admin_menu' hook. */
add_action( 'admin_menu', 'supreme_admin_setup' );

/*
Name : supreme_admin_setup
Descriptuin :  Sets up the adminstration functionality for the framework and themes.
*/
function supreme_admin_setup() {

	/* Load the post meta boxes on the new post and edit post screens. */
	add_action( 'load-post.php', 'supreme_load_post_meta_boxes' );
	add_action( 'load-post-new.php', 'supreme_load_post_meta_boxes' );

	/* Loads admin stylesheets for the framework. */
	add_action( 'admin_enqueue_scripts', 'supreme_admin_enqueue_styles' );
}

/*
Name :supreme_load_post_meta_boxes
Description: Loads the core post meta box files on the 'load-post.php' action hook.  Each meta box file is only loaded if the theme declares support for the feature.
*/
function supreme_load_post_meta_boxes() {
	/* Load the post template meta box. */
	require_if_theme_supports( 'supreme-core-template-hierarchy', trailingslashit( SUPREME_ADMIN ) . 'meta-boxes.php' );
}

/*
Name :supreme_admin_enqueue_styles
Description : Loads the admin.css stylesheet for admin-related features.
*/
function supreme_admin_enqueue_styles( $suffix ) {

	/* Load admin styles if on the widgets screen and the current theme supports 'supreme-core-widgets'. */
	if ( current_theme_supports( 'supreme-core-widgets' ) && 'widgets.php' == $suffix )
		wp_enqueue_style( 'supreme-core-admin' );
}

/*
Name :supreme_get_post_templates
Description : Function for getting an array of available custom templates with a specific header. Ideally, this function would be used to grab custom singular post (any post type) templates.  It is a recreation of the WordPress page templates function because it doesn't allow for other types of templates.
*/
function supreme_get_post_templates( $args = array() ) {

	/* Parse the arguments with the defaults. */
	$args = wp_parse_args( $args, array( 'label' => array( 'Post Template' ) ) );

	/* Get theme and templates variables. */
	$themes = wp_get_themes();
	$theme = wp_get_theme();
	@$templates = $themes[$theme]['Template Files'];
	$post_templates = array();

	/* If there's an array of templates, loop through each template. */
	if ( is_array( $templates ) ) {

		/* Set up a $base path that we'll use to remove from the file name. */
		$base = array( trailingslashit( get_template_directory() ), trailingslashit( get_stylesheet_directory() ) );

		/* Loop through the post templates. */
		foreach ( $templates as $template ) {

			/* Remove the base (parent/child theme path) from the template file name. */
			$basename = str_replace( $base, '', $template );

			/* Get the template data. */
			$template_data = implode( '', file( $template ) );

			/* Make sure the name is set to an empty string. */
			$name = '';

			/* Loop through each of the potential labels and see if a match is found. */
			foreach ( $args['label'] as $label ) {
				if ( preg_match( "|{$label}:(.*)$|mi", $template_data, $name ) ) {
					$name = _cleanup_header_comment( $name[1] );
					break;
				}
			}

			/* If a post template was found, add its name and file name to the $post_templates array. */
			if ( !empty( $name ) )
				$post_templates[trim( $name )] = $basename;
		}
	}

	/* Return array of post templates. */
	return $post_templates;
}


if ( ! function_exists( 'suprme_alternate_stylesheet' ) ) {

function suprme_alternate_stylesheet() {
	$style = '';

	echo "\n" . '<!-- Alt Stylesheet -->' . "\n";
	// If we're using the query variable, be sure to check for /css/layout.css as well.
	if ( $style != '' ) {
	
		if ( file_exists( get_stylesheet_uri() . '/style.css' ) ) {
			
			echo '<link href="' . esc_url( get_stylesheet_uri() ) . '" rel="stylesheet" type="text/css" />' . "\n";
		} else {
			echo '<link href="' . esc_url( get_template_directory_uri() . '/styles/' . $style . '.css' ) . '" rel="stylesheet" type="text/css" />' . "\n";
		}
	} 
} // End woo_output_alt_stylesheet()
}

/*=========================== Load theme customization options ===========================================*/

/* Load custom control classes. */
add_action( 'customize_register', 'supreme_customize_controls', 1 );

/* Register custom sections, settings, and controls. */
add_action( 'customize_register', 'supreme_customize_register' );

/* Add the footer content Ajax to the correct hooks. */
add_action( 'wp_ajax_supreme_customize_footer_content', 'supreme_customize_footer_content_ajax' );
add_action( 'wp_ajax_nopriv_supreme_customize_footer_content', 'supreme_customize_footer_content_ajax' );



/**
 * Registers custom sections, settings, and controls for the $wp_customize instance.
 *
 * @since 1.4.0
 * @access private
 * @param object $wp_customize
 */
function supreme_customize_register( $wp_customize ) {

	/* Get supported theme settings. */
	$supports = get_theme_support( 'supreme-core-theme-settings' );

	/* Get the theme prefix. */
	$prefix = supreme_prefix();

	/* Get the default theme settings. */
	$default_settings = supreme_default_theme_settings();

	/* Add the footer section, setting, and control if theme supports the 'footer' setting. */
	if ( is_array( $supports[0] ) && in_array( 'footer', $supports[0] ) ) {

		/* Add the footer section. */
		$wp_customize->add_section(
			'supreme-core-footer',
			array(
				'title' => 		esc_html__( 'Footer', 'supreme-core' ),
				'priority' => 	200,
				'capability' => 	'edit_theme_options'
			)
		);

		/* Add the 'footer_insert' setting. */
		$wp_customize->add_setting(
			"{$prefix}_theme_settings[footer_insert]",
			array(
				'label' => 		' HTML tags allow, enter whatever you want to display in footer section.',
				'default' => 		@$default_settings['footer_insert'],
				'type' => 			'option',
				'capability' => 		'edit_theme_options',
				'sanitize_callback' => 	'supreme_customize_sanitize',
				'sanitize_js_callback' => 	'supreme_customize_sanitize',
				'transport' => 		'postMessage',
			)
		);

		/* Add the textarea control for the 'footer_insert' setting. */
		$wp_customize->add_control(
			new Hybrid_Customize_Control_Textarea(
				$wp_customize,
				'supreme-core-footer',
				array(
					'label' => 	 __('Footer', THEME_DOMAIN ),
					'section' => 	'supreme-core-footer',
					'settings' => 	"{$prefix}_theme_settings[footer_insert]",
				)
			)
		);

		/* If viewing the customize preview screen, add a script to show a live preview. */
		if ( $wp_customize->is_preview() && !is_admin() )
			add_action( 'wp_footer', 'supreme_customize_preview_script', 21 );
	}
}

/**
 * Sanitizes the footer content on the customize screen.  Users with the 'unfiltered_html' cap can post 
 * anything.  For other users, wp_filter_post_kses() is ran over the setting.
 *
 * @since 1.4.0
 * @access public
 * @param mixed $setting The current setting passed to sanitize.
 * @param object $object The setting object passed via WP_Customize_Setting.
 * @return mixed $setting
 */
function supreme_customize_sanitize( $setting, $object ) {

	/* Get the theme prefix. */
	$prefix = supreme_prefix();

	/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
	if ( "{$prefix}_theme_settings[footer_insert]" == $object->id && !current_user_can( 'unfiltered_html' )  )
		$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );

	/* Return the sanitized setting and apply filters. */
	return apply_filters( "{$prefix}_customize_sanitize", $setting, $object );
}

/**
 * Runs the footer content posted via Ajax through the do_shortcode() function.  This makes sure the 
 * shortcodes are output correctly in the live preview.
 *
 * @since 1.4.0
 * @access private
 */
function supreme_customize_footer_content_ajax() {

	/* Check the AJAX nonce to make sure this is a valid request. */
	check_ajax_referer( 'supreme_customize_footer_content_nonce' );

	/* If footer content has been posted, run it through the do_shortcode() function. */
	if ( isset( $_POST['footer_content'] ) )
		echo do_shortcode( wp_kses_stripslashes( $_POST['footer_content'] ) );

	/* Always die() when handling Ajax. */
	die();
}

/**
 * Handles changing settings for the live preview of the theme.
 *
 * @since 1.4.0
 * @access private
 */
function supreme_customize_preview_script() {

	/* Create a nonce for the Ajax. */
	$nonce = wp_create_nonce( 'supreme_customize_footer_content_nonce' );

	?>
	<script type="text/javascript">
	wp.customize(
		'<?php echo supreme_prefix(); ?>_theme_settings[footer_insert]',
		function( value ) {
			value.bind(
				function( to ) {
					jQuery.post( 
						'<?php echo admin_url( 'admin-ajax.php' ); ?>', 
						{ 
							action: 'supreme_customize_footer_content',
							_ajax_nonce: '<?php echo $nonce; ?>',
							footer_content: to
						},
						function( response ) {
							jQuery( '.footer-content' ).html( response );
						}
					);
				}
			);
		}
	);
	</script>
	<?php
}

/*
	@Theme Customizer settings for Wordpress customizer.
*/	
global $pagenow;
if(is_admin() && 'admin.php' == $pagenow){
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this section.',THEME_DOMAIN ) );
	}
}

/*	Add Action for Customizer   START	*/
	add_action( 'customize_register',  'templatic_register_customizer_settings');
/*	Add Action for Customizer   END	*/

//echo "<pre>";print_r(get_option('supreme_theme_settings'));echo "</pre>";

/*	Function to create sections, settings, controls for wordpress customizer START.  */
global $support_woocommerce;
$support_woocommerce = get_theme_support('supreme_woocommerce_layout');

/*
Name : templatic_register_customizer_settings
Description : register customizer settings option , it returns the options for theme->customizer.php

*/
function templatic_register_customizer_settings( $wp_customize ){
	global $support_woocommerce;
	//ADD SECTION FOR DIFFERENT CONTROLS IN CUSTOMIZER START
		
		//ADD LAYOUT SECTION START
		$wp_customize->add_section('templatic_layout_settings', array(
			'title' => 'Layouts',
			'priority'=> 4
		));
		//ADD LAYOUT SECTION FINISH
		
		//HEADER IMAGE SECTION SETTINGS START
		$wp_customize->get_section('header_image')->priority = 5;
		//HEADER IMAGE SECTION SETTINGS END
		
		//NAVIGATION MENU SECTION SETTINGS START
		$wp_customize->get_section('nav')->priority = 6;
		//NAVIGATION MENU SECTION SETTINGS END
		
		//COLOR SECTION SETTINGS START
		$wp_customize->get_section('colors')->title = __( 'Colors Settings' ,THEME_DOMAIN);
		$wp_customize->get_section('colors')->priority = 7;
		//COLOR SECTION SETTINGS END
		
		//BACKGROUND SECTION SETTINGS START
		$wp_customize->get_section('background_image')->title = __( 'Background Settings',THEME_DOMAIN );
		$wp_customize->get_section('background_image')->priority = 8;
		//BACKGROUND SECTION SETTINGS END
		
		//ADD SITE LOGO SECTION START
		$wp_customize->add_section('templatic_logo_settings', array(
			'title' => 'Site Logo',
			'priority'=> 9
		));
		//ADD SITE LOGO SECTION FINISH
		
		//SITE TITLE SECTION SETTINGS START
		$wp_customize->get_section('title_tagline')->priority = 10;
		//SITE TITLE SECTION SETTINGS END
		
		//ADD THEME OPTIONS SECTION START
		$wp_customize->add_section('templatic_theme_settings', array(
			'title' => 'General Settings',
			'priority'=> 11
		));
		//ADD THEME OPTIONS SECTION FINISH
		
		//STATIC FRONT PAGE SECTION SETTINGS START
		$wp_customize->get_section('static_front_page')->priority = 12;
		//STATIC FRONT PAGE SECTION SETTINGS END
		
		//STATIC FRONT PAGE SECTION SETTINGS START
		$wp_customize->add_section('templatic_contact_page', array(
			'title' => 'Contact Page Settings',
			'priority'=> 13
		));
		//STATIC FRONT PAGE SECTION SETTINGS END
		
		//ADD EXCERPTS SETTING SECTION START
		$wp_customize->add_section('templatic_excerpts_settings', array(
			'title' => 'Listing Page Settings',
			'priority'=> 14
		));
		//ADD EXCERPTS SETTING SECTION FINISH
		
		//Post/Taxonomy detail SECTION START
		$wp_customize->add_section('templatic_detail_settings', array(
			'title' => 'Detail Page Settings',
			'priority'=> 15
		));
		//Post/Taxonomy detail SECTION FINISH
		
		//ADD 404 PAGE SETTING SECTION START
		$wp_customize->add_section('templatic_404_settings', array(
			'title' => '404 Error Page Setting',
			'priority'=> 16
		));
		//ADD 404 PAGE SETTING SECTION FINISH
		
		//SUPREME CORE FOOTER SECTION SETTINGS START
		$wp_customize->get_section('supreme-core-footer')->priority = 17;
		//SUPREME CORE FOOTER SECTION SETTINGS END
		
		//Add Google analytics section start
		$wp_customize->add_section('templatic_google_analytics', array(
			'title' => 'Add Google analytics code',
			'priority'=> 18
		));
		//Add Google analytics section end
		
	//ADD SECTION FOR DIFFERENT CONTROLS IN CUSTOMIZER FINISH
		
	/*	Add Settings START */
		
		//ADD SETTINGS FOR SITE LOGO START
		//CALLBACK FUNCTION: templatic_customize_supreme_logo_url
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[supreme_logo_url]',array(
			'default' => '',
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	"templatic_customize_supreme_logo_url",
			'sanitize_js_callback' => 	"templatic_customize_supreme_logo_url",
			//'transport' => 'postMessage',
		));
		//ADD SETTINGS FOR SITE LOGO FINISH
		
		//ADD SETTINGS FOR FAVICON ICON START
		//CALLBACK FUNCTION: templatic_customize_supreme_favicon_icon
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[supreme_favicon_icon]',array(
			'default' => '',
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	"templatic_customize_supreme_favicon_icon",
			'sanitize_js_callback' => 	"templatic_customize_supreme_favicon_icon",
			//'transport' => 'postMessage',
		));
		//ADD SETTINGS FOR FAVICON ICON FINISH
		
		//ADD SETTINGS FOR HIDE/SHOW SITE DESCRIPTION START
		//CALLBACK FUNCTION: templatic_customize_supreme_site_description
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[supreme_site_description]',array(
			'default' => '',
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	'templatic_customize_supreme_site_description',
			'sanitize_js_callback' => 	'templatic_customize_supreme_site_description',
			
			//'transport' => 'postMessage',
		));
		//ADD SETTINGS FOR HIDE/SHOW SITE DESCRIPTION FINISH
			
		//ADD SETTINGS FOR HIDE/SHOW AUTOINSTALL STRIPE START
		//CALLBACK FUNCTION: templatic_customize_supreme_show_auto_install_message	
		$wp_customize->add_setting('hide_ajax_notification',array(
			'default' => '',
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	"templatic_customize_supreme_show_auto_install_message",
			'sanitize_js_callback' => 	"templatic_customize_supreme_show_auto_install_message",
			//'transport' => 'postMessage',
		));
		//ADD SETTINGS FOR HIDE/SHOW AUTOINSTALL STRIPE FINISH
		
		//ADD SETTINGS TO ENABLE/DISABLE CUSTOM CSS START
		//CALLBACK FUNCTION: templatic_customize_supreme_customcss	
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[customcss]',array(
			'default' => '',
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	'templatic_customize_supreme_customcss',
			'sanitize_js_callback' => 	'templatic_customize_supreme_customcss',
			//'transport' => 'postMessage',
		));
		//ADD SETTINGS TO ENABLE/DISABLE CUSTOM CSS FINISH
		
		
		//ADD SETTINGS TO ENABLE/DISABLE COMMENTS ON PAGE START
		//CALLBACK FUNCTION: templatic_customize_supreme_enable_comments	
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[enable_comments_on_page]',array(
			'default' => 0,
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	'templatic_customize_enable_comments_on_page',
			'sanitize_js_callback' => 	'templatic_customize_enable_comments_on_page',
			//'transport' => 'postMessage',
		));
		//ADD SETTINGS TO ENABLE/DISABLE COMMENTS ON PAGE FINISH
		
		
		//ADD SETTINGS TO ENABLE/DISABLE COMMENTS ON POST START
		//CALLBACK FUNCTION: templatic_customize_supreme_enable_comments	
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[enable_comments_on_post]',array(
			'default' => 1,
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	'templatic_customize_enable_comments_on_post',
			'sanitize_js_callback' => 	'templatic_customize_enable_comments_on_post',
			//'transport' => 'postMessage',
		));
		//ADD SETTINGS TO ENABLE/DISABLE COMMENTS ON POST FINISH
		
		
		//ADD SETTINGS TO ENABLE/DISABLE Sticky Header Menu START
		//CALLBACK FUNCTION: templatic_customize_enable_sticky_header_menu	
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[enable_sticky_header_menu]',array(
			'default' => 1,
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	'templatic_customize_enable_sticky_header_menu',
			'sanitize_js_callback' => 	'templatic_customize_enable_sticky_header_menu',
			//'transport' => 'postMessage',
		));
		//ADD SETTINGS TO ENABLE/DISABLE Sticky Header Menu  FINISH
		
		
		
		//ADD SETTINGS TO SHOW/HIDE IMAGES START
		//CALLBACK FUNCTION: templatic_customize_supreme_display_image	
		if ( current_theme_supports( 'get-the-image' )){
			$wp_customize->add_setting(supreme_prefix().'_theme_settings[supreme_display_image]',array(
				'default' => 1,
				'type' => 'option',
				'capabilities' => 'edit_theme_options',
				'sanitize_callback' => 	'templatic_customize_supreme_display_image',
				'sanitize_js_callback' => 	'templatic_customize_supreme_display_image',
				//'transport' => 'postMessage',
			));	
			//ADD SETTINGS TO SHOW/HIDE IMAGES FINISH
			
			//ADD SETTINGS TO SHOW/HIDE NO IMAGE AVAILABLE START
			//CALLBACK FUNCTION: templatic_customize_supreme_display_noimage	
			$wp_customize->add_setting(supreme_prefix().'_theme_settings[supreme_display_noimage]',array(
				'default' => 1,
				'type' => 'option',
				'capabilities' => 'edit_theme_options',
				'sanitize_callback' => 	'templatic_customize_supreme_display_noimage',
				'sanitize_js_callback' => 	'templatic_customize_supreme_display_noimage',
				//'transport' => 'postMessage',
			));
			//ADD SETTINGS TO SHOW/HIDE NO IMAGE AVAILABLE FINISH
		}
		
		
		//ADD SETTINGS TO SHOW/HIDE EXCERPTS ON ARCHIVE PAGES START
		//CALLBACK FUNCTION: templatic_customize_supreme_archive_display_excerpt	
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[supreme_archive_display_excerpt]',array(
			'default' => 1,
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	'templatic_customize_supreme_archive_display_excerpt',
			'sanitize_js_callback' => 	'templatic_customize_supreme_archive_display_excerpt',
			//'transport' => 'postMessage',
		));
		//ADD SETTINGS TO SHOW/HIDE EXCERPTS ON ARCHIVE PAGES FINISH
		
		//ADD SETTINGS TO DEFINE EXCERPTS LENGTH START
		//CALLBACK FUNCTION: templatic_customize_templatic_excerpt_length	
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[templatic_excerpt_length]',array(
			'default' => 20,
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	'templatic_customize_templatic_excerpt_length',
			'sanitize_js_callback' => 	'templatic_customize_templatic_excerpt_length',
			//'transport' => 'postMessage',
		));
		//ADD SETTINGS TO DEFINE EXCERPTS LENGTH FINISH
		
		//ADD SETTINGS TO DEFINE EXCERPT'S READ MORE LINK TEXT START
		//CALLBACK FUNCTION: templatic_customize_templatic_excerpt_link	
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[templatic_excerpt_link]',array(
			'default' => __('Read More',THEME_DOMAIN),
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	'templatic_customize_templatic_excerpt_link',
			'sanitize_js_callback' => 	'templatic_customize_templatic_excerpt_link',
			//'transport' => 'postMessage',
		));
		//ADD SETTINGS TO DEFINE EXCERPT'S READ MORE LINK TEXT FINISH
		
		//ADD SETTINGS TO SHOW/HIDE AUTHOR BIOGRAPHI ON POST START
		//CALLBACK FUNCTION: templatic_customize_supreme_author_bio_posts	
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[supreme_author_bio_posts]',array(
			'default' => 0,
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	'templatic_customize_supreme_author_bio_posts',
			'sanitize_js_callback' => 	'templatic_customize_supreme_author_bio_posts',
			//'transport' => 'postMessage',
		));
		//ADD SETTINGS TO SHOW/HIDE AUTHOR BIOGRAPHI ON POST FINISH
		
		//ADD SETTINGS TO SHOW/HIDE AUTHOR BIOGRAPHI ON PAGES START
		//CALLBACK FUNCTION: templatic_customize_supreme_author_bio_pages	
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[supreme_author_bio_pages]',array(
			'default' => 0,
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	'templatic_customize_supreme_author_bio_pages',
			'sanitize_js_callback' => 	'templatic_customize_supreme_author_bio_pages',
			//'transport' => 'postMessage',
		));
		//ADD SETTINGS TO SHOW/HIDE AUTHOR BIOGRAPHI ON PAGES FINISH
		
		//ADD SETTINGS FOR PAGE LAYOUTS START
		//CALLBACK FUNCTION: templatic_customize_supreme_global_layout	
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[supreme_global_layout]',array(
			'default' => '',
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	'templatic_customize_supreme_global_layout',
			'sanitize_js_callback' => 	'templatic_customize_supreme_global_layout',
			//'transport' => 'postMessage',
		));
		//ADD SETTINGS FOR PAGE LAYOUTS FINISH
		
		//ADD SETTINGS SHOW/HIDE BREADCRUMBS START
		//CALLBACK FUNCTION: templatic_customize_supreme_show_breadcrumb	
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[supreme_show_breadcrumb]',array(
				'default' => 1,
				'type' => 'option',
				'capabilities' => 'edit_theme_options',
				'sanitize_callback' => 	"templatic_customize_supreme_show_breadcrumb",
				'sanitize_js_callback' => 	"templatic_customize_supreme_show_breadcrumb"
				//'transport' => 'postMessage',
		));
		//ADD SETTINGS SHOW/HIDE BREADCRUMBS FINISH
		
		//ADD SETTINGS SHOW/HIDE CAPTCHA ON CONTACT US PAGE START
		//CALLBACK FUNCTION: templatic_customize_supreme_global_contactus_captcha
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[supreme_global_contactus_captcha]',array(
			'default' => 0,
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	'templatic_customize_supreme_global_contactus_captcha',
			'sanitize_js_callback' => 	'templatic_customize_supreme_global_contactus_captcha',
			//'transport' => 'postMessage',
		));
		//ADD SETTINGS SHOW/HIDE CAPTCHA ON CONTACT US PAGE FINISH
		
		//ADD SETTINGS SHOW/HIDE INQUIRY FORM ON CONTACT US PAGE START
		//CALLBACK FUNCTION: templatic_customize_supreme_global_contactus_captcha
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[enable_inquiry_form]',array(
			'default' => 0,
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	'templatic_customize_enable_inquiry_form',
			'sanitize_js_callback' => 	'templatic_customize_enable_inquiry_form',
			//'transport' => 'postMessage',
		));
		//ADD SETTINGS SHOW/HIDE CAPTCHA ON CONTACT US PAGE FINISH
				
		//ADD SETTINGS FOR WOOCOMMERCE PAGE LAYOUTS START
		//CALLBACK FUNCTION: templatic_customize_supreme_woocommerce_layout
		if($support_woocommerce){
			$wp_customize->add_setting(supreme_prefix().'_theme_settings[supreme_woocommerce_layout]',array(
				'default' => '',
				'type' => 'option',
				'capabilities' => 'edit_theme_options',
				'sanitize_callback' => 	'templatic_customize_supreme_woocommerce_layout',
				'sanitize_js_callback' => 	'templatic_customize_supreme_woocommerce_layout',
				//'transport' => 'postMessage',
			));
		}
		//ADD SETTINGS FOR WOOCOMMERCE PAGE LAYOUTS FINISH
		
		//ADD SETTINGS 404 PAGE SETTINGS START
		//CALLBACK FUNCTION: templatic_customize_post_type_label
				// ADDED CUSTOM LABEL CONTROL START
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[temp_label]', array(
	        'default' => '',
		));
		
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[footer_lbl]', array(
	        'default' => '',
		));
				// ADDED CUSTOM LABEL CONTROL FINISH
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[post_type_label]', array(
	        'default' => '',
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	'templatic_customize_post_type_label',
			'sanitize_js_callback' => 	'templatic_customize_post_type_label',
		));
		//ADD SETTINGS 404 PAGE SETTINGS FINISH
		
		//Google Analiticla code controls start
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[analytics_lbl]', array(
	        'default' => '',
		));
		
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[supreme_gogle_analytics_code]', array(
	        'default' => '',
			'type' => 'option',
			'capabilities' => 'templatic_google_analytics',
			'sanitize_callback' => 	'templatic_google_analytics_code',
			'sanitize_js_callback' => 	'templatic_google_analytics_code',
		));
		//Google Analiticla code controls end
		
		
		
		//COLOR SETTINGS START.
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[color_picker_color1]',array(
				'default' => '',
				'type' => 'option',
				'capabilities' => 'edit_theme_options',
				'sanitize_callback' => 	"templatic_customize_supreme_color1",
				'sanitize_js_callback' => 	"templatic_customize_supreme_color1",
				//'transport' => 'postMessage',
			));
			
			$wp_customize->add_setting(supreme_prefix().'_theme_settings[color_picker_color2]',array(
				'default' => '',
				'type' => 'option',
				'capabilities' => 'edit_theme_options',
				'sanitize_callback' => 	"templatic_customize_supreme_color2",
				'sanitize_js_callback' => 	"templatic_customize_supreme_color2",
				//'transport' => 'postMessage',
			));
			
			$wp_customize->add_setting(supreme_prefix().'_theme_settings[color_picker_color3]',array(
				'default' => '',
				'type' => 'option',
				'capabilities' => 'edit_theme_options',
				'sanitize_callback' => 	"templatic_customize_supreme_color3",
				'sanitize_js_callback' => 	"templatic_customize_supreme_color3",
				//'transport' => 'postMessage',
			));
			
			$wp_customize->add_setting(supreme_prefix().'_theme_settings[color_picker_color4]',array(
				'default' => '',
				'type' => 'option',
				'capabilities' => 'edit_theme_options',
				'sanitize_callback' => 	"templatic_customize_supreme_color4",
				'sanitize_js_callback' => 	"templatic_customize_supreme_color4",
				//'transport' => 'postMessage',
			));
			
			$wp_customize->add_setting(supreme_prefix().'_theme_settings[color_picker_color5]',array(
				'default' => '',
				'type' => 'option',
				'capabilities' => 'edit_theme_options',
				'sanitize_callback' => 	"templatic_customize_supreme_color5",
				'sanitize_js_callback' => 	"templatic_customize_supreme_color5",
				//'transport' => 'postMessage',
			));
			
		//COLOR SETTINGS FINISH.
		
		//TEXTURE SETTINGS START.
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[templatic_texture1]',array(
				'default' => '',
				'type' => 'option',
				'capabilities' => 'edit_theme_options',
				'sanitize_callback' => 	"templatic_customize_templatic_texture1",
				'sanitize_js_callback' => 	"templatic_customize_templatic_texture1",
				//'transport' => 'postMessage',
		));
		
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[alternate_of_texture]',array(
			'default' => '',
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	"templatic_customize_alternate_of_texture",
			'sanitize_js_callback' => 	"templatic_customize_alternate_of_texture",
			//'transport' => 'postMessage',
		));
		//TEXTURE SETTINGS FINISH.
				
		//ADD SETTINGS FOR BACKGROUND HEADER IMAGE START
		//CALLBACK FUNCTION: templatic_customize_supreme_header_background_image
		$wp_customize->add_setting( 'header_image', array(
			'default'        => get_theme_support( 'custom-header', 'default-image' ),
			'theme_supports' => 'custom-header',
		) );
		
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[header_image_display]',array(
			'default' => 'after_nav',
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	"templatic_customize_header_image_display",
			'sanitize_js_callback' => 	"templatic_customize_header_image_display",
			//'transport' => 'postMessage',
		));
		//ADD SETTINGS FOR BACKGROUND HEADER IMAGE FINISH
		
		//Add settings for hide/show header text start
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[display_header_text]',array(
			'default' => 1,
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	"templatic_customize_display_header_text",
			'sanitize_js_callback' => 	"templatic_customize_display_header_text",
			//'transport' => 'postMessage',
		));
		//Add settings for hide/show header text end
		
		//Add settings for hide/show header text start
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[display_author_name]',array(
			'default' => 1,
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	"templatic_customize_display_author_name",
			'sanitize_js_callback' => 	"templatic_customize_display_author_name",
			//'transport' => 'postMessage',
		));
		//Add settings for hide/show header text end
		
		//Add settings to hide/show publish date start
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[display_publish_date]',array(
			'default' => 1,
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	"templatic_customize_display_publish_date",
			'sanitize_js_callback' => 	"templatic_customize_display_publish_date",
			//'transport' => 'postMessage',
		));
		//Add settings for hide/show publish date end
		
		//Add settings to hide/show terms of post start
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[display_post_terms]',array(
			'default' => 1,
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	"templatic_customize_display_post_terms",
			'sanitize_js_callback' => 	"templatic_customize_display_post_terms",
			//'transport' => 'postMessage',
		));
		//Add settings to hide/show terms of post end
		
		//Add settings to hide/show terms of post start
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[display_post_response]',array(
			'default' => 1,
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	"templatic_customize_display_post_response",
			'sanitize_js_callback' => 	"templatic_customize_display_post_response",
			//'transport' => 'postMessage',
		));
		//Add settings to hide/show terms of post end
		
		//Add settings to hide/show view counter on detail start
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[enable_view_counter]',array(
			'default' => '',
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	"templatic_customize_enable_view_counter",
			'sanitize_js_callback' => 	"templatic_customize_enable_view_counter",
			//'transport' => 'postMessage',
		));
		//Add settings to hide/show view counter on detail end

		//Add settings to hide/show facebook share on detail start
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[facebook_share_detail_page]',array(
			'default' => 1,
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	"templatic_customize_facebook_share_detail_page",
			'sanitize_js_callback' => 	"templatic_customize_facebook_share_detail_page",
			//'transport' => 'postMessage',
		));
		//Add settings to hide/show facebook share on detail end
		
		//Add settings to hide/show google share on detail start
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[google_share_detail_page]',array(
			'default' => 1,
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	"templatic_customize_google_share_detail_page",
			'sanitize_js_callback' => 	"templatic_customize_google_share_detail_page",
			//'transport' => 'postMessage',
		));
		//Add settings to hide/show google share on detail end
		
		//Add settings to hide/show twitter share on detail start
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[twitter_share_detail_page]',array(
			'default' => 1,
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	"templatic_customize_twitter_share_detail_page",
			'sanitize_js_callback' => 	"templatic_customize_twitter_share_detail_page",
			//'transport' => 'postMessage',
		));
		//Add settings to hide/show twitter share on detail end
		
		//Add settings to hide/show pintrest share on detail start
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[pintrest_detail_page]',array(
			'default' => 1,
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	"templatic_customize_pintrest_detail_page",
			'sanitize_js_callback' => 	"templatic_customize_pintrest_detail_page",
			//'transport' => 'postMessage',
		));
		//Add settings to hide/show twitter share on detail end
		
		
	/*	Add Settings END */
		
	/*	Add Control START */
		
		//ADDED SITE LOGO CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		$wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, supreme_prefix().'_theme_settings[supreme_logo_url]', array(
			'label' => __(' Upload image for logo',THEME_DOMAIN),
			'section' => 'templatic_logo_settings',
			'settings' => supreme_prefix().'_theme_settings[supreme_logo_url]',
		)));
		//ADDED SITE LOGO CONTROL FINISH
		
		//ADDED SITE FAVICON ICON CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		$wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, supreme_prefix().'_theme_settings[supreme_favicon_icon]', array(
			'label' => __(' Upload favicon icon',THEME_DOMAIN),
			'section' => 'templatic_logo_settings',
			'settings' => supreme_prefix().'_theme_settings[supreme_favicon_icon]',
		)));
		//ADDED SITE FAVICON ICON CONTROL FINISH
		
		//ADDED SHOW/HIDE SITE DESCRIPTION CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		//type    : Type of control you want to use
		$wp_customize->add_control( 'supreme_site_description', array(
			'label' => __('Hide Site Description',THEME_DOMAIN),
			'section' => 'title_tagline',
			'settings' => supreme_prefix().'_theme_settings[supreme_site_description]',
			'type' => 'checkbox',
			'priority' => 106
		));
		//ADDED SHOW/HIDE SITE DESCRIPTION CONTROL FINISH
		
		
		//ADDED SHOW/HIDE AUTOINSTALL STRIPE CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		//type    : Type of control you want to use
		$wp_customize->add_control('hide_ajax_notification', array(
			'label'   => __( 'Hide autoinstall', THEME_DOMAIN ),
			'section' => 'templatic_theme_settings',
			'settings'   => 'hide_ajax_notification',
			'type' => 'checkbox',
			'priority' => '1',
		) );
		//ADDED SHOW/HIDE AUTOINSTALL STRIPE CONTROL FINISH
		
		
		//ADDED ENABLE/DISABLE CUSTOM CSS CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		//type    : Type of control you want to use
		$wp_customize->add_control( 'customcss', array(
			'label' => __('Use custom css',THEME_DOMAIN),
			'section' => 'templatic_theme_settings',
			'settings' => supreme_prefix().'_theme_settings[customcss]',
			'type' => 'checkbox',
			'priority' => '2',
		));
		//ADDED ENABLE/DISABLE CUSTOM CSS CONTROL FINISH
		
		
		//ADDED ENABLE/DISABLE COMMENTS ON PAGES CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		//type    : Type of control you want to use
		$wp_customize->add_control( 'enable_comments_on_page', array(
			'label' => __('Enable comments on page',THEME_DOMAIN),
			'section' => 'templatic_detail_settings',
			'settings' => supreme_prefix().'_theme_settings[enable_comments_on_page]',
			'type' => 'checkbox',
			'priority' => '6',
		));
		//ADDED ENABLE/DISABLE COMMENTS ON PAGES CONTROL FINISH
		
		
		//ADDED ENABLE/DISABLE COMMENTS ON POSTS CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		//type    : Type of control you want to use
		$wp_customize->add_control( 'enable_comments_on_post', array(
			'label' => __('Enable comments on post',THEME_DOMAIN),
			'section' => 'templatic_detail_settings',
			'settings' => supreme_prefix().'_theme_settings[enable_comments_on_post]',
			'type' => 'checkbox',
			'priority' => '7',
		));
		//ADDED ENABLE/DISABLE COMMENTS ON POSTS CONTROL FINISH
		
		//ADDED ENABLE/DISABLE sticky header menu START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		//type    : Type of control you want to use
		$wp_customize->add_control( 'enable_sticky_header_menu', array(
			'label' => __('Enable sticky header menu',THEME_DOMAIN),
			'section' => 'templatic_theme_settings',
			'settings' => supreme_prefix().'_theme_settings[enable_sticky_header_menu]',
			'type' => 'checkbox',
			'priority' => '5',
		));
		//ADDED ENABLE/DISABLE COMMENTS ON POSTS CONTROL FINISH
		
		
		//ADDED SHOW/HIDE AUTHOR BIOGRAPHY ON POSTS CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		//type    : Type of control you want to use
		$wp_customize->add_control( 'supreme_author_bio_posts', array(
			'label' => __('Show author biography on posts',THEME_DOMAIN),
			'section' => 'templatic_theme_settings',
			'settings' => supreme_prefix().'_theme_settings[supreme_author_bio_posts]',
			'type' => 'checkbox',
			'priority' => '6',
		));
		//ADDED SHOW/HIDE AUTHOR BIOGRAPHY ON POSTS CONTROL FINISH
		
		//ADDED SHOW/HIDE AUTHOR BIOGRAPHY ON PAGES CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		//type    : Type of control you want to use
		$wp_customize->add_control( 'supreme_author_bio_pages', array(
			'label' => __('Show author biography on pages',THEME_DOMAIN),
			'section' => 'templatic_theme_settings',
			'settings' => supreme_prefix().'_theme_settings[supreme_author_bio_pages]',
			'type' => 'checkbox',
			'priority' => '7',
		));
		//ADDED SHOW/HIDE AUTHOR BIOGRAPHY ON PAGES CONTROL FINISH
		
		//ADDED SHOW/HIDE BREADCRUMBS CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		//type    : Type of control you want to use
		$wp_customize->add_control( 'supreme_show_breadcrumb', array(
				'label'   => __( 'Show Breadcrumb', THEME_DOMAIN),
				'section' => 'templatic_theme_settings',
				'settings'   => supreme_prefix().'_theme_settings[supreme_show_breadcrumb]',
				'type' => 'checkbox',
				'priority' => '8',
		) ) ;
		//ADDED SHOW/HIDE BREADCRUMBS CONTROL FINISH
		
		//ADDED GLOBAL PAGE LAYOUT CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		//type    : Type of control you want to use
		//choices : The options which you want to prompt to choose one
		$wp_customize->add_control( 'supreme_global_layout', array(
			'label' => __('Global Content Layout',THEME_DOMAIN),
			'section' => 'templatic_layout_settings',
			'settings' => supreme_prefix().'_theme_settings[supreme_global_layout]',
			'type' => 'select',
			'choices' => array(
								'layout_default' => 'Default Layout',	
								'layout_1c' => 'One Column',	
								'layout_2c_l' => 'Two Columns, Left',	
								'layout_2c_r' => 'Two Columns, Right',	
							  ),
		));
		//ADDED GLOBAL PAGE LAYOUT CONTROL FINISH
		
		//ADDED WOO COMMERCE PAGE LAYOUT CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		//type    : Type of control you want to use
		//choices : The options which you want to prompt to choose one
		if($support_woocommerce){
			$wp_customize->add_control( 'supreme_woocommerce_layout', array(
				'label' => __('WooCommerce Layout',THEME_DOMAIN),
				'section' => 'templatic_layout_settings',
				'settings' => supreme_prefix().'_theme_settings[supreme_woocommerce_layout]',
				'type' => 'select',
				'choices' => array(
									'layout_default' => 'Default Layout',	
									'layout_1c' => 'One Column',	
									'layout_2c_l' => 'Two Columns, Left',	
									'layout_2c_r' => 'Two Columns, Right',	
								  ),
			));
		}
		//ADDED WOO COMMERCE PAGE LAYOUT CONTROL FINISH
		
		
		//ADDED CONTACT US PAGE CAPTCHA SETTING CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		//type    : Type of control you want to use
		//choices : The options which you want to prompt to choose one
		$wp_customize->add_control( 'supreme_global_contactus_captcha', array(
			'label' => __('Show captcha',THEME_DOMAIN),
			'section' => 'templatic_contact_page',
			'settings' => supreme_prefix().'_theme_settings[supreme_global_contactus_captcha]',
			'type' => 'checkbox',
			'choices' => array(
								'WP-reCaptcha' => 'WP-reCaptcha',	
							  ),
		));
		//ADDED CONTACT US PAGE CAPTCHA SETTING CONTROL FINISH
		
		
		//ADDED CONTACT US PAGE CAPTCHA SETTING CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		//type    : Type of control you want to use
		//choices : The options which you want to prompt to choose one
		$wp_customize->add_control( 'enable_inquiry_form', array(
			'label' => __('Show inquiry form',THEME_DOMAIN),
			'section' => 'templatic_contact_page',
			'settings' => supreme_prefix().'_theme_settings[enable_inquiry_form]',
			'type' => 'checkbox',
		));
		//ADDED CONTACT US PAGE CAPTCHA SETTING CONTROL FINISH
		
		
		if ( current_theme_supports( 'get-the-image' )){
			//ADDED DISPLAY IMAGE ON EXCERPT CONTROL START
			//ARGS USAGES
			//label   : Text which you want to display for which this control is to be used. 
			//section : In which section you want to display this control
			//settings: Define the settings to call callback function
			//type    : Type of control you want to use
			//choices : The options which you want to prompt to choose one
			$wp_customize->add_control( 'supreme_display_image', array(
				'label' => __('Display Images',THEME_DOMAIN),
				'section' => 'templatic_excerpts_settings',
				'settings' => supreme_prefix().'_theme_settings[supreme_display_image]',
				'type' => 'checkbox',
				'priority' => '1',
			));	
			//ADDED DISPLAY IMAGE ON EXCERPT CONTROL FINISH
			
			
			//ADDED DISPLAY NO IMAGE AVAILABLE ON EXCERPT CONTROL START
			//ARGS USAGES
			//label   : Text which you want to display for which this control is to be used. 
			//section : In which section you want to display this control
			//settings: Define the settings to call callback function
			//type    : Type of control you want to use
			//choices : The options which you want to prompt to choose one
			$wp_customize->add_control( 'supreme_display_noimage', array(
				'label' => __('Display No-Image-Available ',THEME_DOMAIN),
				'section' => 'templatic_excerpts_settings',
				'settings' => supreme_prefix().'_theme_settings[supreme_display_noimage]',
				'type' => 'checkbox',
				'priority' => '2',
			));	
			//ADDED DISPLAY NO IMAGE AVAILABLE ON EXCERPT CONTROL FINISH
		}
		
		//ADDED SHOW/HIDE EXCERPT ON ARCHIVE PAGE CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		//type    : Type of control you want to use
		//choices : The options which you want to prompt to choose one
		$wp_customize->add_control( 'supreme_archive_display_excerpt', array(
			'label' => __('Display excerpts',THEME_DOMAIN),
			'section' => 'templatic_excerpts_settings',
			'settings' => supreme_prefix().'_theme_settings[supreme_archive_display_excerpt]',
			'type' => 'checkbox',
			'priority' => '6',
		));
		//ADDED SHOW/HIDE EXCERPT ON ARCHIVE PAGE CONTROL FINISH
		
		
		//ADDED CONTROL FOR 404 PAGE SETTINGS START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		//type    : Type of control you want to use
		//choices : The options which you want to prompt to choose one
		$post_types=get_post_types();
		$PostTypeName = '';
		foreach($post_types as $post_type):		
			if($post_type!='page' && $post_type!="attachment" && $post_type!="revision" && $post_type!="nav_menu_item"):
				$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));	
				$archive_query = new WP_Query('showposts=60&post_type='.$post_type);
				if( count(@$archive_query->posts) > 0 ){
					$PostTypeName .= $post_type.', ';
				}
			endif;
		endforeach;
		$all_post_types = rtrim($PostTypeName,', ');
		
		$wp_customize->add_control( new supreme_custom_lable_control($wp_customize, supreme_prefix().'_theme_settings[temp_label]', array(
			'label' => __("Enter comma saperated post type slug(s) which you want to display on 404 page",THEME_DOMAIN),
			'section' => 'templatic_404_settings',
		)));
		
		
		$wp_customize->add_control( new supreme_custom_lable_control($wp_customize, supreme_prefix().'_theme_settings[footer_lbl]', array(
			'label' => __('Footer Text ( e.g. <p class="copyright">&copy; 2011 <a href="http://templatic.com/demos/responsive">Responsive</a>. All Rights Reserved. </p>)',THEME_DOMAIN),
			'section' => 'supreme-core-footer',
			'priority' => 1,
		)));
		
		$wp_customize->add_control( 'post_type_label', array(
			'label' => __("Post type slug(s):",THEME_DOMAIN).$all_post_types,
			'section' => 'templatic_404_settings',
			'settings' => supreme_prefix().'_theme_settings[post_type_label]',
		));
		//ADDED CONTROL FOR 404 PAGE SETTINGS FINISH
		$a = '<a href="#">Click</a>';
		//Google Analiticla code controls start
		$wp_customize->add_control( new supreme_custom_lable_control($wp_customize, supreme_prefix().'_theme_settings[analytics_lbl]', array(
			'label' => __("You can add Google Analytics tracking code here.",THEME_DOMAIN),
			'section' => 'templatic_google_analytics',
		)));
		$wp_customize->add_control( 
			new Hybrid_Customize_Control_Textarea(
				$wp_customize,
			'supreme_gogle_analytics_code', array(
			'label' => __("Add google analytics code",THEME_DOMAIN),
			'section' => 'templatic_google_analytics',
			'settings' => supreme_prefix().'_theme_settings[supreme_gogle_analytics_code]',
		)));
		//Google Analiticla code controls end
		
		//Color Settings Control Start
		/*
			Primary: 	 Effect on buttons, links and main headings.
			Secondary: 	 Effect on sub-headings.
			Content: 	 Effect on content.
			Sub-text: 	 Effect on sub-texts.
			Background:  Effect on body & menu background. 
		
		*/
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_picker_color1', array(
			'label'   => __( 'Body Background Color', 'templatic' ),
			'section' => 'colors',
			'settings'   => supreme_prefix().'_theme_settings[color_picker_color1]',
			'priority' => 1,
		) ) );
		
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_picker_color2', array(
			'label'   => __( 'Links, Headings, Titles and Buttons Color', 'templatic' ),
			'section' => 'colors',
			'settings'   => supreme_prefix().'_theme_settings[color_picker_color2]',
			'priority' => 2,	
		) ) );
		
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_picker_color3', array(
			'label'   => __( 'Page Content Color', 'templatic' ),
			'section' => 'colors',
			'settings'   => supreme_prefix().'_theme_settings[color_picker_color3]',
			'priority' => 3,
		) ) );
		
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_picker_color4', array(
			'label'   => __( 'Sub Text Color', 'templatic' ),
			'section' => 'colors',
			'settings'   => supreme_prefix().'_theme_settings[color_picker_color4]',
			'priority' => 4,
		) ) );
		
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_picker_color5', array(
			'label'   => __( 'Secondary Color - Link Hover & Slider Background', 'templatic' ),
			'section' => 'colors',
			'settings'   => supreme_prefix().'_theme_settings[color_picker_color5]',
			'priority' => 5,
		) ) );
	
		
		//REMOVE WORDPRESS DEFAULT CONTROL START.
		$wp_customize->remove_control('background_color');
		//REMOVE WORDPRESS DEFAULT CONTROL FINISH.
		//Color Settings Control End
		
		
		//ADD CONTROL FOR TEXTURE SETTINGS START.
		$wp_customize->add_control( new WP_Image_Control($wp_customize, supreme_prefix().'_theme_settings[templatic_texture1]', array(
			'label'   => __( 'Texture Overlays', 'templatic' ),
			'section' => 'background_image',
			'settings'   => supreme_prefix().'_theme_settings[templatic_texture1]',
		)));
		
		$wp_customize->add_control( supreme_prefix().'_theme_settings[alternate_of_texture]', array(
			'label' => __('OR Enter Your Custom Texture',THEME_DOMAIN),
			'section' => 'background_image',
			'settings' => supreme_prefix().'_theme_settings[alternate_of_texture]',
			'type' => 'text',
		));
		
		//ADD CONTROL FOR TEXTURE SETTINGS FINISH.
		
		
		//ADDED HEADER BACKGROUND IMAGE CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		$wp_customize->add_control( new WP_Customize_Header_Image_Control( $wp_customize ) );
		
		$wp_customize->add_control( supreme_prefix().'_theme_settings[header_image_display]', array(
			'label' => __('Display Header Image ( Go in Appearance -> Header to set/change the image )',THEME_DOMAIN),
			'section' => 'header_image',
			'settings' => supreme_prefix().'_theme_settings[header_image_display]',
			'type' => 'select',
			'choices' => array(
								'before_nav' 	=> 'Before Secondary Menu',	
								'after_nav' 	=> 'After Secondary Menu',	
							  ),
		));
		
		//Added display header text CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		$wp_customize->add_control( supreme_prefix().'_theme_settings[display_header_text]', array(
			'label' => __('Display Header Text',THEME_DOMAIN),
			'section' => 'title_tagline',
			'settings' => supreme_prefix().'_theme_settings[display_header_text]',
			'type' => 'checkbox',
			'priority' => 105,
		));
		
		//Added display author name CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		$wp_customize->add_control( supreme_prefix().'_theme_settings[display_author_name]', array(
			'label' => __('Display Author Name',THEME_DOMAIN),
			'section' => 'templatic_excerpts_settings',
			'settings' => supreme_prefix().'_theme_settings[display_author_name]',
			'type' => 'checkbox',
			'priority' => '3',
		));
		
		//Added display publish date CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		$wp_customize->add_control( supreme_prefix().'_theme_settings[display_publish_date]', array(
			'label' => __('Display Publish Date',THEME_DOMAIN),
			'section' => 'templatic_excerpts_settings',
			'settings' => supreme_prefix().'_theme_settings[display_publish_date]',
			'type' => 'checkbox',
			'priority' => '4',
		));
		
		//Added display Terms of post CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		$wp_customize->add_control( supreme_prefix().'_theme_settings[display_post_terms]', array(
			'label' => __('Display Terms of Post',THEME_DOMAIN),
			'section' => 'templatic_excerpts_settings',
			'settings' => supreme_prefix().'_theme_settings[display_post_terms]',
			'type' => 'checkbox',
			'priority' => '5',
		));
		
		$wp_customize->add_control( supreme_prefix().'_theme_settings[display_post_response]', array(
			'label' => __('Display Rensponses On Post',THEME_DOMAIN),
			'section' => 'templatic_excerpts_settings',
			'settings' => supreme_prefix().'_theme_settings[display_post_response]',
			'type' => 'checkbox',
			'priority' => '6',
		));
		
		
		
		//ADDED EXCERPT LENGTH ON ARCHIVE PAGE CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		//type    : Type of control you want to use
		//choices : The options which you want to prompt to choose one
		$wp_customize->add_control( 'templatic_excerpt_length', array(
			'label' => __('Excerpt length',THEME_DOMAIN),
			'section' => 'templatic_excerpts_settings',
			'settings' => supreme_prefix().'_theme_settings[templatic_excerpt_length]',
			'type' => 'text',
		));
		//ADDED EXCERPT LENGTH ON ARCHIVE PAGE CONTROL FINISH
		
		
		//ADDED EXCERPT LINK TEXT CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		//type    : Type of control you want to use
		//choices : The options which you want to prompt to choose one
		$wp_customize->add_control( 'templatic_excerpt_link', array(
			'label' => __('Text for Continue reading',THEME_DOMAIN),
			'section' => 'templatic_excerpts_settings',
			'settings' => supreme_prefix().'_theme_settings[templatic_excerpt_link]',
			'type' => 'text',
		));

		//ADDED EXCERPT LINK TEXT CONTROL FINISH
		
		//ADDED View counter CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		//type    : Type of control you want to use
		//choices : The options which you want to prompt to choose one
		$wp_customize->add_control( supreme_prefix().'_theme_settings[enable_view_counter]', array(
			'label' => __('Enable view counter',THEME_DOMAIN),
			'section' => 'templatic_detail_settings',
			'settings' => supreme_prefix().'_theme_settings[enable_view_counter]',
			'type' => 'checkbox',
			'priority' => '1',
		));

		//ADDED view counter CONTROL FINISH
		
		//ADDED facebook share CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		//type    : Type of control you want to use
		//choices : The options which you want to prompt to choose one
		$wp_customize->add_control( supreme_prefix().'_theme_settings[facebook_share_detail_page]', array(
			'label' => __('Enable Facebook share button',THEME_DOMAIN),
			'section' => 'templatic_detail_settings',
			'settings' => supreme_prefix().'_theme_settings[facebook_share_detail_page]',
			'type' => 'checkbox',
			'priority' => '2',
		));

		//ADDED facebook share CONTROL FINISH
		
		//ADDED google share CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		//type    : Type of control you want to use
		//choices : The options which you want to prompt to choose one
		$wp_customize->add_control( supreme_prefix().'_theme_settings[google_share_detail_page]', array(
			'label' => __('Enable Google share button',THEME_DOMAIN),
			'section' => 'templatic_detail_settings',
			'settings' => supreme_prefix().'_theme_settings[google_share_detail_page]',
			'type' => 'checkbox',
			'priority' => '3',
		));

		//ADDED google share CONTROL FINISH
		
		//ADDED twitter share CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		//type    : Type of control you want to use
		//choices : The options which you want to prompt to choose one
		$wp_customize->add_control( supreme_prefix().'_theme_settings[twitter_share_detail_page]', array(
			'label' => __('Enable Twitter share button',THEME_DOMAIN),
			'section' => 'templatic_detail_settings',
			'settings' => supreme_prefix().'_theme_settings[twitter_share_detail_page]',
			'type' => 'checkbox',
			'priority' => '4',
		));

		//ADDED twitter share CONTROL FINISH
		
		//ADDED pintrest share CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		//type    : Type of control you want to use
		//choices : The options which you want to prompt to choose one
		$wp_customize->add_control( supreme_prefix().'_theme_settings[pintrest_detail_page]', array(
			'label' => __('Enable pintrest share button',THEME_DOMAIN),
			'section' => 'templatic_detail_settings',
			'settings' => supreme_prefix().'_theme_settings[pintrest_detail_page]',
			'type' => 'checkbox',
			'priority' => '5',
		));

		//ADDED pintrest share CONTROL FINISH
		
		
		//ADDED HEADER BACKGROUND IMAGE CONTROL FINISH
		$wp_customize->remove_control('header_textcolor');
		$wp_customize->remove_control('display_header_text');
	/*	Add Control END */
		

	
}
/*	Function to create sections, settings, controls for wordpress customizer END.  */


/*  Handles changing settings for the live preview of the theme START.  */	
	
	function templatic_customize_templatic_excerpt_length( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( "templatic_excerpt_length" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_templatic_excerpt_length", $setting, $object );
	}
	function templatic_customize_templatic_excerpt_link( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( "templatic_excerpt_link" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_templatic_excerpt_link", $setting, $object );
	}
	
	function templatic_customize_supreme_customcss( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( "customcss" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_customcss", $setting, $object );
	}
	
	function templatic_customize_supreme_show_auto_install_message( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( "hide_ajax_notification" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_show_auto_install_message", $setting, $object );
	}
	
	function templatic_customize_enable_comments_on_page( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[enable_comments_on_page]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_enable_comments_on_page", $setting, $object );
	}
	
	function templatic_customize_enable_comments_on_post( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[enable_comments_on_post]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_enable_comments_on_post", $setting, $object );
	}
	
	function templatic_customize_enable_sticky_header_menu( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[enable_sticky_header_menu]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_enable_sticky_header_menu", $setting, $object );
	}

	
	
	function templatic_customize_supreme_logo_url( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[supreme_logo_url]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_logo_url", $setting, $object );
	}
	
	function templatic_customize_supreme_favicon_icon( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[supreme_favicon_icon]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_favicon_icon", $setting, $object );
	}
	
	function templatic_customize_supreme_site_description( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[supreme_site_description]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_site_description", $setting, $object );
	}

	function templatic_customize_supreme_display_image( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[supreme_display_image]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_display_image", $setting, $object );
	}
	
	function templatic_customize_supreme_display_noimage( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[supreme_display_noimage]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_display_noimage", $setting, $object );
	}	
	
	function templatic_customize_supreme_archive_display_excerpt( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[supreme_archive_display_excerpt]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_archive_display_excerpt", $setting, $object );
	}
	
	
	function templatic_customize_supreme_author_bio_posts( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[supreme_author_bio_posts]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_author_bio_posts", $setting, $object );
	}
	
	function templatic_customize_supreme_author_bio_pages( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[supreme_author_bio_pages]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_author_bio_pages", $setting, $object );
	}
	
	
	function templatic_customize_supreme_global_layout( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[supreme_global_layout]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_global_layout", $setting, $object );
	}
	
	
	function templatic_customize_supreme_show_breadcrumb( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[supreme_show_breadcrumb]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_show_breadcrumb", $setting, $object );
	}
	
	
	function templatic_customize_supreme_global_contactus_captcha( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[supreme_global_contactus_captcha]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_global_contactus_captcha", $setting, $object );
	}
	
	function templatic_customize_enable_inquiry_form( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[enable_inquiry_form]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_enable_inquiry_form", $setting, $object );
	}

	if($support_woocommerce){
		function templatic_customize_supreme_woocommerce_layout( $setting, $object ) {
			global $support_woocommerce;
			/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
			if ( supreme_prefix()."_theme_settings[supreme_woocommerce_layout]" == $object->id && !current_user_can( 'unfiltered_html' )  )
				$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
			/* Return the sanitized setting and apply filters. */
			return apply_filters( "templatic_customize_supreme_woocommerce_layout", $setting, $object );
		}
	}
	
	function templatic_customize_post_type_label( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[post_type_label]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_post_type_label", $setting, $object );
	}

	function templatic_google_analytics_code( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[supreme_gogle_analytics_code]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_google_analytics_code", $setting, $object );
	}
	
	function templatic_customize_supreme_color1( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[color_picker_color1]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_color1", $setting, $object );
	}
	
	function templatic_customize_supreme_color2( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[color_picker_color2]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_color2", $setting, $object );
	}
	
	function templatic_customize_supreme_color3( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[color_picker_color3]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_color3", $setting, $object );
	}
	
	function templatic_customize_supreme_color4( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[color_picker_color4]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_color4", $setting, $object );
	}
	
	function templatic_customize_supreme_color5( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[color_picker_color5]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_color5", $setting, $object );
	}
	
	
	//TEXTURE SETTINGS START.
	function templatic_customize_templatic_texture1( $setting, $object ) {
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[templatic_texture1]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_templatic_texture1", $setting, $object );
	}
	
	function templatic_customize_alternate_of_texture( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[alternate_of_texture]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_alternate_of_texture", $setting, $object );
	}
	//TEXTURE SETTINGS FINISH.
	
	//BACKGROUND HEADER IMAGE FUNCTION START
	function templatic_customize_header_image_display( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[header_image_display]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_header_image_display", $setting, $object );
	}
	//BACKGROUND HEADER IMAGE FUNCTION END
	
	//Display header text FUNCTION START
	function templatic_customize_display_header_text( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[display_header_text]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_display_header_text", $setting, $object );
	}
	//Display header text FUNCTION END
	
	//Display publish date FUNCTION START
	function templatic_customize_display_publish_date( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[display_publish_date]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_display_publish_date", $setting, $object );
	}
	//Display publish date FUNCTION END
	
	//Display author name FUNCTION START
	function templatic_customize_display_author_name( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[display_author_name]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_display_author_name", $setting, $object );
	}
	//Display author name FUNCTION END
	
	//Display response FUNCTION START
	function templatic_customize_display_post_response( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[display_post_response]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_display_post_response", $setting, $object );
	}
	//Display response FUNCTION END
	
	//Display terms of post FUNCTION START
	function templatic_customize_display_post_terms( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[display_post_terms]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_display_post_terms", $setting, $object );
	}
	//Display terms of post FUNCTION END
	
	
	//Display view counter FUNCTION START
	function templatic_customize_enable_view_counter( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[enable_view_counter]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_enable_view_counter", $setting, $object );
	}
	//Display view counter FUNCTION END
	
	//Display facebook share button FUNCTION START
	function templatic_customize_facebook_share_detail_page( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[facebook_share_detail_page]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_facebook_share_detail_page", $setting, $object );
	}
	//Display facebook share FUNCTION END
	
	//Display google share button FUNCTION START
	function templatic_customize_google_share_detail_page( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[google_share_detail_page]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_google_share_detail_page", $setting, $object );
	}
	//Display google share FUNCTION END
	
	//Display twitter share button FUNCTION START
	function templatic_customize_twitter_share_detail_page( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[twitter_share_detail_page]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_twitter_share_detail_page", $setting, $object );
	}
	//Display twitter share FUNCTION END
	
	//Display pintrest share button FUNCTION START
	function templatic_customize_pintrest_detail_page( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[pintrest_detail_page]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_pintrest_detail_page", $setting, $object );
	}
	//Display pintrest share FUNCTION END
	
/*  Handles changing settings for the live preview of the theme END.  */	


/**
 * Loads framework-specific customize control classes.  Customize control classes extend the WordPress 
 * WP_Customize_Control class to create unique classes that can be used within the framework.
 */
function supreme_customize_controls() {
	 /*
	 * Custom label customize control class.
	 */
	if(class_exists('WP_Customize_Control')){
		class supreme_custom_lable_control extends WP_Customize_Control{
			  public function render_content(){
	?>
				<label>
					<span><?php echo esc_html( $this->label ); ?></span>
				</label>
	<?php
			 }
		}
	}
	/**
	 * Textarea customize control class.
	 */
	if(class_exists('WP_Customize_Control')){
		class Hybrid_Customize_Control_Textarea extends WP_Customize_Control {

			public $type = 'textarea';

			public function __construct( $manager, $id, $args = array() ) {

				parent::__construct( $manager, $id, $args );
			}

			public function render_content() { ?>
				<label>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<div class="customize-control-content">
						<textarea cols="40" rows="5" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
					</div>
				</label>
			<?php }
		}
	}
	
	
	
	
	//CREATE CUSTOM TEXTURE CONTROL START.
	if(!class_exists('WP_Image_Control')){
		class WP_Image_Control extends WP_Customize_Control{
			public function render_content(){
				$name = '_customize-radio-' . $this->id;?>
                	<style type="text/css">
                    	.texture_wrap {
							margin-left: -5px;
							}
							
						.texture_wrap label {
							display: inline-block;
							*display: inline;
							zoom: 1;
							vertical-align: top;
							position: relative;
							width: 32px;
							height: 32px;
							border: 1px solid #ccc;
							color: #fff;
							margin: 0 0 7px 4px;
							}
							
						.texture_wrap label input[type='radio']{
							position: absolute;
							visibility: hidden;
							}
                    </style>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<div class="texture_wrap">
						<label>
							<input type="radio" value="" name="templatic_texture" <?php $this->link(); checked( $this->value(), '' ); ?> />
							<span id="texture1"><?php _e('None',THEME_DOMAIN);?></span>
						</label>
						<label>
							<input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture2.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture2.png' ); ?> />
							<span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture2.png'; ?>" alt="" /></span>
						</label>
						<label>
							<input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture3.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture3.png' ); ?> />
							<span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture3.png'; ?>" alt="" /></span>
						</label>
						<label>
							<input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture4.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture4.png' ); ?> />
							<span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture4.png'; ?>" alt="" /></span>
						</label>
						<label>
							<input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture5.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture5.png' ); ?> />
							<span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture5.png'; ?>" alt="" /></span>
						</label>
						<label>
							<input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture6.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture6.png' ); ?> />
							<span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture6.png'; ?>" alt="" /></span>
						</label>
						<label>
							<input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture7.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture7.png' ); ?> />
							<span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture7.png'; ?>" alt="" /></span>
						</label>
						<label>
							<input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture8.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture8.png' ); ?> />
							<span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture8.png'; ?>" alt="" /></span>
						</label>
						<label>
							<input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture9.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture9.png' ); ?> />
							<span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture9.png'; ?>" alt="" /></span>
						</label>
						<label>
							<input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture10.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture10.png' ); ?> />
							<span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture10.png'; ?>" alt="" /></span>
						</label>
						<label>
							<input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture11.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture11.png' ); ?> />
							<span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture11.png'; ?>" alt="" /></span>
						</label>
						<label>
							<input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture12.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture12.png' ); ?> />
							<span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture12.png'; ?>" alt="" /></span>
						</label>
						<label>
							<input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture13.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture13.png' ); ?> />
							<span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture13.png'; ?>" alt="" /></span>
						</label>
						<label>
							<input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture14.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture14.png' ); ?> />
							<span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture14.png'; ?>" alt="" /></span>
						</label>
						<label>
							<input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture15.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture15.png' ); ?> />
							<span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture15.png'; ?>" alt="" /></span>
						</label>
						<label>
							<input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture16.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture16.png' ); ?> />
							<span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture16.png'; ?>" alt="" /></span>
						</label>
                        <label>
                            <input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture17.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture17.png' ); ?> />
                            <span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture17.png'; ?>" alt="" /></span>
                        </label>
                    </div>
			<?php
			}
		}
	}
	//CREATE CUSTOM TEXTURE CONTROL FINISH.
	
	
	
}

add_action( 'admin_bar_init', 'supreme_admin_bar_init' );


	function supreme_admin_bar_init(){
	//if ( ! is_super_admin() || ! is_admin_bar_showing() || $this->is_wp_login() )
	//		return;

		add_action( 'admin_bar_menu','supreme_admin_bar_menu', 1001 );

	}

	function supreme_admin_bar_menu() {
		global $wp_admin_bar;

		$classes = apply_filters( 'debug_bar_classes', array() );
		$classes = implode( " ", $classes );

		/* Add the main siteadmin menu item */
		$wp_admin_bar->add_menu( array(
			'id'     => 'supreme-customize',
			'href'   => admin_url( 'customize.php' ),
			'parent' => 'top-secondary',
			'title'  => apply_filters( 'supreme_bar_title', __('Customization', 'debug-bar') ),
			'meta'   => array( 'class' => $classes ),
		) );


	}


/*
Name :get_header_image_location
Description : to display header image
*/
if(!function_exists('get_header_image_location')){
	function get_header_image_location(){
		$theme_name = get_option('stylesheet');
		$theme_settings = get_option(supreme_prefix().'_theme_settings');
		if(!empty($theme_settings)){
			if(isset($theme_settings['header_image_display']) && @$theme_settings['header_image_display']!="" && @$theme_settings['header_image_display'] == 'before_nav'){
				return 0;
			}elseif(isset($theme_settings['header_image_display']) && @$theme_settings['header_image_display']!="" && @$theme_settings['header_image_display'] == 'after_nav'){
				return 1;
			}
		}
	}
}
?>