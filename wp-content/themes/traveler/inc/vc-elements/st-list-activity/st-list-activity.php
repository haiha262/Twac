<?php
if(!st_check_service_available( 'st_activity' )) {
    return;
}
if(function_exists( 'vc_map' ) and class_exists( 'TravelerObject' )) {
    $list_taxonomy = st_list_taxonomy( 'st_activity' );

    $list_location                                              = TravelerObject::get_list_location();
    $list_location_data[ __( '-- Select --' , ST_TEXTDOMAIN ) ] = '';
    if(!empty( $list_location )) {
        foreach( $list_location as $k => $v ) {
            $list_location_data[ $v[ 'title' ] ] = $v[ 'id' ];
        }
    }
    $params  = array(

        array(
            "type"        => "textfield" ,
            'admin_label' => true,
            "heading"     => __( "List IDs of Activity (Optional)" , ST_TEXTDOMAIN ) ,
            "param_name"  => "st_ids" ,
            "description" => __( "Ids separated by commas" , ST_TEXTDOMAIN ) ,
            'value'       => "" ,
        ) ,
        array(
            "type"             => "textfield" ,
            'admin_label' => true,
            "heading"          => __( "Number" , ST_TEXTDOMAIN ) ,
            "param_name"       => "st_number" ,
            "description"      => "" ,
            'value'            => 4 ,
            'edit_field_class' => 'vc_col-sm-3' ,
        ) ,
        array(
            "type"             => "dropdown" ,
            'admin_label' => true,
            "heading"          => __( "Order By" , ST_TEXTDOMAIN ) ,
            "param_name"       => "st_orderby" ,
            "description"      => "" ,
            'edit_field_class' => 'vc_col-sm-3' ,
            'value'            => function_exists( 'st_get_list_order_by' ) ? st_get_list_order_by(
                array(
                    __( 'Price' , ST_TEXTDOMAIN )         => 'sale' ,
                    __( 'Rate' , ST_TEXTDOMAIN )          => 'rate' ,
                    __( 'Featured' , ST_TEXTDOMAIN ) => 'featured' ,
                    /*__( 'Discount rate' , ST_TEXTDOMAIN ) => 'discount'*/
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
            'edit_field_class' => 'vc_col-sm-3' ,
        ) ,
        array(
            "type"             => "dropdown" ,
            'admin_label' => true,
            "heading"          => __( "Item per row" , ST_TEXTDOMAIN ) ,
            "param_name"       => "st_of_row" ,
            'edit_field_class' => 'vc_col-sm-3' ,
            "value"            => array(
                __( '--Select--' , ST_TEXTDOMAIN ) => '' ,
                __( 'One' , ST_TEXTDOMAIN ) => '1' ,
                __( 'Four' , ST_TEXTDOMAIN )       => '4' ,
                __( 'Three' , ST_TEXTDOMAIN )      => '3' ,
                __( 'Two' , ST_TEXTDOMAIN )        => '2' ,
            ) ,
        ) ,
        array(
            "type"       => "dropdown" ,
            'admin_label' => true,
            "heading"    => __( "Only in Featured Location" , ST_TEXTDOMAIN ) ,
            "param_name" => "only_featured_location" ,
            "value"      => array(
                __( '--Select--' , ST_TEXTDOMAIN ) => '' ,
                __( 'No' , ST_TEXTDOMAIN )         => 'no' ,
                __( 'Yes' , ST_TEXTDOMAIN )        => 'yes' ,
            ) ,
        ) ,
        array(
            "type"        => "st_list_location" ,
            'admin_label' => true,
            "heading"     => __( "Location" , ST_TEXTDOMAIN ) ,
            "param_name"  => "st_location" ,
            "description" => __( "Location" , ST_TEXTDOMAIN ) ,
            "dependency"  =>
                array(
                    "element" => "only_featured_location" ,
                    "value"   => "no"
                ) ,
        ) ,
    );
    $list_tax = TravelHelper::get_object_taxonomies_service('st_activity');
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
                $params[] = array(
                    'type' => 'checkbox',
                    'heading' => $label,
                    'param_name' => 'taxonomies'.'--'.$name,
                    'value' => $terms_list
                );
            }
            
        }
    }

    vc_map( array(
        "name"            => __( "ST List of Activities" , ST_TEXTDOMAIN ) ,
        "base"            => "st_list_activity" ,
        "content_element" => true ,
        "icon"            => "icon-st" ,
        "category"        => "Shinetheme" ,
        "params"          => $params
    ) );
}

if(!function_exists( 'st_vc_list_activity' )) {
    function st_vc_list_activity( $attr , $content = false )
    {
        $data_vc = STActivity::get_taxonomy_and_id_term_activity();
        global $st_search_args;
        $param = array(
            'st_ids'                 => "" ,
            'st_number'              => 4 ,
            'st_order'               => '' ,
            'st_orderby'             => '' ,
            'st_of_row'              => 4 ,
            'only_featured_location' => 'no' ,
            'st_location'            => '' ,
            'sort_taxonomy'          => '' ,
        );
        $list_tax = TravelHelper::get_object_taxonomies_service('st_activity');
        if( !empty( $list_tax ) ){
            foreach( $list_tax as $name => $label ){
                $param['taxonomies--'. $name] = '';
            }
        }
        $data  = shortcode_atts( $param , $attr , 'st_list_activity' );
        extract( $data );
        $st_search_args=$data;


        $page = STInput::request( 'paged' );
        if(!$page) {
            $page = get_query_var( 'paged' );
        }
        $query = array(
            'post_type'      => 'st_activity' ,
            'posts_per_page' => $st_number ,
            'paged'          => $page ,
            'order'          => $st_order ,
            'orderby'        => $st_orderby
        );
        $st_search_args['featured_location']=STLocation::inst()->get_featured_ids();

        $activity = STActivity::inst();

        $activity->alter_search_query();
        query_posts( $query );

        $r = "<div class='list_tours'>" . st()->load_template( 'vc-elements/st-list-activity/loop' , '' , $data ) . "</div>";

        $activity->remove_alter_search_query();
        wp_reset_query();
        $st_search_args=FALSE;

        return $r;
    }
}
if(st_check_service_available( 'st_activity' )) {
    st_reg_shortcode( 'st_list_activity' , 'st_vc_list_activity' );
}
