<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends BASE_Controller {

    const MENU_ID = 11;

    public function __construct() {
        parent::__construct();
        if (!$this->ion_auth->is_admin()) {
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
        $this->data['roles'] = $this->common->get_all_these('role', ['coop_id' => $this->coop->id, 'group_id' => 1]);
        $this->data['title'] = lang('role');
        $this->data['controller'] = lang('users');
        $this->layout->set_app_page('users/role', $this->data);
    }

    public function add_role() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');
        
        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('role_added')
            ];
            $role = $this->input->post();
            $role['coop_id'] = $this->coop->id;
            $role['group_id'] = 1;
            $menu = $this->common->get_all('menu');

            //db operation
            $this->common->start_trans();
            $role_id = $this->common->add('role', $role);
            foreach ($menu as $m) {
                $this->common->add('privilege', ['coop_id' => $this->coop->id, 'menu_id' => $m->id, 'role_id' => $role_id]);
            }
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('role_added'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function edit_role() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');

        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('role_edited')
            ];
            $id = $this->input->post('id');
            $role = $this->input->post();

            $this->common->start_trans();
            $this->common->update_this('role', ['coop_id' => $this->coop->id, 'id' => $id], $role);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('role_edited'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function ajax_get_role() {
        $id = $this->input->get('id', true);
        $role = $this->common->get_this('role', ['id' => $id, 'coop_id' => $this->coop->id]);

        if (!$role) {
            echo json_encode(array('status' => 'error', 'message' => 'No Role found'));
        } else {
            echo json_encode(array('status' => 'success', 'message' => $role));
        }
    }

    public function delete_role($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xdelete', 'on');
        $id = $this->utility->un_mask($id);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('role_deleted')
        ];
        $this->common->start_trans();
        $this->common->delete_this('role', ['coop_id' => $this->coop->id, 'id' => $id]);
        $this->common->delete_this('privilege', ['role_id' => $id]);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('role_deleted'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function privilege($role_id = NULL) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $role_id = $this->utility->un_mask($role_id);
        $menu = $this->common->get_all('menu');
        $post_data = $this->input->post();

        if ($this->input->post('save') === 'save') {
            foreach ($menu as $m) {
                if (isset($post_data['xread'])) {

                    if (array_key_exists($m->id, $post_data['xread'])) {
                        $this->common->update_this('privilege', ['role_id' => $role_id, 'menu_id' => $m->id], ['xread' => 'on']);
                    } else {
                        $this->common->update_this('privilege', ['role_id' => $role_id, 'menu_id' => $m->id], ['xread' => 'off']);
                    }
                } else {
                    $this->common->update_this('privilege', ['role_id' => $role_id, 'menu_id' => $m->id], ['xread' => 'off']);
                }
            }
            foreach ($menu as $m) {
                if (isset($post_data['xwrite'])) {
                    if (array_key_exists($m->id, $post_data['xwrite'])) {
                        $this->common->update_this('privilege', ['role_id' => $role_id, 'menu_id' => $m->id], ['xwrite' => 'on']);
                    } else {
                        $this->common->update_this('privilege', ['role_id' => $role_id, 'menu_id' => $m->id], ['xwrite' => 'off']);
                    }
                } else {
                    $this->common->update_this('privilege', ['role_id' => $role_id, 'menu_id' => $m->id], ['xwrite' => 'off']);
                }
            }
            foreach ($menu as $m) {
                if (isset($post_data['xdelete'])) {
                    if (array_key_exists($m->id, $post_data['xdelete'])) {
                        $this->common->update_this('privilege', ['role_id' => $role_id, 'menu_id' => $m->id], ['xdelete' => 'on']);
                    } else {
                        $this->common->update_this('privilege', ['role_id' => $role_id, 'menu_id' => $m->id], ['xdelete' => 'off']);
                    }
                } else {
                    $this->common->update_this('privilege', ['role_id' => $role_id, 'menu_id' => $m->id], ['xdelete' => 'off']);
                }
            }
            $this->session->set_flashdata('message', lang('action_successful'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->data['menu'] = $this->info->get_privilege_by_role_id($role_id);
//        var_dump($this->data['menu']);exit;
        $this->data['role_id'] = $role_id;
        $this->data['title'] = lang('privilege');
        $this->data['controller'] = lang('users');
        $this->layout->set_app_page('users/privilege', $this->data);
    }

    // this method should be ran when ever a new menu is added to the app
    public function update_menu_privilege($menu_id = null) {
        if (!$menu_id) {
            $this->session->set_flashdata('error', 'Please supply menu ID');
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $menu_id;
        $all_roles = $this->common->get_all('role');
        foreach ($all_roles as $m) {
            if (!$this->common->get_this('privilege', ['coop_id' => $m->coop_id, 'menu_id' => $menu_id, 'role_id' => $m->id])) {
                $this->common->add('privilege', [
                    'coop_id' => $m->coop_id,
                    'menu_id' => $menu_id,
                    'role_id' => $m->id,
                    'xread' => 'off',
                    'xwrite' => 'off',
                    'xdelete' => 'off',
                ]);
            }
        }
        $this->session->set_flashdata('message', lang('action_successful'));
        redirect($_SERVER["HTTP_REFERER"]);
    }

}
