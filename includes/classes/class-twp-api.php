<?php

class TWP_API {

    var $api_url,
        $plugin_slug;

    function __construct( $api_url ) {
        
        $this->api_url = $api_url;
        $this->plugin_slug = 'tweet-wheel/tweet-wheel.php';

    }
    
    /** 
     * Call API
     **/
    
    function call( $action, $args ) {

        $request_args = wp_parse_args( array(
            'slug' => $this->plugin_slug,
            'version' => TWP_VERSION
        ), $args );

        $request_string = $this->prepare( $action, $request_args );
        $raw_response = wp_remote_post( $this->api_url, $request_string );
    
        $response = null;
        if( !is_wp_error($raw_response) && ($raw_response['response']['code'] == 200) )
            $response = $raw_response['body'];
        
        if( is_wp_error($raw_response) ){
            $res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $raw_response->get_error_message());
        } else {
            $res = unserialize($raw_response['body']);
            if ($res === false)
                $res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $raw_response['body']);
        }

        return $res;
    }

    // ...
    
    /**
     * Prepare API request
     **/
    
    function prepare( $action, $args ) {
        global $wp_version;

        return array(
            'body' => array(
                'action' => $action, 
                'request' => serialize($args),
                'api_key' => md5(home_url())
            ),
            'user-agent' => 'WordPress/'. $wp_version .'; '. home_url()
        );	
    }


}