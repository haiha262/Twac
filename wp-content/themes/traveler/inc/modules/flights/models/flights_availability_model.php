<?php
/**
 * Created by wpbooking.
 * Developer: nasanji
 * Date: 6/7/2017
 * Version: 1.0
 */


if(!class_exists('ST_Flight_Availability_Models')){
    class ST_Flight_Availability_Models{

        static $_inst;
        protected $_table_version = "1.0.1";
        protected $_table_name = '';

        function __construct()
        {
            $this->_table_name = 'st_flight_availability';
            add_action( 'after_setup_theme', array($this, '_check_table_activity' ));
        }

        function _check_table_activity()
        {
            $dbhelper = new DatabaseHelper( $this->_table_version );
            $dbhelper->setTableName( $this->_table_name );
            $column = array(
                'id'           => array(
                    'type'           => 'bigint',
                    'length'         => 9,
                    'AUTO_INCREMENT' => TRUE
                ),
                'post_id' => array(
                    'type' => 'INT',
                    'length' => 11
                ),
                'start_date' => array(
                    'type' => 'varchar',
                    'length' => 255
                ),
                'end_date' => array(
                    'type' => 'varchar',
                    'length' => 255
                ),
                'eco_price' => array(
                    'type' => 'varchar',
                    'length' => 255
                ),
                'business_price' => array(
                    'type' => 'varchar',
                    'length' => 255
                ),
                'status' => array(
                    'type' => 'varchar',
                    'length' => 255
                )
            );

            $column = apply_filters( 'st_change_column_st_flight_availability', $column );

            $dbhelper->setDefaultColums( $column );
            $dbhelper->check_meta_table_is_working( 'st_flight_availability_table_version' );

            return array_keys( $column );
        }

        function get_price_data($post_id, $start){
            global $wpdb;

            $table = $wpdb->prefix.$this->_table_name;

            $sql = "SELECT * FROM {$table} WHERE post_id=%d AND `start_date`=%s";

            $res = $wpdb->get_row($wpdb->prepare($sql,$post_id, $start), ARRAY_A);

            return $res;
        }

        static function inst(){
            if(!self::$_inst)
                self::$_inst = new self();

            return self::$_inst;
        }
    }

    ST_Flight_Availability_Models::inst();
}