<?php
/*
 * Create the templatic facebook post widget
 */
	
class supreme_facebook extends WP_Widget {
	function supreme_facebook() {
		//Constructor
		$widget_ops = array('classname' => 'widget Templatic facebook fans t_facebook_fans', 'description' => __('Show your facebook fans on your site.',THEME_DOMAIN) );
		$this->WP_Widget('supreme_facebook', __('T &rarr; Facebook Fans',THEME_DOMAIN), $widget_ops);
	}
	
	function widget($args, $instance) {
		// prints the widget
			extract($args, EXTR_SKIP);
			echo $before_widget;
			$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
			$facebook_page_url = empty($instance['facebook_page_url']) ? '' : apply_filters('widget_facebook_page_url', $instance['facebook_page_url']);
			$width = empty($instance['width']) ? '' : apply_filters('widget_width', $instance['width']);
			$show_faces = empty($instance['show_faces']) ? '' : apply_filters('widget_show_faces', $instance['show_faces']);
			$show_stream = empty($instance['show_stream']) ? '' : apply_filters('widget_show_stream', $instance['show_stream']);
			$show_header = empty($instance['show_header']) ? '' : apply_filters('widget_show_header', $instance['show_header']);
			
			echo $before_title;
			echo $title;
			echo $after_title;
			
			if($show_faces == 1) $face='true'; else $face='false';
			if($show_stream == 1) $stream='true'; else $stream='false';
			if($show_header == 1) $header='true'; else $header='false';
			?>		 
			<div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like-box href="<?php echo $facebook_page_url; ?>" width="<?php echo $width; ?>" show_faces="<?php echo $face; ?>" border_color="" stream="<?php echo $stream; ?>" header="<?php echo $header; ?>"></fb:like-box>
         
		<?php
		echo $after_widget;		
	}
	function update($new_instance, $old_instance) {
		//save the widget
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['facebook_page_url'] = strip_tags($new_instance['facebook_page_url']);
		$instance['width'] = strip_tags($new_instance['width']);
		$instance['show_faces'] = strip_tags($new_instance['show_faces']);
		$instance['show_stream'] = strip_tags($new_instance['show_stream']);
		$instance['show_header'] = strip_tags($new_instance['show_header']);			
		return $instance;

	}
	function form($instance) {
		//widgetform in backend
		$instance = wp_parse_args( (array) $instance, array('title'=>'', 'width'=>'', 'facebook_page_url'=>'', 'show_faces'=>'', 'show_stream'=>'', 'show_header'=>'' ) );
			$title = strip_tags($instance['title']);
			$facebook_page_url = strip_tags($instance['facebook_page_url']);
			$width = strip_tags($instance['width']);
			$show_faces = strip_tags($instance['show_faces']);
			$show_stream = strip_tags($instance['show_stream']);
			$show_header = strip_tags($instance['show_header']);
			
	?>
        <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php  _e('Title',THEME_DOMAIN)?>:
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
          </label>
        </p>
		<p>
          <label for="<?php echo $this->get_field_id('facebook_page_url'); ?>"><?php  _e('Facebook Page Full URL',THEME_DOMAIN)?>:
            <input class="widefat" id="<?php echo $this->get_field_id('facebook_page_url'); ?>" name="<?php echo $this->get_field_name('facebook_page_url'); ?>" type="text" value="<?php echo esc_attr($facebook_page_url); ?>" />
          </label>
        </p>        
        <p>
          <label for="<?php echo $this->get_field_id('width'); ?>"><?php  _e('Width',THEME_DOMAIN)?>:
            <input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo esc_attr($width); ?>" />
          </label>
        </p> 
		<p>
		  <label for="<?php echo $this->get_field_id('show_faces'); ?>"><?php  _e('Show Faces',THEME_DOMAIN)?>:
		  <select id="<?php echo $this->get_field_id('show_faces'); ?>" name="<?php echo $this->get_field_name('show_faces'); ?>" style="width:50%;">
			  <option value="1" <?php if(esc_attr($show_faces)=='1'){ echo 'selected="selected"';}?>><?php _e('Yes',THEME_DOMAIN); ?></option>
			  <option value="0" <?php if(esc_attr($show_faces)=='0'){ echo 'selected="selected"';}?>><?php _e('No',THEME_DOMAIN); ?></option>
		  </select>
		  </label>
		</p>		
		<p>
          <label for="<?php echo $this->get_field_id('show_stream'); ?>"><?php  _e('Show Stream',THEME_DOMAIN)?>:
          <select id="<?php echo $this->get_field_id('show_stream'); ?>" name="<?php echo $this->get_field_name('show_stream'); ?>" style="width:50%;">
			  <option value="1" <?php if(esc_attr($show_stream)=='1'){ echo 'selected="selected"';}?>><?php _e('Yes',THEME_DOMAIN); ?></option>
			  <option value="0" <?php if(esc_attr($show_stream)=='0'){ echo 'selected="selected"';}?>><?php _e('No',THEME_DOMAIN); ?></option>
		  </select>
          </label>
        </p>
		<p>
          <label for="<?php echo $this->get_field_id('show_header'); ?>"><?php  _e('Show Header',THEME_DOMAIN)?>:
            <select id="<?php echo $this->get_field_id('show_header'); ?>" name="<?php echo $this->get_field_name('show_header'); ?>" style="width:50%;">
			  <option value="1" <?php if(esc_attr($show_header)=='1'){ echo 'selected="selected"';}?>><?php _e('Yes',THEME_DOMAIN); ?></option>
			  <option value="0" <?php if(esc_attr($show_header)=='0'){ echo 'selected="selected"';}?>><?php _e('No',THEME_DOMAIN); ?></option>
			</select>
          </label>
        </p>
       
	<?php
		
	}
	
}
register_widget('supreme_facebook');

/*
 * templatic templatic facebook widget init
 */

?>