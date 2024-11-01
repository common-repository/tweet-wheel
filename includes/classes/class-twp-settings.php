<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Main class of TWP_Settings
 *
 * @class TWP_Settings
 * @since 2.0
 */

class TWP_Settings {
    
    private $status,
            $publish_action,
            $order;
    
    // ...
    
    public function __construct( $term_id ) {
        
        if( $term_id == null || is_wp_error( get_term( $term_id, 'twp_queue' ) ) )
            return;
        
        $this->term_id = $term_id;
        
        $this->status = get_term_meta( $this->term_id, 'twp_settings_status', true );
        $this->publish_action = get_term_meta( $this->term_id, 'twp_settings_publish_action', true );
        $this->order = get_term_meta( $this->term_id, 'twp_settings_order', true );
        
    }
    
    // ...
    
    public function get_queue_status() {
     
        return $this->status;
        
    }
    
    // ...
    
    /**
     * An actual tweet times field with form fields
     *
     * @type function
     * @date 06/10/2015
	 * @since 2.0
     *
     * @param n/a
	 * @return n/a
     */
    
    public function display_tab() {
        
        ?>
        
        <div class="settings-wrapper">
            
            <form method="post" class="twp-settings-form">
            
                <input id="twp-settings-form-data-<?php echo $this->term_id; ?>" class="serialized-form" type="hidden" value="0">
                
                <input type="hidden" name="term_id" value="<?php echo $this->term_id; ?>">
            
                <div class="form-top"><input type="submit" class="twp-button twp-save-queue-settings" value="Save Settings"><span class="form-status"></span></div>
                
                <div class="twp-option-group">

                    <h4>Queue name</h4>
                    
                    <?php
                    $term = get_term( $this->term_id, 'twp_queue' );
                    ?>

                    <input type="text" name="name" value="<?php echo $term->name; ?>">
                    
                </div>
                
                <div class="twp-option-group">

                    <h4>Queue status</h4>

                    <select name="status">
                        <option value="running" <?php echo $this->status == 'resumed' ? 'selected' : ''; ?>>Running</option>
                        <option value="paused" <?php echo $this->status == 'paused' ? 'selected' : ''; ?>>Paused</option>
                        <option value="frozen" <?php echo $this->status == 'frozen' ? 'selected' : ''; ?>>Frozen</option>
    
                    </select>
                    
                </div>
                
                <div class="twp-option-group">

                    <h4>What to do after a post has been tweeted?</h4>

                    <select name="publish_action">
                        <option value="loop" <?php echo $this->publish_action == 'loop' ? 'selected' : ''; ?>>Re-queue at the bottom of the queue</option>
                        <option value="remove" <?php echo $this->publish_action == 'remove' ? 'selected' : ''; ?>>Remove from the queue</option>
                    </select>
                    
                </div>
                
                <div class="twp-option-group">

                    <h4>What's the order you would like your posts to be tweeted in?</h4>

                    <select name="order">
                        <option value="order" <?php echo $this->order == 'order' ? 'selected' : ''; ?>>Queue order</option>
                        <option value="random" <?php echo $this->order == 'random' ? 'selected' : ''; ?>>Random order</option>
                    </select>
                    
                </div>
                
                <div class="twp-option-group">

                    <a style="display:block" href="<?php echo TWP_UPGRADE_LINK; ?>" target="_blank">
                        <img width="568" height="94" src="<?php echo TWP_PLUGIN_URL; ?>/assets/images/go-pro/email-notifications.png">
                    </a>

                </div>
                
            </form>
            
            <script>
                jQuery(document).ready(function() {
                     var data = jQuery('#twp-settings-form-data-<?php echo $this->term_id; ?>').val();

                    if( data == 0 )
                        jQuery('#twp-settings-form-data-<?php echo $this->term_id; ?>').val(jQuery('#twp-settings-form-data-<?php echo $this->term_id; ?>').parents('form').serialize());
                });
            </script>
            
        </div>
        
        <?php
        
    }
    
    // ...
    
    public static function get_status( $term_id ) {
     
        return get_term_meta( $term_id, 'twp_settings_status', true );
        
    }
    
    // ...
    
    /**
     * Restores default settings for a given queue
     *
     * @type function
     * @date 07/10/2015
	 * @since 2.0
     *
     * @param n/a
	 * @return array
     */
    
    public static function restore_default_settings( $term_id ) {
        
        update_term_meta( $term_id, 'twp_settings_status', 'paused' );
        update_term_meta( $term_id, 'twp_settings_publish_action', 'loop' );
        update_term_meta( $term_id, 'twp_settings_order', 'order' );
        
    }
    
    // ...
    
    /**
     * Restores default settings for a given queue
     *
     * @type function
     * @date 07/10/2015
	 * @since 2.0
     *
     * @param n/a
	 * @return array
     */
    
    public static function delete_all_settings( $term_id ) {
        
        delete_term_meta( $term_id, 'twp_settings_status' );
        delete_term_meta( $term_id, 'twp_settings_publish_action' );
        delete_term_meta( $term_id, 'twp_settings_order' );
        
    }
    
    // ...
    
    public function status() { return $this->status; }
    public function publish_action() { return $this->publish_action; }
    public function order() { return $this->order; }
    
}