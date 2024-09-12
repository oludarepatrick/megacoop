<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration extends BASE_Controller {

    const MENU_ID = 16;
    const CHAR_FILTER = [',', '$', '#', '&', '@'];
    private $_loan_error = [];
    private $_savings_error = [];
    private $_credit_sales_error = [];
    private $_member_error = [];
    


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

    private function migration_config($description,$table_name){
        return (object)[
            'migrated_date'=>date('Y-m-d H:i:s'),
            'coop_id'=>$this->coop->id,
            'migrated_status'=>1,
            'description'=> $description,
            'table_name'=> $table_name,
        ];
    }

    private function set_loan_error($details, $error){
        $this->_loan_error [] = (object)[
            'member_id'=> $details->member_id,
            'full_name'=> $details->full_name,
            'principal'=> $details->principal,
            'monthly_repayment'=> $details->monthly_repayment,
            'total_repayment'=> $details->total_repayment,
            'balance'=> $details->loan_balance,
            'disbursed_on'=> $details->disbursed_on,
            'tenure'=> $details->tenure,
            'loan_type'=> $details->loan_type,
            'error'=> $error,
        ];
    }

    private function set_member_error($details, $error){
        $this->_member_error [] = (object)[
            'mem_since' => $details->member_since,
            'surname' => $details->surname,
            'othernames' => $details->othernames,
            'email' => $details->email,
            'phone' => $details->phone,
            'reg_fee' => $details->reg_fee,
            "message" => $error
        ];
    }

    public function index() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->data['savings_source'] = $this->common->get_all('savings_source');
        $this->data['savings_type'] = $this->common->get_all_these('savings_types', ['coop_id' => $this->coop->id]);
        $this->data['loan_type'] = $this->common->get_all_these('loan_types', ['coop_id' => $this->coop->id]);
        $this->data['product_type'] = $this->common->get_all_these('product_types', ['coop_id' => $this->coop->id]);
        $this->data['error_message_url'] = $this->session->userdata('url');
        $this->data['failed_upload'] = $this->session->userdata('failed_upload');
        $this->data['record_num'] = $this->session->userdata('record_num');
        $this->data['title'] = lang('migration');
        $this->data['controller'] = lang('migration');
        $this->layout->set_app_page('migration/index', $this->data);
    }

    public function message($type=null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');

        $this->data['failed_upload'] = $this->session->userdata('failed_upload');
        $this->data['record_num'] = $this->session->userdata('record_num');

        $this->data['title'] = lang('migration');
        $this->data['controller'] = lang('migration');

        //for both loan and credit sales
       if($type == 'loan'){
            $this->layout->set_app_page('migration/loan_message', $this->data);
       }else{
            $this->layout->set_app_page('migration/message', $this->data);
       }
    }

    public function member_record() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->load->helper('security');
        $this->form_validation->set_rules('file', lang("upload_file"), 'xss_clean');
        $this->form_validation->set_rules('default_pass', lang("default_pass"), 'trim|required');

        $field_name = 'file';
        $file_name = 'batchsavings';
        $is_uploaded = $this->utility->file_upload($field_name, $file_name);
        if (isset($is_uploaded['error'])) {
            $this->session->set_flashdata('error', $is_uploaded['error']);
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $filename = $is_uploaded['upload_data']['file_name'];

        if ($this->form_validation->run()) {
            $file_path = 'assets/files/uploads/' . $filename;
            $objPHPExcel = PHPExcel_IOFactory::load($file_path);
            $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
            $arrayCount = count($allDataInSheet);  // Here get total count of row in that Excel sheet
            $total_members = $arrayCount - 1;

            $this->licence_upgrade_required($total_members);
            
            $member_role = $this->common->get_this('role', ['coop_id' => $this->coop->id, 'group_id' => 2]);
            $tranx_pin = hash('sha256', '123456');
            $record_num = 0;
            $migration_config = $this->migration_config("Member Record", 'users');

            for ($i = 2; $i <= $arrayCount; $i++) {
                if (!$allDataInSheet[$i]["B"]) {
                    continue;
                }

                $date_obj = date_create(trim($allDataInSheet[$i]["A"]));
                if ($date_obj == false) {
                    $date_obj = date_create("");
                }
                
                $date = date_format($date_obj, 'Y-m-d H:i:s');
                $surname = trim($allDataInSheet[$i]["B"]);
                $othernames = trim($allDataInSheet[$i]["C"]);
                $email = trim($allDataInSheet[$i]["D"]);
                $phone = str_replace('|', '',trim($allDataInSheet[$i]["E"]));
                $reg_fee = trim($allDataInSheet[$i]["F"]);

                $password = $this->input->post('default_pass');
                
                $member_id = $this->utility->generate_member_id($this->coop->coop_name, $this->coop->id, $record_num);
                $month_id = date_format($date_obj, 'n');
                $year = date_format($date_obj, 'Y');
                $email = $email ? $email : $member_id;
                $details = (object)[
                    'member_since' => trim($allDataInSheet[$i]["A"]),
                    'surname' => $surname,
                    'othernames' => $othernames,
                    'email' => $email,
                    'phone' => $phone,
                    'reg_fee' => $reg_fee,
                    "message" => "duplicate email"
                ];
                //skip duplicate member
                if ($this->common->get_this('users', ['coop_id' => $this->coop->id,'email' => $email])) {
                    $this->set_member_error($details, "Duplicate Email");
                    continue;
                }

                if ($this->common->get_this('users', ['coop_id' => $this->coop->id,'phone' => $phone])) {
                    $this->set_member_error($details, "Duplicate Phone number");
                    continue;
                }
                
                if ($surname and $othernames and $password) {                   
                    $additional_data = [
                        'coop_id' => $this->coop->id,
                        'member_id' => $member_id,
                        'first_name' => $surname,
                        'last_name' => $othernames,
                        'phone' => $phone? $phone: $member_id,
                        'reg_fee' => $reg_fee,
                        'month_id' => $month_id,
                        'year' => $year,
                        'reg_date' => $date,
                        'tranx_pin' => $tranx_pin,
                        'role_id' => $member_role->id,
                        'status' => 'approved',
                        'migrated_status' => $migration_config->migrated_status,
                        'migrated_date' => $migration_config->migrated_date,       
                    ];

                    $groups = [2];
                    $this->ion_auth->register($member_id, $password, $email, $additional_data, $groups);
                    $record_num++;
                }
            }
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('member_record_migrated')
            ];

            //at least one record uploaded
            if($record_num > 0){
                $migration_config->total_record = $record_num;
                $this->common->add('migration_logs', $migration_config);
                $this->common->add('activities', $activities);
            }

            $this->session->set_userdata('record_num', $record_num);
            $this->session->set_userdata('failed_upload', $this->_member_error);
            $this->session->set_userdata('url', 'message');
            if (!$this->_member_error) {
                $this->session->set_flashdata('message', lang('member_record_migrated'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
                
            }

            redirect('migration/message');
        }else{
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    public function generate_savings_template() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle(lang('savings_balance_template'));
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', lang('savings_balance_template'));
        $this->excel->getActiveSheet()->setCellValue('A2', lang('savings_record'));
        $this->excel->getActiveSheet()->setCellValue('B5', lang('member_id'));
        $this->excel->getActiveSheet()->setCellValue('C5', lang('full_name'));
        $this->excel->getActiveSheet()->setCellValue('D5', lang('savings_bal'));
        //merge cell A1 until I1
        $this->excel->getActiveSheet()->mergeCells('A1:I1');
        //merge cell A2 until I2
        $this->excel->getActiveSheet()->mergeCells('A2:I2');
        //set aligment to center for that merged cell (A1 to I1)
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //set aligment to center for that merged cell (A2 to I2)
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //make the font become bold
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
        $this->excel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('#080');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(14);
        $this->excel->getActiveSheet()->getStyle('A2')->getFill()->getStartColor()->setARGB('#f00');

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

    public function savings_record() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->load->helper('security');
        $this->form_validation->set_rules('file', lang("upload_file"), 'xss_clean');
        $this->form_validation->set_rules('savings_type', lang('savings_type'), 'trim|required');
        $this->form_validation->set_rules('source', lang('source'), 'trim|required');
        $this->form_validation->set_rules('month', lang('month'), 'trim|required');
        $this->form_validation->set_rules('year', lang('year'), 'trim|required');

        $field_name = 'file';
        $file_name = 'batchsavins';
        $is_uploaded = $this->utility->file_upload($field_name, $file_name);
        if (isset($is_uploaded['error'])) {
            $this->session->set_flashdata('error', $is_uploaded['error']);
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $filename = $is_uploaded['upload_data']['file_name'];

        if ($this->form_validation->run()) {
            $file_path = 'assets/files/uploads/' . $filename;
            $objPHPExcel = PHPExcel_IOFactory::load($file_path);
            $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
            $arrayCount = count($allDataInSheet);  // Here get total count of row in that Excel sheet

            $savings_type = $this->input->post('savings_type');
            $year = $this->input->post('year');
            $source = $this->input->post('source');
            $date = date('Y-m-d H:i:s');
            $month = $this->common->get_this('months', ['name' => $this->input->post('month')])->id;
            $migration_config = $this->migration_config("Savings Record", 'savings');
            $record_num = 0;
            $this->common->start_trans();
            for ($i = 6; $i <= $arrayCount; $i++) {
                $user = $this->common->get_this('users', ['username' => $allDataInSheet[$i]["B"]]);
                $amount = floatval(str_replace(self::CHAR_FILTER, '', $allDataInSheet[$i]["D"]));
                
                if ($user and $amount > 0) {
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
                        'narration' => lang('migrated_balance'),
                        'status' => 'paid',
                        'payment_date' => $date,
                        'score' => 5,
                        'migrated_status' => $migration_config->migrated_status,
                        'migrated_date' => $migration_config->migrated_date,  
                    ];

                    $this->common->add('savings', $savings);
                    $record_num++;
                }
            }

            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('member_balance_migrated')
            ];

            //at least one record uploaded
            $migration_config->total_record = $record_num;
            $this->common->add('migration_logs', $migration_config);
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
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    public function generate_loan_template() {
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle(lang('loan_balance_template'));
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', lang('loan_balance_template'));
        $this->excel->getActiveSheet()->setCellValue('A2', lang('loan_record_instruction'));
        $this->excel->getActiveSheet()->setCellValue('A3', lang('loan_record_instruction_2'));
        $this->excel->getActiveSheet()->setCellValue('A4', lang('loan_record_instruction_3'));
        $this->excel->getActiveSheet()->setCellValue('A5', lang('dont_modify'));
        $this->excel->getActiveSheet()->setCellValue('B7', lang('member_id'));
        $this->excel->getActiveSheet()->setCellValue('C7', lang('full_name'));
        $this->excel->getActiveSheet()->setCellValue('D7', lang('principal'));
        $this->excel->getActiveSheet()->setCellValue('E7', lang('monthly_repayment'));
        $this->excel->getActiveSheet()->setCellValue('F7', lang('total_repayment'));
        $this->excel->getActiveSheet()->setCellValue('G7', lang('loan_balance'));
        $this->excel->getActiveSheet()->setCellValue('H7', lang('disbursed_on'));
        $this->excel->getActiveSheet()->setCellValue('I7', lang('tenure'));
        $this->excel->getActiveSheet()->setCellValue('J7', lang('loan_type'));
        //merge cell A1 until I1
        $this->excel->getActiveSheet()->mergeCells('A1:I1');
        //merge cell A2 until I2
        $this->excel->getActiveSheet()->mergeCells('A2:I2');
        //merge cell A2 until I2
        $this->excel->getActiveSheet()->mergeCells('A3:I3');
        //merge cell A2 until I2
        $this->excel->getActiveSheet()->mergeCells('A4:I4');
        //merge cell A2 until I2
        $this->excel->getActiveSheet()->mergeCells('A5:I5');
        //set aligment to center for that merged cell (A1 to I1)
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //set aligment to center for that merged cell (A2 to I2)
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //set aligment to center for that merged cell (A2 to I2)
        $this->excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //set aligment to center for that merged cell (A2 to I2)
        $this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //make the font become bold
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
        $this->excel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('#080');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(false);
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(14);
        $this->excel->getActiveSheet()->getStyle('A2')->getFill()->getStartColor()->setARGB('#f00');
        $this->excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(false);
        $this->excel->getActiveSheet()->getStyle('A3')->getFont()->setSize(14);
        $this->excel->getActiveSheet()->getStyle('A3')->getFill()->getStartColor()->setARGB('#f00');
        $this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(false);
        $this->excel->getActiveSheet()->getStyle('A4')->getFont()->setSize(14);
        $this->excel->getActiveSheet()->getStyle('A4')->getFill()->getStartColor()->setARGB('#ff0000');
        $this->excel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A5')->getFont()->setSize(14);
        $this->excel->getActiveSheet()->getStyle('A5')->getFill()->getStartColor()->setARGB('#00f');

        for ($col = ord('B'); $col <= ord('J'); $col++) {
            $this->excel->getActiveSheet()->getStyle(chr($col) . '7')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle(chr($col) . '7')->getFont()->setSize(14);
            $this->excel->getActiveSheet()->getStyle(chr($col) . '7')->getFill()->getStartColor()->setARGB('#333');
        }
        for ($col = ord('B'); $col <= ord('J'); $col++) { //set column dimension $this->excel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(true);
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
        $k = 9;
        foreach ($exceldata as $value) {
            $this->excel->getActiveSheet()->setCellValue('B' . $k, $value->username);
            $this->excel->getActiveSheet()->setCellValue('C' . $k, $value->first_name . ' ' . $value->last_name);
            $this->excel->getActiveSheet()->setCellValue('D' . $k, 0);
            $this->excel->getActiveSheet()->setCellValue('E' . $k, 0);
            $this->excel->getActiveSheet()->setCellValue('F' . $k, 0);
            $this->excel->getActiveSheet()->setCellValue('G' . $k, 0);
            $this->excel->getActiveSheet()->setCellValue('H' . $k, null);
            $this->excel->getActiveSheet()->setCellValue('I' . $k, 0);
            $this->excel->getActiveSheet()->setCellValue('J' . $k, null);
            $k += 1;
        }

        $this->excel->getActiveSheet()->getStyle('B8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('C8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('D8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('E8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('F8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('G8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('H8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('I8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('J8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
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

    public function loan_record() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->load->helper('security');
        $this->form_validation->set_rules('file', lang("upload_file"), 'xss_clean');
        $this->form_validation->set_rules('loan_type', lang('loan_type'), 'trim|required');

        $field_name = 'file';
        $file_name = 'batchloans';
        $is_uploaded = $this->utility->file_upload($field_name, $file_name);

        if (isset($is_uploaded['error'])) {
            $this->session->set_flashdata('error', $is_uploaded['error']);
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $filename = $is_uploaded['upload_data']['file_name'];

        if ($this->form_validation->run()) {
            $file_path = 'assets/files/uploads/' . $filename;
            $objPHPExcel = PHPExcel_IOFactory::load($file_path);
            $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
            $arrayCount = count($allDataInSheet);  // Here get total count of row in that Excel sheet
            $loan_type_id = $this->input->post('loan_type');
            $loan_type = $this->common->get_this('loan_types', ['id' => $loan_type_id]);

            $migration_config = $this->migration_config("Loan Record", 'loans');
            $record_num = 0;
            for ($i = 9; $i <= $arrayCount ; $i++) {
                if(!$allDataInSheet[$i]["B"]){
                    continue;
                }
 
                $user = $this->common->get_this('users', ['username' => $allDataInSheet[$i]["B"]]);
                $amount = floatval(str_replace(self::CHAR_FILTER, '', $allDataInSheet[$i]["D"]));
                $monthly_due = floatval(str_replace(self::CHAR_FILTER, '', $allDataInSheet[$i]["E"]));
                $total_due = floatval(str_replace(self::CHAR_FILTER, '', $allDataInSheet[$i]["F"]));
                $balance = floatval(str_replace(self::CHAR_FILTER, '', $allDataInSheet[$i]["G"]));
                $tenure = intval(trim($allDataInSheet[$i]["I"]));
                
                $details = (object)[
                    'member_id'=> trim($allDataInSheet[$i]["B"]),
                    'full_name'=> trim($allDataInSheet[$i]["C"]),
                    'principal'=> $amount,
                    'monthly_repayment'=> $monthly_due,
                    'total_repayment'=> $total_due,
                    'loan_balance'=> $balance,
                    'disbursed_on'=> trim($allDataInSheet[$i]["H"]),
                    'tenure'=> $tenure,
                    'loan_type'=> trim($allDataInSheet[$i]["J"]),
                ];
                
                if (!$user) {
                    $this->set_loan_error($details, "Invalid Member ID");
                    continue;
                }

                if ($amount > 0 and $monthly_due <= 0) {
                    $this->set_loan_error($details, "Invalid amount in Monthly Repayment field");
                    continue;
                }
                if ($amount > 0 and $total_due <= 0) {
                    $this->set_loan_error($details, "Invalid amount in Total Repayment field");
                    continue;
                }
                // if ($amount > 0 and $balance <= 0) {
                //     $this->set_loan_error($details, "Invalid amount in Loan balance field");
                //     continue;
                // }

                if ($amount > 0 and $tenure < 1 ) {
                    $this->set_loan_error($details, "Tenure field must not be less than 1");
                    continue;
                }

                if ($user and $amount > 0) {
                    $date_obj = date_create(trim($allDataInSheet[$i]["H"]));
                    if ($date_obj == false) {
                        $date_obj = date_create("");
                    }
                    $disbursed_date = date_format($date_obj, 'Y-m-d H:i:s');
                    $schedule = $this->utility->get_loan_breakdown($amount, $loan_type->rate, $tenure, $loan_type->calc_method);
                    $loan_data = (object)[
                        'principal' => $amount,
                        'interest' => $total_due - $amount,
                        'total_due' => $total_due,
                    ];
                    $balance_schedule = $this->utility->split_loan_bal_to_principal_and_interest($balance, $loan_data);

                    $loan = [
                        'referrer_code' => $this->coop->referrer_code,
                        'coop_id' => $this->coop->id,
                        'user_id' => $user->id,
                        'loan_type_id' => $loan_type_id,
                        'tenure' => $tenure,
                        'rate' => $loan_type->rate,
                        'amount_requested' => $amount,
                        'principal' => $amount,
                        'interest' =>  $loan_data->interest ,
                        'total_due' =>  $loan_data->total_due ,
                        'principal_due' =>  $schedule->principal_due ,
                        'interest_due' =>  $schedule->interest_due ,
                        'monthly_due' => $monthly_due,
                        'principal_remain' =>  $balance_schedule->principal,
                        'interest_remain' =>  $balance_schedule->interest ,
                        'total_remain' => $balance,
                        'created_by' => $this->user->id,
                        'disbursed_date' => $disbursed_date,
                        'start_date' => $disbursed_date,
                        'end_date' => $this->utility->get_loan_end_date($disbursed_date, $tenure),
                        'created_on' => date('Y-m-d g:i:s'),
                        'status' => ($balance > 0) ? 'disbursed' : 'finished',
                        'migrated_status' => $migration_config->migrated_status,
                        'migrated_date' => $migration_config->migrated_date, 
                    ];
                    $this->common->add('loans', $loan);
                    $record_num++;
                }
            }

            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('member_loan_balance_migrated')
            ];
            //at least one record uploaded
            if ($record_num > 0) {
                $migration_config->total_record = $record_num;
                $this->common->add('migration_logs', $migration_config);
                $this->common->add('activities', $activities);
            }

            $this->session->set_userdata('record_num', $record_num);
            $this->session->set_userdata('failed_upload', $this->_loan_error);
            $this->session->set_userdata('url', 'message/loan');
            if (!$this->_loan_error) {
                $this->session->set_flashdata('message', lang('member_record_migrated'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
                redirect('migration/message/loan');
            }

        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }
    
    public function generate_credit_sales_template() {
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle(lang('credit_sales_balance_template'));
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', lang('credit_sales_balance_template'));
        $this->excel->getActiveSheet()->setCellValue('A2', lang('credit_sales_record_instruction'));
        $this->excel->getActiveSheet()->setCellValue('A3', lang('credit_sales_record_instruction_2'));
        $this->excel->getActiveSheet()->setCellValue('A4', lang('credit_sales_record_instruction_3'));
        $this->excel->getActiveSheet()->setCellValue('A5', lang('dont_modify'));
        $this->excel->getActiveSheet()->setCellValue('B7', lang('member_id'));
        $this->excel->getActiveSheet()->setCellValue('C7', lang('full_name'));
        $this->excel->getActiveSheet()->setCellValue('D7', lang('principal'));
        $this->excel->getActiveSheet()->setCellValue('E7', lang('monthly_repayment'));
        $this->excel->getActiveSheet()->setCellValue('F7', lang('total_repayment'));
        $this->excel->getActiveSheet()->setCellValue('G7', lang('loan_balance'));
        $this->excel->getActiveSheet()->setCellValue('H7', lang('start_date'));
        $this->excel->getActiveSheet()->setCellValue('I7', lang('tenure'));
        $this->excel->getActiveSheet()->setCellValue('J7', lang('description'));
        $this->excel->getActiveSheet()->setCellValue('K7', lang('product_type'));
        //merge cell A1 until I1
        $this->excel->getActiveSheet()->mergeCells('A1:I1');
        //merge cell A2 until I2
        $this->excel->getActiveSheet()->mergeCells('A2:I2');
        //merge cell A2 until I2
        $this->excel->getActiveSheet()->mergeCells('A3:I3');
        //merge cell A2 until I2
        $this->excel->getActiveSheet()->mergeCells('A4:I4');
        //merge cell A2 until I2
        $this->excel->getActiveSheet()->mergeCells('A5:I5');
        //set aligment to center for that merged cell (A1 to I1)
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //set aligment to center for that merged cell (A2 to I2)
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //set aligment to center for that merged cell (A2 to I2)
        $this->excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //set aligment to center for that merged cell (A2 to I2)
        $this->excel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //make the font become bold
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
        $this->excel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('#080');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(false);
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(14);
        $this->excel->getActiveSheet()->getStyle('A2')->getFill()->getStartColor()->setARGB('#f00');
        $this->excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(false);
        $this->excel->getActiveSheet()->getStyle('A3')->getFont()->setSize(14);
        $this->excel->getActiveSheet()->getStyle('A3')->getFill()->getStartColor()->setARGB('#f00');
        $this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(false);
        $this->excel->getActiveSheet()->getStyle('A4')->getFont()->setSize(14);
        $this->excel->getActiveSheet()->getStyle('A4')->getFill()->getStartColor()->setARGB('#ff0000');
        $this->excel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A5')->getFont()->setSize(14);
        $this->excel->getActiveSheet()->getStyle('A5')->getFill()->getStartColor()->setARGB('#00f');

        for ($col = ord('B'); $col <= ord('K'); $col++) {
            $this->excel->getActiveSheet()->getStyle(chr($col) . '7')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle(chr($col) . '7')->getFont()->setSize(14);
            $this->excel->getActiveSheet()->getStyle(chr($col) . '7')->getFill()->getStartColor()->setARGB('#333');
        }
        for ($col = ord('B'); $col <= ord('K'); $col++) { //set column dimension $this->excel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(true);
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
        $k = 9;
        foreach ($exceldata as $value) {
            $this->excel->getActiveSheet()->setCellValue('B' . $k, $value->username);
            $this->excel->getActiveSheet()->setCellValue('C' . $k, $value->first_name . ' ' . $value->last_name);
            $this->excel->getActiveSheet()->setCellValue('D' . $k, 0);
            $this->excel->getActiveSheet()->setCellValue('E' . $k, 0);
            $this->excel->getActiveSheet()->setCellValue('F' . $k, 0);
            $this->excel->getActiveSheet()->setCellValue('G' . $k, 0);
            $this->excel->getActiveSheet()->setCellValue('H' . $k, null);
            $this->excel->getActiveSheet()->setCellValue('I' . $k, 0);
            $this->excel->getActiveSheet()->setCellValue('J' . $k, null);
            $this->excel->getActiveSheet()->setCellValue('K' . $k, null);
            $k += 1;
        }

        $this->excel->getActiveSheet()->getStyle('B8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('C8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('D8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('E8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('F8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('G8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('H8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('I8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('J8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('K8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $filename = 'credit-sales-template' . $this->coop->id . '.xls'; //save our workbook as this file name
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

    public function credit_sales_record() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->load->helper('security');
        $this->form_validation->set_rules('file', lang("upload_file"), 'xss_clean');
        $this->form_validation->set_rules('product_type', lang('product_type'), 'trim|required');

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

            $loan_type_id = $this->input->post('product_type');
            $loan_type = $this->common->get_this('product_types', ['id' => $loan_type_id]);

            $migration_config = $this->migration_config("Credit Sales Record", 'credit_sales');
            $record_num = 0;
            for ($i = 9; $i <= $arrayCount; $i++) {
                if (!$allDataInSheet[$i]["B"]) {
                    continue;
                }
                $user = $this->common->get_this('users', ['username' => $allDataInSheet[$i]["B"]]);
                $amount = floatval(str_replace(self::CHAR_FILTER, '', $allDataInSheet[$i]["D"]));
                $monthly_due = floatval(str_replace(self::CHAR_FILTER, '', $allDataInSheet[$i]["E"]));
                $total_due = floatval(str_replace(self::CHAR_FILTER, '', $allDataInSheet[$i]["F"]));
                $balance = floatval(str_replace(self::CHAR_FILTER, '', $allDataInSheet[$i]["G"]));
                $tenure = trim($allDataInSheet[$i]["I"]);
                $description = trim($allDataInSheet[$i]["J"]);

                $details = (object)[
                    'member_id' => trim($allDataInSheet[$i]["B"]),
                    'full_name' => trim($allDataInSheet[$i]["C"]),
                    'principal' => $amount,
                    'monthly_repayment' => $monthly_due,
                    'total_repayment' => $total_due,
                    'loan_balance' => $balance,
                    'disbursed_on' => trim($allDataInSheet[$i]["H"]),
                    'tenure' => $tenure,
                    'loan_type' => trim($allDataInSheet[$i]["J"]),
                ];

                if (!$user) {
                    $this->set_loan_error($details, "Invalid Member ID");
                    continue;
                }

                if ($amount > 0 and $monthly_due <= 0) {
                    $this->set_loan_error($details, "Invalid amount in Monthly Repayment field");
                    continue;
                }

                if ($amount > 0 and $total_due <= 0) {
                    $this->set_loan_error($details, "Invalid amount in Total Repayment field");
                    continue;
                }

                // if ($amount > 0 and $balance <= 0) {
                //     $this->set_loan_error($details, "Invalid amount in Loan balance field");
                //     continue;
                // }

                if ($amount > 0 and $tenure < 1) {
                    $this->set_loan_error($details, "Tenure field must not be less than 1");
                    continue;
                }

                if ($user and $amount > 0) {
                    $date_obj = date_create(trim($allDataInSheet[$i]["H"]));
                    if ($date_obj == false) {
                        $date_obj = date_create("");
                    }
                    $disbursed_date = date_format($date_obj, 'Y-m-d H:i:s');
                    $schedule = $this->utility->get_loan_breakdown($amount, $loan_type->rate, $tenure, $loan_type->calc_method);

                    $loan_data = (object)[
                        'principal' => $amount,
                        'interest' => $total_due - $amount,
                        'total_due' => $total_due,
                    ];
                    $balance_schedule = $this->utility->split_loan_bal_to_principal_and_interest($balance, $loan_data);
                    $loan = [
                        'referrer_code' => $this->coop->referrer_code,
                        'coop_id' => $this->coop->id,
                        'user_id' => $user->id,
                        'product_type_id' => $loan_type_id,
                        'tenure' => $tenure,
                        'rate' => $loan_type->rate,
                        'amount_requested' => $amount,
                        'principal' => $amount,
                        'interest' =>  $loan_data->interest,
                        'total_due' =>  $loan_data->total_due,
                        'principal_due' =>  $schedule->principal_due,
                        'interest_due' =>  $schedule->interest_due,
                        'monthly_due' => $monthly_due,
                        'principal_remain' =>  $balance_schedule->principal,
                        'interest_remain' =>  $balance_schedule->interest,
                        'total_remain' => $balance,
                        'created_by' => $this->user->id,
                        'disbursed_date' => $disbursed_date,
                        'start_date' => $disbursed_date,
                        'end_date' => $this->utility->get_loan_end_date($disbursed_date, $tenure),
                        'created_on' => date('Y-m-d g:i:s'),
                        'description' => $description,
                        'status' => ($balance > 0) ? 'disbursed' : 'finished',
                        'migrated_status' => $migration_config->migrated_status,
                        'migrated_date' => $migration_config->migrated_date, 
                    ];
                    $this->common->add('credit_sales', $loan);
                    $record_num++;
                }
            }
            // exit;
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('member_credit_sales_balance_migrated')
            ];

            if ($record_num > 0) {
                $migration_config->total_record = $record_num;
                $this->common->add('migration_logs', $migration_config);
                $this->common->add('activities', $activities);
            }

            $this->session->set_userdata('record_num', $record_num);
            $this->session->set_userdata('failed_upload', $this->_loan_error);
            $this->session->set_userdata('url', 'message/loan');

            if (!$this->_loan_error) {
                $this->session->set_flashdata('message', lang('member_credit_sales_balance_migrated'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
                redirect('migration/message/loan');
            }
            
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }

        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function send_invite() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('recipients', lang('recipients'), 'trim|required');
        if ($this->form_validation->run()) {
            $all_recipients = $this->input->post('recipients');
            $emails = '';
            $subject = 'Member Registration Invite';

            if (is_array($all_recipients)) {
                foreach ($all_recipients as $recipient) {
                    $emails .= $recipient->email . ',';
                }
                $emails = trim($emails, ',');
            } else {
                $emails = $all_recipients;
            }

            $message = $this->load->view('emails/email_member_invite', null, true);
            
            if ($this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $emails, $subject, $message)) {
                $this->session->set_flashdata('message', lang('act_successful'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function logs(){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $where = ['coop_id' => $this->coop->id, 'migrated_status'=>1];
        $this->data['logs'] = $this->common->get_all_these('migration_logs',$where);
        $this->data['title'] = lang('migration').' '.lang('logs');
        $this->data['controller'] = lang('migration');
        $this->layout->set_app_page('migration/logs', $this->data);
    }

    public function rollback($id){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xdelete', 'on');
        $id = $this->utility->un_mask($id);
        $log = $this->common->get_this('migration_logs', ['coop_id' => $this->coop->id, 'id' => $id] );
        $where = ['coop_id' => $this->coop->id, 'migrated_status' => 1, 'migrated_date'=>$log->migrated_date];
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('migration') . ''. lang('logs').' ' . lang('deleted')
        ];

        $this->common->start_trans();
        $this->common->delete_this($log->table_name, $where);
        $this->common->delete_this('migration_logs', $where);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', $activities['action']);
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function reset_member_id(){
        // $coops = $this->common->get_limit('cooperatives', false, 1000, 'id', 'ASC');
        // foreach($coops as $c){
        //     $members = $this->common->get_limit('users', ['coop_id'=>$c->id], 1000, 'id', 'ASC');
        //     $serial = 1;
        //     foreach ($members as $m){
        //         $member_id = $this->utility->reset_member_id($c, $serial);
        //         $this->common->update_this('users', ['id'=>$m->id], ['username'=>$member_id]);
        //         $serial++;
        //     }
        // }
    }

    public function fixe_email(){
        $members = $this->common->get_all('users');
        foreach ($members as $m){
            if(strpos($m->email, '@')){
                continue;
            }
            var_dump($m->username, $m->email);
            $this->common->update_this('users', ['id'=>$m->id], ['email'=>$m->username]);
        }
    }
}
