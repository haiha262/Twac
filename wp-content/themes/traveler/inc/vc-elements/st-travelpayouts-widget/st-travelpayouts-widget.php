<?php
/**
 * Created by wpbooking.
 * Developer: nasanji
 * Date: 5/15/2017
 * Version: 1.0
 */
if(function_exists('vc_map')){
    vc_map( array(
        "name" => esc_html__("ST TravelPayouts API Widget", ST_TEXTDOMAIN),
        "base" => "st_tp_widgets",
        "content_element" => true,
        'description' => esc_html__('Get widgets from TravelPayouts API', ST_TEXTDOMAIN),
        "icon" => "icon-st",
        'category' => 'Shinetheme',
        "params" => array(
            // add params same as with any other content element
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Widget Type", ST_TEXTDOMAIN),
                "param_name" => "widget_type",
                'admin_label' => true,
                "description" => esc_html__('Select a widget type', ST_TEXTDOMAIN),
                'value' => array(
                    esc_html__('Widget popular routes', ST_TEXTDOMAIN) => 'popular-router',
                    esc_html__('Flights Map', ST_TEXTDOMAIN) => 'flights-map',
                    esc_html__('Hotels Map', ST_TEXTDOMAIN) => 'hotels-map',
                    esc_html__('Calendar Widget', ST_TEXTDOMAIN) => 'calendar',
                    esc_html__('Hotel Widget', ST_TEXTDOMAIN) => 'hotel',
                    esc_html__('Hotels Selections', ST_TEXTDOMAIN) => 'hotel-selections',
                ),
                'std' => 'popular-router'
            ),
            array(
                "type" => "st_tp_locations",
                "heading" => esc_html__("Default Origin", ST_TEXTDOMAIN),
                "param_name" => "pr_origin",
                "description" =>esc_html__('Find a origin', ST_TEXTDOMAIN),
                'location_type' => 'flight',
                'dependency' => array(
                    'element' => 'widget_type',
                    'value' => array('calendar')
                )
            ),
            array(
                "type" => "st_tp_locations",
                "heading" => esc_html__("Default Destination", ST_TEXTDOMAIN),
                "param_name" => "pr_destination",
                "description" =>esc_html__('Find a destination', ST_TEXTDOMAIN),
                'location_type' => 'flight',
                'dependency' => array(
                    'element' => 'widget_type',
                    'value' => array('popular-router','flights-map','calendar')
                )
            ),
            array(
                "type" => "st_tp_locations",
                "heading" => esc_html__("Hotel", ST_TEXTDOMAIN),
                "param_name" => "hotel_id",
                "description" =>esc_html__('Find a hotel', ST_TEXTDOMAIN),
                'location_type' => 'hotel_id',
                'dependency' => array(
                    'element' => 'widget_type',
                    'value' => array('hotel')
                )
            ),
            array(
                "type" => "st_tp_locations",
                "heading" => esc_html__("Location", ST_TEXTDOMAIN),
                "param_name" => "map_lat_lon",
                "description" =>esc_html__('Find a location', ST_TEXTDOMAIN),
                'location_type' => 'hotel_map',
                'dependency' => array(
                    'element' => 'widget_type',
                    'value' => array('hotels-map')
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Language', ST_TEXTDOMAIN),
                'param_name' => 'language',
                'description' => esc_html__('Select a language', ST_TEXTDOMAIN),
                'dependency' => array(
                    'element' => 'widget_type',
                    'value' => array('popular-router','hotel','hotels-map')
                ),
                'value' => array(
                    esc_html__('Russian', ST_TEXTDOMAIN) => 'ru',
                    esc_html__('English (Great Britan)', ST_TEXTDOMAIN) => 'en',
                    esc_html__('Thai', ST_TEXTDOMAIN) => 'th',
                ),
                'std' => 'en'
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Locale', ST_TEXTDOMAIN),
                'param_name' => 'language1',
                'description' => esc_html__('Select a locale', ST_TEXTDOMAIN),
                'dependency' => array(
                    'element' => 'widget_type',
                    'value' => array('flights-map')
                ),
                'value' => array(
                    esc_html__('Deutsch (DE)', ST_TEXTDOMAIN) => 'de',
                    esc_html__('English', ST_TEXTDOMAIN) => 'en',
                    esc_html__('French', ST_TEXTDOMAIN) => 'fr',
                    esc_html__('Italian', ST_TEXTDOMAIN) => 'it',
                    esc_html__('Russian', ST_TEXTDOMAIN) => 'ru',
                    esc_html__('Thai', ST_TEXTDOMAIN) => 'th',
                ),
                'std' => 'en'
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Language', ST_TEXTDOMAIN),
                'param_name' => 'language2',
                'description' => esc_html__('Select a language', ST_TEXTDOMAIN),
                'dependency' => array(
                    'element' => 'widget_type',
                    'value' => array('calendar')
                ),
                'value' => array(
                    esc_html__('Deutsch (DE)', ST_TEXTDOMAIN) => 'de',
                    esc_html__('English', ST_TEXTDOMAIN) => 'en',
                    esc_html__('French', ST_TEXTDOMAIN) => 'fr',
                    esc_html__('Italian', ST_TEXTDOMAIN) => 'it',
                    esc_html__('Russian', ST_TEXTDOMAIN) => 'ru',
                    esc_html__('Thai', ST_TEXTDOMAIN) => 'th',
                    esc_html__('Spanish', ST_TEXTDOMAIN) => 'es',
                    esc_html__('Chinese', ST_TEXTDOMAIN) => 'zh',
                    esc_html__('Brazilian', ST_TEXTDOMAIN) => 'br',
                    esc_html__('Japanese', ST_TEXTDOMAIN) => 'ja',
                    esc_html__('Portuguese', ST_TEXTDOMAIN) => 'pt',
                    esc_html__('Polish', ST_TEXTDOMAIN) => 'pl',
                ),
                'std' => 'en'
            ),
            // Hotel selection
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Widget\'s layout', ST_TEXTDOMAIN),
                'param_name' => 'w_layout',
                'value' => array(
                    esc_html__('Full', ST_TEXTDOMAIN) => 'full',
                    esc_html__('Compact', ST_TEXTDOMAIN) => 'compact'
                ),
                'std' => 'full',
                'dependency' => array(
                    'element' => 'widget_type',
                    'value' => array('hotel-selections')
                ),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Language', ST_TEXTDOMAIN),
                'param_name' => 'language3',
                'description' => esc_html__('Select a language', ST_TEXTDOMAIN),
                'dependency' => array(
                    'element' => 'widget_type',
                    'value' => array('hotel-selections')
                ),
                'value' => array(
                    esc_html__('Deutsch (DE)', ST_TEXTDOMAIN) => 'de',
                    esc_html__('English', ST_TEXTDOMAIN) => 'en',
                    esc_html__('French', ST_TEXTDOMAIN) => 'fr',
                    esc_html__('Italian', ST_TEXTDOMAIN) => 'it',
                    esc_html__('Russian', ST_TEXTDOMAIN) => 'ru',
                    esc_html__('Thai', ST_TEXTDOMAIN) => 'th',
                    esc_html__('Chinese', ST_TEXTDOMAIN) => 'zh',
                    esc_html__('Japanese', ST_TEXTDOMAIN) => 'ja',
                ),
                'std' => 'en'
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Max hotels in list', ST_TEXTDOMAIN),
                'param_name' => 'limit',
                'value' => array(
                    esc_html__('1', ST_TEXTDOMAIN) => '1',
                    esc_html__('2', ST_TEXTDOMAIN) => '2',
                    esc_html__('3', ST_TEXTDOMAIN) => '3',
                    esc_html__('4', ST_TEXTDOMAIN) => '4',
                    esc_html__('5', ST_TEXTDOMAIN) => '5',
                    esc_html__('6', ST_TEXTDOMAIN) => '6',
                    esc_html__('7', ST_TEXTDOMAIN) => '7',
                    esc_html__('8', ST_TEXTDOMAIN) => '8',
                    esc_html__('9', ST_TEXTDOMAIN) => '9',
                    esc_html__('10', ST_TEXTDOMAIN) => '10'
                ),
                'std' => '10',
                'dependency' => array(
                    'element' => 'widget_type',
                    'value' => array('hotel-selections')
                ),
            ),
            array(
                'type' => 'checkbox',
                'heading' => esc_html__('Direct Flights Only', ST_TEXTDOMAIN),
                'param_name' => 'direct',
                'value' => array(
                    esc_html__('Yes', ST_TEXTDOMAIN) => 'yes'
                ),
                'dependency' => array(
                    'element' => 'widget_type',
                    'value' => array('flights-map')
                ),
                'std' => 'yes'
            ),
            array(
                "type" => "textfield",
                "heading" => esc_html__("Additional marker", ST_TEXTDOMAIN),
                "param_name" => "add_marker",
                'dependency' => array(
                    'element' => 'widget_type',
                    'value' => array('hotel','hotels-map','hotel-selections')
                )
            ),
            array(
                'type' => 'checkbox',
                'heading' => esc_html__('Map Controls', ST_TEXTDOMAIN),
                'param_name' => 'map_control',
                'value' => array(
                    esc_html__('Draggable', ST_TEXTDOMAIN) => 'drag',
                    esc_html__('Disable zoom', ST_TEXTDOMAIN) => 'disable_zoom',
                    esc_html__('Scroll wheel', ST_TEXTDOMAIN) => 'scroll',
                    esc_html__('Map styled', ST_TEXTDOMAIN) => 'map_styled'
                ),
                'std' => 'drag',
                'dependency' => array(
                    'element' => 'widget_type',
                    'value' => array('hotels-map')
                )
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__('Map Zoom', ST_TEXTDOMAIN),
                'param_name' => 'map_zoom',
                'value' => '12',
                'std' => '12',
                'dependency' => array(
                    'element' => 'widget_type',
                    'value' => array('hotels-map')
                )
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__('Marker Size', ST_TEXTDOMAIN),
                'param_name' => 'marker_size',
                'value' => '16',
                'std' => '16',
                'dependency' => array(
                    'element' => 'widget_type',
                    'value' => array('hotels-map')
                )
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Color Schema', ST_TEXTDOMAIN),
                'param_name' => 'color_schema',
                'value' => '#00b1dd',
                'std' => '#00b1dd',
                'dependency' => array(
                    'element' => 'widget_type',
                    'value' => array('hotels-map')
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Find By', ST_TEXTDOMAIN),
                'param_name' => 'find_by',
                'value' => array(
                    esc_html__('Hotels', ST_TEXTDOMAIN) => 'hotels',
                    esc_html__('City', ST_TEXTDOMAIN) => 'city'
                ),
                'std' => 'city',
                'dependency' => array(
                    'element' => 'widget_type',
                    'value' => array('hotel-selections')
                ),
            ),
            array(
                "type" => "st_tp_locations",
                "heading" => esc_html__("City", ST_TEXTDOMAIN),
                "param_name" => "city_data",
                "description" =>esc_html__('Find a city', ST_TEXTDOMAIN),
                'location_type' => 'city',
                'dependency' => array(
                    'element' => 'find_by',
                    'value' => array('city')
                )
            ),
            array(
                'type' => 'param_group',
                'heading' => esc_html__('List Hotels', ST_TEXTDOMAIN),
                'param_name' => 'list_hotel',
                'value' => '',
                'params' => array(
                    array(
                        "type" => "st_tp_locations",
                        "heading" => esc_html__("Hotel", ST_TEXTDOMAIN),
                        "param_name" => "s_hotel_id",
                        'location_type' => 'hotel_id'
                    ),
                ),
                'callbacks' => array(
                    'after_add' => 'vcChartParamAfterAddCallback'
                ),
                'dependency' => array(
                    'element' => 'find_by',
                    'value' => array('hotels')
                ),
            )

        )
    ) );
}

if(!function_exists('st_vc_tp_widgets')){
    function st_vc_tp_widgets($arg, $content = false)
    {
        $output = $widget_type = $pr_destination = $language = $language1 = $find_by = $list_hotel = $city_data = $direct = $color_schema = $w_layout = $limit = $pr_origin = $language2 = $language3 = $add_marker = $hotel_id = $map_lat_lon = $map_control = $map_zoom = $marker_size = '';
        $data = shortcode_atts(array(
            'widget_type' => 'popular-router',
            'pr_destination'=>'',
            'language' => 'en',
            'language1' => 'en',
            'language2' => 'en',
            'language3' => 'en',
            'direct' => 'yes',
            'pr_origin' => '',
            'add_marker' => '',
            'hotel_id' => '',
            'map_lat_lon' => '',
            'map_control' => 'drag',
            'map_zoom' => 12,
            'marker_size' =>16,
            'color_schema' => '#00b1dd',
            'w_layout' => 'full',
            'limit' => '10',
            'find_by' => 'city',
            'list_hotel' => '',
            'city_data' => ''
        ), $arg );
        extract($data);

        parse_str(urldecode($pr_destination),$destination);

        parse_str(urldecode($pr_origin),$origin);

        parse_str(urldecode($hotel_id),$ho_id);

        parse_str(urldecode($map_lat_lon),$hotel_map);

        parse_str(urldecode($city_data),$city);

        $output .= '<div class="st_travelpayouts_widgets">';
        $output .= st()->load_template('vc-elements/st-travelpayouts-widget/tp-widget', null, array(
            'widget_type' => $widget_type,
            'pr_destination' => $destination,
            'language' => $language,
            'language1' => $language1,
            'direct' => $direct,
            'pr_origin' => $origin,
            'language2' => $language2,
            'add_marker' => $add_marker,
            'hotel_id' => $ho_id,
            'map_lat_lon' => $hotel_map,
            'map_control' => $map_control,
            'map_zoom' => $map_zoom,
            'marker_size' => $marker_size,
            'color_schema' => $color_schema,
            'language3' => $language3,
            'w_layout' => $w_layout,
            'limit' => $limit,
            'find_by' => $find_by,
            'list_hotel' => $list_hotel,
            'city_data' => $city
        ));
        $output .= '</div>';

        return $output;
    }
}
st_reg_shortcode('st_tp_widgets','st_vc_tp_widgets');