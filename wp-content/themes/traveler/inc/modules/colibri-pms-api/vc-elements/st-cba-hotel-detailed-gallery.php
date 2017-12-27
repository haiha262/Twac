<?php
$enable_cba = st()->get_option('cba_enable', 'off');
if($enable_cba == 'off') return;

if (function_exists('vc_map')) {
    vc_map(array(
        "name" => __("ST Colibri PMS Detailed Hotel Gallery", ST_TEXTDOMAIN),
        "base" => "st_cba_hotel_detail_photo",
        "content_element" => true,
        "icon" => "icon-st",
        "category" => __('Colibri PMS', ST_TEXTDOMAIN),
        "params" => array(
            array(
                "type" => "dropdown",
                "holder" => "div",
                "heading" => __("Style", ST_TEXTDOMAIN),
                "param_name" => "style",
                "description" => "",
                "value" => array(
                    __('--Select--', ST_TEXTDOMAIN) => '',
                    __('Slide', ST_TEXTDOMAIN) => 'slide',
                    __('Grid', ST_TEXTDOMAIN) => 'grid',
                ),
            )
        )
    ));
}

if (!function_exists('st_cba_hotel_detail_photo')) {
    function st_cba_hotel_detail_photo($attr, $content = false)
    {
        $default = array(
            'style' => 'slide'
        );
        $attr = wp_parse_args($attr, $default);

        $output = st_cba_load_view('vc-elements/st-cba-hotel-detail/st-cba-hotel-gallery', false, array('attr' => $attr));
        return $output;
    }
}

st_reg_shortcode('st_cba_hotel_detail_photo', 'st_cba_hotel_detail_photo');
