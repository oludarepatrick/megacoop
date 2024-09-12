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
        $this->form_validation->set_rules('start_date', lang("start_date"), 'trim|required');
        $this->form_validation->set_rules('end_date', lang("end_date"), 'trim|required');
        
        $this->data['loan_type'] = 'ALL LOANS' ; 
        if ($this->form_validation->run()) {
            $where['loans.created_on>='] = $this->input->post('start_date');
            $where['loans.created_on<='] = $this->input->post('end_date');
            if($this->input->post('loan_type')){
                $where['loans.loan_type_id'] = $this->input->post('loan_type');
                $this->data['loan_type'] = $this->common->get_this('loan_types', ['id' => $this->input->post('loan_type')]); 
            }
            $this->data['loans'] = $this->info->get_loans($where);
            $this->data['start_date'] = $this->input->post('start_date');
            $this->data['end_date'] = $this->input->post('end_date');
        } else {
            $where = [
                'users.coop_agent_id' => $this->user->id,
                'loans.coop_id' => $this->coop->id,
                'loans.status!=' => 'finished',
            ];
            $this->data['loans'] = $this->info->get_loans($where, 1000);
        }
        $this->data['loan_types'] = $this->common->get_all_these('loan_types', ['coop_id' => $this->coop->id]);
        $this->data['title'] = lang('loans');
        $this->data['controller'] = lang('loan');
        $this->layout->set_app_page_agency('loan/index', $this->data);
    }

    public function add() {
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
            redirect('agency/loan');
        } else {
            $this->data['loan_type'] = $this->common->get_all_these('loan_types', ['coop_id' => $this->coop->id]);
            $this->data['savings_source'] = $this->common->get_all('savings_source');
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->data['title'] = lang('add_loan');
            $this->data['controller'] = lang('loan');
            $this->layout->set_app_page_agency('loan/add', $this->data);
        }
    }

    public function edit($id = null) {
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

            $check_guarantor = $this->utility->check_guarantor($guarantor, $member_id);
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

            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('loan_edited')
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
            redirect('agency/loan');
        } else {
            $this->data['loan_type'] = $this->common->get_all_these('loan_types', ['coop_id' => $this->coop->id]);
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->data['title'] = lang('edit_loan');
            $this->data['controller'] = lang('loan');
            $this->layout->set_app_page_agency('loan/edit', $this->data);
        }
    }

    public function delete($id = null) {
        $id = $this->utility->un_mask($id);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('request_deleted')
        ];

        $this->common->start_trans();
        $this->common->delete_this('loans', ['coop_id' => $this->coop->id, 'id' => $id]);
        $this->common->delete_this('loan_guarantors', ['coop_id' => $this->coop->id, 'loan_id' => $id]);
        $this->common->delete_this('loan_approvals', ['coop_id' => $this->coop->id, 'loan_id' => $id]);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('request_deleted'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect('agency/loan');
    }

    public function preview($id) {
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
        $this->layout->set_app_page_agency('loan/preview', $this->data);
    }
}
