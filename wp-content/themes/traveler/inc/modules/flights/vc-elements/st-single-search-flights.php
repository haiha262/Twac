<?php
/**
 * Created by wpbooking.
 * Developer: nasanji
 * Date: 6/14/2017
 * Version: 1.0
 */

if(!function_exists('st_vc_single_search_flights') && function_exists('vc_map') && function_exists('st_reg_shortcode') && st_check_service_available('st_flight')){
    function st_vc_single_search_flights($atts, $content = false){
        $atts = shortcode_atts(array(
            'title' => '',
            'style' => 'default',
            'search_type' => 'both',
            'box_shadow' => 'no'
        ), $atts);

        $html = st_flight_load_view('vc-elements/st-single-search-flights/st-single-search-flights', false, array('atts' => $atts));

        return $html;
    }

    st_reg_shortcode('st_single_search_flights', 'st_vc_single_search_flights');

    vc_map(array(
        'name' => esc_html__('ST Single Search Flights', ST_TEXTDOMAIN),
        'base' => 'st_single_search_flights',
        'icon' => 'icon-st',
        'category' => esc_html__('Flights', ST_TEXTDOMAIN),
        'params' => array(
            array(
                'type' => 'textfield',
                'param_name' => 'title',
                'heading' => esc_html__('Title Form', ST_TEXTDOMAIN),
                'admin_label' => true,
                'description' => esc_html__('Add a text for title form', ST_TEXTDOMAIN)
            ),
            array(
                'type' => 'dropdown',
                'param_name' => 'style',
                'admin_label' => true,
                'heading' => esc_html__('Style', ST_TEXTDOMAIN),
                'description' => esc_html__('Choose a style', ST_TEXTDOMAIN),
                'value' => array(
                    esc_html__('Default', ST_TEXTDOMAIN) => 'default',
                    esc_html__('Small', ST_TEXTDOMAIN) => 'small'
                ),
                'std' => 'default'
            ),
            array(
                'type' => 'dropdown',
                'param_name' => 'search_type',
                'heading' => esc_html__('Search Type', ST_TEXTDOMAIN),
                'value' => array(
                    esc_html__('One-Way', ST_TEXTDOMAIN) => 'one_way',
                    esc_html__('Return', ST_TEXTDOMAIN) => 'return',
                    esc_html__('Both', ST_TEXTDOMAIN) => 'both',
                ),
                'std' => 'both'
            ),
            array(
                'type' => 'dropdown',
                'param_name' => 'box_shadow',
                'heading' => esc_html__('Show Box Shadow', ST_TEXTDOMAIN),
                'value' => array(
                    esc_html__('No', ST_TEXTDOMAIN) => 'no',
                    esc_html__('Yes', ST_TEXTDOMAIN) => 'yes',
                ),
                'std' => 'no'
            ),
        )
    ));
}

