<?php
/**
 * Template Name: Colibri PMS API Detail Hotel
 */
//if (st()->get_option('cba_enable', 'off') == 'off') {
//    wp_redirect(home_url());
//    die;
//}
wp_enqueue_script('st-filter-colibri-api');

//Colibri_PMS::cl_get_rates_restric_of_hotel(STInput::get('start'), STInput::get('end'), STInput::get('id', ''));

$hotel_code = STInput::get('id', '');
$start = TravelHelper::convertDateFormatColibri(STInput::get('start'));
$end = TravelHelper::convertDateFormatColibri(STInput::get('end'));

$request_xml = '<OTA_HotelSearchRQ PrimaryLangID="eng" AltLangID="deu" Version="1.003" xmlns="http://www.opentravel.org/OTA/2003/05">
    <POS />
    <Criteria>
        <Criterion>
            <StayDateRange Start="' . $start . '" End="' . $end . '" />
        </Criterion>
    </Criteria>
</OTA_HotelSearchRQ>';

Colibri_PMS::init();
Colibri_PMS::cl_set_request_xml($request_xml);

$old_page_content = '';
while (have_posts()) {
    the_post();
    $old_page_content = get_the_content();
}
get_header();
echo st()->load_template('search-loading');
get_template_part('breadcrumb');
?>
    <div class="mfp-with-anim mfp-dialog mfp-search-dialog mfp-hide" id="search-dialog">
        <?php echo st()->load_template('hotel/search-form-2'); ?>
    </div>
    <div class="container mb20">
        <div class="booking-item-details cba-hotel-detail">
            <?php
            if (!Colibri_PMS::cl_check_authorization()) {
                echo __('Please check username or password API again.', ST_TEXTDOMAIN);
            } else {
                Colibri_PMS::cl_get_detail_hotel($hotel_code);
                Colibri_PMS::cl_get_list_rooms_of_hotel($hotel_code, $start, $end);
                if(!empty($cldt_dtht)) {
                    echo apply_filters('the_content', $old_page_content);
                }else{
                    echo '<br /><div class="alert alert-danger">' . __('No rooms available in hotel', ST_TEXTDOMAIN) . '</div>';
                }
            }
            ?>
        </div>
    </div>
<?php
wp_reset_query();
get_footer();
?>