<?php global $post; ?>

<div class="row calendar-starttime-wrapper" data-post-id="<?php echo $post->ID; ?>">
    <div class="col-xs-12 col-lg-4">
        <div class="calendar-starttime-form">
            <div class="form-group">
                <label for="calendar_starttime_check_in"><strong><?php echo __( 'Check In', ST_TEXTDOMAIN ); ?></strong></label>
                <input readonly="readonly" type="text" class="widefat option-tree-ui-input date-picker"
                       name="calendar_starttime_check_in" id="calendar_starttime_check_in"
                       placeholder="<?php echo __( 'Check In', ST_TEXTDOMAIN ); ?>">
            </div>
            <div class="form-group">
                <label for="calendar_starttime_check_out"><strong><?php echo __( 'Check Out', ST_TEXTDOMAIN ); ?></strong></label>
                <input readonly="readonly" type="text" class="widefat option-tree-ui-input date-picker"
                       name="calendar_starttime_check_out" id="calendar_starttime_check_out"
                       placeholder="<?php echo __( 'Check Out', ST_TEXTDOMAIN ); ?>">
            </div>
			<?php do_action( 'st_after_day_tour_calendar' ); ?>
            <!-- XKEI - Starttime form in custom option type -->
            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group">
                        <label><strong><?php echo __( 'Start time', ST_TEXTDOMAIN ); ?></strong></label>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="calendar-starttime-wraper starttime-origin">
                            <select class="calendar_starttime_hour" name="calendar_starttime_hour[]">
								<?php
								for ( $i = 0; $i < 24; $i ++ ) {
									echo '<option value="' . (($i < 10) ? ('0' . $i) : $i) . '">' . (($i < 10) ? ('0' . $i) : $i) . '</option>';
								}
								?>
                            </select>
                            <span><i><?php echo __( 'hour', ST_TEXTDOMAIN ); ?></i></span>
                            <select class="calendar_starttime_minute" name="calendar_starttime_minute[]">
								<?php
								for ( $i = 0; $i < 60; $i ++ ) {
									echo '<option value="' . (($i < 10) ? ('0' . $i) : $i) . '">' . (($i < 10) ? ('0' . $i) : $i) . '</option>';
								}
								?>
                            </select>
                            <span><i><?php echo __( 'minute', ST_TEXTDOMAIN ); ?></i></span>
                            <div class="calendar-remove-starttime"><span class="dashicons dashicons-no-alt"></span></div>
                        </div>
                        <div id="calendar-add-starttime"><span class="dashicons dashicons-plus"></span></div>
                    </div>
                </div>
            </div>
            <!-- End form custom option type -->
            <div class="form-group">
                <div class="form-message">
                    <p></p>
                </div>
            </div>
            <div class="form-group">
                <input type="hidden" name="calendar_starttime_post_id" value="<?php echo $post->ID; ?>">
                <input type="submit" id="calendar_starttime_submit" class="option-tree-ui-button button button-primary"
                       name="calendar_starttime_submit" value="<?php echo __( 'Update', ST_TEXTDOMAIN ); ?>">
				<?php //do_action( 'traveler_after_form_submit_tour_calendar' ); ?>
	            <?php do_action( 'traveler_after_form_submit_tour_starttime_calendar' ); ?>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-lg-8">
        <div class="calendar-starttime-content"
             data-hide_adult="<?php echo get_post_meta( $post->ID, 'hide_adult_in_booking_form', true ) ?>"
             data-hide_children="<?php echo get_post_meta( $post->ID, 'hide_children_in_booking_form', true ) ?>"
             data-hide_infant="<?php echo get_post_meta( $post->ID, 'hide_infant_in_booking_form', true ) ?>"
        >
        </div>
        <div class="overlay">
            <span class="spinner is-active"></span>
        </div>
    </div>
	<?php do_action( 'traveler_after_form_tour_starttime_calendar' ); ?>
</div>