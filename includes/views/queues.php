<div class="wrap tweet-wheel tw-queue-page">
 
    <div class="tw-queue-wrapper">

        <nav class="tw-queue-tabs" role="navigation">

           <ul>

            <?php

                if( ! empty( $this->queues ) ) :

                    foreach( $this->queues as $q ) :

                        $q->render_queue_tab();

                    endforeach;            

                endif;

            ?>

                <li>
                    <a href="<?php echo admin_url('/admin.php?page=twp_queues&queue=0'); ?>" <?php echo $this->requested_queue == 0 ? 'class="active"' : ''; ?>>Add New Queue</a>                 
                </li>

            </ul>
    
            <select id="twp-queue-tab-select">
            <?php

                if( ! empty( $this->queues ) ) :

                    foreach( $this->queues as $q ) :

                        echo '<option value="' . admin_url( '/admin.php?page=twp_queues&queue=' . $q->term()->term_id ) . '">' . $q->term()->name . '</option>';

                    endforeach;            

                endif;

            ?>
                <option value="new">Add New Queue</option>
            </select>

        </nav>

        <div class="twp-queue-content-wrapper">

            <?php

                if( $this->requested_queue !== 0 ) :

                    $this->requested_queue->render_queue();

                else :

            ?>

            <div id="tw-queue-new" class="tw-queue-content" <?php echo empty( $this->queues ) ? 'style="display:block"' : ''; ?>>

                <a href="<?php echo TWP_UPGRADE_LINK; ?>" target="_blank" style="display:block">
                    <img width="560" height="147" src="<?php echo TWP_PLUGIN_URL; ?>/assets/images/go-pro/unlimited-queues.png">
                </a>

            </div>
            
            <?php

                endif;

            ?>

        </div>
        
        <?php include_once 'ads.php'; ?>

    </div>

</div>