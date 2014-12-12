<?php
/*
 * supreme Framework Version
 */
function supreme_version_init () {
    $suprem_framework_version = '2.0.4';
    if ( get_option( 'suprem_framework_version' ) != $suprem_framework_version ) {
    		update_option( 'suprem_framework_version', $suprem_framework_version );
    }
}

add_action( 'init', 'supreme_version_init', 10 );
add_action('admin_menu','supreme_templatic_menu');
add_action('admin_menu','remove_supreme_templatic_menu');
/*
 * Supreme framework update menu
 */
 
function supreme_templatic_menu(){

	add_menu_page('Templatic', 'Templatic', 'administrator', 'templatic_menu', 'tmpl_framework_update', '',111); 
	
	add_submenu_page( 'templatic_menu', 'Theme Update','Theme Update', 'administrator', 'tmpl_theme_update', 'tmpl_theme_update',27 );
	
	add_submenu_page( 'templatic_menu', 'Framework Update','Framework Update', 'administrator', 'tmpl_framework_update', 'tmpl_framework_update',28 );
	
	add_submenu_page( 'templatic_menu', 'Get Support' ,'Get Support' , 'administrator', 'tmpl_support_theme', 'tmpl_support_theme',29 );
	
	add_submenu_page( 'templatic_menu', 'Purchase theme','Purchase theme', 'administrator', 'tmpl_purchase_theme', 'tmpl_purchase_theme',30 );
	
	
}
function remove_supreme_templatic_menu(){
	remove_submenu_page('templatic_menu','templatic_menu');
}

if(!function_exists('tmpl_purchase_theme')){
function tmpl_purchase_theme(){
 echo "here";
	wp_redirect('http://templatic.com/wordpress-themes-store/'); exit;
}}
/* frame work update templatic menu*/
function tmpl_support_theme(){
	echo "<h3>Need Help?</h3>";
	echo "<p>Here's how you can get help from templatic on any thing you need with regarding this theme. </p>";
	echo "<br/>";
	echo '<p><a href="http://templatic.com/docs/theme-guides/">'."Take a look at theme guide".'</a></p>';
	echo '<p><a href="http://templatic.com/docs/" target="blank">'."Knowlegebase".'</a></p>';
	echo '<p><a href="http://templatic.com/forums/" target="blank">'."Explore our community forums".'</a></p>';
	echo '<p><a href="http://templatic.com/helpdesk/" target="blank">'."Create a support ticket in Helpdesk".'</a></p>';
}



/* frame work update templatic menu*/
function tmpl_theme_update(){
	
	require_once(TEMPLATE_DIR."library/templatic_login.php");
}

/**/
function tmpl_framework_update()
{
	$flibrary='library';
	$theme_data = wp_get_theme($flibrary);
	$framework_version=get_option( 'suprem_framework_version' );
	$request_args = array(   	
		    'slug' => $flibrary,
    		    'version' => $framework_version
    		);
	$request_string = framework_prepare_request( 'templatic_theme_update', $request_args );
	$raw_response = wp_remote_post( 'http://templatic.com/updates/api/', $request_string );
	if( !is_wp_error($raw_response) && ($raw_response['response']['code'] == 200) )
		$response = json_decode($raw_response['body']);		
	
	$response = (array)$response;
	$supreme_version = $framework_version;
	$remote_version = $response['new_version'];	
	
	?>
     <div class="wrap themes-page">
     	<h2><?php _e('Framework Update',THEME_DOMAIN);?></h2>
          <?php
		if (version_compare($supreme_version, $remote_version, '<') && $supreme_version!='')
		{
			update_option('supreme_framework_response',$response);
			?>
               <form action="" method="post" enctype="multipart/form-data">
                    <h3><?php _e('A new version of Framework is available.',THEME_DOMAIN);?></h3>
                    <p><?php _e('This updater will download and extract the latest Framework files to your current themes functions folder.',THEME_DOMAIN);?> </p>
                    <p><?php _e('We recommend backing up your theme files and updating WordPress to latest version before proceeding.',THEME_DOMAIN);?></p>
                    <p>&rarr; <strong>Your version:</strong> <?php echo $framework_version; ?></p>
                    
                    <p>&rarr; <strong>Current Version:</strong> <?php echo $remote_version; ?></p>
                    <input type="submit" name="spreme_update" value="Update Framework" />
                    <input type="hidden" name="supreme_update_save" value="save" />
                    <input type="hidden" name="remote_version" value="<?php echo $remote_version;?>" />
                    <input type="hidden" name="supreme_ftp_cred" value="<?php echo esc_attr( base64_encode(serialize($_POST))); ?>" />
               </form>    
               <?php
		}else{
			echo '<h3>'.__('You have the latest version of Framework',THEME_DOMAIN).'</h3>';
               echo '<p>&rarr;'.sprintf(__('<strong>Your version:</strong> %s',THEME_DOMAIN),$framework_version).'</p>';
		}
		?>
     </div>
     <?php
}

/*
 * Function Name: framework_prepare_request
 * Return: check framework request argument
 */
function framework_prepare_request( $action, $args ) {
	global $wp_version;
	
	return array(
				'body' => array(
				'action' => $action, 
				'request' => serialize($args),
				'api-key' => md5(home_url())
				),
				'user-agent' => 'WordPress/'. $wp_version .'; '. home_url()
		);	
}


/*
 * Function Name: supreme_framework_update_head
 * Return: update new framwork and also display the message notice for which action do it happen.
 */
function supreme_framework_update_head()
{
	if( isset( $_REQUEST['page'] ) ) {
	// Sanitize page being requested.
	$_page = esc_attr( $_REQUEST['page'] );

	if( ($_page == 'templatic_menu' || $_page=='tmpl_framework_update')  && @$_REQUEST['spreme_update']=='Update Framework') {
		//Setup Filesystem
		$method = get_filesystem_method();

		if( isset( $_POST['supreme_ftp_cred'] ) ) {
			$cred = unserialize( base64_decode( $_POST['supreme_ftp_cred'] ) );
			$filesystem = WP_Filesystem($cred);
		} else {
		   $filesystem = WP_Filesystem();
		}
		
		if( $filesystem == false && $_POST['upgrade'] != 'Proceed' ) {

			function supreme_framework_update_filesystem_warning() {
					$method = get_filesystem_method();
					echo "<div id='filesystem-warning' class='updated fade'><p>Failed: Filesystem preventing downloads. ( ". $method .")</p></div>";
				}
				add_action( 'admin_notices', 'supreme_framework_update_filesystem_warning' );
				return;
		}
		if(isset($_REQUEST['supreme_update_save'])){

			// Sanitize action being requested.
			$_action = esc_attr( $_REQUEST['supreme_update_save'] );

		if( $_action == 'save' ) {

		$temp_file_addr = download_url( esc_url( 'http://www.templatic.com/updates/library.zip' ) );		
		if ( is_wp_error($temp_file_addr) ) {

			$error = esc_html( $temp_file_addr->get_error_code() );

			if( $error == 'http_no_url' ) {
			//The source file was not found or is invalid
				function supreme_framework_update_missing_source_warning() {
					echo "<div id='source-warning' class='updated fade'><p>Failed: Invalid URL Provided</p></div>";
				}
				add_action( 'admin_notices', 'supreme_framework_update_missing_source_warning' );
			} else {
				function supreme_framework_update_other_upload_warning() {
					echo "<div id='source-warning' class='updated fade'><p>Failed: Upload - $error</p></div>";
				}
				add_action( 'admin_notices', 'supreme_framework_update_other_upload_warning' );

			}

			return;

		  }
		//Unzip it
		global $wp_filesystem;
		$to = $wp_filesystem->wp_content_dir() . "/themes/" . get_option( 'template' ) ;		
		
		$dounzip = unzip_file($temp_file_addr, $to);

		unlink($temp_file_addr); // Delete Temp File

		if ( is_wp_error($dounzip) ) {

			//DEBUG
			$error = esc_html( $dounzip->get_error_code() );
			$data = $dounzip->get_error_data($error);
			//echo $error. ' - ';
			//print_r($data);

			if($error == 'incompatible_archive') {
				//The source file was not found or is invalid
				function supreme_framework_update_no_archive_warning() {
					echo "<div id='woo-no-archive-warning' class='updated fade'><p>Failed: Incompatible archive</p></div>";
				}
				add_action( 'admin_notices', 'supreme_framework_update_no_archive_warning' );
			}
			if($error == 'empty_archive') {
				function supreme_framework_update_empty_archive_warning() {
					echo "<div id='woo-empty-archive-warning' class='updated fade'><p>Failed: Empty Archive</p></div>";
				}
				add_action( 'admin_notices', 'supreme_framework_update_empty_archive_warning' );
			}
			if($error == 'mkdir_failed') {
				function supreme_framework_update_mkdir_warning() {
					echo "<div id='woo-mkdir-warning' class='updated fade'><p>Failed: mkdir Failure</p></div>";
				}
				add_action( 'admin_notices', 'supreme_framework_update_mkdir_warning' );
			}
			if($error == 'copy_failed') {
				function supreme_framework_update_copy_fail_warning() {
					echo "<div id='woo-copy-fail-warning' class='updated fade'><p>Failed: Copy Failed</p></div>";
				}
				add_action( 'admin_notices', 'supreme_framework_update_copy_fail_warning' );
			}

			return;

		}

		function supreme_framework_updated_success() {
			echo "<div id='framework-upgraded' class='updated fade'><p>New framework successfully downloaded, extracted and updated.</p></div>";
			update_option( 'suprem_framework_version', $_POST['remote_version']);
		}
		
		add_action( 'admin_notices', 'supreme_framework_updated_success' );

		}
	}
	} //End user input save part of the update
 }	
}
add_action( 'admin_head', 'supreme_framework_update_head' );

/*
 * Function Name: supreme_framework_update_notice
 * Return: display the framework update message
 */

function supreme_framework_update_notice()
{	
	$framework_version=get_option( 'suprem_framework_version' );
	$supreme_framework_response=get_option('supreme_framework_response');	
	if(empty($supreme_framework_response) || $supreme_framework_response=='')
		return false;
	if (version_compare($framework_version, $supreme_framework_response['new_version'], '<') && $framework_version!='' && (@$_REQUEST['page'] != 'tmpl_framework_update')){
		echo '<div id="update-nag">';
		$framework_changelog = esc_url( add_query_arg( array( 'slug' => 'framework_changelog', 'action' => 'framework_changelog' , '_ajax_nonce' => wp_create_nonce( 'framework_changelog' ), 'TB_iframe' => true ,'width'=>500,'height'=>400), admin_url( 'admin-ajax.php' ) ) );
		
		$new_version .= ' <a class="thickbox" title="Framework" href="'.$framework_changelog.'">'. sprintf(__('View version %s Details', 'Framework'), $supreme_framework_response['new_version']) . '</a>. or ' ;
		echo "A new version of Framework is available. {$new_version} update framework from <a href='".site_url()."/wp-admin/admin.php?page=tmpl_framework_update'>Framework Update</a> menu";			 
		echo '</div>';	;	
	}
}
add_action('admin_notices','supreme_framework_update_notice');

add_action('wp_ajax_framework_changelog','display_framework_changelog');

function display_framework_changelog(){

	$options = array('method' => 'POST', 'timeout' => 3, 'body' => $body);
	$options['headers'] = array(
	  'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option('blog_charset'),
	  'Content-Length' => strlen($body),
	  'User-Agent' => 'WordPress/' . get_bloginfo("version"),
	  'Referer' => get_bloginfo("url")
	);
	
	$raw_response = wp_remote_request('http://templatic.com/updates/change_log.txt', $options);	
	if ( is_wp_error( $raw_response ) || 200 != $raw_response['response']['code']){
	  $page_text = __("Oops!! Something went wrong.<br/>Please try again or <a href='http://www.templatic.com'>contact us</a>.",THEME_DOMAIN);
	}
	else{
	  $page_text = $raw_response['body'];
	
	}
	echo  stripslashes($page_text);
	exit;
}
?>