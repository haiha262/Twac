<?php
/**
 * Created by wpbooking.
 * Developer: nasanji
 * Date: 6/7/2017
 * Version: 1.0
 */


if(!class_exists('ST_Flight_Location_Models')){
    class ST_Flight_Location_Models{

        static $_inst;
        protected $_table_version = "2.0.0";
        protected $_table_name = '';

        function __construct()
        {
            $this->_table_name = 'st_flight_location';
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
                'airport_id' => array(
                    'type' => 'INT',
                    'length' => 11
                ),
                'map_lat' => array(
                    'type' => 'varchar',
                    'length' => 255
                ),
                'map_lng' => array(
                    'type' => 'varchar',
                    'length' => 255
                ),
                'map_zoom' => array(
                    'type' => 'varchar',
                    'length' => 255
                ),
                'map_address' => array(
                    'type' => 'text',
                    'length' => 500
                ),
                'map_country' => array(
                    'type' => 'varchar',
                    'length' => 20
                )
            );

            $column = apply_filters( 'st_change_column_st_flight_location', $column );

            $dbhelper->setDefaultColums( $column );
            $dbhelper->check_meta_table_is_working( 'st_flight_location_table_version' );

            return array_keys( $column );
        }

        function get_data($airport_id){
            global $wpdb;
            $table = $wpdb->prefix.$this->_table_name;

            $sql = "SELECT * FROM {$table} WHERE airport_id=%s";

            $res = $wpdb->get_row($wpdb->prepare($sql, $airport_id));

            if(!empty($res) && count($res) > 0){
                return $res;
            }
            return false;
        }

        function insert_data($data){
            global $wpdb;

            $table = $wpdb->prefix.$this->_table_name;
            $wpdb->insert($table, $data);
        }

        function update_data($data, $where){
            global $wpdb;
            $table = $wpdb->prefix.$this->_table_name;

            $wpdb->update($table, $data, $where);
        }

        function delete($airport_id){
            global $wpdb;
            $table = $wpdb->prefix.$this->_table_name;

            $wpdb->delete($table, ['airport_id' => $airport_id]);
        }

        function get_id($airport_id){
            global $wpdb;
            $table = $wpdb->prefix.$this->_table_name;
            $sql = "SELECT id FROM {$table} WHERE airport_id=%d";
            $res = $wpdb->get_var($wpdb->prepare($sql, $airport_id));

            return $res;
        }


        static function inst(){
            if(!self::$_inst)
                self::$_inst = new self();

            return self::$_inst;
        }
    }

    ST_Flight_Location_Models::inst();
}