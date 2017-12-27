<?php
$enable_cba = st()->get_option('cba_enable', 'off');
if($enable_cba == 'off') return;

if(!function_exists('st_vc_sum_of_cba_hotel_search_result') && function_exists('vc_map') && function_exists('st_reg_shortcode')){
    function st_vc_sum_of_cba_hotel_search_result($atts, $content = false){
        $atts = shortcode_atts(array(
            'extra_class' => '',
        ), $atts);

        $html = st_cba_load_view('vc-elements/st-sum-of-cba-search-results/st-sum-of-cba-search-results', false, array('atts' => $atts));

        return $html;
    }

    st_reg_shortcode('st_sum_of_cba_hotel_search_result', 'st_vc_sum_of_cba_hotel_search_result');

    vc_map(array(
        'name' => esc_html__('ST Sum Of Colibri PMS Hotel Search Results', ST_TEXTDOMAIN),
        'base' => 'st_sum_of_cba_hotel_search_result',
        'icon' => 'icon-st',
        'category' => esc_html__('Colibri PMS', ST_TEXTDOMAIN),
        'params' => array(
            array(
                "type" => "textfield",
                "heading" => __("Extra Class", ST_TEXTDOMAIN),
                "param_name" => "extra_class",
                'value'=>"",
            )
        )
    ));
}
