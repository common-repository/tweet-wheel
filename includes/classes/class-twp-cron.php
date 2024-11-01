<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class TWP_Cron {
    
    public static $_instance = null;

    // ...
    
	/**
	 * Main TweetWheel Cron Instance
	 *
	 * Ensures only one instance of TweetWheel Cron is loaded or can be loaded.
     * @type function
	 * @date 16/06/2015
	 * @since 1.0
     *
	 * @static
     * @param N/A
	 * @return TWP_Cron - Main instance
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
        
        // Add 15 minutes cron job
        add_filter( 'cron_schedules', array( $this, 'intervals' ), 10, 1 );

        // An actual cron task to be run by WP Cron
        add_action( 'tweet_wheel_tweet', array( $this, 'tweet_task' ) );
        
        // ...
        
        add_action( 'init', array( $this, 'cron_error' ) );
        
        add_action( 'wp_ajax_wp_cron_alert', 'twp_ajax_wp_cron_alert' );
    }
    
    // ...
    
    /**
     * Adds a custom interval to cron schedule (every minute)
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param array
     * @return array
     **/
    
    public function intervals( $schedules ) {
        
     	// Adds a minute interval to the existing schedules.
     	$schedules['every_five'] = array(
     		'interval' => 5*60,
     		'display' => __( 'Every 5 Minutes', TWP_TEXTDOMAIN )
     	);
        
     	return apply_filters( 'twp_cron_interval', $schedules );
        
     }

    // ...
    
    /**
     * Cron job
     * Checks if it is apprioriate time to tweet and tweets eventually
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    public function tweet_task() {
        
        $queues = TWP_Queues()->get_queues();
        
        if( ! $queues )
            return;

        // Run through all queues
        foreach( $queues as $term ) :
        
            // Initiate a queue
            $queue = new TWP_Queue( $term );
            
            // If is not running, nothing to do here
            if( ( $status = $queue->settings()->get_queue_status() ) != 'running' )
                continue;
        
            // Get next schedule date
            if( ( $closest_time = TWP_Schedule::should_tweet( $term->term_id ) ) === false )
                continue;
        
            // Check if there are any posts in the queue
            if( $queue->posts()->has_queue_posts() === false )
                continue;
        
            $queue_posts = $queue->posts()->get_queued_posts();
        
            if( $queue->settings()->order() == 'random' )
                shuffle( $queue_posts );

            // Try until something is tweeted...
            foreach( $queue_posts as $q ) :

                if( TWP()->tweet()->tweet( $q->ID, $term ) != false )
                    break;
            
            endforeach;
        
        endforeach;
            
        die;
    
    }
    
    // ...
    
    /**
     * Shows hideable error about WP cron being disabled
     *
     * @type function
     * @date 04/07/2015
     * @since 1.1.1
     *
     * @param N/A
     * @return N/A
     **/
    
    public function cron_error() {

		if( $this->is_wp_cron_disabled() == true && ! get_transient( '_twp_wp_cron_alert_' . get_current_user_id() ) )
            add_action( 'admin_notices', array( $this, 'cron_error_notice' ) );
		
	}
    
    // ...
    
    /**
     * Cron error content
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/

    public function cron_error_notice() {
        
        ?>
        <div class="tw-wp-cron-alert error">
            <p><?php _e( 'Tweet Wheel needs WP Cron to be enabled!', TWP_TEXTDOMAIN ); ?><a id="wp-cron-alert-hide" href="#" class="button" style="margin-left:10px;"><?php _e( 'I know, don\'t bug me.', TWP_TEXTDOMAIN ); ?></a></p>
        </div>

        <?php
        
    }
    
    // ...
    
    /**
     * Helpers
     */
    
    /**
     * Checks WP cron status
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    public function is_wp_cron_disabled() {
        
        if( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON == true )
            return true;
        
        return false;
        
    }
    
    // ...
    
    public function fix_cron(){

        $config_file = ABSPATH.'wp-config.php';

        if(  file_exists($config_file) ) {
            
            $const_names = array( 'DISABLE_WP_CRON' => 'false', 'ALTERNATE_WP_CRON' => 'true' );

            $config_contents = file_get_contents($config_file);

            /* Return all lines containing 'define' statements in wp-config.php. */
            preg_match_all( '/^.*\bdefine\b.*$/im', $config_contents, $matches );

            /* Turn $matches array into string for further preg_match() calls. */
            $matches_str = implode( '', $matches[0] );

            foreach( $const_names as $const_name => $new_value ) {
                if( preg_match( '/\b'.$const_name.'\b/', $matches_str ) ) {
                    $res = $this->array_find( $const_name, $matches[0] );
                    if($res !== false) {
                        $updated_constant = str_replace( array( 'true', 'false' ), $new_value, trim( $matches[0][$res] ) );
                        $config_contents = str_replace( trim($matches[0][$res]), $updated_constant, $config_contents );
                    }
                }
            }

            /* Update wp-config.php. */
            if( file_put_contents( $config_file, $config_contents ) ) :
                echo json_encode( array( 'status' => 'OK', 'response' => 'Success' ) ); exit;
            endif;
            
            echo json_encode( array( 'status' => 'FAIL', 'response' => 'Couldn\'t save the file. Check permissions.' ) ); exit;
        }
    }
    
    // ...

    public function array_find($needle, $haystack, $search_keys = false) {

        if(!is_array($haystack)) return false;
            foreach($haystack as $key=>$value) {
                $what = ($search_keys) ? $key : $value;
                if(strpos($what, $needle)!==false) return $key;
            }
        return false;
    }
    
}

/**
 * Returns the main instance of TWP_Cron
 *
 * @since  0.4
 * @return TWP_Cron
 */
function TWP_Cron() {
	return TWP_Cron::instance();
}
TWP_Cron();