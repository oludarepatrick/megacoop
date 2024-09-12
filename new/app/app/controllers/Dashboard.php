<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends BASE_Controller{
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
        $this->data['savings_bal']= $this->utility->get_savings_bal(null, $this->coop->id);
        $this->data['wallet_bal']= $this->utility->get_wallet_bal(null, $this->coop->id);
        $this->data['loan_bal']= $this->utility->get_loan_bal(null, $this->coop->id);
        $this->data['credit_sales_bal']= $this->utility->get_credit_sales_bal(null, $this->coop->id);
        $this->data['loan_interest']= $this->utility->get_loan_interest($this->coop->id);
        $this->data['credit_sales_interest']= $this->utility->get_credit_sales_interest($this->coop->id);
        $this->data['reg_fee']= $this->common->sum_this('users', ['coop_id'=> $this->coop->id], 'reg_fee')->reg_fee;
        $this->data['liquidity']= $this->data['savings_bal'] + $this->data['loan_interest'] + $this->data['reg_fee'] + 
                $this->data['credit_sales_interest'] - $this->data['loan_bal'] - $this->data['credit_sales_bal'];
        $this->data['product'] = $this->info->get_product_report($this->coop->id);
         //pichart data
        $this->data['overview_data'] = json_encode([
            'savings_bal'=>(float)$this->data['savings_bal'],
            'loan_interest'=>(float)$this->data['loan_interest'],
            'loan_bal'=>(float)$this->data['loan_bal'],
            'credit_sales_interest'=>(float)$this->data['credit_sales_interest'],
            'credit_sales_bal'=>(float)$this->data['credit_sales_bal'],
            'reg_fee'=>(float)$this->data['reg_fee'],
        ]);
        
        $year = $this->input->post('year')? $this->input->post('year'):date('Y');
        
        // lingraph data
        $this->data['comparison_savings'] = json_encode($this->utility->gen_year_month_savings_bal_graph($year, $this->coop->id));
        $this->data['comparison_liquidity'] = json_encode($this->utility->gen_year_month_liquidity_graph($year, $this->coop->id));
        
        //barchart data
        $this->data['savings_data'] = json_encode($this->utility->gen_year_month_savings_graph($year, $this->coop->id));
        $this->data['withdrawal_data'] = json_encode($this->utility->gen_year_month_withdrawal_graph($year, $this->coop->id));
        $this->data['loan_data'] = json_encode($this->utility->gen_year_month_loan_graph($year, $this->coop->id));
        
        $this->data['total_member'] = $this->common->count_this('users', ['coop_id'=> $this->coop->id]);
        $this->data['male'] = $this->common->count_this('users', ['coop_id'=> $this->coop->id, 'gender'=>'male']);
        $this->data['female'] = $this->common->count_this('users', ['coop_id'=> $this->coop->id, 'gender'=>'female']);
        $this->data['year'] = $year;
        $this->data['title'] = lang('dashboard');
        $this->data['controller'] = lang('dashboard');
        $this->layout->set_app_page('dashboard/index', $this->data);
    }
    
    public function ajax_savings_bal(){
        $savings_type = $this->common->get_all_these('savings_types', ['coop_id'=> $this->coop->id]);
        foreach ($savings_type as $st){
            $savings_bal [] = (object)[
                'id' => $st->id,
                'name'=> $st->name,
                'bal' => $this->utility->get_savings_bal(null, $this->coop->id, $st->id)
            ];
        }
        $this->data['savings_bal']= $savings_bal;
        $message = $this->load->view('dashboard/savings_bal', $this->data, true);
        echo json_encode(['status' => 'success', 'message' => $message]);
    }

    public function ajax_loan_bal(){
        $loan_type = $this->common->get_all_these('loan_types', ['coop_id'=> $this->coop->id]);
        foreach ($loan_type as $st){
            $loan_bal [] = (object)[
                'id' => $st->id,
                'name'=> $st->name,
                'bal' => $this->utility->get_loan_bal(null, $this->coop->id, $st->id)
            ];
        }
        $this->data['loan_bal']= $loan_bal;
        $message = $this->load->view('dashboard/loan_bal', $this->data, true);
        echo json_encode(['status' => 'success', 'message' => $message]);
    }
   
}
