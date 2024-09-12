<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Agents extends BASE_Controller {
    const MENU_ID =20;
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
        $this->data['members'] = $this->info->get_users( ['users.coop_id' => $this->coop->id, 'groups.id'=>3]);
        $this->data['title'] = lang('agents');
        $this->data['controller'] = lang('agents');
        $this->layout->set_app_page('agents/index', $this->data);
    }

    public function transactions() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('start_date', lang("start_date"), 'trim|required');
        $this->form_validation->set_rules('end_date', lang("end_date"), 'trim|required');
        $where = [
            'agent_wallet.coop_id' => $this->coop->id,
            'agent_wallet.created_on>=' => $this->utility->get_this_year('start'),
            'agent_wallet.created_on<=' => $this->utility->get_this_year('end'),
        ];
        if ($this->form_validation->run()) {
            $where['agent_wallet.created_on>='] = $this->input->post('start_date');
            $where['agent_wallet.created_on<='] = $this->input->post('end_date');
            $this->data['wallet'] = $this->info->get_agent_wallet($where);
            $where['tranx_type'] = 'credit';
            $this->data['total_wallet_credit'] = $this->common->sum_this('agent_wallet', $where, 'amount');
            $where['tranx_type'] = 'debit';
            $this->data['total_wallet_debit'] = $this->common->sum_this('agent_wallet', $where, 'amount');
            $this->data['start_date'] = $this->input->post('start_date');
            $this->data['end_date'] = $this->input->post('end_date');
        } else {
            $this->data['wallet'] = $this->info->get_agent_wallet($where, 1000);
            $where['tranx_type'] = 'credit';
            $this->data['total_wallet_credit'] = $this->common->sum_this('agent_wallet', $where, 'amount');
            $where['tranx_type'] = 'debit';
            $this->data['total_wallet_debit'] = $this->common->sum_this('agent_wallet', $where, 'amount');
            $this->data['start_date'] = $this->utility->get_this_year('start');
            $this->data['end_date'] = $this->utility->get_this_year('end');
        }
        
        $this->data['title'] = lang('agent') .' '.lang('wallet');
        $this->data['controller'] = lang('agents');
        $this->layout->set_app_page('agents/transaction', $this->data);
    }

    public function fund($id){
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required');
        $this->data['id'] = $id = $this->utility->un_mask($id);
        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('agent_wallet_funded')
            ];
            $wallet = [
                'tranx_ref' => $this->user->id . 'WAL' . date('Ymdhis'),
                'coop_id' => $this->coop->id,
                'user_id' => $id,
                'amount' =>  str_replace(',', '', $this->input->post('amount')),
                'tranx_type' => 'credit',
                'gate_way_id' => 3,
                'status' => 'successful',
                'narration' => 'Wallet Funding'
            ];

            $this->common->start_trans();
            $this->common->add('agent_wallet', $wallet);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('agent_wallet_funded'));
               
            }else{
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
            redirect($_SERVER['HTTP_REFERER']);
        }

       
        $this->data['wallet_bal'] = $this->utility->get_agent_wallet_bal($id, $this->coop->id);
        $this->data['user'] = $this->info->get_user_details(['users.id' => $id, 'users.coop_id' => $this->coop->id]);
        $this->data['title'] = lang('agents');
        $this->data['controller'] = lang('agents');
        $this->layout->set_app_page('agents/fund', $this->data);
    }

     public function preview($id) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $id = $this->utility->un_mask($id);
        $this->data['wallet'] = $this->info->get_agent_wallet_details(['agent_wallet.id' => $id, 'users.coop_id' => $this->coop->id]);
        $this->data['title'] = lang('print');
        $this->data['controller'] = lang('Agents');
        $this->layout->set_app_page('agents/preview', $this->data);
    }

    public function approve($id){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $id = $this->utility->un_mask($id);
        $wallet = $this->common->get_this('agent_wallet', ['id'=>$id, 'coop_id'=>$this->coop->id]);
        $user = $this->common->get_this('users', ['id'=>$wallet->user_id, 'coop_id'=>$this->coop->id]);
        $old_bal = $this->utility->get_wallet_bal($wallet->user_id, $this->coop->id);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('wallet_loading') . ' ' . lang('approve'),
        ];
        $this->common->start_trans();
        $this->common->update_this('agent_wallet', ['coop_id' => $this->coop->id, 'id' => $id], ['status' => 'successful']);
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

}
