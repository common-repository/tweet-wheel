function twp_refresh_widget() {
    
    var widgets = jQuery('[id^=twp_widget-]');
    
    if( widgets.length > 0 ) {
    
        setTimeout(function () {

            jQuery.post(
                twpwidget.ajaxurl,
                {
                    action : 'twp_refresh_widget'   
                },
                function(response) {
                    
                    var data = jQuery.parseJSON( response );
                    
                    if( data.status == 'OK' ) {
                     
                        jQuery.each(widgets, function(k,v) {
                            
                            var ul = jQuery(this).find('ul');
                            
                            ul.prepend(data.feed);
                            
                        });
                        
                    }
                    
                }
            );

            twp_refresh_widget();
        }, 60000); // check every minute
        
    }
    
}

jQuery(document).ready(function() {
    
    twp_refresh_widget();
    
});