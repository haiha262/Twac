<?php
if (isset($_GET['$hotel_code']) && isset($_GET['hotel_code']) && isset($_GET['rate_plan'])) {
    $room_list = Colibri_PMS::cl_rt_get_list_rooms_of_hotel($hotel_code, $start, $end, $rate_plan);
    $item = [];
    foreach ($room_list as $k => $v) {
        if ($v['id'] == $room_code) {
            $item = $v;
        }
    }
    $price = Colibri_Helper::cl_cal_price_for_room_item($item['room_rates'], 1);
    $price = $price * 1;
}

if (is_user_logged_in()) {
    $current_user = wp_get_current_user();
    $first_name = $current_user->display_name;
    $email = $current_user->user_email;
}

/**
 * Setup data for popup
 */
$data_booking = json_decode($data->data);
$data_res = json_decode($data->data_res);
$data_user = json_decode($data->data_user);
$data_card = json_decode($data->data_card);
?>
<div class="row">
    <div class="col-lg-8">
        <form id="cba-checkout-form-modify" class="cba-form-modal cba-form-checkout" method="post" onsubmit="return false">
            <input type="hidden" name="cba_room_type_code" id="cba_co_room_type_code"
                   value="<?php echo $data_booking->room_code; ?>"/>
            <input type="hidden" name="cba_rate_plan_code" id="cba_co_rate_plan_code"
                   value="[1201,1584]"/>
            <input type="hidden" name="cba_room_rates" id="cba_co_room_rates"
                   value="<?php echo htmlspecialchars(stripcslashes($data_booking->room_rates)); ?>"/>
            <input type="hidden" name="cba_time_span_start" id="cba_co_time_span_start"
                   value="<?php echo $data_booking->start; ?>"/>
            <input type="hidden" name="cba_time_span_end" id="cba_co_time_span_end"
                   value="<?php echo $data_booking->end; ?>"/>
            <input type="hidden" name="cba_hotel_code" id="cba_co_hotel_code"
                   value="<?php echo $data_booking->hotel_code; ?>"/>
            <input type="hidden" name="cba_hotel_name" id="cba_co_hotel_name"
                   value="<?php echo $data_booking->hotel_name; ?>"/>
            <input type="hidden" name="cba_room_name" id="cba_co_room_name"
                   value="<?php echo $data_booking->room_name; ?>"/>
            <input type="hidden" name="cba_code_age" id="cba_co_code_age"
                   value="<?php echo htmlspecialchars(str_replace('"', '', stripcslashes($data_booking->age_code))); ?>"/>
            <div class="clearfix">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group form-group-icon-left">
                            <label for="field-cba_first_name"><?php echo __('First Name', ST_TEXTDOMAIN); ?> <span
                                        class="require">*</span> </label>
                            <i class="fa fa-user input-icon"></i>
                            <input class="form-control required" id="field-cba_first_name"
                                   value="<?php echo $data_user->first_name; ?>" name="cba_first_name"
                                   placeholder="<?php echo __('First Name', ST_TEXTDOMAIN); ?>" type="text">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group form-group-icon-left">
                            <label for="field-cba_last_name"><?php echo __('Last Name', ST_TEXTDOMAIN); ?> <span
                                        class="require">*</span> </label>
                            <i class="fa fa-user input-icon"></i>
                            <input class="form-control required" id="field-cba_last_name" value="<?php echo $data_user->last_name; ?>" name="cba_last_name"
                                   placeholder="<?php echo __('Last Name', ST_TEXTDOMAIN); ?>" type="text">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group form-group-icon-left">
                            <label for="field-cba_email"><?php echo __('Email', ST_TEXTDOMAIN); ?> <span
                                        class="require">*</span> </label>
                            <i class="fa fa-envelope input-icon"></i>
                            <input class="form-control required" id="field-cba_email"
                                   value="<?php echo $data_user->email; ?>" name="cba_email"
                                   placeholder="<?php echo __('email@domain.com', ST_TEXTDOMAIN); ?>" type="text">
                        </div>
                    </div>

                    <!-- Password -->
                    <?php if (!is_user_logged_in()) { ?>
                        <div class="col-sm-6">
                            <div class="form-group form-group-icon-left">
                                <label for="field-cba_password"><?php echo __('Password', ST_TEXTDOMAIN); ?> <span
                                            class="require">*</span> </label>
                                <i class="fa fa-lock input-icon"></i>
                                <input class="form-control required" id="field-cba_password" value=""
                                       name="cba_password"
                                       placeholder="" type="password">
                            </div>
                        </div>
                    <?php } else { ?>
                        <input class="form-control required hidden" id="field-cba_password" value="" name="cba_password"
                               placeholder="" type="password" style="display: none;">
                    <?php } ?>

                    <div class="col-sm-6">
                        <div class="form-group form-group-icon-left">
                            <label for="field-cba_phone"><?php echo __('Phone', ST_TEXTDOMAIN); ?> <span
                                        class="require">*</span> </label>
                            <i class="fa fa-phone input-icon"></i>
                            <input class="form-control required" id="field-cba_phone" value="<?php echo $data_user->phone; ?>" name="cba_phone"
                                   placeholder="<?php echo __('Your Phone', ST_TEXTDOMAIN); ?>" type="text">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group form-group-icon-left">
                            <label for="field-cba_country"><?php echo __('Country', ST_TEXTDOMAIN); ?> </label>
                            <i class="fa fa-globe input-icon"></i>
                            <input class="form-control" id="field-cba_country" value="<?php echo $data_user->country; ?>" name="cba_country"
                                   placeholder="<?php echo __('Country', ST_TEXTDOMAIN); ?>"
                                   type="text">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group form-group-icon-left">
                            <label for="field-cba_city"><?php echo __('City', ST_TEXTDOMAIN); ?> </label>
                            <i class="fa fa-map-marker input-icon"></i>
                            <input class="form-control" id="field-cba_city" value="<?php echo $data_user->city; ?>" name="cba_city"
                                   placeholder="<?php echo __('Your City', ST_TEXTDOMAIN); ?>"
                                   type="text">
                        </div>
                    </div>
                    <div class="col-sm-12 cba-payment-menthod">
                        <?php $card_type = $data_card->card_type; ?>
                        <h5><?php echo __('Payment by Credit Card', ST_TEXTDOMAIN); ?></h5>
                        <div class="cba-credit-card-type">
                            <input type="hidden" value="2" name="cba_card_type"/>
                            <label><?php echo __('Credit card type', ST_TEXTDOMAIN); ?></label>
                            <ul>
                                <li class="<?php echo $card_type == '1' ? 'active' : ''; ?>" data-card="1" data-toggle="tooltip" data-placement="bottom"
                                    title="<?php echo __('Visa', ST_TEXTDOMAIN); ?>">
                                    <img src="<?php echo get_template_directory_uri(); ?>/img/colibripms/visa.png"
                                         alt="<?php echo __('Visa', ST_TEXTDOMAIN); ?>"/>
                                    <div class="cba-credit-check-wraper">
                                        <div class="cba-credit-check"></div>
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </div>
                                </li>
                                <li class="<?php echo $card_type == '2' ? 'active' : ''; ?>" data-card="2" data-toggle="tooltip" data-placement="bottom"
                                    title="<?php echo __('Master Card', ST_TEXTDOMAIN); ?>">
                                    <img src="<?php echo get_template_directory_uri(); ?>/img/colibripms/master_card.jpg"
                                         alt="<?php echo __('Master Card', ST_TEXTDOMAIN); ?>"/>
                                    <div class="cba-credit-check-wraper">
                                        <div class="cba-credit-check"></div>
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </div>
                                </li>
                                <li class="<?php echo $card_type == '3' ? 'active' : ''; ?>" data-card="3" data-toggle="tooltip" data-placement="bottom"
                                    title="<?php echo __('Maestro', ST_TEXTDOMAIN); ?>">
                                    <img src="<?php echo get_template_directory_uri(); ?>/img/colibripms/card-maestro.png"
                                         alt="<?php echo __('Maestro', ST_TEXTDOMAIN); ?>"/>
                                    <div class="cba-credit-check-wraper">
                                        <div class="cba-credit-check"></div>
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </div>
                                </li>
                                <li class="<?php echo $card_type == '4' ? 'active' : ''; ?>" data-card="4" data-toggle="tooltip" data-placement="bottom"
                                    title="<?php echo __('American Express', ST_TEXTDOMAIN); ?>">
                                    <img src="<?php echo get_template_directory_uri(); ?>/img/colibripms/amex.jpg"
                                         alt="<?php echo __('American Express', ST_TEXTDOMAIN); ?>"/>
                                    <div class="cba-credit-check-wraper">
                                        <div class="cba-credit-check"></div>
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </div>
                                </li>
                                <li class="<?php echo $card_type == '5' ? 'active' : ''; ?>" data-card="5" data-toggle="tooltip" data-placement="bottom"
                                    title="<?php echo __('Dinners Club', ST_TEXTDOMAIN); ?>">
                                    <img src="<?php echo get_template_directory_uri(); ?>/img/colibripms/diner_club.jpg"
                                         alt="<?php echo __('Dinners Club', ST_TEXTDOMAIN); ?>"/>
                                    <div class="cba-credit-check-wraper">
                                        <div class="cba-credit-check"></div>
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="cba-credit-card-form">
                            <div class="row">
                                <div class="col-sm-7">
                                    <div class="form-group">
                                        <label for="">Card number </label>
                                        <input value="4111111111111111" name="cba_card_number"
                                               class="form-control required"
                                               type="text"
                                               placeholder="<?php echo __('Enter your card number', ST_TEXTDOMAIN); ?>">
                                    </div>
                                    <div class="form-group cba-form-ds">
                                        <div class="cba-form-date">
                                            <label for="">Expire Date</label>
                                            <input value="05" name="cba_card_month" class="form-control required"
                                                   type="text"
                                                   placeholder="MM"> /
                                            <input value="2012" name="cba_card_year" class="form-control required"
                                                   type="text"
                                                   placeholder="YY">
                                        </div>
                                        <div class="cba-form-secure">
                                            <label for="">Series Code</label>
                                            <input value="555" name="cba_card_secure_code" class="form-control required"
                                                   type="ext"
                                                   placeholder="___">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="cba-form-holder-name">
                                            <label for="">Card Holder Name</label>
                                            <input value="<?php echo $data_card->card_holder_fname; ?>" name="cba_card_holder_fname" class="form-control required"
                                                   type="text"
                                                   placeholder="<?php echo __('First name', ST_TEXTDOMAIN); ?>">
                                            <input value="<?php echo $data_card->card_holder_lname; ?>" name="cba_card_holder_lname" class="form-control required"
                                                   type="text"
                                                   placeholder="<?php echo __('Last name', ST_TEXTDOMAIN); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label></label>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (!is_user_logged_in()) { ?>
                <div class="checkbox st_check_create_account">
                    <label>
                        <input class="i-check" checked disabled type="checkbox" value="1" name="create_account">
                        Create <?php echo get_bloginfo(); ?> account
                    </label>
                </div>
            <?php } ?>
            <div class="checkbox cba_st_check_term_conditions">
                <label>
                    <input class="i-check" type="checkbox" value="1" name="">
                    I have read and accept the<a target="_blank" href="#"> terms
                        and conditions</a> </label>
            </div>
            <div class="alert form_alert hidden"></div>
            <a href="#" onclick="return false"
               class="btn btn-primary cba-btn-st-checkout-submit-modify btn-st-big "><?php echo __('Submit', ST_TEXTDOMAIN); ?>
                <i
                        class="fa fa-spinner fa-spin" style="display: none"></i></a>
            <input type="hidden" name="cba_room_thumb" id="cba_co_room_thumb"
                   value="<?php echo $data_booking->room_thumb; ?>"/>
            <input type="hidden" name="cba_hotel_thumb" id="cba_co_hotel_thumb"
                   value="<?php echo $data_booking->hotel_thumb; ?>"/>
            <input type="hidden" name="cba_reservation_id" id="cba_co_reservation_id"
                   value="<?php echo $data_res->res_value; ?>"/>
            <input type="hidden" name="cba_reservation_type" id="cba_co_reservation_type"
                   value="<?php echo $data_res->res_type; ?>"/>
            <input type="hidden" name="cba_res_id" id="cba_co_res_id"
                   value="<?php echo $data->id; ?>"/>
        </form>
    </div>
    <div class="col-lg-4">
        <?php
        $room_code = $data_booking->room_code;
        $hotel_code = $data_booking->hotel_code;
        $start = $data_booking->start;
        $end = $data_booking->end;
        $rate_plan = $data_booking->room_code;
        $room_list = Colibri_PMS::cl_rt_get_list_rooms_of_hotel($hotel_code, $start, $end);
        $item = [];
        foreach ($room_list as $k => $v) {
            if ($v['id'] == $room_code) {
                $item = $v;
            }
        }
        ?>
        <div class="booking-item-payment">
            <header class="clearfix" style="position: relative">
                <a class="booking-item-payment-img" href="#">
                    <img width="98" height="74" src="<?php echo $item['thumb']; ?>" alt="<?php echo $item['name']; ?>"/>
                </a>
                <h5 class="booking-item-payment-title"><a><?php echo $item['name']; ?></a>
                </h5>
                <!--<ul class="icon-group booking-item-rating-stars">
                    <li><i class="fa  fa-star"></i></li>
                    <li><i class="fa  fa-star"></i></li>
                    <li><i class="fa  fa-star"></i></li>
                    <li><i class="fa  fa-star"></i></li>
                    <li><i class="fa  fa-star-o"></i></li>
                </ul>
                <h5 class="booking-item-payment-title"><i class="fa fa-map-marker mr5"></i> Avenue of the
                    Americas, New York, NY, United States</h5>-->
            </header>
            <ul class="booking-item-payment-details">
                <li>
                    <h5><?php echo __('Old info', ST_TEXTDOMAIN); ?></h5>
                    <ul class="booking-item-payment-price">
                        <!--<?php
                        foreach ($item['guest_count'] as $key => $val) {
                            if ($val['count'] > 0) {
                                ?>
                        <li>
                            <p class="booking-item-payment-price-title">
	                            <?php echo $val['age']['text']; ?> </p>
                            <p class="booking-item-payment-price-amount">
	                            <?php echo $val['count']; ?> </p>
                        </li>
						<?php
                            }
                        }
                        ?>
                <?php if ($item['bed'] != '') { ?>
                    <li>
                        <p class="booking-item-payment-price-title">
	                        <?php echo __('Number room', ST_TEXTDOMAIN); ?> </p>
                        <p class="booking-item-payment-price-amount">
				            <?php echo $item['bed']; ?> </p>
                    </li>
	            <?php } ?>

	            <?php if ($item['num_of_unit'] != '') { ?>
                    <li>
                        <p class="booking-item-payment-price-title">
				            <?php echo __('Bedroom', ST_TEXTDOMAIN); ?> </p>
                        <p class="booking-item-payment-price-amount">
				            <?php echo $item['num_of_unit']; ?> </p>
                    </li>
	            <?php } ?>-->

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
                </li>
            </ul>
            <br />
            <div class="col-lg-12">
                <div class="input-daterange cba-modify-booking-date" data-date-format="<?php echo TravelHelper::getDateFormatJs(); ?>">

                    <div class="form-group form-group-icon-left">
                        <label for="field-hotel-adult"><?php echo __('Arrival', ST_TEXTDOMAIN); ?></label>
                        <i class="fa fa-calendar input-icon input-icon-hightlight"></i>
                        <input readonly id="field-cba-hotel-checkin" data-post-id="<?php echo get_the_ID(); ?>"
                               placeholder="<?php echo TravelHelper::getDateFormatJs(__("Arrival date", ST_TEXTDOMAIN)); ?>"
                               class="form-control checkin_hotel" value="<?php echo $start; ?>" name="start"
                               type="text">
                    </div>
                    <div class="form-group form-group-icon-left">
                        <label for="field-hotel-adult"><?php echo __('Departure', ST_TEXTDOMAIN); ?></label>
                        <i class="fa fa-calendar input-icon input-icon-hightlight"></i>
                        <input readonly id="field-cba-hotel-checkout" data-post-id="<?php echo get_the_ID(); ?>"
                               placeholder="<?php echo TravelHelper::getDateFormatJs(__("Departure date", ST_TEXTDOMAIN)); ?>"
                               value="<?php echo $end; ?>" class="form-control checkout_hotel" name="end"
                               type="text">
                    </div>
                </div>

                <div id="cba-age-select-modify">
                    <?php
                    $list_aqc = Colibri_Helper::cl_parse_aqc_code('', true);
                    $list_guest_count = Colibri_Helper::cl_cal_guest_count($room_list);
                    $data_age_code = explode(',', rtrim($data_booking->age_code, ','));
                    $data_age_code_arr = [];
                    if(!empty($data_age_code)){
                        foreach ($data_age_code as $k_ac => $v_ac){
                            $sub_arr = explode('|', $v_ac);
                            $data_age_code_arr[$sub_arr[0]] = $sub_arr[1];
                        }
                    }
                    foreach ($list_guest_count as $key => $val):
                        $select_pos = Colibri_Helper::cl_get_has_age_code($data_age_code_arr, $val['code']);
                        if ($val['count'] > 0):
                            ?>

                            <!-- Number of peoples -->
                            <div class="form-group form-group-select-plus cba-select-people" data-age-code = "<?php echo $val['code']; ?>">
                                <label for="field-hotel-adult"><?php echo __($val['text'], ST_TEXTDOMAIN); ?></label>
                                <?php
                                $max_people = 5;
                                if($val['count'] > 5){
                                    $max_people = $val['count'];
                                }
                                //$max_room = Colibri_Helper::cl_cal_number_max_people($cldt_dtr);
                                ?>
                                <select id="field-hotel-people-<?php echo $val['code']; ?>" name="room_num_search"
                                        class=" form-control <?php if ($dropdown_style == 'number' and $room_num < 4) echo "hidden"; ?>">
                                    <?php
                                    for ($i = 1; $i <= $max_people; $i++) {
                                        $select = selected($i, $select_pos);
                                        echo '<option ' . $select . ' value="' . $i . '">' . $i . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <?php
                        endif;
                    endforeach;
                    ?>
                </div>
                <button class="btn btn-primary btn-sm" id="load_rates_data" style="display: none"><?php echo __('Load Rates Data', ST_TEXTDOMAIN); ?></button>
            </div>

            <?php
            $rooms_rates = json_decode(stripcslashes($data_booking->room_rates));
            ?>

            <input type="hidden" value="<?php echo $hotel_code; ?>" id="load_hotel_code" />
            <input type="hidden" value="<?php echo $room_code; ?>" id="load_room_code" />
            <input type="hidden" value="<?php echo htmlspecialchars(stripcslashes($data_booking->room_rates)); ?>" id="load_room_rates" />




            <div id="ccond">
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
                        $curr_post = Colibri_Helper::cl_get_has_rate_pos($rooms_rates, $k);
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
                                                //if ($i == 0) {
                                                if ($nr == $curr_post) {
                                                    $select = ' selected ';
                                                }
                                                //}
                                                echo '<option data-base-price="'. $price .'" data-price="' . $nr * $price . '" ' . $select . ' value="' . $nr . '">' . $nr . ' (' . Colibri_Helper::cl_format_money($nr * $price, 'EUR', 'left', '', 2) . ')</option>';
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
                                    <?php if (!$check_los): ?>
                                        <div class="alert alert-danger">
                                            <?php echo __('Minimal Length of Stay is bigger than selected date range', ST_TEXTDOMAIN); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php
                                    $rates = Colibri_PMS::cl_get_rates($item['hotel_code'], $start, $end, $k);
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
            </div>

        </div>
    </div>
</div>