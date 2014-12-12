<?php
/*
Template Name: Sitemap Page
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
	get_header(); // Loads the header.php template.
}
do_action( 'before_content' ); // rainbow_before_content

$supreme2_theme_settings = get_option(supreme_prefix().'_theme_settings');
	if ( current_theme_supports( 'breadcrumb-trail' ) && $supreme2_theme_settings['supreme_show_breadcrumb'] == 1 ) breadcrumb_trail( array( 'separator' => '&raquo;' ) ); ?>
<section class="content" id="content">
	<?php do_action( 'open_content' ); // rainbow_open_content ?>
	<div class="hfeed">
		<?php 
			get_template_part( 'loop-meta' ); // Loads the loop-meta.php template.
			apply_filters('tmpl_before-content',supreme_sidebar_before_content() ); // Loads the sidebar-before-content. 
		?>
<!--  CONTENT AREA START -->
	<div class="entry sitemap">
    <h1 class="loop-title"><?php the_title(); ?></h1>
    <div class="loop-description">
		<?php 
          $content = $post->post_content;
          $content = apply_filters('the_content', $content);	
          echo $content;
          ?>  
     </div><!-- .entry-content -->
	 <?php 
		$args = array('title_li' => '', 'echo' => 0 );
		$WPLisPages = new WP_Query('showposts=60&post_type=page');
		if( count(@$WPLisPages->posts) > 0 ){
	 ?>
      <div class="arclist">
        <h2><?php _e('Pages',THEME_DOMAIN);?></h2>
        <ul class="sitemap">
          <?php wp_list_pages('title_li='); ?>
        </ul>
      </div>
	  <?php } 
			$archive_query = new WP_Query('showposts=60&post_type=post');
			if( count(@$archive_query->posts) > 0 ){
		?>	
			  <div class="arclist">
				
				<h2><?php _e('Posts',THEME_DOMAIN);?></span></h2>

				<ul>
				  <?php 
						while ($archive_query->have_posts()) : $archive_query->the_post(); ?>
					  <li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>">
						<?php the_title(); ?>
						</a> <span class="arclist_comment">
						<?php comments_number(__('0 comment',THEME_DOMAIN), __('1 comment',THEME_DOMAIN),__('% comments',THEME_DOMAIN)); ?>
						</span></li>
					  <?php 
						endwhile;wp_reset_query(); 
				  ?>
				</ul>
			  </div>
		<?php } ?>	  
	  <!--/arclist -->
      <!--/arclist -->
	  <?php 
		$WPListCategories = wp_list_categories('title_li=&hierarchical=0&show_count=0&taxonomy=category&echo=0');
		if(($WPListCategories) && $WPListCategories!="No categories" && $WPListCategories!="<li>No categories</li>"){
	  ?>
      <div class="arclist">
        
        <h2><?php _e('Post Categories',THEME_DOMAIN);?></h2>
        	
        <ul>
          <?php 
			echo $WPListCategories;
		  ?>
        </ul>
      </div>	     
	<?php 
		}
		$post_types=get_post_types();
		foreach($post_types as $post_type):		
			if($post_type!='post' && $post_type!='page' && $post_type!="attachment" && $post_type!="revision" && $post_type!="nav_menu_item" && $post_type!="booking"):
			$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));	
			$archive_query = new WP_Query('showposts=60&post_type='.$post_type);
			if( count(@$archive_query->posts) > 0 ){
				$PostTypeObject = get_post_type_object($post_type);
				$PostTypeName = $PostTypeObject->labels->name;
				$post_type_title = ucfirst($PostTypeName);
				if(function_exists('icl_register_string')){
					icl_register_string(THEME_DOMAIN,$post_type_title ,$post_type_title );
				}
				
				if(function_exists('icl_t')){
					$post_title1 = icl_t(THEME_DOMAIN,$post_type_title,$post_type_title);
				}else{
					$post_title1 = sprintf(__('%s',THEME_DOMAIN),$post_type_title);
				}
				?>
	   
       <div class="arclist">
            
            <h2><?php echo $post_title1;?></h2>
                
            <ul>
          <?php 
            while ($archive_query->have_posts()) : $archive_query->the_post(); ?>
          <li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>">
            <?php the_title(); ?>
            </a> <span class="arclist_comment">
            <?php comments_number(__('0 comment',THEME_DOMAIN), __('1 comment',THEME_DOMAIN),__('% comments',THEME_DOMAIN)); ?>
            </span></li>
          <?php endwhile; wp_reset_query(); ?>
        </ul>
      </div>
	  
	  <?php } ?>
       <!--/arclist -->
	  <?php 
		if( is_plugin_active('woocommerce/woocommerce.php') && "product" == $post_type ){
			$taxonomies[0] = $taxonomies[1];
		}
		$WPListCustomCategories = wp_list_categories('title_li=&hierarchical=0&show_count=0&echo=0&taxonomy='.$taxonomies[0]);
		if(($WPListCustomCategories) && $WPListCustomCategories!="No categories" && $WPListCustomCategories!="<li>No categories</li>"){ 
			$post_categories_title = ucfirst($PostTypeName).' '.'Categories';
			 if(function_exists('icl_register_string')){
				icl_register_string(THEME_DOMAIN,$post_categories_title,$post_categories_title);
			}
			
			if(function_exists('icl_t')){
				$post_description1 = icl_t(THEME_DOMAIN,$post_categories_title,$post_categories_title);
			}else{
				$post_description1 = sprintf(__('%s',THEME_DOMAIN),$post_categories_title);
			}
		?> 
      <div class="arclist">
        
        <h2><?php echo $post_description1;?></h2>
		<ul>
          <?php echo $WPListCustomCategories; ?>
        </ul>
      </div>
      <?php }
	endif;
	endforeach;?>      

	<?php 
		$WPListArchives = wp_get_archives('type=monthly&echo=0');
		if(($WPListArchives)){
	?> 
	<div class="arclist">
        <h2><?php _e('Archives',THEME_DOMAIN);?></h2>

        <ul>
          <?php echo $WPListArchives; ?>
        </ul>
	</div>
	<?php } ?>  
      <!--/arclist -->
    </div>
	<?php apply_filters('tmpl_after-content',supreme_sidebar_after_content()); // afetr-content-sidebar use remove filter to dont display it ?>
		
	</div><!-- .hfeed -->
	
	<?php do_action( 'close_content' ); // rainbow_close_content ?>

</section><!-- #content -->

<?php do_action( 'after_content' ); // rainbow_after_content
if($front_page_id == $post->ID){}else{
	get_sidebar();
}
if($front_page_id == $post->ID){}else{
	get_footer(); // Loads the footer.php template. 
}
?>