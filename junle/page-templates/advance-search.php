<?php
/*
Template Name: Advance Search
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
$post_type = get_post_meta($post->ID,'template_post_type',true);
?>
<?php $supreme2_theme_settings = get_option(supreme_prefix().'_theme_settings');
	if($front_page_id == $post->ID){}else{
		if ( current_theme_supports( 'breadcrumb-trail' ) && $supreme2_theme_settings['supreme_show_breadcrumb'] == 1 ) breadcrumb_trail( array( 'separator' => '&raquo;' ) ); 
	}?>
<section id="content" class="contentarea">
        <?php do_action( 'open_content' ); // supreme_open_content ?>

		<div class="hfeed">
			
			<?php apply_filters('tmpl_before-content',supreme_sidebar_before_content() ); // Loads the sidebar-before-content.

			if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); 

					do_action( 'before_entry' ); // supreme_before_entry ?>

					<div id="post-<?php the_ID(); ?>" class="<?php supreme_entry_class(); ?>">

						<?php do_action( 'open_entry' ); // supreme_open_entry 
					
							 do_action('entry-title'); ?>

						<div class="entry-content">
							<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', THEME_DOMAIN ) );
							wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', THEME_DOMAIN ), 'after' => '</p>' ) );
							do_action('entry-edit-link'); ?>
						</div><!-- .entry-content -->
						
						<?php apply_filters('supreme_author_biograply',supreme_author_biography_($post));	// show author biography below post

						do_action( 'close_entry' ); // supreme_close_entry ?>

					</div><!-- .hentry -->
			<?php
				endwhile;
			endif; ?>

        <form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" onsubmit="return sformcheck();" class="form_front_style">        
           <div class="form_row clearfix">
			   <label><?php _e('Search',THEME_DOMAIN);?><span class="indicates">*</span></label>
			   <input class="adv_input" name="s" id="adv_s" type="text" PLACEHOLDER="<?php _e('Search',THEME_DOMAIN); ?>" value="" />			  
			   <span class="message_error2"  style="color:red;font-size:12px;" id="search_error"></span>			  
		   </div>
           
		   <div class="form_row clearfix">
			   <label><?php _e('Tags',THEME_DOMAIN);?></label>
			   <input class="adv_input" name="tag_s" id="tag_s" type="text"  PLACEHOLDER="<?php _e('Tags',THEME_DOMAIN); ?>" value=""  />			  
		   </div>
		   <?php 
				$post_type = get_post_meta($post->ID,'template_post_type',true);
				$custom_post_types_args = array();  
				$custom_post_types = get_post_types($custom_post_types_args,'objects');
				foreach ($custom_post_types as $content_type){
					if($content_type->name == $post_type)
					{
						if($content_type->name =='post' || strtolower($content_type->name) ==strtolower('posts')){ 
							$taxonomy='category';
						}else{
							if(isset($content_type->slugs[0]) && $content_type->slugs[0] !='')
							$taxonomy =  $content_type->slugs[0];
						}
					}else{
						if($content_type->name =='post' || strtolower($content_type->name) ==strtolower('posts')){ 
							$taxonomy='category';
						}else{
							if(isset($content_type->slugs[0]) && $content_type->slugs[0] !='')
							$taxonomy =  $content_type->slugs[0];
						}
					}
				}
				$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));
				if($post_type!='post'){	
						if(isset($taxonomies[0]) && $taxonomies[0] !='')
						$taxonomy=$taxonomies[0];
						if(is_plugin_active('woocommerce/woocommerce.php') && $post_type == 'product'){
							$taxonomy = $taxonomies[1];
						}
				}else
					$taxonomy='category';
					
				$categories = get_terms($taxonomy, 'orderby=count&hide_empty=0');				
			?>
		   <div class="form_row clearfix">
			   <label><?php _e('Category',THEME_DOMAIN);?></label>
				<select name="category">
					<option value=""><?php _e("Select Category",THEME_DOMAIN);?></value>
					<?php foreach($categories as $cat_informs){?>
							<option value="<?php echo $cat_informs->term_id;?>"><?php echo $cat_informs->name;?></value>
					<?php }?>		
				</select>
			   <div class="clearfix"></div>
		   </div>
		   <script type="text/javascript">	
				<?php if( get_option('start_of_week') != "" ){ ?>
						var start_day = <?php echo get_option('start_of_week');?>;
				<?php } ?>
				jQuery(function(){
				var pickerOpts = {
					showOn: "button",
					buttonText: '<i class="icon-calendar"></i>'
					<?php if( get_option('start_of_week') != "" ){ ?>
						,firstDay: start_day
					<?php } ?>	
				};	
				jQuery("#todate").datepicker(pickerOpts);
				jQuery("#frmdate").datepicker(pickerOpts);
			});
			</script>
		   <div class="form_row clearfix">
			   <label><?php _e('Date',THEME_DOMAIN);?></label>
			   <input name="todate" id="todate" type="text" size="25" PLACEHOLDER="<?php _e('Start Date',THEME_DOMAIN); ?>"  class="clearfix" />
               <input name="frmdate" id="frmdate" type="text" size="25" PLACEHOLDER="<?php _e('End Date',THEME_DOMAIN); ?>"   class="clearfix"  />
           </div>	
            <div class="form_row clearfix">
			   <label><?php _e('Author name',THEME_DOMAIN);?></label>
			   <input name="articleauthor" type="text" PLACEHOLDER="<?php _e('Author',THEME_DOMAIN); ?>" />
			   <label class="adv_author">
               <?php _e('Exact author',THEME_DOMAIN);?>
			   <input name="exactyes" type="checkbox" value="1" class="checkbox" />	
			   </label>
            </div>
			<?php 
			if(function_exists('get_search_post_fields_templ_plugin')){
				$default_custom_metaboxes = get_search_post_fields_templ_plugin($post_type,'custom_fields','post');
				display_search_custom_post_field_plugin($default_custom_metaboxes,'custom_fields','post');//displaty custom fields html.
				}
			?>
			<input type="hidden" name="search_template" value="1"/>
            <!--<input class="adv_input" name="adv_search" id="adv_search" type="hidden" value="1"  />-->
		    <input class="adv_input" name="post_type" id="post_type" type="hidden" value="<?php echo $post_type; ?>"  />
           <input type="submit" name="submit" value="<?php _e('Search',THEME_DOMAIN); ?>" class="adv_submit" />              
        </form>
		
		</div><!-- .hfeed -->

		<?php do_action( 'close_content' ); // supreme_close_content ?>
</section>
<?php 
if($front_page_id == $post->ID){}else{
	get_sidebar(); 
}

add_action('wp_footer','supreme_search_validation');
function supreme_search_validation(){
?> 
<script type="text/javascript" >
function sformcheck(){
	jQuery.noConflict();
	var search = jQuery('#adv_s').val();
	if(search==""){
		jQuery('#search_error').html('<?php _e('Please enter word you want to search',THEME_DOMAIN); ?>');
		return false;
	}else{
		search.bind(change,function(){jQuery('#search_error').html('');});
		jQuery('#search_error').html('');
		return true;
	}
}
</script>        
<?php } 
if($front_page_id == $post->ID){}else{
	get_footer(); 
}
?>