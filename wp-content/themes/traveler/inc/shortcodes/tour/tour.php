<?php
/**
 * Created by PhpStorm.
 * User: me664
 * Date: 12/15/14
 * Time: 9:44 AM
 */
if(!st_check_service_available( 'st_tours' )) {
    return;
}
/**
 * ST Thumbnail Tour
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( "ST Tour Thumbnail" , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_thumbnail_tours' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Tour' ,
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

if(!function_exists( 'st_thumbnail_tours_func' )) {
    function st_thumbnail_tours_func()
    {
        if(is_singular( 'st_tours' )) {
            return st()->load_template( 'tours/elements/image' , 'featured' );
        }
    }

    st_reg_shortcode( 'st_thumbnail_tours' , 'st_thumbnail_tours_func' );
}

/**
 * ST Excerpt Tour
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( "ST Tour Excerpt" , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_excerpt_tour' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Tour' ,
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
if(!function_exists( 'st_excerpt_tours_func' )) {
    function st_excerpt_tours_func( $attr = array() )
    {
        if(is_singular( 'st_tours' )) {
            $default = array(
                'title'     => '' ,
                'font_size' => '3' ,
            );
            extract( wp_parse_args( $attr , $default ) );
            while(have_posts())
            {
                the_post();
                $html = '<div class="center">' . get_the_excerpt() . "</div>";
                if(!empty( $title ) and !empty( $html )) {
                    $html = '<h' . $font_size . '>' . $title . '</h' . $font_size . '>' . $html;
                }
            }
            return $html;
        }
    }

    st_reg_shortcode( 'st_excerpt_tour' , 'st_excerpt_tours_func' );
}


/**
 * ST Tour Content
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( "ST Tour Content" , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_tour_content' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Tour' ,
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
if(!function_exists( 'st_tour_content_func' )) {
    function st_tour_content_func( $attr = array() )
    {
        if(is_singular( 'st_tours' )) {
            $default = array(
                'title'     => '' ,
                'font_size' => 1 ,
            );
            extract( wp_parse_args( $attr , $default ) );
            $html = st()->load_template( 'tours/elements/content' , 'tours' );
            if(!empty( $title ) and !empty( $html )) {
                $html = '<h' . $font_size . '>' . $title . '</h' . $font_size . '><div class="st_tour_content">' . $html."</div>";
            }
            return $html;
        }
    }

    st_reg_shortcode( 'st_tour_content' , 'st_tour_content_func' );
}

/**
 * ST Info Tour
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( "ST Tour Info" , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_info_tours' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Tour' ,
            'show_settings_on_create' => false ,
            'params'                  => array(
                array(
                    "type"        => "dropdown" ,
                    "holder"      => "div" ,
                    "heading"     => __( "Style" , ST_TEXTDOMAIN ) ,
                    "param_name"  => "style" , 
                    "value"       => array(
                        __( "--Select--" , ST_TEXTDOMAIN )  => "" ,
                        __( "Style 1" , ST_TEXTDOMAIN ) => "1",
                        __( "Style 2" , ST_TEXTDOMAIN ) => "2"
                    ) ,
                ) ,
                array(
                    "type"        => "textfield" ,
                    "holder"      => "div" ,
                    "heading"     => __( "Title 1" , ST_TEXTDOMAIN ) ,
                    "param_name"  => "title1" ,                     
                    'dependency' => array(
                        'element' => 'style',
                        'value' => array( '2' )
                    ),
                ) ,
                array(
                    "type"        => "textfield" ,
                    "holder"      => "div" ,
                    "heading"     => __( "Title 2" , ST_TEXTDOMAIN ) ,
                    "param_name"  => "title2" ,                     
                    'dependency' => array(
                        'element' => 'style',
                        'value' => array( '2' )
                    ),
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
                    'dependency' => array(
                        'element' => 'style',
                        'value' => array( '2' )
                    ),
                ) ,
            )
                
        )
    );
}
if(!function_exists( 'st_info_tours_func' )) {
    function st_info_tours_func($attr)
    {
        if(is_singular( 'st_tours' )) {
            $default = array(
                'style'      => '' , 
                'font_size' => 3,
                'title1'    => __("Tour Informations" , ST_TEXTDOMAIN),
                'title2'    => __("Place Order" , ST_TEXTDOMAIN)
            );
			$dump = wp_parse_args( $attr , $default );
            extract( $dump );
            if ($style ==1) $style ="";
            return st()->load_template( 'tours/elements/info-tours' , $style, array('attr'=> $attr));
        }
    }

    st_reg_shortcode( 'st_info_tours' , 'st_info_tours_func' );
}

/**
 * ST Tour Detail Map
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( "ST Detailed Tour Map" , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_tour_detail_map' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Tour' ,
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
                        __( "No" , ST_TEXTDOMAIN )  => "no" ,
                        __( "Yes" , ST_TEXTDOMAIN ) => "yes"
                    ) ,
                )
            )
        )
    );
}

if(!function_exists( 'st_tour_detail_map' )) {
    function st_tour_detail_map($attr)
    {
        if(is_singular( 'st_tours' )) {

            $default = array(
                'number'      => '12' ,
                'range'       => '20' ,
                'show_circle' => 'no' ,
            );
			$dump = wp_parse_args( $attr , $default ) ;
            extract( $dump);
            $lat   = get_post_meta( get_the_ID() , 'map_lat' , true );
            $lng   = get_post_meta( get_the_ID() , 'map_lng' , true );
            $zoom  = get_post_meta( get_the_ID() , 'map_zoom' , true );
            $class = new STTour();
            $data  = $class->get_near_by( get_the_ID() , $range , $number );
            $location_center                     = '[' . $lat . ',' . $lng . ']';
            $data_map                            = array();
            $data_map[ 0 ][ 'id' ]               = get_the_ID();
            $data_map[ 0 ][ 'name' ]             = get_the_title();
            $data_map[ 0 ][ 'post_type' ]        = get_post_type();
            $data_map[ 0 ][ 'lat' ]              = $lat;
            $data_map[ 0 ][ 'lng' ]              = $lng;
            $data_map[ 0 ][ 'icon_mk' ]          = get_template_directory_uri() . '/img/mk-single.png';
            $data_map[ 0 ][ 'content_html' ]     = preg_replace( '/^\s+|\n|\r|\s+$/m' , '' , st()->load_template( 'vc-elements/st-list-map/loop/tour' , false , array( 'post_type' => '' ) ) );
            $data_map[ 0 ][ 'content_adv_html' ] = preg_replace( '/^\s+|\n|\r|\s+$/m' , '' , st()->load_template( 'vc-elements/st-list-map/loop-adv/tour' , false , array( 'post_type' => '' ) ) );
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
                        $data_map[ $stt ][ 'icon_mk' ]          = st()->get_option( 'st_tours_icon_map_marker' , 'http://maps.google.com/mapfiles/marker_yellow.png' );
                        $data_map[ $stt ][ 'content_html' ]     = preg_replace( '/^\s+|\n|\r|\s+$/m' , '' , st()->load_template( 'vc-elements/st-list-map/loop/tour' , false , array( 'post_type' => '' ) ) );
                        $data_map[ $stt ][ 'content_adv_html' ] = preg_replace( '/^\s+|\n|\r|\s+$/m' , '' , st()->load_template( 'vc-elements/st-list-map/loop-adv/tour' , false , array( 'post_type' => '' ) ) );
                        $stt++;
                    }
                endforeach;
                wp_reset_postdata();
            }
            $properties = $class->properties_near_by(get_the_ID(), $lat, $lng, $range);
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
            $html                   = st()->load_template( 'hotel/elements/detail' , 'map' , $data_tmp );
            return $html;
        }
    }
    st_reg_shortcode( 'st_tour_detail_map' , 'st_tour_detail_map' );
}

/**
 * ST Tour Detail Review Summary
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( "ST Tour Review Summary" , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_tour_detail_review_summary' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Tour' ,
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
if(!function_exists( 'st_tour_detail_review_summary' )) {
    function st_tour_detail_review_summary()
    {

        if(is_singular( 'st_tours' )) {
            return st()->load_template( 'tours/elements/review_summary' );
        }
    }
}

/**
 * ST Tour Detail Review Detail
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( "ST Detailed Tour Review" , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_tour_detail_review_detail' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Tour' ,
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
if(!function_exists( 'st_tour_detail_review_detail' )) {
    function st_tour_detail_review_detail()
    {
        if(is_singular( 'st_tours' )) {
            return st()->load_template( 'tours/elements/review_detail' );
        }
    }
}


/**
 * ST Tour Program
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( "ST Tour Program" , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_tour_program' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Tour' ,
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
if(!function_exists( 'st_tour_program' )) {
    function st_tour_program( $attr = array() )
    {
        if(is_singular( 'st_tours' )) {
            $default = array(
                'title'     => '' ,
                'font_size' => '3' ,
            );
            extract( wp_parse_args( $attr , $default ) );
            $html = st()->load_template( 'tours/elements/program' );
            if(!empty( $title ) and !empty( $html )) {
                $html = '<h' . $font_size . '>' . $title . '</h' . $font_size . '>' . $html;
            }
            return $html;

        }
    }
}
st_reg_shortcode( 'st_tour_program' , 'st_tour_program' );

/**
 * ST Tour Share
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( "ST Tour Share" , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_tour_share' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Tour' ,
            'show_settings_on_create' => false,
            'params'=>array(
                array(
                    'type' => 'dropdown',
                    'heading' => esc_html__('Style', ST_TEXTDOMAIN),
                    'param_name' => 'style',
                    'description' => esc_html__('Select a style', ST_TEXTDOMAIN),
                    'value' => array(
                        esc_html__('Style 1', ST_TEXTDOMAIN) => 'style-1',
                        esc_html__('Style 2', ST_TEXTDOMAIN) => 'style-2'
                    ),
                    'std' => 'style-1'
                ),
                array(
                    'type' => 'textfield',
                    'heading' => esc_html__('Extra Class', ST_TEXTDOMAIN),
                    'param_name' => 'extra_class'
                )
            )
        )
    );
}
if(!function_exists( 'st_tour_share' )) {
    function st_tour_share($atts, $content = false){
        $atts = shortcode_atts(array(
            'style' => 'style-1',
            'extra_class' => ''
        ),$atts);

        extract($atts);
        if(is_singular( 'st_tours' )) {

            return '<div class="package-info tour_share '.$style.' '.$extra_class.'" style="clear: both;text-align: right">
                    ' . st()->load_template( 'hotel/share' ) . '
                </div>';
        }
    }
}
st_reg_shortcode( 'st_tour_share' , 'st_tour_share' );


/**
 * ST Tour Review
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( "ST Tour Review" , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_tour_review' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Tour' ,
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

if(!function_exists( 'st_tour_review' )) {
    function st_tour_review( $attr = array() )
    {
        if(is_singular( 'st_tours' )) {
            $default = array(
                'title'     => '' ,
                'font_size' => '3' ,
            );
            extract( wp_parse_args( $attr , $default ) );
            if(comments_open() and st()->get_option( 'activity_tour_review' ) != 'off') {
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


/**
 * ST Tour Price
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( "ST Tour Price" , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_tour_price' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Tour' ,
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

if(!function_exists( 'st_tour_price' )) {

    function st_tour_price( $attr = array() )
    {
        if(is_singular( 'st_tours' )) {
            return st()->load_template( 'tours/elements/price' );
        }
    }
}


/**
 * ST Tour Video
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( "ST Tour Video" , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_tour_video' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Tour' ,
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
if(!function_exists( 'st_tour_video' )) {
    function st_tour_video( $attr = array() )
    {
        if(is_singular( 'st_tours' )) {
            if($video = get_post_meta( get_the_ID() , 'video' , true )) {
                return "<div class='media-responsive'>" . wp_oembed_get( $video ) . "</div>";
            }
        }
    }
}

/**
 * ST Tour Nearby
 * @since 1.1.0
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( "ST Tour Nearby" , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_tour_nearby' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Tour' ,
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
                    'admin_label' => true,
                    "heading"          => __( "Title" , ST_TEXTDOMAIN ) ,
                    "param_name"       => "title" ,
                    "description"      => "" ,
                    "value"            => "" ,
                    'edit_field_class' => 'vc_col-sm-6' ,
                    'dependency' => array(
                        'element' => 'style',
                        'value' => array('style-1')
                    )
                ) ,
                array(
                    "type"             => "dropdown" ,
                    'admin_label' => true,
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
                    'dependency' => array(
                        'element' => 'style',
                        'value' => array('style-1')
                    )
                ) ,
            )
        )
    );
}
if(!function_exists( 'st_tour_nearby' )) {
    function st_tour_nearby( $arg = array() )
    {
        if(is_singular( 'st_tours' )) {
            $default = array(
                'style' => 'style-1',
                'title'     => '' ,
                'font_size' => '3' ,
            );
			$data = wp_parse_args( $arg , $default );
            extract( $data );
            return st()->load_template( 'tours/elements/nearby' , '' , $data );
        }
    }
}


st_reg_shortcode( 'st_tour_nearby' , 'st_tour_nearby' );


st_reg_shortcode( 'st_tour_video' , 'st_tour_video' );

st_reg_shortcode( 'st_tour_price' , 'st_tour_price' );


st_reg_shortcode( 'st_tour_review' , 'st_tour_review' );

st_reg_shortcode( 'st_tour_detail_list_schedules' , 'st_tour_detail_list_schedules' );


st_reg_shortcode( 'st_tour_detail_review_detail' , 'st_tour_detail_review_detail' );
st_reg_shortcode( 'st_tour_detail_review_summary' , 'st_tour_detail_review_summary' );

st_reg_shortcode( 'st_tour_detail_map' , 'st_tour_detail_map' );

/**
 * ST tours show discount
 * @since 1.1.9
 **/
if(function_exists( 'vc_map' )) {
    vc_map(
        array(
            'name'                    => __( 'ST Tour Show Discount' , ST_TEXTDOMAIN ) ,
            'base'                    => 'st_tour_show_discount' ,
            'content_element'         => true ,
            'icon'                    => 'icon-st' ,
            'category'                => 'Tour' ,
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


if(!function_exists( 'st_tour_show_discount' )) {
    function st_tour_show_discount()
    {
        if(is_singular( 'st_tours' )) {
            return st()->load_template( 'tours/elements/tour_show_info_discount' );
        }
    }
}
st_reg_shortcode( 'st_tour_show_discount' , 'st_tour_show_discount' );

/**
 * Version 1.4.6
 */

if(function_exists( 'vc_map' )) {
    vc_map( array(
        "name"            => esc_html__( "ST Tour Gallery Map" , ST_TEXTDOMAIN ) ,
        "base"            => "st_tour_gallery_map" ,
        "content_element" => true ,
        "icon"            => "icon-st" ,
        "category"        => 'Tour',
        'description' => esc_html__('Display gallery image and map', ST_TEXTDOMAIN),
        "params"          => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Style', ST_TEXTDOMAIN),
                'admin_label' => true,
                'param_name' => 'style',
                'description' => esc_html__('Select a style', ST_TEXTDOMAIN),
                'value' => array(
                    esc_html__('Default', ST_TEXTDOMAIN) => 'style-1',
                    esc_html__('Half Map', ST_TEXTDOMAIN) => 'half_map'
                ),
                'std' => 'default'
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
            )
        )
    ) );
}

if(!function_exists( 'st_vc_tour_gallery_map' )) {
    function st_vc_tour_gallery_map( $atts , $content = false )
    {
        $atts = shortcode_atts(array(
            'map_style' => 'style_icy_blue',
            'style' => 'style-1'
        ), $atts);

        return st()->load_template( 'tours/elements/gallery-map/gallery-map' , false , array( 'atts' => $atts ) );

    }
}
st_reg_shortcode('st_tour_gallery_map', 'st_vc_tour_gallery_map');

if(function_exists( 'vc_map' )) {
    vc_map( array(
        "name"            => esc_html__( "ST Tour Title - Address" , ST_TEXTDOMAIN ) ,
        "base"            => "st_tour_title_address" ,
        "icon"            => "icon-st" ,
        "category"        => 'Tour',
        'content_element'         => true ,
        'description' => esc_html__('Display title and address', ST_TEXTDOMAIN),
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

if(!function_exists( 'st_vc_tour_title_address' )) {
    function st_vc_tour_title_address($atts, $content = false)
    {
        $atts = shortcode_atts(array(
           'extra_class' => '',
        ),$atts);
        return st()->load_template( 'tours/elements/title-address/title-address' , false , array( 'atts' => $atts ) );

    }
    st_reg_shortcode('st_tour_title_address', 'st_vc_tour_title_address');
}

if(function_exists( 'vc_map' )) {
    vc_map( array(
        "name"            => esc_html__( "ST Tour List Info" , ST_TEXTDOMAIN ) ,
        "base"            => "st_tour_list_info" ,
        "icon"            => "icon-st" ,
        "category"        => 'Tour',
        'content_element'         => true ,
        'description' => esc_html__('Display list tour info', ST_TEXTDOMAIN),
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

if(!function_exists( 'st_vc_tour_list_info' )) {
    function st_vc_tour_list_info($atts, $content = false)
    {
        $atts = shortcode_atts(array(
            'extra_class' => '',
        ),$atts);
        return st()->load_template( 'tours/elements/list-info/list-info' , false , array( 'atts' => $atts ) );

    }
    st_reg_shortcode('st_tour_list_info', 'st_vc_tour_list_info');
}

if(function_exists( 'vc_map' )) {
    vc_map( array(
        "name"            => esc_html__( "ST Tour Tabs Content" , ST_TEXTDOMAIN ) ,
        "base"            => "st_tour_tabs_content" ,
        "icon"            => "icon-st" ,
        "category"        => 'Tour',
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
                    esc_html__('Itinerary', ST_TEXTDOMAIN) => 'itinerary',
                    esc_html__('FAQ & Reviews', ST_TEXTDOMAIN) => 'review',
                    esc_html__('Gallery', ST_TEXTDOMAIN) => 'gallery',
                    esc_html__('Prices & Payment', ST_TEXTDOMAIN) => 'payment',
                    esc_html__('Request To Book', ST_TEXTDOMAIN) => 'request',
                ),
                'std' => 'overview,itinerary,review,gallery,payment,request'
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

if(!function_exists( 'st_vc_tour_tabs_content' )) {
    function st_vc_tour_tabs_content($atts, $content = false)
    {
        $atts = shortcode_atts(array(
            'extra_class' => '',
            'display_tabs' => 'overview,itinerary,review,gallery,payment,request'
        ),$atts);
        return st()->load_template( 'tours/elements/tabs-content/tabs-content' , false , array( 'atts' => $atts ) );

    }
    st_reg_shortcode('st_tour_tabs_content', 'st_vc_tour_tabs_content');
}
