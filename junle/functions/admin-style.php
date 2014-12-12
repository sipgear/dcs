<?php 
/*
    File contain the code for color options in customizer 
*/

ob_start();
	$file = dirname(__FILE__);
	$file = substr($file,0,stripos($file, "wp-content"));
	//require($file . "/wp-load.php");
	global $wpdb;
	if(function_exists('supreme_get_setting')){
		$color1 = supreme_get_setting( 'color_picker_color1' );
		$color2 = supreme_get_setting( 'color_picker_color2' );
		$color3 = supreme_get_setting( 'color_picker_color3' );
		$color4 = supreme_get_setting( 'color_picker_color4' );
		$color5 = supreme_get_setting( 'color_picker_color5' );
		$color6 = supreme_get_setting( 'color_picker_color6' );
	}else{
		$supreme_theme_settings = get_option(supreme_prefix().'_theme_settings');
		if(isset($supreme_theme_settings[ 'color_picker_color1' ]) && $supreme_theme_settings[ 'color_picker_color1' ] !=''):
			$color1 = $supreme_theme_settings[ 'color_picker_color1' ];
		else:
			$color1 ='';
		endif;
		
		if(isset($supreme_theme_settings[ 'color_picker_color2' ]) && $supreme_theme_settings[ 'color_picker_color2' ] !=''):
			$color2 = $supreme_theme_settings[ 'color_picker_color2' ];
		else:
			$color2 = '';
		endif;
		
		if(isset($supreme_theme_settings[ 'color_picker_color3' ]) && $supreme_theme_settings[ 'color_picker_color3' ] !=''):
			$color3 = $supreme_theme_settings[ 'color_picker_color3' ];
		else:
			$color3 ='';
		endif;
		
		if(isset($supreme_theme_settings[ 'color_picker_color4' ]) && $supreme_theme_settings[ 'color_picker_color4' ] !=''):
			$color4 = $supreme_theme_settings[ 'color_picker_color4' ];
		else:
			$color4 = '';
		endif;
		
		if(isset($supreme_theme_settings[ 'color_picker_color5' ]) && $supreme_theme_settings[ 'color_picker_color5' ] !=''):
			$color5 = $supreme_theme_settings[ 'color_picker_color5' ];
		else:
			$color5 ='';
		endif;
		
		if(isset($supreme_theme_settings[ 'color_picker_color6' ]) && $supreme_theme_settings[ 'color_picker_color6' ] !=''):
			$color6 = $supreme_theme_settings[ 'color_picker_color6' ];
		else:
			$color6 ='';
		endif;
	}

//Change color of body background -------------------------------------------------------------------------------------------------
if($color1 != "#" || $color1 != ""){?>

body, .home .flexslider .slides > li h2, div#menu-secondary .menu li > a:after, div#menu-secondary1 .menu li > a:after, body .mega-menu ul.mega > li > a:after, .widget-portfolio .portfolio .entry .video-hover span a:hover, #home_wrapper .about_member li .hover .hov a:hover, input[type="date"], input[type="datetime"], input[type="datetime-local"], input[type="email"], input[type="month"], input[type="number"], input[type="password"], input[type="search"], input[type="tel"], input[type="text"], input.input-text, input[type="time"], input[type="url"], input[type="week"], select, textarea, .widget-search input[type="text"], .gform_wrapper input[type="file"], div.pp_woocommerce div.pp_content_container, .sidebar .widget-search input[type="text"], #footer .widget-search input[type="text"], div.modal, div.modal-footer
{background-color: <?php echo $color1;?>}

body .mega-menu ul.mega li a, div.mega-menu ul.mega li .sub li.mega-hdr a.mega-hdr-a, div#menu-secondary .menu li a:hover, div#menu-secondary1 .menu li a:hover, div#menu-secondary .menu li:hover > a, div#menu-secondary1 .menu li:hover > a, div#menu-secondary .menu li.current-menu-item > a, div#menu-secondary1 .menu li.current-menu-item > a, div#menu-subsidiary .menu li.current-menu-item > a, div#menu-secondary .menu li li:hover > a, div#menu-secondary1 .menu li li:hover > a, div#menu-secondary .menu li li a:hover, div#menu-secondary1 .menu li li a:hover, div#menu-subsidiary .menu li li a:hover, .nav_bg .widget-nav-menu li li a:hover, .home .flexslider .slides > li h2, #home_wrapper .about_member li:hover .details h4, #home_wrapper .about_member li:hover .details h4 + span, #home_wrapper .about_member li .hover .hov a, #home_wrapper .widget-portfolio h3.widget-title, .widget-portfolio nav.primary ul li a, .widget-portfolio .portfolio .entry .video-hover span a, .home_content .postpagination a:hover, .postpagination a.active, #home_wrapper .subscribe_wall h3.widget-title, #home_wrapper .home_content .subscribe p, button, .button.alt input[type="reset"], input[type="submit"], input[type="button"], a.button, .button, .upload, body.woocommerce a.button, body.woocommerce button.button, body.woocommerce input.button, body.woocommerce #respond input#submit, body.woocommerce #content input.button, body.woocommerce-page a.button, body.woocommerce-page button.button, body.woocommerce-page input.button, body.woocommerce-page #respond input#submit, body.woocommerce-page #content input.button, #searchform input[type="submit"], body.woocommerce .widget_layered_nav_filters ul li a, body.woocommerce-page .widget_layered_nav_filters ul li a, div.woocommerce form.track_order input.button, body.woocommerce a.button.alt, body.woocommerce button.button.alt, body.woocommerce input.button.alt, body.woocommerce #respond input#submit.alt, body.woocommerce #content input.button.alt, body.woocommerce-page a.button.alt, body.woocommerce-page button.button.alt, body.woocommerce-page input.button.alt, body.woocommerce-page #respond input#submit.alt, body.woocommerce-page #content input.button.alt, div.home_page_banner .slider-post a.moretag, #footer .contacts_wrap a, .social_media ul li a abbr, .social_media ul li a .social_icon, button:hover, .button.alt:hover,  input[type="reset"]:hover,  input[type="submit"]:hover,  input[type="button"]:hover,  a.button:hover,  .button:hover, .upload:hover, body.woocommerce a.button:hover, body.woocommerce button.button:hover, body.woocommerce input.button:hover, body.woocommerce #respond input#submit:hover, body.woocommerce #content input.button:hover, body.woocommerce-page a.button:hover, body.woocommerce-page button.button:hover, body.woocommerce-page input.button:hover, body.woocommerce-page #respond input#submit:hover, body.woocommerce-page #content input.button:hover, #content input.button:hover, #searchform input[type="submit"]:hover, body.woocommerce .widget_layered_nav_filters ul li a:hover, body.woocommerce-page .widget_layered_nav_filters ul li a:hover, div.woocommerce form.track_order input.button:hover, body.woocommerce a.button.alt:hover, body.woocommerce button.button.alt:hover, body.woocommerce input.button.alt:hover, body.woocommerce #respond input#submit.alt:hover, body.woocommerce #content input.button.alt:hover, body.woocommerce-page a.button.alt:hover, body.woocommerce-page button.button.alt:hover, body.woocommerce-page input.button.alt:hover, body.woocommerce-page #respond input#submit.alt:hover, body.woocommerce-page #content input.button.alt:hover, div.home_page_banner .slider-post a.moretag:hover, .loop-nav span.previous:hover, .loop-nav span.next:hover, .pagination .page-numbers:hover, .comment-pagination .page-numbers:hover, .bbp-pagination .page-numbers:hover, .pagination span.current, .postpagination a, .loop-nav span.previous, .loop-nav span.next, div.pagination .page-numbers, .comment-pagination .page-numbers, body.woocommerce nav.woocommerce-pagination ul li a:hover, body.woocommerce-page nav.woocommerce-pagination ul li a:hover, body.woocommerce #content nav.woocommerce-pagination ul li a:hover, body.woocommerce-page #content nav.woocommerce-pagination ul li a:hover, body.woocommerce nav.woocommerce-pagination ul li span.current, body.woocommerce-page nav.woocommerce-pagination ul li span.current, body.woocommerce #content nav.woocommerce-pagination ul li span.current, body.woocommerce-page #content nav.woocommerce-pagination ul li span.current, body.woocommerce nav.woocommerce-pagination ul li a, body.woocommerce-page nav.woocommerce-pagination ul li a, body.woocommerce #content nav.woocommerce-pagination ul li a, body.woocommerce-page #content nav.woocommerce-pagination ul li a, body div.product form.cart .button, body #content div.product form.cart .button, body.woocommerce .quantity .plus, body.woocommerce-page .quantity .plus, body.woocommerce #content .quantity .plus, body.woocommerce-page #content .quantity .plus, body.woocommerce .quantity .minus, body.woocommerce-page .quantity .minus, body.woocommerce #content .quantity .minus, body.woocommerce-page #content .quantity .minus, #footer input.replace, #footer input.b_submit, #footer .button, #footer a.button, #footer #searchform input[type="submit"], #footer .postpagination a, #footer .chosen a, #footer .chosen a:hover, body.woocommerce #footer .widget_price_filter .ui-slider .ui-slider-handle, body.woocommerce-page #footer .widget_price_filter .ui-slider .ui-slider-handle, #footer p.buttons a:hover, #footer .postpagination a:hover, .widget #wp-calendar caption, .widget #wp-calendar th, .home_content .postpagination a.active
{color: <?php echo $color1;?>}

ul.nav-tabs > li > a
{border-bottom-color: <?php echo $color1;?>}

<?php }


//Primary - Change color of Links, Headings, Titles and Buttons ----------------------------------------------------------------------------
if($color2 != "#" || $color2 != ""){?>

.header_container, #container .header_container .header_strip .sticky_main, #home_wrapper .about_member li .details > .bg, #home_wrapper .about_member li .hover .hov a, .widget-portfolio nav.primary ul li a:hover, .widget-portfolio .portfolio .entry .video-hover span a, .home_content .postpagination a:hover, .postpagination a.active, #home_wrapper .home_content .subscribe, button:hover, .button.alt:hover, input[type="reset"]:hover, input[type="submit"]:hover, input[type="button"]:hover, a.button:hover, .button:hover, .upload:hover, body.woocommerce a.button:hover, body.woocommerce button.button:hover, body.woocommerce input.button:hover, body.woocommerce #respond input#submit:hover, body.woocommerce #content input.button:hover, body.woocommerce-page a.button:hover, body.woocommerce-page button.button:hover, body.woocommerce-page input.button:hover, body.woocommerce-page #respond input#submit:hover, body.woocommerce-page #content input.button:hover, #content input.button:hover, #searchform input[type="submit"]:hover, body.woocommerce .widget_layered_nav_filters ul li a:hover, body.woocommerce-page .widget_layered_nav_filters ul li a:hover, div.woocommerce form.track_order input.button:hover, body.woocommerce a.button.alt:hover, body.woocommerce button.button.alt:hover, body.woocommerce input.button.alt:hover, body.woocommerce #respond input#submit.alt:hover, body.woocommerce #content input.button.alt:hover, body.woocommerce-page a.button.alt:hover, body.woocommerce-page button.button.alt:hover, body.woocommerce-page input.button.alt:hover, body.woocommerce-page #respond input#submit.alt:hover, body.woocommerce-page #content input.button.alt:hover, div.home_page_banner .slider-post a.moretag:hover, .social_media ul li a:hover abbr, body.woocommerce nav.woocommerce-pagination ul li a:hover, body.woocommerce-page nav.woocommerce-pagination ul li a:hover, body.woocommerce #content nav.woocommerce-pagination ul li a:hover, body.woocommerce-page #content nav.woocommerce-pagination ul li a:hover, body.woocommerce nav.woocommerce-pagination ul li span.current, body.woocommerce-page nav.woocommerce-pagination ul li span.current, body.woocommerce #content nav.woocommerce-pagination ul li span.current, body.woocommerce-page #content nav.woocommerce-pagination ul li span.current, body.woocommerce a.button.alt, body.woocommerce button.button.alt, body.woocommerce input.button.alt, body.woocommerce #respond input#submit.alt, body.woocommerce #content input.button.alt, body.woocommerce-page a.button.alt, body.woocommerce-page button.button.alt, body.woocommerce-page input.button.alt, body.woocommerce-page #respond input#submit.alt, body.woocommerce-page #content input.button.alt, body.woocommerce .quantity .plus:hover, body.woocommerce-page .quantity .plus:hover, body.woocommerce #content .quantity .plus:hover, body.woocommerce-page #content .quantity .plus:hover, body.woocommerce .quantity .minus:hover, body.woocommerce-page .quantity .minus:hover, body.woocommerce #content .quantity .minus:hover, body.woocommerce-page #content .quantity .minus:hover, .loop-nav span.previous:hover, .loop-nav span.next:hover, .pagination .page-numbers:hover, .comment-pagination .page-numbers:hover, .bbp-pagination .page-numbers:hover, div.pagination span.current, .widget #wp-calendar caption, #footer input.replace, #footer input.b_submit, #footer .button, #footer a.button, #footer #searchform input[type="submit"], #footer .postpagination a, #footer .chosen a, #footer .chosen a:hover, body.woocommerce #footer .widget_price_filter .ui-slider .ui-slider-handle, body.woocommerce-page #footer .widget_price_filter .ui-slider .ui-slider-handle, a.button.alt, div.home_page_banner .slider-post a.moretag, .entry-content .flex-direction-nav a:hover
{background-color: <?php echo $color2;?>}

a:hover, #home_wrapper .about_member li .hover .hov a:hover, ul li a:hover, ol li a:hover, #home_wrapper a.readmore:hover, .widget-portfolio .portfolio .entry .video-hover span a:hover, #footer .contacts_wrap a:hover, #footer a:hover, #footer ul li a:hover, #footer ol li a:hover, #breadcrumb a, .breadcrumb a, .bbp-breadcrumb a, .widget .follow_us_twitter, .comment-reply-link:hover, .comment-reply-login:hover, #recentcomments a:hover, h3.popover-title, li.ui-tabs-active > a, ul.nav-tabs > .active > a, h3.ui-accordion-header, div.modal h3, .arclist ul li a:hover, .sitemap ul li a:hover
{color: <?php echo $color2;?>}

article.post img:hover, article.hentry img:hover
{border-color: <?php echo $color2;?>}

<?php }


//Secondary - buttons, headings ---------------------------------------------------------------------------------------------------
if($color3 != "#" || $color3 != ""){?>

a, ul li a, ol li a, .widget h3, .widget.title, .widget-title, .widget-search .widget-title, .wpcf7-form h2, .arclist h2, #comments-number, #reply-title, body.woocommerce .pp_woocommerce .ppt, #home_wrapper .about_member li .details h4, #home_wrapper .theme_services_list ul li h4, #home_wrapper a.readmore, #home_wrapper .home_content .testimonials .quote, #home_wrapper .home_content .Advertisements h3, .home_content .postpagination a, .sidebar .widget-tags a, .sidebar .widget .tagcloud a, .comment-author cite, .comment-meta a, h1.post-title, h1.page-title, h1.loop-title, #content .boxes h3, #content .about_author h4, #content ul.products li.product h3, body.woocommerce div.product .woocommerce-tabs ul.tabs li.active a, body.woocommerce-page div.product .woocommerce-tabs ul.tabs li.active a, body.woocommerce #content div.product .woocommerce-tabs ul.tabs li.active a, body.woocommerce-page #content div.product .woocommerce-tabs ul.tabs li.active a, body.woocommerce div.product .woocommerce-tabs ul.tabs li a:hover, body.woocommerce-page div.product .woocommerce-tabs ul.tabs li a:hover, body.woocommerce #content div.product .woocommerce-tabs ul.tabs li a:hover, body.woocommerce-page #content div.product .woocommerce-tabs ul.tabs li a:hover, #recentcomments a, .arclist ul li a, .sitemap ul li a, #home_wrapper .home_content .contact_widget h3, #home_wrapper .home_content .widget-googlemap h3
{color: <?php echo $color3;?>}

.widget-portfolio, button, .button.alt input[type="reset"], input[type="submit"], input[type="button"], a.button, .button, .upload, body.woocommerce a.button, body.woocommerce button.button, body.woocommerce input.button, body.woocommerce #respond input#submit, body.woocommerce #content input.button, body.woocommerce-page a.button, body.woocommerce-page button.button, body.woocommerce-page input.button, body.woocommerce-page #respond input#submit, body.woocommerce-page #content input.button, #searchform input[type="submit"], body.woocommerce .widget_layered_nav_filters ul li a, body.woocommerce-page .widget_layered_nav_filters ul li a, div.woocommerce form.track_order input.button, body.woocommerce a.button.alt, body.woocommerce button.button.alt, body.woocommerce input.button.alt, body.woocommerce #respond input#submit.alt, body.woocommerce #content input.button.alt, body.woocommerce-page a.button.alt, body.woocommerce-page button.button.alt, body.woocommerce-page input.button.alt, body.woocommerce-page #respond input#submit.alt, body.woocommerce-page #content input.button.alt, #home_wrapper .home_content .subscribe_cont input.replace:hover, #footer, .widget #wp-calendar th, body.woocommerce nav.woocommerce-pagination ul li a, body.woocommerce-page nav.woocommerce-pagination ul li a, body.woocommerce #content nav.woocommerce-pagination ul li a, body.woocommerce-page #content nav.woocommerce-pagination ul li a, body.woocommerce .widget_price_filter .ui-slider .ui-slider-handle, body.woocommerce-page .widget_price_filter .ui-slider .ui-slider-handle, .loop-nav span.previous, .loop-nav span.next, div.pagination .page-numbers, .comment-pagination .page-numbers, .ui-datepicker-header, .social_media ul li a abbr, .social_media ul li a .social_icon, body.woocommerce .quantity .plus, body.woocommerce-page .quantity .plus, body.woocommerce #content .quantity .plus, body.woocommerce-page #content .quantity .plus, body.woocommerce .quantity .minus, body.woocommerce-page .quantity .minus, body.woocommerce #content .quantity .minus, body.woocommerce-page #content .quantity .minus, .home_page_banner .flexslider ul.slides, .entry-content .flex-direction-nav a
{background-color: <?php echo $color3;?>}

.sidebar .widget-tags a:hover, .sidebar .widget .tagcloud a:hover
{border-color: <?php echo $color3;?>}

<?php }


//Change color of page content -------------------------------------------------------------------------------------------------------
if($color4 != "#" || $color4 != ""){?>

body, p, #home_wrapper .home_content .Advertisements p, input[type="date"], input[type="datetime"], input[type="datetime-local"], input[type="email"], input[type="month"], input[type="number"], input[type="password"], input[type="search"], input[type="tel"], input[type="text"], input.input-text, input[type="time"], input[type="url"], input[type="week"], select, textarea, .widget-search input[type="text"], .gform_wrapper input[type="file"], #footer, #footer del, #footer del span.amount, #footer .templatic_twitter_widget .twit_time, #content ul.products li.product .price .from, #content ul.products li.product .price del, #content ul.products li.product:hover h3, #content ul.products li.product .price, ins span.amount, body.woocommerce div.product span.price, body.woocommerce-page div.product span.price, body.woocommerce #content div.product span.price, body.woocommerce-page #content div.product span.price, body.woocommerce div.product p.price, body.woocommerce-page div.product p.price, body.woocommerce #content div.product p.price, body.woocommerce-page #content div.product p.price, .breadcrumb, div.woocommerce-message, div.woocommerce-error, div.woocommerce-info, p.woocommerce-info, p.woocommerce-message, p.woocommerce-error
{color: <?php echo $color4;?>}

body .mega-menu ul.mega li ul.sub-menu, .widget-portfolio nav.primary ul li a
{background-color: <?php echo $color4;?>}

<?php }


//Change color of sub text -----------------------------------------------------------------------------------------------
if($color5 != "#" || $color5 != ""){?>

#home_wrapper .about_member li .details h4 + span, #home_wrapper .home_content .testimonials .quote cite, .popular_post ul li .post_data p .author_meta, .home_content .popular_post ul li .date, .home_content .popular_post ul li .views, #footer a, #footer ul li a, #footer ol li a, #footer .widget_rss ul li a.rsswidget, #footer h3.widget-title, #footer .widget_rss a.rsswidget, .widget-widget_rss ul li span.rss-date, .widget-widget_rss ul li cite, #footer ins span.amount, del, del span.amount, .sidebar del, .sidebar del span.amount, body.woocommerce .widget_layered_nav ul small.count, body.woocommerce-page .widget_layered_nav ul small.count, body.woocommerce div.product .woocommerce-tabs ul.tabs li a, body.woocommerce-page div.product .woocommerce-tabs ul.tabs li a, body.woocommerce #content div.product .woocommerce-tabs ul.tabs li a, body.woocommerce-page #content div.product .woocommerce-tabs ul.tabs li a, .woocommerce #reviews #comments ol.commentlist li p.meta, .woocommerce-page #reviews #comments ol.commentlist li p.meta, body.woocommerce .star-rating:before, body.woocommerce-page .star-rating:before, .comment-author, .entry-meta, .byline, form#commentform p.log-in-out, .templatic_twitter_widget .twit_time, .home_content .popular_post ul li .post_data p .author_meta, .arclist ul li span.arclist_comment, .arclist ul li, .sitemap ul li
{color: <?php echo $color5;?>}

#footer .widget-tags a, #footer .widget .tagcloud a, #footer .widget-tags a:hover, #footer .widget .tagcloud a:hover, .sidebar .widget-tags a, .sidebar .widget .tagcloud a
{border-color: <?php echo $color5;?>}


<?php }


 

if($color6 != "#" || $color6 != ""){?>

<?php }

$color_data = ob_get_contents();
ob_clean();
if(isset($color_data) && $color_data !=''){ 
    file_put_contents(trailingslashit(get_template_directory())."library/css/admin_style.css" , $color_data); 
}
?>