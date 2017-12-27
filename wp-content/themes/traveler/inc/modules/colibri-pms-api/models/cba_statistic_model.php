<?php
if (!class_exists('ST_CBA_Statistic_Models')) {
    class ST_CBA_Statistic_Models
    {

        static $_inst;
        protected $_table_version = "1.0.9";
        protected $_table_name = '';

        function __construct()
        {
            $this->_table_name = 'st_cba_statistic';
            add_action('after_setup_theme', array($this, '_check_table_activity'));
        }

        function _check_table_activity()
        {
            $dbhelper = new DatabaseHelper($this->_table_version);
            $dbhelper->setTableName($this->_table_name);
            $column = array(
                'id' => array(
                    'type' => 'bigint',
                    'length' => 9,
                    'AUTO_INCREMENT' => TRUE
                ),
                'user_id' => array(
                    'type' => 'INT',
                    'length' => 11
                ),
                'status' => array(
                    'type' => 'varchar',
                    'length' => 255
                ),
                'booking_date' => array(
                    'type' => 'varchar',
                    'length' => 255
                ),
                'booking_from' => array(
                    'type' => 'varchar',
                    'length' => 255
                ),
                'booking_to' => array(
                    'type' => 'varchar',
                    'length' => 255
                ),
                'modify_date' => array(
	                'type' => 'varchar',
	                'length' => 255
                ),
                'data_user' => array(
                    'type' => 'text',
                    'length' => 500
                ),
                'data_card' => array(
                    'type' => 'text',
                    'length' => 500
                ),
                'data_res' => array(
                    'type' => 'text',
                    'length' => 500
                ),
                'data' => array(
                    'type' => 'text',
                    'length' => 500
                )
            );

            $column = apply_filters('st_change_column_st_cba_statistic', $column);

            $dbhelper->setDefaultColums($column);
            $dbhelper->check_meta_table_is_working('st_cba_statistic_table_version');

            return array_keys($column);
        }

        function insert_data($data)
        {
            global $wpdb;

            $table = $wpdb->prefix . $this->_table_name;

            $wpdb->insert($table, $data);
        }

        function update_data($data, $where)
        {
            global $wpdb;
            $table = $wpdb->prefix . $this->_table_name;

            $wpdb->update($table, $data, $where);
        }

        function check_data_for_user($id, $user_id){
            global $wpdb;
            $table = $wpdb->prefix.$this->_table_name;
            $sql = "SELECT count(*) FROM {$table} WHERE id={$id} AND user_id={$user_id}";
            return $wpdb->get_var($sql);
        }

        /**
         * @param $where
         */
        function delete_data($where){
            global $wpdb;
            $table = $wpdb->prefix.$this->_table_name;
            $wpdb->delete( $table, $where );
        }

        function get_data($stas_id){
            global $wpdb;
            $table = $wpdb->prefix.$this->_table_name;

            $sql = "SELECT * FROM {$table} WHERE id=%s";

            $res = $wpdb->get_row($wpdb->prepare($sql, $stas_id));

            if(!empty($res) && count($res) > 0){
                return $res;
            }
            return false;
        }

        function get_data_by_date($start, $end){
            global $wpdb;
            $table = $wpdb->prefix.$this->_table_name;

            $sql = "SELECT * FROM {$table} WHERE booking_from >= {$start} AND booking_to <= {$end}";

            $res = $wpdb->get_results($sql, ARRAY_A  );

            if(!empty($res) && count($res) > 0){
                return $res;
            }
            return false;
        }

        function get_data_by_date_and_id($id, $start, $end){
            global $wpdb;
            $table = $wpdb->prefix.$this->_table_name;

            $sql = "SELECT * FROM {$table} WHERE id IN ({$id}) AND booking_from >= {$start} AND booking_to <= {$end}";

            $res = $wpdb->get_results($sql, ARRAY_A  );

            if(!empty($res) && count($res) > 0){
                return $res;
            }
            return false;
        }

        static function inst()
        {
            if (!self::$_inst)
                self::$_inst = new self();

            return self::$_inst;
        }
    }

    ST_CBA_Statistic_Models::inst();
}