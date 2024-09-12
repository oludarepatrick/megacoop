<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Support extends BASE_Controller {
    const MENU_ID = 14;

    public function __construct() {
        parent::__construct();
        if(!$this->ion_auth->is_admin()){
            redirect($_SERVER['HTTP_REFERER']);
        }
        
    }

    public function index() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('subject', lang('subject'), 'trim|required');
        $this->form_validation->set_rules('message', lang('message'), 'trim|required');
        $this->form_validation->set_rules('priority', lang('priority'), 'trim|required');
        if ($this->form_validation->run()) {
            $tickets = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'subject' => $this->input->post('subject'),
                'description' => $this->input->post('message'),
                'priority' => $this->input->post('priority'),
                'created_on' => date('Y-m-d H:i:s'),
                'status' => 'open',
            ];
            
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('support_requested')
            ];
            
            $this->common->start_trans();
            $this->common->add('tickets', $tickets);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('support_requested'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->data['title'] = lang('support');
            $this->data['controller'] = lang('support');
            $this->layout->set_app_page('support/index', $this->data);
        }
    }

    function send() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('subject', lang('subject'), 'trim|required');
        $this->form_validation->set_rules('message', lang('message'), 'trim|required');
        $this->form_validation->set_rules('recip_type', lang('recip_type'), 'trim|required');
        $recipent_typ = $this->input->post('recip_type');
        if ($recipent_typ == 'custom') {
            $this->form_validation->set_rules('recipients', lang('recipaients'), 'trim|required');
        }
        if ($this->form_validation->run()) {
            if ($recipent_typ == 'admin') {
                $all_recipients = $this->info->get_users(['users_groups.group_id' => 1, 'users.coop_id' => $this->coop->id]);
            } elseif ($recipent_typ == 'member') {
                $all_recipients = $this->info->get_users(['users_groups.group_id' => 2, 'users.coop_id' => $this->coop->id]);
            } elseif ($recipent_typ == 'all') {
                $all_recipients = $this->info->get_users(['users.coop_id' => $this->coop->id]);
            } else {
                $all_recipients = $this->input->post('recipients');
            }
            $emails = '';
            if (is_array($all_recipients)) {
                foreach ($all_recipients as $recipient) {
                    $emails .= $recipient->email . ',';
                }
                $emails = trim($emails, ',');
            } else {
                $emails = $all_recipients;
            }
            $subject = $this->input->post('subject');
            $this->data['message'] = $this->input->post('message');
            $message = $this->load->view('emails/email_broadcast', $this->data, true);
            $this->utility->send_mail($this->coop->contact_email, $this->coop->coop_name, $emails, $subject, $message);
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

}
