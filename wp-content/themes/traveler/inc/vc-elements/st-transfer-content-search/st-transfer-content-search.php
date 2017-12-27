<?php

if(function_exists( 'vc_map' )) {

    vc_map( array(
        "name"            => __( "ST Car Transfer Search Result" , ST_TEXTDOMAIN ) ,
        "base"            => "st_search_car_transfer_result" ,
        "content_element" => true ,
        "icon"            => "icon-st" ,
        "category"        => "Shinetheme" ,
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
            )
        )
    ) );
}
if(!function_exists( 'st_search_car_transfer_result' )) {
    function st_search_car_transfer_result( $arg = array() )
    {
        $default = array( 
            'style'    => '2' ,
            'taxonomy' => '' ,
        );
        $arg     = wp_parse_args( $arg , $default );
        return st()->load_template( 'car_transfer/search-elements/result/result' , false , array( 'arg' => $arg ) );
    }
}
st_reg_shortcode( 'st_search_car_transfer_result' , 'st_search_car_transfer_result' );

