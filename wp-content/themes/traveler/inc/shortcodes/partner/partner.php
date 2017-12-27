<?php
/* Partner Info */
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( "ST Partner Info" , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_partner_info' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => __('Shinetheme', ST_TEXTDOMAIN) ,
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

if(!function_exists( 'st_partner_info' )) {
    function st_partner_info( $arg )
    {
        $output = st()->load_template('user/partner/partner', 'info', array('atts' => $arg));
        return $output;
    }
}

st_reg_shortcode( 'st_partner_info' , 'st_partner_info' );

/* Partner avarage rating */
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( "ST Partner Average Rating" , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_partner_average_rating' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => __('Shinetheme', ST_TEXTDOMAIN) ,
            'params'=>array(
                array(
                    "type"             => "textfield" ,
                    "holder"           => "div" ,
                    "heading"          => __( "Title" , ST_TEXTDOMAIN ) ,
                    "param_name"       => "title" ,
                    "description"      => "" ,
                    "value"            => "" ,
                    'edit_field_class' => 'vc_col-sm-6' ,
                ),
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
            )
        )
    );
}

if(!function_exists( 'st_partner_average_rating' )) {
    function st_partner_average_rating( $attr , $content = false )
    {
        $default=array(
            'font_size'=>4
        );
        $attr=wp_parse_args($attr,$default);

        $output = st()->load_template('user/partner/partner', 'average-rating', array('atts' => $attr));
        return $output;
    }
}

st_reg_shortcode( 'st_partner_average_rating' , 'st_partner_average_rating' );

/* Partner contact form */
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( "ST Partner Contact Form" , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_partner_contact_form' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => __('Shinetheme', ST_TEXTDOMAIN) ,
            'params'=>array(
                array(
                    "type"             => "textfield" ,
                    "holder"           => "div" ,
                    "heading"          => __( "Title" , ST_TEXTDOMAIN ) ,
                    "param_name"       => "title" ,
                    "description"      => "" ,
                    "value"            => "" ,
                    'edit_field_class' => 'vc_col-sm-6' ,
                ),
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
            )
        )
    );
}

if(!function_exists( 'st_partner_contact_form' )) {
    function st_partner_contact_form( $attr , $content = false )
    {
        $default=array(
            'font_size'=>4
        );
        $attr=wp_parse_args($attr,$default);

        $output = st()->load_template('user/partner/partner', 'contact-form', array('atts' => $attr));
        return $output;
    }
}

st_reg_shortcode( 'st_partner_contact_form' , 'st_partner_contact_form' );

/* Partner list service */
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( "ST Partner List Services" , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_partner_list_service' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => __('Shinetheme', ST_TEXTDOMAIN) ,
            'params'=>array(
                array(
                    "type"             => "textfield" ,
                    "holder"           => "div" ,
                    "heading"          => __( "Title" , ST_TEXTDOMAIN ) ,
                    "param_name"       => "title" ,
                    "description"      => "" ,
                    "value"            => "" ,
                    'edit_field_class' => 'vc_col-sm-6' ,
                ),
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
                    "type"             => "textfield" ,
                    "holder"           => "div" ,
                    "heading"          => __( "Post per page of service" , ST_TEXTDOMAIN ) ,
                    "param_name"       => "post_per_page_service" ,
                    "description"      => "" ,
                    "value"            => "" ,
                    'edit_field_class' => 'vc_col-sm-6' ,
                ),
                array(
                    "type"             => "textfield" ,
                    "holder"           => "div" ,
                    "heading"          => __( "Post per page of review" , ST_TEXTDOMAIN ) ,
                    "param_name"       => "post_per_page_review" ,
                    "description"      => "" ,
                    "value"            => "" ,
                    'edit_field_class' => 'vc_col-sm-6' ,
                ),
            )
        )
    );
}

if(!function_exists( 'st_partner_list_service' )) {
    function st_partner_list_service( $attr , $content = false )
    {
        $default=array(
            'font_size'=>4,
            'post_per_page_service' => 10,
            'post_per_page_review' => 5,
        );
        $attr=wp_parse_args($attr,$default);

        $output = st()->load_template('user/partner/partner', 'list-services', array('atts' => $attr));
        return $output;
    }
}

st_reg_shortcode( 'st_partner_list_service' , 'st_partner_list_service' );

/* Partner Info In post */
if(function_exists( 'vc_map' )) {
    $list_tabs = array(
        esc_html__('All') => 'all',
        esc_html__('Avatar') => 'avatar',
        esc_html__('Email') => 'email',
        esc_html__('Phone') => 'phone',
        esc_html__('Email PayPal') => 'email_paypal',
        esc_html__('Home Airport') => 'home_airport',
        esc_html__('Address') => 'address'
    );
    vc_map(
        array(
            'name'                    => __( "ST Partner Info (Single Post)" , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_partner_info_in_post' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => __('Shinetheme', ST_TEXTDOMAIN) ,
            'params'=>array(
                array(
                    "type"             => "textfield" ,
                    "holder"           => "div" ,
                    "heading"          => __( "Title" , ST_TEXTDOMAIN ) ,
                    "param_name"       => "title" ,
                    "description"      => "" ,
                    "value"            => "" ,
                    'edit_field_class' => 'vc_col-sm-6' ,
                ),
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
                    "type"             => "dropdown" ,
                    "holder"           => "div" ,
                    "heading"          => __( "Avatar type" , ST_TEXTDOMAIN ) ,
                    "param_name"       => "avatar_type" ,
                    "description"      => "" ,
                    "value"            => array(
                        __('--Select--',ST_TEXTDOMAIN)=>'',
                        __( "Square" , ST_TEXTDOMAIN ) => 'square' ,
                        __( "Circle" , ST_TEXTDOMAIN ) => 'circle' ,
                    ) ,
                    'edit_field_class' => 'vc_col-sm-6' ,
                ) ,
                array(
                    "type"             => "dropdown" ,
                    "holder"           => "div" ,
                    "heading"          => __( "Layout" , ST_TEXTDOMAIN ) ,
                    "param_name"       => "format_column" ,
                    "description"      => "" ,
                    "value"            => array(
                        __('--Select--',ST_TEXTDOMAIN)=>'',
                        __( "1 Column" , ST_TEXTDOMAIN ) => '1' ,
                        __( "2 Column" , ST_TEXTDOMAIN ) => '2' ,
                    ) ,
                    'edit_field_class' => 'vc_col-sm-6' ,
                ) ,
                array(
                    'type' => 'checkbox',
                    'admin_label' => true,
                    'heading' => __('Select Information Display', ST_TEXTDOMAIN),
                    'param_name' => 'display_info',
                    'description' => __('Please choose information to display in page', ST_TEXTDOMAIN),
                    'value' => $list_tabs,
                    'std' => 'all'
                )
            )
        )
    );
}

if(!function_exists( 'st_partner_info_in_post' )) {
    function st_partner_info_in_post( $attr , $content = false )
    {
        $default=array(
            'font_size'=>4
        );
        $attr=wp_parse_args($attr,$default);

        $output = st()->load_template('user/partner/partner', 'info-inpost', array('atts' => $attr));
        return $output;
    }
}
st_reg_shortcode( 'st_partner_info_in_post' , 'st_partner_info_in_post' );