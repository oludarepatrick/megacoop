<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Coa extends BASE_Controller {

    const MENU_ID = 17;

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
        $this->load->model('account_model', 'coa');
    }

    public function index() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->data['acc_title'] = $this->coa->get_acc_titles();
        $this->data['title'] = lang('coa');
        $this->data['controller'] = lang('coa');
        $this->layout->set_app_page('coa/index', $this->data);
    }

    public function accounts($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $id = $this->utility->un_mask($id);
        $this->data['assets_sub'] = $this->common->get_all_these('acc_sub_title', ['coop_id' => $this->coop->id, 'acc_title_id' => $id]);


        $title_sub = $this->coa->get_title_and_subtitle(['coop_id' => $this->coop->id, 'acc_title_id' => $id]);
        foreach ($title_sub as $key => $ts) {
            $title_sub[$key]->act_val = $this->coa->get_acc_values(['coop_id' => $this->coop->id, 'acc_sub_title_id' => $ts->sub_title_id]);
        }
         
        $this->data['acc_title_id'] = $this->utility->mask($id);
        $this->data['acc_title'] = $this->coa->get_acc_title(['id'=>$id]);
        $this->data['title_sub'] = $title_sub;
        $this->data['title'] = $this->data['acc_title']->name;
        $this->data['controller'] = lang('coa');
        $this->layout->set_app_page('coa/accounts', $this->data);
    }

    public function add_subtitle($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $id = $this->utility->un_mask($id);
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        $this->form_validation->set_rules('code', lang('code'), 'trim|required');

        if ($this->form_validation->run()) {
            $name = $this->input->post('name', true);
            $code = $this->input->post('code', true);
            if ($this->common->get_this('acc_sub_title', ['name' => $name, 'coop_id' => $this->coop->id])) {
                $this->session->set_flashdata('error', lang('diplicate_subtitle'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $subtitle = [
                'coop_id' => $this->coop->id,
                'acc_title_id' => $id,
                'name' => $name,
                'code' => $code
            ];

            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('add') . ' ' . lang('sub_title')
            ];

            $this->common->start_trans();
            $this->common->add('acc_sub_title', $subtitle);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('act_successful'));
                redirect($_SERVER["HTTP_REFERER"]);
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }
    }

    public function delete_subtitle($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xdelete', 'on');
        $id = $this->utility->un_mask($id);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('acc_subtitile_deleted')
        ];

        $this->common->start_trans();
        $this->common->delete_this('acc_sub_title', ['coop_id' => $this->coop->id, 'id' => $id]);
        $this->common->delete_this('acc_value', ['coop_id' => $this->coop->id, 'acc_sub_title_id' => $id]);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('acc_subtitile_deleted'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function add_acc_value($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $id = $this->utility->un_mask($id);
        $this->form_validation->set_rules('acc_subtitle', lang('sub_title'), 'trim|required');
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        $this->form_validation->set_rules('code', lang('code'), 'trim|required');

        if ($this->form_validation->run()) {
            $acc_subtitle = $this->input->post('acc_subtitle', true);
            $name = $this->input->post('name', true);
            $code = $this->input->post('code', true);

            $act_value = [
                'coop_id' => $this->coop->id,
                'acc_title_id' => $id,
                'acc_sub_title_id' => $acc_subtitle,
                'name' => $name,
                'code' => $code
            ];

            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('add') . ' ' . lang('sub_title')
            ];

            $this->common->start_trans();
            $this->common->add('acc_value', $act_value);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('act_successful'));
                redirect($_SERVER["HTTP_REFERER"]);
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }
    }

    public function delete_acc_value($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xdelete', 'on');
        $id = $this->utility->un_mask($id);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('acc_value_deleted')
        ];

        $this->common->start_trans();
        $this->common->delete_this('acc_value', ['coop_id' => $this->coop->id, 'id' => $id]);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('acc_value_deleted'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

}
