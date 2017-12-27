<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 23/03/2016
 * Time: 15:22 CH
 */
if (function_exists('vc_map')) {
    vc_map(array(
        "name"                    => __("ST List Partner", ST_TEXTDOMAIN),
        "base"                    => "st_list_partner",
        "content_element"         => true,
        "show_settings_on_create" => true,
        "icon"                    => "icon-st",
        "category"                => "Shinetheme",
        "params"                  => array(
            array(
                'type'       => 'textfield',
                'param_name' => 'speed_slider',
                'holder'     => 'div',
                'heading'    => __('Speed of slider', ST_TEXTDOMAIN),
                'value'      => '3000',
            ),
            array(
                'heading'    => __('Auto play', ST_TEXTDOMAIN),
                'param_name' => 'autoplay',
                'type'       => 'checkbox',
                'value'      => array(
                    __('Yes', ST_TEXTDOMAIN) => 'yes',
                ),
            ),
        ),
    ));

    if (!function_exists('st_list_partner')) {
        function st_list_partner($arg)
        {
            wp_enqueue_script('owl-carousel.js');
            extract(
                wp_parse_args(
                    $arg, array(
                        'style'        => '',
                        'items'        => 4,
                        'speed_slider' => 3000,
                        'autoplay'     => 'yes',
                    )
                )
            );

            global $wpdb;

            $sql = "SELECT SQL_CALC_FOUND_ROWS
                {$wpdb->prefix}users.*, count(post.ID) AS services
            FROM
                {$wpdb->prefix}users
            INNER JOIN {$wpdb->prefix}usermeta ON (
                {$wpdb->prefix}users.ID = {$wpdb->prefix}usermeta.user_id
            )
            INNER JOIN {$wpdb->prefix}posts AS post ON (
                {$wpdb->prefix}users.ID = post.post_author
            )
            WHERE
                1 = 1
            AND (
                (
                    (
                        {$wpdb->prefix}usermeta.meta_key = '{$wpdb->prefix}capabilities'
                        AND {$wpdb->prefix}usermeta.meta_value LIKE '%partner%'
                    )
                )
            )
            AND post.post_type IN (
                'st_hotel',
                'hotel_room',
                'st_rental',
                'st_tours',
                'st_activity',
                'st_cars'
            )
            GROUP BY
                {$wpdb->prefix}users.ID
            ORDER BY
                user_login ASC";

            $results = $wpdb->get_results($sql);
            $html = '';
            if (!empty($results)) {
                $items = count($results);
                $html = "<div class='st_list_partner owl-theme' data-items ='" . $items . "' data-speed='" . $speed_slider . "' data-autoplay='" . $autoplay . "'>";
                foreach( $results as $key => $val){
                    $item_string = ($val->services <= 1) ? __(' service', ST_TEXTDOMAIN) : __(' services', ST_TEXTDOMAIN);
                    $html .= "<div class='item st_tour_ver'>
                    <div class='dummy'>
                    <h4 class='title'>" . $val->user_login . "</h4>
                    <div class ='nums'>" . $val->services . $item_string . "</div>
                    </div>
                    <div class='img-container'>". get_avatar($val->ID, 512, null, TravelHelper::get_alt_image() , array('class' => 'img-responsive')) . "</div>
                    </div>";
                }
                $html .= "</div> <div class='st_list_partner_nav' >
                <i class=' prev fa main-color  fa-angle-left box-icon-sm box-icon-border round'>  </i>
                <i class=' next fa main-color  fa-angle-right box-icon-sm box-icon-border round'>  </i>
                </div>";
                return $html;
            }   
        }
    }

    st_reg_shortcode('st_list_partner', 'st_list_partner');
}
