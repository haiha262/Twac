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
$item_all = $item;
$item = array_values($item)[0];

$number_of_days = Colibri_Helper::cl_get_number_of_day(TravelHelper::convertDateFormat($start), TravelHelper::convertDateFormat($end));
foreach ($item_all as $k_r => $v_r) {
    $list_rates[$v_r['rate_plan_code']] = $v_r['rate_plan_name'];
    $list_rates_data[$v_r['rate_plan_code']] = $v_r['room_rates'];
}

$check_los = true;
$currency_code = 'EUR';
if (!empty($item['room_rates'])) {
    $currency_code = $item['room_rates'][0]['rate'][0]['currency_code'];
}
?>
<li itemscope itemtype="http://schema.org/Hotel">
    <div class="booking-item">
        <div class="row">
            <div class="col-md-3">
                <?php
                $link = '';
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
                <ul class="booking-item-features booking-item-features-sign clearfix">
                    <?php
                    foreach ($item['guest_count'] as $key => $val) {
                        ?>
                        <li rel="tooltip" data-placement="top" title=""
                            data-original-title="<?php echo $val['age']['text']; ?>"><i
                                    class="<?php echo $val['age']['icon']; ?>"></i><span
                                    class="booking-item-feature-sign"> x <?php echo $val['count']; ?></span>
                        </li>
                        <?php
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
                <?php
                $price = Colibri_Helper::cl_cal_price_for_room_item_opt($item_all, 1);
                ?>
                <?php echo __('Price from: ', ST_TEXTDOMAIN); ?><span
                        class="booking-item-price"><?php echo Colibri_Helper::cl_format_money($price, $currency_code, 'left', false); ?></span>
                <span class="booking-item-price-unit">/ <?php echo $number_of_days . ' ' . __('night(s)', ST_TEXTDOMAIN); ?> </span>

                <?php
                $check_out_page = st()->get_option('cba_room_checkout', 'off');
                if ($check_out_page == 'off'):
                    ?>
                    <br/>
                    <a <?php echo $item['num_of_unit'] > 0 && $check_los ? '' : ' disabled ' ?>
                            data-number-room="<?php echo $item['number_select_room']; ?>"
                            data-room-type-code="<?php echo $item['id'] ?>"
                            data-rate-plan-code="<?php echo $item['rate_plan_code'] ?>"
                            data-hotel-code="<?php echo $item['hotel_code']; ?>"
                            data-room-name="<?php echo $item['name']; ?>" class="btn btn-primary btn_cba_hotel_booking"
                            data-currency="<?php echo $currency_code; ?>"
                            data-toggle="modal"
                            data-target="#modalCBACheckOut"><?php echo __('Book', ST_TEXTDOMAIN); ?></a>
                    <?php
                endif;

                if ($check_out_page == 'on'):
                    $page_link = st()->get_option('cba_page_checkout', '');
                    $page_slug = get_post($page_link)->post_name;
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
                        <input type="hidden" class="cba-form-hotel-thumb" name="hotel_thumb" value=""/>
                        <input type="hidden" name="currency_code" value="<?php echo $currency_code; ?>"/>
                        <button <?php echo $item['num_of_unit'] > 0 && $check_los ? '' : ' disabled ' ?> type="submit"
                                                                                                         class="btn btn-primary cba-btn-checkout-page">
                            <?php echo __('Book', ST_TEXTDOMAIN); ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
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
                    $room_rate = $list_rates_data[$k];
                    $price = Colibri_Helper::cl_cal_price_for_room_item($room_rate, 1);
                    ?>
                    <tr>
                        <td class="cba-condition-push" data-hotel-code="<?php echo $item['hotel_code'] ?>"
                            data-start="<?php echo $start ?>" data-end="<?php echo $end; ?>"
                            data-rate="<?php echo $k; ?>"><i
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
                        <td colspan="3" style="position: relative;">
                            <div class="overlay-form"><i class="fa fa-refresh text-color"></i></div>
                            <div id="cba-condition-detail-content" style="display:none;"></div>
                        </td>
                    </tr>
                    <?php $i++; endforeach; ?>
            </table>
        </div>
        <!--/ End Condition /-->
    </div>
</li>