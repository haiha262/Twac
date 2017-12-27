<?php
$link = st_get_link_with_search(get_permalink(), array('start', 'end', 'room_num_search', 'adult_number'), $_GET);
$thumb_url = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));

$check_in = '';
$check_out = '';
if(!isset($_REQUEST['start']) || empty($_REQUEST['start'])){
    $check_in = date('m/d/Y', strtotime("now"));
}else{
    $check_in = TravelHelper::convertDateFormat(STInput::request('start'));
}

if(!isset($_REQUEST['end']) || empty($_REQUEST['end'])){
    $check_out = date('m/d/Y', strtotime("+1 day"));
}else{
    $check_out = TravelHelper::convertDateFormat(STInput::request('end'));
}
$numberday = STDate::dateDiff($check_in, $check_out);

if(!isset($taxonomy)){
    $taxonomy = '';
}

$page_link = st()->get_option('cba_page_detail_hotel', '');
$page_slug = get_post($page_link)->post_name;
$thumb_url = $atts['hotel_thumb'];
$start = STInput::get('start', '');
$end = STInput::get('end', '');
?>
<div class="col-md-4">
    <div class="thumb">
        <header class="thumb-header">
            <a class="hover-img" href="<?php echo get_site_url() . '/' . $page_slug . '?id=' . $atts['hotel_code'] . '&start=' . $start . '&end=' . $end; ?>">
                <?php
                if ($thumb_url != '') {
                    echo '<img src="' . $thumb_url . '" />';
                } else {
                    if (!empty($atts['hotel_photos'])) {
                        echo '<img src="' . $atts['hotel_photos'][0] . '" />';
                    } else {
                        echo st_get_default_image();
                    }
                }
                ?>
                <h5 class="hover-title-center"><?php st_the_language('book_now')?> </h5>
            </a>
        </header>

        <div class="thumb-caption">
                <ul class="icon-list icon-group booking-item-rating-stars">
                    <span class="pull-left mr10"><?php echo __('Hotel star', ST_TEXTDOMAIN); ?></span>
                    <?php
                    echo  TravelHelper::rate_to_string($atts['rating']);
                    ?>
                </ul>


            <h5 class="thumb-title"><a class="text-darken"
                                       href="<?php echo get_site_url() . '/' . $page_slug . '?id=' . $atts['hotel_code'] . '&start=' . $start . '&end=' . $end; ?>"><?php echo $atts['hotel_name']; ?></a></h5>

                <p class="mb0">
                    <small><i class="fa fa-map-marker"></i> <?php echo esc_html($atts['address_line'] . ', ' . $atts['city_name'] . ', ' . $atts['country']); ?></small>
                </p>

            <p class="mb0 text-darken">
                <small>
                        <?php _e("Price from", ST_TEXTDOMAIN) ?>
                </small>
                <span class="text-lg lh1em"><?php echo Colibri_Helper::cl_format_money($atts['min_price'], $atts['currency_code'], 'left', '', 2); ?></span>
            </p>
        </div>

    </div>
</div>