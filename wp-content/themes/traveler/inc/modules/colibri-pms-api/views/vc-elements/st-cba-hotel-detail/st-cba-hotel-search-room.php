<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Hotel search room
 *
 * Created by ShineTheme
 *
 */
wp_enqueue_script('bootstrap-datepicker.js');
wp_enqueue_script('bootstrap-datepicker-lang.js');

$default = array(
    'dropdown_style' => 'number',
    'style' => 'horizon'
);

global $cldt_dtr;

$adult_number = STInput::get('adult_number', 1);
$child_number = STInput::get('children_num', 0);
$room_num = STInput::get('room_num_search', 1);
$hotel_id = get_the_ID();
$booking_period = intval(get_post_meta($hotel_id, 'hotel_booking_period', TRUE));
$hotel_code = STInput::get('id', '');

if (isset($attr) and is_array($attr)) {
    extract(wp_parse_args($attr, $default));
} else {
    extract($default);
}
echo STTemplate::message();
if ($style == "vertical") {

    ?>
    <div class="search_room_alert"></div>
    <div class="booking-item-dates-change" data-booking-period="<?php echo $booking_period; ?>">
        <div class="overlay-form"><i class="fa fa-refresh text-color"></i></div>
        <form>
            <input type="hidden" name="is_search_room" value="true">
            <input type="hidden" name="paged_room" class="paged_room" value="1">
            <?php wp_nonce_field('room_search', 'room_search') ?>
            <input type="hidden" name="action" value="ajax_search_room">
            <div class="input-daterange" data-date-format="<?php echo TravelHelper::getDateFormatJs(); ?>">
                <div class="form-group form-group-icon-left">
                    <label for="field-hotel-start"><?php st_the_language('check_in') ?></label>
                    <i class="fa fa-calendar input-icon input-icon-hightlight"></i>
                    <input readonly id="field-cba-hotel-checkin" data-post-id="<?php echo get_the_ID(); ?>"
                           placeholder="<?php echo TravelHelper::getDateFormatJs(__("Select date", ST_TEXTDOMAIN)); ?>"
                           class="form-control checkin_hotel" value="<?php echo STInput::get('start') ?>" name="start"
                           type="text">
                </div>
                <div class="form-group form-group-icon-left">
                    <label for="field-hotel-end"><?php st_the_language('check_out') ?></label>
                    <i class="fa fa-calendar input-icon input-icon-hightlight"></i>
                    <input readonly id="field-cba-hotel-checkout" data-post-id="<?php echo get_the_ID(); ?>"
                           placeholder="<?php echo TravelHelper::getDateFormatJs(__("Select date", ST_TEXTDOMAIN)); ?>"
                           value="<?php echo STInput::get('end') ?>" class="form-control checkout_hotel" name="end"
                           type="text">
                </div>
            </div>
            <!-- Select Rate Plan -->
            <!--<div class="form-group form-group-select-plus">
                <label for="field-hotel-rate"><?php //_e('Rate Plan', ST_TEXTDOMAIN) ?></label>
                <select name="rack_plan" class="form-control" id="field-hotel-rate">
                    <?php
                    //$list_rates = Colibri_Helper::cl_get_list_rate_plan($cldt_dtr);
                    //foreach ($list_rates as $k_rate => $v_rate) {
                    //    echo '<option value="' . $k_rate . '">' . __($v_rate, ST_TEXTDOMAIN) . '</option>';
                    //}
                    ?>
                </select>
            </div>
            <!-- Select rooms number -->
            <div class="form-group form-group-select-plus cba-select-rooms">
                <label for="field-hotel-room"><?php _e('Room(s)', ST_TEXTDOMAIN) ?></label>
                <?php if ($dropdown_style == 'number'): ?>
                    <div class="btn-group btn-group-select-num <?php echo ($room_num > 3) ? 'hidden' : false ?>"
                         data-toggle="buttons">
                        <label class="btn btn-primary <?php echo ($room_num == 1) ? 'active' : false ?>">
                            <input type="radio" value="1" name="adult_num_opt">1</label>
                        <label class="btn btn-primary <?php echo ($room_num == 2) ? 'active' : false ?>">
                            <input type="radio" value="2" name="adult_num_opt">2</label>
                        <label class="btn btn-primary <?php echo ($room_num == 3) ? 'active' : false ?>">
                            <input type="radio" value="3" name="adult_num_opt">3</label>
                        <label class="btn btn-primary">
                            <input type="radio" value="4" name="adult_num_opt">3+</label>
                    </div>
                <?php endif; ?>
                <?php
                $max_room = Colibri_Helper::cl_cal_number_max_room($cldt_dtr);
                ?>
                <select id="field-hotel-room" name="room_num_search"
                        class=" form-control <?php if ($dropdown_style == 'number' and $room_num < 4) echo "hidden"; ?>">
                    <?php
                    for ($i = 1; $i <= $max_room; $i++) {
                        $select = selected($i, $room_num);
                        echo '<option ' . $select . ' value="' . $i . '">' . $i . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div id="cba-age-select">
            <?php
            $list_aqc = Colibri_Helper::cl_parse_aqc_code('', true);

            $list_guest_count = Colibri_Helper::cl_cal_guest_count($cldt_dtr);

            foreach ($list_guest_count as $key => $val):
                if ($val['count'] > 0):
                    ?>

                    <!-- Number of peoples -->
                    <div class="form-group form-group-select-plus cba-select-people" data-age-code = "<?php echo $val['code']; ?>">
                        <label for="field-hotel-adult"><?php echo __($val['text'], ST_TEXTDOMAIN); ?></label>
                        <div class="btn-group btn-group-select-num " data-toggle="buttons">
                            <label class="btn btn-primary active">
                                <input type="radio" value="1" name="adult_num_opt">1</label>
                            <label class="btn btn-primary ">
                                <input type="radio" value="2" name="adult_num_opt">2</label>
                            <label class="btn btn-primary ">
                                <input type="radio" value="3" name="adult_num_opt">3</label>
                            <label class="btn btn-primary">
                                <input type="radio" value="4" name="adult_num_opt">3+</label>
                        </div>
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
                                $select = selected($i, $room_num);
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

            <div class="text-right">
                <a href="#" class="btn btn-primary" data-hotel-code="<?php echo $hotel_code; ?>"
                   id="cba-search-room"><?php st_the_language('search') ?></a>
            </div>
        </form>
    </div>
    <?php
}
if ($style == 'horizon') {
    ?>
    <div class="search_room_alert "></div>
    <div class="booking-item-dates-change" data-booking-period="<?php echo $booking_period; ?>">
        <div class="overlay-form"><i class="fa fa-refresh text-color"></i></div>
        <form>
            <input type="hidden" name="is_search_room" value="true">
            <input type="hidden" name="paged_room" class="paged_room" value="1">
            <?php wp_nonce_field('room_search', 'room_search') ?>
            <input type="hidden" name="action" value="ajax_search_room">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-daterange" data-date-format="<?php echo TravelHelper::getDateFormatJs(); ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-icon-left">
                                    <label for="field-hotel-checkin"><?php st_the_language('check_in') ?></label>
                                    <i class="fa fa-calendar input-icon input-icon-hightlight"></i>
                                    <input readonly id="field-hotel-checkin" data-post-id="<?php echo get_the_ID(); ?>"
                                           placeholder="<?php echo TravelHelper::getDateFormatJs(__("Select date", ST_TEXTDOMAIN)); ?>"
                                           class="form-control checkin_hotel"
                                           value="<?php echo STInput::get('start') ?>" name="start" type="text">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-group-icon-left">
                                    <label for="field-hotel-checkout"><?php st_the_language('check_out') ?></label>
                                    <i class="fa fa-calendar input-icon input-icon-hightlight"></i>
                                    <input readonly id="field-hotel-checkout" data-post-id="<?php echo get_the_ID(); ?>"
                                           placeholder="<?php echo TravelHelper::getDateFormatJs(__("Select date", ST_TEXTDOMAIN)); ?>"
                                           value="<?php echo STInput::get('end') ?>" class="form-control checkout_hotel"
                                           name="end" type="text">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <?php $room_num_search = STInput::get('room_num_search'); ?>
                    <div class="form-group form-group-select-plus cba-select-rooms">
                        <label for="field-hotel-rooms"><?php echo __('Room(s)', ST_TEXTDOMAIN); ?></label>
                        <?php if ($dropdown_style == 'number' and $room_num_search < 4): ?>
                            <div class="btn-group btn-group-select-num" data-toggle="buttons">
                                <label
                                        class="btn btn-primary <?php echo (!$room_num_search or $room_num_search == 1) ? 'active' : false; ?>">
                                    <input type="radio" value="1">1</label>
                                <label
                                        class="btn btn-primary <?php echo ($room_num_search == 2) ? 'active' : false; ?>">
                                    <input type="radio" value="2">2</label>
                                <label
                                        class="btn btn-primary <?php echo ($room_num_search == 3) ? 'active' : false; ?>">
                                    <input type="radio" value="3">3</label>
                                <label class="btn btn-primary ">
                                    <input type="radio" value="4">3+</label>
                            </div>
                        <?php endif; ?>
                        <select id="field-hotel-rooms" name="room_num_search"
                                class="form-control <?php if ($dropdown_style == 'number' and $room_num_search < 4) echo "hidden"; ?>">
                            <?php
                            $max_room = 5;
                            for ($i = 1; $i <= $max_room; $i++) {

                                echo '<option ' . selected($room_num_search, $i, false) . ' value="' . $i . '">' . $i . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <a href="#" class="btn btn-primary" id="cba-search-room"><?php st_the_language('search') ?></a>
            </div>
        </form>
    </div>
    <?php
} ?>
<?php if ($style == 'style_3') { ?>
    <div class="search_room_alert "></div>
    <div class="booking-item-dates-change" data-booking-period="<?php echo $booking_period; ?>">
        <div class="overlay-form"><i class="fa fa-refresh text-color"></i></div>
        <form>
            <input type="hidden" name="is_search_room" value="true">
            <input type="hidden" name="paged_room" class="paged_room" value="1">
            <?php wp_nonce_field('room_search', 'room_search') ?>
            <input type="hidden" name="action" value="ajax_search_room">
            <div class="row">
                <div class="col-md-12">
                    <div class="input-daterange" data-date-format="<?php echo TravelHelper::getDateFormatJs(); ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-icon-left">
                                    <label for="field-hotel-checkin"><?php st_the_language('check_in') ?></label>
                                    <i class="fa fa-calendar input-icon input-icon-hightlight"></i>
                                    <input readonly id="field-hotel-checkin" data-post-id="<?php echo get_the_ID(); ?>"
                                           placeholder="<?php echo TravelHelper::getDateFormatJs(__("Select date", ST_TEXTDOMAIN)); ?>"
                                           class="form-control checkin_hotel"
                                           value="<?php echo STInput::get('start') ?>" name="start" type="text">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-group-icon-left">
                                    <label for="field-hotel-checkout"><?php st_the_language('check_out') ?></label>
                                    <i class="fa fa-calendar input-icon input-icon-hightlight"></i>
                                    <input readonly id="field-hotel-checkout" data-post-id="<?php echo get_the_ID(); ?>"
                                           placeholder="<?php echo TravelHelper::getDateFormatJs(__("Select date", ST_TEXTDOMAIN)); ?>"
                                           value="<?php echo STInput::get('end') ?>" class="form-control checkout_hotel"
                                           name="end" type="text">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?php $room_num_search = STInput::get('room_num_search'); ?>
                                <div class="form-group form-group-select-plus">
                                    <label for="field-hotel-rooms"><?php echo __('Room(s)', ST_TEXTDOMAIN); ?></label>
                                    <select name="room_num_search" class="form-control">
                                        <?php
                                        $max_room = 5;
                                        for ($i = 1; $i <= $max_room; $i++) {
                                            echo '<option ' . selected($room_num_search, $i, false) . ' value="' . $i . '">' . $i . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-group-select-plus">
                                    <label for="field-hotel-adult">&nbsp</label>
                                    <a href="#" class="btn btn-primary"
                                       id="cba-search-room"><?php st_the_language('search') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}
?>
<br>
