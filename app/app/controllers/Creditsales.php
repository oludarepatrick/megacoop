<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Creditsales extends BASE_Controller {

    const MENU_ID = 18;

    public function __construct() {
        parent::__construct();
        if (!$this->ion_auth->is_admin()) {
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->load->library('excel');
        if ($this->coop) {
            if ($this->coop->status === 'processing') {
                redirect('settings');
            }
        }
        $this->licence_cheker($this->coop, $this->app_settings);
    }

    public function index() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('start_date', lang("start_date"), 'trim|required');
        $this->form_validation->set_rules('end_date', lang("end_date"), 'trim|required');
        $where = [
            'credit_sales.coop_id' => $this->coop->id,
            'credit_sales.status' => 'request',
            'credit_sales.created_on>=' => $this->utility->get_this_year('start'),
            'credit_sales.created_on<=' => $this->utility->get_this_year('end'),
        ];
        if ($this->form_validation->run()) {
            $where['credit_sales.created_on>='] = $this->input->post('start_date');
            $where['credit_sales.created_on<='] = $this->input->post('end_date');
            $this->data['credit_sales'] = $this->info->get_credit_sales($where);
            $this->data['filter_total_credit_sales'] = $this->common->sum_this('credit_sales', $where, 'principal');
            $this->data['start_date'] = $this->input->post('start_date');
            $this->data['end_date'] = $this->input->post('end_date');
        } else {
            $this->data['credit_sales'] = $this->info->get_credit_sales(['users.coop_id' => $this->coop->id, 'credit_sales.status' => 'request'], 1000);
            $this->data['filter_total_credit_sales'] = $this->common->sum_this('credit_sales', $where, 'principal');
            $this->data['start_date'] = $this->utility->get_this_year('start');
            $this->data['end_date'] = $this->utility->get_this_year('end');
        }
        $this->data['total_credit_sales'] = $this->common->sum_this('credit_sales', ['credit_sales.coop_id' => $this->coop->id, 'credit_sales.status' => 'request'], 'principal');
        $this->data['title'] = lang('requested_credit_sales');
        $this->data['controller'] = lang('credit_sales');
        $this->layout->set_app_page('creditsales/index', $this->data);
    }

    public function order_product(){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('product_type_id', lang("product_type"), 'trim|required');

        $this->data['products'] = $this->info->get_products(['products.coop_id' => $this->coop->id, 'products.status'=>'available' ], '1000');
        if ($this->form_validation->run()) {
            $where = [
                'products.coop_id' => $this->coop->id,
                'products.product_type_id' => $this->input->post('product_type_id'),
            ];
            $this->data['products'] = $this->info->get_products($where, false);
        }

        $product_type = $this->common->get_all_these('product_types', ['coop_id' => $this->coop->id]);
        $vendor = $this->common->get_all_these('vendors', ['coop_id' => $this->coop->id, 'deleted' => 0]);

        foreach ($product_type as $p) {
            $this->data['product_type'][$p->id] = $p->name;
        }
        foreach ($vendor as $v) {
            $this->data['vendor'][$v->id] = $v->name;
        }

        $this->data['title'] = lang('order').' '.lang('credit_sales');
        $this->data['controller'] = lang('credit_sales');
        $this->layout->set_app_page('creditsales/order_product', $this->data);
    }

    public function order_proceed(){
        $this->form_validation->set_rules('member_id', lang('member_id'), 'trim|required');
        $this->form_validation->set_rules('order_details', lang('order_empty'), 'trim|required');
        if ($this->form_validation->run()) {
            $order_details = $this->utility->format_order_details($this->input->post('order_details'));
            $user = $this->common->get_this('users',['username'=>$this->input->post('member_id')]);

            if(!$user){
                $this->session->set_flashdata('error', lang('invalid_member_id'));
                redirect($_SERVER["HTTP_REFERER"]);
            }
            $order_err = $this->utility->order_details_errors_exists($order_details->product_details);
            if($order_err){
                $this->session->set_flashdata('error', $order_err);
                redirect($_SERVER["HTTP_REFERER"]);
            }
            $this->data['order_details'] = (object)[
                'user_id' => $user->id,
                'username' => $user->username,
                'product_type_id' => $order_details->category_details->product_type_id,
                'amount' => $order_details->category_details->total_amount,
                'order_details' => $this->input->post('order_details'),
            ];

            $this->data['product_type'] = $this->common->get_this('product_types', ['coop_id'=>$this->coop->id, 'id'=> $order_details->category_details->product_type_id]);
            $this->data['product_types'] = $this->common->get_all_these('product_types', ['coop_id' => $this->coop->id]);
            
            $this->data['title'] = lang('add') . ' ' . lang('credit_sales');
            $this->data['controller'] = lang('credit_sales');
            $this->layout->set_app_page('creditsales/order_proceed', $this->data);
        }else{
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            redirect('creditsales/order_product');
        }
    }

    public function add() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('member_id', lang('member_id'), 'trim|required');
        $this->form_validation->set_rules('product_type', lang('product_type'), 'trim|required');
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required');
        $this->form_validation->set_rules('tenure', lang('tenure'), 'trim|required');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');
        $this->form_validation->set_rules('order_details', lang('order_empty'), 'trim|required');
        if ($this->form_validation->run()) {
            $order_details = $this->utility->format_order_details($this->input->post('order_details'));
            $guarantor = $this->input->post('guarantor');
            $member_id = $this->input->post('member_id');
            $product_type_id = $this->input->post('product_type');
            $amount = str_replace(',', '', $this->input->post('amount'));
            $tenure = $this->input->post('tenure');
            $member = $this->common->get_this('users', ['username' => $member_id]);
            $product_type = $this->common->get_this('product_types', ['id' => $product_type_id]);

            if ($tenure > $product_type->max_month) {
                $this->session->set_flashdata('error', lang('tenure_cannot_exeed') . ' ' . $product_type->max_month);
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $check_guarantor = $this->utility->check_guarantor($guarantor, $member_id, $this->coop->id);
            if (isset($check_guarantor['error'])) {
                $this->session->set_flashdata('error', $check_guarantor['error']);
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $pending_approvals = $this->common->get_this('credit_sales', ['user_id' => $member->id, 'coop_id' => $this->coop->id, 'status' => 'request']);
            if ($pending_approvals) {
                $this->session->set_flashdata('error', lang('pending_approval_exists'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $check_duplicte_product_type = $this->common->get_this('credit_sales', ['user_id' => $member->id, 'product_type_id' => $product_type_id, 'coop_id' => $this->coop->id, 'status' => 'disbursed']);
            if ($check_duplicte_product_type) {
                $this->session->set_flashdata('error', lang('duplicate_product_type_exists'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $schedule = $this->utility->get_loan_breakdown($amount, $product_type->rate, $tenure, $product_type->calc_method);
            $loan = [
                'referrer_code' => $this->coop->referrer_code,
                'coop_id' => $this->coop->id,
                'user_id' => $member->id,
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
                'created_by' => $this->user->id,
                'created_on' => date('Y-m-d g:i:s'),
                'description' => $this->input->post('description'),
                'status' => 'request',
            ];

            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('credit_sales_added')
            ];
            
            
            $this->common->start_trans();
            $loan_id = $this->common->add('credit_sales', $loan);
            
            foreach ($order_details->product_details as $prod) {
                $prod->coop_id = $this->coop->id;
                $prod->credit_sales_id = $loan_id;
                $this->common->add('orders', $prod);
            }
            
            if ($check_guarantor['guarantor']) {
                foreach ($check_guarantor['guarantor'] as $g) {
                    $this->common->add('credit_sales_guarantors', ['coop_id' => $this->coop->id, 'credit_sales_id' => $loan_id, 'guarantor_id' => $g->id, 'request_date' => date('Y-m-d g:i:s')]);
                }
            }

            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->data['name'] = ucwords($member->first_name . ' ' . $member->last_name);
                $this->data['member_id'] = $member->username;
                $this->data['status'] = 'request';
                $this->data['principal'] = number_format($amount, 2);
                $this->data['interest'] = number_format($schedule->interest, 2);
                $this->data['monthly_due'] = number_format($schedule->monthly_due, 2);
                $this->data['total_due'] = number_format($schedule->total_due, 2);
                $this->data['status'] = 'request';
                $this->data['date'] = date('Y-m-d g:i a');
                $subject = 'Credit Sales Request';
                $message = $this->load->view('emails/email_credit_sales_request', $this->data, true);
                $this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $member->email, $subject, $message);

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
                    $message = $this->load->view('emails/email_credit_sales_request_exco', $this->data, true);
                    $this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $exco->email, $subject, $message);
                }
                $this->session->set_flashdata('message', lang('credit_sales').' '. lang('added'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
            redirect('creditsales');
        } else {
            $this->data['product_types'] = $this->common->get_all_these('product_types', ['coop_id' => $this->coop->id]);
            $this->data['savings_source'] = $this->common->get_all('savings_source');
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            redirect('creditsales/order_product');
        }
    }

    public function delete($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xdelete', 'on');
        $id = $this->utility->un_mask($id);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('credit_sales_deleted')
        ];

        $this->common->start_trans();
        $this->common->delete_this('credit_sales', ['coop_id' => $this->coop->id, 'id' => $id]);
        $this->common->delete_this('credit_sales_guarantors', ['coop_id' => $this->coop->id, 'credit_sales_id' => $id]);
        $this->common->delete_this('credit_sales_approvals', ['coop_id' => $this->coop->id, 'credit_sales_id' => $id]);
        $this->common->delete_this('orders', ['coop_id' => $this->coop->id, 'credit_sales_id' => $id]);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('credit_sales_deleted'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function approved() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('start_date', lang("start_date"), 'trim|required');
        $this->form_validation->set_rules('end_date', lang("end_date"), 'trim|required');
        $where = [
            'credit_sales.coop_id' => $this->coop->id,
            'credit_sales.status' => 'approved',
            'credit_sales.created_on>=' => $this->utility->get_this_year('start'),
            'credit_sales.created_on<=' => $this->utility->get_this_year('end'),
        ];
        if ($this->form_validation->run()) {
            $where['credit_sales.created_on>='] = $this->input->post('start_date');
            $where['credit_sales.created_on<='] = $this->input->post('end_date');
            $this->data['credit_sales'] = $this->info->get_credit_sales($where);
            $this->data['filter_total_credit_sales'] = $this->common->sum_this('credit_sales', $where, 'principal');
            $this->data['start_date'] = $this->input->post('start_date');
            $this->data['end_date'] = $this->input->post('end_date');
        } else {
            $this->data['credit_sales'] = $this->info->get_credit_sales(['users.coop_id' => $this->coop->id, 'credit_sales.status' => 'approved'], 1000);
            $this->data['filter_total_credit_sales'] = $this->common->sum_this('credit_sales', $where, 'principal');
            $this->data['start_date'] = $this->utility->get_this_year('start');
            $this->data['end_date'] = $this->utility->get_this_year('end');
        }
        $this->data['total_credit_sales'] = $this->common->sum_this('credit_sales', ['credit_sales.coop_id' => $this->coop->id, 'credit_sales.status' => 'approved'], 'principal');
        $this->data['title'] = lang('approved_credit_sales');
        $this->data['controller'] = lang('credit_sales');
        $this->layout->set_app_page('creditsales/approved', $this->data);
    }

    public function supplied() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('start_date', lang("start_date"), 'trim|required');
        $this->form_validation->set_rules('end_date', lang("end_date"), 'trim|required');
        $where = [
            'credit_sales.coop_id' => $this->coop->id,
            'credit_sales.status' => 'disbursed',
            'credit_sales.created_on>=' => $this->utility->get_this_year('start'),
            'credit_sales.created_on<=' => $this->utility->get_this_year('end'),
        ];
        if ($this->form_validation->run()) {
            $where['credit_sales.created_on>='] = $this->input->post('start_date');
            $where['credit_sales.created_on<='] = $this->input->post('end_date');
            $this->data['credit_sales'] = $this->info->get_credit_sales($where);
            $this->data['principal'] = $this->common->sum_this('credit_sales', $where, 'principal')->principal;
            $this->data['interest'] = $this->common->sum_this('credit_sales', $where, 'interest')->interest;
            $this->data['start_date'] = $this->input->post('start_date');
            $this->data['end_date'] = $this->input->post('end_date');
        } else {
            $this->data['credit_sales'] = $this->info->get_credit_sales(['users.coop_id' => $this->coop->id, 'credit_sales.status' => 'disbursed'], 1000);
            $this->data['principal'] = $this->common->sum_this('credit_sales', $where, 'principal')->principal;
            $this->data['interest'] = $this->common->sum_this('credit_sales', $where, 'interest')->interest;
            $this->data['start_date'] = $this->utility->get_this_year('start');
            $this->data['end_date'] = $this->utility->get_this_year('end');
        }
        $this->data['title'] = lang('supplied_products');
        $this->data['controller'] = lang('credit_sales');
        $this->layout->set_app_page('creditsales/supplied', $this->data);
    }

    public function supply($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $id = $this->utility->un_mask($id);
        $loan = $this->common->get_this('credit_sales', ['id' => $id, 'coop_id' => $this->coop->id]);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('product_supplied')
        ];
        $start_date = date('Y-m-d H:i:s');
        $loan_data = [
            'start_date' => $start_date,
            'end_date' => $this->utility->get_loan_end_date($start_date, $loan->tenure),
            'disbursed_date' => $start_date,
            'status' => 'disbursed'
        ];
        $orders = $this->info->get_orders(['orders.coop_id' => $this->coop->id, 'orders.credit_sales_id' => $id]);
        
        $this->common->start_trans();

        $this->common->update_this('credit_sales', ['coop_id' => $this->coop->id, 'id' => $id], $loan_data);
        $this->common->update_this('orders', ['credit_sales_id' => $id,], ['status' => 'delivered']);
        foreach ($orders as $od) {
            $od->image = $this->common->update_this('products', ['id' => $od->product_id], ['sold' => $od->sold + 1]);
        }
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('product_supplied'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function preview($id) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $id = $this->utility->un_mask($id);
        //member_data
        $this->data['loan'] = $this->info->get_credit_sales_details(['credit_sales.id' => $id, 'users.coop_id' => $this->coop->id]);
        $this->data['saving_bal'] = $this->utility->get_savings_bal($this->data['loan']->user_id, $this->data['loan']->coop_id);
        $this->data['wallet_bal'] = $this->utility->get_wallet_bal($this->data['loan']->user_id, $this->data['loan']->coop_id);
        $this->data['existing_loans'] = $this->info->get_credit_sales(['credit_sales.user_id' => $this->data['loan']->user_id, 'credit_sales.status' => 'disbursed']);

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

        $approvals = $this->info->get_credit_sales_approvals(['credit_sales.id' => $id]);
        $approval_data = [];
        if ($approvals) {
            foreach ($approvals as $g) {
                $approval_data[] = (object) [
                            'full_name' => $g->full_name,
                            'member_id' => $g->username,
                            'avatar' => $g->avatar,
                            'approval' => $g->status,
                            'response_date' => $g->action_date,
                            'role' => $g->role,
                ];
            }
        }

        $this->data['guarantor'] = $gurantor_data;
        $this->data['loan_approval'] = $approval_data;
        $orders = $this->info->get_orders(['orders.coop_id'=>$this->coop->id, 'orders.credit_sales_id'=>$id]);
        foreach($orders as $od){
            $od->image = $this->common->get_this('product_image', ['product_id'=>$od->product_id]);
        }
        $this->data['orders'] = $orders;
        // var_dump($this->data['orders']);exit;
        $this->data['title'] = lang('credit_sales_request');
        $this->data['controller'] = lang('credit_sales');
        $this->layout->set_app_page('creditsales/preview', $this->data);
    }

    public function approve($id) {
       $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $id = $this->utility->un_mask($id);
        $approval_exist = $this->common->get_this('credit_sales_approvals', ['credit_sales_id' => $id, 'exco_id' => $this->user->id, 'coop_id' => $this->coop->id]);

        $approval_complete = $this->utility->loan_approval_completed($id, $this->coop->credit_sales_approval_level, 'credit_sales', $approval_exist);

        if (!$this->utility->guarantor_approval_completed($id, $this->coop->id, 'credit_sales')) {
            $this->session->set_flashdata('error', lang('guarantor_approval_not_completed'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('credit_sales_approved')
        ];
        
        $approval_data = [
            'status' => 'approved',
            'action_date' => date('Y-m-d H:i:s')
        ];
        $this->common->start_trans();
        if ($approval_exist) { //if exco has once approved or declined
            $this->common->update_this('credit_sales_approvals', ['credit_sales_id' => $id, 'exco_id' => $this->user->id], $approval_data);
        } else {
            $approval_data['exco_id'] = $this->user->id;
            $approval_data['coop_id'] = $this->coop->id;
            $approval_data['credit_sales_id'] = $id;
            $this->common->add('credit_sales_approvals', $approval_data);
        }
        if ($approval_complete) { //if all exco has approved
            $this->common->update_this('credit_sales', ['id' => $id,], ['status' => 'approved']);
            $this->common->update_this('orders', ['credit_sales_id' => $id,], ['status' => 'approved']);
        }
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('credit_sales_approved'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }

        if ($approval_complete) {
            redirect('creditsales/approved');
        } else {
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    public function decline($id) {
        $id = $this->utility->un_mask($id);
        $declines = $this->info->get_credit_sales_approvals(['credit_sales.id' => $id, 'credit_sales_approvals.exco_id' => $this->user->id, 'credit_sales_approvals.status' => 'declined']);
        $exco_approval_exist = $this->common->get_this('credit_sales_approvals', ['credit_sales_id' => $id, 'exco_id' => $this->user->id, 'coop_id' => $this->coop->id]);
        $declines_complete = $this->utility->loan_approval_completed($declines, $this->coop->loan_approval_level, 'credit_sales');

        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('credit_sales_declined')
        ];
        $approval_data = [
            'status' => 'declined',
            'action_date' => date('Y-m-d H:i:s')
        ];
        $this->common->start_trans();
        if ($exco_approval_exist) { //if exco has once approved or declined
            $this->common->update_this('credit_sales_approvals', ['credit_sales_id' => $id, 'exco_id' => $this->user->id], $approval_data);
        } else {
            $approval_data['exco_id'] = $this->user->id;
            $approval_data['coop_id'] = $this->coop->id;
            $approval_data['credit_sales_id'] = $id;
            $this->common->add('credit_sales_approvals', $approval_data);
        }
        if ($declines_complete) { //if all exco has declined
            $this->common->update_this('credit_sales', ['id' => $id,], ['status' => 'declined']);
            $this->common->update_this('orders', ['credit_sales_id' => $id,], ['status' => 'cancelled']);
        }
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('credit_sales_declined'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }

        if ($declines_complete) {
            redirect('creditsales/approved');
        } else {
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

}
