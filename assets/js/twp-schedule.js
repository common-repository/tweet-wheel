var TWPSchedule = new function () {
    
    var t = this;
    
    t.createDateField = function (obj) {
                
        // get timestamp
        var d = new Date(obj.selectedYear,obj.selectedMonth,obj.selectedDay);
        
        // set values
        var day = d.getDate();
        var month = d.getMonth() + 1; // fix for PHP; JS months start with 0
        var year = d.getFullYear();
        
        if( day < 10 )
            day = '0' + day;
        
        if( month < 10 )
            month = '0' + month;
     
        return '<div class="twp-schedule-date-times"><input class="selected_date_'+day+month+year+'" type="hidden" name="selected_dates['+day+month+year+']" value="1"><span class="twp-remove-date-time" data-date="'+(d.getTime()/1000)+'"><i class="dashicons dashicons-trash"></i></span><h4 style="margin-top: 0px;">Schedule for <strong>'+day+'/'+month+'/'+year+'</strong></h4><ul class="schedule-tools"><li><a href="#" class="add-new-time time-date-specific" data-date="'+day+month+year+'">Add</a></li><li><span>Generate</span><ul class="generate-times"><li><span data-interval="5" data-date="'+day+month+year+'">Every 5 minutes</span></li><li><span data-interval="10" data-date="'+day+month+year+'">Every 10 minutes</span></li><li><span data-interval="15" data-date="'+day+month+year+'">Every 15 minutes</span></li><li><span data-interval="30" data-date="'+day+month+year+'">Every 30 minutes</span></li><li><span data-interval="60" data-date="'+day+month+year+'">Every 1 hour</span></li><li><span data-interval="120" data-date="'+day+month+year+'">Every 2 hours</span></li></ul></li><li><a href="#" class="clear-times">Clear</a></li></ul><ul class="times"></ul></div>';
        
    }
    
    // ...
    
    t.getDateField = function (el,dateText) {
        
        if( el.parents('form').find('input.selected_date_'+dateText).length == 0 ) {
            return false;   
        }

        return el.parents('form').find('input.selected_date_'+dateText);
        
    }
    
    // ...
    
    t.getNextTime = function (term_id) {
     
        jQuery.post( 
            ajaxurl,
            {
                action : 'twp_ajax_action', 
                admin_action : 'get_time',
                nonce : _TWPAJAX.twNonce,
                data : {
                    term_id: term_id
                }
            },
            function(response) {
                jQuery('.tw-queue-tabs span[data-tab-content="tw-queue-'+term_id+'"] small').text('Next: ' + response);
            }
        ); 

    }
    
    // ...
    
    t.renderHours = function( curr_hour ) {
        
        var hours = '';
        
        for( var i = 0; i < 24; i++ ) {

            var label = i;

            if( label < 10 )
                label = '0' + i;

            hours += '<option value=' + i + ' ' + ( curr_hour == i ? 'selected' : '' ) +'>' + label + '</option>';

        }
        
        return hours;

    }
    
    // ...
    
    t.renderMinutes = function( curr_minute ) {
        
        var minutes = '';
     
        for( var i = 0; i < 60; i+=5 ) {

            var label = i;

            if( label < 10 )
                label = '0' + i;

            minutes += '<option value=' + i + ' ' + ( curr_minute == i ? 'selected' : '' ) +'>' + label + '</option>';

        }
        
        return minutes;
        
    }
    
    // ...
    
    t.renderTimeRow = function( target, interval, last_index, time ) {

        if( last_index == undefined ) {
            var last_index = 0;   
        }
        
        if( time == undefined ) {
            var time = 0;   
        }
        
        if( time < 1440 ) {
            
            var curr_hour = Math.floor( time / 60 );
            var curr_minute = time % 60;

            if( target.parents('.schedule-tools').next().find('li').length != 0 ) { 
                last_index = target.parents('.schedule-tools').next().find('li').last().data('index');
                last_index++;
            }

            hours = t.renderHours( curr_hour );
            minutes = t.renderMinutes( curr_minute );

            template = vsprintf( _TWPQueues.templates.time_row, [ 'weekly_times['+target.data('day')+']['+last_index+'][hour]', hours, 'weekly_times['+target.data('day')+']['+last_index+'][minute]', minutes ] );  

            target.parents('.schedule-tools').next('ul').append( '<li data-index="'+last_index+'">' + template + '</li>' );
            
            t.renderTimeRow( target, interval, last_index, time += interval );
        }
        
    }
    
    // ...
    
    t.resetIndex = function( times_list ) {
        
        // Reset field names and array keys
        times_list.find('li').each(function(index) {
            var prefix = "weekly_times[" + times_list.data('day') + "][" + index + "]";
            jQuery(this).find("select").each(function() {
               this.name = this.name.replace(/weekly\_times\[\d+\]\[\d+\]/, prefix);   
            });
        });
        
        // Reset li branch indexes
        times_list.find('li').each(function(index) {
            jQuery(this).attr('data-index',index)
        });
        
    }
    
    // ...
 
    var init = function () {
       
        jQuery( "#tw-schedule label[for^=day]" ).click(function(){

            if( jQuery(this).find('input').is(':checked') ) {
                jQuery(this).addClass('active');
            } else {
                jQuery(this).removeClass('active');
            }

        });

        // ...

        jQuery('body').on('click','.add-new-time',function(e) {

            e.preventDefault();

            var template = jQuery('.time-template').html();
            var last_index = 0;
            
            if( jQuery(this).parents('.schedule-tools').next().find('li').length != 0 ) { 
                last_index = jQuery(this).parents('.schedule-tools').next().find('li').last().data('index');
                last_index++;
            }

            var hours = '';
            
            for( var i = 0; i < 24; i++ ) {
             
                var label = i;
                
                if( label < 10 )
                    label = '0' + i;
                
                hours += '<option value=' + i + '>' + label + '</option>';
                
            }
            
            var minutes = '';
            
            for( var i = 0; i < 60; i+=5 ) {
                
                var label = i;
                
                if( label < 10 )
                    label = '0' + i;
             
                minutes += '<option value=' + i + '>' + label + '</option>';
                
            }

            template = vsprintf( _TWPQueues.templates.time_row, [ 'weekly_times['+jQuery(this).data('day')+']['+last_index+'][hour]', hours, 'weekly_times['+jQuery(this).data('day')+']['+last_index+'][minute]', minutes ] );  
   
            jQuery(this).parents('.schedule-tools').next('ul').append( '<li data-index="'+last_index+'">' + template + '</li>' );
            
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
        
        jQuery('body').on('click','.generate-times span',function(e) {

            e.preventDefault();
            
            jQuery(this).parents('.generate-times').hide().prev().removeClass('active');

            var template = jQuery('.time-template').html();
            var interval = jQuery(this).data('interval');
            var hours = '';
            var minutes = '';
            
            t.renderTimeRow( jQuery(this), interval );
            
            // ...
            
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
        
        jQuery('body').on('click','.copy-times span',function(e) {

            e.preventDefault();
            
            jQuery(this).parents('.copy-times').hide().prev().removeClass('active');

            var copied_html = jQuery('#twp-times-day-' + jQuery(this).data('day')).html();
            
            jQuery(this).parents('.schedule-tools').next('ul').append( copied_html );
            
            t.resetIndex( jQuery(this).parents('.schedule-tools').next('ul') );
            
            // ...
            
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
        
        jQuery('body').on('click','.clear-times',function(e) {

            e.preventDefault();

            var times = jQuery(this).parents('.schedule-tools').next('ul'); 

            times.empty();
            
        });

        // ...

        jQuery('body').on( 'click', '.remove-time', function(e) {

            e.preventDefault();
            
            var form = jQuery(this).parents('form');
            
            jQuery(this).parent().remove();
            
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

        jQuery('div[id^="tw-queue-content-schedule"] form input,div[id^="tw-queue-content-schedule"] form select').on('change keypress', function() {
  
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
        
        jQuery('div[id^="tw-queue-content-schedule"] form').submit(function(e) {
           
            e.preventDefault();
            
            var form = jQuery(this);
            var status = form.find('.form-status');
            var data_field = form.find('.serialized-form');
            var button = form.find('input[type=submit]');
            
            status.removeClass('ok changed').addClass('saving').text('Saving your settings...');  
            TWPHelpers.showFormSaveLoader(form.parents('.tw-queue-content-inner'));
            
            jQuery.post(
                ajaxurl, 
                { 
                    action : 'twp_ajax_action',
                    admin_action: 'save_schedule', 
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
                    t.getNextTime(form.find('input[name="term_id"]').val());
                }
            );
            
            return false;
            
        });
        
        // ...
        
        jQuery('.date-from,.date-to').datepicker({ 
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            beforeShow: function(input, inst) {
               jQuery('#ui-datepicker-div').addClass('twp-ui-datepicker');
            }
        });
        
        // ...
        
        jQuery('.schedule-span[name=span_from]').datepicker({
            dateFormat: 'dd/mm/yy',
            minDate: new Date(),
            changeMonth: true,
            changeYear: true,
            onSelect: function(dateText, inst){
                inst.input.next('.schedule-span[name=span_to]').datepicker("option","minDate",inst.input.datepicker("getDate"));
                inst.input.change();
            },
            beforeShow: function(input, inst) {
                jQuery('#ui-datepicker-div').addClass('twp-ui-datepicker');
            }
        });
        
        // ...
        
        jQuery('.schedule-span[name=span_to]').datepicker({ 
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            beforeShow: function(input, inst) {
               jQuery('#ui-datepicker-div').addClass('twp-ui-datepicker');
            }
        });
        
        // ...
        
        jQuery('.schedule-type').click(function() {
            jQuery(this).parents('form').find('.schedule-type').removeClass('selected');
            jQuery(this).addClass('selected'); 
        });

        // ...
        
        jQuery('.twp-schedule-date-calendar').multiDatesPicker({
            dateFormat: 'ddmmyy',
            minDate: new Date(),
            changeMonth: true,
            changeYear: true,
            onSelect: function(dateText, inst) {  
                
                // Preventing unselection
                jQuery(this).multiDatesPicker('addDates', dateText);

                // Hide all fields
                jQuery(this).parents('form').find('.twp-schedule-date-times').hide();

                // Creating form fields
                if( t.getDateField(jQuery(this),dateText) == false ) {
                    var field = t.createDateField(inst);
                    jQuery(this).parents('form').append(field).show();
                    return;
                } else {
                    // Show relevant field
                    t.getDateField(jQuery(this),dateText).parent().show();
                }
                
            }
        });
        
        jQuery('.ui-datepicker-inline').addClass('twp-ui-datepicker');

        // ...
        
        jQuery('.twp-schedule-date-times').each(function() {
            
            var d = jQuery(this).data('date');
            
            var calendar = jQuery(this).parents('form').find('.twp-schedule-date-calendar');
            calendar.multiDatesPicker('addDates',[new Date(d*1000)]);
            
        });
        
        // ...
        
        jQuery('body').on('click','.twp-remove-date-time',function() {
           
            var calendar = jQuery(this).parents('form').find('.twp-schedule-date-calendar');
            
            calendar.multiDatesPicker('removeDates',[new Date(jQuery(this).data('date')*1000)]);
            
            var times = jQuery(this).parents('.twp-schedule-date-times');
            
            times.animate({
                opacity:0,
                top:-20
            },function() {
                if( times.next().length != 0 ) {
                    times.next().show();   
                } else if ( times.prev().length != 0 ) {
                    times.prev().show();   
                }
                times.remove();
            });
         
        });
        
    }
    
    // ...
    
    jQuery(document).ready(function() {
       init(); 
    });
    
}