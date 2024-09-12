<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Savings extends BASE_Controller {
    
    const MENU_ID = 2;

    public function __construct() {
        parent::__construct();
        if(!$this->ion_auth->is_admin()){
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
        $this->form_validation->set_rules('month', lang("month"), 'trim');
        $this->form_validation->set_rules('year', lang("year"), 'trim');
       
        
        $month = ($this->input->post('month')? $this->input->post('month'):date('m'));
        $year = ($this->input->post('year')? $this->input->post('year'):date('Y'));
        $where = [
            'savings.coop_id' => $this->coop->id,
            'tranx_type' => 'credit',
            'savings.month_id' => $month,
            'savings.year' => $year,
        ];
        if ($this->input->post()) {
            
            $savings_type = $this->input->post('savings_type');

            if($month){
                $where['savings.month_id'] = $this->common->get_this('months', ['name' => $month])->id;
            }
            if($year){
                $where['savings.year'] = $year;
            }
            if($savings_type){
                $where['savings.savings_type'] = $savings_type;
            }
           
            $this->data['savings'] = $this->info->get_savings($where);
        } else {
            $this->data['savings'] = $this->info->get_savings(['users.coop_id' => $this->coop->id], 1000);
        }
        $this->data['filter_total_savings'] = $this->common->sum_this('savings', $where, 'amount');
        $this->data['total_savings'] = $this->common->sum_this('savings', ['tranx_type' => 'credit', 'savings.coop_id' => $this->coop->id], 'amount');
        $this->data['month'] = $month;
        $this->data['year'] = $year;

        $this->data['savings_type'] = $this->common->get_all_these('savings_types', ['coop_id' => $this->coop->id]);
        $this->data['title'] = lang('savings');
        $this->data['controller'] = lang('savings');
        $this->layout->set_app_page('savings/index', $this->data);
    }

    public function type($id) {
        $id = $this->utility->un_mask($id);
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('month', lang("month"), 'trim|required');
        $this->form_validation->set_rules('year', lang("year"), 'trim|required');
        $where = [
            'savings.coop_id' => $this->coop->id,
            'savings.month_id' => date('n'),
            'savings.year' => date('Y'),
            'tranx_type'=>'credit',
            'savings.savings_type'=>$id
        ];

        if ($this->form_validation->run()) {
            $month = $this->input->post('month');
            $where['savings.month_id'] = $this->common->get_this('months', ['name' => $month])->id;
            $where['savings.year'] = $this->input->post('year');
            $this->data['savings'] = $this->info->get_savings($where);
        } else {
            $month = date('F');
            $this->data['savings'] = $this->info->get_savings(['savings.savings_type'=>$id, 'users.coop_id' => $this->coop->id], 1000);
        }
        $this->data['filter_total_savings'] = $this->common->sum_this('savings', $where, 'amount');
        $this->data['total_savings'] = $this->common->sum_this('savings', ['savings.savings_type'=>$id,'tranx_type'=>'credit','savings.coop_id' => $this->coop->id], 'amount');
        $this->data['month'] = $month;
        $this->data['year'] = $where['savings.year'];
        $this->data['savings_type'] = $this->common->get_this('savings_types', ['coop_id' => $this->coop->id, 'id'=>$id]);
        $this->data['title'] = lang('savings');
        $this->data['controller'] = lang('savings');
        $this->layout->set_app_page('savings/type', $this->data);
    }

    public function add() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('member_id', lang('member_id'), 'trim|required');
        $this->form_validation->set_rules('savings_type', lang('savings_type'), 'trim|required');
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required');
        $this->form_validation->set_rules('source', lang('source'), 'trim|required');
        $this->form_validation->set_rules('month', lang('month'), 'trim|required');
        $this->form_validation->set_rules('year', lang('year'), 'trim|required');
        $this->form_validation->set_rules('narration', lang('narration'), 'trim|required');

        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('savings_added')
            ];
            $source = $this->input->post('source');
            $user = $this->common->get_this('users', ['username' => $this->input->post('member_id')]);
            $amount = str_replace(',', '', $this->input->post('amount'));
            
            if ($amount <= 0) {
                $this->session->set_flashdata('error', lang('invalid_amount'));
                redirect($_SERVER["HTTP_REFERER"]);
            }
            if ($source == 1) {
                $wallet_bal = $this->utility->get_wallet_bal($user->id, $this->coop->id);
                if ($amount > $wallet_bal or $amount <= 0) {
                    $this->session->set_flashdata('error', lang('low_wallet_bal'));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                $wallet = [
                    'tranx_ref' => $user->id . 'WAL' . date('Ymdhis'),
                    'referrer_code' => $this->coop->referrer_code,
                    'coop_id' => $this->coop->id,
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'tranx_type' => 'debit',
                    'gate_way_id' => 0,
                    'status' => 'successful',
                ];
            } else {
                $wallet['tranx_ref'] = $user->id . 'DEF' . date('Ymdhis');
            }

            $savings = [
                'tranx_ref' => $wallet['tranx_ref'],
                'tranx_type' => 'credit',
                'referrer_code' => $this->coop->referrer_code,
                'coop_id' => $this->coop->id,
                'user_id' => $user->id,
                'balance' => $this->utility->get_savings_bal($user->id, $this->coop->id, $this->input->post('savings_type')) + $amount,
                'amount' => $amount,
                'month_id' => $this->common->get_this('months', ['name' => $this->input->post('month')])->id,
                'year' => $this->input->post('year'),
                'savings_type' => $this->input->post('savings_type'),
                'source' => $this->input->post('source'),
                'narration' => $this->input->post('narration'),
                'status' => 'paid',
                'payment_date' => date('Y-m-d g:i:s'),
                'score' => 5
            ];

            $this->common->start_trans();
            if ($source == 1) {
                $this->common->add('wallet', $wallet);
            }
            $item_id = $this->common->add('savings', $savings);
            $this->utility->auto_post_to_general_ledger((object)$savings, $item_id, "SAV");
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $savings_type_name = $this->common->get_this('savings_types',[
                    'coop_id'=>$this->coop->id, 'id'=>$this->input->post('savings_type')
                ])->name;
                
                //email data
                $this->data['name'] = ucwords($user->first_name.' '.$user->last_name);
                $this->data['member_id'] = $user->username;
                $this->data['savings_type_name'] = $savings_type_name;
                $this->data['month'] = $this->input->post('month');
                $this->data['year'] = $this->input->post('year');
                $this->data['amount'] = number_format($amount,2);
                $this->data['status'] = 'paid';
                $this->data['balance'] = number_format($savings['balance'],2);
                $this->data['date'] = date('Y-m-d H:i:s');
                $subject = 'Savings Notice';
                $message = $this->load->view('emails/email_savings', $this->data, true);

                if ($this->coop->sms_notice == 'on'){
                    $content = "Cedit Alert" 
                    ."\n"."ST: ". $savings_type_name 
                    ."\n"."ID: ".$user->username 
                    ."\n". "DATE: ". $this->utility->just_date(date('Y-m-d H:i:s'), true)
                    ."\n". "AMT: NGN" . number_format($amount,2)
                    ."\n"."Av.BAL: NGN". number_format($savings['balance'],2);
                    $this->fivelinks->send_SMS($user->phone, $content);
                }
               
                $this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $user->email, $subject, $message);
                $this->session->set_flashdata('message', lang('savings_added'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
            redirect('savings');
        } else {
            $this->data['savings_type'] = $this->common->get_all_these('savings_types', ['coop_id' => $this->coop->id]);
            $this->data['savings_source'] = $this->common->get_all('savings_source');

            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->data['title'] = lang('add_savings');
            $this->data['controller'] = lang('savings');
            $this->layout->set_app_page('savings/add', $this->data);
        }
    }

    public function edit($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('member_id', lang('member_id'), 'trim|required');
        $this->form_validation->set_rules('savings_type', lang('savings_type'), 'trim|required');
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required');
        $this->form_validation->set_rules('source', lang('source'), 'trim|required');
        $this->form_validation->set_rules('month', lang('month'), 'trim|required');
        $this->form_validation->set_rules('year', lang('year'), 'trim|required');
        $this->form_validation->set_rules('narration', lang('narration'), 'trim|required');

        $id = $this->utility->un_mask($id);
        $this->data['savings'] = $this->info->get_savings_details(['savings.id' => $id, 'users.coop_id' => $this->coop->id]);

        if ($this->form_validation->run()) {
            $savings_type_name = $this->common->get_this('savings_types', [
                'coop_id' => $this->coop->id, 'id' => $this->input->post('savings_type')
            ])->name;
           
            $amount = str_replace(',', '', $this->input->post('amount'));
            $user = $this->common->get_this('users', ['id' => $this->data['savings']->user_id]);
            $old_balnce = $this->data['savings']->balance - $this->data['savings']->amount;
            $savings = [
                'balance' => $old_balnce + $amount,
                'amount' => $amount,
                'coop_id' => $this->coop->id,
                'user_id' => $user->id,
                'month_id' => $this->common->get_this('months', ['name' => $this->input->post('month')])->id,
                'year' => $this->input->post('year'),
                'savings_type' => $this->input->post('savings_type'),
                'source' => $this->input->post('source'),
                'narration' => $this->input->post('narration'),
                'payment_date' => date('Y-m-d g:i:s'),
            ];
            $previous_data = $this->common->get_this('savings', ['id' => $id]);
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('savings_edited'),
                'metadata' => $this->utility->activities_matadata($previous_data, $savings)
            ];

            $this->common->start_trans();
            $this->common->update_this('savings', ['coop_id' => $this->coop->id, 'id' => $id], $savings);
            $this->utility->auto_post_to_general_ledger((object)$savings, $id, "SAV");
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->data['name'] = ucwords($user->first_name.' '.$user->last_name);
                $this->data['member_id'] = $user->username;
                $this->data['savings_type_name'] = $savings_type_name;
                $this->data['month'] = $this->input->post('month');
                $this->data['year'] = $this->input->post('year');
                $this->data['amount'] = $amount;
                $this->data['status'] = 'paid';
                $this->data['balance'] = $savings['balance'];
                $this->data['date'] = date('Y-m-d g:is');
                if ($this->coop->sms_notice == 'on') {
                    $content = "Cedit Alert"
                        . "\n" . "ST: " . $savings_type_name
                        . "\n" . "ID: " . $this->utility->shortend_str_len($user->username, 5, '***')
                        . "\n" . "DATE: " . $this->utility->just_date(date('Y-m-d H:i:s'), true)
                        . "\n" . "AMT: NGN" . number_format($amount, 2)
                        . "\n" . "Av.BAL: NGN" . number_format($savings['balance'], 2);
                    $this->fivelinks->send_SMS($user->phone, $content);
                }
               
                
                $subject = 'Savings Notice';
                $message = $this->load->view('emails/email_savings', $this->data, true);
                $this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $user->email, $subject, $message);
                $this->session->set_flashdata('message', lang('savings_edited'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
            redirect('savings');
        } else {
            $this->data['savings_type'] = $this->common->get_all_these('savings_types', ['coop_id' => $this->coop->id]);
            $this->data['savings_source'] = $this->common->get_all('savings_source');

            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->data['title'] = lang('edit_savings');
            $this->data['controller'] = lang('savings');
            $this->layout->set_app_page('savings/edit_savings', $this->data);
        }
    }

    public function delete($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xdelete', 'on');
        $id = $this->utility->un_mask($id);

        
        $previous_data = $this->common->get_this('savings', ['id' => $id]);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('savings_deleted'),
            'metadata' => $this->utility->activities_matadata($previous_data, [])
        ];

        $this->common->start_trans();
        $this->common->delete_this('savings', ['coop_id' => $this->coop->id, 'id' => $id]);
        $this->common->delete_this('ledger', ['pv_no' => 'SAV'.$id]);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('savings_deleted'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function preview($id) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $id = $this->utility->un_mask($id);
        $this->data['savings'] = $this->info->get_savings_details(['savings.id' => $id, 'users.coop_id' => $this->coop->id]);
        $this->data['title'] = lang('print');
        $this->data['controller'] = lang('savings');
        $this->layout->set_app_page('savings/preview', $this->data);
    }

    public function add_batch() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->load->helper('security');
        $this->form_validation->set_rules('file', lang("upload_file"), 'xss_clean');
        $this->form_validation->set_rules('savings_type', lang('savings_type'), 'trim|required');
        $this->form_validation->set_rules('source', lang('source'), 'trim|required');
        $this->form_validation->set_rules('month', lang('month'), 'trim|required');
        $this->form_validation->set_rules('year', lang('year'), 'trim|required');
        $this->form_validation->set_rules('narration', lang('narration'), 'trim|required');

        if (isset($_FILES['file']) and $_FILES['file']['error'] == 0) {
            $field_name = 'file';
            $file_name = 'batchsavins';
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

            $savings_type = $this->input->post('savings_type');
            $year = $this->input->post('year');
            $source = $this->input->post('source');
            $narration = $this->input->post('narration');
            $date = date('Y-m-d H:i:s');
            $month = $this->common->get_this('months', ['name' => $this->input->post('month')])->id;

            $this->common->start_trans();
            for ($i = 6; $i <= $arrayCount; $i++) {
                $user = $this->common->get_this('users', ['username' => $allDataInSheet[$i]["B"]]);
                $amount = floatval(str_replace(',', '', $allDataInSheet[$i]["D"]));
                $savings = [
                    'tranx_type' => 'credit',
                    'referrer_code' => $this->coop->referrer_code,
                    'coop_id' => $this->coop->id,
                    'user_id' => $user->id,
                    'balance' => $this->utility->get_savings_bal($user->id, $this->coop->id, $savings_type) + $amount,
                    'amount' => $amount,
                    'month_id' => $month,
                    'year' => $year,
                    'savings_type' => $savings_type,
                    'source' => $source,
                    'narration' => $narration,
                    'status' => 'paid',
                    'payment_date' => $date,
                    'score' => 5
                ];

                if ($user and $amount > 0) {
                    $item_id = $this->common->add('savings', $savings);
                    $this->utility->auto_post_to_general_ledger((object)$savings, $item_id, "SAV");
                }
            }

            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('batch_savings_added')
            ];
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('batch_savings_added'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
            redirect('savings');
        } else {
            $this->data['savings_type'] = $this->common->get_all_these('savings_types', ['coop_id' => $this->coop->id]);
            $this->data['savings_source'] = $this->common->get_all('savings_source');

            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->data['title'] = lang('add_batch_saving');
            $this->data['controller'] = lang('savings');
            $this->layout->set_app_page('savings/add_batch', $this->data);
        }
    }

    public function generate_savings_template() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle(lang('savings_template'));
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', $this->coop->coop_name);
        $this->excel->getActiveSheet()->setCellValue('A2', lang('savings_template'));
        $this->excel->getActiveSheet()->setCellValue('A3', "Insert the amount member is saving in the AMOUNT column. Leave the value as 0 if member is not saving");
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
        $exceldata = $this->common->get_all_these('users', ['coop_id' => $this->coop->id]);

        //Fill data
        //$this->excel->getActiveSheet()->fromArray($exceldata, null, 'A6');
        $k = 6;
        foreach ($exceldata as $value) {
            $this->excel->getActiveSheet()->setCellValue('B' . $k, $value->username);
            $this->excel->getActiveSheet()->setCellValue('C' . $k, $value->first_name . ' ' . $value->last_name);
            $this->excel->getActiveSheet()->setCellValue('D' . $k, 0);
            $k += 1;
        }

        $this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $filename = 'savings-template' . $this->coop->id . '.xls'; //save our workbook as this file name
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
    
    public function savings_history($savings_type = null, $user_id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $savings_type = $this->utility->un_mask($savings_type);
        $user_id = $this->utility->un_mask($user_id);
        $this->form_validation->set_rules('month', lang("month"), 'trim|required');
        $this->form_validation->set_rules('year', lang("year"), 'trim|required');
        $where = [
            'savings.coop_id' => $this->coop->id,
            'savings.month_id' => date('n'),
            'savings.year' => date('Y'),
            'savings_type'=>$savings_type,
            'savings.user_id'=>$user_id,
            'tranx_type'=>'credit'
        ];

        if ($this->form_validation->run()) {
            $month = $this->input->post('month');
            $where['savings.month_id'] = $this->common->get_this('months', ['name' => $month])->id;
            $where['savings.year'] = $this->input->post('year');
            $this->data['savings'] = $this->info->get_savings($where);
        } else {
            $month = date('F');
            $this->data['savings'] = $this->info->get_savings(['users.coop_id' => $this->coop->id,'savings_type'=>$savings_type,'savings.user_id'=>$user_id], 1000);
        }
        $this->data['filter_total_savings'] = $this->common->sum_this('savings', $where, 'amount');
        $this->data['total_savings'] = $this->common->sum_this('savings', ['tranx_type'=>'credit','savings.coop_id' => $this->coop->id,'savings_type'=>$savings_type,'savings.user_id'=>$user_id,], 'amount');
        $this->data['user_id'] = $this->utility->mask($user_id);
        $this->data['savings_type'] = $this->utility->mask($savings_type);
        $this->data['month'] = $month;
        $this->data['year'] = $where['savings.year'];
        $this->data['title'] = lang('savings_history');
        $this->data['controller'] = lang('savings');
        $this->layout->set_app_page('savings/savings_history', $this->data);
    }
}
