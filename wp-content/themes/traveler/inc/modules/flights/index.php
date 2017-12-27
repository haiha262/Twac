<?php
/**
 * Created by wpbooking.
 * Developer: nasanji
 * Date: 6/7/2017
 * Version: 1.0
 */

if(!class_exists('ST_Traveler_Flights')){
    class ST_Traveler_Flights{

        static $_inst;

        protected $dir;

        function __construct()
        {
            $this->dir = dirname(__FILE__);

            add_filter('st_custom_list_post_type_tree', array($this, '_add_custom_post_type_setting'));

            if(st_check_service_available( 'st_flight' )) {
                $this->loadModels();
                $this->loadHelpers();
                $this->loadController();

                add_action('init', array($this, 'loadElements'));
            }
        }

        function loadModels()
        {
            $models=glob($this->dir.'/models/*');

            if(!is_array($models) or empty($models)) return false;

            if(!empty($models))
            {
                foreach($models as $key => $value)
                {
                    $dirname = basename($value, '.php');

                    $file = $this->dir_name('models/'.$dirname);

                    get_template_part($file);
                }
            }

            return true;
        }

        function loadController()
        {
            $files = glob($this->dir.'/controllers/*');

            if(!is_array($files) or empty($files)) return false;

            if(!empty($files))
            {
                foreach($files as $key => $value)
                {
                    $dirname = basename($value, '.php');

                    $file = $this->dir_name('controllers/'.$dirname);

                    get_template_part($file);
                }
            }

            return true;
        }

        function loadHelpers()
        {
            $files = glob($this->dir.'/helpers/*');

            if(!is_array($files) or empty($files)) return false;

            if(!empty($files))
            {
                foreach($files as $key => $value)
                {
                    $dirname = basename($value, '.php');

                    $file = $this->dir_name('helpers/'.$dirname);

                    get_template_part($file);
                }
            }

            return true;
        }

        function loadElements(){

            $files = glob($this->dir.'/vc-elements/*');

            if(!is_array($files) or empty($files)) return false;

            if(!empty($files)){
                foreach($files as $key => $val){
                    $dirname = basename($val, '.php');

                    $file = $this->dir_name('vc-elements/'.$dirname);

                    if (!function_exists('vc_map') or !function_exists('st_reg_shortcode')) return false;

                    get_template_part($file);
                }
            }

            return true;
        }

        function dir_name($url=false)
        {
            return "inc/modules/flights/".$url;
        }

        function _add_custom_post_type_setting($arr){

            array_push($arr,
                array(
                    'label' => __( 'Flight' , ST_TEXTDOMAIN ) ,
                    'value' => 'st_flight'
                )
            );
            return $arr;
        }

        static function inst(){
            if(!self::$_inst)
                self::$_inst = new self();

            return self::$_inst;
        }

    }

    ST_Traveler_Flights::inst();

}