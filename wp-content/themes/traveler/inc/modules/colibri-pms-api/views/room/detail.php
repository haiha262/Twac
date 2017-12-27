<?php
$style = st()->get_option('cba_room_gallery_type', 'slider');
$item_all = $item;
$item = array_values($item)[0];
foreach ($item_all as $k_r => $v_r) {
    $list_rates[$v_r['rate_plan_code']] = $v_r['rate_plan_name'];
    $list_rates_data[$v_r['rate_plan_code']] = $v_r['room_rates'];
}
//$check_los = Colibri_Helper::cl_check_min_lenght_of_stay($item['hotel_code'], $start, $end, $item['id']);
$check_los = true;

$currency_code = 'EUR';
if (!empty($item['room_rates'])) {
    $currency_code = $item['room_rates'][0]['rate'][0]['currency_code'];
}
?>
<div class="row">
    <div class="col-sm-6">
        <?php
        switch ($style) {
            case "grid":
                ?>
                <div class="row row-no-gutter popup-gallery">
                    <?php
                    foreach ($item['photos'] as $key => $value) {
                        ?>
                        <div class="col-md-3 col-xs-4">
                            <a class="hover-img popup-gallery-image"
                               href="<?php echo $value; ?>"
                               data-effect="mfp-zoom-out">

                                <img src="<?php echo $value; ?>" alt="<?php echo $item['name']; ?>"/>
                                <i class="fa fa-plus round box-icon-small hover-icon i round"></i>
                            </a>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php
                break;
            case "slider";
            default :
                ?>
                <div class="fotorama" data-allowfullscreen="true" data-nav="thumbs">
                    <?php
                    foreach ($item['photos'] as $key => $value) {
                        ?>
                        <a href="<?php echo $value; ?>">
                            <img src="<?php echo $value; ?>" alt="<?php echo $item['name']; ?>"/>
                        </a>
                        <?php
                    }
                    ?>

                </div>
                <?php
                break;
        }
        ?>
    </div>

    <div class="col-sm-6">
        <h4><?php echo $item['name']; ?></h4>
        <?php
        $number_of_days = Colibri_Helper::cl_get_number_of_day(TravelHelper::convertDateFormat($start), TravelHelper::convertDateFormat($end));
        $price = Colibri_Helper::cl_cal_price_for_room_item_opt($item_all, 1);
        echo __('Price from: ', ST_TEXTDOMAIN);
        ?>
        <span class=""><strong><?php echo Colibri_Helper::cl_format_money($price, $currency_code, 'left', false); ?></strong></span>
        <span class="booking-item-price-unit">/ <?php echo $number_of_days . ' ' . __('night(s)', ST_TEXTDOMAIN); ?> </span>
        <br/><br/>
        <ul class="booking-item-features booking-item-features-sign clearfix">
            <?php
            foreach ($item['guest_count'] as $key => $val) {
                if ($val['count'] > 0) {
                    ?>
                    <li rel="tooltip" data-placement="top" title=""
                        data-original-title="<?php echo $val['age']['text']; ?>"><i
                                class="<?php echo $val['age']['icon']; ?>"></i><span class="booking-item-feature-sign"> x <?php echo $val['count']; ?></span>
                    </li>
                    <?php
                }
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

        <?php if (!empty($item['amenity']) != ''): ?>
            <div class="cba-room-detail-desc">
                <h4><?php echo __('Amenities', ST_TEXTDOMAIN); ?></h4>
                <?php
                $te = '';
                foreach ($item['amenity'] as $k => $v) {
                    $te .= $v . ', ';
                }
                echo rtrim($te, ', ');
                ?>
            </div>
            <br/>
        <?php endif; ?>


        <?php if (trim($item['desc']) != ''): ?>
            <div class="cba-room-detail-desc">
                <h4><?php echo __('Description', ST_TEXTDOMAIN); ?></h4>
                <?php echo $item['desc']; ?>
            </div>
        <?php endif; ?>

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
</div>