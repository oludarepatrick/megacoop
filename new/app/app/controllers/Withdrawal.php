<?php

class Withdrawal extends BASE_Controller {
    const MENU_ID = 4;

    public function __construct() {
        parent::__construct();
        if (!$this->ion_auth->is_admin()) {
            redirect($_SERVER['HTTP_REFERER']);
        }
        if ($this->coop) {
            if ($this->coop->status === 'processing') {
                redirect('settings');
            }
        }
        $this->licence_cheker($this->coop, $this->app_settings);
    }

    public function index() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('month', lang("month"), 'trim|required');
        $this->form_validation->set_rules('year', lang("year"), 'trim|required');
        $where = [
            'savings.coop_id' => $this->coop->id,
            'savings.month_id' => date('n'),
            'savings.year' => date('Y'),
            'tranx_type' => 'debit',
            'savings.status'=>'paid'
        ];

        if ($this->form_validation->run()) {
            $month = $this->input->post('month');
            $where['savings.month_id'] = $this->common->get_this('months', ['name' => $month])->id;
            $where['savings.year'] = $this->input->post('year');
            $this->data['withdrawals'] = $this->info->get_withdrawals($where);
        } else {
            $month = date('F');
            $this->data['withdrawals'] = $this->info->get_withdrawals(['users.coop_id' => $this->coop->id, 'savings.status'=>'paid'], 1000);
        }
        $where['savings.status'] = 'paid';
        $this->data['filter_total_withdrawal'] = $this->common->sum_this('savings', $where, 'amount');
        $this->data['total_withdrawal'] = $this->common->sum_this('savings', ['savings.status' => 'paid','tranx_type' => 'debit', 'savings.coop_id' => $this->coop->id], 'amount');
        $this->data['month'] = $month;
        $this->data['year'] = $where['savings.year'];
        $this->data['title'] = lang('withdrawals');
        $this->data['controller'] = lang('withdrawals');
        $this->layout->set_app_page('withdrawal/index', $this->data);
    }

    public function add() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('member_id', lang('member_id'), 'trim|required');
        $this->form_validation->set_rules('savings_type', lang('savings_type'), 'trim|required');
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required');
        $this->form_validation->set_rules('narration', lang('narration'), 'trim|required');

        if ($this->form_validation->run()) {
            $savings_type_name = $this->common->get_this('savings_types', [
                'coop_id' => $this->coop->id, 'id' => $this->input->post('savings_type')
            ])->name;

            $user = $this->common->get_this('users', ['username' => $this->input->post('member_id')]);
            $amount = str_replace(',', '', $this->input->post('amount'));
            $savings_type = $this->input->post('savings_type');

            $old_bal = $this->utility->get_savings_bal($user->id, $this->coop->id, $savings_type);
            $new_bal = $old_bal - $amount;
            
            if ($amount <= 0) {
                $this->session->set_flashdata('error', lang('invalid_amount'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            if ($amount > $old_bal) {
                $this->session->set_flashdata('error', lang('low_bal'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $s_type = $this->common->get_this('savings_types', ['id' => $savings_type]);
            if ($this->utility->max_withdrawal_exeed($amount, $old_bal, $s_type, $this->input->post('ignore_limit'))) {
                $this->session->set_flashdata('error', lang('max_withdrawal_limit') . '. ' . $s_type->max_withdrawal . '% ' . lang('is_withdrawable'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $tranx_ref = $user->id . 'DEF' . date('Ymdhis');
            $withdrawal = [
                'tranx_ref' => $tranx_ref,
                'tranx_type' => 'debit',
                'referrer_code' => $this->coop->referrer_code,
                'coop_id' => $this->coop->id,
                'user_id' => $user->id,
                'balance' => $new_bal,
                'amount' => $amount,
                'month_id' => date('n'),
                'year' => date('Y'),
                'savings_type' => $savings_type,
                'source' => 0,
                'narration' => $this->input->post('narration'),
                'status' => 'paid',
                'payment_date' => date('Y-m-d H:i:s'),
                'score' => 0
            ];

            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('withdrawal_added')
            ];
            $this->common->start_trans();

            $item_id = $this->common->add('savings', $withdrawal);
            $this->utility->auto_post_to_general_ledger((object)$withdrawal, $item_id, "WIT");
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->data['name'] = ucwords($user->first_name . ' ' . $user->last_name);
                $this->data['member_id'] = $user->username;
                $this->data['savings_type_name'] = $savings_type_name;
                $this->data['month'] = $this->input->post('month');
                $this->data['year'] = $this->input->post('year');
                $this->data['amount'] = $amount;
                $this->data['status'] = 'paid';
                $this->data['balance'] = $withdrawal['balance'];
                $this->data['date'] = date('Y-m-d g:i:s');


                $content = "Debit Alert"
                . "\n" . "ST: " . $savings_type_name
                . "\n" . "ID: " . $this->utility->shortend_str_len($user->username, 5, '***')
                . "\n" . "DATE: " . $this->utility->just_date(date('Y-m-d H:i:s'), true)
                . "\n" . "AMT: NGN" . number_format($amount, 2)
                . "\n" . "Av.BAL: NGN" . number_format($withdrawal['balance'], 2);
                $this->fivelinks->send_SMS($user->phone, $content);

                $subject = 'Withdrawal Notice';
                $message = $this->load->view('emails/email_withdrawal', $this->data, true);
                $this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $user->email, $subject, $message);

                $this->session->set_flashdata('message', lang('withdrawal_added'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
            redirect('withdrawal');
        } else {
            $this->data['savings_type'] = $this->common->get_all_these('savings_types', ['coop_id' => $this->coop->id]);
            $this->data['savings_source'] = $this->common->get_all('savings_source');

            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->data['title'] = lang('add_withdrawal');
            $this->data['controller'] = lang('withdrawals');
            $this->layout->set_app_page('withdrawal/add', $this->data);
        }
    }

    public function edit($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('member_id', lang('member_id'), 'trim|required');
        $this->form_validation->set_rules('savings_type', lang('savings_type'), 'trim|required');
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required');
        $this->form_validation->set_rules('narration', lang('narration'), 'trim|required');

        $id = $this->utility->un_mask($id);
        $this->data['withdrawal'] = $this->info->get_withdrawal_details(['savings.id' => $id, 'users.coop_id' => $this->coop->id]);

        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('withdrawal_edited')
            ];
            $user = $this->common->get_this('users', ['username' => $this->input->post('member_id')]);
            $amount = str_replace(',', '', $this->input->post('amount'));
            $savings_type = $this->input->post('savings_type');

            if ($savings_type == $this->data['withdrawal']->savings_type) {
                $old_bal = $this->utility->get_savings_bal($user->id, $this->coop->id, $savings_type) + $this->data['withdrawal']->amount;
                $new_bal = $old_bal - $amount;
            } else {
                $old_bal = $this->utility->get_savings_bal($user->id, $this->coop->id, $savings_type);
                $new_bal = $old_bal - $amount;
            }
            if ($amount <= 0) {
                $this->session->set_flashdata('error', lang('invalid_amount'));
                redirect($_SERVER["HTTP_REFERER"]);
            } 

            if ($amount > $old_bal) {
                $this->session->set_flashdata('error', lang('low_bal'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $s_type = $this->common->get_this('savings_types', ['id' => $savings_type]);
            if ($this->utility->max_withdrawal_exeed($amount, $old_bal, $s_type, $this->input->post('ignore_limit'))) {
                $this->session->set_flashdata('error', lang('max_withdrawal_limit') . '. ' . $s_type->max_withdrawal . '% ' . lang('is_withdrawable'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $withdrawal = [
                'user_id' => $user->id,
                'coop_id' => $this->coop->id,
                'balance' => $new_bal,
                'amount' => $amount,
                'month_id' => date('n'),
                'year' => date('Y'),
                'savings_type' => $savings_type,
                'source' => 0,
                'narration' => $this->input->post('narration'),
                'status' => 'paid',
                'payment_date' => date('Y-m-d H:i:s'),
                'score' => 0
            ];

            $this->common->start_trans();
            $this->common->update_this('savings', ['id' => $id, 'coop_id' => $this->coop->id], $withdrawal);
            $this->utility->auto_post_to_general_ledger((object)$withdrawal, $id, "WIT");

            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('withdrawal_edited'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
            redirect('withdrawal');
        } else {
            $this->data['savings_type'] = $this->common->get_all_these('savings_types', ['coop_id' => $this->coop->id]);
            $this->data['savings_source'] = $this->common->get_all('savings_source');

            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->data['title'] = lang('edit_withdrawal');
            $this->data['controller'] = lang('withdrawals');
            $this->layout->set_app_page('withdrawal/edit', $this->data);
        }
    }

    public function delete($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xdelete', 'on');
        $id = $this->utility->un_mask($id);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('withdrawal_deleted')
        ];

        $this->common->start_trans();
        $this->common->delete_this('savings', ['coop_id' => $this->coop->id, 'id' => $id]);
        $this->common->delete_this('ledger', ['pv_no' => 'WIT'.$id]);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('withdrawal_deleted'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function preview($id) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $id = $this->utility->un_mask($id);
        $this->data['withdrawal'] = $this->info->get_withdrawal_details(['savings.id' => $id, 'users.coop_id' => $this->coop->id]);
        $this->data['title'] = lang('print');
        $this->data['controller'] = lang('withdrawal');
        $this->layout->set_app_page('withdrawal/preview', $this->data);
    }
    
    public function pending_withdrawal() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('month', lang("month"), 'trim|required');
        $this->form_validation->set_rules('year', lang("year"), 'trim|required');
        $where = [
            'savings.coop_id' => $this->coop->id,
            'savings.month_id' => date('n'),
            'savings.year' => date('Y'),
            'tranx_type' => 'debit',
            'savings.status'=>'due'
        ];

        if ($this->form_validation->run()) {
            $month = $this->input->post('month');
            $where['savings.month_id'] = $this->common->get_this('months', ['name' => $month])->id;
            $where['savings.year'] = $this->input->post('year');
            $this->data['withdrawals'] = $this->info->get_withdrawals($where);
        } else {
            $month = date('F');
            $this->data['withdrawals'] = $this->info->get_withdrawals(['users.coop_id' => $this->coop->id, 'savings.status'=>'due'], 1000);
        }
        $this->data['filter_total_withdrawal'] = $this->common->sum_this('savings', $where, 'amount');
        $this->data['total_withdrawal'] = $this->common->sum_this('savings', ['savings.status' => 'due','tranx_type' => 'debit', 'savings.coop_id' => $this->coop->id], 'amount');
        $this->data['month'] = $month;
        $this->data['year'] = $where['savings.year'];
        $this->data['title'] = lang('pending_withdrawal');
        $this->data['controller'] = lang('withdrawals');
        $this->layout->set_app_page('withdrawal/pending', $this->data);
    }

    public function preview_request($savings_id = null){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $savings_id = $this->utility->un_mask($savings_id);

        $savings = $this->common->get_this('savings', ['id' => $savings_id]);
        $id = $savings->user_id;
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

        $approvals = $this->info->get_withdrawal_approvals(['savings.id' => $savings_id]);
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
        $this->data['withdrawal'] = $savings;
        // var_dump($savings);exit;
        $this->data['approval'] = $approval_data;
        $this->data['savings_id'] = $savings_id;
        $this->data['title'] = lang('preview');
        $this->data['controller'] = lang('withdrawal');
        $this->layout->set_app_page('withdrawal/preview_request', $this->data);
    }

    public function approve($id){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $id = $this->utility->un_mask($id);
        $approval_exist = $this->common->get_this('withdrawal_approvals', ['savings_id' => $id, 'exco_id' => $this->user->id, 'coop_id' => $this->coop->id]);

        $approval_complete = $this->utility->withdrawal_approvals_completed($id, $this->coop->withdrawal_approval_level, $approval_exist);

        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('withdrawal').' '.lang('approve'),
        ];

        $approval_data = [
            'status' => 'approved',
            'action_date' => date('Y-m-d H:i:s')
        ];

        
        $this->common->start_trans();
        if ($approval_exist) {
            $this->common->update_this('withdrawal_approvals', ['savings_id' => $id, 'exco_id' => $this->user->id], $approval_data);
        } else {
            $approval_data['exco_id'] = $this->user->id;
            $approval_data['coop_id'] = $this->coop->id;
            $approval_data['savings_id'] = $id;
            $this->common->add('withdrawal_approvals', $approval_data);
        }

        if ($approval_complete) {
            $savings = $this->common->get_this('savings', ['id' => $id]);
            $this->utility->auto_post_to_general_ledger((object)$savings, $savings->id, "WIT");
            //send notifications
            $user = $this->common->get_this('users', ['id' => $savings->user_id]);
            $savings_type_name = $this->common->get_this('savings_types', [
                'coop_id' => $this->coop->id, 'id' => $savings->savings_type
            ])->name;
            $old_bal = $this->utility->get_savings_bal($user->id, $this->coop->id, $savings->savings_type);
            $new_bal = $old_bal - $savings->amount;

            $this->data['name'] = ucwords($user->first_name . ' ' . $user->last_name);
            $this->data['member_id'] = $user->username;
            $this->data['savings_type_name'] = $savings_type_name;
            $this->data['month'] = $this->input->post('month');
            $this->data['year'] = $this->input->post('year');
            $this->data['amount'] = $savings->amount;
            $this->data['status'] = 'paid';
            $this->data['balance'] = $new_bal;
            $this->data['date'] = date('Y-m-d g:i:s');
            
            if ($this->coop->sms_notice == 'on') {
                $content = "Debit Alert"
                . "\n" . "ST: " . $savings_type_name
                    . "\n" . "ID: " . $this->utility->shortend_str_len($user->username, 5, '***')
                    . "\n" . "DATE: " . $this->utility->just_date(date('Y-m-d H:i:s'), true)
                    . "\n" . "AMT: NGN" . number_format($savings->amount, 2)
                    . "\n" . "Av.BAL: NGN" . number_format($new_bal, 2);
                $this->fivelinks->send_SMS($user->phone, $content);
            }
            

            $subject = 'Withdrawal Notice';
            $message = $this->load->view('emails/email_withdrawal', $this->data, true);
            $this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $user->email, $subject, $message);

            $this->common->update_this('savings', ['coop_id' => $this->coop->id, 'id' => $id], ['status' => 'paid']);
        }

        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message',lang('withdrawal') . ' ' . lang('approve'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect('withdrawal');
    }

    public function decline($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xdelete', 'on');
        $id = $this->utility->un_mask($id);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('withdrawal').' '.lang('decline'),
        ];

        $this->common->start_trans();
        $this->common->delete_this('savings', ['coop_id' => $this->coop->id, 'id' => $id]);
        $this->common->delete_this('withdrawal_approvals', ['coop_id' => $this->coop->id, 'savings_id' => $id]);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('withdrawal').' '.lang('decline'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

}
