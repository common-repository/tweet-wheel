<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function twp_install() {

    global $wpdb;
    
    // Regsiter Tax aka Queues
    TWP_Post_Types::register_tax();

    // Create a default queue
    twp_create_default_queue();
    
    twp_delete_depracated_tables();
    twp_create_cron_jobs();
    twp_create_options();

}

// ...

function twp_delete_depracated_tables() {

    global $wpdb;

    $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "twp_queue" );
    $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "twp_log" );
    $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "twp_stats" );

}

// ...

/**
 * Create cron jobs (clear them first)
 */
function twp_create_cron_jobs() {

    wp_clear_scheduled_hook( 'tweet_wheel_tweet' );

    if( ! wp_next_scheduled( 'tweet_wheel_tweet' ) )
        wp_schedule_event( current_time( 'timestamp' ), 'every_five', 'tweet_wheel_tweet' );

}

// ...

function twp_create_options() {

    global $twp_db_version;

    $default = array(
        'post_type' => array( 0 => 'post' ),
        'tweet_text' => '{{TITLE}} - {{URL}}',
        'keep_data' => 1,
        'user_roles' => array( 'administrator' => 1 )
    );

    add_option( 'twp_settings_options', $default );
    add_option( 'twp_activation_redirect', true );

}

// ...

/**
 * Redirect after plugin activation (unless its a bulk update)
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param N/A
 * @return N/A
 **/

function twp_redirect() {
    if (get_option('twp_activation_redirect', false)) {
        delete_option('twp_activation_redirect');
        if(!isset($_GET['activate-multi']))
        {
            wp_redirect(admin_url('/admin.php?page=twp_about'));
        }
    }
}

// ...
    
function twp_activation_check() {
    if ( ! twp_compatible_version() ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die( __( 'Tweet Wheel Pro requires at least WordPress 4.4 and PHP 5.4!', TWP_TEXTDOMAIN ) );
    }
}

// ...

function twp_assign_caps() {

    // Make sure admin always have the capability
    $admin = get_role( 'administrator' );        
    $admin->add_cap( TWP_USER_CAP );

}

// ...

function twp_check_version() {

    if ( ! twp_compatible_version() ) {
        if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            add_action( 'admin_notices', array( $this, 'disabled_notice' ) );
            if ( isset( $_GET['activate'] ) ) {
                unset( $_GET['activate'] );
            }
        }
    }

}

// ...

function twp_disabled_notice() {

    echo '<strong>' . esc_html__( 'Tweet Wheel Pro requires WordPress 4.4 or higher and PHP 5.4 or higher!', TWP_TEXTDOMAIN ) . '</strong>';

} 

// ...

function twp_compatible_version() {

    global $wp_version;

    if ( version_compare( $wp_version, '4.4', '<' ) ) {
        return false;
    }

    if ( version_compare( PHP_VERSION, '5.4.0', '<' ) ) {
        return false;
    }

    return true;
}

function twp_create_default_queue() {

    if( ! term_exists( 'Untitled Queue', 'twp_queue' ) ) :

        $term = wp_insert_term(
            'Untitled Queue',
            'twp_queue'
        );

        
        
        var_dump( TWP_Settings::restore_default_settings( $term['term_id'] ) ); die;
    
        TWP_Schedule::restore_default_settings( $term['term_id'] );

        return true;

    endif;  

    return false;
    
}