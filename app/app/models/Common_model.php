<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Common_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    //add data to any table
    public function add($table, $data) {
        if ($this->db->insert($table, $data)) {
            return $this->db->insert_id();
        }
        return FALSE;
    }

    public function update_this($table, $where, $data) {
        $this->db->where($where);
        return $this->db->update($table, $data);
    }
    

    //get all data in a table
    public function get_all($table) {
        $query = $this->db->select('*')
                ->from($table)
                ->get();
        return $query->result();
    }

    public function get_this($table, $where) {
        $query = $this->db->select('*')
                ->where($where)
                ->get($table);
        return $query->row();
    }

    public function get_all_these($table, $where) {
        $query = $this->db->select('*')
                ->from($table)
                ->where($where)
                ->get();
        return $query->result();
    }

    public function delete_this($table, $where) {
        $this->db->where($where);
        $this->db->delete($table);
        return $this->db->affected_rows();
    }

    public function count_this($table, $where) {
        $this->db->select('*')
                ->from($table)
                ->where($where);
        return $this->db->count_all_results();
    }
    
    public function sum_this($table, $where, $field, $range = null) {
        $this->db->select_sum($field)->where($where);
        if($range){
            $this->db->where($range);
        }
        return $this->db->get($table)->row();
    }
    
    public function sum($table,$field) {
        $this->db->select_sum($field);
        return $this->db->get($table)->row();
    }

    public function count($table) {
        $this->db->select('*')
                ->from($table);
        return $this->db->count_all_results();
    }

    public function link_not_expire($token) {
        $query = $this->db->select('*')
                ->from('users')
                ->where('pwd_token', $token)
                ->where('link_expire >', time())
                ->get();
        return $query->row();
    }

    function start_trans($test_mode = FALSE) {
        $this->db->trans_start($test_mode);
    }

    function finish_trans() {
        $this->db->trans_complete();
    }

    function status_trans() {
        return $this->db->trans_status();
    }

    function get_limit($table, $where = false, $limit = 5, $order = false, $direction = 'ASC') {
        if ($where) {
            $this->db->where($where);
        }
        if ($order) {
            $this->db->order_by($order, $direction);
        }
        $query = $this->db->select('*')
                ->from($table)
                ->limit($limit)
                ->get();
        return $query->result();
    }

}
