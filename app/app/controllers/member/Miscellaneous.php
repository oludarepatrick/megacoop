<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Miscellaneous extends BASE_Controller {
 public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->data['bye_law'] = $this->common->get_all_these('bye_law', ['coop_id'=>$this->coop->id]);
        $this->data['title'] = lang('bye_law');
        $this->data['controller'] = lang('miscellaneous');
        $this->layout->set_app_page_member('member/miscellaneous/index', $this->data);
    }

    public function view_bye_law($id){
        $id = $this->utility->un_mask($id);
        $this->data['bye_law'] = $this->common->get_this('bye_law', ['coop_id' => $this->coop->id, 'id' => $id]);
        $this->data['title'] = lang('preview') . ' ' . lang('bye_law');
        $this->data['controller'] = lang('miscellaneous');
        $this->layout->set_app_page_member('member/miscellaneous/view_bye_law', $this->data);
    }

    public function minutes() {
        $this->data['minutes'] = $this->common->get_all_these('minutes', ['coop_id'=>$this->coop->id]);
        $this->data['title'] = lang('minute');
        $this->data['controller'] = lang('miscellaneous');
        $this->layout->set_app_page_member('member/miscellaneous/minute', $this->data);
    }


    public function view_minute($id){
        $id = $this->utility->un_mask($id);
        $this->data['minutes'] = $this->common->get_this('minutes', ['coop_id' => $this->coop->id, 'id' => $id]);
        $this->data['title'] = lang('preview') . ' ' . lang('minute');
        $this->data['controller'] = lang('miscellaneous');
        $this->layout->set_app_page_member('member/miscellaneous/view_minute', $this->data);
    }

     public function training() {
        $this->data['training'] = $this->info->get_trained_users(['training.coop_id'=>$this->coop->id, 'users.id'=>$this->user->id]);
        $this->data['title'] = lang('training');
        $this->data['controller'] = lang('miscellaneous');
        $this->layout->set_app_page_member('member/miscellaneous/training', $this->data);
    }

}
