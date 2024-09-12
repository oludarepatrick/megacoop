<?php

class Withdrawal extends BASE_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->form_validation->set_rules('month', lang("month"), 'trim|required');
        $this->form_validation->set_rules('year', lang("year"), 'trim|required');
        $where = [
            'savings.user_id' => $this->user->id,
            'savings.coop_id' => $this->coop->id,
            'savings.year' => date('Y'),
            'tranx_type'=>'debit'
        ];

        if ($this->form_validation->run()) {
            $month = $this->input->post('month');
            $where['savings.month_id'] = $this->common->get_this('months', ['name' => $month])->id;
            $where['savings.year'] = $this->input->post('year');
            $this->data['withdrawals'] = $this->info->get_withdrawals($where);
        } else {
            $month = date('F');
            $this->data['withdrawals'] = $this->info->get_withdrawals(['savings.user_id' => $this->user->id,'users.coop_id' => $this->coop->id], 1000);
        }
        $where['savings.status'] = 'paid';
        $this->data['filter_total_withdrawal'] = $this->common->sum_this('savings', $where, 'amount');
        $this->data['total_withdrawal'] = $this->common->sum_this('savings', ['savings.status' => 'paid','savings.user_id' => $this->user->id,'tranx_type'=>'debit','savings.coop_id' => $this->coop->id], 'amount');
        $this->data['month'] = $month;
        $this->data['year'] = $where['savings.year'];
        $this->data['title'] = lang('withdrawals');
        $this->data['controller'] = lang('withdrawals');
        $this->layout->set_app_page_member('member/withdrawal/index', $this->data);
    }
 
    public function request() {
        $this->form_validation->set_rules('member_id', lang('member_id'), 'trim|required');
        $this->form_validation->set_rules('savings_type', lang('savings_type'), 'trim|required');
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required');

        if ($this->form_validation->run()) {
            $user = $this->common->get_this('users', ['username' => $this->input->post('member_id')]);
            $amount = str_replace(',', '', $this->input->post('amount'));
            $savings_type = $this->input->post('savings_type');
            
            $old_bal = $this->utility->get_savings_bal($user->id, $this->coop->id, $savings_type);
            $new_bal = $old_bal - $amount;
            
            if ($amount <= 0) {
                $this->session->set_flashdata('error', lang('invalid_amount'));
                redirect($_SERVER["HTTP_REFERER"]);
            }
            
            if($amount > $old_bal or $amount <=0){
                $this->session->set_flashdata('error', lang('low_bal'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

           $s_type = $this->common->get_this('savings_types', ['id' => $savings_type]);
            if($this->utility->max_withdrawal_exeed($amount, $old_bal, $s_type)){
                $this->session->set_flashdata('error', lang('max_withdrawal_limit').'. '.$s_type->max_withdrawal.'% '.lang('is_withdrawable'));
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
                'narration' => 'Partial Withdrawal',
                'status' => 'due',
                'payment_date' => date('Y-m-d H:i:s'),
                'score' => 0
            ];
            $notification = [
                'coop_id'=> $this->coop->id,
                'from'=> $this->user->first_name . ' ' . $this->user->last_name,
                'description'=> lang('withdrawal_request'),
                'url'=> base_url('withdrawal/pending_withdrawal')
            ];

            $this->common->start_trans();
            $this->common->add('savings', $withdrawal);
            $this->common->add('exco_notification', $notification);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('req').' '.lang('successful'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
            redirect('member/withdrawal');
        } else {
            $this->data['savings_type'] = $this->common->get_all_these('savings_types', ['coop_id' => $this->coop->id]);
            $this->data['savings_source'] = $this->common->get_all('savings_source');

            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->data['title'] = lang('req').' '.lang('withdrawal');
            $this->data['controller'] = lang('withdrawals');
            $this->layout->set_app_page_member('member/withdrawal/request', $this->data);
        }
    }

     public function preview($id) {
        $id = $this->utility->un_mask($id);
        $this->data['withdrawal'] = $this->info->get_withdrawal_details(['savings.id' => $id, 'users.coop_id' => $this->coop->id]);
        $this->data['title'] = lang('print');
        $this->data['controller'] = lang('withdrawal');
        $this->layout->set_app_page_member('member/withdrawal/preview', $this->data);
    }

}
