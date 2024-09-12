<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Memberexit extends BASE_Controller {

    const MENU_ID = 1;

    public function __construct() {
        parent::__construct();
        if (!$this->ion_auth->is_admin()) {
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->load->library('excel');
        if ($this->coop) {
            if ($this->coop->status === 'processing') {
                redirect('settings');
            }
        }
        $this->licence_cheker($this->coop, $this->app_settings);
    }

    public function index(){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->data['members'] = $this->info->get_exit_members(['users.status !=' => 'exit']);
        $this->data['title'] = lang('exit_request');
        $this->data['controller'] = lang('registration');
        $this->layout->set_app_page('registration/account_closure', $this->data);
    }

    public function exited_members(){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->data['members'] = $this->info->get_exit_members(['users.status'=>'exit']);
        $this->data['title'] = lang('exited_members');
        $this->data['controller'] = lang('registration');
        $this->layout->set_app_page('registration/exited_members', $this->data);
    }

    public function preview($member_exit_id = null){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $member_exit_id = $this->utility->un_mask($member_exit_id);
        $id = $this->common->get_this('member_exit', ['id'=> $member_exit_id])->user_id;
        $this->data['existing_loans'] = $this->info->get_loans(['loans.user_id' => $id, 'loans.coop_id' => $this->coop->id, 'loans.status !=' => 'finished']);
        $this->data['active_loans'] = $this->info->get_loans(['loans.user_id' => $id, 'loans.coop_id' => $this->coop->id, 'loans.status' => 'disbursed']);
        $this->data['existing_credit_sales'] = $this->info->get_credit_sales(['credit_sales.user_id' => $id, 'credit_sales.coop_id' => $this->coop->id, 'credit_sales.status !=' => 'finished']);
        $this->data['active_credit_sales'] = $this->info->get_credit_sales(['credit_sales.user_id' => $id, 'credit_sales.coop_id' => $this->coop->id, 'credit_sales.status' => 'disbursed']);
        $savings_type = $this->common->get_all_these('savings_types', ['coop_id' => $this->coop->id]);
        $this->data['savings'] = [];
        foreach ($savings_type as $st) {
            $this->data['savings'][] = (object) [
                'savings_type' => $st->id,
                'name' => $st->name,
                'total_savings' => $this->common->sum_this('savings', ['tranx_type' => 'credit', 'savings_type' => $st->id, 'user_id' => $id,], 'amount')->amount,
                'bal' => $this->utility->get_savings_bal($id, $this->coop->id, $st->id),
            ];
        }
        $this->data['all_savings_bal'] = $this->utility->get_savings_bal($id, $this->coop->id);
        $this->data['all_loan_bal'] = $this->utility->get_loan_bal($id, $this->coop->id);
        $this->data['all_credit_sales_bal'] = $this->utility->get_credit_sales_bal($id, $this->coop->id);
        $this->data['user'] = $this->info->get_user_details(['users.id' => $id, 'users.coop_id' => $this->coop->id]);

        $approvals = $this->info->get_member_exit_approvals(['member_exit.id' => $member_exit_id]);
        $approval_data = [];
        if ($approvals) {
            foreach ($approvals as $g) {
                $approval_data[] = (object) [
                    'full_name' => $g->full_name,
                    'member_id' => $g->username,
                    'avatar' => $g->avatar,
                    'approval' => $g->status,
                    'response_date' => $g->action_date,
                    'role' => $g->role,
                ];
            }
        }
        $this->data['approval'] = $approval_data;
        $this->data['member_exit_id'] = $member_exit_id;
        $this->data['title'] = lang('member_exit');
        $this->data['controller'] = lang('registration');
        $this->layout->set_app_page('registration/preview', $this->data);
    }

    public function approve($id){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $id = $this->utility->un_mask($id);
        $approval_exist = $this->common->get_this('member_exit_approvals', ['member_exit_id' => $id, 'exco_id' => $this->user->id, 'coop_id' => $this->coop->id]);
        $approval_complete = $this->utility->member_exit_approvals_completed($id, $this->coop->member_exit_approval_level, $approval_exist);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('member_exit_approved')
        ];

        $approval_data = [
            'status' => 'approved',
            'action_date' => date('Y-m-d H:i:s')
        ];
        $this->common->start_trans();
        if ($approval_exist) {
            $this->common->update_this('member_exit_approvals', ['member_exit_id' => $id, 'exco_id' => $this->user->id], $approval_data);
        } else {
            $approval_data['exco_id'] = $this->user->id;
            $approval_data['coop_id'] = $this->coop->id;
            $approval_data['member_exit_id'] = $id;
            $this->common->add('member_exit_approvals', $approval_data);
        }

        if ($approval_complete) {
            $this->common->update_this('member_exit', ['id' => $id,], ['status' => 'completed']);
        }

        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('member_exit_approved'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function member_exit($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $id = $this->utility->un_mask($id);
        $all_savings_bal = $this->utility->get_savings_bal($id, $this->coop->id);
        $all_loan_bal = $this->utility->get_loan_bal($id, $this->coop->id);
        $all_credit_sales_bal = $this->utility->get_credit_sales_bal($id, $this->coop->id);

        $approval = $this->common->get_this('member_exit', ['user_id' => $id]);
        if ($approval->status != 'completed') {
            $this->session->set_flashdata('error', lang('needs_more_exco_approval'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $liquidity = $all_savings_bal - $all_loan_bal - $all_credit_sales_bal;
        if ($liquidity < 0) {
            $this->session->set_flashdata('error', lang('member_exit_not_allowed'). ' '.number_format($liquidity));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('member_acc_clossed')
        ];

        $this->common->start_trans();
        $this->common->update_this('users', ['id' => $id, 'coop_id' => $this->coop->id], ['status' => 'exit', 'active' => 0]);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('member_acc_clossed'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }

        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function reactivate($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $id = $this->utility->un_mask($id);
        
        
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('member_acc_reactivated')
        ];
        $member_exit = $this->common->get_this('member_exit',['id'=>$id]);
        $this->common->start_trans();
        $this->common->update_this('users', ['id' => $member_exit->user_id, 'coop_id' => $this->coop->id], ['status' => 'approved', 'active' => 1]);
        $this->common->delete_this('member_exit', ['id'=>$id]);
        $this->common->delete_this('member_exit_approvals', ['member_exit_id'=>$id, ]);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('member_acc_reactivated'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }

        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function delete($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xdelete', 'on');
        $id = $this->utility->un_mask($id);
        
        
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => "Member Account Closure request deleted"
        ];
        $this->common->start_trans();
        $this->common->delete_this('member_exit', ['id'=>$id]);
        $this->common->delete_this('member_exit_approvals', ['member_exit_id'=>$id, ]);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', "Member Account Closure request deleted");
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }

        redirect($_SERVER["HTTP_REFERER"]);
    }

}