<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Dashboard class
 *
 * @TODO: Eventually, this will be an actual dashboard with useful widgets and shortcuts, but for now let's leave it as About page.
 */

class TWP_Dashboard {

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
    
    // ...
    
    /**
     * Construct
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    public function __construct() {}
    
    // ...
    
    /**
     * About us page content
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    public static function page() {

        require_once( TWP_PLUGIN_DIR . '/includes/views/about.php' );
        
    }
    
}

/**
 * Returns the main instance of TWP_Dashboard
 *
 * @since  1.0
 * @return TWP_Dashboard
 */
function TWP_Dashboard() {
	return TWP_Dashboard::instance();
}