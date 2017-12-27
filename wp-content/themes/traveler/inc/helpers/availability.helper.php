<?php
    /**
     * @since 1.1.9
     **/
    if ( !class_exists( 'AvailabilityHelper' ) ) {
        class AvailabilityHelper
        {
            public function __construct()
            {
                if ( is_admin() ) {
                    add_action( 'wp_ajax_st_get_availability_hotel', [ &$this, '_get_availability_hotel' ] );
                    add_action( 'wp_ajax_st_get_availability_rental', [ &$this, '_get_availability_rental' ] );
                    add_action( 'wp_ajax_st_get_availability_flight', [ &$this, '_get_availability_flight' ] );

                    add_action( 'wp_ajax_st_get_availability_tour', [ &$this, '_get_availability_tour' ] );
                    add_action( 'wp_ajax_st_get_availability_activity', [ &$this, '_get_availability_activity' ] );

                    add_action( 'wp_ajax_st_get_availability_tour_frontend', [ &$this, '_get_availability_tour_frontend' ] );
                    add_action( 'wp_ajax_nopriv_st_get_availability_tour_frontend', [ &$this, '_get_availability_tour_frontend' ] );

                    add_action( 'wp_ajax_st_get_availability_activity_frontend', [ &$this, '_get_availability_activity_frontend' ] );
                    add_action( 'wp_ajax_nopriv_st_get_availability_activity_frontend', [ &$this, '_get_availability_activity_frontend' ] );

                    add_action( 'wp_ajax_st_add_custom_price', [ &$this, '_add_custom_price' ] );
                    add_action( 'wp_ajax_st_add_custom_price_rental', [ &$this, '_add_custom_price_rental' ] );
                    add_action( 'wp_ajax_st_add_custom_price_flight', [ &$this, '_add_custom_price_flight' ] );
                    add_action( 'wp_ajax_st_add_custom_price_tour', [ &$this, '_add_custom_price_tour' ] );

                    add_action( 'wp_ajax_st_add_custom_price_activity', [ &$this, '_add_custom_price_activity' ] );


                    add_action( 'wp_ajax_traveler_calendar_bulk_edit_form', [ $this, 'traveler_calendar_bulk_edit_form' ] );

                    /**
                     * @update 2.0.0
                     * Add data start time tour
                     */
                    add_action( 'wp_ajax_st_add_custom_starttime_tour', [ &$this, '_add_custom_starttime_tour' ] );
                    add_action( 'wp_ajax_st_get_availability_starttime_tour', [ &$this, '_get_availability_starttime_tour' ] );

                    add_action( 'wp_ajax_st_get_starttime_tour_frontend', [ &$this, '_get_starttime_tour_frontend' ] );
                    add_action( 'wp_ajax_nopriv_st_get_starttime_tour_frontend', [ &$this, '_get_starttime_tour_frontend' ] );

                    add_action( 'wp_ajax_traveler_calendar_bulk_starttime_edit_form', [ $this, 'traveler_calendar_bulk_starttime_edit_form' ] );
                }
            }


            static function _get_availability_date_srart( $post_id )
            {
                $date = date( "Y-m", strtotime( 'today' ) ) . '-01';
                if ( !empty( $post_id ) ) {
                    $post_type   = get_post_type( $post_id );
                    $is_get_date = false;
                    if ( $post_type == 'st_activity' ) {
                        $type_activity = get_post_meta( $post_id, 'type_activity', true );
                        if ( $type_activity == "specific_date" ) {
                            $is_get_date = true;
                        }
                    }
                    if ( $post_type == 'st_tours' ) {
                        $type_activity = get_post_meta( $post_id, 'type_tour', true );
                        if ( $type_activity == "specific_date" ) {
                            $is_get_date = true;
                        }
                    }
                    if ( $is_get_date == true ) {
                        $today = strtotime( 'today' );
                        global $wpdb;
                        $sql     = "SELECT check_in
                    FROM
                    {$wpdb->prefix}st_availability
                    WHERE 
                    post_id = {$post_id} 
                    AND check_in >= {$today}
                    AND `status` = 'available'
                    ORDER BY check_in ASC
                    LIMIT 1 ";
                        $results = $wpdb->get_row( $sql );
                        if ( !empty( $results->check_in ) ) {
                            $date = date( "Y-m", $results->check_in ) . '-01';
                        }
                    }

                }

                return $date;
            }

            /**
             * @update 2.0.0
             */

            static function _get_tour_cant_order_by_starttime( $tour_id, $check_in, $max_people = 0, $starttime )
            {
                if ( !TravelHelper::checkTableDuplicate( 'st_tours' ) ) {
                    return '';
                }
                global $wpdb;

                $sql = "SELECT
				st_booking_id AS tour_id,
				SUM(
					(
						adult_number + child_number + infant_number
					)
				) AS booked
			FROM
				{$wpdb->prefix}st_order_item_meta
			WHERE
				st_booking_id = '{$tour_id}'
			AND st_booking_post_type = 'st_tours'
			AND (
				STR_TO_DATE('{$check_in}', '%m/%d/%Y') = STR_TO_DATE(check_in, '%m/%d/%Y')
			)
			AND `status` NOT IN ('trash', 'canceled')
			AND `starttime` = '{$starttime}'
			HAVING
				{$max_people} - SUM(
					(
						adult_number + child_number + infant_number
					)
				) <= 0";

                $result    = $wpdb->get_col( $sql, 0 );
                $list_date = [];
                if ( is_array( $result ) && count( $result ) ) {
                    $list_date = $result;
                }

                return $list_date;
            }

            static function _getdataTourEachDateStartTime( $tour_id, $check_in, $check_out )
            {
                global $wpdb;

                $sql     = "
			SELECT
				`id`,
				`post_id`,
				`post_type`,
				`check_in`,
				`check_out`,
				`starttime`,
				`adult_price`,
				`child_price`,
				`infant_price`,
				`status`,
				`priority`,
				`groupday`
			FROM
				{$wpdb->prefix}st_availability
			WHERE
				post_id = '{$tour_id}'
			AND (
				(
					UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_in}'), '%Y-%m-%d')) < UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_in), '%Y-%m-%d'))
					AND UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_out}'), '%Y-%m-%d')) > UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_out), '%Y-%m-%d'))
				)
				OR (
					UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_in}'), '%Y-%m-%d')) BETWEEN UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_in), '%Y-%m-%d'))
					AND UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_out), '%Y-%m-%d'))
				)
				OR (
					UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_out}'), '%Y-%m-%d')) BETWEEN UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_in), '%Y-%m-%d'))
					AND UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_out), '%Y-%m-%d'))
				)
			) AND post_type = 'st_starttime_tours'";
                $results = $wpdb->get_results( $sql );

                return $results;
            }

            static function _get_check_out_by_groupday( $tour_id, $check_in )
            {
                global $wpdb;
                $sql = "SELECT
			  	  `check_out_timestamp`
			FROM
				{$wpdb->prefix}st_order_item_meta
			WHERE
				st_booking_id = '{$tour_id}'
			AND
				check_in_timestamp = '{$check_in}'
			AND 
			    st_booking_post_type = 'st_tours'";

                $result = $wpdb->get_results( $sql );

                if ( !empty( $result ) ) {
                    return $result[ 0 ]->check_out_timestamp;
                } else {
                    return '';
                }

            }

            static function _get_in_out_by_group_day( $tour_id )
            {
                global $wpdb;
                $sql = "SELECT
                `post_id`,
				`check_in`,
				`check_out`
			FROM
				{$wpdb->prefix}st_availability
			WHERE
				post_id = '{$tour_id}'
			AND
				groupday = 1
			AND 
			    post_type = 'st_tours'";

                $results = $wpdb->get_results( $sql );

                return $results;
            }

            static function _get_free_peple_by_time_groupday( $tour_id, $check_in, $check_out, $start_time, $order_item_id = '' )
            {
                global $wpdb;
                $sql = "SELECT
				st_booking_id AS tour_id,
				mt.max_people AS max_people,
				mt.max_people - SUM(adult_number + child_number + infant_number) AS free_people
			FROM
				{$wpdb->prefix}st_order_item_meta
			INNER JOIN {$wpdb->prefix}st_tours AS mt ON mt.post_id = st_booking_id
			WHERE
				st_booking_id = '{$tour_id}'
			AND 
			    check_in_timestamp = '{$check_in}'
			AND 
			    check_out_timestamp = '{$check_out}'
			AND starttime = '{$start_time}'
			AND status NOT IN ('trash', 'canceled')
			
			GROUP BY
				st_booking_id";

                $result = $wpdb->get_row( $sql, ARRAY_A );

                return $result;
            }

            static function _get_free_peple_by_time( $tour_id, $check_in, $check_out, $start_time, $order_item_id = '' )
            {
                global $wpdb;
                $sql = "SELECT
				st_booking_id AS tour_id,
				mt.max_people AS max_people,
				mt.max_people - SUM(adult_number + child_number + infant_number) AS free_people
			FROM
				{$wpdb->prefix}st_order_item_meta
			INNER JOIN {$wpdb->prefix}st_tours AS mt ON mt.post_id = st_booking_id
			WHERE
				st_booking_id = '{$tour_id}'
			AND 
			    check_out_timestamp = '{$check_in}'
			AND starttime = '{$start_time}'
			AND status NOT IN ('trash', 'canceled')
			
			GROUP BY
				st_booking_id";

                $result = $wpdb->get_row( $sql, ARRAY_A );

                return $result;
            }

            static function _get_starttime_by_date( $tour_id, $check_in, $check_out )
            {
                global $wpdb;
                $sql = "SELECT
				starttime
			FROM
				{$wpdb->prefix}st_availability
			WHERE
				post_id = '{$tour_id}'
			AND
				check_in = '{$check_in}'
			AND 
			    check_out = '{$check_out}'
			AND 
			    post_type = 'st_starttime_tours'";

                $result = $wpdb->get_results( $sql );

                if ( !empty( $result ) ) {
                    return $result[ 0 ]->starttime;
                } else {
                    return '';
                }

            }

            public static function _get_starttime_tour_by_date( $post_id, $date )
            {
                global $wpdb;

                $sql     = "
			SELECT
				`starttime`
			FROM
				{$wpdb->prefix}st_availability
			WHERE
				post_id = '{$post_id}'
			AND
			    check_in = '{$date}'
			AND 
			    post_type = 'st_starttime_tours'";
                $results = $wpdb->get_results( $sql );

                return $results;
            }

            public function traveler_insert_availability_starttime( $post_id = '', $check_in = '', $check_out = '', $starttime = '', $status = '', $group_day = '' )
            {
                global $wpdb;

                $post_type_starttime = '';
                if ( get_post_type( $post_id ) == 'st_tours' ) {
                    $post_type_starttime = 'st_starttime_tours';
                }
                if ( get_post_type( $post_id ) == 'st_activity' ) {
                    $post_type_starttime = 'st_starttime_activity';
                }

                $table = $wpdb->prefix . 'st_availability';
                if ( $group_day == 1 ) {
                    $wpdb->insert(
                        $table,
                        [
                            'post_id'   => $post_id,
                            'check_in'  => $check_in,
                            'check_out' => $check_out,
                            'starttime' => $starttime,
                            'status'    => $status,
                            'groupday'  => 1,
                            'post_type' => $post_type_starttime
                        ]
                    );
                } else {
                    for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                        $wpdb->insert(
                            $table,
                            [
                                'post_id'   => $post_id,
                                'check_in'  => $i,
                                'check_out' => $i,
                                'starttime' => $starttime,
                                'status'    => $status,
                                'groupday'  => 0,
                                'post_type' => $post_type_starttime
                            ]
                        );
                    }
                }

                return (int)$wpdb->insert_id;
            }

            public function traveler_delete_availability_starttime( $id = '' )
            {
                global $wpdb;
                $table = $wpdb->prefix . 'st_availability';

                $wpdb->delete(
                    $table,
                    [
                        'id' => $id
                    ]
                );
            }

            public function traveler_split_availability_starttime( $result = [], $check_in = '', $check_out = '' )
            {
                $return = [];

                if ( !empty( $result ) ) {
                    foreach ( $result as $item ) {
                        $check_in  = (int)$check_in;
                        $check_out = (int)$check_out;

                        $start = strtotime( $item[ 'start' ] );
                        $end   = strtotime( '-1 day', strtotime( $item[ 'end' ] ) );

                        if ( $start < $check_in && $end >= $check_in ) {
                            $return[ 'insert' ][] = [
                                'post_id'   => $item[ 'post_id' ],
                                'check_in'  => strtotime( $item[ 'check_in' ] ),
                                'check_out' => strtotime( '-1 day', $check_in ),
                                'starttime' => $item[ 'starttime' ],
                                'status'    => $item[ 'status' ],
                                'groupday'  => $item[ 'groupday' ],
                            ];
                        }

                        if ( $start <= $check_out && $end > $check_out ) {
                            $return[ 'insert' ][] = [
                                'post_id'   => $item[ 'post_id' ],
                                'check_in'  => strtotime( '+1 day', $check_out ),
                                'check_out' => strtotime( '-1 day', strtotime( $item[ 'check_out' ] ) ),
                                'starttime' => $item[ 'starttime' ],
                                'status'    => $item[ 'status' ],
                                'groupday'  => $item[ 'groupday' ],
                            ];
                        }

                        $return[ 'delete' ][] = [
                            'id' => $item[ 'id' ]
                        ];
                    }
                }

                return $return;
            }

            public function traveler_get_availability_starttime( $post_id = '', $check_in = '', $check_out = '' )
            {
                global $wpdb;

                $table = $wpdb->prefix . 'st_availability';

                $post_type_starttime = 'st_starttime_tours';

                $sql = "SELECT * FROM {$table} WHERE post_id = {$post_id} AND post_type = '{$post_type_starttime}' AND ( ( CAST( check_in AS UNSIGNED ) >= CAST( {$check_in} AS UNSIGNED) AND CAST( check_in AS UNSIGNED ) <= CAST( {$check_out} AS UNSIGNED ) ) OR ( CAST( check_out AS UNSIGNED ) >= CAST( {$check_in} AS UNSIGNED ) AND ( CAST( check_out AS UNSIGNED ) <= CAST( {$check_out} AS UNSIGNED ) ) ) )";

                $result = $wpdb->get_results( $sql, ARRAY_A );

                $return = [];

                if ( !empty( $result ) ) {
                    foreach ( $result as $item ) {
                        $return[] = [
                            'id'        => $item[ 'id' ],
                            'post_id'   => $item[ 'post_id' ],
                            'check_in'  => date( 'Y-m-d', $item[ 'check_in' ] ),
                            'check_out' => date( 'Y-m-d', strtotime( '+1 day', $item[ 'check_out' ] ) ),
                            'status'    => $item[ 'status' ],
                            'groupday'  => $item[ 'groupday' ],
                        ];
                    }
                }

                return $return;
            }

            public function insert_calendar_bulk_starttime( $data, $posts_per_page, $total, $current_page, $all_days, $post_id )
            {
                $start = ( $current_page - 1 ) * $posts_per_page;

                $end = ( $current_page - 1 ) * $posts_per_page + $posts_per_page - 1;

                if ( $end > $total - 1 ) {
                    $end = $total - 1;
                }

                if ( $data[ 'groupday' ] == 0 ) {

                    for ( $i = $start; $i <= $end; $i++ ) {


                        $data[ 'start' ] = $all_days[ $i ];
                        $data[ 'end' ]   = $all_days[ $i ];

                        /*  Delete old item */
                        $result = $this->traveler_get_availability_starttime( $post_id, $all_days[ $i ], $all_days[ $i ] );

                        $split = $this->traveler_split_availability_starttime( $result, $all_days[ $i ], $all_days[ $i ] );

                        if ( isset( $split[ 'delete' ] ) && !empty( $split[ 'delete' ] ) ) {
                            foreach ( $split[ 'delete' ] as $item ) {
                                $this->traveler_delete_availability_starttime( $item[ 'id' ] );
                            }
                        }
                        /*  .End */

                        $this->traveler_insert_availability_starttime( $data[ 'post_id' ], $data[ 'start' ], $data[ 'end' ], $data[ 'starttime' ], $data[ 'status' ], $data[ 'groupday' ] );
                    }
                } else {

                    for ( $i = $start; $i <= $end; $i++ ) {
                        $data[ 'start' ] = $all_days[ $i ][ 'min' ];
                        $data[ 'end' ]   = $all_days[ $i ][ 'max' ];
                        /*  Delete old item */
                        $result = $this->traveler_get_availability_starttime( $post_id, $all_days[ $i ][ 'min' ], $all_days[ $i ][ 'max' ] );
                        $split  = $this->traveler_split_availability_starttime( $result, $all_days[ $i ][ 'min' ], $all_days[ $i ][ 'max' ] );
                        if ( isset( $split[ 'delete' ] ) && !empty( $split[ 'delete' ] ) ) {
                            foreach ( $split[ 'delete' ] as $item ) {
                                $this->traveler_delete_availability_starttime( $item[ 'id' ] );
                            }
                        }
                        /*  .End */

                        $this->traveler_insert_availability_starttime( $data[ 'post_id' ], $data[ 'start' ], $data[ 'end' ], $data[ 'starttime' ], $data[ 'status' ], $data[ 'groupday' ] );
                    }
                }

                $next_page = (int)$current_page + 1;

                $progress = ( $current_page / $total ) * 100;

                $return = [
                    'all_days'       => $all_days,
                    'current_page'   => $next_page,
                    'posts_per_page' => $posts_per_page,
                    'total'          => $total,
                    'status'         => 2,
                    'data'           => $data,
                    'progress'       => $progress,
                    'post_id'        => $post_id,
                ];

                return $return;
            }

            public function traveler_calendar_bulk_starttime_edit_form()
            {
                $post_id = (int)STInput::post( 'post_id', 0 );
                if ( $post_id > 0 ) {
                    if ( isset( $_POST[ 'all_days' ] ) && !empty( $_POST[ 'all_days' ] ) ) {

                        $data           = STInput::post( 'data', '' );
                        $all_days       = STInput::post( 'all_days', '' );
                        $posts_per_page = (int)STInput::post( 'posts_per_page', '' );
                        $current_page   = (int)STInput::post( 'current_page', '' );
                        $total          = (int)STInput::post( 'total', '' );

                        if ( $current_page > ceil( $total / $posts_per_page ) ) {

                            echo json_encode( [
                                'status'  => 1,
                                'message' => '<div class="text-success">' . __( 'Added successful.', ST_TEXTDOMAIN ) . '</div>'
                            ] );
                            die;
                        } else {
                            $return = $this->insert_calendar_bulk_starttime( $data, $posts_per_page, $total, $current_page, $all_days, $post_id );
                            echo json_encode( $return );
                            die;
                        }
                    }

                    $day_of_week  = STInput::post( 'day-of-week', '' );
                    $day_of_month = STInput::post( 'day-of-month', '' );

                    $array_month = [
                        'January'   => '1',
                        'February'  => '2',
                        'March'     => '3',
                        'April'     => '4',
                        'May'       => '5',
                        'June'      => '6',
                        'July'      => '7',
                        'August'    => '8',
                        'September' => '9',
                        'October'   => '10',
                        'November'  => '11',
                        'December'  => '12',
                    ];

                    $months = STInput::post( 'months', '' );

                    $years = STInput::post( 'years', '' );

                    $starttime = STInput::post( 'starttime', '' );

                    $starttime_string = '';
                    if ( !empty( $starttime ) ) {
                        $starttime_string = implode( ', ', $starttime );
                    }

                    $status = STInput::post( 'status', 'available' );

                    $group_day = STInput::post( 'calendar_groupday', 0 );

                    /*  Start, End is a timestamp */
                    $all_years  = [];
                    $all_months = [];
                    $all_days   = [];

                    if ( !empty( $years ) ) {

                        sort( $years, 1 );

                        foreach ( $years as $year ) {
                            $all_years[] = $year;
                        }

                        if ( !empty( $months ) ) {

                            foreach ( $months as $month ) {
                                foreach ( $all_years as $year ) {
                                    $all_months[] = $month . ' ' . $year;
                                }
                            }

                            if ( !empty( $day_of_week ) && !empty( $day_of_month ) ) {
                                // Each day in month
                                foreach ( $day_of_month as $day ) {
                                    // Each day in week
                                    foreach ( $day_of_week as $day_week ) {
                                        // Each month year
                                        foreach ( $all_months as $month ) {
                                            $time = strtotime( $day . ' ' . $month );

                                            if ( date( 'l', $time ) == $day_week ) {
                                                $all_days[] = $time;
                                            }
                                        }
                                    }
                                }
                            } elseif ( empty( $day_of_week ) && empty( $day_of_month ) ) {
                                foreach ( $all_months as $month ) {
                                    for ( $i = strtotime( 'first day of ' . $month ); $i <= strtotime( 'last day of ' . $month ); $i = strtotime( '+1 day', $i ) ) {
                                        $all_days[] = $i;
                                    }
                                }
                            } elseif ( empty( $day_of_week ) && !empty( $day_of_month ) ) {

                                foreach ( $day_of_month as $day ) {
                                    foreach ( $all_months as $month ) {
                                        $month_tmp = trim( $month );
                                        $month_tmp = explode( ' ', $month );

                                        $num_day = cal_days_in_month( CAL_GREGORIAN, $array_month[ $month_tmp[ 0 ] ], $month_tmp[ 1 ] );

                                        if ( $day <= $num_day ) {
                                            $all_days[] = strtotime( $day . ' ' . $month );
                                        }
                                    }
                                }
                            } elseif ( !empty( $day_of_week ) && empty( $day_of_month ) ) {
                                foreach ( $day_of_week as $day ) {
                                    foreach ( $all_months as $month ) {
                                        for ( $i = strtotime( 'first ' . $day . ' of ' . $month ); $i <= strtotime( 'last ' . $day . ' of ' . $month ); $i = strtotime( '+1 week', $i ) ) {
                                            $all_days[] = $i;
                                        }
                                    }
                                }
                            }

                            if ( !empty( $all_days ) ) {
                                $posts_per_page = 10;

                                if ( $group_day == 1 ) {
                                    $all_days = $this->change_allday_to_group( $all_days );
                                }

                                $total = count( $all_days );

                                $current_page = 1;

                                $data = [
                                    'post_id'   => $post_id,
                                    'status'    => $status,
                                    'groupday'  => $group_day,
                                    'starttime' => $starttime_string
                                ];

                                $return = $this->insert_calendar_bulk_starttime( $data, $posts_per_page, $total, $current_page, $all_days, $post_id );

                                echo json_encode( $return );
                                die;
                            }
                        } else {
                            echo json_encode( [
                                'status'  => 0,
                                'message' => '<div class="text-error">' . __( 'The months field is required.', ST_TEXTDOMAIN ) . '</div>'
                            ] );
                            die;
                        }

                    } else {
                        echo json_encode( [
                            'status'  => 0,
                            'message' => '<div class="text-error">' . __( 'The years field is required.', ST_TEXTDOMAIN ) . '</div>'
                        ] );
                        die;
                    }
                }
            }

            public function _add_custom_starttime_tour()
            {
                global $wpdb;
                $check_in  = STInput::request( 'calendar_starttime_check_in', '' );
                $check_out = STInput::request( 'calendar_starttime_check_out', '' );
                if ( empty( $check_in ) || empty( $check_out ) ) {
                    echo json_encode( [
                        'type'    => 'error',
                        'status'  => 0,
                        'message' => __( 'The start time check in or check out field is not empty.', ST_TEXTDOMAIN )
                    ] );
                    die();
                }

                $check_in  = strtotime( $check_in );
                $check_out = strtotime( $check_out );
                if ( $check_in > $check_out ) {
                    echo json_encode( [
                        'type'    => 'error',
                        'status'  => 0,
                        'message' => __( 'The start time check out is later than the check in field.', ST_TEXTDOMAIN )
                    ] );
                    die();
                }

                $status             = STInput::request( 'calendar_status', 'available' );
                $starttime_hour_arr = STInput::request( 'calendar_starttime_hour', '' );
                $starttime_min_arr  = STInput::request( 'calendar_starttime_minute', '' );
                $post_id            = STInput::request( 'calendar_starttime_post_id', '' );

                if ( $starttime_hour_arr == '' || $starttime_min_arr == '' ) {
                    $list_tour_del = self::_getdataTourEachDateStartTime( $post_id, $check_in, $check_out );
                    $a             = 'a';
                    if ( is_array( $list_tour_del ) && count( $list_tour_del ) ) {
                        foreach ( $list_tour_del as $keyd => $vald ) {
                            self::_deleteData( $vald->id );
                        }
                    }
                    echo json_encode( [
                        'type'    => 'success',
                        'status'  => 1,
                        'message' => __( 'Remove start time successful', ST_TEXTDOMAIN )
                    ] );
                    die();
                }

                $starttime_unique_arr = [];

                for ( $k = 0; $k < count( $starttime_hour_arr ); $k++ ) {
                    array_push( $starttime_unique_arr, $starttime_hour_arr[ $k ] . ':' . $starttime_min_arr[ $k ] );
                }

                $starttime_unique_arr = array_unique( $starttime_unique_arr );
                sort( $starttime_unique_arr );

                $starttime_unique_string = implode( ', ', array_filter( $starttime_unique_arr ) );

                $starttime_posttype = 'st_starttime_tours';

                for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                    $list_tour = self::_getdataTourEachDateStartTime( $post_id, $i, $i );
                    if ( is_array( $list_tour ) && count( $list_tour ) ) {
                        foreach ( $list_tour as $key => $val ) {
                            if ( $i == $val->check_in ) {
                                if ( intval( $val->check_out ) - intval( $val->check_in ) == 0 ) {
                                    self::_deleteData( $val->id );
                                } else {
                                    $group_day = 1;
                                    if ( strtotime( '+1 day', $val->check_in ) == $val->check_out ) {
                                        $group_day = 0;
                                    }
                                    $data  = [
                                        'check_in' => strtotime( '+1 day', $val->check_in ),
                                        'groupday' => $group_day
                                    ];
                                    $where = [
                                        'id' => $val->id
                                    ];

                                    self::_updateData( $where, $data );
                                }

                            } elseif ( $i == $val->check_out && $val->check_out != $val->check_in ) {
                                $group_day = 1;
                                if ( strtotime( '-1 day', $val->check_out ) == $val->check_in ) {
                                    $group_day = 0;
                                }
                                $data = [
                                    'check_out' => strtotime( '-1 day', $val->check_out ),
                                    'groupday'  => $group_day
                                ];

                                $where = [
                                    'id' => $val->id
                                ];

                                self::_updateData( $where, $data );
                            } elseif ( $i > $val->check_in && $i < $val->check_out ) {
                                $group_day = 1;
                                if ( $val->check_in == strtotime( '-1 day', $i ) ) {
                                    $group_day = 0;
                                }
                                $data  = [
                                    'check_out' => strtotime( '-1 day', $i ),
                                    'groupday'  => $group_day
                                ];
                                $where = [
                                    'id' => $val->id
                                ];
                                self::_updateData( $where, $data );

                                $group_day = 1;
                                if ( strtotime( '+1 day', $i ) == $val->check_out ) {
                                    $group_day = 0;
                                }

                                $data = [
                                    'post_id'   => $val->post_id,
                                    'post_type' => $starttime_posttype,
                                    'check_in'  => strtotime( '+1 day', $i ),
                                    'check_out' => $val->check_out,
                                    //'adult_price' => ( (float)$val->adult_price < 0 ) ? 0 : (float)$val->adult_price,
                                    //'child_price' => ( (float)$val->child_price < 0 ) ? 0 : (float)$val->child_price,
                                    //'infant_price' => ( (float)$val->infant_price < 0 ) ? 0 : (float)$val->infant_price,
                                    'starttime' => $starttime_unique_string,
                                    'groupday'  => $group_day,
                                    'status'    => $val->status,
                                ];
                                self::_insertData( $data );
                            }
                        }
                    }
                }
                $groupday = '';
                if ( intval( $groupday ) == 1 ) {
                    $data  = [
                        'post_id'   => $post_id,
                        'post_type' => $starttime_posttype,
                        'check_in'  => $check_in,
                        'check_out' => $check_out,
                        'starttime' => $starttime_unique_string,
                        'status'    => $status,
                        'groupday'  => $groupday,
                    ];
                    $table = $wpdb->prefix . 'st_availability';
                    $wpdb->insert( $table, $data );
                    if ( $wpdb->insert_id <= 0 ) {
                        echo json_encode( [
                            'type'    => 'error',
                            'status'  => 0,
                            'message' => $wpdb->show_errors()
                        ] );
                        die();
                    }
                } else {
                    for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                        $data  = [
                            'post_id'   => $post_id,
                            'post_type' => $starttime_posttype,
                            'check_in'  => $i,
                            'check_out' => $i,
                            'starttime' => $starttime_unique_string,
                            'status'    => $status,
                            'groupday'  => $groupday,
                        ];
                        $table = $wpdb->prefix . 'st_availability';
                        $wpdb->insert( $table, $data );
                        if ( $wpdb->insert_id <= 0 ) {
                            echo json_encode( [
                                'type'    => 'error',
                                'status'  => 0,
                                'message' => $wpdb->show_errors()
                            ] );
                            die();
                        }
                    }
                }
                echo json_encode( [
                    'type'      => 'success',
                    'status'    => 1,
                    'message'   => __( 'Successfully', ST_TEXTDOMAIN ),
                    'starttime' => $starttime_unique_string,
                ] );
                die();
            }

            public function _get_starttime_tour_frontend()
            {
                $results   = [];
                $tour_id   = STInput::request( 'tour_id', '' );
                $tour_id   = TravelHelper::post_origin( $tour_id, 'st_tours' );
                $check_in  = trim( STInput::request( 'check_in', '' ) );
                $check_out = STInput::request( 'check_out', '' );
                //$check_in = str_replace('/', '-', $check_in);
                //$check_out = str_replace('/', '-', $check_out);
                $re_format_check_in = strtotime( date( "Y-m-d", strtotime( $check_in ) ) );

                $re_format_check_out = strtotime( date( "Y-m-d", strtotime( $check_out ) ) );

                if ( $check_out != '' ) {
                    /*
                 * xkei
                 * old code
                 * $tour_starttime_data = self::_get_starttime_tour_by_date($tour_id, $re_format_check_out);
                 * First day  - Checl avaiable true with daily tour and special tour
                 */
                    $tour_starttime_data = self::_get_starttime_tour_by_date( $tour_id, $re_format_check_in );
                    //$tour_starttime_data = self::_get_starttime_tour_by_date($tour_id, $re_format_check_in);
                } else {
                    $tour_starttime_data = self::_get_starttime_tour_by_date( $tour_id, $re_format_check_in );
                }


                $result_data = [];

                if ( !empty( $tour_starttime_data ) ) {
                    foreach ( $tour_starttime_data as $item ) {
                        $result_data = explode( ', ', $item->starttime );
                    }
                }

                $disable_option = [];

                $max_people = intval( get_post_meta( $tour_id, 'max_people', true ) );


                foreach ( $result_data as $item ) {
                    if ( $max_people == '' || $max_people == '0' || !is_numeric( $max_people ) ) {
                        array_push( $disable_option, '-1' );
                    } else {
                        if ( $check_out != '' ) {
                            $result = TourHelper::_get_free_peple_by_time( $tour_id, $re_format_check_in, $re_format_check_out, $item );
                        } else {
                            $result = TourHelper::_get_free_peple_by_time( $tour_id, $re_format_check_in, $re_format_check_in, $item );
                        }
                        if ( is_array( $result ) && count( $result ) ) {
                            $free_people = intval( $result[ 'free_people' ] );
                            if ( $free_people == '0' ) {
                                array_push( $disable_option, '0' );
                            } else {
                                array_push( $disable_option, $free_people );
                            }
                        } else {
                            array_push( $disable_option, $max_people );
                        }
                    }
                }

                $data = [
                    'check' => $disable_option,
                    'data'  => $result_data
                ];

                echo json_encode( $data );
                die;
            }

            public function _get_availability_starttime_tour()
            {
                $results   = [];
                $tour_id   = STInput::request( 'tour_id', '' );
                $check_in  = STInput::request( 'start', '' );
                $check_out = STInput::request( 'end', '' );

                if ( get_post_type( $tour_id ) == 'st_tours' ) {
                    $starttime = get_post_meta( $tour_id, 'starttime', true );
                    $type_tour = get_post_meta( $tour_id, 'type_tour', true );

                    $data_tour = self::_getdataTourEachDateStartTime( $tour_id, $check_in, $check_out );

                    if ( is_array( $data_tour ) && count( $data_tour ) ) {
                        foreach ( $data_tour as $key => $val ) {
                            if ( $val->status == 'available' ) {
                                if ( intval( $val->groupday ) == 1 ) {
                                    $results[] = [
                                        'title'     => get_the_title( $tour_id ),
                                        'start'     => date( 'Y-m-d', $val->check_in ),
                                        'end'       => date( 'Y-m-d', strtotime( '+1 day', $val->check_out ) ),
                                        'starttime' => $val->starttime,
                                        'status'    => 'available'
                                    ];
                                } else {
                                    for ( $i = $val->check_in; $i <= $val->check_out; $i = strtotime( '+1 day', $i ) ) {
                                        $results[] = [
                                            'title'     => get_the_title( $tour_id ),
                                            'start'     => date( 'Y-m-d', $i ),
                                            'starttime' => $val->starttime,
                                            'status'    => 'available',
                                        ];
                                    }
                                }
                            }
                        }

                        if ( $type_tour == 'daily_tour' ) {
                            for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                                $in_item = false;
                                foreach ( $data_tour as $key => $val ) {
                                    if ( $i >= $val->check_in && $i <= $val->check_out ) {
                                        $in_item = true;
                                        break;
                                    }
                                }

                                if ( !$in_item ) {
                                    $results[] = [
                                        'title'     => get_the_title( $tour_id ),
                                        'start'     => date( 'Y-m-d', $i ),
                                        'starttime' => $starttime,
                                        'status'    => 'available',
                                    ];
                                }
                            }
                        }

                    } else {
                        if ( $type_tour == 'daily_tour' || !$type_tour ) {
                            for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                                $results[] = [
                                    'title'     => get_the_title( $tour_id ),
                                    'start'     => date( 'Y-m-d', $i ),
                                    'starttime' => $starttime,
                                    'status'    => 'available'
                                ];
                            }
                        }
                    }
                }
                echo json_encode( $results );
                die();
            }

            //End update Start Time

            public function traveler_calendar_bulk_edit_form()
            {
                $post_id = (int)STInput::post( 'post_id', 0 );
                if ( $post_id > 0 ) {

                    if ( isset( $_POST[ 'all_days' ] ) && !empty( $_POST[ 'all_days' ] ) ) {

                        $data           = STInput::post( 'data', '' );
                        $all_days       = STInput::post( 'all_days', '' );
                        $posts_per_page = (int)STInput::post( 'posts_per_page', '' );
                        $current_page   = (int)STInput::post( 'current_page', '' );
                        $total          = (int)STInput::post( 'total', '' );

                        if ( $current_page > ceil( $total / $posts_per_page ) ) {

                            echo json_encode( [
                                'status'  => 1,
                                'message' => '<div class="text-success">' . __( 'Added successful.', ST_TEXTDOMAIN ) . '</div>'
                            ] );
                            die;
                        } else {
                            $return = $this->insert_calendar_bulk( $data, $posts_per_page, $total, $current_page, $all_days, $post_id );
                            echo json_encode( $return );
                            die;
                        }
                    }

                    $day_of_week  = STInput::post( 'day-of-week', '' );
                    $day_of_month = STInput::post( 'day-of-month', '' );

                    $array_month = [
                        'January'   => '1',
                        'February'  => '2',
                        'March'     => '3',
                        'April'     => '4',
                        'May'       => '5',
                        'June'      => '6',
                        'July'      => '7',
                        'August'    => '8',
                        'September' => '9',
                        'October'   => '10',
                        'November'  => '11',
                        'December'  => '12',
                    ];

                    $months = STInput::post( 'months', '' );

                    $years = STInput::post( 'years', '' );

                    $price          = STInput::post( 'price_bulk', 0 );
                    $adult_price    = STInput::post( 'adult-price_bulk', 0 );
                    $children_price = STInput::post( 'children-price_bulk', 0 );
                    $infant_price   = STInput::post( 'infant-price_bulk', 0 );

                    if ( !is_numeric( $price ) || !is_numeric( $adult_price ) || !is_numeric( $children_price ) || !is_numeric( $infant_price ) ) {
                        echo json_encode( [
                            'status'  => 0,
                            'message' => '<div class="text-error">' . __( 'The price field is not a number.', ST_TEXTDOMAIN ) . '</div>'
                        ] );
                        die;
                    }
                    $price          = (float)$price;
                    $adult_price    = (float)$adult_price;
                    $children_price = (float)$children_price;
                    $infant_price   = (float)$infant_price;

                    $status = STInput::post( 'status', 'available' );

                    $group_day = STInput::post( 'calendar_groupday', 0 );

                    /*  Start, End is a timestamp */
                    $all_years  = [];
                    $all_months = [];
                    $all_days   = [];

                    if ( !empty( $years ) ) {

                        sort( $years, 1 );

                        foreach ( $years as $year ) {
                            $all_years[] = $year;
                        }

                        if ( !empty( $months ) ) {

                            foreach ( $months as $month ) {
                                foreach ( $all_years as $year ) {
                                    $all_months[] = $month . ' ' . $year;
                                }
                            }

                            if ( !empty( $day_of_week ) && !empty( $day_of_month ) ) {
                                // Each day in month
                                foreach ( $day_of_month as $day ) {
                                    // Each day in week
                                    foreach ( $day_of_week as $day_week ) {
                                        // Each month year
                                        foreach ( $all_months as $month ) {
                                            $time = strtotime( $day . ' ' . $month );

                                            if ( date( 'l', $time ) == $day_week ) {
                                                $all_days[] = $time;
                                            }
                                        }
                                    }
                                }
                            } elseif ( empty( $day_of_week ) && empty( $day_of_month ) ) {
                                foreach ( $all_months as $month ) {
                                    for ( $i = strtotime( 'first day of ' . $month ); $i <= strtotime( 'last day of ' . $month ); $i = strtotime( '+1 day', $i ) ) {
                                        $all_days[] = $i;
                                    }
                                }
                            } elseif ( empty( $day_of_week ) && !empty( $day_of_month ) ) {

                                foreach ( $day_of_month as $day ) {
                                    foreach ( $all_months as $month ) {
                                        $month_tmp = trim( $month );
                                        $month_tmp = explode( ' ', $month );

                                        $num_day = cal_days_in_month( CAL_GREGORIAN, $array_month[ $month_tmp[ 0 ] ], $month_tmp[ 1 ] );

                                        if ( $day <= $num_day ) {
                                            $all_days[] = strtotime( $day . ' ' . $month );
                                        }
                                    }
                                }
                            } elseif ( !empty( $day_of_week ) && empty( $day_of_month ) ) {
                                foreach ( $day_of_week as $day ) {
                                    foreach ( $all_months as $month ) {
                                        for ( $i = strtotime( 'first ' . $day . ' of ' . $month ); $i <= strtotime( 'last ' . $day . ' of ' . $month ); $i = strtotime( '+1 week', $i ) ) {
                                            $all_days[] = $i;
                                        }
                                    }
                                }
                            }


                            if ( !empty( $all_days ) ) {
                                $posts_per_page = 10;

                                if ( $group_day == 1 ) {
                                    $all_days = $this->change_allday_to_group( $all_days );
                                }

                                $total = count( $all_days );

                                $current_page = 1;

                                $data = [
                                    'post_id'        => $post_id,
                                    'status'         => $status,
                                    'groupday'       => $group_day,
                                    'price'          => $price,
                                    'adult_price'    => $adult_price,
                                    'children_price' => $children_price,
                                    'infant_price'   => $infant_price,
                                ];

                                $return = $this->insert_calendar_bulk( $data, $posts_per_page, $total, $current_page, $all_days, $post_id );

                                echo json_encode( $return );
                                die;
                            }
                        } else {
                            echo json_encode( [
                                'status'  => 0,
                                'message' => '<div class="text-error">' . __( 'The months field is required.', ST_TEXTDOMAIN ) . '</div>'
                            ] );
                            die;
                        }

                    } else {
                        echo json_encode( [
                            'status'  => 0,
                            'message' => '<div class="text-error">' . __( 'The years field is required.', ST_TEXTDOMAIN ) . '</div>'
                        ] );
                        die;
                    }
                } else {
                    echo json_encode( [
                        'status'  => 0,
                        'message' => '<div class="text-error">' . __( 'The room field is required.', ST_TEXTDOMAIN ) . '</div>'
                    ] );
                    die;
                }
            }

            public function change_allday_to_group( $all_days = [] )
            {
                $return_tmp = [];
                $return     = [];

                foreach ( $all_days as $item ) {
                    $month = date( 'm', $item );
                    if ( !isset( $return_tmp[ $month ] ) ) {
                        $return_tmp[ $month ][ 'min' ] = $item;
                        $return_tmp[ $month ][ 'max' ] = $item;
                    } else {
                        if ( $return_tmp[ $month ][ 'min' ] > $item ) {
                            $return_tmp[ $month ][ 'min' ] = $item;
                        }
                        if ( $return_tmp[ $month ][ 'max' ] < $item ) {
                            $return_tmp[ $month ][ 'max' ] = $item;
                        }
                    }
                }

                foreach ( $return_tmp as $key => $val ) {
                    $return[] = [
                        'min' => $val[ 'min' ],
                        'max' => $val[ 'max' ],
                    ];
                }

                return $return;
            }

            public function insert_calendar_bulk( $data, $posts_per_page, $total, $current_page, $all_days, $post_id )
            {

                $start = ( $current_page - 1 ) * $posts_per_page;

                $end = ( $current_page - 1 ) * $posts_per_page + $posts_per_page - 1;

                if ( $end > $total - 1 ) $end = $total - 1;

                if ( $data[ 'groupday' ] == 0 ) {
                    for ( $i = $start; $i <= $end; $i++ ) {

                        $data[ 'start' ] = $all_days[ $i ];
                        $data[ 'end' ]   = $all_days[ $i ];

                        /*  Delete old item */
                        $result = $this->traveler_get_availability( $post_id, $all_days[ $i ], $all_days[ $i ] );

                        $split = $this->traveler_split_availability( $result, $all_days[ $i ], $all_days[ $i ] );

                        if ( isset( $split[ 'delete' ] ) && !empty( $split[ 'delete' ] ) ) {
                            foreach ( $split[ 'delete' ] as $item ) {
                                $this->traveler_delete_availability( $item[ 'id' ] );
                            }
                        }
                        /*  .End */

                        $this->traveler_insert_availability( $data[ 'post_id' ], $data[ 'start' ], $data[ 'end' ], $data[ 'price' ], $data[ 'adult_price' ], $data[ 'children_price' ], $data[ 'infant_price' ], $data[ 'status' ], $data[ 'groupday' ] );
                    }
                } else {
                    for ( $i = $start; $i <= $end; $i++ ) {
                        $data[ 'start' ] = $all_days[ $i ][ 'min' ];
                        $data[ 'end' ]   = $all_days[ $i ][ 'max' ];
                        /*  Delete old item */
                        $result = $this->traveler_get_availability( $post_id, $all_days[ $i ][ 'min' ], $all_days[ $i ][ 'max' ] );
                        $split  = $this->traveler_split_availability( $result, $all_days[ $i ][ 'min' ], $all_days[ $i ][ 'max' ] );
                        if ( isset( $split[ 'delete' ] ) && !empty( $split[ 'delete' ] ) ) {
                            foreach ( $split[ 'delete' ] as $item ) {
                                $this->traveler_delete_availability( $item[ 'id' ] );
                            }
                        }
                        /*  .End */

                        $this->traveler_insert_availability( $data[ 'post_id' ], $data[ 'start' ], $data[ 'end' ], $data[ 'price' ], $data[ 'adult_price' ], $data[ 'children_price' ], $data[ 'infant_price' ], $data[ 'status' ], $data[ 'groupday' ] );
                    }
                }


                $next_page = (int)$current_page + 1;

                $progress = ( $current_page / $total ) * 100;

                $return = [
                    'all_days'       => $all_days,
                    'current_page'   => $next_page,
                    'posts_per_page' => $posts_per_page,
                    'total'          => $total,
                    'status'         => 2,
                    'data'           => $data,
                    'progress'       => $progress,
                    'post_id'        => $post_id,
                ];

                return $return;
            }

            public function traveler_delete_availability( $id = '' )
            {

                global $wpdb;

                $table = $wpdb->prefix . 'st_availability';

                $wpdb->delete(
                    $table,
                    [
                        'id' => $id
                    ]
                );

            }

            public function traveler_insert_availability( $post_id = '', $check_in = '', $check_out = '', $price = '', $adult_price = '', $children_price = '', $infant_price = '', $status = '', $group_day = '' )
            {
                global $wpdb;

                $table = $wpdb->prefix . 'st_availability';
                if ( $group_day == 1 ) {
                    $wpdb->insert(
                        $table,
                        [
                            'post_id'      => $post_id,
                            'check_in'     => $check_in,
                            'check_out'    => $check_out,
                            'price'        => $price,
                            'adult_price'  => $adult_price,
                            'child_price'  => $children_price,
                            'infant_price' => $infant_price,
                            'status'       => $status,
                            'groupday'     => 1,
                            'post_type'    => get_post_type( $post_id )
                        ]
                    );
                } else {
                    for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                        $wpdb->insert(
                            $table,
                            [
                                'post_id'      => $post_id,
                                'check_in'     => $i,
                                'check_out'    => $i,
                                'price'        => $price,
                                'adult_price'  => $adult_price,
                                'child_price'  => $children_price,
                                'infant_price' => $infant_price,
                                'status'       => $status,
                                'groupday'     => 0,
                                'post_type'    => get_post_type( $post_id )
                            ]
                        );
                    }
                }


                return (int)$wpdb->insert_id;
            }

            public function traveler_get_availability( $post_id = '', $check_in = '', $check_out = '' )
            {
                global $wpdb;

                $table = $wpdb->prefix . 'st_availability';

                $sql = "SELECT * FROM {$table} WHERE post_id = {$post_id} AND ( ( CAST( check_in AS UNSIGNED ) >= CAST( {$check_in} AS UNSIGNED) AND CAST( check_in AS UNSIGNED ) <= CAST( {$check_out} AS UNSIGNED ) ) OR ( CAST( check_out AS UNSIGNED ) >= CAST( {$check_in} AS UNSIGNED ) AND ( CAST( check_out AS UNSIGNED ) <= CAST( {$check_out} AS UNSIGNED ) ) ) )";

                $result = $wpdb->get_results( $sql, ARRAY_A );

                $return = [];

                if ( !empty( $result ) ) {
                    foreach ( $result as $item ) {
                        $return[] = [
                            'id'             => $item[ 'id' ],
                            'post_id'        => $item[ 'post_id' ],
                            'check_in'       => date( 'Y-m-d', $item[ 'check_in' ] ),
                            'check_out'      => date( 'Y-m-d', strtotime( '+1 day', $item[ 'check_out' ] ) ),
                            'price'          => (float)$item[ 'price' ],
                            'adult_price'    => (float)$item[ 'adult_price' ],
                            'children_price' => (float)$item[ 'child_price' ],
                            'infant_price'   => (float)$item[ 'infant_price' ],
                            'status'         => $item[ 'status' ],
                            'groupday'       => $item[ 'groupday' ],
                        ];
                    }
                }

                return $return;
            }

            public function traveler_split_availability( $result = [], $check_in = '', $check_out = '' )
            {
                $return = [];

                if ( !empty( $result ) ) {
                    foreach ( $result as $item ) {
                        $check_in  = (int)$check_in;
                        $check_out = (int)$check_out;

                        $start = strtotime( $item[ 'start' ] );
                        $end   = strtotime( '-1 day', strtotime( $item[ 'end' ] ) );

                        if ( $start < $check_in && $end >= $check_in ) {
                            $return[ 'insert' ][] = [
                                'post_id'   => $item[ 'post_id' ],
                                'check_in'  => strtotime( $item[ 'check_in' ] ),
                                'check_out' => strtotime( '-1 day', $check_in ),
                                'price'     => (float)$item[ 'price' ],
                                'status'    => $item[ 'status' ],
                                'groupday'  => $item[ 'groupday' ],
                            ];
                        }

                        if ( $start <= $check_out && $end > $check_out ) {
                            $return[ 'insert' ][] = [
                                'post_id'   => $item[ 'post_id' ],
                                'check_in'  => strtotime( '+1 day', $check_out ),
                                'check_out' => strtotime( '-1 day', strtotime( $item[ 'check_out' ] ) ),
                                'price'     => (float)$item[ 'price' ],
                                'status'    => $item[ 'status' ],
                                'groupday'  => $item[ 'groupday' ],
                            ];
                        }

                        $return[ 'delete' ][] = [
                            'id' => $item[ 'id' ]
                        ];
                    }
                }

                return $return;
            }

            public function _get_availability_hotel()
            {
                $results       = [];
                $post_id       = STInput::request( 'post_id', '' );
                $check_in      = STInput::request( 'start', '' );
                $check_out     = STInput::request( 'end', '' );
                $price_ori     = floatval( get_post_meta( $post_id, 'price', true ) );
                $default_state = get_post_meta( $post_id, 'default_state', true );
                $number_room   = intval( get_post_meta( $post_id, 'number_room', true ) );

                if ( get_post_type( $post_id ) == 'hotel_room' ) {
                    $data = self::_getdataHotel( $post_id, $check_in, $check_out );

                    for ( $i = intval( $check_in ); $i <= intval( $check_out ); $i = strtotime( '+1 day', $i ) ) {
                        $in_date = false;
                        if ( is_array( $data ) && count( $data ) ) {
                            foreach ( $data as $key => $val ) {
                                if ( $i >= intval( $val->check_in ) && $i <= intval( $val->check_out ) ) {
                                    $status = $val->status;
                                    if ( $status != 'unavailable' ) {
                                        $item = [
                                            'price'   => floatval( $val->price ),
                                            'start'   => date( 'Y-m-d', $i ),
                                            'title'   => get_the_title( $post_id ),
                                            'item_id' => $val->id,
                                            'status'  => $val->status,
                                        ];
                                    } else {
                                        unset( $item );
                                    }
                                    if ( !$in_date )
                                        $in_date = true;
                                }
                            }
                        }
                        if ( isset( $item ) ) {
                            $results[] = $item;
                            unset( $item );
                        }
                        if ( !$in_date && ( $default_state == 'available' || !$default_state ) ) {
                            $item_ori  = [
                                'price'  => $price_ori,
                                'start'  => date( 'Y-m-d', $i ),
                                'title'  => get_the_title( $post_id ),
                                'number' => $number_room,
                                'status' => 'available'
                            ];
                            $results[] = $item_ori;
                            unset( $item_ori );
                        }
                    }
                }

                echo json_encode( $results );
                die();
            }

            public function _get_availability_rental()
            {
                $results     = [];
                $post_id     = STInput::request( 'post_id', '' );
                $check_in    = STInput::request( 'start', '' );
                $check_out   = STInput::request( 'end', '' );
                $price_ori   = floatval( get_post_meta( $post_id, 'price', true ) );
                $number_room = intval( get_post_meta( $post_id, 'number_room', true ) );

                if ( get_post_type( $post_id ) == 'st_rental' ) {
                    $data = self::_getdataRental( $post_id, $check_in, $check_out );

                    for ( $i = intval( $check_in ); $i <= intval( $check_out ); $i = strtotime( '+1 day', $i ) ) {
                        $in_date = false;
                        if ( is_array( $data ) && count( $data ) ) {
                            foreach ( $data as $key => $val ) {
                                if ( $i == intval( $val->check_in ) && $i == intval( $val->check_out ) ) {
                                    $status = $val->status;
                                    if ( $status != 'unavailable' ) {
                                        $item = [
                                            'price'   => floatval( $val->price ),
                                            'start'   => date( 'Y-m-d', $i ),
                                            'title'   => get_the_title( $post_id ),
                                            'item_id' => $val->id,
                                            'status'  => $val->status,
                                        ];
                                    } else {
                                        unset( $item );
                                    }
                                    if ( !$in_date )
                                        $in_date = true;
                                }
                            }
                        }
                        if ( isset( $item ) ) {
                            $results[] = $item;
                            unset( $item );
                        }
                        if ( !$in_date ) {
                            $item_ori  = [
                                'price'  => $price_ori,
                                'start'  => date( 'Y-m-d', $i ),
                                'title'  => get_the_title( $post_id ),
                                'number' => $number_room,
                                'status' => 'available'
                            ];
                            $results[] = $item_ori;
                            unset( $item_ori );
                        }
                    }
                }

                echo json_encode( $results );
                die();
            }

            public function _get_availability_flight()
            {
                $results   = [];
                $post_id   = STInput::request( 'post_id', '' );
                $check_in  = STInput::request( 'start', '' );
                $check_out = STInput::request( 'end', '' );
                $price_ori = 0;

                if ( get_post_type( $post_id ) == 'st_flight' ) {
                    $data = self::_getdataFlight( $post_id, $check_in, $check_out );

                    for ( $i = intval( $check_in ); $i <= intval( $check_out ); $i = strtotime( '+1 day', $i ) ) {
                        $in_date = false;
                        if ( is_array( $data ) && count( $data ) ) {
                            foreach ( $data as $key => $val ) {
                                if ( $i == intval( $val->start_date ) && $i == intval( $val->end_date ) ) {
                                    $status = $val->status;
                                    if ( $status != 'unavailable' ) {
                                        $item = [
                                            'eco_price'      => floatval( $val->eco_price ),
                                            'business_price' => floatval( $val->business_price ),
                                            'start'          => date( 'Y-m-d', $i ),
                                            'title'          => get_the_title( $post_id ),
                                            'item_id'        => $val->id,
                                            'status'         => $val->status,
                                        ];
                                    } else {
                                        unset( $item );
                                    }
                                    if ( !$in_date )
                                        $in_date = true;
                                }
                            }
                        }
                        if ( isset( $item ) ) {
                            $results[] = $item;
                            unset( $item );
                        }

                        if ( !$in_date ) {
                            $item_ori  = [
                                'eco_price'      => $price_ori,
                                'business_price' => $price_ori,
                                'start'          => date( 'Y-m-d', $i ),
                                'title'          => get_the_title( $post_id ),
                                'status'         => 'available'
                            ];
                            $results[] = $item_ori;
                            unset( $item_ori );
                        }
                    }
                }

                echo json_encode( $results );
                die();
            }


            public function _get_availability_tour()
            {
                $results   = [];
                $tour_id   = STInput::request( 'tour_id', '' );
                $check_in  = STInput::request( 'start', '' );
                $check_out = STInput::request( 'end', '' );
                if ( get_post_type( $tour_id ) == 'st_tours' ) {
                    $max_people   = intval( get_post_meta( $tour_id, 'max_people', true ) );
                    $adult_price  = floatval( get_post_meta( $tour_id, 'adult_price', true ) );
                    $child_price  = floatval( get_post_meta( $tour_id, 'child_price', true ) );
                    $infant_price = floatval( get_post_meta( $tour_id, 'infant_price', true ) );

                    if ( $adult_price < 0 ) $adult_price = 0;
                    if ( $child_price < 0 ) $child_price = 0;
                    if ( $infant_price < 0 ) $infant_price = 0;

                    $type_tour = get_post_meta( $tour_id, 'type_tour', true );

                    $data_tour = self::_getdataTourEachDate( $tour_id, $check_in, $check_out );
                    if ( is_array( $data_tour ) && count( $data_tour ) ) {
                        foreach ( $data_tour as $key => $val ) {
                            if ( $val->status == 'available' ) {
                                if ( intval( $val->groupday ) == 1 ) {
                                    $results[] = [
                                        'title'        => get_the_title( $tour_id ),
                                        'start'        => date( 'Y-m-d', $val->check_in ),
                                        'end'          => date( 'Y-m-d', strtotime( '+1 day', $val->check_out ) ),
                                        'adult_price'  => ( (float)$val->adult_price < 0 ) ? 0 : (float)$val->adult_price,
                                        'child_price'  => ( (float)$val->child_price < 0 ) ? 0 : (float)$val->child_price,
                                        'infant_price' => ( (float)$val->infant_price < 0 ) ? 0 : (float)$val->infant_price,
                                        'status'       => 'available'
                                    ];
                                } else {
                                    for ( $i = $val->check_in; $i <= $val->check_out; $i = strtotime( '+1 day', $i ) ) {
                                        $results[] = [
                                            'title'        => get_the_title( $tour_id ),
                                            'start'        => date( 'Y-m-d', $i ),
                                            'adult_price'  => ( (float)$val->adult_price < 0 ) ? 0 : (float)$val->adult_price,
                                            'child_price'  => ( (float)$val->child_price < 0 ) ? 0 : (float)$val->child_price,
                                            'infant_price' => ( (float)$val->infant_price < 0 ) ? 0 : (float)$val->infant_price,
                                            'status'       => 'available',
                                        ];
                                    }
                                }
                            }
                        }

                        if ( $type_tour == 'daily_tour' ) {
                            for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                                $in_item = false;
                                foreach ( $data_tour as $key => $val ) {
                                    if ( $i >= $val->check_in && $i <= $val->check_out ) {
                                        $in_item = true;
                                        break;
                                    }
                                }

                                if ( !$in_item ) {
                                    $results[] = [
                                        'title'        => get_the_title( $tour_id ),
                                        'start'        => date( 'Y-m-d', $i ),
                                        'adult_price'  => $adult_price,
                                        'child_price'  => $child_price,
                                        'infant_price' => $infant_price,
                                        'status'       => 'available',
                                    ];
                                }
                            }
                        }

                    } else {
                        if ( $type_tour == 'daily_tour' || !$type_tour ) {
                            for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                                $results[] = [
                                    'title'        => get_the_title( $tour_id ),
                                    'start'        => date( 'Y-m-d', $i ),
                                    'adult_price'  => $adult_price,
                                    'child_price'  => $child_price,
                                    'infant_price' => $infant_price,
                                    'status'       => 'available'
                                ];
                            }
                        }
                    }
                }
                echo json_encode( $results );
                die();
            }

            public function _get_availability_activity()
            {
                $results     = [];
                $activity_id = STInput::request( 'activity_id', '' );
                $check_in    = STInput::request( 'start', '' );
                $check_out   = STInput::request( 'end', '' );
                if ( get_post_type( $activity_id ) == 'st_activity' ) {
                    $max_people   = intval( get_post_meta( $activity_id, 'max_people', true ) );
                    $adult_price  = floatval( get_post_meta( $activity_id, 'adult_price', true ) );
                    $child_price  = floatval( get_post_meta( $activity_id, 'child_price', true ) );
                    $infant_price = floatval( get_post_meta( $activity_id, 'infant_price', true ) );

                    if ( $adult_price < 0 ) $adult_price = 0;
                    if ( $child_price < 0 ) $child_price = 0;
                    if ( $infant_price < 0 ) $infant_price = 0;

                    $type_activity = get_post_meta( $activity_id, 'type_activity', true );

                    $data_activity = self::_getdataActivityEachDate( $activity_id, $check_in, $check_out );
                    if ( is_array( $data_activity ) && count( $data_activity ) ) {
                        foreach ( $data_activity as $key => $val ) {
                            if ( $val->status == 'available' ) {
                                if ( intval( $val->groupday ) == 1 ) {
                                    $results[] = [
                                        'title'        => get_the_title( $activity_id ),
                                        'start'        => date( 'Y-m-d', $val->check_in ),
                                        'end'          => date( 'Y-m-d', strtotime( '+1 day', $val->check_out ) ),
                                        'adult_price'  => ( (float)$val->adult_price < 0 ) ? 0 : (float)$val->adult_price,
                                        'child_price'  => ( (float)$val->child_price < 0 ) ? 0 : (float)$val->child_price,
                                        'infant_price' => ( (float)$val->infant_price < 0 ) ? 0 : (float)$val->infant_price,
                                        'status'       => 'available'
                                    ];
                                } else {
                                    for ( $i = $val->check_in; $i <= $val->check_out; $i = strtotime( '+1 day', $i ) ) {
                                        $results[] = [
                                            'title'        => get_the_title( $activity_id ),
                                            'start'        => date( 'Y-m-d', $i ),
                                            'adult_price'  => ( (float)$val->adult_price < 0 ) ? 0 : (float)$val->adult_price,
                                            'child_price'  => ( (float)$val->child_price < 0 ) ? 0 : (float)$val->child_price,
                                            'infant_price' => ( (float)$val->infant_price < 0 ) ? 0 : (float)$val->infant_price,
                                            'status'       => 'available',
                                        ];
                                    }
                                }
                            }
                        }

                        if ( $type_activity == 'daily_activity' ) {
                            for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                                $in_item = false;
                                foreach ( $data_activity as $key => $val ) {
                                    if ( $i >= $val->check_in && $i <= $val->check_out ) {
                                        $in_item = true;
                                        break;
                                    }
                                }

                                if ( !$in_item ) {
                                    $results[] = [
                                        'title'        => get_the_title( $activity_id ),
                                        'start'        => date( 'Y-m-d', $i ),
                                        'adult_price'  => $adult_price,
                                        'child_price'  => $child_price,
                                        'infant_price' => $infant_price,
                                        'status'       => 'available',
                                    ];
                                }
                            }
                        }

                    } else {
                        if ( $type_activity == 'daily_activity' || !$type_activity ) {
                            for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                                $results[] = [
                                    'title'        => get_the_title( $activity_id ),
                                    'start'        => date( 'Y-m-d', $i ),
                                    'adult_price'  => $adult_price,
                                    'child_price'  => $child_price,
                                    'infant_price' => $infant_price,
                                    'status'       => 'available'
                                ];
                            }
                        }
                    }
                }
                echo json_encode( $results );
                die();
            }

            public function _get_availability_tour_frontend()
            {
                $results   = [];
                $tour_id   = STInput::request( 'tour_id', '' );
                $tour_id   = TravelHelper::post_origin( $tour_id, 'st_tours' );
                $check_in  = STInput::request( 'start', '' );
                $check_out = STInput::request( 'end', '' );
                $today     = strtotime( date( 'Y-m-d' ) );

                if ( get_post_type( $tour_id ) == 'st_tours' ) {
                    $adult_price  = floatval( get_post_meta( $tour_id, 'adult_price', true ) );
                    $child_price  = floatval( get_post_meta( $tour_id, 'child_price', true ) );
                    $infant_price = floatval( get_post_meta( $tour_id, 'infant_price', true ) );

                    if ( $adult_price < 0 ) $adult_price = 0;
                    if ( $child_price < 0 ) $child_price = 0;
                    if ( $infant_price < 0 ) $infant_price = 0;

                    $type_tour      = get_post_meta( $tour_id, 'type_tour', true );
                    $booking_period = intval( get_post_meta( $tour_id, 'tours_booking_period', true ) );
                    $max_people     = intval( get_post_meta( $tour_id, 'max_people', true ) );

                    $data_tour = self::_getdataTourEachDate( $tour_id, $check_in, $check_out );

                    if ( is_array( $data_tour ) && count( $data_tour ) ) {

                        foreach ( $data_tour as $key => $val ) {
                            if ( $val->status == 'available' ) {
                                /**
                                 * @updated 1.2.8
                                 **/
                                $cant_book = [];
                                if ( $max_people > 0 ) {
                                    //$cant_book = self::_get_tour_cant_order($tour_id, date('m/d/Y', $val->check_in), $max_people);
                                    if ( $type_tour == 'daily_tour' ) {
                                        $check_out_group  = $val->check_in;
                                        $starttime_string = self::_get_starttime_by_date( $tour_id, $check_out_group, $check_out_group );
                                    } else {
                                        //$check_out_group = $val->check_in;
                                        $check_out_group  = self::_get_check_out_by_groupday( $tour_id, $val->check_in );
                                        $starttime_string = self::_get_starttime_by_date( $tour_id, $val->check_in, $val->check_in );
                                    }

                                    $starttime_arr = explode( ', ', $starttime_string );

                                    $sub_total = 0;
                                    foreach ( $starttime_arr as $starttime_item ) {
                                        if ( $type_tour == 'daily_tour' ) {
                                            $test = self::_get_free_peple_by_time( $tour_id, $check_out_group, $check_out_group, $starttime_item );
                                        } else {
                                            $test = self::_get_free_peple_by_time( $tour_id, $val->check_in, $check_out_group, $starttime_item );
                                        }

                                        if ( !empty( $test ) ) {
                                            $free_people = intval( $test[ 'free_people' ] );
                                            $sub_total   = $sub_total + $free_people;
                                        } else {
                                            $free_people = $max_people;
                                            $sub_total   = $sub_total + $free_people;
                                        }
                                    }
                                } else {
                                    $sub_total = 2;
                                }

                                $period = STDate::dateDiff( date( 'Y-m-d', $today ), date( 'Y-m-d', $val->check_in ) );
                                if ( intval( $val->groupday ) == 1 ) {
                                    if ( $max_people > 0 ) {
                                        /*if ($type_tour == 'daily_tour') {
		                                $check_out_group = $val->check_in;
		                                $starttime_string = self::_get_starttime_by_date($tour_id, $check_out_group, $check_out_group);
	                                }else{*/
                                        $check_out_group  = self::_get_check_out_by_groupday( $tour_id, $val->check_in );
                                        $starttime_string = self::_get_starttime_by_date( $tour_id, $val->check_in, $val->check_in );
                                        /*}*/

                                        ////Get starttime of checkout day

                                        $starttime_arr = explode( ', ', $starttime_string );

                                        $sub_total = 0;
                                        foreach ( $starttime_arr as $starttime_item ) {
                                            $test1 = self::_get_free_peple_by_time_groupday( $tour_id, $val->check_in, $check_out_group, $starttime_item );

                                            if ( !empty( $test1 ) ) {
                                                $free_people = intval( $test1[ 'free_people' ] );
                                                $sub_total   = $sub_total + $free_people;
                                            } else {
                                                $free_people = $max_people;
                                                $sub_total   = $sub_total + $free_people;
                                            }
                                        }
                                    } else {
                                        $sub_total = 2;
                                    }

                                    if ( $val->check_in >= $today && $sub_total > 0 && $period >= $booking_period ) {

                                        $results[] = [
                                            'start'        => date( 'Y-m-d', $val->check_in ),
                                            'day'          => date( 'd', $val->check_in ),
                                            'date'         => date( 'Y-m-d', $val->check_in ),
                                            'end'          => date( 'Y-m-d', strtotime( '+1 day', $val->check_out ) ),
                                            'date_end'     => date( 'd', $val->check_out ),
                                            'adult_price'  => ( (float)$val->adult_price > 0 ) ? TravelHelper::format_money( $val->adult_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'child_price'  => ( (float)$val->child_price > 0 ) ? TravelHelper::format_money( $val->child_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'infant_price' => ( (float)$val->infant_price > 0 ) ? TravelHelper::format_money( $val->infant_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'status'       => 'available'
                                        ];
                                    }
                                } else {
                                    if ( $val->check_in >= $today && $sub_total > 0 && $period >= $booking_period ) {
                                        $results[] = [
                                            'start'        => date( 'Y-m-d', $val->check_in ),
                                            'day'          => date( 'd', $val->check_in ),
                                            'date'         => date( 'Y-m-d', $val->check_in ),
                                            'adult_price'  => ( (float)$val->adult_price > 0 ) ? TravelHelper::format_money( $val->adult_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'child_price'  => ( (float)$val->child_price > 0 ) ? TravelHelper::format_money( $val->child_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'infant_price' => ( (float)$val->infant_price > 0 ) ? TravelHelper::format_money( $val->infant_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'status'       => 'available'
                                        ];
                                    }
                                }
                            }
                        }

                        if ( $type_tour == 'daily_tour' ) {

                            for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                                $in_item = false;
                                $status  = 'available';
                                foreach ( $data_tour as $key => $val ) {
                                    if ( $i >= $val->check_in && $i <= $val->check_out ) {
                                        $in_item = true;
                                        $status  = $val->status;
                                        break;
                                    }
                                }

                                if ( !$in_item ) {

                                    /**
                                     * @updated 1.2.8
                                     **/
                                    $cant_book = [];
                                    if ( $max_people > 0 ) {
                                        $cant_book = self::_get_tour_cant_order( $tour_id, date( 'm/d/Y', $i ), $max_people );

                                        $check_out_group = $i;

                                        $starttime_string = self::_get_starttime_by_date( $tour_id, $check_out_group, $check_out_group );
                                        $starttime_arr    = explode( ', ', $starttime_string );

                                        $sub_total = 0;
                                        foreach ( $starttime_arr as $starttime_item ) {
                                            $test = self::_get_free_peple_by_time( $tour_id, $check_out_group, $check_out_group, $starttime_item );

                                            if ( !empty( $test ) ) {
                                                $free_people = intval( $test[ 'free_people' ] );
                                                $sub_total   = $sub_total + $free_people;
                                            } else {
                                                $free_people = $max_people;
                                                $sub_total   = $sub_total + $free_people;
                                            }
                                        }
                                    } else {
                                        $sub_total = 2;
                                    }

                                    $period = STDate::dateDiff( date( 'Y-m-d', $today ), date( 'Y-m-d', $i ) );
                                    if ( $i >= $today && $sub_total > 0 && $period >= $booking_period ) {
                                        $results[] = [
                                            'start'        => date( 'Y-m-d', $i ),
                                            'day'          => date( 'd', $i ),
                                            'date'         => date( 'Y-m-d', $i ),
                                            'adult_price'  => ( $adult_price ) ? TravelHelper::format_money( $adult_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'child_price'  => ( $child_price ) ? TravelHelper::format_money( $child_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'infant_price' => ( $infant_price ) ? TravelHelper::format_money( $infant_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'status'       => 'available'
                                        ];
                                    }

                                }
                            }
                        }

                    } else {
                        //DAILY TOUR CHECK
                        if ( $type_tour == 'daily_tour' ) {
                            for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                                /**
                                 * @updated 1.2.8
                                 **/
                                $cant_book = [];
                                if ( $max_people > 0 ) {
                                    $cant_book = self::_get_tour_cant_order( $tour_id, date( 'm/d/Y', $i ), $max_people );


                                    $check_out_group = $i;

                                    $starttime_string = self::_get_starttime_by_date( $tour_id, $check_out_group, $check_out_group );
                                    $starttime_arr    = explode( ', ', $starttime_string );

                                    $sub_total = 0;
                                    foreach ( $starttime_arr as $starttime_item ) {
                                        $test = self::_get_free_peple_by_time( $tour_id, $check_out_group, $check_out_group, $starttime_item );

                                        if ( !empty( $test ) ) {
                                            $free_people = intval( $test[ 'free_people' ] );
                                            $sub_total   = $sub_total + $free_people;
                                        } else {
                                            $free_people = $max_people;
                                            $sub_total   = $sub_total + $free_people;
                                        }
                                    }
                                } else {
                                    $sub_total = 2;
                                }

                                $period = STDate::dateDiff( date( 'Y-m-d', $today ), date( 'Y-m-d', $i ) );
                                if ( $i >= $today && $sub_total > 0 && $period >= $booking_period ) {
                                    $results[] = [
                                        'title'        => get_the_title( $tour_id ),
                                        'start'        => date( 'Y-m-d', $i ),
                                        'day'          => date( 'd', $i ),
                                        'date'         => date( 'Y-m-d', $i ),
                                        'adult_price'  => ( $adult_price ) ? TravelHelper::format_money( $adult_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                        'child_price'  => ( $child_price ) ? TravelHelper::format_money( $child_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                        'infant_price' => ( $infant_price ) ? TravelHelper::format_money( $infant_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                        'status'       => 'available'
                                    ];
                                }
                            }
                        }
                    }
                }//End check type is st_tours

                $st_tour_available = $results;
                $return            = [];
                if ( count( $results ) ) {
                    for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                        $in_date = false;
                        foreach ( $results as $key => $val ) {
                            $start = strtotime( $val[ 'start' ] );
                            $end   = ( isset( $val[ 'end' ] ) ) ? strtotime( '-1 day', strtotime( $val[ 'end' ] ) ) : strtotime( $val[ 'start' ] );
                            if ( $i >= $start && $i <= $end ) {
                                $in_date = true;
                                break;
                            }
                        }

                        if ( !$in_date ) {

                            //Check pediod date
                            $return[] = [
                                'start'  => date( 'Y-m-d', $i ),
                                'day'    => date( 'd', $i ),
                                'date'   => date( 'Y-m-d', $i ),
                                'status' => 'not_available'
                            ];
                        }
                    }

                    foreach ( $results as $key => $val ) {
                        $return[] = $val;
                    }
                } else {
                    for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                        $return[] = [
                            'start'  => date( 'Y-m-d', $i ),
                            'day'    => date( 'd', $i ),
                            'date'   => date( 'Y-m-d', $i ),
                            'status' => 'not_available'
                        ];
                    }
                }
                echo json_encode( $return );
                die();
            }

            public function _get_availability_activity_frontend()
            {
                $results     = [];
                $activity_id = STInput::request( 'activity_id', '' );
                $activity_id = TravelHelper::post_origin( $activity_id, 'st_activity' );
                $check_in    = STInput::request( 'start', '' );
                $check_out   = STInput::request( 'end', '' );
                $today       = strtotime( date( 'Y-m-d' ) );
                if ( get_post_type( $activity_id ) == 'st_activity' ) {
                    $adult_price  = floatval( get_post_meta( $activity_id, 'adult_price', true ) );
                    $child_price  = floatval( get_post_meta( $activity_id, 'child_price', true ) );
                    $infant_price = floatval( get_post_meta( $activity_id, 'infant_price', true ) );

                    if ( $adult_price < 0 ) $adult_price = 0;
                    if ( $child_price < 0 ) $child_price = 0;
                    if ( $infant_price < 0 ) $infant_price = 0;

                    $type_activity  = get_post_meta( $activity_id, 'type_activity', true );
                    $booking_period = intval( get_post_meta( $activity_id, 'activity_booking_period', true ) );
                    $max_people     = intval( get_post_meta( $activity_id, 'max_people', true ) );

                    $data_activity = self::_getdataActivityEachDate( $activity_id, $check_in, $check_out );
                    if ( is_array( $data_activity ) && count( $data_activity ) ) {

                        foreach ( $data_activity as $key => $val ) {
                            if ( $val->status == 'available' ) {
                                $cant_book = [];
                                $cant_book = [];
                                if ( $max_people > 0 ) {
                                    $cant_book = self::_get_activity_cant_order( $activity_id, date( 'm/d/Y', $val->check_in ), $max_people );
                                }
                                $period = STDate::dateDiff( date( 'Y-m-d', $today ), date( 'Y-m-d', $val->check_in ) );

                                if ( intval( $val->groupday ) == 1 ) {
                                    if ( $val->check_in >= $today && count( $cant_book ) <= 0 && $period >= $booking_period ) {
                                        $results[] = [
                                            'start'        => date( 'Y-m-d', $val->check_in ),
                                            'day'          => date( 'd', $val->check_in ),
                                            'date'         => date( 'Y-m-d', $val->check_in ),
                                            'end'          => date( 'Y-m-d', strtotime( '+1 day', $val->check_out ) ),
                                            'date_end'     => date( 'd', $val->check_out ),
                                            'adult_price'  => ( (float)$val->adult_price > 0 ) ? TravelHelper::format_money( $val->adult_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'child_price'  => ( (float)$val->child_price > 0 ) ? TravelHelper::format_money( $val->child_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'infant_price' => ( (float)$val->infant_price > 0 ) ? TravelHelper::format_money( $val->infant_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'status'       => 'available'
                                        ];
                                    }
                                } else {
                                    if ( $val->check_in >= $today && count( $cant_book ) <= 0 && $period >= $booking_period ) {
                                        $results[] = [
                                            'start'        => date( 'Y-m-d', $val->check_in ),
                                            'day'          => date( 'd', $val->check_in ),
                                            'date'         => date( 'Y-m-d', $val->check_in ),
                                            'adult_price'  => ( (float)$val->adult_price > 0 ) ? TravelHelper::format_money( $val->adult_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'child_price'  => ( (float)$val->child_price > 0 ) ? TravelHelper::format_money( $val->child_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'infant_price' => ( (float)$val->infant_price > 0 ) ? TravelHelper::format_money( $val->infant_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'status'       => 'available'
                                        ];
                                    }
                                }
                            }
                        }

                        if ( $type_activity == 'daily_activity' ) {

                            for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                                $in_item = false;
                                $status  = 'available';
                                foreach ( $data_activity as $key => $val ) {
                                    if ( $i >= $val->check_in && $i <= $val->check_out ) {
                                        $in_item = true;
                                        $status  = $val->status;
                                        break;
                                    }
                                }

                                if ( !$in_item ) {
                                    $cant_book = [];
                                    if ( $max_people > 0 ) {
                                        $cant_book = self::_get_activity_cant_order( $activity_id, date( 'm/d/Y', $i ), $max_people );
                                    }
                                    $period = STDate::dateDiff( date( 'Y-m-d', $today ), date( 'Y-m-d', $i ) );
                                    if ( $i >= $today && count( $cant_book ) <= 0 && $period >= $booking_period ) {
                                        $results[] = [
                                            'start'        => date( 'Y-m-d', $i ),
                                            'day'          => date( 'd', $i ),
                                            'date'         => date( 'Y-m-d', $i ),
                                            'adult_price'  => ( $adult_price ) ? TravelHelper::format_money( $adult_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'child_price'  => ( $child_price ) ? TravelHelper::format_money( $child_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'infant_price' => ( $infant_price ) ? TravelHelper::format_money( $infant_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'status'       => 'available'
                                        ];
                                    }

                                }
                            }
                        }

                    } else {
                        if ( $type_activity == 'daily_activity' ) {
                            for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                                $cant_book = [];
                                if ( $max_people > 0 ) {
                                    $cant_book = self::_get_activity_cant_order( $activity_id, date( 'm/d/Y', $i ), $max_people );
                                }
                                $period = STDate::dateDiff( date( 'Y-m-d', $today ), date( 'Y-m-d', $i ) );
                                if ( $i >= $today && count( $cant_book ) <= 0 && $period >= $booking_period ) {
                                    $results[] = [
                                        'title'        => get_the_title( $activity_id ),
                                        'start'        => date( 'Y-m-d', $i ),
                                        'day'          => date( 'd', $i ),
                                        'date'         => date( 'Y-m-d', $i ),
                                        'adult_price'  => ( $adult_price ) ? TravelHelper::format_money( $adult_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                        'child_price'  => ( $child_price ) ? TravelHelper::format_money( $child_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                        'infant_price' => ( $infant_price ) ? TravelHelper::format_money( $infant_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                        'status'       => 'available'
                                    ];
                                }
                            }
                        }
                    }
                }
                $st_tour_available = $results;
                $return            = [];
                if ( count( $results ) ) {
                    for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                        $in_date = false;
                        foreach ( $results as $key => $val ) {
                            $start = strtotime( $val[ 'start' ] );
                            $end   = ( isset( $val[ 'end' ] ) ) ? strtotime( '-1 day', strtotime( $val[ 'end' ] ) ) : strtotime( $val[ 'start' ] );
                            if ( $i >= $start && $i <= $end ) {
                                $in_date = true;
                                break;
                            }
                        }

                        if ( !$in_date ) {
                            $return[] = [
                                'start'  => date( 'Y-m-d', $i ),
                                'day'    => date( 'd', $i ),
                                'date'   => date( 'Y-m-d', $i ),
                                'status' => 'not_available'
                            ];
                        }
                    }

                    foreach ( $results as $key => $val ) {
                        $return[] = $val;
                    }
                } else {
                    for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                        $return[] = [
                            'start'  => date( 'Y-m-d', $i ),
                            'day'    => date( 'd', $i ),
                            'date'   => date( 'Y-m-d', $i ),
                            'status' => 'not_available'
                        ];
                    }
                }
                echo json_encode( $return );
                die();
            }

            static function _get_list_availability_tour_frontend( $tour_id, $check_in, $check_out )
            {
                $results = [];
                $today   = strtotime( date( 'Y-m-d' ) );
                if ( get_post_type( $tour_id ) == 'st_tours' ) {
                    $adult_price  = floatval( get_post_meta( $tour_id, 'adult_price', true ) );
                    $child_price  = floatval( get_post_meta( $tour_id, 'child_price', true ) );
                    $infant_price = floatval( get_post_meta( $tour_id, 'infant_price', true ) );

                    if ( $adult_price < 0 ) $adult_price = 0;
                    if ( $child_price < 0 ) $child_price = 0;
                    if ( $infant_price < 0 ) $infant_price = 0;

                    $type_tour      = get_post_meta( $tour_id, 'type_tour', true );
                    $booking_period = intval( get_post_meta( $tour_id, 'tours_booking_period', true ) );
                    $max_people     = intval( get_post_meta( $tour_id, 'max_people', true ) );
                    $data_tour      = self::_getdataTourEachDate( $tour_id, $check_in, $check_out );

                    if ( is_array( $data_tour ) && count( $data_tour ) ) {

                        foreach ( $data_tour as $key => $val ) {
                            if ( $val->status == 'available' ) {

                                /**
                                 * @updated 1.2.8
                                 **/
                                $cant_book = [];
                                if ( $max_people > 0 ) {
                                    $cant_book = self::_get_tour_cant_order( $tour_id, date( 'm/d/Y', $val->check_in ), $max_people );
                                }

                                $period = STDate::dateDiff( date( 'Y-m-d', $today ), date( 'Y-m-d', $val->check_in ) );

                                if ( intval( $val->groupday ) == 1 ) {
                                    if ( $val->check_in >= $today && count( $cant_book ) <= 0 && $period >= $booking_period ) {
                                        $results[] = [
                                            'start'        => date( 'Y-m-d', $val->check_in ),
                                            'end'          => date( 'Y-m-d', $val->check_out ),
                                            'adult_price'  => ( (float)$val->adult_price > 0 ) ? TravelHelper::format_money( $val->adult_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'child_price'  => ( (float)$val->child_price > 0 ) ? TravelHelper::format_money( $val->child_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'infant_price' => ( (float)$val->infant_price > 0 ) ? TravelHelper::format_money( $val->infant_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'status'       => 'available'
                                        ];
                                    }
                                } else {
                                    if ( $val->check_in >= $today && count( $cant_book ) <= 0 && $period >= $booking_period ) {
                                        $results[] = [
                                            'start'        => date( 'Y-m-d', $val->check_in ),
                                            'end'          => date( 'Y-m-d', $val->check_in ),
                                            'adult_price'  => ( (float)$val->adult_price > 0 ) ? TravelHelper::format_money( $val->adult_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'child_price'  => ( (float)$val->child_price > 0 ) ? TravelHelper::format_money( $val->child_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'infant_price' => ( (float)$val->infant_price > 0 ) ? TravelHelper::format_money( $val->infant_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'status'       => 'available'
                                        ];
                                    }
                                }
                            }
                        }

                        if ( $type_tour == 'daily_tour' ) {

                            for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                                $in_item = false;
                                $status  = 'available';
                                foreach ( $data_tour as $key => $val ) {
                                    if ( $i >= $val->check_in && $i <= $val->check_out ) {
                                        $in_item = true;
                                        $status  = $val->status;
                                        break;
                                    }
                                }

                                if ( !$in_item ) {
                                    /**
                                     * @updated 1.2.8
                                     **/
                                    $cant_book = [];
                                    if ( $max_people > 0 ) {
                                        $cant_book = self::_get_tour_cant_order( $tour_id, date( 'm/d/Y', $i ), $max_people );
                                    }
                                    $period = STDate::dateDiff( date( 'Y-m-d', $today ), date( 'Y-m-d', $i ) );
                                    if ( $i >= $today && count( $cant_book ) <= 0 && $period >= $booking_period ) {
                                        $results[] = [
                                            'start'        => date( 'Y-m-d', $i ),
                                            'end'          => date( 'Y-m-d', $i ),
                                            'adult_price'  => ( $adult_price ) ? TravelHelper::format_money( $adult_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'child_price'  => ( $child_price ) ? TravelHelper::format_money( $child_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'infant_price' => ( $infant_price ) ? TravelHelper::format_money( $infant_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'status'       => 'available'
                                        ];
                                    }

                                }
                            }
                        }

                    } else {
                        if ( $type_tour == 'daily_tour' ) {
                            for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                                /**
                                 * @updated 1.2.8
                                 **/
                                $cant_book = [];
                                if ( $max_people > 0 ) {
                                    $cant_book = self::_get_tour_cant_order( $tour_id, date( 'm/d/Y', $i ), $max_people );
                                }
                                $period = STDate::dateDiff( date( 'Y-m-d', $today ), date( 'Y-m-d', $i ) );
                                if ( $i >= $today && count( $cant_book ) <= 0 && $period >= $booking_period ) {
                                    $results[] = [
                                        'title'        => get_the_title( $tour_id ),
                                        'start'        => date( 'Y-m-d', $i ),
                                        'end'          => date( 'Y-m-d', $i ),
                                        'adult_price'  => ( $adult_price ) ? TravelHelper::format_money( $adult_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                        'child_price'  => ( $child_price ) ? TravelHelper::format_money( $child_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                        'infant_price' => ( $infant_price ) ? TravelHelper::format_money( $infant_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                        'status'       => 'available'
                                    ];
                                }
                            }
                        }
                    }
                }

                return $results;

            }

            static function _get_tour_cant_order( $tour_id, $check_in, $max_people = 0 )
            {
                if ( !TravelHelper::checkTableDuplicate( 'st_tours' ) ) return '';
                global $wpdb;

                $sql = "SELECT
				st_booking_id AS tour_id,
				SUM(
					(
						adult_number + child_number + infant_number
					)
				) AS booked
			FROM
				{$wpdb->prefix}st_order_item_meta
			WHERE
				st_booking_id = '{$tour_id}'
			AND st_booking_post_type = 'st_tours'
			AND (
				STR_TO_DATE('{$check_in}', '%m/%d/%Y') = STR_TO_DATE(check_in, '%m/%d/%Y')
			)
			AND `status` NOT IN ('trash', 'canceled')
			HAVING
				{$max_people} - SUM(
					(
						adult_number + child_number + infant_number
					)
				) <= 0";

                $result    = $wpdb->get_col( $sql, 0 );
                $list_date = [];
                if ( is_array( $result ) && count( $result ) ) {
                    $list_date = $result;
                }

                return $list_date;
            }

            static function _get_activity_cant_order( $activity_id, $check_in, $max_people = 0 )
            {
                if ( !TravelHelper::checkTableDuplicate( 'st_activity' ) ) return '';
                global $wpdb;

                $sql = "SELECT
				st_booking_id AS tour_id,
				SUM(
					(
						adult_number + child_number + infant_number
					)
				) AS booked
			FROM
				{$wpdb->prefix}st_order_item_meta
			WHERE
				st_booking_id = '{$activity_id}'
			AND st_booking_post_type = 'st_activity'
			AND (
				STR_TO_DATE('{$check_in}', '%m/%d/%Y') = STR_TO_DATE(check_in, '%m/%d/%Y')
			)
			AND `status` NOT IN ('trash', 'canceled')
			HAVING
				{$max_people} - SUM(
					(
						adult_number + child_number + infant_number
					)
				) <= 0";

                $result    = $wpdb->get_col( $sql, 0 );
                $list_date = [];
                if ( is_array( $result ) && count( $result ) ) {
                    $list_date = $result;
                }

                return $list_date;
            }

            static function _getdataHotel( $post_id, $check_in, $check_out )
            {
                global $wpdb;
                $sql     = "
			SELECT
				`id`,
				`post_id`,
				`post_type`,
				`check_in`,
				`check_out`,
				`number`,
				`price`,
				`status`
			FROM
				{$wpdb->prefix}st_availability
			WHERE
			post_id = {$post_id} 
			AND post_type='hotel_room'
			AND
			(
				(
					UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_in}'), '%Y-%m-%d')) < UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_in), '%Y-%m-%d'))
					AND UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_out}'), '%Y-%m-%d')) > UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_out), '%Y-%m-%d'))
				)
				OR (
					UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_in}'), '%Y-%m-%d')) BETWEEN UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_in), '%Y-%m-%d'))
					AND UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_out), '%Y-%m-%d'))
				)
				OR (
					UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_out}'), '%Y-%m-%d')) BETWEEN UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_in), '%Y-%m-%d'))
					AND UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_out), '%Y-%m-%d'))
				)
			)";
                $results = $wpdb->get_results( $sql );

                return $results;
            }

            static function _getdataRental( $post_id, $check_in, $check_out )
            {
                global $wpdb;
                $sql     = "
			SELECT
				`id`,
				`post_id`,
				`post_type`,
				`check_in`,
				`check_out`,
				`number`,
				`price`,
				`status`
			FROM
				{$wpdb->prefix}st_availability
			WHERE
			post_id = {$post_id} 
			AND post_type='st_rental'
			AND
			(
				(
					UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_in}'), '%Y-%m-%d')) < UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_in), '%Y-%m-%d'))
					AND UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_out}'), '%Y-%m-%d')) > UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_out), '%Y-%m-%d'))
				)
				OR (
					UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_in}'), '%Y-%m-%d')) BETWEEN UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_in), '%Y-%m-%d'))
					AND UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_out), '%Y-%m-%d'))
				)
				OR (
					UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_out}'), '%Y-%m-%d')) BETWEEN UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_in), '%Y-%m-%d'))
					AND UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_out), '%Y-%m-%d'))
				)
			)";
                $results = $wpdb->get_results( $sql );

                return $results;
            }


            static function _getdataFlight( $post_id, $check_in, $check_out )
            {
                global $wpdb;
                $sql     = "
			SELECT
				`id`,
				`post_id`,
				`start_date`,
				`end_date`,
				`eco_price`,
				`business_price`,
				`status`
			FROM
				{$wpdb->prefix}st_flight_availability
			WHERE
			post_id = {$post_id}
			AND
			(
				(
					UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_in}'), '%Y-%m-%d')) < UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(`start_date`), '%Y-%m-%d'))
					AND UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_out}'), '%Y-%m-%d')) > UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(`end_date`), '%Y-%m-%d'))
				)
				OR (
					UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_in}'), '%Y-%m-%d')) BETWEEN UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(`start_date`), '%Y-%m-%d'))
					AND UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(`end_date`), '%Y-%m-%d'))
				)
				OR (
					UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_out}'), '%Y-%m-%d')) BETWEEN UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(`start_date`), '%Y-%m-%d'))
					AND UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(`end_date`), '%Y-%m-%d'))
				)
			)";
                $results = $wpdb->get_results( $sql );

                return $results;
            }

            static function _getdataTourEachDate( $tour_id, $check_in, $check_out )
            {
                global $wpdb;

                $sql     = "
			SELECT
				`id`,
				`post_id`,
				`post_type`,
				`check_in`,
				`check_out`,
				`adult_price`,
				`child_price`,
				`infant_price`,
				`status`,
				`priority`,
				`groupday`
			FROM
				{$wpdb->prefix}st_availability
			WHERE
				post_id = '{$tour_id}'
			AND (
				(
					UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_in}'), '%Y-%m-%d')) < UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_in), '%Y-%m-%d'))
					AND UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_out}'), '%Y-%m-%d')) > UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_out), '%Y-%m-%d'))
				)
				OR (
					UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_in}'), '%Y-%m-%d')) BETWEEN UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_in), '%Y-%m-%d'))
					AND UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_out), '%Y-%m-%d'))
				)
				OR (
					UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_out}'), '%Y-%m-%d')) BETWEEN UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_in), '%Y-%m-%d'))
					AND UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_out), '%Y-%m-%d'))
				)
			) AND post_type = 'st_tours'";
                $results = $wpdb->get_results( $sql );

                return $results;
            }

            static function _getdataActivityEachDate( $activity_id, $check_in, $check_out )
            {
                global $wpdb;

                $sql     = "
                SELECT
                    `id`,
                    `post_id`,
                    `post_type`,
                    `check_in`,
                    `check_out`,
                    `adult_price`,
                    `child_price`,
                    `infant_price`,
                    `status`,
                    `groupday`
                FROM
                    {$wpdb->prefix}st_availability
                WHERE
                    post_id = '{$activity_id}'
                AND (
                    (
                        UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_in}'), '%Y-%m-%d')) < UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_in), '%Y-%m-%d'))
                        AND UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_out}'), '%Y-%m-%d')) > UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_out), '%Y-%m-%d'))
                    )
                    OR (
                        UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_in}'), '%Y-%m-%d')) BETWEEN UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_in), '%Y-%m-%d'))
                        AND UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_out), '%Y-%m-%d'))
                    )
                    OR (
                        UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME('{$check_out}'), '%Y-%m-%d')) BETWEEN UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_in), '%Y-%m-%d'))
                        AND UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(check_out), '%Y-%m-%d'))
                    )
                )";
                $results = $wpdb->get_results( $sql );

                return $results;
            }

            static function _getDisableCustomDate( $room_id, $month, $month2, $year, $year2, $date_format = false )
            {
                $date1            = $year . '-' . $month . '-01';
                $date2            = strtotime( $year2 . '-' . $month2 . '-01' );
                $date2            = date( 'Y-m-t', $date2 );
                $date_time_format = TravelHelper::getDateFormat();
                if ( !empty( $date_format ) ) {
                    $date_time_format = $date_format;
                }
                global $wpdb;
                $sql = "
                    SELECT
                        `check_in`,
                        `check_out`,
                        `number`,
                        `status`,
                        `priority`
                    FROM
                        {$wpdb->prefix}st_availability
                    WHERE
                        post_id = {$room_id}
        
                    AND (
                        (
                            '{$date1}' < DATE_FORMAT(FROM_UNIXTIME(check_in), '%Y-%m-%d')
                            AND '{$date2}' > DATE_FORMAT(FROM_UNIXTIME(check_out), '%Y-%m-%d')
                        )
                        OR (
                            '{$date1}' BETWEEN DATE_FORMAT(FROM_UNIXTIME(check_in), '%Y-%m-%d')
                            AND DATE_FORMAT(FROM_UNIXTIME(check_out), '%Y-%m-%d')
                        )
                        OR (
                            '$date2' BETWEEN DATE_FORMAT(FROM_UNIXTIME(check_in), '%Y-%m-%d')
                            AND DATE_FORMAT(FROM_UNIXTIME(check_out), '%Y-%m-%d')
                        )
                    )";

                $results       = $wpdb->get_results( $sql );
                $default_state = get_post_meta( $room_id, 'default_state', true );

                if ( !$default_state ) $default_state = 'available';
                $list_date = [];

                $start = strtotime( $date1 );
                $end   = strtotime( $date2 );
                if ( is_array( $results ) && count( $results ) ) {
                    for ( $i = $start; $i <= $end; $i = strtotime( '+1 day', $i ) ) {
                        $priority = 0;
                        $in_date  = false;
                        foreach ( $results as $key => $val ) {
                            $status = $val->status;
                            if ( $i == $val->check_in && $i == $val->check_out ) {
                                if ( $status == 'unavailable' ) {
                                    $date = $i;
                                } else {
                                    unset( $date );
                                }
                                if ( !$in_date ) {
                                    $in_date = true;
                                }
                            }
                        }

                        if ( $in_date && isset( $date ) ) {
                            $list_date[] = date( $date_time_format, $date );
                            unset( $date );
                        } else {
                            if ( !$in_date && $default_state == 'not_available' ) {
                                $list_date[] = date( $date_time_format, $i );
                                unset( $in_date );
                            }
                        }
                    }
                } else {
                    if ( $default_state == 'not_available' ) {
                        for ( $i = $start; $i <= $end; $i = strtotime( '+1 day', $i ) ) {
                            $list_date[] = date( $date_time_format, $i );
                        }
                    }
                }

                return $list_date;
            }

            static function _getDisableCustomDateRental( $rental_id, $month, $month2, $year, $year2, $date_format = false )
            {
                $date1            = $year . '-' . $month . '-01';
                $date2            = strtotime( $year2 . '-' . $month2 . '-01' );
                $date2            = date( 'Y-m-t', $date2 );
                $date_time_format = TravelHelper::getDateFormat();
                if ( !empty( $date_format ) ) {
                    $date_time_format = $date_format;
                }
                global $wpdb;
                $sql     = "
			SELECT
				`check_in`,
				`check_out`,
				`number`,
				`status`
			FROM
				{$wpdb->prefix}st_availability
			WHERE
				post_id = {$rental_id}

			AND (
				(
					'{$date1}' < DATE_FORMAT(FROM_UNIXTIME(check_in), '%Y-%m-%d')
					AND '{$date2}' > DATE_FORMAT(FROM_UNIXTIME(check_out), '%Y-%m-%d')
				)
				OR (
					'{$date1}' BETWEEN DATE_FORMAT(FROM_UNIXTIME(check_in), '%Y-%m-%d')
					AND DATE_FORMAT(FROM_UNIXTIME(check_out), '%Y-%m-%d')
				)
				OR (
					'$date2' BETWEEN DATE_FORMAT(FROM_UNIXTIME(check_in), '%Y-%m-%d')
					AND DATE_FORMAT(FROM_UNIXTIME(check_out), '%Y-%m-%d')
				)
			)";
                $results = $wpdb->get_results( $sql );

                $list_date = [];

                $start = strtotime( $date1 );
                $end   = strtotime( $date2 );
                if ( is_array( $results ) && count( $results ) ) {
                    for ( $i = $start; $i <= $end; $i = strtotime( '+1 day', $i ) ) {
                        foreach ( $results as $key => $val ) {
                            $status = $val->status;
                            if ( $i == $val->check_in && $i == $val->check_out ) {
                                if ( $status == 'unavailable' ) {
                                    $list_date[] = date( $date_time_format, $i );
                                } else {
                                    unset( $date );
                                }
                            }
                        }
                    }
                }

                return $list_date;
            }

            public function _add_custom_price()
            {
                global $wpdb;
                $check_in  = STInput::request( 'calendar_check_in', '' );
                $check_out = STInput::request( 'calendar_check_out', '' );
                if ( empty( $check_in ) || empty( $check_out ) ) {
                    echo json_encode( [
                        'type'    => 'error',
                        'status'  => 0,
                        'message' => __( 'The check in or check out field is not empty.', ST_TEXTDOMAIN )
                    ] );
                    die();
                }
                $check_in  = strtotime( $check_in );
                $check_out = strtotime( $check_out );
                if ( $check_in > $check_out ) {
                    echo json_encode( [
                        'type'    => 'error',
                        'status'  => 0,
                        'message' => __( 'The check out is later than the check in field.', ST_TEXTDOMAIN )
                    ] );
                    die();
                }

                $status = STInput::request( 'calendar_status', 'available' );
                if ( $status == 'available' ) {
                    if ( filter_var( $_POST[ 'calendar_price' ], FILTER_VALIDATE_FLOAT ) === false ) {
                        echo json_encode( [
                            'type'    => 'error',
                            'status'  => 0,
                            'message' => __( 'The price field is not a number.', ST_TEXTDOMAIN )
                        ] );
                        die();
                    }
                }
                $price   = floatval( STInput::request( 'calendar_price', '' ) );
                $post_id = STInput::request( 'calendar_post_id', '' );

                for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                    $hotels = self::_getdataHotel( $post_id, $i, $i );
                    if ( is_array( $hotels ) && count( $hotels ) ) {
                        foreach ( $hotels as $key => $val ) {
                            if ( $i == $val->check_in && $i == $val->check_out ) {
                                $data  = [
                                    'price'  => $price,
                                    'status' => $status,
                                ];
                                $where = [
                                    'id' => $val->id
                                ];
                                self::_updateData( $where, $data );
                            } else {
                                $data = [
                                    'post_id'   => $post_id,
                                    'post_type' => 'hotel_room',
                                    'check_in'  => $i,
                                    'check_out' => $i,
                                    'price'     => $price,
                                    'status'    => $status,
                                ];
                                self::_insertData( $data );
                            }
                        }
                    } else {
                        $data = [
                            'post_id'   => $post_id,
                            'post_type' => 'hotel_room',
                            'check_in'  => $i,
                            'check_out' => $i,
                            'price'     => $price,
                            'status'    => $status,
                        ];
                        self::_insertData( $data );
                    }
                }

                for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {

                }
                echo json_encode( [
                    'type'    => 'success',
                    'status'  => 1,
                    'message' => __( 'Successfully', ST_TEXTDOMAIN )
                ] );
                die();
            }

            public function _add_custom_price_rental()
            {
                global $wpdb;
                $check_in  = STInput::request( 'calendar_check_in', '' );
                $check_out = STInput::request( 'calendar_check_out', '' );
                if ( empty( $check_in ) || empty( $check_out ) ) {
                    echo json_encode( [
                        'type'    => 'error',
                        'status'  => 0,
                        'message' => __( 'The check in or check out field is not empty.', ST_TEXTDOMAIN )
                    ] );
                    die();
                }
                $check_in  = strtotime( $check_in );
                $check_out = strtotime( $check_out );
                if ( $check_in > $check_out ) {
                    echo json_encode( [
                        'type'    => 'error',
                        'status'  => 0,
                        'message' => __( 'The check out is later than the check in field.', ST_TEXTDOMAIN )
                    ] );
                    die();
                }

                $status = STInput::request( 'calendar_status', 'available' );
                if ( $status == 'available' ) {
                    if ( filter_var( $_POST[ 'calendar_price' ], FILTER_VALIDATE_FLOAT ) === false ) {
                        echo json_encode( [
                            'type'    => 'error',
                            'status'  => 0,
                            'message' => __( 'The price field is not a number.', ST_TEXTDOMAIN )
                        ] );
                        die();
                    }
                }
                $price   = floatval( STInput::request( 'calendar_price', '' ) );
                $post_id = STInput::request( 'calendar_post_id', '' );

                for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                    $hotels = self::_getdataRental( $post_id, $i, $i );
                    if ( is_array( $hotels ) && count( $hotels ) ) {
                        foreach ( $hotels as $key => $val ) {
                            if ( $i == $val->check_in && $i == $val->check_out ) {
                                $data  = [
                                    'price'  => $price,
                                    'status' => $status,
                                ];
                                $where = [
                                    'id' => $val->id
                                ];
                                self::_updateData( $where, $data );
                            } else {
                                $data = [
                                    'post_id'   => $post_id,
                                    'post_type' => 'st_rental',
                                    'check_in'  => $i,
                                    'check_out' => $i,
                                    'price'     => $price,
                                    'status'    => $status,
                                ];
                                self::_insertData( $data );
                            }
                        }
                    } else {
                        $data = [
                            'post_id'   => $post_id,
                            'post_type' => 'st_rental',
                            'check_in'  => $i,
                            'check_out' => $i,
                            'price'     => $price,
                            'status'    => $status,
                        ];
                        self::_insertData( $data );
                    }
                }

                for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {

                }
                echo json_encode( [
                    'type'    => 'success',
                    'status'  => 1,
                    'message' => __( 'Successfully', ST_TEXTDOMAIN )
                ] );
                die();
            }

            //Flight
            public function _add_custom_price_flight()
            {
                global $wpdb;
                $check_in  = STInput::request( 'calendar_check_in', '' );
                $check_out = STInput::request( 'calendar_check_out', '' );
                if ( empty( $check_in ) || empty( $check_out ) ) {
                    echo json_encode( [
                        'type'    => 'error',
                        'status'  => 0,
                        'message' => __( 'The check in or check out field is not empty.', ST_TEXTDOMAIN )
                    ] );
                    die();
                }
                $check_in  = strtotime( $check_in );
                $check_out = strtotime( $check_out );
                if ( $check_in > $check_out ) {
                    echo json_encode( [
                        'type'    => 'error',
                        'status'  => 0,
                        'message' => __( 'The check out is later than the check in field.', ST_TEXTDOMAIN )
                    ] );
                    die();
                }

                $price = STInput::request( 'calendar_price', '' );

                $status = STInput::request( 'calendar_status', 'available' );
                if ( $status == 'available' ) {
                    $price = STInput::request( 'calendar_price', '' );
                    if ( filter_var( $price[ 'economy' ], FILTER_VALIDATE_FLOAT ) === false ) {
                        echo json_encode( [
                            'type'    => 'error',
                            'status'  => 0,
                            'message' => __( 'The economy price field is not a number.', ST_TEXTDOMAIN )
                        ] );
                        die();
                    }
                    if ( filter_var( $price[ 'business' ], FILTER_VALIDATE_FLOAT ) === false ) {
                        echo json_encode( [
                            'type'    => 'error',
                            'status'  => 0,
                            'message' => __( 'The business price field is not a number.', ST_TEXTDOMAIN )
                        ] );
                        die();
                    }
                }
                $eco_price      = floatval( $price[ 'economy' ] );
                $business_price = floatval( $price[ 'business' ] );
                $post_id        = STInput::request( 'calendar_post_id', '' );

                for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                    $hotels = self::_getdataFlight( $post_id, $i, $i );
                    if ( is_array( $hotels ) && count( $hotels ) ) {
                        foreach ( $hotels as $key => $val ) {
                            if ( $i == $val->start_date && $i == $val->end_date ) {
                                $data  = [
                                    'eco_price'      => $eco_price,
                                    'business_price' => $business_price,
                                    'status'         => $status,
                                ];
                                $where = [
                                    'id' => $val->id
                                ];
                                self::_updateDataFlight( $where, $data );
                            } else {
                                $data = [
                                    'post_id'        => $post_id,
                                    'start_date'     => $i,
                                    'end_date'       => $i,
                                    'eco_price'      => $eco_price,
                                    'business_price' => $business_price,
                                    'status'         => $status,
                                ];
                                self::_insertDataFlight( $data );
                            }
                        }
                    } else {
                        $data = [
                            'post_id'        => $post_id,
                            'start_date'     => $i,
                            'end_date'       => $i,
                            'eco_price'      => $eco_price,
                            'business_price' => $business_price,
                            'status'         => $status,
                        ];
                        self::_insertDataFlight( $data );
                    }
                }

                for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {

                }
                echo json_encode( [
                    'type'    => 'success',
                    'status'  => 1,
                    'message' => __( 'Successfully', ST_TEXTDOMAIN )
                ] );
                die();
            }

            public function _add_custom_price_tour()
            {
                global $wpdb;
                $check_in  = STInput::request( 'calendar_check_in', '' );
                $check_out = STInput::request( 'calendar_check_out', '' );
                if ( empty( $check_in ) || empty( $check_out ) ) {
                    echo json_encode( [
                        'type'    => 'error',
                        'status'  => 0,
                        'message' => __( 'The check in or check out field is not empty.', ST_TEXTDOMAIN )
                    ] );
                    die();
                }
                $check_in  = strtotime( $check_in );
                $check_out = strtotime( $check_out );
                if ( $check_in > $check_out ) {
                    echo json_encode( [
                        'type'    => 'error',
                        'status'  => 0,
                        'message' => __( 'The check out is later than the check in field.', ST_TEXTDOMAIN )
                    ] );
                    die();
                }

                $status = STInput::request( 'calendar_status', 'available' );
                /*if($status == 'available'){
                if(filter_var(STInput::request('calendar_adult_price', 0), FILTER_VALIDATE_FLOAT) === false){
                    echo json_encode(array(
                            'type' => 'error',
                            'status' => 0,
                            'message' => __('The adult price field is not a number.', ST_TEXTDOMAIN)
                        ));
                    die();
                }

                if(filter_var(STInput::request('calendar_child_price', 0), FILTER_VALIDATE_FLOAT) === false){
                    echo json_encode(array(
                            'type' => 'error',
                            'status' => 0,
                            'message' => __('The children price field is not a number.', ST_TEXTDOMAIN)
                        ));
                    die();
                }

                if(filter_var(STInput::request('calendar_infant_price', 0), FILTER_VALIDATE_FLOAT) === false){
                    echo json_encode(array(
                            'type' => 'error',
                            'status' => 0,
                            'message' => __('The infant price field is not a number.', ST_TEXTDOMAIN)
                        ));
                    die();
                }
            }*/

                $adult_price  = floatval( STInput::request( 'calendar_adult_price', 0 ) );
                $child_price  = floatval( STInput::request( 'calendar_child_price', 0 ) );
                $infant_price = floatval( STInput::request( 'calendar_infant_price', 0 ) );

                if ( $adult_price < 0 ) $adult_price = 0;
                if ( $child_price < 0 ) $child_price = 0;
                if ( $infant_price < 0 ) $infant_price = 0;

                $priority = floatval( STInput::request( 'calendar_priority', '' ) );
                $post_id  = STInput::request( 'calendar_post_id', '' );
                $groupday = STInput::request( 'calendar_groupday', '' );
                for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                    $list_tour = self::_getdataTourEachDate( $post_id, $i, $i );
                    if ( is_array( $list_tour ) && count( $list_tour ) ) {
                        foreach ( $list_tour as $key => $val ) {
                            if ( $i == $val->check_in ) {
                                if ( intval( $val->check_out ) - intval( $val->check_in ) == 0 ) {
                                    self::_deleteData( $val->id );
                                } else {
                                    $group_day = 1;
                                    if ( strtotime( '+1 day', $val->check_in ) == $val->check_out ) {
                                        $group_day = 0;
                                    }
                                    $data  = [
                                        'check_in' => strtotime( '+1 day', $val->check_in ),
                                        'groupday' => $group_day
                                    ];
                                    $where = [
                                        'id' => $val->id
                                    ];

                                    self::_updateData( $where, $data );
                                }

                            } elseif ( $i == $val->check_out && $val->check_out != $val->check_in ) {
                                $group_day = 1;
                                if ( strtotime( '-1 day', $val->check_out ) == $val->check_in ) {
                                    $group_day = 0;
                                }
                                $data = [
                                    'check_out' => strtotime( '-1 day', $val->check_out ),
                                    'groupday'  => $group_day
                                ];

                                $where = [
                                    'id' => $val->id
                                ];

                                self::_updateData( $where, $data );
                            } elseif ( $i > $val->check_in && $i < $val->check_out ) {
                                $group_day = 1;
                                if ( $val->check_in == strtotime( '-1 day', $i ) ) {
                                    $group_day = 0;
                                }
                                $data  = [
                                    'check_out' => strtotime( '-1 day', $i ),
                                    'groupday'  => $group_day
                                ];
                                $where = [
                                    'id' => $val->id
                                ];
                                self::_updateData( $where, $data );

                                $group_day = 1;
                                if ( strtotime( '+1 day', $i ) == $val->check_out ) {
                                    $group_day = 0;
                                }

                                $data = [
                                    'post_id'      => $val->post_id,
                                    'post_type'    => $val->post_type,
                                    'check_in'     => strtotime( '+1 day', $i ),
                                    'check_out'    => $val->check_out,
                                    'adult_price'  => ( (float)$val->adult_price < 0 ) ? 0 : (float)$val->adult_price,
                                    'child_price'  => ( (float)$val->child_price < 0 ) ? 0 : (float)$val->child_price,
                                    'infant_price' => ( (float)$val->infant_price < 0 ) ? 0 : (float)$val->infant_price,
                                    'groupday'     => $group_day,
                                    'status'       => $val->status,
                                ];
                                self::_insertData( $data );
                            }
                        }
                    }
                }
                if ( intval( $groupday ) == 1 ) {
                    $data  = [
                        'post_id'      => $post_id,
                        'post_type'    => 'st_tours',
                        'check_in'     => $check_in,
                        'check_out'    => $check_out,
                        'adult_price'  => $adult_price,
                        'child_price'  => $child_price,
                        'infant_price' => $infant_price,
                        'status'       => $status,
                        'groupday'     => $groupday,
                    ];
                    $table = $wpdb->prefix . 'st_availability';
                    $wpdb->insert( $table, $data );
                    if ( $wpdb->insert_id <= 0 ) {
                        echo json_encode( [
                            'type'    => 'error',
                            'status'  => 0,
                            'message' => $wpdb->show_errors()
                        ] );
                        die();
                    }
                } else {
                    for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                        $data  = [
                            'post_id'      => $post_id,
                            'post_type'    => 'st_tours',
                            'check_in'     => $i,
                            'check_out'    => $i,
                            'adult_price'  => $adult_price,
                            'child_price'  => $child_price,
                            'infant_price' => $infant_price,
                            'status'       => $status,
                            'groupday'     => $groupday,
                        ];
                        $table = $wpdb->prefix . 'st_availability';
                        $wpdb->insert( $table, $data );
                        if ( $wpdb->insert_id <= 0 ) {
                            echo json_encode( [
                                'type'    => 'error',
                                'status'  => 0,
                                'message' => $wpdb->show_errors()
                            ] );
                            die();
                        }
                    }
                }
                echo json_encode( [
                    'type'         => 'success',
                    'status'       => 1,
                    'message'      => __( 'Successfully', ST_TEXTDOMAIN ),
                    'adult_price'  => $adult_price,
                    'child_price'  => $child_price,
                    'infant_price' => $infant_price
                ] );
                die();
            }

            public function _add_custom_price_activity()
            {
                global $wpdb;
                $check_in  = STInput::request( 'calendar_check_in', '' );
                $check_out = STInput::request( 'calendar_check_out', '' );
                if ( empty( $check_in ) || empty( $check_out ) ) {
                    echo json_encode( [
                        'type'    => 'error',
                        'status'  => 0,
                        'message' => __( 'The check in or check out field is not empty.', ST_TEXTDOMAIN )
                    ] );
                    die();
                }
                $check_in  = strtotime( $check_in );
                $check_out = strtotime( $check_out );
                if ( $check_in > $check_out ) {
                    echo json_encode( [
                        'type'    => 'error',
                        'status'  => 0,
                        'message' => __( 'The check out is later than the check in field.', ST_TEXTDOMAIN )
                    ] );
                    die();
                }

                $status = STInput::request( 'calendar_status', 'available' );
                /*if($status == 'available'){
                if(filter_var($_POST['calendar_adult_price'], FILTER_VALIDATE_FLOAT) === false){
                    echo json_encode(array(
                            'type' => 'error',
                            'status' => 0,
                            'message' => __('The adult price field is not a number.', ST_TEXTDOMAIN)
                        ));
                    die();
                }

                if(filter_var($_POST['calendar_child_price'], FILTER_VALIDATE_FLOAT) === false){
                    echo json_encode(array(
                            'type' => 'error',
                            'status' => 0,
                            'message' => __('The children price field is not a number.', ST_TEXTDOMAIN)
                        ));
                    die();
                }

                if(filter_var($_POST['calendar_infant_price'], FILTER_VALIDATE_FLOAT) === false){
                    echo json_encode(array(
                            'type' => 'error',
                            'status' => 0,
                            'message' => __('The infant price field is not a number.', ST_TEXTDOMAIN)
                        ));
                    die();
                }
            }*/

                $adult_price  = floatval( STInput::request( 'calendar_adult_price', '' ) );
                $child_price  = floatval( STInput::request( 'calendar_child_price', '' ) );
                $infant_price = floatval( STInput::request( 'calendar_infant_price', '' ) );
                $priority     = floatval( STInput::request( 'calendar_priority', '' ) );
                $post_id      = STInput::request( 'calendar_post_id', '' );
                $groupday     = STInput::request( 'calendar_groupday', '' );
                for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                    $list_activity = self::_getdataActivityEachDate( $post_id, $i, $i );
                    if ( is_array( $list_activity ) && count( $list_activity ) ) {
                        foreach ( $list_activity as $key => $val ) {
                            if ( $i == $val->check_in ) {
                                if ( intval( $val->check_out ) - intval( $val->check_in ) == 0 ) {
                                    self::_deleteData( $val->id );
                                } else {
                                    $group_day = 1;
                                    if ( strtotime( '+1 day', $val->check_in ) == $val->check_out ) {
                                        $group_day = 0;
                                    }
                                    $data  = [
                                        'check_in' => strtotime( '+1 day', $val->check_in ),
                                        'groupday' => $group_day
                                    ];
                                    $where = [
                                        'id' => $val->id
                                    ];

                                    self::_updateData( $where, $data );
                                }

                            } elseif ( $i == $val->check_out && $val->check_out != $val->check_in ) {
                                $group_day = 1;
                                if ( strtotime( '-1 day', $val->check_out ) == $val->check_in ) {
                                    $group_day = 0;
                                }
                                $data = [
                                    'check_out' => strtotime( '-1 day', $val->check_out ),
                                    'groupday'  => $group_day
                                ];

                                $where = [
                                    'id' => $val->id
                                ];

                                self::_updateData( $where, $data );
                            } elseif ( $i > $val->check_in && $i < $val->check_out ) {
                                $group_day = 1;
                                if ( $val->check_in == strtotime( '-1 day', $i ) ) {
                                    $group_day = 0;
                                }
                                $data  = [
                                    'check_out' => strtotime( '-1 day', $i ),
                                    'groupday'  => $group_day
                                ];
                                $where = [
                                    'id' => $val->id
                                ];
                                self::_updateData( $where, $data );

                                $group_day = 1;
                                if ( strtotime( '+1 day', $i ) == $val->check_out ) {
                                    $group_day = 0;
                                }
                                $data = [
                                    'post_id'      => $val->post_id,
                                    'post_type'    => $val->post_type,
                                    'check_in'     => strtotime( '+1 day', $i ),
                                    'check_out'    => $val->check_out,
                                    'adult_price'  => $val->adult_price,
                                    'child_price'  => $val->child_price,
                                    'infant_price' => $val->infant_price,
                                    'groupday'     => $group_day,
                                    'status'       => $val->status,
                                ];
                                self::_insertData( $data );
                            }
                        }
                    }
                }
                if ( intval( $groupday ) == 1 ) {
                    $data  = [
                        'post_id'      => $post_id,
                        'post_type'    => 'st_activity',
                        'check_in'     => $check_in,
                        'check_out'    => $check_out,
                        'adult_price'  => $adult_price,
                        'child_price'  => $child_price,
                        'infant_price' => $infant_price,
                        'status'       => $status,
                        'groupday'     => $groupday,
                    ];
                    $table = $wpdb->prefix . 'st_availability';
                    $wpdb->insert( $table, $data );
                    if ( $wpdb->insert_id <= 0 ) {
                        echo json_encode( [
                            'type'    => 'error',
                            'status'  => 0,
                            'message' => $wpdb->show_errors()
                        ] );
                        die();
                    }
                } else {
                    for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                        $data  = [
                            'post_id'      => $post_id,
                            'post_type'    => 'st_activity',
                            'check_in'     => $i,
                            'check_out'    => $i,
                            'adult_price'  => $adult_price,
                            'child_price'  => $child_price,
                            'infant_price' => $infant_price,
                            'status'       => $status,
                            'groupday'     => $groupday,
                        ];
                        $table = $wpdb->prefix . 'st_availability';
                        $wpdb->insert( $table, $data );
                        if ( $wpdb->insert_id <= 0 ) {
                            echo json_encode( [
                                'type'    => 'error',
                                'status'  => 0,
                                'message' => $wpdb->show_errors()
                            ] );
                            die();
                        }
                    }
                }
                echo json_encode( [
                    'type'    => 'success',
                    'status'  => 1,
                    'message' => __( 'Successfully', ST_TEXTDOMAIN )
                ] );
                die();
            }

            static function _insertData( $data = [] )
            {
                global $wpdb;
                $table = $wpdb->prefix . 'st_availability';
                $wpdb->insert( $table, $data );
            }

            static function _insertDataFlight( $data = [] )
            {
                global $wpdb;
                $table = $wpdb->prefix . 'st_flight_availability';
                $wpdb->insert( $table, $data );
            }

            static function _updateData( $where = [], $data = [] )
            {
                global $wpdb;
                $table = $wpdb->prefix . 'st_availability';
                $wpdb->update( $table, $data, $where );
            }

            static function _updateDataFlight( $where = [], $data = [] )
            {
                global $wpdb;
                $table = $wpdb->prefix . 'st_flight_availability';
                $wpdb->update( $table, $data, $where );
            }

            static function _deleteData( $id )
            {
                global $wpdb;
                $table = $wpdb->prefix . 'st_availability';
                $where = [
                    'id' => $id
                ];
                $wpdb->delete( $table, $where );
            }

            static function _deleteDataFlight( $id )
            {
                global $wpdb;
                $table = $wpdb->prefix . 'st_flight_availability';
                $where = [
                    'id' => $id
                ];
                $wpdb->delete( $table, $where );
            }

            /**
             * @param $data is array, has ('check_in', 'check_out') object field
             **/
            static function getMinMaxFromData( $data = [] )
            {
                $minmax = [
                    'min' => 0,
                    'max' => 0
                ];
                if ( is_array( $data ) && count( $data ) ) {
                    foreach ( $data as $key => $val ) {
                        if ( $minmax[ 'min' ] == 0 ) $minmax[ 'min' ] = intval( $val->$check_in );
                        if ( $minmax[ 'min' ] > intval( $val->check_in ) ) $minmax[ 'min' ] = intval( $val->check_in );
                        if ( $minmax[ 'min' ] > intval( $val->check_out ) ) $minmax[ 'min' ] = intval( $val->check_out );
                        if ( $minmax[ 'max' ] == 0 ) $minmax[ 'max' ] = intval( $val->$check_in );
                        if ( $minmax[ 'max' ] < intval( $val->check_in ) ) $minmax[ 'max' ] = intval( $val->check_in );
                        if ( $minmax[ 'max' ] < intval( $val->check_out ) ) $minmax[ 'max' ] = intval( $val->check_out );
                    }
                }

                return $minmax;
            }

            /* Available activity by month */
            public static function _get_current_availability_activity( $activity_id, $check_in, $check_out )
            {
                $results = [];
                $today   = strtotime( date( 'Y-m-d' ) );
                if ( get_post_type( $activity_id ) == 'st_activity' ) {
                    $adult_price  = floatval( get_post_meta( $activity_id, 'adult_price', true ) );
                    $child_price  = floatval( get_post_meta( $activity_id, 'child_price', true ) );
                    $infant_price = floatval( get_post_meta( $activity_id, 'infant_price', true ) );

                    if ( $adult_price < 0 ) $adult_price = 0;
                    if ( $child_price < 0 ) $child_price = 0;
                    if ( $infant_price < 0 ) $infant_price = 0;

                    $type_activity  = get_post_meta( $activity_id, 'type_activity', true );
                    $booking_period = intval( get_post_meta( $activity_id, 'activity_booking_period', true ) );
                    $max_people     = intval( get_post_meta( $activity_id, 'max_people', true ) );

                    $data_activity = self::_getdataActivityEachDate( $activity_id, $check_in, $check_out );
                    if ( is_array( $data_activity ) && count( $data_activity ) ) {

                        foreach ( $data_activity as $key => $val ) {
                            if ( $val->status == 'available' ) {
                                $cant_book = [];
                                $cant_book = [];
                                if ( $max_people > 0 ) {
                                    $cant_book = self::_get_activity_cant_order( $activity_id, date( 'm/d/Y', $val->check_in ), $max_people );
                                }
                                $period = STDate::dateDiff( date( 'Y-m-d', $today ), date( 'Y-m-d', $val->check_in ) );

                                if ( intval( $val->groupday ) == 1 ) {
                                    if ( $val->check_in >= $today && count( $cant_book ) <= 0 && $period >= $booking_period ) {
                                        $results[] = [
                                            'start'        => date( 'Y-m-d', $val->check_in ),
                                            'day'          => date( 'd', $val->check_in ),
                                            'date'         => date( 'Y-m-d', $val->check_in ),
                                            'end'          => date( 'Y-m-d', strtotime( '+1 day', $val->check_out ) ),
                                            'date_end'     => date( 'd', $val->check_out ),
                                            'adult_price'  => ( (float)$val->adult_price > 0 ) ? TravelHelper::format_money( $val->adult_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'child_price'  => ( (float)$val->child_price > 0 ) ? TravelHelper::format_money( $val->child_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'infant_price' => ( (float)$val->infant_price > 0 ) ? TravelHelper::format_money( $val->infant_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'status'       => 'available'
                                        ];
                                    }
                                } else {
                                    if ( $val->check_in >= $today && count( $cant_book ) <= 0 && $period >= $booking_period ) {
                                        $results[] = [
                                            'start'        => date( 'Y-m-d', $val->check_in ),
                                            'day'          => date( 'd', $val->check_in ),
                                            'date'         => date( 'Y-m-d', $val->check_in ),
                                            'adult_price'  => ( (float)$val->adult_price > 0 ) ? TravelHelper::format_money( $val->adult_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'child_price'  => ( (float)$val->child_price > 0 ) ? TravelHelper::format_money( $val->child_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'infant_price' => ( (float)$val->infant_price > 0 ) ? TravelHelper::format_money( $val->infant_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'status'       => 'available'
                                        ];
                                    }
                                }
                            }
                        }

                        if ( $type_activity == 'daily_activity' ) {

                            for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                                $in_item = false;
                                $status  = 'available';
                                foreach ( $data_activity as $key => $val ) {
                                    if ( $i >= $val->check_in && $i <= $val->check_out ) {
                                        $in_item = true;
                                        $status  = $val->status;
                                        break;
                                    }
                                }

                                if ( !$in_item ) {
                                    $cant_book = [];
                                    if ( $max_people > 0 ) {
                                        $cant_book = self::_get_activity_cant_order( $activity_id, date( 'm/d/Y', $i ), $max_people );
                                    }
                                    $period = STDate::dateDiff( date( 'Y-m-d', $today ), date( 'Y-m-d', $i ) );
                                    if ( $i >= $today && count( $cant_book ) <= 0 && $period >= $booking_period ) {
                                        $results[] = [
                                            'start'        => date( 'Y-m-d', $i ),
                                            'day'          => date( 'd', $i ),
                                            'date'         => date( 'Y-m-d', $i ),
                                            'adult_price'  => ( $adult_price ) ? TravelHelper::format_money( $adult_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'child_price'  => ( $child_price ) ? TravelHelper::format_money( $child_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'infant_price' => ( $infant_price ) ? TravelHelper::format_money( $infant_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'status'       => 'available'
                                        ];
                                    }

                                }
                            }
                        }

                    } else {
                        if ( $type_activity == 'daily_activity' ) {
                            for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                                $cant_book = [];
                                if ( $max_people > 0 ) {
                                    $cant_book = self::_get_activity_cant_order( $activity_id, date( 'm/d/Y', $i ), $max_people );
                                }
                                $period = STDate::dateDiff( date( 'Y-m-d', $today ), date( 'Y-m-d', $i ) );
                                if ( $i >= $today && count( $cant_book ) <= 0 && $period >= $booking_period ) {
                                    $results[] = [
                                        'title'        => get_the_title( $activity_id ),
                                        'start'        => date( 'Y-m-d', $i ),
                                        'day'          => date( 'd', $i ),
                                        'date'         => date( 'Y-m-d', $i ),
                                        'adult_price'  => ( $adult_price ) ? TravelHelper::format_money( $adult_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                        'child_price'  => ( $child_price ) ? TravelHelper::format_money( $child_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                        'infant_price' => ( $infant_price ) ? TravelHelper::format_money( $infant_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                        'status'       => 'available'
                                    ];
                                }
                            }
                        }
                    }
                }
                $st_tour_available = $results;
                $return            = [];
                if ( count( $results ) ) {
                    for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                        $in_date = false;
                        foreach ( $results as $key => $val ) {
                            $start = strtotime( $val[ 'start' ] );
                            $end   = ( isset( $val[ 'end' ] ) ) ? strtotime( '-1 day', strtotime( $val[ 'end' ] ) ) : strtotime( $val[ 'start' ] );
                            if ( $i >= $start && $i <= $end ) {
                                $in_date = true;
                                break;
                            }
                        }

                        if ( !$in_date ) {
                            $return[] = [
                                'start'  => date( 'Y-m-d', $i ),
                                'day'    => date( 'd', $i ),
                                'date'   => date( 'Y-m-d', $i ),
                                'status' => 'not_available'
                            ];
                        }
                    }

                    foreach ( $results as $key => $val ) {
                        $return[] = $val;
                    }
                } else {
                    for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                        $return[] = [
                            'start'  => date( 'Y-m-d', $i ),
                            'day'    => date( 'd', $i ),
                            'date'   => date( 'Y-m-d', $i ),
                            'status' => 'not_available'
                        ];
                    }
                }

                return $return;
            }

            public static function _get_current_availability_tour( $tour_id, $check_in, $check_out )
            {
                $results = [];
                $today   = strtotime( date( 'Y-m-d' ) );

                if ( get_post_type( $tour_id ) == 'st_tours' ) {
                    $adult_price  = floatval( get_post_meta( $tour_id, 'adult_price', true ) );
                    $child_price  = floatval( get_post_meta( $tour_id, 'child_price', true ) );
                    $infant_price = floatval( get_post_meta( $tour_id, 'infant_price', true ) );

                    if ( $adult_price < 0 ) $adult_price = 0;
                    if ( $child_price < 0 ) $child_price = 0;
                    if ( $infant_price < 0 ) $infant_price = 0;

                    $type_tour      = get_post_meta( $tour_id, 'type_tour', true );
                    $booking_period = intval( get_post_meta( $tour_id, 'tours_booking_period', true ) );
                    $max_people     = intval( get_post_meta( $tour_id, 'max_people', true ) );

                    $data_tour = self::_getdataTourEachDate( $tour_id, $check_in, $check_out );

                    if ( is_array( $data_tour ) && count( $data_tour ) ) {

                        foreach ( $data_tour as $key => $val ) {
                            if ( $val->status == 'available' ) {
                                /**
                                 * @updated 1.2.8
                                 **/
                                $cant_book = [];
                                if ( $max_people > 0 ) {
                                    //$cant_book = self::_get_tour_cant_order($tour_id, date('m/d/Y', $val->check_in), $max_people);
                                    if ( $type_tour == 'daily_tour' ) {
                                        $check_out_group  = $val->check_in;
                                        $starttime_string = self::_get_starttime_by_date( $tour_id, $check_out_group, $check_out_group );
                                    } else {
                                        //$check_out_group = $val->check_in;
                                        $check_out_group  = self::_get_check_out_by_groupday( $tour_id, $val->check_in );
                                        $starttime_string = self::_get_starttime_by_date( $tour_id, $val->check_in, $val->check_in );
                                    }

                                    $starttime_arr = explode( ', ', $starttime_string );

                                    $sub_total = 0;
                                    foreach ( $starttime_arr as $starttime_item ) {
                                        if ( $type_tour == 'daily_tour' ) {
                                            $test = self::_get_free_peple_by_time( $tour_id, $check_out_group, $check_out_group, $starttime_item );
                                        } else {
                                            $test = self::_get_free_peple_by_time( $tour_id, $val->check_in, $check_out_group, $starttime_item );
                                        }

                                        if ( !empty( $test ) ) {
                                            $free_people = intval( $test[ 'free_people' ] );
                                            $sub_total   = $sub_total + $free_people;
                                        } else {
                                            $free_people = $max_people;
                                            $sub_total   = $sub_total + $free_people;
                                        }
                                    }
                                } else {
                                    $sub_total = 2;
                                }

                                $period = STDate::dateDiff( date( 'Y-m-d', $today ), date( 'Y-m-d', $val->check_in ) );
                                if ( intval( $val->groupday ) == 1 ) {
                                    if ( $max_people > 0 ) {
                                        /*if ($type_tour == 'daily_tour') {
                                        $check_out_group = $val->check_in;
                                        $starttime_string = self::_get_starttime_by_date($tour_id, $check_out_group, $check_out_group);
                                    }else{*/
                                        $check_out_group  = self::_get_check_out_by_groupday( $tour_id, $val->check_in );
                                        $starttime_string = self::_get_starttime_by_date( $tour_id, $val->check_in, $val->check_in );
                                        /*}*/

                                        ////Get starttime of checkout day

                                        $starttime_arr = explode( ', ', $starttime_string );

                                        $sub_total = 0;
                                        foreach ( $starttime_arr as $starttime_item ) {
                                            $test1 = self::_get_free_peple_by_time_groupday( $tour_id, $val->check_in, $check_out_group, $starttime_item );

                                            if ( !empty( $test1 ) ) {
                                                $free_people = intval( $test1[ 'free_people' ] );
                                                $sub_total   = $sub_total + $free_people;
                                            } else {
                                                $free_people = $max_people;
                                                $sub_total   = $sub_total + $free_people;
                                            }
                                        }
                                    } else {
                                        $sub_total = 2;
                                    }

                                    if ( $val->check_in >= $today && $sub_total > 0 && $period >= $booking_period ) {

                                        $results[] = [
                                            'start'        => date( 'Y-m-d', $val->check_in ),
                                            'day'          => date( 'd', $val->check_in ),
                                            'date'         => date( 'Y-m-d', $val->check_in ),
                                            'end'          => date( 'Y-m-d', strtotime( '+1 day', $val->check_out ) ),
                                            'date_end'     => date( 'd', $val->check_out ),
                                            'adult_price'  => ( (float)$val->adult_price > 0 ) ? TravelHelper::format_money( $val->adult_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'child_price'  => ( (float)$val->child_price > 0 ) ? TravelHelper::format_money( $val->child_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'infant_price' => ( (float)$val->infant_price > 0 ) ? TravelHelper::format_money( $val->infant_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'status'       => 'available'
                                        ];
                                    }
                                } else {
                                    if ( $val->check_in >= $today && $sub_total > 0 && $period >= $booking_period ) {
                                        $results[] = [
                                            'start'        => date( 'Y-m-d', $val->check_in ),
                                            'day'          => date( 'd', $val->check_in ),
                                            'date'         => date( 'Y-m-d', $val->check_in ),
                                            'adult_price'  => ( (float)$val->adult_price > 0 ) ? TravelHelper::format_money( $val->adult_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'child_price'  => ( (float)$val->child_price > 0 ) ? TravelHelper::format_money( $val->child_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'infant_price' => ( (float)$val->infant_price > 0 ) ? TravelHelper::format_money( $val->infant_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'status'       => 'available'
                                        ];
                                    }
                                }
                            }
                        }

                        if ( $type_tour == 'daily_tour' ) {

                            for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                                $in_item = false;
                                $status  = 'available';
                                foreach ( $data_tour as $key => $val ) {
                                    if ( $i >= $val->check_in && $i <= $val->check_out ) {
                                        $in_item = true;
                                        $status  = $val->status;
                                        break;
                                    }
                                }

                                if ( !$in_item ) {

                                    /**
                                     * @updated 1.2.8
                                     **/
                                    $cant_book = [];
                                    if ( $max_people > 0 ) {
                                        $cant_book = self::_get_tour_cant_order( $tour_id, date( 'm/d/Y', $i ), $max_people );

                                        $check_out_group = $i;

                                        $starttime_string = self::_get_starttime_by_date( $tour_id, $check_out_group, $check_out_group );
                                        $starttime_arr    = explode( ', ', $starttime_string );

                                        $sub_total = 0;
                                        foreach ( $starttime_arr as $starttime_item ) {
                                            $test = self::_get_free_peple_by_time( $tour_id, $check_out_group, $check_out_group, $starttime_item );

                                            if ( !empty( $test ) ) {
                                                $free_people = intval( $test[ 'free_people' ] );
                                                $sub_total   = $sub_total + $free_people;
                                            } else {
                                                $free_people = $max_people;
                                                $sub_total   = $sub_total + $free_people;
                                            }
                                        }
                                    } else {
                                        $sub_total = 2;
                                    }

                                    $period = STDate::dateDiff( date( 'Y-m-d', $today ), date( 'Y-m-d', $i ) );
                                    if ( $i >= $today && $sub_total > 0 && $period >= $booking_period ) {
                                        $results[] = [
                                            'start'        => date( 'Y-m-d', $i ),
                                            'day'          => date( 'd', $i ),
                                            'date'         => date( 'Y-m-d', $i ),
                                            'adult_price'  => ( $adult_price ) ? TravelHelper::format_money( $adult_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'child_price'  => ( $child_price ) ? TravelHelper::format_money( $child_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'infant_price' => ( $infant_price ) ? TravelHelper::format_money( $infant_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                            'status'       => 'available'
                                        ];
                                    }

                                }
                            }
                        }

                    } else {
                        //DAILY TOUR CHECK
                        if ( $type_tour == 'daily_tour' ) {
                            for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                                /**
                                 * @updated 1.2.8
                                 **/
                                $cant_book = [];
                                if ( $max_people > 0 ) {
                                    $cant_book = self::_get_tour_cant_order( $tour_id, date( 'm/d/Y', $i ), $max_people );


                                    $check_out_group = $i;

                                    $starttime_string = self::_get_starttime_by_date( $tour_id, $check_out_group, $check_out_group );
                                    $starttime_arr    = explode( ', ', $starttime_string );

                                    $sub_total = 0;
                                    foreach ( $starttime_arr as $starttime_item ) {
                                        $test = self::_get_free_peple_by_time( $tour_id, $check_out_group, $check_out_group, $starttime_item );

                                        if ( !empty( $test ) ) {
                                            $free_people = intval( $test[ 'free_people' ] );
                                            $sub_total   = $sub_total + $free_people;
                                        } else {
                                            $free_people = $max_people;
                                            $sub_total   = $sub_total + $free_people;
                                        }
                                    }
                                } else {
                                    $sub_total = 2;
                                }

                                $period = STDate::dateDiff( date( 'Y-m-d', $today ), date( 'Y-m-d', $i ) );
                                if ( $i >= $today && $sub_total > 0 && $period >= $booking_period ) {
                                    $results[] = [
                                        'title'        => get_the_title( $tour_id ),
                                        'start'        => date( 'Y-m-d', $i ),
                                        'day'          => date( 'd', $i ),
                                        'date'         => date( 'Y-m-d', $i ),
                                        'adult_price'  => ( $adult_price ) ? TravelHelper::format_money( $adult_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                        'child_price'  => ( $child_price ) ? TravelHelper::format_money( $child_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                        'infant_price' => ( $infant_price ) ? TravelHelper::format_money( $infant_price ) : __( 'Free', ST_TEXTDOMAIN ),
                                        'status'       => 'available'
                                    ];
                                }
                            }
                        }
                    }
                }//End check type is st_tours

                $st_tour_available = $results;
                $return            = [];
                if ( count( $results ) ) {
                    for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                        $in_date = false;
                        foreach ( $results as $key => $val ) {
                            $start = strtotime( $val[ 'start' ] );
                            $end   = ( isset( $val[ 'end' ] ) ) ? strtotime( '-1 day', strtotime( $val[ 'end' ] ) ) : strtotime( $val[ 'start' ] );
                            if ( $i >= $start && $i <= $end ) {
                                $in_date = true;
                                break;
                            }
                        }

                        if ( !$in_date ) {

                            //Check pediod date
                            $return[] = [
                                'start'  => date( 'Y-m-d', $i ),
                                'day'    => date( 'd', $i ),
                                'date'   => date( 'Y-m-d', $i ),
                                'status' => 'not_available'
                            ];
                        }
                    }

                    foreach ( $results as $key => $val ) {
                        $return[] = $val;
                    }
                } else {
                    for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
                        $return[] = [
                            'start'  => date( 'Y-m-d', $i ),
                            'day'    => date( 'd', $i ),
                            'date'   => date( 'Y-m-d', $i ),
                            'status' => 'not_available'
                        ];
                    }
                }

                return $return;
            }
        }

        new AvailabilityHelper();
    }