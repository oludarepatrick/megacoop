<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class BASE_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('ion_auth');
        if (!$this->ion_auth->logged_in()) {
            redirect('auth', 'refresh');
        }
       
        date_default_timezone_set('Africa/Lagos');
        $this->user_id = $this->session->userdata('user_id');
        $this->is_super_admin = false;
        $this->pass_super_admin(); //override the userid
        $this->user = $this->common->get_this('users', ['id' => $this->user_id]);
        $this->coop = $this->common->get_this('cooperatives', ['id' => $this->user->coop_id]);
        $this->country = $this->common->get_this('country', ['id' => $this->coop->country_id]);
        $this->exco_notification = $this->common->get_limit('exco_notification', ['coop_id' => $this->user->coop_id], 10, 'id', 'DESC');
        $this->member_notification = $this->common->get_limit('member_notification', ['coop_id' => $this->coop->id, 'user_id'=> $this->user_id], 10, 'id', 'DESC');
        $this->coop_short_name = $this->utility->shortend_str_len($this->coop->coop_name, 15);
        $this->coop_sms_sender = $this->coop->sms_sender;
        $this->app_settings = $this->common->get_this('app_settings', ['id' => 1]);
    }

    protected function licence_cheker($coop, $app_settings) {
        if($this->app_settings->enable_monetization == 0){
            return;
        }
        if ($app_settings->monetization == 'licence' and $coop->status == 'active') {
            $licence = $this->common->get_this('licence', ['coop_id' => $coop->id, 'status' => 'successful', 'active' => 1]);
            
            // ignore licence_cheker if the cooperative members number hs not exeeded the demo member
            $total_mem = $this->common->count_this('users', ['coop_id' => $this->coop->id]);
            
            if ($total_mem <= $app_settings->demo_member and !$licence) {
                return;
            }
        
            // if the licence is expired, update the licence status and disable it
            if ($licence) {
                if ((date('Y-m-d') > $licence->end_date)) {
                    $this->common->update_this('licence', ['coop_id' => $coop->id, 'status' => 'successful', 'active' => 1], ['active' => 0]);
                }
            }

            $is_licence_active = $this->common->get_this('licence', ['coop_id' => $coop->id, 'status' => 'successful', 'active' => 1]);
            if (!$is_licence_active) {
                $this->session->set_flashdata('error', 'Your licence/subscription has expired, kindly renew it below');
                redirect('licence');
            }
        }
    }

    private function pass_super_admin(){
        $token = $this->common->get_this('super_agent_login_token',[ 
        'private_token' =>$this->session->userdata('private_token'),
        'public_token' => $this->session->userdata('public_token')
        ]
    ); 
        if($token){
            $user = $this->common->get_limit('users', ['coop_id'=> $token->coop_id], 1, 'id');
            $this->user_id = $user[0]->id;
            $this->is_super_admin = true;
        }
    }


    protected function licence_upgrade_required( $total_mem = false){
        if ($this->app_settings->enable_monetization == 0) {
            return;
        }
        
        $licence = $this->common->get_this('licence', ['coop_id' => $this->coop->id, 'status' => 'successful', 'active' => 1]);
        if($total_mem == false){
            $total_mem = $this->common->count_this('users', ['coop_id' => $this->coop->id]);
        }

        if ($total_mem >= $this->app_settings->demo_member) {
            if($total_mem >= $licence->unit){

                $this->session->set_flashdata('error', 'Cannot add more than ' .$licence->unit.' member, kindly upgrade your licence/subscription below');
                redirect('licence');
            }
        }
    }
}
