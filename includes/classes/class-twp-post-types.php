<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class TWP_Post_Types {
    
    public static function init() {
     
        add_action( 'init', array( 'TWP_Post_Types', 'register_tax' ) );
        //add_action( 'init', array( 'TWP_Queues', 'create_default_queue' ) );
        
    }

    /**
     * Register Taxonomy
     *
     * @type function
     * @date 25/08/2015
     * @since 2.0
     *
     * @param N/A
     * @return N/A
     **/

    public static function register_tax() {

        if ( taxonomy_exists( 'twp_queue' ) )
            return;

        register_taxonomy( 
            'twp_queue',
            twp_get_all_enabled_post_types(),
            array(
                'label' => __( 'Queue' ),
                'public' => false,
                'rewrite' => false,
                'hierarchical' => false,
                'query_var' => true,
                'sort' => true
            )
        );
        
    }
    
}

TWP_Post_Types::init();