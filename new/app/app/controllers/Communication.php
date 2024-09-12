<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Communication extends BASE_Controller {

    const MENU_ID = 6;

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
    }

    public function index() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->data['title'] = lang('communication');
        $this->data['controller'] = lang('communication');
        $this->layout->set_app_page('communication/index', $this->data);
    }

    public function send() {
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
            $channel = $this->input->post('channel');
            $subject = $this->input->post('subject');
            $content = $this->input->post('message');

            if ($channel == 'sms' and $this->coop->sms_notice == 'on') {
                if (is_array($all_recipients)) {
                    $phone ='';
                    foreach ($all_recipients as $recipient) {
                        $phone .= $recipient->phone . ',';
                    }
                    $phone = trim($phone, ',');
                } else {
                    $phone = $all_recipients;
                }
                if( $this->fivelinks->send_SMS($phone,strip_tags($content), true)){
                    $this->session->set_flashdata('message', lang('act_successful'));
                } else {
                    $this->session->set_flashdata('error', lang('act_unsuccessful'));
                } 
            }

            if ($channel == 'email') {
                if (is_array($all_recipients)) {
                    $emails = [];
                    foreach ($all_recipients as $recipient) {
                        $email = filter_var($recipient->email, FILTER_SANITIZE_EMAIL);
                        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                            $emails[] = $email;
                        }
                    }
                       
                    $emails = $emails;
                } else {
                    $emails = $all_recipients;
                }
                $this->data['message'] = $content;
                $this->data['subject'] = $subject;
                $message = $this->load->view('emails/email_broadcast', $this->data, true);
                if($this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $emails, $subject, $message)){
                    $this->session->set_flashdata('message', lang('act_successful'));
                } else {
                    $this->session->set_flashdata('error', lang('act_unsuccessful'));
                }
            }
            
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function sms_report(){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->data['sms'] = $this->info->get_sms_report(['sms_log.coop_id'=>$this->coop->id, 'payment' => 'unsettled']);
        $this->data['settled'] = $this->common->sum_this('sms_log', ['sms_log.coop_id' => $this->coop->id, 'payment'=>'settled'] , 'price');
        $this->data['unsettled'] = $this->common->sum_this('sms_log', ['sms_log.coop_id' => $this->coop->id, 'payment'=>'unsettled'] , 'price');
        $this->data['savings_type'] = $this->common->get_all_these('savings_types', ['coop_id' => $this->coop->id]);
        $this->data['title'] = lang('sms_report');
        $this->data['controller'] = lang('communication');
        $this->layout->set_app_page('communication/sms_report', $this->data);
    }

    public function settle_sms_fee(){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('savings_type', lang('savings_type'), 'trim|required');
        $unsettled =  $this->info->get_sms_report(['sms_log.coop_id'=>$this->coop->id, 'payment'=>'unsettled']);
        $savings_type = $this->input->post('savings_type');
        if ($this->form_validation->run() and  $unsettled) {
            $count = 0;
            foreach ($unsettled as $s) {
                $old_bal = $this->utility->get_savings_bal($s->user_id, $this->coop->id, $savings_type);

                if ($old_bal < $s->price) {
                    continue;
                }
                $new_bal = $old_bal - $s->price;
                $tranx_ref = $s->user_id . 'DEF' . date('Ymdhis');
                $withdrawal = [
                    'tranx_ref' => $tranx_ref,
                    'tranx_type' => 'debit',
                    'referrer_code' => $this->coop->referrer_code,
                    'coop_id' => $this->coop->id,
                    'user_id' => $s->user_id,
                    'balance' => $new_bal,
                    'amount' => $s->price,
                    'month_id' => date('n'),
                    'year' => date('Y'),
                    'savings_type' => $savings_type,
                    'source' => 0,
                    'narration' => "SMS Charges",
                    'status' => 'paid',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'score' => 0
                ];

                $this->common->start_trans();
                $item_id = $this->common->add('savings', $withdrawal);
                $this->common->update_this('sms_log', ['user_id' => $s->user_id],['payment'=>"settled", 'settled_on'=>date('Y-m-d- H:i:s')]);
                $this->utility->auto_post_to_general_ledger((object)$withdrawal, $item_id, "WIT");
                $this->common->finish_trans();
                if ($this->common->status_trans()) {
                    $count++;
                }
            }

            if($count > 0){
                $activities = [
                    'coop_id' => $this->coop->id,
                    'user_id' => $this->user->id,
                    'action' => "SMS Charges Settled"
                ];
                $this->common->add('activities', $activities);
                $this->session->set_flashdata('message', $count." of ".count($unsettled) ."member(s) SMS fee settled");
            }else{
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
            redirect($_SERVER['HTTP_REFERER']);
        }else{
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            if(!$unsettled and !$err){
                $err = "No SMS fee to be settled";
            }
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
}
