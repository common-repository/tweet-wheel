<?php 

/**
 * $settings is an array and contains following indexes
    array(
        'number_of_tweets',
        'skip_keywords',
        'skip_replies',
        'skip_rts',
        'skip_mentions',
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
 *
 * $tweet equals the response as per documentation https://dev.twitter.com/rest/reference/get/statuses/user_timeline
 */

global $settings,$tweet; ?>

<li class="twp-tweet-container">
    <img class="twp-profile-pic" src="<?php echo $tweet->user->profile_image_url; ?>">
    <div class="twp-tweet-content">
        <div class="twp-tweet-meta">
            <?php if( $settings['profile_link'] ) : ?>
                <a href="https://twitter.com/<?php echo $settings['screen_name']; ?>" target="_blank">
            <?php endif; ?>
                    <cite>@<?php echo $settings['screen_name']; ?></cite>
            <?php if( $settings['profile_link'] ) : ?>
                </a>
            <?php endif; ?>

            <?php if( $settings['time_format'] ) : ?>
                <?php if( $settings['time_linked'] ) : ?>
                    <a href="https://twitter.com/<?php echo $tweet->user->screen_name; ?>/status/<?php echo $tweet->id; ?>" target="_blank">
                    <?php endif; ?>
                        <time><?php echo date( $settings['time_format'], strtotime( $tweet->created_at ) ); ?></time>  
                    <?php if( $settings['time_linked'] ) : ?>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <p class="twp-tweet-text">
            <?php 
            if( $settings['links_clickable'] ) :
                echo TWP_Widget::parse_tweet( $tweet->text );
            else :
                echo $tweet->text; 
            endif;
            ?>
        </p>
        <div class="twp-tweet-actions">
            <a class="twp-reply" href="https://twitter.com/intent/tweet?in_reply_to=<?php echo $tweet->id; ?>">
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAA3klEQVQ4T6WT0RGCMBBEd1MBJWgFageWgBU4SQV2oC34D4lWYAvaAVagJWABTJxzBGNkFDCfF/bl9m4hWo5zLtFal213cY1xwVq7A5AYY9LegKd4CeBkjJn3AgRi0RUAVjWA5E1rLbWP87AQib89XJAUi/t6RuwhDsFXkgvpaihAYCXJcV8Lsb1ts8bQivf+rJRqhlhVVUJyCiAlOQkoxVsOuqwxy7JUKXVoNjQkSHmeb0iuRfuRRCn+irJzbuS9v4jVVkCXBFprPYDXELuIwm8EQHL2TwdH+V8GA+pu7kfMWuGHU0+1AAAAAElFTkSuQmCC"/>
            <a class="twp-retweet" href="https://twitter.com/intent/retweet?tweet_id=<?php echo $tweet->id; ?>">
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAA4ElEQVQ4T62T0RWCMAxF8zZwEx1BJ5GQAXQDdQMXaIubMAJu4AhOQDxRiqVw+BDz2bQ37yUpaGFg4Xv6L8A5V4hIFVV5769EtM5VAmBmfth5r6C7fFDVSkTYkiGEVdu2NYABRFUvInLuAVYZQIiVcggRbSynqgUR7QeA/HEC4dSOnTvnzgBOA0AIYZvSiegG4N0HZq5T/5OAeGEqGXNWRD9hVguzGIv0TZwDzNmcAnzpwJ2Zn6bEe2+29nmPRoBs5g2AXQ4ZjbGb+UZVbXHyaMqyPCa9Gizbf1f5l4+1WMELQD2gYZzrJQAAAAAASUVORK5CYII="/></a>
</a>
            <a class="twp-favorite" href="https://twitter.com/intent/favorite?tweet_id=<?php echo $tweet->id; ?>">
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABI0lEQVQ4T52T/VECMRDF87YBLQEqUCtQO6AD2dCAHYgdUABJoAKhA0rQCjwrkArynL25mwlwH4P7393s/vLy8hZuoEIIcxGpVPXQ14YRQAWA3vvp1QA7HUCyQQDPfSp6FcQYTfZjc/Leez/rUtEJSClNSH6XAwCmqlqdQ5BSuid545ybAJhYA8m5fZ81VyQPZmrxf48QwhGAAa4ukl+1gpzzBsDdNQQbFpGn2oOU0i3JXWHaGGsL4FVVjycmxhg3zrmXkemt9948qusE0Cj5HQIAeFDVz07Aer2eicjHEICkLhYLU3qpIISwBPD27yuU6SP5IyJ21yPJVWFwVe7GuYlsgvQuIitzuVVju+GcW1lmylRevAKAZVdk2+fOOduKb1r4H3yyh7Jr1VvyAAAAAElFTkSuQmCC"/></a>
        </div>
    </div>
</li>