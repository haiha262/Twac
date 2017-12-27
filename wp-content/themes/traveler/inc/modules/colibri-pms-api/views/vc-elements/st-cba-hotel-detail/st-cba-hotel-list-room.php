<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Hotel loop room
 *
 * Created by ShineTheme
 *
 */

global $cldt_dtr;
//$start = TravelHelper::convertDateFormat(STInput::get('start', ''));
//$end = TravelHelper::convertDateFormat(STInput::get('end', ''));
$start = STInput::get('start', '');
$end = STInput::get('end', '');
?>
<div class="cba-list-room-of-hotel">
    <div class="overlay-form"><i class="fa fa-refresh text-color"></i></div>
    <ul class="booking-list loop-room">

        <?php
        if (!empty($cldt_dtr)) {
            $unique_room = [];
            foreach ($cldt_dtr as $key => $value) {
                $key_unique = $value['id'];
                $unique_room[$key_unique] = $value;
            }

            foreach ($unique_room as $key => $val) {
                //if($val['rate_plan_code'] == $default_rate)
                    echo st_cba_load_view('vc-elements/st-cba-hotel-detail/st-cba-hotel-list-room-item', false, array('item' => $val, 'start' => $start, 'end' => $end));
            }
        } else {
            echo st_cba_load_view('vc-elements/st-cba-hotel-detail/st-cba-hotel-list-room-none', false);
        }
        ?>
    </ul>
    <div class="div_paged_room">
        <?php //echo Colibri_Helper::pag(); ?>
    </div>
</div>
