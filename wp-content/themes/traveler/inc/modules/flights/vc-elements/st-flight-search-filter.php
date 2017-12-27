<?php
if(function_exists( 'vc_map' ) && st_check_service_available('st_flight')) {
    vc_map(array(
        "name" => esc_html__("ST Flight Search Filter", ST_TEXTDOMAIN),
        "base" => "st_flight_search_filter",
        "as_parent" => array('only' => 'st_flight_filter_price,st_flight_filter_stops,st_flight_filter_departure,st_flight_filter_airlines'),
        "content_element" => true,
        "show_settings_on_create" => true,
        "js_view" => 'VcColumnView',
        "icon" => "icon-st",
        "category" => esc_html__('Flights', ST_TEXTDOMAIN),
        "params" => array(
            array(
                "type" => "textfield",
                "heading" => esc_html__("Title", ST_TEXTDOMAIN),
                "param_name" => "title",
                "description" => "",
            ),
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Style", ST_TEXTDOMAIN),
                "param_name" => "style",
                "value" => array(
                    esc_html__('--Select--', ST_TEXTDOMAIN) => '',
                    esc_html__('Dark', ST_TEXTDOMAIN) => 'dark',
                    esc_html__('Light', ST_TEXTDOMAIN) => 'light',
                ),
            ),
        )
    ));
    vc_map(array(
        "name" => esc_html__("ST Flight Filter Price", ST_TEXTDOMAIN),
        "base" => "st_flight_filter_price",
        "content_element" => true,
        "as_child" => array('only' => 'st_flight_search_filter'),
        "icon" => "icon-st",
        "params" => array(
            array(
                "type" => "textfield",
                "heading" => esc_html__("Title", ST_TEXTDOMAIN),
                "param_name" => "title",
                "description" => ""
            )
        )
    ));

    vc_map(array(
        "name" => esc_html__("ST Flight Filter Stops", ST_TEXTDOMAIN),
        "base" => "st_flight_filter_stops",
        "content_element" => true,
        "as_child" => array('only' => 'st_flight_search_filter'),
        "icon" => "icon-st",
        "params" => array(
            array(
                "type" => "textfield",
                "heading" => esc_html__("Title", ST_TEXTDOMAIN),
                "param_name" => "title",
                "description" => ""
            )
        )
    ));
    vc_map(array(
        "name" => esc_html__("ST Flight Filter Departure Time", ST_TEXTDOMAIN),
        "base" => "st_flight_filter_departure",
        "content_element" => true,
        "as_child" => array('only' => 'st_flight_search_filter'),
        "icon" => "icon-st",
        "params" => array(
            array(
                "type" => "textfield",
                "heading" => esc_html__("Title", ST_TEXTDOMAIN),
                "param_name" => "title",
                "description" => ""
            )
        )
    ));
    vc_map(array(
        "name" => esc_html__("ST Flight Filter Airlines", ST_TEXTDOMAIN),
        "base" => "st_flight_filter_airlines",
        "content_element" => true,
        "as_child" => array('only' => 'st_flight_search_filter'),
        "icon" => "icon-st",
        "params" => array(
            array(
                "type" => "textfield",
                "heading" => esc_html__("Title", ST_TEXTDOMAIN),
                "param_name" => "title",
                "description" => ""
            )
        )
    ));
}

if(class_exists( 'WPBakeryShortCodesContainer' ) and !class_exists( 'WPBakeryShortCode_st_flight_search_filter' )) {
    class WPBakeryShortCode_st_flight_search_filter extends WPBakeryShortCodesContainer
    {
        protected function content( $arg , $content = null )
        {
            $style = $title = '';
            $data = shortcode_atts( array(
                'title' => "" ,
                'style' => "" ,
            ) , $arg , 'st_flight_search_filter' );
            extract( $data );
            $content = do_shortcode( $content );
            if($style == 'dark') {
                $class_side_bar = 'booking-filters text-white';
            } else {
                $class_side_bar = 'booking-filters booking-filters-white';
            }
            $html = '<aside class="st-elements-filters ' . $class_side_bar . '">
                        <h3>' . $title . '</h3>
                        <ul class="list booking-filters-list">' . $content . '</ul>
                    </aside>';
            return $html;
        }
    }
}
if(class_exists( 'WPBakeryShortCode' ) and !class_exists( 'WPBakeryShortCode_st_flight_filter_price' )) {
    class WPBakeryShortCode_st_flight_filter_price extends WPBakeryShortCode
    {
        protected function content( $arg , $content = null )
        {
            $title = '';
            $data = shortcode_atts( array(
                'title'     => "" ,
            ) , $arg , 'st_flight_filter_price' );
            extract( $data );
            $html = '<li><h5 class="booking-filters-title">' . $title . '</h5>' . st_flight_load_view( 'vc-elements/st-flight-search-filter/filter' , 'price' ). '</li>';
            return $html;
        }
    }
}
if(class_exists( 'WPBakeryShortCode' ) and !class_exists( 'WPBakeryShortCode_st_flight_filter_stops' )) {
    class WPBakeryShortCode_st_flight_filter_stops extends WPBakeryShortCode
    {
        protected function content( $arg , $content = null )
        {
            $title = '';
            $data = shortcode_atts( array(
                'title'     => "" ,
            ) , $arg , 'st_flight_filter_stops' );
            extract( $data );
            $html = '<li><h5 class="booking-filters-title">' . $title . '</h5>' . st_flight_load_view( 'vc-elements/st-flight-search-filter/filter' , 'stops' ). '</li>';
            return $html;
        }
    }
}

if(class_exists( 'WPBakeryShortCode' ) and !class_exists( 'WPBakeryShortCode_st_flight_filter_departure' )) {
    class WPBakeryShortCode_st_flight_filter_departure extends WPBakeryShortCode
    {
        protected function content( $arg , $content = null )
        {
            $title = '';
            $data = shortcode_atts( array(
                'title'     => "" ,
            ) , $arg , 'st_flight_filter_departure' );
            extract( $data );
            $html = '<li><h5 class="booking-filters-title">' . $title . '</h5>' . st_flight_load_view( 'vc-elements/st-flight-search-filter/filter' , 'departure' ). '</li>';
            return $html;
        }
    }
}

if(class_exists( 'WPBakeryShortCode' ) and !class_exists( 'WPBakeryShortCode_st_flight_filter_airlines' )) {
    class WPBakeryShortCode_st_flight_filter_airlines extends WPBakeryShortCode
    {
        protected function content( $arg , $content = null )
        {
            $title = '';
            $data = shortcode_atts( array(
                'title'     => "" ,
            ) , $arg , 'st_flight_filter_airlines' );
            extract( $data );
            $html = '<li><h5 class="booking-filters-title">' . $title . '</h5>' . st_flight_load_view( 'vc-elements/st-flight-search-filter/filter' , 'airlines' ). '</li>';
            return $html;
        }
    }
}

