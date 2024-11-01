<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Library that handles Twitter API
require_once( TWP_PLUGIN_DIR . '/includes/libraries/twitteroauth/autoload.php' );

use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * TWP_Twitter Class
 *
 * @class TWP_Twitter
 */

class TWP_Twitter {
    
    // Keeps Twitter OAuth data
    private $auth;
    
    public static $_instance = null;

    // ...
    
	/**
	 * Main TweetWheel Twitter Instance
	 *
	 * Ensures only one instance of TweetWheel Twitter is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @return TweetWheel - Main instance
	 */
    
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
    
    public function __construct() {
    
        // Load auth data to the plugin
        $this->auth = (object) array(
            'consumer_key' => get_option( 'twp_twitter_consumer_key' ),
            'consumer_secret' => get_option( 'twp_twitter_consumer_secret' ),
            'oauth_token' => get_option( 'twp_twitter_oauth_token' ),
            'oauth_token_secret' => get_option( 'twp_twitter_oauth_token_secret' )
        );
        
        add_action( 'twp_settings_options_type_deauth', array( $this, 'show_deauth_url' ) );

        // Check if there is a response from Twitter to handle
        add_action( 'init', array( $this, 'maybe_handle_response' ) );
        
    }

    
    // ...
    
    /**
     * Authorize page content
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
     */
    
    public function page() {
        
	    require_once( TWP_PLUGIN_DIR . '/includes/views/auth.php' );
        
    }    
    
    // ...
    
    /**
     * Talks to Twitter. Handles authorisation, deauthorisation.
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    public function maybe_handle_response() {
        
        if( isset( $_GET['deauth'] ) )
            $this->deauthorize();
        
        if( isset( $_POST['consumer_key'] ) ) :
        
            $access_token = $_REQUEST['access_token'];
            $access_token_secret = $_REQUEST['access_token_secret'];
            $consumer_key = $_REQUEST['consumer_key'];
            $consumer_secret = $_REQUEST['consumer_secret'];
        
            $connection = new TwitterOAuth( 
                $consumer_key, 
                $consumer_secret,
                $access_token,
                $access_token_secret
            );

            // Try to authorize with given values
            try {

                $account = $connection->get( 
                    'account/verify_credentials'
                );
                
                if( isset( $account->errors ) )
                    return;
                
                if( $account ) :
                
                    update_option( 'twp_twitter_oauth_token', $access_token );
                    update_option( 'twp_twitter_oauth_token_secret', $access_token_secret );
                    update_option( 'twp_twitter_consumer_key', $consumer_key );
                    update_option( 'twp_twitter_consumer_secret', $consumer_secret );
                    update_option( 'twp_twitter_is_authed', 1 );
                    update_option( 'twp_twitter_screen_name', $account->screen_name );
                
                    if( self::is_authed() == 1 )
                        wp_redirect( admin_url( '/admin.php?page=twp_queues' ) );
                    exit;
                
                endif;
            
            } catch ( Exception $e ) {

                _e( "Your app details were incorrect. Please make sure you got them right!", TWP_TEXTDOMAIN );

            }
        
        endif;
        
    }
    
    // ...
    
    /**
     * Returns user's authorisation data
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return object
     **/
    
    public function get_auth_data() {
        
        return $this->auth;
        
    }
    
    // ...
    
    /**
     * Returns connected Twitter handle
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return object
     **/
    
    public function get_connection() {
        
        // Create a connection with Twitter
        $connection = new TwitterOAuth( 
            $this->auth->consumer_key, 
            $this->auth->consumer_secret,
            $this->auth->oauth_token,
            $this->auth->oauth_token_secret
        );
        
        return $connection;
        
    }
    
    // ...
    
    /**
     * Determines if user is authorised with Twitter
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return boolean
     **/
    
    public static function is_authed() {
        
        if( get_option( 'twp_twitter_is_authed' ) == 1 )
            return true;
        
        return false;
        
    }
    
    // ...
    
    /**
     * Build a deauthorisation button on settings page
     *
     * @type function
     * @date 02/02/2015
     * @since 1.0
     *
     * @param N/A
     * @return string (html)
     **/
    
    public function get_deauth_url() {
        
        return '<a href="' . admin_url( '/admin.php?page=twp_settings&deauth=true' ) . '" class="twp-button twp-button-important">' . __('De-Authorize', TWP_TEXTDOMAIN ) . ' &raquo;</a><p>' . __( 'Tweet Wheel will cease from working after de-authorization. Re-authorization will be required to resume the plugin.', TWP_TEXTDOMAIN ) . '</p>';
        
    }
    
    // ...
    
    public static function show_deauth_url() {
     
        echo '<a href="' . admin_url( '/admin.php?page=twp_settings&deauth=true' ) . '" class="twp-button twp-button-important">De-Authorize &raquo;</a><p>' . __( 'Tweet Wheel will cease from working after de-authorization. Re-authorization will be required to resume the plugin.', TWP_TEXTDOMAIN ) . '</p>';
        
    }
    
    // ...
    
    /**
     * Deauthorises and redirects to authorisation screen
     *
     * @type function
     * @date 02/02/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    public function deauthorize() {

        if( self::is_authed() == true ) :
            
            delete_option( 'twp_twitter_consumer_key' );
            delete_option( 'twp_twitter_consumer_secret' );
            delete_option( 'twp_twitter_oauth_token' );
            delete_option( 'twp_twitter_oauth_token_secret' );
            delete_option( 'twp_twitter_is_authed' );
            delete_option( 'twp_twitter_screen_name' );
        
            TWP_Widget::clear_cache();

            wp_redirect( admin_url( '/admin.php?page=twp_auth' ) );
            
        endif;
        
        return;
        
    }
    
}