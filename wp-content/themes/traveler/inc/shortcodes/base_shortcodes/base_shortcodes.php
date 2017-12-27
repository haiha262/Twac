<?php

if (!function_exists('st_sc_custom_meta')) {
    function st_sc_custom_meta($attr, $content = false)
    {
        $data = shortcode_atts(
            array(
                'key' => ''
            ), $attr, 'st_custom_meta');
        extract($data);
        if (!empty($key)) {
            $data = get_post_meta(get_the_ID(), $key, true);
            return balanceTags($data);
        }

    }

    st_reg_shortcode('st_custom_meta', 'st_sc_custom_meta');
}
if (!function_exists('st_sc_admin_email')) {
    function st_sc_admin_email()
    {
        return '<a class="contact_admin_email" href="mailto:' . get_bloginfo('admin_email') . '" ><i class="fa fa-envelope-o"></i>  ' . get_bloginfo('admin_email') . "</a>";
    }

    st_reg_shortcode('admin_email', 'st_sc_admin_email');
}
if (!function_exists('st_sc_languages_select')) {
    function st_sc_languages_select()
    {
        return st()->load_template("menu/language_select", null, array('container' => "div", "class" => "top-user-area-lang nav-drop"));
    }

    st_reg_shortcode('languages_select', 'st_sc_languages_select');
}
if (!function_exists('st_sc_social')) {
    function st_sc_social($attr)
    {
        $data = shortcode_atts(
            array(
                'name' => '',
                'link' => ''
            ), $attr, 'social_link'
        );
        extract($data);
        if (!empty($name)) {
            switch ($name) {
                case 'facebook':
                    $icon = "fa fa-facebook";
                    break;
                case 'twitter':
                    $icon = "fa fa-twitter";
                    break;
                case 'youtube':
                    $icon = "fa fa-youtube-play";
                    break;
                default:
                    # code...
                    break;
            }
            return "<a class='top_bar_social' href='" . $link . "'><i class='" . $icon . "'></i></a>";
        }
    }

    st_reg_shortcode('social_link', 'st_sc_social');
}
if (!function_exists('st_sc_login_select')) {
    function st_sc_login_select()
    {
        return st()->load_template("menu/login_select", null, array('container' => "div"));
    }

    st_reg_shortcode('login_select', 'st_sc_login_select');
}
if (!function_exists('st_sc_currency_select')) {
    function st_sc_currency_select()
    {
        return st()->load_template("menu/currency_select", null, array('container' => "div"));
    }

    st_reg_shortcode('currency_select', 'st_sc_currency_select');
}


if (function_exists('vc_map')) {
    $menus = get_terms('nav_menu', array('hide_empty' => true));

    $_list_nav = array();
    foreach ($menus as $key => $value) {
        $_list_nav[$value->name] = $value->slug;
    }

    vc_map(array(
        "name" => __("Custom menu", ST_TEXTDOMAIN),
        "base" => "st_custom_menu",
        "content_element" => true,
        "icon" => "icon-st",
        "category" => "Content",
        'show_settings_on_create' => true,
        "params" => array(
            array(
                "type" => "textfield",
                "heading" => __("Title", ST_TEXTDOMAIN),
                "param_name" => "title",
                "description" => "",
            ),
            array(
                "type" => "dropdown",
                "heading" => __("Style", ST_TEXTDOMAIN),
                "param_name" => "menu",
                "value" => $_list_nav,
            ),
        )
    ));
}

if (!function_exists('st_custom_menu')) {
    function st_custom_menu($arg, $content = null)
    {
        $data = shortcode_atts(array(
            'title' => "",
            'menu' => "",
        ), $arg, 'st_custom_menu');
        extract($data);

        $menu_obj = wp_get_nav_menu_object($menu);

        $nav_menu_args = array(
            'fallback_cb' => '',
            'menu' => $menu_obj->term_id,
            'container' => 'div',
            'echo' => false,
            'container_class' => 'widget widget_nav_menu'
        );

        $html = wp_nav_menu($nav_menu_args);
        return $html;
    }

    st_reg_shortcode('st_custom_menu', 'st_custom_menu');
}

if (function_exists('vc_map')) {
    vc_map(array(
        'name' => __('ST Cancellation Data', ST_TEXTDOMAIN),
        'base' => 'st_cancellation',
        'content_element' => true,
        'icon' => 'icon-st',
        'category' => 'Rental',
        'show_settings_on_create' => true,
        'params' => array(
            array(
                "type" => "textfield",
                "holder" => "div",
                "heading" => __("Title", ST_TEXTDOMAIN),
                "param_name" => "title",
                "description" => "",
                "value" => "",
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                "type" => "dropdown",
                "holder" => "div",
                "heading" => __("Font Size", ST_TEXTDOMAIN),
                "param_name" => "font_size",
                "description" => "",
                "value" => array(
                    __('--Select--', ST_TEXTDOMAIN) => '',
                    __("H1", ST_TEXTDOMAIN) => '1',
                    __("H2", ST_TEXTDOMAIN) => '2',
                    __("H3", ST_TEXTDOMAIN) => '3',
                    __("H4", ST_TEXTDOMAIN) => '4',
                    __("H5", ST_TEXTDOMAIN) => '5',
                ),
                'edit_field_class' => 'vc_col-sm-6',
            ),
        )
    ));
}
if (!function_exists('st_cancellation')) {
    function st_cancellation($arg)
    {
        if (is_singular()) {
            $default = array(
                'title' => '',
                'font_size' => '3',
            );
            extract(wp_parse_args($arg, $default));


            $cancel_policy = get_post_meta(get_the_ID(), 'st_allow_cancel', true);
            $html = '';
            if ($cancel_policy == 'on'):
                if (empty($font_size)) $font_size = '3';
                if (!empty($title)) {
                    $html .= '<h'. esc_attr($font_size) .'>'. esc_html($title) .'</h'. esc_attr($font_size) .'>';
                }
                ?>
                <p>
                    <?php
                    $cancel_policy_day = get_post_meta(get_the_ID(), 'st_cancel_number_days', true);
                    $cancel_policy_amount = get_post_meta(get_the_ID(), 'st_cancel_percent', true);
                    $html .= sprintf(__('Cancellation within %s Day(s) before the date of arrival %s%s will be charged.', ST_TEXTDOMAIN), $cancel_policy_day, $cancel_policy_amount, '%');
                    ?>
                </p>
                <?php
            endif;
            return $html;
        }
        return false;
    }
}
st_reg_shortcode('st_cancellation', 'st_cancellation');






