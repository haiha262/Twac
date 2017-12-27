<?php
$enable_cba = st()->get_option('cba_enable', 'off');
if($enable_cba == 'off') return;

if (function_exists('vc_map')) {
    vc_map(
        array(
            'name' => __('ST Colibri PMS Hotel Price', ST_TEXTDOMAIN),
            'base' => 'st_cba_hotel_price',
            'icon' => 'icon-st',
            'category' => __('Colibri PMS', ST_TEXTDOMAIN),
            "content_element" => true,
            'show_settings_on_create' => false,
            'params' => array(
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


if (!function_exists('st_cba_hotel_price')) {
    function st_cba_hotel_price($attr, $content = false)
    {
        $output = st_cba_load_view('vc-elements/st-cba-hotel-detail/st-cba-hotel-price', false, array('atts' => ''));
        return $output;
    }
}

st_reg_shortcode('st_cba_hotel_price', 'st_cba_hotel_price');