<?php
$enable_cba = st()->get_option('cba_enable', 'off');
if($enable_cba == 'off') return;

if (function_exists('vc_map')) {
    vc_map(
        array(
            'name' => __('ST Colibri PMS Detailed Hotel Map', ST_TEXTDOMAIN),
            'base' => 'st_cba_hotel_detail_map',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => __('Colibri PMS', ST_TEXTDOMAIN),
            'show_settings_on_create' => true,
            'params' => array(
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "heading" => __("Range", ST_TEXTDOMAIN),
                    "param_name" => "range",
                    "description" => "Km",
                    "value" => "20",
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "heading" => __("Number", ST_TEXTDOMAIN),
                    "param_name" => "number",
                    "description" => "",
                    "value" => "12",
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "heading" => __("Show Circle", ST_TEXTDOMAIN),
                    "param_name" => "show_circle",
                    "description" => "",
                    "value" => array(
                        __("No", ST_TEXTDOMAIN) => "no",
                        __("Yes", ST_TEXTDOMAIN) => "yes"
                    ),
                ),
            )
        )
    );
}

if (!function_exists('st_cba_hotel_detail_map')) {
    function st_cba_hotel_detail_map($attr)
    {
        $default = array(
            'number' => '12',
            'range' => '20',
            'show_circle' => 'no',
        );

        global $cldt_dtht;
        extract(wp_parse_args($attr, $default));

        $lat = $cldt_dtht['map_lat'];
        $lng = $cldt_dtht['map_lng'];
        $zoom = 12;
        $location_center = '[' . $lat . ',' . $lng . ']';

        $data_map = array();
        $data_map[0]['id'] = $cldt_dtht['hotel_code'];
        $data_map[0]['name'] = $cldt_dtht['name'];
        $data_map[0]['post_type'] = 'CBA';
        $data_map[0]['lat'] = $lat;
        $data_map[0]['lng'] = $lng;
        $data_map[0]['icon_mk'] = get_template_directory_uri() . '/img/mk-single.png';

        $data_map[0]['content_html'] = preg_replace('/^\s+|\n|\r|\s+$/m', '', st_cba_load_view('vc-elements/st-cba-hotel-detail/st-cba-hotel-detail-map-popup', false, array('hotel_map' => $cldt_dtht)));
        //$data_map[0]['content_adv_html'] = preg_replace('/^\s+|\n|\r|\s+$/m', '', st()->load_template('vc-elements/st-list-map/loop-adv/hotel', false, array('post_type' => '')));

        //$data_map[0]['content_html'] = preg_replace('/^\s+|\n|\r|\s+$/m', '', st()->load_template('vc-elements/st-list-map/loop/hotel', false, array('post_type' => '')));
        //$data_map[0]['content_adv_html'] = preg_replace('/^\s+|\n|\r|\s+$/m', '', st()->load_template('vc-elements/st-list-map/loop-adv/hotel', false, array('post_type' => '')));

        if ($show_circle == 'no') {
            $range = 0;
        }

        $data_tmp = array(
            'location_center' => $location_center,
            'zoom' => $zoom,
            'data_map' => $data_map,
            'height' => 500,
            'style_map' => 'normal',
            'number' => $number,
            'range' => $range,
        );
        $data_tmp['data_tmp'] = $data_tmp;

        $output = st_cba_load_view('vc-elements/st-cba-hotel-detail/st-cba-hotel-detail-map', false, array('data_map' => $data_tmp));
        return $output;
    }
}

st_reg_shortcode('st_cba_hotel_detail_map', 'st_cba_hotel_detail_map');