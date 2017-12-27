<?php
$html_all = Colibri_Helper::cl_get_booking_history();
?>
<div class="st-create">
    <h2><?php echo __('Booking History', ST_TEXTDOMAIN); ?></h2>
</div>
<div class="alert alert-success cba-alert-cancel-booking" style="display: none">dsd</div>
<div class="tabbable">
    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a href="#tab-all" data-toggle="tab"><?php _e("All", ST_TEXTDOMAIN) ?></a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade in active" id="tab-all">
            <?php
            if (!empty($html_all)) {
                ?>
                <table class="table table-bordered table-striped table-booking-history ">
                    <thead>
                    <tr>
                        <!--<th class="hidden-xs"><?php //echo __('#', ST_TEXTDOMAIN); ?></th>-->
                        <th class="hidden-xs"><?php echo __('Reservation ID', ST_TEXTDOMAIN); ?></th>
                        <th><?php echo __('Hotel Name'); ?></th>
                        <th><?php echo __('Room Name'); ?></th>
                        <th><?php echo __('Arrival/Departure'); ?></th>
                        <th><?php echo __('Amount'); ?></th>
                        <th><?php echo __('Status'); ?></th>
                    </tr>
                    </thead>
                    <tbody id="cba_data_history_book">
                    <?php
                    Colibri_Helper::cl_render_data_booking_history($html_all);
                    ?>
                    </tbody>
                </table>
                <?php
                $count_post = Colibri_Helper::cl_count_booking_history(get_current_user_id());
                if ($count_post > 5):
                    ?>
                    <span class="btn btn-primary btn_cba_load_more_history_book" data-paged="1"
                          data-per="2" data-type=""><?php st_the_language('user_load_more') ?><i class="fa fa-spinner fa-spin"></i></span>
                <?php endif; ?>
                <?php
            } else {
                echo '<br /><p>'. __('No Booking History', ST_TEXTDOMAIN) .'</p>';
            } ?>
        </div>
    </div>
</div>