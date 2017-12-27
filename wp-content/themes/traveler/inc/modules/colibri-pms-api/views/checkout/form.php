<?php
$cba_room_type_code = STInput::get('room_code', '');
$cba_rate_plan_code = STInput::get('rate_plan', '');
$cba_room_rates = STInput::get('room_rates', '');
$cba_time_span_start = STInput::get('start', '');
$cba_time_span_end = STInput::get('end', '');
$cba_hotel_code = STInput::get('hotel_code', '');
$cba_hotel_name = STInput::get('hotel_name', '');
$cba_room_name = STInput::get('room_name', '');
$cba_code_age = STInput::get('age_code', '');
$cba_hotel_thumb = STInput::get('hotel_thumb', '');
if(isset($_GET['$hotel_code']) && isset($_GET['hotel_code']) && isset($_GET['rate_plan'])) {
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

if(is_user_logged_in()){
    //get_currentuserinfo();
    $current_user = wp_get_current_user();
    $first_name = $current_user->display_name;
    $email = $current_user->user_email;
}

?>
<form id="cba-checkout-form" class="cba-form-modal cba-form-checkout" method="post" onsubmit="return false">
    <input type="hidden" name="cba_room_type_code" id="cba_co_room_type_code" value="<?php echo $cba_room_type_code; ?>"/>
    <input type="hidden" name="cba_rate_plan_code" id="cba_co_rate_plan_code" value="<?php echo $cba_rate_plan_code; ?>"/>
    <input type="hidden" name="cba_room_rates" id="cba_co_room_rates" value="<?php echo htmlspecialchars(stripcslashes($cba_room_rates)); ?>"/>
    <input type="hidden" name="cba_time_span_start" id="cba_co_time_span_start" value="<?php echo $cba_time_span_start; ?>"/>
    <input type="hidden" name="cba_time_span_end" id="cba_co_time_span_end" value="<?php echo $cba_time_span_end; ?>"/>
    <input type="hidden" name="cba_hotel_code" id="cba_co_hotel_code" value="<?php echo $cba_hotel_code; ?>"/>
    <input type="hidden" name="cba_hotel_name" id="cba_co_hotel_name" value="<?php echo $cba_hotel_name; ?>"/>
    <input type="hidden" name="cba_room_name" id="cba_co_room_name" value="<?php echo $cba_room_name; ?>"/>
    <input type="hidden" name="cba_code_age" id="cba_co_code_age" value="<?php echo htmlspecialchars(str_replace('"', '', stripcslashes($cba_code_age))); ?>"/>
    <div class="clearfix">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group form-group-icon-left">
                    <label for="field-cba_first_name"><?php echo __('First Name', ST_TEXTDOMAIN); ?> <span
                                class="require">*</span> </label>
                    <i class="fa fa-user input-icon"></i>
                    <input class="form-control required" id="field-cba_first_name" value="<?php echo isset($first_name) ? $first_name : ''; ?>" name="cba_first_name"
                           placeholder="<?php echo __('First Name', ST_TEXTDOMAIN); ?>" type="text">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group form-group-icon-left">
                    <label for="field-cba_last_name"><?php echo __('Last Name', ST_TEXTDOMAIN); ?> <span
                                class="require">*</span> </label>
                    <i class="fa fa-user input-icon"></i>
                    <input class="form-control required" id="field-cba_last_name" value="" name="cba_last_name"
                           placeholder="<?php echo __('Last Name', ST_TEXTDOMAIN); ?>" type="text">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group form-group-icon-left">
                    <label for="field-cba_email"><?php echo __('Email', ST_TEXTDOMAIN); ?> <span
                                class="require">*</span> </label>
                    <i class="fa fa-envelope input-icon"></i>
                    <input class="form-control required" id="field-cba_email" value="<?php echo isset($email) ? $email : ''; ?>" name="cba_email"
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
                    <input class="form-control required" id="field-cba_password" value="" name="cba_password"
                           placeholder="" type="password">
                </div>
            </div>
            <?php }else{ ?>
                <input class="form-control required hidden" id="field-cba_password" value="" name="cba_password"
                       placeholder="" type="password" style="display: none;">
            <?php } ?>

            <div class="col-sm-6">
                <div class="form-group form-group-icon-left">
                    <label for="field-cba_phone"><?php echo __('Phone', ST_TEXTDOMAIN); ?> <span
                                class="require">*</span> </label>
                    <i class="fa fa-phone input-icon"></i>
                    <input class="form-control required" id="field-cba_phone" value="" name="cba_phone"
                           placeholder="<?php echo __('Your Phone', ST_TEXTDOMAIN); ?>" type="text">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group form-group-icon-left">
                    <label for="field-cba_country"><?php echo __('Country', ST_TEXTDOMAIN); ?> </label>
                    <i class="fa fa-globe input-icon"></i>
                    <input class="form-control" id="field-cba_country" value="" name="cba_country"
                           placeholder="<?php echo __('Country', ST_TEXTDOMAIN); ?>"
                           type="text">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group form-group-icon-left">
                    <label for="field-cba_city"><?php echo __('City', ST_TEXTDOMAIN); ?> </label>
                    <i class="fa fa-map-marker input-icon"></i>
                    <input class="form-control" id="field-cba_city" value="" name="cba_city"
                           placeholder="<?php echo __('Your City', ST_TEXTDOMAIN); ?>"
                           type="text">
                </div>
            </div>
            <div class="col-sm-12 cba-payment-menthod">
                <h5><?php echo __('Payment by Credit Card', ST_TEXTDOMAIN); ?></h5>
                <div class="cba-credit-card-type">
                    <input type="hidden" value="2" name="cba_card_type"/>
                    <label><?php echo __('Credit card type', ST_TEXTDOMAIN); ?></label>
                    <ul>
                        <li class="active" data-card="1" data-toggle="tooltip" data-placement="bottom"
                            title="<?php echo __('Visa', ST_TEXTDOMAIN); ?>">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/colibripms/visa.png"
                                 alt="<?php echo __('Visa', ST_TEXTDOMAIN); ?>"/>
                            <div class="cba-credit-check-wraper">
                                <div class="cba-credit-check"></div>
                                <i class="fa fa-check" aria-hidden="true"></i>
                            </div>
                        </li>
                        <li data-card="2" data-toggle="tooltip" data-placement="bottom"
                            title="<?php echo __('Master Card', ST_TEXTDOMAIN); ?>">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/colibripms/master_card.jpg"
                                 alt="<?php echo __('Master Card', ST_TEXTDOMAIN); ?>"/>
                            <div class="cba-credit-check-wraper">
                                <div class="cba-credit-check"></div>
                                <i class="fa fa-check" aria-hidden="true"></i>
                            </div>
                        </li>
                        <li data-card="3" data-toggle="tooltip" data-placement="bottom"
                            title="<?php echo __('Maestro', ST_TEXTDOMAIN); ?>">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/colibripms/card-maestro.png"
                                 alt="<?php echo __('Maestro', ST_TEXTDOMAIN); ?>"/>
                            <div class="cba-credit-check-wraper">
                                <div class="cba-credit-check"></div>
                                <i class="fa fa-check" aria-hidden="true"></i>
                            </div>
                        </li>
                        <li data-card="4" data-toggle="tooltip" data-placement="bottom"
                            title="<?php echo __('American Express', ST_TEXTDOMAIN); ?>">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/colibripms/amex.jpg"
                                 alt="<?php echo __('American Express', ST_TEXTDOMAIN); ?>"/>
                            <div class="cba-credit-check-wraper">
                                <div class="cba-credit-check"></div>
                                <i class="fa fa-check" aria-hidden="true"></i>
                            </div>
                        </li>
                        <li data-card="5" data-toggle="tooltip" data-placement="bottom"
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
                                <input value="4111111111111111" name="cba_card_number" class="form-control required" type="text"
                                       placeholder="<?php echo __('Enter your card number', ST_TEXTDOMAIN); ?>">
                            </div>
                            <div class="form-group cba-form-ds">
                                <div class="cba-form-date">
                                    <label for="">Expire Date</label>
                                    <input value="05" name="cba_card_month" class="form-control required" type="text"
                                           placeholder="MM"> /
                                    <input value="2012" name="cba_card_year" class="form-control required" type="text"
                                           placeholder="YY">
                                </div>
                                <div class="cba-form-secure">
                                    <label for="">Series Code</label>
                                    <input value="555" name="cba_card_secure_code" class="form-control required" type="ext"
                                           placeholder="___">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="cba-form-holder-name">
                                    <label for="">Card Holder Name</label>
                                    <input value="" name="cba_card_holder_fname" class="form-control required" type="text"
                                           placeholder="<?php echo __('First name', ST_TEXTDOMAIN); ?>">
                                    <input value="" name="cba_card_holder_lname" class="form-control required" type="text"
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
       class="btn btn-primary cba-btn-st-checkout-submit btn-st-big "><?php echo __('Submit', ST_TEXTDOMAIN); ?> <i
                class="fa fa-spinner fa-spin"></i></a>
    <input type="hidden" name="cba_room_thumb" id="cba_co_room_thumb" value=""/>
    <input type="hidden" name="cba_hotel_thumb" id="cba_co_hotel_thumb" value="<?php echo $cba_hotel_thumb; ?>"/>
    <input type="hidden" name="cba_currency" id="cba_currency" value=""/>
</form>