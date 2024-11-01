<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'widgets_init', function(){
    register_widget( 'TWP_Widget' );
});	

class TWP_Widget extends WP_Widget {
    
    private $defaults;
 
	function __construct() {
        
        // Load parent construct
		parent::__construct(
			'TWP_Widget', // Base ID
			__( 'TW - Twitter Feed', TWP_TEXTDOMAIN ), // Name
			array( 'description' => __( 'Widget displays relevant tweets from an authorised Twitter account.', TWP_TEXTDOMAIN ), ) // Args
		);
        
        // Get & parse settings
        $_settings = $this->get_settings();
        $this->settings = array();
        
        foreach($_settings as $value) :
            $this->settings += $value;
        endforeach;
        
        $this->settings = wp_parse_args( $this->settings, array(
            'number_of_tweets' => 0, // unlimited
            'skip_replies' => 1,
            'skip_rts' => 1,
            'theme' => 'default',
            'title' => 'Latest Tweets',
            'title_icon' => 1,
            'title_link' => 1,
            'profile_link' => 1,
            'follow_button' => 1,
            'owner_thumbnail' => 1,
            'others_thumbnail' => 0,
            'links_clickable' => 1,
            'time' => 1,
            'time_format' => 'H:i jS M Y',
            'time_linked' => 1,
            'screen_name' => get_option( 'twp_twitter_screen_name' )
        ) );
        
        // Load other stuff
        add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
        add_action( 'wp_ajax_twp_refresh_widget', array( $this, 'ajax_refresh' ) );
        add_action( 'wp_ajax_nopriv_twp_refresh_widget', array( $this, 'ajax_refresh' ) );
        
        // Shortcode
        add_shortcode( 'twp_widget', array( $this, 'shortcode' ) );

	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
        
        if( TWP_Twitter::is_authed() == false )
            return;
        
        global $settings, $tweets, $before_title, $after_title;
        
     	echo $args['before_widget'];
        
        extract( $args );
        
        // This is not part of widget setting to got to append it manually
        $settings['screen_name'] = get_option( 'twp_twitter_screen_name' );

        $settings = wp_parse_args(
            $instance,
            $settings
        );
        
        if( self::should_cache() ) :
        
            try {
        
                $connection = TWP()->twitter()->get_connection();
                $tweets = $connection->get( "statuses/user_timeline", array( 
                    "count" => $settings['number_of_tweets'],
                    "exclude_replies" => $settings['skip_replies'] == 1 ? true : false,
                    "include_rts" => $settings['skip_rts'] == 1 ? false : true
                ) );

                if( ! isset( $tweets->errors ) ) :
                    self::cache( $tweets );
                endif;
                
            } catch (Exception $e) {
                
                $tweets = self::get_cache();
                
            }            
        
        else :
        
            $tweets = self::get_cache();
        
        endif;
        
        // Wrapper TOP
        if ( $overridden_template = locate_template( 'twp/widget/wrapper/top.php' ) ) {
            load_template( $overridden_template );
        } else {
            load_template( TWP_PLUGIN_DIR . '/includes/views/widget/wrapper/top.php' );
        }
        
        // Header
        if ( $overridden_template = locate_template( 'twp/widget/header.php' ) ) {
            load_template( $overridden_template );
        } else {
            load_template( TWP_PLUGIN_DIR . '/includes/views/widget/header.php' );
        }
        
        // Body
        if ( $overridden_template = locate_template( 'twp/widget/body.php' ) ) {
            load_template( $overridden_template );
        } else {
            load_template( TWP_PLUGIN_DIR . '/includes/views/widget/body.php' );
        }
        
        // Footer
        if ( $overridden_template = locate_template( 'twp/widget/footer.php' ) ) {
            load_template( $overridden_template );
        } else {
            load_template( TWP_PLUGIN_DIR . '/includes/views/widget/footer.php' );
        }
        
        // Wrapper BOTTOM
        if ( $overridden_template = locate_template( 'twp/widget/wrapper/bottom.php' ) ) {
            load_template( $overridden_template );
        } else {
            load_template( TWP_PLUGIN_DIR . '/includes/views/widget/wrapper/bottom.php' );
        }
        
		echo $args['after_widget'];
        
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
        
        $settings = wp_parse_args(
            $instance,
            $this->settings
        );
        
        extract( $settings );
		
        ?>
        
        <div class="twp-widget-settings">
        
            <div class="twp-option-group">
                <h4>Feed Settings</h4>
                
                <p>
                    <label for="<?php echo $this->get_field_id( 'number_of_tweets' ); ?>"><?php _e( 'Number of visible tweets:' ); ?></label> 
                    <input class="widefat" id="<?php echo $this->get_field_id( 'number_of_tweets' ); ?>" name="<?php echo $this->get_field_name( 'number_of_tweets' ); ?>" type="text" value="<?php echo esc_attr( $number_of_tweets ); ?>">
                </p>
                
                <p>
                    <input class="checkbox" id="<?php echo $this->get_field_id( 'skip_replies' ); ?>" name="<?php echo $this->get_field_name( 'skip_replies' ); ?>" type="checkbox" value="1" <?php checked( $skip_replies, 1 ); ?>>          
                    <label for="<?php echo $this->get_field_id( 'skip_replies' ); ?>"><?php _e( 'Exclude replies?' ); ?></label>           
                </p>
                
                <p>
                    <input class="checkbox" id="<?php echo $this->get_field_id( 'skip_rts' ); ?>" name="<?php echo $this->get_field_name( 'skip_rts' ); ?>" type="checkbox" value="1" <?php checked( $skip_rts, 1 ); ?>>  
                    <label for="<?php echo $this->get_field_id( 'skip_rts' ); ?>"><?php _e( 'Exclude RTs?' ); ?></label>                   
                </p>
                
            </div>
            
            <div class="twp-option-group">
               
                <h4>Appearence</h4>
                
                <p>
                    <label for="<?php echo $this->get_field_id( 'theme' ); ?>"><?php _e( 'Theme:' ); ?></label>
                    <select class="widefat" id="<?php echo $this->get_field_id( 'theme' ); ?>" name="<?php echo $this->get_field_name( 'theme' ); ?>">
                        <option value="default" <?php selected( $theme, 'default' ); ?>>Default</option>
                        <option value="dark" <?php selected( $theme, 'dark' ); ?>>Dark</option>
                        <option value="light" <?php selected( $theme, 'light' ); ?>>Light</option>
                    </select>
                </p>
                
                <p>
                    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
                    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
                </p>
                
                <p> 
                    <input class="checkbox" id="<?php echo $this->get_field_id( 'title_icon' ); ?>" name="<?php echo $this->get_field_name( 'title_icon' ); ?>" type="checkbox" value="1" <?php checked( $title_icon, 1 ); ?>>   
                    <label for="<?php echo $this->get_field_id( 'title_icon' ); ?>"><?php _e( 'Show Twitter icon next to title?' ); ?></label>                 
                </p>
                
                <p> 
                    <input class="checkbox" id="<?php echo $this->get_field_id( 'title_link' ); ?>" name="<?php echo $this->get_field_name( 'title_link' ); ?>" type="checkbox" value="1" <?php checked( $title_link, 1 ); ?>>   
                    <label for="<?php echo $this->get_field_id( 'title_link' ); ?>"><?php _e( 'Link title to your Twitter profile?' ); ?></label>                 
                </p>
                
                <p> 
                    <input class="checkbox" id="<?php echo $this->get_field_id( 'profile_link' ); ?>" name="<?php echo $this->get_field_name( 'profile_link' ); ?>" type="checkbox" value="1" <?php checked( $profile_link, 1 ); ?>>   
                    <label for="<?php echo $this->get_field_id( 'profile_link' ); ?>"><?php _e( 'Show a link to your Twitter profile?' ); ?></label>                 
                </p>

                <p> 
                    <input class="checkbox" id="<?php echo $this->get_field_id( 'follow_button' ); ?>" name="<?php echo $this->get_field_name( 'follow_button' ); ?>" type="checkbox" value="1" <?php checked( $follow_button, 1 ); ?>>   
                    <label for="<?php echo $this->get_field_id( 'follow_button' ); ?>"><?php _e( 'Show the follow button?' ); ?></label>                 
                </p>
                
                <hr/>
                
                <p> 
                    <input class="checkbox" id="<?php echo $this->get_field_id( 'owner_thumbnail' ); ?>" name="<?php echo $this->get_field_name( 'owner_thumbnail' ); ?>" type="checkbox" value="1" <?php checked( $owner_thumbnail, 1 ); ?>>   
                    <label for="<?php echo $this->get_field_id( 'owner_thumbnail' ); ?>"><?php _e( 'Show your profile picture next to tweets?' ); ?></label>                 
                </p>
                
                <p> 
                    <input class="checkbox" id="<?php echo $this->get_field_id( 'others_thumbnail' ); ?>" name="<?php echo $this->get_field_name( 'others_thumbnail' ); ?>" type="checkbox" value="1" <?php checked( $others_thumbnail, 1 ); ?>>   
                    <label for="<?php echo $this->get_field_id( 'others_thumbnail' ); ?>"><?php _e( 'Show others\' profile picture next their tweets?' ); ?></label>                 
                </p>
                
                <p> 
                    <input class="checkbox" id="<?php echo $this->get_field_id( 'links_clickable' ); ?>" name="<?php echo $this->get_field_name( 'links_clickable' ); ?>" type="checkbox" value="1" <?php checked( $links_clickable, 1 ); ?>>   
                    <label for="<?php echo $this->get_field_id( 'links_clickable' ); ?>"><?php _e( 'Make links in tweets clickable?' ); ?></label>                 
                </p>
                
                <hr/>
                
                <p> 
                    <input class="checkbox" id="<?php echo $this->get_field_id( 'time' ); ?>" name="<?php echo $this->get_field_name( 'time' ); ?>" type="checkbox" value="1" <?php checked( $time, 1 ); ?>>   
                    <label for="<?php echo $this->get_field_id( 'time' ); ?>"><?php _e( 'Show tweets\' publish time?' ); ?></label>                 
                </p>
                
                <p>
                    <label for="<?php echo $this->get_field_id( 'time_format' ); ?>"><?php _e( 'Specify time format:' ); ?></label> 
                    <input class="widefat" id="<?php echo $this->get_field_id( 'time_format' ); ?>" name="<?php echo $this->get_field_name( 'time_format' ); ?>" type="text" value="<?php echo esc_attr( $time_format ); ?>">
                </p>
                
                <p> 
                    <input class="checkbox" id="<?php echo $this->get_field_id( 'time_linked' ); ?>" name="<?php echo $this->get_field_name( 'time_linked' ); ?>" type="checkbox" value="1" <?php checked( $time_linked, 1 ); ?>>   
                    <label for="<?php echo $this->get_field_id( 'time_linked' ); ?>"><?php _e( 'Link the time to a tweet?' ); ?></label>                 
                </p>
                
            </div>

        </div>

		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
        
        $instance['number_of_tweets'] = ctype_digit( $new_instance['number_of_tweets'] ) && $new_instance['number_of_tweets'] > 0 ? $new_instance['number_of_tweets'] : '10';
        
        $instance['skip_replies'] = $new_instance['skip_replies'] ? $new_instance['skip_replies'] : 0;
        
        $instance['skip_rts'] = ctype_digit( $new_instance['skip_rts'] ) ? $new_instance['skip_rts'] : 0;

        $instance['theme'] = in_array( $new_instance['theme'], array( 'default', 'light', 'dark' ) ) ? $new_instance['theme'] : 'default';
        
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        
        $instance['title_icon'] = ctype_digit( $new_instance['title_icon'] ) ? $new_instance['title_icon'] : 0;
        
        $instance['title_link'] = ctype_digit( $new_instance['title_link'] ) ? $new_instance['title_link'] : 0;
        
        $instance['profile_link'] = ctype_digit( $new_instance['profile_link'] ) ? $new_instance['profile_link'] : 0;
        
        $instance['follow_button'] = ctype_digit( $new_instance['follow_button'] ) ? $new_instance['follow_button'] : 0;
        
        $instance['owner_thumbnail'] = ctype_digit( $new_instance['owner_thumbnail'] ) ? $new_instance['owner_thumbnail'] : 0;
        
        $instance['others_thumbnail'] = ctype_digit( $new_instance['others_thumbnail'] ) ? $new_instance['others_thumbnail'] : 0;
        
        $instance['links_clickable'] = ctype_digit( $new_instance['links_clickable'] ) ? $new_instance['links_clickable'] : 0;
        
        $instance['time'] = ctype_digit( $new_instance['time'] ) ? $new_instance['time'] : 0;
        
        $instance['time_format'] = ( ! empty( $new_instance['time_format'] ) ) ? $new_instance['time_format'] : 0;
        
        $instance['time_linked'] = ctype_digit( $new_instance['time_linked'] ) ? $new_instance['time_linked'] : 0;
        
        self::clear_cache();
        
		return $instance;
	}

    // ...
    
    public static function parse_tweet( $tweet ) {

        //Convert urls to <a> links
        $tweet = preg_replace("/([\w]+\:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/", "<a target=\"_blank\" href=\"$1\">$1</a>", $tweet);

        //Convert hashtags to twitter searches in <a> links
        $tweet = preg_replace("/#([A-Za-z0-9\/\.]*)/", "<a target=\"_new\" href=\"http://twitter.com/search?q=$1\">#$1</a>", $tweet);

        //Convert attags to twitter profiles in &lt;a&gt; links
        $tweet = preg_replace("/@([A-Za-z0-9\/\.]*)/", "<a href=\"http://www.twitter.com/$1\">@$1</a>", $tweet);

        echo $tweet;
        
    }
    
    // ...
    
    public static function cache( $response ) {
     
        delete_transient( 'twp_widget_feed' );
        return set_transient( 'twp_widget_feed', $response, 5 * 60 ); // 5 minute cache
        
    }
    
    // ...
    
    public static function get_cache() {
        
        return get_transient( 'twp_widget_feed' );
        
    }
    
    // ...
    
    public static function should_cache() {
        
        if ( false === self::get_cache() || '' === self::get_cache() )
            return true;
        
        return false;

    }
    
    // ... 
    
    public static function clear_cache() {
     
        return delete_transient( 'twp_widget_feed' );
        
    }
    
    // ...
    
    public function ajax_refresh() {
        
        global $settings, $tweet; 
        
        if( false === ( $cache = self::get_cache() ) || empty( $cache ) || ! is_array( $cache ) ) :
            echo json_encode( array( 'status' => 'FAIL', 'response' => 'Empty cache' ) );
            exit;
        endif;
        
        $since_id = $cache[0]->id;
        
        if( ! $since_id ) :
            echo json_encode( array( 'status' => 'FAIL', 'response' => 'No since id' ) );
            exit;
        endif;

        // Get & parse settings
        $_settings = $this->get_settings();
        $_settings = reset( $_settings );
        
        $settings = array();
        $settings = wp_parse_args( $_settings, array(
            'number_of_tweets' => 0, // unlimited
            'skip_replies' => 1,
            'skip_rts' => 1,
            'theme' => 'default',
            'title' => 'Latest Tweets',
            'title_icon' => 1,
            'title_link' => 1,
            'profile_link' => 1,
            'follow_button' => 1,
            'owner_thumbnail' => 1,
            'others_thumbnail' => 0,
            'links_clickable' => 1,
            'time' => 1,
            'time_format' => 'H:i jS M Y',
            'time_linked' => 1,
            'screen_name' => get_option( 'twp_twitter_screen_name' )
        ) );
        
        $html = '';
        
        $connection = TWP()->twitter()->get_connection();
        $tweets = $connection->get( "statuses/user_timeline", array( 
            "count" => $settings['number_of_tweets'],
            "exclude_replies" => $settings['skip_replies'] == 1 ? true : false,
            "include_rts" => $settings['skip_rts'] == 1 ? false : true,
            "since_id" => $since_id
        ) );
        
        if( $tweets && ! isset( $tweets->errors ) ) :
        
            $new_cache = array_merge( $tweets, $cache );
        
            if( count( $new_cache ) > $settings['number_of_tweets'] && $settings['number_of_tweets'] > 0 )
                $new_cache = array_slice( $new_cache, 0, $settings['number_of_tweets'] );

            self::cache( $new_cache ); // force cache

            foreach( $tweets as $tweet ) :
                ob_start();
                if ( $overridden_template = locate_template( 'twp/widget/single.php' ) ) {
                    load_template( $overridden_template, false );
                } else {
                    load_template( TWP_PLUGIN_DIR . '/includes/views/widget/single.php', false );
                }
                $html .= ob_get_contents();
                ob_end_clean();
            endforeach;
            
            echo json_encode( array( 'status' => 'OK', 'feed' => $html ) );
            exit;
        
        endif;
        
        echo json_encode( array( 'status' => 'FAIL', ) );
        exit;
        
    }
    
    // ..
    
    public function assets() {
        
        // Widget CSS
        wp_register_style( 'twp-widget', TWP_PLUGIN_URL . '/assets/css/twp-widget.css', null, TWP()->version );
        wp_enqueue_style( 'twp-widget' );
        
        // Widget JS
        wp_register_script( 'twp-widget', TWP_PLUGIN_URL . '/assets/js/twp-widget.js', null, TWP()->version );
        wp_localize_script( 'twp-widget', 'twpwidget', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
        wp_enqueue_script( 'twp-widget' );
        
    }
    
    // ...
    
    public function shortcode( $atts ) {
     
        global $wp_widget_factory;

        extract(shortcode_atts(array(
            'number_of_tweets' => 0, // unlimited
            'skip_replies' => 1,
            'skip_rts' => 1,
            'theme' => 'default',
            'title' => 'Latest Tweets',
            'title_icon' => 1,
            'title_link' => 1,
            'profile_link' => 1,
            'follow_button' => 1,
            'owner_thumbnail' => 1,
            'others_thumbnail' => 0,
            'links_clickable' => 1,
            'time' => 1,
            'time_format' => 'H:i jS M Y',
            'time_linked' => 1,
            'screen_name' => get_option( 'twp_twitter_screen_name' )
        ), $atts));

        $widget_name = wp_specialchars('TWP_Widget');

        if (!is_a($wp_widget_factory->widgets[$widget_name], 'WP_Widget')):
            $wp_class = 'WP_Widget_'.ucwords(strtolower($class));

            if (!is_a($wp_widget_factory->widgets[$wp_class], 'WP_Widget')):
                return '<p>'.sprintf(__("%s: Widget class not found. Make sure this widget exists and the class name is correct"),'<strong>'.$class.'</strong>').'</p>';
            else:
                $class = $wp_class;
            endif;
        endif;
        
        ob_start();
        the_widget($widget_name, $instance, array('widget_id'=>'arbitrary-instance-'.$id,
            'before_widget' => '',
            'after_widget' => '',
            'before_title' => '',
            'after_title' => ''
        ));
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
        
    }
    
}