<?php
    if(function_exists('vc_map')){
        vc_map( array(
            "name" => __("ST Inbox Form", ST_TEXTDOMAIN),
            "base" => "st_inbox_form",
            "content_element" => true,
            "icon" => "icon-st",
            "category"=>"Shinetheme",
            "params" => array(
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "heading" => __("Title Form", ST_TEXTDOMAIN),
                    "param_name" => "title",
                    "description" =>"",
                    'edit_field_class'=>'vc_col-sm-12'
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "heading" => __("Active Form", ST_TEXTDOMAIN),
                    "param_name" => "active",
                    'value'=>array(
                        esc_html__("No",ST_TEXTDOMAIN)=>'',
                        esc_html__("Yes",ST_TEXTDOMAIN)=>'active',
                    ),
                    'edit_field_class'=>'vc_col-sm-12'
                ),
            )
        ) );
    }
    if(!function_exists('st_vc_inbox_form')){
        function st_vc_inbox_form($attr,$content=false)
        {
            $data = shortcode_atts(
                array(
                    'title' =>'',
                    'active' =>'',
                ), $attr, 'st_inbox_form' );
            extract($data);
            $enable_inbox = st()->get_option('enable_inbox');
            $html = '';
            if($enable_inbox == 'on') {
                $html = st()->load_template( 'vc-elements/st-inbox-form/index' , false ,$data );
            }
            return $html;
        }

    }
    st_reg_shortcode('st_inbox_form','st_vc_inbox_form');