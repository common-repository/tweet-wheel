<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class TWP_Plugin_Settings {
	
	private $settings_framework = null;
    
    public function __construct() {
        
        // Settings only for authed users
        if( TWP()->twitter()->is_authed() == 0 )
            return;

		$this->settings_framework = new SF_Settings_API( $id = 'twp_settings', $title = '', __FILE__);
        
		$this->settings_framework->load_options( $this->options() );

		add_filter( 'twp_settings_tab_options-general', array( $this, 'post_type_value' ), 10, 2 );
		add_action( 'twp_settings_options_type_post_type', array( $this, 'post_type_settings' ) );
		add_action( 'twp_settings_after_form_tab-general', array( $this, 'post_type_js' ) );

    }
    
    public function options() {
     
        // General tab
        $options[] = array( 'name' => __( 'General', TWP_TEXTDOMAIN ), 'type' => 'heading' );
        $options[] = array( 'name' => __( 'General Options', TWP_TEXTDOMAIN ), 'type' => 'title', 'desc' => '' );

        $options[] = array(
            'name' => __( 'Allowed Post Types', TWP_TEXTDOMAIN ),
            'desc' => __( 'Select custom post types, which should be used by the plugin', TWP_TEXTDOMAIN ),
            'id'   => 'post_type',
            'type' => 'post_type'
        );

        $options[] = array(
            'name' => __( 'Default Tweet Template', TWP_TEXTDOMAIN ),
            'desc' => __( 'Default tweet text can be overriden by custom post tweet text setting available on edit page of each post. Allowed tags: {{TITLE}} for post title and {{URL}} for post permalink.', TWP_TEXTDOMAIN ),
            'id'   => 'tweet_template',
            'type' => 'textarea',
            'placeholder' => __( 'What\'s happenng?', TWP_TEXTDOMAIN ),
            'std' => '{{TITLE}} - {{URL}}'
        );
        
        $roles = get_editable_roles();
        $roles_dropdown = array();
        foreach( $roles as $role => $caps ) :
            $roles_dropdown += array( $role => $caps['name'] );        
        endforeach;

        $options[] = array(
            'name' => __( 'User Roles', TWP_TEXTDOMAIN ),
            'desc' => __( 'Select which a required user role allowed to manage the plugin', TWP_TEXTDOMAIN ),
            'id' => 'user_roles',
            'type' => 'custom',
            'std' => '<a style="display:inline-block;" href="' . TWP_UPGRADE_LINK . '" target="_blank"><img width="333" height="134" src="' . TWP_PLUGIN_URL . '/assets/images/go-pro/user-roles.png"></a>'
        );
        
        $options[] = array(
            'name' => __( 'Keep Plugin Data', TWP_TEXTDOMAIN ),
            'desc' => __( 'Select if you want to keep all settings when plugin gets uninstalled.', TWP_TEXTDOMAIN ),
            'id' => 'keep_data',
            'type' => 'checkbox'
        );

        $options[] = array(
            'name' => __( 'Disconnect @' . get_option( 'twp_twitter_screen_name' ) . ' Twitter Account', TWP_TEXTDOMAIN ),
            'desc' => __( 'You will need to authorize another account to resume using this plugin.', TWP_TEXTDOMAIN ),
            'id'   => 'deauth',
            'type' => 'deauth'
        );
        
        return $options;
        
    }
    
    public function page() {
        
        if( TWP()->twitter()->is_authed() == false ) :
            echo "You need to be authorised with Twitter to access this page";
            return;
        endif;
        
	    ?>
		<div class="wrap tw-settings-page">
			<h2><img class="alignleft" style="margin-right:10px;" src="<?php echo TWP_PLUGIN_URL . '/assets/images/tweet-wheel-page-icon.png'; ?>"><?php _e( 'Settings', TWP_TEXTDOMAIN ); ?></h2>
			<?php $this->settings_framework->init_settings_page(); ?>
		</div>
		<?php
        
    }
	
	public function post_type_settings() {
		
		echo '<div id="post_type_wrapper"></div>';
		
	}
	
    public function post_type_value( $tabs, $post ) {

        if( ! isset( $tabs['general'] ) )
            return $tabs;
        
        if( ! isset( $post['post_type'] ) )
            $post['post_type'] = array();

        $tabs['general'][] = array(
            'name' => __( 'Allowed post types', TWP_TEXTDOMAIN ),
            'id' => 'post_type',
            'type' => 'post_type',
            'options' => $post['post_type']
        );
        
        return $tabs;
        
    }
	
	public function post_type_js() {
		
		$options = twp_get_option( 'twp_settings', 'post_type' );
		
		?>
		
		<script>
		jQuery.noConflict();
		jQuery(window).load(function(){
	
			var el = jQuery('#post_type_wrapper');
			var post_types = jQuery.parseJSON('<?php echo json_encode($options); ?>');
		
			if( el.length == 0 )
				return;
		
			el.text( 'Loading...' );
		
			jQuery.post(
				ajaxurl, 
				{
                    action : 'twp_ajax_action',
					admin_action : 'get_post_types',
					nonce: _TWPAJAX.twNonce
				},
				function( response ) {
				
					var data = jQuery.parseJSON( response );
				
					if( data.response == 'error' ) {
						el.text( data.message );
					}
				
					el.empty();

					jQuery.each( data.data, function( k,v ) {
						
						var is_checked = jQuery.inArray( k, post_types ) != -1 ? true : false;
					
						var html = '<label for="post_type_'+k+'"><input name="twp_settings_options[post_type][]" id="post_type_'+k+'" type="checkbox" value="'+k+'" '+( is_checked ? 'checked' : '' )+'>'+v.label+'</label><br/>';

						el.append(html);
					
					} );
				
				}
			);
		
		});
		
		</script>
		
		<?php		
	}
    
    // ...

}

function twp_load_plugin_settings() {
    
    if ( !function_exists('get_editable_roles') ) {
        require_once( ABSPATH . '/wp-admin/includes/user.php' );
    }
 
    new TWP_Plugin_Settings;
    
}

add_action( 'init', 'twp_load_plugin_settings' );