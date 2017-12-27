<?php
extract($arr);
$room_list = Colibri_PMS::cl_rt_get_list_rooms_of_hotel($hotel_code, $start, $end);
$rooms_rates = json_decode(stripcslashes($room_rates));
?>

<h5><?php echo __('New info', ST_TEXTDOMAIN); ?></h5>
<ul class="booking-item-new-info">
    <li>
        <p class="booking-item-payment-price-title"><?php echo __('Arrival', ST_TEXTDOMAIN); ?></p>
        <p class="booking-item-payment-price-amount">
            <?php echo $start; ?> </p>
    </li>

    <li>
        <p class="booking-item-payment-price-title"><?php echo __('Departure', ST_TEXTDOMAIN); ?></p>
        <p class="booking-item-payment-price-amount">
            <?php echo $end; ?> </p>
    </li>


    <li>
        <p class="booking-item-payment-price-title"><?php echo __('Duration', ST_TEXTDOMAIN); ?></p>
        <p class="booking-item-payment-price-amount">
            <?php echo Colibri_Helper::cl_get_number_of_day(TravelHelper::convertDateFormatColibri($start), TravelHelper::convertDateFormatColibri($end)); ?>
            night(s) </p>
    </li>
</ul>

<!--/ Condition /-->
<div class="cba-room-conditions cba-room-conditions-modify">
    <table>
        <tr>
            <th><?php echo __('Conditions', ST_TEXTDOMAIN); ?></th>
            <th><?php echo __('Number', ST_TEXTDOMAIN); ?></th>
        </tr>
        <?php $i = 0;
        $list_rates = Colibri_PMS::cl_get_rates_plan_of_hotel($hotel_code, $start, $end);
        foreach ($list_rates as $k => $v):
            $room_rate = Colibri_Helper::cl_get_room_rate_by_rate_plan_code($room_list, $room_code, $k);
            $price = Colibri_Helper::cl_cal_price_for_room_item($room_rate, 1);
            if ($price > 0):
                ?>
                <tr>
                    <td class="cba-condition-push"><i
                                class="fa fa-angle-double-right"></i> <?php echo $v . ' ' . __('Rates', ST_TEXTDOMAIN); ?>
                    </td>
                    <td>
                        <?php
                        //$number_room = $item['num_of_unit'];
                        $number_room = Colibri_Helper::cl_get_max_room_number_modify($rooms_rates);
                        if ($number_room > 0) {
                            ?>
                            <select data-rate-name="<?php echo $v; ?>"
                                    data-total-unit="<?php echo $item['num_of_unit']; ?>"
                                    data-rate-code="<?php echo $k; ?>" data-price="<?php echo $price; ?>"
                                    class="cba-select-numbr-room-modify">
                                <option value="0">0</option>
                                <?php
                                for ($nr = 1; $nr <= $number_room; $nr++) {
                                    $select = '';
                                    if ($i == 0) {
                                        $select = ' selected ';
                                    }
                                    echo '<option data-base-price="' . $price . '" data-price="' . $nr * $price . '" ' . $select . ' value="' . $nr . '">' . $nr . ' (' . Colibri_Helper::cl_format_money($nr * $price, 'EUR', 'left', '', 2) . ')</option>';
                                }
                                ?>
                            </select>
                            <?php
                        } else {
                            echo __('Sold out', ST_TEXTDOMAIN);
                        }
                        ?>
                    </td>
                </tr>
                <tr class="cba-condition-detail">
                    <td colspan="2">

                        <?php
                        $rates = Colibri_PMS::cl_get_rates($hotel_code, $start, $end, $k);
                        if (!empty($rates['cancel'])):
                            ?>
                            <strong><?php echo __('Cancellation: ', ST_TEXTDOMAIN); ?></strong><br/>
                            <?php
                            foreach ($rates['cancel'] as $kk => $vv) {
                                if (!empty($vv['amount_percent'])) {
                                    foreach ($vv['amount_percent'] as $kkk => $vvv) {
                                        if (trim($vvv['unit']) != '') {
                                            printf(__('- Cancellation within %s %s before the date of arrival %s will be charged;', ST_TEXTDOMAIN), $vv['deadline']['unit'], $vv['deadline']['time_unit'], $vvv['unit'] . ' ' . $vvv['text']);
                                            echo '<br />';
                                        }
                                    }
                                }
                            }
                        else:
                            echo __('No data', ST_TEXTDOMAIN);
                        endif;
                        ?>
                    </td>
                </tr>
                <?php $i++; endif; endforeach; ?>
    </table>
</div>
<!--/ End Condition /-->