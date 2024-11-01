<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Creates all metaboxes used within the plugin
 *
 * @since 1.0
 * @date 16/06/2015
 */

// ...

/**
 * Fire our meta box setup function on the post editor screen. 
 *
 * @since 1.0
 */

if( TWP()->twitter()->is_authed() ) :

	add_action( 'load-post.php', 'twp_post_meta_boxes_setup' );

endif;

// ...

/**
 * Meta box setup function. 
 *
 * @since 1.0
 */

function twp_post_meta_boxes_setup() {
    
    $post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'] ;
    
    $post = get_post( $post_id );
    
    if( $post && $post->post_status == 'publish' ) :

        /* Add meta boxes on the 'add_meta_boxes' hook. */
        add_action( 'add_meta_boxes', 'twp_exclude_tweet_templates_meta' );
    
    endif;

}

// ...

/**
 * Add all metaboxes
 *
 * @since 1.0
 */

function twp_exclude_tweet_templates_meta() {
	
	$post_types = twp_get_option( 'twp_settings', 'post_type' );
    
    $allowed_pts = twp_get_all_enabled_post_types();
	
	if( empty( $post_types ) || ! is_array( $post_types ) )
		return;
	
	foreach( $post_types as $post_type ) :

		add_meta_box(
			'tw-tweet-settings',
			esc_html__( 'Tweet Settings', TWP_TEXTDOMAIN ),
			'twp_tweet_settings_meta_box',
			$allowed_pts,
			'normal',
			'default'
		);

		add_meta_box(
			'tw-tweet-templates',
			esc_html__( 'Tweet Templates', TWP_TEXTDOMAIN ),
			'twp_tweet_templates_meta_box',
			$allowed_pts,
			'normal',
			'default'
		);

	endforeach;
    
}

add_action( 'save_post', 'twp_save_tweet_templates_meta', 10, 2 );

// ...

/*

Particular metaboxes down below

*/

// ...

/***************************************
 * Tweet Wheel Settings Metabox
 **************************************/

function twp_tweet_settings_meta_box( $object, $box ) {
    
    wp_nonce_field( basename( __FILE__ ), 'tweet_settings_nonce' ); 
    
    $tweet_order = get_post_meta( $object->ID, 'twp_templates_order', true);
    $tweet_order = $tweet_order == '' ? 'order' : $tweet_order;

    ?>

    <div class="tw-metabox tw-tweet-settings">

        <p>
            <span class="section-title"><?php _e( 'Queue Assignment', TWP_TEXTDOMAIN ); ?></span>
            <span class="section-note"><?php _e( 'Choose queues to which post should be assigned. Alternatively leave all unchecked to exclude it from the plugin.', TWP_TEXTDOMAIN ); ?></span>
            
            <?php
        
            $queues = TWP()->queues()->get_queues();

            if( $queues ) : ?>
                
                <ul class="manage-queue-tooltip buttons-list">

                    <?php

                    foreach( $queues as $q ) :

                        $in_term = has_term( $q->term_id, 'twp_queue', $object->ID ) ? true : false;

                        echo '<li data-post-id="' . $object->ID . '" data-term-id="' . $q->term_id . '" class="tw-queue-post ' . ( $in_term ? 'in-queue' : 'not-in-queue' ) . '">' . $q->name . '</li>';

                    endforeach;

                    ?>

                </ul>

            <?php else : ?>

                <span style="line-height:28px;">You haven\'t added any queues yet.</span>&nbsp;&nbsp;<a href="'.admin_url('/admin.php?page=twp_queues').'" class="button button-primary">Add</a>';

            <?php endif; ?>

        </p>
        
        <hr/>
        
        <p>
            <a href="<?php echo TWP_UPGRADE_LINK; ?>" target="_blank" style="display:block;width:100%;">
                <img width="871" height="78" src="<?php echo TWP_PLUGIN_URL; ?>/assets/images/go-pro/featured-image.png">
            </a>
        </p>
        
        <hr/>
        
        <p>
            <span class="section-title"><?php _e( 'Templates Order', TWP_TEXTDOMAIN ); ?></span>
            <span class="section-note"><?php _e( 'Ignore if you are using a default tweet template or a single custom one. Otherwise, please choose whether you would want your templates to be used in the order or randomly picked.', TWP_TEXTDOMAIN ); ?></span>
            <input type="radio" name="twp_templates_order" id="twp_templates_order" value="order" <?php checked( $tweet_order, 'order' ) ?>>
            <label for="twp_templates_order"><?php _e( 'Follow the order', TWP_TEXTDOMAIN ); ?></label><br/>
            <input type="radio" name="twp_templates_order" id="twp_templates_order_random" value="random" <?php checked( $tweet_order, 'random' ) ?>>
            <label for="twp_templates_order_random"><?php _e( 'Randomise selection', TWP_TEXTDOMAIN ); ?></label>
        </p>
        
    </div>
    
<?php }

// ...

/* Save the meta box's post metadata. */
function twp_save_tweet_settings_meta( $post_id, $post ) {
    
    /* Verify the nonce before proceeding. */
    if ( !isset( $_POST['tweet_settings_nonce'] ) || !wp_verify_nonce( $_POST['tweet_settings_nonce'], basename( __FILE__ ) ) )
        return $post_id;

    /* Get the post type object. */
    $post_type = get_post_type_object( $post->post_type );

    /* Check if the current user has permission to edit the post. */
    if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
        return $post_id;

    /* Get the posted data and sanitize it for use as an HTML class. */
    $tweet_order = $_POST['twp_templates_order'];

    update_post_meta( $post_id, 'twp_templates_order', $tweet_order );
    
}

add_action( 'save_post', 'twp_save_tweet_settings_meta', 10, 2 );

// ...

/***************************************
 * Tweet Templates Metabox
 **************************************/

function twp_tweet_template_default() {
    
    return apply_filters( 'twp_tweet_template_default', '<div class="tweet-template-item"><span class="tw-remove-tweet-template dashicons dashicons-no control" title="' . __( 'Delete this template', TWP_TEXTDOMAIN ) . '"></span><div class="tw-template-wrap"><div contenteditable="true" class="tweet-template-content" placeholder="' . __( 'Enter your custom tweet text', TWP_TEXTDOMAIN ) . '" required>%s</div><textarea class="tweet-template-textarea" name="twp_post_templates[%d]">%s</textarea><span class="twp-counter">%d</span></div></div>' ); 
    
}

function twp_tweet_templates_meta_box( $object, $box ) { 
    
    wp_nonce_field( basename( __FILE__ ), 'tweet_templates_nonce' ); 
    
    $tweet_templates = get_post_meta( $object->ID, 'twp_post_templates', true );
    
    $template = twp_tweet_template_default();

    ?>

    <div class="tw-metabox tw-tweet-templates">
        <a href="#add-tweet-template" id="add-tweet-template" class="button">
            <?php _e( 'Add a Tweet Template', TWP_TEXTDOMAIN ); ?>
        </a>
        <a href="#how-to" data-content="templates-learn-more" class="tw-learn-more tw-template-learn-more">Learn more<span class="dashicons dashicons-arrow-down"></span></a>
        
        <div id="templates-learn-more" class="tw-learn-more-content">
            <p><?php _e( 'Create as many tweet templates as you like by clicking "Add a Tweet Template" button above. Below you can find tags that you can use within tweet templates.', TWP_TEXTDOMAIN ); ?></p>
            <ul>
                <li>
                    <strong>{{URL}}</strong> - <?php _e( '(mandatory) displays link to this post', TWP_TEXTDOMAIN ); ?>
                </li>
                <li>
                    <strong>{{TITLE}}</strong> - <?php _e( '(optional) display this post title', TWP_TEXTDOMAIN ); ?>
                </li>
            </ul>
            <div style="margin-top:15px;">
                <p style="margin:0px;"><strong>Ready for live hashtag analytics?</strong>&nbsp;&nbsp;<a class="twp-button twp-button-pro" href="<?php echo TWP_UPGRADE_LINK; ?>" target="_blank">GO PRO</a></p>
            </div>
        </div>
        <?php
        
        // now load any templates
        
        if( '' != $tweet_templates ) :
    
            $j = 0;
        
            foreach( $tweet_templates as $t ) :

                echo sprintf( $template, $t, $j, $t, twp_character_counter( $t ) );
    
                $j++;
            
            endforeach;
        
        endif;
            
        ?>
        
    </div>

<?php }

// ...

/* Save the meta box's post metadata. */
function twp_save_tweet_templates_meta( $post_id, $post ) {

    /* Verify the nonce before proceeding. */
    if ( !isset( $_POST['tweet_templates_nonce'] ) || !wp_verify_nonce( $_POST['tweet_templates_nonce'], basename( __FILE__ ) ) )
        return $post_id;

    /* Get the post type object. */
    $post_type = get_post_type_object( $post->post_type );

    /* Check if the current user has permission to edit the post. */
    if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
        return $post_id;

    /* Get the posted data and sanitize it for use as an HTML class. */
    $new_meta_value = isset( $_POST['twp_post_templates'] ) ? $_POST['twp_post_templates'] : '';
    
    $sorted = array();
    
    // Reset keys
    if( ! empty( $new_meta_value ) ) : 

        foreach( $new_meta_value as $m ):

            $sorted[] = $m;

        endforeach;
    
    endif;
    
    /* Get the meta key. */
    $meta_key = 'twp_post_templates';

    /* Get the meta value of the custom field key. */
    $meta_value = get_post_meta( $post_id, $meta_key, true );

    /* If a new meta value was added and there was no previous value, add it. */
    if ( $new_meta_value && '' == $meta_value )
        add_post_meta( $post_id, $meta_key, $new_meta_value, true );

    /* If the new meta value does not match the old value, update it. */
    elseif ( $new_meta_value && $new_meta_value != $meta_value )
        update_post_meta( $post_id, $meta_key, $new_meta_value );

    /* If there is no new meta value but an old value exists, delete it. */
    elseif ( '' == $new_meta_value && $meta_value )
        delete_post_meta( $post_id, $meta_key, $meta_value );

}