<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Abraham\TwitterOAuth\TwitterOAuth;

class TWP_Tweet {
    
    private $tags;
    
    public static $_instance = null;

    // ...
    
	/**
	 * Main TWP_Tweet Instance
	 *
	 * Ensures only one instance of TWP_Tweet is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @return TWP_Tweet - Main instance
	 */
    
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
    
    // ...
    
    /**
     * Class constructor
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    public function __construct() {

        // Loads allowed tags for tweet template
        $this->tags = $this->allowed_tags();
        
        // Required JS variables for template tags
        add_action( 'admin_print_scripts', array( $this, 'mb_print_js' ) );
        
    }
    
    // ...
    
    /**
     * Metabox JS variables - template tags.
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    public function mb_print_js() {
        
        global $post;
        
        if( $post == null || empty( $this->tags ) )
            return;
        
        $id = $post->ID;
        
        ?>

        <script>
            var twp_template_tags = {
        <?php
        
        $i = 1;
        
        foreach( $this->tags as $tag => $func ) :
        
            ?>
            <?php echo strtoupper( $tag ); ?> : '<?php echo call_user_func( $func, $id ); ?>'<?php echo $i != count($this->tags) ? ',' : ''; ?>
            <?php
            
            $i++;
        
        endforeach; 
        
        ?>
                
            };
        
        </script>
        
        <?php
        
    }
    
    // ...
    
    /**
     * Returns a ready-to-go tweet; a tweet in its final form
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return string
     **/
    
    public function preview( $post_id ) {
        
        return $this->parse( $post_id );
        
    }
    
    // ...
    
    /**
     * Parses a tweet template; replaces tags with a proper values.
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return string
     **/
    
    public function parse( $post_id, $tweet = null ) {
        
        if( empty( $this->tags ) )
            return;
        
        foreach( $this->tags as $tag => $func ) :
            
            $tweet = str_replace( '{{'.$tag.'}}', call_user_func( $func, $post_id, $tweet ), $tweet );
            
        endforeach; 
        
        return html_entity_decode( $tweet, ENT_QUOTES, 'UTF-8' );
        
    }

    // ...
    
    /**
     * Include allowed template tags. Feel free to add your own using the filter.
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return array
     **/
    
    public function allowed_tags() {
        
        $tags = array(
            'URL' => 'twp_tweet_parse_url',
            'TITLE' => 'twp_tweet_parse_title',
            'EXCERPT' => 'twp_tweet_parse_excerpt'
        );

        $tags = apply_filters( 'twp_tweet_allowed_tags', $tags );
        
        return $tags;
        
    }
    
    // ...
    
    /**
     * The Magic!
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     * @update 1.3.5
     *
     * @param N/A
     * @return N/A
     **/
    
    public function tweet( $post_id, $term ) {
        
        global $wpdb;
        
        if( ! TWP()->twitter()->is_authed() )
            return false;
        
        $auth = TWP()->twitter()->get_auth_data();
        
        $term_id = $term->term_id;
        
        $queue = new TWP_Queue( $term );

        $order = $this->get_tweeting_order( $post_id );

        switch( $order ) :

            case 'random';
            $raw_tweet = $this->get_random_template( $post_id );
            break;

            default:
            $raw_tweet = $this->get_next_template( $post_id );
            break;

        endswitch;

        $raw_template = $raw_tweet;
        $raw_tweet = apply_filters( 'twp_tweet_text', $raw_tweet, $post_id );
        
        $tweet = $this->parse( $post_id, $raw_tweet );
        
        // Make sure a tweet is 280 chars. 
        // Consider it a user error and stop script
        if( twp_character_counter( $tweet, $post_id ) > 280 ) :
            return false;
        endif;

        // Create a connection with Twitter
        $connection = new TwitterOAuth( 
            $auth->consumer_key, 
            $auth->consumer_secret,
            $auth->oauth_token,
            $auth->oauth_token_secret
        );

        // Start building args to send to Twitter API
        $args = array(
            'status' => stripslashes($tweet)
        );
    
        // Sending a tweet....
        $response = $connection->post( "statuses/update", $args );

        if( isset( $response->errors ) && is_array( $response->errors ) ) :

            do_action( 'twp_tweet_error', $post_id, $response );

            return array( 'status' => 'error', 'errormsg' => $response->errors[0]->message . ' (#' . $response->errors[0]->code . ')' );
            
        endif;
        
        update_term_meta( $term_id, 'twp_last_tweeted_time', current_time( 'timestamp' ) );
        update_post_meta( $post_id, 'twp_last_tweeted_template', $raw_template );
        
        do_action( 'twp_before_tweet_dequeue', $post_id );
        
        /**
         * If the order isn't random, remove post from the queue
         * and (optionally) append at the bottom
         */
        if( $queue->settings()->order() != 'random' ) :
        
            // Remove post from the queue
            $queue->posts()->remove_post( $post_id );

            do_action( 'twp_after_tweet_dequeue', $post_id );

            // If loop goes infinitely
            if( $queue->settings()->publish_action() == 'loop' ) :

                $queue->posts()->insert_post( $post_id, $queue->posts()->get_last_queued_post_id() );

            endif;
        
        endif;
        
        do_action( 'twp_after_tweet', $post_id );

        return $post_id;
        
    }

    // ...
    
    /**
     * Check if a post has multiple templates
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param int
	 * @return boolean
     */
    
    public function has_multiple_templates( $post_id ) {
     
        if( $post_id == null )
            return;
        
        $meta = get_post_meta( $post_id, 'twp_post_templates', true );
        
        if( count( $meta ) > 1 )
            return true;
        
        return false;
        
    }
    
    // ...
    
    /**
     * Count post templates
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param int
	 * @return int | null
     */
    
    public function count_templates( $post_id ) {
     
        if( $post_id == null )
            return;
        
        $meta = get_post_meta( $post_id, 'twp_post_templates', true );
        
        return count( $meta );
        
    }
    
    // ...
    
    /**
     * Checks if a post has any custom templates (even one)
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param int
	 * @return boolean
     */
    
    public function has_custom_templates( $post_id ) {
     
        if( $post_id == null )
            return;
        
        $meta = get_post_meta( $post_id, 'twp_post_templates', true );
        
        if( $meta == '' || count( $meta ) == 0 )
            return false;
        
        return true;
        
    }
    
    // ...
    
    /**
     * Checks if a post has an attached image
     *
     * @type function
     * @date 06/06/2015
	 * @since 1.0
     *
     * @param int
	 * @return boolean
     */
    
    public function has_image( $post_id ) {
     
        if( $post_id == null )
            return;
        
        $meta = get_post_meta( $post_id, 'exclude_tweet_image', true );

        if( has_post_thumbnail( $post_id ) && $meta != 1 )
            return true;
        
        return false;
        
    }
    
    // ...
    
    /**
     * Retrieve post's all custom templates
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param int
	 * @return null | array
     */
    
    public function get_custom_templates( $post_id ) {
        
        if( $post_id == null )
            return;
        
        return get_post_meta( $post_id, 'twp_post_templates', true );
        
    }
    
    // ...
    
    /**
     * Retrieves default template setting
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return string
     */
    
    public function get_default_template() {
     
        return twp_get_option( 'twp_settings', 'tweet_template' );

    }
    
    // ...
    
    /**
     * Retrieves last tweeted template for a post
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param int
	 * @return string | false
     */

    public function get_last_tweeted_template( $post_id ) {
        
        $template = get_post_meta( $post_id, 'twp_last_tweeted_template', true );
        
        if( '' != $template )
            return $template;
        
        return false;
        
    }
    
    // ...
    
    /**
     * Retrieves tweeting order for a post (random or following the order)
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param int
	 * @return string
     */
    
    public function get_tweeting_order( $post_id ) {
        
        return get_post_meta( $post_id, 'twp_templates_order', true ); 
        
    }
    
    // ...
    
    /**
     * Retrieves random template for a post
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param int
	 * @return string
     */
    
    public function get_random_template( $post_id ) {
        
        // fallback if misused on single-templated post
        if( ! TWP()->tweet()->has_multiple_templates( $post_id ) )
            return $this->get_next_template( $post_id );
        
        $meta = TWP()->tweet()->get_custom_templates( $post_id );
        $sanitized = '';

        foreach( $meta as $k => $v ) :
        
            $sanitized[$k] = sanitize_title_with_dashes( $v );
        
        endforeach;
        
        // check for last tweeted
        $last_tweeted_template = $this->get_last_tweeted_template( $post_id );
        
        if( $last_tweeted_template ) :
        
            $last_tweeted_template = sanitize_title_with_dashes( $last_tweeted_template );

            $key = array_search( $last_tweeted_template, $sanitized );

            if( false !== $key && isset( $meta[$key] ) )
                unset( $meta[$key] );
        
        endif;
        
        return $meta[array_rand( $meta )];
        
    }
    
    // ...
    
    /**
     * Retrieves next template for a post (the one after recently tweeted one)
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param int
	 * @return string
     */
    
    public function get_next_template( $post_id ) {
        
        // custom & multiple
        if( $this->has_multiple_templates( $post_id ) ) :
        
            $meta = $this->get_custom_templates( $post_id );
            $sanitized = '';

            foreach( $meta as $k => $v ) :

                $sanitized[$k] = sanitize_title_with_dashes( $v );

            endforeach;
        
            // @TODO - get it from post meta or sth
            $last_tweeted_template = sanitize_title_with_dashes( $this->get_last_tweeted_template( $post_id ) );
        
            $key = array_search( $last_tweeted_template, $sanitized );
        
            // If last tweeted template no longer exist, fallback to first in the array
            if( $key === false ) :
                
                $key = key($sanitized);
        
                return $meta[$key];    
            
            // If last tweeted template exists, go for next!
            else :
                
                return twp_get_next_in_array( $meta, $key );
                    
            endif;
        
        endif;
    
        // custom template
        if( $this->has_custom_templates( $post_id ) )
            return array_shift( $this->get_custom_templates( $post_id ) );
        
        // fallback to default
        return $this->get_default_template();
        
    }

}
// has to be here for js tags templates var to be printed in admin header... not sure why, gotta sort it later!
new TWP_Tweet;