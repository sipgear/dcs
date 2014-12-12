<script type="text/javascript">/* sticky navigation for mobile view*/
jQuery.noConflict();jQuery(function(){var e=jQuery("#mobile_header");var t=jQuery("#mobile_header").css("display");var n=e.offset().top;var r=jQuery("#branding");var i=false;if(t=="block"){var s=jQuery(window);s.scroll(function(){var e=s.scrollTop();var t=e>n})}})
/* sticky navigation for desk top*/
// Stick the #nav to the top of the window
jQuery(document).ready(function(){
	var stickyTop = jQuery('.header_container').offset().top;
	jQuery(window).scroll(function(){
		var e=jQuery(window).scrollTop();
		if(e>60){
			jQuery(".sticky_main").fadeIn(200);
		}else{
			jQuery(".sticky_main").fadeOut(200);
		}
	})
})
</script>