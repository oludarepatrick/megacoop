<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Licence extends BASE_Controller {

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
        $licence = $this->info->get_licence_details(['coop_id' => $this->coop->id, 'licence.status' => 'successful', 'licence.active' => 1]);
        if ($licence) {
            $interval = $this->utility->get_date_interval(date('Y-m-d'), $licence->end_date);
        }
        $this->data = [
            'status' => $licence ? 'active' : 'due',
            'month' => $licence ? $licence->month : '0',
            'start_date' => $licence ? $licence->start_date : 'Not available',
            'end_date' => $licence ? $licence->end_date : 'Not available',
            'remain' => $licence ? $interval->days : '0',
            'licence_cat' => $licence ? $licence->licence_cat : 'Not available',
            'unit' => $licence ? $licence->unit : 'Not available',
            'licence_cat_id' => $licence ? $licence->licence_cat_id : 'Not available',
        ];
        $this->data['licence'] = $this->common->get_all('licence_cat');
        $this->data['controller'] = lang('licence');
        $this->data['title'] = lang('licence');
        $this->layout->set_app_page('licence/index', $this->data);
    }

    function ajax_licence_component() {
        $id = $this->utility->un_mask($this->input->get('licence_cat_id', true));
        $licence_cat = $this->common->get_this('licence_cat', ['id' => $id]);
        $licence = $this->common->get_this('licence', ['coop_id' => $this->coop->id, 'status'=>'successful', 'active'=>1]);
        $start_date = date('Y-m-d');
        $total_mem = $this->common->count_this('users', ['coop_id' => $this->coop->id]);

        if($total_mem < $licence_cat->min_member){
            $total_mem = $licence_cat->min_member;
        }

        $paystack_amount = $this->paystack->get_amount_paystack($licence_cat->amount * $total_mem);
        $data = [
            'start_date' => $start_date,
            'end_date' => $this->utility->get_end_date($start_date, $licence_cat->month),
            'total_member' => $total_mem,
            'rate' => $licence_cat->amount,
            'fee' => $paystack_amount - ($licence_cat->amount * $total_mem),
            'total' => $paystack_amount,
            'licence_cat_id' => $id,
            'active_licence' => $licence
        ];

        $data['licence'] = $this->common->get_all('licence_cat');
        $data['total_mem'] = $total_mem;
        $data['calc_month'] = ($licence)?$this->utility->get_months_between($start_date, $licence->end_date, 1):0 ;

        $message = $this->load->view('licence/payment_brk_down', $data, true);
        echo json_encode(['status' => 'success', 'message' => $message]);
    }

    public function history() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->data['licence'] = $this->info->get_licence(['coop_id' => $this->coop->id], 1000);
        $this->data['controller'] = lang('licence');
        $this->data['title'] = lang('history');
        $this->layout->set_app_page('licence/history', $this->data);
    }

    public function pay_with_paystack($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $id = $this->utility->un_mask($id);
        $licence_cat = $this->common->get_this('licence_cat', ['id' => $id]);
        $coop_licence = $this->common->get_this('licence', ['coop_id' => $this->coop->id]);
        $start_date = date('Y-m-d');
        $total_mem = $this->common->count_this('users', ['coop_id' => $this->coop->id]);
        if ($total_mem < $licence_cat->min_member) {
            $total_mem = $licence_cat->min_member;
        }
        $paystack_amount = $this->paystack->get_amount_paystack($licence_cat->amount * $total_mem);

        if ($paystack_amount > 0) {
            $pay_data = [
                'email' => ($this->user->email) ? $this->user->email : 'noemail@noemail.com',
                'amount' => $paystack_amount * 100,
                'currency' => 'NGN',
                'reference' => strtoupper(str_replace(' ', '', $this->coop->coop_initial)) . date('dYmHis'),
                'bearer' => 'account',
                'callback_url' => base_url() . 'licence/verify_paystack',
            ];

            $subs_data = [
                'coop_id' => $this->coop->id,
                'tranx_ref' => $pay_data['reference'],
                'referrer_code' => $this->coop->referrer_code,
                'total_amount' => $licence_cat->amount * $total_mem,
                'licence_cat_id' => $id,
                'rate' => $licence_cat->amount,
                'unit' => $total_mem,
                'status' => 'processing',
                'start_date' => $start_date,
                'end_date' => $this->utility->get_end_date($start_date, $licence_cat->month),
                'created_on' => date('Y-m-d H:i:s'),
            ];

            $payment_data = [
                'coop_id' => $this->coop->id,
                'tranx_ref' => $pay_data['reference'],
                'amount' => $licence_cat->amount * $total_mem,
                'amount_plus_charges' =>  $paystack_amount,
                'item' => json_encode(['rate' => $licence_cat->amount,'unit' => $total_mem]),
                'status' => 'processing',
                'gate_way_id' => 1,
                'created_on' => date('Y-m-d H:i:s'),
            ];

            $this->common->start_trans();
            if(!$coop_licence){
                $this->common->add('licence', $subs_data);
            }else{
                $this->common->update_this('licence', ['id'=>$coop_licence->id], $subs_data);
            }
            $this->common->add('payment', $payment_data);
            $this->common->finish_trans();

            if($this->common->status_trans()){
                $this->paystack->initialize_paystack($pay_data, $this->app_settings->paystack_secrete);
            }else{
                 $this->session->set_flashdata('error', lang('act_unsuccessful'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function verify_paystack() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $verify = $this->paystack->verify_paystack_payment($this->app_settings->paystack_secrete);
        if ($verify) {
            $where = ['tranx_ref' => $this->input->get('reference', true), 'coop_id' => $this->coop->id];
            $coop_licence = $this->common->get_this('licence', ['coop_id' => $this->coop->id]);
            if ($verify['status'] == true and $verify['data']['status'] == 'success') {
                //for licence upgrade
                if($this->session->userdata('upgrade_data')) {
                   
                    $upgrade_data = $this->session->userdata('upgrade_data');
                    ;
                    $this->common->update_this('licence', ['id' => $coop_licence->id], ['unit' => $upgrade_data['unit'], 'upgraded'=>1]);
                    $this->common->update_this('payment', $where, ['status' => 'successful', 'metadata' => json_encode($verify)]);
                    $this->session->unset_userdata('unit');
                }else{
                   
                    $licence = $this->common->get_this('licence', $where);
                    $this->common->update_this('payment', $where, ['status' => 'successful', 'metadata' => json_encode($verify)]);
                    $this->common->update_this('licence', ['id' => $coop_licence->id], ['status' => 'successful', 'active' => 1]);
                    $this->common->update_this('cooperatives', ['id' => $this->coop->id], ['licence_cat_id' => $licence->licence_cat_id, 'status' => 'active']);
                }
               
                $this->session->set_flashdata('message', lang('licence_upgraded'));
            }else{
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        }
        redirect('licence/history');
    }

    public function extended(){
        $this->form_validation->set_rules('member', lang('member'), 'trim|required|numeric');
        if ($this->form_validation->run()) {
            $member = $this->input->post('member');
            $licence = $this->common->get_this('licence', ['coop_id' => $this->coop->id, 'status' => 'successful', 'active' => 1]);
            $licence_cat = $this->common->get_this('licence_cat', ['id'=>1]);
            $start_date = date('Y-m-d');
            $calc_month = $this->utility->get_months_between($start_date, $licence->end_date, 1);
            $amount = $member * $licence_cat->amount * $calc_month;
            $paystack_amount = $this->paystack->get_amount_paystack($amount);

            $pay_data = [
                'email' => ($this->user->email) ? $this->user->email : 'noemail@noemail.com',
                'amount' => $paystack_amount * 100,
                'currency' => 'NGN',
                'reference' => strtoupper(str_replace(' ', '', $this->coop->coop_initial)) . date('dYmHis'),
                'bearer' => 'account',
                'callback_url' => base_url() . 'licence/verify_paystack',
            ];

            $payment_data = [
                'coop_id' => $this->coop->id,
                'source_id' => $licence->source_id,
                'tranx_ref' => $pay_data['reference'],
                'amount' => $amount,
                'amount_plus_charges' =>  $paystack_amount,
                'item' => json_encode(['rate' => $licence_cat->amount, 'unit' => $member, 'calc_month'=>$calc_month]),
                'status' => 'processing',
                'gate_way_id' => 1,
                'created_on' => date('Y-m-d H:i:s'),
            ];
            
            $this->session->set_userdata('upgrade_data',['unit'=> $licence->unit + $member,'source_id' => $licence->source_id]);
            if ($this->common->add('payment', $payment_data)) {
                $this->paystack->initialize_paystack($pay_data, $this->app_settings->paystack_secrete);
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        }else{
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
}
