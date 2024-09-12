<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Loan extends BASE_Controller {

    const MENU_ID = 3;

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

    public function index() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('start_date', lang("start_date"), 'trim|required');
        $this->form_validation->set_rules('end_date', lang("end_date"), 'trim|required');
        $where = [
            'loans.coop_id' => $this->coop->id,
            'loans.status' => 'request',
            'loans.created_on>=' => $this->utility->get_this_year('start'),
            'loans.created_on<=' => $this->utility->get_this_year('end'),
        ];
        $this->data['loan_type'] = 'ALL LOANS' ; 
        if ($this->form_validation->run()) {
            $where['loans.created_on>='] = $this->input->post('start_date');
            $where['loans.created_on<='] = $this->input->post('end_date');
            if($this->input->post('loan_type')){
                $where['loans.loan_type_id'] = $this->input->post('loan_type');
                $this->data['loan_type'] = $this->common->get_this('loan_types', ['id' => $this->input->post('loan_type')]); 
            }
            $this->data['loans'] = $this->info->get_loans($where);
            $this->data['filter_total_loans'] = $this->common->sum_this('loans', $where, 'principal');
            $this->data['filter_total_interest'] = $this->common->sum_this('loans', $where, 'interest');
            $this->data['start_date'] = $this->input->post('start_date');
            $this->data['end_date'] = $this->input->post('end_date');
        } else {
            $this->data['loans'] = $this->info->get_loans(['users.coop_id' => $this->coop->id, 'loans.status' => 'request'], 1000);
            $this->data['filter_total_loans'] = $this->common->sum_this('loans', $where, 'principal');
            $this->data['filter_total_interest'] = $this->common->sum_this('loans', $where, 'interest');
            $this->data['start_date'] = $this->utility->get_this_year('start');
            $this->data['end_date'] = $this->utility->get_this_year('end');
        }

        $this->data['total_loans'] = $this->common->sum_this('loans', ['loans.coop_id' => $this->coop->id, 'loans.status' => 'request'], 'principal');
        $this->data['total_interest'] = $this->common->sum_this('loans', ['loans.coop_id' => $this->coop->id, 'loans.status' => 'request'], 'interest');
        $this->data['loan_types'] = $this->common->get_all_these('loan_types', ['coop_id' => $this->coop->id]);
        $this->data['title'] = lang('requested_loans');
        $this->data['controller'] = lang('loan');
        $this->layout->set_app_page('loan/index', $this->data);
    }

    public function add() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('member_id', lang('member_id'), 'trim|required');
        $this->form_validation->set_rules('loan_type', lang('loan_type'), 'trim|required');
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required');
        $this->form_validation->set_rules('tenure', lang('tenure'), 'trim|required');

        if ($this->form_validation->run()) {
            $guarantor = $this->input->post('guarantor');
            $member_id = $this->input->post('member_id');
            $loan_type_id = $this->input->post('loan_type');
            $amount = str_replace(',', '', $this->input->post('amount'));
            $tenure = $this->input->post('tenure');
            $member = $this->common->get_this('users', ['username' => $member_id]);
            $loan_type = $this->common->get_this('loan_types', ['id' => $loan_type_id]);

            if ($tenure > $loan_type->max_month) {
                $this->session->set_flashdata('error', lang('tenure_cannot_exeed') . ' ' . $loan_type->max_month);
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $check_guarantor = $this->utility->check_guarantor($guarantor, $member_id, $this->coop->id);
            if (isset($check_guarantor['error'])) {
                $this->session->set_flashdata('error', $check_guarantor['error']);
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $pending_approvals = $this->common->get_this('loans', ['user_id' => $member->id, 'coop_id' => $this->coop->id, 'status' => 'request']);
            if ($pending_approvals) {
                $this->session->set_flashdata('error', lang('pending_approval_exists'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $check_duplicte_loan_type = $this->common->get_this('loans', ['user_id' => $member->id, 'loan_type_id' => $loan_type_id, 'coop_id' => $this->coop->id, 'status' => 'disbursed']);
            if ($check_duplicte_loan_type) {
                $this->session->set_flashdata('error', lang('duplicate_loan_type_exists'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $schedule = $this->utility->get_loan_breakdown($amount, $loan_type->rate, $tenure, $loan_type->calc_method);
            
            $loan = [
                'referrer_code' => $this->coop->referrer_code,
                'coop_id' => $this->coop->id,
                'user_id' => $member->id,
                'loan_type_id' => $loan_type_id,
                'tenure' => $tenure,
                'rate' => $loan_type->rate,
                'amount_requested' => $amount,
                'principal' => $amount,
                'interest' => $schedule->interest,
                'total_due' => $schedule->total_due,
                'principal_due' => $schedule->principal_due,
                'interest_due' => $schedule->interest_due,
                'monthly_due' => $schedule->monthly_due,
                'principal_remain' => $amount,
                'interest_remain' => $schedule->interest,
                'total_remain' => $schedule->total_due,
                'created_by' => $this->user->id,
                'created_on' => date('Y-m-d g:i:s'),
                'status' => 'request',
            ];

            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('loan_added')
            ];

            $this->common->start_trans();
            $loan_id = $this->common->add('loans', $loan);
            if ($check_guarantor['guarantor']) {
                foreach ($check_guarantor['guarantor'] as $g) {
                    $this->common->add('loan_guarantors', ['coop_id' => $this->coop->id, 'loan_id' => $loan_id, 'guarantor_id' => $g->id, 'request_date' => date('Y-m-d g:i:s')]);
                }
            }

            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->data['name'] = ucwords($member->first_name . ' ' . $member->last_name);
                $this->data['member_id'] = $member->username;
                $this->data['status'] = 'request';
                $this->data['principal'] = number_format($amount, 2);
                $this->data['interest'] = number_format($schedule->interest, 2);
                $this->data['monthly_due'] = number_format($schedule->monthly_due, 2);
                $this->data['total_due'] = number_format($schedule->total_due, 2);
                $this->data['status'] = 'request';
                $this->data['date'] = date('Y-m-d g:i a');
                $subject = 'Loan Request';
                $message = $this->load->view('emails/email_loan_request', $this->data, true);
                $this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $member->email, $subject, $message);

                // send email to gurantor
                if ($check_guarantor['guarantor']) {
                    foreach ($check_guarantor['guarantor'] as $g) {
                        $this->data['g_name'] = ucwords($g->first_name . ' ' . $g->last_name);
                        $this->data['g_member_id'] = $g->username;
                        $subject = "Guarantor Request";
                        $message = $this->load->view('emails/email_loan_guarantor_request', $this->data, true);
                        $this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $g->email, $subject, $message);
                    }
                }
                // send email to exco
                $excos = $this->info->get_users(['groups.id' => 1, 'users.coop_id' => $this->coop->id]);
                foreach ($excos as $exco) {
                    $this->data['exco_name'] = ucwords($exco->first_name . ' ' . $exco->last_name);
                    $this->data['exco_member_id'] = $exco->username;
                    $subject = "Loan Request";
                    $message = $this->load->view('emails/email_loan_request_exco', $this->data, true);
                    $this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $exco->email, $subject, $message);
                }
                $this->session->set_flashdata('message', lang('loan_added'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
            redirect('loan');
        } else {
            $this->data['loan_type'] = $this->common->get_all_these('loan_types', ['coop_id' => $this->coop->id]);
            $this->data['savings_source'] = $this->common->get_all('savings_source');
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->data['title'] = lang('add_loan');
            $this->data['controller'] = lang('loan');
            $this->layout->set_app_page('loan/add', $this->data);
        }
    }

    public function edit($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('member_id', lang('member_id'), 'trim|required');
        $this->form_validation->set_rules('loan_type', lang('loan_type'), 'trim|required');
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required');
        $this->form_validation->set_rules('tenure', lang('tenure'), 'trim|required');

        $id = $this->utility->un_mask($id);
        $this->data['loan'] = $this->info->get_loan_details(['loans.id' => $id, 'users.coop_id' => $this->coop->id]);
        $this->data['gurantors'] = $this->info->get_loan_guarantors(['loans.id' => $id, 'users.coop_id' => $this->coop->id]);
        if ($this->form_validation->run()) {
            $guarantor = $this->input->post('guarantor');
            $member_id = $this->input->post('member_id');
            $loan_type_id = $this->input->post('loan_type');
            $amount = str_replace(',', '', $this->input->post('amount'));
            $tenure = $this->input->post('tenure');
            $member = $this->common->get_this('users', ['username' => $member_id]);
            $loan_type = $this->common->get_this('loan_types', ['id' => $loan_type_id]);

            if ($tenure > $loan_type->max_month) {
                $this->session->set_flashdata('error', lang('tenure_cannot_exeed') . ' ' . $loan_type->max_month);
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $check_guarantor = $this->utility->check_guarantor($guarantor, $member_id, $this->coop->id);
            if (isset($check_guarantor['error'])) {
                $this->session->set_flashdata('error', $check_guarantor['error']);
                redirect($_SERVER["HTTP_REFERER"]);
            }
            $check_duplicte_loan_type = $this->common->get_this('loans', ['user_id' => $member->id, 'loan_type_id' => $loan_type_id, 'coop_id' => $this->coop->id, 'status' => 'disbursed']);
            if ($check_duplicte_loan_type) {
                $this->session->set_flashdata('error', lang('duplicate_loan_type_exists'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $schedule = $this->utility->get_loan_breakdown($amount, $loan_type->rate, $tenure, $loan_type->calc_method);
            $loan = [
                'user_id' => $member->id,
                'loan_type_id' => $loan_type_id,
                'tenure' => $tenure,
                'rate' => $loan_type->rate,
                'amount_requested' => $amount,
                'principal' => $amount,
                'interest' => $schedule->interest,
                'total_due' => $schedule->total_due,
                'principal_due' => $schedule->principal_due,
                'interest_due' => $schedule->interest_due,
                'monthly_due' => $schedule->monthly_due,
                'principal_remain' => $amount,
                'interest_remain' => $schedule->interest,
                'total_remain' => $schedule->total_due,
                'created_by' => $this->user->id,
                'status' => 'request',
            ];

            $previous_data = $this->common->get_this('loans', ['id' => $id]);
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('loan_edited'),
                'metadata' => $this->utility->activities_matadata($previous_data, $loan)
            ];
            $this->common->start_trans();
            $this->common->delete_this('loan_guarantors', ['loan_id' => $id, 'coop_id' => $this->coop->id]);
            if ($check_guarantor['guarantor']) {
                foreach ($check_guarantor['guarantor'] as $g) {
                    $this->common->add('loan_guarantors', ['coop_id' => $this->coop->id, 'loan_id' => $id, 'guarantor_id' => $g->id]);
                }
            }
            $this->common->update_this('loans', ['id' => $id, 'coop_id' => $this->coop->id], $loan);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('loan_edited'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
            redirect('loan');
        } else {
            $this->data['loan_type'] = $this->common->get_all_these('loan_types', ['coop_id' => $this->coop->id]);
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->data['title'] = lang('edit_loan');
            $this->data['controller'] = lang('loan');
            $this->layout->set_app_page('loan/edit', $this->data);
        }
    }

    public function delete($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xdelete', 'on');
        $id = $this->utility->un_mask($id);

        $previous_data = $this->common->get_this('loans', ['id' => $id]);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('loan_deleted'),
            'metadata' => $this->utility->activities_matadata($previous_data, [])
        ];

        $this->common->start_trans();
        $this->common->delete_this('loans', ['coop_id' => $this->coop->id, 'id' => $id]);
        $this->common->delete_this('loan_guarantors', ['coop_id' => $this->coop->id, 'loan_id' => $id]);
        $this->common->delete_this('loan_approvals', ['coop_id' => $this->coop->id, 'loan_id' => $id]);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('loan_deleted'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function approved_loans() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('start_date', lang("start_date"), 'trim|required');
        $this->form_validation->set_rules('end_date', lang("end_date"), 'trim|required');
        $where = [
            'loans.coop_id' => $this->coop->id,
            'loans.status' => 'approved',
            'loans.created_on>=' => $this->utility->get_this_year('start'),
            'loans.created_on<=' => $this->utility->get_this_year('end'),
        ];

        $this->data['loan_type'] = 'ALL LOANS'; 
        if ($this->form_validation->run()) {
            $where['loans.created_on>='] = $this->input->post('start_date');
            $where['loans.created_on<='] = $this->input->post('end_date');
            if ($this->input->post('loan_type')) {
                $where['loans.loan_type_id'] = $this->input->post('loan_type');
                $this->data['loan_type'] = $this->common->get_this('loan_types', ['id' => $this->input->post('loan_type')]);
            }
            $this->data['loans'] = $this->info->get_loans($where);
            $this->data['filter_total_loans'] = $this->common->sum_this('loans', $where, 'principal');
            $this->data['filter_total_interest'] = $this->common->sum_this('loans', $where, 'interest');
            $this->data['start_date'] = $this->input->post('start_date');
            $this->data['end_date'] = $this->input->post('end_date');
        } else {
            $this->data['loans'] = $this->info->get_loans(['users.coop_id' => $this->coop->id, 'loans.status' => 'approved'], 1000);
            $this->data['filter_total_loans'] = $this->common->sum_this('loans', $where, 'principal');
            $this->data['filter_total_interest'] = $this->common->sum_this('loans', $where, 'interest');
            $this->data['total_interest'] = $this->common->sum_this('loans', ['loans.coop_id' => $this->coop->id, 'loans.status' => 'approved'], 'interest');
            $this->data['start_date'] = $this->utility->get_this_year('start');
            $this->data['end_date'] = $this->utility->get_this_year('end');
        }

       
        $this->data['total_loans'] = $this->common->sum_this('loans', ['loans.coop_id' => $this->coop->id, 'loans.status' => 'approved'], 'principal');
        $this->data['loan_types'] = $this->common->get_all_these('loan_types', ['coop_id' => $this->coop->id]);
        $this->data['title'] = lang('approved_loans');
        $this->data['controller'] = lang('loan');
        $this->layout->set_app_page('loan/approved_loans', $this->data);
    }

    public function ajax_disburse_option() {
        $id = $this->input->get('id', TRUE);
        $loan = $this->common->get_this('loans', ['id' => $id, 'coop_id' => $this->coop->id]);
        $message = [
            'url' => base_url('loan/handle_disbursement/' . $this->utility->mask($loan->id))
        ];
        echo json_encode(array('status' => 'success', 'message' => $message));
    }

    public function disbursed_loans() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('loan_type', lang("loan_type"), 'trim|required');
        $where = [
            'loans.coop_id' => $this->coop->id,
            'loans.status' => 'disbursed',
            'loans.created_on>=' => $this->utility->get_this_year('start'),
            'loans.created_on<=' => $this->utility->get_this_year('end'),
        ];
        if ($this->form_validation->run()) {
            if($this->input->post('start_date') and $this->input->post('end_date')){
                $where['loans.created_on>='] = $this->input->post('start_date');
                $where['loans.created_on<='] = $this->input->post('end_date');
            }
            $where['loans.loan_type_id'] = $this->input->post('loan_type');


            $this->data['loans'] = $this->info->get_loans($where);
            $this->data['principal'] = $this->common->sum_this('loans', $where, 'principal')->principal;
            $this->data['interest'] = $this->common->sum_this('loans', $where, 'interest')->interest;
            $this->data['start_date'] = $this->input->post('start_date');
            $this->data['end_date'] = $this->input->post('end_date');
        } else {
            $this->data['loans'] = $this->info->get_loans(['users.coop_id' => $this->coop->id, 'loans.status' => 'disbursed'], 1000);
            $this->data['principal'] = $this->common->sum_this('loans', ['coop_id' => $this->coop->id, 'loans.status' => 'disbursed'], 'principal')->principal;
            $this->data['interest'] = $this->common->sum_this('loans', ['coop_id' => $this->coop->id, 'loans.status' => 'disbursed'], 'interest')->interest;
            $this->data['start_date'] = $this->utility->get_this_year('start');
            $this->data['end_date'] = $this->utility->get_this_year('end');
        }
        $loan_types = $this->common->get_all_these('loan_types', ['coop_id'=>$this->coop->id]);
        foreach($loan_types as $l){
            $this->data['loan_types'][$l->id] = $l->name;
        }
        $this->data['title'] = lang('disbursed_loans');
        $this->data['controller'] = lang('loan');
        $this->layout->set_app_page('loan/disbursed_loans', $this->data);
    }

    public function finished_loans() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('start_date', lang("start_date"), 'trim|required');
        $this->form_validation->set_rules('end_date', lang("end_date"), 'trim|required');
        $where = [
            'loans.coop_id' => $this->coop->id,
            'loans.status' => 'finished',
            'loans.created_on>=' => $this->utility->get_this_year('start'),
            'loans.created_on<=' => $this->utility->get_this_year('end'),
        ];
        if ($this->form_validation->run()) {
            $where['loans.created_on>='] = $this->input->post('start_date');
            $where['loans.created_on<='] = $this->input->post('end_date');
            $this->data['loans'] = $this->info->get_loans($where);
            $this->data['principal'] = $this->common->sum_this('loans', $where, 'principal')->principal;
            $this->data['interest'] = $this->common->sum_this('loans', $where, 'interest')->interest;
            $this->data['start_date'] = $this->input->post('start_date');
            $this->data['end_date'] = $this->input->post('end_date');
        } else {
            $this->data['loans'] = $this->info->get_loans(['users.coop_id' => $this->coop->id, 'loans.status' => 'finished'], 1000);
            $this->data['principal'] = $this->common->sum_this('loans', ['coop_id' => $this->coop->id, 'loans.status' => 'finished'], 'principal')->principal;
            $this->data['interest'] = $this->common->sum_this('loans', ['coop_id' => $this->coop->id, 'loans.status' => 'finished'], 'interest')->interest;
            $this->data['start_date'] = $this->utility->get_this_year('start');
            $this->data['end_date'] = $this->utility->get_this_year('end');
        }
        $this->data['title'] = lang('finished_loans');
        $this->data['controller'] = lang('loan');
        $this->layout->set_app_page('loan/finished_loans', $this->data);
    }

    public function handle_disbursement($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $id = $this->utility->un_mask($id);
        $loan = $this->common->get_this('loans', ['id' => $id, 'coop_id' => $this->coop->id]);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('loan_disbursed')
        ];

        $start_date = date('Y-m-d H:i:s');
        $loan_data = [
            'start_date' => $start_date,
            'end_date' => $this->utility->get_loan_end_date($start_date, $loan->tenure),
            'disbursed_date' => $start_date,
            'next_payment_date' => $this->utility->get_end_date($start_date, $this->coop->next_payment_date, true),
            'status' => 'disbursed'
        ];

        $this->common->start_trans();
        $this->common->update_this('loans', ['coop_id' => $this->coop->id, 'id' => $id], $loan_data);
        $this->utility->auto_post_to_general_ledger($loan, $loan->id, 'LOA');
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('loan_disbursed'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function preview($id) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $id = $this->utility->un_mask($id);
        $this->data['loan'] = $this->info->get_loan_details(['loans.id' => $id, 'users.coop_id' => $this->coop->id]);
        $this->data['saving_bal'] = $this->utility->get_savings_bal($this->data['loan']->user_id, $this->data['loan']->coop_id);
        $this->data['wallet_bal'] = $this->utility->get_wallet_bal($this->data['loan']->user_id, $this->data['loan']->coop_id);
        $this->data['existing_loans'] = $this->info->get_loans(['loans.user_id' => $this->data['loan']->user_id, 'loans.status' => 'disbursed']);

        //gurantor data
        $gurantor = $this->info->get_loan_guarantors(['loans.id' => $id]);
        $gurantor_data = [];
        if ($gurantor) {
            foreach ($gurantor as $g) {
                $gurantor_data[] = (object) [
                            'savings_bal' => $this->utility->get_savings_bal($g->guarantor_id, $g->coop_id),
                            'wallet_bal' => $this->utility->get_wallet_bal($g->guarantor_id, $g->coop_id),
                            'total_due' => $this->utility->get_loan_colleted($g->guarantor_id, $g->coop_id),
                            'total_remain' => $this->utility->get_loan_bal($g->guarantor_id, $g->coop_id),
                            'full_name' => $g->full_name,
                            'member_id' => $g->username,
                            'avatar' => $g->avatar,
                            'approval' => $g->status,
                            'request_date' => $g->request_date,
                            'response_date' => $g->action_date,
                            'status' => $g->status
                ];
            }
        }

        $approvals = $this->info->get_loan_approvals(['loans.id' => $id]);
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

        $this->data['guarantor'] = $gurantor_data;
        $this->data['loan_approval'] = $approval_data;
        $this->data['title'] = lang('preview');
        $this->data['controller'] = lang('loan');
        $this->layout->set_app_page('loan/preview', $this->data);
    }

    public function approve($id) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $id = $this->utility->un_mask($id);
        $approval_exist = $this->common->get_this('loan_approvals', ['loan_id' => $id, 'exco_id' => $this->user->id, 'coop_id' => $this->coop->id]);
        $loan = $this->common->get_this('loans', ['id'=>$id]);
        $member = $this->common->get_this('users', ['id'=>$loan->user_id]);

        $approval_complete = $this->utility->loan_approval_completed($id, $this->coop->loan_approval_level, false, $approval_exist);

        if (!$this->utility->guarantor_approval_completed($id, $this->coop->id)) {
            $this->session->set_flashdata('error', lang('guarantor_approval_not_completed'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('loan_approved')
        ];
        $approval_data = [
            'status' => 'approved',
            'action_date' => date('Y-m-d H:i:s')
        ];

        $this->common->start_trans();
        if ($approval_exist) { 
            $this->common->update_this('loan_approvals', ['loan_id' => $id, 'exco_id' => $this->user->id], $approval_data);
        } else {
            $approval_data['exco_id'] = $this->user->id;
            $approval_data['coop_id'] = $this->coop->id;
            $approval_data['loan_id'] = $id;
            $this->common->add('loan_approvals', $approval_data);
        }
        if ($approval_complete) { 
            $this->common->update_this('loans', ['id' => $id,], ['status' => 'approved']);
        }
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('loan_approved'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }

        if ($approval_complete) {
            $this->data['name'] = ucwords($member->first_name . ' ' . $member->last_name);
            $this->data['member_id'] = $member->username;
            $this->data['principal'] = number_format($loan->principal, 2);
            $this->data['interest'] = number_format($loan->interest, 2);
            $this->data['monthly_due'] = number_format($loan->monthly_due, 2);
            $this->data['total_due'] = number_format($loan->total_due, 2);
            $this->data['status'] = 'approved';
            $this->data['date'] = date('Y-m-d g:i a');
            $subject = 'Loan Approved';
            $message = $this->load->view('emails/email_loan_request', $this->data, true);
            $this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $member->email, $subject, $message);
            redirect('loan/approved_loans');
        } else {
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    public function decline($id) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $id = $this->utility->un_mask($id);
       
        $declines = $this->info->get_loan_approvals(['loans.id' => $id, 'loan_approvals.exco_id' => $this->user->id, 'loan_approvals.status' => 'declined']);
        $exco_approval_exist = $this->common->get_this('loan_approvals', ['loan_id' => $id, 'exco_id' => $this->user->id, 'coop_id' => $this->coop->id]);
        $declines_complete = $this->utility->loan_approval_completed($id, $this->coop->loan_approval_level);
        $this->form_validation->set_rules('note', lang('note'), 'trim|required');

        if ($this->form_validation->run()) {
            $note = $this->input->post('note');
            $approval_data = [
                'note' => $note,
                'status' => 'declined',
                'action_date' => date('Y-m-d H:i:s')
            ];

            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('loan_declined')
            ];
            $this->common->start_trans();
            if ($exco_approval_exist) { //if exco has once approved or declined
                $this->common->update_this('loan_approvals', ['loan_id' => $id, 'exco_id' => $this->user->id], $approval_data);
            } else {
                $approval_data['exco_id'] = $this->user->id;
                $approval_data['coop_id'] = $this->coop->id;
                $approval_data['loan_id'] = $id;
                $this->common->add('loan_approvals', $approval_data);
            }

            if ($declines_complete) { //if all exco has declined
                $this->common->update_this('loans', ['id' => $id,], ['status' => 'declined']);
            }

            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $subject = "Loan Request Declined";
                $this->data['subject'] = $subject;
                $this->data['message'] = $note;
                $message = $this->load->view('emails/email_broadcast', $this->data, true);
                $this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $this->user->email, $subject, $message);
                $this->session->set_flashdata('message', lang('loan_declined'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }

            if ($declines_complete) {
                redirect('loan/approved_loans');
            } else {
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function generate_disbursement_template(){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $loan_type_id = $this->input->get('loan_type', true);
        $loan_type = $this->common->get_this('loan_types', ['id' => $loan_type_id, 'coop_id' => $this->coop->id]);
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle(lang('loan_template'));
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', $this->coop->coop_name);
        $this->excel->getActiveSheet()->setCellValue('A2', $loan_type->name . ' Disbursement Template');
        $this->excel->getActiveSheet()->setCellValue('A3', 'Change the value "NO" to "YES to incate a loan has been disbursed"');
        $this->excel->getActiveSheet()->setCellValue('B5', lang('member_id'));
        $this->excel->getActiveSheet()->setCellValue('C5', lang('full_name'));
        $this->excel->getActiveSheet()->setCellValue('D5', lang('amount'));
        $this->excel->getActiveSheet()->setCellValue('E5', lang('disbursed'));
        //merge cell A1 until I1
        $this->excel->getActiveSheet()->mergeCells('A1:I1');
        //merge cell A2 until I2
        $this->excel->getActiveSheet()->mergeCells('A2:I2');
        //merge cell A2 until I3
        $this->excel->getActiveSheet()->mergeCells('A3:I3');
        //set aligment to center for that merged cell (A1 to I1)
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //set aligment to center for that merged cell (A2 to I2)
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //set aligment to center for that merged cell (A2 to I2)
        $this->excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //make the font become bold
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
        $this->excel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('#080');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(14);
        $this->excel->getActiveSheet()->getStyle('A2')->getFill()->getStartColor()->setARGB('#f00');
        $this->excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(false);
        $this->excel->getActiveSheet()->getStyle('A3')->getFont()->setSize(14);
        $this->excel->getActiveSheet()->getStyle('A3')->getFill()->getStartColor()->setARGB('#c00');

        for ($col = ord('B'); $col <= ord('E'); $col++) {
            $this->excel->getActiveSheet()->getStyle(chr($col) . '5')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle(chr($col) . '5')->getFont()->setSize(14);
            $this->excel->getActiveSheet()->getStyle(chr($col) . '5')->getFill()->getStartColor()->setARGB('#333');
        }
        for ($col = ord('B'); $col <= ord('E'); $col++) { //set column dimension $this->excel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(true);
            //change the font size
            $this->excel->getActiveSheet()->getStyle(chr($col))->getFont()->setSize(14);
            $this->excel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(true);
            $this->excel->getActiveSheet()->getStyle(chr($col))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        //retrive schedule table data
        //$exceldata="";
        $exceldata = $this->info->get_loans(['loans.loan_type_id' => $loan_type_id, 'loans.status' => 'approved', 'loans.coop_id' => $this->coop->id]);

        //Fill data
        //$this->excel->getActiveSheet()->fromArray($exceldata, null, 'A6');
        $k = 6;
        foreach ($exceldata as $value) {
            $this->excel->getActiveSheet()->setCellValue('B' . $k, $value->username);
            $this->excel->getActiveSheet()->setCellValue('C' . $k, $value->full_name);
            $this->excel->getActiveSheet()->setCellValue('D' . $k, $value->monthly_due);
            $this->excel->getActiveSheet()->setCellValue('E' . $k, 'NO');
            $k += 1;
        }

        $this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $filename = 'approved-loan-template' . $this->coop->id . '.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('assets/files/' . $filename); //php://output
        echo json_encode(["status" => 'success', "message" => "assets/files/" . $filename]);
    }

    public function upload_batch_disbursement(){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->load->helper('security');
        $this->form_validation->set_rules('file', lang("upload_file"), 'xss_clean');
        $this->form_validation->set_rules('loan_type', lang('loan_type'), 'trim|required');

        if (isset($_FILES['file']) and $_FILES['file']['error'] == 0) {
            $field_name = 'file';
            $file_name = 'batchloan';
            $is_uploaded = $this->utility->file_upload($field_name, $file_name);
            if (isset($is_uploaded['error'])) {
                $this->session->set_flashdata('error', $is_uploaded['error']);
                redirect($_SERVER["HTTP_REFERER"]);
            }
            $filename = $is_uploaded['upload_data']['file_name'];
        }

        if ($this->form_validation->run()) {
            $file_path = 'assets/files/uploads/' . $filename;
            $objPHPExcel = PHPExcel_IOFactory::load($file_path);
            $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
            $arrayCount = count($allDataInSheet);  // Here get total count of row in that Excel sheet

            $loan_type = $this->input->post('loan_type');
            $total_success = 0;
            $total_fail = 0;
            for ($i = 6; $i <= $arrayCount; $i++) {
                $user = $this->common->get_this('users', ['username' => $allDataInSheet[$i]["B"]]);
                $loan = $this->common->get_this('loans', ['user_id' => $user->id, 'loan_type_id' => $loan_type, 'status' => 'approved']);
                $disbursed = strtoupper( trim( $allDataInSheet[$i]["E"]) );
                $start_date = date('Y-m-d H:i:s');
                $loan_data = [
                    'start_date' => $start_date,
                    'end_date' => $this->utility->get_loan_end_date($start_date, $loan->tenure),
                    'disbursed_date' => $start_date,
                    'next_payment_date' => $this->utility->get_end_date($start_date, $plus_a_month = 1, true),
                    'status' => 'disbursed'
                ];
                if($user and $disbursed=='YES'){
                    $this->common->update_this('loans', ['coop_id' => $this->coop->id, 'id' => $loan->id], $loan_data);
                    $this->utility->auto_post_to_general_ledger($loan, $loan->id, 'LOA');
                    $total_success++ ;
                }else{
                    $total_fail++ ;
                }
                
            }

            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('batch_disbursement')
            ];
           
            $this->common->add('activities', $activities);
            $this->session->set_flashdata('message', $total_success.' '.lang('record_upload_success').' <br> '.$total_fail.' '. lang('record_upload_failed'));
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect('loan/approved_loans');
    }

}
