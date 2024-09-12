<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Statement extends BASE_Controller {
    const MENU_ID = 7;

    public function __construct() {
        parent::__construct();
        if(!$this->ion_auth->is_admin()){
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->coop) {
            if ($this->coop->status === 'processing') {
                redirect('settings');
            }
        }
        $this->licence_cheker($this->coop, $this->app_settings);
    }

    public function index(){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('member_id', lang('member_id'), 'trim|required');
        $this->form_validation->set_rules('statement_type', lang('statement_type'), 'trim|required');
        $this->form_validation->set_rules('start_date', lang('start_date'), 'trim|required');
        $this->form_validation->set_rules('end_date', lang('end_date'), 'trim|required');
        $statement_type = $this->input->post('statement_type');
        if($statement_type ==='loan'){
             $this->form_validation->set_rules('loan_type', lang('loan_type'), 'trim|required');
        }
        if($statement_type ==='savings'){
             $this->form_validation->set_rules('savings_type', lang('savings_type'), 'trim|required');
        }
        
        if ($this->form_validation->run()) {
            if($statement_type ==='loan'){
                $this->loan_statement($this->input->post());
            }
            
            if($statement_type ==='savings'){
                $this->savings_statement($this->input->post());
            }
            
        } else {
            
            $this->data['loan_types'] = $this->common->get_all_these('loan_types', ['coop_id' => $this->coop->id]);
            $this->data['savings_type'] = $this->common->get_all_these('savings_types', ['coop_id' => $this->coop->id]);
            
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->data['title'] = lang('statement');
            $this->data['controller'] = lang('statement');
            $this->layout->set_app_page('statement/index', $this->data);
        }
    }
    
    public function loan_statement($post){
        $user = $this->data['user'] = $this->common->get_this('users',['username'=>$post['member_id']]);
        $this->data['loan_type'] = $this->common->get_this('loan_types',['id'=>$post['loan_type']]);
        $this->data['active_loan'] = $this->common->get_this('loans',['loan_type_id'=>$post['loan_type'], 'status'=>'disbursed', 'user_id'=>$user->id]);
        $this->data['start_date'] = $this->utility->just_date($post['start_date'], FALSE);
        $this->data['end_date'] = $this->utility->just_date($post['end_date'], FALSE);
        if(!$this->data['active_loan']){
            $this->session->set_flashdata('error', lang('no_active_loan'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        $where = [
            'loan_repayment.user_id'=>$this->data['user']->id,
            'users.coop_id' => $this->coop->id, 
            'loan_repayment.status' => 'paid',
            'loan_repayment.loan_id' => $this->data['active_loan']->id,
            'loan_repayment.loan_type_id' => $post['loan_type'],
            'loan_repayment.created_on>=' => $post['start_date'],
            'loan_repayment.created_on<=' => $post['end_date'],
            ];
        $this->data['loan_repayment'] = $this->info->get_loan_repayment($where);
        $this->data['active_loan'] = $this->common->get_this('loans',['loan_type_id'=>$post['loan_type'], 'status'=>'disbursed','user_id' => $user->id]);
        $this->data['title'] = $this->data['loan_type']->name;
        $this->data['controller'] = lang('statement');
        $this->layout->set_app_page('statement/loan', $this->data);
        
    }
    
    public function savings_statement($post){
        $this->data['user'] = $this->common->get_this('users',['username'=>$post['member_id']]);
        $this->data['savings_type'] = $this->common->get_this('savings_types',['id'=>$post['savings_type']]);
        $this->data['start_date'] = $this->utility->just_date($post['start_date'], FALSE);
        $this->data['end_date'] = $this->utility->just_date($post['end_date'], FALSE);
        
        $where = [
            'savings.user_id'=>$this->data['user']->id,
            'users.coop_id' => $this->coop->id, 
            'savings.status' => 'paid',
            'savings.savings_type' => $post['savings_type'],
            'savings.created_on>=' => $post['start_date'],
            'savings.created_on<=' => $post['end_date'],
            ];
        $where_credit = ['savings_type'=>$post['savings_type'], 'user_id' =>$this->data['user']->id, 'tranx_type' => 'credit', 'coop_id' => $this->coop->id,];
        $where_debit = ['savings_type'=>$post['savings_type'], 'user_id' => $this->data['user']->id, 'tranx_type' => 'debit', 'coop_id' => $this->coop->id,];

        $this->data['credit'] = $this->common->sum_this('savings', $where_credit, 'amount')->amount;
        $this->data['debit'] = $this->common->sum_this('savings', $where_debit, 'amount')->amount;
        
        $this->data['savings'] = $this->info->get_savings_statement($where);
        
//        var_dump($this->data['savings']);exit;
        $this->data['title'] = $this->data['savings_type']->name;
        $this->data['controller'] = lang('statement');
        $this->layout->set_app_page('statement/savings', $this->data);
    }
    
}
