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

global $settings, $tweets, $tweet; ?>

<script type="text/javascript" async src="//platform.twitter.com/widgets.js"></script>

<div class="twp-widget-body">
    <ul>
        <?php 
        if( ! empty( $tweets ) ) :
            foreach( $tweets as $tweet ) :
                // Body
                if ( $overridden_template = locate_template( 'twp/widget/single.php' ) ) {
                    load_template( $overridden_template, false );
                } else {
                    load_template( TWP_PLUGIN_DIR . '/includes/views/widget/single.php', false );
                }
            endforeach;
        else :
            echo '<li>No tweets!</li>';
        endif;
        ?>
    </ul>
</div>