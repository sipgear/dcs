<?php
/* 
include theme related functions and filters 
*/
	/* the post type */

	
	/* to fetch the front page page template */
	global $wpdb;
	$pageid='';
	$wp_pages = get_pages(array(
	'meta_key' => '_wp_page_template',
	'meta_value' => 'page-templates/front-page.php'
	));
	foreach($wp_pages as $page){
		$pageid = $page->ID;
	}
	if(!$pageid){
		$page_meta = array('_wp_page_template'=>'page-templates/front-page.php','Layout'=>'default'); 
		$page_info_arr[] = array('post_title'=>'Front page',
								'post_content'=>'',
								'post_meta'=> $page_meta);

		set_page_info_autorun(@$pages_array,$page_info_arr); /* function to save.autosave the pages */
		
		$wp_pages = get_pages(array(
		'meta_key' => '_wp_page_template',
		'meta_value' => 'page-templates/front-page.php'
		));
		foreach($wp_pages as $page){
		 $pageid = $page->ID;
		}
	}
	
	/* show custom home page for this theme*/
	if(get_option('show_on_front') && !get_option('page_update_first')){
		update_option('page_update_first', 1);
	}

	/* Hook to change the height of croausal slider image  */
	
	add_filter('carousel_slider_height', 'responsive_crousal_height');

	/*
	Name : responsive_crousal_height
	Args : height 
	Description : return height for crausal slider image
	*/
	function responsive_crousal_height($height){
		
		$height = 400;
		return $height;
	}
	
	/* set default hight and width of slider images */
	add_filter('supreme_slider_width','supreme_slider_width_',11);
	add_filter('supreme_slider_height','supreme_slider_height',11);
	
	/*
	Name : supreme_slider_height
	Args : height 
	Description : return height for crausal slider image
	*/
	function supreme_slider_height($height){
		return 300;
	}
	/*
	Name : supreme_slider_width_
	Args : width 
	Description : return width for crausal slider image
	*/
	function supreme_slider_width_($width){
		return 300;
	}
	
	/* to provide a support of display content in slider */
	add_theme_support('slider-post-content');
	
	/* to provide a option of posts per slide */
	add_theme_support('postperslide');
	
	apply_filters('supreme_post_perslide',8,11); // default slides
	
	/* Do something with the data entered */
	add_action( 'save_post', 'mytheme_save_custom_box' ,11);
	
	

	
	/*
	Name : mytheme_inner_custom_box
	Args : width 
	Description : Prints the box content
	*/
	function mytheme_inner_custom_box( $post ) {

	  // Use nonce for verification
	  wp_nonce_field( basename(get_template_directory()), 'responsive_noncename' );

	  // The actual fields for data entry
	  // Use get_post_meta to retrieve an existing value from the database and use the value for the form
	

	  
	  _e('Enter image url , after enter URL please select <b>post format</b> image, so it will display as large image.',THEME_DOMAIN);
	}	
	
	/*
	Name : mytheme_columns_custom_box
	Args : post 
	Description : Prints the list of columns
	*/
	function mytheme_columns_custom_box( $post ) {

	  // Use nonce for verification
	  wp_nonce_field( basename(get_template_directory()), 'responsive_c_noncename' );
	  global $page_id,$post;
	  $page_id = $post->ID;
	  // The actual fields for data entry
	  // Use get_post_meta to retrieve an existing value from the database and use the value for the form
	  $value = get_post_meta( $page_id, $key = '_page_column', $single = true );
	  $_page_column = get_post_meta( $page_id, $key = '_page_column', $single = true );
	  if($_page_column ==''){ $_page_column = 4; }
	  echo "<ul>";
	  for($i = 1; $i <= 4 ; $i ++){
	    /* to display page columns */
		if($_page_column == $i){ $checked = "checked=chekced"; }
		if($i == 1){ $c = __('Column',THEME_DOMAIN); }else{ $c = __('Columns',THEME_DOMAIN);  }
		echo '<label><li><input type="radio" name="_page_column" value="'.$i.'" '. $checked.'> '.$i." ".$c.'</li></label>';
		$checked='';
	  }	
	  echo "</ul>";
		_e('Select the columns you want to show in listing of work.',THEME_DOMAIN);
	}
	
	/*
	Name : mytheme_save_custom_box
	Args : post id 
	Description : save custom fields
	*/
	function mytheme_save_custom_box($post_id){
		global $post,$post_id; 
	
		
		update_post_meta($post_id,'_page_column', @$_POST['_page_column']);

	}
	
	
	/*
	Name : tthemes_page_id
	Args : page template 
	Description : return the id of page template
	*/
	function tthemes_page_id($page){
		global $wpdb,$post,$page,$wp_query;
			$wp_pages = get_pages(array(
			'meta_key' => '_wp_page_template',
			'meta_value' => $page
			));

			foreach($wp_pages as $page){
				$pageid = $page->ID;
			}

		return $pageid; 
	}

/* Child Theme Customizer option */
if(!function_exists('supreme_child_customize_register')){
	function supreme_child_customize_register($wp_customize ){
		
	}
}


/*
Name : theme_post_info
Description : Function return post tagline line like postauthor,post date,total comment.
*/
if(!function_exists('theme_post_info')){
	function theme_post_info(){
		global $post;
		$num_comments = get_comments_number();
		if ( comments_open() ) {
			if ( $num_comments == 0 ) {
				$comments = __('No Comments',THEME_DOMAIN);
			} elseif ( $num_comments > 1 ) {
				$comments = $num_comments .' '. __('Comments',THEME_DOMAIN);
			} else {
				$comments = __('1 Comment',THEME_DOMAIN);
			}
			if(theme_get_settings('disable_comments_on_'.get_post_type())!=1){
				$write_comments = '<a href="' . get_comments_link() .'">'. $comments.'</a>';
			}else{
				$write_comments = '';
			}
		}
		?>
		<div class="byline">
			<?php
			$post_type = get_post_type_object( get_post_type() );
			if ( !current_user_can( $post_type->cap->edit_post, get_the_ID() ) ){
				$edit = '';
			}else{
				$edit = '<span class="5star_edit"><a class="post-edit-link" href="' . esc_url( get_edit_post_link( get_the_ID() ) ) . '" title="' . sprintf( esc_attr__( 'Edit %1$s', 'supreme-core' ), $post_type->labels->singular_name ) . '">' . __( 'Edit', 'supreme-core' ) . '</a></span>';
			}	
			$author = __('Published by',THEME_DOMAIN).' <span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr( get_the_author_meta( 'display_name' ) ) . '">' . get_the_author_meta( 'display_name' ) . '</a></span>';
			
			$published = __('On',THEME_DOMAIN).' <abbr class="published" title="' . sprintf( get_the_time( esc_attr__( get_option('date_format')) ) ) . '">' . sprintf( get_the_time( esc_attr__( get_option('date_format')) ) ) . '</abbr>';
			 echo sprintf(__('%s %s %s %s',THEME_DOMAIN),$author,$published,$write_comments,$edit);
			?>
		</div>
		<?php		
	}
}
/*
Name : theme_tax_post_info
Description : Function return post tagline line like postauthor,post date,total comment.
*/
if(!function_exists('theme_tax_post_info')){
	function theme_tax_post_info(){
		global $post;
		$num_comments = get_comments_number();
		if ( comments_open() ) {
			if ( $num_comments == 0 ) {
				$comments = __('No Comments',THEME_DOMAIN);
			} elseif ( $num_comments > 1 ) {
				$comments = $num_comments .' '. __('Comments',THEME_DOMAIN);
			} else {
				$comments = __('1 Comment',THEME_DOMAIN);
			}
			if(theme_get_settings('disable_comments_on_'.get_post_type())!=1){
				$write_comments = '<a href="' . get_comments_link() .'">'. $comments.'</a>';
			}else{
				$write_comments = '';
			}
		}
		?>
		<div class="byline">
			<?php
			$post_type = get_post_type_object( get_post_type() );
			if ( !current_user_can( $post_type->cap->edit_post, get_the_ID() ) ){
				$edit = '';
			}else{
				$edit = '<span class="5star_edit"><a class="post-edit-link" href="' . esc_url( get_edit_post_link( get_the_ID() ) ) . '" title="' . sprintf( esc_attr__( 'Edit %1$s', 'supreme-core' ), $post_type->labels->singular_name ) . '">' . __( 'Edit', 'supreme-core' ) . '</a></span>';
			}	
			if(theme_get_settings( 'display_author_name' )){
				$author = __('Published by',THEME_DOMAIN).' <span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr( get_the_author_meta( 'display_name' ) ) . '">' . get_the_author_meta( 'display_name' ) . '</a></span>';
			}
			if(theme_get_settings( 'display_publish_date' )){
				$published = __('On',THEME_DOMAIN).' <abbr class="published" title="' . sprintf( get_the_time( esc_attr__( get_option('date_format')) ) ) . '">' . sprintf( get_the_time( esc_attr__( get_option('date_format')) ) ) . '</abbr>';
			}
			echo sprintf(__('%s %s %s %s',THEME_DOMAIN),$author,$published,$write_comments,$edit);
			?>
		</div>
		<?php		
	}
}

/* to add the page settings section in home page manu */
add_action('admin_init','tmpl_init');
add_action( 'save_post', 'save_page_data' );

function save_page_data($post_id){
	global $wpdb;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	  return;
	 
	$other_post_type = @$_POST['post_type'];
	 // because save_post can be triggered at other times
	  if ( !wp_verify_nonce( @$_POST['page_nonce'], basename(__FILE__) ) )
		  return $post_id;
	  
	// Check permissions
	  if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
			return;
	  }else{
		if ( !current_user_can( 'edit_post', $post_id ) )
			return;
	  }
	 
	  if( @$_REQUEST['home_page_section'] != "" ){
		update_post_meta($_POST['post_ID'],'use_as_home_section', @$_REQUEST['home_page_section']);
	 }
}

function tmpl_init(){
	add_meta_box( 'menbu_title', 'One Pager Setting', 'menu_box_callback', 'nav-menus', 'side', 'high' );
	add_meta_box( 'page_title', 'Home Page Settings', 'page_callback', 'page', 'side', 'default' );
	if(isset($_POST['homepage-menuid']) && $_POST['homepage-menuid'] !=''){
		 for($m=1; $m<= 6; $m++){ 
			update_option('menu_section'.$m,$_POST['menu_section'.$m]);
			update_option('menu_page_section'.$m,$_POST['menu_page_section'.$m]);
		 }
	}
}
function page_callback(){
	global $post;
 ?>
	<script type="text/javascript">
		jQuery.noConflict(); 
		jQuery(document).ready(function() {
		var page = jQuery("#page_template").val();
		if( page !='page-templates/front-page.php' ){
			if(page =='page-templates/advance-search.php' || page =='page-templates/archives.php' || page =='page-templates/contact-us.php' || page =='page-templates/sitemap.php' ){
				jQuery("#page_title").css('display','block');
			}else{
				jQuery("#page_title").css('display','none');
			}
		}
		
		jQuery("#page_template").change(function() {
			var src = jQuery(this).val();
			var page1 = jQuery("#page_template").val();
				if(page1 =='page-templates/advance-search.php' || page1 =='page-templates/archives.php' || page1 =='page-templates/contact-us.php' || page1 =='page-templates/sitemap.php' ){
					jQuery("#page_title").fadeIn(2000); 
				}else{
					jQuery("#page_title").fadeOut(2000);
				}
			});
		});
	</script>
	<p class="howto"><?php echo "If you want to use this page on home page section then check this box"; ?></p>
	<div id="customlinkdiv" class="customlinkdiv">
		<p id="menu-item-name-wrap">
			<label for="home_page_section" class="">
				<input type="checkbox" value="1" <?php if(get_post_meta($post->ID,'use_as_home_section',true) == 1){echo 'checked="checked"';}?> name="home_page_section" id="home_page_section"/> <?php _e("Use as home page sections",THEME_DOMAIN);?>
			</label>
			<input type="hidden" name="page_nonce" value="<?php echo wp_create_nonce(basename(__FILE__));?>" />
		</p>
	</div>
<?php 
}
function menu_box_callback(){ ?>
	<p class="howto"><?php _e('Here given fields represents all the home page widget areas in their given chronological orders. Add Ids to these fields and save it. Then add same Id (e.g. sec2) in Custom Links URL field and add it to your menu item. Doing this, it will link that home page sections in the menu bar. <a href="http://templatic.com/docs/one-pager-theme-guide/#specialmenu">Read More</a>',THEME_DOMAIN); ?></p>
	<div id="customlinkdiv" class="customlinkdiv">
	<?php for($m=1; $m<= 6; $m++){ ?>
		<p id="menu-item-name-wrap">
			<label for="menu_section<?php echo $m; ?>" class="howto">
				<span><?php 
				if($m ==1){
					_e('Home Page Full Slider',THEME_DOMAIN); 
				}elseif($m == 2){
					_e('Home Page Section 1',THEME_DOMAIN); 
				}elseif($m == 3){
					_e('Home Page Section 2',THEME_DOMAIN); 
				}elseif($m == 4){
					_e('Home Page Section 3',THEME_DOMAIN); 
				}elseif($m == 5){
					_e('Home Page Section 4',THEME_DOMAIN); 
				}elseif($m == 6){
					_e('Home Page Section 5',THEME_DOMAIN); 
				}
				?></span>
				<input type="text" PLACEHOLDER="<?php echo "Section ".$m." ID"; ?>" value="<?php echo get_option('menu_section'.$m); ?>" class="regular-text menu-item-textbox input-with-default-title" name="menu_section<?php echo $m; ?>" id="menu_section<?php echo $m; ?>" style="width:257px;"/>
			</label>
		</p>
		<p id="menu-item-name-wrap">
			<label for="menu_page_section<?php echo $m; ?>" class="howto">
				<span><?php 
				if($m ==1){
					_e('Page',THEME_DOMAIN); echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; 
				}else{
					_e('Page',THEME_DOMAIN);echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; 
				}
				?></span>
				&nbsp;<select name="menu_page_section<?php echo $m; ?>" id="menu_page_section<?php echo $m; ?>" style="width:257px" class="regular-text menu-item-textbox input-with-default-title" >
						<option value="0"><?php _e("Select page",THEME_DOMAIN);?></option>
					<?php
						$args = array("post_type"=>'page', "post_status"=>"publish", "posts_per_page"=> -1 );
						$allpages = new WP_Query($args);
						if($allpages->have_posts()) : 
							while($allpages->have_posts()) : $allpages->the_post();
								global $post;
								$meta = get_post_custom($post->ID);
								$selected = (get_option("menu_page_section$m") == $post->ID ) ? "selected='selected'" : "";
								if( 'page-templates/front-page.php' != get_post_meta($post->ID,'_wp_page_template',true) ){
									echo '<option value="'.$post->ID.'" '.$selected.'>'.$post->post_title.'</option>';
								}
							endwhile;
						endif;
					?>
				</select>
			</label>
		</p>
		<p style="height:0px;border-bottom:1px solid #D6D5D6">&nbsp;</p>
		
	<?php } 
	?>	
		<p class="button-controls">
		<input type="submit" value="Save" class="button button-primary right" id="homepage-menuid" name="homepage-menuid"><span class="spinner"></span>
		</p>
	</div>
	
<?php
}
add_action( 'customize_register',  'one_pager_register_customizer_settings',14);
if(!function_exists('one_pager_register_customizer_settings')){
	function one_pager_register_customizer_settings( $wp_customize ){
		$wp_customize ->remove_control('color_picker_color6');
		$wp_customize->remove_control(supreme_prefix().'_theme_settings[templatic_texture1]');
		$wp_customize->remove_control(supreme_prefix().'_theme_settings[alternate_of_texture]');
		$wp_customize->remove_section( 'header_image');
		$wp_customize->get_control('color_picker_color2')->label = 'Primary - button, link hover';
		$wp_customize->get_control('color_picker_color3')->label = 'Secondary - button, link, headings';
		$wp_customize->get_control('color_picker_color4')->label = 'Page Content Color';
		$wp_customize->get_control('color_picker_color5')->label = 'Sub-texts';
	}
}
//Filter for google map ping image: start
add_filter('map_pin_image','map_pin_image');
if(!function_exists('map_pin_image')){
	function map_pin_image(){
		return get_template_directory_uri().'/images/pin-img.png';
	}
}
//Filter for google map ping image: end

//Filter for google map widget description: start
add_filter('google_map_widget_description','GoogleMapWidgetDescription');
if(!function_exists('GoogleMapWidgetDescription')){
	function GoogleMapWidgetDescription(){
		return esc_html__( 'Display a map of a specific location. Use in: Footer area, Homepage Content area, Primary, Contact Page Widget area', THEME_DOMAIN );
	}
}
//Filter for google map widget description: end

//Filter for newsletter widget description: start
add_filter('supreme_subscriber_widget_title','SubscribeWidgetDescription');
if(!function_exists('SubscribeWidgetDescription')){
	function SubscribeWidgetDescription(){
		return esc_html__( 'Shows a subscribe box with which users can subscribe your newsletter. Use in: Homepage Content Area, Footer area, Sidebar areas', THEME_DOMAIN );
	}
}
//Filter for newsletter widget description: end

//Filter for social media widget description: start
add_filter('supreme_social_media_description','SocialMapWidgetDescription');
if(!function_exists('SocialMapWidgetDescription')){
	function SocialMapWidgetDescription(){
		return esc_html__( 'Provide a link to your account on various social media sites. Use in: Primary, Footer and Sidebar areas', THEME_DOMAIN );
	}
}
//Filter for social media widget description: end


//Filter for woocommerce cart widget description: start
add_filter('supreme_woo_shop_cart_description','WooCartWidgetDescription');
if(!function_exists('WooCartWidgetDescription')){
	function WooCartWidgetDescription(){
		return esc_html__( 'Display Cart Informations with automatic cart update. Best to use it in "Secondary Navigation Right" widget area', THEME_DOMAIN );
	}
}
//Filter for woocommerce cart widget description: end

//Filter for home page content sidebar name: start
add_filter('home_page_content_name','home_page_content_name');
if(!function_exists('home_page_content_name')){
	function home_page_content_name(){
		return esc_html__( 'Home page section 1', THEME_DOMAIN );
	}
}
//Filter for home page content sidebar name: end


//
add_action('admin_init','onepager_admin_init');
if(!function_exists('onepager_admin_init')){
	function onepager_admin_init(){
		add_action( 'add_meta_boxes', 'onepager_remove_theme_layout_meta_box',11 );	
	}
}

if(!function_exists('onepager_remove_theme_layout_meta_box')){
	function onepager_remove_theme_layout_meta_box(){
		global $post;		
		$post_type=get_post_type();	
		if($post_type == 'portfolio'){
			remove_meta_box('theme-layouts-post-meta-box',$post_type,'side');	
		}
	}
}

// Register menu area for innerpages: start
add_action('admin_init','onepager_menu_admin_init');
if(!function_exists('onepager_menu_admin_init')){
	function onepager_menu_admin_init(){
		$menus = get_theme_support( 'supreme-core-menus' );

		/* If there is no array of menus IDs, return. */
		if ( !is_array( $menus[0] ) ){
			return;
		}	
		if ( in_array( 'innerpagemenu', $menus[0] ) ){
			register_nav_menu( 'innerpagemenu', _x( 'Menu For Inner Pages', 'nav menu location', 'supreme-core' ) );
		}		
	}
}	
// Register menu area for innerpages: end

add_action('wp_head','function_call');
function function_call(){
	if(is_home() || is_front_page()){
		add_action('wp_footer','header_menu_script');
	}
}

/*
Name :supreme_secondary_navigation
Description : return secondary navigation menu
*/
function onepager_supreme_secondary_navigation(){
	$theme_name = get_option('stylesheet');
	$nav_menu = get_option('theme_mods_'.strtolower($theme_name));
	?>
     <?php 
		$flag = 0;
	echo '<div id="nav-secondary" class="nav_bg">';	
		if(is_home() || is_front_page()){
			if(isset($nav_menu['nav_menu_locations'])  && @$nav_menu['nav_menu_locations']['secondary'] != 0){
				apply_filters('tmpl_supreme_header_secondary',supreme_header_secondary_navigation()); // Loads the menu-secondary template.
				apply_filters('tmpl_after-header',supreme_sidebar_after_header()); // Loads the sidebar-after-header. 
				$flag = 1;
			}
		}else{
			if(isset($nav_menu['nav_menu_locations'])  && @$nav_menu['nav_menu_locations']['innerpagemenu'] != 0){
				apply_filters('tmpl_supreme_header_secondary',supreme_header_inner_secondary_navigation()); // Loads the menu-secondary template.
				apply_filters('tmpl_after-header',supreme_sidebar_after_header()); // Loads the sidebar-after-header. 
				$flag = 1;
			}
		}
	echo '</div>';
	if($flag == 1){
	}elseif((is_home() || is_front_page()) && is_active_sidebar('mega_menu')){
			if(function_exists('dynamic_sidebar')){
				echo '<div class="nav_bg">
					<div id="menu-mobi-secondary" class="menu-container">
						<nav role="navigation" class="wrap">
						';
							dynamic_sidebar('mega_menu'); // jQuery mega menu
						echo "</nav></div></div>";	
		}
	}elseif(is_active_sidebar('innerpage_mega')){
			if(function_exists('dynamic_sidebar')){
				echo '<div class="nav_bg">
					<div id="menu-mobi-secondary" class="menu-container">
						<nav role="navigation" class="wrap">
						';
							dynamic_sidebar('innerpage_mega'); // jQuery mega menu
						echo "</nav></div></div>";	
		}
	}else{
		if(is_home() || is_front_page()){
			add_action('wp_footer','header_menu_script');
			echo '<div class="nav_bg" id="nav-secondary">
					<div class="menu-container" id="menu-secondary">
						<nav role="navigation" class="wrap">
							<div class="menu">
								<ul class="" id="menu-secondary-items">
									<li class="current-menu-item"><a href="#sec1">Home</a></li>
									<li class=""><a href="#sec2">About Us</a></li>
									<li class=""><a href="#sec3">Services</a></li>
									<li class=""><a href="#sec4">Portfolio</a></li>
									<li class=""><a href="#sec5">Blog</a></li>
									<li class=""><a href="#sec6">Contact Us</a></li>
								</ul>
							</div>';
							apply_filters('supreme-nav-right',dynamic_sidebar('secondary_navigation_right'));
			echo '		</nav>
					</div>
				</div>';
		}else{
			echo '<div class="nav_bg" id="nav-secondary">
					<div class="menu-container" id="menu-secondary">
						<nav role="navigation" class="wrap">
							<div class="menu">
								<ul class="" id="menu-secondary-items">
									<li class="current-menu-item"><a href="'.home_url().'/#sec1">Home</a></li>
									<li class=""><a href="'.home_url().'/#sec2">About Us</a></li>
									<li class=""><a href="'.home_url().'/#sec3">Services</a></li>
									<li class=""><a href="'.home_url().'/#sec4">Portfolio</a></li>
									<li class=""><a href="'.home_url().'/#sec5">Blog</a></li>
									<li class=""><a href="'.home_url().'/#sec6">Contact Us</a></li>
								</ul>
							</div>';
							apply_filters('supreme-nav-right',dynamic_sidebar('secondary_navigation_right'));
			echo '		</nav>
					</div>
				</div>';
		}	
	}
}


/*
Name :onepager_supreme_sticky_secondary_navigation
Description : return secondary navigation menu
*/
function onepager_supreme_sticky_secondary_navigation(){
	$theme_name = get_option('stylesheet');
	$nav_menu = get_option('theme_mods_'.strtolower($theme_name));
	?>
<?php
	echo '<div class="sticky_main" style="display:none">';
	?>
<?php if(supreme_get_settings( 'display_header_text' )){ ?>
<div id="branding1">
  <?php if ( supreme_get_settings( 'supreme_logo_url' ) ) : ?>
	  <h1 id="site-title1"> <a href="<?php echo home_url(); ?>/" title="<?php echo bloginfo( 'name' ); ?>" rel="Home"> <img class="logo" src="<?php echo supreme_get_settings( 'supreme_logo_url' ); ?>" alt="<?php echo bloginfo( 'name' ); ?>" /> </a> </h1>
  <?php else :
			supreme_site_title();
		  endif; 
		if ( !supreme_get_settings( 'supreme_site_description' ) )  : // If hide description setting is un-checked, display the site description. 
			supreme_site_description(); 
	endif; ?>
     
</div>
<!-- #branding -->
<?php } ?>
<?php
	$flag = 0;
	echo '<div id="nav-secondary" class="nav_bg">';	
		if(is_home() || is_front_page()){
			if(isset($nav_menu['nav_menu_locations'])  && @$nav_menu['nav_menu_locations']['secondary'] != 0){
				apply_filters('tmpl_supreme_header_secondary',supreme_header_secondary_navigation()); // Loads the menu-secondary template.
				apply_filters('tmpl_after-header',supreme_sidebar_after_header()); // Loads the sidebar-after-header. 
				$flag = 1;
			}
		}else{
			if(isset($nav_menu['nav_menu_locations'])  && @$nav_menu['nav_menu_locations']['innerpagemenu'] != 0){
				apply_filters('tmpl_supreme_header_secondary',supreme_header_inner_secondary_navigation()); // Loads the menu-secondary template.
				apply_filters('tmpl_after-header',supreme_sidebar_after_header()); // Loads the sidebar-after-header. 
				$flag = 1;
			}
		}	
	echo '</div>';	
	if($flag == 1){
	}elseif((is_home() || is_front_page()) && is_active_sidebar('mega_menu')){
			if(function_exists('dynamic_sidebar')){
				echo '<div class="nav_bg">
					<div id="menu-mobi-secondary" class="menu-container">
						<nav role="navigation" class="wrap">
						';
							dynamic_sidebar('mega_menu'); // jQuery mega menu
						echo "</nav></div></div>";	
		}
	}elseif(is_active_sidebar('innerpage_mega')){
			if(function_exists('dynamic_sidebar')){
				echo '<div class="nav_bg">
					<div id="menu-mobi-secondary" class="menu-container">
						<nav role="navigation" class="wrap">
						';
							dynamic_sidebar('innerpage_mega'); // jQuery mega menu
						echo "</nav></div></div>";	
		}
	}else{
		if(is_home() || is_front_page()){
			add_action('wp_footer','header_menu_script');
			echo '<div class="nav_bg" id="nav-secondary1">
					<div class="menu-container" id="menu-secondary1">
						<nav role="navigation" class="wrap">
							<div class="menu">
								<ul class="" id="menu-secondary-items1">
									<li class=""><a href="#sec1">Home</a></li>
									<li class=""><a href="#sec2">About Us</a></li>
									<li class=""><a href="#sec3">Services</a></li>
									<li class=""><a href="#sec4">Portfolio</a></li>
									<li class=""><a href="#sec5">Blog</a></li>
									<li class=""><a href="#sec6">Contact Us</a></li>
								</ul>
							</div>';
							apply_filters('supreme-nav-right',dynamic_sidebar('secondary_navigation_right'));
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
							apply_filters('supreme-nav-right',dynamic_sidebar('secondary_navigation_right'));
			echo '		</nav>
					</div>
				</div>';
		}
	}
	echo '</div>';
}
function header_menu_script(){?>
	<script type="text/javascript">
		jQuery.noConflict();
		jQuery(document).ready(function(){
			jQuery( '#header' ).localScroll();
		});	
	</script>	
<?php 
}


/**
Name : supreme_header_inner_secondary_navigation
Description : header secondary menu - display below header
**/
if(!function_exists('supreme_header_inner_secondary_navigation')){
	function supreme_header_inner_secondary_navigation(){
		
	if ( has_nav_menu( 'innerpagemenu' ) ) : 
	   
	do_action( 'before_menu_secondary' ); // supreme_before_menu_secondary ?>

	<div id="menu-secondary" class="menu-container">

			<nav role="navigation" class="wrap">
			<!-- #menu-secondary-title -->
			<div id="menu-secondary-title"><?php _e( 'Menu', THEME_DOMAIN ); ?></div>

			<?php do_action( 'open_menu_secondary' ); // supreme_open_menu_secondary ?>

			<?php wp_nav_menu( array( 'theme_location' => 'innerpagemenu', 'container_class' => 'menu', 'menu_class' => '', 'menu_id' => 'menu-secondary-items', 'fallback_cb' => '' ) ); ?>

			<?php do_action( 'close_menu_secondary' ); // supreme_close_menu_secondary 
			
			apply_filters('supreme-nav-right',dynamic_sidebar('secondary_navigation_right')); ?>

			</nav>

	</div><!-- #menu-secondary .menu-container -->

	<?php do_action( 'after_menu_secondary' ); // supreme_after_menu_secondary 
	endif; 
	}
}

/**
Name : supreme_header_inner_secondary_mobile_navigation
Description : header secondary menu - display below header
**/

function supreme_header_inner_secondary_mobile_navigation(){
	
if ( has_nav_menu( 'innerpagemenu' ) ) : 
   
   do_action( 'before_menu_secondary' ); // supreme_before_menu_secondary ?>

	<div id="menu-mobi-secondary" class="menu-container">

		<nav role="navigation" class="wrap">
		<div id="menu-mobi-secondary-title"><?php _e( 'Menu', THEME_DOMAIN ); ?></div><!-- #menu-secondary-title -->
			<?php do_action( 'open_menu_secondary' ); // supreme_open_menu_secondary ?>
			
			<?php wp_nav_menu( array( 'theme_location' => 'innerpagemenu', 'container_class' => 'menu', 'menu_class' => '', 'menu_id' => 'menu-mobi-secondary-items', 'fallback_cb' => '' ) ); ?>

			<?php do_action( 'close_menu_secondary' ); // supreme_close_menu_secondary  ?>
		
		</nav>

	</div><!-- #menu-secondary .menu-container -->

	<?php do_action( 'after_menu_secondary' ); // supreme_after_menu_secondary 
	endif; 
}
?>