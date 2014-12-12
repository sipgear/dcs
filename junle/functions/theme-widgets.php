<?php
/*  Child theme widgets file*/

/* add theme side bars */
global $theme_sidebars;
$theme_sidebars = array(
		'innerpage_mega' => array(
			'name' =>	_x( 'Inner Page Mega Menu Navigation', 'sidebar', THEME_DOMAIN ),
			'description' =>	apply_filters('supreme_innerpage_mega_sidebar_description',__('Load only jquery mega menu in inner pages.',THEME_DOMAIN)),
			'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
			'after_widget' => '</div></div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>'
		),
		'home_after_content2' => array(
			'name' =>	_x( 'Home Page Section 2', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( "Widgets placed here will be visible below the home page content area.", THEME_DOMAIN ),
			'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
			'after_widget' => '</div></div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>'
		),
		'home_after_content3' => array(
			'name' =>	_x( 'Home Page Section 3', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( "Widgets placed here will be visible below the home page after content 1.", THEME_DOMAIN ),
			'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
			'after_widget' => '</div></div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>'
		),
		'home_after_content4' => array(
			'name' =>	_x( 'Home Page Section 4', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( "Widgets placed here will be visible below the home page after content 2.", THEME_DOMAIN ),
			'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
			'after_widget' => '</div></div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>'
		),
		'home_after_content5' => array(
			'name' =>	_x( 'Home Page Section 5', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( "Widgets placed here will be visible below the home page after content 3.", THEME_DOMAIN ),
			'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
			'after_widget' => '</div></div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>'
		),
		'home-page-full-slider' => array(
			'name' =>	_x( 'Home Page Full Slider', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( "Place home page slider widget here , it will be visible after header.", THEME_DOMAIN ),
			'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
			'after_widget' => '</div></div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>'
		),
		'footer2' => array(
			'name' =>	_x( 'After Footer with 3 columns', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( "This area has 3 columns and so widgets placed here will appear in 3 divisions vertically.", THEME_DOMAIN ),
			'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
			'after_widget' => '</div></div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>'
		));


if ( !function_exists('add_footer_widget') ){
	function add_footer_widget()
	{
		//unregister_widget( 'secondary_navigation_right' );
		unregister_widget( 'WP_Widget_Text' );
		unregister_sidebar('entry');
		unregister_sidebar('before-content');
		unregister_sidebar('after-content');
		unregister_sidebar('subsidiary-3c');
		unregister_sidebar('after-singular');
		unregister_sidebar('home-page-banner');
		unregister_sidebar('subsidiary');
		unregister_widget('supreme_advertisements');
	}
}
add_action( 'widgets_init', 'add_footer_widget' , 11 );


/* =============================== Custom List Widget ================================ */

if(!class_exists('theme_aboutus_widget')){
	class theme_aboutus_widget extends WP_Widget {
		function theme_aboutus_widget() {
		//Constructor
			$widget_ops = array('classname' => 'theme_aboutus_widget', 'description' => __('Displays about you and your team with detailed information such as email, designation, fb-link, twitter etc.','templatic') );		
			$this->WP_Widget('theme_aboutus_widget', __('T &rarr; About Team','templatic'), $widget_ops);
		}
		function widget($args, $instance) {
		// prints the widget
			extract($args, EXTR_SKIP);
			$title = empty($instance['title']) ? '' :  $instance['title'];
			$text = empty($instance['text']) ? '' : $instance['text'];
			$at_name = empty($instance['at_name']) ? '' : $instance['at_name'];
			$at_email = empty($instance['at_email']) ? '' : $instance['at_email'];
			$at_photo_link = empty($instance['at_photo_link']) ? '' : $instance['at_photo_link'];
			$at_link = empty($instance['at_link']) ? '' : $instance['at_link'];
			$at_post = empty($instance['at_post']) ? '' : $instance['at_post'];
			$at_fb = empty($instance['at_fb']) ? '' : $instance['at_fb'];
			$at_twitter = empty($instance['at_twitter']) ? '' : $instance['at_twitter'];
			$at_linkedin = empty($instance['at_linkedin']) ? '' : $instance['at_linkedin'];
			echo $before_widget;
			?>
		<div class="ccboxlist">
		<?php if($title){?><h3 class="widget-title "><?php echo sprintf(__('%s',THEME_DOMAIN), $title); ?></h3><?php }?>    
		<?php if($text){?><p><?php echo sprintf(__('%s',THEME_DOMAIN), $text); ?></p><?php }?>   
			<ul class="about_member clearfix">
			<?php
					for($c=0; $c < count($at_name); $c++){
						if($at_name[$c] !='')
						{ 
							if(function_exists('icl_register_string')){
								icl_register_string(THEME_DOMAIN,$at_name[$c],$at_name[$c]);
								$at_name1 = icl_t(THEME_DOMAIN,$at_name[$c],$at_name[$c]);
								icl_register_string(THEME_DOMAIN,$at_post[$c],$at_post[$c]);
								$at_post1 = icl_t(THEME_DOMAIN,$at_post[$c],$at_post[$c]);
							}else{
								$at_name1 = __($at_name[$c],THEME_DOMAIN);
								$at_post1 = __($at_post[$c],THEME_DOMAIN);
							}
						?>
						  <li>
										<div class="hover">
											<?php
												if( @$at_email[$c]!="" ){
													echo get_avatar( $at_email[$c], 200, @$default, @$alt ); 
												}elseif( @$at_photo_link[$c]!="" ){	?>
													<img class="avatar avatar-200 photo avatar-default" src="<?php echo $at_photo_link[$c];?>" alt="<?php echo  @$at_name1;?>">
											<?php
												}else{
													echo '<img class="avatar avatar-200 photo avatar-default" src="'.get_stylesheet_directory_uri().'/images/default_grav.png" alt="'. @$at_name1 .'">';
												}
											?>
											<span class="hov">
												<a class="fb_link" href="<?php echo $at_fb[$c]; ?>"><i class="icon-facebook"></i></a>
												<a class="twitter_link" href="<?php echo $at_twitter[$c]; ?>"><i class="icon-twitter"></i></a>
												<a class="linkedin_link" href="<?php echo $at_linkedin[$c]; ?>"><i class="icon-linkedin"></i></a>
											</span>
										</div>
										<div class="details">
											<span class="bg"></span>
											<h4><?php echo $at_name1; ?></h4>
											<span><?php echo $at_post1; ?></span>
										</div>
								</li>
						<?php 	}
					}
			?>
		</ul>
		</div>
		<?php
		echo $after_widget;
		}
		function update($new_instance, $old_instance) {
		//save the widget
			return $new_instance;
		}
		function form($instance) {
		//widgetform in backend
			$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'at_name'=>'', 'at_email' => '','at_photo_link' => '','at_post' => '','at_fb'=>'','at_twitter'=>'','at_linkedin'=>'') );
			
			global $at_name,$at_fb,$at_twitter,$at_linkedin,$at_email,$at_photo_link,$at_post;
		
			$text =  $instance['text'];
			
			$title =  $instance['title'];
			
			$at_name1 = $instance['at_name'];
			
		    $at_fb1 =  $instance['at_fb'];
		    $at_twitter1 =  $instance['at_twitter'];
		    $at_linkedin1 =  $instance['at_linkedin'];
		    $at_email1 =  $instance['at_email'];
		    $at_photo_link1 =  $instance['at_photo_link'];
	
			$at_post1 =  $instance['at_post'];

		    $text_title =  $this->get_field_name('title');
			$textbox_text=$this->get_field_name('text');
			$at_name = $this->get_field_name('at_name');
			$at_fb = $this->get_field_name('at_fb');
			$at_twitter = $this->get_field_name('at_twitter');
			$at_linkedin = $this->get_field_name('at_linkedin');
			$at_email =  $this->get_field_name('at_email');
			$at_photo_link =  $this->get_field_name('at_photo_link');
			$at_post =  $this->get_field_name('at_post');

		?>
<div id="fGroup" class="fGroup">
  <p>
    <label for="<?php echo $this->get_field_id('title'); ?>">
      <?php _e('Title: ','templatic');?>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $text_title; ?>" type="text" value="<?php echo esc_attr($title); ?>" />
    </label>
  </p>
  
  <p>
    <label for="<?php echo $this->get_field_id('text'); ?>">
      <?php _e('Description:','templatic');?>
      <textarea id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $textbox_text; ?>" class="widefat" ><?php echo $text; ?></textarea>
    </label>
  </p>
  <p><strong><?php _e('Team Members',THEME_DOMAIN); ?></strong></p>
  <p>
    <label for="<?php echo $this->get_field_id('at_name'); ?>">
      <?php _e('Name 1: ',THEME_DOMAIN);?>
      <input class="widefat" id="<?php echo $this->get_field_id('at_name'); ?>" name="<?php echo $at_name; ?>[]" type="text" value="<?php echo esc_attr(@$at_name1[0]); ?>" />
    </label>
  </p>

  <p>
    <label for="<?php echo $this->get_field_id('at_email'); ?>">
      <?php _e('Email-ID 1: ',THEME_DOMAIN);?><small><?php _e("(Used to get photo from gravatar)",THEME_DOMAIN);?></small>
      <input class="widefat" id="<?php echo $this->get_field_id('at_email'); ?>" name="<?php echo $at_email; ?>[]" type="text" value="<?php echo esc_attr(@$at_email1[0]); ?>" />
    </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id('at_photo_link'); ?>">
      <?php echo '<b>';_e("OR",THEME_DOMAIN);echo '</b> ';_e('Link to your photo: ',THEME_DOMAIN);?>
      <input class="widefat" id="<?php echo $this->get_field_id('at_photo_link'); ?>" name="<?php echo $at_photo_link; ?>[]" type="text" value="<?php echo esc_attr(@$at_photo_link1[0]); ?>" />
    </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id('at_post'); ?>">
      <?php _e('Designation 1: ',THEME_DOMAIN);?>
      <input class="widefat" id="<?php echo $this->get_field_id('at_post'); ?>" name="<?php echo $at_post; ?>[]" type="text" value="<?php echo esc_attr($at_post1[0]); ?>" />
    </label>
  </p>
   <p>
    <label for="<?php echo $this->get_field_id('at_fb'); ?>">
      <?php _e('FB Link 1: ',THEME_DOMAIN);?>
      <input class="widefat" id="<?php echo $this->get_field_id('at_fb'); ?>" name="<?php echo $at_fb; ?>[]" type="text" value="<?php echo esc_attr(@$at_fb1[0]); ?>" />
    </label>
  </p>
   <p>
    <label for="<?php echo $this->get_field_id('at_twitter'); ?>">
      <?php _e('Twitter Link 1: ',THEME_DOMAIN);?>
      <input class="widefat" id="<?php echo $this->get_field_id('at_twitter'); ?>" name="<?php echo $at_twitter; ?>[]" type="text" value="<?php echo esc_attr(@$at_twitter1[0]); ?>" />
    </label>
  </p>
   <p>
    <label for="<?php echo $this->get_field_id('at_linkedin'); ?>">
      <?php _e('Linked-In Link 1: ',THEME_DOMAIN);?>
      <input class="widefat" id="<?php echo $this->get_field_id('at_linkedin'); ?>" name="<?php echo $at_linkedin; ?>[]" type="text" value="<?php echo esc_attr(@$at_linkedin1[0]); ?>" />
    </label>
  </p>
  <?php
			for($i=1;$i<count($at_name1);$i++)
				{							
					if($at_name1[$i]!="")
							{
								$j=$i+1;
								echo '<div  class="TextBoxDiv'.$j.'">';
								
								echo '<p>';
								echo '<label>Name: '.$j.': ';
								echo ' <input type="text" class="widefat"  name="'.$at_name.'[]" value="'.esc_attr($at_name1[$i]).'"></label>';
								echo '</label>';
								echo '</p>';
								
								echo '<p>';
								echo '<label>Email-ID '.$j.': <small>(Used to get photo from gravatar)</small>';
								echo ' <input type="text" class="widefat"  name="'.$at_email.'[]" value="'.esc_attr($at_email1[$i]).'"></label>';
								echo '</label>';
								echo '</p>';	
								
								echo '<p>';
								echo '<label><b> OR </b> Link to photo '.$j.': ';
								echo ' <input type="text" class="widefat"  name="'.$at_photo_link.'[]" value="'.esc_attr($at_photo_link1[$i]).'"></label>';
								echo '</label>';
								echo '</p>';								
								
								
								echo '<p>';
								echo '<label>Post '.$j.': ';
								echo ' <input type="text" class="widefat"  name="'.$at_post.'[]" value="'.esc_attr($at_post1[$i]).'"></label>';
								echo '</label>';
								echo '</p>';						
								echo '</div>';
								
								echo '<p>';
								echo '<label>FB Profile Link (including http://) '.$j.': ';
								echo ' <input type="text" class="widefat"  name="'.$at_fb.'[]" value="'.esc_attr($at_fb1[$i]).'"></label>';
								echo '</label>';
								echo '</p>';
								
								echo '<p>';
								echo '<label>Twitter Link (including http://) '.$j.': ';
								echo '<input type="text" class="widefat"  name="'.$at_twitter.'[]" value="'.esc_attr($at_twitter1[$i]).'"></label>';
								echo '</label>';
								echo '</p>';
								
								echo '<p>';
								echo '<label>Linked-In Profile Link(including http://) '.$j.': ';
								echo ' <input type="text" class="widefat"  name="'.$at_linkedin.'[]" value="'.esc_attr($at_linkedin1[$i]).'"></label>';
								echo '</label>';
								echo '</p>';
					}
			}
			?>
		</div>
		<a href="javascript:void(0);" id="addButton" class="addButton"  onclick="add_about_fields('<?php echo $at_name; ?>','<?php echo $at_email; ?>','<?php echo $at_photo_link; ?>','<?php echo $at_post; ?>','<?php echo $at_fb; ?>','<?php echo $at_twitter; ?>','<?php echo $at_linkedin; ?>');">+ Add more</a> &nbsp;&nbsp;
		<a href="javascript:void(0);" id="removeButton" class="removeButton"  onclick="remove_about_fields();">- Remove</a>
		<?php
		}
	}
	register_widget('theme_aboutus_widget');
}

/* =============================== Add fields for custom content box ================================ */

add_action('admin_footer','tmpl_add_script_addnew_');

function tmpl_add_script_addnew_()
{  
	global $at_name,$at_fb,$at_twitter,$at_linkedin,$at_email,$at_photo_link,$at_post;
	?>
	<script type="application/javascript">			
		var aboutcounter =  2;
		function add_about_fields(at_name,at_email,at_photo_link,at_post,at_fb,at_twitter,at_linkedin)
		{
			var newTextBoxDiv = jQuery(document.createElement('div')).attr("class", 'TextBoxDiv' + aboutcounter);
			
			newTextBoxDiv.html('<p><label>Name '+ aboutcounter +' </label>'+'<input type="text" class="widefat" name="'+at_name+'[]" id="textbox' + aboutcounter + '" value="" ></p>');	
			
			newTextBoxDiv.append('<p><label>Email-ID '+ aboutcounter +': </label>'+'<input type="text" class="widefat" name="'+at_email+'[]" id="textbox' + aboutcounter + '" value="" ></p>');	
			
			newTextBoxDiv.append('<p><label>Email-ID '+ aboutcounter +': </label>'+'<input type="text" class="widefat" name="'+at_photo_link+'[]" id="textbox' + aboutcounter + '" value="" ></p>');	
						  
			newTextBoxDiv.append('<p><label>Designation '+ aboutcounter + ': </label>'+'<input type="text" class="widefat" name="'+at_post+'[]" id="textbox' + aboutcounter + '" value="" ></p>');		

			newTextBoxDiv.append('<p><label>FB Profile Link '+ aboutcounter +' (including http://) : </label>'+'<input type="text" class="widefat" name="'+at_fb+'[]" id="textbox' + aboutcounter + '" value="" ></p>');	
					  
			newTextBoxDiv.append('<p><label>Twitter Link '+ aboutcounter +' (including http://)  </label>'+'<input type="text" class="widefat" name="'+at_twitter+'[]" id="textbox' + aboutcounter + '" value="" ></p>');	
					  
			newTextBoxDiv.append('<p><label>Linked-In Profile Link '+ aboutcounter +' (including http://)  </label>'+'<input type="text" class="widefat" name="'+at_linkedin+'[]" id="textbox' + aboutcounter + '" value="" ></p>');	
					  
			
			newTextBoxDiv.appendTo(".fGroup");
				
		    aboutcounter++;
		}
		function remove_about_fields()
		{
		    if(aboutcounter-1==1){
			   alert("you need one textbox required.");
			   return false;
		    }
		    aboutcounter--;						
		    jQuery(".TextBoxDiv" + aboutcounter).remove();
		}
	</script>
<?php
}

/* =============================== Services Widget ================================ */

if(!class_exists('theme_services_list')){
	class theme_services_list extends WP_Widget {
		function theme_services_list() {
		//Constructor
			$widget_ops = array('classname' => 'theme_services_list', 'description' => __('Displays list of services which is provided by you or your team on home page.',THEME_DOMAIN) );		
			$this->WP_Widget('theme_services_list', __('T &rarr; Services'), $widget_ops);
		}
		function widget($args, $instance) {
		// prints the widget
			extract($args, EXTR_SKIP);
			$title = empty($instance['title']) ? '' :  $instance['title'];
			$desc = empty($instance['desc']) ? '' :  $instance['desc'];
			$text = empty($instance['text']) ? '' : $instance['text'];
			$link_desc = empty($instance['link_desc']) ? '' : $instance['link_desc'];
			$link_text = empty($instance['link_text']) ? '' : $instance['link_text'];
			$link_url = empty($instance['link_url']) ? '' : $instance['link_url'];
			$view_text = empty($instance['view_text']) ? 'VIEW DETAILS' : $instance['view_text'];
			
			?>
				<div class="ccboxlist">
			<?php echo $before_widget; ?>
			  <h3 class="widget-title"><?php echo $title; ?></h3>
			  <p><?php echo $desc; ?></p>
			  <ul>
			  <?php
				for($c=0; $c < count($link_text); $c++){
					if($link_text[$c] !=''){ 
						if(function_exists('icl_register_string')){
							icl_register_string(THEME_DOMAIN,$link_text[$c],$link_text[$c]);
							$link_text1 = icl_t(THEME_DOMAIN,$link_text[$c],$link_text[$c]);
							if(icl_register_string(THEME_DOMAIN,$link_desc[$c],$link_desc[$c])){
								$link_desc1 = icl_t(THEME_DOMAIN,$link_desc[$c],$link_desc[$c]);
							}else{
								$link_desc1 = __($link_desc[$c],THEME_DOMAIN);
							}
							icl_register_string(THEME_DOMAIN,$view_text[$c],$view_text[$c]);
							$view_text1 = icl_t(THEME_DOMAIN,$view_text,$view_text);
						}else{
							$link_text1 = __($link_text[$c],THEME_DOMAIN);
							$link_desc1 = __($link_desc[$c],THEME_DOMAIN);
							$view_text1 = __($view_text,THEME_DOMAIN);
						}
					?>
					
					<li class="services_section">
						<h4><a href="<?php echo $link_url[$c]; ?>" class="listcont clearfix"><?php echo $link_text1; ?></a></h4>
						<p><?php echo $link_desc1; ?></p>
						<a class="readmore" href="<?php echo $link_url[$c]; ?>"><?php echo $view_text1; ?></a>
					</li>

			<?php 	}
				}?>
			</ul>
			<?php echo $after_widget; ?>

			</div>
			<?php
		}
		function update($new_instance, $old_instance) {
		//save the widget

			return $new_instance;
		}
		function form($instance) {
		//widgetform in backend
			$instance = wp_parse_args( (array) $instance, array( 'title' => '','desc' => '', 'text' => '', 'link_desc'=>'', 'link_text' => '','link_url' => '','view_text'=>'' ) );
			$title =  strip_tags(stripslashes($instance['title']));
			$desc =  strip_tags(stripslashes($instance['desc']));
			$link_text1 = $instance['link_text'];
			
			$link_url1 =  $instance['link_url'];
			$link_desc1 =  $instance['link_desc'];
			$view_text1 =  $instance['view_text'];
			
			global $link_text,$link_desc,$link_url,$view_text;
			
			$link_text=$this->get_field_name('link_text');
			$textbox_image=$this->get_field_name('image');
			$link_url =  $this->get_field_name('link_url');
			$link_desc =  $this->get_field_name('link_desc');
			$view_text =  $this->get_field_name('view_text');
		
		?>
		<div id="fGroup2" class="fGroup2">
		  <p>
			<label for="<?php echo $this->get_field_id('title'); ?>">
			  <?php _e('Title :','templatic');?>
			  <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" PLACEHOLDER="Enter widget title" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
			</label>
		  </p> 

		   <p>
			<label for="<?php echo $this->get_field_id('desc'); ?>">
			  <?php _e('Description : ','templatic');?>
			  <textarea class="widefat" id="<?php echo $this->get_field_id('desc'); ?>" PLACEHOLDER="Summarize  your services"  name="<?php echo $this->get_field_name('desc'); ?>" ><?php echo esc_attr($desc); ?></textarea>
			</label>
		  </p> 
		  
		  <p>
			<label for="<?php echo $this->get_field_id('view_text'); ?>">
			  <?php _e('Read More Text: ','templatic');?>
			  <input class="widefat" id="<?php echo $this->get_field_id('view_text'); ?>" PLACEHOLDER="VIEW DETAILS"  name="<?php echo $this->get_field_name('view_text'); ?>" type="text" value="<?php echo esc_attr($view_text1); ?>" />
			</label>
		  </p>
		
		<p><strong>Services Type</strong></p>
		  
		  <p>
			<label for="<?php echo $this->get_field_id('link_text'); ?>">
			  <?php _e('Link Text 1:',THEME_DOMAIN);?> 
			  <input class="widefat" id="<?php echo $this->get_field_id('link_text'); ?>" PLACEHOLDER="Define your service" name="<?php echo $link_text; ?>[]" type="text" value="<?php echo esc_attr(@$link_text1[0]); ?>" />
			</label>
		  </p>
		  <p>
			<label for="<?php echo $this->get_field_id('link_desc'); ?>">
			  <?php _e('Link Description 1:',THEME_DOMAIN);?>
			  <textarea class="widefat" id="<?php echo $this->get_field_id('link_desc'); ?>" PLACEHOLDER="Describe your service" name="<?php echo $link_desc; ?>[]"><?php echo esc_attr(@$link_desc1[0]); ?></textarea>
			</label>
		  </p>  
		  
		  <p>
			<label for="<?php echo $this->get_field_id('link_url'); ?>">
			  <?php _e('Link Url 1:',THEME_DOMAIN);?>
			  <input class="widefat" id="<?php echo $this->get_field_id('link_url'); ?>" PLACEHOLDER="Paste URL of detail description page" name="<?php echo $link_url; ?>[]" type="text" value="<?php echo esc_attr(@$link_url1[0]); ?>" />
			</label>
		  </p>
			<?php 
			for($i=1;$i<count($link_text1);$i++)
				{							
					if($link_text1[$i]!="")
							{
								$j=$i+1;
								echo '<div  class="spacing TextBoxDiv'.$j.'">';
								
								
								
								echo '<p>';
								echo '<label>Link Text: '.$j;
								echo ' <input type="text" class="widefat"  name="'.$link_text.'[]" value="'.esc_attr($link_text1[$i]).'"></label>';
								echo '</label>';
								echo '</p>';
								
								echo '<p>';
								echo '<label>Link Description: '.$j;
								echo ' <textarea type="text" class="widefat"  name="'.$link_desc.'[]" >'.esc_attr($link_desc1[$i]).'</textarea></label>';
								echo '</label>';
								echo '</p>';
								
								echo '<p>';
								echo '<label>Link Url '.$j;
								echo ' <input type="text" class="widefat"  name="'.$link_url.'[]" value="'.esc_attr($link_url1[$i]).'"></label>';
								echo '</label>';
								echo '</p>';							
								echo '</div>';
					}
			}
			?>
</div>
<a href="javascript:void(0);" id="addButton" class="addButton"  onclick="add_services_fields('<?php echo $link_desc; ?>','<?php echo $link_text; ?>','<?php echo $link_url; ?>');">+ Add more</a> &nbsp;&nbsp;
<a href="javascript:void(0);" id="removeButton" class="removeButton"  onclick="remove_services_fields();">- Remove</a>

<?php
		}
	}
	register_widget('theme_services_list');
}

add_action('admin_head','tmpl_add_services_script');

function tmpl_add_services_script()
{
	global $textbox_title,$link_text,$textbox_image,$link_url;
	?>
<script type="application/javascript">			
		var servicescounter = 2;
		function add_services_fields(ldesc,tname,lurl)
		{
			var newTextBoxDiv = jQuery(document.createElement('div')).attr("class", 'TextBoxDiv' + servicescounter);
			newTextBoxDiv.append('<p><label>Link Text '+ servicescounter + '</label>'+'<input type="text" class="widefat" name="'+tname+'[]" id="textbox' + servicescounter + '" value="" ></p>');
			newTextBoxDiv.append('<p><label>Link Description '+ servicescounter +'</label>'+'<textarea type="text" class="widefat" name="'+ldesc+'[]" id="textbox' + servicescounter + '"></textarea></p>');				  
			newTextBoxDiv.append('<p><label>Link Url '+ servicescounter + '</label>'+'<input type="text" class="widefat" name="'+lurl+'[]" id="textbox' + servicescounter + '" value="" ></p>');			  
			newTextBoxDiv.appendTo(".fGroup2");
		    servicescounter++;
		}
		function remove_services_fields()
		{
		    if(servicescounter-1==1){
			   alert("you need one textbox required.");
			   return false;
		    }
		    servicescounter--;						
		    jQuery(".TextBoxDiv" + servicescounter).remove();
		}
	</script>
<?php
}

/* =============================== Services Widget End ================================ */


/*=============================== jQuery Portfolio WIdget =======================*/

if(!class_exists('tmpl_jquery_post_listing')){
class tmpl_jquery_post_listing extends WP_Widget {
         public function tmpl_jquery_post_listing() {
               $widget_ops = array('classname' => 'tmpl_jquery_post_listing', 'description' => __('Display Post listing on home page using jquery.','templatic') );		
			   $this->WP_Widget('tmpl_jquery_post_listing', __('T &rarr; Portfolio','templatic'), $widget_ops);
        }

        public function form( $instance ) {

				
		//widgetform in backend
		$instance = wp_parse_args( (array)$instance, array(
			'title' => '',
			'post_type'=>'',
			'post_number' => 0,			
		) );	?>
	<p>
	  <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:',THEME_DOMAIN);?>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
	  </label>
	</p>
     <p>
    	<label for="<?php echo $this->get_field_id('post_type');?>" ><?php _e('Select Post type:',THEME_DOMAIN);?>    	
    	<select  id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" class="widefat">        	
    <?php
		$all_post_types = get_post_types();
		foreach($all_post_types as $post_types){
			if( $post_types != "page" && $post_types != "attachment" && $post_types != "revision" && $post_types != "nav_menu_item" ){
				?>
                	<option value="<?php echo $post_types;?>" <?php if($post_types== $instance['post_type'])echo "selected";?>><?php echo esc_attr($post_types);?></option>
                <?php				
			}
		}
	?>	
    	</select>
    </label>
    </p>
	<p>
		  <label for="<?php echo $this->get_field_id('post_number'); ?>"><?php _e('Number of posts:',THEME_DOMAIN);?>
		  <input class="widefat" id="<?php echo $this->get_field_id('post_number'); ?>" name="<?php echo $this->get_field_name('post_number'); ?>" type="text" value="<?php echo $instance['post_number']; ?>" />
		  </label>
		</p>	
		
		<?php
        }

        public function update( $new_instance, $old_instance ) {
               // processes widget options to be saved
			   return $new_instance;
        }

        public function widget( $args, $instance ) { 
			// prints the widget
		?>
		<script type="text/javascript" src="<?php echo get_template_directory_uri();?>/js/jquery.isotope.min.js"></script>
		<script type="text/javascript" src="<?php echo get_template_directory_uri();?>/js/jquery.prettyPhoto.js"></script>
		<div class="widget widget-portfolio">
		<div class="widget-wrap">
		<?php

		add_action('wp_footer','isoscript')	;
		extract($args, EXTR_SKIP);
		// defaults
			$instance = wp_parse_args( (array)$instance, array(
			'title' => '',
			'post_type'=>'',
			'post_number' => 0,			
			) );
		// Set up the author bio
		if (!empty($instance['title']))
			echo $before_title . apply_filters('widget_title', $instance['title']) . $after_title;
		
		remove_all_actions('posts_where');	
		$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $instance['post_type'],'public'   => true, '_builtin' => true ));	
		

		if(is_plugin_active('woocommerce/woocommerce.php') && $instance['post_type'] == 'product'){
			$taxonomies[0] = $taxonomies[1];
		}
		$featured_arg=array('post_type' => $instance['post_type'], 'showposts' => $instance['post_number'], 'post_status' => 'publish');
		
		remove_all_actions('posts_orderby');
		//add_filter('posts_join', 'templatic_posts_where_filter');
		if(is_plugin_active('wpml-translation-management/plugin.php') && function_exists('templatic_widget_wpml_filter')){
			add_filter('posts_where','templatic_widget_wpml_filter');
		}
		$featured_posts = new WP_Query($featured_arg);
		
		if(function_exists('templatic_widget_wpml_filter')){
			remove_filter('posts_where','templatic_widget_wpml_filter');
		}	
		//echo $featured_posts->request;
	
		?>

<!-- DEMO -->
				<nav class="primary">
					<?php 
					$taxonomy = get_object_taxonomies($instance['post_type']);
					$terms = get_terms($taxonomy[0]);
					$count = count($terms);
					if ( $count > 0 ){
						$c=0;
						echo "<ul>";
						echo '<li><a href="#" class="selected" data-filter="*">'.__("All",THEME_DOMAIN).'</a></li>';
						foreach ( $terms as $term ) {
							$c++;
							if($c == 1){ $class="active"; }else{ $class= ""; }
							echo "<li><a href='#' data-filter='.".$term->slug."' class='".$class."'>".$term->name . "</a></li>";
							
					
						}
						echo "</ul>";
					}?>
    </nav>
			
			<div class="portfolio clearfix">
				<?php
									
				if($featured_posts->have_posts()) : 
				$count=0;
				$incr=1;
				
				while($featured_posts->have_posts()) : $featured_posts->the_post();
					global $post;
					if( $taxonomies[0] !='category'){
						$cats = wp_get_object_terms( $post->ID, $taxonomies[0], array('fields'=>'all'));
					}else{
						$cats = wp_get_post_categories( $post->ID,array('fields'=>'all'));
					}
					$sep='$nbsp;';
					$cats1 = '';
					for($c=0; $c<= count($cats); $c++){ 
						if( @$cats[$c]->slug !=''){
							if($c == (count($cats[$c]->slug) -1 ))
								$sep = ' ';
							@$cats1 .= $cats[$c]->slug.$sep;
						}
					}
						echo '<div class="entry '.$cats1.'">';		
							
							/* show post title*/
							
							echo get_the_image(array('data-rel'=>'prettyPhoto','data-gal'=>'23','link_class'=>' ','size'=> 'portfolio-thumbnail','default_image'=>get_template_directory_uri()."/images/noimage.jpg")); 
							$large_images = supreme_get_images($post->ID,'large');
							$gallery_image = "";
							$gallery_title = "";
							$gallery_title = "";
							for($i=0;$i<count($large_images);$i++){
								$gallery_image.="'".$large_images[$i]['file']."',";	
								$gallery_title.="'".get_the_title($post->ID)."',";
							}
							$gallery_image=substr($gallery_image,0,-1);	
							$gallery_title=substr($gallery_title,0,-1);	
							
							?>
							<script type="text/javascript">
								 
								api_gallery<?php echo $incr;?>=[<?php echo $gallery_image;?>];
								api_titles<?php echo $incr;?>=[<?php echo $gallery_title;?>];
								api_descriptions<?php echo $incr;?>=[];
								jQuery.noConflict();
								jQuery(document).ready(function(){
								  jQuery.fn.prettyPhoto({animationSpeed:'slow',slideshow:false,overlay_gallery: false,social_tools:false,deeplinking:false}); 
								});
							</script>
							<div class="video-hover">	
								<h4><a href="<?php echo get_permalink();?>"><?php the_title(); ?></a></h4>
								<div class="link_incons">
								<span class="portfolio_link"><a href="<?php echo get_permalink();?>"><i class="icon-link"></i></a></span>
								<span class="portfolio_pop"><a href="javascript:void(0);" onClick="jQuery.prettyPhoto.open(api_gallery<?php echo $incr;?>,api_titles<?php echo $incr;?>,api_descriptions<?php echo $incr;?>); return false" rel="prettyPhoto"><i class="icon-search"></i></a></span>
								</div>
							</div>
							<?php
						echo "</div>";
						$incr++;
				endwhile; wp_reset_query();
				endif; ?>
			</div>
			</div>
			</div>       
        <?php }
}
	register_widget('tmpl_jquery_post_listing');
}

function isoscript(){
	?>
	<script type="text/javascript">
	//Portfolio
		var $container = jQuery('.portfolio');
		$container.isotope({
			filter: '*',
			animationOptions: {
				duration: 750,
				easing: 'linear',
				queue: false,
			}
		});

		jQuery('nav.primary ul a').click(function(){
			var selector = jQuery(this).attr('data-filter');
			$container.isotope({
				filter: selector,
				animationOptions: {
					duration: 750,
					easing: 'linear',
					queue: true,
				}
			});
		  return false;
		});

		var $optionSets = jQuery('nav.primary ul'),
			   $optionLinks = $optionSets.find('a');
		 
			   $optionLinks.click(function(){
				  var $this = jQuery(this);
			  // don't proceed if already selected
			  if ( $this.hasClass('selected') ) {
				  return false;
			  }
		   var $optionSet = $this.parents('nav.primary ul');
		   $optionSet.find('.selected').removeClass('selected');
		   $this.addClass('selected'); 
		});
	</script>
	<?php
}


if(!class_exists('theme_supreme_advertisements')){
	class theme_supreme_advertisements extends WP_Widget {
		function theme_supreme_advertisements() {
		//Constructor
			$widget_ops = array('classname' => 'widget Templatic Advertisements templatic_advertisement', 'description' => __('Show the advertisements. here You can paste HTML, JavaScript, an IFrame into this widget.you can place this widget in home page content area, footer and sidebar area.',THEME_DOMAIN) );
			$this->WP_Widget('theme_supreme_advertisements', __('T &rarr; Advertisements',THEME_DOMAIN), $widget_ops);
		}
		function widget($args, $instance) {
		// prints the widget

			extract($args, EXTR_SKIP);
			
			$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
			$img_url = empty($instance['img_url']) ? get_template_directory_uri().'/images/dummy/addbg.jpg' : apply_filters('widget_img_url', $instance['img_url']);
			$ads = empty($instance['ads']) ? '' : $instance['ads'];
			echo $before_widget;
			if ( $title <> "" ) { 
				echo ' <h3 class="widget-title">'.$title.'</h3>';
			}
			 ?>
			 <style type="text/css" >
				.templatic_advertisement {
					background: url("<?php echo $img_url;?>") no-repeat center center;
				}
			</style>
				<!-- Display advertisment -->
				<div class="advertisements">
			
					<?php echo $ads; ?>
			
				</div>
			<?php
			
			echo $after_widget;		
		}
		
		function update($new_instance, $old_instance) {
		//save the widget
			$instance = $old_instance;		
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['img_url'] = strip_tags($new_instance['img_url']);
			$instance['ads'] = $new_instance['ads'];
			return $instance;
		}

		function form($instance) {
		//widgetform in backend
			$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'ads' => '', 'img_url' => '') );		
			$title = strip_tags($instance['title']);
			$img_url = strip_tags($instance['img_url']);
			$ads = ($instance['ads']);
		?>
		<p>
			<label for="<?php  echo $this->get_field_id('title'); ?>"><?php _e('Title',THEME_DOMAIN);?>: 
			<input class="widefat" id="<?php  echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label>
		</p>  
		<p>
			<label for="<?php  echo $this->get_field_id('img_url'); ?>"><?php _e('Background image url',THEME_DOMAIN);?>: 
			<input class="widefat" id="<?php  echo $this->get_field_id('img_url'); ?>" name="<?php echo $this->get_field_name('img_url'); ?>" type="text" value="<?php echo esc_attr($img_url); ?>" /></label>
		</p>     
		<p>
			<label for="<?php echo $this->get_field_id('ads'); ?>">
				<?php _e('Advertisement code <small>(ex.&lt;a href="#"&gt;&lt;img src="http://templatic.com/banner.png" /&gt;&lt;/a&gt; and google ads code here )</small>',THEME_DOMAIN);?>: 
				<textarea class="widefat" rows="6" cols="20" id="<?php echo $this->get_field_id('ads'); ?>" name="<?php echo $this->get_field_name('ads'); ?>"><?php echo $ads; ?></textarea>
			</label>
		</p>
		<?php
		}
	}
	register_widget('theme_supreme_advertisements');
}
?>
