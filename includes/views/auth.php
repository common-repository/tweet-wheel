<div class="wrap tweet-wheel about-wrap auth-screen">
          
    <?php if( $this->is_authed() ) : ?>
        
        <div class="headline-feature">

            <h2><?php _e( 'Hey! You are already connected with Twitter!', TWP_TEXTDOMAIN ); ?></h2>
            <div class="return-to-dashboard">
                <a class="tw-start-button button" href="http://twp.local/wp-admin/admin.php?page=twp_queues">Start Twheelin' !</a>
            </div>

        </div>

        <?php else : ?>

        <div class="twp-row">
            <div class="twp-auth-form">
                <div class="feature-image">
                    <img style="margin:auto;display:block" src="<?php echo TWP_PLUGIN_URL ?>/assets/images/tweet-wheel-auth-pic.png">
                </div>
                <div>
                    <h3><?php _e( 'Twitter Authorization', TWP_TEXTDOMAIN ); ?></h3>
                    <p><?php _e( 'Before you can unleash the awesomeness of Tweet Wheel, you need to authorize a Twitter app.', TWP_TEXTDOMAIN ); ?></p>
                    <p style="padding:10px;background:#FCF9D9;">
                        <a href="https://nerdcow.co.uk/doc/tweet-wheel/getting-started/authorise-with-twitter/" target="_blank">Click here</a> for step-by-step guide how to obtain required values for the authorisation.
                    </p>
                    <?php if( isset( $_POST['consumer_key'] ) ) : ?>
                        <p style="padding:10px;background:#FCD2D7">We couldn't authorise using provided details. Try again!</p>
                    <?php endif; ?>
                    <form class="twp-auth-form" action="<?php echo admin_url('admin.php?page=twp_auth'); ?>" method="post">
                        <p>
                            <label>
                                Consumer Key:
                                <input style="width:400px" type="text" name="consumer_key" value="<?php echo isset( $_POST['consumer_key'] ) ? esc_attr( $_POST['consumer_key'] ) : ''; ?>">
                            </label> 
                        </p>
                        <p>
                            <label>
                                Consumer Secret:
                                <input style="width:400px" type="text" name="consumer_secret" value="<?php echo isset( $_POST['consumer_secret'] ) ? esc_attr( $_POST['consumer_secret'] ) : ''; ?>">
                            </label>   
                        </p>
                        <p>
                            <label>
                                Access Token:
                                <input style="width:400px" type="text" name="access_token" value="<?php echo isset( $_POST['access_token'] ) ? esc_attr( $_POST['access_token'] ) : ''; ?>">
                            </label>
                        </p>
                        <p>
                            <label>
                                Access Token Secret:
                                <input style="width:400px" type="text" name="access_token_secret" value="<?php echo isset( $_POST['access_token_secret'] ) ? esc_attr( $_POST['access_token_secret'] ) : ''; ?>">
                            </label>
                        </p>
                        <p>
                            <input type="submit" class="twp-button twp-button-primary" value="Authorise">
                        </p>
                    </form>
                </div>
            </div>
        </div>

        <?php endif; ?>
        
    </div>

</div>


<div class="clear"></div>