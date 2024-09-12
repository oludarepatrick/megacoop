<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_title_and_subtitle($where = null) {
        $this->db->select("acc_title.id as title_id, acc_title.name as title, acc_title.code as title_code,acc_sub_title.id as sub_title_id,"
                        . " acc_sub_title.name as sub_title, acc_sub_title.code as sub_title_code")
                ->from('acc_title')
                ->join('acc_sub_title', 'acc_sub_title.acc_title_id=acc_title.id');
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get()->result();
    }
    
    public function get_title_and_subtitle_and_value($where = null) {
        $this->db->select("acc_title.id as title_id, acc_title.name as title, acc_title.code as title_code,acc_sub_title.id as sub_title_id,"
                        . " acc_sub_title.name as sub_title, acc_sub_title.code as sub_title_code, acc_value.id as value_id, acc_value.name "
                . "as value_name, acc_value.code as value_code")
                ->from('acc_title')
                ->join('acc_sub_title', 'acc_sub_title.acc_title_id=acc_title.id')
                ->join('acc_value', 'acc_value.acc_sub_title_id=acc_sub_title.id');
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get()->result();
    }

    public function get_acc_values($where = null) {
        $this->db->select("*")
                ->from('acc_value');
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get()->result();
    }

    public function get_acc_titles($where = null) {
        $this->db->select("*")
                ->from('acc_title');
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get()->result();
    }

    public function get_acc_title($where) {
        $this->db->select("*")
                ->from('acc_title')
                ->where($where);
        return $this->db->get()->row();
    }
    
    public function get_acc($where= null) {
        $this->db->select("acc_title.id as title_id, acc_title.name as title_name,"
                        . "acc_sub_title.id as sub_title_id, acc_sub_title.name as sub_title_name,"
                        . "acc_value.id as value_id, acc_value.name as value_name, ledger.amount")
                ->from('acc_title')
                ->order_by('acc_sub_title_id', 'ASC')
                ->join('acc_sub_title', 'acc_sub_title.acc_title_id = acc_title.id')
                ->join('acc_value', 'acc_value.acc_sub_title_id = acc_sub_title.id')
                ->join('ledger', 'ledger.credit_id = acc_value.id');
        if ($where) {
            $this->db->where($where);
        }
        $q = $this->db->get();
        return $q->result();
    }

    public function get_gl_savings_tracker($where = null){
        $this->db->select("gl_savings_tracker.cr as cr_id, gl_savings_tracker.dr as dr_id, 
        savings_types.name as savings_type,  gl_savings_tracker.created_on, acc_value.name as cr_acc, ")
            ->from('gl_savings_tracker')
            ->join('savings_types', 'savings_types.id = gl_savings_tracker.savings_type')
            ->join('acc_value', 'acc_value.id = gl_savings_tracker.cr');
        if ($where) {
            $this->db->where($where);
        }
        $qq = $this->db->get()->result();
        foreach($qq as $q){
            $query = $this->db->select('name')
                ->where('id', $q->dr_id)
                ->get('acc_value');
            $q->dr_acc = $query->row()->name;
        }

        return $qq;
        
    }
    public function get_gl_withdrawal_tracker($where = null){
        $this->db->select("gl_withdrawal_tracker.cr as cr_id, gl_withdrawal_tracker.dr as dr_id, 
        savings_types.name as savings_type,  gl_withdrawal_tracker.created_on, acc_value.name as cr_acc, ")
            ->from('gl_withdrawal_tracker')
            ->join('savings_types', 'savings_types.id = gl_withdrawal_tracker.savings_type')
            ->join('acc_value', 'acc_value.id = gl_withdrawal_tracker.cr');
        if ($where) {
            $this->db->where($where);
        }
        $qq = $this->db->get()->result();
        foreach($qq as $q){
            $query = $this->db->select('name')
                ->where('id', $q->dr_id)
                ->get('acc_value');
            $q->dr_acc = $query->row()->name;
        }

        return $qq;
        
    }
    public function get_gl_loan_tracker($where = null, $table){
        $this->db->select("{$table}.principal_cr as principal_cr_id, {$table}.principal_dr as principal_dr_id,
        interest_cr as interest_cr_id, interest_dr as interest_dr_id ,loan_types.name as loan_type,  {$table}.created_on, 
        acc_value.name as principal_cr,")
            ->from("{$table}")
            ->join('loan_types', "loan_types.id = {$table}.loan_type")
            ->join('acc_value', "acc_value.id = {$table}.principal_cr");
        if ($where) {
            $this->db->where($where);
        }
        $qq = $this->db->get()->result();
        foreach($qq as $q){
            $q->principal_dr = $this->db->select('name')->where('id', $q->principal_dr_id)->get('acc_value')->row()->name;
            $q->interest_dr = $this->db->select('name')->where('id', $q->interest_dr_id)->get('acc_value')->row()->name;
            $q->interest_cr = $this->db->select('name')->where('id', $q->interest_cr_id)->get('acc_value')->row()->name;
        }
        return $qq;
    }
    public function get_gl_credit_sales_tracker($where = null, $table){
        $this->db->select("{$table}.principal_cr as principal_cr_id, {$table}.principal_dr as principal_dr_id,
        interest_cr as interest_cr_id, interest_dr as interest_dr_id ,product_types.name as loan_type,  {$table}.created_on, 
        acc_value.name as principal_cr,")
            ->from("{$table}")
            ->join('product_types', "product_types.id = {$table}.product_type")
            ->join('acc_value', "acc_value.id = {$table}.principal_cr");
        if ($where) {
            $this->db->where($where);
        }
        $qq = $this->db->get()->result();
        foreach($qq as $q){
            $q->principal_dr = $this->db->select('name')->where('id', $q->principal_dr_id)->get('acc_value')->row()->name;
            $q->interest_dr = $this->db->select('name')->where('id', $q->interest_dr_id)->get('acc_value')->row()->name;
            $q->interest_cr = $this->db->select('name')->where('id', $q->interest_cr_id)->get('acc_value')->row()->name;
        }
        return $qq;
    }
}
