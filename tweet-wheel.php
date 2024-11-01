<?php 

/**
 * Plugin Name: Tweet Wheel
 * Plugin URI: http://www.tweet-wheel.com
 * Description: A powerful tool that keeps your Twitter profile active. Even when you are busy.
 * Version: 1.1.6
 * Author: Tomasz Lisiecki from Nerd Cow Ltd.
 * Author URI: https://tweet-wheel.com
 * Requires at least: 4.4
 * Tested up to: 5.0
 *
 * @package Tweet Wheel
 * @category Core
 * @author Tomasz Lisiecki from Nerd Cow Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if ( ! class_exists( 'TweetWheel' ) ) :

/**
 * Main TweetWheel Class
 *
 * @class TweetWheel
 */
    
final class TweetWheel {
    
    /**
     * @var string
     */
    public $version = '1.1.6';
    
    // ...
    
    /**
     * @var the singleton
     * @static
     */
    protected static $_instance = null;
    
    // ...
    
    /**
     * @var TWP_Queue object
     */
    public $queues = null;
    
    // ...
    
	/**
	 * Main TweetWheel Instance
	 *
	 * Ensures only one instance of TweetWheel is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @return TweetWheel - Main instance
	 */
	public static function instance() {
		NULL === self::$_instance and self::$_instance = new self;
		return self::$_instance;
	}
    
    // ...
    
	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', TWP_TEXTDOMAIN ), $this->version );
	}
    
    // ...

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', TWP_TEXTDOMAIN ), $this->version );
	}
    
    // ...
    
    /**
     * TweetWheel Constructor
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A 
     */
    
    public function __construct() {}
    
    // ...
    
    /**
     * Initialize the plugin! Woop!
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    public function init() {
        
        global $pagenow;
        
        // Define all necessary constants
        $this->constants();
        
        // Load dependencies
        $this->includes();
        
        add_action( 'admin_init', 'twp_check_version' );

        if ( ! twp_compatible_version() )
            return;
        
        // ...

        // Hooks
        add_action( 'admin_init', 'twp_redirect' );
        
        // Assets
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );

        // Add translations
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
        
        // Add some post actions to the post list screen
        add_action( 'admin_footer', array( $this, 'admin_footer' ) );
        
        if( 'edit.php' == $pagenow ) :
        
            add_filter( 'admin_footer-edit.php', array( $this, 'bulk_queue_option' ) );
            add_action( 'load-edit.php', array( $this, 'bulk_queue' ) );
            add_action( 'admin_notices', array( $this, 'bulk_queue_admin_notice' ) );

            // Hooks to action on particular post status changes
            $post_types = twp_get_all_enabled_post_types();

            if( $post_types != '' ) :

                foreach( $post_types as $post_type ) :

                    add_filter( $post_type . '_row_actions', array( $this, 'post_row_queue' ), 10, 2);

                endforeach;

            endif;
        
        endif;
		
        add_action( 'transition_post_status', array( $this, 'on_unpublish_post' ), 999, 3 );
        
        if( ! wp_next_scheduled( 'tweet_wheel_tweet' ) )
            wp_schedule_event( current_time( 'timestamp' ), 'every_five', 'tweet_wheel_tweet' );
        

    }
    
    // ...
    
    /**
     * Define constants used in the plugin
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    private function constants() {
        
        // Textdomain		
        if( ! defined( 'TWP_TEXTDOMAIN' ) )		
            define( 'TWP_TEXTDOMAIN', 'tweetwheelpro' );
        
        // Plugin Version
        if( ! defined( 'TWP_VERSION' ) )
            define( 'TWP_VERSION', $this->version );
        
        // Paths
        if( ! defined( 'TWP_PLUGIN_FILE' ) )
            define( 'TWP_PLUGIN_FILE', __FILE__ );
        
        if( ! defined( 'TWP_PLUGIN_BASENAME' ) )
            define( 'TWP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
        
        if( ! defined( 'TWP_PLUGIN_DIR' ) )
            define( 'TWP_PLUGIN_DIR', dirname( __FILE__ ) );
        
        if( ! defined( 'TWP_PLUGIN_URL' ) )
            define( 'TWP_PLUGIN_URL', plugins_url( '/tweet-wheel' ) );
        
        if( ! defined( 'TWP_USER_CAP' ) )
            define( 'TWP_USER_CAP', 'twheel_it' );
        
        // Misc
        if( ! defined( 'TWP_UPGRADE_LINK' ) )
            define( 'TWP_UPGRADE_LINK', 'https://tweet-wheel.com' );
        
    }
    
    // ...
    
    /**
     * Include all dependencies, not loaded by autoload of course
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    private function includes() {
        
        // Fundamental settings
        require_once( 'includes/helpers.php' );
        
        require_once( 'includes/classes/class-twp-menus.php' );
        require_once( 'includes/libraries/sf-settings.php' );
        require_once( 'includes/classes/class-twp-plugin-settings.php' );
        require_once( 'includes/classes/class-twp-post-types.php' );

        // Third-parties
        include_once( 'includes/libraries/twitteroauth/autoload.php' );
        
        // Twitter Class
        require_once( 'includes/classes/class-twp-api.php' );
        
        // Twitter Class
        require_once( 'includes/classes/class-twp-twitter.php' );
        
        // Tweet Class
        require_once( 'includes/classes/class-twp-tweet.php' );
        
        // Queues Class
        require_once( 'includes/classes/class-twp-queues.php' );
        
        // Queue Class
        require_once( 'includes/classes/class-twp-queue.php' );

        // Schedule Class
        require_once( 'includes/classes/class-twp-schedule.php' );

        // Settings Class
        require_once( 'includes/classes/class-twp-settings.php' );

        // Posts Class
        require_once( 'includes/classes/class-twp-posts.php' );
        
        // Dashboard Class
        require_once( 'includes/classes/class-twp-dashboard.php' );
        
        // Cron class
        require_once( 'includes/classes/class-twp-cron.php' ); 
        
        // Debug class
        require_once( 'includes/classes/class-twp-debug.php' );
        
        // Widget
        require_once( 'includes/classes/class-twp-widget.php' );
        
        require_once( 'includes/twp-metaboxes.php' );
        
        require_once( 'includes/install.php' );
        require_once( 'includes/uninstall.php' );

        if( defined( 'DOING_AJAX' ) ) :
            $this->ajax_includes();
        endif;
        
    }
    
    // ..
    
    /**
     * Include admin assets
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     */
    
    public function admin_assets() {
        
        global $wp_customize;		
        		
        if ( isset( $wp_customize ) )		
            return;
        
        // WP Core CSS
        wp_enqueue_style( 'thickbox' );
        wp_enqueue_style( 'dashicons' );
        
        // Other CSS
        wp_register_style( 'twp-calendar', TWP_PLUGIN_URL . '/assets/css/twp-calendar.css', null, $this->version );
        wp_enqueue_style( 'twp-calendar' );
        
        wp_register_style( 'twp', TWP_PLUGIN_URL . '/assets/css/twp.css', null, $this->version );
        wp_enqueue_style( 'twp' );
        
        wp_register_style( 'twp-widget-admin', TWP_PLUGIN_URL . '/assets/css/twp-widget-admin.css', null, $this->version );
        wp_enqueue_style( 'twp-widget-admin' );
        
        // ...
        
        // WP Core JS
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script( 'thickbox' );
        
        // Other JS
        wp_register_script( 'twp-sprintf', TWP_PLUGIN_URL . '/assets/js/vendor/sprintf.js', array( 'jquery' ), $this->version );
        wp_enqueue_script( 'twp-sprintf' );
        
        wp_register_script( 'twp-validate', TWP_PLUGIN_URL . '/assets/js/vendor/jquery.validate.min.js', array( 'jquery' ), $this->version );
        wp_enqueue_script( 'twp-validate' );
        
        wp_register_script( 'twp-multidate-picker', TWP_PLUGIN_URL . '/assets/js/vendor/jquery-ui.multidatespicker.js', array( 'jquery-ui-datepicker' ), $this->version );
        wp_enqueue_script( 'twp-multidate-picker' );

        wp_register_script( 'twp-chart-js', TWP_PLUGIN_URL . '/assets/js/vendor/charts/chart.min.js', array( 'jquery' ) ); 
        wp_enqueue_script( 'twp-chart-js' );
        
        wp_register_script( 'twp-chart-line-js', TWP_PLUGIN_URL . '/assets/js/vendor/charts/src/Chart.Line.js', array( 'jquery','twp-chart-js' ) ); 
        wp_enqueue_script( 'twp-chart-line-js' );
        
        wp_register_script( 'twp-rangy-core', TWP_PLUGIN_URL . '/assets/js/vendor/rangy-core.js', array( 'jquery' ) ); 
        wp_enqueue_script( 'twp-rangy-core' );
        
        wp_register_script( 'twp-rangy-textrange', TWP_PLUGIN_URL . '/assets/js/vendor/rangy-textrange.js', array( 'jquery','twp-rangy-core' ) ); 
        wp_enqueue_script( 'twp-rangy-textrange' );
        
        wp_register_script( 'twp-rangy-classapplier', TWP_PLUGIN_URL . '/assets/js/vendor/rangy-classapplier.js', array( 'jquery','twp-rangy-core' ) ); 
        wp_enqueue_script( 'twp-rangy-classapplier' );
        
        wp_register_script( 'twp-functions', TWP_PLUGIN_URL . '/assets/js/twp-functions.js', array( 'jquery' ), $this->version ); 
        wp_enqueue_script( 'twp-functions' );
        
        if( ! wp_script_is( 'twp-queues' ) ) :
            wp_register_script( 'twp-queues', TWP_PLUGIN_URL . '/assets/js/twp-queues.js', array( 'jquery' ), $this->version ); 
            wp_localize_script( 'twp-queues', '_TWPQueues', array(
                    'templates' => array(
                        'time_row' => '<span class="remove-time dashicons dashicons-no-alt"></span> <select name="%s">%s</select> : <select name="%s">%s</select>'                   
                    )
                )
            );
            wp_enqueue_script( 'twp-queues' );
        endif;
        
        wp_register_script( 'twp-schedule', TWP_PLUGIN_URL . '/assets/js/twp-schedule.js', array( 'jquery' ), $this->version ); 
        wp_enqueue_script( 'twp-schedule' );
        
        wp_register_script( 'twp-helpers', TWP_PLUGIN_URL . '/assets/js/twp-helpers.js', array( 'jquery' ), $this->version ); 
        wp_enqueue_script( 'twp-helpers' );
        
        if( ! wp_script_is( 'twp-templates' ) ) :
            wp_register_script( 'twp-templates', TWP_PLUGIN_URL . '/assets/js/twp-templates.js', array( 'jquery' ), $this->version );    
            wp_localize_script( 'twp-templates', 'tweet_template', sprintf( twp_tweet_template_default(), '', 0, '', 0 ) );
            wp_localize_script( 'twp-templates', 'default_tweet_template', TWP()->tweet()->get_default_template() );
            wp_enqueue_script( 'twp-templates' );
        endif;

        if( ! wp_script_is( 'twp' ) ) :
            wp_register_script( 'twp', TWP_PLUGIN_URL . '/assets/js/twp.js', array( 'jquery' ), $this->version );    
            wp_localize_script( 'twp', '_TWPAJAX', array(
                    'twNonce' => wp_create_nonce( 'twp-nonce' ),
                    'post_types' => twp_get_option( 'twp_settings', 'post_type' )
                )
            );
            wp_enqueue_script( 'twp' );
        endif;
        
    }
    
    // ...
    
    function load_textdomain() {
        load_plugin_textdomain( TWP_TEXTDOMAIN, false, dirname( plugin_basename(__FILE__) ) . '/i18n/languages/' );
    }
    
    // ...
    
    /**
     * Include all dependencies for AJAX needs
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
	public function ajax_includes() {
        
        self::add_ajax_action( 'ajax_action', array( $this, 'ajax_actions' ) );
        
	}
    
    // ...
    
    public static function add_ajax_action( $ajax_action, $event_function ) {
     
        add_action( 'wp_ajax_twp_'.$ajax_action, $event_function );
        
    }
    
    // ...
    
    public function ajax_actions() {
        
        $action = $_POST['admin_action'];
        
        if( isset( $_POST['data'] ) )
            $data = $_POST['data'];
        
        check_ajax_referer( 'twp-nonce', 'nonce' );
        
        switch( $action ) :
        
            case 'save_queue':

                foreach( $data['elements'] as $index => $post_id ) :
        
                    update_post_meta( $post_id, 'twp_term_' . $data['term_id'] . '_order', $index ); 

                endforeach;

                // @TODO - use wp_send_json_success instead
                echo json_encode( array( 'response' => 'ok' ) );

                exit;

            break;
        
            case 'add_to_queue':
        
                $posts = new TWP_Posts( $data['term_id'] );
        
                $result = $posts->insert_post( $data['post_id'], $data['insert_after'] );

                if( ! is_wp_error( $result ) ) :

                    echo json_encode( array( 'response' => 'ok' ) );

                    exit;
                
                else :
        
                    echo json_encode( array( 'status' => 'error', 'response' => $result->get_error_message() ) );
        
                    exit;

                endif;
        
            break;
        
            case 'remove_from_queue':
        
                $posts = new TWP_Posts( $data['term_id'] );

                if( $posts->remove_post( $data['post_id'] ) ) :
        
                    echo json_encode( array( 'response' => 'ok' ) );

                    exit;

                endif;
        
            break;
        
            case 'get_queues_list':
 
                $queues = TWP_Queues()->get_queues(true);

                $post_id = isset( $data['postId'] ) ? $data['postId'] : null;

                if( $queues ) :

                    $html = '<ul class="manage-queue-tooltip buttons-list">';

                    foreach( $queues as $q ) :

                        $in_term = $post_id != null && has_term( $q->term_id, 'twp_queue', $post_id ) ? true : false;

                        $html .= '<li data-post-id="' . $post_id . '" data-term-id="' . $q->term_id . '" class="tw-queue-post ' . ( $in_term ? 'in-queue' : 'not-in-queue' ) . '">' . $q->name . '</li>';

                    endforeach;

                    $html .= '</ul>';

                else :

                    $html .= '<span style="line-height:28px;">You haven\'t added any queues yet.</span>&nbsp;&nbsp;<a href="'.admin_url('/admin.php?page=twp_queues').'" class="button button-primary">Add</a>';

                endif;

                echo $html;

                exit;
        
            break;
        
            case 'render_queue_post':

                $items = get_posts( array(
                    'post__in' => array( $data['post_id'] ),
                    'post_type' => twp_get_all_enabled_post_types()
                ) );

                $term_id = $data['term_id'];
        
                $posts = new TWP_Posts( $term_id );

                foreach( $items as $item ) :

                    echo $posts->render_queue_post( $item, 'li', true );

                endforeach;
        
                exit;
        
            break;
        
            case 'tweet':
        
                $term = get_term( $data['term_id'], 'twp_queue' );
                
                $tweet = TWP()->tweet()->tweet( $data['post_id'], $term );

                if( false != $tweet && ! is_array( $tweet ) ) :
            
                    echo json_encode( array( 'response' => 'ok' ) );

                    exit;

                endif;

                echo json_encode( array( 'response' => 'error', 'message' => $tweet['errormsg'] ) );

                exit;
        
            break;
        
            case 'get_post_types':
        
                $post_types = get_post_types( array( 'public' => true ), 'objects' );
		
                if( empty( $post_types ) ) :

                    echo json_encode( array( 'response' => 'error', 'message' => 'No public post types enabled.' ) );

                    exit;

                endif;

                echo json_encode( array( 'response' => 'success', 'data' => $post_types ) );

                exit;
        
            break;
        
            case 'search_content':

                $pts = twp_get_all_enabled_post_types();
                $results = array();
        
                if( empty( $pts ) ) :
                    echo json_encode( array( 'status' => 'ERROR', 'response' => 'No post types enabled' ) ); exit;
                endif;

                foreach( $pts as $pt ) :

                    $posts = get_posts(
                        array(
                            'post_type' => $pt,
                            'posts_per_page' => 5,
                            'post_status' => 'publish',
                            's' => $data['search'],
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'twp_queue',
                                    'field'    => 'term_id',
                                    'terms'    => array( $data['term_id'] ),
                                    'operator' => 'NOT IN',
                                )
                            )
                        )
                    );

                    if( $posts ) :

                        $results[$pt] = array(
                            'label' => ucfirst( str_replace( '-', ' ', $pt ) )  
                        );

                        foreach( $posts as $p ) :

                            $results[$pt]['posts'][$p->ID] = $p->post_title; 

                        endforeach;
        
                        echo json_encode( array( 'status' => 'OK', 'response' => $results ) ); exit;

                    endif;

                endforeach;

                echo json_encode( array( 'status' => 'ERROR', 'response' => 'No results found' ) ); exit;
        
            break;
        
            case 'found_posts':
        
                $post = $data['args'];
    
                $args = array (
                    'posts_per_page' => ctype_digit( $post['number'] ) ? $post['number'] : '-1',
                    'post_type' => $post['post_type'],
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'twp_queue',
                            'field'    => 'term_id',
                            'terms'    => array( $post['term_id'] ),
                            'operator' => 'NOT IN',
                        )
                    )
                );

                // Check if date range hsa been set
                if( ! empty( $post['date_from'] ) || ! empty( $post['date_to'] ) ) :

                    $args['date_query'] = array(
                        'inclusive' => 'true'
                    );

                    if( ! empty( $post['date_from'] ) ) :
        
                        $from = explode( '/', $post['date_from'] );
        
                        $args['date_query'][]['after'] = array(
                            'day' => $from[0],
                            'month' => $from[1],
                            'year' => $from[2]
                        );
        
                    endif;
        
                    if( ! empty( $post['date_to'] ) ) :
        
                        $from = explode( '/', $post['date_to'] );
        
                        $args['date_query'][]['before'] = array(
                            'day' => $from[0],
                            'month' => $from[1],
                            'year' => $from[2]
                        );
        
                    endif;

                endif;
        
                $data = get_posts( $args );

                echo json_encode( array( 'response' => 'success', 'data' => count( $data ) ) );

                exit;
        
            break;
        
            case 'fill_up_queue':
        
                $errors = array();
                $args = array();

                parse_str($data["data"], $input);

                $pts = $input['fillup'];
                $term_id = $input['term_id'];
                $insert_after = $input['insert_after'];

                foreach( $pts as $pt => $value ) :

                    if( ! isset( $value['included'] ) )
                        continue;

                    // Standard args for the query
                    $args = array (
                        'posts_per_page' => ctype_digit( $value['number'] ) ? $value['number'] : '-1',
                        'post_type' => $pt,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'twp_queue',
                                'field'    => 'term_id',
                                'terms'    => array( $term_id ),
                                'operator' => 'NOT IN'
                            )
                        )
                    );

                    // Check if date range hsa been set
                    if( ! empty( $value['from'] ) || ! empty( $value['to'] ) ) :

                        $args['date_query'] = array(
                            'inclusive' => 'true'
                        );

                        if( ! empty( $value['from'] ) ) :
        
                            $from = explode( '/', $value['from'] );

                            $args['date_query'][]['after'] = array(
                                'day' => $from[0],
                                'month' => $from[1],
                                'year' => $from[2]
                            );

                        endif;

                        if( ! empty( $value['to'] ) ) :

                            $from = explode( '/', $value['to'] );

                            $args['date_query'][]['before'] = array(
                                'day' => $from[0],
                                'month' => $from[1],
                                'year' => $from[2]
                            );

                        endif;

                    endif;

                endforeach;
        
                $obj = new TWP_Posts( $term_id );

                $posts = $obj->fill_up_by_query( $args, $insert_after );

                $ids = array();
        
                $reversed = array_reverse( $posts );

                foreach( $reversed as $p ) :

                    $ids[] = $p->ID;

                endforeach;

                echo json_encode( array( 'status' => 'OK', 'response' => array( 'ids' => $ids, 'term_id' => $term_id, 'insert_after' => $insert_after ) ) ); exit;

            break;
        
            case 'save_schedule':

                parse_str($data["form"], $form);
        
                extract( $form );
        
                // Sanitize weekly times
                if( ! empty( $weekly_times ) && is_array( $weekly_times ) ) :
        
                    $weekly_times = TWP_Schedule::sanitize_times( $weekly_times );
        
                endif;
        
                if( ! $weekly_times )
                    $weekly_times = false;
        
                update_term_meta( $term_id, 'twp_schedule_type', $type );
                update_term_meta( $term_id, 'twp_schedule_weekly_times', $weekly_times );
                update_term_meta( $term_id, 'twp_schedule_span_from', $span_from );
                update_term_meta( $term_id, 'twp_schedule_span_to', $span_to );
        
                update_term_meta( $term_id, 'twp_last_tweeted_time', current_time( 'timestamp' ) );

                exit;
        
            break;
        
            case 'save_settings':

                parse_str($data["form"], $form);
        
                extract( $form );
        
                wp_update_term( $term_id, 'twp_queue', array( 'name' => $name ) );
        
                update_term_meta( $term_id, 'twp_settings_status', $status );
        
                update_term_meta( $term_id, 'twp_settings_publish_action', $publish_action );
        
                update_term_meta( $term_id, 'twp_settings_order', $order );

                exit;
        
            break;
        
            case 'empty_queue':
        
                $posts = new TWP_Posts( $data['term_id'] );
        
                if( taxonomy_exists( 'twp_queue' ) ) echo ':D';
                if( ! taxonomy_exists( 'twp_queue' ) ) echo ':(';

                $posts->remove_all();
        
                exit;
        
            break; 
        
            case 'get_time':
        
                $time = TWP_Schedule::get_time( $data['term_id'] );

                if( '' == $time ) :
                    echo 'Not set';
                else :
                    echo date( 'H:i jS M \'y', $time );
                endif;
        
                exit;
        
            break;
        
            case 'get_status':
        
                $status = TWP_Settings::get_status( $data['term_id'] );

                if( '' == $status ) :
                    echo 'paused';
                else :
                    echo $status;
                endif;
        
                exit;
        
            break;
        
            case 'get_queue_name':
        
                $term = get_term( $data['term_id'], 'twp_queue' );

                echo $term->name;
        
                exit;
        
            break;
        
            case 'fix_cron':
        
                TWP_Cron()->fix_cron();
        
                exit;
        
            break;
        
            case 'close_cron_alert':

                set_transient( '_twp_wp_cron_alert_' . get_current_user_id(), 'hide', 60*60*24*7 ); // hide for a week

                exit;
     
            break;

        endswitch;
        
    }
    
    // ...
    
    /**
     * Checks if user is allowed to use the plugin
     *
     * @type function
     * @date 21/10/2015
     * @since 2.0
     *
     * @param N/A
     * @return N/A
     **/
    
    public function user_can() {
        
        require_once ABSPATH . '/wp-includes/pluggable.php';
        
        if( current_user_can( TWP_USER_CAP ) )
            return true;
        
        return false;
        
    }
    
    /*
    
    
    
    */
    
    // ... Helpers ...
    
    // ...
    
    /**
     * Get plugin path
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return string
     **/
    
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
    
    /*
    
    
    
    */
    
    // ... Class Instances ...
    
    /**
     * Gets an instance of TWP_Twitter class
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return object
     **/
    
    public function twitter() {
        return TWP_Twitter::instance();
    }
    
    // ...
    
    /**
     * Gets an instance of TWP_Teet class
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return object
     **/
    
    public function tweet() {
        return TWP_Tweet::instance();
    }
    
    // ...
    
    /**
     * Gets an instance of TWP_Queue class
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return object
     **/
    
    public function queues() {
        return TWP_Queues::instance();
    }
    
    // ...
    
    /**
     * Gets an instance of TWP_Link_Shortening class
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return object
     **/
    
    public function link_shortening() {
        return TWP_Link_Shortening::instance();
    }
    
    // ...
    
	/**
	 * Adds an action to posts on the edit.php screen
	 *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function post_row_queue( $actions, $post ) {
        
        //check for your post type
        
        if ( twp_is_post_type_enabled( $post->post_type ) && $post->post_status == "publish" ) :
        
            $queues = wp_get_post_terms( $post->ID, 'twp_queue' );

            $actions['queue'] = '<a class="manage-queue-post" href="#" data-post-id="'.$post->ID.'">' . __( 'Manage Queue', TWP_TEXTDOMAIN ) . ' (' . count( $queues ) . ')</a>';

        endif;
        
        return $actions;
        
    }
    
    // ...
    
	/**
	 * Injects options to Bulk Actions dropdown on the edit.php screen
	 *
	 * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function bulk_queue_option() {
        
        global $post_type;
        
        $screen = $_REQUEST['post_status'];
        
        if( $screen != '' && $screen != 'publish' )
            return;

		?>
		
		<script type="text/javascript">
			jQuery(document).ready(function() {
			jQuery("select[name^='action']").append('<option disabled></option><option disabled>Tweet Wheel Pro</option>');
			jQuery('<option>').val('queue').text('- <?php _e('Append to All Queues',TWP_TEXTDOMAIN)?>').appendTo("select[name='action']")
			jQuery('<option>').val('queue').text('- <?php _e('Append to All Queues',TWP_TEXTDOMAIN)?>').appendTo("select[name='action2']");
			jQuery('<option>').val('dequeue').text('- <?php _e('Remove from All Queues',TWP_TEXTDOMAIN)?>').appendTo("select[name='action']");
			jQuery('<option>').val('dequeue').text('- <?php _e('Remove from All Queues',TWP_TEXTDOMAIN)?>').appendTo("select[name='action2']");
			});
		</script>
		
		<?php

    }
    
    // ...
    
	/**
	 * Handles bulk actions
	 *
	 * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function bulk_queue() {

        // 1. get the action
        $wp_list_table = _get_list_table('WP_Posts_List_Table');
        $action = $wp_list_table->current_action();
        
		if(isset($_REQUEST['post'])) {
			$post_ids = array_map('intval', $_REQUEST['post']);
		}
		
        if(empty($post_ids)) return;
        
        // 2. security check
        check_admin_referer('bulk-posts');

        switch($action) {

        // 3. Perform the action
        case 'queue':

            foreach( $post_ids as $post_id ) {
                
                if( get_post_status( $post_id ) != 'publish' )
                    continue;
                
                $queues = TWP_Queues()->get_queues();
                
                if( $queues ) :

                    foreach( $queues as $queue ) :
                
                        $posts = new TWP_Posts( $queue->term_id );
                
                        $posts->insert_post( $post_id, $posts->get_last_queued_post_id() );

                    endforeach;

                endif;
                
            }

            // build the redirect url
            $sendback = add_query_arg( array( 'queued' => true, 'post_type' => get_post_type( $post_id ) ), $sendback );

            break;

        case 'dequeue':
            
            foreach( $post_ids as $post_id ) {
                
                $queues = TWP_Queues()->get_queues();
                
                if( $queues ) :

                    foreach( $queues as $queue ) :
                
                        $posts = new TWP_Posts( $queue->term_id );
                
                        $posts->remove_post($post_id);

                    endforeach;

                endif;

            }

            // build the redirect url
            $sendback = add_query_arg( array( 'queued' => true, 'post_type' => get_post_type( $post_id ) ), $sendback );
            
            break;

        default: return;

        }

        // ...

        // 4. Redirect client
        wp_redirect($sendback);

        exit();
        
    }
    
    // ...
    
	/**
	 * Display relevant notice after a bulk action has been performed
	 *
	 * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function bulk_queue_admin_notice() {
 
		global $post_type, $pagenow;

		// Posts queued

		if(isset($_REQUEST['queued']) && (int) $_REQUEST['queued']) {
			$message = 'Post(s) queued.';
			echo '<div class="updated"><p>' . $message . '</p></div>';
		}

		// ...

		// Posts dequeued

		if(isset($_REQUEST['dequeued']) && (int) $_REQUEST['dequeued']) {
			$message = sprintf( _n( 'Post dequeued.', '%s posts dequeued.', $_REQUEST['dequeued'], TWP_TEXTDOMAIN ), number_format_i18n( $_REQUEST['dequeued'] ) );
			echo '<div class="updated"><p>' . $message . '</p></div>';
		}

		// ...

		// Posts excluded

		if(isset($_REQUEST['excluded']) && (int) $_REQUEST['excluded']) {
			$message = sprintf( _n( 'Post excluded.', '%s posts excluded.', $_REQUEST['excluded'], TWP_TEXTDOMAIN ), number_format_i18n( $_REQUEST['excluded'] ) );
			echo '<div class="updated"><p>' .$message .'</p></div>';
		}
      
    }
    
    // ...

    /**
     * Action on unpublishing post
     * Removes post from the queue
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param string | string | object
	 * @return n/a
     */
    
    public function on_unpublish_post( $new_status, $old_status, $post ) {
        
        if ( $old_status == 'publish'  &&  $new_status != 'publish' ) {
            
            $queues = TWP_Queues()->get_queues();
                
            if( $queues ) :

                foreach( $queues as $queue ) :

                    $posts = new TWP_Posts( $queue->term_id );

                    $posts->remove_post( $post->ID );

                endforeach;

            endif;

        }
    
        return;
        
    }
    
    // ...
    
    public function admin_footer() {}

}

/**
 * Returns the main instance of TWP
 *
 * @since  1.0
 * @return TweetWheel
 */

function TWP() {
	return TweetWheel::instance();
}

TWP()->init();

register_activation_hook( __FILE__, 'twp_install' );
register_activation_hook( __FILE__, 'twp_activation_check' );
register_activation_hook( __FILE__, 'twp_assign_caps' );

register_uninstall_hook( __FILE__, 'twp_uninstall' );;	

endif;