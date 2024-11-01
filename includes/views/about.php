<div class="wrap tweet-wheel intro-page about-wrap">
            
    <h1><?php _e( 'Welcome to Tweet Wheel ' . TWP()->version . '!', TWP_TEXTDOMAIN ); ?></h1>

    <div class="about-text">
        <?php printf( __( 'Re-thought. Re-written. Still loved. Even more features at your disposal.', TWP_TEXTDOMAIN ), TWP_VERSION ); ?>
    </div>

    <div class="twp-badge"><?php _e( 'Version', TWP_TEXTDOMAIN ); ?> <?php echo TWP()->version; ?></div>
    
    <?php 

    $tab = isset( $_GET['tab'] ) ? esc_attr( $_GET['tab'] ) : '';

    ?>
    
    <h2 class="nav-tab-wrapper">
        <a href="<?php echo admin_url('admin.php?page=twp_about'); ?>" class="nav-tab <?php echo $tab != 'support' && $tab != 'subscribe' ? 'nav-tab-active' : ''; ?>">What’s New</a>
        <a href="<?php echo admin_url('admin.php?page=twp_about&tab=subscribe'); ?>" class="nav-tab <?php echo $tab == 'subscribe' ? 'nav-tab-active' : ''; ?>">Subscribe</a>
        <a href="<?php echo admin_url('admin.php?page=twp_about&tab=support'); ?>" class="nav-tab <?php echo $tab == 'support' ? 'nav-tab-active' : ''; ?>">Support</a>
        <a href="https://wordpress.org/support/view/plugin-reviews/tweet-wheel" class="nav-tab" target="_blank">Rate the plugin <span class="dashicons dashicons-external"></span></a>
        <a href="http://tweet-wheel.com/affiliates/" class="nav-tab" target="_blank">Earn money <span class="dashicons dashicons-external"></span></a>
    </h2>
    
    <div class="twp-spacer"></div>
    
    <?php

    switch( $tab ) :

        case 'subscribe':
        include 'about-subscribe.php';
        break;

        case 'support':
        include 'about-support.php';
        break;

        default:
        include 'about-whatsnew.php';
        break;

    endswitch;    

    ?>

</div>