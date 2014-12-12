<?php
/**
 * Metadata functions used in the core framework.  This file registers meta keys for use in WordPress 
 * in a safe manner by setting up a custom sanitize callback.
 *
 * @package HybridCore
 * @subpackage Functions
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2012, Justin Tadlock
 * @link http://themehybrid.com/supreme-core
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Register meta on the 'init' hook. */
add_action( 'init', 'supreme_register_meta' );

/**
 * Registers the framework's custom metadata keys and sets up the sanitize callback function.
 *
 * @since 1.3.0
 * @return void
 */
function supreme_register_meta() {

	/* Register meta if the theme supports the 'supreme-core-seo' feature. */
	if ( current_theme_supports( 'supreme-core-seo' ) ) {

		/* Register 'Title', 'Description', and 'Keywords' meta for posts. */
		register_meta( 'post', 'Title', 'supreme_sanitize_meta' );
		register_meta( 'post', 'Description', 'supreme_sanitize_meta' );
		register_meta( 'post', 'Keywords', 'supreme_sanitize_meta' );

		/* Register 'Title', 'Description', and 'Keywords' meta for users. */
		register_meta( 'user', 'Title', 'supreme_sanitize_meta' );
		register_meta( 'user', 'Description', 'supreme_sanitize_meta' );
		register_meta( 'user', 'Keywords', 'supreme_sanitize_meta' );
	}

	/* Register meta if the theme supports the 'supreme-core-template-hierarchy' feature. */
	if ( current_theme_supports( 'supreme-core-template-hierarchy' ) ) {

		$post_types = get_post_types( array( 'public' => true ) );

		foreach ( $post_types as $post_type ) {
			if ( 'page' !== $post_type )
				register_meta( 'post', "_wp_{$post_type}_template", 'supreme_sanitize_meta' );
		}
	}
}

/**
 * Callback function for sanitizing meta when add_metadata() or update_metadata() is called by WordPress. 
 * If a developer wants to set up a custom method for sanitizing the data, they should use the 
 * "sanitize_{$meta_type}_meta_{$meta_key}" filter hook to do so.
 *
 * @since 1.3.0
 * @param mixed $meta_value The value of the data to sanitize.
 * @param string $meta_key The meta key name.
 * @param string $meta_type The type of metadata (post, comment, user, etc.)
 * @return mixed $meta_value
 */
function supreme_sanitize_meta( $meta_value, $meta_key, $meta_type ) {
	return strip_tags( $meta_value );
}
add_action( 'wp_dashboard_setup', 'TemplaticDashboardWidgetSetup');
function TemplaticDashboardWidgetSetup() {
	add_meta_box( 'templatic_dashboard_news_widget', 'News From Templatic', 'TemplaticDashboardWidgetFunction', 'dashboard', 'normal', 'high' );
}

function TemplaticDashboardWidgetFunction() {
	//error_reporting(E_ALL);
	?>
	<div class="table table_tnews">
    <p class="sub"><strong><?php _e('Templatic News',THEME_DOMAIN); ?></strong></p>
    <div class="trss-widget">
	<?php
	$items = get_transient('templatic_dashboard_news');

    if (empty($items)) {
	include_once(ABSPATH . WPINC . '/class-simplepie.php');
    $trss = new SimplePie();
	$trss->set_timeout(5);
    $trss->set_feed_url('http://feeds.feedburner.com/Templatic');
    $trss->strip_htmltags(array_merge($trss->strip_htmltags, array('h1', 'a')));
    $trss->enable_cache(false);
    $trss->init();
    $items = $trss->get_items(0, 6);
	$cached = array();
	
    foreach ($items as $item) { 
        preg_match('/(.{128}.*?)\b/', $item->get_content(), $matches);
        $cached[] = array(
            'url' => $item->get_permalink(),
            'title' => $item->get_title(),
            'date' => $item->get_date("d M Y"),
            'content' => rtrim($matches[1]) . '...'
        );
    }
	 $items = $cached;
    set_transient('templatic_dashboard_news', $cached, 60 * 60 * 24);
	}
   
	?>
	<ul class="news">
            <?php 
                foreach ($items as $item) {
            ?>
            
                <li class="post">
                    <a href="<?php echo $item['url']; ?>" class="rsswidget"><?php echo $item['title']; ?></a>
                    <span class="rss-date"><?php echo $item['date']; ?></span>
                    <div class="rssSummary"><?php echo strip_tags($item['content']); ?></div>
                </li>
    <?php
                } 
            
            ?>
    </ul>
	</div>
	
	
	</div>
	
	<div class="t_theme">
		
		<div class="t_thumb">


        <?php

			$lastTheme = get_transient('templatic_dashboard_theme');
            if (!$lastTheme) {
               $lastTheme = file_get_contents('http://templatic.com/latest-theme/');
                if ($lastTheme) {
                    set_transient('templatic_dashboard_theme', $lastTheme, 60 * 60 * 24);
                }
           } 
		

        ?>
        <?php if ($lastTheme) echo $lastTheme; ?>

		</div>
<hr / >
	<p class="sub"><strong><?php _e('More...',THEME_DOMAIN); ?></strong></p>
	<ul id="templatic-services">
	<li><a href="http://templatic.com/support">Need support? </a></li>
	<li><a href="http://templatic.com/free-theme-install-service/">Custom services</a></li>
	<li><a href="http://templatic.com/premium-themes-club">Join our theme club</a></li>
	</ul>	
	</div>
    <div class="clearfix"></div>
	<?php
}
/* Add user profile fields */
add_action( 'show_user_profile', 'supreme_show_extra_profile_fields' );
add_action( 'personal_options_update', 'supreme_save_extra_profile_fields',10 );
add_action( 'edit_user_profile_update', 'supreme_save_extra_profile_fields',10 );

function supreme_show_extra_profile_fields( $user ) { global $user_id;
	$cur_user = get_userdata($user_id);
?>

	<h3><?php _e('Extra profile information',THEME_DOMAIN); ?></h3>

	<table class="form-table">

		<tr>
			<th><label for="twitter"><?php _e('Twitter',THEME_DOMAIN) ; ?></label></th>

			<td>
				<input type="text" name="twitter" id="twitter" value="<?php echo esc_attr($user->twitter ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e('Please enter your twitter link.',THEME_DOMAIN); ?>*</span>
			</td>
		</tr>
		
		<tr>
			<th><label for="facebook"><?php _e('Facebook',THEME_DOMAIN); ?></label></th>

			<td>
				<input type="text" name="facebook" id="facebook" value="<?php echo esc_attr(  get_user_meta($user_id,'facebook',true ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e('Please enter your facebook profile/page link.',THEME_DOMAIN); ?>*</span>
			</td>
		</tr>

	</table>
<?php }

/* save profile fields */
function supreme_save_extra_profile_fields( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;
	update_user_meta( $user_id, 'twitter', $_POST['twitter'] );
	update_user_meta( $user_id, 'facebook', $_POST['facebook'] );
}
?>