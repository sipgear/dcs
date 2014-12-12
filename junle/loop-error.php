<?php

?>

	<li id="post-0" class="<?php supreme_entry_class(); ?>">

		<div class="entry-summary">

			<p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', THEME_DOMAIN ); ?></p>
			  <?php remove_all_actions('posts_where');
					$WPLisPages = new WP_Query('showposts=60&post_type=page');
					
					if( count(@$WPLisPages->posts) > 0 ){
			   ?>
				  <div class="arclist">
			        <h2><?php _e('Pages',THEME_DOMAIN);?></h2>
			        <ul class="sitemap">
			          <?php wp_list_pages('title_li='); ?>
			        </ul>
			      </div>
				<?php } ?>
	
	  <!--/arclist -->
	 <?php remove_all_actions('posts_where');
		$Supreme_Theme_Settings_Options = get_option(supreme_prefix().'_theme_settings');
		$Get_All_Post_Types = explode(',',@$Supreme_Theme_Settings_Options['post_type_label']);
		foreach($Get_All_Post_Types as $post_type):
			if($post_type!='page' && $post_type!="attachment" && $post_type!="revision" && $post_type!="nav_menu_item"):
			$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));	
			$archive_query = new WP_Query('showposts=60&post_type='.$post_type);
			if( count(@$archive_query->posts) > 0 ){
				$PostTypeObject = get_post_type_object($post_type);
				if($PostTypeObject)
				$PostTypeName = $PostTypeObject->labels->name;
			}	
			if( is_plugin_active('woocommerce/woocommerce.php') && "product" == $post_type ){
				$taxonomies[0] = $taxonomies[1];
			}
				$WPListCustomCategories = wp_list_categories('title_li=&hierarchical=0&show_count=0&echo=0&taxonomy='.$taxonomies[0]);
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
   	</div><!-- .entry-summary -->

	</li><!-- .hentry .error -->