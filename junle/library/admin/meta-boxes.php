<?php
/**
 * Adds the template meta box to the post editing screen for public post types.  This feature allows users and 
 * devs to create custom templates for any post type, not just pages as default in WordPress core.  The 
 * functions in this file create the template meta box and save the template chosen by the user when the 
 * post is saved.  This file is only used if the theme supports the 'supreme-core-template-hierarchy' feature.
 *
 * @package HybridCore
 * @subpackage Admin
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2012, Justin Tadlock
 * @link http://themehybrid.com/supreme-core
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Add the post template meta box on the 'add_meta_boxes' hook. */
add_action( 'add_meta_boxes', 'supreme_meta_box_post_add_template', 10, 2 );
add_action( 'add_meta_boxes', 'supreme_meta_box_post_remove_template', 10, 2 );

/* Save the post template meta box data on the 'save_post' hook. */
add_action( 'save_post', 'supreme_meta_box_post_save_template', 10, 2 );

/**
 * Adds the post template meta box for all public post types, excluding the 'page' post type since WordPress 
 * core already handles page templates.
 *
 * @since 1.2.0
 * @return void
 */
 /*
function supreme_meta_box_post_add_template( $post_type, $post ) {

	$post_type_object = get_post_type_object( $post_type );
	
	/* Only add meta box if current user can edit, add, or delete meta for the post. */
	/*if ( ( true === $post_type_object->public ) && ( current_user_can( 'edit_post_meta', $post->ID ) || current_user_can( 'add_post_meta', $post->ID ) || current_user_can( 'delete_post_meta', $post->ID ) ) && 'post' == $post_type )
		add_meta_box( 'supreme-core-post-template', __( 'Template', 'supreme-core' ), 'supreme_metabox_post_display_template', $post_type, 'side', 'default' );
}
*/

/**
 * Displays the post template meta box.

 */
function supreme_metabox_post_display_template( $object, $box ) {

	/* Get the post type object. */
	$post_type_object = get_post_type_object( $object->post_type );

	/* Get a list of available custom templates for the post type. */
	$templates = supreme_get_post_templates( array( 'label' => array( "{$post_type_object->labels->singular_name} Template", "{$post_type_object->name} Template" ) ) );

	wp_nonce_field( basename( __FILE__ ), 'supreme-core-post-meta-box-template' ); ?>

	<p>
		<?php if ( 0 != count( $templates ) ) { ?>
			<select name="supreme-post-template" id="supreme-post-template" class="widefat">
				<option value=""></option>
				<?php foreach ( $templates as $label => $template ) { ?>
					<option value="<?php echo esc_attr( $template ); ?>" <?php selected( esc_attr( get_post_meta( $object->ID, "_wp_{$post_type_object->name}_template", true ) ), esc_attr( $template ) ); ?>><?php echo esc_html( $label ); ?></option>
				<?php } ?>
			</select>
		<?php } else { ?>
			<?php _e( 'No templates exist for this post type.', 'supreme-core' ); ?>
		<?php } ?>
	</p>
<?php
}

/**
 * Saves the post template meta box settings as post metadata.
 *
 * @since 1.2.0
 * @param int $post_id The ID of the current post being saved.
 * @param int $post The post object currently being saved.
 * @return void|int
 */
function supreme_meta_box_post_save_template( $post_id, $post ) {

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['supreme-core-post-meta-box-template'] ) || !wp_verify_nonce( $_POST['supreme-core-post-meta-box-template'], basename( __FILE__ ) ) )
		return $post_id;

	/* Return here if the template is not set. There's a chance it won't be if the post type doesn't have any templates. */
	if ( !isset( $_POST['supreme-post-template'] ) )
		return $post_id;

	/* Get the posted meta value. */
	$new_meta_value = $_POST['supreme-post-template'];

	/* Set the $meta_key variable based off the post type name. */
	$meta_key = "_wp_{$post->post_type}_template";

	/* Get the meta value of the meta key. */
	$meta_value = get_post_meta( $post_id, $meta_key, true );

	/* If there is no new meta value but an old value exists, delete it. */
	if ( current_user_can( 'delete_post_meta', $post_id ) && '' == $new_meta_value && $meta_value )
		delete_post_meta( $post_id, $meta_key, $meta_value );

	/* If a new meta value was added and there was no previous value, add it. */
	elseif ( current_user_can( 'add_post_meta', $post_id, $meta_key ) && $new_meta_value && '' == $meta_value )
		add_post_meta( $post_id, $meta_key, $new_meta_value, true );

	/* If the new meta value does not match the old value, update it. */
	elseif ( current_user_can( 'edit_post_meta', $post_id ) && $new_meta_value && $new_meta_value != $meta_value )
		update_post_meta( $post_id, $meta_key, $new_meta_value );
}
/**
 * Remove the meta box from some post types.
 *
 * @since 1.3.0
 * @param string $post_type The post type of the current post being edited.
 * @param object $post The current post being edited.
 * @return void
 */ 
function supreme_meta_box_post_remove_template( $post_type, $post ) {

	/* Removes meta box from pages since this is a built-in WordPress feature. */
	if ( 'page' == $post_type )
		remove_meta_box( 'supreme-core-post-template', 'page', 'side' );

}

/**
 * Adds the SEO meta box to the post editing screen for public post types.  This feature allows the post author 
 * to set a custom title, description, and keywords for the post, which will be viewed on the singular post page.  
 * To use this feature, the theme must support the 'supreme-core-seo' feature.  The functions in this file create
 * the SEO meta box and save the settings chosen by the user when the post is saved.
 */

/* Add the post SEO meta box on the 'add_meta_boxes' hook. */
add_action( 'add_meta_boxes', 'supreme_meta_box_post_add_seo', 10, 2 );
add_action( 'add_meta_boxes', 'supreme_meta_box_post_remove_seo', 10, 2 );

/* Save the post SEO meta box data on the 'save_post' hook. */
add_action( 'save_post', 'supreme_meta_box_post_save_seo', 10, 2 );

/*
Name : supreme_meta_box_post_add_seo
Description : Adds the post SEO meta box for all public post types.
Arguments : post tyle and post
return :metabox in admin
*/
function supreme_meta_box_post_add_seo( $post_type, $post ) {

	$post_type_object = get_post_type_object( $post_type );

	/* Only add meta box if current user can edit, add, or delete meta for the post. */
	if ( ( true === $post_type_object->public ) && ( current_user_can( 'edit_post_meta', $post->ID ) || current_user_can( 'add_post_meta', $post->ID ) || current_user_can( 'delete_post_meta', $post->ID ) ) )
		add_meta_box( 'supreme-core-post-seo', __( 'SEO', 'supreme-core' ), 'supreme_meta_box_post_display_seo', $post_type, 'normal', 'high' );
}

/*
Name : supreme_meta_box_post_display_seo
Description :Displays the post SEO meta box.
return :metabox in admin
*/
function supreme_meta_box_post_display_seo( $object, $box ) {

	wp_nonce_field( basename( __FILE__ ), 'supreme-core-post-seo' ); ?>

	<p>
		<label for="supreme-document-title"><?php _e( 'Document Title', 'supreme-core' ); echo ": "; ?></label>
		<br />
		<input type="text" name="supreme-document-title" id="supreme-document-title" value="<?php echo esc_attr( get_post_meta( $object->ID, 'Title', true ) ); ?>" size="30" tabindex="30" style="width: 99%;" />
	</p>

	<p>
		<label for="supreme-meta-description"><?php _e( 'Meta Description', 'supreme-core' ); echo ": "; ?></label>
		<br />
		<textarea name="supreme-meta-description" id="supreme-meta-description" cols="60" rows="2" tabindex="30" style="width: 99%;"><?php echo esc_textarea( get_post_meta( $object->ID, 'Description', true ) ); ?></textarea>
	</p>

	<p>
		<label for="supreme-meta-keywords"><?php _e( 'Meta Keywords', 'supreme-core' ); echo ": "; ?></label>
		<br />
		<input type="text" name="supreme-meta-keywords" id="supreme-meta-keywords" value="<?php echo esc_attr( get_post_meta( $object->ID, 'Keywords', true ) ); ?>" size="30" tabindex="30" style="width: 99%;" />
	</p>
<?php }

/*
Name : supreme_meta_box_post_save_seo
Description :Saves the post SEO meta box settings as post metadata.
return :save meta boxies values
*/
function supreme_meta_box_post_save_seo( $post_id, $post ) {

	$prefix = supreme_prefix();

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['supreme-core-post-seo'] ) || !wp_verify_nonce( $_POST['supreme-core-post-seo'], basename( __FILE__ ) ) )
		return $post_id;

	$meta = array(
		'Title' => 	$_POST['supreme-document-title'],
		'Description' => 	$_POST['supreme-meta-description'],
		'Keywords' => 	$_POST['supreme-meta-keywords']
	);

	foreach ( $meta as $meta_key => $new_meta_value ) {

		/* Get the meta value of the custom field key. */
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		/* If there is no new meta value but an old value exists, delete it. */
		if ( current_user_can( 'delete_post_meta', $post_id, $meta_key ) && '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, $meta_key, $meta_value );

		/* If a new meta value was added and there was no previous value, add it. */
		elseif ( current_user_can( 'add_post_meta', $post_id, $meta_key ) && $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, $meta_key, $new_meta_value, true );

		/* If the new meta value does not match the old value, update it. */
		elseif ( current_user_can( 'edit_post_meta', $post_id, $meta_key ) && $new_meta_value && $new_meta_value != $meta_value )
			update_post_meta( $post_id, $meta_key, $new_meta_value );
	}
}

/*
Name :supreme_meta_box_post_remove_seo
Description: Remove the meta box from some post types.
 */ 
function supreme_meta_box_post_remove_seo( $post_type, $post ) {

	/* Removes post stylesheets support of the bbPress 'topic' post type. */
	if ( function_exists( 'bbp_get_topic_post_type' ) && bbp_get_topic_post_type() == $post_type )
		remove_meta_box( 'supreme-core-post-seo', bbp_get_topic_post_type(), 'normal' );

	/* Removes post stylesheets support of the bbPress 'reply' post type. */
	elseif ( function_exists( 'bbp_get_reply_post_type' ) && bbp_get_reply_post_type() == $post_type )
		remove_meta_box( 'supreme-core-post-seo', bbp_get_reply_post_type(), 'normal' );
}

?>