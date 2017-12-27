<?php
$default = array(
    'align' => 'right'
);

extract($default);
global $cldt_dtht;
$price = $cldt_dtht['min_price'];
$start = TravelHelper::convertDateFormat(STInput::get('start', ''));
$end = TravelHelper::convertDateFormat(STInput::get('end', ''));
$number_days = Colibri_Helper::cl_get_number_of_day($start, $end);
$unit = __('nights', ST_TEXTDOMAIN);
if($number_days == 1){
    $unit = __('night', ST_TEXTDOMAIN);
}
?>
<p class="booking-item-header-price text-<?php echo esc_html($align) ?>">
    <small><?php _e("price from", ST_TEXTDOMAIN) ?></small>
    <span class="text-lg"><?php echo Colibri_Helper::cl_format_money($price, $cldt_dtht['currency_code'], 'left', '', 2); ?></span>/<?php echo $number_days . ' ' . $unit; ?>
</p>