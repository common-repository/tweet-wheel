TWPTemplates = new function () {
 
    var t = this;
    
    t.characterCounter = function (raw,has_image) {
        
        // Max characters accepted for a single tweet
        maxCharacters = 280;

        // Load custom tweet text to a variable
        var tweet_template = raw;

        // ...

        if( twp_template_tags.length != 0 || typeof twp_template_tags !== 'undefined' ) {

            jQuery.each( twp_template_tags, function(k,v) {

                var regex = new RegExp( '{{'+k+'}}', 'g' );
                tweet_template = tweet_template.replace( regex, v );

            });

        }

        /**
         * Calculate a whole string length
         */
        var current_length = 0;
        current_length = tweet_template.length;

        // ...

        /**
         * Amend character limit if URL is detected (22 characters per url)
         */

        var url_chars = 23;

        // urls will be an array of URL matches
        var urls = tweet_template.match(/(?:(?:https?|ftp):\/\/)?(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/?[^\s]*)?/g);

        // If urls were found, play the max character value accordingly
        if( urls != null ) {

            for (var i = 0, il = urls.length; i < il; i++) {

                // get url length difference
                var diff = url_chars - urls[i].length;

                // apply difference
                current_length += diff;

            }

        }

        // ...

        // return actually tweet length
        return current_length;
        
    }
    
    // ...
    
    t.refreshCounters = function () {
    
        var tweet_templates = jQuery( '.tweet-template-item textarea' );

        tweet_templates.each( function( k, v ) {

            if( jQuery(this).val().length == null )
                return;

            var count = 0;

            count = t.characterCounter( jQuery(this).val(), true );

            jQuery(this).parent().find('.twp-counter').text( count );

            if( count > 280 ) {
                jQuery(this).parent().find('.twp-counter').addClass( 'too-long' );   
            } else {
                jQuery(this).parent().find('.twp-counter').removeClass( 'too-long' );
            }

        } );

    }
    
    // ...
    
    t.refreshTitle = function () {
    
        var title = jQuery('#title').val();

        if( title !== '' && title !== twp_template_tags.TITLE ) {

            // clean HTML iz
            twp_template_tags.TITLE = title.replace(/<\/?[^>]+(>|$)/g, "");
            t.refreshCounters();

        }

    }
    
    // ...
    
    t.refreshExcerpt = function () {
    
        var excerpt = jQuery('#excerpt').val();

        if( excerpt !== '' && excerpt !== twp_template_tags.EXCERPT ) {

            twp_template_tags.EXCERPT = excerpt;
            t.refreshCounters();

        }

    }
    
    // ...
    
    t.refreshPermalinks = function () {
    
        var permalink = jQuery('#sample-permalink').text();

        if( permalink !== '' && permalink !== twp_template_tags.URL && jQuery('#new-post-slug').length == '' ) {

            twp_template_tags.URL = permalink;
            t.refreshCounters();

        }

    }
    
    // ...
    
    var init = function () {
     
        jQuery( document ).on('click','.tw-remove-tweet-template',function(e){

            e.preventDefault();

            jQuery( this ).parent().remove();

        });
        
        // ...

        // Check how many templates are there
        var no_of_templates = jQuery('.tweet-template-item').length;

        // Adjust index
        var i = no_of_templates != null ? no_of_templates : 0;

        jQuery( '#add-tweet-template' ).click( function(e) {

            e.preventDefault();

            // Append a tweet template
            jQuery('.tw-tweet-templates').append( tweet_template );

            // Fix name indexing for jQuery validator plugin. It doesn't like array names with no specified index e.g. name[]
            jQuery('.tw-tweet-templates > div:last-of-type textarea').attr('name','twp_post_templates['+i+']');
            
            i++;                                                          

        } );
        
        // ...

        jQuery('.tw-learn-more').click(function(e){

            e.preventDefault();

            var el = jQuery('#' + jQuery(this).data('content') );

            el.slideToggle();

        });
        
        // ...

        // Is 280 chars
        jQuery.validator.addMethod(
            "tweetFit", 
            function(value, element) {
                return twp_character_counter( value, false ) > 280;
            }, 
            "Sorry, amigo. Maximum 280 characters."
        );

        // Has post url
        jQuery.validator.addMethod(
            "tweetURL", 
            function(value, element) {
                if( /{{URL}}/i.test(value) ) {
                    return true;
                }

                return false;
            }, 
            "Please add {{URL}} tag to your template."
        );

        // ...

        // Hook the script only to post types that are used by the plugin
        if( jQuery.inArray( typenow, _TWPAJAX.post_types ) !== -1 ) {

            // Some WP hacking to skip the bug with posts not being published (just saved as drafts)
            // more: http://wordpress.stackexchange.com/questions/119814/validating-custom-meta-boxes-with-jquery-results-in-posts-being-saved-as-draft-i
            var form = jQuery("#post");
            var send = form.find("#publish");

            send.addClass('tw-submit');

            jQuery('.tw-submit').click(function(e){

                form.validate({
                    ignore: [] // fix to validate hidden textareas
                });

                jQuery('.tweet-template-textarea').each(function(){

                    jQuery(this).rules("add",{
                        required : true,
                        tweetFit : true,
                        tweetURL : true
                    });

                });

                if(jQuery(form).valid()) {
                    jQuery("#publishing-action .spinner").show();
                    return true;
                } else {
                    jQuery("#publishing-action .spinner").hide();
                    jQuery('html, body').animate({
                        scrollTop: jQuery(".tweet-template-textarea.error").prev('div').offset().top - 30
                    }, 2000);
                }

                return false;

            });

        }

        // ...

        jQuery( '#tos-enable' ).click(function() {

            if( jQuery(this).is(':checked') ) {

                jQuery( '#tos-enabled' ).show();

                // Set cursor at the end of value
                var el = jQuery('textarea[name=twp_tos]');
                var elemLen = el.value.length;

                el.selectionStart = elemLen;
                el.selectionEnd = elemLen;
                el.focus();

            } else {

                jQuery( '#tos-enabled' ).hide();

            }

        });

        // ...

        jQuery( '#tos-template' ).change(function() {

            jQuery('textarea[name=twp_tos]').val(jQuery(this).val());
            jQuery('.tos-tweet-template-content').text(jQuery(this).val());

            jQuery.each(jQuery('#tos-template option[value=""]'),function() {
                jQuery(this).remove();
            });

            // Set cursor at the end of value
            var el = jQuery('textarea[name=twp_tos]').get(0);
            var elemLen = el.value.length;

            el.selectionStart = elemLen;
            el.selectionEnd = elemLen;
            el.focus();

            twp_refresh_counters();

        });

        // ...
        
        if( jQuery( '.tag-legend span' ).length > 0 ) {
            jQuery( '.tag-legend span' ).twpTooltip();
        }
        
        // ...
        
        jQuery('.show-all-templates').click(function(e) {
        
            e.preventDefault();

            jQuery(this).parent().find('li').not(':first-child').toggleClass('visible');

        });
        
        // ...
        
        // Update global variable holding title
        jQuery(document).on('keyup keydown','#title', function(e) {
            
            // clean from HTML
            twp_template_tags.TITLE = jQuery(this).val().replace(/<\/?[^>]+(>|$)/g, "");
            TWPTemplates.refreshCounters();

        });

        // Update global variable holding permalink
        // Update global variable holding permalink
        setTimeout(function refreshTemplateVars(){
            
            if( typeof twp_template_tags == 'undefined' )
                return;
            
            TWPTemplates.refreshTitle();           
            TWPTemplates.refreshExcerpt();          
            TWPTemplates.refreshPermalinks(); 
            setTimeout(refreshTemplateVars,3000);
        },3000);

        // Handle custom tweet text box input and update counter
        jQuery(document).on('keyup keydown','.tweet-template-content', function(e) {

            jQuery(this).parent().find('.tweet-template-textarea').val(jQuery(this).text()).keyup();
            TWPTemplates.refreshCounters();

        } );

        // Handle tweet image and update counter    
        jQuery(document).on('change','.exclude-tweet-image', function(e) {

            TWPTemplates.refreshCounters();

        } );
        
        // ...
        
        jQuery( '.twp-tooltip' ).twpTooltip();

        // ...
        
        jQuery( ".post-header .title" ).click(function() {
            jQuery(this).parent().parent().find( ".post-content" ).toggle();
        });

    }
    
    // ...
    
    jQuery(document).ready(function() {
       init(); 
    });
    
}