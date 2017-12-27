<?php
if(!st_check_service_available( 'st_cars' )) {
    return;
}
if(function_exists( 'vc_map' )) {
    vc_map( array(
        "name"            => __( "[Ajax] ST Cars Search Results" , ST_TEXTDOMAIN ) ,
        "base"            => "st_cars_content_search_ajax" ,
        "content_element" => true ,
        "icon"            => "icon-st" ,
        "category"        => "Shinetheme" ,
        "params"          => array(
            array(
                "type"        => "dropdown" ,
                "holder"      => "div" ,
                "heading"     => __( "Style" , ST_TEXTDOMAIN ) ,
                "param_name"  => "st_style" ,
                "description" => "" ,
                "value"       => array(
                    __( '--Select--' , ST_TEXTDOMAIN ) => '' ,
                    __( 'List' , ST_TEXTDOMAIN )       => '1' ,
                    __( 'Grid' , ST_TEXTDOMAIN )       => '2' ,
                ) ,
            ),
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
        )
    ) );
}
if(!function_exists( 'st_vc_cars_content_search_ajax' )) {
    function st_vc_cars_content_search_ajax( $attr , $content = false )
    {
        $default = array(
            'st_style' => 1
        );
        $attr    = wp_parse_args( $attr , $default );
		global $st_search_query;
		if(!$st_search_query) return;
        return st()->load_template( 'cars/content' , 'cars-ajax' , array( 'attr' => $attr ) );
    }
}

if(st_check_service_available( 'st_cars' )) {
    st_reg_shortcode( 'st_cars_content_search_ajax' , 'st_vc_cars_content_search_ajax' );
}