<?php
$enable_cba = st()->get_option('cba_enable', 'off');
if($enable_cba == 'off') return;

if(function_exists( 'vc_map' )) {
    vc_map(array(
        "name" => esc_html__("ST Colibri PMS Hotel Search Filter", ST_TEXTDOMAIN),
        "base" => "st_cba_hotel_search_filter",
        "as_parent" => array('only' => 'st_cba_hotel_filter_date,st_cba_hotel_filter_price,st_cba_hotel_filter_amenity,st_cba_hotel_filter_city'),
        "content_element" => true,
        "show_settings_on_create" => true,
        "js_view" => 'VcColumnView',
        "icon" => "icon-st",
        "category" => esc_html__('Colibri PMS', ST_TEXTDOMAIN),
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
		"name" => esc_html__("ST Colibri PMS Hotel Filter Date", ST_TEXTDOMAIN),
		"base" => "st_cba_hotel_filter_date",
		"content_element" => true,
		"as_child" => array('only' => 'st_cba_hotel_search_filter'),
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
        "name" => esc_html__("ST Colibri PMS Hotel Filter Price", ST_TEXTDOMAIN),
        "base" => "st_cba_hotel_filter_price",
        "content_element" => true,
        "as_child" => array('only' => 'st_cba_hotel_search_filter'),
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
		"name" => esc_html__("ST Colibri PMS Hotel Filter City", ST_TEXTDOMAIN),
		"base" => "st_cba_hotel_filter_city",
		"content_element" => true,
		"as_child" => array('only' => 'st_cba_hotel_search_filter'),
		"icon" => "icon-st",
		"params" => array(
			array(
				"type" => "textfield",
				"heading" => esc_html__("Title", ST_TEXTDOMAIN),
				"param_name" => "title",
				"description" => ""
			),
			array(
				"type" => "dropdown",
				"heading" => esc_html__("Default Country", ST_TEXTDOMAIN),
				"param_name" => "country",
				"value" => Colibri_Helper::cl_parse_country_code()
			),
		)
	));
    vc_map(array(
        "name" => esc_html__("ST Colibri PMS Hotel Filter Amenity Code", ST_TEXTDOMAIN),
        "base" => "st_cba_hotel_filter_amenity",
        "content_element" => true,
        "as_child" => array('only' => 'st_cba_hotel_search_filter'),
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
if(class_exists( 'WPBakeryShortCodesContainer' ) and !class_exists( 'WPBakeryShortCode_st_cba_hotel_search_filter' )) {
    class WPBakeryShortCode_st_cba_hotel_search_filter extends WPBakeryShortCodesContainer
    {
        protected function content( $arg , $content = null )
        {
            $style = $title = '';
            $data = shortcode_atts( array(
                'title' => "" ,
                'style' => "" ,
            ) , $arg , 'st_cba_hotel_search_filter' );
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
if(class_exists( 'WPBakeryShortCode' ) and !class_exists( 'WPBakeryShortCode_st_cba_hotel_filter_date' )) {
	class WPBakeryShortCode_st_cba_hotel_filter_date extends WPBakeryShortCode
	{
		protected function content( $arg , $content = null )
		{
			$title = '';
			$data = shortcode_atts( array(
				'title'     => "" ,
			) , $arg , 'st_cba_hotel_filter_date' );
			extract( $data );
			$html = '<li><h5 class="booking-filters-title">' . $title . '</h5>' . st_cba_load_view( 'vc-elements/st-cba-search-filter/filter' , 'date' ). '</li>';
			return $html;
		}
	}
}
if(class_exists( 'WPBakeryShortCode' ) and !class_exists( 'WPBakeryShortCode_st_cba_hotel_filter_price' )) {
    class WPBakeryShortCode_st_cba_hotel_filter_price extends WPBakeryShortCode
    {
        protected function content( $arg , $content = null )
        {
            $title = '';
            $data = shortcode_atts( array(
                'title'     => "" ,
            ) , $arg , 'st_cba_hotel_filter_price' );
            extract( $data );
            $html = '<li><h5 class="booking-filters-title">' . $title . '</h5>' . st_cba_load_view( 'vc-elements/st-cba-search-filter/filter' , 'price' ). '</li>';
            return $html;
        }
    }
}
if(class_exists( 'WPBakeryShortCode' ) and !class_exists( 'WPBakeryShortCode_st_cba_hotel_filter_city' )) {
	class WPBakeryShortCode_st_cba_hotel_filter_city extends WPBakeryShortCode
	{
		protected function content( $arg , $content = null )
		{
			$title = '';
			$data = shortcode_atts( array(
				'title'     => "" ,
				'country'     => "" ,
			) , $arg , 'st_cba_hotel_filter_city' );
			extract( $data );
			$html = '<li><h5 class="booking-filters-title">' . $title . '</h5>' . st_cba_load_view( 'vc-elements/st-cba-search-filter/filter' , 'city', array('data' => $data)). '</li>';
			return $html;
		}
	}
}
if(class_exists( 'WPBakeryShortCode' ) and !class_exists( 'WPBakeryShortCode_st_cba_hotel_filter_amenity' )) {
    class WPBakeryShortCode_st_cba_hotel_filter_amenity extends WPBakeryShortCode
    {
        protected function content( $arg , $content = null )
        {
            $title = '';
            $data = shortcode_atts( array(
                'title'     => "" ,
            ) , $arg , 'st_cba_hotel_filter_amenity' );
            extract( $data );
            $html = '<li><h5 class="booking-filters-title">' . $title . '</h5>' . st_cba_load_view( 'vc-elements/st-cba-search-filter/filter' , 'hac' ). '</li>';
            return $html;
        }
    }
}