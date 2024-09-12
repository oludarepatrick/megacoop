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
        $this->data['wallet_bal'] = $this->utility->get_agent_wallet_bal($this->user->id, $this->coop->id);
        $this->data['total_savings'] = $this->common->sum_this('savings', ['coop_agent_id'=>$this->user->id], 'amount');
        $this->data['pass_expired'] = $this->utility->password_expired($this->user->id);
        $this->data['title'] = lang('dashboard');
        $this->data['controller'] = lang('dashboard');
        $this->layout->set_app_page_agency('dashboard/index', $this->data);
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
