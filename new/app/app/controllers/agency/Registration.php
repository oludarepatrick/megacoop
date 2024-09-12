<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Registration extends BASE_Controller {

    const MENU_ID = 1;

    public function __construct() {
        parent::__construct();
        
    }

    public function index() {
        $this->data['members'] = $this->common->get_limit("users", ['coop_agent_id'=>$this->user->id,'coop_id' => $this->coop->id], 1000, 'id', 'DESC');
        $this->data['total_approved'] = $this->common->count_this('users', ['coop_agent_id' => $this->user->id, 'coop_id' => $this->coop->id, 'status' => 'approved']);
        $this->data['total_pending'] = $this->common->count_this('users', ['coop_agent_id' => $this->user->id, 'coop_id' => $this->coop->id, 'status' => 'pending']);
        $this->data['total_exit'] = $this->common->count_this('users', ['coop_agent_id' => $this->user->id, 'coop_id' => $this->coop->id, 'status' => 'exit']);
        $this->data['title'] = lang('members');
        $this->data['controller'] = lang('registration');
        $this->layout->set_app_page_agency('registration/index', $this->data);
    }

    public function add_member() {
        $this->form_validation->set_rules('username', lang('username'), 'trim|required|alpha_numeric|is_unique[users.username]');
        $this->form_validation->set_rules('first_name', lang('first_name'), 'trim|required');
        $this->form_validation->set_rules('last_name', lang('last_name'), 'trim|required');
        // $this->form_validation->set_rules('email', lang('email'), 'trim|is_unique[users.email]', 
        //         array('is_unique' => 'The %s you entered has already been used by another member, kindly try another email'));
        // $this->form_validation->set_rules('phone', lang('phone'), 'trim|required|is_unique[users.phone]|numeric|min_length[11]|max_length[11]', 
        //         array('is_unique' => 'The %s you entered has already been used by another member, kindly try another phone'));
        $this->form_validation->set_rules('password', lang('password'), 'trim|required');
        $this->form_validation->set_rules('dob', lang('dob'), 'trim');
        $this->form_validation->set_rules('gender', lang('gender'), 'trim|required');
        $this->form_validation->set_rules('marital_status', lang('marital_status'), 'trim|required');
        $this->form_validation->set_rules('address', lang('address'), 'trim|required');
        $this->form_validation->set_rules('month_id', lang('start_month'), 'trim|required');
        $this->form_validation->set_rules('year', lang('year'), 'trim|required');
        // $this->form_validation->set_rules('monthly_savings', lang('monthly_savings'), 'trim|required');
        $this->form_validation->set_rules('acc_name', lang('acc_name'), 'trim');
        $this->form_validation->set_rules('acc_no', lang('acc_no'), 'trim');
        $this->form_validation->set_rules('bank_id', lang('bank'), 'trim');
        $this->form_validation->set_rules('id_card_id', lang('id_card'), 'trim');

        $this->form_validation->run();
        $phone = $this->input->post('phone');
        $email = $this->input->post('email');
        if ($this->common->get_this('users', ['coop_id' => $this->coop->id, 'phone' => $phone])) {
            $this->session->set_flashdata('error', lang('phone_exist'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if ($this->common->get_this('users', ['coop_id' => $this->coop->id, 'email' => $email])) {
            $this->session->set_flashdata('error', lang('email_exist'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if ($this->form_validation->run()) {
            if (!$this->utility->subscription_status($this->coop->id)) {
                $this->session->set_flashdata('error', lang('registration_limit_exided'));
                redirect('susbsription');
            }

            foreach ($this->input->post() as $key => $post) {
                if ($key == 'username'or $key == 'password' or $key == 'email') {
                    $auth_data[$key] = $post;
                } elseif ($key == 'kin_name' or $key == 'kin_address' or $key == 'kin_phone') {
                    $kin_data[$key] = $post;
                }else {
                    $additional_data[$key] = $post;
                }
            }

            $member_role = $this->common->get_this('role', ['coop_id' => $this->coop->id, 'group_id' => 2]);
            $additional_data['role_id'] = $member_role->id;

            if ($_FILES['file']['error'] == 0) {
                $field_name = 'file';
                $file_name = $additional_data['first_name'] . $auth_data['username'];
                $upload_path = 'users';
                $max_upload = 100;
                $is_uploaded = $this->utility->img_upload($field_name, $file_name, $max_upload, $upload_path);
                if (isset($is_uploaded['error'])) {
                    $this->session->set_flashdata('error', $is_uploaded['error']);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $additional_data['avatar'] = $is_uploaded['upload_data']['file_name'];
            }

            if ($this->input->post('id_card_id') and $_FILES['id_card']['error'] == 4) {
                $this->session->set_flashdata('error', 'ID card type selected but no ID uploaded');
                redirect($_SERVER["HTTP_REFERER"]);
            }

            if ($_FILES['id_card']['error'] == 0) {
                $field_name = 'id_card';
                $file_name = $additional_data['first_name'] . $auth_data['username'];
                $upload_path = 'idcards';
                $max_upload = 100;
                $is_uploaded = $this->utility->img_upload($field_name, $file_name, $max_upload, $upload_path);
                if (isset($is_uploaded['error'])) {
                    $this->session->set_flashdata('error', $is_uploaded['error']);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $additional_data['avatar'] = $is_uploaded['upload_data']['file_name'];
                $id_card_uploaded = true;
            }

            $groups = [2];
            $member_role = $this->common->get_this('role', ['coop_id' => $this->coop->id, 'group_id' => 2]);
            $additional_data['role_id'] = $member_role->id;
            $additional_data['coop_id'] = $this->coop->id;
            $additional_data['coop_agent_id'] = $this->user->id;
            $additional_data['status'] = 'pending';
            $additional_data['active'] = 0;

            if($id_card_uploaded){
                $additional_data['status'] = 'approved';
                $additional_data['active'] = 1;
            }

            $additional_data['tranx_pin'] = hash('sha256', '123456');
            $additional_data['kin_details'] = json_encode($kin_data);
            $additional_data['savings_amount'] = json_encode($this->input->post('savings_amount'));

            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('member_added')
            ];

            $this->common->start_trans();
            $this->ion_auth->register($auth_data['username'], $auth_data['password'], $auth_data['email'], $additional_data, $groups);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->data['name'] = ucwords($additional_data['first_name'] . ' ' . $additional_data['last_name']);
                $this->data['member_id'] = $auth_data['username'];
                $subject = 'Member Account Creation';
                $message = $this->load->view('emails/email_mem_reg', $this->data, true);
                $this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $auth_data['email'], $subject, $message);
                $this->session->set_flashdata('message', lang('member_added'));
                redirect($_SERVER["HTTP_REFERER"]);
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->data['roles'] = $this->common->get_all_these('role', ['coop_id' => $this->coop->id]);
            $this->data['months'] = $this->common->get_all('months');
            $id_cards = $this->common->get_all('id_cards');
            $this->data['id_card'][''] = "None";
            foreach ($id_cards as $id) {
                $this->data['id_card'][$id->id] = $id->name;
            }
            $this->data['savings_types'] = $this->common->get_all_these('savings_types', ['coop_id' => $this->coop->id]);
            $this->data['banks'] = $this->common->get_all_these('banks', ['country_id' => $this->coop->country_id]);
            $this->data['member_id'] = $this->utility->generate_member_id($this->coop->coop_name, $this->coop->id);
            $this->data['default_pwd'] = $this->utility->generate_default_pass($this->coop->coop_name);
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->data['title'] = lang('add_member');
            $this->data['controller'] = lang('registration');
            $this->layout->set_app_page_agency('registration/add_member', $this->data);
        }
    }

    public function edit_member($id = null) {
        $this->form_validation->set_rules('first_name', lang('first_name'), 'trim|required');
        $this->form_validation->set_rules('last_name', lang('last_name'), 'trim|required');
        $this->form_validation->set_rules('gender', lang('gender'), 'trim|required');
        $this->form_validation->set_rules('marital_status', lang('marital_status'), 'trim|required');
        $this->form_validation->set_rules('address', lang('address'), 'trim|required');
        $this->form_validation->set_rules('month_id', lang('start_month'), 'trim|required');
        $this->form_validation->set_rules('year', lang('year'), 'trim|required');
        $this->form_validation->set_rules('acc_name', lang('acc_name'), 'trim');
        $this->form_validation->set_rules('acc_no', lang('acc_no'), 'trim');
        $this->form_validation->set_rules('bank_id', lang('bank'), 'trim');
        
        $id = $this->utility->un_mask($id);
        if ($this->form_validation->run()) {
            foreach ($this->input->post() as $key => $post) {
                if ($key == 'kin_name' or $key == 'kin_address' or $key == 'kin_phone') {
                    $kin_data[$key] = $post;
                } else if ($key != 'reg_fee' or $key != 'email' or $key != "phone" or $key != "username") {
                    if($post == '') continue;
                    $additional_data[$key] = $post;
                }
            }

            $member_role = $this->common->get_this('role', ['coop_id' => $this->coop->id, 'group_id' => 2]);
            $additional_data['role_id'] = $member_role->id;

            if ($_FILES['file']['error'] == 0) {
                $field_name = 'file';
                $file_name = $additional_data['first_name'] . $this->input->post('username');
                $upload_path = 'users';
                $max_upload = 100;
                $is_uploaded = $this->utility->img_upload($field_name, $file_name, $max_upload, $upload_path);
                if (isset($is_uploaded['error'])) {
                    $this->session->set_flashdata('error', $is_uploaded['error']);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $additional_data['avatar'] = $is_uploaded['upload_data']['file_name'];
            }
            if ($this->input->post('id_card_id') and $_FILES['id_card']['error'] == 4) {
                $this->session->set_flashdata('error', 'ID card type selected but no ID uploaded');
                redirect($_SERVER["HTTP_REFERER"]);
            }

            if ($_FILES['id_card']['error'] == 0) {
                $field_name = 'id_card';
                $file_name = $additional_data['first_name'] . $this->input->post('username');
                $upload_path = 'idcards';
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
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('member_edited')
            ];

            $this->common->start_trans();
            $this->common->update_this('users', ['id' => $id], $additional_data);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
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

    public function profile($id = null) {
        $id = $this->utility->un_mask($id);
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
        $id_cards = $this->common->get_all('id_cards');
        $this->data['id_card'][''] = "None";
        foreach ($id_cards as $id) {
            $this->data['id_card'][$id->id] = $id->name;
        }
        $this->data['savings_types'] = $this->common->get_all_these('savings_types', ['coop_id' => $this->coop->id]);
        $this->data['roles'] = $this->common->get_all_these('role', ['coop_id' => $this->coop->id]);
        $this->data['title'] = lang('profile');
        $this->data['controller'] = lang('registration');
        $this->layout->set_app_page_agency('registration/profile', $this->data);
    }

    public function ajax_member_approval() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $status = $this->input->get('status', true);
        $user_id = $this->input->get('user_id', true);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('member_acc_clossed')
        ];

        $this->common->start_trans();
        if ($status == 'false') {
            $activities['action'] = lang('member_acc_enabled');
            $this->common->update_this('users', ['id' => $user_id, 'coop_id' => $this->coop->id], ['status' => 'approved', 'active' => 1]);
        }
        if ($status == 'true') {
            $activities['action'] = lang('member_acc_clossed');
            $this->common->update_this('users', ['id' => $user_id, 'coop_id' => $this->coop->id], ['status' => 'pending', 'active' => 0]);
        }
        $this->common->add('activities', $activities);
        $this->common->finish_trans();
    }

    public function delete_member($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xdelete', 'on');
        $id = $this->utility->un_mask($id);

        $is_admin = $this->common->get_this('users_groups', ['user_id'=>$id, 'group_id'=>1]);

        if($is_admin){
            $this->session->set_flashdata('error', lang('cannot_delete_admin_account'));
             redirect($_SERVER["HTTP_REFERER"]);
        }

        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('member_deleted')
        ];

        $this->common->start_trans();
        $this->common->delete_this('users', ['id' => $id, 'coop_id' => $this->coop->id]);
        $this->common->delete_this('savings', ['user_id' => $id, 'coop_id' => $this->coop->id]);
        $this->common->delete_this('loans', ['user_id' => $id, 'coop_id' => $this->coop->id]);
        $this->common->delete_this('loan_repayment', ['user_id' => $id, 'coop_id' => $this->coop->id, 'id' => $id]);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('member_deleted'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function reset_password($id){
        $this->load->library('ion_auth');
        $id = $this->utility->un_mask($id);
        $identity = $this->common->get_this("users", ['id' => $id]);
        if ($identity) {
            // var_dump($identity);exit;
            $change = $this->ion_auth->reset_password($identity->email,"12345678");

            if ($change) {
                //if the password was successfully changed
                $this->session->set_flashdata('message', lang('act_successful'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function update_email($id){
         $this->form_validation->set_rules('email', lang('email'), 'trim|required|is_unique[users.email]', 
                array('is_unique' => 'The %s you entered has already been used by another member or is the same as the previous %s, kindly try another email'));
        
        $id = $this->utility->un_mask($id);
        if ($this->form_validation->run()) {
            $data['email'] = $this->input->post('email', true);
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('email') . ' ' . lang('edited'),
            ];

            $this->common->start_trans();
            $this->common->update_this('users', ['id' => $id, 'coop_id' => $this->coop->id], $data);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('email').' '. lang('edited'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
            
        }
        $this->session->set_flashdata('error', validation_errors());
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function update_phone($id){
        $this->form_validation->set_rules('phone', lang('phone'), 'trim|required|is_unique[users.phone]|numeric|min_length[11]|max_length[11]', 
                array('is_unique' => 'The %s you entered has already been used by another member or the same as the previous, kindly try another phone'));
        $id = $this->utility->un_mask($id);
        if ($this->form_validation->run()) {
            $data['phone'] = $this->input->post('phone', true);
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('phone') . ' ' . lang('edited'),
            ];

            $this->common->start_trans();
            $this->common->update_this('users', ['id' => $id, 'coop_id' => $this->coop->id], $data);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('phone').' '. lang('edited'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
            
        }
        $this->session->set_flashdata('error', validation_errors());
        redirect($_SERVER["HTTP_REFERER"]);
    }

}
