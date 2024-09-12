<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Savings extends BASE_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->form_validation->set_rules('year', lang("year"), 'trim|required');
        $where = [
            'users.coop_agent_id' => $this->user->id,
            'savings.coop_id' => $this->coop->id,
            'savings.year' => date('Y'),
            'tranx_type' => 'credit'
        ];

        if ($this->form_validation->run()) {
            $where['savings.year'] = $this->input->post('year');
            $this->data['savings'] = $this->info->get_savings($where, FALSE, true);
        } else {
            $this->data['savings'] = $this->info->get_savings(['users.coop_agent_id' => $this->user->id, 'users.coop_id' => $this->coop->id], 1000, true);
        }
        $this->data['year'] = $where['savings.year'];
        $this->data['title'] = lang('savings');
        $this->data['controller'] = lang('savings');
        $this->layout->set_app_page_agency('savings/index', $this->data);
    }
    public function add(){
        $this->form_validation->set_rules('member_id', lang('member_id'), 'trim|required');
        $this->form_validation->set_rules('savings_type', lang('savings_type'), 'trim|required');
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required');
        $this->form_validation->set_rules('month', lang('month'), 'trim|required');
        $this->form_validation->set_rules('year', lang('year'), 'trim|required');
        $this->form_validation->set_rules('narration', lang('narration'), 'trim|required');

        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('savings_added')
            ];
            $user = $this->common->get_this('users', ['username' => $this->input->post('member_id')]);
            $amount = str_replace(',', '', $this->input->post('amount'));

            if ($amount <= 0) {
                $this->session->set_flashdata('error', lang('invalid_amount'));
                redirect($_SERVER["HTTP_REFERER"]);
            }
           
            $wallet_bal = $this->utility->get_agent_wallet_bal($this->user->id, $this->coop->id);
            if ($amount > $wallet_bal or $amount <= 0) {
                $this->session->set_flashdata('error', lang('low_wallet_bal'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            
            $wallet = [
                'tranx_ref' => $this->user->id . 'WAL' . date('Ymdhis'),
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'amount' => $amount,
                'tranx_type' => 'debit',
                'gate_way_id' => 8,
                'status' => 'successful',
                'narration' =>'Saving payment for'. $user->first_name
            ];

            $savings = [
                'tranx_ref' => $wallet['tranx_ref'],
                'tranx_type' => 'credit',
                'coop_agent_id' => $this->user->id,
                'referrer_code' => $this->coop->referrer_code,
                'coop_id' => $this->coop->id,
                'user_id' => $user->id,
                'balance' => $this->utility->get_savings_bal($user->id, $this->coop->id, $this->input->post('savings_type')) + $amount,
                'amount' => $amount,
                'month_id' => $this->common->get_this('months', ['name' => $this->input->post('month')])->id,
                'year' => $this->input->post('year'),
                'savings_type' => $this->input->post('savings_type'),
                'source' => 8,
                'narration' => $this->input->post('narration'),
                'status' => 'paid',
                'payment_date' => date('Y-m-d g:i:s'),
                'score' => 5
            ];

            $this->common->start_trans();
            $this->common->add('agent_wallet', $wallet);
            $item_id = $this->common->add('savings', $savings);
            $this->utility->auto_post_to_general_ledger((object)$savings, $item_id, "SAV");
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $savings_type_name = $this->common->get_this('savings_types', [
                    'coop_id' => $this->coop->id, 'id' => $this->input->post('savings_type')
                ])->name;

                //email data
                $this->data['name'] = ucwords($user->first_name . ' ' . $user->last_name);
                $this->data['member_id'] = $user->username;
                $this->data['savings_type_name'] = $savings_type_name;
                $this->data['month'] = $this->input->post('month');
                $this->data['year'] = $this->input->post('year');
                $this->data['amount'] = number_format($amount, 2);
                $this->data['status'] = 'paid';
                $this->data['balance'] = number_format($savings['balance'], 2);
                $this->data['date'] = date('Y-m-d H:i:s');
                $subject = 'Savings Notice';
                $message = $this->load->view('emails/email_savings', $this->data, true);

                if ($this->coop->sms_notice == 'on') {
                    $content = "Cedit Alert"
                    . "\n" . "ST: " . $savings_type_name
                        . "\n" . "ID: " . $this->utility->shortend_str_len($user->username, 5, '***')
                        . "\n" . "DATE: " . $this->utility->just_date(date('Y-m-d H:i:s'), true)
                        . "\n" . "AMT: NGN" . number_format($amount, 2)
                        . "\n" . "Av.BAL: NGN" . number_format($savings['balance'], 2);
                    $this->fivelinks->send_SMS($user->phone, $content);
                }

                $this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $user->email, $subject, $message);
                $this->session->set_flashdata('message', lang('savings_added'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
            redirect('agency/savings');
        } else {
            $this->data['savings_type'] = $this->common->get_all_these('savings_types', ['status'=>'public','coop_id' => $this->coop->id]);
            $this->data['savings_source'] = $this->common->get_all('savings_source');

            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->data['title'] = lang('add_savings');
            $this->data['controller'] = lang('savings');
            $this->layout->set_app_page_agency('savings/add', $this->data);
        }
    }
    public function preview($id) {
        $id = $this->utility->un_mask($id);
        $this->data['savings'] = $this->info->get_savings_details(['users.coop_agent_id'=> $this->user->id,'savings.id' => $id, 'users.coop_id' => $this->coop->id]);
        $this->data['title'] = lang('print');
        $this->data['controller'] = lang('savings');
        $this->layout->set_app_page_agency('savings/preview', $this->data);
    }

}
