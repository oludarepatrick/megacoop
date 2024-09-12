<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Guarantor extends BASE_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->data['loan_guaranteed'] = $this->info->get_loan_guaranteed([
            'loans.status' => 'request',
            'loan_guarantors.status' => 'request',
            'loan_guarantors.guarantor_id' => $this->user->id,
            'loan_guarantors.coop_id' => $this->coop->id
        ]);

        $this->data['credit_sales_guaranteed'] = $this->info->get_credit_sales_guaranteed([
            'credit_sales.status' => 'request',
            'credit_sales_guarantors.status' => 'request',
            'credit_sales_guarantors.guarantor_id' => $this->user->id,
            'credit_sales_guarantors.coop_id' => $this->coop->id
        ]);


        $this->data['title'] = lang('guarantor') . ' ' . lang('request');
        $this->data['controller'] = lang('guarantor');
        $this->layout->set_app_page_member('member/guarantor/index', $this->data);
    }

    public function preview($src, $id = null) {
        $id = $this->utility->un_mask($id);
        if ($src === 'loan') {
            $this->data['loan'] = $this->info->get_loan_details(['loans.id' => $id, 'users.coop_id' => $this->coop->id]);
        }
        if ($src === 'crs') {
            $this->data['loan'] = $this->info->get_credit_sales_details(['credit_sales.id' => $id, 'users.coop_id' => $this->coop->id]);
        }
        $this->data['src'] = $src;
        $this->data['title'] = lang('preview') . ' ' . lang('request');
        $this->data['controller'] = lang('guarantor');
        $this->layout->set_app_page_member('member/guarantor/preview', $this->data);
    }

    public function approve($src, $id) {
        $id = $this->utility->un_mask($id);
        $approval_data = [
            'status' => 'approved',
            'action_date' => date('Y-m-d H:i:s')
        ];
        
        $approv = false;
        if($src ==='loan'){
            $approv = $this->common->update_this('loan_guarantors', ['loan_id' => $id, 'guarantor_id' => $this->user->id], $approval_data);
        }

        if($src ==='crs'){
            $approv = $this->common->update_this('credit_sales_guarantors', ['credit_sales_id' => $id, 'guarantor_id' => $this->user->id], $approval_data);
        }

        if ($approv) {
            $user_loan = $this->common->get_this('loans', ['id' => $id]);
            $notification_m = [
                'coop_id' => $this->coop->id,
                'user_id' => $user_loan->user_id,
                'from' => $this->user->first_name . ' ' . $this->user->last_name,
                'description' => lang('guarantor_request') . ' ' . lang('approved'),
                'url' => base_url('member/loan')
            ];
            $this->common->add('member_notification', $notification_m);

            $subject = "Guarantor Request Approved";
            $this->data['subject'] = $subject;
            $this->data['message'] = ucwords($this->user->first_name.' '. $this->user->last_name).' has accepted to guarantee your loan request';
            $message = $this->load->view('emails/email_broadcast', $this->data, true);
            $this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $this->user->email, $subject, $message);
                
            $this->session->set_flashdata('  mmessage', lang('act_successful'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }

        redirect('member/guarantor');
    }

    public function decline($src, $id) {
        $id = $this->utility->un_mask($id);
        $this->form_validation->set_rules('note', lang('note'), 'trim|required');

        if ($this->form_validation->run()) {
            $note = $this->input->post('note');
            $approval_data = [
                'note' => $note,
                'status' => 'declined',
                'action_date' => date('Y-m-d H:i:s')
            ];
           
            $approv = false;
            if ($src === 'loan') {
                $approv = $this->common->update_this('loan_guarantors', ['loan_id' => $id, 'guarantor_id' => $this->user->id], $approval_data);
            }
            if ($src === 'crs') {
                $approv = $this->common->update_this('credit_sales_guarantors', ['credit_sales_id' => $id, 'guarantor_id' => $this->user->id], $approval_data);
            }

            if ($approv) {
                $user_loan = $this->common->get_this('loans', ['id' => $id]);
                $notification_m = [
                    'coop_id' => $this->coop->id,
                    'user_id' => $user_loan->user_id,
                    'from' => $this->user->first_name . ' ' . $this->user->last_name,
                    'description' => lang('guarantor_request').' '. lang('declined'),
                    'url' => base_url('member/loan')
                ];
                $this->common->add('member_notification', $notification_m);

                $subject = "Guarantor Request Declined";
                $this->data['subject'] = $subject;
                $this->data['message'] = $note;
                $message = $this->load->view('emails/email_broadcast', $this->data, true);
                $this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $this->user->email, $subject, $message);
                $this->session->set_flashdata('message', lang('act_successful'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        }else{
            $this->session->set_flashdata('error', validation_errors());
        }
        redirect('member/guarantor');
    }

}
