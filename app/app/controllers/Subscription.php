<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Subscription extends BASE_Controller {
    const MENU_ID = 13;
    public function __construct() {
        parent::__construct();
        if (!$this->ion_auth->is_admin()) {
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->load->library('paystack');
    }

    public function index() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->data['subscriptions'] = $this->common->get_all('subscription_category');
        $this->data['controller'] = lang('subscription');
        $this->data['title'] = lang('subscription');
        $this->layout->set_app_page('subscription/index', $this->data);
    }
    
    public function history() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->data['subscriptions'] = $this->info->get_subscriptions( ['coop_id'=> $this->coop->id], 1000);
        $this->data['controller'] = lang('subscription');
        $this->data['title'] = lang('history');
        $this->layout->set_app_page('subscription/history', $this->data);
    }

    public function gateway() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('members', lang('members'), 'trim|required|numeric');
        if ($this->form_validation->run()) {
            $members = $this->input->post('members');
            $subscriptions = $this->common->get_this('subscription_category', ['id' => 2]);
            $actual_amt = $members * $subscriptions->amount;
            $paystsck_amount = $this->paystack->get_amount_paystack($actual_amt);
            $this->data['payment'] = (object)[
              'fee' => $paystsck_amount - $actual_amt,  
              'amount' => $actual_amt,  
              'total_amount' => $paystsck_amount,  
            ];
            
            $this->session->set_userdata('actual_amt', $actual_amt);
            $this->session->set_userdata('members', $members);
            $this->data['controller'] = lang('subscription');
            $this->data['title'] = lang('subscription');
            $this->layout->set_app_page('subscription/gateway', $this->data);
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function pay_with_paystack() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $amount = floatval($this->session->userdata('actual_amt'));
        $members = $this->session->userdata('members');
        unset($_SESSION['actual_paid']);
        if ($amount > 0) {
            $pay_data = [
                'email'=>($this->user->email)?$this->user->email:'noemail@noemail.com',
                'amount'=> $this->paystack->get_amount_paystack($amount) * 100,
                'currency'=> 'NGN',
                'reference'=> strtoupper(str_replace(' ', '', $this->coop->coop_name)). date('dYmHis').'SUBSCRIPTION',
                'bearer' =>'account',
                'callback_url' =>base_url() . 'subscription/verify_paystack',
            ];
            $subs_data = [
                'coop_id'=> $this->coop->id,
                'tranx_ref'=> $pay_data['reference'],
                'referrer_code'=> $this->coop->referrer_code,
                'total_amount'=> $amount,
                'subscription_cat_id'=> 2,
                'rate'=> 1000,
                'unit'=> $members,
                'status'=> 'processing',
                'created_on'=> date('Y-m-d H:i:s'),
            ];
            $this->common->add('subscription', $subs_data);
            $this->paystack->initialize_paystack($pay_data, $this->app_settings->paystack_secrete);
        } 
    }
    
    public function verify_paystack(){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $verify = $this->paystack->verify_paystack_payment($this->coop->paystack_secrete);
        if($verify){
            if($verify['status']==true and $verify['data']['status']=='success'){
                $where = ['tranx_ref'=> $this->input->get('reference', true), 'coop_id'=> $this->coop->id];
                if($this->common->get_this('subscription', $where)){
                    $this->common->update_this('subscription',$where, ['status'=>'successful']);
                    $this->common->update_this('cooperatives',['id'=>$this->coop->id], ['subscription_cat_id'=>2]);
                    $this->session->set_flashdata('message', lang('subs_successful'));
                } else {
                    $this->session->set_flashdata('error', lang('act_unsuccessful'));
                }
            }
        }
        redirect('subscription/history');
    }

}
