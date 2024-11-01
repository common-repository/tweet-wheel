var TWPHelpers = new function() {

    var t = this;
    
    // ...
    
    t.hashtagReplacer = function (hash){
    
        var replacementString = jQuery.trim(hash);

        return '<a href="#gethashtag"  class="tw-tag tw-popularity-unknown tw-tag-check" contenteditable="false" data-tag="' + replacementString.replace('#','') + '">' + replacementString + '</a>';

    }
    
    // ...
    
    t.showFormSaveLoader = function (container) {
     
        var overlay = '<div class="form-saving-overlay"></div>';
        container.prepend( overlay );
        
    }
    
    // ...
    
    t.hideFormSaveLoader = function (container) {
     
        container.find('.form-saving-overlay').fadeOut(200);
        
    }
    
    /*========================
    /*=======================*/
    
    var handleScheduleTabs = function() {
     
        jQuery('.tw-queue-tabs > ul > li > span, .tw-queue-content-tab, .weekly-schedule-tabs span').click(function() {
       
            var content = jQuery( '#' + jQuery(this).data('tab-content') );
            var content_class = jQuery( '.' + jQuery(this).data('tab-content-class') );

            if( jQuery(this).hasClass('tw-schedule-tab') ) {

                jQuery(this).parents('.times-wrapper').find('.active').removeClass('active');
                jQuery(this).parents('.times-wrapper').find(content_class).hide();

            }

            jQuery(this).addClass('active');

            content.show();

        });
        
    }
    
    // ...
    
    var handleMobileTabs = function() {
     
        jQuery('#twp-queue-tab-select').change(function() {

            window.location.replace( jQuery(this).val() );

            return;

        });
        
    }
    
    // ...
    
    var handleScheduleTools = function() {
     
        jQuery('body').on('click','.schedule-tools>li>span',function() {
            
            jQuery('.schedule-tools>li>span').not(this).removeClass('active');
            jQuery('.schedule-tools>li>span').not(this).next('ul').hide();
            
            if( jQuery(this).hasClass('active') ) {                
                jQuery(this).removeClass('active');
                jQuery(this).next('ul').hide();
            } else {
                jQuery(this).addClass('active');
                jQuery(this).next('ul').show();
            }
            
        });
        
    }
    
    // ...
    
    var init = function () {
       
        handleMobileTabs();
        handleScheduleTabs();
        handleScheduleTools();
        
    }
    
    // ...
    
    jQuery(document).ready(function() {
        
        init();
    
    });

}