<?php
/**
 * Created by wpbooking.
 * Developer: nasanji
 * Date: 6/16/2017
 * Version: 1.0
 */

if(!function_exists('st_vc_flight_search_results_ajax') && st_check_service_available('st_flight') && function_exists('vc_map') && function_exists('st_reg_shortcode')) {
    function st_vc_flight_search_results_ajax($atts, $content = false)
    {
        $atts = shortcode_atts(array(
            'extra_class' => ''
        ), $atts);

        $output = st_flight_load_view('vc-elements/st-flight-search-results-ajax/st-flight-search-results-ajax', false, array('atts' => $atts));

        return $output;
    }


    st_reg_shortcode('st_flight_search_results_ajax', 'st_vc_flight_search_results_ajax');

    vc_map(array(
        'name' => esc_html__('[Ajax] ST Flight Search Result', ST_TEXTDOMAIN),
        'base' => 'st_flight_search_results_ajax',
        'icon' => 'icon-st',
        'category' => esc_html__('Flights', ST_TEXTDOMAIN),
        'params' => array(
            array(
                'type' => 'textfield',
                'heading' => esc_html__('Extra Class', ST_TEXTDOMAIN),
                'param_name' => 'extra_class'
            )
        )
    ));
}