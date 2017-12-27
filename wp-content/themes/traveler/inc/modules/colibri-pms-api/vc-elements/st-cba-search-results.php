<?php
$enable_cba = st()->get_option('cba_enable', 'off');
if($enable_cba == 'off') return;

if(function_exists( 'vc_map' )) {
    //$list = st_list_taxonomy( 'st_hotel' );
    //$txt  = __( '--Select--' , ST_TEXTDOMAIN );
    //unset( $list[ $txt ] );
    vc_map( array(
        "name"            => __( "ST Colibri PMS Hotel Search Result" , ST_TEXTDOMAIN ) ,
        "base"            => "st_cba_search_results" ,
        "content_element" => true ,
        "icon"            => "icon-st" ,
        "category"        => esc_html__('Colibri PMS', ST_TEXTDOMAIN),
        "params"          => array(
            array(
                "type"        => "dropdown" ,
                "holder"      => "div" ,
                "heading"     => __( "Style" , ST_TEXTDOMAIN ) ,
                "param_name"  => "style" ,
                "description" => "" ,
                "value"       => array(
                    __( '--Select--' , ST_TEXTDOMAIN ) => '' ,
                    __( 'List' , ST_TEXTDOMAIN )       => '1' ,
                    __( 'Grid' , ST_TEXTDOMAIN )       => '2' ,
                ) ,
            ) ,
            /*array(
                "type"        => "checkbox" ,
                "holder"      => "div" ,
                "heading"     => __( "Select Taxonomy Show" , ST_TEXTDOMAIN ) ,
                "param_name"  => "taxonomy" ,
                "description" => "" ,
                "value"       => $list ,
            ) ,*/
        )
    ) );
}
if(!function_exists( 'st_vc_cba_search_results' )) {
    function st_vc_cba_search_results( $arg = array() )
    {
        $default = array(
            'style'    => '1' ,
            'taxonomy' => '' ,
        );
        $arg     = wp_parse_args( $arg , $default );

        $output = st_cba_load_view('vc-elements/st-cba-hotel-search-results/st-cba-hotel-search-results', false, array('atts' => $arg));

        return $output;
    }
}
if(function_exists('st_reg_shortcode')) {
    st_reg_shortcode( 'st_cba_search_results' , 'st_vc_cba_search_results' );
}

