<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends BASE_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $id = $this->user->id;
        $this->data['existing_loans'] = $this->info->get_loans(['loans.user_id' => $id, 'loans.coop_id' => $this->coop->id, 'loans.status !=' => 'finished']);
        $this->data['active_loans'] = $this->info->get_loans(['loans.user_id' => $id, 'loans.coop_id' => $this->coop->id, 'loans.status' => 'disbursed']);
        $this->data['existing_credit_sales'] = $this->info->get_credit_sales(['credit_sales.user_id' => $id, 'credit_sales.coop_id' => $this->coop->id, 'credit_sales.status !=' => 'finished']);
        $this->data['active_credit_sales'] = $this->info->get_credit_sales(['credit_sales.user_id' => $id, 'credit_sales.coop_id' => $this->coop->id, 'credit_sales.status' => 'disbursed']);
        $savings_type = $this->common->get_all_these('savings_types', ['coop_id' => $this->coop->id]);
        $this->data['savings'] = [];
        foreach ($savings_type as $st) {
            $this->data['savings'][] = (object) [
                        'savings_type' => $st->id,
                        'name' => $st->name,
                        'total_savings' => $this->common->sum_this('savings', ['tranx_type' => 'credit', 'savings_type' => $st->id, 'user_id' => $id,], 'amount')->amount,
                        'bal' => $this->utility->get_savings_bal($id, $this->coop->id, $st->id),
            ];
        }
        $this->data['all_savings_bal'] = $this->utility->get_savings_bal($id, $this->coop->id);
        $this->data['all_loan_bal'] = $this->utility->get_loan_bal($id, $this->coop->id);
        $this->data['all_credit_sales_bal'] = $this->utility->get_credit_sales_bal($id, $this->coop->id);
        $this->data['months'] = $this->common->get_all('months');
        $this->data['banks'] = $this->common->get_all_these('banks', ['country_id' => $this->coop->country_id]);
        $this->data['user'] = $this->info->get_user_details(['users.id' => $id, 'users.coop_id' => $this->coop->id]);
        $kin_details2 = [];
        //process kin details
        $kin_details = json_decode($this->data['user']->kin_details);
        if ($kin_details) {
            for ($i = 0; $i < count($kin_details->kin_name); $i++) {
                $kin_data[] = $kin_details->kin_name[$i];
                $kin_data[] = $kin_details->kin_phone[$i];
                $kin_data[] = $kin_details->kin_address[$i];
            }
            $start = 0;
            $offset = 3;
            for ($i = 0; $i < count($kin_details->kin_name); $i++) {
                $kin_details2[] = array_slice($kin_data, $start, $offset);
                $start = $offset;
                $offset += $start;
            }
        }
        $this->data['kin_details'] = $kin_details2;
        $this->data['savings_types'] = $this->common->get_all_these('savings_types', ['coop_id' => $this->coop->id]);
        $this->data['title'] = lang('profile');
        $this->data['controller'] = lang('profile');
        $this->layout->set_app_page_member('member/profile/index', $this->data);
    }

    public function update() {
        $this->form_validation->set_rules('first_name', lang('first_name'), 'trim|required');
        $this->form_validation->set_rules('last_name', lang('last_name'), 'trim|required');
        // $this->form_validation->set_rules('email', lang('email'), 'trim|required');
        // $this->form_validation->set_rules('phone', lang('phone'), 'trim|required|numeric|min_length[11]|max_length[11]');
        $this->form_validation->set_rules('dob', lang('dob'), 'trim|required');
        $this->form_validation->set_rules('gender', lang('gender'), 'trim|required');
        $this->form_validation->set_rules('marital_status', lang('marital_status'), 'trim|required');
        $this->form_validation->set_rules('address', lang('address'), 'trim|required');
        // $this->form_validation->set_rules('acc_name', lang('acc_name'), 'trim|required');
        // $this->form_validation->set_rules('acc_no', lang('acc_no'), 'trim|required|numeric');
        // $this->form_validation->set_rules('bank_id', lang('bank'), 'trim|required');
        if ($this->form_validation->run()) {
            $email = $this->input->post('email', true);
            if($email !== $this->user->email){
                if ($this->common->get_this('users', ['coop_id' => $this->coop->id, 'email' => $email])) {
                    $this->session->set_flashdata('error', lang('email_exist'));
                    redirect($_SERVER["HTTP_REFERER"]);
                }
            }

            $phone = $this->input->post('phone', true);
            if($phone !== $this->user->phone){
                if ($this->common->get_this('users', ['coop_id' => $this->coop->id, 'phone' => $phone])) {
                    $this->session->set_flashdata('error', lang('email_exist'));
                    redirect($_SERVER["HTTP_REFERER"]);
                }
            }
            foreach ($this->input->post() as $key => $post) {
                if ($key == 'kin_name' or $key == 'kin_address' or $key == 'kin_phone') {
                    $kin_data[$key] = $post;
                } else if ($key != 'reg_fee' or $key != 'email' or $key != "phone" or $key !="username") {
                    $additional_data[$key] = $post;
                }
                // if ($key == 'kin_name' or $key == 'kin_address' or $key == 'kin_phone') {
                //     $kin_data[$key] = $post;
                // } else if ($key != 'reg_fee' or $key !="username") {
                //     $additional_data[$key] = $post;
                // }
            }
            $reg_date = $this->input->post('reg_date');
            $additional_data['year'] = substr($reg_date, 0, 4);
            $additional_data['month_id'] = intval(substr($reg_date, 5, 2));
            if ($_FILES['file']['error'] == 0) {
                $field_name = 'file';
                $file_name = $additional_data['first_name'] . $this->users->username;
                $upload_path = 'users';
                $max_upload = 100;
                $is_uploaded = $this->utility->img_upload($field_name, $file_name, $max_upload, $upload_path);
                if (isset($is_uploaded['error'])) {
                    $this->session->set_flashdata('error', $is_uploaded['error']);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $additional_data['avatar'] = $is_uploaded['upload_data']['file_name'];
            }

            $additional_data['kin_details'] = json_encode($kin_data);
            $additional_data['savings_amount'] = json_encode($this->input->post('savings_amount'));
            if ($this->common->update_this('users', ['id' => $this->user->id], $additional_data)) {
                $this->session->set_flashdata('message', lang('act_successful'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function security() {
        $this->data['title'] = lang('security');
        $this->data['controller'] = lang('profile');
        $this->layout->set_app_page_member('member/profile/security', $this->data);
    }
    public function preferences() {
        $this->data['title'] = lang('preferences');
        $this->data['controller'] = lang('profile');
        $this->layout->set_app_page_member('member/profile/preferences', $this->data);
    }

    public function change_password() {
        $this->load->library('ion_auth');
        $this->form_validation->set_rules('old_pass', $this->lang->line('old_pass'), 'required');
        $this->form_validation->set_rules('new_pass', $this->lang->line('new_pass'), 'required');
        if ($this->form_validation->run()) {
            $identity = $this->session->userdata('identity');
            $change = $this->ion_auth->change_password($identity, $this->input->post('old_pass'), $this->input->post('new_pass'));
            $password_data_exist = $this->common->get_this('password_manager', ['user_id'=>$this->user->id]);
            if ($change) {
                $update_on = date('Y:m:d H:i:s');
                $pass_data = [
                    'updated_on'=> $update_on,
                    'expired_on' => date('Y-m-d H:i:s', strtotime('+2160 hour +0 minutes', strtotime($update_on)))
                ];

                if($password_data_exist){
                    $this->common->update_this('password_manager', ['user_id'=>$this->user->id], $pass_data);
                }else{
                    $pass_data['user_id'] = $this->user->id;
                    $pass_data['user_id'] = $this->user->id;
                    $this->common->add('password_manager', $pass_data);
                }   
                $this->session->set_flashdata('message', $this->ion_auth->messages());
            } else {
                $this->session->set_flashdata('error', $this->ion_auth->errors());
            }
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }
    
    public function change_pin() {
        $this->form_validation->set_rules('old_pin', $this->lang->line('old_pin'), 'required');
        $this->form_validation->set_rules('new_pin', $this->lang->line('new_pin'), 'required');

        if ($this->form_validation->run()) {
            $hash_new_pin = hash('sha256', str_replace('-', '',$this->input->post('new_pin')));
            $hash_old_pin = hash('sha256', str_replace('-', '',$this->input->post('old_pin')));
            if($this->user->tranx_pin != $hash_old_pin){
                $this->session->set_flashdata('error', lang('tranx_pin_changed_failed'));
                redirect($_SERVER["HTTP_REFERER"]);
            }
            
            $change = $this->common->update_this('users',['id'=> $this->user->id, 'coop_id'=> $this->coop->id], ['tranx_pin'=>$hash_new_pin]);
            if ($change) {
                $this->session->set_flashdata('message', lang('tranx_pin_changed'));
            } else {
                $this->session->set_flashdata('error', lang('tranx_pin_changed_failed'));
            }
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function close_account(){
        $data = [
            'user_id'=> $this->user->id,
            'request_date' => date('Y-m-d H:i:s')
        ];

        $notification = [
            'coop_id' => $this->coop->id,
            'from' => $this->user->first_name . ' ' . $this->user->last_name,
            'description' => lang('exit_request'),
        ];

        $this->common->start_trans();
        $last_id = $this->common->add('member_exit', $data);
        // $notification['url'] =  base_url('memberexit/preview/'.$this->utility->mask($last_id));
        // $this->common->add('exco_notification', $notification);
        
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('act_successful'));
        }else{
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

     public function ajax_enable_2fa() {
        $status = $this->input->get('status', true);
        $user_id = $this->user->id;

        $this->common->start_trans();
        if ($status == 'false') {
            $this->common->update_this('users', ['id' => $user_id, 'coop_id' => $this->coop->id], ['twofa' => 'true']);
        }
        if ($status == 'true') {
            $this->common->update_this('users', ['id' => $user_id, 'coop_id' => $this->coop->id], ['twofa' => 'false']);
        }
        $this->common->finish_trans();
    }
     public function ajax_preferences() {
        $status = $this->input->get('status', true);
        $title = $this->input->get('title', true);
        $user_id = $this->user->id;
        $this->common->update_this('users', ['id' => $user_id, 'coop_id' => $this->coop->id], [$title => $status]);
    }
}
