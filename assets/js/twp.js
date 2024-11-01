var TWP = new function () {
 
    "use strict";
    
    var t = this;
    
    t.getStatus = function (term_id) {
     
        jQuery.post( 
            ajaxurl,
            {
                action : 'twp_ajax_action', 
                admin_action : 'get_status',
                nonce : _TWPAJAX.twNonce,
                data : {
                    term_id: term_id
                }
            },
            function(response) {
                jQuery('.tw-queue-tabs span[data-tab-content="tw-queue-'+term_id+'"]').removeClass('tw-queue-status-frozen tw-queue-status-running tw-queue-status-paused').addClass('tw-queue-status-'+response);
            }
        ); 

    }
    
    // ...
    
    t.getQueueName = function (term_id) {
     
        jQuery.post( 
            ajaxurl,
            {
                action : 'twp_ajax_action', 
                admin_action : 'get_queue_name',
                nonce : _TWPAJAX.twNonce,
                data : {
                    term_id: term_id
                }
            },
            function(response) {
                jQuery('.tw-queue-tabs span[data-tab-content="tw-queue-'+term_id+'"] span').text(response);
            }
        ); 

    }
    
    // ...
    
    t.fixCron = function() {
     
        jQuery.post( 
            ajaxurl,
            {
                action : 'twp_ajax_action', 
                admin_action : 'fix_cron',
                nonce : _TWPAJAX.twNonce
            },
            function(response) {
                
                var data = jQuery.parseJSON(response);
                var res = jQuery('#twp-fix-cron-results');
                res.text(data.response);
                
                if( data.status == 'OK') {
                    location.reload();    
                }
                
            }
        ); 
        
    }
    
    // ...
    
    var init = function () {

        jQuery('#wp-cron-alert-hide').click(function(e){
            e.preventDefault();
            jQuery('.tw-wp-cron-alert').slideUp();
            jQuery.post( 
                ajaxurl, 
                { 
                    action : 'twp_ajax_action',
                    admin_action: 'close_cron_alert', 
                    nonce: _TWPAJAX.twNonce 
                }
            ); 
        });
        
        // ...
        
        jQuery('div[id^="tw-queue-content-settings"] form').submit(function(e) {
           
            e.preventDefault();
            
            var form = jQuery(this);
            var status = form.find('.form-status');
            var button = form.find('input[type=submit]');
            var data_field = form.find('.serialized-form');
            
            status.removeClass('ok changed').addClass('saving').text('Saving your settings...');  
            TWPHelpers.showFormSaveLoader(form.parents('.tw-queue-content-inner'));
            
            jQuery.post(
                ajaxurl, 
                { 
                    action : 'twp_ajax_action',
                    admin_action: 'save_settings', 
                    data : {
                        form : form.serialize()
                    },
                    nonce: _TWPAJAX.twNonce
                },
                function(response) { 
                    status.removeClass('saving').addClass('ok').text('Your settings have been saved.'); 
                    data_field.val(form.serialize());
                    button.removeClass('twp-button-primary');
                    TWPHelpers.hideFormSaveLoader(form.parents('.tw-queue-content-inner'));
                    t.getStatus(form.find('input[name="term_id"]').val());
                    t.getQueueName(form.find('input[name="term_id"]').val());
                }
            );
            
            return false;
            
        });
        
        // ...
        
        jQuery('div[id^="tw-queue-content-settings"] form select').on('change keypress', function() {

            var form = jQuery(this).parents('form');
            var status = form.find('.form-status');
            var button = form.find('input[type=submit]');
            var curr_data = form.find('.serialized-form').val();
            var new_data = form.serialize();

            if( curr_data !== new_data ) { 
                status.removeClass('ok saved').addClass('changed').text('Some settings have been changed. Please save them!');   
                button.addClass('twp-button-primary');
            } else {
                status.removeClass('ok saved changed').text(''); 
                button.removeClass('twp-button-primary');
            }
            
        });

        // ...
        
        jQuery('#twp-fix-cron').click(function(e) {
            
            e.preventDefault();
            t.fixCron(); 
            
        });
            
    }

    // ...
    
    jQuery(document).ready(function() {
       init(); 
    });
    
}