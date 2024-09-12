<?php

defined('BASEPATH') OR exit('No direct script access allowed');
class Wallet extends BASE_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('paystack');
        $this->load->library('flutter');
    }

    public function index() {
        $this->form_validation->set_rules('start_date', lang("start_date"), 'trim|required');
        $this->form_validation->set_rules('end_date', lang("end_date"), 'trim|required');
        $where = [
            'wallet.user_id' => $this->user->id,
            'wallet.coop_id' => $this->coop->id,
        ];
        if ($this->form_validation->run()) {
            $where['wallet.created_on>='] = $this->input->post('start_date');
            $where['wallet.created_on<='] = $this->input->post('end_date');
            $this->data['wallet'] = $this->info->get_wallet($where, FALSE, true);
            $where['wallet.status'] = 'successful';
            $where['tranx_type'] = 'credit';
            $this->data['total_wallet_credit'] = $this->common->sum_this('wallet', $where, 'amount');
            $where['tranx_type'] = 'debit';
            $this->data['total_wallet_debit'] = $this->common->sum_this('wallet', $where, 'amount');
            $this->data['start_date'] = $this->input->post('start_date');
            $this->data['end_date'] = $this->input->post('end_date');
        } else {
            $this->data['wallet'] = $this->info->get_wallet($where, 1000, true);
            $where['wallet.status'] = 'successful';
            $where['tranx_type'] = 'credit';
            $this->data['total_wallet_credit'] = $this->common->sum_this('wallet', $where, 'amount');
            $where['tranx_type'] = 'debit';
            $this->data['total_wallet_debit'] = $this->common->sum_this('wallet', $where, 'amount');
        }


        $this->data['title'] = lang('wallet_trans');
        $this->data['controller'] = lang('wallet');
        $this->layout->set_app_page_member('member/wallet/index', $this->data);
    }

    public function load_wallet() {
        $this->form_validation->set_rules('amount', lang("amount"), 'trim|required');
        $this->form_validation->set_rules('gate_way', lang("gate_way"), 'trim|required');

        if ($this->form_validation->run()) {
            $amount = str_replace(',', '', $this->input->post('amount'));
            $tranx_ref = $this->user->id."WAL" . date('dYmHis');
            $gate_way = $this->input->post('gate_way');
            if($gate_way == 1){
                $pay_data = [
                    'email' => ($this->user->email) ? $this->user->email : 'noemail@noemail.com',
                    'amount' => $this->paystack->get_amount_paystack($amount) * 100,
                    'currency' => 'NGN',
                    'reference' => $tranx_ref,
                    'bearer' => 'account',
                    'callback_url' => base_url() . 'member/wallet/verify',
                ];
            }elseif($gate_way == 2){
                $pay_data = [
                    'email' => ($this->user->email) ? $this->user->email : 'noemail@noemail.com',
                    'amount' => $this->flutter->get_amount_flutter($amount),
                    'currency' => 'NGN',
                    'tx_ref' => $tranx_ref,
                    'redirect_url' => base_url() . 'member/wallet/verify_flutter',
                    'payment_options'=>'card',
                    'customer'=>[
                        'email' => ($this->user->email) ? $this->user->email : 'noemail@noemail.com',
                        'phonenumber' => ($this->user->phone) ? $this->user->phone : '08000000000',
                        'name' => $this->user->first_name .' ' . $this->user->last_name 
                    ],

                    'customizations'=>[
                        'title' => 'Wallet Loading',
                        'description' => 'Wallet Loading',
                        'logo' => '' 
                    ]
                ];
            }
            $wallet_data = [
                'coop_id'=> $this->coop->id,
                'user_id'=> $this->user->id,
                'tranx_ref'=> $tranx_ref,
                'referrer_code'=> $this->coop->referrer_code,
                'amount'=> $amount,
                'tranx_type'=> 'credit',
                'gate_way_id'=> $gate_way,
                'narration'=> 'Load Wallet',
                'status'=> 'processing',
                'created_on'=> date('Y-m-d H:i:s'),
            ];
            $this->common->add('wallet', $wallet_data);
           if($gate_way == 1){
                $this->paystack->initialize_paystack($pay_data, $this->coop->paystack_secrete);
           }elseif($gate_way == 2){
                $this->flutter->initialize_flutter($pay_data, $this->coop->flutter_secrete);
           }
        } else {
            $this->data['gate_way'] = $this->common->get_all('payment_gate_way');
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->data['title'] = lang('load');
            $this->data['controller'] = lang('wallet');
            $this->layout->set_app_page_member('member/wallet/load_wallet', $this->data);
        }
    }
    
    public function verify(){
        $verify = $this->paystack->verify_paystack_payment($this->coop->paystack_secrete);
        if($verify){
            if($verify['status']==true and $verify['data']['status']=='success'){
                $where = ['tranx_ref'=> $this->input->get('reference', true), 'coop_id'=> $this->coop->id, 'user_id'=> $this->user->id];
                if($this->common->get_this('wallet', $where)){
                    $this->common->update_this('wallet',$where, ['status'=>'successful']);
                    $this->session->set_flashdata('message', lang('wallet_loading_successful'));
                } else {
                    $this->session->set_flashdata('error', lang('act_unsuccessful'));
                }
            }
        }
        redirect('member/dashboard');
    }
    public function verify_flutter(){
        $verify = $this->flutter->verify_flutter_payment($this->coop->flutter_secrete);
        if($verify){
            if($verify['status']=='success'){
                $where = ['tranx_ref'=> $verify['data']['tx_ref'], 'coop_id'=> $this->coop->id, 'user_id'=> $this->user->id];
                if($this->common->get_this('wallet', $where)){
                    $this->common->update_this('wallet',$where, ['status'=>'successful', 'amount'=>$verify['data']['amount_settled']]);
                    $this->session->set_flashdata('message', lang('wallet_loading_successful'));
                } else {
                    $this->session->set_flashdata('error', lang('act_unsuccessful'));
                }
            }
        }
        redirect('member/dashboard');
    }

    public function ajax_get_payment_break_down() {
        $amount = str_replace(',', '', $this->input->get('amount'));
        $gate_way = $this->input->get('gate_way');
        if ($gate_way == 1) {
            $total = $this->paystack->get_amount_paystack($amount);
        }
        if ($gate_way == 2) {
            $total = $this->flutter->get_amount_flutter($amount);
        }
      

        $break_down = [
            'fee' => number_format($total - $amount, 2),
            'amount' => number_format($amount, 2),
            'total' => number_format($total, 2)
        ];

        echo json_encode(array('status' => 'success', 'message' => $break_down));
    }

}
