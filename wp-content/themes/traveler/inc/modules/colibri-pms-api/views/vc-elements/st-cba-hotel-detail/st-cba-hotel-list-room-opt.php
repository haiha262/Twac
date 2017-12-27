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

            foreach($cldt_dtr as $key => $item)
            {
                $arr[$item['id']][$key] = $item;
            }
            ksort($arr, SORT_NUMERIC);
            foreach ($arr as $key => $val) {
                echo st_cba_load_view('vc-elements/st-cba-hotel-detail/st-cba-hotel-list-room-item-opt', false, array('item' => $val, 'start' => $start, 'end' => $end));
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
