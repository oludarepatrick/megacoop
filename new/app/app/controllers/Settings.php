<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends BASE_Controller {

    const MENU_ID = 12;

    public function __construct() {
        parent::__construct();
        if (!$this->ion_auth->is_admin()) {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function notification($action) {
        if ($action == 'no') {
            $this->common->update_this('cooperatives', ['id' => $this->coop->id], ['record_upload_reminder' => 'no']);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function index() {
        if ($this->coop->status != 'processing') {
            $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        }
        $this->form_validation->set_rules('coop_name', lang('coop_name'), 'trim|required');
        $this->form_validation->set_rules('reg_no', lang('reg_no'), 'trim');
        $this->form_validation->set_rules('coop_address', lang('coop_address'), 'trim|required');
        $this->form_validation->set_rules('country_id', lang('country'), 'trim|required');
        $this->form_validation->set_rules('state_id', lang('state'), 'trim|required');
        $this->form_validation->set_rules('city_id', lang('city'), 'trim|required');
        $this->form_validation->set_rules('acc_name', lang('acc_name'), 'trim|required');
        $this->form_validation->set_rules('acc_no', lang('acc_no'), 'trim|required');
        $this->form_validation->set_rules('bank_id', lang('bank'), 'trim|required');
        $this->form_validation->set_rules('contact_name', lang('full_name'), 'trim|required');
        $this->form_validation->set_rules('contact_email', lang('email'), 'trim|required');
        $this->form_validation->set_rules('contact_phone', lang('phone'), 'trim|required');
        $this->form_validation->set_rules('contact_address', lang('address'), 'trim|required');
        if (!$this->coop->url) {
            $this->form_validation->set_rules('url', lang('url'), 'trim|required|alpha_numeric|is_unique[cooperatives.url]',
                    array('is_unique' => 'The %s you entered has already been used by another cooperative, kindly try another url'));
        }
        $settings_data = $this->input->post();
        $settings_data['status'] = 'active';
        if($this->input->post('url')){
            $settings_data['url'] = strtolower($this->input->post('url'));
        }
        
        if ($this->form_validation->run() === TRUE and $this->common->update_this('cooperatives', ['id' => $this->coop->id], $settings_data)) {
            if ($this->coop->status == 'processing') {
                $this->user_role_config();
            }
            $this->session->set_flashdata('message', lang('act_successful'));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $this->data['country'] = $this->common->get_all('country');
            $this->data['mystate'] = $this->common->get_this('state', ['id' => $this->coop->state_id]);
            $this->data['state'] = $this->common->get_all_these('state', ['country_id' => $this->coop->country_id]);
            $this->data['mycity'] = $this->common->get_this('cities', ['id' => $this->coop->city_id]);
            $this->data['city'] = $this->common->get_all_these('cities', ['country_id' => $this->coop->country_id]);
            $this->data['mybank'] = $this->common->get_this('banks', ['id' => $this->coop->bank_id]);
            $this->data['bank'] = $this->common->get_all_these('banks', ['country_id' => $this->coop->country_id]);
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->data['controller'] = lang('settings');
            $this->data['title'] = lang('settings');
            $this->layout->set_app_page('settings/index', $this->data);
        }
    }

    public function upload() {
        if ($this->coop->status != 'processing') {
            $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        }
        if ($_FILES['file']['error'] == 0) {
            $field_name = 'file';
            $file_name = $this->coop->coop_code;
            $upload_path = 'logo/coop/';
            $max_upload = 50;
            $is_uploaded = $this->utility->img_upload($field_name, $file_name, $max_upload, $upload_path);
            if (isset($is_uploaded['error'])) {
                $this->session->set_flashdata('error', $is_uploaded['error']);
                redirect($_SERVER["HTTP_REFERER"]);
            }
            $settings_data['logo'] = $is_uploaded['upload_data']['file_name'];
        }
        if ($this->common->update_this('cooperatives', ['id' => $this->coop->id], $settings_data)) {
            $this->session->set_flashdata('message', lang('act_successful'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function paystack() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('paystack_secrete', lang('paystack_secrete'), 'trim|required');
        $this->form_validation->set_rules('paystack_public', lang('paystack_public'), 'trim|required');

        $settings_data = $this->input->post();
        if (!$this->input->post('paystack_status')) {
            $settings_data['paystack_status'] = 'off';
        }
        if ($this->form_validation->run() === TRUE and $this->common->update_this('cooperatives', ['id' => $this->coop->id], $settings_data)) {
            $this->session->set_flashdata('message', lang('act_successful'));
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function flutter() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('flutter_secrete', lang('flutter_secrete'), 'trim|required');
        $this->form_validation->set_rules('flutter_public', lang('flutter_public'), 'trim|required');

        $settings_data = $this->input->post();
        if (!$this->input->post('flutter_status')) {
            $settings_data['flutter_status'] = 'off';
        }
        if ($this->form_validation->run() === TRUE and $this->common->update_this('cooperatives', ['id' => $this->coop->id], $settings_data)) {
            $this->session->set_flashdata('message', lang('act_successful'));
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function sms() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('beta_sms_username', lang('beta_sms_username'), 'trim|required');
        $this->form_validation->set_rules('beta_sms_pass', lang('beta_sms_pass'), 'trim|required');

        $settings_data = $this->input->post();
        if (!$this->input->post('sms_notice')) {
            $settings_data['sms_notice'] = 'off';
        }
        if ($this->form_validation->run() === TRUE and $this->common->update_this('cooperatives', ['id' => $this->coop->id], $settings_data)) {
            $this->session->set_flashdata('message', lang('act_successful'));
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function loan() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('loan_processing_fee', lang('loan_processing_fee'), 'trim|required|numeric');
        $this->form_validation->set_rules('loan_approval_level', lang('loan_approval_level'), 'trim|required|numeric');
        $this->form_validation->set_rules('next_payment_date', lang('next_payment_date'), 'trim|required|numeric');
        $this->form_validation->set_rules('max_loan_requestable', lang('max_loan_requestable'), 'trim|required|numeric');

        $settings_data = $this->input->post();
        if ($this->form_validation->run() === TRUE and $this->common->update_this('cooperatives', ['id' => $this->coop->id], $settings_data)) {
            $this->session->set_flashdata('message', lang('act_successful'));
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function others() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('credit_sales_approval_level', lang('credit_sales_approval_level'), 'trim|required|numeric');
        $this->form_validation->set_rules('member_exit_approval_level', lang('member_exit_approval_level'), 'trim|required|numeric');
        $this->form_validation->set_rules('withdrawal_approval_level', lang('withdrawal_approval_level'), 'trim|required|numeric');

        $settings_data = $this->input->post();
        $settings_data['approve_reg_member'] = 'true';
        if(empty($this->input->post('approve_reg_member'))){
            $settings_data['approve_reg_member'] = 'false';
        }
        if ($this->form_validation->run() === TRUE and $this->common->update_this('cooperatives', ['id' => $this->coop->id], $settings_data)) {
            $this->session->set_flashdata('message', lang('act_successful'));
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    private function user_role_config() {
        $member_role_exist = $this->common->get_this('role', ['coop_id' => $this->coop->id, 'group_id' => 2]);
        $admin_role_exist = $this->common->get_this('role', ['coop_id' => $this->coop->id, 'group_id' => 1]);

        $role['coop_id'] = $this->coop->id;

        $menu = $this->common->get_all('menu');

        $this->common->start_trans();
        if (!$member_role_exist) {
            $role['group_id'] = 2;
            $role['description'] = 'Has no administarative privilege';
            $role['name'] = 'Member Role';

            $role_id = $this->common->add('role', $role);
            foreach ($menu as $m) {
                $this->common->add('privilege', ['coop_id' => $this->coop->id, 'menu_id' => $m->id, 'role_id' => $role_id]);
            }
        }

        if (!$admin_role_exist) {
            $role['group_id'] = 1;
            $role['description'] = 'Has all administarative privilege';
            $role['name'] = 'Supper Admin';
            $role_id = $this->common->add('role', $role);
            $this->common->update_this('users', ['id' => $this->user->id], ['role_id' => $role_id]);
            foreach ($menu as $m) {
                $this->common->add('privilege', [
                    'coop_id' => $this->coop->id,
                    'menu_id' => $m->id,
                    'role_id' => $role_id,
                    'xread' => 'on',
                    'xwrite' => 'on',
                    'xdelete' => 'on',
                ]);
            }
        }
        $this->common->finish_trans();
    }

    public function clear_notification(){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xdelete', 'on');
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('notification') . ' ' . lang('deleted')
        ];
        $this->common->start_trans();
        $this->common->delete_this('exco_notification', ['coop_id'=>$this->coop->id]);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', $activities['action']);
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }
}
