<?php
/**
 * Created by PhpStorm.
 * User: me664
 * Date: 12/15/14
 * Time: 9:44 AM
 */

if(!st_check_service_available( 'st_hotel' )) {
    return;
}
/**
 * ST Hotel header
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( "ST Hotel Header" , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_hotel_header' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Hotel' ,
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

if(!function_exists( 'st_hotel_header' )) {
    function st_hotel_header( $arg )
    {
        if(is_singular( 'st_hotel' )) {
            return st()->load_template( 'hotel/elements/header' , false , array( 'arg' => $arg ) );
        }
        return false;
    }
}

st_reg_shortcode( 'st_hotel_header' , 'st_hotel_header' );

/**
 * ST Hotel star
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'            => __( "ST Hotel Star" , ST_TEXTDOMAIN ) ,
            'base'            => 'st_hotel_star' ,
            'content_element' => true ,
            'icon'            => 'icon-st' ,
            'category'        => 'Hotel' ,
            'params'          => array(
                array(
                    "type"        => "textfield" ,
                    "heading"     => __( "Title" , ST_TEXTDOMAIN ) ,
                    "param_name"  => "title" ,
                    'admin_label' => true ,
                    'std'         => 'Hotel Star'
                )
            )
        )
    );
}


if(!function_exists( 'st_hotel_star' )) {
    function st_hotel_star( $attr = array() )
    {
        $attr = wp_parse_args( $attr , array(
            'title' => ''
        ) );
        if(is_singular( 'st_hotel' )) {
            return st()->load_template( 'hotel/elements/star' , false , $attr );
        }
        return false;
    }
}
st_reg_shortcode( 'st_hotel_star' , 'st_hotel_star' );
/**
 * ST Hotel Video
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( 'ST Hotel Video' , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_hotel_video' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Hotel' ,
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

if(!function_exists( 'st_hotel_video' )) {
    function st_hotel_video( $attr = array() )
    {
        if(is_singular( 'st_hotel' )) {
            if($video = get_post_meta( get_the_ID() , 'video' , true )) {
                return "<div class='media-responsive'>" . wp_oembed_get( $video ) . "</div>";
            }
        }
    }
}

st_reg_shortcode( 'st_hotel_video' , 'st_hotel_video' );

/**
 * ST Hotel Price
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'            => __( 'ST Hotel Price' , ST_TEXTDOMAIN ) ,
            'base'            => 'st_hotel_price' ,
            'icon'            => 'icon-st' ,
            'category'        => 'Hotel' ,
            "content_element" => true ,
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


if(!function_exists( 'st_hotel_price_func' )) {
    function st_hotel_price_func( $attr , $content = false )
    {
        if(is_singular( 'st_hotel' )) {
            return st()->load_template( 'hotel/elements/price' );
        }
    }
}

st_reg_shortcode( 'st_hotel_price' , 'st_hotel_price_func' );

/**
*hotel policy
*@since 1.1.9
*/

if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'            => __( 'ST Hotel Policy' , ST_TEXTDOMAIN ) ,
            'base'            => 'st_hotel_policy' ,
            'icon'            => 'icon-st' ,
            'category'        => 'Hotel' ,
            "content_element" => true ,
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


if(!function_exists( 'st_hotel_policy_func' )) {
    function st_hotel_policy_func( $attr , $content = false )
    {
        if(is_singular( 'st_hotel' )) {
            return st()->load_template( 'hotel/elements/policy' );
        }
    }
}

st_reg_shortcode( 'st_hotel_policy' , 'st_hotel_policy_func' );


/**
 * ST Hotel Logo
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'            => __( 'ST Hotel Logo' , ST_TEXTDOMAIN ) ,
            'base'            => 'st_hotel_logo' ,
            'content_element' => true ,
            'icon'            => 'icon-st' ,
            'category'        => 'Hotel' ,
            'params'          => array(
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
                        __( "H1" , ST_TEXTDOMAIN ) => '1' ,
                        __( "H2" , ST_TEXTDOMAIN ) => '2' ,
                        __( "H3" , ST_TEXTDOMAIN ) => '3' ,
                        __( "H4" , ST_TEXTDOMAIN ) => '4' ,
                        __( "H5" , ST_TEXTDOMAIN ) => '5' ,
                    ) ,
                    'edit_field_class' => 'vc_col-sm-6' ,
                ) ,
                array(
                    'type'       => 'dropdown' ,
                    'heading'    => __( 'Thumbnail Size' , ST_TEXTDOMAIN ) ,
                    'param_name' => 'thumbnail_size' ,
                    'value'      => array(
                        'Full'      => 'full' ,
                        'Large'     => 'large' ,
                        'Medium'    => 'medium' ,
                        'Thumbnail' => 'thumbnail'
                    )
                ) ,
            )
        )
    );
}

if(!function_exists( 'st_hotel_logo' )) {
    function st_hotel_logo( $attr = array() )
    {
        if(is_singular( 'st_hotel' )) {
            $default = array(
                'thumbnail_size' => 'full' ,
                'title'          => '' ,
                'font_size'      => '3' ,
            );

            extract( wp_parse_args( $attr , $default ) );

            $img_id = get_post_meta( get_the_ID() , 'logo' , true );
                        $img_id = get_post_meta( get_the_ID() , 'logo' , true );
            $meta = false;
            if(is_numeric($img_id)){
                $meta = wp_get_attachment_url($img_id);
            }else{
                $meta = $img_id;
            }

            $html = '';
            if($meta) {
                $html = "<img src=".$meta." class='img-responsive' style='margin-bottom:10px;' alt='" . TravelHelper::get_alt_image($img_id) ."'/>";
            }

            if(!empty( $title ) and !empty( $html )) {
                $html = '<h' . $font_size . '>' . $title . '</h' . $font_size . '>' . $html;
            }
            return $html;
        }
    }
}

st_reg_shortcode( 'st_hotel_logo' , 'st_hotel_logo' );


/**
 * ST Hotel Add Review
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( 'ST Add Hotel Review' , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_hotel_add_review' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Hotel' ,
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

if(!function_exists( 'st_hotel_add_review' )) {
    function st_hotel_add_review()
    {
        if(is_singular( 'st_hotel' )) {
            return '<div class="text-right mb10">
                      <a class="btn btn-primary" href="' . get_comments_link() . '">' . __( 'Write a review' , ST_TEXTDOMAIN ) . '</a>
                   </div>';
        }
    }
}

st_reg_shortcode( 'st_hotel_add_review' , 'st_hotel_add_review' );

/**
 * ST Hotel Nearby
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( 'ST Hotel Nearby' , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_hotel_nearby' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Hotel' ,
            'show_settings_on_create' => true ,
            'params'                  => array(
                array(
                    'type' => 'dropdown',
                    'admin_label' => true,
                    'heading' => esc_html__('Style', ST_TEXTDOMAIN),
                    'param_name' => 'style',
                    'value' => array(
                        esc_html__('Style 1', ST_TEXTDOMAIN) => 'style-1',
                        esc_html__('Style 2', ST_TEXTDOMAIN) => 'style-2'
                    ),
                    'std' => 'style-1'
                ),
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
                        __( '--Select--' , ST_TEXTDOMAIN ) => '' ,
                        __( "H1" , ST_TEXTDOMAIN )         => '1' ,
                        __( "H2" , ST_TEXTDOMAIN )         => '2' ,
                        __( "H3" , ST_TEXTDOMAIN )         => '3' ,
                        __( "H4" , ST_TEXTDOMAIN )         => '4' ,
                        __( "H5" , ST_TEXTDOMAIN )         => '5' ,
                    ) ,
                    'edit_field_class' => 'vc_col-sm-6' ,
                ) ,
            )
        )
    );
}

if(!function_exists( 'st_hotel_nearby' )) {
    function st_hotel_nearby( $attr = array() , $content = null )
    {
        if(is_singular( 'st_hotel' )) {
            $default = array(
                'style'     => 'style-1' ,
                'title'     => '' ,
                'font_size' => '3' ,
            );
            $attr    = wp_parse_args( $attr , $default );
            return st()->load_template( 'hotel/elements/nearby' , false , array( 'attr' => $attr ) );
        }
    }
}

st_reg_shortcode( 'st_hotel_nearby' , 'st_hotel_nearby' );

/**
 * ST Hotel Review
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( 'ST Hotel Review' , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_hotel_review' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Hotel' ,
            'show_settings_on_create' => true ,
            'params'                  => array(
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
                        __( '--Select--' , ST_TEXTDOMAIN ) => '' ,
                        __( "H1" , ST_TEXTDOMAIN )         => '1' ,
                        __( "H2" , ST_TEXTDOMAIN )         => '2' ,
                        __( "H3" , ST_TEXTDOMAIN )         => '3' ,
                        __( "H4" , ST_TEXTDOMAIN )         => '4' ,
                        __( "H5" , ST_TEXTDOMAIN )         => '5' ,
                    ) ,
                    'edit_field_class' => 'vc_col-sm-6' ,
                ) ,
            )
        )
    );
}

if(!function_exists( 'st_hotel_review' )) {
    function st_hotel_review( $attr = array() )
    {
        if(is_singular( 'st_hotel' )) {
            $default = array(
                'title'     => '' ,
                'font_size' => '3' ,
            );
            extract( wp_parse_args( $attr , $default ) );
            if(comments_open() and st()->get_option( 'hotel_review' ) == 'on') {
                ob_start();
                comments_template( '/reviews/reviews.php' );
                $html = @ob_get_clean();
                if(!empty( $title ) and !empty( $html )) {
                    $html = '<h' . $font_size . '>' . $title . '</h' . $font_size . '>' . $html;
                }
                return $html;
            }
        }
    }
}

st_reg_shortcode( 'st_hotel_review' , 'st_hotel_review' );

/**
 * ST Hotel Detail List Rooms
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( 'ST Detailed List of Hotel Rooms' , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_hotel_detail_list_rooms' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Hotel' ,
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


if(!function_exists( 'st_hotel_detail_list_rooms' )) {
    function st_hotel_detail_list_rooms( $attr = array() )
    {
        $attr = wp_parse_args( $attr , array(
            'style' => 'style-1'
        ) );
        if(is_singular( 'st_hotel' )) {
            return st()->load_template( 'hotel/elements/loop_room' , null , array( 'attr' => $attr ) );
        }
    }
}

st_reg_shortcode( 'st_hotel_detail_list_rooms' , 'st_hotel_detail_list_rooms' );

/**
 * ST Hotel Detail Card Accept
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'            => __( 'ST Detailed Hotel Card Accept' , ST_TEXTDOMAIN ) ,
            'base'            => 'st_hotel_detail_card_accept' ,
            'content_element' => true ,
            'icon'            => 'icon-st' ,
            'category'        => 'Hotel' ,
            "params"          => array(
                // add params same as with any other content element
                array(
                    "type"        => "textfield" ,
                    "heading"     => __( "Title" , ST_TEXTDOMAIN ) ,
                    "param_name"  => "title" ,
                    "description" => "" ,
                ) ,
            )
        )
    );
}

if(!function_exists( 'st_hotel_detail_card_accept' )) {
    function st_hotel_detail_card_accept( $arg = array() )
    {
        $arg = wp_parse_args( $arg , array(
            'title' => ''
        ) );
        if(is_singular( 'st_hotel' )) {
            return st()->load_template( 'hotel/elements/card' , false , array( 'arg' => $arg ) );
        }
        return false;
    }
}

st_reg_shortcode( 'st_hotel_detail_card_accept' , 'st_hotel_detail_card_accept' );

/**
 * ST Hotel Detail Search Room
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( 'ST Hotel Rooms Available' , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_hotel_detail_search_room' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Hotel' ,
            'show_settings_on_create' => true ,
            'params'                  => array(
                array(
                    "type"             => "textfield" ,
					"admin_label"           => true ,
                    "heading"          => __( "Title" , ST_TEXTDOMAIN ) ,
                    "param_name"       => "title" ,
                    "description"      => "" ,
                    "value"            => "" ,
                    'edit_field_class' => 'vc_col-sm-6' ,
                ) ,
                array(
                    "type"             => "dropdown" ,
					"admin_label"           => true ,
                    "heading"          => __( "Font Size" , ST_TEXTDOMAIN ) ,
                    "param_name"       => "font_size" ,
                    "description"      => "" ,
                    "value"            => array(
                        __( '--Select--' , ST_TEXTDOMAIN ) => '' ,
                        __( "H1" , ST_TEXTDOMAIN )         => '1' ,
                        __( "H2" , ST_TEXTDOMAIN )         => '2' ,
                        __( "H3" , ST_TEXTDOMAIN )         => '3' ,
                        __( "H4" , ST_TEXTDOMAIN )         => '4' ,
                        __( "H5" , ST_TEXTDOMAIN )         => '5' ,
                    ) ,
                    'edit_field_class' => 'vc_col-sm-6' ,
                ) ,
                array(
                    "type"             => "dropdown" ,
                    "heading"          => __( "Style" , ST_TEXTDOMAIN ) ,
                    "param_name"       => "style" ,
                    "description"      => "" ,
                    "value"            => array(
                        __( "Horizontal" , ST_TEXTDOMAIN )         => 'horizon' ,
                        __( "Vertical" , ST_TEXTDOMAIN )         => 'vertical' ,
                        __( "Vertical 2" , ST_TEXTDOMAIN )         => 'style_3' ,
                    ) ,
                    'edit_field_class' => 'vc_col-sm-6' ,
                ) ,
            )
        )
    );
}

if(!function_exists( 'st_hotel_detail_search_room' )) {
    function st_hotel_detail_search_room( $attr = array() )
    {
        if(is_singular( 'st_hotel' )) {
            $default = array(
                'title'     => '' ,
                'font_size' => '3' ,
				'style'=>'horizon'
            );
            extract( wp_parse_args( $attr , $default ) );
            $html = st()->load_template( 'hotel/elements/search_room' , null , array( 'attr' => $attr ) );
            if(!empty( $title ) and !empty( $html )) {
                $html = '<h' . $font_size . '>' . $title . '</h' . $font_size . '>' . $html;
            }
            return $html;
        }
    }
}

st_reg_shortcode( 'st_hotel_detail_search_room' , 'st_hotel_detail_search_room' );

/**
 * ST Hotel Detail Review Detail
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( 'ST Detailed Hotel Review' , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_hotel_detail_review_detail' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Hotel' ,
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


if(!function_exists( 'st_hotel_detail_review_detail' )) {
    function st_hotel_detail_review_detail()
    {
        if(is_singular( 'st_hotel' )) {
            return st()->load_template( 'hotel/elements/review_detail' );
        }
    }
}

st_reg_shortcode( 'st_hotel_detail_review_detail' , 'st_hotel_detail_review_detail' );

/**
 * ST Hotel Detail Review Summary
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( 'ST Hotel Review Summary' , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_hotel_detail_review_summary' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Hotel' ,
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

if(!function_exists( 'st_hotel_detail_review_summary' )) {
    function st_hotel_detail_review_summary()
    {
        if(is_singular( 'st_hotel' )) {
            return st()->load_template( 'hotel/elements/review_summary' );
        }
    }
}

st_reg_shortcode( 'st_hotel_detail_review_summary' , 'st_hotel_detail_review_summary' );

/**
 * ST Hotel Detail Map
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( 'ST Detailed Hotel Map' , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_hotel_detail_map' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Hotel' ,
            'show_settings_on_create' => true ,
            'params'                  => array(
                array(
                    "type"        => "textfield" ,
                    "holder"      => "div" ,
                    "heading"     => __( "Range" , ST_TEXTDOMAIN ) ,
                    "param_name"  => "range" ,
                    "description" => "Km" ,
                    "value"       => "20" ,
                ) ,
                array(
                    "type"        => "textfield" ,
                    "holder"      => "div" ,
                    "heading"     => __( "Number" , ST_TEXTDOMAIN ) ,
                    "param_name"  => "number" ,
                    "description" => "" ,
                    "value"       => "12" ,
                ) ,
                array(
                    "type"        => "dropdown" ,
                    "holder"      => "div" ,
                    "heading"     => __( "Show Circle" , ST_TEXTDOMAIN ) ,
                    "param_name"  => "show_circle" ,
                    "description" => "" ,
                    "value"       => array(
                        __("No",ST_TEXTDOMAIN)=>"no",
                        __("Yes",ST_TEXTDOMAIN)=>"yes"
                    ) ,
                ) ,
            )
        )
    );
}

if(!function_exists( 'st_hotel_detail_map' )) {
    function st_hotel_detail_map( $attr )
    {
        if(is_singular( 'st_hotel' )) {
            $hotel=new STHotel();
            $data=$hotel->get_near_by(get_the_ID(),200,10);
            $default = array(
                'number'      => '12' ,
                'range'       => '20' ,
                'show_circle' => 'no' ,
            );
            extract( wp_parse_args( $attr , $default ) );
            $lat   = get_post_meta( get_the_ID() , 'map_lat' , true );
            $lng   = get_post_meta( get_the_ID() , 'map_lng' , true );
            $zoom  = get_post_meta( get_the_ID() , 'map_zoom' , true );
            $hotel = new STHotel();
            $data  = $hotel->get_near_by( get_the_ID() , $range , $number );
            $location_center                     = '[' . $lat . ',' . $lng . ']';
            $data_map                            = array();
            $data_map[ 0 ][ 'id' ]               = get_the_ID();
            $data_map[ 0 ][ 'name' ]             = get_the_title();
            $data_map[ 0 ][ 'post_type' ]        = get_post_type();
            $data_map[ 0 ][ 'lat' ]              = $lat;
            $data_map[ 0 ][ 'lng' ]              = $lng;
            $data_map[ 0 ][ 'icon_mk' ]          = get_template_directory_uri() . '/img/mk-single.png';
            $data_map[ 0 ][ 'content_html' ]     = preg_replace( '/^\s+|\n|\r|\s+$/m' , '' , st()->load_template( 'vc-elements/st-list-map/loop/hotel' , false , array( 'post_type' => '' ) ) );
            $data_map[ 0 ][ 'content_adv_html' ] = preg_replace( '/^\s+|\n|\r|\s+$/m' , '' , st()->load_template( 'vc-elements/st-list-map/loop-adv/hotel' , false , array( 'post_type' => '' ) ) );
            $stt                                 = 1;
            global $post;
            if(!empty( $data )) {
                foreach( $data as $post ) :
                    setup_postdata( $post );
                    $map_lat = get_post_meta( get_the_ID() , 'map_lat' , true );
                    $map_lng = get_post_meta( get_the_ID() , 'map_lng' , true );
                    if(!empty( $map_lat ) and !empty( $map_lng ) and is_numeric( $map_lat ) and is_numeric( $map_lng )) {
                        $post_type                              = get_post_type();
                        $data_map[ $stt ][ 'id' ]               = get_the_ID();
                        $data_map[ $stt ][ 'name' ]             = get_the_title();
                        $data_map[ $stt ][ 'post_type' ]        = $post_type;
                        $data_map[ $stt ][ 'lat' ]              = $map_lat;
                        $data_map[ $stt ][ 'lng' ]              = $map_lng;
                        $data_map[ $stt ][ 'icon_mk' ]          = st()->get_option( 'st_hotel_icon_map_marker' , 'http://maps.google.com/mapfiles/marker_black.png' );
                        $data_map[ $stt ][ 'content_html' ]     = preg_replace( '/^\s+|\n|\r|\s+$/m' , '' , st()->load_template( 'vc-elements/st-list-map/loop/hotel' , false , array( 'post_type' => '' ) ) );
                        $data_map[ $stt ][ 'content_adv_html' ] = preg_replace( '/^\s+|\n|\r|\s+$/m' , '' , st()->load_template( 'vc-elements/st-list-map/loop-adv/hotel' , false , array( 'post_type' => '' ) ) );
                        $stt++;
                    }
                endforeach;
                wp_reset_postdata();
            }
            $properties = $hotel->properties_near_by(get_the_ID(), $lat, $lng, $range);
            if( !empty($properties)){
                foreach($properties as $key => $val){
                    $data_map[] = array(
                        'id' => get_the_ID(),
                        'name' => $val['name'],
                        'post_type' => 'st_hotel',
                        'lat' => (float)$val['lat'],
                        'lng' => (float)$val['lng'],
                        'icon_mk' => (empty($val['icon']))? 'http://maps.google.com/mapfiles/marker_black.png': $val['icon'],
                        'content_html' => preg_replace( '/^\s+|\n|\r|\s+$/m' , '' , st()->load_template( 'vc-elements/st-list-map/loop-adv/property' , null , array( 'post_type' => '', 'data' => $val ) ) ),
                        'content_adv_html' => preg_replace( '/^\s+|\n|\r|\s+$/m' , '' , st()->load_template( 'vc-elements/st-list-map/loop-adv/property' , null , array( 'post_type' => '', 'data' => $val ) ) ),
                    );
                }
            }
            if($location_center == '[,]')
                $location_center = '[0,0]';
            if($show_circle == 'no') {
                $range = 0;
            }
            $data_tmp               = array(
                'location_center' => $location_center ,
                'zoom'            => $zoom ,
                'data_map'        => $data_map ,
                'height'          => 500 ,
                'style_map'       => 'normal' ,
                'number'          => $number ,
                'range'           => $range ,
            );
            $data_tmp[ 'data_tmp' ] = $data_tmp;
            $html                   = '<div class="map_single">'.st()->load_template( 'hotel/elements/detail' , 'map' , $data_tmp ).'</div>';
            return $html;
        }
    }
}

st_reg_shortcode( 'st_hotel_detail_map' , 'st_hotel_detail_map' );





/**
 * NEW STYLE SINGLE HOTEL
 */

if(!function_exists('st_vc_map_gallery')){
    function st_vc_map_gallery($atts, $content = false){
        wp_enqueue_script( 'owl-carousel.js');
        $output = $style = $map_style = $num_image = $extra_class = $style_tour = $service_type_el = '';
        extract(shortcode_atts(array(
            'map_style' => 'style_icy_blue',
            'style' => 'full_map',
            'num_image' => '3',
            'extra_class' => '',
        ), $atts));
        $output .= '<div class="st-hotel-map-gallery '.$extra_class.'">';
        $output .= st()->load_template('hotel/elements/map_gallery_'.$style, false, array(
            'style' => $style,
            'num_image' => $num_image,
            'map_style' => $map_style,
        ));
        $output .= '</div>';
        return $output;
    }
}

st_reg_shortcode('st_hotel_map_gallery', 'st_vc_map_gallery');
if(function_exists( 'vc_map' )) {
    vc_map(array(
        'name' => esc_html__('ST Hotel Map And Gallery',ST_TEXTDOMAIN),
        'base' => 'st_hotel_map_gallery',
        'category' => '[ST] Single Hotel',
        'icon' => 'icon-st',
        'description' => esc_html__('Display map and gallery in hotel single',ST_TEXTDOMAIN),
        'params' => array(
            array(
                'type' => 'dropdown',
                'admin_label' => true,
                'heading' => esc_html__('Style',ST_TEXTDOMAIN),
                'param_name' => 'style',
                'description' => esc_html__('Select a style',ST_TEXTDOMAIN),
                'value' => array(
                    esc_html__('Full Map', ST_TEXTDOMAIN) => 'full_map',
                    esc_html__('Half Map', ST_TEXTDOMAIN) => 'half_map'
                ),
                'std' => 'full_map',
            ),
            array(
                'type' => 'textfield',
                'admin_label' => true,
                'heading' => esc_html__('Number Image',ST_TEXTDOMAIN),
                'param_name' => 'num_image',
                'description' => esc_html__('Max image for gallery',ST_TEXTDOMAIN),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Map Style', ST_TEXTDOMAIN),
                'param_name' => 'map_style',
                'description' => esc_html__('Select a style for map', ST_TEXTDOMAIN),
                'value' => array(
                    esc_html__('Normal', ST_TEXTDOMAIN) => 'style_normal',
                    esc_html__('Midnight', ST_TEXTDOMAIN) => 'style_midnight',
                    esc_html__('Icy Blue', ST_TEXTDOMAIN) => 'style_icy_blue',
                    esc_html__('Family Fest', ST_TEXTDOMAIN) => 'style_family_fest',
                    esc_html__('Open Dark', ST_TEXTDOMAIN) => 'style_open_dark',
                    esc_html__('Riverside', ST_TEXTDOMAIN) => 'style_riverside',
                    esc_html__('Ozan', ST_TEXTDOMAIN) => 'style_ozan'
                ),
                'std' => 'style_icy_blue'
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__('Extra Class', ST_TEXTDOMAIN),
                'param_name' => 'extra_class',
                'description' => esc_html__('Style particular content element differently - add a class name and refer to it in custom CSS.', ST_TEXTDOMAIN)
            ),
        )
    ));
}


if(function_exists( 'vc_map' )) {
    vc_map( array(
        "name"            => esc_html__( "ST Hotel Title - Address" , ST_TEXTDOMAIN ) ,
        "base"            => "st_hotel_title_address" ,
        "icon"            => "icon-st" ,
        "category"        => '[ST] Single Hotel',
        'content_element'         => true ,
        'description' => esc_html__('Display title and address', ST_TEXTDOMAIN),
        "params"          => array(
            array(
                'type' => 'dropdown',
                'admin_label' => true,
                'heading' => esc_html__('Content Align','oceaus'),
                'param_name' => 'align',
                'description' => esc_html__('Select align content','oceaus'),
                'value' => array(
                    esc_html__('Center','oceaus') => 'text-center',
                    esc_html__('Left','oceaus') => 'text-left'
                )
            ),
            array(
                "type"        => "textfield" ,
                'admin_label' => true,
                "heading"     => esc_html__( "Extra Class" , ST_TEXTDOMAIN ) ,
                "param_name"  => "extra_class" ,
                "description" => ""
            ) ,
        )
    ) );
}

if(!function_exists( 'st_vc_hotel_title_address' )) {
    function st_vc_hotel_title_address($atts, $content = false)
    {
        $atts = shortcode_atts(array(
            'align' => 'text-center',
            'extra_class' => '',
        ),$atts);
        return st()->load_template( 'hotel/elements/title-address' , false , array( 'atts' => $atts ) );

    }
}
st_reg_shortcode('st_hotel_title_address', 'st_vc_hotel_title_address');


if(!function_exists('st_hotel_vc_review_score_list')){
    function st_hotel_vc_review_score_list($atts, $content = false){
        $output = $extra_class = '';
        extract(shortcode_atts(array(
            'extra_class' => ''
        ),$atts));
        $output .= '<div class="st-review-score-list text-center '.$extra_class.'">';
        $output .= st()->load_template( 'hotel/elements/review-score-list' , false , array( 'atts' => $atts ) );
        $output .= '</div>';
        return $output;
    }
}

st_reg_shortcode('st_hotel_review_score_list', 'st_hotel_vc_review_score_list');

if(function_exists( 'vc_map' )) {
    vc_map(array(
        'name' => esc_html__('ST Hotel Review Score List','oceaus'),
        'base' => 'st_hotel_review_score_list',
        'category' => '[ST] Single Hotel',
        'icon' => 'icon-st',
        'description' => esc_html__('Display list reviews score','oceaus'),
        'params' => array(
            array(
                'type' => 'textfield',
                'heading' => esc_html__('Extra Class', 'oceaus'),
                'param_name' => 'extra_class',
                'description' => esc_html__('Style particular content element differently - add a class name and refer to it in custom CSS.', 'oceaus')
            ),
        )
    ));
}

if(function_exists( 'vc_map' )) {
    vc_map( array(
        "name"            => esc_html__( "ST Hotel Tabs Content" , ST_TEXTDOMAIN ) ,
        "base"            => "st_hotel_tabs_content" ,
        "icon"            => "icon-st" ,
        "category"        => '[ST] Single Hotel',
        'content_element'         => true ,
        'description' => esc_html__('Display tabs content', ST_TEXTDOMAIN),
        "params"          => array(
            array(
                'type' => 'checkbox',
                'heading' => esc_html__('Display Tabs', ST_TEXTDOMAIN),
                'param_name' => 'display_tabs',
                'description' => esc_html__('Select tabs to show in single', ST_TEXTDOMAIN),
                'value' => array(
                    esc_html__('Overview', ST_TEXTDOMAIN) => 'overview',
                    esc_html__('Facilities', ST_TEXTDOMAIN) => 'facilities',
                    esc_html__('Policies & FAQ', ST_TEXTDOMAIN) => 'policies_fqa',
                    esc_html__('Reviews', ST_TEXTDOMAIN) => 'reviews',
                    esc_html__('Gallery', ST_TEXTDOMAIN) => 'gallery',
                    esc_html__('Check Availability', ST_TEXTDOMAIN) => 'check_availability',
                ),
                'std' => 'overview,facilities,policies_fqa,reviews,gallery,check_availability'
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Tab Align','oceaus'),
                'param_name' => 'tab_align',
                'value' => array(
                    esc_html__('Center','oceaus') => '',
                    esc_html__('Left','oceaus') => 'text-left'
                ),
                'std' => ''
            ),
            array(
                "type"        => "textfield" ,
                'admin_label' => true,
                "heading"     => esc_html__( "Extra Class" , ST_TEXTDOMAIN ) ,
                "param_name"  => "extra_class" ,
                "description" => ""
            ) ,
        )
    ) );
}

if(!function_exists( 'st_vc_hotel_tabs_content' )) {
    function st_vc_hotel_tabs_content($atts, $content = false)
    {
        $atts = shortcode_atts(array(
            'extra_class' => '',
            'tab_align' => '',
            'display_tabs' => 'overview,facilities,policies_fqa,reviews,gallery,check_availability'
        ),$atts);
        return st()->load_template( 'hotel/elements/tabs-content/tabs-content' , false , array( 'atts' => $atts ) );

    }
    st_reg_shortcode('st_hotel_tabs_content', 'st_vc_hotel_tabs_content');
}


if(!function_exists('st_vc_hotel_more_info')){
    function st_vc_hotel_more_info($atts, $content = false){
        $output = $extra_class = $title = $style = $icon = '';
        extract(shortcode_atts(array(
            'style' => 'style-1',
            'icon' => '',
            'title' => '',
            'extra_class' => ''
        ),$atts));
        $output .= '<div class="st-more-info '.$style.' '.$extra_class.'">';
        $output .= st()->load_template('hotel/elements/more-information',false, array(
            'icon' => $icon,
            'style' => $style,
            'title' => $title,
            'content' => wpb_js_remove_wpautop($content)
        ));
        $output .= '</div>';
        return $output;
    }
}

st_reg_shortcode('st_hotel_more_info', 'st_vc_hotel_more_info');
if(function_exists( 'vc_map' )) {
    vc_map(array(
        'name' => esc_html__('ST Information','oceaus'),
        'base' => 'st_hotel_more_info',
        'category' => array('Shinetheme','[ST] Single Hotel'),
        'icon' => 'icon-st',
        'description' => esc_html__('More information for accommodation single','oceaus'),
        'params' => array(
            array(
                'type' => 'dropdown',
                'admin_label' => true,
                'heading' => esc_html__('Style','oceaus'),
                'param_name' => 'style',
                'value' => array(
                    esc_html__('Normal','oceaus') => 'style-1',
                    esc_html__('More Icon','oceaus') => 'style-2'
                )
            ),
            array(
                "type" => "iconpicker",
                "heading" => esc_html__("Icon", 'oceaus'),
                "param_name" => "icon",
                "description" => esc_html__("Icon", 'oceaus'),
                'dependency' => array(
                    'element' => 'style',
                    'value' => array('style-2')
                )
            ),
            array(
                'type' => 'textfield',
                'admin_label' => true,
                'param_name' => 'title',
                'heading' => esc_html__('Title','oceaus')
            ),
            array(
                'type' => 'textarea_html',
                'param_name' => 'content',
                'heading' => esc_html__('Content','oceaus')
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__('Extra Class', 'oceaus'),
                'param_name' => 'extra_class',
                'description' => esc_html__('Style particular content element differently - add a class name and refer to it in custom CSS.', 'oceaus')
            ),
        )
    ));
}

if(!function_exists('st_shortcode_lists')){
    function st_shortcode_lists($atts, $content = false){
        return '<div class="st-list">'.wpb_js_remove_wpautop($content,true).'</div>';
    }
}
st_reg_shortcode('st_lists', 'st_shortcode_lists');

/**
 * ST Tour Share
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( "ST Hotel Share" , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_hotel_share' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Hotel' ,
            'show_settings_on_create' => false,
            'params'=>array(
                array(
                    'type' => 'textfield',
                    'heading' => esc_html__('Extra Class', ST_TEXTDOMAIN),
                    'param_name' => 'extra_class'
                )
            )
        )
    );
}
if(!function_exists( 'st_vc_hotel_share' )) {
    function st_vc_hotel_share($atts, $content = false){
        $atts = shortcode_atts(array(
            'extra_class' => ''
        ),$atts);

        extract($atts);
        if(is_singular( 'st_hotel' ) || is_singular( 'location' ) || is_singular( 'page' )) {

            return '<div class="package-info tour_share style-2 '.$extra_class.'" style="clear: both;text-align: right">
                    ' . st()->load_template( 'hotel/share' ) . '
                </div>';
        }
    }
}
st_reg_shortcode( 'st_hotel_share' , 'st_vc_hotel_share' );

if(function_exists( 'vc_map' )) {
    vc_map( array(
        "name"            => esc_html__( "ST Hotel Contact Info" , ST_TEXTDOMAIN ) ,
        "base"            => "st_hotel_contact_info" ,
        "icon"            => "icon-st" ,
        "category"        => '[ST] Single Hotel',
        'content_element'         => true ,
        'description' => esc_html__('Display Contact Info', ST_TEXTDOMAIN),
        "params"          => array(
            array(
                "type"        => "textfield" ,
                'admin_label' => true,
                "heading"     => esc_html__( "Extra Class" , ST_TEXTDOMAIN ) ,
                "param_name"  => "extra_class" ,
                "description" => ""
            ) ,
        )
    ) );
}

if(!function_exists( 'st_vc_hotel_contact_info' )) {
    function st_vc_hotel_contact_info($atts, $content = false)
    {
        $atts = shortcode_atts(array(
            'extra_class' => '',
        ),$atts);
        return st()->load_template( 'hotel/elements/contact-info' , false , array( 'atts' => $atts ) );

    }
}
st_reg_shortcode('st_hotel_contact_info', 'st_vc_hotel_contact_info');