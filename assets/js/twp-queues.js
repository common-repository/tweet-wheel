var TWPQueues = new function () {
    
    "use strict";
    
    var t = this;
    
    /***********************************
     * The Queues Screen
     ***********************************/
    
    /**
     * Add / Delete / Save Queues
     */
    
    // ...
    
    t.saveQueue = function (obj) {
     
        if( obj == null )
        return;
    
        if( obj.find('li').length == 0 )
            return;
        
        obj.sortable();
        
        var status = obj.parent().find('.form-status');
        status.removeClass('ok changed').addClass('saving').text('Saving queue...');  
        TWPHelpers.showFormSaveLoader(obj.parents('.tw-queue-content-inner'));

        jQuery.post( 
            ajaxurl, 
            { 
                action : 'twp_ajax_action', 
                admin_action : 'save_queue',
                nonce : _TWPAJAX.twNonce,
                data : {
                    elements: obj.sortable('toArray',{attribute:'data-post-id'}),
                    term_id: obj.parent().data('term-id')
                }
            },
            function(response) {
                status.removeClass('saving').addClass('ok').text('Queue has been saved');  
                TWPHelpers.hideFormSaveLoader(obj.parents('.tw-queue-content-inner'));
            }
        ); 
        
    }
    
    /**
     * Queue Content Management
     */
    
    t.fillUpQueue = function (serialized) {

        jQuery.post( 
            ajaxurl, 
            { 
                action: 'twp_ajax_action',
                admin_action: 'fill_up_queue', 
                data : {
                    data:serialized   
                },
                nonce: _TWPAJAX.twNonce
            }, 
            function( response ) {
                
                var data = jQuery.parseJSON( response );
                
                if( data.status == "OK" ) {

                    var term_id = data.response.term_id;
                    var insert_after = data.response.insert_after;
                    var ids = data.response.ids;
                    
                    jQuery.each(ids,function(k,v) {
                       t.renderQueuePost({post_id:v,term_id:term_id,insert_after:insert_after}); 
                    });
                }

            } 
        );   
      
    };
    
    // ...
    
    t.addToQueue = function (obj) {
        
        if( obj.post_id === null || obj.term_id === null )
            return;

        if( obj.insert_after == null )
            obj.insert_after = false;

        jQuery.post( 
            ajaxurl, 
            { 
                action:'twp_ajax_action',
                admin_action: 'add_to_queue',
                data : {
                    post_id : obj.post_id,
                    term_id : obj.term_id,
                    insert_after : obj.insert_after
                },            
                nonce: _TWPAJAX.twNonce
            }
        );

        t.renderQueuePost(obj);

    };
    
    // ...
    
    t.renderQueuePost = function(obj) {
        
        jQuery.post(
            ajaxurl,
            {
                action: 'twp_ajax_action',
                admin_action:'render_queue_post',
                data : {
                    post_id:obj.post_id,
                    term_id:obj.term_id
                },
                nonce: _TWPAJAX.twNonce
            },
            function(response) {
                
                if( obj.insert_after == 0 ) {
                    jQuery('.the-queue[data-term-id=' + obj.term_id + ']').find( '>ul' ).prepend(response);
                } else {
                    jQuery('.the-queue-post[data-post-id=' + obj.insert_after + ']').after(response);
                }
                
            }
        );
        
    }
    
    // ...
    
    t.removeFromQueue = function (obj) {
    
        if( obj.post_id === null || obj.term_id === null )
            return;

        jQuery.post( 
            ajaxurl, 
            { 
                action:'twp_ajax_action',
                admin_action: 'remove_from_queue',
                data : {
                    post_id : obj.post_id,
                    term_id : obj.term_id
                },            
                nonce: _TWPAJAX.twNonce
            }
        );
        
    }
    
    // ...
    
    t.searchContent = function( input, results ) {
        
        var s = input.val();
        var insert_after = input.data('insert-after');

        if( s.length < 3 ) {
            results.fadeOut().empty();
            return;
        }

        results.html('<p>Loading...</p>');

        jQuery.post(
            ajaxurl,
            {
                action: 'twp_ajax_action',
                admin_action: 'search_content',
                data: {
                    search: s,
                    term_id : input.data('term-id')
                },
                nonce: _TWPAJAX.twNonce
            },
            function(response) {
                
                var data = jQuery.parseJSON( response );

                if( data.status == 'OK' ) {

                    results.empty().fadeIn();

                    jQuery.each( data.response, function(k,v) {

                        results.append( '<h4>' + v.label + '</h4>' );

                        jQuery.each( v.posts, function(o,p) {

                            results.append( '<span data-post-id="' + o + '" data-term-id="' + input.data('term-id') + '" data-insert-after="' + insert_after + '">' + p + '</span>' );

                        });

                    } );

                } else {

                    results.empty().fadeIn().html('<p>' + data.response + '</p>');                

                }

            }
        );
        
    }
    
    // ...
    
    t.changeQueueStatus = function () {
     
        jQuery.post( 
            ajaxurl, 
            { 
                action: 'twp_ajax_action',
                admin_action: 'change_queue_status',
                nonce: _TWPAJAX.twNonce
            }
        ); 
        
    }
    
    // ..
    
    
    
    /* ============================== */
    /* ============================== */

    var switchRefillBoxTabs = function () {
    
        jQuery('body').on('click','.fill-up-options li span',function() {

            var container = jQuery(this).parents('.fill-up-box');

            if( jQuery(this).hasClass( 'fill-up-search' ) ) {
                jQuery(this).addClass('active');
                container.find('.fill-up-search-content').show();
                container.find('.fill-up-bulk').removeClass('active');
                container.find('.fill-up-bulk-content').hide();
            }

            if( jQuery(this).hasClass( 'fill-up-bulk' ) ) {
                jQuery(this).addClass('active');
                container.find('.fill-up-bulk-content').show();
                container.find('.fill-up-search').removeClass('active');
                container.find('.fill-up-search-content').hide();
            }

        });
        
    };
    
    // ...
    
    var showRefillBox = function () {
    
        jQuery('body').on('click','.refill-bar',function() {

            var bar = jQuery(this);
            var form = bar.next().find('.fill-up-box');

            bar.removeClass('dashicons-plus').addClass('dashicons-minus opened');

            form.slideToggle(400,function(){

                if( form.css('display') == 'none' ) {
                    bar.removeClass('dashicons-minus opened').addClass('dashicons-plus');
                } else {
                    bar.removeClass('dashicons-plus').addClass('dashicons-minus opened');
                }

            });

        });
        
    };
    
    // ...
    
    var showToolTip = function () {
     
        // The post row actions
        jQuery('.manage-queue-post').twpTooltip({
            admin_action:'get_queues_list'
        });
        
    }
    
    // ...
    
    var init = function () {
    
        // ...
        
        jQuery('body').on( 'click', '.fill-up-search-results span', function() {
            
            t.addToQueue({
                post_id:jQuery(this).data('post-id'),
                term_id:jQuery(this).data('term-id'),
                insert_after:jQuery(this).data('insert-after')
            });
            
            jQuery(this).remove();
            
        });
        
        // ...
        
        var timer = 0;

        jQuery('body').on( 'keyup', '.fill-up-search-input', function() {

            if (timer) {
                clearTimeout(timer);
            }
            
            var $el = jQuery(this);
            var $res = jQuery(this).parents('.fill-up-box').find('.fill-up-search-results');
            
            timer = setTimeout(function() {
                t.searchContent( $el, $res );
            }, 400);

        });
        
        // ...
        
        jQuery( ".the-queue > ul" ).sortable({
            handle : '.drag-handler',
            update : function() {
                
                var $this = jQuery(this);
                t.saveQueue($this);
                
            }
        });
        
        // ...
        
        jQuery('#add-new-queue').click( function() {

            jQuery(this).addClass('clicked');
            jQuery('#new-queue-form').show();

        });
        
        // ...
        
        jQuery('body').on('submit','.fill-up-form',function(e){

            e.preventDefault();

            var data = jQuery(this).serialize();

            t.fillUpQueue( data );
            
            return false;

        });
        
        // ...
        
        jQuery(document).on('click','.tw-queue-post',function(e){
       
            e.preventDefault();

            var el = jQuery(this);

            if( el.hasClass( 'in-queue' ) ) {

                t.removeFromQueue({post_id:el.data('post-id'), term_id:el.data('term-id')});
                el.removeClass('in-queue').addClass('not-in-queue');

            } else {

                t.addToQueue({post_id:el.data('post-id'), term_id:el.data('term-id')});
                el.removeClass('not-in-queue').addClass('in-queue');

            }

        });
        
        // ...
        
        // Keep post row actions visible when tooltip showed
        jQuery('.manage-queue-post').click(function() {

            jQuery(this).parents('.row-actions').css('visibility','visible');

        });

        // ...

        // Restore post row actions visibility when tooltip is hidden
        jQuery(document).on('mouseleave','.twp-tooltip',function(){

            jQuery('body').find('.row-actions').css('visibility','');

        });
        
        // ...
        
        jQuery('body').on('click','.tweet-now',function(e){
       
            e.preventDefault();

            var el = jQuery(this);

            el.text('Tweeting...');

            jQuery.post( 
                ajaxurl, 
                { 
                    action : 'twp_ajax_action',
                    admin_action: 'tweet', 
                    data : {
                        post_id : el.data('post-id'),
                        term_id : el.parents('.the-queue').data('term-id')
                    },
                    nonce: _TWPAJAX.twNonce
                }, 
                function( response ) {
                    
                    var data = jQuery.parseJSON( response );

                    if( data.response == "error" ) {
                        
                        el.parents('.the-queue-post').animate({backgroundColor:'red'}, 300).animate({backgroundColor:'#fff'}, 300);

                        el.text('Tweet Now');

                        alert( 'Twitter did not accept your tweet. Here is the reason: ' + data.message );

                    } else {
                        el.parents('.the-queue-post').slideUp().remove();
                    }

                } 
            );

        });
        
        // ...
        
        jQuery(document).on('click','.tw-dequeue-post',function(e){
       
            e.preventDefault();

            var el = jQuery(this);

            el.text('Dequeuing...');

            jQuery.post( 
                ajaxurl, 
                { 
                    action: 'remove_from_queue', 
                    post_id : el.data('post-id'),
                    term_id : el.data('term-id'),
                    nonce: _TWPAJAX.twNonce
                }, 
                function( response ) {

                    var data = jQuery.parseJSON( response );

                    if( data.response == "error" ) {

                        el.replaceWith('<a href="#" style="color:#a00" class="tw-dequeue-post" data-post-id="'+el.data('post-id')+'">Dequeue</a>');

                        alert( 'We couldn\'t remove your tweet... Not sure why. Try excluding it in the post edit screen.' );

                    } else {

                        el.replaceWith('<a href="#" class="tw-queue-post" data-post-id="'+el.data('post-id')+'">Queue</a>');

                    }

                } 
            );

        });

        // ...
        
        jQuery('body').on('click','.tw-dequeue',function(e){

            e.preventDefault();

            var el = jQuery(this);

            el.text('Removing...');

            jQuery.post( 
                ajaxurl, 
                { 
                    action : 'twp_ajax_action',
                    admin_action: 'remove_from_queue', 
                    data : {
                        post_id : el.data('post-id'),
                        term_id : el.data('term-id')
                    },
                    nonce: _TWPAJAX.twNonce
                },
                function( response ) {

                    var data = jQuery.parseJSON( response );

                    if( data.response == "error" ) {

                        jQuery('#'+el.data('post-id')).animate({backgroundColor:'red'}, 300).animate({backgroundColor:'#fff'}, 300);

                        el.text('Remove');

                        alert( 'We couldn\'t remove your tweet... Not sure why. Try excluding it in the post edit screen.' );

                    } else {

                        jQuery('#the-'+el.data('post-id')+'-post-in-'+el.data('term-id')).css( 'background', '#00AB2B' ).slideUp().remove();

                    }

                } 
            );

        });
        
        // ...
        
        jQuery('.fill-up-pt input').bind( 'blur change', function(e){
        
            var el = jQuery(this);
            var wrapper = el.parents('.fill-up-pt');
            var brap = {
                'post_type' : wrapper.data('pt'),
                'number' : wrapper.find('.max-posts').val(),
                'date_from' : wrapper.find('.date-from').val(),
                'date_to' : wrapper.find('.date-to').val(),
                'term_id' : el.parents('.fill-up-form').find('.term-id-hidden').val()
            }
            
            if( wrapper.find('input[type=checkbox]').is(':checked') == false ) {

                wrapper.find( '.' + wrapper.data('pt') + '-count' ).html( '= 0 posts' );
                return false;

            }

            wrapper.find( '.' + wrapper.data('pt') + '-count' ).html( '<img width="20" height="20" src="/wp-admin/images/spinner-2x.gif">' );

            jQuery.post( 
                ajaxurl, 
                { 
                    action: 'twp_ajax_action',
                    admin_action: 'found_posts', 
                    data: {
                        args : brap
                    },
                    nonce: _TWPAJAX.twNonce
                },
                function( response ) {

                    var data = jQuery.parseJSON( response );
                    var count = 0;
     
                    if( data.response != "error" ) {
                        count = data.data;
                    }

                    wrapper.find( '.' + wrapper.data('pt') + '-count' ).html( '= ' + count + ' post' + ( count != 1 ? 's' : '' ) );

                } 
            );

        });
        
        // ...
        
        jQuery('.twp-activate-simple-view').click(function(e){
        
            e.preventDefault();

            jQuery(this).toggleClass('twp-button-primary');
            jQuery(this).parents('.the-queue').find('> ul').toggleClass('simple');

        });
        
        // ...
        
        jQuery('.twp-empty-queue').click(function(e) {
           
            e.preventDefault();
            
            if(confirm('Are you sure?')) {
          
                var status = jQuery(this).next('.form-status');
                var obj = jQuery(this);
                status.removeClass('ok changed').addClass('saving').text('Emptying queue...');  
                TWPHelpers.showFormSaveLoader(jQuery(this).parents('.tw-queue-content-inner'));

                jQuery.post(
                    ajaxurl, 
                    { 
                        action: 'twp_ajax_action',
                        admin_action: 'empty_queue', 
                        data: {
                            term_id : obj.data('term-id')
                        },
                        nonce: _TWPAJAX.twNonce
                    },
                    function( response ) {

                        obj.parents('.the-queue').find('>ul').empty();

                        status.removeClass('saving').addClass('ok').text('Done.');  
                        TWPHelpers.hideFormSaveLoader(obj.parents('.tw-queue-content-inner'));

                    } 
                );
                
            }
            
        });

        // ...
        
        switchRefillBoxTabs();
        showRefillBox();
        showToolTip();
        
    }
    
    // ...
    
    jQuery(document).ready(function() {
        init(); 
    });
    
}