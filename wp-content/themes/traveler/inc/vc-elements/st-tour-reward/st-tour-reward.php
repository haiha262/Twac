<?php
if (function_exists('vc_map')){
    vc_map(array(
        "name"            => __( "ST Tour Rewards" , ST_TEXTDOMAIN ) ,
        "base"            => "st_tour_rewards" ,
        "content_element" => true ,
        "icon"            => "icon-st" ,
        "category"        => 'Shinetheme' ,
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
if (!function_exists('st_tour_rewards_fn')){
    function st_tour_rewards_fn($attr){
        $rewards = st()->get_option('tour_rewards' ,'');
        $html = "<div class='st_tour_rewards row'><div class='col-xs-12'>";
        if (!empty($rewards) and is_array($rewards)) {
            foreach ($rewards as $item ){
                extract(wp_parse_args($item,array('title'=>"#" , 'image'=>"#",'link'=> "#")));
                if (!empty($image) and !empty($link)){
                    $html .="<a href='".$link."'><img height='35px' width='auto' alt='".$title."' src='".$image."'/></a>";
                }
            }
        }
        $html .="</div></div>";
        return $html;
        
    }

}
st_reg_shortcode('st_tour_rewards' , 'st_tour_rewards_fn');