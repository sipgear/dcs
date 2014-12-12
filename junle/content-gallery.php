	<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', THEME_DOMAIN ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
	<?php 
	if(function_exists('the_post_format_gallery')){
		the_post_format_gallery(); // wordpre 3.6 compatibility
	}else{
		the_content();
	}

	echo apply_filters( 'gallery-post-info', supreme_gallery_post_info()); ?>