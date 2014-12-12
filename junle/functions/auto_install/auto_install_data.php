<?php 
global $upload_folder_path,$wpdb,$blog_id;
global $wp_rewrite;
$wp_rewrite->set_permalink_structure( '/%postname%/' );
if(get_option('upload_path') && !strstr(get_option('upload_path'),'wp-content/uploads')){
	$upload_folder_path = "wp-content/blogs.dir/$blog_id/files/";
}else{
	$upload_folder_path = "wp-content/uploads/";
}
global $blog_id;

if($blog_id){ $thumb_url = "&amp;bid=$blog_id";}
$folderpath = $upload_folder_path . "dummy/";
$strpost = strpos(get_template_directory(),'wp-content');
$dirinfo = wp_upload_dir();
$target =$dirinfo['basedir']."/dummy"; 
full_copy( get_template_directory()."/images/dummy/", $target );
function full_copy( $source, $target ) {
	global $upload_folder_path;
	$imagepatharr = explode('/',$upload_folder_path."dummy");
	$year_path = ABSPATH;
	for($i=0;$i<count($imagepatharr);$i++){
	  if($imagepatharr[$i]) {
		  $year_path .= $imagepatharr[$i]."/";
		  //echo "<br />";
		  if (!file_exists($year_path)){
			  @mkdir($year_path, 0777);
		  }     
		}
	}
	@mkdir( $target );
		$d = dir( $source );
		
	if ( is_dir( $source ) ) {
		@mkdir( $target );
		$d = dir( $source );
		while ( FALSE !== ( $entry = $d->read() ) ) {
			if ( $entry == '.' || $entry == '..' ) {
				continue;
			}
			$Entry = $source . '/' . $entry; 
			if ( is_dir( $Entry ) ) {
				full_copy( $Entry, $target . '/' . $entry );
				continue;
			}
			copy( $Entry, $target . '/' . $entry );
		}
		$d->close();
	}else {
		copy( $source, $target );
	}
}
$a = get_option(supreme_prefix().'_theme_settings');
$b = array(
		'supreme_logo_url' 					=> get_template_directory_uri()."/images/logo.png",
		'supreme_site_description'			=> 1,
		'supreme_display_image'				=> 1,
		'display_author_name'				=> 1,
		'display_publish_date'				=> 1,
		'display_post_terms'				=> 1,
		'supreme_display_noimage'			=> 1,
		'supreme_archive_display_excerpt'	=> 1,
		'templatic_excerpt_length'			=> 27,
		'display_header_text'				=> 1,
		'supreme_show_breadcrumb'			=> 1,
		'footer_insert' 					=> '<p class="copyright">&copy; '.date('Y').' <a href="'.home_url().'">'.get_option('blogname').'</a>. '. __("All Rights Reserved.",THEME_DOMAIN).' </p>
		<p class="credit">'.__("Designed by",THEME_DOMAIN).' <a href="http://templatic.com" title="wordpress themes"><img src="'.get_template_directory_uri().'/library/images/templatic-wordpress-themes.png" alt="wordpress themes"></a></p>' ,
		'enable_comments_on_page' 			=> 0,
		'enable_comments_on_post' 			=> 1,
		'enable_sticky_header_menu'			=> 1,
		'supreme_author_bio_posts'			=> 0,
		'supreme_author_bio_pages'			=> 0,
		'supreme_global_contactus_captcha'	=> 0,
		'enable_inquiry_form'				=> 1,
		'header_image_display'				=> 'after_nav',
		'enable_view_counter'				=> 0,
		'facebook_share_detail_page'		=> 1,
		'google_share_detail_page'			=> 1,
		'twitter_share_detail_page'			=> 1,
		'pintrest_detail_page'				=> 1,
		'color_picker_color1' 				=> '',
		'color_picker_color2' 				=> '',
		'color_picker_color3' 				=> '',
		'color_picker_color4' 				=> '',
		'color_picker_color5' 				=> '',
	);
update_option(supreme_prefix().'_theme_settings',$b);
update_option('posts_per_page',5);
update_option('show_on_front','page');
$args = array(
			'post_type' => 'page',
			'meta_key' => '_wp_page_template',
			'meta_value' => 'page-templates/front-page.php'
			);
$page_query = new WP_Query($args);
$front_page_id = $page_query->post->ID;
update_option('page_on_front',$front_page_id);


$dummy_image_path = get_template_directory_uri().'/images/dummy/';
$post_info = array();
$category_array = array('Blog','News','Facebook','Google','Mobile','Apple');
insert_taxonomy_category($category_array);
function insert_taxonomy_category($category_array){
	global $wpdb;
	for($i=0;$i<count($category_array);$i++)	{
		$parent_catid = 0;
		if(is_array($category_array[$i]))		{
			$cat_name_arr = $category_array[$i];
			for($j=0;$j<count($cat_name_arr);$j++)			{
				$catname = $cat_name_arr[$j];
				if($j>1){
					$catid = $wpdb->get_var("select term_id from $wpdb->terms where name=\"$catname\"");
					if(!$catid)					{
					$last_catid = wp_insert_term( $catname, 'category' );
					}					
				}else				{
					$catid = $wpdb->get_var("select term_id from $wpdb->terms where name=\"$catname\"");
					if(!$catid)
					{
						$last_catid = wp_insert_term( $catname, 'category');
					}
				}
			}
		}else		{
			$catname = $category_array[$i];
			$catid = $wpdb->get_var("select term_id from $wpdb->terms where name=\"$catname\"");
			if(!$catid)
			{
				wp_insert_term( $catname, 'category');
			}
		}
	}
	for($i=0;$i<count($category_array);$i++)	{
		$parent_catid = 0;
		if(is_array($category_array[$i]))		{
			$cat_name_arr = $category_array[$i];
			for($j=0;$j<count($cat_name_arr);$j++)			{
				$catname = $cat_name_arr[$j];
				if($j>0)				{
					$parentcatname = $cat_name_arr[0];
					$parent_catid = $wpdb->get_var("select term_id from $wpdb->terms where name=\"$parentcatname\"");
					$last_catid = $wpdb->get_var("select term_id from $wpdb->terms where name=\"$catname\"");
					wp_update_term( $last_catid, 'category', $args = array('parent'=>$parent_catid) );
				}
			}
			
		}
	}
}

////post end///
//====================================================================================//
////post start 19///
$image_array = array();
$post_meta = array();
$image_array[] = "dummy/blog1.jpg" ;
$post_meta = array(
				   "templ_seo_page_title" =>'This wordpress theme is Responsive',
				   "templ_seo_page_kw" => '',
				   "tl_dummy_content"	=> '1',
				   "templ_seo_page_desc" => '',
				);
$post_info[] = array(
					"post_title" =>	'This wordpress theme is Responsive',
					"post_content" =>	'<p>This tutorial will show how to install Templatic Wordpress premium themes. Just follow the below steps.</p>
<p><strong>Step 1: Login to Member Area</strong></p>
<ul>
<li>Head over to templatic.com and click on <a href=http://templatic.com/members/>Member Login</a>.</li>,
<li>Enter your User ID and password and click on Login.</li>
<li>Now you will be redirected to your Members Area.</li>
</ul>
<ul>
<li>Click on the theme name and then the download process would start. All the themes which you purchased would be listed here</li>
<li>Save the file to your Computer</li>
</ul>
<p><strong>Step 2: Uploading theme</strong></p>
<ul>
<li>After downloading the file, unzip it and a folder would be created with that theme name</li>
<li>Now we have to upload the theme to wp-content/themes</li>
<li>For this we will be using a FTP client like FileZilla</li>
<li>FileZilla is a free software and it can be downloaded from <a href=http:"//filezilla-project.org/download.php" target="_blank">here</a></li>
<li>Now open FileZilla and your hosting provider must have provided you the FTP details. Insert those details like Host, username, password and port and click on &acute;Quickconnect&acute; or simply press Enter</li>
</ul>
<ul>
<li>Navigate to public_html &gt; wp-content &gt; themes (On the server, which is in the right hand side)</li>
<li>And in the left hand side, which is your computer, navigate to the path where you have downloaded the theme. Simply right click on the theme&acute;s folder and click on &acute;Upload&acute;. That&acute;s it, your theme has been uploaded.</li>
</ul>
<p><strong>Step 3: Activating the theme</strong></p>
<ul>
<li>Now login to your Wordpress Admin area and navigate to Appearance &gt; Themes</li>
<li>Here you would be able to see the theme name and screenshot, just click on Activate.</li>
</ul>
<p>So now your theme is activated and its also populated with some added dummy content to help you get started. If you do not want that dummy content then click on Yes Delete Please!.<br />
So this is how you can install Templatic theme. Now go ahead and customize it according to your needs.2</p>
<p><strong>Still having issues installing the theme? Watch our theme installation video tutorial:</strong><br />
<a href="http://www.youtube.com/watch?v=dFN95RM_jJQ" target="_blank"> http://www.youtube.com/watch?v=dFN95RM_jJQ</a></p>
<p><strong>Step 4: Login to Member Area</strong></p>
<ul>
<li>Head over to templatic.com and click on <a href=http://templatic.com/members/>Member Login</a>.</li>,
<li>Enter your User ID and password and click on Login.</li>
<li>Now you will be redirected to your Members Area.</li>
</ul>
<ul>
<li>Click on the theme name and then the download process would start. All the themes which you purchased would be listed here</li>
<li>Save the file to your Computer</li>
</ul>
<p><strong>Step 5: Uploading theme</strong></p>
<ul>
<li>After downloading the file, unzip it and a folder would be created with that theme name</li>
<li>Now we have to upload the theme to wp-content/themes</li>
<li>For this we will be using a FTP client like FileZilla</li>
<li>FileZilla is a free software and it can be downloaded from <a href=http:"//filezilla-project.org/download.php" target="_blank">here</a></li>
<li>Now open FileZilla and your hosting provider must have provided you the FTP details. Insert those details like Host, username, password and port and click on &acute;Quickconnect&acute; or simply press Enter</li>
</ul>',
					"post_author"	=> $agents_ids_array[array_rand($agents_ids_array)],
					"post_meta" =>	$post_meta,
					"post_image" =>	$image_array,
					"post_category" =>	array('Blog','Mobile'),
					"post_tags" =>	array('Tags','Sample Tags')

					);
////post end///
//====================================================================================//
////post start 20///
$image_array = array();
$post_meta = array();
$image_array[] = "dummy/blog2.jpg" ;
$post_meta = array(
				   "templ_seo_page_title" =>'Automatic Updates ',
				   "templ_seo_page_kw" => '',
"tl_dummy_content"	=> '1',
				   "templ_seo_page_desc" => '',
				);
$post_info[] = array(
					"post_title" =>	'Automatic Updates ',
					"post_content" =>	'You can update the Supreme Wordpress theme automatically with a few clicks ! <h2>Update the Supreme Wordpress theme automatically</h2>
<p>While updating the theme Sometimes you may have got an error like</p>
<p>"Fatal error: Allowed memory size of 34554432 bytes exhausted (tried to  allocate 2348617 bytes) in  /home4/xxxx/public_html/wp-includes/plugin.php on line xxx"</p>
<p>This error occurs because of exceeding the default WordPress memory limit. Also if you have a Blog with a high traffic and have installed lots of plugins, then its advisable to increase this memory limit.</p>
<p>Lets first increase the WordPress memory by editing a file named as "wp-config.php" (This file would be in the root of your WordPress installation)</p>
<p>Open this file and find this:</p>
<p>define(</p>
<p>While updating the theme Sometimes you may have got an error like</p>
<p>"Fatal error: Allowed memory size of 34554432 bytes exhausted (tried to  allocate 2348617 bytes) in  /home4/xxxx/public_html/wp-includes/plugin.php on line xxx"</p>
<p>This error occurs because of exceeding the default WordPress memory limit. Also if you have a Blog with a high traffic and have installed lots of plugins, then its advisable to increase this memory limit.</p>
<p>Lets first increase the WordPress memory by editing a file named as "wp-config.php" (This file would be in the root of your WordPress installation)</p>
<p>Open this file and find this:</p>

<p>While updating the theme Sometimes you may have got an error like</p>
<p>"Fatal error: Allowed memory size of 34554432 bytes exhausted (tried to  allocate 2348617 bytes) in  /home4/xxxx/public_html/wp-includes/plugin.php on line xxx"</p>
<p>This error occurs because of exceeding the default WordPress memory limit. Also if you have a Blog with a high traffic and have installed lots of plugins, then its advisable to increase this memory limit.</p>
<p>Lets first increase the WordPress memory by editing a file named as "wp-config.php" (This file would be in the root of your WordPress installation)</p>
<p>Open this file and find this:</p>
',
					"post_author"	=> $agents_ids_array[array_rand($agents_ids_array)],
					"post_meta" =>	$post_meta,
					"post_image" =>	$image_array,
					"post_category" =>	array('Blog','Mobile'),
					"post_tags" =>	array('Tags','Sample Tags')

					);
////post end///
//====================================================================================//
////post start 21///
$image_array = array();
$post_meta = array();
$image_array[] = "dummy/blog3.jpg" ;
$post_meta = array(
				   "templ_seo_page_title" =>'Widgetized Home page',
				   "templ_seo_page_kw" => '',
"tl_dummy_content"	=> '1',
				   "templ_seo_page_desc" => '',
				);
$post_info[] = array(
					"post_title" =>	'Widgetized Home page',
					"post_content" =>	'The whole home page of Supreme wordpress theme is widget ready !<h4>Widget ready home page!</h4>
WordPress Widgets add content and features to your sidebars. Examples are the default widgets that come with WordPress; for post categories, tag clouds, navigation, search, etc. Plugins will often add their own widgets.

Widgets were originally designed to provide a simple and easy-to-use way of giving design and structure control of the WordPress Theme to the user, which is now available on properly "widgetized" WordPress Themes to include the header, footer, and elsewhere in the WordPress design and structure.

Example of the WordPress Widget Panel

Widgets require no code experience or expertise. They can be added, removed, and rearranged on the WordPress Administration Appearance &gt; Widgets panel. The order and placement is set by the WordPress Theme in the functions.php file.

Some WordPress Widgets offer customization and options such as forms to fill out, includes or excludes of data and information, optional images, and other customization features.

The Widgets SubPanel explains how to use the various Widgets that come delivered with WordPress.

Plugins that come bundled with widgets can be found in the WordPress Plugin Directory .

The Widget menu will only appear of your Theme has active widgetized sidebars. If it does, you can add widgets by:
<ul>
	<li>Go to Appearance &gt; Widgets.</li>
	<li>Choose a Widget and drag it to the sidebar where you wish it to appear. There might be more than one sidebar option, so begin with the first one. Once in place, WordPress automatically updates the Theme.</li>
	<li>Preview the site. You should find that the "default" sidebar elements are now gone and only the new addition is visible.</li>
	<li>Return to the Widgets Panel to continue adding Widgets.</li>
	<li>To arrange the Widgets within the sidebar or Widget area, click and drag it into place.</li>
	<li>To customize the Widget features, click the down arrow in the upper right corner to expand the Widget"s interface.</li>
	<li>To save the Widget"s customization, click Save.</li>
	<li>To remove the Widget, click Remove or Delete.</li>
	<li>If you change WordPress Themes, the Widgets will return to the left side of the page in the Widget Archives or Available Widgets list. You may need to add them again and rearrangement depending upon the Theme"s ability to preserve other Theme"s Widgets.</li>
</ul>
<h4>The whole home page of Supreme wordpress theme is widget ready !</h4>
WordPress Widgets add content and features to your sidebars. Examples are the default widgets that come with WordPress; for post categories, tag clouds, navigation, search, etc. Plugins will often add their own widgets.

Widgets were originally designed to provide a simple and easy-to-use way of giving design and structure control of the WordPress Theme to the user, which is now available on properly "widgetized" WordPress Themes to include the header, footer, and elsewhere in the WordPress design and structure.

Example of the WordPress Widget Panel

Widgets require no code experience or expertise. They can be added, removed, and rearranged on the WordPress Administration Appearance &gt; Widgets panel. The order and placement is set by the WordPress Theme in the functions.php file.

Some WordPress Widgets offer customization and options such as forms to fill out, includes or excludes of data and information, optional images, and other customization features.

The Widgets SubPanel explains how to use the various Widgets that come delivered with WordPress.

Plugins that come bundled with widgets can be found in the WordPress Plugin Directory .

The Widget menu will only appear of your Theme has active widgetized sidebars. If it does, you can add widgets by:
<ul>
	<li>Go to Appearance &gt; Widgets.</li>
	<li>Choose a Widget and drag it to the sidebar where you wish it to appear. There might be more than one sidebar option, so begin with the first one. Once in place, WordPress automatically updates the Theme.</li>
	<li>Preview the site. You should find that the "default" sidebar elements are now gone and only the new addition is visible.</li>
	<li>Return to the Widgets Panel to continue adding Widgets.</li>
	<li>To arrange the Widgets within the sidebar or Widget area, click and drag it into place.</li>
	<li>To customize the Widget features, click the down arrow in the upper right corner to expand the Widget"s interface.</li>
	<li>To save the Widget"s customization, click Save.</li>
	<li>To remove the Widget, click Remove or Delete.</li>
	<li>If you change WordPress Themes, the Widgets will return to the left side of the page in the Widget Archives or Available Widgets list. You may need to add them again and rearrangement depending upon the Theme"s ability to preserve other Theme"s Widgets.</li>
</ul>
',
					"post_author"	=> $agents_ids_array[array_rand($agents_ids_array)],
					"post_meta" =>	$post_meta,
					"post_image" =>	$image_array,
					"post_category" =>	array('Blog','Google'),
					"post_tags" =>	array('Tags','Sample Tags')

					);
////post end///
//====================================================================================//
////post start 22///
$image_array = array();
$post_meta = array();
$image_array[] = "dummy/blog1.jpg" ;
$post_meta = array(
				   "templ_seo_page_title" =>'Use Shortcodes with this theme to make your content look awesome',
				   "templ_seo_page_kw" => '',
"tl_dummy_content"	=> '1',
				   "templ_seo_page_desc" => '',
				);
$post_info[] = array(
					"post_title" =>	'Use Shortcodes and make your content look awesome',
					"post_content" =>	'See all the shortcodes you can use on <a title="shortcodes" href="http://templatic.com/demos/anchor/shortcodes-2/" target="_blank">this </a>page
					<h2>Shortcodes to style your content</h2>
<p>Gathering blessed likeness after firmament after. Us fill place living thing under behold bring. Give tree void gathering stars brought subdue midst also winged air creeping beginning darkness void Itself his <strong>heaven</strong> without. Seas earth itself were. She&quot;d cattle shall itself fly fruitful upon and <strong>his</strong> own, own.</p>
<p>This theme is compatible with Templatic Shortcodes plugin which offers an array of shortcode options which can be used to make your content talk with the site visitor.</p>
<p>Gathering blessed likeness after firmament after. Us fill place living thing under behold bring. Give tree void gathering stars brought subdue midst also winged air creeping beginning darkness void Itself his <strong>heaven</strong> without. Seas earth itself were. She&quot;d cattle shall itself fly fruitful upon and <strong>his</strong> own, own.</p>
<p>This theme is compatible with Templatic Shortcodes plugin which offers an array of shortcode options which can be used to make your content talk with the site visitor.</p>
<p>Gathering blessed likeness after firmament after. Us fill place living thing under behold bring. Give tree void gathering stars brought subdue midst also winged air creeping beginning darkness void Itself his <strong>heaven</strong> without. Seas earth itself were. She&quot;d cattle shall itself fly fruitful upon and <strong>his</strong> own, own.</p>
<p>This theme is compatible with Templatic Shortcodes plugin which offers an array of shortcode options which can be used to make your content talk with the site visitor.</p>
<p>Gathering blessed likeness after firmament after. Us fill place living thing under behold bring. Give tree void gathering stars brought subdue midst also winged air creeping beginning darkness void Itself his <strong>heaven</strong> without. Seas earth itself were. She&quot;d cattle shall itself fly fruitful upon and <strong>his</strong> own, own.</p>
<p>This theme is compatible with Templatic Shortcodes plugin which offers an array of shortcode options which can be used to make your content talk with the site visitor.</p>
<p>This theme is compatible with Templatic Shortcodes plugin which offers an array of shortcode options which can be used to make your content talk with the site visitor.</p>
<p>Gathering blessed likeness after firmament after. Us fill place living thing under behold bring. Give tree void gathering stars brought subdue midst also winged air creeping beginning darkness void Itself his <strong>heaven</strong> without. Seas earth itself were. She&quot;d cattle shall itself fly fruitful upon and <strong>his</strong> own, own.</p>
<p>This theme is compatible with Templatic Shortcodes plugin which offers an array of shortcode options which can be used to make your content talk with the site visitor.</p>
<p>This theme is compatible with Templatic Shortcodes plugin which offers an array of shortcode options which can be used to make your content talk with the site visitor.</p>
<p>Gathering blessed likeness after firmament after. Us fill place living thing under behold bring. Give tree void gathering stars brought subdue midst also winged air creeping beginning darkness void Itself his <strong>heaven</strong> without. Seas earth itself were. She&quot;d cattle shall itself fly fruitful upon and <strong>his</strong> own, own.</p>
<p>This theme is compatible with Templatic Shortcodes plugin which offers an array of shortcode options which can be used to make your content talk with the site visitor.</p>
<p>This theme is compatible with Templatic Shortcodes plugin which offers an array of shortcode options which can be used to make your content talk with the site visitor.</p>
<p>Gathering blessed likeness after firmament after. Us fill place living thing under behold bring. Give tree void gathering stars brought subdue midst also winged air creeping beginning darkness void Itself his <strong>heaven</strong> without. Seas earth itself were. She&quot;d cattle shall itself fly fruitful upon and <strong>his</strong> own, own.</p>
<p>This theme is compatible with Templatic Shortcodes plugin which offers an array of shortcode options which can be used to make your content talk with the site visitor.</p>

',
					"post_author"	=> $agents_ids_array[array_rand($agents_ids_array)],
					"post_meta" =>	$post_meta,
					"post_image" =>	$image_array,
					"post_category" =>	array('Blog','Apple'),
					"post_tags" =>	array('Tags','Sample Tags')

					);











$post_meta = array();
$image_array[] = "dummy/blog1.jpg" ;
$post_meta = array(
				   "templ_seo_page_title" =>'Use MegaMenu Widget with its Plugin',
				   "templ_seo_page_kw" => '',
				   "tl_dummy_content"	=> '1',
				   "templ_seo_page_desc" => '',
				);
$post_info[] = array(
					"post_title" =>	'Use MegaMenu Widget with its Plugin',
					"post_content" =>	'Mega menu is something that enhances your menu bar i.e. navigation bar&acute;s effect visually. It looks and works awesome.<h2>Want to display so many pages and categories in your menu ? We have a MegaMenu for you.</h2>
<blockquote>Hover over <strong>Theme Features</strong> or <strong>Shop</strong> menu item to see the mega menu in action.</blockquote>
If you&acute;re new to WordPress and website management in general you might be confused when someone says something like <em>&acute;Overwrite xxxx.php in your /library/functions folder and xxxx.css in your /skins folder</em>&acute;. What we mean by that is that you need to connect to your site using an FTP client, navigate to the mentioned folder and overwrite (more on how to do this later).
Sometimes we say things like <em>&acute;You need to make changes inside xxxx.php located in your theme root around line 100&acute;</em>. What we mean by that is that you need to connect to your site, navigate to that file, download it to your hard drive, edit it and upload it back. Sounds tricky - but it&acute;s far from it.
<h6>To successfully connect to your server you&acute;ll need 2 things:</h6>
<strong>1.</strong> FTP client
FTP client is a program you will use to connect to your server. We&acute;ll be using FileZilla during this tutorial. You can download it here
<strong>2.</strong> FTP account
Most people get an FTP account from their hosting provider. In case you didn&acute;t receive one or you forgot the information contact your hosting provider or create one yourself. In case you&acute;re using cPanel follow this tutorial. If you&acute;re using Plesk as a server management software following this tutorial
<h6>Connecting to your server</h6>
<strong>1.</strong> Open FileZilla

<strong>2.</strong> Enter your FTP account details in the boxes at the top left corner. In most cases you can leave &acute;Port&acute; empty
<img alt="" src="http://dl.dropbox.com/u/6640365/Temp%20Images/filezilla1.png" width="650px" />

<strong>3.</strong> If you entered everything correctly the panel on the right should populate with some folders and files. The left panel is showing your hard-disk files
<h6>How to find my theme files?</h6>
In the right panel find a public_html folder (or htdocs on Windows servers). Open that folder. If you installed WordPress in the root folder of your site you should see three folders called wp-admin, wp-content and wp-includes. All theme files are located in the wp-content folder; more specifically, the themes folder inside wp-content. A lot of times you&acute;ll hear us say the term theme root. Theme root means you need to navigate to the first/parent folder of your theme. The path will be something like public_html/wp-content/themes/your_theme_name
<h6>How to install a theme?</h6>
Installing themes using FTP is really easy - drag-n-drop easy. Unpack* your theme on your hard drive and navigate to it in the left (hard drive) panel. In the right (server) panel navigate to wp-content/themes. Now just drag your theme folder from the left to the themes folder on the right. When the files finish uploading go to your site Dashboard -&gt; Apparance -&gt; Themes and activate your theme. Piece of cake, right :)

* - make sure you don&acute;t extract the theme in a folder, use &acute;Extract Here&acute; so you don&acute;t create a double folder problem.

<span style="text-align: center;">For optimal viewing experience watch the video in Full Screen and HD quality</span>
<h6>How to update a theme?</h6>
For detailed instructions on updating themes please read the <a href="http://templatic.com/docs/how-to-manage-and-handle-theme-updates/" target="_blank"><strong>following article</strong></a>. For video instructions on updating start watching the video tutorial above around the 4 minute mark.
<h6>How to overwrite a file?</h6>
To overwrite one of the theme files navigate to the folder where the file is located and just drag the file from your left (hard disk) panel to your right panel. When you do that you&acute;ll be prompted to overwrite - click yes.
<h6>How to edit a file?</h6>
In case you want to edit one of the files navigate to it in your right (server) panel, right mouse click on the file and select View/Edit. When you do that the file will open in your default text editor. Edit the file in your text editor and when done hit Save. As soon as you hit Save a popup will appear in FileZilla asking you whether you want that file uploaded back to the server- be sure to click Yes.
<h6>How to change file/folder permissions?</h6>
In the right Filezilla panel (the server panel) right click on a file or folder and choose &acute;File permissions...&acute;. Inside the new window set the permissions either by clicking on the checkboxes or by entering the exact numerical value. Recommended permissions for files are <strong>644</strong>, for folders <strong>755</strong>. Some folders (like the cache folder) often require 777 permissions.
<h6>How can I find line no. 100 if there are no line numbers in Notepad?</h6>
If you plan on doing any serious file editing you&acute;ll need to say goodbye to Notepad. My personal preference is <a href="http://notepad-plus-plus.org/download/v6.1.6.html" target="_blank"><strong>Notepad++</strong></a>. The program is very easy to use, it features line numbers and also code highlighting.
<h6>How to make FileZilla open files in Notepad++</h6>
In case FileZilla doesn&acute;t ask you which editor you want to use to edit php, js and css files go to (in Filezila) Edit -&gt; Settings... -&gt; File editing -&gt; Filetype associations and add Notepad++ as a default editor for your files. This is how that area looks in my FileZilla
<img alt="" src="http://dl.dropbox.com/u/6640365/Temp%20Images/filezilla2.png" width="650px" />

If you followed this guide carefully you should now be able to (easily) navigate, edit and overwrite any file in your theme.
',
					"post_author"	=> $agents_ids_array[array_rand($agents_ids_array)],
					"post_meta" =>	$post_meta,
					"post_image" =>	$image_array,
					"post_category" =>	array('Blog','Mobile'),
					"post_tags" =>	array('Tags','Sample Tags')

					);
////post end///
//====================================================================================//
////post start 20///
$image_array = array();
$post_meta = array();
$image_array[] = "dummy/blog2.jpg" ;
$post_meta = array(
				   "templ_seo_page_title" =>'Theme Features',
				   "templ_seo_page_kw" => '',
"tl_dummy_content"	=> '1',
				   "templ_seo_page_desc" => '',
				);
$post_info[] = array(
					"post_title" =>	'Theme Features',
					"post_content" =>	"<h1>Theme Features</h1>
<div>

<h3>Mobile Friendly Design</h3>
Just as all our recent themes, it is completely <strong>responsive</strong> and will look good on virtually every device. The best way to see it in action is to visit the <a href='http://templatic.com/demos/theme_name' target='_blank'>demo site</a> with your smartphone or to resize your browser window while on the demo site.
<h3>Hassle-free Updates</h3>
With it you wont have to worry about updating the theme, notifications about new updates will come directly to your WordPress dashboard. What’s more, you’ll be able to perform the update right there inside the dashboard. Be careful though…updating will undo all your customization so be sure to back everything up first.
<h3>More than just a Blog theme</h3>
On top of being an awesome magazine theme, it can also be used to start a fancy online webstore. To enable that it is compatible with the free <a href='http://wordpress.org/extend/plugins/woocommerce/' target='_blank'><strong>WooCommerce</strong></a> plugin. Digital, physical, variable products with WooCommerce you can sell pretty much anything.
<h3>Unlimited Colors</h3>
Instead of providing a couple of preset skins, it will allow you to define colors for things like buttons, titles, header, footer and so on. All of this is accomplished using the WordPress Customizer meaning you get to see your site change in real-time.
<h3>New Shortcodes</h3>
it comes with an extensive selection of shortcodes created using Twitter Bootstrap. Along with standard shortcodes such as alert boxes or icon lists you can also add tabs, galleries, tooltips, popovers and more. A full list of shortcodes is available on the it <strong><a href='http://templatic.com/demos/anchor/shortcodes/' target='_blank'>demo site</a></strong>
<div>
<h3>Other features</h3>
&nbsp;
<ul>
	<li><strong>1-click install</strong> for getting started with the theme in literally seconds</li>
	<li><strong>Fully widgetized homepage</strong> lets you control and edit almost every homepage element, even the slider!</li>
	<li>Full <a href='http://wordpress.org/extend/plugins/wordpress-seo/' target='_blank'>WordPress SEO by Yoast</a> and <a href='http://wordpress.org/extend/plugins/bbpress/' target='_blank'>bbPress</a> plugin compatibility</li>
	<li>Create massive header menus using the included Mega Menu widget</li>
</ul>
<h3></h3>
&nbsp;

</div>
</div>
",
					"post_author"	=> $agents_ids_array[array_rand($agents_ids_array)],
					"post_meta" =>	$post_meta,
					"post_image" =>	$image_array,
					"post_category" =>	array('Blog','Mobile'),
					"post_tags" =>	array('Tags','Sample Tags')

					);
////post end///
//====================================================================================//
////post start 21///
$image_array = array();
$post_meta = array();
$image_array[] = "dummy/blog3.jpg" ;
$post_meta = array(
				   "templ_seo_page_title" =>'Something about us!',
				   "templ_seo_page_kw" => '',
"tl_dummy_content"	=> '1',
				   "templ_seo_page_desc" => '',
				);
$post_info[] = array(
					"post_title" =>	'Something about us!',
					"post_content" =>	"
<blockquote>For those that want to see the theme back-end in action we have the <a href='http://test.templatic.com/' target='_blank'>test site</a>. Just as the name suggests, the site allows you to take a test drive of a specific theme in order to ultimately decide whether it will be right for the project or not. Each test site is valid for at least ten days, allowing you to thoroughly test each aspect/feature of the theme.</blockquote>
<h2>Who we are ?</h2>

We are a group of designers and developers working round the clock to make a difference with WordPress. Providing high quality, completely customizable Premium WordPress themes is what Templatic is all about. Our mission is to take WordPress to the next level with unparalleled e-commerce, directory and app themes.
<h2>What we do ?</h2>
We provide all types of themes depending on the needs and demands of our customers. From a simple blog to a e-Commerce website; we have it all! Many of our themes feature functionality not available anywhere else. Not only that, many of our themes paved the way for entire niches. Perhaps the best examples of that are GeoPlaces and iPhone App. If you have an idea for a cool WordPress theme, <a href='http://templatic.com/contact' target='_blank'>let us know</a>! To view our selection of WordPress themes please visit our <a href='http://templatic.com/wordpress-themes-store' target='_blank'>Theme Gallery</a>.
<br/>
<br/>
[templatic_msg_box type='success'] Resize your browser window or better yet open this demo site in your mobile device to see the responsive design in action ![/templatic_msg_box]
<h2>Our Approach</h2>
Approach to <strong>Theme Design</strong> is another aspect which sets Templatic apart from many other companies. Each theme released has been created with the potential visitor in mind. Business sites tend to have a more professional look while modern directories often display vivid colors with lots of dynamic content. All of this is reflected in our theme's designs.
<h2>Ever Growing Community</h2>
Templatic <a href='http://templatic.com/forums' target='_blank'>community forum</a> is a truly unique support system. It’s a place where members can share their experiences, communicate with other members <strong>and</strong> request professional support from Templatic agents. The system enables those that require assistance to get it quickly and efficiently while still providing lots of content (and discussions) for those that want to be active inside the community. As a reward to those that go out of their way to help others we give out free themes to active members each month!
Templatic <a href='http://templatic.com/forums' target='_blank'>community forum</a> is a truly unique support system. It’s a place where members can share their experiences, communicate with other members <strong>and</strong> request professional support from Templatic agents. The system enables those that require assistance to get it quickly and efficiently while still providing lots of content (and discussions) for those that want to be active inside the community. As a reward to those that go out of their way to help others we give out free themes to active members each month!
Templatic <a href='http://templatic.com/forums' target='_blank'>community forum</a> is a truly unique support system. It’s a place where members can share their experiences, communicate with other members <strong>and</strong> request professional support from Templatic agents. The system enables those that require assistance to get it quickly and efficiently while still providing lots of content (and discussions) for those that want to be active inside the community. As a reward to those that go out of their way to help others we give out free themes to active members each month!


",
					"post_author"	=> $agents_ids_array[array_rand($agents_ids_array)],
					"post_meta" =>	$post_meta,
					"post_image" =>	$image_array,
					"post_category" =>	array('Blog','Google'),
					"post_tags" =>	array('Tags','Sample Tags')

					);
////post end///
//====================================================================================//
////post start 22///
$image_array = array();
$post_meta = array();
$image_array[] = "dummy/blog1.jpg" ;
$post_meta = array(
				   "templ_seo_page_title" =>'Works with the Powerful Supreme Framework',
				   "templ_seo_page_kw" => '',
"tl_dummy_content"	=> '1',
				   "templ_seo_page_desc" => '',
				);
$post_info[] = array(
					"post_title" =>	'Works with the Powerful Supreme Framework',
					"post_content" =>	'<h2>Supreme is a child theme which works on top of our Supreme parent theme which also powers our <a href="http://templatic.com/wordpress-themes-store/" title="link" target="_blank">other </a>recent themes</h2>
<p>With 3.0 release, WordPress has introduced a new user interface to help manage navigation menus, which simply means you&acute;ll get a new page with some tools on it to help you add, delete, and arrange links.</p>
<p>To utilize this feature, you must first activate it. Without activation, your menu management page will display nothing, but an error.<br />
If it&acute;s currently inactive, in your WordPress administration panel, go to Appearance &gt; Menus to see the error.</p>
<h3>How to Activate WordPress 3.0 Menu Management</h3>
<p>Add the following code to the functions.php file of your theme.<br />
[php]if (function_exists("add_theme_support")) {<br />
	add_theme_support("menus");<br />
}<br />
[/php]<br />
While add_theme_support("menus"); is enough to activate the Menu Management page, the additional code around this necessary line makes sure if later or earlier versions of WordPress doesn"t have this feature then it will simply do nothing and cause no error.</p>
<h4>What the code above means:</h4>
<p>The code above simply means if the <strong>Add Theme Support</strong> function exists, use that function to add <strong>Menus</strong> feature. If it doesn&acute;t exist, do nothing.</p>
<h4>Step by Step</h4>
<ol>
<li>Open theme folder and find functions.php.</li>
<li>Open functions.php using Notepad or text editor of your choice.</li>
<li>Copy and paste the code above.</li>
<li> File &gt; Save functions.php</li>
</ol>
<h4>Where to place the code</h4>
<p>If the functions.php file of your theme is messy or you don&acute;t really know where to place the code, go to the end of functions.php and paste the code before:<br />
[php]?&gt;[/php]<br />
A question mark immediately next to a right arrow marks the end of a set of codes. The last combination of question mark and right arrow in the file marks the end of the file. Normally, if you add any code right before the file ends, you&acute;d have no problem.</p>

This set of codes is only slightly different from what you were first given. The additional<br />
[php]&lt;?php[/php]<br />
and<br />
[php]?&gt;[/php]<br />
at the beginning and ending of this set of codes means start PHP and end PHP.</p>
<p>You may close functions.php. For the rest of this tutorial, you don&acute;t need it. Now you&acute;ve activated the Menu Management feature or user interface, here&acute;s what it looks like:</p>
<h3>Using Menu Management User Interface</h3>
<p>If you use <a href="http://codex.wordpress.org/Function_Reference/wp_nav_menu">wp_nav_menu()</a> in theme template files to display the menu, by default, it will list only links with a Page attached to it. But, what if you wanted to add custom external links without creating a new page just to point to it? For example, adding a Twitter link to your site&acute;s main menu. Here&acute;s how.</p>
<p>First, create a custom menu because WordPress will not allow you to add, delete, or re-arrange links without at least having one custom menu. Name your menu then save it. For this tutorial, my first custom menu is named, &acute;first.&acute;</p>
<p>After creating the custom menu, you have several options on Menu Management page to add links. For example, you can simply check the boxes next to the Pages and Categories you want to add then click the <strong>Add to Menu</strong> button. You can also add custom links and here&acute;s what it looks like:<br />
Don&acute;t forget to click the Save Menu button after adding new links.</p>
<h3>How to Display Custom Menu</h3>
<p>Like I mentioned before, wp_nav_menu() by default displays your list of links based on what Pages you have. It doesn&acute;t display custom menu links. To display the custom menu wherever you want it to show up, copy and paste the following:<br />

Replace &acute;first&acute; with the name of your menu.</p>
<h4>What the code above means:</h4>
<ul>
<li>Start PHP</li>
<li>Use wp_nav_menu() to display menu</li>
<li>The custom menu I want to use is &acute;first.&acute;</li>
<li>End PHP</li>
</ul>
<p>In whichever file you&acute;re pasting it in, save the file. Upload this file to the theme folder on your server if you&acute;re not directly editing it through the WordPress administration panel.</p>
<p>I created a blank theme just for this tutorial. Here&acute;s what it looks like for me after putting the code above in the index.php file of my blank theme.</p>
<p>If you right click on the page currently displaying your menu and go to View Source, you get to see what this menu looks like under the hood. Here&acute;s what it looks like for me:<br />
[php]&lt;div class=&quot;menu-first-container&quot;&gt;<br />
&lt;ul id=&quot;menu-first&quot; class=&quot;menu&quot;&gt;<br />
	&lt;li id=&quot;menu-item-4&quot; class=&quot;menu-item menu-item-type-custom&quot;&gt;&lt;a href=&quot;http://son&quot;&gt;son&lt;/a&gt;<br />
		&lt;ul class=&quot;sub-menu&quot;&gt;<br />
			&lt;li id=&quot;menu-item-6&quot; class=&quot;menu-item menu-item-type-custom&quot;&gt;&lt;a href=&quot;http://grandchild&quot;&gt;grand child&lt;/a&gt;&lt;/li&gt;<br />
		&lt;/ul&gt;<br />
	&lt;/li&gt;<br />
	&lt;li id=&quot;menu-item-5&quot; class=&quot;menu-item menu-item-type-custom&quot;&gt;&lt;a href=&quot;http://daughter&quot;&gt;daughter&lt;/a&gt;&lt;/li&gt;<br />
&lt;/ul&gt;<br />
&lt;/div&gt;<br />
[/php]<br />
Wherever you see &acute;first&acute; in the set of codes above, you know it&acute;s there only because I named the custom menu &acute;first.&acute;</p>
<h3>Display Multiple Custom Menus</h3>
<p>To do that, you have to first create the second menu. Here&acute;s my second menu, which is conveniently named, &acute;second.&acute; And, I&acute;ve added two links under the second menu.<br />
To display the second menu, duplicate code for the first menu and change menu=first to menu=second. If you named your second menu, &acute;submenu,&acute; then change menu=first to menu=submenu. Here&acute;s the entire code:<br />

<h3>Differentiating Custom Menus and Customizing Them</h3>
<p>There are several ways to differentiate and customize custom menus. The most obvious one is using different names for first and second menus. The less obvious ways are customizing <strong>container_class</strong>, <strong>container_id</strong>, and <strong>menu_class</strong>.
Under the hood, by adding <strong>menu_class=my-main-menu</strong> using the ampersand sign (&amp;), my menu list changes from:<br />
[php]&lt;ul id=&quot;menu-first&quot; class=&quot;menu&quot;&gt;[/php]<br />
to:<br />
[php]&lt;ul id=&quot;menu-first&quot; class=&quot;my-main-menu&quot;&gt;[/php]<br />
As you can see, to add another customizable option to the menu, you simply use the ampersand sign, the option you&acute;re customizing, and the value of that option, which can be any string of texts of your choosing. After you&acute;ve added the code, save the file, refresh the web page, and check under the hood by choosing View Source to see the changes made.</p>
<p>For a list of options you can customize or assign values to, go to <a href="http://codex.wordpress.org/Function_Reference/wp_nav_menu">wp_nav_menu at the WordPress Codex</a>.</p>


',
					"post_author"	=> $agents_ids_array[array_rand($agents_ids_array)],
					"post_meta" =>	$post_meta,
					"post_image" =>	$image_array,
					"post_category" =>	array('Blog','Apple'),
					"post_tags" =>	array('Tags','Sample Tags')

					);









////post end///
//====================================================================================//
insert_posts($post_info);
function insert_posts($post_info)
{
	global $wpdb,$current_user;
	for($i=0;$i<count($post_info);$i++)
	{
		$post_title = $post_info[$i]['post_title'];
		$post_count = $wpdb->get_var("SELECT count(ID) FROM $wpdb->posts where post_title like \"$post_title\" and post_type='post' and post_status in ('publish','draft')");
		if(!$post_count)
		{
			$post_info_arr = array();
			$catids_arr = array();
			$my_post = array();
			$post_info_arr = $post_info[$i];
			if($post_info_arr['post_category'])
			{
				for($c=0;$c<count($post_info_arr['post_category']);$c++)
				{
					$catids_arr[] = get_cat_ID($post_info_arr['post_category'][$c]);
				}
			}else
			{
				$catids_arr[] = 1;
			}
			$my_post['post_title'] = $post_info_arr['post_title'];
			$my_post['post_content'] = $post_info_arr['post_content'];
			if($post_info_arr['post_author'])
			{
				$my_post['post_author'] = $post_info_arr['post_author'];
			}else
			{
				$my_post['post_author'] = 1;
			}
			$my_post['post_status'] = 'publish';
			$my_post['post_category'] = $catids_arr;
			$my_post['tags_input'] = $post_info_arr['post_tags'];
			$last_postid = wp_insert_post( $my_post );
			$post_meta = $post_info_arr['post_meta'];
			$data = array(
				'comment_post_ID' => $last_postid,
				'comment_author' => 'admin',
				'comment_author_email' => get_option('admin_email'),
				'comment_author_url' => 'http://',
				'comment_content' => $post_info_arr['post_title'].'its amazing.',
				'comment_type' => '',
				'comment_parent' => 0,
				'user_id' => $current_user->ID,
				'comment_author_IP' => '127.0.0.1',
				'comment_agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)',
				'comment_date' => $time,
				'comment_approved' => 1,
			);

			wp_insert_comment($data);
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
					$post_img['post_status'] = 'inherit';
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

//=============================PORTFOLIO CUSTOM TAXONOMY post entry start =======================================================//
$post_info = array();
insert_taxonomy_category($category_array);
/// Portfolio 1 ////post start 1//
$image_array = array();
$post_meta = array();
$image_array[0] = "dummy/5Star.jpg" ;
$post_meta = array(
  					"tl_dummy_content"	=> '1',
					"Layout"	=> '1c',
			);
$post_info[] = array(
					"post_title"	    =>	'5Star',
					"post_content"	    =>	'<strong>5Star</strong> - <a href="http://templatic.com/app-themes/5-star-responsive-hotel-theme">Online Hotel Booking and Reservations Theme</a>
					5 Star is a sleek hotel booking WordPress theme which is powered by our advanced Tevolution plugin and the Booking System add-on. Use the section below to read more about each plugin as well as features that are unique to 5 Star CMS theme for WordPress that help you manage your hotel online including, hotel booking with an online booking form and a booking calendar. 
					
					<b>Why buy this 5Star theme?</b>
					Because it’s one of the best WordPress hotel themes. You can build a complete site for hotel in just a few hours using nothing but the all-powerful WordPress and our theme. This is a new concept with a beautiful design and focus on content – the hotels themselves. 5Star is a complete theme with built in functionality for booking.',
 					"post_meta"		    =>	$post_meta,
					"post_image" 		=>	$image_array,
					"post_category"	    =>	array('Artworks','Web Design'),
					"post_tags"		    =>	array('sample tags, tags')
					);
////post end/// 
/// Portfolio 2 ////post start 2//
$image_array = array();
$post_meta = array();
$image_array[0] = "dummy/publisher.jpg" ;
$post_meta = array(
  					"tl_dummy_content"	=> '1',
					"Layout"	=> '1c',
			);
$post_info[] = array(
					"post_title"	    =>	'Publisher',
					"post_content"	    =>	'<strong>Publisher</strong> - <a href="http://templatic.com/app-themes/publisher">Showcasing and selling products Publisher Theme</a>
					Publisher is a sleek WordPress theme powered by our advanced Tevolution plugin and the Digital Download add-on. It is also completely WooCommerce compatible. Use the section below to read more about each plugin as well as features that are unique to Publisher WordPress App Theme. 
					
					Publisher WordPress theme for showcasing and selling your digital goods easily. Showcase and sell your digital products like ebooks or digital paintings. Offer native download or links to third party sites. Bonus E Junkie support. The homepage of this theme has been carefully designed and coded, without compromising on customizability and flexibility to give you the best possible results. The custom dynamic widgets add more to the overall appeal of your site.Reality Bites coverband at midnight.
					
					<b>Mobile Friendly</b>
					The theme’s responsive design will ensure that it works and looks great on all devices. The width of the theme will adjust automatically based on the device you’re using. To test this open the <a herf="templatic.com/demos/publisher">demo site</a> and resize your browser window.
					
					<b>1-click Install</b>
					Like the default theme layout? Don’t have a lot of time to setup the theme? 1-click auto install will sort that out! By clicking on just one button you can make the theme look like the demo site.',
 					"post_meta"		    =>	$post_meta,
					"post_image" 		=>	$image_array,
					"post_category"	    =>	array('Photography','Web Design'),
					"post_tags"		    =>	array('sample tags, tags')
					);
////post end///
/// Portfolio 3 ////post start 3//
$image_array = array();
$post_meta = array();
$image_array[0] = "dummy/vacationrental.jpg" ;
$post_meta = array(
  					"tl_dummy_content"	=> '1',
			);
$post_info[] = array(
					"post_title"	    =>	'Vacation Rental',
					"post_content"	    =>	'<strong>Vacation Rental</strong> - <a href="http://templatic.com/app-themes/vacation-rental">Manage hotel Vacation Rental Theme</a>
					Vacation Rental WordPress theme is a sleek theme powered by our advanced Tevolution plugin and the Booking System add-on. Use the section below to read more about each plugin as well as features that are unique to CMS Hotel Booking system for WordPress. Our Vacation Rental Premium theme is ideal for you to manage your hotel online with great features like an online booking form a booking calendar and much more.
					
					WordPress theme with great features for Hotel business owners. This theme helps you to create a beautiful Hotel website, complete with booking and reservation management functionality. To give you the best possible results, the homepage of this theme has been carefully designed and coded, without compromising on customizability and flexibility. The custom dynamic widgets add more to the overall appeal of your site.
					
					<b>Widgetized Homepage</b>
					Customizing the Vacation Rental homepage is easy as drag n’ drop, literally. Widgets allow you to populate the homepage with various content while still preserving that clean and beautiful look!
					
					<b>Unlimited Color Options</b>
					Hotel Booking allows you to quickly change the color of your buttons or body backrground. Using the WordPress Customizer these changes can be made in seconds. To top it off, all of these changes are made in real time!',
 					"post_meta"		    =>	$post_meta,
					"post_image" 		=>	$image_array,
					"post_category"	    =>	array('Artworks','Web Design'),
					"post_tags"		    =>	array('sample tags, tags')
					);
////post end///
/// Portfolio 4 ////post start 4//
$image_array = array();
$post_meta = array();
$image_array[0] = "dummy/realestate.jpg" ;
$post_meta = array(
  					"tl_dummy_content"	=> '1',
					"Layout"	=> '1c',
			);
$post_info[] = array(
					"post_title"	    =>	'Real Estate 2',
					"post_content"	    =>	'<strong>Real Estate</strong> - <a href="http://templatic.com/app-themes/real-estate-wordpress-theme-templatic">Property Classifieds Listings Theme</a>
					This powerful IDX/MLS compatible real estate classifieds theme is both unique and powerful in the features it provides. With this real estate listings theme for WordPress, you can allow estate agencies and home sellers an opportunity to submit properties to your site. This real estate theme comes with many features including powerful search filter. 
					
					<b>Why buy this Real Estate theme?</b>
					Fully customizable front page and many widget ready areas across all the pages/posts.
Excellent Support
PSD File Included with multiple use license
Multi level drop down menu
Custom page templates such as archives, full width page, sitemap, contact page etc.
Custom built, dynamic widgets that you can use multiple times.
Standard wordpress Blog & Pages
Gravatar Support & Threaded Comments
Built-in Ad Monetization
Widget Ready with custom widgets
Valid, Cross browser compatible',
 					"post_meta"		    =>	$post_meta,
					"post_image" 		=>	$image_array,
					"post_category"	    =>	array('Artworks','Photography','Web Design'),
					"post_tags"		    =>	array('sample tags, tags')
					);
////post end///
/// Portfolio 5 ////post start 5//
$image_array = array();
$post_meta = array();
$image_array[0] = "dummy/cartsy.jpg" ;
$post_meta = array(
  					"tl_dummy_content"	=> '1',
					"Layout"	=> '1c',
			);
$post_info[] = array(
					"post_title"	    =>	'Cartsy',
					"post_content"	    =>	'<strong>Cartsy</strong> - <a href="http://templatic.com/ecommerce-themes/cartsy-wordpress-woocommerce-theme">e-commerce theme with beautiful typography</a>
					Cartsy is a simple and stylish e-commerce theme which is great for those just starting out and it’s also ideal for developers due to its customization-friendly design and structure. Cartsy offers full compatibility with the free WooCommerce plugin which will provide unparalleled product control and shipping management to this powerful WordPress Woocommerce theme.
					
					<b>Why buy this Cartsy theme?</b>
					By purchasing Cartsy you get infinite customization possibilities, modern & responsive design, full WooCommerce compatibility and unparalleled theme support and documentation!',
 					"post_meta"		    =>	$post_meta,
					"post_image" 		=>	$image_array,
					"post_category"	    =>	array('Photography','Web Design'),
					"post_tags"		    =>	array('sample tags, tags')
					);
////post end///
/// Portfolio 6 ////post start 6//
$image_array = array();
$post_meta = array();
$image_array[0] = "dummy/nightlife.jpg" ;
$post_meta = array(
  					"tl_dummy_content"	=> '1',
					"Layout"	=> '1c',
			);
$post_info[] = array(
					"post_title"	    =>	'Nightlife',
					"post_content"	    =>	'<strong>Nightlife</strong> - <a href="http://templatic.com/cms-themes/nightlife-events-directory-wordpress-theme">Events Directory Theme</a>
					Nightlife, the Events Directory Theme for WordPress will do a lot more than just store events like a normal directory – it will make them come to life! The homepage slider showcases events beautifully and can even be set to show static images instead of events. Featured events can be set for both the homepage and category page while advanced search options guarantee that no event will go unnoticed. Google Maps support enables new ways of finding events, and Nightlife allows you to create as many map pages as you want!
					
					<b>Why buy this Private Lawyer theme?</b>
					Nightlife is a very advanced theme and it can take time to absorb all the information. If you have any questions don’t hesitate to contact us. To experience Nightlife (as an admin) click on the <a href="http://test.templatic.com">Create a test site</a> link.',
 					"post_meta"		    =>	$post_meta,
					"post_image" 		=>	$image_array,
					"post_category"	    =>	array('Photography','Web Design'),
					"post_tags"		    =>	array('sample tags, design, tags')
					);
////post end///
/// Portfolio 7 ////post start 7//
$image_array = array();
$post_meta = array();
$image_array[0] = "dummy/geoplaces1.jpg" ;
$image_array[1] = "dummy/geoplaces2.jpg" ;
$image_array[2] = "dummy/geoplaces3.jpg" ;
$post_meta = array(
  					"tl_dummy_content"	=> '1',
					"Layout"	=> '1c',
			);
$post_info[] = array(
					"post_title"	    =>	'Geo Places',
					"post_content"	    =>	'Create a professional city-directory, show places and set-up events, let users submit listings and showcase all of this on Google Map embeds. This is our city directory theme, where users can submit events and places and also comment on them. We call it geoplaces. The powerful backend features Design settings, adding custom fields through our interface, adding new custom fields on registration page, and the features does not ends here. There is lot more. Designed strategically, the structure of the page is done in such a way that gives maximum exposure to essential elements.
					
					<strong>GeoPlaces</strong> - <a href="http://templatic.com/app-themes/geo-places-city-directory-wordpress-theme">Business Directory Theme</a>
The popular business directory theme that lets you have your very own local business listings directory or an international companies pages directory. This elegant and responsive design theme gives you powerful admin features to run a free or paid local business directory or both. GeoPlaces even has its own integrated events section so you not only get a business directory but an events directory too.

					<b>Why buy Geo Places ?</b>
					Because this is the best WordPress theme so far of its kind. GeoPlaces is a great Business Directory Theme for WordPress allowing you to create a city directory, list out the best places, add reviews and rating, show locations through Google maps, monetize and earn from it, and these are just some benefits. In this city directory theme, we have included the features very thoughtfully, neglecting the not so important stuff and including only the necessary features with the best usability and easy of use. GeoPlaces offers you a feature-packed WordPress Business Directory Theme that would make your site look great and also give you plenty of admin tools in the backend.',
 					"post_meta"		    =>	$post_meta,
					"post_image" 		=>	$image_array,
					"post_category"	    =>	array('Web Design'),
					"post_tags"		    =>	array('sample tags, tags')
					);
////post end///
/// Portfolio 8 ////post start 8//
$image_array = array();
$post_meta = array();
$image_array[0] = "dummy/Templatic-Theme-Gallery.png" ;
$post_meta = array(
  					"tl_dummy_content"	=> '1',
  					"Layout"	=> '1c',
			);
$post_info[] = array(
					"post_title"	    =>	'Wordpress Themes Club',
					"post_content"	    =>	'The Templatic <a href="http://templatic.com/premium-themes-club/">Wordpress Themes Club</a> membership is ideal for any WordPress developer and freelancer that needs access to a wide variety of Wordpress themes. This themes collection saves you hundreds of dollars and also gives you the fantastic deal of allowing you to install any of our themes on unlimited domains.

You can see below just a few of our WordPress themes that are included in the club membership
&nbsp;
<p class="clearfix"><img style="margin-top : 0px" src="http://templatic.com/_data/images/Business-Directory-Theme-For-Wordpress_GeoPlaces.png" class="alignleft" /><strong>GeoPlaces</strong> - <a href="http://templatic.com/app-themes/geo-places-city-directory-wordpress-theme">Business Directory Theme</a>
The popular business directory theme that lets you have your very own local business listings directory or an international companies pages directory. This elegant and responsive design theme gives you powerful admin features to run a free or paid local business directory or both. GeoPlaces even has its own integrated events section so you not only get a business directory but an events directory too.</p>

<p class="clearfix"><img style="margin-top : 0px" src="http://templatic.com/_data/images/Car-Classifieds-Wordpress-Theme_Automotive.png" class="alignleft" /><strong>Automotive</strong> - <a href="http://templatic.com/cms-themes/automotive-responsive-vehicle-directory">Car Classifieds Theme</a>
A responsive auto classifieds theme that gives you the ability of allowing vehicles submission on free or paid listing packages which you decide on the price and duration. This sleek auto classifieds and car directory theme is also WooCommerce compatible so you can even use part of your site to run as a car spares online store. Details</p>

<p class="clearfix"><img style="margin-top : 0px" src="http://templatic.com/_data/images/Daily-Deal-Wordpress-Deals-Theme_DailyDeal.png" class="alignleft"/><strong>Daily Deal</strong> - <a href="http://templatic.com/app-themes/daily-deal-premium-wordpress-app-theme">Deals Theme</a>
A powerful Deals theme for WordPress which lets your visitors buy or sell deals on your deals website. Daily Deal is by far the easiest and cheapest way to create a deals site where you can earn money by creating different deals submission price packages but you can also allow free deal submissions. Details</p>

<p class="clearfix"><img style="margin-top : 0px" src="http://templatic.com/_data/images/Events-Directory-Wordpress-Theme_Events.png" class="alignleft"/><strong>Events V2</strong> - <a href="http://templatic.com/app-themes/events">Events Directory Theme</a>
Launch a successful Events directory portal with this elegant responsive events theme. The theme has many powerful admin features including allowing event organizers to submit events on free or paid payment packages. This theme is simple to setup and you can get your events site up in no time.</p>

<p class="clearfix"><img style="margin-top : 0px" src="http://templatic.com/_data/images/Events-Manager-Wordpress-Theme_NightLife.png" class="alignleft"/><strong>NightLife</strong> - <a href="http://templatic.com/cms-themes/nightlife-events-directory-wordpress-theme">Events Directory Theme</a>
A beautifully designed events management theme which is responsive and allows you to run an events website. Allow event organizers free or paid event listing submissions and offer online event registrations. Nightlife is feature-packed with all the features you can expect from an events directory theme.</p>

<p class="clearfix"><img style="margin-top : 0px" src="http://templatic.com/_data/images/Hotel-Bookings-WordPress-Theme_5Star.png" class="alignleft"/><strong>5 Star</strong> - <a href="http://templatic.com/app-themes/5-star-responsive-hotel-theme">
Online Hotel Booking and Reservations Theme</a>A well designed hotel booking theme which is ideal for showcasing and promoting a hotel online in style. This responsive design hotel reservation Wordpress theme will surely impress your guests and is also a theme that gives you a lot of powerful features including an advanced online booking system and a booking calendar.</p>

<p class="clearfix"><img style="margin-top : 0px" src="http://templatic.com/_data/images/Job-Classifieds-Wordpress-Theme_JobBoard.png" class="alignleft"/><strong>Job Board</strong> - <a href="http://templatic.com/app-themes/job-board">Job Classifieds Theme</a>
Start your job classifieds or job board site with this responsive premium jobs board theme. Allow employers to submit job listings for free, paid or both and also allow job seekers to apply for jobs or submit their resumes. Packed with great features you would expect from a premium jobs board theme. Details</p>

<p class="clearfix"><img style="margin-top : 0px" src="http://templatic.com/_data/images/News-Magazine-Blog-WordPress-Theme_TechNews.png" class="alignleft"/><strong>TechNews</strong> - <a href="http://templatic.com/magazine-themes/technews-advanced-blog-theme">
Blogging and News Theme</a>A news theme that is an ideal solution for your a news blog. An elegant theme which is ideal for news blogs, magazine or newspaper sites. This mobile friendly theme is both responsive and WooCommerce compatible. Impress your visitors with the stylish layout and available color schemes. Details</p>

<p class="clearfix"><img style="margin-top : 0px" src="http://templatic.com/_data/images/Property-Classifieds-Listings-WordPress-Theme_RealEstate.png" class="alignleft"/><strong>Real Estate V2</strong> - <a href="http://templatic.com/app-themes/real-estate-wordpress-theme-templatic">
Property Classifieds Listings Theme</a>This powerful IDX/MLS compatible real estate classifieds theme is both unique and powerful in the features it provides. With this real estate listings theme for WordPress, you can allow estate agencies and home sellers an opportunity to submit properties to your site. This real estate theme comes with many features including powerful search filter.</p>

<p class="clearfix"><img style="margin-top : 0px" src="http://templatic.com/_data/images/Online-Store-Wordpress-Theme_ECommerce.png" class="alignleft"/><strong>e-Commerece</strong> - <a href="http://templatic.com/ecommerce-themes/e-commerce">Online Store Theme</a>
A powerful and elegant WooCoomerce compatible e-commerce WordPress theme with many features advanced features. This online store theme offers various modes of product display such as a shopping Cart, digital Shop or catalog mode. This theme for e-commerce offers multiple payment gateways, coupon codes. Details</p>

See the full collection of the <a href="http://templatic.com/premium-themes-club/">WordPress Themes Club Membership</a>',
 					"post_meta"		    =>	$post_meta,
					"post_image" 		=>	$image_array,
					"post_category"	    =>	array('Artworks','Web Design'),
					"post_tags"		    =>	array('tags, design, sample tags')
					);
////post end/// 


insert_taxonomy_posts_($post_info);
function insert_taxonomy_posts_($post_info)
{
	global $wpdb,$current_user;
	for($i=0;$i<count($post_info);$i++)
	{
		$post_title = $post_info[$i]['post_title'];
		$post_count = $wpdb->get_var("SELECT count(ID) FROM $wpdb->posts where post_title like \"$post_title\" and post_type='".CUSTOM_POST_TYPE1."' and post_status in ('publish','draft')");
		if(!$post_count)
		{
			$post_info_arr = array();
			$catids_arr = array();
			$my_post = array();
			$post_info_arr = $post_info[$i];
			$my_post['post_title'] = $post_info_arr['post_title'];
			$my_post['post_content'] = $post_info_arr['post_content'];
			$my_post['post_type'] = CUSTOM_POST_TYPE1;
			if($post_info_arr['post_author'])
			{
				$my_post['post_author'] = $post_info_arr['post_author'];
			}else
			{
				$my_post['post_author'] = 1;
			}
			$my_post['post_status'] = 'publish';
			$my_post['post_category'] = $post_info_arr['post_category'];
			$my_post['tags_input'] = $post_info_arr['post_tags'];
			$last_postid = wp_insert_post( $my_post );
			
			wp_set_object_terms($last_postid,$post_info_arr['post_category'], $taxonomy=CUSTOM_CATEGORY_TYPE1);
			wp_set_object_terms($last_postid,$post_info_arr['post_tags'], $taxonomy='cartags');

			$post_meta = $post_info_arr['post_meta'];
			if($post_meta)
			{
				foreach($post_meta as $mkey=>$mval)
				{
					update_post_meta($last_postid, $mkey, $mval);
					update_post_meta($last_postid,'Layout','1c');
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
					$post_img['post_status'] = 'inherit';
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
// Portfolio entry end ====================================================================================//




//=============================PAGES ENTRY START=======================================================//
$post_info = array();

$pages_array = array(array('Page Templates','Advanced Search','Archives','Sitemap','Contact Us','Front page'));
$page_info_arr = array();
$post_meta = array();
$post_meta = array(
  					"tl_dummy_content"	=> '1',
			);
$page_info_arr[] = array('post_title'=>'Page Templates',
						 'comment_status'=>'closed',
						 'post_content' =>'In WordPress, you can write either posts or pages. When you writing a regular blog entry, you write a post. Posts automatically appear in reverse chronological order on your blog home page. Pages, on the other hand, are for content such as "About Me," "Contact Me," etc. Pages live outside of the normal blog chronology, and are often used to present information about yourself or your site that is somehow timeless -- information that is always applicable. You can use Pages to organize and manage any amount of content. WordPress can be configured to use different Page Templates for different Pages.

To create a new Page, log in to your WordPress admin with sufficient admin privileges to create new page. Select the Pages -> Add New option to begin writing a new Page.


Now, give a suitable title to your page as per your requirement and start adding content using Visual/Text editor given there. In text editior, you have to use html<tags> to design format of your content like bullets, fonts, table etc. Whereas, in visual editor you can simply work like you do in Ms Word! You can find the bullets, numbers on the menu of the editor and you just have to click them and nothing else. However using html <tags> you can design something new to your page using various tags available in html!

Coming back to the page templates, templates as all of us know that often used for something that is pre designed with a specific purpose and which can be reusable with some modification. Page Templats acts in the same way! These are the pages made on some obvious need of business irrespective of their type. You can directly use these pages by just selecting the template from the drop down available in Page Attributes section in WordPress create new page. You can also add some content, if you want to show that on your page along with the pre specified sontent and design of the template.

As the page templates and pages are important, so do the URL of your pages! Do not ignore it. These URLs or say permalinks are very useful when it comes to SEO, so you can increase the search rank of your page too. Other than just SEO, you can place these links wherever you want on your site and redirect your users on a particulat page. These links can also be given in the widgets itslef wherever it allows. You can do this as per your requirement. WordPress is very easy to use medium to build a website and run it successfully without having much technical knowledge! Yes, you do not have to be a programmer to build up your site and that is possible with the WordPress and the themes that provide you the ready made well thought design and functionality and you can have a complete professional site with some minor efforts! Also, when you purchase the premium theme, it often comes with support for some limited time and they always help you if you are stuck in between. 
',
'post_meta'=>$post_meta);
$page_info_arr[] = array('post_title'=>'Advanced Search',
						 'comment_status'=>'closed',
						 'post_content' =>"This is the Advanced Search page template. Just select this template from the page attributes section and you are good to go. The Advance Search template helps you to locate anything listed on the website by typing related key words, tags or even categories! And that's why, it is no just search but an advance search."
						 ,'post_meta'=>$post_meta);
$page_info_arr[] = array('post_title'=>'Archives',
						 'comment_status'=>'closed',
						'post_content'=>'This is Archives page template. Just create a page and select it from page Attributes > Templates section from your WordPress dashboard and you are good to go. The basic purpose of this template is to list all your posts month wise and it counts the post publish date for doing this. This page comes as the sample page, if you have inserted a sample data on your site.'
						,'post_meta'=>$post_meta);

$page_info_arr[] =  array('post_title'=>'Sitemap',
						'comment_status'=>'closed',
						'post_content'=>'Sitemap is a page template that helps you show all the things that your site cosists of. Using this template, you can provide your site visitors, a short summary about your site! Seeing all the things listed on just a single page, visitor will understand the basic purpose and offerings of your website in very easy manner. You can create it in the same way like you do for other templates. The same process of creating new page and selecting the Sitemap template from the Page Attributes section and you are done!'
						,'post_meta'=>$post_meta);
$page_info_arr[] = array('post_title'=>'Contact Us',
						'comment_status'=>'closed',
						'post_content'=>'With Templatic theme, you get some ready pages widely know as Page Templates. This is one of them, a Contact Page Template! It shows an inquiry form to your users and these inquires are sent to the admin email ID set in your WordPress sites back in general settings.'
						,'post_meta'=>$post_meta);


$page_meta = array('_wp_page_template'=>'page-templates/front-page.php','Layout'=>'1c'); 
$page_info_arr[] = array('post_title'=>'Front page',
						'post_content'=>'',
						'comment_status'=>'closed',
						'post_meta'=> $page_meta);

set_page_info_autorun($pages_array,$page_info_arr);

//=====================================================================
$photo_page_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts where post_title like 'Advanced Search' and post_type='page'");
update_post_meta( $photo_page_id, '_wp_page_template', 'page-templates/advance-search.php' );

$photo_page_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts where post_title like 'Archives' and post_type='page'");
update_post_meta( $photo_page_id, '_wp_page_template', 'page-templates/archives.php' );

$photo_page_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts where post_title like 'Sitemap' and post_type='page'");
update_post_meta( $photo_page_id, '_wp_page_template', 'page-templates/sitemap.php' );
update_post_meta( $photo_page_id, 'template_post_type', 'post' );

$photo_page_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts where post_title like 'Contact Us' and post_type='page'");
update_post_meta( $photo_page_id, '_wp_page_template', 'page-templates/contact-us.php' );

$photo_page_id1 = $wpdb->get_var("SELECT ID FROM $wpdb->posts where post_title like 'Map' and post_type='page'");
update_post_meta( $photo_page_id1, '_wp_page_template', 'page-template/map.php' );
update_option('show_on_front','page');

$sidebars_widgets = get_option('sidebars_widgets');  //collect widget informations
$sidebars_widgets = array();


// Home Page Full Slider Sidebar widgets settings: start
$supreme_banner_slider = array();
$supreme_banner_slider[1] = array(
					"animation"				=>	'fade',
					"autoplay"				=>	'true',
					"sliding_direction" 	=>	'horizontal',
					"reverse"				=>	'true',
					"slideshowSpeed"		=>	'4000',
					"animation_speed"		=>	'1500',
					"custom_banner_temp"	=>	1,
					"s1_title"				=> array('Slide your images with home page main slider widget','Ideal size of image is 1920X900 Px','These are banner titles','One Page Theme','Slide your images with home page main slider widget','Ideal size of image is 1920X900 Px','These are banner titles'),
					"s1_title_link"			=> array('http://www.templatic.com/onepager','http://www.templatic.com/onepager','http://www.templatic.com/onepager','http://www.templatic.com/onepager','http://www.templatic.com/onepager','http://www.templatic.com/onepager','http://www.templatic.com/onepager'),
					"s1"					=> array(get_template_directory_uri().'/images/dummy/slider1.jpg',get_template_directory_uri().'/images/dummy/slider2.jpg',get_template_directory_uri().'/images/dummy/slider3.jpg',get_template_directory_uri().'/images/dummy/slider4.jpg',get_template_directory_uri().'/images/dummy/slider5.jpg',get_template_directory_uri().'/images/dummy/slider6.jpg',get_template_directory_uri().'/images/dummy/slider7.jpg'),
					);						
$supreme_banner_slider['_multiwidget'] = '1';
update_option('widget_supreme_banner_slider',$supreme_banner_slider);
$supreme_banner_slider = get_option('widget_supreme_banner_slider');
krsort($supreme_banner_slider);
foreach($supreme_banner_slider as $key=>$val1)
{
	$supreme_banner_slider_key = $key;
	if(is_int($supreme_banner_slider_key))
	{
		break;
	}
}
$sidebars_widgets["home-page-full-slider"] = array("supreme_banner_slider-{$supreme_banner_slider_key}");
// Home Page Full Slider Sidebar widgets settings: end


//Home Page Content Sidebar widgets settings: start
$theme_aboutus_widget = array();
$theme_aboutus_widget[1] = array(
				"title"				=>	'About Us',
				"text"				=>	"We are a company that specializes in creating business solutions in a form of WordPress themes. With 5 years under our belt we are one of the oldest companies in the business and have helped thousands in creating their dream website. What makes Templatic stand out is the unique approach to theme development. In a time where most companies choose either design or functionality, we aim to bring best of both worlds. As a result, our themes both work and look the part eliminating the need for any additional plugins or extensions.Approach to Theme Design is another aspect which sets Templatic apart from many other companies. Each theme released has been created with the potential visitor in mind. Business sites tend to have a more professional look while modern directories often display vivid colors with lots of dynamic content. All of this is reflected in our theme’s designs.",
				"at_name"			=>	array("Parker","Lisa","Demi","Sandra"),
				"at_email"			=>	array("","cristintemp123@gmail.com","laratemp123@gmail.com","cristintemp123@gmail.com"),
				"at_photo_link"		=>	array(get_template_directory_uri().'/images/dummy/about1.jpg',"","",""),
				"at_post"			=>	array("Founder","Admin","Front end Developer","Designer"),
				"at_fb"				=>	array("#","#","#","#"),
				"at_twitter"		=>	array("#","#","#","#"),
				"at_linkedin"		=>	array("#","#","#","#"),
				);						
$theme_aboutus_widget['_multiwidget'] = '1';
update_option('widget_theme_aboutus_widget',$theme_aboutus_widget);
$theme_aboutus_widget = get_option('widget_theme_aboutus_widget');

krsort($theme_aboutus_widget);
foreach($theme_aboutus_widget as $key=>$val)
{
	$theme_aboutus_widget_key = $key;
	if(is_int($theme_aboutus_widget_key))
	{
		break;
	}
}
$sidebars_widgets["home-page-content"] = array("theme_aboutus_widget-{$theme_aboutus_widget_key}");
//Home Page Content Sidebar widgets settings: end


// Home Page Content Area 1 Sidebar widgets settings:start
$theme_services_list = array();
$theme_services_list[1] = array(
				"title"			=>	'Services',
				"desc"			=>	"We are a five year old company which specilizes in creating beautiful app-like WordPress themes. Our themes are known for their stunning design and powerful features which provide a unique experience for visitors to the tens of thousands of websites we have helped create. Your dream site is literally just a few clicks away! What makes Templatic stand out is the unique approach to theme development. In a time where most companies choose either design or functionality, we aim to bring best of both worlds.As a result, our themes both work and look the part eliminating the need for any additional plugins or extensions.",
				"view_text"		=>	"View Details",
				"link_text"		=>	array("<i class='icon-edit'></i> Quality Features","<i class='icon-user'></i> Customer Care","<i class='icon-book'></i> Theme Resources","<i class='icon-truck'></i> Test Drive","<i class='icon-heart'></i> 1-click Install","<i class='icon-mobile-phone'></i> Make Money","<i class='icon-pencil'></i> Theme Updates","<i class='icon-coffee'></i> Track Record"),
				"link_desc"		=>	array("Each Templatic theme is one of the most advanced WordPress themes in its niche. Most of our themes provide features that are simply not available anywhere else.","All registered members can receive help via two support systems, Helpdesk (private) and forums (public). Get the best technical support around!","Along with the theme guide that comes with each theme purchase, each registered member also has access to our extensive documentation library with various WordPress-related articles.","Sales pages and demos allow you to learn a lot of about a theme, but they don't show you everything. With our test site you can see the admin back-end in action and see whether the theme meets your needs.","Want to make your site look like our demo? It's one click away! This feature makes it easy to get started, especially because the added data can be removed afterwards.","Instead of relying on secondary revenue streams such as ads, most Templatic themes come with built-in payment systems that allow you to process payments and make money without any additional apps.","To provide maximum compatibility with WordPress and some plugins, our themes are routinely updated. We also aim to release upgrades for popular themes at least once a year.","Over the past five years we have helped create tens of thousands of sites. To read the experiences of some of our customers be sure to visit the  testimonials page."),
				"link_url"			=>	array("http://templatic.com","http://templatic.com","http://templatic.com","http://templatic.com","http://templatic.com","http://templatic.com","http://templatic.com","http://templatic.com"),
				);						
$theme_services_list['_multiwidget'] = '1';
update_option('widget_theme_services_list',$theme_services_list);
$theme_services_list = get_option('widget_theme_services_list');

krsort($theme_services_list);
foreach($theme_services_list as $key=>$val)
{
	$theme_services_list_key = $key;
	if(is_int($theme_services_list_key))
	{
		break;
	}
}

$testimonials_widget = array();
$testimonials_widget[1] = array(
				"title"					=>	'',
				"fadin"					=>	'3000',
				"fadout"				=>	'600',
				"transition"			=>	'fade',
				"quotetext"				=>	array("Templatic has the best support of any wordpress theme seller I have ever worked with. They always respond promptly and always find a way to help. It's reassuring to me to know there's someone on the other end that actually cares if you’re satisfied.","Templatic offers world class WordPress theme support and unique, highly innovative and professionally useful WordPress themes. So glad to have found you! All the best and many more years of creativity, productivity and success.","Templatic has the best support of any wordpress theme seller I've ever worked with. They always respond promptly and always find a way to help. It's reassuring to me to know there’s someone on the other end that actually cares if you’re satisfied."),
				"author"				=> array("Sandra","John Smith","Mark Todd"),
				"s1_email"				=> array("laratemp123@gmail.com","cristintemp123@gmail.com","laratemp123@gmail.com"),
				"link_text"				=> "",
				"link_url"				=> "",
				);						
$testimonials_widget['_multiwidget'] = '1';
update_option('widget_testimonials_widget',$testimonials_widget);
$testimonials_widget = get_option('widget_testimonials_widget');
krsort($testimonials_widget);

foreach($testimonials_widget as $subscriberkey=>$subscriberval)
{
	$testimonials_widget_key = $subscriberkey;
	if(is_int($testimonials_widget_key))
	{
		break;
	}
}

$sidebars_widgets["home_after_content2"] = array("theme_services_list-{$theme_services_list_key}","testimonials_widget-{$testimonials_widget_key}");
// Home Page After Content 1 Sidebar widgets settings:end


// Home Page After Content 2 Sidebar widgets settings: start
$tmpl_jquery_post_listing = array();
$tmpl_jquery_post_listing[2] = array(
					"title"					=>	'Creative Portfolio',
					"post_type"				=>  'portfolio',
					"enable_categories"		=>	1,
					"post_number"			=>	8,
					"show_image" 			=>	1,
					"image_size"			=>	'supreme-thumbnail',
					);						
$tmpl_jquery_post_listing['_multiwidget'] = '1';
$get_supreme_banner_slider1 = get_option('widget_supreme_banner_slider');
update_option('widget_tmpl_jquery_post_listing',$tmpl_jquery_post_listing);
$tmpl_jquery_post_listing = get_option('widget_tmpl_jquery_post_listing');
krsort($tmpl_jquery_post_listing);
foreach($tmpl_jquery_post_listing as $key1=>$val1)
{
	$tmpl_jquery_post_listing_key1 = $key1;
	if(is_int($tmpl_jquery_post_listing_key1))
	{
		break;
	}
}

$theme_supreme_advertisements = array();
$theme_supreme_advertisements[1] = array(
				"title"					=>	'',
				"img_url"				=>	get_template_directory_uri().'/images/dummy/addbg.jpg',
				"ads"					=>	'<div class="home_page_aftercontent content_four "><h3>This is an Advertisment widget</h3><p>A simple image is used here with some html tags that help to show text and description on it. You can view these html tags and how they are used in back-end at wp-admin-Widgets- Home Page After Content 2. The same way you can also show such image with the description on it. You can even use any other widget suitable here as per your requirement. you can follow the theme guide to know what else you can do with this theme.</p></div>',
				);						
$theme_supreme_advertisements['_multiwidget'] = '1';
update_option('widget_theme_supreme_advertisements',$theme_supreme_advertisements);
$theme_supreme_advertisements = get_option('widget_theme_supreme_advertisements');
krsort($theme_supreme_advertisements);

foreach($theme_supreme_advertisements as $subscriberkey=>$subscriberval)
{
	$theme_supreme_advertisements_key = $subscriberkey;
	if(is_int($theme_supreme_advertisements_key))
	{
		break;
	}
}
$sidebars_widgets["home_after_content3"] = array("tmpl_jquery_post_listing-{$tmpl_jquery_post_listing_key1}","theme_supreme_advertisements-{$theme_supreme_advertisements_key}");
// Home Page After Content 2 Sidebar widgets settings: end


// Home Page After Content 3 Sidebar widgets settings: start
$templatic_popular_post_technews[1] = array(
				"title"					=>	'Blog',
				"post_type"				=>	'post',
				"number"				=>	9,
				"show_excerpt"			=>	0,
				"slide"					=>	6,
				"popular_per"			=>	'comments',
				"pagination_position"	=>	0,
				);						
$templatic_popular_post_technews['_multiwidget'] = '1';
update_option('widget_templatic_popular_post_technews',$templatic_popular_post_technews);
$templatic_popular_post_technews = get_option('widget_templatic_popular_post_technews');
krsort($templatic_popular_post_technews);

foreach($templatic_popular_post_technews as $subscriberkey=>$subscriberval)
{
	$supreme_recent_post_key = $subscriberkey;
	if(is_int($supreme_recent_post_key))
	{
		break;
	}
}

$supreme_subscriber_widget = array();
$supreme_subscriber_widget[1] = array(
				"title"					=>	'Newsletter',
				"text"					=>	'By subscribing to our mailing list you will always be updated with the latest new from us.',
				"newsletter_provider"	=>	'feedburner',
				"feedburner_id"			=>	'',
				"mailchimp_api_key"		=>	'',
				"mailchimp_list_id"		=>	'',
				"aweber_list_name"		=>	'',
				"feedblitz_list_id"		=>	'',
				);						
$supreme_subscriber_widget['_multiwidget'] = '1';
update_option('widget_supreme_subscriber_widget',$supreme_subscriber_widget);
$supreme_subscriber_widget = get_option('widget_supreme_subscriber_widget');
krsort($supreme_subscriber_widget);

foreach($supreme_subscriber_widget as $subscriberkey=>$subscriberval)
{
	$supreme_subscriber_widget_key = $subscriberkey;
	if(is_int($supreme_subscriber_widget_key))
	{
		break;
	}
}

$sidebars_widgets["home_after_content4"] = array("templatic_popular_post_technews-{$supreme_recent_post_key}","supreme_subscriber_widget-{$supreme_subscriber_widget_key}");
// Home Page After Content 3 Sidebar widgets settings: end


// Home Page After Content 4 Sidebar widgets settings: start
$templatic_text = array();
$templatic_text[1] = array(
				"title"					=>	'Contact Us',
				"text"					=>	'<div class="span left">
<p>It is a text widget used with some html tags. You can use it to show your contact details straight on home page like this. Check out the html tags in the back-end, wp-admin - Widgets - Home Page After Content 4. You can then redirect user to your contact form using link.<b><a href="http://templatic.com">Keep in touch!</a></b></p></div><div class="span right"><h3>New York</h3>230 Vine Street And locations throughout Old City,<br/>Philadelphia, PA 19106<br/><b><i class="icon-envelope-alt"></i>  newyork@eleven.com<br /><i class="icon-phone-sign"></i>  ( 495 ) 663 287 547</b></div>',
				);						
$templatic_text['_multiwidget'] = '1';
update_option('widget_templatic_text',$templatic_text);
$templatic_text = get_option('widget_templatic_text');
krsort($templatic_text);

foreach($templatic_text as $subscriberkey=>$subscriberval)
{
	$templatic_text_key = $subscriberkey;
	if(is_int($templatic_text_key))
	{
		break;
	}
}

$templatic_google_map = array();
$templatic_google_map[1] = array(
				"title"					=>	'',
				"address"				=>	'230 Vine Street And locations throughout Old City, Philadelphia, PA 19106',
				"map_height"			=>	500,
				"scale"					=>	17,
				"map_type"				=>	'SATELLITE',
				);						
$templatic_google_map['_multiwidget'] = '1';
update_option('widget_templatic_google_map',$templatic_google_map);
$templatic_google_map = get_option('widget_templatic_google_map');
krsort($templatic_google_map);

foreach($templatic_google_map as $subscriberkey=>$subscriberval)
{
	$templatic_google_map_key = $subscriberkey;
	if(is_int($templatic_google_map_key))
	{
		break;
	}
}

$supreme_contact_widget = array();
$supreme_contact_widget[1] = array(
				"title"					=>	'Send Us A Message',
				);						
$supreme_contact_widget['_multiwidget'] = '1';
update_option('widget_supreme_contact_widget',$supreme_contact_widget);
$supreme_contact_widget = get_option('widget_supreme_contact_widget');
krsort($supreme_contact_widget);

foreach($supreme_contact_widget as $subscriberkey=>$subscriberval)
{
	$supreme_contact_widget_key = $subscriberkey;
	if(is_int($supreme_contact_widget_key))
	{
		break;
	}
}

$sidebars_widgets["home_after_content5"] = array("templatic_text-{$templatic_text_key}","templatic_google_map-{$templatic_google_map_key}","supreme_contact_widget-{$supreme_contact_widget_key}");
// Home Page After Content 4 Sidebar widgets settings: end


// Footer Sidebar widgets settings: start
$templatic_text[2] = array(
				"title"					=>	'',
				"text"					=>	'<div class="contacts_wrap"><a href=""><i class="icon-envelope"></i>mail: eleven@example.com</a><a href="#"><i class="icon-phone"></i>  Phone: (12)  345 6789</a></div>',
				);						
$templatic_text['_multiwidget'] = '1';
update_option('widget_templatic_text',$templatic_text);
$templatic_text = get_option('widget_templatic_text');
krsort($templatic_text);

foreach($templatic_text as $subscriberkey=>$subscriberval)
{
	$templatic_text1_key = $subscriberkey;
	if(is_int($templatic_text1_key))
	{
		break;
	}
}

$social_media = array();
$social_media[1] = array(
				"title"					=>	'',
				"social_description"	=>	'',
				"social_link"			=>	array("http://facebook.com/templatic","http://twitter.com/templatic","http://in.linkedin.com/pub/templatic-templatic/1a/34/401","https://plus.google.com/116364132852083985421/posts"),
				"social_icon"			=>	array("","","",""),
				"social_text"			=>	array("<abbr>F</abbr>","<abbr>t</abbr>","<abbr>l</abbr>","<abbr>g</abbr>"),
				);						
$social_media['_multiwidget'] = '1';
update_option('widget_social_media',$social_media);
$social_media = get_option('widget_social_media');
krsort($social_media);

foreach($social_media as $subscriberkey=>$subscriberval)
{
	$social_media_key = $subscriberkey;
	if(is_int($social_media_key))
	{
		break;
	}
}
$sidebars_widgets["footer"] = array("templatic_text-{$templatic_text1_key}","social_media-{$social_media_key}");
// Footer Sidebar widgets settings: end


// Post Listing Page Sidebar widgets settings: start
$hybrid_categories = array();
$hybrid_categories[1] = array(
				"title"		=>	'Categories',
				"taxonomy"	=>	'category',
				'style' 	=> 'list',
				'include' 	=> '',
				'exclude' 	=> '',
				'exclude_tree' => '',
				'child_of' => '',
				'current_category' => '',
				'search' => '',
				'hierarchical' => true,
				'hide_empty' => true,
				'order' => 'ASC',
				'orderby' => 'name',
				'depth' => 0,
				'number' => '',
				'feed' => '',
				'feed_type' => '',
				'feed_image' => '',
				'use_desc_for_title' => false,
				'show_count' => false,
				);						
$hybrid_categories['_multiwidget'] = '1';
update_option('widget_hybrid-categories',$hybrid_categories);
$hybrid_categories = get_option('widget_hybrid-categories');
krsort($hybrid_categories);

foreach($hybrid_categories as $subscriberkey=>$subscriberval)
{
	$hybrid_categories_key = $subscriberkey;
	if(is_int($hybrid_categories_key))
	{
		break;
	}
}

$templatic_popular_post_technews[2] = array(
				"title"					=>	'Popular Posts',
				"post_type"				=>	'post',
				"number"				=>	5,
				"show_excerpt"			=>	0,
				"slide"					=>	3,
				"popular_per"			=>	'comments',
				"pagination_position"	=>	0,
				);						
$templatic_popular_post_technews['_multiwidget'] = '1';
update_option('widget_templatic_popular_post_technews',$templatic_popular_post_technews);
$templatic_popular_post_technews = get_option('widget_templatic_popular_post_technews');
krsort($templatic_popular_post_technews);

foreach($templatic_popular_post_technews as $subscriberkey=>$subscriberval)
{
	$templatic_popular_post_technews_key = $subscriberkey;
	if(is_int($templatic_popular_post_technews_key))
	{
		break;
	}
}
$sidebars_widgets["post-listing-sidebar"] = array("hybrid-categories-{$hybrid_categories_key}","templatic_popular_post_technews-{$templatic_popular_post_technews_key}");
// Post Listing Page Sidebar widgets settings: end

// Post Detail Page Sidebar widgets settings: start
$hybrid_categories1 = array();
$hybrid_categories1[1] = array(
				"title"		=>	'Categories',
				"taxonomy"	=>	'category',
				'style' 	=> 'list',
				'include' 	=> '',
				'exclude' 	=> '',
				'exclude_tree' => '',
				'child_of' => '',
				'current_category' => '',
				'search' => '',
				'hierarchical' => true,
				'hide_empty' => true,
				'order' => 'ASC',
				'orderby' => 'name',
				'depth' => 0,
				'number' => '',
				'feed' => '',
				'feed_type' => '',
				'feed_image' => '',
				'use_desc_for_title' => false,
				'show_count' => false,
				);						
$hybrid_categories1['_multiwidget'] = '1';
update_option('widget_hybrid-categories',$hybrid_categories1);
$hybrid_categories1 = get_option('widget_hybrid-categories');
krsort($hybrid_categories1);

foreach($hybrid_categories1 as $subscriberkey=>$subscriberval)
{
	$hybrid_categories1_key = $subscriberkey;
	if(is_int($hybrid_categories1_key))
	{
		break;
	}
}

$templatic_popular_post_technews[3] = array(
				"title"					=>	'Popular Posts',
				"post_type"				=>	'post',
				"number"				=>	5,
				"show_excerpt"			=>	0,
				"slide"					=>	3,
				"popular_per"			=>	'comments',
				"pagination_position"	=>	0,
				);						
$templatic_popular_post_technews['_multiwidget'] = '1';
update_option('widget_templatic_popular_post_technews',$templatic_popular_post_technews);
$templatic_popular_post_technews = get_option('widget_templatic_popular_post_technews');
krsort($templatic_popular_post_technews);

foreach($templatic_popular_post_technews as $subscriberkey=>$subscriberval)
{
	$templatic_popular_post_technews_key1 = $subscriberkey;
	if(is_int($templatic_popular_post_technews_key1))
	{
		break;
	}
}
$sidebars_widgets["post-detail-sidebar"] = array("hybrid-categories-{$hybrid_categories1_key}","templatic_popular_post_technews-{$templatic_popular_post_technews_key1}");
// Post Detail Page Sidebar widgets settings: end


// Primary Sidebar widgets settings: start
$supreme_facebook = array();
$supreme_facebook[1] = array(
				"title"					=>	'Our Facebook Fans',
				"facebook_page_url"		=>	'http://facebook.com/templatic',
				"width"					=>	300,
				"show_faces"			=>	1,
				"show_stream"			=>	1,
				"show_header"			=>	1,
				);						
$supreme_facebook['_multiwidget'] = '1';
update_option('widget_supreme_facebook',$supreme_facebook);
$supreme_facebook = get_option('widget_supreme_facebook');
krsort($supreme_facebook);

foreach($supreme_facebook as $subscriberkey=>$subscriberval)
{
	$supreme_facebook_key = $subscriberkey;
	if(is_int($supreme_facebook_key))
	{
		break;
	}
}


$theme_supreme_advertisements[2] = array(
				"title"	=>	'About One Pager',
				"img_url"	=>	'',
				"ads"	=>	'One Pager is available for purchase now! It helps in creating the meaningful, useful portfolio & helps you in giving your viewers a visual treat with the beautiful images, sliders & enriches the total experience. Make One Pager your companion & also get the access to our awesome community forums and 1 year of theme updates and support',
				);						
$theme_supreme_advertisements['_multiwidget'] = '1';
update_option('widget_theme_supreme_advertisements',$theme_supreme_advertisements);
$theme_supreme_advertisements = get_option('widget_theme_supreme_advertisements');
krsort($theme_supreme_advertisements);

foreach($theme_supreme_advertisements as $subscriberkey=>$subscriberval)
{
	$theme_supreme_advertisements1_key1 = $subscriberkey;
	if(is_int($theme_supreme_advertisements1_key1))
	{
		break;
	}
}
$sidebars_widgets["primary-sidebar"] = array("supreme_facebook-{$supreme_facebook_key}","theme_supreme_advertisements-{$theme_supreme_advertisements1_key1}");
// Primary Sidebar widgets settings: end


// Contact Page Content Area Sidebar widgets settings: start
$templatic_google_map[2] = array(
				"title"					=>	'',
				"address"				=>	'230 Vine Street And locations throughout Old City, Philadelphia, PA',
				"map_height"			=>	500,
				"scale"					=>	13,
				"map_type"				=>	'ROADMAP',
				);						
$templatic_google_map['_multiwidget'] = '1';
update_option('widget_templatic_google_map',$templatic_google_map);
$templatic_google_map = get_option('widget_templatic_google_map');
krsort($templatic_google_map);

foreach($templatic_google_map as $subscriberkey=>$subscriberval)
{
	$templatic_google_map_key1 = $subscriberkey;
	if(is_int($templatic_google_map_key1))
	{
		break;
	}
}
$sidebars_widgets["contact_page_widget"] = array("templatic_google_map-{$templatic_google_map_key1}");
// Contact Page Content Area Sidebar widgets settings: end


// Contact Page Sidebar widgets settings: start
$supreme_facebook[1] = array(
				"facebook_page_url"		=>	'http://facebook.com/templatic',
				"width"					=>	300,
				"show_faces"			=>	1,
				"show_stream"			=>	1,
				"show_header"			=>	1,
				);						
$supreme_facebook['_multiwidget'] = '1';
update_option('widget_supreme_facebook',$supreme_facebook);
$supreme_facebook = get_option('widget_supreme_facebook');
krsort($supreme_facebook);

foreach($supreme_facebook as $subscriberkey=>$subscriberval)
{
	$supreme_facebook_key1 = $subscriberkey;
	if(is_int($supreme_facebook_key1))
	{
		break;
	}
}
$sidebars_widgets["contact_page_sidebar"] = array("supreme_facebook-{$supreme_facebook_key1}");
// Contact Page Sidebar widgets settings: end

//=========================================
update_option('sidebars_widgets',$sidebars_widgets);  //save widget iformations

//===============================================================================

/* ======================== CODE TO ADD RESIZED IMAGES ======================= */
regenerate_all_attachment_sizes();
 
function regenerate_all_attachment_sizes() {
	$args = array( 'post_type' => 'attachment', 'numberposts' => 100, 'post_status' => 'inherit'); 
	$attachments = get_posts( $args );
	if ($attachments) {
		foreach ( $attachments as $post ) {
			$file = get_attached_file( $post->ID );
			wp_update_attachment_metadata( $post->ID, wp_generate_attachment_metadata( $post->ID, $file ) );
		}
	}		
}?>