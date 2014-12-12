<?php
//----------------------------------------------------------------------//
// Initiate the plugin to add custom post type
//----------------------------------------------------------------------//

/* difine taxonomies */
define('CUSTOM_POST_TYPE1','portfolio');
define('CUSTOM_CATEGORY_TYPE1','portfoliocategory');
define('CUSTOM_TAG_TYPE1','portfoliotags');

define('CUSTOM_MENU_TITLE',__('Portfolio','templatic'));
define('CUSTOM_MENU_NAME',__('Portfolio','templatic'));
define('CUSTOM_MENU_SIGULAR_NAME',__('Portfolio','templatic'));
define('CUSTOM_MENU_ADD_NEW',__('Add Work','templatic'));
define('CUSTOM_MENU_ADD_NEW_ITEM',__('Add New Work','templatic'));
define('CUSTOM_MENU_EDIT',__('Edit','templatic'));
define('CUSTOM_MENU_EDIT_ITEM',__('Edit Work','templatic'));
define('CUSTOM_MENU_NEW',__('New Work','templatic'));
define('CUSTOM_MENU_VIEW',__('View Work','templatic'));
define('CUSTOM_MENU_SEARCH',__('Search Work','templatic'));
define('CUSTOM_MENU_NOT_FOUND',__('No Work found','templatic'));
define('CUSTOM_MENU_NOT_FOUND_TRASH',__('No Work found in trash','templatic'));

define('CUSTOM_MENU_CAT_LABEL',__('Type of work','templatic'));
define('CUSTOM_MENU_CAT_TITLE',__('Type of work','templatic'));
define('CUSTOM_MENU_SIGULAR_CAT',__('Category','templatic'));
define('CUSTOM_MENU_CAT_SEARCH',__('Search Category','templatic'));
define('CUSTOM_MENU_CAT_POPULAR',__('Popular Categories','templatic'));
define('CUSTOM_MENU_CAT_ALL',__('All Categories','templatic'));
define('CUSTOM_MENU_CAT_PARENT',__('Parent Category','templatic'));
define('CUSTOM_MENU_CAT_PARENT_COL',__('Parent Category:','templatic'));
define('CUSTOM_MENU_CAT_EDIT',__('Edit Category','templatic'));
define('CUSTOM_MENU_CAT_UPDATE',__('Update Category','templatic'));
define('CUSTOM_MENU_CAT_ADDNEW',__('Add New Category','templatic'));
define('CUSTOM_MENU_CAT_NEW_NAME',__('New Category Name','templatic'));

define('CUSTOM_MENU_TAG_LABEL',__('Tags','templatic'));
define('CUSTOM_MENU_TAG_TITLE',__('Tags','templatic'));
define('CUSTOM_MENU_TAG_NAME',__('Tags','templatic'));
define('CUSTOM_MENU_TAG_SEARCH',__('Tags','templatic'));
define('CUSTOM_MENU_TAG_POPULAR',__('Popular Tags','templatic'));
define('CUSTOM_MENU_TAG_ALL',__('All Tags','templatic'));
define('CUSTOM_MENU_TAG_PARENT',__('Parent Tags','templatic'));
define('CUSTOM_MENU_TAG_PARENT_COL',__('Parent Tags:','templatic'));
define('CUSTOM_MENU_TAG_EDIT',__('Edit Tags','templatic'));
define('CUSTOM_MENU_TAG_UPDATE',__('Update Tags','templatic'));
define('CUSTOM_MENU_TAG_ADD_NEW',__('Add New Tags','templatic'));
define('CUSTOM_MENU_TAG_NEW_ADD',__('New Tags Name','templatic'));


/* function to register post type  */
add_action("init", "custom_posttype__slider_menu_wp_admin");
function custom_posttype__slider_menu_wp_admin()
{

//===============EVENT SECTION START================
$custom_post_type = CUSTOM_POST_TYPE1;
$custom_cat_type = CUSTOM_CATEGORY_TYPE1;
$custom_tag_type = CUSTOM_TAG_TYPE1;

register_post_type(	"$custom_post_type", 
				array(	'label' 			=> CUSTOM_MENU_TITLE,
						'labels' 			=> array(	'name' 					=> 	CUSTOM_MENU_NAME,
														'singular_name' 		=> 	CUSTOM_MENU_SIGULAR_NAME,
														'add_new' 				=>  CUSTOM_MENU_ADD_NEW,
														'add_new_item' 			=>  CUSTOM_MENU_ADD_NEW_ITEM,
														'edit' 					=>  CUSTOM_MENU_EDIT,
														'edit_item' 			=>  CUSTOM_MENU_EDIT_ITEM,
														'new_item' 				=>  CUSTOM_MENU_NEW,
														'view_item'				=>  CUSTOM_MENU_VIEW,
														'search_items' 			=>  CUSTOM_MENU_SEARCH,
														'not_found' 			=>  CUSTOM_MENU_NOT_FOUND,
														'not_found_in_trash' 	=>  CUSTOM_MENU_NOT_FOUND_TRASH	),
						'public' 			=> true,
						'can_export'		=> true,
						'show_ui' 			=> true, // UI in admin panel
						'_builtin' 			=> false, // It's a custom post type, not built in
						'_edit_link' 		=> 'post.php?post=%d',
						'capability_type' 	=> 'post',
						'menu_icon' 		=> trailingslashit(get_template_directory_uri()).'library/images/favicon.ico',
						'hierarchical' 		=> false,
						'rewrite' 			=> array("slug" => "$custom_post_type"), // Permalinks
						'query_var' 		=> "$custom_post_type", // This goes to the WP_Query schema
						'supports' 			=> array(	'title',
														'author', 
														'excerpt',
														'thumbnail',
														'comments',
														'editor', 
														'trackbacks',
														'custom-fields',
														'revisions') ,
						'show_in_nav_menus'	=> true ,
						'taxonomies'		=> array("$custom_cat_type","$custom_tag_type")
					)
				);

// Register custom taxonomy
register_taxonomy(	"$custom_cat_type", 
				array(	"$custom_post_type"	), 
				array (	"hierarchical" 		=> true, 
						"label" 			=> CUSTOM_MENU_CAT_LABEL, 
						'labels' 			=> array(	'name' 				=>  CUSTOM_MENU_CAT_TITLE,
														'singular_name' 	=>  CUSTOM_MENU_SIGULAR_CAT,
														'search_items' 		=>  CUSTOM_MENU_CAT_SEARCH,
														'popular_items' 	=>  CUSTOM_MENU_CAT_SEARCH,
														'all_items' 		=>  CUSTOM_MENU_CAT_ALL,
														'parent_item' 		=>  CUSTOM_MENU_CAT_PARENT,
														'parent_item_colon' =>  CUSTOM_MENU_CAT_PARENT_COL,
														'edit_item' 		=>  CUSTOM_MENU_CAT_EDIT,
														'update_item'		=>  CUSTOM_MENU_CAT_UPDATE,
														'add_new_item' 		=>  CUSTOM_MENU_CAT_ADDNEW,
														'new_item_name' 	=>  CUSTOM_MENU_CAT_NEW_NAME,	), 
						'public' 			=> true,
						'show_ui' 			=> true,
						"rewrite" 			=> true	)
				);
register_taxonomy(	"$custom_tag_type", 
				array(	"$custom_post_type"	), 
				array(	"hierarchical" 		=> false, 
						"label" 			=> CUSTOM_MENU_TAG_LABEL, 
						'labels' 			=> array(	'name' 				=>  CUSTOM_MENU_TAG_TITLE,
														'singular_name' 	=>  CUSTOM_MENU_TAG_NAME,
														'search_items' 		=>  CUSTOM_MENU_TAG_SEARCH,
														'popular_items' 	=>  CUSTOM_MENU_TAG_POPULAR,
														'all_items' 		=>  CUSTOM_MENU_TAG_ALL,
														'parent_item' 		=>  CUSTOM_MENU_TAG_PARENT,
														'parent_item_colon' =>  CUSTOM_MENU_TAG_PARENT_COL,
														'edit_item' 		=>  CUSTOM_MENU_TAG_EDIT,
														'update_item'		=>  CUSTOM_MENU_TAG_UPDATE,
														'add_new_item' 		=>  CUSTOM_MENU_TAG_ADD_NEW,
														'new_item_name' 	=>  CUSTOM_MENU_TAG_NEW_ADD,	),  
						'public' 			=> true,
						'show_ui' 			=> true,
						"rewrite" 			=> true	)
				);

}

//===============EVENT SECTION END================
add_filter( 'manage_edit-portfolio_columns', 'templatic_edit_portfolio_columns' ) ;

function templatic_edit_portfolio_columns( $columns ) {

	unset($columns['comments']);
	unset($columns['date']);
	$columns['post_category'] = __( 'Categories' ,THEME_DOMAIN);
	$columns['post_tags'] = __( 'Tags',THEME_DOMAIN );
	$columns['date'] = __( 'Date' ,THEME_DOMAIN);
	return $columns;
}

add_action( 'manage_portfolio_posts_custom_column', 'templatic_manage_portfolio_columns', 10, 2 );

function templatic_manage_portfolio_columns( $column, $post_id ) {
	echo '<link href="'.get_template_directory_uri().'/monetize/admin.css" rel="stylesheet" type="text/css" />';
	global $post;

	switch( $column ) {
	case 'post_category' :
			/* Get the post_category for the post. */
			$templ_events = get_the_terms($post_id,CUSTOM_CATEGORY_TYPE1);
			if (is_array($templ_events)) {
				foreach($templ_events as $key => $templ_event) {
					$edit_link = site_url()."/wp-admin/edit.php?".CUSTOM_CATEGORY_TYPE1."=".$templ_event->slug."&post_type=".CUSTOM_POST_TYPE1;
					$templ_events[$key] = '<a href="'.$edit_link.'">' . $templ_event->name . '</a>';
					}
				echo implode(' , ',$templ_events);
			}else {
				_e( 'Uncategorized',THEME_DOMAIN );
			}
			break;
			
		case 'post_tags' :
			/* Get the post_tags for the post. */
			$templ_event_tags = get_the_terms($post_id,CUSTOM_TAG_TYPE1);
			if (is_array($templ_event_tags)) {
				foreach($templ_event_tags as $key => $templ_event_tag) {
					$edit_link = site_url()."/wp-admin/edit.php?".CUSTOM_TAG_TYPE1."=".$templ_event_tag->slug."&post_type=".CUSTOM_POST_TYPE1;
					$templ_event_tags[$key] = '<a href="'.$edit_link.'">' . $templ_event_tag->name . '</a>';
				}
				echo implode(' , ',$templ_event_tags);
			}else {
				echo '';
			}
				
			break;
		
		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}
add_filter( 'manage_edit-portfolio_sortable_columns', 'templatic_portfolio_sortable_columns' );
function templatic_portfolio_sortable_columns( $columns ) {
	$columns['post_category'] = 'Categories';
	$columns['post_city_id'] = 'City';
	$columns['geo_address'] = 'Address';
	$columns['start_timing'] = 'Start time';
	$columns['end_timing'] = 'End time';
	return $columns;
}
/////The filter code to get the custom post type in the RSS feed
function myfeed_request_2($qv) {
	if (isset($qv['feed']))
		$qv['post_type'] = get_post_types();
	return $qv;
}
add_filter('request', 'myfeed_request_2');

?>