<?php
/**
 * Created by wpbooking.
 * Developer: nasanji
 * Date: 6/26/2017
 * Version: 1.0
 */
if(!function_exists('st_vc_flight_destinations') && function_exists('vc_map') && function_exists('st_reg_shortcode') && st_check_service_available('st_flight')){
    function st_vc_flight_destinations($atts, $content = false){
        $atts = shortcode_atts(array(
            'st_ids' => '',
            'column' => 'col-md-3'
        ), $atts);

        $html = st_flight_load_view('vc-elements/st-flight-destinations/st-flight-destinations', false, array('atts' => $atts));

        return $html;
    }

    st_reg_shortcode('st_flight_destinations', 'st_vc_flight_destinations');

    vc_map(array(
        'name' => esc_html__('ST Flight Destinations', ST_TEXTDOMAIN),
        'base' => 'st_flight_destinations',
        'icon' => 'icon-st',
        'category' => esc_html__('Flights', ST_TEXTDOMAIN),
        'params' => array(
            array(
                "type" => "st_post_type_location",
                "heading" => __("List IDs in Location", ST_TEXTDOMAIN),
                "param_name" => "st_ids",
                "description" =>__("Ids separated by commas",ST_TEXTDOMAIN),
                'value'=>"",
            ),
            array(
                'type' => 'dropdown',
                'param_name' => 'column',
                'admin_label' => true,
                'heading' => esc_html__('No Of Columns', ST_TEXTDOMAIN),
                'description' => esc_html__('Choose column to display element', ST_TEXTDOMAIN),
                'value' => array(
                    esc_html__('3 columns', ST_TEXTDOMAIN) => 'col-md-4',
                    esc_html__('4 columns', ST_TEXTDOMAIN) => 'col-md-3'
                ),
                'std' => 'col-md-3'
            )
        )
    ));
}
