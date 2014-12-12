/* admin auto install js */
(function(e){"use strict";e(function(){e("#dismiss-ajax-notification").length>0&&e("#dismiss-ajax-notification").click(function(t){t.preventDefault();e.post(ajaxurl,{action:"hide_admin_notification",nonce:e.trim(e("#ajax-notification-nonce").text())},function(t){"1"===t?e("#ajax-notification").fadeOut("normal"):e("#ajax-notification").fadeOut("normal")})})})})(jQuery);

jQuery(document).ready(function() {
	var val = jQuery("#page_template").val();
	jQuery('#mytheme_select_columns').hide();
	if(val == 'page-templates/portfolio_columns.php'){
		jQuery('#mytheme_select_columns').show();
	}else{ 
		jQuery('#mytheme_select_columns').hide();
	}
	
	jQuery('#page_template').change(function() {
		var val = jQuery("#page_template").val();
		
		if(val == 'page-templates/portfolio_columns.php'){
			jQuery('#mytheme_select_columns').fadeIn('slow');
		}else{
			jQuery('#mytheme_select_columns').fadeOut('slow');
		}
	});
});