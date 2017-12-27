<?php
if(!class_exists('ST_Colibri_Base_Controller')) {
    class ST_Colibri_Base_Controller
    {
        static $_inst;
        static $order_by;

        public function __construct()
        {
            add_action('wp_enqueue_scripts', array($this, '_enqueue_scripts'));
	        add_action('admin_enqueue_scripts', array($this, 'load_admin_scripts'));

            self::$order_by = [
                'ID' => [
                    'key' => 'default',
                    'name' => __('Default', ST_TEXTDOMAIN)
                ],
                'price_asc' => [
                    'key' => 'price_asc',
                    'name' => __('Price ', ST_TEXTDOMAIN) . ' (<i class="fa fa-long-arrow-down"></i>)'
                ],
                'price_desc' => [
                    'key' => 'price_desc',
                    'name' => __('Price ', ST_TEXTDOMAIN) . ' (<i class="fa fa-long-arrow-up"></i>)'
                ],
                'name_asc' => [
                    'key' => 'name_asc',
                    'name' => __('Name (A-Z)', ST_TEXTDOMAIN)
                ],
                'name_desc' => [
                    'key' => 'name_desc',
                    'name' => __('Name (Z-A)', ST_TEXTDOMAIN)
                ],

            ];
        }

        public static function getOrderby()
        {
            return self::$order_by;
        }

        function _enqueue_scripts(){
            wp_enqueue_style('st-colibri-api', get_template_directory_uri().'/css/colibri-api.css');
            wp_enqueue_script('st-filter-colibri-api', get_template_directory_uri().'/js/filter-ajax-colibri.js');
            wp_enqueue_style('st-colibri-api-select2', get_template_directory_uri().'/js/select2/select2.css');
            wp_enqueue_script('st-filter-colibri-api-select2', get_template_directory_uri().'/js/select2/select2.min.js', [ 'jquery' ], null, true );
        }

	    public function load_admin_scripts($hook){
            if($hook != 'toplevel_page_cba-statistic-menu') {
                return;
            }
		    wp_enqueue_style( 'st-colibri-api-admin', get_template_directory_uri().'/css/admin-colibri-api.css' );
            wp_enqueue_style( 'bootstrap-datepicker.css', get_template_directory_uri().'/css/bootstrap-datepicker.temp.css.css' );
		    wp_enqueue_script( 'st-colibri-api-admin', get_template_directory_uri().'/js/admin/admin-colibri-api.js' );
            wp_enqueue_script( 'bootstrap-datepicker.js', get_template_directory_uri() . '/js/bootstrap-datepicker-cba.min.js', [ 'jquery' ], null, true );
	    }

        static function inst(){
            if(!self::$_inst)
                self::$_inst = new self();

            return self::$_inst;
        }
    }
    ST_Colibri_Base_Controller::inst();
}