(function($) {
    
    $.fn.twpTooltip = function(options) {

        var settings = jQuery.extend({}, {
            admin_action:null,
            content: null
        }, options);        

        return this.each(function() {

            // tooltip function 
            jQuery(this).bind( 'click', function(e)
            {

                e.preventDefault();

                target  = jQuery( this );
                tooltip = jQuery( '<div class="twp-tooltip"></div>' );

                tooltip.css( 'opacity', 0 )
                       .html( '<span class="twp-loader"></span>' )
                       .appendTo( 'body' );

                var url = ajaxurl;

                // Get all elements data attributes into a request
                strJson = "{"
                jQuery.each(jQuery(this).data(), function(i, v) {
                    strJson += '"' + i + '":"' + v + '",';
                });
                strJson = strJson.substring(0, strJson.length - 1);
                strJson += '}';
                var data = jQuery.parseJSON( strJson );

                // Run a ajax request
                jQuery.post(
                    url,
                    {
                        data : data,
                        action : 'twp_ajax_action',
                        admin_action : settings.admin_action,
                        nonce:_TWPAJAX.twNonce
                    },
                    function(response) {
                        tooltip.html(response);  
                        init_tooltip();
                    }
                );

                var init_tooltip = function()
                {
                    if( jQuery( window ).width() < tooltip.outerWidth() * 1.5 )
                        tooltip.css( 'max-width', jQuery( window ).width() / 2 );
                    else
                        tooltip.css( 'max-width', 340 );

                    var pos_left = target.offset().left + ( target.outerWidth() / 2 ) - ( tooltip.outerWidth() / 2 ),
                        pos_top  = target.offset().top - tooltip.outerHeight() - 20;

                    if( pos_left < 0 )
                    {
                        pos_left = target.offset().left + target.outerWidth() / 2 - 20;
                        tooltip.addClass( 'left' );
                    }
                    else
                        tooltip.removeClass( 'left' );

                    if( pos_left + tooltip.outerWidth() > jQuery( window ).width() )
                    {
                        pos_left = target.offset().left - tooltip.outerWidth() + target.outerWidth() / 2 + 20;
                        tooltip.addClass( 'right' );
                    }
                    else
                        tooltip.removeClass( 'right' );

                    if( pos_top < 0 )
                    {
                        var pos_top  = target.offset().top + target.outerHeight();
                        tooltip.addClass( 'top' );
                    }
                    else
                        tooltip.removeClass( 'top' );

                    tooltip.css( { left: pos_left, top: pos_top } )
                           .animate( { top: '+=10', opacity: 1 }, 50 );
                };

                init_tooltip();
                jQuery( window ).resize( init_tooltip );

                var remove_tooltip = function()
                {
                    tooltip.animate( { top: '-=10', opacity: 0 }, 50, function()
                    {
                        jQuery( this ).remove();
                    });

                };

                tooltip.bind('mouseleave',remove_tooltip);
            });

        });

    };
    
})(jQuery);