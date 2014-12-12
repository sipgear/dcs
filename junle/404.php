<?php


@header( 'HTTP/1.1 404 Not found', true, 404 );

get_header(); // Loads the header.php template. 
global $post;
$single_post = $post;

	do_action( 'before_content' ); // supreme_before_content
	
	$supreme2_theme_settings = get_option(supreme_prefix().'_theme_settings');
	if ( current_theme_supports( 'breadcrumb-trail' ) && $supreme2_theme_settings['supreme_show_breadcrumb'] == 1 ) breadcrumb_trail( array( 'separator' => '&raquo;' ) ); ?>


	<section id="content" class="error_404">

		<?php do_action( 'open_content' ); // supreme_open_content ?>

		<div class="hfeed">

			<div id="post-0" class="<?php //supreme_entry_class(); ?>">

				<h1 class="error-404-title entry-title"><?php _e( 'Error 404', THEME_DOMAIN ); ?></h1>

				<div class="wrap404 clearfix">
                	<p class="display404">404</p>
                    <h4><?php _e("Sorry, this page doesn't exist!",THEME_DOMAIN); ?></h4>
                    <p><?php _e("The page you are looking for may have been removed, has a change in name or is temporarily unavailable.",THEME_DOMAIN); ?></p>
                </div>
                
                
            
            	<div class="entry-content">
                	
                	<p>
						<?php printf( __( 'You tried going to %1$s, and it doesn\'t exist. All is not lost! You can search for what you\'re looking for.', THEME_DOMAIN ), '<code>' . home_url( esc_url( $_SERVER['REQUEST_URI'] ) ) . '</code>' ); ?>
					</p>

					<div class="search404"><?php get_search_form(); // Loads the searchform.php template. ?></div>

				</div><!-- .entry-content -->
				 <div class="arclist">
			        <h2><?php _e('Pages',THEME_DOMAIN);?></h2>
			        <ul class="sitemap">
			          <?php wp_list_pages('title_li='); ?>
			        </ul>
		    	  </div>
		    	 <?php
				$Supreme_Theme_Settings_Options = get_option(supreme_prefix().'_theme_settings');
				
				$Get_All_Post_Types = explode(',', @$Supreme_Theme_Settings_Options['post_type_label']);
				foreach($Get_All_Post_Types as $post_type):
					if($post_type!='page' && $post_type!="attachment" && $post_type!="revision" && $post_type!="nav_menu_item"):
					$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));	
					$archive_query = new WP_Query('showposts=60&post_type='.$post_type);
					if( count(@$archive_query->posts) > 0 ){
						$PostTypeObject = get_post_type_object($post_type);
						if(isset( $PostTypeObject) &&  $PostTypeObject!='')
							$PostTypeName = $PostTypeObject->labels->name;
					}	
				if( is_plugin_active('woocommerce/woocommerce.php') && "product" == $post_type ){
					$taxonomies[0] = $taxonomies[1];
				}
				$WPListCustomCategories = wp_list_categories('title_li=&hierarchical=0&show_count=0&echo=0&taxonomy='. @$taxonomies[0]);
				if(($WPListCustomCategories) && $WPListCustomCategories!="No categories" && $WPListCustomCategories!="<li>No categories</li>"){
		?> 
			  <div class="arclist">
				<div class="title-container">
					<h2 class="title_green"><span><?php echo sprintf(__('%s Categories','supreme'), ucfirst($PostTypeName));?></span></h2>
					<div class="clearfix"></div>
				</div>
				<ul>
				  <?php echo $WPListCustomCategories;?>
				</ul>
			  </div>
			  <?php } 
			endif;
		endforeach; 
	?>            
   	</div><!-- .hentry -->

	</div><!-- .hfeed -->

	<?php $post = $single_post; do_action( 'close_content' ); // supreme_close_content ?>

	</section><!-- #content -->

	<?php do_action( 'after_content' ); // supreme_after_content
get_sidebar();
get_footer(); // Loads the footer.php template. ?>