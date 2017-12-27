<?php
/*if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( 'ST Colibri PMS List of Hotel Rooms' , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_cba_hotel_detail_list_rooms' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => __('Colibri PMS', ST_TEXTDOMAIN) ,
            'show_settings_on_create' => false,
            'params'=>array(
                array(
                    'type' => 'textfield',
                    'heading' => esc_html__('There is no option in this element', ST_TEXTDOMAIN),
                    'param_name' => 'description_field',
                    'edit_field_class' => 'vc_column vc_col-sm-12 st_vc_hidden_input'
                )
            )
        )
    );
}


if(!function_exists( 'st_cba_hotel_detail_list_rooms' )) {
    function st_cba_hotel_detail_list_rooms( $attr = array() )
    {
        $attr = wp_parse_args( $attr , array(
            'style' => 'style-1'
        ) );

        return st_cba_load_view('vc-elements/st-cba-hotel-detail/st-cba-hotel-list-room', false, array('attr' => $attr));

    }
}

st_reg_shortcode( 'st_cba_hotel_detail_list_rooms' , 'st_cba_hotel_detail_list_rooms' );*/
$enable_cba = st()->get_option('cba_enable', 'off');
if($enable_cba == 'off') return;

if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( 'ST Colibri PMS List of Hotel Rooms' , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_cba_hotel_detail_list_rooms_opt' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => __('Colibri PMS', ST_TEXTDOMAIN) ,
            'show_settings_on_create' => false,
            'params'=>array(
                array(
                    'type' => 'textfield',
                    'heading' => esc_html__('There is no option in this element', ST_TEXTDOMAIN),
                    'param_name' => 'description_field',
                    'edit_field_class' => 'vc_column vc_col-sm-12 st_vc_hidden_input'
                )
            )
        )
    );
}


if(!function_exists( 'st_cba_hotel_detail_list_rooms_opt' )) {
    function st_cba_hotel_detail_list_rooms_opt( $attr = array() )
    {
        $attr = wp_parse_args( $attr , array(
            'style' => 'style-1'
        ) );

        return st_cba_load_view('vc-elements/st-cba-hotel-detail/st-cba-hotel-list-room-opt', false, array('attr' => $attr));

    }
}

st_reg_shortcode( 'st_cba_hotel_detail_list_rooms_opt' , 'st_cba_hotel_detail_list_rooms_opt' );