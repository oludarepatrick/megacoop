<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Auth
 * @property Ion_auth|Ion_auth_model $ion_auth        The ION Auth spark
 * @property CI_Form_validation      $form_validation The form validation library
 */
class Ussd extends CI_Controller {
    const USSD_LEVEL_PATH = "assets/ussd/";
    const DEFAULT_PASS = 123456;

    public function __construct() {
        parent::__construct();
    }

    private function get_level($msisdn, $userdata){
        if (file_exists(self::USSD_LEVEL_PATH . $msisdn. '.ussd')) {
            $level = file_get_contents(self::USSD_LEVEL_PATH . $msisdn. '.ussd');
            if ($level) {
                return $level . "*" . $userdata;
            }
        }
        return $userdata;
    }

    private function in_current_sub_level($level, $prifix, $count){
        if (substr($level, 0, 2) == $prifix and substr_count($level, "*") == $count) {
            return true;
        }
        return false;
    }

    private function set_level($msisdn, $level){
        return file_put_contents(self::USSD_LEVEL_PATH . $msisdn . '.ussd', $level);
    }

    private function unset_level($msisdn){
        if(file_exists(self::USSD_LEVEL_PATH.$msisdn. '.ussd')){
            unlink(self::USSD_LEVEL_PATH . $msisdn.'.ussd');
        }
    }

    private function extract_data($data , $index){
        return explode("*" ,$data)[$index];
    }

    private function get_user($msisdn){
        $phone  = '0'.ltrim($msisdn, "234");
        $user = $this->common->get_this('users',['phone'=>$phone]);

        if($user){
            return $user;
        }
        return false;
    }

    private function validate_tranx_pin ($user, $tranx_pin){
        if($user->tranx_pin == hash('sha256', $tranx_pin)){
            return true;
        }
        return false;
    }

    private function response($response, $endofsesssion, $msisdn, $network, $sessionid){
        header('Content-type: application/json');
        echo json_encode([
            'userdata' => $response,
            'endofsession' => $endofsesssion,
            'msisdn' => $msisdn,
            'network' => $network,
            'sessionid' => $sessionid
        ]);
    }


    public function index() {
        $base_string = "*5075*9";
        $msisdn   = $this->input->get("msisdn");
        $network = $this->input->get("network");
        $endofsesssion = $this->input->get("endofsesssion");
        $userdata        = $this->input->get("userdata");
        $sessionid        = $this->input->get("sessionid");
        $user  =$this->get_user($msisdn);

        ###########################################################################
         #                            INITIALIZATION                             #
        ###########################################################################
        if ($userdata == $base_string or $userdata == "565") {
            $this->unset_level($msisdn);
            if(!$user){
                $response = "Record not found! \n";
                $response .= "You can only user a phone number registered on this system.";
                $endofsesssion = true;
            }else{
                $response  = "Welcome to Cle \n";
                $response .= "1. Check Balance \n";
                $response .= "2. Add Savings \n";
                $response .= "3. Repay Loan \n";
                $response .= "4. Repay Credit Sale \n";
                $response .= "5. Load Wallet \n";
            }
        }
        
        ###########################################################################
         #                           BALANCE CHECKER                             #
        ###########################################################################
        if ($this->get_level($msisdn, $userdata) == "1") {
            $this->set_level($msisdn, $userdata);
            $response  = "Select an option \n";
            $response .= "1. Savings Balance \n";
            $response .= "2. Loan Balance \n";
            $response .= "3. Credit Sales Balance \n";
            $response .= "4. Wallet Balance \n";
           
        } else if ($this->in_current_sub_level($this->get_level($msisdn, $userdata), "1*", 1)) {
            $level = $this->get_level($msisdn, $userdata);
            $this->set_level($msisdn, $level);
            $response  = "Enter 6-digit PIN \n";

        } else if ($this->in_current_sub_level($this->get_level($msisdn, $userdata), "1*", 2)) {
            $type = $this->extract_data($this->get_level($msisdn, $userdata), 1);
            $amount = $this->extract_data($this->get_level($msisdn, $userdata), 2);

            if ($this->validate_tranx_pin($user, $userdata)) {

                if ($type == "1") {
                    $response = $this->ussds->savings_balance($user);
                    $endofsesssion = true;
                } else if ($type == "2") {
                    $response = $this->ussds->loan_balance($user);
                    $endofsesssion = true;
                } else if ($type == "3") {
                    $response = $this->ussds->credit_sales_balance($user);
                    $endofsesssion = true;
                } else if ($type == "4") {
                    $response = $this->ussds->wallet_balance($user);
                    $endofsesssion = true;
                }
                $endofsesssion = true;
            } else {
                $response  = "Invalid PIN. Please re-try \n";
                $response  .= "Enter 6-digit PIN \n";
            }
        }

        ###########################################################################
         #                               Add SAVINGS                             #
        ###########################################################################

        if ($this->get_level($msisdn, $userdata) == "2") {
            $this->set_level($msisdn, $userdata);
            $savings_type = $this->common->get_all_these('savings_types', ['coop_id' => $user->coop_id]);
            $response  = "Select an option \n";
            $index = 1;
            foreach($savings_type as $s){
                $response .=$index. ". ". $s->name." \n";
                $index++;
            }

        } else if ($this->in_current_sub_level($this->get_level($msisdn, $userdata), "2*", 1)) {
            $level = $this->get_level($msisdn, $userdata);
            $this->set_level($msisdn, $level);
            $response  = "Enter Amount: \n";
            
        }else if($this->in_current_sub_level($this->get_level($msisdn, $userdata), "2*", 2)) {
            $level = $this->get_level($msisdn, $userdata);
            $this->set_level($msisdn, $level);
            $response  = "Enter 6-digit PIN \n";

        }else if($this->in_current_sub_level($this->get_level($msisdn, $userdata), "2*", 3)) {
            $type = $this->extract_data($this->get_level($msisdn, $userdata), 1);
            $amount = $this->extract_data($this->get_level($msisdn, $userdata), 2);
            if($this->validate_tranx_pin($user, $userdata)){
                $response = $this->ussds->add_savings($user, $amount, $type);
                $endofsesssion = true;
            }else{
                $response  = "Invalid PIN. Please re-try \n";
                $response  .= "Enter 6-digit PIN \n";
            }
            
        }

        ###########################################################################
         #                            LOAN REPAYMENT                             #
        ###########################################################################
        if ($this->get_level($msisdn, $userdata) == "3") {
            $this->set_level($msisdn, $userdata);
            $user_loans = $this->info->get_loans(['loans.user_id'=>$user->id, 'loans.status'=>'disbursed']);
            $loan_type = $this->common->get_all_these('loan_types', ['coop_id' => $user->coop_id]);
            $this->set_level($msisdn, $userdata);

            $response  = "Select an option  \n";
            if($user_loans){
                $index = 1;
                foreach ($loan_type as $s) {
                    $response .= $index . ". " . $s-> name . " \n";
                    $index++;
                }
            }else{
                $response = 'You currently do not have an active loan';
                $endofsesssion = true;
            }
            
        } else if ($this->in_current_sub_level($this->get_level($msisdn, $userdata), "3*", 1)) {
            $level = $this->get_level($msisdn, $userdata);
            $this->set_level($msisdn, $level);
            $response  = "Enter Amount: \n";

        } else if ($this->in_current_sub_level($this->get_level($msisdn, $userdata), "3*", 2)) {
            $level = $this->get_level($msisdn, $userdata);
            $this->set_level($msisdn, $level);
            $response  = "Enter 6-digit PIN \n";

        } else if ($this->in_current_sub_level($this->get_level($msisdn, $userdata), "3*", 3)) {
            $type = $this->extract_data($this->get_level($msisdn, $userdata), 1);
            $amount = $this->extract_data($this->get_level($msisdn, $userdata), 2);

            if ($this->validate_tranx_pin($user, $userdata)) {
                $response = $this->ussds->repay_loan($user, $amount, $type);
                $endofsesssion = true;
            } else {
                $response  = "Invalid PIN. Please re-try \n";
                $response  .= "Enter 6-digit PIN \n";
            }
        }

        ###########################################################################
        #                        CREDIT SALES REPAYMENT                          #
        ###########################################################################
        if ($this->get_level($msisdn, $userdata) == "4") {
            $this->set_level($msisdn, $userdata);
            $user_loans = $this->info->get_credit_sales(['credit_sales.user_id' => $user->id, 'credit_sales.status' => 'disbursed']);
            $loan_type = $this->common->get_all_these('product_types', ['coop_id' => $user->coop_id]);
            $response  = "Select an option  \n";
            if ($user_loans) {
                $index = 1;
                foreach ($loan_type as $s) {
                    $response .= $index . ". " . $s-> name . " \n";
                    $index++;
                }
            } else {
                $response = 'You currently do not have an active credit sales';
                $endofsesssion = true;
            }
        } else if ($this->in_current_sub_level($this->get_level($msisdn, $userdata), "4*", 1)) {
            $level = $this->get_level($msisdn, $userdata);
            $this->set_level($msisdn, $level);
            $response  = "Enter Amount: \n";

        } else if ($this->in_current_sub_level($this->get_level($msisdn, $userdata), "4*", 2)) {
            $level = $this->get_level($msisdn, $userdata);
            $this->set_level($msisdn, $level);
            $response  = "Enter 6-digit PIN \n";

        } else if ($this->in_current_sub_level($this->get_level($msisdn, $userdata), "4*", 3)) {
            $type = $this->extract_data($this->get_level($msisdn, $userdata), 1);
            $amount = $this->extract_data($this->get_level($msisdn, $userdata), 2);

            if ($this->validate_tranx_pin($user, $userdata)) {
                $response = $this->ussds->repay_credit_sales($user, $amount, $type);
                $endofsesssion = true;
            } else {
                $response  = "Invalid PIN. Please re-try \n";
                $response  .= "Enter 6-digit PIN \n";
            }
        }


        ###########################################################################
        #                                 WALLET LOADING                          #
        ###########################################################################
        if ($this->get_level($msisdn, $userdata) == "5") {
            $this->set_level($msisdn, $userdata);
            $response  = "Select an option  \n";
            $response  .= "1. Member Wallet  \n";
            $response  .= "2. Agent Wallet  \n";
            
        } else if ($this->in_current_sub_level($this->get_level($msisdn, $userdata), "5*", 1)) {
            $level = $this->get_level($msisdn, $userdata);
            $this->set_level($msisdn, $level);
            $response  = "Enter Amount: \n";

        } else if ($this->in_current_sub_level($this->get_level($msisdn, $userdata), "5*", 2)) {
            $level = $this->get_level($msisdn, $userdata);
            $this->set_level($msisdn, $level);
            $response  = "Enter 6-digit PIN \n";

        } else if ($this->in_current_sub_level($this->get_level($msisdn, $userdata), "5*", 3)) {
            $type = $this->extract_data($this->get_level($msisdn, $userdata), 1);
            $amount = $this->extract_data($this->get_level($msisdn, $userdata), 2);

            if ($this->validate_tranx_pin($user, $userdata)) {
                $response = $this->ussds->load_wallet($user, $amount, $type);
                $endofsesssion = true;
            } else {
                $response  = "Invalid PIN. Please re-try \n";
                $response  .= "Enter 6-digit PIN \n";
            }
        }

        if(!is_numeric($userdata) and $userdata != $base_string){
            $this->unset_level($msisdn);
            $this->response("Invalid Selection", true, $msisdn, $network, $sessionid);
            
        }else{
            $this->response($response, $endofsesssion, $msisdn, $network, $sessionid);
        }
       
    }

    
}
