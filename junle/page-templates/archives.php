<?php
/*
Template Name: Archives Page
*/
$args = array(
			'post_type' => 'page',
			'meta_key' => '_wp_page_template',
			'meta_value' => 'page-templates/front-page.php'
			);
$page_query = new WP_Query($args);
$front_page_id = $page_query->post->ID;
global $post;
if($front_page_id == $post->ID){}else{
	get_header(); 
}	
	do_action( 'before_content' ); // supreme_before_content
	$supreme2_theme_settings = get_option(supreme_prefix().'_theme_settings');
	if ( current_theme_supports( 'breadcrumb-trail' ) && $supreme2_theme_settings['supreme_show_breadcrumb'] == 1 ) breadcrumb_trail( array( 'separator' => '&raquo;' ) ); 
?>
<!--  CONTENT AREA START -->

<section id="content" class="multiple">
  <?php do_action( 'open_content' ); // supreme_open_content ?>
  <div class="hfeed">
    <h1 class="loop-title"><?php the_title(); ?></h1>
    <div class="loop-description">
		<?php 
          $content = $post->post_content;
          $content = apply_filters('the_content', $content);	
          echo $content;
          ?>  
     </div><!-- .entry-content -->
    
	<?php 
		global $post;
		$archives_post=$post;
		$templatic_catelog_post_type = get_post_meta($post->ID,'template_post_type',true);
		if(isset($templatic_catelog_post_type) && $templatic_catelog_post_type!=""){
			$templatic_catelog_post_type = $templatic_catelog_post_type;
		}else{
			$templatic_catelog_post_type = "post";
		}
		
		$years = $wpdb->get_results("SELECT DISTINCT MONTH(post_date) AS month, YEAR(post_date) as year
			FROM $wpdb->posts WHERE post_status = 'publish' and post_date <= now( ) and 
			post_type = '$templatic_catelog_post_type' ORDER BY post_date DESC");
			if($years)
			{
				foreach($years as $years_obj)
				{
					$year = $years_obj->year;	
					$month = $years_obj->month; ?>
				<?php if($templatic_catelog_post_type != '') {
						query_posts("post_type=$templatic_catelog_post_type&showposts=1000&year=$year&monthnum=$month");
					  } else {
						query_posts("post_type='product'&showposts=1000&year=$year&monthnum=$month");
					  }?>
         		<div class="arclist">  
               
                <h2><?php echo $year; ?> <?php echo  date('F', mktime(0,0,0,$month,1)); ?></h2>
                
               <ul>
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                       <li>
                            <a href="<?php the_permalink() ?>">
                                <?php the_title(); ?>
                            </a><br />
                            <span class="arclist_date">  
                            <?php _e('&nbsp;by&nbsp;',THEME_DOMAIN);?>
                            <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" title="Posts by <?php the_author(); ?>"><?php the_author(); ?></a>
                            <?php _e('&nbsp;on&nbsp;',THEME_DOMAIN);?> 
                            <?php the_time(__(get_option('date_format'),THEME_DOMAIN)) ?> // 
                            <?php comments_popup_link(__('&nbsp;No Comments&nbsp;',THEME_DOMAIN), __('&nbsp;1 Comment&nbsp;',THEME_DOMAIN), __('% Comments',THEME_DOMAIN), '', __('&nbsp;Comments Closed&nbsp;',THEME_DOMAIN)); ?>
                            </span>
                        </li> 
                <?php endwhile; endif; ?>
	          </ul>
            </div>
            <?php
			}
		}
		$post=$archives_post;
	 ?> 
  </div>
  <?php do_action( 'close_content' ); // supreme_close_content ?>
</section>
<?php do_action( 'after_content' ); // supreme_after_content 
	if($front_page_id == $post->ID){}else{
		get_sidebar();
	}
if($front_page_id == $post->ID){}else{
	get_footer(); 
}	
	?>
