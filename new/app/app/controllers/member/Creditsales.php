<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Creditsales extends BASE_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->form_validation->set_rules('start_date', lang("start_date"), 'trim|required');
        $this->form_validation->set_rules('end_date', lang("end_date"), 'trim|required');
        $where = "credit_sales.user_id= {$this->user->id} AND credit_sales.coop_id={$this->coop->id} AND (credit_sales.status='approved' OR credit_sales.status='disbursed' OR credit_sales.status='request')";
        $where2 = "credit_sales.user_id= {$this->user->id} AND credit_sales.coop_id={$this->coop->id} AND credit_sales.status='disbursed'";

        if ($this->form_validation->run()) {
            $this->data['credit_sales'] = $this->info->get_credit_sales($where);
            $this->data['total_due'] = $this->common->sum_this('credit_sales', $where2, 'total_due')->total_due;
            $this->data['total_remain'] = $this->common->sum_this('credit_sales', $where2, 'total_remain')->total_remain;
        } else {
            $this->data['credit_sales'] = $this->info->get_credit_sales($where, 1000);
            $this->data['total_due'] = $this->common->sum_this('credit_sales', $where2, 'total_due')->total_due;
            $this->data['total_remain'] = $this->common->sum_this('credit_sales', $where2, 'total_remain')->total_remain;
        }
        $this->data['title'] = lang('credit_sales');
        $this->data['controller'] = lang('credit_sales');
        $this->layout->set_app_page_member('member/creditsales/index', $this->data);
    }

    public function ajax_preview_credit_sales_schedule() {
        $product_type_id = $this->input->get('product_type', TRUE);
        $tenure = (int) $this->input->get('tenure', TRUE);
        $amount = (float) str_replace(',', '', $this->input->get('amount', TRUE));
        $product_type = $this->common->get_this('product_types', ['id' => $product_type_id, 'coop_id' => $this->coop->id]);
        $schedule = $this->utility->get_loan_breakdown($amount, $product_type->rate, $tenure);
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
        $this->data['credit_sales'] = $this->info->get_credit_sales_details(['credit_sales.id' => $id, 'users.coop_id' => $this->coop->id]);
        $this->data['saving_bal'] = $this->utility->get_savings_bal($this->data['credit_sales']->user_id, $this->data['credit_sales']->coop_id);
        $this->data['wallet_bal'] = $this->utility->get_wallet_bal($this->data['credit_sales']->user_id, $this->data['credit_sales']->coop_id);

        //gurantor data
        $gurantor = $this->info->get_credit_sales_guarantors(['credit_sales.id' => $id]);
        $gurantor_data = [];
        if ($gurantor) {
            foreach ($gurantor as $g) {
                $gurantor_data[] = (object) [
                            'savings_bal' => $this->utility->get_savings_bal($g->guarantor_id, $g->coop_id),
                            'wallet_bal' => $this->utility->get_wallet_bal($g->guarantor_id, $g->coop_id),
                            'total_due' => $this->utility->get_credit_sales_colleted($g->guarantor_id, $g->coop_id),
                            'total_remain' => $this->utility->get_credit_sales_bal($g->guarantor_id, $g->coop_id),
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

        $orders = $this->info->get_orders(['orders.coop_id' => $this->coop->id, 'orders.credit_sales_id' => $id]);
        foreach ($orders as $od) {
            $od->image = $this->common->get_this('product_image', ['product_id' => $od->product_id]);
        }
        $this->data['orders'] = $orders;

        $this->data['guarantor'] = $gurantor_data;
        $this->data['title'] = lang('preview');
        $this->data['controller'] = lang('credit_sales');
        $this->layout->set_app_page_member('member/creditsales/preview', $this->data);
    }

    public function repay($id = null) {
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required');

        $id = $this->utility->un_mask($id);
        $this->data['credit_sales'] = $this->info->get_credit_sales_details(['credit_sales.id' => $id, 'users.coop_id' => $this->coop->id]);
        $this->data['member'] = $this->info->get_user_details(['users.id' => $this->data['credit_sales']->user_id, 'users.coop_id' => $this->coop->id]);
        if ($this->form_validation->run()) {
            $amount = str_replace(',', '', $this->input->post('amount'));

            if ($amount <= 0) {
                $this->session->set_flashdata('error', lang('invalid_amount'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            if ($amount > $this->data['credit_sales']->total_remain) {
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
                    'narration' => "Credit Sales Repayment",
                    'status' => 'successful',
                ];
            } else {
                $wallet['tranx_ref'] = $this->data['member']->id . 'DEF' . date('Ymdhis');
            }
            $splited_amount = $this->utility->split_repayment_amt($amount, $this->data['credit_sales']);

            $credit_sales_data = [
                'principal_remain' => $splited_amount->principal_remain,
                'interest_remain' => $splited_amount->interest_remain,
                'total_remain' => $splited_amount->total_remain,
                'next_payment_date' => $this->utility->get_end_date(date('Y-m-d H:i:s'), $plus_a_month = 1, true),
            ];
            
            if ($splited_amount->total_remain == 0 && $splited_amount->principal_remain == 0 && $splited_amount->interest_remain == 0) {
                $credit_sales_data['status'] = 'finished';
            }

            $credit_sales_repayment_data = [
                'referrer_code' => $this->coop->referrer_code,
                'coop_id' => $this->coop->id,
                'tranx_ref' => $wallet['tranx_ref'],
                'user_id' => $this->data['member']->id,
                'credit_sales_id' => $id,
                'product_type_id' => $this->data['credit_sales']->product_type_id,
                'principal_repayment' => $splited_amount->principal_repayment,
                'interest_repayment' => $splited_amount->interest_repayment,
                'amount' => $splited_amount->amount,
                'principal_remain' => $splited_amount->principal_remain,
                'interest_remain' => $splited_amount->interest_remain,
                'amount_remain' => $splited_amount->total_remain,
                'month_id' => date('n'),
                'year' => date('Y'),
                'source' => $source,
                'narration' => date('n') . ' ' . date('Y') . ' Credit Sales Repayment',
                'created_by' => $this->user->id,
                'created_on' => date('Y-m-d H:i:s'),
                'status' => 'paid',
            ];

            $this->common->start_trans();
            if ($source == 1) {
                $this->common->add('wallet', $wallet);
            }
            $item_id = $this->common->add('credit_sales_repayment', $credit_sales_repayment_data);
            $this->utility->auto_post_to_general_ledger((object)$credit_sales_repayment_data, $item_id, 'CRSR');
            $this->common->update_this('credit_sales', ['id' => $id], $credit_sales_data);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('credit_sales_repayment_added'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function repayment_history($id) {
        $id = $this->utility->un_mask($id);
        $where = [
            'credit_sales_repayment.user_id' => $this->user->id,
            'credit_sales_repayment.coop_id' => $this->coop->id,
            'credit_sales_repayment.status' => 'paid',
            'credit_sales_repayment.credit_sales_id' => $id
        ];
        $this->data['credit_sales_repayment'] = $this->info->get_credit_sales_repayment($where, 1000, true);
        $this->data['credit_sales'] = $this->common->get_this('credit_sales', ['id' => $id, 'user_id' => $this->user->id, 'coop_id' => $this->coop->id]);
        $this->data['title'] = lang('repayment_history');
        $this->data['controller'] = lang('credit_sales_repayment');
        $this->layout->set_app_page_member('member/creditsales/repayment_history', $this->data);
    }

    public function receipt($id = null) {
        $id = $this->utility->un_mask($id);
        $this->data['credit_sales'] = $this->info->get_credit_sales_repayment_details([
            'credit_sales_repayment.user_id' => $this->user->id,
            'credit_sales_repayment.id' => $id,
            'users.coop_id' => $this->coop->id
        ]);
        $this->data['title'] = lang('print');
        $this->data['controller'] = lang('credit_sales');
        $this->layout->set_app_page_member('member/creditsales/receipt', $this->data);
    }

    public function order_proceed($id){
        $id = $this->utility->un_mask($id);
       
        $this->form_validation->set_rules('quantity', lang('quantity'), 'trim|required');
        if ($this->form_validation->run()) {
            // $id = $this->input->post('id');
            $quantity = $this->input->post('quantity');

            $product = $this->common->get_this('products', ['id' => $id]);
           
            $this->data['order_details'] = (object)[
                'product_id' => $id,
                'user_id' => $this->user->id,
                'username' => $this->user->username,
                'product_type_id' => $product->product_type_id,
                'amount' => $product->price * $quantity,
                'quantity' => $quantity,
            ];

            // var_dump($this->data['order_details']);exit;

            $this->data['product_type'] = $this->common->get_this('product_types', ['coop_id' => $this->coop->id, 'id' => $product->product_type_id]);
            $this->data['product_types'] = $this->common->get_all_these('product_types', ['coop_id' => $this->coop->id]);

            $this->data['title'] = lang('add') . ' ' . lang('credit_sales');
            $this->data['controller'] = lang('credit_sales');
            $this->layout->set_app_page_member('member/creditsales/order_proceed', $this->data);
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            redirect('member/products');
        }
    }

    public function request() {
        $this->form_validation->set_rules('member_id', lang('member_id'), 'trim|required');
        $this->form_validation->set_rules('product_type', lang('product_type'), 'trim|required');
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required');
        $this->form_validation->set_rules('tenure', lang('tenure'), 'trim|required');
        // $this->form_validation->set_rules('description', lang('description'), 'trim|required');
        
        if ($this->form_validation->run()) {
            $guarantor = $this->input->post('guarantor');
            $product_type_id = $this->input->post('product_type');
            $quantity = $this->input->post('quantity');
            $product_id = $this->input->post('prodcut_id');
            $amount = str_replace(',', '', $this->input->post('amount'));

            if ($amount <= 0) {
                $this->session->set_flashdata('error', lang('invalid_amount'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $tenure = $this->input->post('tenure');
            $product_type = $this->common->get_this('product_types', ['id' => $product_type_id]);

            if ($tenure > $product_type->max_month) {
                $this->session->set_flashdata('error', lang('tenure_cannot_exeed') . ' ' . $product_type->max_month);
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $check_guarantor = $this->utility->check_guarantor($guarantor, $this->user->username, $this->coop->id);
            
            if (isset($check_guarantor['error'])) {
                $this->session->set_flashdata('error', $check_guarantor['error']);
                redirect($_SERVER["HTTP_REFERER"]);
            }
            
            $pending_approvals = $this->common->get_this('credit_sales', ['user_id' => $this->user->id, 'coop_id' => $this->coop->id, 'status' => 'request']);
            if ($pending_approvals) {
                $this->session->set_flashdata('error', lang('pending_approval_exists'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $check_duplicte_product_type = $this->common->get_this('credit_sales', ['user_id' => $this->user->id, 'product_type_id' => $product_type_id, 'coop_id' => $this->coop->id, 'status' => 'disbursed']);
            if ($check_duplicte_product_type) {
                $this->session->set_flashdata('error', lang('duplicate_product_type_exists'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            
            $schedule = $this->utility->get_loan_breakdown($amount, $product_type->rate, $tenure, $product_type->calc_method);
            
            $credit_sales = [
                'referrer_code' => $this->coop->referrer_code,
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'product_type_id' => $product_type_id,
                'tenure' => $tenure,
                'rate' => $product_type->rate,
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
                'interest' => $schedule->interest,
                'created_by' => $this->user->id,
                'description' => lang('credit_sales_request'),
                'created_on' => date('Y-m-d g:i:s'),
                'status' => 'request',
            ];
            $notification = [
                'coop_id' => $this->coop->id,
                'from' => $this->user->first_name .' '.$this->user->last_name ,
                'description' => lang('credit_sales_request'),
                'url' => base_url('creditsales')
            ];

            $order_details = $this->utility->format_order_details(false, $product_id, $quantity);
            $this->common->start_trans();
            $credit_sales_id = $this->common->add('credit_sales', $credit_sales);
            foreach ($order_details->product_details as $prod) {
                $prod->coop_id = $this->coop->id;
                $prod->credit_sales_id = $credit_sales_id;
                $this->common->add('orders', $prod);
            }
            if ($check_guarantor['guarantor']) {
                foreach ($check_guarantor['guarantor'] as $g) {
                    $this->common->add('credit_sales_guarantors', ['coop_id' => $this->coop->id, 'credit_sales_id' => $credit_sales_id, 'guarantor_id' => $g->id, 'request_date' => date('Y-m-d g:i:s')]);
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
                $subject = 'Credit Sales Request';
                $message = $this->load->view('emails/email_credit_sales_request', $this->data, true);
                $this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $this->user->email, $subject, $message);

                // send email to gurantor
                if ($check_guarantor['guarantor']) {
                    foreach ($check_guarantor['guarantor'] as $g) {
                        $this->data['g_name'] = ucwords($g->first_name . ' ' . $g->last_name);
                        $this->data['g_member_id'] = $g->username;
                        $subject = "Guarantor Request";
                        $message = $this->load->view('emails/email_credit_sales_guarantor_request', $this->data, true);
                        $this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $g->email, $subject, $message);
                    }
                }
                // send email to exco
                $excos = $this->info->get_users(['groups.id' => 1, 'users.coop_id' => $this->coop->id]);
                foreach ($excos as $exco) {
                    $this->data['exco_name'] = ucwords($exco->first_name . ' ' . $exco->last_name);
                    $this->data['exco_member_id'] = $exco->username;
                    $subject = "Credit Sales Request";
                    $message = $this->load->view('emails/email_credit_sales_request_exco', $this->data, true);
                    // $this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $exco->email, $subject, $message);
                }
                $this->session->set_flashdata('message', lang('credit_sales_request_successful'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
            redirect('member/creditsales');
        } else {
            $this->data['product_type'] = $this->common->get_all_these('product_types', ['coop_id' => $this->coop->id]);
            $this->data['savings_source'] = $this->common->get_all('savings_source');

            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->data['title'] = lang('request_credit_sales');
            $this->data['controller'] = lang('credit_sales');
            $this->layout->set_app_page_member('member/creditsales/request', $this->data);
        }
    }

}
