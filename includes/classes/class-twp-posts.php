<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Main class of TWP_Queue
 *
 * @class TWP_Queue
 */

class TWP_Posts {
    
    private $term_id;
    
	/**
	 * TWP_Posts __construct
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function __construct( $term_id ) {
    
        if( $term_id == null || is_wp_error( get_term( $term_id, 'twp_queue' ) ) )
            return;
        
        $this->term_id = $term_id;
        
    }
    
    // ...
    
    /**
     *
     */
    
    public function render_queue_post( $post, $wrapper = 'li', $allow_queuing = true ) {
  
        ?>
     
        <<?php echo $wrapper;?> class="the-queue-post" id="the-<?php echo $post->ID; ?>-post-in-<?php echo $this->term_id; ?>" data-post-id="<?php echo $post->ID; ?>">
            <div class="post-header">
                <span class="title"><?php echo get_the_title( $post->ID ); ?></span>
                <span class="drag-handler"><img src="<?php echo TWP_PLUGIN_URL; ?>/assets/images/reorder.png"/></span>
                <?php $this->post_tools( $post->ID ); ?>
            </div>
            <div class="post-content">
                <ul>
                    <?php if ( TWP()->tweet()->has_custom_templates( $post->ID ) ) : ?>

                        <?php 

                            $templates = TWP()->tweet()->get_custom_templates( $post->ID );

                            foreach( $templates as $t ) : 

                        ?>

                            <li>
                                <?php echo $t; ?>
                                <ul class="post-icons">
                                <?php 
                                    if( 
                                        TWP()->tweet()->get_tweeting_order( $post->ID ) == 'order' && 
                                        TWP()->tweet()->get_next_template( $post->ID ) == $t
                                    ) :
                                ?>
                                    <li>
                                        <span title="<?php _e( 'Next tweet\'s template', TWP_TEXTDOMAIN ); ?>" class="dashicons dashicons-clock"></span>
                                    </li>
                                <?php endif; ?>

                                <?php if( twp_compare_tweet_templates( TWP()->tweet()->get_last_tweeted_template( $post->ID ), $t ) ) : ?>
                                    <li>
                                        <span title="<?php _e( 'Recently tweeted template', TWP_TEXTDOMAIN ); ?>" class="dashicons dashicons-share"></span>
                                    </li>
                                <?php endif; ?>
                                </ul>
                            </li>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <li><?php echo TWP()->tweet()->get_default_template(); ?></li>

                    <?php endif; ?>
                </ul>
                <?php if( TWP()->tweet()->has_multiple_templates( $post->ID ) ) : ?>

                    <span class="show-all-templates dashicons dashicons-arrow-down"></span>

                <?php endif; ?>
            </div>
            <?php if( $allow_queuing && $this->term_id != null ) : ?>
                <span class="refill-bar dashicons-before dashicons-plus"></span>
                <div class="twp-clearfix">
                    <?php $this->fill_up_form( $post->ID ); ?>
                </div>
            <?php endif; ?>
        </<?php echo $wrapper;?>>

        <?php
        
    }
    
    /**
	 * Toolbar for each post in the queue
	 *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function post_tools( $post_id ) {
        
        $post_tools = array();
        
        $post_tools = apply_filters( 
            'twp_queue_post_tools', 
            array(
                array(
                    'button_label' => '<span class="dashicons dashicons-twitter"></span>' . __( 'Tweet Now', TWP_TEXTDOMAIN ),
                    'button_class' => 'tweet-now',
                    'button_attrs' => array(
                        'data-post-id=' . $post_id
                    )
                ),
                array(
                    'button_label' => '<span class="dashicons dashicons-trash"></span>' . __( 'Remove', TWP_TEXTDOMAIN ),
                    'button_class' => 'tw-dequeue',
                    'button_attrs' => array(
                        'data-post-id=' . $post_id,
                        'data-term-id=' . $this->term_id
                    ) 
                ),
                array(
                    'button_label' => '<span class="dashicons dashicons-edit"></span>' . __( 'Edit Post', TWP_TEXTDOMAIN ),
                    'button_href' => get_edit_post_link( $post_id )
                )
            ), 
            $post_tools 
        );
        
        if( ! is_array( $post_tools ) || empty( $post_tools ) )
            return;
        
        echo '<div class="queue-post-sidebar">';

            echo '<ul class="queue-post-tools">';

                foreach( $post_tools as $post ) : 

                    $post = wp_parse_args( $post, array(
                        'button_id' => '',
                        'button_class' => '',
                        'button_href' => '#',
                        'button_label' => 'Button!',
                        'button_attrs' => array()
                    ) );

                    extract( $post );

                    ?>

                    <li><a id="<?php echo $button_id; ?>" class="<?php echo $button_class; ?>" href="<?php echo $button_href; ?>" <?php echo implode( ' ', $button_attrs ); ?>><?php echo $button_label; ?></a></li>

                    <?php

                endforeach;

            echo '</ul>';

            echo '<ul class="queue-icons">';

                if( TWP()->tweet()->has_custom_templates( $post_id ) )  
                    echo '<li><span title="' . __( 'Custom template', TWP_TEXTDOMAIN ) . '" class="dashicons dashicons-admin-tools"></span></li>';

                if( TWP()->tweet()->has_multiple_templates( $post_id ) )  
                    echo '<li><span title="' . __( 'Multiple templates', TWP_TEXTDOMAIN ) . '" class="dashicons dashicons-screenoptions"></span></li>';

                if( TWP()->tweet()->get_tweeting_order( $post_id ) == 'random' )
                    echo '<li><span title="' . __( 'Random order', TWP_TEXTDOMAIN ) . '" class="dashicons dashicons-randomize"></span></li>';

                if( TWP()->tweet()->has_image( $post_id ) )  
                    echo '<li><span title="' . __( 'Attached image', TWP_TEXTDOMAIN ) . '" class="dashicons dashicons-format-image"></span></li>';

            echo '</ul>';

        echo '</div>';
        
    }
    
    // ...
    
	/**
	 * Displays the queue of posts
	 *
	 * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function display_tab() {
        
        $in_queue = $this->get_queued_posts();

        ?>
        
        <div class="the-queue" data-term-id="<?php echo $this->term_id; ?>">
          
            <div class="form-top">
                <input type="submit" class="twp-button twp-activate-simple-view" value="Compact View">
                <input type="submit" class="twp-button twp-empty-queue" value="Empty Queue" data-term-id="<?php echo $this->term_id; ?>">
                <span class="form-status"></span>
            </div>
           
            <span class="refill-bar refill-bar-top dashicons-before <?php echo $this->has_queue_posts() ? 'dashicons-plus' : 'dashicons-minus opened'; ?>"></span>
            <div class="fill-up-top <?php echo $this->has_queue_posts() ? '' : 'fill-up-visible'; ?> twp-clearfix">
                <?php $this->fill_up_form( 0 ); ?>
            </div>
            <ul>
            <?php if( ! empty( $in_queue ) ): ?>

                <?php foreach( $in_queue as $q ) : ?>
                    <?php $this->render_queue_post( $q, 'li', true, $this->term_id ); ?>
                <?php endforeach; ?>
            
            <?php endif; ?>
            </ul>
        </div>
        
        <?php
        
    }
    
    // ...
    
     /**
     * Retrieves all queued posts
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     * @update 2.0 (28.08.2015)
     *
     * @param n/a
	 * @return array | boolean
     */
    
    public function get_queued_posts( $order = 'ASC', $limit = -1 ) {
        
        global $wpdb;
    
        $posts = get_posts(
            array(
                'posts_per_page' => $limit,
                'post_type' => twp_get_all_enabled_post_types(),
                'tax_query' => array(
                    array(
                        'taxonomy' => 'twp_queue',
                        'field' => 'id',
                        'terms' => $this->term_id
                    )
                ),
                'orderby' => 'meta_value_num',
                'meta_key' => 'twp_term_' . $this->term_id . '_order',
                'order' => $order
            )
        );
        
        if( $posts )
            return $posts;

        return false;
        
    }
    
    // ...
    
    // ...
    
    /**
     * Retrieve an item from bottom of the queue
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return array
     */
    
    public function get_last_queued_post() {
        
        global $wpdb;

        if( $this->has_queue_posts() == false )
            return 0;
        
        $item = $this->get_queued_posts( 'DESC', 1 );
        
        if( ! $item )
            return 0;
        
        return $item;

    }
    
    // ...
    
    /**
     * Wrapper to get just an ID of last queued item
     *
     * @type function
     * @date 02/09/2015
	 * @since 2.0
     *
     * @param n/a
	 * @return array
     */
    
    public function get_last_queued_post_id() {
        
        global $wpdb;

        if( $this->get_last_queued_post() != 0 ) :
        
            $item = $this->get_last_queued_post();
        
            return $item[0]->ID;
        
        endif;
        
        return 0;

    }
    
    // ...
    
    /**
     * Retrieve an item from top of the queue
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return array
     */
    
    public function get_first_queued_post() {
        
        global $wpdb;
        
        if( $this->has_queue_posts() == false )
            return 0;
        
        $item = $this->get_queued_posts( 'ASC', 1 );
        
        if( ! $item )
            return 0;
        
        return $item;
        
    }
    
    // ...
 
    /**
     * Checks if queue has posts in it
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return boolean
     **/
        
    public function has_queue_posts() {

        $posts = $this->get_queued_posts();
        
        if( ! is_array( $posts ) )
            return false;

        if( empty( $posts ) )
            return false;
        
        return true;
        
    }
    
    // ...
    
    /**
     * Checks if given post is queued
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param int
	 * @return boolean
     */
    
    public function is_post_queued( $post_id ) {
        
        global $wpdb;
        
        $terms = wp_get_post_terms( $post_id, 'twp_queue', array( 'fields' => 'ids' ) );
        
        if( ! empty( $terms ) )
            return $terms;
        
        return false;
        
    }
    
    // ...

    public function fill_up_form( $post_id ) {
        
        ?>
        
        <div class="fill-up-box">
        
            <ul class="fill-up-options">
                <li><span class="fill-up-search active">Search</span></li>
                <li><span class="fill-up-bulk">Bulk</span></li>
            </ul>
            
            <div class="fill-up-option-content fill-up-search-content" style="display:block">
                
                <input type="text" class="fill-up-search-input regular-text" style="width:100%;" placeholder="Enter any keyword to search" data-term-id="<?php echo $this->term_id; ?>" data-insert-after="<?php echo $post_id; ?>">
                
                <div style="display:none;" class="fill-up-search-results"></div>
                
            </div>
            
            <div class="fill-up-option-content fill-up-bulk-content" style="display:none">
                
                <form class="fill-up-form" method="post" action="#">
                    
                    <input class="term-id-hidden" type="hidden" name="fillup[term_id]" value="<?php echo $this->term_id; ?>">

                    <?php wp_nonce_field( 'fill_up_queue', 'fill_up_queue_nonce' ); ?>

                    <?php

                        $post_type = twp_get_all_enabled_post_types();

                        if( ! empty( $post_type ) ) :

                            echo '<h4 style="margin:0px;margin-bottom:5px;">Choose which posts to import here...</h4>';

                            foreach( $post_type as $pt ) :

                                ?>

                                <div class="fill-up-pt" data-pt="<?php echo $pt; ?>">

                                    <div class="row">

                                        <input type="checkbox" name="fillup[<?php echo $pt; ?>][included]" value="1">&nbsp;
                                        <input type="text" class="regular-text max-posts" name="fillup[<?php echo $pt; ?>][number]" placeholder="all">&nbsp;<strong><?php echo $pt; ?>s</strong>&nbsp;from&nbsp; 
                                        <input type="text" value="" name="fillup[<?php echo $pt; ?>][from]" class="date-from" placeholder="beginning">
                                        &nbsp;to&nbsp;
                                        <input type="text" value="" name="fillup[<?php echo $pt; ?>][to]" class="date-to" placeholder="now">&nbsp;
                                        <span class="fill-counter <?php echo $pt; ?>-count"></span>

                                    </div>

                                </div>

                                <?php

                            endforeach;

                            echo '<input type="submit" class="twp-button twp-button-primary tw-fill-up" value="' . __( 'Go!', TWP_TEXTDOMAIN ) . '">'; ?>

                            <span style="font-size:10px;position:relative;top:4px;left:4px"><?php _e( 'Please note posts that are already in the queue will be omitted.', TWP_TEXTDOMAIN ); ?></span>

                            <?php

                        else :

                            _e( 'Bummer! You need to allow some post types into the queue! Head off to the <a href="' . admin_url( '/admin.php?page=twp_settings' ) . '">settings page</a> to fix it.', TWP_TEXTDOMAIN );

                        endif;

                    ?>

                    <input type="hidden" name="term_id" value="<?php echo $this->term_id; ?>">
                    <input type="hidden" name="insert_after" value="<?php echo $post_id; ?>">

                </form>
                
            </div>
            
        </div>

        <?php       
        
    }
    
    /**
	 * Fills up the queue with given data
     * 
	 * @type function
     * @date 02/09/2015
	 * @since 2.0
     *
     * @param array | int | boolean/int
	 * @return n/a
	 */
    
    public function fill_up_by_query( $args, $insert_after = 0 ) {
        
        $merged_queue = array();
        
        // ...
        
        $posts = get_posts( $args );
        
        if( ! $posts )
            return false;

        if( $this->has_queue_posts() ) :
        
            // ... Get queued posts and parse the format
            $queue = $this->get_queued_posts();

            foreach( $queue as $q ) :

                $queue_order = get_post_meta( $q->ID, 'twp_term_' . $this->term_id . '_order', true );

                $merged_queue[$queue_order] = $q->ID;

            endforeach; 
        
        endif;
        
        if( $insert_after != 0 ) :
        
            // ... Add new posts to the term & rearrange array

            $insert_after_key = array_search( $insert_after, $merged_queue );

            $insert_after_key = $insert_after_key === false ? $insert_after : $insert_after_key;

            $insert_after_key++;
        
        endif;
        
        foreach( $posts as $p ) :
        
            wp_set_post_terms( $p->ID, array( (int) $this->term_id ), 'twp_queue', true );
    
            twp_array_insert( $merged_queue, ( $insert_after == 0 ? 0 : $insert_after_key ), $p->ID );
        
        endforeach;

        // ... Save final order
        
        foreach( $merged_queue as $k => $v ) :
        
            update_post_meta( $v, 'twp_term_' . $this->term_id . '_order', $k );
        
        endforeach;
        
        // ...        
        
        return $posts;
        
    }
    
    // ...
    
	/**
	 * Inserts a post to the queue. Performs checks for duplication and exclusion. 
     * The check be skipped giving "true" as a value for last two parameters.
	 *
	 * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param int | int | bool
	 * @return WP Insert | false
	 */
    
    public function insert_post( $post_id, $insert_after = 0 ) {
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        
        global $wpdb;
        
        $args = array(
            'post__in' => array( $post_id ),
            'post_type' => twp_get_all_enabled_post_types()
        );

        return $this->fill_up_by_query( $args, $insert_after );
        
    }
    
    // ...
    
	/**
	 * Removes post from the queue
	 *
	 * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function remove_post( $post_id ) {
        
        global $wpdb;

        $term = get_term( $this->term_id, 'twp_queue' );
        
        if( $term != null ) :
            
            $term = array( (int) $term->term_id );
        
        else :
        
            $terms = get_terms(
                'twp_queue',
                array(
                    'hide_empty' => false    
                )
            );
        
            if( empty( $terms ) )
                return false;
        
            foreach( $terms as $t ) :

                $term[] = (int) $t->term_id;
        
            endforeach;

        endif;
        
        $result = wp_remove_object_terms( $post_id, $term, 'twp_queue' );
        
        return $result;
        
    }
    
    // ...
    
	/**
	 * Removes post meta related to the queue
	 *
	 * @type function
     * @date 09/10/2015
	 * @since 2.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public static function delete_all_settings( $term_id ) {
        
        global $wpdb;
        
        $wpdb->query( "DELETE FROM " . $wpdb->prefix . "postmeta WHERE meta_key = 'twp_term_" . $term_id . "_order'" );
        
    }
    
    // ...
    
	/**
	 * Removes all posts from the queue
	 *
	 * @type function
     * @date 10/10/2015
	 * @since 2.0
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function remove_all() {
        
        $posts = $this->get_queued_posts();
        
        if( ! $posts )
            return false;

        foreach( $posts as $post ) :
        
            $this->remove_post( $post->ID );
        
        endforeach;
        
        return false;
        
    }
    
}