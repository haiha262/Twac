<?php 

if(function_exists( 'vc_map' )) {
    vc_map( array(
        "name"            => __( "ST Sum of Car Transfer Search Results" , ST_TEXTDOMAIN ) ,
        "base"            => "st_search_car_transfer_title" ,
        "content_element" => true ,
        "icon"            => "icon-st" ,
        "category"        => "Transfer" ,
        "params"          => array(
            array(
                "type"        => "dropdown" ,
                "holder"      => "div" ,
                "heading"     => __( "Search Modal" , ST_TEXTDOMAIN ) ,
                "param_name"  => "search_modal" ,
                "description" => "" ,
                "value"       => array(
                    __( '--Select--' , ST_TEXTDOMAIN ) => '' ,
                    __( 'Yes' , ST_TEXTDOMAIN )        => '1' ,
                    __( 'No' , ST_TEXTDOMAIN )         => '0' ,
                ) ,
            )
        )
    ) );
}
if(!function_exists( 'st_search_car_transfer_title' )) {
    function st_search_car_transfer_title( $arg = array() )
    {

        $default = array(
            'search_modal' => 1
        );
        
        wp_enqueue_script('magnific.js' );

        extract( wp_parse_args( $arg , $default ) );

        $object = STCarTransfer::inst();
        $a      = '<h3 class="booking-title"><span id="count-filter-tour">' . balanceTags( $object->get_result_string() ) . '</span>';

        if($search_modal) {
            $a .= '<small><a class="popup-text" href="#search-dialog" data-effect="mfp-zoom-out">' . __( 'Change search' , ST_TEXTDOMAIN ) . '</a></small>';
        }
        $a .= '</h3>';

        return $a;
    }
}
st_reg_shortcode( 'st_search_car_transfer_title' , 'st_search_car_transfer_title' );