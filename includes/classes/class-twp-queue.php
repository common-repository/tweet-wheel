<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Main class of TWP_Queue
 *
 * @class TWP_Queue
 */

class TWP_Queue {
    
    private $term,
            $posts,
            $schedule,
            $settings,
            $allowed_tabs,
            $current_queue_tab;
    
    // ...
    
    public function __construct( $term ) {
     
        if( $term->term_id == null || is_wp_error( get_term( $term->term_id, 'twp_queue' ) ) )
            return;
        
        $this->term = $term;
        $this->allowed_tabs = array( 'posts', 'schedule', 'settings', 'logs' );
        $this->current_queue_tab = isset( $_GET['tab'] ) && in_array( $_GET['tab'], $this->allowed_tabs ) ? $_GET['tab'] : 'posts';
        
        $this->posts = new TWP_Posts( $this->term->term_id );
        $this->settings = new TWP_Settings( $this->term->term_id );
        $this->schedule = new TWP_Schedule( $this->term->term_id );

    }
    
    // ...
    
    public function render_queue_tab() {
        
        $next_time = TWP_Schedule::get_time( $this->term->term_id );
        
        echo '<li>
                <a href="' . admin_url( '/admin.php?page=twp_queues&queue=' . $this->term->term_id ) . '" class="tw-queue-status tw-queue-status-' . $this->settings->get_queue_status() . ' ' . ( TWP()->queues()->requested_queue->term->term_id == $this->term->term_id ? 'active' : '' ) . '"><span>' . $this->term->name . '</span><small>Next: ' . ( $next_time != false ? date( 'H:i jS M \'y', $next_time ) : 'Not set' ) . '</small></a>
            </li>';
        
    }
    
    // ...
    
    public function render_queue() {

        echo '<div id="tw-queue-' . $this->term->term_id . '" class="tw-queue-content tw-queue-' . $this->term->term_id . '">';
        
            $this->display_tab_links();

            switch( $this->current_queue_tab ) :
        
                case 'schedule':
                    echo '<div id="tw-queue-content-schedule-' . $this->term->term_id . '" class="tw-queue-content-inner">';

                        $this->schedule->display_tab();

                    echo '</div>';
                    break;

                case 'settings':
                    echo '<div id="tw-queue-content-settings-' . $this->term->term_id . '" class="tw-queue-content-inner">';
                
                        $this->settings->display_tab();

                    echo '</div>';
                    break;

                case 'logs':
                    echo '<div id="tw-queue-content-log-' . $this->term->term_id . '" class="tw-queue-content-inner">';

                        echo '<a href="' . TWP_UPGRADE_LINK . '" target="_blank" style="display:block;float:left">
                    <img width="587" height="306" src="' . TWP_PLUGIN_URL . '/assets/images/go-pro/log.png">
                </a>';
        
                    echo '</div>';
                    break;

                default:
                    echo '<div id="tw-queue-content-queue-' . $this->term->term_id . '" class="tw-queue-content-inner">';
                        $this->posts->display_tab();
                    echo '</div>';
                    break;
        
            endswitch;

        echo '</div>';
        
    }
    
    // ...
    
	/**
	 * Queue tools / buttons
	 *
	 * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function display_tab_links() {

        ?>
        <ul class="queue-tools">
            <li><a href="<?php echo admin_url('/admin.php?page=twp_queues&queue='.$this->term->term_id.'&tab=posts'); ?>" class="tw-queue-content-tab <?php echo $this->current_queue_tab == 'posts' ? 'active' : ''; ?>"><span class="dashicons dashicons-menu"></span> Posts</a></li>
            <li><a href="<?php echo admin_url('/admin.php?page=twp_queues&queue='.$this->term->term_id.'&tab=schedule'); ?>" class="tw-queue-content-tab <?php echo $this->current_queue_tab == 'schedule' ? 'active' : ''; ?>"><span class="dashicons dashicons-calendar-alt"></span> Schedule</a></li>
            <li><a href="<?php echo admin_url('/admin.php?page=twp_queues&queue='.$this->term->term_id.'&tab=settings'); ?>" class="tw-queue-content-tab <?php echo $this->current_queue_tab == 'settings' ? 'active' : ''; ?>"><span class="dashicons dashicons-admin-generic"></span> Settings</a></li>
            <li><a href="<?php echo admin_url('/admin.php?page=twp_queues&queue='.$this->term->term_id.'&tab=logs'); ?>" class="tw-queue-content-tab <?php echo $this->current_queue_tab == 'logs' ? 'active' : ''; ?>"><span class="dashicons dashicons-flag"></span> Log</a></li>
        </ul>
                
        <?php
        
    }
    
    // ...
    
    public function settings() { return $this->settings; }
    public function posts() { return $this->posts; }
    public function schedule() { return $this->schedule; }
    public function term() { return $this->term; }
    
}