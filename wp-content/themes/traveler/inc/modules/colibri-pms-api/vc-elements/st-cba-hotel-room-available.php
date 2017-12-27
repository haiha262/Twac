<?php
$enable_cba = st()->get_option('cba_enable', 'off');
if($enable_cba == 'off') return;

if (function_exists('vc_map')) {
    vc_map(
        array(
            'name' => __('ST Colibri PMS Hotel Rooms Available', ST_TEXTDOMAIN),
            'base' => 'st_cba_hotel_detail_search_room',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => __('Colibri PMS', ST_TEXTDOMAIN),
            'show_settings_on_create' => true,
            'params' => array(
                array(
                    "type" => "textfield",
                    "admin_label" => true,
                    "heading" => __("Title", ST_TEXTDOMAIN),
                    "param_name" => "title",
                    "description" => "",
                    "value" => "",
                    'edit_field_class' => 'vc_col-sm-6',
                ),
                array(
                    "type" => "dropdown",
                    "admin_label" => true,
                    "heading" => __("Font Size", ST_TEXTDOMAIN),
                    "param_name" => "font_size",
                    "description" => "",
                    "value" => array(
                        __('--Select--', ST_TEXTDOMAIN) => '',
                        __("H1", ST_TEXTDOMAIN) => '1',
                        __("H2", ST_TEXTDOMAIN) => '2',
                        __("H3", ST_TEXTDOMAIN) => '3',
                        __("H4", ST_TEXTDOMAIN) => '4',
                        __("H5", ST_TEXTDOMAIN) => '5',
                    ),
                    'edit_field_class' => 'vc_col-sm-6',
                ),
                array(
                    "type" => "dropdown",
                    "heading" => __("Style", ST_TEXTDOMAIN),
                    "param_name" => "style",
                    "description" => "",
                    "value" => array(
                        __("Horizontal", ST_TEXTDOMAIN) => 'horizon',
                        __("Vertical", ST_TEXTDOMAIN) => 'vertical',
                        __("Vertical 2", ST_TEXTDOMAIN) => 'style_3',
                    ),
                    'edit_field_class' => 'vc_col-sm-6',
                ),
            )
        )
    );
}

if (!function_exists('st_cba_hotel_detail_search_room')) {
    function st_cba_hotel_detail_search_room($attr = array())
    {
        $default = array(
            'title' => '',
            'font_size' => '3',
            'style' => 'horizon'
        );
        extract(wp_parse_args($attr, $default));
        $html = st_cba_load_view('vc-elements/st-cba-hotel-detail/st-cba-hotel-search-room', false, array('attr' => $attr));
        if (!empty($title) and !empty($html)) {
            $html = '<h' . $font_size . '>' . $title . '</h' . $font_size . '>' . $html;
        }
        return $html;
    }
}

st_reg_shortcode('st_cba_hotel_detail_search_room', 'st_cba_hotel_detail_search_room');