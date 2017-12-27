<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Hotel loop room item
 *
 * Created by ShineTheme
 *
 */
extract($item);
if (!isset($cldt_dtr))
    global $cldt_dtr;

$number_of_days = Colibri_Helper::cl_get_number_of_day(TravelHelper::convertDateFormat($start), TravelHelper::convertDateFormat($end));
$list_rates = Colibri_PMS::cl_get_rates_plan_of_hotel($item['hotel_code'], $start, $end);
$check_los = Colibri_Helper::cl_check_min_lenght_of_stay($item['hotel_code'], $start, $end, $item['id']);

$currency_code = 'EUR';
if(!empty($item['room_rates'])){
    $currency_code = $item['room_rates'][0]['rate'][0]['currency_code'];
}
?>
<li itemscope itemtype="http://schema.org/Hotel">
    <div class="booking-item">
        <div class="row">
            <div class="col-md-3">
                <?php
                $link = '';
                //$link = get_the_permalink();
                /*if (STInput::request('start') and STInput::request('end')){
                    $link = esc_url(
                        add_query_arg( array(
                            'check_in'=>STInput::request('start'),
                            'check_out'=>STInput::request('end'),
                            'room_num_search'=>STInput::request('room_num_search') ,
                            'child_number'=> STInput::request('child_number'),
                            'adult_number'=> STInput::request('adult_number')
                        ) , $link )
                    );
                }*/

                ?>
                <a href="<?php echo esc_url($link); ?>"
                   data-toggle="modal"
                   data-target="#modalCBARoomDetail"
                   class="cba-show-room-detail hover-img"
                   data-room-code="<?php echo $item['id'] ?>"
                   data-hotel-code="<?php echo $item['hotel_code']; ?>"
                   data-rate-plan-code="<?php echo $item['rate_plan_code'] ?>"
                   data-start="<?php echo $start; ?>"
                   data-end="<?php echo $end; ?>"
                   data-number-room="<?php echo $item['number_select_room']; ?>">
                    <?php
                    if ($item['thumb'] != '') {
                        echo '<img class="cba-room-item-thumb" src = "' . $item['thumb'] . '" alt="' . $item['name'] . '" />';
                    } else {
                        if (function_exists('st_get_default_image'))
                            echo st_get_default_image();
                    }
                    ?>
                </a>
            </div>
            <div class="col-md-6">
                <h5 class="booking-item-title">
                    <a href="#"
                       class="cba-show-room-detail"
                       data-toggle="modal"
                       data-target="#modalCBARoomDetail"
                       title=""
                       data-room-code="<?php echo $item['id'] ?>"
                       data-hotel-code="<?php echo $item['hotel_code']; ?>"
                       data-rate-plan-code="<?php echo $item['rate_plan_code'] ?>"
                       data-start="<?php echo $start; ?>"
                       data-end="<?php echo $end; ?>"
                       data-number-room="<?php echo $item['number_select_room']; ?>">
                        <?php echo $item['name']; ?>
                    </a>
                </h5>
                <div class="text-small">
                    <p style="margin-bottom: 10px;">
                        <?php
                        //echo Colibri_Helper::cl_parse_aqc_code(10);
                        // TravelHelper::cutnchar($excerpt,120);
                        ?>
                    </p>
                </div>
                <ul class="booking-item-features booking-item-features-sign clearfix">
                    <?php
                    foreach ($item['guest_count'] as $key => $val) {
                        //if ($val['count'] > 0) {
                        ?>
                        <li rel="tooltip" data-placement="top" title=""
                            data-original-title="<?php echo $val['age']['text']; ?>"><i
                                    class="<?php echo $val['age']['icon']; ?>"></i><span
                                    class="booking-item-feature-sign"> x <?php echo $val['count']; ?></span>
                        </li>
                        <?php
                        //}
                    }
                    if ($item['bed'] != '') {
                        ?>
                        <li rel="tooltip" data-placement="top" title=""
                            data-original-title="<?php echo __('Bedroom', ST_TEXTDOMAIN); ?>"><i
                                    class="im im-bed"></i><span
                                    class="booking-item-feature-sign"> x <?php echo $item['bed']; ?></span>
                        </li>
                        <?php
                    }
                    if ($item['num_of_unit'] != '') {
                        ?>
                        <li rel="tooltip" data-placement="top" title=""
                            data-original-title="<?php echo __('Number room', ST_TEXTDOMAIN); ?>"><i
                                    class="fa fa-server"></i><span
                                    class="booking-item-feature-sign"> x <?php echo $item['num_of_unit']; ?></span>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
            <div class="col-md-3">
                <!--<a href="#" class="btn btn-primary btn_hotel_booking cba-btn-show-price"><?php echo __('Show price', ST_TEXTDOMAIN); ?></a>-->
                <!--<div class="cba-show-price">-->
                <!--<span class="booking-item-price"><?php echo TravelHelper::format_money(Colibri_Helper::cl_cal_price_for_room_item($item['room_rates'], 1), false); ?></span>-->

                <?php
                //$price = Colibri_Helper::cl_cal_price_for_room_item($item['room_rates'], 1);
                //$price = $price * $item['number_select_room'];

                $arr_price_rate = [];
                foreach ($list_rates as $k => $v) {
                    $room_rate = Colibri_Helper::cl_get_room_rate_by_rate_plan_code($cldt_dtr, $item['id'], $k);
                    if (Colibri_Helper::cl_cal_price_for_room_item($room_rate, 1) > 0)
                        array_push($arr_price_rate, Colibri_Helper::cl_cal_price_for_room_item($room_rate, 1));
                }

                $price = min($arr_price_rate);
                $price = $price * $item['number_select_room'];
                ?>

                <?php echo __('Price from: ', ST_TEXTDOMAIN); ?><span
                        class="booking-item-price"><?php echo Colibri_Helper::cl_format_money($price, $currency_code, 'left', false); ?></span>
                <span class="booking-item-price-unit">/ <?php echo $number_of_days . ' ' . __('night(s)', ST_TEXTDOMAIN); ?> </span>
                <?php
                $page_link = st()->get_option('cba_page_checkout', '');
                $page_slug = get_post($page_link)->post_name;

                //Data price for per night
                $data_price = Colibri_Helper::cl_get_arr_price_for_range_night($item['room_rates'], 2, 2);
                $check_out_page = st()->get_option('cba_room_checkout', 'off');
                if ($check_out_page == 'off'):
                    ?>
                    <br/>
                    <a <?php echo $item['num_of_unit'] > 0 && $check_los ? '' : ' disabled ' ?>
                            data-number-room="<?php echo $item['number_select_room']; ?>"
                            data-price="<?php echo $data_price; ?>"
                            data-room-type-code="<?php echo $item['id'] ?>"
                            data-rate-plan-code="<?php echo $item['rate_plan_code'] ?>"
                            data-hotel-code="<?php echo $item['hotel_code']; ?>"
                            data-room-name="<?php echo $item['name']; ?>" class="btn btn-primary btn_cba_hotel_booking"
                            data-toggle="modal"
                            data-target="#modalCBACheckOut"><?php echo __('Book', ST_TEXTDOMAIN); ?></a>
                    <?php
                endif;

                if ($check_out_page == 'on'):
                    ?>
                    <form method="GET" action="<?php echo get_site_url() . '/' . $page_slug ?>"
                          id="cba-form-checkout-page">
                        <input type="hidden" name="room_code" value="<?php echo $item['id']; ?>"/>
                        <input type="hidden" name="rate_plan" value="<?php echo $item['rate_plan_code'] ?>"/>
                        <input type="hidden" class="cba-form-room-rates" name="room_rates" value=""/>
                        <input type="hidden" name="start" value="<?php echo $start; ?>"/>
                        <input type="hidden" name="end" value="<?php echo $end; ?>"/>
                        <input type="hidden" name="hotel_code" value="<?php echo $item['hotel_code']; ?>"/>
                        <input type="hidden" class="cba-form-age-code" name="age_code" value=""/>
                        <input type="hidden" class="cba-form-hotel-name" name="hotel_name" value=""/>
                        <input type="hidden" name="room_name" value="<?php echo $item['name']; ?>"/>
                        <button <?php echo $item['num_of_unit'] > 0 && $check_los ? '' : ' disabled ' ?> type="submit"
                                                                                                         class="btn btn-primary cba-btn-checkout-page">
                            <?php echo __('Book', ST_TEXTDOMAIN); ?>
                        </button>
                    </form>
                <?php endif; ?>
                <!--</div>-->
            </div>
        </div>

        <?php

        ?>
        <!--/ Condition /-->
        <div class="cba-room-conditions">
            <table>
                <tr>
                    <th><?php echo __('Conditions', ST_TEXTDOMAIN); ?></th>
                    <th><?php printf(__('Max	Price (for %s nights)', ST_TEXTDOMAIN), $number_of_days); ?></th>
                    <th><?php echo __('Number', ST_TEXTDOMAIN); ?></th>
                </tr>
                <?php $i = 0;
                foreach ($list_rates as $k => $v):
                    $room_rate = Colibri_Helper::cl_get_room_rate_by_rate_plan_code($cldt_dtr, $item['id'], $k);
                    $price = Colibri_Helper::cl_cal_price_for_room_item($room_rate, 1);
                    if ($price > 0):
                        ?>
                        <tr>
                            <td class="cba-condition-push"><i
                                        class="fa fa-angle-double-right"></i> <?php echo $v . ' ' . __('Rates', ST_TEXTDOMAIN); ?>
                            </td>
                            <td>
                                <?php
                                echo Colibri_Helper::cl_format_money($price, $currency_code, 'left', '', 2);
                                ?>
                            </td>
                            <td>
                                <?php
                                $number_room = $item['num_of_unit'];
                                if ($number_room > 0) {
                                    ?>
                                    <select data-rate-name="<?php echo $v; ?>"
                                            data-total-unit="<?php echo $item['num_of_unit']; ?>"
                                            data-rate-code="<?php echo $k; ?>" data-price="<?php echo $price; ?>"
                                            class="cba-select-numbr-room">
                                        <option value="0">0</option>
                                        <?php
                                        for ($nr = 1; $nr <= $number_room; $nr++) {
                                            $select = '';
                                            if ($i == 0) {
                                                if ($nr == 1) {
                                                    $select = ' selected ';
                                                }
                                            }
                                            echo '<option data-price="' . $nr * $price . '" ' . $select . ' value="' . $nr . '">' . $nr . ' (' . Colibri_Helper::cl_format_money($nr * $price, $currency_code, 'left', '', 2) . ')</option>';
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
                            <td colspan="3">
                                <?php if (!$check_los): ?>
                                    <div class="alert alert-danger">
                                        <?php echo __('Minimal Length of Stay is bigger than selected date range', ST_TEXTDOMAIN); ?>
                                    </div>
                                <?php endif; ?>
                                <?php
                                $rates = Colibri_PMS::cl_get_rates($item['hotel_code'], $start, $end, $k);
                                if (!empty($rates['cancel'])):
                                    $i = 0;
                                    ?>

                                    <?php
                                    foreach ($rates['cancel'] as $kk => $vv) {
                                        if (!empty($vv['amount_percent'])) {
                                            foreach ($vv['amount_percent'] as $kkk => $vvv) {
                                                if (trim($vvv['unit']) != '') {
                                                    if ($i == 0) {
                                                        ?>
                                                        <strong><?php echo __('Cancellation: ', ST_TEXTDOMAIN); ?></strong>
                                                        <br/>
                                                        <?php
                                                    }
                                                    printf(__('- Cancellation within %s %s before the date of arrival %s will be charged;', ST_TEXTDOMAIN), $vv['deadline']['unit'], $vv['deadline']['time_unit'], $vvv['unit'] . ' ' . $vvv['text']);
                                                    echo '<br />';
                                                }
                                            }
                                            $i++;
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

    </div>
</li>