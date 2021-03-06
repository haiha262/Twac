<?php
if(!st_check_service_available( 'st_activity' )) {
    return;
}
if(function_exists( 'vc_map' )) {
    $list = st_list_taxonomy( 'st_activity' );
    $txt  = __( '--Select--' , ST_TEXTDOMAIN );
    unset( $list[ $txt ] );
    vc_map( array(
        "name"            => __( "[Ajax] ST Activity Search Results" , ST_TEXTDOMAIN ) ,
        "base"            => "st_activiry_content_search_ajax" ,
        "content_element" => true ,
        "icon"            => "icon-st" ,
        "category"        => 'Shinetheme' ,
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
if(!function_exists( 'st_vc_activiry_content_search_ajax' )) {
    function st_vc_activiry_content_search_ajax( $attr , $content = false )
    {
        $default = array(
            'st_style' => 1 ,
            'taxonomy' => ''
        );
        $attr    = wp_parse_args( $attr , $default );
        return st()->load_template( 'activity/content' , 'activity-ajax' , array( 'attr' => $attr ) );
    }
}
if(st_check_service_available( 'st_activity' )) {
    st_reg_shortcode( 'st_activiry_content_search_ajax' , 'st_vc_activiry_content_search_ajax' );
}
