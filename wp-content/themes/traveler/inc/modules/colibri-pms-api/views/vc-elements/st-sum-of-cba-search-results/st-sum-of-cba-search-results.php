<?php
global $cldt;

wp_enqueue_script('magnific.js');

$found_posts = $cldt->found_posts;

if($found_posts == 0){
    $result_str = esc_html__('No Hotels found', ST_TEXTDOMAIN);
}else{
    $result_str = sprintf(_n('%d Hotel', '%d Hotels',(int)$cldt->found_posts, ST_TEXTDOMAIN), $cldt->found_posts);
}
$start = STInput::get('start',false);
if(!empty($start)) {
    $str_start = strtotime(TravelHelper::convertDateFormat($start));
    $result_str .= esc_html__(' from ', ST_TEXTDOMAIN) . '<strong>' . date('M d', $str_start) . '</strong>';
}
$end = STInput::get('end',false);
if(!empty($end)) {
    $str_end = strtotime(TravelHelper::convertDateFormat($end));
    $result_str .= esc_html__(' to ', ST_TEXTDOMAIN). '<strong>' . date('M d', $str_end) . '</strong>';
}
$link=st_get_link_with_search(get_permalink(),array('start','end','style', 'orderby', 'price_range'),$_GET);
?>
<input type="hidden" value="<?php echo $link; ?>" id="link_amenity"/>
<h3 class="booking-title"><?php echo $result_str; ?></h3>