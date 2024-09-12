<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Loan extends BASE_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->form_validation->set_rules('start_date', lang("start_date"), 'trim|required');
        $this->form_validation->set_rules('end_date', lang("end_date"), 'trim|required');
        $where = "loans.user_id= {$this->user->id} AND loans.coop_id={$this->coop->id} AND (loans.status='approved' OR loans.status='disbursed' OR loans.status='request')";
        $where2 = "loans.user_id= {$this->user->id} AND loans.coop_id={$this->coop->id} AND loans.status='disbursed'";

        if ($this->form_validation->run()) {
            $this->data['loans'] = $this->info->get_loans($where);
            $this->data['total_due'] = $this->common->sum_this('loans', $where2, 'total_due')->total_due;
            $this->data['total_remain'] = $this->common->sum_this('loans', $where2, 'total_remain')->total_remain;
        } else {
            $this->data['loans'] = $this->info->get_loans($where, 1000);
            $this->data['total_due'] = $this->common->sum_this('loans', $where2, 'total_due')->total_due;
            $this->data['total_remain'] = $this->common->sum_this('loans', $where2, 'total_remain')->total_remain;
        }
        $this->data['title'] = lang('loan');
        $this->data['controller'] = lang('loan');
        $this->layout->set_app_page_member('member/loan/index', $this->data);
    }

    public function ajax_preview_loan_schedule() {
        $loan_type_id = $this->input->get('loan_type', TRUE);
        $tenure = (int) $this->input->get('tenure', TRUE);
        $amount = (float) str_replace(',', '', $this->input->get('amount', TRUE));
        $loan_type = $this->common->get_this('loan_types', ['id' => $loan_type_id, 'coop_id' => $this->coop->id]);
        $schedule = $this->utility->get_loan_breakdown($amount, $loan_type->rate, $tenure);
        $message = [
            'principal' => number_format($amount, 2),
            'interest' => number_format($schedule->interest, 2),
            'monthly_due' => number_format($schedule->monthly_due, 2),
            'total_due' => number_format($schedule->total_due, 2),
            'tenure' => $tenure,
        ];
        echo json_encode(array('status' => 'success', 'message' => $message));
    }

    public function preview($id) {
        $id = $this->utility->un_mask($id);
        //member_data
        $this->data['loan'] = $this->info->get_loan_details(['loans.id' => $id, 'users.coop_id' => $this->coop->id]);
        $this->data['saving_bal'] = $this->utility->get_savings_bal($this->data['loan']->user_id, $this->data['loan']->coop_id);
        $this->data['wallet_bal'] = $this->utility->get_wallet_bal($this->data['loan']->user_id, $this->data['loan']->coop_id);

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
                            'status' => $g->status,
                            'note' => $g->note,
                            'guarantor_id' => $g->guarantor_id,
                            'loan_guarantor_id' => $g->id
                ];
            }
        }

        $this->data['repayment_schedule'] = $this->utility->repayment_shedule_generator($this->data['loan']);
        // var_dump($this->data['repayment_schedule']);exit;
        $this->data['guarantor'] = $gurantor_data;
        $this->data['title'] = lang('preview');
        $this->data['sub_title'] = lang('repayment').' '. lang('schedule');
        $this->data['controller'] = lang('loan');
        $this->layout->set_app_page_member('member/loan/preview', $this->data);
    }

    public function repay($id = null) {
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required');

        $id = $this->utility->un_mask($id);
        $this->data['loan'] = $this->info->get_loan_details(['loans.id' => $id, 'users.coop_id' => $this->coop->id]);
        $this->data['member'] = $this->info->get_user_details(['users.id' => $this->data['loan']->user_id, 'users.coop_id' => $this->coop->id]);
        if ($this->form_validation->run()) {
            $amount = str_replace(',', '', $this->input->post('amount'));

            if ($amount <= 0) {
                $this->session->set_flashdata('error', lang('invalid_amount'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            if ($amount > $this->data['loan']->total_remain) {
                $this->session->set_flashdata('error', lang('amount_geater_than_bal'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $source = 1;
            //source == member wallet
            if ($source == 1) {
                $wallet_bal = $this->utility->get_wallet_bal($this->data['member']->id, $this->coop->id);
                if ($amount > $wallet_bal) {
                    $this->session->set_flashdata('error', lang('low_wallet_bal'));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                $wallet = [
                    'tranx_ref' => $this->data['member']->id . 'WAL' . date('Ymdhis'),
                    'referrer_code' => $this->coop->referrer_code,
                    'coop_id' => $this->coop->id,
                    'user_id' => $this->data['member']->id,
                    'amount' => $amount,
                    'tranx_type' => 'debit',
                    'gate_way_id' => 0,
                    'narration' => "Loan Repayment",
                    'status' => 'successful',
                ];
            } else {
                $wallet['tranx_ref'] = $this->data['member']->id . 'DEF' . date('Ymdhis');
            }

            $splited_amount = $this->utility->split_repayment_amt($amount, $this->data['loan']);

            $loan_data = [
                'principal_remain' => $splited_amount->principal_remain,
                'interest_remain' => $splited_amount->interest_remain,
                'total_remain' => $splited_amount->total_remain,
                'next_payment_date' => $this->utility->get_end_date(date('Y-m-d H:i:s'), $plus_a_month = 1, true),
            ];

            if ($splited_amount->total_remain == 0 && $splited_amount->principal_remain == 0 && $splited_amount->interest_remain == 0) {
                $loan_data['status'] = 'finished';
            }

            $loan_repayment_data = [
                'referrer_code' => $this->coop->referrer_code,
                'coop_id' => $this->coop->id,
                'tranx_ref' => $wallet['tranx_ref'],
                'user_id' => $this->data['member']->id,
                'loan_id' => $id,
                'loan_type_id' => $this->data['loan']->loan_type_id,
                'principal_repayment' => $splited_amount->principal_repayment,
                'interest_repayment' => $splited_amount->interest_repayment,
                'amount' => $splited_amount->amount,
                'principal_remain' => $splited_amount->principal_remain,
                'interest_remain' => $splited_amount->interest_remain,
                'amount_remain' => $splited_amount->total_remain,
                'month_id' => date('n'),
                'year' => date('Y'),
                'source' => $source,
                'narration' => date('n') . ' ' . date('Y') . ' Loan Repayment',
                'created_by' => $this->user->id,
                'created_on' => date('Y-m-d H:i:s'),
                'status' => 'paid',
            ];
            
            $this->common->start_trans();
            if ($source == 1) {
                $this->common->add('wallet', $wallet);
            }
            $item_id = $this->common->add('loan_repayment', $loan_repayment_data);
            $this->utility->auto_post_to_general_ledger((object)$loan_repayment_data, $item_id, 'LOAR');
            $this->common->update_this('loans', ['id' => $id], $loan_data);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('loan_repayment_added'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function repayment_history($id) {
        $id = $this->utility->un_mask($id);
        $where = [
            'loan_repayment.user_id' => $this->user->id,
            'loan_repayment.coop_id' => $this->coop->id,
            'loan_repayment.status' => 'paid',
            'loan_repayment.loan_id' => $id
        ];

        $this->data['loan_repayment'] = $this->info->get_loan_repayment($where, 1000, true);
        $this->data['loan'] = $this->common->get_this('loans', ['id' => $id, 'user_id' => $this->user->id, 'coop_id' => $this->coop->id]);
        $this->data['title'] = lang('repayment_history');
        $this->data['controller'] = lang('loan_repayment');
        $this->layout->set_app_page_member('member/loan/repayment_history', $this->data);
    }

    public function receipt($id = null) {
        $id = $this->utility->un_mask($id);
        $this->data['loan'] = $this->info->get_loan_repayment_details([
            'loan_repayment.user_id' => $this->user->id,
            'loan_repayment.id' => $id,
            'users.coop_id' => $this->coop->id
        ]);
        $this->data['title'] = lang('print');
        $this->data['controller'] = lang('loan');
        $this->layout->set_app_page_member('member/loan/receipt', $this->data);
    }

    public function request() {
        $this->form_validation->set_rules('member_id', lang('member_id'), 'trim|required');
        $this->form_validation->set_rules('loan_type', lang('loan_type'), 'trim|required');
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required');
        $this->form_validation->set_rules('tenure', lang('tenure'), 'trim|required');

        if ($this->form_validation->run()) {
            $guarantor = $this->input->post('guarantor');
            $loan_type_id = $this->input->post('loan_type');
            $amount = str_replace(',', '', $this->input->post('amount'));
            $tenure = $this->input->post('tenure');
            
            $loan_type = $this->common->get_this('loan_types', ['id' => $loan_type_id]);
            
            if ($amount <= 0) {
                $this->session->set_flashdata('error', lang('invalid_amount'));
                redirect($_SERVER["HTTP_REFERER"]);
            }
            
            $saving_bal  = $this->utility->get_savings_bal($this->user->id, $this->coop->id);
            $requestable = $this->utility->get_requestable($saving_bal, $this->coop->max_loan_requestable);
            
            if ($this->coop->max_loan_requestable and ($amount > $requestable)) {
                $this->session->set_flashdata('error', "Cannot request more than ". number_format($requestable, 2) );
                redirect($_SERVER["HTTP_REFERER"]);
            }

            if ($tenure > $loan_type->max_month) {
                $this->session->set_flashdata('error', lang('tenure_cannot_exeed') . ' ' . $loan_type->max_month);
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $check_guarantor = $this->utility->check_guarantor($guarantor, $this->user->username, $this->coop->id);
            if (isset($check_guarantor['error'])) {
                $this->session->set_flashdata('error', $check_guarantor['error']);
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $pending_approvals = $this->common->get_this('loans', ['user_id' => $this->user->id, 'coop_id' => $this->coop->id, 'status' => 'request']);
            if ($pending_approvals) {
                $this->session->set_flashdata('error', lang('pending_approval_exists'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $check_duplicte_loan_type = $this->common->get_this('loans', ['user_id' => $this->user->id, 'loan_type_id' => $loan_type_id, 'coop_id' => $this->coop->id, 'status' => 'disbursed']);
            if ($check_duplicte_loan_type) {
                $this->session->set_flashdata('error', lang('duplicate_loan_type_exists'));
                redirect($_SERVER["HTTP_REFERER"]);
            }
            
            $schedule = $this->utility->get_loan_breakdown($amount, $loan_type->rate, $tenure, $loan_type->calc_method);

            $loan = [
                'referrer_code' => $this->coop->referrer_code,
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
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
            $notification = [
                'coop_id' => $this->coop->id,
                'from' => $this->user->first_name . ' ' . $this->user->last_name,
                'description' => lang('loan_request'),
                'url' => base_url('loan')
            ];

            $this->common->start_trans();
            $loan_id = $this->common->add('loans', $loan);
            if ($check_guarantor['guarantor']) {
                foreach ($check_guarantor['guarantor'] as $g ) {
                    $notification_g = [
                        'coop_id' => $this->coop->id,
                        'user_id' => $g->id,
                        'from' => $this->user->first_name . ' ' . $this->user->last_name,
                        'description' => lang('guarantor_request'),
                        'url' => base_url('member/guarantor')
                    ];

                    $this->common->add('loan_guarantors', ['coop_id' => $this->coop->id, 'loan_id' => $loan_id, 'guarantor_id' => $g->id, 'request_date' => date('Y-m-d g:i:s')]);
                    $this->common->add('member_notification', $notification_g);
                }
            }

            $this->common->add('exco_notification', $notification);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->data['name'] = ucwords($this->user->first_name . ' ' . $this->user->last_name);
                $this->data['member_id'] = $this->user->username;
                $this->data['status'] = 'request';
                $this->data['principal'] = number_format($amount, 2);
                $this->data['interest'] = number_format($schedule->interest, 2);
                $this->data['monthly_due'] = number_format($schedule->monthly_due, 2);
                $this->data['total_due'] = number_format($schedule->total_due, 2);
                $this->data['status'] = 'request';
                $this->data['date'] = date('Y-m-d g:i a');
                $subject = 'Loan Request';
                $message = $this->load->view('emails/email_loan_request', $this->data, true);
                $this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $this->user->email, $subject, $message);

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
                $this->session->set_flashdata('message', lang('loan_request_successful'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
            redirect('member/loan');
        } else {
            $this->data['loan_type'] = $this->common->get_all_these('loan_types', ['coop_id' => $this->coop->id]);
            $this->data['savings_source'] = $this->common->get_all('savings_source');

            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->data['title'] = lang('request_loan');
            $this->data['controller'] = lang('loan');
            $this->layout->set_app_page_member('member/loan/request', $this->data);
        }
    }

    public function change_guarantor(){
        $this->form_validation->set_rules('member_id', lang('member_id'), 'trim|required');

        if ($this->form_validation->run()) {
            $loan_g = $this->input->post('loan_guarantor_id');
            $member_id[] = $this->input->post('member_id');
            $check_guarantor = $this->utility->check_guarantor($member_id, $this->user->username, $this->coop->id);
            $new_guarantor =  $check_guarantor['guarantor'][0];

            if (isset($check_guarantor['error'])) {
                $this->session->set_flashdata('error', $check_guarantor['error']);
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $gurantor_data = [
                'guarantor_id'=> $check_guarantor['guarantor'][0]->id,
                'request_date'=> date('Y-m-d H:i:s'),
                'note'=> null,
                'status'=> 'request',
                'action_date'=> null,
            ];

            $loan_guarantor = $this->common->get_this('loan_guarantors', ['id' => $loan_g]);
            $loan = $this->common->get_this('loans', ['id' => $loan_guarantor->loan_id]);
            $updated = $this->common->update_this('loan_guarantors', ['id'=> $loan_guarantor->id, 'guarantor_id'=>$loan_g], $gurantor_data);
            if($updated){
                $notification_g = [
                    'coop_id' => $this->coop->id,
                    'user_id' => $check_guarantor['guarantor'][0]->id,
                    'from' => $this->user->first_name . ' ' . $this->user->last_name,
                    'description' => lang('guarantor_request'),
                    'url' => base_url('member/guarantor')
                ];
                $this->common->add('member_notification', $notification_g);

                $this->data['name'] = ucwords($this->user->first_name . ' ' . $this->user->last_name);
                $this->data['member_id'] = $this->user->username;
                $this->data['status'] = 'request';
                $this->data['principal'] = number_format($loan->principal, 2);
                $this->data['interest'] = number_format($loan->interest, 2);
                $this->data['monthly_due'] = number_format($loan->monthly_due, 2);
                $this->data['total_due'] = number_format($loan->total_due, 2);
                $this->data['date'] = date('Y-m-d g:i a');

                $this->data['g_name'] = ucwords($new_guarantor->first_name . ' ' . $new_guarantor->last_name);
                $this->data['g_member_id'] = $new_guarantor->username;
                $subject = "Guarantor Request";
                $message = $this->load->view('emails/email_loan_guarantor_request', $this->data, true);
                $this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $new_guarantor->email, $subject, $message);
                $this->session->set_flashdata('message', "Guarantor changed");
            }
           
            redirect($_SERVER["HTTP_REFERER"]);

        }else{
            $this->session->set_flashdata('error', validation_errors());
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }
}
