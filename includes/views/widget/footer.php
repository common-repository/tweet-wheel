<?php 

/**
 * $settings is an array and contains following indexes
    array(
        'number_of_tweets',
        'skip_keywords',
        'skip_replies',
        'skip_rts',
        'theme',
        'title',
        'title_icon',
        'title_link',
        'profile_link',
        'follow_button',
        'owner_thumbnail',
        'others_thumbnail',
        'links_clickable',
        'time',
        'time_format',
        'time_linked',
        'screen_name'
    )
 */

global $settings; ?>

<div class="twp-widget-footer">
    
    <?php if( $settings['follow_button'] ) : ?>
        <a href="https://twitter.com/<?php echo $settings['screen_name']; ?>" class="twitter-follow-button" data-show-count="false">Follow @<?php echo $settings['screen_name']; ?></a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script> 
    <?php endif; ?>  
    
</div>