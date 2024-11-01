<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Main class of TWP_Schedule
 *
 * @class TWP_Schedule
 * @since 1.0
 */

class TWP_Schedule {

    private $term_id,
            $type,
            $weekly_times,
            $span_from,
            $span_to;
    
    // ...
    
	/**
	 * TWP_Schedule __construct
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
        
        $this->type = get_term_meta( $this->term_id, 'twp_schedule_type', true );
        $this->weekly_times = get_term_meta( $this->term_id, 'twp_schedule_weekly_times', true );
        $this->span_from = get_term_meta( $this->term_id, 'twp_schedule_span_from', true );
        $this->span_to = get_term_meta( $this->term_id, 'twp_schedule_span_to', true );

    }
          
    // ...
    
    /**
     * An actual tweet times field with form fields
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return n/a
     */
    
    public function display_tab() {

        ?>

        <div class="times-wrapper">

            <form method="post" class="twp-schedule-form">
            
                <input id="twp-schedule-form-data-<?php echo $this->term_id; ?>" class="serialized-form" type="hidden" value="0">
                
                <input type="hidden" name="term_id" value="<?php echo $this->term_id; ?>">
            
                <div class="form-top"><input type="submit" class="twp-button twp-save-queue-settings" value="Save Schedule"><span class="form-status"></span></div>
                
                <?php do_action( 'twp_screen_notice', $this->term_id, 'schedule' ); ?>
                
                <label class="schedule-type <?php echo $this->type == 'weekly' ? 'selected' : ''; ?>" for="twp-weekly-schedule-<?php echo $this->term_id; ?>">
                    <input id="twp-weekly-schedule-<?php echo $this->term_id; ?>" type="radio" name="type" value="weekly" <?php echo $this->type == 'weekly' ? 'checked' : ''; ?>> Weekly
                </label>
                
                <div class="twp-option-group">

                    <h4>Timings</h4>

                    <ul class="weekly-schedule-tabs">
                        <li><span class="tw-schedule-tab active" data-tab-content="tw-schedule-weekly-mon-<?php echo $this->term_id; ?>" data-tab-content-class="tw-schedule-weekly-inner">Mon</span>
                        <li><span class="tw-schedule-tab" data-tab-content="tw-schedule-weekly-tue-<?php echo $this->term_id; ?>" data-tab-content-class="tw-schedule-weekly-inner">Tue</span>
                        <li><span class="tw-schedule-tab" data-tab-content="tw-schedule-weekly-wed-<?php echo $this->term_id; ?>" data-tab-content-class="tw-schedule-weekly-inner">Wed</span>
                        <li><span class="tw-schedule-tab" data-tab-content="tw-schedule-weekly-thu-<?php echo $this->term_id; ?>" data-tab-content-class="tw-schedule-weekly-inner">Thu</span>
                        <li><span class="tw-schedule-tab" data-tab-content="tw-schedule-weekly-fri-<?php echo $this->term_id; ?>" data-tab-content-class="tw-schedule-weekly-inner">Fri</span>
                        <li><span class="tw-schedule-tab" data-tab-content="tw-schedule-weekly-sat-<?php echo $this->term_id; ?>" data-tab-content-class="tw-schedule-weekly-inner">Sat</span>
                        <li><span class="tw-schedule-tab" data-tab-content="tw-schedule-weekly-sun-<?php echo $this->term_id; ?>" data-tab-content-class="tw-schedule-weekly-inner">Sun</span>
                        </li>
                    </ul>

                    <div style="display:block" id="tw-schedule-weekly-mon-<?php echo $this->term_id; ?>" class="tw-schedule-weekly-inner">
                        <ul class="schedule-tools">
                            <li>
                                <a href="#" class="add-new-time" data-day="0"><?php _e( 'Add', TWP_TEXTDOMAIN ); ?></a>
                            </li>
                            <li>
                                <span><?php _e( 'Generate', TWP_TEXTDOMAIN ); ?></span>
                                <ul class="generate-times">
                                    <li><span data-interval="5" data-day="0"><?php _e( 'Every 5 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="10" data-day="0"><?php _e( 'Every 10 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="15" data-day="0"><?php _e( 'Every 15 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="30" data-day="0"><?php _e( 'Every 30 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="60" data-day="0"><?php _e( 'Every 1 hour', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="120" data-day="0"><?php _e( 'Every 2 hours', TWP_TEXTDOMAIN ); ?></span></li>
                                </ul>
                            </li>
                            <li>
                                <span><?php _e( 'Copy', TWP_TEXTDOMAIN ); ?></span>
                                <ul class="copy-times">
                                    <li><span data-day="1"><?php _e( 'From Tuesday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="2"><?php _e( 'From Wednesday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="3"><?php _e( 'From Thursday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="4"><?php _e( 'From Friday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="5"><?php _e( 'From Saturday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="6"><?php _e( 'From Sunday', TWP_TEXTDOMAIN ); ?></span></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#" class="clear-times" data-day="0"><?php _e( 'Clear', TWP_TEXTDOMAIN ); ?></a>
                            </li>
                        </ul>
                        <?php $times = $this->get_weekly_times(0); ?>
                        <ul id="twp-times-day-0" data-day="0" class="times">
                            <?php 
                            if( false != $times ) : $i = 0;
                                foreach( $times as $t ) :
                                ?>
                                <li data-index="<?php echo $i; ?>">
                                    <span class="remove-time dashicons dashicons-no-alt"></span>
                                    <select name="weekly_times[0][<?php echo $i; ?>][hour]">
                                        <?php for( $h = 0; $h < 24; $h++ ) : ?>
                                            <option value="<?php echo $h; ?>" <?php echo $h == $t['hour'] ? "selected" : ""; ?>><?php echo $h < 10 ? '0' . $h : $h; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    :
                                    <select name="weekly_times[0][<?php echo $i; ?>][minute]">
                                        <?php for( $m = 0; $m < 60; $m+=5 ) : ?>
                                            <option value="<?php echo $m; ?>" <?php echo $m == $t['minute'] ? "selected" : ""; ?>><?php echo $m < 10 ? '0' . $m : $m; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </li>
                                <?php
                                $i++;
                                endforeach;
                            endif;
                            ?>
                        </ul>
                                                
                    </div>
                    
                    <div id="tw-schedule-weekly-tue-<?php echo $this->term_id; ?>" class="tw-schedule-weekly-inner">
                        <ul class="schedule-tools">
                            <li>
                                <a href="#" class="add-new-time" data-day="1"><?php _e( 'Add', TWP_TEXTDOMAIN ); ?></a>
                            </li>
                            <li>
                                <span><?php _e( 'Generate', TWP_TEXTDOMAIN ); ?></span>
                                <ul class="generate-times">
                                    <li><span data-interval="5" data-day="1"><?php _e( 'Every 5 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="10" data-day="1"><?php _e( 'Every 10 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="15" data-day="1"><?php _e( 'Every 15 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="30" data-day="1"><?php _e( 'Every 30 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="60" data-day="1"><?php _e( 'Every 1 hour', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="120" data-day="1"><?php _e( 'Every 2 hours', TWP_TEXTDOMAIN ); ?></span></li>
                                </ul>
                            </li>
                            <li>
                                <span><?php _e( 'Copy', TWP_TEXTDOMAIN ); ?></span>
                                <ul class="copy-times">
                                    <li><span data-day="0"><?php _e( 'From Monday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="2"><?php _e( 'From Wednesday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="3"><?php _e( 'From Thursday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="4"><?php _e( 'From Friday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="5"><?php _e( 'From Saturday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="6"><?php _e( 'From Sunday', TWP_TEXTDOMAIN ); ?></span></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#" class="clear-times" data-day="1"><?php _e( 'Clear', TWP_TEXTDOMAIN ); ?></a>
                            </li>
                        </ul>
                        <?php $times = $this->get_weekly_times(1); ?>
                        <ul id="twp-times-day-1" data-day="1" class="times">
                            <?php 
                            if( false != $times ) : $i = 0;
                                foreach( $times as $t ) :
                                ?>
                                <li data-index="<?php echo $i; ?>">
                                    <span class="remove-time dashicons dashicons-no-alt"></span>
                                    <select name="weekly_times[1][<?php echo $i; ?>][hour]">
                                        <?php for( $h = 0; $h < 24; $h++ ) : ?>
                                            <option value="<?php echo $h; ?>" <?php echo $h == $t['hour'] ? "selected" : ""; ?>><?php echo $h < 10 ? '0' . $h : $h; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    :
                                    <select name="weekly_times[1][<?php echo $i; ?>][minute]">
                                        <?php for( $m = 0; $m < 60; $m+=5 ) : ?>
                                            <option value="<?php echo $m; ?>" <?php echo $m == $t['minute'] ? "selected" : ""; ?>><?php echo $m < 10 ? '0' . $m : $m; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </li>
                                <?php
                                $i++;
                                endforeach;
                            endif;
                            ?>
                        </ul>
                    </div>
                    
                    <div id="tw-schedule-weekly-wed-<?php echo $this->term_id; ?>" class="tw-schedule-weekly-inner">
                        <ul class="schedule-tools">
                            <li>
                                <a href="#" class="add-new-time" data-day="2"><?php _e( 'Add', TWP_TEXTDOMAIN ); ?></a>
                            </li>
                            <li>
                                <span><?php _e( 'Generate', TWP_TEXTDOMAIN ); ?></span>
                                <ul class="generate-times">
                                    <li><span data-interval="5" data-day="2"><?php _e( 'Every 5 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="10" data-day="2"><?php _e( 'Every 10 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="15" data-day="2"><?php _e( 'Every 15 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="30" data-day="2"><?php _e( 'Every 30 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="60" data-day="2"><?php _e( 'Every 1 hour', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="120" data-day="2"><?php _e( 'Every 2 hours', TWP_TEXTDOMAIN ); ?></span></li>
                                </ul>
                            </li>
                            <li>
                                <span><?php _e( 'Copy', TWP_TEXTDOMAIN ); ?></span>
                                <ul class="copy-times">
                                    <li><span data-day="0"><?php _e( 'From Monday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="1"><?php _e( 'From Tuesday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="3"><?php _e( 'From Thursday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="4"><?php _e( 'From Friday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="5"><?php _e( 'From Saturday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="6"><?php _e( 'From Sunday', TWP_TEXTDOMAIN ); ?></span></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#" class="clear-times" data-day="2"><?php _e( 'Clear', TWP_TEXTDOMAIN ); ?></a>
                            </li>
                        </ul>
                        <?php $times = $this->get_weekly_times(2); ?>
                        <ul id="twp-times-day-2" data-day="2" class="times">
                            <?php 
                            if( false != $times ) : $i = 0;
                                foreach( $times as $t ) :
                                ?>
                                <li data-index="<?php echo $i; ?>">
                                    <span class="remove-time dashicons dashicons-no-alt"></span>
                                    <select name="weekly_times[2][<?php echo $i; ?>][hour]">
                                        <?php for( $h = 0; $h < 24; $h++ ) : ?>
                                            <option value="<?php echo $h; ?>" <?php echo $h == $t['hour'] ? "selected" : ""; ?>><?php echo $h < 10 ? '0' . $h : $h; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    :
                                    <select name="weekly_times[2][<?php echo $i; ?>][minute]">
                                        <?php for( $m = 0; $m < 60; $m+=5 ) : ?>
                                            <option value="<?php echo $m; ?>" <?php echo $m == $t['minute'] ? "selected" : ""; ?>><?php echo $m < 10 ? '0' . $m : $m; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </li>
                                <?php
                                $i++;
                                endforeach;
                            endif;
                            ?>
                        </ul>
                    </div>
                    <div id="tw-schedule-weekly-thu-<?php echo $this->term_id; ?>" class="tw-schedule-weekly-inner">
                        <ul class="schedule-tools">
                            <li>
                                <a href="#" class="add-new-time" data-day="3"><?php _e( 'Add', TWP_TEXTDOMAIN ); ?></a>
                            </li>
                            <li>
                                <span><?php _e( 'Generate', TWP_TEXTDOMAIN ); ?></span>
                                <ul class="generate-times">
                                    <li><span data-interval="5" data-day="3"><?php _e( 'Every 5 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="10" data-day="3"><?php _e( 'Every 10 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="15" data-day="3"><?php _e( 'Every 15 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="30" data-day="3"><?php _e( 'Every 30 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="60" data-day="3"><?php _e( 'Every 1 hour', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="120" data-day="3"><?php _e( 'Every 2 hours', TWP_TEXTDOMAIN ); ?></span></li>
                                </ul>
                            </li>
                            <li>
                                <span><?php _e( 'Copy', TWP_TEXTDOMAIN ); ?></span>
                                <ul class="copy-times">
                                    <li><span data-day="0"><?php _e( 'From Monday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="1"><?php _e( 'From Tuesday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="2"><?php _e( 'From Wednesday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="4"><?php _e( 'From Friday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="5"><?php _e( 'From Saturday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="6"><?php _e( 'From Sunday', TWP_TEXTDOMAIN ); ?></span></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#" class="clear-times" data-day="3"><?php _e( 'Clear', TWP_TEXTDOMAIN ); ?></a>
                            </li>
                        </ul>
                        <?php $times = $this->get_weekly_times(3); ?>
                        <ul id="twp-times-day-3" data-day="3" class="times">
                            <?php 
                            if( false != $times ) : $i = 0;
                                foreach( $times as $t ) :
                                ?>
                                <li data-index="<?php echo $i; ?>">
                                    <span class="remove-time dashicons dashicons-no-alt"></span>
                                    <select name="weekly_times[3][<?php echo $i; ?>][hour]">
                                        <?php for( $h = 0; $h < 24; $h++ ) : ?>
                                            <option value="<?php echo $h; ?>" <?php echo $h == $t['hour'] ? "selected" : ""; ?>><?php echo $h < 10 ? '0' . $h : $h; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    :
                                    <select name="weekly_times[3][<?php echo $i; ?>][minute]">
                                        <?php for( $m = 0; $m < 60; $m+=5 ) : ?>
                                            <option value="<?php echo $m; ?>" <?php echo $m == $t['minute'] ? "selected" : ""; ?>><?php echo $m < 10 ? '0' . $m : $m; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </li>
                                <?php
                                $i++;
                                endforeach;
                            endif;
                            ?>
                        </ul>
                    </div>
                    <div id="tw-schedule-weekly-fri-<?php echo $this->term_id; ?>" class="tw-schedule-weekly-inner">
                        <ul class="schedule-tools">
                            <li>
                                <a href="#" class="add-new-time" data-day="4"><?php _e( 'Add', TWP_TEXTDOMAIN ); ?></a>
                            </li>
                            <li>
                                <span><?php _e( 'Generate', TWP_TEXTDOMAIN ); ?></span>
                                <ul class="generate-times">
                                    <li><span data-interval="5" data-day="4"><?php _e( 'Every 5 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="10" data-day="4"><?php _e( 'Every 10 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="15" data-day="4"><?php _e( 'Every 15 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="30" data-day="4"><?php _e( 'Every 30 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="60" data-day="4"><?php _e( 'Every 1 hour', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="120" data-day="4"><?php _e( 'Every 2 hours', TWP_TEXTDOMAIN ); ?></span></li>
                                </ul>
                            </li>
                            <li>
                                <span><?php _e( 'Copy', TWP_TEXTDOMAIN ); ?></span>
                                <ul class="copy-times">
                                    <li><span data-day="0"><?php _e( 'From Monday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="1"><?php _e( 'From Tuesday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="2"><?php _e( 'From Wednesday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="3"><?php _e( 'From Thursday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="5"><?php _e( 'From Saturday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="6"><?php _e( 'From Sunday', TWP_TEXTDOMAIN ); ?></span></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#" class="clear-times" data-day="4"><?php _e( 'Clear', TWP_TEXTDOMAIN ); ?></a>
                            </li>
                        </ul>
                        <?php $times = $this->get_weekly_times(4); ?>
                        <ul id="twp-times-day-4" data-day="4" class="times">
                            <?php 
                            if( false != $times ) : $i = 0;
                                foreach( $times as $t ) :
                                ?>
                                <li data-index="<?php echo $i; ?>">
                                    <span class="remove-time dashicons dashicons-no-alt"></span>
                                    <select name="weekly_times[4][<?php echo $i; ?>][hour]">
                                        <?php for( $h = 0; $h < 24; $h++ ) : ?>
                                            <option value="<?php echo $h; ?>" <?php echo $h == $t['hour'] ? "selected" : ""; ?>><?php echo $h < 10 ? '0' . $h : $h; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    :
                                    <select name="weekly_times[4][<?php echo $i; ?>][minute]">
                                        <?php for( $m = 0; $m < 60; $m+=5 ) : ?>
                                            <option value="<?php echo $m; ?>" <?php echo $m == $t['minute'] ? "selected" : ""; ?>><?php echo $m < 10 ? '0' . $m : $m; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </li>
                                <?php
                                $i++;
                                endforeach;
                            endif;
                            ?>
                        </ul>
                    </div>
                    <div id="tw-schedule-weekly-sat-<?php echo $this->term_id; ?>" class="tw-schedule-weekly-inner">
                        <ul class="schedule-tools">
                            <li>
                                <a href="#" class="add-new-time" data-day="5"><?php _e( 'Add', TWP_TEXTDOMAIN ); ?></a>
                            </li>
                            <li>
                                <span><?php _e( 'Generate', TWP_TEXTDOMAIN ); ?></span>
                                <ul class="generate-times">
                                    <li><span data-interval="5" data-day="5"><?php _e( 'Every 5 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="10" data-day="5"><?php _e( 'Every 10 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="15" data-day="5"><?php _e( 'Every 15 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="30" data-day="5"><?php _e( 'Every 30 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="60" data-day="5"><?php _e( 'Every 1 hour', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="120" data-day="5"><?php _e( 'Every 2 hours', TWP_TEXTDOMAIN ); ?></span></li>
                                </ul>
                            </li>
                            <li>
                                <span><?php _e( 'Copy', TWP_TEXTDOMAIN ); ?></span>
                                <ul class="copy-times">
                                    <li><span data-day="0"><?php _e( 'From Monday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="1"><?php _e( 'From Tuesday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="2"><?php _e( 'From Wednesday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="3"><?php _e( 'From Thursday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="4"><?php _e( 'From Friday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="6"><?php _e( 'From Sunday', TWP_TEXTDOMAIN ); ?></span></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#" class="clear-times" data-day="5"><?php _e( 'Clear', TWP_TEXTDOMAIN ); ?></a>
                            </li>
                        </ul>
                        <?php $times = $this->get_weekly_times(5); ?>
                        <ul id="twp-times-day-5" data-day="5" class="times">
                            <?php 
                            if( false != $times ) : $i = 0;
                                foreach( $times as $t ) :
                                ?>
                                <li data-index="<?php echo $i; ?>">
                                    <span class="remove-time dashicons dashicons-no-alt"></span>
                                    <select name="weekly_times[5][<?php echo $i; ?>][hour]">
                                        <?php for( $h = 0; $h < 24; $h++ ) : ?>
                                            <option value="<?php echo $h; ?>" <?php echo $h == $t['hour'] ? "selected" : ""; ?>><?php echo $h < 10 ? '0' . $h : $h; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    :
                                    <select name="weekly_times[5][<?php echo $i; ?>][minute]">
                                        <?php for( $m = 0; $m < 60; $m+=5 ) : ?>
                                            <option value="<?php echo $m; ?>" <?php echo $m == $t['minute'] ? "selected" : ""; ?>><?php echo $m < 10 ? '0' . $m : $m; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </li>
                                <?php
                                $i++;
                                endforeach;
                            endif;
                            ?>
                        </ul>
                    </div>
                    <div id="tw-schedule-weekly-sun-<?php echo $this->term_id; ?>" class="tw-schedule-weekly-inner">
                        <ul class="schedule-tools">
                            <li>
                                <a href="#" class="add-new-time" data-day="6"><?php _e( 'Add', TWP_TEXTDOMAIN ); ?></a>
                            </li>
                            <li>
                                <span><?php _e( 'Generate', TWP_TEXTDOMAIN ); ?></span>
                                <ul class="generate-times">
                                    <li><span data-interval="5" data-day="6"><?php _e( 'Every 5 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="10" data-day="6"><?php _e( 'Every 10 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="15" data-day="6"><?php _e( 'Every 15 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="30" data-day="6"><?php _e( 'Every 30 minutes', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="60" data-day="6"><?php _e( 'Every 1 hour', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-interval="120" data-day="6"><?php _e( 'Every 2 hours', TWP_TEXTDOMAIN ); ?></span></li>
                                </ul>
                            </li>
                            <li>
                                <span><?php _e( 'Copy', TWP_TEXTDOMAIN ); ?></span>
                                <ul class="copy-times">
                                    <li><span data-day="0"><?php _e( 'From Monday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="1"><?php _e( 'From Tuesday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="2"><?php _e( 'From Wednesday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="3"><?php _e( 'From Thursday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="4"><?php _e( 'From Friday', TWP_TEXTDOMAIN ); ?></span></li>
                                    <li><span data-day="5"><?php _e( 'From Saturday', TWP_TEXTDOMAIN ); ?></span></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#" class="clear-times" data-day="6"><?php _e( 'Clear', TWP_TEXTDOMAIN ); ?></a>
                            </li>
                        </ul>
                        <?php $times = $this->get_weekly_times(6); ?>
                        <ul id="twp-times-day-6" data-day="6" class="times">
                            <?php 
                            if( false != $times ) : $i = 0;
                                foreach( $times as $t ) :
                                ?>
                                <li data-index="<?php echo $i; ?>">
                                    <span class="remove-time dashicons dashicons-no-alt"></span>
                                    <select name="weekly_times[6][<?php echo $i; ?>][hour]">
                                        <?php for( $h = 0; $h < 24; $h++ ) : ?>
                                            <option value="<?php echo $h; ?>" <?php echo $h == $t['hour'] ? "selected" : ""; ?>><?php echo $h < 10 ? '0' . $h : $h; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    :
                                    <select name="weekly_times[6][<?php echo $i; ?>][minute]">
                                        <?php for( $m = 0; $m < 60; $m+=5 ) : ?>
                                            <option value="<?php echo $m; ?>" <?php echo $m == $t['minute'] ? "selected" : ""; ?>><?php echo $m < 10 ? '0' . $m : $m; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </li>
                                <?php
                                $i++;
                                endforeach;
                            endif;
                            ?>
                        </ul>
                    </div>

                </div>
                
                <div class="twp-option-group">
                
                    <h4>Activity Date Span</h4>

                    <div class="tw-schedule-weekly-range">
                        <p>
                            <?php _e( 'Run from', TWP_TEXTDOMAIN ); ?>
                            <input class="schedule-span" type="text" placeholder="DD/MM/YYYY" name="span_from" value="<?php echo $this->span_from; ?>">
                            <?php _e( 'to', TWP_TEXTDOMAIN ); ?>
                            <input class="schedule-span" type="text" placeholder="DD/MM/YYYY" name="span_to" value="<?php echo $this->span_to; ?>">
                        </p>

                    </div>

                </div>
              
                <hr style="margin:15px 0px;"/>
                
                <a href="<?php echo TWP_UPGRADE_LINK; ?>" target="_blank" style="display:block;float:left">
                    <img width="590" height="301" src="<?php echo TWP_PLUGIN_URL; ?>/assets/images/go-pro/schedule-dates.png">
                </a>

            </form>

        </div>

        <?php

    }

    // ...
    
    /**
     * Check if there are any tweeting times set
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return boolean
     */
    
    public function has_weekly_times( $day ) {

        if( ! isset( $this->weekly_times[$day] ) || null == $this->weekly_times[$day] )
            return false;
        
        return true;
        
    }
    
    // ...
    
    /**
     * Retrieve tweeting times in unchanged form (human readable)
     * Sorted by time in ASC order
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return array
     */
    
    public function get_weekly_times( $day ) {
        
        if( false == $this->has_weekly_times( $day ) )
            return false;

        return $this->weekly_times[$day];
        
    }
    
    // ...
    
    /**
     * Retrieve tweeting times as timestamps
     * Sorted by value in ASC order
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return array
     */
    
    public function get_weekly_times_in_seconds( $day ) {
        
        if( false == $this->has_weekly_times( $day ) )
            return false;
        
        $times = $this->weekly_times[$day];
     
        $timestamps = array();
        
        foreach( $times as $t ) :
            
            // turn into seconds
            $timestamps[] = ( $t['hour']*3600 ) + ( $t['minute']*60 );
        
        endforeach;
        
        return $timestamps;
        
    }
    
    /**
     * Determined if queue should send a tweet
     *
     * @type function
     * @date 10/10/2015
	 * @since 2.0
     *
     * @param n/a
	 * @return int | false
     */
    
    public static function should_tweet( $term_id ) {
     
        return self::get_time( $term_id, true );
        
    }
    
    // ...
    
    /**
     * Finds next tweeting time in the future from now
     * If reversed, it will search for closest past time
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return int | false
     */
    
    public static function get_time( $term_id, $reversed = false ) {
        
        $dates = array();
        $a = array(); 
        $return = array(); 

        // Define base for date calculation
        if( $reversed ) :

            // Timestamps
            if( ( $span_from = get_term_meta( $term_id, 'twp_schedule_span_from', true ) ) != null ) :
                if( strtotime( '00:00:00 ' . str_replace( '/', '-', $span_from ), current_time('timestamp') ) >= current_time('timestamp') )
                    return false;                   
            endif;

            if( ( $span_to = get_term_meta( $term_id, 'twp_schedule_span_to', true ) ) != null ) :
                if( strtotime( '23:59:59 ' . str_replace( '/', '-', $span_to ), current_time('timestamp') ) < current_time('timestamp') )
                    return false;                   
            endif;

            $span_from = current_time('timestamp');

        else :

            $span_from = get_term_meta( $term_id, 'twp_schedule_span_from', true ) ? strtotime( str_replace( "/", "-", get_term_meta( $term_id, 'twp_schedule_span_from', true ) ), current_time( 'timestamp' ) ) : current_time('timestamp');

            $span_to = get_term_meta( $term_id, 'twp_schedule_span_to', true ) ? strtotime( str_replace( "/", "-", get_term_meta( $term_id, 'twp_schedule_span_to', true ) ) ) : '';

        endif;

        // ...

        $data = get_term_meta( $term_id, 'twp_schedule_weekly_times', true );

        if( ! $data )
            return false;

        // turn into full valid timestamp including date
        foreach( $data as $day => $times ) :

            // turn into full valid timestamp including date
            foreach( $times as $t ) :

                $translate_days = array(
                    '0' => 'Monday',
                    '1' => 'Tuesday',
                    '2' => 'Wednesday',
                    '3' => 'Thursday',
                    '4' => 'Friday',
                    '5' => 'Saturday',
                    '6' => 'Sunday'
                );

                $operator = '';

                if( 
                    date('N',$span_from)-1 < $day ||
                    ( ! $reversed && date( 'N',$span_from )-1 == $day && strtotime( 'today midnight' ) + ( $t['hour']*3600 ) + ( $t['minute']*60 ) < current_time('timestamp') )
                ):
                    $operator = 'next ' . $translate_days[$day];
                elseif( date('N',$span_from)-1 == $day ) :
                    $operator = 'today';
                else :
                    $operator = 'this ' . $translate_days[$day];
                endif;

                if( ! $reversed ) :
                    // Check if the date doesnt go outside of the date range
                    if( $span_to != '' && $span_to < ( strtotime( $operator . ' midnight', $span_from ) + ( $t['hour']*3600 ) + ( $t['minute']*60 ) ) )
                        continue;
                endif;

                $dates[] = strtotime( $operator . ' midnight', $span_from ) + ( $t['hour']*3600 ) + ( $t['minute']*60 );

            endforeach;

        endforeach;

        // find closest
        foreach( $dates as $key => $val ) : 

            $a[$key] = abs( $val - ( get_term_meta( $term_id, 'twp_schedule_type', true ) == 'weekly' ? $span_from : current_time( 'timestamp' ) ) ); 

        endforeach;
        
        asort($a); 
        
        foreach( $a as $key => $val ) :
        
            $return[] = $dates[$key]; 
        
        endforeach;
        
        // At this point we have sorted array with the closest time at the top
        // just need to return first one that is in the past
        foreach( $return as $r ) :
        
            if( $reversed ) {
        
                $last_tweet_time = ( get_term_meta( $term_id, 'twp_last_tweeted_time', true ) != null ? get_term_meta( $term_id, 'twp_last_tweeted_time', true ) : 0 );
        
                if( $r < current_time( 'timestamp' ) && $r > $last_tweet_time )
                    return $r;
        
            } else {

                if( $r >= current_time( 'timestamp' ) )
                    return $r;
        
            }
        
        endforeach;
        
        return false;

    }
    
    // ...
    
    /**
     * Retrieves set days for tweeting
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return array | false
     */
    
    public function get_weekly_days() {
     
        if( ! isset( $this->settings['days'] ) || empty( $this->settings['days'] ) )
            return false;
        
        return $this->settings['days'];
        
    }
    
    // ...
    
    /**
     * Sorts given array of tweeting times in ASC order
     *
     * @type function
     * @date 16/06/2015
	 * @since 1.0
     *
     * @param n/a
	 * @return array
     */
    
    public static function sanitize_times( $times ) {

        $sanitized = array();
        
        foreach( $times as $k => $v ) :
        
            $timestamps = array();

            // Convert to timestamps
            foreach( $v as $t ) :

                // turn into seconds
                $timestamps[] = ( $t['hour']*3600 ) + ( $t['minute']*60 );

            endforeach;
        
            // Remove duplicated times
            $timestamps = array_unique( $timestamps );    
        
            // Sort by order
            sort($timestamps);

            // Revert back to original form
            foreach( $timestamps as $ts ) :

                $hours = floor($ts / 3600);
                $minutes = floor(($ts / 60) % 60);

                $sanitized[$k][] = array( 'hour' => $hours, 'minute' => $minutes );

            endforeach;
        
        endforeach;
        
        return $sanitized;
        
    }
    
    // ...
    
    /**
     * Restores default settings for a given queue
     *
     * @type function
     * @date 07/10/2015
	 * @since 2.0
     *
     * @param n/a
	 * @return array
     */
    
    public static function restore_default_settings( $term_id ) {
        
        update_term_meta( $term_id, 'twp_schedule_type', 'weekly' );
        delete_term_meta( $term_id, 'twp_schedule_weekly_times' );
        delete_term_meta( $term_id, 'twp_schedule_span_from' );
        delete_term_meta( $term_id, 'twp_schedule_span_to' );
        
    }
    
    // ...
    
    /**
     * Restores default settings for a given queue
     *
     * @type function
     * @date 07/10/2015
	 * @since 2.0
     *
     * @param n/a
	 * @return array
     */
    
    public static function delete_all_settings( $term_id ) {
        
        delete_term_meta( $term_id, 'twp_schedule_type', 'weekly' );
        delete_term_meta( $term_id, 'twp_schedule_weekly_times' );
        delete_term_meta( $term_id, 'twp_schedule_span_from' );
        delete_term_meta( $term_id, 'twp_schedule_span_to' );
        
    }
    
}