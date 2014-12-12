	<?php // supreme_open_entry
	$post_type = get_post_type($post->ID);
	do_action( 'open_entry'.$post_type );
	if ( is_sticky() && is_home() && ! is_paged() ) : ?>
		<div class="featured-post"><?php _e( 'Featured post', THEME_DOMAIN ); ?></div>
	<?php endif;?>
		
		<?php 		
		/* get the image code - show image if Display imege option is enable from backend - Start */
		$supreme2_theme_settings = get_option(supreme_prefix().'_theme_settings');
		if ( current_theme_supports( 'get-the-image' ) && $supreme2_theme_settings['supreme_display_image'] ) :
		do_action('supreme_before-image'.$post_type);
		$image = get_the_image( array( 'echo' => false ) );
		if ( $image ) : ?>
		<figure class="post_fig">
		<a href="<?php echo get_permalink(); ?>" title="<?php the_title_attribute( 'echo=1' ); ?>" rel="bookmark" class="featured-image-link"><?php get_the_image( array( 'size' => 'supreme-thumbnail', 'link_to_post' => false, 'width' => '175' ) ); ?></a>
		</figure> 
		<?php 
		else : 
			if($supreme2_theme_settings['supreme_display_noimage']){
				$post_image = apply_filters('supreme_noimage-url',get_template_directory_uri()."/images/noimage.jpg");
		?>
        <figure class="post_fig">
		<a href="<?php echo get_permalink(); ?>" title="<?php the_title_attribute( 'echo=1' ); ?>" rel="bookmark" class="featured-image-link"><img src="<?php echo $post_image; ?>" alt="<?php the_title_attribute( 'echo=1' ); ?>"/></a>	
		<?php } ?>
		</figure> 
		
        <?php endif;
		
		do_action('supreme_after-image'.$post_type);
		endif;
		/* get the image code - show image if Display imege option is enable from backend - Start */
		do_action('supreme_before-title'.$post_type);
		?>
        
		<header class="entry-header">
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', THEME_DOMAIN ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
		
		<?php apply_filters('supreme-post-info',supreme_core_post_info($post)); // return post information; 	
			do_action('supreme_after-title'.$post_type);
		?>
		
		<?php 
		do_action( 'tmpl-before-entry'.$post_type); // Loads the sidebar-entry
		if( $supreme2_theme_settings['supreme_archive_display_excerpt'] ) { ?>
		<div class="entry-summary">
		<?php the_excerpt(); ?>
		</div><!-- .entry-summary -->
		<?php }else{ ?>
		<div class="entry-content">
		<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', THEME_DOMAIN ) ); ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', THEME_DOMAIN ), 'after' => '</div>' ) ); ?>
		</div><!-- .entry-content -->
		<?php } 
		apply_filters( 'tmpl-after-entry',supreme_sidebar_entry() ); // Loads the sidebar-entry
		$taxonomies =  supreme_get_post_taxonomies($post);
		$cat_slug = @$taxonomies [0];
		$tag_slug = @$taxonomies [1];
		$theme_options = get_option(supreme_prefix().'_theme_settings');
		$display_post_terms = $theme_options['display_post_terms'];
		if($display_post_terms){
			apply_filters('supreme_list_post_categories',supreme_get_categories(__('Categories:',THEME_DOMAIN).' ',$cat_slug,'',__('Tags:',THEME_DOMAIN).' ',$tag_slug));// 1- category label, 2- category slug,3- class name of div, 3- tags label,4- tags slug
		}

		do_action('supreme_aftercontent'.$post_type);
		
		do_action( 'close_entry'.$post_type); // supreme_close_entry ?>
		<!-- #post -->
        </header><!-- .entry-header -->