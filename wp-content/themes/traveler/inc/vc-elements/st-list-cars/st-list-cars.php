<?php
if(!st_check_service_available( 'st_cars' )) {
    return;
}
if(function_exists( 'vc_map' ) and class_exists( 'TravelerObject' )) {
    $list_taxonomy = st_list_taxonomy( 'st_cars' );
    $list_taxonomy = array_merge( array( "--Select--" => "" ) , $list_taxonomy );

    $list_location                                              = TravelerObject::get_list_location();
    $list_location_data[ __( '-- Select --' , ST_TEXTDOMAIN ) ] = '';
    if(!empty( $list_location )) {
        foreach( $list_location as $k => $v ) {
            $list_location_data[ $v[ 'title' ] ] = $v[ 'id' ];
        }
    }

    $param = array(
        array(
            "type"        => "textfield" ,
            'admin_label' => true,
            "heading"     => __( "List ID in Car" , ST_TEXTDOMAIN ) ,
            "param_name"  => "st_ids" ,
            "description" => __( "Ids separated by commas" , ST_TEXTDOMAIN ) ,
            'value'       => "" ,
        ) ,
        //        array(
        //            "type"        => "dropdown" ,
        //            "holder"      => "div" ,
        //            "heading"     => __( "Select Taxonomy" , ST_TEXTDOMAIN ) ,
        //            "param_name"  => "taxonomy" ,
        //            "description" => "" ,
        //            "value"       => st_list_taxonomy( 'st_cars' ) ,
        //        ) ,
        array(
            "type"        => "textfield" ,
            'admin_label' => true,
            "heading"     => __( "Number cars" , ST_TEXTDOMAIN ) ,
            "param_name"  => "st_number_cars" ,
            "description" => "" ,
            'value'       => 4 ,
        ) ,
        array(
            "type"             => "dropdown" ,
            'admin_label' => true,
            "heading"          => __( "Order By" , ST_TEXTDOMAIN ) ,
            "param_name"       => "st_orderby" ,
            "description"      => "" ,
            'edit_field_class' => 'vc_col-sm-6' ,
            'value'            => function_exists( 'st_get_list_order_by' ) ? st_get_list_order_by(
                array(
                    __( 'Sale' , ST_TEXTDOMAIN )     => 'sale' ,
                    __( 'Featured' , ST_TEXTDOMAIN ) => 'featured' ,
                )
            ) : array() ,
        ) ,
        array(
            "type"             => "dropdown" ,
            'admin_label' => true,
            "heading"          => __( "Order" , ST_TEXTDOMAIN ) ,
            "param_name"       => "st_order" ,
            'value'            => array(
                __( '--Select--' , ST_TEXTDOMAIN ) => '' ,
                __( 'Asc' , ST_TEXTDOMAIN )        => 'asc' ,
                __( 'Desc' , ST_TEXTDOMAIN )       => 'desc'
            ) ,
            'edit_field_class' => 'vc_col-sm-6' ,
        ) ,
        array(
            "type"             => "dropdown" ,
            'admin_label' => true,
            "heading"          => __( "Items per row" , ST_TEXTDOMAIN ) ,
            "param_name"       => "st_cars_of_row" ,
            'edit_field_class' => 'vc_col-sm-12' ,
            "value"            => array(
                __( '--Select--' , ST_TEXTDOMAIN ) => '' ,
                __( 'Four' , ST_TEXTDOMAIN )       => 4 ,
                __( 'Three' , ST_TEXTDOMAIN )      => 3 ,
                __( 'Two' , ST_TEXTDOMAIN )        => 2 ,
            ) ,
        ) ,
        array(
            "type"        => "st_list_location" ,
            'admin_label' => true,
            "heading"     => __( "Location" , ST_TEXTDOMAIN ) ,
            "param_name"  => "st_location" ,
            "description" => __( "Location" , ST_TEXTDOMAIN )
        ) ,

    );

    $list_tax = TravelHelper::get_object_taxonomies_service('st_cars');
    if( !empty( $list_tax ) ){
        foreach( $list_tax as $name => $label ){
            $terms_list = array();
            $terms = get_terms( $name, array(
                'hide_empty' => true,
            ) );
            foreach( $terms as $key => $val){
                $terms_list[$val->name] = $val->term_id;
            }
            if( !empty( $terms_list ) ){
                $param[] = array(
                    'type' => 'checkbox',
                    'heading' => $label,
                    'param_name' => 'taxonomies'.'--'.$name,
                    'value' => $terms_list
                );
            }
        }
    }

    vc_map( array(
        "name"            => __( "ST List of Cars" , ST_TEXTDOMAIN ) ,
        "base"            => "st_list_cars" ,
        "content_element" => true ,
        "icon"            => "icon-st" ,
        "category"        => "Shinetheme" ,
        "params"          => $param
    ) );
}
if(!function_exists( 'st_vc_list_cars' )) {
    function st_vc_list_cars( $attr , $content = false )
    {
        global $st_search_args;

        $param   = array(
            'st_ids'         => '' ,
            'taxonomy'       => '' ,
            'st_number_cars' => 4 ,
            'st_order'       => '' ,
            'st_orderby'     => '' ,
            'st_cars_of_row' => 4 ,
            'st_location'    => '' ,
            'only_featured_location'    => 'no' ,
        );
        $list_tax = TravelHelper::get_object_taxonomies_service('st_cars');
        if( !empty( $list_tax ) ){
            foreach( $list_tax as $name => $label ){
                $param['taxonomies--'. $name] = '';
            }
        }

        $data    = wp_parse_args( $attr , $param );
        extract( $data );
        $st_search_args=$data;
        $query = array(
            'post_type'      => 'st_cars' ,
            'posts_per_page' => $st_number_cars ,
            'order'          => $st_order ,
            'orderby'        => $st_orderby
        );

        $st_search_args['featured_location']=STLocation::inst()->get_featured_ids();
        $cars=STCars::get_instance();
        $cars->alter_search_query();
        global $wp_query;
        query_posts( $query );
        $txt = '';
        while( have_posts() ) {
            the_post();
            $txt .= st()->load_template( 'vc-elements/st-list-cars/loop' , 'list' , array(
                'attr'  => $attr ,
                'data_' => $data
            ) );;
        }
        wp_reset_query();
        $cars->remove_alter_search_query();
        $st_search_args=null;
        return '<div class="row row-wrap">' . $txt . '</div>';
    }
}
if(st_check_service_available( 'st_cars' )) {
    st_reg_shortcode( 'st_list_cars' , 'st_vc_list_cars' );
}