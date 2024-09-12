<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Utility
{

    public function __construct()
    {
        $this->ignite = &get_instance();
        $this->ignite->load->model('common_model');
    }

    public function generate_temp_pwd()
    {
        return substr(strtoupper(md5(date('g:i:s'))), 5, 8);
    }

    public function generate_pv_no($coop_name)
    {
        $new_str = str_split($coop_name, 2);
        return strtoupper('PV-' . $new_str[0] . '-' . date('Ymdgis'));
    }

    public function get_2fa_token()
    {
        $token = random_string('numeric', 6);
        return (object)[
            'split_token' => str_split($token, 2),
            'token' => $token,
            'hash' => hash('sha1', $token),
            'expires_on' => date_create(date('Y-m-d H:i:s'))->modify('+10 minutes')->format('Y-m-d H:i:s')
        ];
    }

    public function verify_twofa_token($user_id, $token)
    {
        $where = ['id' => $user_id, 'twofa_token' => hash('sha1', $token), 'twofa_expires_on > ' => date('Y-m-d H:i:s')];
        $token_state = $this->ignite->common->get_this('users', $where);
        if ($token_state) {
            return true;
        }

        return false;
    }


    public function coop_code(){
        $increament = 1;
        
        do {
            $coop = $this->ignite->common->get_limit('cooperatives', false, 1, 'id', 'DESC');
            $digit = 3;
            $prefix = '';
            $coop_code = '';
            if ($coop) {
                $last_coop_id = $coop[0]->id;
                $left_over = $digit - strlen($last_coop_id);
                for ($i = 1; $i <= $left_over; $i++) {
                    $prefix .= '0';
                }
                $coop_code =  $prefix . ($last_coop_id + $increament) . 'C';
            } else {
                $coop_code = '00' . '1' . 'C';
            }
            $increament++;
        } while ($this->ignite->common->get_this('cooperatives', ['coop_code' => $coop_code]));

        return $coop_code;
    }

    public function generate_member_id($coop_name, $coop_id, $increase = false){
        $increament = 1;
        do {
            $new_str = str_split($coop_name);
            $coop_initials = strtoupper($new_str[0] . $new_str[1]);
            $coop = $this->ignite->common->get_limit('cooperatives',['id' => $coop_id], 1, 'id', 'DESC');
            $user = $this->ignite->common->get_limit('users', ['coop_id' => $coop_id], 1, 'id', 'DESC');

            $digit = 4;
            $prefix = '';
            $new_serial_no = 1;
            $member_id = '';
            if ($user) {
                //extract user serial number
                $serial_no = strrev(strstr(strstr(strrev($user[0]->username), strrev($coop_initials), true), 'C', true));
                $new_serial_no = $serial_no + $increament;

                $left_over = $digit - strlen($new_serial_no);
                for ($i = 1; $i <= $left_over; $i++) {
                    $prefix .= '0';
                }
                $member_id = $coop_initials . $coop[0]->coop_code . $prefix . $new_serial_no;
            } else {
                $member_id = $coop_initials . $coop[0]->coop_code . $prefix . '000' . $new_serial_no;
            }
            $increament++;
        } while ($this->ignite->common->get_this('users', ['username' => $member_id]));
        return $member_id;
    }

    public function reset_member_id($coop, $serial_no){
        $new_str = str_split($coop->coop_name);
        $coop_initials = strtoupper($new_str[0] . $new_str[1]);

        $digit = 4;
        $prefix = '';
        $new_serial_no = 1;
        $new_serial_no = $serial_no;

        $left_over = $digit - strlen($new_serial_no);
        for ($i = 1; $i <= $left_over; $i++) {
            $prefix .= '0';
        }
        return $coop_initials . $coop->coop_code . $prefix . $new_serial_no;

        
    }

    public function generate_default_pass($coop_name)
    {
        $new_str = str_split($coop_name, 3);
        return strtoupper($new_str[0] . '-12345');
    }

    public function referer_check($referer_code)
    {
        if ($referer_code) {
            if (!$this->ignite->common->get_this('agent_referrer_code', ['code' => $referer_code])) {
                return ['error' => 'Invalid referrer code'];
            }
        }
        return ['referer_code' => 'CFYAA0001'];
    }

    public function shortend_str_len($str, $len, $surfix = "...")
    {
        $splited_str = str_split($str, $len);
        if (count($splited_str) > 1) {
            return $splited_str[0] . $surfix;
        } else {
            return $splited_str[0];
        }
    }

    public function access_check($menu_id, $role_id, $access, $status)
    {

        // preventing the supper admin from write and delete access
        if (!$this->ignite->is_super_admin) {
            // if($access == 'xwrite' ){
            //     $this->ignite->session->set_flashdata('error', lang('access_denied'));
            //     redirect($_SERVER["HTTP_REFERER"]);
            // }
            // if($access == 'xdelete' ){
            //     $this->ignite->session->set_flashdata('error', lang('access_denied'));
            //     redirect($_SERVER["HTTP_REFERER"]);
            // }
            if (!$this->ignite->common->get_this('privilege', ['menu_id' => $menu_id, 'role_id' => $role_id, $access => $status])) {
                $this->ignite->session->set_flashdata('error', lang('access_denied'));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }
    }

    public function password_expired($user_id)
    {
        $password_manager = $this->ignite->common->get_this('password_manager', ['user_id' => $user_id]);

        if (!$password_manager) {
            return true;
        }
        if (new DateTime(date('Y:m:d H:i:s')) > new DateTime($password_manager->expired_on)) {
            return true;
        }
        return false;
    }

    public function get_date_interval($first_date, $second_date)
    {
        if (!$first_date or !$second_date) {
            return FALSE;
        }

        $d1 = new DateTime($first_date . '00:00:00');
        $d2 = new DateTime($second_date . '00:00:00');
        return $d1->diff($d2);
    }

    public function sms_bal_ok($messages, $reciepient, $sms_bal)
    {
        $len = strlen($messages);
        $unit_page = 160;

        $pages = ceil($len / $unit_page);
        $cout_reciepient = count(explode(',', $reciepient));

        $total_pages_to_send = $pages * $cout_reciepient;
        if ($total_pages_to_send > $sms_bal) {
            return false;
        }
        return true;
    }

    public function send_SMS($phone_nos, $sender, $msg, $username, $pass){
        $phone_nos = '234' . trim($phone_nos, '0');
        $postdata = http_build_query(
            array(
                'user' => $username,
                'password' => $pass,
                'sender' => $sender,
                'SMSText' => $msg,
                'GSM' => $phone_nos,

            )
        );
        //prepare a http post request
        $opts = array(
            'http' =>
            array(
                'method' => 'GET',
            )
        );

        //craete a stream to communicate with betasms api
        header("Access-Control-Allow-Origin: *");

        $context = stream_context_create($opts);
        //get result from communication
        $result = file_get_contents($this->ignite->app_settings->sms_api . $postdata, false, $context);
        //return result to client, this will return the appropriate respond code
        $json_result = json_decode($result);
        if ($json_result > 0) {
            return true;
        }
        return false;
    }
    // public function send_SMS($phone_nos, $sender, $msg, $username, $pass) {

    //     $postdata = http_build_query(
    //             array(
    //                 'username' => $username,
    //                 'password' => $pass,
    //                 'message' => $msg,
    //                 'mobiles' => $phone_nos,
    //                 'sender' => $sender,
    //             )
    //     );
    //     //prepare a http post request
    //     $opts = array('http' =>
    //         array(
    //             'method' => 'POST',
    //             'header' => 'Content-type: application/x-www-form-urlencoded',
    //             'content' => $postdata
    //         )
    //     );

    //     //craete a stream to communicate with betasms api
    //     header("Access-Control-Allow-Origin: *");

    //     $context = stream_context_create($opts);
    //     //get result from communication
    //     $result = file_get_contents($this->ignite->app_settings->sms_api, false, $context);
    //     //return result to client, this will return the appropriate respond code
    //     return json_decode($result);
    // }

    public function send_mail($sender, $sender_name, $recievers, $subject, $message)
    {
        $this->ignite->load->library('email');

        $config['smtp_crypto'] = 'ssl';
        $config['priority'] = 5;
        $config['charset'] = 'iso-8859-1';
        $config['crlf'] = "\r\n";
        $config['mailtype'] = "html";
        $config['newline'] = "\r\n";
        $config['protocol'] = "smtp";
        $config['smtp_host'] = $this->ignite->app_settings->smtp_host;
        $config['smtp_user'] = $this->ignite->app_settings->smtp_user;
        $config['smtp_pass'] = $this->ignite->app_settings->smtp_pass;
        $config['smtp_port'] = $this->ignite->app_settings->smtp_port;

        $this->ignite->load->initialize($config);
        $this->ignite->email->from($sender, $sender_name);
        $this->ignite->email->bcc($recievers);

        $this->ignite->email->subject($subject);
        $this->ignite->email->message($message);
        $this->ignite->email->set_mailtype('html');
        if ($this->ignite->email->send()) {
            // echo $this->ignite->email->print_debugger(); die();
            return TRUE;
        } else {
            // echo $this->ignite->email->print_debugger(); die();
            return TRUE;
        }
    }

    public function img_upload($field_name, $filename, $max_size = false, $upload_path = false)
    {

        $config['upload_path'] = 'assets/images/id';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|svg';
        $config['max_size'] = 200;

        if ($upload_path) {
            $config['upload_path'] = "assets/images/$upload_path";
        }

        if ($max_size) {
            $config['max_size'] = $max_size;
        }
        //        $config['max_width'] = 300;
        //        $config['max_height'] = 300;
        $config['overwrite'] = TRUE;
        $config['file_ext_tolower'] = TRUE;
        $config['file_name'] = $filename;

        $this->ignite->load->library('upload', $config);

        if (!$this->ignite->upload->do_upload($field_name)) {

            return array('error' => $this->ignite->upload->display_errors());
        } else {
            return array('upload_data' => $this->ignite->upload->data());
        }
    }

    public function file_upload($field_name, $filename, $max_size = false, $upload_path = false)
    {
        $config['upload_path'] = "assets/files/uploads/";
        $config['allowed_types'] = 'xls|xlsx|csv';
        $config['max_size'] = 500;

        if ($upload_path) {
            $config['upload_path'] = $upload_path;
        }

        if ($max_size) {
            $config['max_size'] = $max_size;
        }
        $config['overwrite'] = TRUE;
        $config['file_ext_tolower'] = TRUE;
        $config['file_name'] = $filename;
        $this->ignite->load->library('upload', $config);

        if (!$this->ignite->upload->do_upload($this->ignite->security->xss_clean($field_name))) {

            return array('error' => $this->ignite->upload->display_errors());
        } else {
            return array('upload_data' => $this->ignite->upload->data());
        }
    }

    public function img_resize($source)
    {
        $config['image_library'] = 'gd2';
        $config['source_image'] = $source;
        $config['maintain_ratio'] = TRUE;
        $config['width'] = 450;
        $config['height'] = 300;
        $this->ignite->load->library('image_lib', $config);
        return $this->ignite->image_lib->resize();
    }

    public function img_thumb($source)
    {
        $config['image_library'] = 'gd2';
        $config['source_image'] = $source;
        $config['maintain_ratio'] = TRUE;
        $config['create_thumb'] = TRUE;
        $config['width'] = 75;
        $config['height'] = 50;
        $this->ignite->load->library('image_lib', $config);
        return $this->ignite->image_lib->resize();
    }

    public function video_upload($field_name, $filename)
    {

        $config['upload_path'] = '../assets/video/';
        $config['allowed_types'] = 'mp4|3gp|rv|flv|wmv|mov|avi|f4v|webm|ogg';
        //$config['max_size']             = 100;
        //$config['max_width']            = 1024;
        //$config['max_height']           = 768;
        $config['overwrite'] = TRUE;
        $config['remove_spaces'] = TRUE;
        $config['file_ext_tolower'] = TRUE;
        $config['file_name'] = $filename;

        $this->ignite->load->library('upload', $config);

        if (!$this->ignite->upload->do_upload($field_name)) {
            return array('error' => $this->ignite->upload->display_errors('<p>', '</p>'));
        } else {
            return array('upload_data' => $this->ignite->upload->data());
        }
    }

    function directory_copy($srcdir, $dstdir)
    {
        $this->ignite->load->helper('directory');
        //preparing the paths
        $srcdir = rtrim($srcdir, '/');
        $dstdir = rtrim($dstdir, '/');

        //creating the destination directory
        if (!is_dir($dstdir)) {
            mkdir($dstdir, 0777, true);
        }

        //Mapping the directory
        $dir_map = directory_map($srcdir, 0, true);
        foreach ($dir_map as $object_key => $object_value) {
            if (is_numeric($object_key))
                copy($srcdir . '/' . $object_value, $dstdir . '/' . $object_value); //This is a File not a directory
            else
                $this->directory_copy($srcdir . '/' . $object_key, $dstdir . '/' . $object_key); //this is a directory
        }
        return true;
    }

    // get a date in this format 2019-12-30 02:22:10 or 2019-12-30
    public function just_date($date = null, $with_time = true)
    {
        if (!$date) {
            return;
        }
        $date = date_create($date);
        if ($with_time == false) {
            return date_format($date, 'Y-M-d');
        }
        return date_format($date, 'Y-M-d g:i a');
    }

    public function brk_date($date, $opt = false)
    {

        if ($opt == 'datetime') {
            $d1 = DateTime::createFromFormat('Y-m-d H:i:s', $date);
            return $d1->format('Y-m-d');
        }
        $d1 = DateTime::createFromFormat('Y-m-d', $date);
        return ['y' => $d1->format('Y'), 'm' => $d1->format('m'), 'd' => $d1->format('d')];
    }

    private function switch_str($string, $to_num_or_alpa)
    {
        $swicher = [0 => "X", 1 => "Z", 2 => "B", 3 => "J", 4 => "E", 5 => "M", 6 => "O", 7 => "H", 8 => "I", 9 => "S"];
        $final_str = "";
        $string_to_array = str_split(strtoupper($string));
        if ($to_num_or_alpa === "number") {
            foreach ($string_to_array as $val) {
                foreach ($swicher as $key => $s_val) {
                    if ($val == $s_val) {
                        $final_str .= $key;
                    }
                }
            }
        } elseif ($to_num_or_alpa === "alpha") {
            foreach ($string_to_array as $val) {
                foreach ($swicher as $key => $s_val) {
                    if ($val == $key) {
                        $final_str .= $s_val;
                    }
                }
            }
        }

        return $final_str;
    }

    public function mask($string)
    {
        $prefix = random_string('alnum', 150);
        $surfix = random_string('alnum', 28);
        return strtoupper($prefix . $this->switch_str($string, 'alpha') . $surfix);
    }

    public function un_mask($string)
    {
        $new_string = substr($string, 150);
        $len = strlen($new_string) - 28;
        return $this->switch_str(substr($new_string, 0, $len), 'number');
    }

    public function get_due_month($last_month, $last_year)
    {
        $next_month = $last_month + 1;
        $next_year = $last_year;
        if ($last_month == 12) {
            $next_year = $last_year + 1;
            $next_month = 1;
        }
        return ['next_month' => $next_month, 'next_year' => $next_year];
    }

    public function get_wallet_bal($user_id = null, $coop_id = null)
    {
        $where_credit = ['tranx_type' => 'credit', 'coop_id' => $coop_id, 'status' => 'successful'];
        $where_debit = ['tranx_type' => 'debit', 'coop_id' => $coop_id, 'status' => 'successful'];

        if ($user_id) {
            $where_credit['user_id'] = $user_id;
            $where_debit['user_id'] = $user_id;
        }

        $wallet_credit = $this->ignite->common->sum_this('wallet', $where_credit, 'amount')->amount;
        $wallet_debit = $this->ignite->common->sum_this('wallet', $where_debit, 'amount')->amount;
        return $wallet_credit - $wallet_debit;
    }
    public function get_agent_wallet_bal($user_id = null, $coop_id = null)
    {
        $where_credit = ['tranx_type' => 'credit', 'coop_id' => $coop_id, 'status' => 'successful'];
        $where_debit = ['tranx_type' => 'debit', 'coop_id' => $coop_id, 'status' => 'successful'];

        if ($user_id) {
            $where_credit['user_id'] = $user_id;
            $where_debit['user_id'] = $user_id;
        }

        $wallet_credit = $this->ignite->common->sum_this('agent_wallet', $where_credit, 'amount')->amount;
        $wallet_debit = $this->ignite->common->sum_this('agent_wallet', $where_debit, 'amount')->amount;
        return $wallet_credit - $wallet_debit;
    }

    public function get_savings_bal($user_id = null, $coop_id = null, $savings_type = null)
    {
        $where_credit = ['tranx_type' => 'credit', 'coop_id' => $coop_id, 'status' => 'paid'];
        $where_debit = ['tranx_type' => 'debit', 'coop_id' => $coop_id, 'status' => 'paid'];

        if ($savings_type) {
            $where_credit['savings_type'] = $savings_type;
            $where_debit['savings_type'] = $savings_type;
        }

        if ($user_id) {
            $where_credit['user_id'] = $user_id;
            $where_debit['user_id'] = $user_id;
        }

        $credit = $this->ignite->common->sum_this('savings', $where_credit, 'amount')->amount;
        $debit = $this->ignite->common->sum_this('savings', $where_debit, 'amount')->amount;
        return $credit - $debit;
    }

    public function get_loan_bal($user_id = null, $coop_id = null, $loan_type = null)
    {
        $where = ['coop_id' => $coop_id, 'status' => 'disbursed'];
        if ($loan_type) {
            $where['loan_type_id'] = $loan_type;
        }

        if ($user_id) {
            $where['user_id'] = $user_id;
        }
        return $this->ignite->common->sum_this('loans', $where, 'total_remain')->total_remain;
    }

    public function get_credit_sales_bal($user_id = null, $coop_id = null, $product_type = null)
    {
        $where = ['coop_id' => $coop_id, 'status' => 'disbursed'];
        if ($product_type) {
            $where['product_type_id'] = $product_type;
        }

        if ($user_id) {
            $where['user_id'] = $user_id;
        }
        return $this->ignite->common->sum_this('credit_sales', $where, 'total_remain')->total_remain;
    }

    public function get_loan_interest($coop_id = null, $loan_type = null){
        $where = "coop_id = $coop_id AND (status = 'disbursed' OR status='finished')";
        if ($loan_type) {
            $where['loan_type_id'] = $loan_type;
        }

        return $this->ignite->common->sum_this('loans', $where, 'interest')->interest;
    }

    public function get_credit_sales_interest($coop_id = null, $product_type = null)
    {
        $where = "coop_id = $coop_id AND (status = 'disbursed' OR status='finished')";
        if ($product_type) {
            $where['product_type_id'] = $product_type;
        }

        return $this->ignite->common->sum_this('credit_sales', $where, 'interest')->interest;
    }

    public function get_loan_colleted($user_id, $coop_id, $loan_type = null)
    {
        $where = ['user_id' => $user_id, 'coop_id' => $coop_id, 'status' => 'disbursed'];
        if ($loan_type) {
            $where['loan_type_id'] = $loan_type;
        }
        return $this->ignite->common->sum_this('loans', $where, 'total_due')->total_due;
    }

    public function get_credit_sales_colleted($user_id, $coop_id, $product_type = null)
    {
        $where = ['user_id' => $user_id, 'coop_id' => $coop_id, 'status' => 'disbursed'];
        if ($product_type) {
            $where['product_type_id'] = $product_type;
        }
        return $this->ignite->common->sum_this('credit_sales', $where, 'total_due')->total_due;
    }

    // public function get_loan_breakdown($principal, $rate, $term) {
    //     $int = $rate / 1200;
    //     $int1 = 1 + $int;
    //     $r1 = pow($int1, $term);

    //     $monthly_due = round($principal * ($int * $r1) / ($r1 - 1), 2, PHP_ROUND_HALF_UP);
    //     $total_due = round($monthly_due * $term, 2, PHP_ROUND_HALF_UP);
    //     $interst = $total_due - $principal;

    //     return (object) ['monthly_due' => $monthly_due, 'total_due' => $total_due, 'interest' => $interst];
    // }

    public function get_loan_breakdown($principal, $rate, $tenure, $method = '')
    {
        $t = 12; // months in a year
        $year = $tenure / $t;
        $R = $rate / 100; //annual rate
        $r = $R / $t; // monthly rate

        if ($rate <= 0) {
            return (object) [
                'principal' => $principal,
                'monthly_due' => round($principal / $tenure, 2, PHP_ROUND_HALF_UP),
                'principal_due' => $principal,
                'interest_due' => 0,
                'total_due' => $principal,
                'interest' => 0,
            ];
        }

        if ($method == 3) { //reducing balance
            $monthly_due =  $principal * ($r * pow((1 + $r), $tenure)) /  (pow((1 + $r), $tenure) - 1);
        }

        if ($method == 2) { //simple interest
            $monthly_due =  ($principal + ($principal * $year * $R)) / ($tenure);
        }

        if ($method == 1) { //flat or fixed rate
            $interest = $principal * $R;
            $total_due = $principal + $interest;
            $monthly_due = $total_due / $tenure;
            $interest_due = $interest / $tenure;
            $principal_due = $principal / $tenure;

            return (object) [
                'principal' => $principal,
                'monthly_due' => round($monthly_due, 2, PHP_ROUND_HALF_UP),
                'principal_due' => round($principal_due, 2, PHP_ROUND_HALF_UP),
                'interest_due' => round($interest_due, 2, PHP_ROUND_HALF_UP),
                'total_due' => round($total_due, 2, PHP_ROUND_HALF_UP),
                'interest' => round($interest, 2, PHP_ROUND_HALF_UP),
            ];
        }

        $total_due = $monthly_due * $tenure;
        $interest = $total_due - $principal;
        $interest_due = $interest / $tenure;
        $principal_due = $principal / $tenure;
        return (object) [
            'principal' => $principal,
            'monthly_due' => round($monthly_due, 2, PHP_ROUND_HALF_UP),
            'principal_due' => round($principal_due, 2, PHP_ROUND_HALF_UP),
            'interest_due' => round($interest_due, 2, PHP_ROUND_HALF_UP),
            'total_due' => round($total_due, 2, PHP_ROUND_HALF_UP),
            'interest' => round($interest, 2, PHP_ROUND_HALF_UP),
        ];
    }

    public function check_guarantor($guarantor, $member_id, $coop_id)
    {
        if (!$guarantor) {
            return;
        }

        $unique_guarantor = array_unique($guarantor);
        if (count($unique_guarantor) != count($guarantor)) {
            return ['error' => lang('Duplicate guarantor not allowed')];
        }

        if (in_array('', $unique_guarantor)) {
            return ['error' => lang('one_or_more_gurantor_missing')];
        }

        if (in_array($member_id, $guarantor)) {
            return ['error' => lang('cannot_use_self_as_gurantor')];
        }

        $i = 1;
        $error = '';
        $guarantors = [];
        foreach ($unique_guarantor as $g) {
            $gua = $this->ignite->common->get_this('users', ['username' => $g, 'coop_id' => $coop_id]);
            if (!$gua) {
                $error .= "Gurantor {$i} ID not exist <br>";
            } else {
                $guarantors[] = $gua;
            }
            ++$i;
        }

        if ($error) {
            return ['error' => $error];
        } else {
            return ['guarantor' => $guarantors];
        }
    }

    public function loan_approval_completed($loan_id, $approval_level, $is_for = false, $approval_exist = false)
    {
        if ($is_for == 'credit_sales') {
            $approvals = $this->ignite->info->get_credit_sales_approvals(['credit_sales.id' => $loan_id, 'credit_sales_approvals.status' => 'approved']);
        } else {
            $approvals = $this->ignite->info->get_loan_approvals(['loans.id' => $loan_id, 'loan_approvals.status' => 'approved']);
        }

        if ($approval_exist) {
            return FALSE;
        }

        $total_approval = count($approvals) + 1;
        if ($total_approval == $approval_level) {
            return true;
        } else {
            return false;
        }
    }

    public function member_exit_approvals_completed($member_exit_id, $approval_level, $approval_exist = false)
    {
        $approvals = $this->ignite->info->get_member_exit_approvals(['member_exit.id' => $member_exit_id, 'member_exit_approvals.status' => 'approved']);

        if ($approval_exist) {
            return FALSE;
        }

        $total_approval = count($approvals) + 1;
        if ($total_approval == $approval_level) {
            return true;
        } else {
            return false;
        }
    }

    public function withdrawal_approvals_completed($withdrawal_id, $approval_level, $approval_exist = false)
    {
        $approvals = $this->ignite->info->get_withdrawal_approvals(['savings.id' => $withdrawal_id, 'withdrawal_approvals.status' => 'approved']);

        if ($approval_exist) {
            return FALSE;
        }

        $total_approval = count($approvals) + 1;
        if ($total_approval == $approval_level) {
            return true;
        } else {
            return false;
        }
    }

    public function guarantor_approval_completed($loan_id, $coop_id, $is_for = false)
    {
        if ($is_for == 'credit_sales') {
            $guarantor_approvals = $this->ignite->info->get_credit_sales_guarantors(['credit_sales.id' => $loan_id, 'credit_sales.coop_id' => $coop_id, 'credit_sales_guarantors.status' => 'approved']);
            $loan = $this->ignite->common->get_this('credit_sales', ['id' => $loan_id, 'coop_id' => $coop_id]);
            $loan_type = $this->ignite->common->get_this('product_types', ['id' => $loan->product_type_id, 'coop_id' => $coop_id]);
        } else {
            $guarantor_approvals = $this->ignite->info->get_loan_guarantors(['loans.id' => $loan_id, 'loans.coop_id' => $coop_id, 'loan_guarantors.status' => 'approved']);
            $loan = $this->ignite->common->get_this('loans', ['id' => $loan_id, 'coop_id' => $coop_id]);
            $loan_type = $this->ignite->common->get_this('loan_types', ['id' => $loan->loan_type_id, 'coop_id' => $coop_id]);
        }

        $total_approval = count($guarantor_approvals);

        if ($total_approval == $loan_type->guarantor) {
            return true;
        } else {
            return false;
        }
    }

    public function check_duplicate_loan_approval($approvals, $user_id)
    {
        foreach ($approvals as $a) {
            if ($a->exco_id == $user_id and $a->status == 'approved') {
                return ['error' => 'Duplicate approval not allowed'];
            } elseif ($a->exco_id == $user_id and $a->status == 'declined') {
                return ['error' => 'Duplicate Decline not allowed'];
            }
        }
    }

    public function get_this_year($range)
    {
        if ($range === 'start') {
            return date('Y-m-d g:i:s', strtotime('first day of january this year'));
        } elseif ($range === 'end') {
            return date('Y-m-d g:i:s', strtotime('last day of december this year'));
        } else {
            return date('Y-m-d g:i:s');
        }
    }

    public function get_loan_end_date($start_date, $duration)
    {
        $d = new DateTime($start_date);
        return $d->modify("+$duration month")->format('Y-m-d H:i:s');
        //        return $d->modify("last day of this month")->format('Y-m-d H:i:s');
    }

    public function get_end_date($start_date, $duration, $with_time = false)
    {
        $d = new DateTime($start_date);
        if ($with_time) {
            return $d->modify("+$duration month")->format('Y-m-d H:i:s');
        }
        return $d->modify("+$duration month")->format('Y-m-d');
    }

    public function savings_balance_by_month_year($month, $year, $coop_id)
    {
        $where_credit = ['month_id' => $month, 'savings.status' => 'paid', 'year' => $year, 'tranx_type' => 'credit', 'coop_id' => $coop_id];
        $where_debit = ['month_id' => $month, 'savings.status' => 'paid', 'year' => $year, 'tranx_type' => 'debit', 'coop_id' => $coop_id];
        $credit = $this->ignite->common->sum_this('savings', $where_credit, 'amount')->amount;
        $debit = $this->ignite->common->sum_this('savings', $where_debit, 'amount')->amount;
        return $credit - $debit;
    }

    public function savings_by_month_year($month, $year, $coop_id)
    {
        $where_credit = ['month_id' => $month, 'savings.status' => 'paid', 'year' => $year, 'tranx_type' => 'credit', 'coop_id' => $coop_id];
        $credit = $this->ignite->common->sum_this('savings', $where_credit, 'amount')->amount;
        return $credit;
    }

    public function withdrawal_by_month_year($month, $year, $coop_id)
    {
        $where_debit = ['month_id' => $month, 'savings.status' => 'paid', 'year' => $year, 'tranx_type' => 'debit', 'coop_id' => $coop_id];
        $debit = $this->ignite->common->sum_this('savings', $where_debit, 'amount')->amount;
        return $debit;
    }

    public function gen_year_month_savings_bal_graph($year = null, $coop_id = null)
    {
        $months = ['JAN' => '01', 'FEB' => '02', 'MAR' => '03', 'APR' => '04', 'MAY' => '05', 'JUN' => '06', 'JUL' => '07', 'AUG' => '08', 'SEP' => '09', 'OCT' => '10', 'NOV' => '11', 'DEC' => '12'];
        foreach ($months as $m => $val) {
            $result = $this->savings_balance_by_month_year($val, $year, $coop_id);
            if (!$result) {
                $data[$m] = 0.00;
            } else {
                $data[$m] = $result;
            }
        }
        return $data;
    }

    public function gen_year_month_liquidity_graph($year = null, $coop_id = null)
    {
        $months = ['JAN' => '01', 'FEB' => '02', 'MAR' => '03', 'APR' => '04', 'MAY' => '05', 'JUN' => '06', 'JUL' => '07', 'AUG' => '08', 'SEP' => '09', 'OCT' => '10', 'NOV' => '11', 'DEC' => '12'];

        foreach ($months as $m => $val) {
            $reg_fee = $this->ignite->common->sum_this('users', ['coop_id' => $coop_id, 'month_id' => $val, 'year' => $year], 'reg_fee')->reg_fee;
            $savings = $this->savings_balance_by_month_year($val, $year, $coop_id);
            $loan = $this->ignite->info->get_loan_by_month_year($val, $year, $coop_id);
            $credit_sales = $this->ignite->info->get_credit_sales_by_month_year($val, $year, $coop_id);
            if (!$savings) {
                $savings = 0;
            }

            if (!$loan) {
                $loan = 0;
            } else {
                $loan = $loan->interest - $loan->total_due;
            }

            if (!$credit_sales) {
                $credit_sales = 0;
            } else {
                $credit_sales = $credit_sales->interest - $credit_sales->total_due;
            }

            $data[$m] = $reg_fee + $savings + $loan + $credit_sales;
        }
        return $data;
    }

    public function gen_year_month_savings_graph($year = null, $coop_id = null)
    {
        $months = ['JAN' => '01', 'FEB' => '02', 'MAR' => '03', 'APR' => '04', 'MAY' => '05', 'JUN' => '06', 'JUL' => '07', 'AUG' => '08', 'SEP' => '09', 'OCT' => '10', 'NOV' => '11', 'DEC' => '12'];
        foreach ($months as $m => $val) {
            $savings = $this->savings_by_month_year($val, $year, $coop_id);
            if (!$savings) {
                $data[$m] = 0.00;
            } else {
                $data[$m] = $savings;
            }
        }
        return $data;
    }

    public function gen_year_month_withdrawal_graph($year = null, $coop_id = null)
    {
        $months = ['JAN' => '01', 'FEB' => '02', 'MAR' => '03', 'APR' => '04', 'MAY' => '05', 'JUN' => '06', 'JUL' => '07', 'AUG' => '08', 'SEP' => '09', 'OCT' => '10', 'NOV' => '11', 'DEC' => '12'];
        foreach ($months as $m => $val) {
            $savings = $this->withdrawal_by_month_year($val, $year, $coop_id);
            if (!$savings) {
                $data[$m] = 0.00;
            } else {
                $data[$m] = $savings;
            }
        }
        return $data;
    }

    public function gen_year_month_loan_graph($year = null, $coop_id = null)
    {
        $months = ['JAN' => '01', 'FEB' => '02', 'MAR' => '03', 'APR' => '04', 'MAY' => '05', 'JUN' => '06', 'JUL' => '07', 'AUG' => '08', 'SEP' => '09', 'OCT' => '10', 'NOV' => '11', 'DEC' => '12'];
        foreach ($months as $m => $val) {
            $loan = $this->ignite->info->get_loan_by_month_year($val, $year, $coop_id);
            if (!$loan->total_due) {
                $data[$m] = 0.00;
            } else {
                $data[$m] = $loan->total_due;
            }
        }
        return $data;
    }

    public function subscription_status($coop_id, $total_members = null)
    {
        if ($this->ignite->app_settings->monetization == 'licence') {
            return TRUE;
        }

        $test_unit = 10;
        if (!$total_members) {
            $total_members = $this->ignite->common->count_this('users', ['coop_id' => $coop_id]);
        }
        $total_units = $this->ignite->common->sum_this('subscription', ['coop_id' => $coop_id, 'status' => 'successful'], 'unit');
        $total_units = $test_unit + $total_units->unit;
        if ($total_units > $total_members) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function max_withdrawal_exeed($amount, $balance, $savings_type, $ignore_limit = false)
    {
        if ($ignore_limit == 'on') {
            return false;
        }

        $max_withdrawal = $balance * ($savings_type->max_withdrawal / 100);
        if ($amount > $max_withdrawal) {
            return true;
        } else {
            return false;
        }
    }

    public function max_purchaseable_exeed($amount, $balance, $product_type)
    {
        $max_purchaseable = $balance * ($product_type->max_purchaseable_exeed / 100);
        if ($amount > $max_purchaseable) {
            return $max_purchaseable;
        } else {
            return false;
        }
    }

    public function force_zero($val)
    {
        if ($val <= 0) {
            return 0;
        }
        return $val;
    }

    public function split_repayment_amt($amount, $loan){
        if ($amount == $loan->monthly_due) {
            return (object) [
                'principal_repayment' => $loan->principal_due,
                'interest_repayment' => $loan->interest_due,
                'amount' => $amount,
                'principal_remain' => $this->force_zero($loan->principal_remain - $loan->principal_due),
                'interest_remain' => $this->force_zero($loan->interest_remain - $loan->interest_due),
                'total_remain' => $this->force_zero($loan->total_remain - $amount),
            ];
        } else if ($amount ==  $loan->total_remain) {
            $principal = round(($amount * ($loan->principal / $loan->total_due)), 2, PHP_ROUND_HALF_UP);
            $interest = round(($amount * ($loan->interest / $loan->total_due)), 2, PHP_ROUND_HALF_UP);
            return (object) [
                'principal_repayment' => $principal,
                'interest_repayment' => $interest,
                'amount' => $principal + $interest,
                'principal_remain' => 0,
                'interest_remain' => 0,
                'total_remain' => 0,
            ];
        } else {
            $principal = round(($amount * ($loan->principal / $loan->total_due)), 2, PHP_ROUND_HALF_UP);
            $interest = round(($amount * ($loan->interest / $loan->total_due)), 2, PHP_ROUND_HALF_UP);
            return (object) [
                'principal_repayment' => $principal,
                'interest_repayment' => $interest,
                'amount' => $principal + $interest,
                'principal_remain' => $this->force_zero($loan->principal_remain - $principal),
                'interest_remain' => $this->force_zero($loan->interest_remain - $interest),
                'total_remain' => $this->force_zero($loan->total_remain - ($principal + $interest)),
            ];
        }
    }

    public function split_loan_bal_to_principal_and_interest($balance, $loan)
    {
        $principal = round(($balance * ($loan->principal / $loan->total_due)), 2, PHP_ROUND_HALF_UP);
        $interest = round(($balance * ($loan->interest / $loan->total_due)), 2, PHP_ROUND_HALF_UP);
        return (object) [
            'principal' => $principal,
            'interest' => $interest,
            'balance' => $principal + $interest,
        ];
    }

    public function format_order_details($order_details = false, $product_id = false, $quantity = false)
    {
        if ($product_id and $quantity) {
            $product = $this->ignite->common->get_this('products', ['id' => $product_id]);
            $details[] = (object)[
                'product_id' => $product_id,
                'quantity' => $quantity,
                'total' => $product->price * $quantity,
                'price' => $product->price,
                'product_type_id' => $product->product_type_id,
                'product_name' => $product->name,
                'status' => 'processing'
            ];

            $category_detail = (object)[
                'product_type_id' => $product->product_type_id,
            ];

            return (object)['product_details' => $details, 'category_details' => $category_detail];
        }

        if ($order_details) {
            $order_details = explode('|', $order_details);
            $total_amount = 0;
            foreach ($order_details as $od) {
                if ($od == '') {
                    continue;
                }
                $id = stristr($od, '.', true);
                $quantity = trim(strstr($od, '.'), '.');
                $product = $this->ignite->common->get_this('products', ['id' => $id]);
                $details[] = (object)[
                    'product_id' => $id,
                    'quantity' => $quantity,
                    'total' => $product->price * $quantity,
                    'price' => $product->price,
                    'product_type_id' => $product->product_type_id,
                    'product_name' => $product->name,
                    'status' => 'processing'
                ];

                $total_amount += ($product->price * $quantity);
                $category_detail = (object)[
                    'product_type_id' => $product->product_type_id,
                    'total_amount' => $total_amount,
                ];
            }

            return (object)['product_details' => $details, 'category_details' => $category_detail];
        }
    }

    public function order_details_errors_exists($order)
    {
        $err = false;
        foreach ($order as $od) {
            $number_of_product_cat[$od->product_type_id] = $od->product_type_id;
        }

        //out of stock
        foreach ($order as $od) {
            $product = $this->ignite->common->get_this('products', ['id' => $od->product_id]);
            if ($od->quantity > ($product->stock - $product->sold)) {
                $err .= "<p>" . $product->name . " out of stock  or the quantity is more than the available</p>";
            }
        }

        if (count($number_of_product_cat) > 1) {
            $err = "Cannot select more than one product from the same category category ";
        }

        return $err;
    }

    public function auto_post_to_general_ledger($data, $item_id, $src)
    {
        $auto_post_disabled = $this->ignite->common->get_this('cooperatives', ['id' => $data->coop_id, 'ledger_auto_post' => 'false']);
        if ($auto_post_disabled) {
            return;
        }

        $post = function ($data, $id, $credit_id, $debit_id, $src, $combined_amt = false) {
            $credit_total_credit = $this->ignite->common->sum_this('ledger', ['coop_id' => $data->coop_id, 'credit_id' => $credit_id], 'amount')->amount;
            $credit_total_debit = $this->ignite->common->sum_this('ledger', ['coop_id' => $data->coop_id, 'debit_id' => $credit_id], 'amount')->amount;
            $credit_tatal_bal = $credit_total_credit - $credit_total_debit;

            $debit_total_credit = $this->ignite->common->sum_this('ledger', ['coop_id' => $data->coop_id, 'credit_id' => $debit_id], 'amount')->amount;
            $debit_total_debit = $this->ignite->common->sum_this('ledger', ['coop_id' => $data->coop_id, 'debit_id' => $debit_id], 'amount')->amount;
            $debit_tatal_bal = $debit_total_credit - $debit_total_debit;
            $user = $this->ignite->common->get_this('users', ['id' => $data->user_id]);

            if ($combined_amt) $data->amount = $combined_amt;
            if (empty($data->narration))  $data->narration = "Loan/Credit Salaes";
            if (empty($data->source))  $data->source = 7;
            if (empty($data->payment_date)) $data->payment_date = date("Y-m-d H:i:s");

            $ledger_data = [
                'note' => $data->narration . '. -  ' . $user->first_name . ' ' . $user->last_name,
                'created_by' => $data->user_id,
                'coop_id' => $data->coop_id,
                'mop' => $data->source,
                'pv_no' => $src . $id,
                'credit_id' => $credit_id,
                'debit_id' => $debit_id,
                'amount' => $data->amount,
                'credit_bal' => $credit_tatal_bal + $data->amount,
                'debit_bal' => $debit_tatal_bal - $data->amount,
                'reference' => '',
                'particular' => $user->username,
                'credit_name' => $this->ignite->common->get_this('acc_value', ['id' => $credit_id])->name,
                'debit_name' => $this->ignite->common->get_this('acc_value', ['id' => $debit_id])->name,
                'payment_date' => empty($data->payment_date) ? $data->payment_date : date("Y-m-d H:i:s"),
            ];

            $this->ignite->common->add('ledger', $ledger_data);
        };

        //savings or withdrawal
        if ($src == 'SAV' or $src == 'WIT') {
            ($src == 'SAV') ?  $table = 'gl_savings_tracker' : $table = 'gl_withdrawal_tracker';
            $tracker = $this->ignite->common->get_this($table, ['coop_id' => $data->coop_id, 'savings_type' => $data->savings_type]);
            $this->ignite->common->delete_this('ledger', ['pv_no' => $src . $item_id]);
            $post($data, $item_id, $tracker->cr, $tracker->dr, $src);
        }

        // loan disbursement and credit sales  disbursement
        if ($src == 'LOA' or $src == "CRS") {
            if ($src == 'LOA') {
                $table = 'gl_loan_tracker';
                $where =  ['coop_id' => $data->coop_id, 'loan_type' => $data->loan_type_id];
            } elseif ($src == 'CRS') {
                $table = 'gl_credit_sales_tracker';
                $where =  ['coop_id' => $data->coop_id, 'product_type' => $data->product_type_id];
            }

            $tracker = $this->ignite->common->get_this($table, $where);
            $this->ignite->common->delete_this('ledger', ['pv_no' => $src . $item_id]);
            if ($tracker->principal_cr == $tracker->interest_cr) {
                $post($data, $item_id, $tracker->principal_cr, $tracker->principal_dr, $src, $data->total_due);
            } else {
                $post($data, $item_id, $tracker->principal_cr, $tracker->principal_dr, $src, $data->principal);
                $post($data, $item_id, $tracker->interest_cr, $tracker->interest_dr, $src, $data->interest);
            }
        }

        // loan repayment and credit sales  repayment
        if ($src == 'LOAR' or $src == "CRSR") {
            if ($src == 'LOAR') {
                $table = 'gl_loan_repayment_tracker';
                $where =  ['coop_id' => $data->coop_id, 'loan_type' => $data->loan_type_id];
            } elseif ($src == 'CRSR') {
                $table = 'gl_credit_sales_repayment_tracker';
                $where =  ['coop_id' => $data->coop_id, 'product_type' => $data->product_type_id];
            }

            $tracker = $this->ignite->common->get_this($table, $where);
            $this->ignite->common->delete_this('ledger', ['pv_no' => $src . $item_id]);
            if ($tracker->principal_cr == $tracker->interest_cr) {
                $post($data, $item_id, $tracker->principal_cr, $tracker->principal_dr, $src, $data->amount);
            } else {
                $post($data, $item_id, $tracker->principal_cr, $tracker->principal_dr, $src, $data->principal_repayment);
                $post($data, $item_id, $tracker->interest_cr, $tracker->interest_dr, $src, $data->interest_repayment);
            }
        }
    }

    public function repayment_shedule_generator($loan)
    {
        $loan_type = $this->ignite->common->get_this('loan_types', ['coop_id' => $loan->coop_id, 'id' => $loan->loan_type_id]);
        if ($loan_type->calc_method == 3) { //reducing bal
            $principal = $loan->principal;
            for ($i = 0; $i <= $loan->tenure; $i++) {
                $interest_due = round(($principal * ($loan->rate / 100) / 12), 2, PHP_ROUND_HALF_UP);
                $schedule[] = (object)[
                    'month' => ($i == 0) ? '0' : $i,
                    'monthly_due' => ($i == 0) ? '0' : $loan->monthly_due,
                    'interest_due' => ($i == 0) ? '0' : $interest_due,
                    'principal_due' => ($i == 0) ? '0' :  $this->force_zero($loan->monthly_due - $interest_due),
                    'balance' => ($i == 0) ?  $loan->principal :  $this->force_zero($principal - ($loan->monthly_due - $interest_due)),
                ];

                if ($i > 0) {
                    $principal = $principal - ($loan->monthly_due - $interest_due);
                }
            }
        }

        if ($loan_type->calc_method == 2) { //simple interest
            $principal = $loan->principal;
            $interest_due = round(($principal * ($loan->rate / 100) / 12), 2, PHP_ROUND_HALF_UP);
            $principal_due = $loan->monthly_due - $interest_due;
            for ($i = 0; $i <= $loan->tenure; $i++) {
                $schedule[] = (object)[
                    'month' => ($i == 0) ? '0' : $i,
                    'monthly_due' => ($i == 0) ? '0' : $loan->monthly_due,
                    'interest_due' => ($i == 0) ? '0' : $interest_due,
                    'principal_due' => ($i == 0) ? '0' :  $principal_due,
                    'balance' => ($i == 0) ?  $loan->principal :  $this->force_zero($principal - $principal_due),
                ];

                if ($i > 0) {
                    $principal = $principal - $principal_due;
                }
            }
        }

        if ($loan_type->calc_method == 1) { //flat rate
            $principal = $loan->principal;
            $interest_due = round(($principal * ($loan->rate / 100) / 12), 2, PHP_ROUND_HALF_UP);
            $principal_due = $loan->monthly_due - $interest_due;
            for ($i = 0; $i <= $loan->tenure; $i++) {
                $schedule[] = (object)[
                    'month' => ($i == 0) ? '0' : $i,
                    'monthly_due' => ($i == 0) ? '0' : $loan->monthly_due,
                    'interest_due' => ($i == 0) ? '0' : $interest_due,
                    'principal_due' => ($i == 0) ? '0' :  $principal_due,
                    'balance' => ($i == 0) ?  $loan->principal :  $this->force_zero($principal - $loan->monthly_due),
                ];

                if ($i > 0) {
                    $principal = $principal - $loan->monthly_due;
                }
            }
        }

        return $schedule;
    }

    public function get_requestable($saving_bal, $percentage)
    {
        return ($percentage / 100) * $saving_bal;
    }

    public function activities_matadata($previous_data, $new_data)
    {

        foreach ($new_data as $key => $new) {
            if (array_key_exists($key, $previous_data)) {
                $previous[$key] = $previous_data->$key;
            }
        }
        return json_encode([
            'previous_data' => (!$new_data) ? $previous_data : $previous,
            'new_data' => $new_data,
        ]);
    }

    public function get_dividend($savings_profit, $loan_profit, $credit_sales_profit)
    {
        $users = $this->ignite->common->get_all_these('users', ['coop_id', $this->ignite->coop->id]);
        $total_savings = (float)$this->get_savings_bal(null, $this->ignite->coop->id);
        $total_loan_interest = (float)$this->ignite->common->sum_this('loans', ['coop_id' => $this->ignite->coop->id], 'interest')->interest;
        $credit_sales_interest = (float)$this->ignite->common->sum_this('credit_sales', ['coop_id' => $this->ignite->coop->id], 'interest')->interest;
        foreach ($users as $u) {
            $sb = $this->get_savings_bal($u->id, $this->ignite->coop->id);
            $li = $this->ignite->common->sum_this('loans', ['user_id' => $u->id], 'interest')->interest;
            $cs = $this->ignite->common->sum_this('credit_sales', ['user_id' => $u->id], 'interest')->interest;

            $savings_dividend = 0;
            $loan_dividend = 0;
            $credit_sales_dividend = 0;
            if ($total_savings) {
                $savings_dividend = ($sb / $total_savings) * $savings_profit;
            }
            if ($total_loan_interest) {
                $loan_dividend = ($li / $total_loan_interest) * $loan_profit;
            }
            if ($credit_sales_interest) {
                $credit_sales_dividend = ($cs / $credit_sales_interest) * $credit_sales_profit;
            }

            $data[] = (object)[
                'member_id' => $u->username,
                'first_name' => $u->first_name,
                'last_name' => $u->last_name,
                'savings_bal' => $sb,
                'savings_dividend' => $savings_dividend,
                'loan_interest' => $li,
                'loan_dividend' => $loan_dividend,
                'credit_sales_interest' => $credit_sales_interest,
                'credit_sales_dividend' => $credit_sales_dividend,
                'total_dividend' => $savings_dividend + $loan_dividend + $credit_sales_dividend
            ];
        }
        return $data;
    }

    public function credit_worthines($coop_id, $user_id)
    {
        $savings_bal = $this->get_savings_bal($user_id, $coop_id);
        $loan_bal = $this->get_loan_bal($user_id, $coop_id);
        $credit_sales_bal = $this->get_credit_sales_bal($user_id, $coop_id);
        $liquidity = $savings_bal - ($credit_sales_bal - $loan_bal);

        if ($liquidity < 0) {
            return 0.5;
        }
        if ($liquidity == 0) {
            return 1.5;
        }

        if ($liquidity  > 0) {
            return 4.5;
        }
    }

    public function get_months_between($start_date, $end_date, $additional = 0)
    {
        if (!$start_date or !$end_date) {
            return 0;
        }
        $d1 = new DateTime($end_date);
        $d2 = new DateTime($start_date);
        $months = $d2->diff($d1);
        if ((($months->y) * 12) + ($months->m) == 0) {
            return $additional;
        }
        return (($months->y) * 12) + ($months->m);
    }
}
