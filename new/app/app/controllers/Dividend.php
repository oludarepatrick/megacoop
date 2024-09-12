<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dividend extends BASE_Controller{

    const MENU_ID = 21;

    public function __construct(){
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
    }

    public function index(){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('savings_profit', lang("savings_profit"), 'trim|required');
        $this->form_validation->set_rules('loan_profit', lang("loan_profit"), 'trim|required');
        $this->form_validation->set_rules('credit_sales_profit', lang("credit_sales_profit"), 'trim|required');
        if ($this->form_validation->run()) {
            $savings_profit = str_replace(',','', $this->input->post('savings_profit')); 
            $loan_profit = str_replace(',', '', $this->input->post('loan_profit')); 
            $credit_sales_profit = str_replace(',', '', $this->input->post('credit_sales_profit')); 
            $this->data['dividend'] = $this->utility->get_dividend($savings_profit, $loan_profit, $credit_sales_profit);
            $this->data['title'] = lang('generate') . ' ' . lang('dividend');
            $this->data['controller'] = lang('dividend');
            $this->layout->set_app_page('dividend/output', $this->data);
        }else{
            $this->data['title'] = lang('generate') . ' ' . lang('dividend');
            $this->data['controller'] = lang('dividend');
            $this->layout->set_app_page('dividend/index', $this->data);
        }
       
    }

}
