<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Miscellaneous extends BASE_Controller {

    const MENU_ID = 8;

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
    }

    public function index() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->data['bye_law'] = $this->common->get_all_these('bye_law', ['coop_id'=>$this->coop->id]);
        $this->data['title'] = lang('bye_law');
        $this->data['controller'] = lang('miscellaneous');
        $this->layout->set_app_page('miscellaneous/index', $this->data);
    }

    public function add_bye_law(){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('title', lang('title'), 'trim|required');
        $this->form_validation->set_rules('content', lang('content'), 'trim|required');

        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('bye_law').' '.lang('added')
            ];
            $bye_law_data = [
                'coop_id'=>$this->coop->id,
                'title'=>$this->input->post('title'),
                'content'=>$this->input->post('content'),
            ];
            $this->common->start_trans();
            $this->common->add('bye_law', $bye_law_data);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('bye_law') . ' ' . lang('added'));
                redirect('miscellaneous');
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        }
        $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->session->set_flashdata('error', $err);
        $this->data['title'] = lang('add').' '.lang('bye_law');
        $this->data['controller'] = lang('miscellaneous');
        $this->layout->set_app_page('miscellaneous/add_bye_law', $this->data);
    }

    public function edit_bye_law($id){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('title', lang('title'), 'trim|required');
        $this->form_validation->set_rules('content', lang('content'), 'trim|required');
        $this->data['id']=$id;
        $id = $this->utility->un_mask($id);
        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('bye_law').' '.lang('edited')
            ];
            $bye_law_data = [
                'title'=>$this->input->post('title'),
                'content'=>$this->input->post('content'),
                'updated_on'=>date('Y-m-d H:i:s'),
            ];
            $this->common->start_trans();
            $this->common->update_this('bye_law', ['coop_id'=>$this->coop->id, 'id'=>$id], $bye_law_data);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('bye_law') . ' ' . lang('edited'));
                redirect('miscellaneous');
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        }
        $this->data['bye_law'] = $this->common->get_this('bye_law', ['coop_id'=>$this->coop->id, 'id'=>$id]);

        $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->session->set_flashdata('error', $err);
        $this->data['title'] = lang('edit').' '.lang('bye_law');
        $this->data['controller'] = lang('miscellaneous');
        $this->layout->set_app_page('miscellaneous/edit_bye_law', $this->data);
    }

    public function view_bye_law($id){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $id = $this->utility->un_mask($id);
        $this->data['bye_law'] = $this->common->get_this('bye_law', ['coop_id' => $this->coop->id, 'id' => $id]);
        $this->data['title'] = lang('preview') . ' ' . lang('bye_law');
        $this->data['controller'] = lang('miscellaneous');
        $this->layout->set_app_page('miscellaneous/view_bye_law', $this->data);
    }

    public function minutes() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->data['minutes'] = $this->common->get_all_these('minutes', ['coop_id'=>$this->coop->id]);
        $this->data['title'] = lang('minute');
        $this->data['controller'] = lang('miscellaneous');
        $this->layout->set_app_page('miscellaneous/minute', $this->data);
    }

    public function add_minute(){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('title', lang('title'), 'trim|required');
        $this->form_validation->set_rules('content', lang('content'), 'trim|required');

        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('minutes') . ' ' . lang('added')
            ];
            $minutes_data = [
                'coop_id' => $this->coop->id,
                'title' => $this->input->post('title'),
                'content' => $this->input->post('content'),
            ];
            $this->common->start_trans();
            $this->common->add('minutes', $minutes_data);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('minutes') . ' ' . lang('added'));
                redirect('miscellaneous/minutes');
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        }
        $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->session->set_flashdata('error', $err);
        $this->data['title'] = lang('add') . ' ' . lang('minutes');
        $this->data['controller'] = lang('miscellaneous');
        $this->layout->set_app_page('miscellaneous/add_minute', $this->data);
    }

    public function edit_minute($id){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('title', lang('title'), 'trim|required');
        $this->form_validation->set_rules('content', lang('content'), 'trim|required');
        $this->data['id']=$id;
        $id = $this->utility->un_mask($id);
        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('minute').' '.lang('edited')
            ];
            $minute_data = [
                'title'=>$this->input->post('title'),
                'content'=>$this->input->post('content'),
                'updated_on'=>date('Y-m-d H:i:s'),
            ];
            $this->common->start_trans();
            $this->common->update_this('minutes', ['coop_id'=>$this->coop->id, 'id'=>$id], $minute_data);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('minute') . ' ' . lang('edited'));
                redirect('miscellaneous/minutes');
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        }
        $this->data['minutes'] = $this->common->get_this('minutes', ['coop_id'=>$this->coop->id, 'id'=>$id]);

        $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->session->set_flashdata('error', $err);
        $this->data['title'] = lang('edit').' '.lang('minute');
        $this->data['controller'] = lang('miscellaneous');
        $this->layout->set_app_page('miscellaneous/edit_minute', $this->data);
    }

    public function view_minute($id){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $id = $this->utility->un_mask($id);
        $this->data['minutes'] = $this->common->get_this('minutes', ['coop_id' => $this->coop->id, 'id' => $id]);
        $this->data['title'] = lang('preview') . ' ' . lang('minute');
        $this->data['controller'] = lang('miscellaneous');
        $this->layout->set_app_page('miscellaneous/view_minute', $this->data);
    }

     public function training() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->data['training'] = $this->info->get_trained_users(['training.coop_id'=>$this->coop->id]);
        $this->data['title'] = lang('training');
        $this->data['controller'] = lang('miscellaneous');
        $this->layout->set_app_page('miscellaneous/training', $this->data);
    }

    public function add_training(){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('title', lang('title'), 'trim|required');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');
        $this->form_validation->set_rules('member_id', lang('member_id'), 'trim|required');

        if ($this->form_validation->run()) {
            $member_id = $this->input->post('member_id');
            $user = $this->common->get_this('users', ['coop_id'=>$this->coop->id, 'username'=>$member_id]);
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('training') . ' ' . lang('added')
            ];
            $training_data = [
                'user_id' => $user->id,
                'coop_id' => $this->coop->id,
                'title' => $this->input->post('title'),
                'description' => $this->input->post('description'),
            ];
            $this->common->start_trans();
            $this->common->add('training', $training_data);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('training') . ' ' . lang('added'));
                redirect('miscellaneous/training');
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        }
        $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->session->set_flashdata('error', $err);
        redirect('miscellaneous/training');
    }

    public function delete_training($id){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xdelete', 'on');
        $id = $this->utility->un_mask($id);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('training').' '. lang('deleted')
        ];

        $this->common->start_trans();
        $this->common->delete_this('training', ['coop_id' => $this->coop->id, 'id' => $id]);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('training') . ' ' . lang('deleted'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

}
