<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Commonapi extends BASE_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function ajax_member_info_live_search() {
        // $agent_id = $this->input->get('agent_id', TRUE);
        $input = $this->input->get('input', TRUE);
        $this->data['member'] = $this->info->search_user($this->coop->id, 5, $input);
        if ($this->data['member']) {
            $mesage = $this->load->view('common/search_member', $this->data, true);
            echo json_encode(array('status' => 'success', 'message' => $mesage));
        } else {
            $mesage = $this->load->view('common/search_member', $this->data, true);
            echo json_encode(array('status' => 'error', 'message' => $mesage));
        }
    }

    public function ajax_member_info() {
        $membe_id = $this->input->get('member_id', TRUE);
        $member = $this->info->get_user_details(['users.username' => $membe_id, 'users.coop_id' => $this->coop->id]);
        if ($member) {
            echo json_encode(array('status' => 'success', 'message' => $member));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Record not found'));
        }
    }

    public function get_savings_type() {
        $savings_type = $this->common->get_all_these('savings_types', ['coop_id' => $this->coop->id]);

        if (!$savings_type) {
            echo json_encode(array('status' => 'error', 'data' => 'No saivings type found'));
        } else {
            echo json_encode(array('status' => 'success', 'data' => $savings_type));
        }
    }
    
    public function get_loan_type() {
        $loan_types = $this->common->get_all_these('loan_types', ['coop_id' => $this->coop->id]);

        if (!$loan_types) {
            echo json_encode(array('status' => 'error', 'data' => 'No loan type found'));
        } else {
            echo json_encode(array('status' => 'success', 'data' => $loan_types));
        }
    }
    
    public function get_product_type() {
        $product_type = $this->common->get_all_these('product_types', ['coop_id' => $this->coop->id]);

        if (!$product_type) {
            echo json_encode(array('status' => 'error', 'data' => 'No product type found'));
        } else {
            echo json_encode(array('status' => 'success', 'data' => $product_type));
        }
    }
    
    public function ajax_get_savings_info() {
        $membe_id = $this->input->get('member_id', TRUE);
        $savings_type = $this->input->get('savings_type', TRUE);
        $member = $this->common->get_this('users', ['username' => $membe_id, 'coop_id'=>$this->coop->id]);
        if ($member) {
            $last_savings = $this->common->get_limit('savings', ['user_id' => $member->id, 'savings_type' => $savings_type, 'coop_id'=>$this->coop->id], 1, 'id', 'DESC');
            if ($last_savings) {
               
                $message = [
                    'full_name' => ucwords($member->first_name . ' ' . $member->last_name),
                    'wallet_bal' => number_format($this->utility->get_wallet_bal($member->id, $this->coop->id), 2),
                    'month' => $this->common->get_this('months', ['id' => $last_savings[0]->month_id])->name,
                    'year' => $last_savings[0]->year,
                    'amount' => number_format($last_savings[0]->amount, 2),
                ];
            } else {
                $message = [
                    'full_name' => ucwords($member->first_name . ' ' . $member->last_name),
                    'wallet_bal' => number_format($this->utility->get_wallet_bal($member->id, $this->coop->id), 2),
                    'month' => 'NA',
                    'year' => 'NA',
                    'amount' => 'NA',
                ];
            }

            echo json_encode(array('status' => 'success', 'message' => $message));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Record not found'));
        }
    }
    
    public function ajax_get_loan_info() {
        $membe_id = $this->input->get('member_id', TRUE);
        $member = $this->common->get_this('users', ['username' => $membe_id, 'coop_id' => $this->coop->id]);
        if ($member) {
            $loan_bal = $this->utility->get_loan_bal($member->id, $this->coop->id);
            $loan_callected = $this->utility->get_loan_colleted($member->id, $this->coop->id);
            $message = [
                'full_name' => ucwords($member->first_name . ' ' . $member->last_name),
                'bal' => number_format($this->utility->get_savings_bal($member->id, $this->coop->id)),
                'total' => number_format($loan_callected, 2),
                'paid' => number_format($loan_callected - $loan_bal, 2),
                'balance' => number_format($loan_bal, 2),
            ];

            echo json_encode(array('status' => 'success', 'message' => $message));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Record not found'));
        }
    }
    
    public function ajax_generate_loan_guarantor_field() {
        $loan_type_id = $this->input->get('loan_type', TRUE);
        $loan_type = $this->common->get_this('loan_types', ['id' => $loan_type_id, 'coop_id' => $this->coop->id]);
        $message = [
            'guarantors' => $loan_type->guarantor,
            'tenure' => $loan_type->max_month,
        ];
        echo json_encode(array('status' => 'success', 'message' => $message));
    }
    
    public function ajax_preview_loan_schedule() {
        $loan_type_id = $this->input->get('loan_type', TRUE);
        $tenure = (int) $this->input->get('tenure', TRUE);
        $amount = (float) str_replace(',', '', $this->input->get('amount', TRUE));
        $loan_type = $this->common->get_this('loan_types', ['id' => $loan_type_id, 'coop_id' => $this->coop->id]);
        $schedule = $this->utility->get_loan_breakdown($amount, $loan_type->rate, $tenure, $loan_type->calc_method);
        $message = [
            'principal' => number_format($amount, 2),
            'interest' => number_format($schedule->interest,2),
            'monthly_due' => number_format($schedule->monthly_due,2),
            'total_due' => number_format($schedule->total_due,2),
            'principal_due' => number_format($schedule->principal_due, 2),
            'interest_due' => number_format($schedule->interest_due, 2),
            'tenure' => $tenure,
            'loan_type' => $loan_type->name,
        ];
        echo json_encode(array('status' => 'success', 'message' => $message));
    }

    public function ajax_refinance_loan_schedule() {
        $loan_id = $this->input->get('loan_id', TRUE);
        $rate = $this->input->get('rate', TRUE);
        $loan_type_id = $this->input->get('loan_type', TRUE);
        $tenure = (int) $this->input->get('tenure', TRUE);
        $amount = (float) str_replace(',', '', $this->input->get('amount', TRUE));
        $loan = $this->common->get_this('loans', ['id' => $loan_id, 'coop_id' => $this->coop->id]);
        $loan_type = $this->common->get_this('loan_types', ['id' => $loan_type_id, 'coop_id' => $this->coop->id]);
        $schedule = $this->utility->get_loan_breakdown($amount, $rate, $tenure, $loan_type->calc_method);
        $message = [
            'principal' => number_format($amount, 2),
            'interest' => number_format($schedule->interest,2),
            'monthly_due' => number_format($schedule->monthly_due,2),
            'total_due' => number_format($schedule->total_due,2),
            'principal_due' => number_format($schedule->principal_due, 2),
            'interest_due' => number_format($schedule->interest_due, 2),
            'tenure' => $tenure,
            'loan_type' => $loan_type->name,
            'old_balance' => $loan->total_remain,
            'new_balance' =>  $loan->total_remain - ($loan->interest - $schedule->interest) 
        ];
        echo json_encode(array('status' => 'success', 'message' => $message));
    }
    
    public function ajax_preview_credit_sales_schedule() {
        $product_type_id = $this->input->get('product_type', TRUE);
        $tenure = (int) $this->input->get('tenure', TRUE);
        $amount = (float) str_replace(',', '', $this->input->get('amount', TRUE));
        $product_type = $this->common->get_this('product_types', ['id' => $product_type_id, 'coop_id' => $this->coop->id]);
        $schedule = $this->utility->get_loan_breakdown($amount, $product_type->rate, $tenure, $product_type->calc_method);
        $message = [
            'principal' => number_format($amount, 2),
            'interest' => number_format($schedule->interest, 2),
            'monthly_due' => number_format($schedule->monthly_due, 2),
            'total_due' => number_format($schedule->total_due, 2),
            'principal_due' => number_format($schedule->principal_due, 2),
            'interest_due' => number_format($schedule->interest_due, 2),
            'tenure' => $tenure,
            'product_type_name' => $product_type->name,
        ];
        echo json_encode(array('status' => 'success', 'message' => $message));
    }
    
    public function ajax_get_withdrawal_info() {
        $membe_id = $this->input->get('member_id', TRUE);
        $savings_type = $this->input->get('savings_type', TRUE);
        $member = $this->common->get_this('users', ['username' => $membe_id, 'coop_id' => $this->coop->id]);
        if ($member) {
            $loan_bal = $this->utility->get_loan_bal($member->id, $this->coop->id);
            $loan_callected = $this->utility->get_loan_colleted($member->id, $this->coop->id);
            $message = [
                'full_name' => ucwords($member->first_name . ' ' . $member->last_name),
                'bal' => number_format($this->utility->get_savings_bal($member->id, $this->coop->id, $savings_type)),
                'total' => number_format($loan_callected, 2),
                'paid' => number_format($loan_callected - $loan_bal, 2),
                'balance' => number_format($loan_bal, 2),
            ];

            echo json_encode(array('status' => 'success', 'message' => $message));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Record not found'));
        }
    }
    
    public function change_theme(){
        $color = $this->input->get('color', true);
        $updated = $this->common->update_this('users', ['id' => $this->user->id], ['theme'=>$color]);
        if (!$updated) {
            echo json_encode(array('status' => 'error', 'data' => 'Failed'));
        } else {
            echo json_encode(array('status' => 'success', 'data' => $color));
        }
    }
    
    public function change_side_bar(){
        $status = $this->input->get('status', true);
        $updated = $this->common->update_this('users', ['id' => $this->user->id], ['side_bar'=>$status]);
        if (!$updated) {
            echo json_encode(array('status' => 'error', 'data' => 'Failed'));
        } else {
            echo json_encode(array('status' => 'success', 'data' => $status));
        }
    }
    
    public function ajax_get_states_and_banks(){
        $country_id = $this->input->get('country_id', true);
        $states = $this->common->get_all_these('state', ['country_id' => $country_id]);
        $banks = $this->common->get_all_these('banks', ['country_id' => $country_id]);
        if (!$states) {
            echo json_encode(array('status' => 'error', 'body' => 'No saivings type found'));
        } else {
            echo json_encode(array('status' => 'success', 'body' => $states, 'banks'=>$banks));
        }
    }
    
    public function ajax_get_city(){
        $state_id = $this->input->get('state_id', true);
        $state = $this->common->get_all_these('cities', ['state_id' => $state_id]);
        if (!$state) {
            echo json_encode(array('status' => 'error', 'body' => 'No saivings type found'));
        } else {
            echo json_encode(array('status' => 'success', 'body' => $state));
        }
    }
    
    public function ajax_get_credit_sales_info() {
        $membe_id = $this->input->get('member_id', TRUE);
        $member = $this->common->get_this('users', ['username' => $membe_id, 'coop_id' => $this->coop->id]);
        if ($member) {
            $loan_bal = $this->utility->get_credit_sales_bal($member->id, $this->coop->id);
            $loan_callected = $this->utility->get_credit_sales_colleted($member->id, $this->coop->id);
            $message = [
                'full_name' => ucwords($member->first_name . ' ' . $member->last_name),
                'bal' => number_format($this->utility->get_savings_bal($member->id, $this->coop->id)),
                'total' => number_format($loan_callected, 2),
                'paid' => number_format($loan_callected - $loan_bal, 2),
                'balance' => number_format($loan_bal, 2),
            ];

            echo json_encode(array('status' => 'success', 'message' => $message));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Record not found'));
        }
    }
    
    public function ajax_generate_credit_sales_guarantor_field() {
        $product_type_id = $this->input->get('product_type', TRUE);
        $product_type = $this->common->get_this('product_types', ['id' => $product_type_id, 'coop_id' => $this->coop->id]);
        $message = [
            'guarantors' => $product_type->guarantor,
            'tenure' => $product_type->max_month,
        ];
        echo json_encode(array('status' => 'success', 'message' => $message));
    }

    public function ajax_previw_order(){
        $id = $this->input->get('id', true);
        $qty = $this->input->get('qty', true);
        // $product = $this->common->get_this
        $mesage = $this->load->view('creditsales/preview_order', $this->data, true);
    }

}
