<!-- post format video -->
		<header class="entry-header">
		
		
		<div class="entry-media">
		<?php 
		if(function_exists('the_post_format_video')){
			the_post_format_video(); // wordpress 3.6 compatibility
		}else{
			the_content();
		}?>
		</div>
		<?php	
	 
			 do_action('supreme_after-image'.$post_type);

		/* get the image code - show image if Display imege option is enable from backend - Start */
			 do_action('supreme_before-title'.$post_type);
		?>
		
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', THEME_DOMAIN ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

		<?php apply_filters('supreme-post-info',supreme_core_post_info($post)); // return post information; 	
			      do_action('supreme_after-title'.$post_type); ?>
				  
				  
		</header><!-- .entry-header -->
		