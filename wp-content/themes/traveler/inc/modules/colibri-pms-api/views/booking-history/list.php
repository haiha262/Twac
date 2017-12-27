<?php
if(isset($stt)){
    $i = $stt + 1;
}else{
    $i = 1;
}

foreach ($data as $k => $v) {
    $data_booking = json_decode($v->data);
    $data_room_rates = json_decode(stripslashes($data_booking->room_rates));
    $data_res = json_decode($v->data_res);
    $status = $v->status;
    ?>
    <tr>
        <!--<td><?php //echo '<strong class="cba-stt">' . $i++ . '</strong>'; ?></td>-->
        <td>
            <?php
                echo '<strong>#' . substr($data_res->res_value, 2, (strlen($data_res->res_value) - 2)) . '</strong>';
                $prex = substr($data_res->res_value, 0, 1);
                if($prex == 'G'){
                    echo '<p><small class="text-danger">'. __('Group', ST_TEXTDOMAIN) .'</small></p>';
                }else{
                    echo '<p><small class="text-primary">'. __('Single', ST_TEXTDOMAIN) .'</small></p>';
                }
            ?>
        </td>
        <td>
            <?php
                if(isset($data_booking->hotel_thumb) && $data_booking->hotel_thumb != '') {
                    echo '<img class="cba-bht-thumb" src="' . $data_booking->hotel_thumb . '" alt="' . str_replace("\'", "'", stripcslashes($data_booking->hotel_name)) . '"/>';
                }
                echo '<b>' . preg_replace("/[^A-Za-z0-9' \-]/", "", $data_booking->hotel_name) . '</b>';
            ?>
        </td>
        <td>
            <?php
            echo '<b>' . $data_booking->room_name . '</b>';
            ?>
        </td>
        <td>
            <?php
            echo $data_booking->start . ' <i class="fa fa-long-arrow-right"></i> ' . $data_booking->end . '<br />';
            echo '<small>' . __('Booking date: ', ST_TEXTDOMAIN);
            echo $v->booking_date . '</small><br />';
            if(isset($v->modify_date) && $v->modify_date != '') {
	            echo '<small>' . __( 'Modify date: ', ST_TEXTDOMAIN );
	            echo $v->modify_date . '</small>';
            }
            ?>
        </td>
        <td>
            <table>
                <tr>
                    <td><?php echo __('Rates', ST_TEXTDOMAIN); ?></td>
                    <td><?php echo __('Price', ST_TEXTDOMAIN); ?></td>
                </tr>
                <?php $total = 0;
                if(!isset($data_booking->currency)){
                    $currency_code = 'EUR';
                }else{
                    $currency_code = $data_booking->currency;
                }
                foreach ($data_room_rates as $kk => $vv): ?>
                    <tr>
                        <td><?php echo $vv[3]; ?></td>
                        <td><?php echo $vv[0] . ' x ' . Colibri_Helper::cl_format_money($vv[2], $currency_code, 'left', ' ', 2); ?></td>
                    </tr>
                    <?php $total = $total + $vv[0] * $vv[2]; endforeach; ?>
                <tr>
                    <td><b><?php echo __('Total', ST_TEXTDOMAIN); ?></b></td>
                    <td><b><?php echo Colibri_Helper::cl_format_money($total, $currency_code, 'left', ' ', 2); ?></b></td>
                </tr>
            </table>

        </td>
        <td>
            <div class="cba-booking-history-status">
                <?php
                switch ($status) {
                    case "pending":
                        $today = strtotime(date('d-m-Y'));
                        $start =  strtotime(TravelHelper::convertDateFormat($data_booking->start));
                        $end =  strtotime(TravelHelper::convertDateFormat($data_booking->end));
                        //if($today >= $start && $today <= $end){
                        //    echo '<strong class="text-success">' . __('In Progress', ST_TEXTDOMAIN) . '</strong>';
                        //}
                        //if($today > $end){
                        //    echo '<strong class="text-primary">' . __('Completed', ST_TEXTDOMAIN) . '</strong>';
                        //}
                        echo '<strong class="text-success">' . __('Success', ST_TEXTDOMAIN) . '</strong>';
                        if($today < $start){
                            ?>
                            <a href="#cancel" class="btn btn-danger btn-xs cba-cancel-booking"
                               data-stas-id="<?php echo $v->id; ?>"
                               data-type="<?php echo isset($data_res->res_type) ? $data_res->res_type : ''; ?>"
                               data-id="<?php echo isset($data_res->res_value) ? $data_res->res_value : ''; ?>"
                               data-source="<?php echo isset($data_res->res_source) ? $data_res->res_source : ''; ?>"><?php echo __('Cancel', ST_TEXTDOMAIN); ?>
                                <i class="fa fa-spinner fa-spin" style="display: none;"></i></a>
                            <br />

                            <a href="#cancel"
                               data-toggle="modal"
                               data-target="#modalCBAModifyBooking"
                               data-stas-id="<?php echo $v->id; ?>"
                               class="btn btn-info btn-xs cba-modify-booking"><?php echo __('Modify', ST_TEXTDOMAIN); ?>
                                <i class="fa fa-spinner fa-spin" style="display: none;"></i></a>
                            <?php
                        }
                        if($today > $end){
                            ?>
                            <a href="#cancel" class="btn btn-warning btn-xs cba-remove-booking"
                               data-stas-id="<?php echo $v->id; ?>"><?php echo __('Remove', ST_TEXTDOMAIN); ?>
                                <i class="fa fa-spinner fa-spin" style="display: none;"></i></a>
                            <?php
                        }
                        break;
                    case "cancelled":
                        echo '<strong class="text-danger">' . __('Cancelled', ST_TEXTDOMAIN) . '</strong>';
                        ?>
                        <a href="#cancel" class="btn btn-warning btn-xs cba-remove-booking"
                           data-stas-id="<?php echo $v->id; ?>"><?php echo __('Remove', ST_TEXTDOMAIN); ?>
                            <i class="fa fa-spinner fa-spin" style="display: none;"></i></a>
                        <?php
                        break;
                }
                ?>
            </div>
        </td>
    </tr>
    <?php
}