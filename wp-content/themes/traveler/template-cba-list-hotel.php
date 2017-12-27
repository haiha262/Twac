<?php
/**
 * Template Name: Colibri PMS API List Hotel
 */
if(st()->get_option('cba_enable', 'off') == 'off') {
	wp_redirect( home_url() );
	die;
}
$start = TravelHelper::convertDateFormatColibri(STInput::get('start'));
$end = TravelHelper::convertDateFormatColibri(STInput::get('end'));
$amenity = STInput::get('amenity');
$request_amenity_xml = '';
if(trim($amenity) != ''){
    $arr_amenity = explode(',', $amenity);
    if(!empty($arr_amenity)) {
        foreach ($arr_amenity as $key_amen => $val_amen) {
            $request_amenity_xml .= '<HotelAmenity Code="' . $val_amen . '" />';
        }
    }
}

$city_code = STInput::get('city_code', '');
$request_city_code_xml = '';
if(trim($city_code) != ''){
    $arr_city_code = explode(',', $city_code);
    if(!empty($arr_city_code)){
        foreach($arr_city_code as $key_ctc => $val_ctc){
            $request_city_code_xml .= '<HotelRef HotelCityCode="'. $val_ctc .'" />';
        }
    }
}

$request_xml = '<OTA_HotelSearchRQ PrimaryLangID="eng" AltLangID="deu" Version="1.003" xmlns="http://www.opentravel.org/OTA/2003/05">
    <POS />
    <Criteria>
        <Criterion>
            <StayDateRange Start="' . $start . '" End="' . $end . '" />
            '. $request_city_code_xml .'
            '. $request_amenity_xml .'
        </Criterion>
    </Criteria>
</OTA_HotelSearchRQ>';

Colibri_PMS::init();
Colibri_PMS::cl_set_request_xml($request_xml);

$old_page_content = '';
while( have_posts() ) {
    the_post();
    $st_search_page_id = get_the_ID();
    $old_page_content  = get_the_content();
}
get_header();
echo st()->load_template( 'search-loading' );
get_template_part( 'breadcrumb' );
$result_string = '';
?>
    <div class="mfp-with-anim mfp-dialog mfp-search-dialog mfp-hide" id="search-dialog">
        <?php echo st()->load_template( 'hotel/search-form-2' ); ?>
    </div>
    <div class="container mb20">
        <?php
        if(!Colibri_PMS::cl_check_authorization()){
            echo __('Please check username or password API again.', ST_TEXTDOMAIN);
        }else{
            Colibri_PMS::cl_get_list_hotels();
            echo apply_filters( 'the_content' , $old_page_content );
        }
        ?>
    </div>
<?php
wp_reset_query();
get_footer();
?>