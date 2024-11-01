<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Main class TWP_Menus
 *
 * The idea is to be the superior class handling menus.
 * I wanted it to be extensible by hooks. Maybe it will come useful later.
 *
 * @class TWP_Menus
 */

class TWP_Menus {
    
    private $menus = array();
    
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

        // Auth
        $this->menus[] = array(
            'page_title' => __( 'Authorise', TWP_TEXTDOMAIN ),
            'menu_title' => __( 'Authorise', TWP_TEXTDOMAIN ),
            'menu_slug'  => 'twp_auth',
            'parent_slug' => 'twp_auth',
            'function'   => array( __CLASS__, 'auth_page' )
        );
        
        // Queue
        $this->menus[] = array(
            'page_title' => __( 'Queues', TWP_TEXTDOMAIN ),
            'menu_title' => __( 'Queues', TWP_TEXTDOMAIN ),
            'menu_slug'  => 'twp_queues',
            'function'   => array( __CLASS__, 'queues_page' )
        );
        
        // Settings
        $this->menus[] = array(
            'page_title' => __( 'Settings', TWP_TEXTDOMAIN ),
            'menu_title' => __( 'Settings', TWP_TEXTDOMAIN ),
            'menu_slug'  => 'twp_settings',
            'function'   => array( __CLASS__, 'settings_page' )
        );
        
        // Health Check
        $this->menus[] = array(
            'page_title' => __( 'Health Check', TWP_TEXTDOMAIN ),
            'menu_title' => __( 'Health Check', TWP_TEXTDOMAIN ),
            'menu_slug'  => 'twp_debug',
            'parent_slug' => get_option( 'twp_twitter_is_authed' ) == 1 ? 'twp_queues' : 'twp_auth',
            'function'   => array( __CLASS__, 'health_check_page' )
        );
        
        // About
        $this->menus[] = array(
            'page_title' => __('About', TWP_TEXTDOMAIN ),
            'menu_title' => __( 'About', TWP_TEXTDOMAIN ),
            'menu_slug' => 'twp_about',
            'parent_slug' => get_option( 'twp_twitter_is_authed' ) == 1 ? 'twp_queues' : 'twp_auth',
            'function'   => array( __CLASS__, 'about_page' )
        );
        
        // ...
        
        add_action( 'admin_enqueue_scripts', array( $this, 'wp_menu_pointers' ) );
        
        add_action( 'admin_menu', array( $this, 'menu' ), 10 );
        add_action( 'admin_menu', array( $this, 'submenu' ), 10 );
        
        add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), 1000 );
        
    }
    
    // ...
    
    /**
     * Adds main parent menu tab Tweet Wheel
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    public function menu() {
        
        add_menu_page( 
            TWP_Twitter::is_authed() ? __( 'Queues', TWP_TEXTDOMAIN ) : __( 'Authorise', TWP_TEXTDOMAIN ),
            TWP_Twitter::is_authed() ? __( 'Queues', TWP_TEXTDOMAIN ) : __( 'Authorise', TWP_TEXTDOMAIN ),
            TWP_USER_CAP, 
            TWP_Twitter::is_authed() ? 'twp_queues' : 'twp_auth',
            '__return_false',
            'none', // custom injected with css
            50
        );

        
    }
    
    // ...
    
    /**
     * Add submenus. Here is where other classes add their own tabs.
     *
     * @type function
     * @date 16/06/2015
     * @since 1.0
     *
     * @param N/A
     * @return N/A
     **/
    
    public function submenu() {
        
        $this->menus = apply_filters( 'twp_load_admin_menu', $this->menus );

        foreach( $this->menus as $menu ) :
            
            $menu = wp_parse_args( $menu, array(
                'parent_slug' => 'twp_queues',
                'page_title' => 'Menu...',
                'menu_title' => 'Menu...',
                'capability' => TWP_USER_CAP,
                'menu_slug' => 'menu_',
                'function' => '__return_false',
                'auth_only' => false
            ) );
              
            add_submenu_page( $menu['parent_slug'], __( $menu['page_title'], TWP_TEXTDOMAIN ), __( $menu['menu_title'], TWP_TEXTDOMAIN ), $menu['capability'], $menu['menu_slug'], $menu['function'] );
            
        endforeach;
        
    }
    
    // ...
    
    public function wp_menu_pointers() {
     
        // find out which pointer IDs this user has already seen 
        $seen_it = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
        
        // at first assume we don't want to show pointers    
        $do_add_script = false;
        
        // Handle our first pointer announcing the plugin's new settings screen.
        // check for dismissal of pksimplenote settings menu pointer 'twpwpmp1'
        if ( ! in_array( 'twpwpmp1', $seen_it ) ) { 
            
            // flip the flag enabling pointer scripts and styles to be added later
            $do_add_script = true;
            
            // hook to function that will output pointer script just for pksn1 
            add_action( 'admin_print_footer_scripts', array( $this, 'wp_menu_pointer_1_footer_script' ) ); 
        
        } // end if
        
        // Handle our first pointer announcing the plugin's new settings screen.
        // check for dismissal of pksimplenote settings menu pointer 'twpwpmp2'
        if ( ! in_array( 'twpwpmp2', $seen_it ) ) { 
            
            // flip the flag enabling pointer scripts and styles to be added later
            $do_add_script = true;
            
            // hook to function that will output pointer script just for pksn1 
            add_action( 'admin_print_footer_scripts', array( $this, 'wp_menu_pointer_2_footer_script' ) ); 
        
        } // end if
        
        // now finally enqueue scripts and styles if we ended up with do_add_script == TRUE 
        if ( $do_add_script ) { 
            
            // add JavaScript for WP Pointers
            wp_enqueue_script( 'wp-pointer' ); 
            // add CSS for WP Pointers 
            wp_enqueue_style( 'wp-pointer' ); 
            
        } // end if checking do_add_script 
        
    }
    
    // ...
    
    // Each pointer has its own function responsible for putting appropriate JavaScript into footer 
    
    public function wp_menu_pointer_1_footer_script() { 
        
        // Build the main content of your pointer balloon in a variable 
        $pointer_content = '<h3>' . __( 'Start Twheeling!', TWP_TEXTDOMAIN ) . '</h3>'; 
        
        // Title should be <h3> for proper formatting. 
        $pointer_content .= '<p>' . __( 'One more thing before you can twheel your content!', TWP_TEXTDOMAIN ) . '</p><p><a href="'; 
        
        $pointer_content .= admin_url( '/admin.php?page=twp_auth' ) . '">' . __( 'Authorise', TWP_TEXTDOMAIN ) . '</a> ' . __( 'with Twitter before using it.', TWP_TEXTDOMAIN ) . '</p>';

        ?>
        
        <script type="text/javascript">
            // <![CDATA[ 
            jQuery(document).ready(function($) {     /* make sure pointers will actually work and have content */     
                if(typeof(jQuery().pointer) != 'undefined') {
                    
                    $('#toplevel_page_twp_auth').pointer({
                        content: '<?php echo $pointer_content; ?>',
                        position: {
                            edge: 'left',
                            align: 'center'
                        },
                        close: function() {
                            $.post( ajaxurl, {
                                pointer: 'twpwpmp1',
                                action: 'dismiss-wp-pointer'
                            });            
                        }        
                    }).pointer('open');     
                } 
            }); // ]]>
        </script> 
        
        <?php
        
    }
    
    // ...
    
    public function wp_menu_pointer_2_footer_script() { 
        
        // Build the main content of your pointer balloon in a variable 
        $pointer_content = '<h3>' . __( 'Awesome!', TWP_TEXTDOMAIN ) . '</h3>'; 
        
        // Title should be <h3> for proper formatting. 
        $pointer_content .= '<p>' . __( 'You are all set. You should probably', TWP_TEXTDOMAIN ) . ' <a href="'; 

        $pointer_content .= admin_url( '/admin.php?page=twp_settings' ) . '">' . __( 'check settings out', TWP_TEXTDOMAIN ) . '</a> ' . __( 'before sending your first tweet.', TWP_TEXTDOMAIN ) . '</p>';

        ?>
        
        <script type="text/javascript">
            // <![CDATA[ 
            jQuery(document).ready(function($) {     /* make sure pointers will actually work and have content */     
                if(typeof(jQuery().pointer) != 'undefined') {
                    
                    $('#toplevel_page_twp_queues').pointer({
                        content: '<?php echo $pointer_content; ?>',
                        position: {
                            edge: 'left',
                            align: 'center'
                        },
                        close: function() {
                            $.post( ajaxurl, {
                                pointer: 'twpwpmp2',
                                action: 'dismiss-wp-pointer'
                            });            
                        }        
                    }).pointer('open');     
                } 
            }); // ]]>
        </script> 
        
        <?php
        
    }
    
    // ...
    
    function admin_bar_menu() {
        
        global $wp_admin_bar;

        if ( !is_super_admin() || !is_admin_bar_showing() || ! TWP_Twitter::is_authed() )
            return;

        /* Add the main siteadmin menu item */
        $wp_admin_bar->add_node( array( 'id' => 'twp', 'title' => __( 'Queues', TWP_TEXTDOMAIN ), 'href' => FALSE ) );

        // Submenu
        $queues = TWP()->queues()->get_queues();
        
        if( ! empty( $queues ) ) :
        
            foreach( $queues as $queue ) : 
        
                $wp_admin_bar->add_node( array( 'id' => 'twp_queue_' . $queue->name, 'parent' => 'twp', 'parent' => 'twp', 'title' => $queue->name, 'href' => admin_url( '/admin.php?page=twp_queues&queue=' . $queue->term_id ) ) );
        
            endforeach;
        
        endif;
        
        $wp_admin_bar->add_node( array( 'id' => 'twp_queue_new', 'parent' => 'twp', 'title' => 'Add New Queue', 'href' => admin_url( '/admin.php?page=twp_queues&queue=0' ) ) );
        
    }
    
    // ...
    
    // callbacks
    public static function about_page() { TWP_Dashboard::page(); }
    public static function queues_page() { TWP()->queues()->page(); }
    public static function settings_page() { $settings = new TWP_Plugin_Settings; $settings->page(); }
    public static function health_check_page() { TWP_Debug()->page(); }
    public static function auth_page() { TWP()->twitter()->page(); }
    
}

// Initiate
new TWP_Menus;