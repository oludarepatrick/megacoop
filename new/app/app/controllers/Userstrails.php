<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Userstrails extends BASE_Controller {
    const MENU_ID = 9;

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

    public function index(){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->data['users'] = $this->info->get_users_login(['users.coop_id' => $this->coop->id, 'last_login!='=>null], 1000);
        $this->data['title'] = lang('user_login');
        $this->data['controller'] = lang('user_trails');
        $this->layout->set_app_page('audit/index', $this->data);
    }
    
    public function activities(){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->data['users'] = $this->info->get_users_activities(['users.coop_id' => $this->coop->id], 1000);
        $this->data['title'] = lang('user_activities');
        $this->data['controller'] = lang('user_trails');
        $this->layout->set_app_page('audit/activities', $this->data);
    }

    public function details($id){
        $id = $this->utility->un_mask($id);
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $activity = $this->info->get_users_activities(['users.id' => $id], 1000);
        foreach ($activity as $a){
            if($a->metadata){
                $a->previous_data = json_encode(json_decode($a->metadata)->previous_data); 
                $a->new_data = json_encode(json_decode($a->metadata)->new_data); 
            }else{
                $a->new_data = "Not Available";
                $a->previous_data = "Not Available";
            }
        }
        $this->data['users'] = $activity;
        $this->data['title'] = lang('user_activities');
        $this->data['controller'] = lang('user_trails');
        $this->layout->set_app_page('audit/details', $this->data);
    }
    
}
