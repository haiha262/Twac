<?php
if(function_exists( 'vc_map' ) && st_check_service_available('st_flight')) {
    vc_map(array(
        "name" => esc_html__("[Ajax] ST Flight Search Filter", ST_TEXTDOMAIN),
        "base" => "st_flight_search_filter_ajax",
        "as_parent" => array('only' => 'st_flight_filter_price_ajax,st_flight_filter_stops_ajax,st_flight_filter_departure_ajax,st_flight_filter_airlines_ajax'),
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
        "name" => esc_html__("[Ajax] ST Flight Filter Price", ST_TEXTDOMAIN),
        "base" => "st_flight_filter_price_ajax",
        "content_element" => true,
        "as_child" => array('only' => 'st_flight_search_filter_ajax'),
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
        "name" => esc_html__("[Ajax] ST Flight Filter Stops", ST_TEXTDOMAIN),
        "base" => "st_flight_filter_stops_ajax",
        "content_element" => true,
        "as_child" => array('only' => 'st_flight_search_filter_ajax'),
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
        "name" => esc_html__("[Ajax] ST Flight Filter Departure Time", ST_TEXTDOMAIN),
        "base" => "st_flight_filter_departure_ajax",
        "content_element" => true,
        "as_child" => array('only' => 'st_flight_search_filter_ajax'),
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
        "name" => esc_html__("[Ajax] ST Flight Filter Airlines", ST_TEXTDOMAIN),
        "base" => "st_flight_filter_airlines_ajax",
        "content_element" => true,
        "as_child" => array('only' => 'st_flight_search_filter_ajax'),
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

if(class_exists( 'WPBakeryShortCodesContainer' ) and !class_exists( 'WPBakeryShortCode_st_flight_search_filter_ajax' )) {
    class WPBakeryShortCode_st_flight_search_filter_ajax extends WPBakeryShortCodesContainer
    {
        protected function content( $arg , $content = null )
        {
            $style = $title = '';
            $data = shortcode_atts( array(
                'title' => "" ,
                'style' => "" ,
            ) , $arg , 'st_flight_search_filter_ajax' );
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
if(class_exists( 'WPBakeryShortCode' ) and !class_exists( 'WPBakeryShortCode_st_flight_filter_price_ajax' )) {
    class WPBakeryShortCode_st_flight_filter_price_ajax extends WPBakeryShortCode
    {
        protected function content( $arg , $content = null )
        {
            $title = '';
            $data = shortcode_atts( array(
                'title'     => "" ,
            ) , $arg , 'st_flight_filter_price_ajax' );
            extract( $data );
            $html = '<li><h5 class="booking-filters-title">' . $title . '</h5>' . st_flight_load_view( 'vc-elements/st-flight-search-filter-ajax/filter' , 'price' ). '</li>';
            return $html;
        }
    }
}
if(class_exists( 'WPBakeryShortCode' ) and !class_exists( 'WPBakeryShortCode_st_flight_filter_stops_ajax' )) {
    class WPBakeryShortCode_st_flight_filter_stops_ajax extends WPBakeryShortCode
    {
        protected function content( $arg , $content = null )
        {
            $title = '';
            $data = shortcode_atts( array(
                'title'     => "" ,
            ) , $arg , 'st_flight_filter_stops_ajax' );
            extract( $data );
            $html = '<li><h5 class="booking-filters-title">' . $title . '</h5>' . st_flight_load_view( 'vc-elements/st-flight-search-filter-ajax/filter' , 'stops' ). '</li>';
            return $html;
        }
    }
}

if(class_exists( 'WPBakeryShortCode' ) and !class_exists( 'WPBakeryShortCode_st_flight_filter_departure_ajax' )) {
    class WPBakeryShortCode_st_flight_filter_departure_ajax extends WPBakeryShortCode
    {
        protected function content( $arg , $content = null )
        {
            $title = '';
            $data = shortcode_atts( array(
                'title'     => "" ,
            ) , $arg , 'st_flight_filter_departure_ajax' );
            extract( $data );
            $html = '<li><h5 class="booking-filters-title">' . $title . '</h5>' . st_flight_load_view( 'vc-elements/st-flight-search-filter-ajax/filter' , 'departure' ). '</li>';
            return $html;
        }
    }
}

if(class_exists( 'WPBakeryShortCode' ) and !class_exists( 'WPBakeryShortCode_st_flight_filter_airlines_ajax' )) {
    class WPBakeryShortCode_st_flight_filter_airlines_ajax extends WPBakeryShortCode
    {
        protected function content( $arg , $content = null )
        {
            $title = '';
            $data = shortcode_atts( array(
                'title'     => "" ,
            ) , $arg , 'st_flight_filter_airlines_ajax' );
            extract( $data );
            $html = '<li><h5 class="booking-filters-title">' . $title . '</h5>' . st_flight_load_view( 'vc-elements/st-flight-search-filter-ajax/filter' , 'airlines' ). '</li>';
            return $html;
        }
    }
}

