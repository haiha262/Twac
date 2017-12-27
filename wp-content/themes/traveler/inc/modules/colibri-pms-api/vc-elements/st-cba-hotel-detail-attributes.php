<?php
$enable_cba = st()->get_option('cba_enable', 'off');
if($enable_cba == 'off') return;

if(function_exists( 'vc_map' )) {
    vc_map( array(
        "name"            => __( "ST Colibri PMS Hotel Detail Attribute" , ST_TEXTDOMAIN ) ,
        "base"            => "st_cba_hotel_detail_attribute" ,
        "content_element" => true ,
        "icon"            => "icon-st" ,
        "category"        => __('Colibri PMS', ST_TEXTDOMAIN) ,
        "params"          => array(
            array(
                "type"             => "textfield" ,
                "holder"           => "div" ,
                "heading"          => __( "Title" , ST_TEXTDOMAIN ) ,
                "param_name"       => "title" ,
                "description"      => "" ,
                "value"            => "" ,
                'edit_field_class' => 'vc_col-sm-6' ,
            ) ,
            array(
                "type"             => "dropdown" ,
                "holder"           => "div" ,
                "heading"          => __( "Font Size" , ST_TEXTDOMAIN ) ,
                "param_name"       => "font_size" ,
                "description"      => "" ,
                "value"            => array(
                    __('--Select--',ST_TEXTDOMAIN)=>'',
                    __( "H1" , ST_TEXTDOMAIN ) => '1' ,
                    __( "H2" , ST_TEXTDOMAIN ) => '2' ,
                    __( "H3" , ST_TEXTDOMAIN ) => '3' ,
                    __( "H4" , ST_TEXTDOMAIN ) => '4' ,
                    __( "H5" , ST_TEXTDOMAIN ) => '5' ,
                ) ,
                'edit_field_class' => 'vc_col-sm-6' ,
            ) ,
            array(
                "type"        => "dropdown" ,
                "holder"      => "div" ,
                "heading"     => __( "Column" , ST_TEXTDOMAIN ) ,
                "param_name"  => "cba_attribute" ,
                "description" => "" ,
                "value"       => array(
                    __('--Select--',ST_TEXTDOMAIN)=>'',
                    __('Amenity',ST_TEXTDOMAIN)=>'amn',
                    __('Service',ST_TEXTDOMAIN)=>'srv'
                ) ,
            ),
            array(
                "type"        => "dropdown" ,
                "holder"      => "div" ,
                "heading"     => __( "Column" , ST_TEXTDOMAIN ) ,
                "param_name"  => "item_col" ,
                "description" => "" ,
                "value"       => array(
                    __('--Select--',ST_TEXTDOMAIN)=>'',
                    1 => 1,
                    2  => 2 ,
                    3  => 3 ,
                    4  => 4 ,
                    6  => 6 ,
                    12 => 12
                ) ,
            )
        )
    ) );
}

if(!function_exists( 'st_vc_cba_hotel_detail_attribute' )) {
    function st_vc_cba_hotel_detail_attribute( $attr , $content = false )
    {
        $default=array(
            'font_size'=>3,
            'item_col'=>1
        );
        $attr=wp_parse_args($attr,$default);

        $output = st_cba_load_view('vc-elements/st-cba-hotel-detail/st-cba-hotel-attribute', false, array('attr' => $attr));
        return $output;
    }
}

st_reg_shortcode( 'st_cba_hotel_detail_attribute' , 'st_vc_cba_hotel_detail_attribute' );
