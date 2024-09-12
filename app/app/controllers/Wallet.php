<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Wallet extends BASE_Controller {
    const MENU_ID =5;
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
        $this->load->library('paystack');
        $this->load->library('flutter');
        $this->licence_cheker($this->coop, $this->app_settings);
    }

    public function index() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('start_date', lang("start_date"), 'trim|required');
        $this->form_validation->set_rules('end_date', lang("end_date"), 'trim|required');
        $where = [
            'wallet.coop_id' => $this->coop->id,
            'wallet.created_on>=' => $this->utility->get_this_year('start'),
            'wallet.created_on<=' => $this->utility->get_this_year('end'),
        ];
        if ($this->form_validation->run()) {
            $where['wallet.created_on>='] = $this->input->post('start_date');
            $where['wallet.created_on<='] = $this->input->post('end_date');
            $this->data['wallet'] = $this->info->get_wallet($where);
            $where['status'] = 'successful';
            $where['tranx_type'] = 'credit';
            $this->data['total_wallet_credit'] = $this->common->sum_this('wallet', $where, 'amount');
            $where['tranx_type'] = 'debit';
            $this->data['total_wallet_debit'] = $this->common->sum_this('wallet', $where, 'amount');
            $this->data['start_date'] = $this->input->post('start_date');
            $this->data['end_date'] = $this->input->post('end_date');
        } else {
            $this->data['wallet'] = $this->info->get_wallet($where, 1000);
            $where['tranx_type'] = 'credit';
            $where['status'] = 'successful';
            $this->data['total_wallet_credit'] = $this->common->sum_this('wallet', $where, 'amount');
            $where['tranx_type'] = 'debit';
            $this->data['total_wallet_debit'] = $this->common->sum_this('wallet', $where, 'amount');
            $this->data['start_date'] = $this->utility->get_this_year('start');
            $this->data['end_date'] = $this->utility->get_this_year('end');
        }
        
        $this->data['title'] = lang('requested_wallet');
        $this->data['controller'] = lang('wallet');
        $this->layout->set_app_page('wallet/index', $this->data);
    }

    public function preview($id) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $id = $this->utility->un_mask($id);
        $this->data['wallet'] = $this->info->get_wallet_details(['wallet.id' => $id, 'users.coop_id' => $this->coop->id]);
        $this->data['title'] = lang('print');
        $this->data['controller'] = lang('wallet');
        $this->layout->set_app_page('wallet/preview', $this->data);
    }

    public function approve($id){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $id = $this->utility->un_mask($id);
        $wallet = $this->common->get_this('wallet', ['id'=>$id, 'coop_id'=>$this->coop->id]);
        $user = $this->common->get_this('users', ['id'=>$wallet->user_id, 'coop_id'=>$this->coop->id]);
        $old_bal = $this->utility->get_wallet_bal($wallet->user_id, $this->coop->id);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('wallet_loading') . ' ' . lang('approve'),
        ];
        $this->common->start_trans();
        $this->common->update_this('wallet', ['coop_id' => $this->coop->id, 'id' => $id], ['status' => 'successful']);
        $this->common->add('activities', $activities);
        if ($this->coop->sms_notice == 'on') {
            $content = "Approved Wallet Loading"
                . "\n" . "REF: " . $wallet->tranx_ref
                . "\n" . "DATE: " . $this->utility->just_date(date('Y-m-d H:i:s'), true)
                . "\n" . "AMT: NGN" . number_format($wallet->amount, 2)
                . "\n" . "Av.BAL: NGN" . number_format($wallet->amount + $old_bal, 2);
            $this->fivelinks->send_SMS($user->phone, $content);
        }
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('wallet_loading') . ' ' . lang('approve'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function verify($id){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $id = $this->utility->un_mask($id);
        $trnx = $this->common->get_this('wallet', ['id'=>$id, 'coop_id'=>$this->coop->id]);
        if($trnx->gate_way_id == 1){ //paystack
            $this->verify_paystack($trnx);
        }
        if($trnx->gate_way_id == 2){ //paystack
            $this->verify_flutter($trnx);
        }
    }

    public function verify_paystack($trnx){
        $verify = $this->paystack->verify_paystack_payment($this->coop->paystack_secrete, $trnx->tranx_ref);
        if($verify['data']['status'] === 'success'){
            $where = ['id'=>$trnx->id, 'coop_id'=> $this->coop->id];

            if($this->common->get_this('wallet', $where)){
                $this->common->update_this('wallet',$where, ['status'=>'successful']);
                $this->session->set_flashdata('message', lang('tranx_verified'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        }else{
            $this->session->set_flashdata('error', 'Payment '. $verify['data']['status']);
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function verify_flutter($trnx){
        $verify = $this->flutter->verify_flutter_payment($this->coop->flutter_secrete, $trnx->tranx_ref);
        if($verify['status']=='success'){
            $where = ['id' => $trnx->id, 'coop_id' => $this->coop->id];
            if($this->common->get_this('wallet', $where)){
                $this->common->update_this('wallet',$where, ['status'=>'successful', 'amount'=>$verify['data']['amount_settled']]);
                $this->session->set_flashdata('message', lang('tranx_verified'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        } else {
            $this->session->set_flashdata('error', $verify['message']);
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

}
