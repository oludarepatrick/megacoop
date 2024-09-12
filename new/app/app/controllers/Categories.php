<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Categories extends BASE_Controller {
    const MENU_ID = 10;

    public function __construct() {
        parent::__construct();
        if(!$this->ion_auth->is_admin()){
            redirect($_SERVER['HTTP_REFERER']);
        }
        
        if ($this->coop) {
            if ($this->coop->status === 'processing') {
                redirect('settings');
            }
        }
        $this->licence_cheker($this->coop, $this->app_settings);
    }

    public function index() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->data['savings_types'] = $this->common->get_all_these('savings_types', ['coop_id'=> $this->coop->id]);
        $this->data['title'] = lang('savings_type');
        $this->data['controller'] = lang('categories');
        $this->layout->set_app_page('categories/index', $this->data);
    }

    public function add_savings_type() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');
        $this->form_validation->set_rules('max_withdrawal', lang('max_withdrawal'), 'trim|required|numeric');

        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('savings_type_added')
            ];
            $savings = $this->input->post();
            $savings['coop_id'] = $this->coop->id;
            $savings['created_on'] = date('Y-m-d H:i:s');

            $this->common->start_trans();
            $this->common->add('savings_types', $savings);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('savings_type_added'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function edit_savings_type() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');
        $this->form_validation->set_rules('max_withdrawal', lang('max_withdrawal'), 'trim|required|numeric');

        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('savings_type_edited')
            ];
            $id = $this->input->post('id');
            $savings = $this->input->post();
            $this->common->start_trans();
            $this->common->update_this('savings_types', ['coop_id' => $this->coop->id, 'id' => $id], $savings);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('savings_type_edited'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function ajax_get_savings_type() {
        $id = $this->input->get('id', true);
        $savings_type = $this->common->get_this('savings_types', ['id' => $id]);

        if (!$savings_type) {
            echo json_encode(array('status' => 'error', 'message' => 'No saivings type found'));
        } else {
            echo json_encode(array('status' => 'success', 'message' => $savings_type));
        }
    }

    public function delete_savings_type($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xdelete', 'on');
        $id = $this->utility->un_mask($id);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('savings_type_deleted')
        ];
        
        $this->common->start_trans();
        $this->common->delete_this('savings_types', ['coop_id' => $this->coop->id, 'id' => $id]);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('savings_type_deleted'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }
    
    public function loan_type() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->data['loan_types'] =  $this->info->get_loan_types(['coop_id'=> $this->coop->id]);
        $calc_method = $this->common->get_all('loan_calc_method');
        foreach($calc_method as $c){
            $this->data['calc_method'][$c->id] = $c->name; 
        }
        $this->data['title'] = lang('loan_type');
        $this->data['controller'] = lang('categories');
        $this->layout->set_app_page('categories/loan_types', $this->data);
    }
    
     public function add_loan_type() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        $this->form_validation->set_rules('rate', lang('rate'), 'trim|required|numeric');
        $this->form_validation->set_rules('guarantor', lang('guarantor'), 'trim|required|integer');
        $this->form_validation->set_rules('min_month', lang('min_month'), 'trim|required|integer');
        $this->form_validation->set_rules('calc_method', lang('calc_method'), 'trim|required');
        $this->form_validation->set_rules('max_month', lang('max_month'), 'trim|required|integer');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');

        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('loan_type_added')
            ];
            $post_data = $this->input->post();
            $post_data['coop_id'] = $this->coop->id;
            $post_data['created_on'] = date('Y-m-d H:i:s');
            $this->common->start_trans();
            $this->common->add('loan_types', $post_data);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();
            
            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('loan_type_added'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }
    
    public function edit_loan_type() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        $this->form_validation->set_rules('rate', lang('rate'), 'trim|required|numeric');
        $this->form_validation->set_rules('guarantor', lang('guarantor'), 'trim|required|integer');
        $this->form_validation->set_rules('min_month', lang('min_month'), 'trim|required|integer');
        $this->form_validation->set_rules('max_month', lang('max_month'), 'trim|required|integer');
        $this->form_validation->set_rules('calc_method', lang('calc_method'), 'trim|required');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');

        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('loan_type_edited')
            ];
            $post_data = $this->input->post();
            $id = $this->input->post('id');
            $post_data['coop_id'] = $this->coop->id;
            $post_data['created_on'] = date('Y-m-d H:i:s');
            $this->common->start_trans();
            $this->common->update_this('loan_types', ['id'=>$id, 'coop_id'=> $this->coop->id], $post_data);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();
            
            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('loan_type_edited'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }
    
    public function ajax_get_loan_type() {
        $id = $this->input->get('id', true);
        $loan_type = $this->common->get_this('loan_types', ['id' => $id]);

        if (!$loan_type) {
            echo json_encode(array('status' => 'error', 'message' => 'Record not found'));
        } else {
            echo json_encode(array('status' => 'success', 'message' => $loan_type));
        }
    }

    public function delete_loan_type($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xdelete', 'on');
        $id = $this->utility->un_mask($id);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('loan_type_deleted')
        ];
        
        $this->common->start_trans();
        $this->common->delete_this('loan_types', ['coop_id' => $this->coop->id, 'id' => $id]);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('loan_type_deleted'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }
    
    public function product_type() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->data['loan_types'] = $this->info->get_product_types(['coop_id'=> $this->coop->id]);
        $calc_method = $this->common->get_all('loan_calc_method');
        foreach ($calc_method as $c) {
            $this->data['calc_method'][$c->id] = $c->name;
        }
        $this->data['title'] = lang('product_type');
        $this->data['controller'] = lang('categories');
        $this->layout->set_app_page('categories/product_type', $this->data);
    }
    
     public function add_product_type() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        $this->form_validation->set_rules('rate', lang('rate'), 'trim|required|numeric');
        $this->form_validation->set_rules('guarantor', lang('guarantor'), 'trim|required|integer');
        $this->form_validation->set_rules('min_month', lang('min_month'), 'trim|required|integer');
        $this->form_validation->set_rules('max_month', lang('max_month'), 'trim|required|integer');
        $this->form_validation->set_rules('calc_method', lang('calc_method'), 'trim|required');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');

        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('product_type_added')
            ];
            
            $post_data = $this->input->post();
            $post_data['coop_id'] = $this->coop->id;
            $post_data['created_on'] = date('Y-m-d H:i:s');
            $this->common->start_trans();
            $this->common->add('product_types', $post_data);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();
            
            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('product_type_added'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }
    
     public function edit_product_type() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        $this->form_validation->set_rules('rate', lang('rate'), 'trim|required|numeric');
        $this->form_validation->set_rules('guarantor', lang('guarantor'), 'trim|required|integer');
        $this->form_validation->set_rules('min_month', lang('min_month'), 'trim|required|integer');
        $this->form_validation->set_rules('max_month', lang('max_month'), 'trim|required|integer');
        $this->form_validation->set_rules('calc_method', lang('calc_method'), 'trim|required');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');

        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('product_type_edited')
            ];
            $post_data = $this->input->post();
            $id = $this->input->post('id');
            $post_data['coop_id'] = $this->coop->id;
            if(!array_key_exists('auto_approval',$post_data )){
                $post_data['auto_approval'] = 'no';
            }
            if(!array_key_exists('is_market_product',$post_data )){
                $post_data['is_market_product'] = 'no';
            }
            $this->common->start_trans();
            $this->common->update_this('product_types', ['id'=>$id, 'coop_id'=> $this->coop->id], $post_data);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();
            
            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('product_type_edited'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }
    
    public function ajax_get_product_type() {
        $id = $this->input->get('id', true);
        $loan_type = $this->common->get_this('product_types', ['id' => $id]);

        if (!$loan_type) {
            echo json_encode(array('status' => 'error', 'message' => 'Record not found'));
        } else {
            echo json_encode(array('status' => 'success', 'message' => $loan_type));
        }
    }

    public function delete_product_type($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xdelete', 'on');
        $id = $this->utility->un_mask($id);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('product_type_deleted')
        ];
        
        $this->common->start_trans();
        $this->common->delete_this('product_types', ['coop_id' => $this->coop->id, 'id' => $id]);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('product_type_deleted'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }
    
     public function investment_type() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->data['investment_types'] = $this->common->get_all_these('investment_types', ['coop_id'=> $this->coop->id]);
        $this->data['title'] = lang('investment_type');
        $this->data['controller'] = lang('categories');
        $this->layout->set_app_page('categories/investment_type', $this->data);
    }
    
    public function add_investment_type() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');

        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('investment_type_added')
            ];
            $investment = $this->input->post();
            $investment['coop_id'] = $this->coop->id;
            $investment['created_on'] = date('Y-m-d H:i:s');

            $this->common->start_trans();
            $this->common->add('investment_types', $investment);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('investment_type_added'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function edit_investment_type() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');

        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('investment_type_edited')
            ];
            $id = $this->input->post('id');
            $investment = $this->input->post();

            $this->common->start_trans();
            $this->common->update_this('investment_types', ['coop_id' => $this->coop->id, 'id' => $id], $investment);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('investment_type_edited'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function ajax_get_investment_type() {
        $id = $this->input->get('id', true);
        $investment_type = $this->common->get_this('investment_types', ['id' => $id]);

        if (!$investment_type) {
            echo json_encode(array('status' => 'error', 'message' => 'No saivings type found'));
        } else {
            echo json_encode(array('status' => 'success', 'message' => $investment_type));
        }
    }

    public function delete_investment_type($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xdelete', 'on');
        $id = $this->utility->un_mask($id);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('investment_type_deleted')
        ];
        
        $this->common->start_trans();
        $this->common->delete_this('investment_types', ['coop_id' => $this->coop->id, 'id' => $id]);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('investment_type_deleted'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }
}
