<?php
if(!st_check_service_available( 'st_rental' )) {
    return;
}
if(function_exists( 'vc_map' )) {
    $list = st_list_taxonomy( 'st_rental' );
    $txt  = __( '--Select--' , ST_TEXTDOMAIN );
    unset( $list[ $txt ] );
    vc_map( array(
        "name"            => __( "[Ajax] ST Rental Search Results" , ST_TEXTDOMAIN ) ,
        "base"            => "st_search_rental_result_ajax" ,
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
                    __( 'Grid' , ST_TEXTDOMAIN )       => 'grid' ,
                    __( 'List' , ST_TEXTDOMAIN )       => 'list' ,
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
            )
        )
    ) );
}

if(!function_exists( 'st_search_rental_result_ajax' )) {
    function st_search_rental_result_ajax( $arg = array() )
    {
        $default = array(
            'style'    => 'grid' ,
            'taxonomy' => '' ,
        );

        $arg = wp_parse_args( $arg , $default );

        if(!get_post_type() == 'st_rental' and get_query_var( 'post_type' ) != "st_rental")
            return;
        
        return st()->load_template( 'rental/search-elements/result-ajax' , false , array( 'arg' => $arg ) );
    }
}
if(st_check_service_available( 'st_rental' )) {
    st_reg_shortcode( 'st_search_rental_result_ajax' , 'st_search_rental_result_ajax' );
}
