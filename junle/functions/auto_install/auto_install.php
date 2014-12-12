<?php
set_time_limit(0);
global  $wpdb,$pagenow;

add_action("admin_head", "star_autoinstall"); // please comment this line if you wish to DEACTIVE SAMPLE DATA INSERT.
add_action('admin_head','add_css_to_admin');
function add_css_to_admin(){
	echo '<style type="text/css">
		#message1{
			display:none;
		}
	</style>';
}
function activate_eco_addons(){
	$url_custom_field = home_url()."/wp-admin/admin.php?page=templatic_system_menu&activated=custom_fields_templates&true=1";
	$url_custom_post_type = home_url()."/wp-admin/admin.php?page=templatic_system_menu&activated=custom_taxonomy&true=1";
	add_css_to_admin();
?>
		
	<div class="error" style="padding:10px 0 10px 10px;font-weight:bold;">
		<span>
			<?php _e('Thanks for choosing templatic themes,  the base system of templatic is not installed at your side Now, Please activate both <a id="templatic_plugin" href="'.$url_custom_post_type.'" style="color:#21759B">Templatic - Custom Post Types Manager</a> and <a  href="'.$url_custom_field.'" style="color:#21759B">Templatic - Custom Fields</a> addons to start your journey of <span style="color:#000">'.get_current_theme().'</span>',"templatic");?>
		</span>
	</div>
	
<?php 
	}
//require_once (TEMPLATEPATH . '/delete_data.php');
function star_autoinstall()
{
	global $wpdb;
	$wp_user_roles_arr = get_option($wpdb->prefix.'user_roles');
	global $wpdb;
	if((strstr($_SERVER['REQUEST_URI'],'themes.php') && !isset($_REQUEST['page'])) && @$_REQUEST['template']=='' || (isset($_REQUEST['page']) && $_REQUEST['page']=="templatic_system_menu") ){
		if(class_exists('Supreme')){
			$prefix = supreme_prefix();
		}else{
			$prefix = sanitize_key( apply_filters( 'hybrid_prefix', get_template() ) );
		}	
			$theme_options = get_option($prefix.'_theme_settings');
		
		$theme_options[ 'display_header_text' ] = 1;
		update_option($prefix.'_theme_settings',$theme_options);
		$post_counts = $wpdb->get_var("select count(post_id) from $wpdb->postmeta,$wpdb->posts where (meta_key='pt_dummy_content' || meta_key='tl_dummy_content') and meta_value=1 and $wpdb->posts.ID = $wpdb->postmeta.post_id and $wpdb->posts.post_type='post'");
		if($post_counts>0){
			$theme_name = get_option('stylesheet');
			$nav_menu = get_option('theme_mods_'.strtolower($theme_name));
			if(!isset($nav_menu['nav_menu_locations']['secondary']) && $nav_menu['nav_menu_locations']['secondary'] == 0){
				$menu_msg = "<p><b>NAVIGATION MENU:</b> <a href='".site_url("/wp-admin/nav-menus.php")."'><b>Setup your Menu here</b></a>  | <b>CUSTOMIZE:</b> <a href='".site_url("/wp-admin/customize.php")."'><b>Customize your Theme Options.</b></a><br/> <b>HELP:</b> <a href='http://templatic.com/docs/5-star-guide/'> <b>Theme Documentation Guide</b></a> | <b>SUPPORT:</b><a href='http://templatic.com/forums'> <b>Community Forum</b></a></p>";
			}else{
				$menu_msg="<p><b>CUSTOMIZE:</b> <a href='".site_url("/wp-admin/customize.php")."'><b>Customize your Theme Options.</b></a><br/> <b>HELP:</b> <a href='http://templatic.com/docs/5-star-guide/'> <b>Theme Documentation Guide</b></a> | <b>SUPPORT:</b><a href='http://templatic.com/forums'> <b>Community Forum</b></a></p>";
			}			
			$dummy_data_msg = '<b>1-click install</b> <a class="button_delete button-primary" href="'.home_url().'/wp-admin/themes.php?dummy=del">Delete Sample Data?</a><p>Dummy data is enabled on your site</p>'.$menu_msg;
		}else{
			$theme_name = get_option('stylesheet');
			$nav_menu = get_option('theme_mods_'.strtolower($theme_name));
			if(@$nav_menu['nav_menu_locations']['secondary'] == 0){
				$menu_msg1 = "<p><b>NAVIGATION MENU:</b> <a href='".site_url("/wp-admin/nav-menus.php")."'><b>Setup your Menu here</b></a>  | <b>CUSTOMIZE:</b> <a href='".site_url("/wp-admin/customize.php")."'><b>Customize your Theme Options.</b></a><br/> <b>HELP:</b> <a href='http://templatic.com/docs/5-star-guide/'> <b>Theme Documentation Guide</b></a> | <b>SUPPORT:</b><a href='http://templatic.com/forums'> <b>Community Forum</b></a></p>";
			}else{
				$menu_msg1="<p><b>CUSTOMIZE:</b> <a href='".site_url("/wp-admin/customize.php")."'><b>Customize your Theme Options.</b></a><br/> <b>HELP:</b> <a href='http://templatic.com/docs'> <b>Theme Documentation Guide</b></a> | <b>SUPPORT:</b><a href='http://templatic.com/forums'> <b>Community Forum</b></a></p>";
			}
			$dummy_data_msg = '<b>1 click-install</b>  <a class="button_insert button-primary" href="'.home_url().'/wp-admin/themes.php?dummy_insert=1">Insert Sample Data</a><p>1 click-install allows you to quickly populate your site with sample content such as page, posts, etc.</p>'.$menu_msg1;
		}
		
		if(isset($_REQUEST['dummy_insert']) && $_REQUEST['dummy_insert']){
			require_once (get_template_directory().'/functions/auto_install/auto_install_data.php');
			wp_redirect(admin_url().'themes.php');
		}
		if(isset($_REQUEST['dummy']) && $_REQUEST['dummy']=='del'){
			supreme_delete_dummy_data();
			wp_redirect(admin_url().'themes.php');
		}
		
		define('THEME_ACTIVE_MESSAGE','<div id="ajax-notification" class="updated templatic_autoinstall"><p> '.$dummy_data_msg.'</p><span id="ajax-notification-nonce" class="hidden">' . wp_create_nonce( 'ajax-notification-nonce' ) . '</span><a href="javascript:;" id="dismiss-ajax-notification" class="templatic-dismiss" style="float:right">Dismiss</a></div>');
		echo THEME_ACTIVE_MESSAGE;
	}
}
function supreme_delete_dummy_data()
{
	global $wpdb;
	delete_option('sidebars_widgets'); //delete widgets
	$productArray = array();
	$pids_sql = "select p.ID from $wpdb->posts p join $wpdb->postmeta pm on pm.post_id=p.ID where (meta_key='pt_dummy_content' || meta_key='tl_dummy_content' || meta_key='auto_install') and (meta_value=1 || meta_value='auto_install')";
	$pids_info = $wpdb->get_results($pids_sql);
	foreach($pids_info as $pids_info_obj)
	{
		wp_delete_post($pids_info_obj->ID,true);
	}
}
/* Setting For dismiss auto install notification message from themes.php START */
register_activation_hook( __FILE__, 'activate'  );
register_deactivation_hook( __FILE__, 'deactivate'  );
add_action( 'admin_enqueue_scripts', 'register_admin_scripts'  );
add_action( 'wp_ajax_hide_admin_notification', 'hide_admin_notification' );
function activate() {
	add_option( 'hide_ajax_notification', false );
}
function deactivate() {
	delete_option( 'hide_ajax_notification' );
}
function register_admin_scripts() {
	wp_register_script( 'ajax-notification-admin', get_template_directory_uri().'/js/_admin-install.js'  );
	wp_enqueue_script( 'ajax-notification-admin' );
}
function hide_admin_notification() {
	if( wp_verify_nonce( $_REQUEST['nonce'], 'ajax-notification-nonce' ) ) {
		if( update_option( 'hide_ajax_notification', true ) ) {
			die( '1' );
		} else {
			die( '0' );
		}
	}
}
/* Setting For dismiss auto install notification message from themes.php END */

/*
Name : set_page_info_autorun
Description : update pages in autorun
*/
function set_page_info_autorun($pages_array,$page_info_arr)
{
	global $wpdb,$current_user;
	for($i=0;$i<count($page_info_arr);$i++)
	{ 
		$post_title = $page_info_arr[$i]['post_title'];
		$post_count = $wpdb->get_var("SELECT count(ID) FROM $wpdb->posts where post_title like \"$post_title\" and post_type='page' and post_status in ('publish','draft')");
		if(!$post_count)
		{
			$post_info_arr = array();
			$catids_arr = array();
			$my_post = array();
			$post_info_arr = $page_info_arr[$i];
			$my_post['post_title'] = $post_info_arr['post_title'];
			$my_post['post_content'] = $post_info_arr['post_content'];
			$my_post['post_type'] = 'page';
			if(@$post_info_arr['post_author'])
			{
				$my_post['post_author'] = $post_info_arr['post_author'];
			}else
			{
				$my_post['post_author'] = 1;
			}
			$my_post['post_status'] = 'publish';
	
			$last_postid = wp_insert_post( $my_post );

			$post_meta = $post_info_arr['post_meta'];
			if($post_meta)
			{
				foreach($post_meta as $mkey=>$mval)
				{
					update_post_meta($last_postid, $mkey, $mval);
				}
			}
			
			$post_image = $post_info_arr['post_image'];
			if($post_image)
			{
				for($m=0;$m<count($post_image);$m++)
				{
					$menu_order = $m+1;
					$image_name_arr = explode('/',$post_image[$m]);
					$img_name = $image_name_arr[count($image_name_arr)-1];
					$img_name_arr = explode('.',$img_name);
					$post_img = array();
					$post_img['post_title'] = $img_name_arr[0];
					$post_img['post_status'] = 'attachment';
					$post_img['post_parent'] = $last_postid;
					$post_img['post_type'] = 'attachment';
					$post_img['post_mime_type'] = 'image/jpeg';
					$post_img['menu_order'] = $menu_order;
					$last_postimage_id = wp_insert_post( $post_img );
					update_post_meta($last_postimage_id, '_wp_attached_file', $post_image[$m]);					
					$post_attach_arr = array(
										"width"	=>	580,
										"height" =>	480,
										"hwstring_small"=> "height='150' width='150'",
										"file"	=> $post_image[$m],
										//"sizes"=> $sizes_info_array,
										);
					wp_update_attachment_metadata( $last_postimage_id, $post_attach_arr );
				}
			}
		}
	}
}
?>