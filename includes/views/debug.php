<div class="wrap tweet-wheel tw-debug-page">
    <h2><img class="alignleft" style="margin-right:10px;" src="<?php echo TWP_PLUGIN_URL . '/assets/images/tweet-wheel-page-icon.png'; ?>"> <?php _e( 'Health Check', TWP_TEXTDOMAIN ); ?></h2>

   <table class="tw-report-table widefat" style="margin-top:20px;" cellspacing="0">

       <?php

       foreach( $this->health_check() as $c ) :

           ?>

           <thead>

               <tr>

                   <th colspan="2"><?php echo $c[0]; ?></th>

               </tr>

           </thead>

           <tbody>

               <?php foreach( $c[1] as $check ) : ?>

                   <tr>
                       <td><?php echo $check[0]; ?></td>
                       <td><?php echo $check[1]; ?></td>
                   </tr>

               <?php endforeach; ?>

           </tbody>

           <?php

       endforeach;

       ?>

   </table>

</div>