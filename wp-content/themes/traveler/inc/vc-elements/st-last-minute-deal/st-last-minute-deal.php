<?php
$list1 = ( STLocation::get_post_type_list_active() );

$list = array();
$list = array( __( '--Select--' , ST_TEXTDOMAIN ) => '' );
if(!empty( $list1 ) and is_array( $list1 )) {
    foreach( $list1 as $key => $value ) {
        if($value == 'st_cars') {
            $list[ __( 'Car' , ST_TEXTDOMAIN ) ] = $value;
        }
        if($value == 'st_tours') {
            $list[ __( 'Tour' , ST_TEXTDOMAIN ) ] = $value;
        }
        if($value == 'st_hotel') {
            $list[ __( 'Hotel' , ST_TEXTDOMAIN ) ] = $value;
        }
        if($value == 'st_rental') {
            $list[ __( 'Rental' , ST_TEXTDOMAIN ) ] = $value;
        }
        if($value == 'st_activity') {
            $list[ __( 'Activity' , ST_TEXTDOMAIN ) ] = $value;
        }
        if($value == 'hotel_room') {
            $list[ esc_html__('Room',ST_TEXTDOMAIN)] = $value;
        }
    }
}
if(function_exists( 'vc_map' )) {
    vc_map( array(
        "name"            => __( "ST Last Minute Deal" , ST_TEXTDOMAIN ) ,
        "base"            => "st_last_minute_deal" ,
        "content_element" => true ,
        "icon"            => "icon-st" ,
        "category"        => "Shinetheme" ,
        "params"          => array(
            array(
                "type"        => "dropdown" ,
                'admin_label' => true,
                "heading"     => __( "Post type" , ST_TEXTDOMAIN ) ,
                "param_name"  => "st_post_type" ,
                "description" => "" ,
                'value'       => $list ,
            ) ,
        )
    ) );
}
if(!function_exists( 'st_vc_last_minute_deal' )) {
    function st_vc_last_minute_deal( $attr , $content = false )
    {
        $data = shortcode_atts(
            array(
                'st_post_type' => 'st_hotel' ,
            ) , $attr , 'st_last_minute_deal' );
        extract( $data );
        $html = "";
        global $wpdb;
        $sql = null;
        $where = $join = "";
        
        $where = TravelHelper::edit_where_wpml($where);
        $date_now = strtotime(date('Y-m-d'));
        switch ( $st_post_type ) {
            case 'st_hotel':
                $date_hotel = strtotime(date('Y-m-d'));
                $join = TravelHelper::edit_join_wpml($join , 'hotel_room') ;
                if( !TravelHelper::checkTableDuplicate(array('st_hotel', 'hotel_room'))){
                    return '';
                }

                for($i = $date_hotel; $i <= strtotime('+15 days', $date_hotel); $i = strtotime('+1 day', $i)){
                    $sql = "SELECT
                        room.post_id,
                        room.room_parent,
                        room.discount_rate
                    FROM
                        {$wpdb->prefix}hotel_room AS room
                    INNER JOIN {$wpdb->prefix}st_hotel AS hotel ON hotel.post_id = room.room_parent
                    WHERE
                        CAST(
                            room.discount_rate AS UNSIGNED
                        ) > 0
                    AND room.post_id NOT IN (
                        SELECT
                            room_id
                        FROM
                            (
                                SELECT
                                    od.room_id,
                                    room1.number_room,
                                    od.room_num_search,
                                    sum(od.room_num_search),
                                    cast(
                                        room1.number_room AS UNSIGNED
                                    )
                                FROM
                                    {$wpdb->prefix}st_order_item_meta AS od
                                INNER JOIN {$wpdb->prefix}hotel_room AS room1 ON room1.post_id = od.room_id
                                WHERE
                                    1 = 1
                                AND (
                                    (
                                        check_in_timestamp <= {$i}
                                        AND check_out_timestamp >= {$i}
                                    )
                                    OR (
                                        check_in_timestamp >= {$i}
                                        AND check_in_timestamp <= {$i}
                                    )
                                )
                                AND od.check_out_timestamp
                                AND `status` NOT IN ('trash', 'canceled')
                                AND od.st_booking_post_type = 'st_hotel'
                                GROUP BY
                                    od.room_id
                                HAVING
                                    sum(od.room_num_search) >= cast(
                                        room1.number_room AS UNSIGNED
                                    )
                            ) AS room_id
                    )
                    AND room.post_id NOT IN (
                        SELECT
                            post_id
                        FROM
                            {$wpdb->prefix}st_availability
                        WHERE
                            1 = 1
                        AND (
                            check_in >= {$i}
                            AND check_out <= {$i}
                            AND `status` = 'unavailable'
                        )
                        AND post_type = 'hotel_room'
                    )
                    ORDER BY
                        room.discount_rate DESC
                    LIMIT 0,
                     1";
                    $rs = $wpdb->get_row($sql);
                    if(!empty($rs)){
                        $data['rs'] = $rs;
                        $data['date'] = $i;
                        $html =  st()->load_template('vc-elements/st-last-minute-deal/html',$st_post_type, $data);
                        return $html;
                    }
                }
                break;
            case 'hotel_room':
                $date_hotel = strtotime(date('Y-m-d'));
                $join = TravelHelper::edit_join_wpml($join , 'hotel_room') ;
                if( !TravelHelper::checkTableDuplicate(array('st_hotel', 'hotel_room')) ){
                    return '';
                }
                for($i = $date_hotel; $i <= strtotime('+15 days', $date_hotel); $i = strtotime('+1 day', $i)){
                    $sql = "SELECT
                        room.post_id,
                        room.room_parent,
                        room.discount_rate
                    FROM
                        {$wpdb->prefix}hotel_room AS room
                    WHERE
                        (
                            room.room_parent = 0
                            OR room.room_parent IS NULL
                        )
                    AND CAST(
                        room.discount_rate AS UNSIGNED
                    ) > 0
                    AND room.post_id NOT IN (
                        SELECT
                            room_id
                        FROM
                            (
                                SELECT
                                    od.room_id,
                                    room1.number_room,
                                    od.room_num_search,
                                    sum(od.room_num_search),
                                    cast(
                                        room1.number_room AS UNSIGNED
                                    )
                                FROM
                                    {$wpdb->prefix}st_order_item_meta AS od
                                INNER JOIN {$wpdb->prefix}hotel_room AS room1 ON room1.post_id = od.room_id
                                WHERE
                                    1 = 1
                                AND (
                                    (
                                        check_in_timestamp <= {$i}
                                        AND check_out_timestamp >= {$i}
                                    )
                                    OR (
                                        check_in_timestamp >= {$i}
                                        AND check_in_timestamp <= {$i}
                                    )
                                )
                                AND od.check_out_timestamp
                                AND `status` NOT IN ('trash', 'canceled')
                                AND od.st_booking_post_type = 'hotel_room'
                                GROUP BY
                                    od.room_id
                                HAVING
                                    sum(od.room_num_search) >= cast(
                                        room1.number_room AS UNSIGNED
                                    )
                            ) AS room_id
                    )
                    AND room.post_id NOT IN (
                        SELECT
                            post_id
                        FROM
                            {$wpdb->prefix}st_availability
                        WHERE
                            1 = 1
                        AND (
                            check_in >= {$i}
                            AND check_out <= {$i}
                            AND `status` = 'unavailable'
                        )
                        AND post_type = 'hotel_room'
                    )
                    ORDER BY
                        room.discount_rate DESC
                    LIMIT 0,
                     1";
                    $rs = $wpdb->get_row($sql);
                    if(!empty($rs)){
                        $data['rs'] = $rs;
                        $data['date'] = $i;
                        $html =  st()->load_template('vc-elements/st-last-minute-deal/html',$st_post_type, $data);
                        return $html;
                    }
                }
            break;
            case 'st_rental' : 
                $date_rental = strtotime(date('Y-m-d'));
                $date_rental_ymd = date('Y-m-d');
                $join = TravelHelper::edit_join_wpml($join , 'hotel_room') ;
                if( !TravelHelper::checkTableDuplicate('st_rental') ){
                    return '';
                }
                for($i = $date_rental; $i <= strtotime('+15 days', $date_rental); $i = strtotime('+1 day', $i)){
                    $date_rental_ymd = date("Y-m-d", $i);
                    $sql = "SELECT
                        rental.post_id as post_id,
                        rental.discount_rate as discount_rate,
                        rental.is_sale_schedule as is_sale_schedule,
                        rental.sale_price_from as sale_price_from,
                        rental.sale_price_to as sale_price_to
                    FROM
                        {$wpdb->prefix}st_rental AS rental
                    JOIN {$wpdb->prefix}postmeta AS meta ON meta.post_id = rental.post_id
                    AND meta.meta_key = 'rental_number'
                    WHERE
                        cast(
                            rental.discount_rate AS UNSIGNED
                        ) > 0
                    AND (
                        (
                            rental.is_sale_schedule = 'on'
                            AND STR_TO_DATE('{$date_rental_ymd}', '%Y-%m-%d') BETWEEN STR_TO_DATE(
                                rental.sale_price_from,
                                '%Y-%m-%d'
                            )
                            AND STR_TO_DATE(
                                rental.sale_price_to,
                                '%Y-%m-%d'
                            )
                        )
                        OR (
                            rental.is_sale_schedule = 'off'
                        )
                    )
                    AND rental.post_id NOT IN (
                        SELECT
                            post_id
                        FROM
                            {$wpdb->prefix}st_availability
                        WHERE
                            1 = 1
                        AND (
                            check_in >= {$i}
                            AND check_out <= {$i}
                            AND `status` = 'unavailable'
                        )
                        AND post_type = 'st_rental'
                    )
                    AND rental.post_id NOT IN (
                        SELECT
                            st_booking_id
                        FROM
                            (
                                SELECT
                                    od.st_booking_id,
                                    meta.meta_value,
                                    meta.meta_value - count(DISTINCT od.id)
                                FROM
                                    {$wpdb->prefix}st_order_item_meta AS od
                                INNER JOIN {$wpdb->prefix}postmeta AS meta ON meta.post_id = od.st_booking_id
                                AND meta.meta_key = 'rental_number'
                                WHERE
                                    1 = 1
                                AND (
                                    (
                                        check_in_timestamp <= {$i}
                                        AND check_out_timestamp >= {$i}
                                    )
                                    OR (
                                        check_in_timestamp >= {$i}
                                        AND check_in_timestamp <= {$i}
                                    )
                                )
                                AND `status` NOT IN ('trash', 'canceled')
                                AND od.st_booking_post_type = 'st_rental'
                                GROUP BY
                                    od.st_booking_id
                                HAVING
                                    meta.meta_value - count(od.id) <= 0
                            ) AS st_booking_id
                    )
                    ORDER BY
                        rental.discount_rate DESC
                    LIMIT 1";

                    $rs = $wpdb->get_row($sql);
                    if(!empty($rs)){
                        $data['rs'] = $rs;
                        $data['date'] = $i;
                        $html =  st()->load_template('vc-elements/st-last-minute-deal/html',$st_post_type, $data);
                        return $html;
                    }
                }
            break;
            case 'st_cars' : 
                $join = TravelHelper::edit_join_wpml($join , 'st_cars') ;
                $date_now = date('Y-m-d');
                if( !TravelHelper::checkTableDuplicate('st_cars') || !TravelHelper::count_all_sale('st_cars')){
                    return '';
                }

                $sql = "SELECT
                    {$wpdb->prefix}posts.*,
                    mt.discount as discount_rate,
                    mt.cars_price as price,
                    mt.is_sale_schedule as is_sale_schedule,
                    mt.sale_price_from as sale_price_from,
                    mt.sale_price_to as sale_price_to
                FROM
                    {$wpdb->prefix}posts
                INNER JOIN {$wpdb->prefix}st_cars AS mt ON mt.post_id = {$wpdb->prefix}posts.ID
                {$join}
                WHERE
                    1 = 1
                {$where}    
                AND post_type = 'st_cars'
                AND mt.discount != ''
                and (
                    (mt.sale_price_from = '0000-00-00' or mt.sale_price_to = '0000-00-00')
                    OR (
                        mt.sale_price_to >= STR_TO_DATE('{$date_now}', '%Y-%m-%d')
                        AND mt.is_sale_schedule = 'on'
                    )
                )
                GROUP BY
                    {$wpdb->prefix}posts.ID
                ORDER BY
                    mt.discount DESC LIMIT 0,1";
            break; 
            case 'st_tours' : 
                $join = TravelHelper::edit_join_wpml($join , 'st_tours') ;
                $date_now = date('Y-m-d');
                if( !TravelHelper::checkTableDuplicate('st_tours') || !TravelHelper::count_all_sale('st_tours')){
                    return '';
                }
                $sql = "SELECT
                    {$wpdb->prefix}posts.*,
                    mt.discount as discount_rate,
                    mt.adult_price as price,
                    mt.is_sale_schedule as is_sale_schedule,
                    mt.sale_price_from as sale_price_from,
                    mt.sale_price_to as sale_price_to
                FROM
                    {$wpdb->prefix}posts
                INNER JOIN {$wpdb->prefix}st_tours AS mt ON mt.post_id = {$wpdb->prefix}posts.ID
                {$join}
                WHERE
                    1 = 1
                {$where}    
                AND post_type = 'st_tours'
                AND mt.discount != ''
                and (
                    (mt.sale_price_from = '0000-00-00' or mt.sale_price_to = '0000-00-00')
                    OR (
                        mt.sale_price_to >= STR_TO_DATE('{$date_now}', '%Y-%m-%d')
                        AND mt.is_sale_schedule = 'on'
                    )
                )
                GROUP BY
                    {$wpdb->prefix}posts.ID
                ORDER BY
                    mt.discount DESC LIMIT 0,1";
            break;   
            case 'st_activity' : 
                $join = TravelHelper::edit_join_wpml($join , 'st_activity') ;
                $date_now = date('Y-m-d');
                if( !TravelHelper::checkTableDuplicate('st_activity') || !TravelHelper::count_all_sale('st_activity')){
                    return '';
                }
                $sql = "SELECT
                    {$wpdb->prefix}posts.*,
                    mt.discount as discount_rate,
                    mt.adult_price as price,
                    mt.is_sale_schedule as is_sale_schedule,
                    mt.sale_price_from as sale_price_from,
                    mt.sale_price_to as sale_price_to
                FROM
                    {$wpdb->prefix}posts
                INNER JOIN {$wpdb->prefix}st_activity AS mt ON mt.post_id = {$wpdb->prefix}posts.ID
                {$join}
                WHERE
                    1 = 1
                {$where}    
                AND post_type = 'st_activity'
                AND mt.discount != ''
                and (
                    (mt.sale_price_from = '0000-00-00' or mt.sale_price_to = '0000-00-00')
                    OR (
                        mt.sale_price_to >= STR_TO_DATE('{$date_now}', '%Y-%m-%d')
                        AND mt.is_sale_schedule = 'on'
                    )
                )
                GROUP BY
                    {$wpdb->prefix}posts.ID
                ORDER BY
                    mt.discount DESC LIMIT 0,1";
            break;  
        }

        $rs = $wpdb->get_row($sql);

        if(!empty($rs)){
            $data['rs'] = $rs;
            $html =  st()->load_template('vc-elements/st-last-minute-deal/html',$st_post_type, $data);
        }
        return $html;
    }
}
st_reg_shortcode( 'st_last_minute_deal' , 'st_vc_last_minute_deal' );