<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends BASE_Controller {

    public function __construct() {
        parent::__construct();
        if ($this->coop->status == 'processing') {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function index() {
        $this->data['savings_bal'] = $this->utility->get_savings_bal($this->user->id, $this->coop->id);
        $this->data['wallet_bal'] = $this->utility->get_wallet_bal($this->user->id, $this->coop->id);
        $this->data['loan_bal'] = $this->utility->get_loan_bal($this->user->id, $this->coop->id);
        $this->data['credit_sales_bal'] = $this->utility->get_credit_sales_bal($this->user->id, $this->coop->id);
        //pichart data
        $this->data['overview_data'] = json_encode([
            'savings_bal' => (float) $this->data['savings_bal'],
            'wallet_bal' => (float) $this->data['wallet_bal'],
            'loan_bal' => (float) $this->data['loan_bal'],
            'credit_sales_bal' => (float) $this->data['credit_sales_bal'],
        ]);

        $this->data['pass_expired'] = $this->utility->password_expired($this->user->id);
        $this->data['credit_worthines'] = $this->utility->credit_worthines( $this->coop->id,$this->user->id);
        $this->data['title'] = lang('dashboard');
        $this->data['controller'] = lang('dashboard');
        $this->layout->set_app_page_member('member/dashboard/index', $this->data);
    }

    public function support() {
        $this->form_validation->set_rules('subject', lang('subject'), 'trim|required');
        $this->form_validation->set_rules('message', lang('message'), 'trim|required');
        if ($this->form_validation->run()) {
            $subject = $this->input->post('subject');
            $content = $this->input->post('message');
            $this->data['message'] = $content;
            $this->data['subject'] = $subject;
            $message = $this->load->view('emails/email_support', $this->data, true);
            if ($this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $this->app_settings->company_email, $subject, $message)) {
                $this->session->set_flashdata('message', lang('act_successful'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        }
    }

}
