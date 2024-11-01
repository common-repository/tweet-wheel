<?php

if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit; // Exit if accessed directly

/**
 * Function that runs on plugin uninstallation (not deactivation)
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param N/A
 * @return N/A
 **/

if( ! function_exists( 'twp_uninstall' ) ) :

function twp_uninstall() {
    
    global $wpdb;
    
    // Remove cron task
    twp_unschedule_task();
    
    // ...
    
    // Don't run anything below if user opted-in to leave settings
    if( twp_get_option( 'twp_settings', 'keep_data' ) == 1 )
        return false;
    
    // Remove 2.0 data from DB
    foreach ( array( 'twp_queue' ) as $taxonomy ) {
        
        // Prepare & excecute SQL
        $terms = $wpdb->get_results( $wpdb->prepare( "SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('%s') ORDER BY t.name ASC", $taxonomy ) );

        // Delete Terms
        if ( $terms ) {
            foreach ( $terms as $term ) {
                $wpdb->delete( $wpdb->term_taxonomy, array( 'term_taxonomy_id' => $term->term_taxonomy_id ) );
                $wpdb->delete( $wpdb->terms, array( 'term_id' => $term->term_id ) );
            }
        }

        // Delete Taxonomy
        $wpdb->delete( $wpdb->term_taxonomy, array( 'taxonomy' => $taxonomy ), array( '%s' ) );
    }
    
    // Post meta
    $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_name LIKE 'twp_%'" );
    
    // Options
    $wpdb->query( "DELETE FROM " . $wpdb->prefix . "options WHERE option_name LIKE 'twp_%'" );
    
}

endif;

// ...

/**
 * Delete Cron Job
 *
 * @type function
 * @date 16/06/2015
 * @since 1.0
 *
 * @param N/A
 * @return N/A
 **/

if( ! function_exists( 'twp_unschedule_task' ) ) :

function twp_unschedule_task() {
    
    wp_clear_scheduled_hook( 'tweet_wheel_tweet' );

}  

endif; 