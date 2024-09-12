<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Loanrepayment extends BASE_Controller {

    const MENU_ID = 3;

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
    }

    public function index() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('start_date', lang("start_date"), 'trim|required');
        $this->form_validation->set_rules('end_date', lang("end_date"), 'trim|required');
        $where = [
            'loan_repayment.coop_id' => $this->coop->id,
            'loan_repayment.status' => 'paid',
            'loan_repayment.created_on>=' => $this->utility->get_this_year('start'),
            'loan_repayment.created_on<=' => $this->utility->get_this_year('end'),
        ];
        $where2 = 
            "loans.coop_id = {$this->coop->id} AND 
             (loans.status = 'disbursed' OR loans.status = 'finished')
             AND loans.created_on >= '{$this->utility->get_this_year('start')}'
             AND loans.created_on <= '{$this->utility->get_this_year('end')}'
            ";
        
        $this->data['loan_type'] = 'ALL LOANS'; 
        if ($this->form_validation->run()) {
            $where['loan_repayment.created_on>='] = $this->input->post('start_date');
            $where['loan_repayment.created_on<='] = $this->input->post('end_date');
            $where2 =
            "loans.coop_id = {$this->coop->id} AND 
             (loans.status = 'disbursed' OR loans.status = 'finished')
             AND loans.created_on >= '{$this->input->post('start_date')}'
             AND loans.created_on <= '{$this->input->post('end_date')}'
            ";
            if ($this->input->post('loan_type')) {
                $where['loan_repayment.loan_type_id'] = $where2['loans.loan_type_id'] = $this->input->post('loan_type');
                $this->data['loan_type'] = $this->common->get_this('loan_types', ['id' => $this->input->post('loan_type')]);
            }
            $this->data['loan_repayment'] = $this->info->get_loan_repayment($where);
            $this->data['principal_remain'] = $this->common->sum_this('loans', $where2, 'principal_remain')->principal_remain;
            $this->data['interest_remain'] = $this->common->sum_this('loans', $where2, 'interest_remain')->interest_remain;
            $this->data['interest'] = $this->common->sum_this('loans', $where2, 'interest')->interest;
            $this->data['principal'] = $this->common->sum_this('loans', $where2, 'principal')->principal;
            $this->data['start_date'] = $this->input->post('start_date');
            $this->data['end_date'] = $this->input->post('end_date');
        } else {
            $this->data['loan_repayment'] = $this->info->get_loan_repayment(['users.coop_id' => $this->coop->id, 'loan_repayment.status' => 'paid'], 1000);
            $this->data['principal_remain'] = $this->common->sum_this('loans', $where2, 'principal_remain')->principal_remain;
            $this->data['interest_remain'] = $this->common->sum_this('loans', $where2, 'interest_remain')->interest_remain;
            $this->data['interest'] = $this->common->sum_this('loans', $where2, 'interest')->interest;
            $this->data['principal'] = $this->common->sum_this('loans', $where2, 'principal')->principal;
            $this->data['start_date'] = $this->utility->get_this_year('start');
            $this->data['end_date'] = $this->utility->get_this_year('end');
        }

        $this->data['total_interest_remain'] = $this->common->sum_this('loans', ['coop_id'=> $this->coop->id, 'status'=>'disbursed'], 'interest_remain')->interest_remain;
        $this->data['total_principal_remain'] = $this->common->sum_this('loans', ['coop_id'=> $this->coop->id, 'status'=>'disbursed'], 'principal_remain')->principal_remain;
        $this->data['total_principal_repayment'] = $this->common->sum_this('loan_repayment', ['loan_repayment.coop_id' => $this->coop->id, 'loan_repayment.status' => 'paid'], 'principal_repayment');
        $this->data['total_interest_repayment'] = $this->common->sum_this('loan_repayment', ['loan_repayment.coop_id' => $this->coop->id, 'loan_repayment.status' => 'paid'], 'interest_repayment');
        $this->data['loan_types'] = $this->common->get_all_these('loan_types', ['coop_id' => $this->coop->id]);
        $this->data['title'] = lang('loan_repayment');
        $this->data['controller'] = lang('loan_repayment');
        $this->layout->set_app_page('loanrepayment/index', $this->data);
    }

    public function add($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required');
        $this->form_validation->set_rules('source', lang('source'), 'trim|required');

        $id = $this->utility->un_mask($id);
        $this->data['loan'] = $this->info->get_loan_details(['loans.id' => $id, 'users.coop_id' => $this->coop->id]);
        $this->data['member'] = $this->info->get_user_details(['users.id' => $this->data['loan']->user_id, 'users.coop_id' => $this->coop->id]);
        if ($this->form_validation->run()) {
            $amount = str_replace(',', '', $this->input->post('amount'));
            $source = $this->input->post('source');
            $narration = $this->input->post('narration');

            if ($amount <= 0) {
                $this->session->set_flashdata('error', lang('invalid_amount'));
                redirect($_SERVER["HTTP_REFERER"]);
            }
            
            if ($amount > $this->data['loan']->total_remain) {
                $this->session->set_flashdata('error', lang('amount_geater_than_bal'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            //source == member wallet
            if ($source == 1) {
                $wallet_bal = $this->utility->get_wallet_bal($this->data['member']->id, $this->coop->id);
                if ($amount > $wallet_bal) {
                    $this->session->set_flashdata('error', lang('low_wallet_bal'));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                $wallet = [
                    'tranx_ref' => $this->data['member']->id . 'WAL' . date('Ymdhis'),
                    'referrer_code' => $this->coop->referrer_code,
                    'coop_id' => $this->coop->id,
                    'user_id' => $this->data['member']->id,
                    'amount' => $amount,
                    'tranx_type' => 'debit',
                    'gate_way_id' => 0,
                    'narration' => $narration,
                    'status' => 'successful',
                ];
            } else {
                $wallet['tranx_ref'] = $this->data['member']->id . 'DEF' . date('Ymdhis');
            }

            $splited_amount = $this->utility->split_repayment_amt($amount, $this->data['loan']);
            $loan_data = [
                'principal_remain' => $splited_amount->principal_remain,
                'interest_remain' => $splited_amount->interest_remain,
                'total_remain' => $splited_amount->total_remain,
                'next_payment_date' => $this->utility->get_end_date(date('Y-m-d H:i:s'), $plus_a_month = 1, true),
            ];

            if ($splited_amount->total_remain == 0 && $splited_amount->principal_remain == 0 && $splited_amount->interest_remain == 0) {
                $loan_data['status'] = 'finished';
            } 

            $loan_repayment_data = [
                'referrer_code' => $this->coop->referrer_code,
                'coop_id' => $this->coop->id,
                'tranx_ref' => $wallet['tranx_ref'],
                'user_id' => $this->data['member']->id,
                'loan_id' => $id,
                'loan_type_id' => $this->data['loan']->loan_type_id,
                'principal_repayment' => $splited_amount->principal_repayment,
                'interest_repayment' => $splited_amount->interest_repayment,
                'amount' => $splited_amount->amount,
                'principal_remain' => $splited_amount->principal_remain,
                'interest_remain' => $splited_amount->interest_remain,
                'amount_remain' => $splited_amount->total_remain,
                'month_id' => $this->common->get_this('months', ['name' => $this->input->post('month')])->id,
                'year' => $this->input->post('year'),
                'source' => $this->input->post('source'),
                'narration' => $narration,
                'created_by' => $this->user->id,
                'created_on' => date('Y-m-d H:i:s'),
                'status' => 'paid',
            ];
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('loan_repayment_added')
            ];

            $this->common->start_trans();
            if ($source == 1) {
                $this->common->add('wallet', $wallet);
            }

            $item_id = $this->common->add('loan_repayment', $loan_repayment_data);
            $this->common->update_this('loans', ['id' => $id], $loan_data);
            $this->utility->auto_post_to_general_ledger((object)$loan_repayment_data, $item_id, 'LOAR');
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('loan_repayment_added'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
            redirect('loanrepayment');
        } else {
            $this->data['loan_type'] = $this->common->get_all_these('loan_types', ['coop_id' => $this->coop->id]);
            $this->data['savings_source'] = $this->common->get_all('savings_source');
            $this->data['wallet_bal'] = $this->utility->get_wallet_bal($this->data['member']->id, $this->coop->id);

            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->data['title'] = lang('add_repayment');
            $this->data['controller'] = lang('loan_repayment');
            $this->layout->set_app_page('loanrepayment/add', $this->data);
        }
    }

    public function edit($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required');
        $this->form_validation->set_rules('source', lang('source'), 'trim|required');

        $id = $this->utility->un_mask($id);
        $loan_repayment = $this->info->get_loan_repayment_details(['loan_repayment.id' => $id, 'loan_repayment.coop_id' => $this->coop->id]);
        $this->data['loan'] = $this->info->get_loan_details(['loans.id' => $loan_repayment->loan_id, 'users.coop_id' => $this->coop->id]);
        $this->data['member'] = $this->info->get_user_details(['users.id' => $this->data['loan']->user_id, 'users.coop_id' => $this->coop->id]);
        if ($this->form_validation->run()) {
            if($loan_repayment->refinance_payment == 'yes'){
                $this->session->set_flashdata('error', lang('cannot_edit_refinance_patment'));
                redirect($_SERVER["HTTP_REFERER"]);
            }
            $amount = str_replace(',', '', $this->input->post('amount'));
            $source = $this->input->post('source');
            $narration = $this->input->post('narration');

            $splited_amount = $this->utility->split_repayment_amt($amount, $this->data['loan']);
            $amount_remain = ($this->data['loan']->total_remain + $loan_repayment->amount) - $amount;
            $principal_remain = ($this->data['loan']->principal_remain + $loan_repayment->principal_repayment) -  $splited_amount->principal_repayment;
            $interest_remain = ($this->data['loan']->interest_remain + $loan_repayment->interest_repayment) -  $splited_amount->interest_repayment;
            
            $loan_data = [
                'principal_remain' => $principal_remain,
                'interest_remain' =>  $interest_remain,
                'total_remain' => $amount_remain,
            ];

            if ($amount_remain == 0 && $principal_remain == 0 && $interest_remain == 0) {
                $loan_data['status'] = 'finished';
            } 

            $loan_repayment_data = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->data['member']->id,
                'loan_type_id' => $this->data['loan']->loan_type_id,
                'amount' => $amount,
                'amount_remain' => $amount_remain,
                'principal_repayment' => $splited_amount->principal_repayment,
                'interest_repayment' => $splited_amount->interest_repayment,
                'principal_remain' => $principal_remain,
                'interest_remain' => $interest_remain,
                'amount_remain' => $amount_remain,
                'month_id' => $this->common->get_this('months', ['name' => $this->input->post('month')])->id,
                'year' => $this->input->post('year'),
                'source' => $source,
                'narration' => $narration,
                'created_by' => $this->user->id,
                'created_on' => date('Y-m-d H:i:s'),
                'status' => 'paid',
            ];
            $loan_data['total_remain'] = $amount_remain;

            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('edit_loan_repayment')
            ];

            $this->common->start_trans();
            $this->common->update_this('loan_repayment', ['id' => $id], $loan_repayment_data);
            $this->common->update_this('loans', ['id' => $loan_repayment->loan_id], $loan_data);
            $this->utility->auto_post_to_general_ledger((object)$loan_repayment_data, $id, 'LOAR');
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('loan_repayment_edited'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
            redirect('loan/disbursed_loans');
        } else {
            $this->data['loan_type'] = $this->common->get_all_these('loan_types', ['coop_id' => $this->coop->id]);
            $this->data['savings_source'] = $this->common->get_all('savings_source');
            $this->data['wallet_bal'] = $this->utility->get_wallet_bal($this->data['member']->id, $this->coop->id);
            $this->data['loan_repayment'] = $loan_repayment;

            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->data['title'] = lang('edit_repayment');
            $this->data['controller'] = lang('loan_repayment');
            $this->layout->set_app_page('loanrepayment/edit', $this->data);
        }
    }

    public function delete($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xdelete', 'on');
        $id = $this->utility->un_mask($id);

        $loan_repayment = $this->info->get_loan_repayment_details(['loan_repayment.id' => $id, 'loan_repayment.coop_id' => $this->coop->id]);
        $loan = $this->info->get_loan_details(['loans.id' => $loan_repayment->loan_id, 'users.coop_id' => $this->coop->id]);

        $amount_remain = ($loan->total_remain + $loan_repayment->amount);
        $principal_remain = ($loan->principal_remain + $loan_repayment->principal_repayment);
        $interest_remain = ($loan->interest_remain + $loan_repayment->interest_repayment);
        
        //if the loan has been refinanced
        if ($loan_repayment->refinance_payment =='yes') {
            $loan_data = [
                'status' => 'disbursed',
            ];
        }else{
            $loan_data = [
                'principal_remain' => $principal_remain,
                'interest_remain' =>  $interest_remain,
                'total_remain' => $amount_remain,
                'status' => 'disbursed',
            ];
        }
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('loan_repayment_deleted')
        ];
        $this->common->start_trans();
        $this->common->update_this('loans', ['id' => $loan_repayment->loan_id], $loan_data);
        $this->common->delete_this('loan_repayment', ['id' => $id]);
        $this->common->delete_this('ledger', ['pv_no' => 'LOAR' . $id, 'coop_id'=>$this->coop->id]);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('loan_repayment_deleted'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function preview($id) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $id = $this->utility->un_mask($id);
        $this->data['loan'] = $this->info->get_loan_repayment_details(['loan_repayment.id' => $id, 'users.coop_id' => $this->coop->id]);
        $this->data['title'] = lang('print');
        $this->data['controller'] = lang('loan');
        $this->layout->set_app_page('loanrepayment/preview', $this->data);
    }

    public function add_batch() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->load->helper('security');
        $this->form_validation->set_rules('file', lang("upload_file"), 'xss_clean');
        $this->form_validation->set_rules('loan_type', lang('loan_type'), 'trim|required');
        $this->form_validation->set_rules('source', lang('source'), 'trim|required');
        $this->form_validation->set_rules('month', lang('month'), 'trim|required');
        $this->form_validation->set_rules('year', lang('year'), 'trim|required');
        $this->form_validation->set_rules('narration', lang('narration'), 'trim|required');

        if (isset($_FILES['file']) and $_FILES['file']['error'] == 0) {
            $field_name = 'file';
            $file_name = 'batchloan';
            $is_uploaded = $this->utility->file_upload($field_name, $file_name);
            if (isset($is_uploaded['error'])) {
                $this->session->set_flashdata('error', $is_uploaded['error']);
                redirect($_SERVER["HTTP_REFERER"]);
            }
            $filename = $is_uploaded['upload_data']['file_name'];
        }

        if ($this->form_validation->run()) {
            $file_path = 'assets/files/uploads/' . $filename;
            $objPHPExcel = PHPExcel_IOFactory::load($file_path);
            $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
            $arrayCount = count($allDataInSheet);  // Here get total count of row in that Excel sheet

            $loan_type = $this->input->post('loan_type');
            $year = $this->input->post('year');
            $source = $this->input->post('source');
            $narration = $this->input->post('narration');
            $date = date('Y-m-d H:i:s');
            $month = $this->common->get_this('months', ['name' => $this->input->post('month')])->id;

            $this->common->start_trans();
            for ($i = 6; $i <= $arrayCount; $i++) {
                $user = $this->common->get_this('users', ['username' => $allDataInSheet[$i]["B"]]);
                $loan = $this->common->get_this('loans', ['user_id' => $user->id, 'loan_type_id' => $loan_type, 'status'=>'disbursed']);
                $amount = floatval(str_replace(',', '', $allDataInSheet[$i]["D"]));

                $splited_amount = $this->utility->split_repayment_amt($amount, $loan);

                $loan_data = [
                    'principal_remain' => $splited_amount->principal_remain,
                    'interest_remain' => $splited_amount->interest_remain,
                    'total_remain' => $splited_amount->total_remain,
                    'next_payment_date' => $this->utility->get_end_date(date('Y-m-d H:i:s'), $plus_a_month = 1, true),
                ];
                
                if ($splited_amount->total_remain == 0 && $splited_amount->principal_remain == 0 && $splited_amount->interest_remain == 0) {
                    $loan_data['status'] = 'finished';
                }

                $loan_repayment_data = [
                    'referrer_code' => $this->coop->referrer_code,
                    'coop_id' => $this->coop->id,
                    'tranx_ref' => $user->id . 'DEF' . date('Ymdhis'),
                    'user_id' => $user->id,
                    'loan_id' => $loan->id,
                    'loan_type_id' => $loan_type,
                    'principal_repayment' => $splited_amount->principal_repayment,
                    'interest_repayment' => $splited_amount->interest_repayment,
                    'amount' => $splited_amount->amount,
                    'principal_remain' => $splited_amount->principal_remain,
                    'interest_remain' => $splited_amount->interest_remain,
                    'amount_remain' => $splited_amount->total_remain,
                    'month_id' => $month,
                    'year' => $year,
                    'source' => $source,
                    'narration' => $narration,
                    'created_by' => $this->user->id,
                    'created_on' => $date,
                    'status' => 'paid',
                ];

                // var_dump($loan_data, $loan, $loan_repayment_data);
               
                if ($user and $amount > 0) {
                    $item_id = $this->common->add('loan_repayment', $loan_repayment_data);
                    $this->common->update_this('loans', ['id' => $loan->id], $loan_data);
                    $this->utility->auto_post_to_general_ledger((object)$loan_repayment_data, $item_id, 'LOAR');
                }
            }
            // exit;
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('batch_repayment_added')
            ];
            
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('batch_repayment_added'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
            redirect('loanrepayment');
        } else {
            $this->data['loan_types'] = $this->common->get_all_these('loan_types', ['coop_id' => $this->coop->id]);
            $this->data['savings_source'] = $this->common->get_all('savings_source');

            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->data['title'] = lang('add_batch_repayment');
            $this->data['controller'] = lang('loan_repayment');
            $this->layout->set_app_page('loanrepayment/add_batch', $this->data);
        }
    }

    function generate_loan_template() {
        $loan_type_id = $this->input->get('loan_type', true);
        $loan_type = $this->common->get_this('loan_types', ['id' => $loan_type_id, 'coop_id' => $this->coop->id]);
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle(lang('loan_template'));
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', $this->coop->coop_name);
        $this->excel->getActiveSheet()->setCellValue('A2', $loan_type->name . ' Repayment Template');
        $this->excel->getActiveSheet()->setCellValue('A3', "Insert the amount member is paying in the AMOUNT column. Leave the value as 0 if member is not paying");
        $this->excel->getActiveSheet()->setCellValue('B5', lang('member_id'));
        $this->excel->getActiveSheet()->setCellValue('C5', lang('full_name'));
        $this->excel->getActiveSheet()->setCellValue('D5', lang('amount'));
        //merge cell A1 until I1
        $this->excel->getActiveSheet()->mergeCells('A1:I1');
        //merge cell A2 until I2
        $this->excel->getActiveSheet()->mergeCells('A2:I2');
        //merge cell A2 until I3
        $this->excel->getActiveSheet()->mergeCells('A3:I3');
        //set aligment to center for that merged cell (A1 to I1)
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //set aligment to center for that merged cell (A2 to I2)
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //set aligment to center for that merged cell (A2 to I2)
        $this->excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //make the font become bold
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
        $this->excel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('#080');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(14);
        $this->excel->getActiveSheet()->getStyle('A2')->getFill()->getStartColor()->setARGB('#f00');
        $this->excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(false);
        $this->excel->getActiveSheet()->getStyle('A3')->getFont()->setSize(14);
        $this->excel->getActiveSheet()->getStyle('A3')->getFill()->getStartColor()->setARGB('#c00');

        for ($col = ord('B'); $col <= ord('D'); $col++) {
            $this->excel->getActiveSheet()->getStyle(chr($col) . '5')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle(chr($col) . '5')->getFont()->setSize(14);
            $this->excel->getActiveSheet()->getStyle(chr($col) . '5')->getFill()->getStartColor()->setARGB('#333');
        }
        for ($col = ord('B'); $col <= ord('D'); $col++) { //set column dimension $this->excel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(true);
            //change the font size
            $this->excel->getActiveSheet()->getStyle(chr($col))->getFont()->setSize(14);
            $this->excel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(true);
            $this->excel->getActiveSheet()->getStyle(chr($col))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        //retrive schedule table data
        //$exceldata="";
        $exceldata = $this->info->get_loans(['loans.loan_type_id' => $loan_type_id, 'loans.status' => 'disbursed', 'loans.coop_id' => $this->coop->id]);

        //Fill data
        //$this->excel->getActiveSheet()->fromArray($exceldata, null, 'A6');
        $k = 6;
        foreach ($exceldata as $value) {
            if ($value->monthly_due > $value->total_remain) {
                $value->monthly_due = $value->total_remain;
            }
            $this->excel->getActiveSheet()->setCellValue('B' . $k, $value->username);
            $this->excel->getActiveSheet()->setCellValue('C' . $k, $value->full_name);
            $this->excel->getActiveSheet()->setCellValue('D' . $k, $value->monthly_due);
            $k += 1;
        }

        $this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $filename = 'loan-template' . $this->coop->id . '.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('assets/files/' . $filename); //php://output
        echo json_encode(["status" => 'success', "message" => "assets/files/" . $filename]);
    }

    public function repayment_history($id) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $id = $this->utility->un_mask($id);
        $loan = $this->common->get_this('loans', ['id' => $id, 'coop_id' => $this->coop->id]);
        $this->data['user'] = $this->info->get_user_details(['users.id' => $loan->user_id, 'users.coop_id' => $this->coop->id]);
        $where = [
            'loan_repayment.user_id' => $loan->user_id,
            'loan_repayment.coop_id' => $this->coop->id,
            'loan_repayment.status' => 'paid',
            'loan_repayment.loan_type_id' => $loan->loan_type_id,
        ];

        $this->data['loan_repayment'] = $this->info->get_loan_repayment($where);
        $this->data['loan'] = $loan;
        $this->data['title'] = lang('repayment_history');
        $this->data['controller'] = lang('loan_repayment');
        $this->layout->set_app_page('loanrepayment/repayment_history', $this->data);
    }

    public function due_payment(){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('start_date', lang("start_date"), 'trim|required');
        $this->form_validation->set_rules('end_date', lang("end_date"), 'trim|required');
        $where = [
            'loans.coop_id' => $this->coop->id,
            'loans.status' => 'disbursed',
            'loans.next_payment_date>=' => $this->utility->get_this_year('start'),
            'loans.next_payment_date<=' => $this->utility->get_this_year('end'),
        ];
        if ($this->form_validation->run()) {
            $where['loans.next_payment_date>='] = $this->input->post('start_date');
            $where['loans.next_payment_date<='] = $this->input->post('end_date');
            $where['loans.loan_type_id'] = $this->input->post('loan_type');
            $this->data['loans'] = $this->info->get_loans($where);
            $this->data['total_loans'] = $this->common->sum_this('loans', $where, 'principal');
            $this->data['total_remain'] = $this->common->sum_this('loans', $where, 'total_remain')->total_remain;
            $this->data['monthly_due'] = $this->common->sum_this('loans', $where, 'monthly_due')->monthly_due;
            $this->data['start_date'] = $this->input->post('start_date');
            $this->data['end_date'] = $this->input->post('end_date');
        } else {
            $this->data['loans'] = $this->info->get_loans(['users.coop_id' => $this->coop->id, 'loans.status' => 'disbursed'], 1000);
            $this->data['total_loans'] = $this->common->sum_this('loans', $where, 'principal');
            $this->data['total_remain'] = $this->common->sum_this('loans', $where, 'total_remain')->total_remain;
            $this->data['monthly_due'] = $this->common->sum_this('loans', $where, 'monthly_due')->monthly_due;
            $this->data['start_date'] = $this->utility->get_this_year('start');
            $this->data['end_date'] = $this->utility->get_this_year('end');
        }
       
        $this->data['loan_type'] = $this->common->get_all_these('loan_types', ['coop_id' => $this->coop->id]);
        $this->data['title'] = lang('due_payment');
        $this->data['controller'] = lang('loan_repayment');
        $this->layout->set_app_page('loanrepayment/due_payment', $this->data);
    }

    public function refinance($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required');
        $this->form_validation->set_rules('source', lang('source'), 'trim|required');
        $this->form_validation->set_rules('rate', lang('rate'), 'trim|required');
        $this->form_validation->set_rules('tenure', lang('tenure'), 'trim|required');
        $this->form_validation->set_rules('acc_year', lang('acc_year'), 'trim|required');
        $this->form_validation->set_rules('narration', lang('narration'), 'trim|required');

        $id = $this->utility->un_mask($id);
        $this->data['loan'] = $this->info->get_loan_details(['loans.id' => $id, 'users.coop_id' => $this->coop->id]);
        $this->data['member'] = $this->info->get_user_details(['users.id' => $this->data['loan']->user_id, 'users.coop_id' => $this->coop->id]);
        if ($this->form_validation->run()) {
            $amount = str_replace(',', '', $this->input->post('amount'));
            $source = $this->input->post('source');
            $narration = $this->input->post('narration');

            if ($amount <= 0) {
                $this->session->set_flashdata('error', lang('invalid_amount'));
                redirect($_SERVER["HTTP_REFERER"]);
            }
            
            if ($amount > $this->data['loan']->total_remain) {
                $this->session->set_flashdata('error', lang('amount_geater_than_bal'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            //source == member wallet
            if ($source == 1) {
                $wallet_bal = $this->utility->get_wallet_bal($this->data['member']->id, $this->coop->id);
                if ($amount > $wallet_bal) {
                    $this->session->set_flashdata('error', lang('low_wallet_bal'));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                $wallet = [
                    'tranx_ref' => $this->data['member']->id . 'WAL' . date('Ymdhis'),
                    'referrer_code' => $this->coop->referrer_code,
                    'coop_id' => $this->coop->id,
                    'user_id' => $this->data['member']->id,
                    'amount' => $amount,
                    'tranx_type' => 'debit',
                    'gate_way_id' => 0,
                    'narration' => $narration,
                    'status' => 'successful',
                ];
            } else {
                $wallet['tranx_ref'] = $this->data['member']->id . 'DEF' . date('Ymdhis');
            }

            $splited_amount = $this->utility->split_repayment_amt($amount, $this->data['loan']);
            $loan_data = [
                'status'=> 'finished',
                'refinance_data'=>$this->input->post('refinance_data')
            ];

            $loan_repayment_data = [
                'referrer_code' => $this->coop->referrer_code,
                'coop_id' => $this->coop->id,
                'tranx_ref' => $wallet['tranx_ref'],
                'user_id' => $this->data['member']->id,
                'loan_id' => $id,
                'loan_type_id' => $this->data['loan']->loan_type_id,
                'principal_repayment' => $splited_amount->principal_repayment,
                'interest_repayment' => $splited_amount->interest_repayment,
                'amount' => $splited_amount->amount,
                'principal_remain' => 0,
                'interest_remain' => 0,
                'amount_remain' => 0,
                'refinance_payment' => 'yes',
                'month_id' => $this->common->get_this('months', ['name' => date("F")])->id,
                'year' => date('Y'),
                'source' => $this->input->post('source'),
                'narration' => $narration,
                'created_by' => $this->user->id,
                'created_on' => date('Y-m-d H:i:s'),
                'status' => 'paid',
            ];
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('loan').' '. lang('refinanced')
            ];

            $this->common->start_trans();
            if ($source == 1) {
                $this->common->add('wallet', $wallet);
            }

            $item_id = $this->common->add('loan_repayment', $loan_repayment_data);
            $this->common->update_this('loans', ['id' => $id], $loan_data);
            $this->utility->auto_post_to_general_ledger((object)$loan_repayment_data, $item_id, 'LOAR');
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('loan'). ' '. lang('refinanced'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
            redirect('loanrepayment');
        } else {
            $this->data['loan_type'] = $this->common->get_all_these('loan_types', ['coop_id' => $this->coop->id]);
            $this->data['savings_source'] = $this->common->get_all('savings_source');
            $this->data['wallet_bal'] = $this->utility->get_wallet_bal($this->data['member']->id, $this->coop->id);

            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->data['title'] = lang('refinance');
            $this->data['controller'] = lang('loan_repayment');
            $this->layout->set_app_page('loanrepayment/refinance', $this->data);
        }
    }

}
