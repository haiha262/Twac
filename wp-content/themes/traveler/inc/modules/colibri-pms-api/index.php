<?php
if(!class_exists('ST_Colobri_API')){
    class ST_Colobri_API{

        static $_inst;

        protected $dir;

        function __construct()
        {
            $this->dir = dirname(__FILE__);

            $this->loadModels();
            $this->loadHelpers();
            $this->loadController();

            add_action('init', array($this, 'loadElements'));
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
            return "inc/modules/colibri-pms-api/".$url;
        }

        static function inst(){
            if(!self::$_inst)
                self::$_inst = new self();

            return self::$_inst;
        }

    }

    ST_Colobri_API::inst();

}