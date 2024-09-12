<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Layout {
    public $config= array();
    public function __construct() {
        $this->ignite=& get_instance();
        $this->data['assets'] = base_url().'assets/';
        $this->data[''] = base_url().'assets/';
    }
    
    private function notification_init(){
        $this->data['error'] = $this->ignite->session->flashdata('error');
        $this->data['message'] = $this->ignite->session->flashdata('message');
        $this->data['warning'] = $this->ignite->session->flashdata('warning');
    }
    
    private function notification_end(){
        $this->ignite->session->unset_userdata(['error', 'message', 'warning']);
    }

    public function set_app_page($page, $d=array()){
        if(!file_exists(APPPATH.'views/'.$page.'.php')){
            show_404();
        }
        $template = 'template/template_app';
        $data = array_merge($d, $this->data);
        $this->notification_init();
        $this->data['view_page'] = $this->ignite->load->view($page, $data, true);
        $this->ignite->load->view($template, $this->data);
        $this->notification_end();
    }
    
    public function set_app_page_member($page, $d=array()){
        if(!file_exists(APPPATH.'views/'.$page.'.php')){
            show_404();
        }
        $template = 'member/template/template_app';
        $data = array_merge($d, $this->data);
        $this->notification_init();
        $this->data['view_page'] = $this->ignite->load->view($page, $data, true);
        $this->ignite->load->view($template, $this->data);
        $this->notification_end();
    }
    public function set_app_page_agency($page, $d=array()){
        if(!file_exists(APPPATH.'views/agency/'.$page.'.php')){
            show_404();
        }
        $template = 'agency/template/template_app';
        $data = array_merge($d, $this->data);
        $this->notification_init();
        $this->data['view_page'] = $this->ignite->load->view('agency/'.$page, $data, true);
        $this->ignite->load->view($template, $this->data);
        $this->notification_end();
    }
    
    public function set_auth_page($page, $d=array()){
        $this->data['app_settings'] = $this->ignite->common->get_this('app_settings', ['id'=>1]);
        if(!file_exists(APPPATH.'views/'.$page.'.php')){
            show_404();
        }
        $template = 'template/template_auth';
        $data = array_merge($d, $this->data);
        $this->notification_init();
        $this->data['view_page'] = $this->ignite->load->view($page, $data, true);
        $this->ignite->load->view($template, $this->data);
        $this->notification_end();
    }
    
}