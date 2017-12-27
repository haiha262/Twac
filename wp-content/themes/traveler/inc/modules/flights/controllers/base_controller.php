<?php
/**
 * Created by wpbooking.
 * Developer: nasanji
 * Date: 6/7/2017
 * Version: 1.0
 */

if(!class_exists('ST_Flight_Base_Controller')){
    class ST_Flight_Base_Controller{
        static $_inst;

        public function __construct()
        {

            add_action('admin_enqueue_scripts', array($this, '_admin_enqueue_scripts'));

            add_action('wp_enqueue_scripts', array($this, '_enqueue_scripts'));

            add_filter('st_option_tree_settings', array($this, '_add_flight_settings'));

        }

        function _admin_enqueue_scripts(){
            //Fix error js select2 min when add new product product vari...
            if(!in_array(get_post_type( ) , array('product', 'shop_order'))) {
                wp_enqueue_script( 'select2.js', get_template_directory_uri() . '/js/select2/select2.min.js', [ 'jquery' ], NULL, TRUE );
            }
            $lang      = get_locale();
            $lang_file = get_template_directory() . '/js/select2/select2_locale_' . $lang . '.js';
            if ( file_exists( $lang_file ) ) {
                wp_enqueue_script( 'select2-lang', get_template_directory_uri() . '/js/select2/select2_locale_' . $lang . '.js', [ 'jquery', 'select2.js' ], null, true );
            } else {
                $locale    = TravelHelper::get_minify_locale( $lang );
                $lang_file = get_template_directory_uri() . '/js/select2/select2_locale_' . $locale . '.js';
                if ( file_exists( $lang_file ) ) {
                    wp_enqueue_script( 'select2-lang', get_template_directory_uri() . '/js/select2/select2_locale_' . $locale . '.js', [ 'jquery', 'select2.js' ], null, true );
                }
            }
            wp_enqueue_style( 'st-select2', get_template_directory_uri() . '/js/select2/select2.css' );
            wp_register_script('st-flight-admin', get_template_directory_uri().'/inc/modules/flights/js/flight_admin.js', array('jquery'), null, true);
            wp_register_style('st-flight-admin-css', get_template_directory_uri().'/inc/modules/flights/css/flight_admin.css');
        }

        function _enqueue_scripts(){

            wp_enqueue_script('st-custom-flight-js', get_template_directory_uri().'/inc/modules/flights/js/custom_flight.js', array('jquery'),null, true);

            wp_enqueue_style('st-flight-css', get_template_directory_uri().'/inc/modules/flights/css/flight-style.css');
            wp_register_style('st-flight-select-css', get_template_directory_uri().'/inc/modules/flights/css/st-flight-select.css');
            wp_register_script('st-flight-select-js', get_template_directory_uri().'/inc/modules/flights/js/st-flight-select.js',array('jquery'),null, true);
            wp_register_script('flight-create.js', get_template_directory_uri().'/inc/modules/flights/js/flight-create.js',array('jquery'),null, true);

        }

        function _add_flight_settings($settings){

            $sections = $settings['sections'];

            $sections1 = array_slice($sections, 0, 12);
            $sections2 = array_slice($sections, 12, count($sections) - 1);
            array_push($sections1,
                array(
                    'id'    => 'option_flight' ,
                    'title' => __( '<i class="fa fa-fighter-jet "></i> Flight Options' , ST_TEXTDOMAIN )
                )
            );
            $sections = array_merge($sections1, $sections2);
            $settings['sections'] = $sections;

            array_push($settings['settings'],
                array(
                    'id'       => 'flight_search_fields' ,
                    'label'    => __( 'Flight Search Fields' , ST_TEXTDOMAIN ) ,
                    'desc'     => __( 'You can add, sort search fields for tour' , ST_TEXTDOMAIN ) ,
                    'type'     => 'Slider' ,
                    'section'  => 'option_flight' ,
                    'settings' => array(
                        array(
                            'id'       => 'flight_field_search' ,
                            'label'    => __( 'Field Type' , ST_TEXTDOMAIN ) ,
                            'type'     => 'select' ,
                            'operator' => 'and' ,
                            'choices'  => ST_Flights_Controller::inst()->get_search_fields_name() ,
                        ) ,
                        array(
                            'id'       => 'placeholder' ,
                            'label'    => __( 'Placeholder' , ST_TEXTDOMAIN ) ,
                            'desc'     => __( 'Placeholder' , ST_TEXTDOMAIN ) ,
                            'type'     => 'text' ,
                            'operator' => 'and' ,
                        ) ,
                        array(
                            'id'       => 'layout_col' ,
                            'label'    => __( 'Layout 1 size' , ST_TEXTDOMAIN ) ,
                            'type'     => 'select' ,
                            'operator' => 'and' ,
                            'choices'  => array(
                                array(
                                    'value' => '1' ,
                                    'label' => __( 'column 1' , ST_TEXTDOMAIN )
                                ) ,
                                array(
                                    'value' => '2' ,
                                    'label' => __( 'column 2' , ST_TEXTDOMAIN )
                                ) ,
                                array(
                                    'value' => '3' ,
                                    'label' => __( 'column 3' , ST_TEXTDOMAIN )
                                ) ,
                                array(
                                    'value' => '4' ,
                                    'label' => __( 'column 4' , ST_TEXTDOMAIN )
                                ) ,
                                array(
                                    'value' => '5' ,
                                    'label' => __( 'column 5' , ST_TEXTDOMAIN )
                                ) ,
                                array(
                                    'value' => '6' ,
                                    'label' => __( 'column 6' , ST_TEXTDOMAIN )
                                ) ,
                                array(
                                    'value' => '7' ,
                                    'label' => __( 'column 7' , ST_TEXTDOMAIN )
                                ) ,
                                array(
                                    'value' => '8' ,
                                    'label' => __( 'column 8' , ST_TEXTDOMAIN )
                                ) ,
                                array(
                                    'value' => '9' ,
                                    'label' => __( 'column 9' , ST_TEXTDOMAIN )
                                ) ,
                                array(
                                    'value' => '10' ,
                                    'label' => __( 'column 10' , ST_TEXTDOMAIN )
                                ) ,
                                array(
                                    'value' => '11' ,
                                    'label' => __( 'column 11' , ST_TEXTDOMAIN )
                                ) ,
                                array(
                                    'value' => '12' ,
                                    'label' => __( 'column 12' , ST_TEXTDOMAIN )
                                ) ,
                            ) ,
                            'std'      => 4
                        ) ,
                        array(
                            'id'       => 'layout2_col' ,
                            'label'    => __( 'Layout 2 Size' , ST_TEXTDOMAIN ) ,
                            'type'     => 'select' ,
                            'operator' => 'and' ,
                            'std'      => 4 ,
                            'choices'  => array(
                                array(
                                    'value' => '1' ,
                                    'label' => __( 'column 1' , ST_TEXTDOMAIN )
                                ) ,
                                array(
                                    'value' => '2' ,
                                    'label' => __( 'column 2' , ST_TEXTDOMAIN )
                                ) ,
                                array(
                                    'value' => '3' ,
                                    'label' => __( 'column 3' , ST_TEXTDOMAIN )
                                ) ,
                                array(
                                    'value' => '4' ,
                                    'label' => __( 'column 4' , ST_TEXTDOMAIN )
                                ) ,
                                array(
                                    'value' => '5' ,
                                    'label' => __( 'column 5' , ST_TEXTDOMAIN )
                                ) ,
                                array(
                                    'value' => '6' ,
                                    'label' => __( 'column 6' , ST_TEXTDOMAIN )
                                ) ,
                                array(
                                    'value' => '7' ,
                                    'label' => __( 'column 7' , ST_TEXTDOMAIN )
                                ) ,
                                array(
                                    'value' => '8' ,
                                    'label' => __( 'column 8' , ST_TEXTDOMAIN )
                                ) ,
                                array(
                                    'value' => '9' ,
                                    'label' => __( 'column 9' , ST_TEXTDOMAIN )
                                ) ,
                                array(
                                    'value' => '10' ,
                                    'label' => __( 'column 10' , ST_TEXTDOMAIN )
                                ) ,
                                array(
                                    'value' => '11' ,
                                    'label' => __( 'column 11' , ST_TEXTDOMAIN )
                                ) ,
                                array(
                                    'value' => '12' ,
                                    'label' => __( 'column 12' , ST_TEXTDOMAIN )
                                ) ,
                            )
                        ) ,

                        array(
                            'id'       => 'is_required' ,
                            'label'    => __( 'Field required' , ST_TEXTDOMAIN ) ,
                            'type'     => 'on-off' ,
                            'operator' => 'and' ,
                            'std'      => 'on' ,
                        ) ,
                    ) ,
                    'std'      => array(
                        array(
                            'title'              => __('From', ST_TEXTDOMAIN) ,
                            'layout_col'         => 6 ,
                            'layout2_col'        => 3 ,
                            'flight_field_search' => 'origin',
                            'placeholder'    => __("Location / Airport" , ST_TEXTDOMAIN)
                        ) ,
                        array(
                            'title'              => __('To', ST_TEXTDOMAIN) ,
                            'layout_col'         => 6 ,
                            'layout2_col'        => 3 ,
                            'flight_field_search' => 'destination',
                            'placeholder'    => __("Location / Airport" , ST_TEXTDOMAIN)
                        ) ,
                        array(
                            'title'              => __('Departing', ST_TEXTDOMAIN) ,
                            'layout_col'         => 3 ,
                            'layout2_col'        => 2 ,
                            'flight_field_search' => 'depart'
                        ) ,
                        array(
                            'title'              => __('Returning', ST_TEXTDOMAIN) ,
                            'layout_col'         => 3 ,
                            'layout2_col'        => 2 ,
                            'flight_field_search' => 'return'
                        ) ,
                        array(
                            'title'              => __('Passengers', ST_TEXTDOMAIN) ,
                            'layout_col'         => 6 ,
                            'layout2_col'        => 2 ,
                            'flight_field_search' => 'passengers'
                        ) ,
                    )
                ),
                array(
                    'type' => 'page-select',
                    'id' => 'flight_search_result_page',
                    'label' => esc_html__('Flight Search Result Page', ST_TEXTDOMAIN),
                    'desc' => esc_html__('Select page to show hotel results search page', ST_TEXTDOMAIN),
                    'section'  => 'option_flight'
                )
            );

            return $settings;
        }


        static function inst(){
            if(!self::$_inst)
                self::$_inst = new self();

            return self::$_inst;
        }
    }

    ST_Flight_Base_Controller::inst();
}