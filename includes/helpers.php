<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Helpers
 */

// ...

/**
 * Retrieves a single setting
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param string | (optional) string
 * @return string | array
 **/

if( !function_exists('twp_get_option') ){

    function twp_get_option( $option, $key = null ){
        
        $data = get_option( $option . '_options' );
        
        if( $key != null && isset( $data[$key] ) ) :
            return $data[$key];
		elseif( $key != null && ! isset( $data[$key] ) ) :
			return false;
		endif;

        return $data;
        
    }
}

// ...

/**
 * Deletes option
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param string
 * @return N/A
 **/

if( !function_exists('twp_delete_settings') ){

    function twp_delete_settings( $option ){
        
        delete_option( $option . '_options' );
        
    }
}

// ...

/**
 * Updates settings in an option group
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param string | anything..
 * @return false or int | string | array | ...
 **/

if( !function_exists('twp_update_settings') ){

    function twp_update_settings( $option, $new_value ){
        
        return update_option( $option . '_options', $new_value );
        
    }
}

// ...

/**
 * Finds next key in an array based on provided one
 * If given one is last one, reverses to the first key (clever, huh)
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param array | string / int
 * @return string / int
 **/

if( ! function_exists( 'twp_get_next_in_array' ) ) :

function twp_get_next_in_array($array, $key) {
    
    reset( $array ); // make sure we start at the beginning
    $keys = array_keys( $array );
    
    while(key($array) !== $key) next($array); // find the position of the current key and set the pointer
    
    if( twp_has_next( $array ) ) 
        return next( $array );
    
    // Fallback - return first element
    reset( $array );
    return $array[ key($array) ];

}

endif;

if( ! function_exists( 'twp_has_next' ) ) :

    function twp_has_next(array $a){
        return next($a) !== false ?: each($a) !== false;
    }

endif;

// ...

if( ! function_exists( 'twp_multidimensional_search' ) ) :

function twp_multidimensional_search($parents, $searched) { 
    
    if (empty($searched) || empty($parents)) { 
        return false; 
    } 

    foreach ($parents as $key => $value) { 
        $exists = true; 
        foreach ($searched as $skey => $svalue) { 
         $exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue); 
        } 
        if($exists){ return $key; } 
    } 

    return false; 
} 

endif;

// ...

/**
 * Basically brings two string to the simplest form and compares them
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param string | string
 * @return boolean
 **/

if( ! function_exists( 'twp_compare_tweet_templates' ) ) :

function twp_compare_tweet_templates( $t1, $t2 ) {
     
    $t1 = sanitize_title_with_dashes( $t1 );
    $t2 = sanitize_title_with_dashes( $t2 );
    
    if( $t1 == $t2 )
        return true;
    
    return false;

}

endif;

// ...

/**
 * Checks if post type is enabled in Tweet Wheel
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param string
 * @return boolean
 **/

if( ! function_exists( 'twp_is_post_type_enabled' ) ) :

function twp_is_post_type_enabled( $post_type ) {
     
	$post_types = twp_get_all_enabled_post_types();

	if( empty( $post_types ) || ! is_array( $post_types ) )
		return false;
	
	if( in_array( $post_type, $post_types ) )
		return true;
    
    return false;

}

endif;

// ...

/**
 * Get an array of all enabled post types
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param format
 * @return array | boolean
 **/

if( ! function_exists( 'twp_get_all_enabled_post_types' ) ) :

function twp_get_all_enabled_post_types( $format = null ) {
     
	$post_types = twp_get_option( 'twp_settings', 'post_type' );
	
	if( empty( $post_types ) || ! is_array( $post_types ) )
		return false;
	
	$data = '';
	
	switch( $format ) :
		
		case 'string':
		$data = implode( ',', $post_types );
		break;
		
		default:
		$data = $post_types;
		break;
		
	endswitch;
    
    return $data;

}

endif;


// ...

/**
 * A callback for {{URL}} template tag
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param N/A
 * @return N/A
 **/

if( ! function_exists( 'twp_tweet_parse_url' ) ) :

function twp_tweet_parse_url( $post_id ) {

    return get_permalink( $post_id );
    
}

endif;

// ...

if( ! function_exists( 'twp_tweet_parse_shorten_url' ) ) :

function twp_tweet_parse_shorten_url( $post_id ) {
  
    $shortened_url = TWP()->link_shortening()->shorten_url( get_permalink( $post_id ), TWP()->link_shortening()->get_domain() );

    if( $shortened_url->status_code == 200 ) :

        set_transient( 'twp_short_url_data_' . $post_id, $shortened_url->data->url );

        return $shortened_url->data->url;

    endif;

    // fallback
    return twp_tweet_parse_url( $post_id );
    
}

endif;

// ...

/**
 * A callback for {{TITLE}} template tag
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param N/A
 * @return N/A
 **/

if( ! function_exists( 'twp_tweet_parse_title' ) ) :

function twp_tweet_parse_title( $post_id ) {
    
    $clean = strip_tags( get_the_title($post_id) ); // some people do have HTML in post titles so gotta get rid of it
    
    return html_entity_decode($clean,ENT_QUOTES,'UTF-8');
    
}

endif;

// ...

/**
 * A callback for {{EXCERPT}} template tag
 *
 * @type function
 * @date 13/02/2016
 * @since 2.0.2
 *
 * @param N/A
 * @return N/A
 **/

if( ! function_exists( 'twp_tweet_parse_excerpt' ) ) :

function twp_tweet_parse_excerpt( $post_id ) {
    
    if( ! post_type_supports( get_post_type( $post_id ), 'excerpt' ) )
        return '';
    
    return strip_tags(html_entity_decode(get_post_field('post_excerpt', $post_id),ENT_QUOTES,'UTF-8'));
    
}

endif;

// ...

/**
 * Counts characters in Twitter way
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param string
 * @return int
 */

if( ! function_exists( 'twp_character_counter' ) ) :

function twp_character_counter( $raw, $post_id = null ) {
    
    global $post;
    
    if( $post_id == null )
        $post_id = $post->ID;
    
    // Max characters accepted for a single tweet
    $maxCharacters = 280;
    
    // Load custom tweet text to a variable
    $tweet_template = $raw;
    
    // ...
    
    $tags = TWP()->tweet()->allowed_tags();

    if( ! empty( $tags ) ) : 
    
        foreach( $tags as $t => $func ) :
    
            $tweet_template = str_replace( '{{' . $t . '}}', call_user_func( $func, $post_id, null ), $tweet_template );
    
        endforeach;
    
    endif;
    
    /**
     * Calculate a whole string length
     */
    
    $current_length = mb_strlen( $tweet_template );

    // ...
    
    /**
     * Amend character limit if URL is detected (22 characters per url)
     */
    
    $url_chars = 23;

    // urls will be an array of URL matches
    preg_match_all("/(?:(?:https?|ftp):\\/\\/)?(?:\\S+(?::\\S*)?@)?(?:(?!(?:10|127)(?:\\.\\d{1,3}){3})(?!(?:169\\.254|192\\.168)(?:\\.\\d{1,3}){2})(?!172\\.(?:1[6-9]|2\\d|3[0-1])(?:\\.\\d{1,3}){2})(?:[1-9]\\d?|1\\d\\d|2[01]\\d|22[0-3])(?:\\.(?:1?\\d{1,2}|2[0-4]\\d|25[0-5])){2}(?:\\.(?:[1-9]\\d?|1\\d\\d|2[0-4]\\d|25[0-4]))|(?:(?:[a-z\\x{00a1}-\\x{ffff}0-9]+-?)*[a-z\\x{00a1}-\\x{ffff}0-9]+)(?:\\.(?:[a-z\\x{00a1}-\\x{ffff}0-9]+-?)*[a-z\\x{00a1}-\\x{ffff}0-9]+)*(?:\\.(?:[a-z\\x{00a1}-\\x{ffff}]{2,})))(?::\\d{2,5})?(?:\\/?[^\\s]*)?/u", $tweet_template, $urls);
    
    $urls = array_shift( $urls );
    
    // If urls were found, play the max character value accordingly
    if( ! empty( $urls ) ) {
        
        foreach( $urls as $u ) {
            
            // get url length difference
            $diff = $url_chars - strlen( $u );
            
            // apply difference
            $current_length = $current_length + $diff;
           
        }
        
    }
    
    // ...
    
    // return actually tweet length
    return $current_length;
    
}

endif;

// ...

/**
 * Notifies user about Lite version being installed and suggests further steps
 *
 * @type function
 * @date 08/07/2015
 * @since 1.2
 *
 * @param string
 * @return int
 */

if( ! function_exists( 'twp_import_notice' ) ) :

    function twp_import_notice() {
        
        // Check if Tweet Wheel Lite plugin is present and present with a notification
        if ( 
            ! in_array( 'tweet-wheel/tweetwheel.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) &&
            file_exists( ABSPATH . 'wp-content/plugins/tweet-wheel/tweetwheel.php' )
        ) :
        
            ?>
            
            <div id="tw-import-data-prompt" class="error tw-alert">
                <p><?php _e( 'We\'ve detected Tweet Wheel Lite version installed. Would you like to migrate settings?', TWP_TEXTDOMAIN ); ?> <a id="twp-import-data" href="#" class="button" style="margin-left:10px;"><?php _e( 'Yes, please!', TWP_TEXTDOMAIN ); ?></a> <a id="twp-uninstall-lite" href="#" class="button button-primary" style="margin-left:10px;background:#D3000D;border-color:#9A0009"><?php _e( 'Uninstall Tweet Wheel Lite', TWP_TEXTDOMAIN ); ?></a></p>
            </div>
            
            <script>
                jQuery.noConflict();
                
                jQuery(document).ready(function(){
                    
                    jQuery( '#twp-import-data' ).click( function() {
                        
                        var r = confirm( "<?php _e( 'Warning! Importing data from Tweet Wheel Lite will override all settings, templates and queue in the Pro version. Are you sure you want to continue?', TWP_TEXTDOMAIN ); ?>");
                        
                        if (r == true) {
                            
                            jQuery('#tw-import-data-prompt').html('<div class="tw-white-spinner"></div><p>Importing data from Tweet Wheel Lite...</p>');
                            
                            jQuery.post(
                                ajaxurl, 
                                { 
                                    action: 'import_data_from_lite', 
                                    nonce: _TWPAJAX.twNonce 
                                },
                                function( response ) {

                                    if( response == 'success' ) {
                                        
                                        jQuery('#tw-import-data-prompt').html('<p>Success! Please check if data has been imported correctly. If so, then <a id="twp-uninstall-lite" href="#" class="button button-primary" style="margin-left:10px;background:#D3000D;border-color:#9A0009"><?php _e( 'Uninstall Tweet Wheel Lite', TWP_TEXTDOMAIN ); ?></a></p>');
                                        
                                    }
                                }
                            );
                            
                        }
                        
                    });
                    
                    jQuery( document ).on( 'click', '#twp-uninstall-lite', function() {
                        
                        var r = confirm( "<?php _e( 'This will permanently delete all plugin files and settings. You will not be able to recover any saved templates or queued up posts. Do you want to continue?', TWP_TEXTDOMAIN ); ?>");
                        
                        if (r == true) {
                            
                            jQuery('#tw-import-data-prompt').html('<div class="tw-white-spinner"></div><p>Uninstalling Tweet Wheel Lite...</p>');
                            
                            jQuery.post(
                            ajaxurl, 
                            { 
                                action : 'twp_ajax_action',
                                admin_action: 'delete_lite', 
                                nonce: _TWPAJAX.twNonce 
                            },
                            function( response ) {

                                if( response == 'success' ) {
                                    jQuery('#tw-import-data-prompt').html('<p>Success! You can carry on with your day =)</p>');   
                                }
                                
                            }
                        );
                            
                        }
                        
                    });
                    
                });
                
            </script>

            <?php
        
        endif;
        
    }

    add_action( 'admin_notices', 'twp_import_notice' );

endif;

// ...

/**
 * twp_is_edit_page 
 * function to check if the current page is a post edit page
 * 
 * @author Ohad Raz <admin@bainternet.info>
 * 
 * @param  string  $new_edit what page to check for accepts new - new post page ,edit - edit post page, null for either
 * @return boolean
 */

if( ! function_exists( 'twp_is_edit_page' ) ) :

function twp_is_edit_page($new_edit = null){
    global $pagenow;
    //make sure we are on the backend
    if (!is_admin()) return false;


    if($new_edit == "edit")
        return in_array( $pagenow, array( 'post.php',  ) );
    elseif($new_edit == "new") //check for new post page
        return in_array( $pagenow, array( 'post-new.php' ) );
    else //check for either new or edit
        return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
}

endif;

// ...

if( ! function_exists( 'twp_array_insert' ) ) :

/**
 * @param array      $array
 * @param int|string $position
 * @param mixed      $insert
 */
function twp_array_insert(&$array, $position, $insert) {
    if (is_int($position)) {
        array_splice($array, $position, 0, $insert);
    } else {
        $pos   = array_search($position, array_keys($array));
        $array = array_merge(
            array_slice($array, 0, $pos),
            $insert,
            array_slice($array, $pos)
        );
    }
}

endif;

// ...

if( ! function_exists( 'twp_remove_footer_admin' ) ) :

$twp_menu_pages = array( 'twp_auth', 'twp_queues', 'twp_about', 'twp_debug' );

if( is_admin() && isset( $_GET['page'] ) && in_array( $_GET['page'], $twp_menu_pages ) ) :

    /**
     * Add a class or many to the body in the dashboard
     */
    function twp_add_admin_body_class( $classes ) {
        return "$classes twp-screen";
    }
    add_filter( 'admin_body_class', 'twp_add_admin_body_class' );

endif;

endif;