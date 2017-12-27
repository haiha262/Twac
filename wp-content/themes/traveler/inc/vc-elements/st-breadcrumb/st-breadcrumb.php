<?php
if (function_exists('vc_map')) {
    vc_map(array(
        "name" => __("ST Breadcrumb", ST_TEXTDOMAIN),
        "base" => "st_breadcrumb",
        "content_element" => true,
        "icon" => "icon-st",
        "category"=>"Shinetheme",
        'show_settings_on_create' => false,
        'params'=>array(
            array(
                'type' => 'textfield',
                'heading' => esc_html__('There is no option in this element', ST_TEXTDOMAIN),
                'param_name' => 'description_field',
                'edit_field_class' => 'vc_column vc_col-sm-12 st_vc_hidden_input'
            )
        )
    ));
}
if (!function_exists('st_breadcrumb_fn')){
    function st_breadcrumb_fn($attr){
        ?>
        <div class="container">
            <div class="breadcrumb">
                <?php st_breadcrumbs(); ?>
            </div>
        </div>
        <?php
    }
    st_reg_shortcode('st_breadcrumb','st_breadcrumb_fn');
}