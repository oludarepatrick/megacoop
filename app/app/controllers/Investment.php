<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Investment extends BASE_Controller {
    const MENU_ID = 19;

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
        $this->data['investment_types'] = $this->common->get_all_these('investment_types',['coop_id'=> $this->coop->id]);
        $this->data['investment'] = $this->info->get_investment(['investment.coop_id'=> $this->coop->id]);
        $this->data['title'] = lang('investment');
        $this->data['controller'] = lang('investment');
        $this->layout->set_app_page('investment/index', $this->data);
    }

    public function add() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('investment_type', lang('investment_type'), 'trim|required');
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required');
        $this->form_validation->set_rules('roi', lang('roi'), 'trim|required');
        $this->form_validation->set_rules('rate', lang('rate'), 'trim|required');
        $this->form_validation->set_rules('maturity_year', lang('maturity_year'), 'trim|required');
        $this->form_validation->set_rules('start_date', lang('start_date'), 'trim|required');
        $this->form_validation->set_rules('end_date', lang('end_date'), 'trim|required');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');

        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('investment_added')
            ];
            $amount = str_replace(',', '', $this->input->post('amount'));
            $roi = str_replace(',', '', $this->input->post('roi'));
            $investment = $this->input->post();
            $investment['amount'] = $amount;
            $investment['roi'] = $roi;
            $investment['coop_id'] = $this->coop->id;
            $investment['user_id'] = $this->user->id;
            $investment['created_on'] = date('Y-m-d H:i:s');
            $this->common->start_trans();
            $this->common->add('investment', $investment);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('investment_added'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }
    
    public function edit() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('investment_type', lang('investment_type'), 'trim|required');
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required');
        $this->form_validation->set_rules('roi', lang('roi'), 'trim|required');
        $this->form_validation->set_rules('rate', lang('rate'), 'trim|required');
        $this->form_validation->set_rules('maturity_year', lang('maturity_year'), 'trim|required');
        $this->form_validation->set_rules('start_date', lang('start_date'), 'trim|required');
        $this->form_validation->set_rules('end_date', lang('end_date'), 'trim|required');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');

        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('investment_edited')
            ];
            
            $amount = str_replace(',', '', $this->input->post('amount'));
            $roi = str_replace(',', '', $this->input->post('roi'));
            $id = $this->input->post('id');
            foreach ($this->input->post() as $key=>$post){
                if($key == 'id'){
                    continue;
                }
                $investment[$key] = $post;
            }
            $investment['amount'] = $amount;
            $investment['roi'] = $roi;
            $this->common->start_trans();
            $this->common->update_this('investment',['id'=>$id, 'coop_id'=> $this->coop->id], $investment);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('investment_edited'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }


    public function ajax_get_investment() {
        $id = $this->input->get('id', true);
        $investment = $this->info->get_investment_details(['investment.id' => $id]);

        if (!$investment) {
            echo json_encode(array('status' => 'error', 'message' => 'No saivings type found'));
        } else {
            echo json_encode(array('status' => 'success', 'message' => $investment));
        }
    }

    public function delete($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xdelete', 'on');
        $id = $this->utility->un_mask($id);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('investment_deleted')
        ];
        
        $this->common->start_trans();
        $this->common->delete_this('investment', ['coop_id' => $this->coop->id, 'id' => $id]);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('investment_deleted'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }
    
    
}
