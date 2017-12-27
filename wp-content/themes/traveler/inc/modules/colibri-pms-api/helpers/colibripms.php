<?php

class Colibri_Helper
{
    public static function init()
    {
        add_action('wp_ajax_cl_get_city_code', array(__CLASS__, 'cl_get_city_code'));
        add_action('wp_ajax_nopriv_cl_get_city_code', array(__CLASS__, 'cl_get_city_code'));

        add_action('wp_ajax_cl_load_city_code', array(__CLASS__, 'cl_load_city_code'));
        add_action('wp_ajax_nopriv_cl_load_city_code', array(__CLASS__, 'cl_load_city_code'));

        add_action('wp_ajax_cl_get_list_room_ajax', array(__CLASS__, 'cl_get_list_room_ajax'));
        add_action('wp_ajax_nopriv_cl_get_list_room_ajax', array(__CLASS__, 'cl_get_list_room_ajax'));

        add_action('wp_footer', array(__CLASS__, 'cl_modal_checkout'));
        add_action('wp_footer', array(__CLASS__, 'cl_modal_detail_room'));
        add_action('wp_footer', array(__CLASS__, 'cl_modal_modify_booking'));

        add_action('wp_ajax_cl_check_out_submit', array(__CLASS__, 'cl_check_out_submit'));
        add_action('wp_ajax_nopriv_cl_check_out_submit', array(__CLASS__, 'cl_check_out_submit'));

        add_action('wp_ajax_cl_check_out_submit_modify', array(__CLASS__, 'cl_check_out_submit_modify'));
        add_action('wp_ajax_nopriv_cl_check_out_submit_modify', array(__CLASS__, 'cl_check_out_submit_modify'));

        add_action('wp_ajax_cl_get_detail_room', array(__CLASS__, 'cl_get_detail_room'));
        add_action('wp_ajax_nopriv_cl_get_detail_room', array(__CLASS__, 'cl_get_detail_room'));

        add_action('wp_ajax_cl_load_more_history', array(__CLASS__, 'cl_load_more_history'));
        add_action('wp_ajax_nopriv_cl_load_more_history', array(__CLASS__, 'cl_load_more_history'));

        add_action('wp_ajax_cl_cancel_reservation', array(__CLASS__, 'cl_cancel_reservation'));
        add_action('wp_ajax_nopriv_cl_cancel_reservation', array(__CLASS__, 'cl_cancel_reservation'));

        add_action('wp_ajax_cl_remove_reservation', array(__CLASS__, 'cl_remove_reservation'));
        add_action('wp_ajax_nopriv_cl_remove_reservation', array(__CLASS__, 'cl_remove_reservation'));

        add_action('wp_ajax_cl_modify_reservation', array(__CLASS__, 'cl_modify_reservation'));
        add_action('wp_ajax_nopriv_cl_modify_reservation', array(__CLASS__, 'cl_modify_reservation'));

        add_action('wp_ajax_cl_modify_reservation_load_cond', array(__CLASS__, 'cl_modify_reservation_load_cond'));
        add_action('wp_ajax_nopriv_cl_modify_reservation_load_cond', array(__CLASS__, 'cl_modify_reservation_load_cond'));

        add_action('wp_ajax_cl_ad_get_detail_booking', array(__CLASS__, 'cl_ad_get_detail_booking'));
        add_action('wp_ajax_nopriv_cl_ad_get_detail_booking', array(__CLASS__, 'cl_ad_get_detail_booking'));

        add_action('wp_ajax_cl_get_rates_condition_detail', array(__CLASS__, 'cl_get_rates_condition_detail'));
        add_action('wp_ajax_nopriv_cl_get_rates_condition_detail', array(__CLASS__, 'cl_get_rates_condition_detail'));

        add_action('st_menu_template_user', array(__CLASS__, 'cl_cba_add_menu_booking_history'), 10, 2);
        add_filter('st_menu_link_page', array(__CLASS__, 'cl_menu_link_page'), 10, 1);

        add_action('admin_menu', [__CLASS__, 'cba_statistic_menu']);
    }

    public static function cl_get_rates_condition_detail()
    {
        $data = $_POST;
        extract($data);
        $res['status'] = true;
        $res['data'] = st_cba_load_view('room/room', 'condition', array('hotel_code' => $hotelCode, 'start' => $start, 'end' => $end, 'rate_code' => $rate));

        echo json_encode($res);
        die;
    }

    public static function cl_ad_get_detail_booking()
    {
        $min_max = STInput::post('min_max', '');
        $id = STInput::post('ids', '');
        $min_max_arr = explode(',', $min_max);

        $cba_statistic = ST_CBA_Statistic_Models::inst();
        $data = $cba_statistic->get_data_by_date_and_id($id, $min_max_arr[0], $min_max_arr[1]);

        $res['status'] = true;
        $res['data'] = st_cba_load_view('admin-statistic/detail-booking', false, array('data' => $data));

        echo json_encode($res);
        die;
    }

    public static function cl_modify_reservation_load_cond()
    {
        $start = STInput::post('start', '');
        $end = STInput::post('end', '');
        $hotel_code = STInput::post('hotel_code', '');
        $room_code = STInput::post('room_code', '');
        $room_rates = STInput::post('room_rates', '');

        if (strtotime(TravelHelper::convertDateFormat($end)) < strtotime(TravelHelper::convertDateFormat($start))) {
            $data['status'] = false;
            $data['message'] = __('Arrival and Departure Date is invalid. Please select again!', ST_TEXTDOMAIN);
            echo json_encode($data);
            die;
        }

        $arr = [
            'start' => $start,
            'end' => $end,
            'hotel_code' => $hotel_code,
            'room_code' => $room_code,
            'room_rates' => $room_rates
        ];

        $data['status'] = true;
        $data['message'] = st_cba_load_view('checkout/checkout-modify', 'cond', array('arr' => $arr));
        echo json_encode($data);
        die;
    }

    public function load_admin_scripts()
    {
        wp_enqueue_style('jbs-admin-booking-service-css', JBS_PLUGIN_URL . 'assets/css/main.css');
        wp_enqueue_script('jbs-admin-booking-service-js', JBS_PLUGIN_URL . 'assets/js/scripts.js');
    }

    public static function cba_statistic_menu()
    {
        $enable_cba = st()->get_option('cba_enable', 'off');
        if($enable_cba == 'on') {
	        if ( current_user_can( 'manage_options' ) && class_exists( 'STTravelCode' ) ) {

		        add_menu_page(
			        __( 'CPMS Statistic', ST_TEXTDOMAIN ),
			        __( 'CPMS Statistic', ST_TEXTDOMAIN ),
			        'manage_options',
			        'cba-statistic-menu',
			        [
				        __CLASS__,
				        'cba_ver_callback_statistic_function'
			        ]
			        ,
			        'dashicons-feedback',
			        35
		        );
	        }
        }
    }

    /**
     * Calendar vertical
     */
    public static function cba_ver_callback_statistic_function()
    {
        echo st_cba_load_view('admin-statistic/calendar', 'vertical');
    }


    public static function cba_callback_statistic_function()
    {
        echo '<div class="wrap">';
        echo '<h1>' . __('Colibri PMS Statistic', ST_TEXTDOMAIN) . '</h1>';
        ?>
        <div class="nav-calendar">
            <?php
            if (isset($_REQUEST['month']) && $_REQUEST['month'] != '' && $_REQUEST['month'] >= 1 && $_REQUEST['month'] <= 12) {
                $cmonth = $_REQUEST['month'];
            } else {
                $cmonth = date('n');
            }
            if (isset($_REQUEST['year']) && $_REQUEST['year'] != '') {
                $cyear = $_REQUEST['year'];
            } else {
                $cyear = date('Y');
            }
            ?>
            <a href="" id="jbs-calendar-prev" data-month="<?php echo $cmonth; ?>" data-year="<?php echo $cyear; ?>">&larr;
                Previous</a>
            <a href="" id="jbs-calendar-next" data-month="<?php echo $cmonth; ?>" data-year="<?php echo $cyear; ?>">Next
                &rarr;</a>

            <form method="GET">
                <input type="hidden" name="page" value="cba-statistic-menu"/>
                <span class="">Month: </span>
                <input name="month" type="number" min="1" max="12" value="<?php echo $cmonth; ?>">
                <span class="">Year: </span>
                <input name="year" type="number" min="<?php echo date('Y'); ?>" value="<?php echo $cyear; ?>">
                <button>Go to</button>
            </form>

            <?php
            /*$args = array(
                'post_type' => JBS()->booking->post_type_name,
                'showposts' => '-1',
                'meta_query' => array(
                    array(
                        'key' => 'jbs_booking_info_month',
                        'value' => intval($cmonth)
                    )
                )
            );*/

            //$calendar_query = new WP_Query($args);

            //$arrDay = array();

            /*if ($calendar_query->have_posts()){
                while ($calendar_query->have_posts()){
                    $calendar_query->the_post();
                    if(!in_array(get_post_meta(get_the_ID(), 'jbs_booking_info_day', true), $arrDay)){
                        array_push($arrDay, get_post_meta(get_the_ID(), 'jbs_booking_info_day', true));
                    }
                }
                wp_reset_postdata();
            }*/

            ?>

            <div class="currentcalendar">
                <?php echo $cmonth; ?>/<?php echo $cyear; ?>
                <?php //echo '<small>(' . $calendar_query->post_count . ' lịch hẹn)</small>';
                ?>
            </div>


        </div>
        <?php

        $view = isset($_REQUEST['view']) ? $_REQUEST['view'] : 'month';
        $view_file = get_template_directory() . '/inc/modules/colibri-pms-api/views/admin-statistic/canlendar.php';

        $args = array();

        switch ($view) {
            case 'month':
                $month = isset($_REQUEST['month']) ? absint($_REQUEST['month']) : date('n');
                $year = isset($_REQUEST['year']) ? absint($_REQUEST['year']) : date('Y');

                $start_of_week = absint(get_option('start_of_week', 1));
                $first_day_of_current_month = date('N', strtotime("$year-$month-01"));

                $diff = $start_of_week - $first_day_of_current_month;

                $start_timestamp = strtotime($diff . ' days midnight', strtotime("$year-$month-01"));
                $end_timestamp = strtotime('+34 days midnight', $start_timestamp);

                $last_day_of_month = strtotime('+1 month -1 day', strtotime("$year-$month-01"));
                if ($end_timestamp < $last_day_of_month) {
                    $end_timestamp = strtotime('+7 days', $end_timestamp);
                }

//                $bookings = YITH_WCBK_Booking_Helper()->get_bookings_in_time_range( $start_timestamp, $end_timestamp );
                $args = array(
                    'month' => $month,
                    'year' => $year,
                    'start_timestamp' => $start_timestamp,
                    'end_timestamp' => $end_timestamp,
                );


                break;
        }

        echo st_cba_load_view('admin-statistic/calendar', false, array('args' => $args));
        //extract($args);
        //if (file_exists($view_file)) {
        //	include($view_file);
        //}

        echo "</div>";
    }

    public static function cl_modify_reservation()
    {
        $stas_id = STInput::post('stas_id', '');
        $user_id = get_current_user_id();

        $result = [];

        $cba_statistic = ST_CBA_Statistic_Models::inst();
        if ($cba_statistic->check_data_for_user($stas_id, $user_id) > 0) {
            $data = $cba_statistic->get_data($stas_id);

            $result['status'] = 'success';
            $result['data'] = st_cba_load_view('checkout/checkout-modify', false, array('data' => $data));
        } else {
            $result['status'] = 'danger';
            $result['data'] = __('Have an errors!.', ST_TEXTDOMAIN);
        }
        echo json_encode($result);
        die;
    }

    public static function cl_render_data_booking_history($data)
    {
        echo st_cba_load_view('booking-history/list', false, array('data' => $data));
    }

    public static function cl_load_more_history()
    {
        $paged = STInput::post('paged', '');
        $post_per_page = STInput::post('post_per_page', '');
        $stt = STInput::post('stt', '');
        $user_id = get_current_user_id();

        $data = self::cl_get_data_booking_history_by_user_id($user_id, 5 * $paged, 5);

        $res = [];
        $res['data'] = st_cba_load_view('booking-history/list', false, array('data' => $data, 'stt' => $stt));
        echo json_encode($res);
        die;
    }

    public static function cl_get_booking_history()
    {
        $user_id = get_current_user_id();
        $data = self::cl_get_data_booking_history_by_user_id($user_id, 0, 5);

        return $data;
    }

    public static function cl_count_booking_history($user_id)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'st_cba_statistic';
        $sql = "SELECT COUNT(*) FROM {$table} WHERE user_id={$user_id}";

        return $wpdb->get_var($sql);
    }

    public static function cl_get_data_booking_history_by_user_id($user_id, $offset, $number)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'st_cba_statistic';
        $sql = "SELECT * FROM {$table} WHERE user_id={$user_id} ORDER BY id DESC LIMIT {$offset},{$number}";

        return $wpdb->get_results($sql);
    }

    public static function cl_menu_link_page($arr_page_menu)
    {
        array_push($arr_page_menu, 'cba-booking-history');

        return $arr_page_menu;
    }

    public static function cl_cba_add_menu_booking_history($sc, $user_link)
    {
        $enable_cba = st()->get_option('cba_enable', 'off');
        if($enable_cba == 'on') {
	        ?>
            <li class="item <?php if ( $sc == 'cba-booking-history' )
		        echo 'active' ?>">
                <a href="<?php echo TravelHelper::get_user_dashboared_link( $user_link, 'cba-booking-history' ); ?>"><i
                            class="fa fa-clock-o"></i><?php echo __( '[CPMS] Booking history', ST_TEXTDOMAIN ); ?>
                </a>
            </li>
	        <?php
        }
    }

    public static function cl_modal_modify_booking()
    {
        ?>
        <!-- Modal -->
        <div id="modalCBAModifyBooking" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?php echo __('Modify Booking', ST_TEXTDOMAIN); ?></h4>
                    </div>
                    <div class="modal-body">
                        <div class="overlay-form"><i class="fa fa-refresh text-color"></i></div>
                        <div id="cba-modify-booking-detail"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
        <?php
    }

    public static function cl_modal_detail_room()
    {
        ?>
        <!-- Modal -->
        <div id="modalCBARoomDetail" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?php echo __('Room detail', ST_TEXTDOMAIN); ?></h4>
                    </div>
                    <div class="modal-body">
                        <div class="overlay-form"><i class="fa fa-refresh text-color"></i></div>
                        <div id="cba-room-detail"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
        <?php
    }

    public static function cl_modal_checkout()
    {
        ?>
        <!-- Modal -->
        <div id="modalCBACheckOut" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?php echo __('Booking Submission', ST_TEXTDOMAIN); ?></h4>
                    </div>
                    <div class="modal-body">
                        <div class="overlay-form"><i class="fa fa-refresh text-color"></i></div>
                        <!--<div class="row">-->
                        <!--<div class="col-lg-8">-->
                        <?php
                        echo st_cba_load_view('checkout/form', false);
                        ?>
                        <!--</div>-->
                        <!--<div class="col-lg-4">-->
                        <?php
                        //echo st_cba_load_view('checkout/checkout-item-modal', false);
                        ?>
                        <!--</div>-->
                        <!--</div>-->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
        <?php
    }

    public static function render_order_by()
    {
        $order_by = STInput::get('orderby', 'ID');
        $order_arr = [];
        switch ($order_by) {
            case 'price_asc':
                $order_arr['field'] = 'min_price';
                $order_arr['sort'] = 'desc';
                break;
            case 'price_desc':
                $order_arr['field'] = 'min_price';
                $order_arr['sort'] = 'asc';
                break;
            case 'name_asc':
                $order_arr['field'] = 'hotel_name';
                $order_arr['sort'] = 'asc';
                break;
            case 'name_desc':
                $order_arr['field'] = 'hotel_name';
                $order_arr['sort'] = 'desc';
                break;
            default:

                break;
        }

        return $order_arr;
    }

    public static function order_cmp($a, $b, $field, $sort)
    {
        if ($a[$field] == $b[$field]) {
            return 0;
        }
        if ($sort == 'asc') {
            return strcmp($a[$field], $b[$field]);
        } else {
            return $a[$field] < $b[$field] ? 1 : -1;
        }
    }

    public static function pag($total, $post_per_page, $paged, $num_of_page)
    {
        $link = st_get_link_with_search(get_permalink(), array(
            'start',
            'end',
            'style',
            'orderby',
            'price_range',
            'amenity',
            'country',
            'city_code'
        ), $_GET);
        if ($total > $post_per_page) {
            echo '<ul class="col-xs-12 pagination 1_pag">';
            if ($paged > 1) {
                if ($num_of_page > 2) {
                    echo '<li><a href="' . $link . '">&#10094;&#10094;</a></li>';
                }
                echo '<li><a href="' . $link . '&cpage=' . ($paged - 1) . '">&#10094;</a></li>';
            }
            for ($i = 0; $i < $num_of_page; $i++) {
                if ($paged == ($i + 1)) {
                    echo '<li><a class="current">' . ($i + 1) . '</a></li>';
                } else {
                    if ($i == 0) {
                        echo '<li><a href="' . $link . '&cpage=' . ($i + 1) . '">' . ($i + 1) . '</a></li>';
                    }
                    if ($i == ($num_of_page - 1)) {
                        echo '<li><a href="' . $link . '&cpage=' . ($i + 1) . '">' . ($i + 1) . '</a></li>';
                    }
                    if ($i == ($paged) && $i != $num_of_page - 1) {
                        echo '<li><a href="' . $link . '&cpage=' . ($i + 1) . '">' . ($i + 1) . '</a></li>';
                    }
                    if ($i == ($paged + 1) && $i != $num_of_page - 1) {
                        echo '<li><a>...</a></li>';
                    }
                    if ($i == ($paged - 2) && $i != 0) {
                        echo '<li><a href="' . $link . '&cpage=' . ($i + 1) . '">' . ($i + 1) . '</a></li>';
                    }
                    if ($i == ($paged - 3) && $i != 0) {
                        echo '<li><a>...</a></li>';
                    }
                }
            }
            if ($paged < $num_of_page) {
                echo '<li><a href="' . $link . '&cpage=' . ($paged + 1) . '">&#10095;</a></li>';
                if ($num_of_page > 2) {
                    echo '<li><a href="' . $link . '&cpage=' . $num_of_page . '">&#10095;&#10095;</a></li>';
                }
            }
            echo '</ul>';
        }
    }

    public static function get_results_string()
    {
        global $cldt;
        $string = '';
        if (empty($cldt)) {
            $string = __('No hotel found', ST_TEXTDOMAIN);
        } else {
            $string = $cldt->found_posts . ' ' . __('hotels.', ST_TEXTDOMAIN);
        }

        return $string;
    }

    public static function get_data_hac()
    {
        $theme_dir = get_template_directory();
        $utf_string = file_get_contents($theme_dir . '/inc/modules/colibri-pms-api/files/hac.txt');
        $data = iconv("UTF-16", "UTF-8//TRANSLIT", $utf_string);
        $arr_data = explode("\n", $data);
        $arr = array();
        foreach ($arr_data as $key => $val) {
            $sub_arr = explode("\t", $val);
            array_push($arr, array('id' => $sub_arr[0], 'title' => $sub_arr[1]));
        }

        return $arr;
    }

    static function rate_to_string($star, $max = 5)
    {
        $html = '';

        if ($star > $max) {
            $star = $max;
        }

        $moc1 = (int)$star;

        for ($i = 1; $i <= $moc1; $i++) {
            $html .= '<li><i class="fa  fa-star"></i></li>';
        }

        $new = $max - $star;

        $du = round((float)$star - $moc1, 1);

        if ($du >= 0.2 and $du <= 0.9) {
            $html .= '<li><i class="fa  fa-star-half-o"></i></li>';
        } elseif ($du) {
            $html .= '<li><i class="fa  fa-star-o"></i></li>';
        }

        for ($i = 1; $i <= $new; $i++) {
            $html .= '<li><i class="fa  fa-star-o"></i></li>';
        }

        return apply_filters('st_rate_to_string', $html);

    }

    public static function cl_parse_aqc_code($aqc_code, $list = false)
    {
        $aqc_arr = [
            '1' => ['text' => 'Over 21', 'icon' => 'fa fa-male'],
            '2' => ['text' => 'Over 65', 'icon' => 'fa fa-male'],
            '3' => ['text' => 'Under 2', 'icon' => 'im im-children'],
            '4' => ['text' => 'Under 12', 'icon' => 'fa fa-male'],
            '5' => ['text' => 'Under 17', 'icon' => 'fa fa-male'],
            '6' => ['text' => 'Under 21', 'icon' => 'fa fa-male'],
            '7' => ['text' => 'Infant', 'icon' => 'im im-children'],
            '8' => ['text' => 'Child', 'icon' => 'im im-children'],
            '9' => ['text' => 'Teenager', 'icon' => 'im im-children'],
            '10' => ['text' => 'Adult', 'icon' => 'fa-male'],
            '11' => ['text' => 'Senior', 'icon' => 'im im-children'],
            '12' => ['text' => 'Additional occupant with adult', 'icon' => 'fa fa-male'],
            '13' => ['text' => 'Additional occupant without adult', 'icon' => 'fa fa-male'],
            '14' => ['text' => 'Free child', 'icon' => 'im im-children'],
            '15' => ['text' => 'Free adult', 'icon' => 'im im-children'],
            '16' => ['text' => 'Young driver', 'icon' => 'im im-children'],
            '17' => ['text' => 'Younger driver', 'icon' => 'im im-children'],
            '18' => ['text' => 'Under 10', 'icon' => 'im im-children'],
            '19' => ['text' => 'Junior', 'icon' => 'im im-children'],
        ];
        if ($list) {
            return $aqc_arr;
        } else {
            return $aqc_arr[$aqc_code];
        }

    }

    public static function cl_get_list_aqc_available()
    {

    }

    public static function cl_parse_country_code()
    {
        $country_codes = simplexml_load_file(get_template_directory() . '/inc/modules/colibri-pms-api/files/country_code.xml');
        $res = [];
        foreach ($country_codes->country as $key => $value) {
            $res[__((string)$value->attributes()['name'], ST_TEXTDOMAIN)] = (string)$value->attributes()['alpha-2'];
        }

        return $res;
    }

    public function cl_get_city_code()
    {
        $country_code = STInput::post('country_code', '');
        $res = Colibri_PMS::cl_get_list_city($country_code, 0, 400);
        echo json_encode($res);
        die;
    }

    public function cl_load_city_code()
    {
        $country_code = STInput::post('country_code', '');
        $page = STInput::post('page', '');
        $res = Colibri_PMS::cl_get_list_city($country_code, ($page * 7), $page * 7 + 7);
        echo json_encode($res);
        die;
    }

    public static function cl_build_link_from_array($params)
    {
        $link = '';
        if (!empty($params)) {
            foreach ($params as $key => $val) {
                //$link .=
            }
        }

        return $link;
    }

    public static function cl_get_number_of_day($start, $end)
    {
        $start_str = strtotime($start);
        $end_str = strtotime($end);

        $one_day = (60 * 60 * 24);
        $number_days = ($end_str - $start_str) / $one_day;

        return $number_days;
    }

    /**
     * @param $room_rate
     * @param $number_people
     *
     * @return string
     */
    public static function cl_cal_price_for_room_item($room_rate, $number_people)
    {
        $res = 0;
        foreach ($room_rate as $key => $val) {
            foreach ($val['rate'] as $k => $v) {
                if ($number_people >= $v['min'] && $number_people <= $v['max']) {
                    $res += $v['price'];
                }
            }
        }

        return $res;
    }

    public static function cl_cal_price_for_room_item_opt($item_all, $number_people)
    {
        $res = [];
        foreach ($item_all as $k => $v) {
            $room_rates = $v['room_rates'];
            $total = 0;
            foreach ($room_rates as $kk => $vv) {
                foreach ($vv['rate'] as $kkk => $vvv) {
                    if ($number_people >= $vvv['min'] && $number_people <= $vvv['max']) {
                        $total += $vvv['price'];
                    }
                }
            }
            array_push($res, $total);
        }
        return min($res);
    }

    public static function cl_cal_number_max_room($list_room)
    {
        $arr_num_room = [];
        foreach ($list_room as $k => $v) {
            array_push($arr_num_room, $v['num_of_unit']);
        }

        return max($arr_num_room);
    }

    public static function cl_cal_number_max_people($list_room)
    {
        $arr_num_people = [];
        foreach ($list_room as $key => $val) {
            $num_people = 0;
            foreach ($val['guest_count'] as $k => $v) {
                $num_people += $v['count'];
            }
            array_push($arr_num_people, $num_people);
        }

        return max($arr_num_people);
    }

    public static function cl_cal_guest_count($list_rooms)
    {
        $arr = [];
        foreach ($list_rooms as $k => $v) {
            foreach ($v['guest_count'] as $k_c => $v_c) {
                array_push($arr, array(
                    'age' => $v_c['age']['text'],
                    'count' => $v_c['count'],
                    'code' => $v_c['code']
                ));
            }
        }
        $data_code = [];

        foreach ($arr as $k_u => $v_u) {
            array_push($data_code, $v_u['code']);
        }

        foreach (array_unique($data_code) as $kk => $vv) {
            $arr_dd[$vv] = [];
        }

        foreach ($arr as $key => $val) {
            foreach (array_unique($data_code) as $kk => $vv) {
                if ($val['code'] == $vv) {
                    array_push($arr_dd[$vv], array(
                        'text' => $val['age'],
                        'count' => $val['count'],
                        'code' => $val['code']
                    ));
                }
            }
        }

        $arr_vvv = [];
        foreach ($arr_dd as $vvv => $kkk) {
            array_push($arr_vvv, max($kkk));
        }

        return $arr_vvv;
    }

    public function cl_get_list_room_ajax()
    {
        $hotel_code = STInput::post('hotel_code');
        $start = STInput::post('start', '');
        $end = STInput::post('end', '');
        $room = STInput::post('room', '');
        $people = STInput::post('people', '');

        $res = [$hotel_code, $start, $end, $people];

        $room_data = Colibri_PMS::cl_rt_get_list_rooms_of_hotel($hotel_code, $start, $end);
        $room_data = self::cl_cal_rooms_by_number_of_unit($room_data, $room);
        $room_data = self::cl_cal_rooms_by_max_people($room_data, $people);
        $room_data = self::cl_cal_length_of_stay_for_search($hotel_code, $start, $end, $room_data);
        $result = [];

        //$start = TravelHelper::convertDateFormat($start);
        //$end = TravelHelper::convertDateFormat($end);
        if (!empty($room_data)) {

	        foreach($room_data as $key => $item)
	        {
		        $arr[$item['id']][$key] = $item;
	        }
	        ksort($arr, SORT_NUMERIC);

            foreach ($arr as $k => $v) {
                foreach ($v as $kk => $vv){
	                $v[$kk]['number_select_room'] = $room;
                }
                $result['data'] .= st_cba_load_view('vc-elements/st-cba-hotel-detail/st-cba-hotel-list-room-item-opt', false, array(
                    'item' => $v,
                    'start' => $start,
                    'end' => $end,
                ));
            }
        } else {
            $result['data'] = st_cba_load_view('vc-elements/st-cba-hotel-detail/st-cba-hotel-list-room-none', false);
        }
        echo json_encode($result);
        die;
    }

    /**
     * @param $hotel_code
     * @param $start
     * @param $end
     * @param $room_code
     *
     * @return bool
     */
    public static function cl_check_min_lenght_of_stay($hotel_code, $start, $end, $room_code)
    {
        $number_day = self::cl_get_number_of_day(TravelHelper::convertDateFormat($start), TravelHelper::convertDateFormat($end));
        $data_restric = Colibri_PMS::cl_get_rates_restric_of_hotel($start, $end, $hotel_code);
        $flag = true;
        if (!empty($data_restric)) {
            foreach ($data_restric[$room_code]['length_of_stay'] as $kk => $vv) {
                if ($vv['time'] == $number_day) {
                    $min_los = $vv['min_los'];
                    if ($min_los > $number_day) {
                        $flag = false;
                    }
                }
            }
        }

        return $flag;
    }

    public static function cl_cal_length_of_stay_for_search($hotel_code, $start, $end, $rooms_data)
    {
        $number_day = self::cl_get_number_of_day(TravelHelper::convertDateFormat($start), TravelHelper::convertDateFormat($end));
        $data_restric = Colibri_PMS::cl_get_rates_restric_of_hotel($start, $end, $hotel_code);
        $arr_key = [];
        foreach ($rooms_data as $k => $v) {
            foreach ($data_restric[$v['id']]['length_of_stay'] as $kk => $vv) {
                if ($vv['time'] == $number_day) {
                    $min_los = $vv['min_los'];
                    if ($min_los > $number_day) {
                        array_push($arr_key, $v['id']);
                    }
                }
            }
        }

        if (!empty($arr_key)) {
            foreach ($arr_key as $key => $val) {
                foreach ($rooms_data as $k => $v) {
                    if ($v['id'] == $val) {
                        unset($rooms_data[$k]);
                    }
                }

            }
        }
        return $rooms_data;
    }

    /**
     * @param $rooms_data
     * @param $number
     *
     * @return mixed
     */
    public static function cl_cal_rooms_by_number_of_unit($rooms_data, $number)
    {
        $arr_key = [];
        foreach ($rooms_data as $k => $v) {
            if ($v['num_of_unit'] < $number) {
                array_push($arr_key, $k);
            }
        }

        if (!empty($arr_key)) {
            foreach ($arr_key as $key => $val) {
                unset($rooms_data[$val]);
            }
        }

        return $rooms_data;

    }

    public static function cl_compare_code_age($people, $age_code)
    {
        $arr = [];
        foreach ($people as $k => $v) {
            $sub_arr = explode('|', $v);
            $arr[$sub_arr[0]] = $sub_arr[1];
        }

        return $arr[$age_code];
    }

    /**
     * @param $rooms_data
     * @param $people
     *
     * @return mixed
     */
    public static function cl_cal_rooms_by_max_people($rooms_data, $people)
    {
        $arr_key = [];
        foreach ($rooms_data as $k => $v) {
            $i = 0;
            foreach ($v['guest_count'] as $kk => $vv) {
                if ($vv['count'] < self::cl_compare_code_age($people, $vv['code'])) {
                    $i++;
                }
            }
            if ($i > 0) {
                array_push($arr_key, $k);
            }
        }

        if (!empty($arr_key)) {
            foreach ($arr_key as $key => $val) {
                unset($rooms_data[$val]);
            }
        }

        return $rooms_data;

        //
        /*$arr_key = [];
        foreach ($rooms_data as $k => $v) {
            $num_people = 0;
            foreach ($v['guest_count'] as $kk => $vv) {
                $num_people += $vv['count'];
            }
            if ($num_people < $people) {
                array_push($arr_key, $k);
            }
        }

        if (!empty($arr_key)) {
            foreach ($arr_key as $key => $val) {
                unset($rooms_data[$val]);
            }
        }

        return $rooms_data;*/
    }

    public static function cl_check_out_submit()
    {
        $result = [];
        $data = STInput::post('data', '');
        if (!is_user_logged_in()) {
            $user_name = $data[11]['value'];
            $user_id = username_exists($user_name);
            if (!$user_id and email_exists($user_name) == false) {
                $password = $data[12]['value'];
                $userdata = [
                    'user_login' => $user_name,
                    'user_pass' => $password,
                    'user_email' => $user_name,
                    'first_name' => $data[9]['value'],
                    'last_name' => $data[10]['value']
                ];
                $user_id = wp_insert_user($userdata);
                wp_send_new_user_notifications($user_id);
            } else {
                $enable_popup_login = st()->get_option('enable_popup_login', 'off');
                $page_login = st()->get_option('page_user_login');
                $login_modal = '';
                $page_login = esc_url(get_the_permalink($page_login));
                if ($enable_popup_login == 'on') {
                    $login_modal = 'data-toggle="modal" data-target="#login_popup"';
                    $page_login = $page_user_register = 'javascript:void(0)';
                }
                $result['status'] = false;
                $result['message'] = sprintf(__('Email already exists! Please <a href="%s" %s target="_blank">login</a> to continue', ST_TEXTDOMAIN), $page_login, $login_modal);
                echo json_encode($result);
                die;
            }
        } else {
            $user_email_form = $data[11]['value'];
            $user_id = get_current_user_id();
            $user_data = get_userdata($user_id);
            $user_email = $user_data->user_email;
            if ($user_email_form != $user_email) {
                if (email_exists($user_email_form) == true) {
                    $result['status'] = false;
                    $result['message'] = sprintf(__('Email already exists with other account! Please check again', ST_TEXTDOMAIN));
                    echo json_encode($result);
                    die;
                }
            }
        }

        $xml = self::cl_set_xml_request_reservation($data);

        Colibri_PMS::init();
        $res = Colibri_PMS::cl_get_response_reservation($xml);

        $arr_res = simplexml_load_string($res);

        if (array_key_exists('Errors', $arr_res)) {
            $message = '';
            $result['status'] = false;
            foreach ($arr_res->Errors->Error as $key => $val) {
                $message .= '<p>' . __((string)$val, ST_TEXTDOMAIN) . '</p>';
            }
            $result['message'] = $message;
            echo json_encode($result);
            die;
        } else {
            $data_booking = [];
            $booking_arr = $arr_res->HotelReservations->HotelReservation->ResGlobalInfo->HotelReservationIDs->HotelReservationID->attributes();
            $data_booking['res_type'] = (string)$booking_arr['ResID_Type'];
            $data_booking['res_value'] = (string)$booking_arr['ResID_Value'];
            $data_booking['res_source'] = (string)$booking_arr['ResID_Source'];


            $data_insert = [];
            $data_insert['user_id'] = $user_id;
            $data_insert['room_code'] = $data[0]['value'];
            $data_insert['hotel_code'] = $data[5]['value'];
            $data_insert['start'] = $data[3]['value'];
            $data_insert['end'] = $data[4]['value'];
            $data_insert['room_rates'] = $data[2]['value'];
            $data_insert['room_name'] = $data[7]['value'];
            $data_insert['hotel_name'] = $data[6]['value'];
            $data_insert['hotel_thumb'] = $data[24]['value'];
            $data_insert['room_thumb'] = $data[23]['value'];
	        $data_insert['currency'] = $data[25]['value'];

            $data_user = [];
            $data_user['first_name'] = $data[9]['value'];
            $data_user['last_name'] = $data[10]['value'];
            $data_user['email'] = $data[11]['value'];
            $data_user['phone'] = $data[13]['value'];
            $data_user['country'] = $data[14]['value'];
            $data_user['city'] = $data[15]['value'];

            $data_card = [];
            $data_card['card_type'] = $data[16]['value'];
            $data_card['card_holder_fname'] = $data[21]['value'];
            $data_card['card_holder_lname'] = $data[22]['value'];


            $str_age = rtrim($data[8]['value'], ',');
            $data_insert['age_code'] = $str_age;

            $cba_statistic = ST_CBA_Statistic_Models::inst();

            $data_ins = array(
                'user_id' => $user_id,
                'status' => 'pending',
                'booking_date' => date('d/m/Y'),
                'booking_from' => strtotime(TravelHelper::convertDateFormat($data[3]['value'])),
                'booking_to' => strtotime(TravelHelper::convertDateFormat($data[4]['value'])),
                'data_res' => json_encode($data_booking),
                'data' => json_encode($data_insert),
                'data_user' => json_encode($data_user),
                'data_card' => json_encode($data_card),
            );
            $cba_statistic->insert_data($data_ins);

            $result['status'] = true;
            $result['message'] = __('Booking Successful!', ST_TEXTDOMAIN);
            echo json_encode($result);
            die;
        }
    }

    /**
     * Ajax for modified reservation
     */
    public static function cl_check_out_submit_modify()
    {
        $result = [];
        $data = STInput::post('data', '');
        if (!is_user_logged_in()) {
            $enable_popup_login = st()->get_option('enable_popup_login', 'off');
            $page_login = st()->get_option('page_user_login');
            $login_modal = '';
            $page_login = esc_url(get_the_permalink($page_login));
            if ($enable_popup_login == 'on') {
                $login_modal = 'data-toggle="modal" data-target="#login_popup"';
                $page_login = $page_user_register = 'javascript:void(0)';
            }
            $result['status'] = false;
            $result['message'] = sprintf(__('Please <a href="%s" %s target="_blank">login</a> to continue', ST_TEXTDOMAIN), $page_login, $login_modal);
            echo json_encode($result);
            die;
        } else {
            $user_email_form = $data[11]['value'];
            $user_id = get_current_user_id();
            $user_data = get_userdata($user_id);
            $user_email = $user_data->user_email;
            if ($user_email_form != $user_email) {
                if (email_exists($user_email_form) == true) {
                    $result['status'] = false;
                    $result['message'] = sprintf(__('Email already exists with other account. Please check again!', ST_TEXTDOMAIN));
                    echo json_encode($result);
                    die;
                }
            }

            $user_id = get_current_user_id();

            $stas_id = $data[27]['value'];
            $cba_statistic = ST_CBA_Statistic_Models::inst();
            if ($cba_statistic->check_data_for_user($stas_id, $user_id) > 0) {
                $xml = self::cl_set_xml_request_reservation_modify($data);
                Colibri_PMS::init();
                $res = Colibri_PMS::cl_get_response_reservation($xml);

                $arr_res = simplexml_load_string($res);

                if (array_key_exists('Errors', $arr_res)) {
                    $message = '';
                    $result['status'] = false;
                    foreach ($arr_res->Errors->Error as $key => $val) {
                        $message .= '<p>' . __((string)$val, ST_TEXTDOMAIN) . '</p>';
                    }
                    $result['message'] = $message;
                    echo json_encode($result);
                    die;
                } else {
                    //Update data
                    $data_booking = [];
                    $booking_arr = $arr_res->HotelResModifies->HotelResModify->ResGlobalInfo->HotelReservationIDs->HotelReservationID->attributes();
                    $data_booking['res_type'] = (string)$booking_arr['ResID_Type'];
                    $data_booking['res_value'] = (string)$booking_arr['ResID_Value'];
                    $data_booking['res_source'] = (string)$booking_arr['ResID_Source'];

                    $data_insert = [];
                    $data_insert['room_code'] = $data[0]['value'];
                    $data_insert['hotel_code'] = $data[5]['value'];
                    $data_insert['start'] = $data[3]['value'];
                    $data_insert['end'] = $data[4]['value'];
                    $data_insert['room_rates'] = $data[2]['value'];
                    $data_insert['room_name'] = $data[7]['value'];
                    $data_insert['hotel_name'] = $data[6]['value'];
                    $data_insert['hotel_thumb'] = $data[24]['value'];
                    $data_insert['room_thumb'] = $data[23]['value'];

                    $str_age = rtrim($data[8]['value'], ',');
                    $data_insert['age_code'] = $str_age;

                    $data_user = [];
                    $data_user['first_name'] = $data[9]['value'];
                    $data_user['last_name'] = $data[10]['value'];
                    $data_user['email'] = $data[11]['value'];
                    $data_user['phone'] = $data[13]['value'];
                    $data_user['country'] = $data[14]['value'];
                    $data_user['city'] = $data[15]['value'];

                    $data_card = [];
                    $data_card['card_type'] = $data[16]['value'];
                    $data_card['card_holder_fname'] = $data[21]['value'];
                    $data_card['card_holder_lname'] = $data[22]['value'];

                    $cba_statistic = ST_CBA_Statistic_Models::inst();

                    $data_ins = array(
                        'modify_date' => date('d/m/Y H:i'),
                        'booking_from' => strtotime(TravelHelper::convertDateFormat($data[3]['value'])),
                        'booking_to' => strtotime(TravelHelper::convertDateFormat($data[4]['value'])),
                        'data_res' => json_encode($data_booking),
                        'data' => json_encode($data_insert),
                        'data_user' => json_encode($data_user),
                        'data_card' => json_encode($data_card),
                    );

                    $where = array(
                        'id' => $stas_id,
                    );
                    $cba_statistic->update_data($data_ins, $where);

                    $result['status'] = true;
                    $result['message'] = __('Update Successful!', ST_TEXTDOMAIN);
                    echo json_encode($result);
                    die;
                }

            } else {
                $result['status'] = false;
                $result['message'] = __('Have an errors!.', ST_TEXTDOMAIN);
                echo json_encode($result);
                die;
            }
        }
    }

    public static function cl_get_detail_room()
    {
        $room_code = STInput::post('room_code', '');
        $hotel_code = STInput::post('hotel_code', '');
        $rate_plan = STInput::post('rate_plan', '');
        $start = STInput::post('start', '');
        $end = STInput::post('end', '');
        $number_room = STInput::post('number_room', '');

        $room = [];

        $cldt_dtr = Colibri_PMS::cl_rt_get_list_rooms_of_hotel($hotel_code, $start, $end);

        foreach ($cldt_dtr as $key => $item) {
            $arr[$item['id']][$key] = $item;
        }

        ksort($arr, SORT_NUMERIC);

        foreach ($arr as $k => $v) {
            if ($k == $room_code) {
                $room = $v;
            }
        }
        $data = [];
        if (!empty(array_values($room)[0])) {
            $data['status'] = true;
            //$arr['number_select_room'] = $number_room;
            //$start = TravelHelper::convertDateFormat($start);
            //$end = TravelHelper::convertDateFormat($end);
            $data['message'] = st_cba_load_view('room/detail', false, array(
                'item' => $room,
                'start' => $start,
                'end' => $end,
            ));
        } else {
            $data['status'] = false;
            $data['message'] = __('No room detail found!', ST_TEXTDOMAIN);
        }

        echo json_encode($data);
        die;
    }

    /**
     * @param $room_rates
     * @param $rate
     *
     * @return int
     */
    public static function cl_get_has_rate_pos($room_rates, $rate)
    {
        $data = 0;
        foreach ($room_rates as $k => $v) {
            if ($v[1] == $rate) {
                $data = $v[0];
            }
        }

        return $data;
    }

    /**
     * @param $room_rates
     *
     * @return mixed
     */
    public static function cl_get_max_room_number_modify($room_rates)
    {
        $data = [];
        foreach ($room_rates as $k => $v) {
            array_push($data, $v[0]);
        }

        return max($data);
    }

    /**
     * @param $age_codes
     * @param $code
     *
     * @return mixed
     */
    public static function cl_get_has_age_code($age_codes, $code)
    {
        $data = $age_codes[0];
        foreach ($age_codes as $k => $v) {
            if ($code == $k) {
                $data = $v;
            }
        }

        return $data;
    }

    /**
     * @param $room_rate
     * @param $people
     * @param int $re
     *
     * @return array|string
     */
    public static function cl_get_arr_price_for_range_night($room_rate, $people, $re = 1)
    {
        $arr_price = [];
        foreach ($room_rate as $key => $val) {
            if (count($val['rate']) > 1) {
                $i = 0;
                foreach ($val['rate'] as $k => $v) {
                    if ($people >= $v['min'] && $people <= $v['max']) {
                        array_push($arr_price, $v['price']);
                        $i++;
                    }
                }

                if ($i == 0) {
                    array_push($arr_price, $val['rate'][0]['price']);
                }
            } else {
                array_push($arr_price, $val['rate'][0]['price']);
            }
        }
        if ($re == 1) {
            return $arr_price;
        } else {
            return implode('|', $arr_price);
        }
    }

    public static function cl_check_minlfs()
    {

    }

    /**
     * @param $price
     * @param $currency_code
     * @param $pos
     * @param $space
     * @param $tag
     *
     * @return string
     */
    public static function cl_format_money($price, $currency_code, $pos, $space = false, $decimal = 2)
    {
        $str_space = '';
        if ($space) {
            $str_space = ' ';
        }
        if ($pos == 'left') {
            $str_price = self::cl_get_currency_code($currency_code) . $str_space . number_format((float)$price, $decimal, '.', ',');
        } else {
            $str_price = number_format((float)$price, $decimal, '.', ',') . $str_space . self::cl_get_currency_code($currency_code);
        }

        return $str_price;
    }

    public static function cl_get_currency_code($currency_code)
    {
        $arr = [
            'EUR' => '€',
            'USD' => '$',
            'ALL' => 'Albania Lek',
            'DZD ' => 'Algeria',
            'AFN' => 'Afghanistan Afghani',
            'ARS' => 'Argentina Peso',
            'AWG' => 'Aruba Guilder',
            'AUD' => 'Australia Dollar',
            'AZN' => 'Azerbaijan New Manat',
            'BSD' => 'Bahamas Dollar',
            'BHD' => 'Bahraini Dinar',
            'BBD' => 'Barbados Dollar',
            'BDT' => 'Bangladeshi taka',
            'BYN' => 'Belarus Ruble',
            'BZD' => 'Belize Dollar',
            'BMD' => 'Bermuda Dollar',
            'BOB' => 'Bolivia Boliviano',
            'BAM' => 'Bosnia and Herzegovina Convertible Marka',
            'BWP' => 'Botswana Pula',
            'BGN' => 'Bulgaria Lev',
            'BRL' => 'Brazil Real',
            'BND' => 'Brunei Darussalam Dollar',
            'KHR' => 'Cambodia Riel',
            'CAD' => 'Canada Dollar',
            'KYD' => 'Cayman Islands Dollar',
            'CLP' => 'Chile Peso',
            'CNY' => 'China Yuan Renminbi',
            'COP' => 'Colombia Peso',
            'CRC' => 'Costa Rica Colon',
            'HRK' => 'Croatia Kuna',
            'CUP' => 'Cuba Peso',
            'CZK' => 'Czech Republic Koruna',
            'DKK' => 'Denmark Krone',
            'DOP' => 'Dominican Republic Peso',
            'XCD' => 'East Caribbean Dollar',
            'EGP' => 'Egypt Pound',
            'SVC' => 'El Salvador Colon',
            'EEK' => 'Estonia Kroon',
            'FKP' => 'Falkland Islands (Malvinas) Pound',
            'FJD' => 'Fiji Dollar',
            'GHC' => 'Ghana Cedis',
            'GIP' => 'Gibraltar Pound',
            'GTQ' => 'Guatemala Quetzal',
            'GGP' => 'Guernsey Pound',
            'GYD' => 'Guyana Dollar',
            'GEL' => 'Georgia',
            'HNL' => 'Honduras Lempira',
            'HKD' => 'Hong Kong Dollar',
            'HUF' => 'Hungary Forint',
            'ISK' => 'Iceland Krona',
            'INR' => 'India Rupee',
            'IDR' => 'Indonesia Rupiah',
            'IRR' => 'Iran Rial',
            'IMP' => 'Isle of Man Pound',
            'ILS' => 'Israel Shekel',
            'JMD' => 'Jamaica Dollar',
            'JPY' => 'Japan Yen',
            'JEP' => 'Jersey Pound',
            'KZT' => 'Kazakhstan Tenge',
            'KPW' => 'Korea (North) Won',
            'KRW' => 'Korea (South) Won',
            'KGS' => 'Kyrgyzstan Som',
            'LAK' => 'Laos Kip',
            'LVL' => 'Latvia Lat',
            'LBP' => 'Lebanon Pound',
            'LRD' => 'Liberia Dollar',
            'LTL' => 'Lithuania Litas',
            'MKD' => 'Macedonia Denar',
            'MYR' => 'Malaysia Ringgit',
            'MUR' => 'Mauritius Rupee',
            'MXN' => 'Mexico Peso',
            'MNT' => 'Mongolia Tughrik',
            'MAD' => 'Morocco Dirhams',
            'MZN' => 'Mozambique Metical',
            'NAD' => 'Namibia Dollar',
            'NPR' => 'Nepal Rupee',
            'ANG' => 'Netherlands Antilles Guilder',
            'NZD' => 'New Zealand Dollar',
            'NIO' => 'Nicaragua Cordoba',
            'NGN' => 'Nigeria Naira',
            'NOK' => 'Norway Krone',
            'OMR' => 'Oman Rial',
            'PKR' => 'Pakistan Rupee',
            'PAB' => 'Panama Balboa',
            'PYG' => 'Paraguay Guarani',
            'PEN' => 'Peru Nuevo Sol',
            'PHP' => 'Philippines Peso',
            'PLN' => 'Poland Zloty',
            'QAR' => 'Qatar Riyal',
            'RON' => 'Romania New Leu',
            'RUB' => 'Russia Ruble',
            'SHP' => 'Saint Helena Pound',
            'SAR' => 'Saudi Arabia Riyal',
            'RSD' => 'Serbia Dinar',
            'SCR' => 'Seychelles Rupee',
            'SGD' => 'Singapore Dollar',
            'SBD' => 'Solomon Islands Dollar',
            'SOS' => 'Somalia Shilling',
            'ZAR' => 'South Africa Rand',
            'LKR' => 'Sri Lanka Rupee',
            'SEK' => 'Sweden Krona',
            'CHF' => 'Switzerland Franc',
            'SRD' => 'Suriname Dollar',
            'SYP' => 'Syria Pound',
            'TWD' => 'Taiwan New Dollar',
            'THB' => 'Thailand Baht',
            'TTD' => 'Trinidad and Tobago Dollar',
            'TRY' => 'Turkey Lira',
            'TRL' => 'Turkey Lira',
            'TVD' => 'Tuvalu Dollar',
            'TD' => 'Tunisian Dinar',
            'UAH' => 'Ukraine Hryvna',
            'AED' => 'United Arab Emirates',
            'GBP' => 'United Kingdom Pound',
            'UYU' => 'Uruguay Peso',
            'UZS' => 'Uzbekistan Som',
            'VEF' => 'Venezuela Bolivar',
            'VND' => 'đ',
            'YER' => 'Yemen Rial',
            'CFA' => 'West African Franc',
            'ZWD' => 'Zimbabwe Dollar',
            'ZMW' => 'Zambian Kwacha'
        ];

        return $arr[$currency_code];
    }

    public static function cl_filter_rooms()
    {

    }

    /**
     * @param $room_arr
     *
     * @return array
     */
    public static function cl_get_list_rate_plan($room_arr)
    {
        $rate_arr = [];
        if (!empty($room_arr)) {
            foreach ($room_arr as $k => $v) {
                $rate_arr[$v['rate_plan_code']] = $v['rate_plan_name'];
            }
        }

        return $rate_arr;
    }

    public static function cl_get_hotel_by_id($hotel_code, $start, $end)
    {
        $xml = '';

        //Colibri_PMS::init();
        //$res = Colibri_PMS::cl_get_response_reservation($xml);

        //$arr_res = simplexml_load_string($res);

    }

    /**
     * @param $room_data
     * @param $room_code
     * @param $plan_code
     *
     * @return array
     */
    public static function cl_get_room_rate_by_rate_plan_code($room_data, $room_code, $plan_code)
    {
        $arr_room_rate = [];
        foreach ($room_data as $k => $v) {
            if ($v['id'] == $room_code && $v['rate_plan_code'] == $plan_code) {
                $arr_room_rate = $v['room_rates'];
            }
        }

        return $arr_room_rate;
    }


    public static function cl_set_xml_request_reservation($data)
    {
        /*$rate_plan = '';
        $rate_plan_arr = json_decode(stripslashes($data[1]['value']));
        if (!empty($rate_plan_arr)) {
            foreach ($rate_plan_arr as $k => $v) {
                $rate_plan .= '<RatePlan RatePlanCode="' . $v . '" />';
            }
        }

        $room_rate = '';
        $room_rate_arr = json_decode(stripslashes($data[2]['value']));
        if (!empty($room_rate_arr)) {
            foreach ($room_rate_arr as $k => $v) {
                $room_rate .= '<RoomRate RoomTypeCode="' . $data[0]['value'] . '" NumberOfUnits="' . $v[0] . '" RatePlanCode="' . $v[1] . '">';
                $room_rate .= '<Rates>';
                $room_rate .= '<Rate UnitMultiplier="1">';
                $room_rate .= '<Total AmountAfterTax="' . $v[2] . '" CurrencyCode="EUR" />';
                $room_rate .= '</Rate>';
                $room_rate .= '</Rates>';
                $room_rate .= '</RoomRate>';
            }
        }*/

        $str_age = rtrim($data[8]['value'], ',');
        $arr_age = explode(',', $str_age);
        $xml_guest_count = '';
        if (!empty($arr_age)) {
            foreach ($arr_age as $k => $v) {
                $sub_age = explode('|', $v);
                $xml_guest_count .= '<GuestCount AgeQualifyingCode="' . $sub_age[0] . '" Count="' . $sub_age[1] . '" />';
            }
        }

        $room_stay = '';
        $room_rate_arr = json_decode(stripslashes($data[2]['value']));
        if (!empty($room_rate_arr)) {
            foreach ($room_rate_arr as $k => $v) {
                $room_stay .= '<RoomStay>';
                $room_stay .= '<RoomTypes>';
                $room_stay .= '<RoomType RoomTypeCode="' . $data[0]['value'] . '" />';
                $room_stay .= '</RoomTypes>';
                $room_stay .= '<RatePlans>';
                $room_stay .= '<RatePlan RatePlanCode="' . $v[1] . '" />';
                $room_stay .= '</RatePlans>';
                $room_stay .= '<RoomRates>';
                $room_stay .= '<RoomRate RoomTypeCode="' . $data[0]['value'] . '" NumberOfUnits="' . $v[0] . '" RatePlanCode="' . $v[1] . '">';
                $room_stay .= '<Rates>';
                $room_stay .= '<Rate UnitMultiplier="1">';
                $room_stay .= '<Total AmountAfterTax="' . $v[2] . '" CurrencyCode="EUR" />';
                $room_stay .= '</Rate>';
                $room_stay .= '</Rates>';
                $room_stay .= '</RoomRate>';
                $room_stay .= '</RoomRates>';
                $room_stay .= '<GuestCounts>';
                $room_stay .= trim($xml_guest_count);
                $room_stay .= '</GuestCounts>';
                $room_stay .= '<TimeSpan Start="' . TravelHelper::convertDateFormatColibri($data[3]['value']) . '" End="' . TravelHelper::convertDateFormatColibri($data[4]['value']) . '" />';
                $room_stay .= '<Guarantee>';
                $room_stay .= '<GuaranteesAccepted>';
                $room_stay .= '<GuaranteeAccepted>';
                $room_stay .= '<PaymentCard CardType="' . $data[16]['value'] . '" CardNumber="' . $data[17]['value'] . '" SeriesCode="' . $data[20]['value'] . '" ExpireDate="' . $data[18]['value'] . '/' . $data[19]['value'] . '">';
                $room_stay .= '<CardHolderName>' . $data[21]['value'] . ' ' . $data[22]['value'] . '</CardHolderName>';
                $room_stay .= '</PaymentCard>';
                $room_stay .= '</GuaranteeAccepted>';
                $room_stay .= '</GuaranteesAccepted>';
                $room_stay .= '</Guarantee>';
                $room_stay .= '<BasicPropertyInfo HotelCode="' . $data[5]['value'] . '" />';
                $room_stay .= '<ResGuestRPHs>';
                $room_stay .= '<ResGuestRPH RPH="11" />';
                $room_stay .= '</ResGuestRPHs>';
                $room_stay .= '</RoomStay>';
            }
        }

        $xml = '<OTA_HotelResRQ Version="1.003" xmlns="http://www.opentravel.org/OTA/2003/05">
    <HotelReservations>
        <HotelReservation>
            <RoomStays>
                ' . $room_stay . '
            </RoomStays>
            <ResGuests>
                <ResGuest ResGuestRPH="11">
                    <Profiles>
                        <ProfileInfo>
                            <Profile>
                                <Customer>
                                    <PersonName>
                                        <GivenName>' . $data[9]['value'] . '</GivenName>
                                        <Surname>' . $data[10]['value'] . '</Surname>
                                    </PersonName>
                                    <Telephone PhoneNumber="' . $data[13]['value'] . '" />
                                    <Email>' . $data[11]['value'] . '</Email>
                                    <Address>
                                        <CityName>' . $data[15]['value'] . '</CityName>
                                        <CountryName>' . $data[14]['value'] . '</CountryName>
                                    </Address>
                                </Customer>
                            </Profile>
                        </ProfileInfo>
                    </Profiles>
                </ResGuest>
            </ResGuests>
        </HotelReservation>
    </HotelReservations>
</OTA_HotelResRQ>';

        return $xml;
    }

    public static function cl_set_xml_request_reservation_modify($data)
    {
        $str_age = rtrim($data[8]['value'], ',');
        $arr_age = explode(',', $str_age);
        $xml_guest_count = '';
        if (!empty($arr_age)) {
            foreach ($arr_age as $k => $v) {
                $sub_age = explode('|', $v);
                $xml_guest_count .= '<GuestCount AgeQualifyingCode="' . $sub_age[0] . '" Count="' . $sub_age[1] . '" />';
            }
        }

        $room_stay = '';
        $room_rate_arr = json_decode(stripslashes($data[2]['value']));
        if (!empty($room_rate_arr)) {
            foreach ($room_rate_arr as $k => $v) {
                $room_stay .= '<RoomStay>';
                $room_stay .= '<RoomTypes>';
                $room_stay .= '<RoomType RoomTypeCode="' . $data[0]['value'] . '" />';
                $room_stay .= '</RoomTypes>';
                $room_stay .= '<RatePlans>';
                $room_stay .= '<RatePlan RatePlanCode="' . $v[1] . '" />';
                $room_stay .= '</RatePlans>';
                $room_stay .= '<RoomRates>';
                $room_stay .= '<RoomRate RoomTypeCode="' . $data[0]['value'] . '" NumberOfUnits="' . $v[0] . '" RatePlanCode="' . $v[1] . '">';
                $room_stay .= '<Rates>';
                $room_stay .= '<Rate UnitMultiplier="1">';
                $room_stay .= '<Total AmountAfterTax="' . $v[2] . '" CurrencyCode="EUR" />';
                $room_stay .= '</Rate>';
                $room_stay .= '</Rates>';
                $room_stay .= '</RoomRate>';
                $room_stay .= '</RoomRates>';
                $room_stay .= '<GuestCounts>';
                $room_stay .= trim($xml_guest_count);
                $room_stay .= '</GuestCounts>';
                $room_stay .= '<TimeSpan Start="' . TravelHelper::convertDateFormatColibri($data[3]['value']) . '" End="' . TravelHelper::convertDateFormatColibri($data[4]['value']) . '" />';
                $room_stay .= '<Guarantee>';
                $room_stay .= '<GuaranteesAccepted>';
                $room_stay .= '<GuaranteeAccepted>';
                $room_stay .= '<PaymentCard CardType="' . $data[16]['value'] . '" CardNumber="' . $data[17]['value'] . '" SeriesCode="' . $data[20]['value'] . '" ExpireDate="' . $data[18]['value'] . '/' . $data[19]['value'] . '">';
                $room_stay .= '<CardHolderName>' . $data[21]['value'] . ' ' . $data[22]['value'] . '</CardHolderName>';
                $room_stay .= '</PaymentCard>';
                $room_stay .= '</GuaranteeAccepted>';
                $room_stay .= '</GuaranteesAccepted>';
                $room_stay .= '</Guarantee>';
                $room_stay .= '<BasicPropertyInfo HotelCode="' . $data[5]['value'] . '" />';
                $room_stay .= '<ResGuestRPHs>';
                $room_stay .= '<ResGuestRPH RPH="11" />';
                $room_stay .= '</ResGuestRPHs>';
                $room_stay .= '</RoomStay>';
            }
        }

        $xml = '<OTA_HotelResModifyRQ Version="1.003" xmlns="http://www.opentravel.org/OTA/2003/05">
    <HotelResModifies>
        <HotelResModify>
            <UniqueID Type="' . $data[26]['value'] . '" ID="' . substr($data[25]['value'], 2, (strlen($data[25]['value']) - 2)) . '" />
            <RoomStays>
                ' . $room_stay . '
            </RoomStays>
            <ResGuests>
                <ResGuest ResGuestRPH="11">
                    <Profiles>
                        <ProfileInfo>
                            <Profile>
                                <Customer>
                                    <PersonName>
                                        <GivenName>' . $data[9]['value'] . '</GivenName>
                                        <Surname>' . $data[10]['value'] . '</Surname>
                                    </PersonName>
                                    <Telephone PhoneNumber="' . $data[13]['value'] . '" />
                                    <Email>' . $data[11]['value'] . '</Email>
                                    <Address>
                                        <CityName>' . $data[15]['value'] . '</CityName>
                                        <CountryName>' . $data[14]['value'] . '</CountryName>
                                    </Address>
                                </Customer>
                            </Profile>
                        </ProfileInfo>
                    </Profiles>
                </ResGuest>
            </ResGuests>
            <ResGlobalInfo>
                <HotelReservationIDs>
                    <HotelReservationID ResID_Value="' . substr($data[25]['value'], 2, (strlen($data[25]['value']) - 2)) . '" />
                </HotelReservationIDs>
            </ResGlobalInfo>
        </HotelResModify>
    </HotelResModifies>
</OTA_HotelResModifyRQ>';

        return $xml;
    }

    public static function cl_get_rates($hotel_code, $start, $end)
    {

    }

    public static function cl_cancel_reservation()
    {
        $res_type = STInput::post('res_type', '');
        $res_id = STInput::post('res_id', '');
        $res_source = STInput::post('res_source', '');
        $stas_id = STInput::post('stas_id', '');

        $xml_request = '<OTA_CancelRQ xmlns="http://www.opentravel.org/OTA/2003/05" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opentravel.org/OTA/2003/05 OTA_CancelRQ.xsd" EchoToken="892345" TimeStamp="2003-03-17T09:30:47-05:00" Target="Production" Version="1.001" SequenceNmbr="1" CancelType="Initiate">
                            <UniqueID Type="' . $res_type . '" ID="' . substr($res_id, 2, (strlen($res_id) - 2)) . '" />
                        </OTA_CancelRQ>';

        Colibri_PMS::init();
        $res = Colibri_PMS::cl_send_request_xml($xml_request);

        $arr_res = simplexml_load_string($res);
        $result = [];
        if (array_key_exists('Errors', $arr_res)) {
            $message = '';
            $result['status'] = 'danger';
            foreach ($arr_res->Errors->Error as $key => $val) {
                $message .= '<p>' . __((string)$val, ST_TEXTDOMAIN) . '</p>';
            }
            $result['message'] = $message;
        }
        if (array_key_exists('Success', $arr_res)) {
            if (array_key_exists('Warnings', $arr_res)) {
                $message = '';
                $result['status'] = 'warning';
                foreach ($arr_res->Warnings->Warning as $key => $val) {
                    $message .= '<p>' . (string)$val->attributes()['ShortText'] . '</p>';
                }
                $result['message'] = $message;
            } else {
                $user_id = get_current_user_id();
                $cba_statistic = ST_CBA_Statistic_Models::inst();
                if ($cba_statistic->check_data_for_user($stas_id, $user_id) > 0) {
                    $data_upd = array(
                        'status' => 'cancelled'
                    );
                    $data_where = array(
                        'id' => $stas_id
                    );
                    $cba_statistic->update_data($data_upd, $data_where);

                    $result['status'] = 'success';
                    $result['message'] = __('This reservation was canceled.', ST_TEXTDOMAIN);
                } else {
                    $result['status'] = 'danger';
                    $result['message'] = __('Have an errors!.', ST_TEXTDOMAIN);
                }

            }
        }
        echo json_encode($result);
        die;
    }

    public static function cl_remove_reservation()
    {
        $result = [];
        $user_id = get_current_user_id();
        $stas_id = STInput::post('stas_id', '');
        $cba_statistic = ST_CBA_Statistic_Models::inst();
        if ($cba_statistic->check_data_for_user($stas_id, $user_id) > 0) {
            $data_where = array(
                'id' => $stas_id
            );
            $cba_statistic->delete_data($data_where);
            $result['status'] = 'success';
            $result['message'] = __('This reservation was removed.', ST_TEXTDOMAIN);
        } else {
            $result['status'] = 'danger';
            $result['message'] = __('Have an errors!.', ST_TEXTDOMAIN);
        }
        echo json_encode($result);
        die;
    }

    /******************* Data for admin *********************/

    /**
     * @param $star
     * @param $end
     * @return array
     */
    public static function cl_ad_get_list_property_by_date($start, $end)
    {
        $start = TravelHelper::convertDateFormatColibri($start);
        $end = TravelHelper::convertDateFormatColibri($end);

        $xml_request = '
            <OTA_HotelSearchRQ PrimaryLangID="eng" AltLangID="deu" Version="1.003" xmlns="http://www.opentravel.org/OTA/2003/05">
                <POS />
                <Criteria>
                    <Criterion>
                        <StayDateRange Start="' . $start . '" End="' . $end . '" />
                    </Criterion>
                </Criteria>
            </OTA_HotelSearchRQ>
        ';

        echo $xml_request;


        Colibri_PMS::init();
        $res_xml = Colibri_PMS::cl_send_request_xml($xml_request);
        $res_arr = simplexml_load_string($res_xml);
        return $res_arr;
    }

    /**
     * @param $start
     * @param $end
     * @return array
     */
    public static function cl_ad_get_list_for_options($start, $end)
    {
        $data = self::cl_ad_get_list_property_by_date($start, $end);
        $return = [];
        if (!array_key_exists('Errors', $data)) {
            $i = 0;
            foreach ($data->Properties->Property as $k => $v) {
                $return[$i]['code'] = (string)$v->attributes()['HotelCode'];
                $return[$i]['name'] = (string)$v->attributes()['HotelName'];
                $i++;
            }
        }
        return $return;
    }

    public static function cl_ad_get_rates_by_date($list_room, $room_code, $plan_code, $start, $end)
    {
        foreach ($list_room as $k => $v) {
            if ($v['rate_plan_code'] == $plan_code && $v['id'] == $room_code) {
                $arr = $v['room_rates'];
            }
        }

        $price = '';
        if (isset($arr) && !empty($arr)) {
            foreach ($arr as $kk => $vv) {
                //echo TravelHelper::convertDateFormatColibri($end) . '<br />';
                if ($vv['start'] == TravelHelper::convertDateFormatColibri($start) && $vv['end'] == TravelHelper::convertDateFormatColibri($end)) {
                    if (isset($vv['rate'][0]['price'])) {
                        $price = $vv['rate'][0]['price'];
                    } else {
                        $price = 'n/a';
                    }

                }
            }
        } else {
            $price = 'n/a';
        }
        return $price;
    }

    public static function cl_ad_get_data_booking_by_date($start, $end)
    {
        $start = strtotime(TravelHelper::convertDateFormat(date('d/m/Y', strtotime($start))));
        $end = strtotime(TravelHelper::convertDateFormat(date('d/m/Y', strtotime($end))));

        $cba_statistic = ST_CBA_Statistic_Models::inst();
        $data = $cba_statistic->get_data_by_date($start, $end);
        return $data;
    }

    public static function add_class_to_table($data_booking, $i, $room_code, $rate_code)
    {
        $j = 1;
        $class = '';
        $extra_class = '';
        $extra_start = '';
        $h = 0;
        if(!empty($data_booking)){
            foreach ($data_booking as $k => $v) {
                $data = json_decode($v['data']);
                $rate_plan = json_decode(stripcslashes($data->room_rates));
                $arr_rates = [];
                foreach ($rate_plan as $kk => $vv) {
                    array_push($arr_rates, $vv[1]);
                }

                if ($i >= $v['booking_from'] && $i <= $v['booking_to'] && $data->room_code == $room_code && in_array($rate_code, $arr_rates)) {
                    if ($h == 0) {
                        $extra_start = ' booking-start ';
                    }
                    $class = 'aclass ';
                    $extra_class .= 'booking-' . $v['id'] . ' ';
                    $j++;
                    $h++;
                }

            }
            return $class . $extra_class;
        }else{
            return '';
        }
    }

    public static function cl_check_api($res){
	    $arr_res = simplexml_load_string($res);

	    if(empty($arr_res)){
		    return false;
        }else {
		    if ( array_key_exists( 'Errors', $arr_res )) {
			    return false;
		    }
		    if ( array_key_exists( 'Success', $arr_res ) ) {
			    return true;
		    }
	    }
    }
}

Colibri_Helper::init();

class OrderCmp
{
    private $field;
    private $sort;

    function __construct($field, $sort)
    {
        $this->field = $field;
        $this->sort = $sort;
    }

    function call($a, $b)
    {
        return Colibri_Helper::order_cmp($a, $b, $this->field, $this->sort);
    }
}