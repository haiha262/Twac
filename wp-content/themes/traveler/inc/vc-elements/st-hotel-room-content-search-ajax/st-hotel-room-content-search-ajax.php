<?php
if(!st_check_service_available( 'hotel_room' )) {
    return;
}
if(function_exists( 'vc_map' )) {
    $list = st_list_taxonomy( 'hotel_room' );
    $txt  = __( '--Select--' , ST_TEXTDOMAIN );
    unset( $list[ $txt ] );
    vc_map( array(
        "name"            => __( "[Ajax] ST Hotel Room Search Result" , ST_TEXTDOMAIN ) ,
        "base"            => "st_search_hotel_room_result_ajax" ,
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
            ) ,
            array(
                "type"        => "dropdown" ,
                "holder"      => "div" ,
                "heading"     => __( "OrderBy Default" , ST_TEXTDOMAIN ) ,
                "param_name"  => "st_orderby" ,
                "description" => "" ,
                "value"       => array(
                    __( '---None---' , ST_TEXTDOMAIN ) => '-1' ,
                    __( 'New' , ST_TEXTDOMAIN ) => 'new' ,
                    __( 'Random' , ST_TEXTDOMAIN )       => 'random' ,
                    __( 'Price' , ST_TEXTDOMAIN )       => 'price' ,
                    __( 'Featured' , ST_TEXTDOMAIN )       => 'featured' ,
                    __( 'Name' , ST_TEXTDOMAIN )       => 'name' ,
                ) ,
            ) ,
            array(
                "type"        => "dropdown" ,
                "holder"      => "div" ,
                "heading"     => __( "Sort By" , ST_TEXTDOMAIN ) ,
                "param_name"  => "st_sortby" ,
                "description" => "" ,
                "value"       => array(
                    __( 'Ascending' , ST_TEXTDOMAIN ) => 'asc' ,
                    __( 'Descending' , ST_TEXTDOMAIN )       => 'desc'
                ) ,
            ) ,
            array(
                "type"        => "checkbox" ,
                "holder"      => "div" ,
                "heading"     => __( "Select Taxonomy Show" , ST_TEXTDOMAIN ) ,
                "param_name"  => "taxonomy" ,
                "description" => "" ,
                "value"       => $list ,
            ) ,
        )
    ) );
}
if(!function_exists( 'st_vc_search_hotel_room_result_ajax' )) {
    function st_vc_search_hotel_room_result_ajax( $arg = array() )
    {
        $default = array(
            'style'    => '2' ,
            'taxonomy' => '' ,
        );
        $arg     = wp_parse_args( $arg , $default );

        return st()->load_template( 'hotel-room/search-elements/result-ajax' , false , array( 'arg' => $arg ) );
    }
}
if(st_check_service_available( 'hotel_room' )) {
    st_reg_shortcode( 'st_search_hotel_room_result_ajax' , 'st_vc_search_hotel_room_result_ajax' );
}

