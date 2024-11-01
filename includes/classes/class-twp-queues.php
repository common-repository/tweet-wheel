<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Main class of TWP_Queue
 *
 * @class TWP_Queue
 */

class TWP_Queues {
    
    public static $_instance = null;
    
    // ...
    
    private $queues;
    
    public $requested_queue;
    
	/**
	 * Main TWP_Queue Instance
	 *
	 * Ensures only one instance of TWP_Queue is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @return TWP_Queue object
	 */
    
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
    
    // ...
    
	/**
	 * TWP_Queues _construct
     *
     * @type function
     * @date 07/10/2015
	 * @since 2.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function __construct() {
        
        // Settings only for authed users
        if( ! TWP()->twitter()->is_authed() )
            return;
        
        $queues = $this->get_queues(false);
        
        if( ! empty( $queues ) ) :

            foreach( $queues as $q ) :
        
                $this->queues[] = new TWP_Queue( $q );
        
            endforeach;          

        endif;
        
        if( isset( $_GET['queue'] ) ) :
        
            if( $_GET['queue'] == 0 ) :
            
                $this->requested_queue = 0;
        
            else :

                if( null !== term_exists( (int) $_GET['queue'], 'twp_queue' ) ) :
                    $this->requested_queue = new TWP_Queue( get_term( (int) $_GET['queue'], 'twp_queue' ) );
                elseif( ! empty( $this->queues ) ):
                    $this->requested_queue = $this->queues[0];
                else :
                    $this->requested_queue = 0;
                endif;
        
            endif;
        
        else :
        
            if( ! empty( $this->queues ) ):
                $this->requested_queue = $this->queues[0];
            else :
                $this->requested_queue = 0;
            endif;
        
        endif;
        
    }
    
    // ...
    
	/**
	 * Loads the Queue screen
	 *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function page() {
         
        if( TWP()->twitter()->is_authed() == false ) :
            echo "You need to be authorised with Twitter to access this page";
            return;
        endif;
        
        require_once( TWP_PLUGIN_DIR . '/includes/views/queues.php' );
        
    }
    
    // ...
    
	/**
	 * Get queues
	 *
     * @type function
     * @date 25/08/2015
	 * @since 2.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function get_queues( $skip_frozen = true ) {
     
        $queues = get_terms(
            array(
                'twp_queue'
            ),
            array(
                'hide_empty' => false,
                'number' => 1
            )
        );
        
        // @TODO: Change the above query to work off meta data when 4.4 is released
        if ( ! empty( $queues ) && ! is_wp_error( $queues ) ) :
        
            if( $skip_frozen ) :
        
                foreach( $queues as $key => $queue ) :

                    if( TWP_Settings::get_status( $queue->term_id ) == 'frozen' )
                        unset( $queues[$key] );

                endforeach;

            endif;
        
            return $queues;

        endif;
        
        return false;
        
    }
    
}
/**
 * Returns the main instance of TWP_Queue
 *
 * @since  0.1
 * @return TWP_Queue
 */
function TWP_Queues() {
	return TWP_Queues::instance();
}