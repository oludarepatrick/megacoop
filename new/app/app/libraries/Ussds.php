<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ussds {
    public function __construct() {
        $this->ignite = & get_instance();
       
    }

    public function savings_balance($user){
        $response = "Savings Balances: \n";
        $coop = $this->ignite->common->get_this('cooperatives', ['id'=>$user->coop_id]);
        $savings_type = $this->ignite->common->get_all_these('savings_types', ['coop_id' => $coop->id]);

        foreach($savings_type as $s){

            $bal= $this->ignite->utility->get_savings_bal($user->id, $coop->id, $s->id);
            $response .= $s->name . ":  " . number_format($bal,2). " \n";
        }

        if ($coop->sms_notice == 'on') {
            $this->ignite->fivelinks->send_SMS($user->phone, $response, $coop);
        }

        return $response;
        
    }

    public function wallet_balance($user){
        $response = "Wallet Balance: \n";
        $coop = $this->ignite->common->get_this('cooperatives', ['id'=>$user->coop_id]);

        $bal= $this->ignite->utility->get_wallet_bal($user->id, $coop->id);
        $response .= number_format($bal,2);

        if ($coop->sms_notice == 'on') {
            $this->ignite->fivelinks->send_SMS($user->phone, $response, $coop);
        }
        return $response;
    }

    public function loan_balance($user){
        $response = "Loan Balances: \n";
        $coop = $this->ignite->common->get_this('cooperatives', ['id' => $user->coop_id]);
        $loan_types = $this->ignite->common->get_all_these('loan_types', ['coop_id' => $coop->id]);

        foreach ($loan_types as $s) {
            $bal = $this->ignite->utility->get_loan_bal($user->id, $coop->id, $s->id);
            $response .= $s->name . ":  " . number_format($bal, 2) . " \n";
        }

        if ($coop->sms_notice == 'on') {
            $this->ignite->fivelinks->send_SMS($user->phone, $response, $coop);
        }

        return $response;
    }

    public function credit_sales_balance($user){
        $response = "Credit Sales Balances: \n";
        $coop = $this->ignite->common->get_this('cooperatives', ['id' => $user->coop_id]);
        $loan_types = $this->ignite->common->get_all_these('product_types', ['coop_id' => $coop->id]);

        foreach ($loan_types as $s) {
            $bal = $this->ignite->utility->get_credit_sales_bal($user->id, $coop->id, $s->id);
            $response .= $s->name . ":  " . number_format($bal, 2) . " \n";
        }

        if ($coop->sms_notice == 'on') {
            $this->ignite->fivelinks->send_SMS($user->phone, $response, $coop);
        }

        return $response;
    }
    
    public function add_savings($user, $amount, $type){
        $response = "Add Savings: \n";
        // $this->app_settings = $this->ignite->common->get_this('app_settings', ['id' => 1]);
        $coop = $this->ignite->common->get_this('cooperatives', ['id' => $user->coop_id]);
        $savings_type = $this->resolve_type('savings', $coop, $type);
        $last_savings = $this->ignite->common->get_limit('savings', ['user_id' => $user->id, 'savings_type' => $savings_type->id, 'coop_id' => $coop->id], 1, 'id', 'DESC');
        
        $month_id = date('n');
        $year = date('Y');

        if($last_savings){
            $month_id = ($last_savings[0]->month_id + 1 == 13) ? 1 : $last_savings[0]->month_id + 1;
            $year = ($last_savings[0]->month_id + 1 == 13) ? $last_savings[0]->year + 1 : $last_savings[0]->year;
        }

        $month_name = $this->ignite->common->get_this('months', ['id' => $month_id])->name;
        
        $source = 1; //wallet

        if ($amount <= 0) {
            return $response .= "Invalid amount";
        }

        //source == user wallet
        if ($source == 1) {
            $wallet_bal = $this->ignite->utility->get_wallet_bal($user->id, $coop->id);
            if ($amount > $wallet_bal or $amount <= 0) {
                return $response .= "Low wallet balance! Kindly load your wallet and retry.";
            }

            $wallet = [
                'tranx_ref' => $user->id . 'WAL' . date('Ymdhis'),
                'referrer_code' => $coop->referrer_code,
                'coop_id' => $coop->id,
                'user_id' => $user->id,
                'amount' => $amount,
                'tranx_type' => 'debit',
                'narration' => $month_name ." ". $year ." Savings",
                'gate_way_id' => 0,
                'status' => 'successful',
            ];
        } else {
            $wallet['tranx_ref'] = $user->id . 'DEF' . date('Ymdhis');
        }

        $savings = [
            'tranx_ref' => $wallet['tranx_ref'],
            'tranx_type' => 'credit',
            'referrer_code' => $coop->referrer_code,
            'coop_id' => $coop->id,
            'user_id' => $user->id,
            'balance' => $this->ignite->utility->get_savings_bal($user->id, $coop->id, $savings_type->id) + $amount,
            'amount' => $amount,
            'month_id' => $month_id,
            'year' => $year,
            'savings_type' => $savings_type->id,
            'source' => $source,
            'narration' => $month_name ." ". $year ." Savings",
            'status' => 'paid',
            'payment_date' => date('Y-m-d H:i:s'),
            'score' => 5
        ];
        $this->ignite->common->start_trans();
        if ($source == 1) {
            $this->ignite->common->add('wallet', $wallet);
        }
        $item_id = $this->ignite->common->add('savings', $savings);
        $this->ignite->utility->auto_post_to_general_ledger((object)$savings, $item_id, "SAV");
        $this->ignite->common->finish_trans();
        if ($this->ignite->common->status_trans()) {
            $savings_type_name = $this->ignite->common->get_this('savings_types', [
                'coop_id' => $coop->id, 'id' =>  $savings_type->id
            ])->name;

            if ($coop->sms_notice == 'on') {
                $content = "Cedit Alert"
                . "\n" . "ST: " . $savings_type_name
                    . "\n" . "ID: " . $this->ignite->utility->shortend_str_len($user->username, 5, '***')
                    . "\n" . "DATE: " . $this->ignite->utility->just_date(date('Y-m-d H:i:s'), true)
                    . "\n" . "AMT: NGN" . number_format($amount, 2)
                    . "\n" . "Av.BAL: NGN" . number_format($savings['balance'], 2);
                $this->ignite->fivelinks->send_SMS($user->phone, $content, $coop);
            }
            return $response .="Savings Successfully Added!";
        } else {
            return $response .= "Savings not Successful";
        }
    }

    public function repay_loan($user, $amount, $type){
        $response = "Loan Repayment \n";
        $coop = $this->ignite->common->get_this('cooperatives', ['id' => $user->coop_id]);
        $loan_type = $this->resolve_type('loan', $coop, $type);
        $loan = $this->ignite->common->get_this('loans',['user_id'=>$user->id, 'loan_type_id'=> $loan_type->id, 'status'=>'disbursed']);


        if ($amount <= 0) {
            return $response .= "Invalid amount";
        }

        if ($amount > $loan->total_remain) {
            return $response .= "Repayment amount cannot be greater than balance";
        }

        $source = 1;
        //source == member wallet
        if ($source == 1) {
            $wallet_bal = $this->ignite->utility->get_wallet_bal($user->id, $coop->id);
            if ($amount > $wallet_bal) {
                return $response .= "Low wallet balance! Kindly load your wallet and retry.";
            }

            $wallet = [
                'tranx_ref' => $user->id . 'WAL' . date('Ymdhis'),
                'referrer_code' => $coop->referrer_code,
                'coop_id' => $coop->id,
                'user_id' => $user->id,
                'amount' => $amount,
                'tranx_type' => 'debit',
                'gate_way_id' => 0,
                'narration' => "Loan Repayment",
                'status' => 'successful',
            ];
        } else {
            $wallet['tranx_ref'] = $user->id . 'DEF' . date('Ymdhis');
        }

        $splited_amount = $this->ignite->utility->split_repayment_amt($amount, $loan);

        $loan_data = [
            'principal_remain' => $splited_amount->principal_remain,
            'interest_remain' => $splited_amount->interest_remain,
            'total_remain' => $splited_amount->total_remain,
            'next_payment_date' => $this->ignite->utility->get_end_date(date('Y-m-d H:i:s'), $plus_a_month = 1, true),
        ];

        if ($splited_amount->total_remain == 0 && $splited_amount->principal_remain == 0 && $splited_amount->interest_remain == 0) {
            $loan_data['status'] = 'finished';
        }

        $loan_repayment_data = [
            'referrer_code' => $coop->referrer_code,
            'coop_id' => $coop->id,
            'tranx_ref' => $wallet['tranx_ref'],
            'user_id' => $user->id,
            'loan_id' => $loan->id,
            'loan_type_id' => $loan->loan_type_id,
            'principal_repayment' => $splited_amount->principal_repayment,
            'interest_repayment' => $splited_amount->interest_repayment,
            'amount' => $splited_amount->amount,
            'principal_remain' => $splited_amount->principal_remain,
            'interest_remain' => $splited_amount->interest_remain,
            'amount_remain' => $splited_amount->total_remain,
            'month_id' => date('n'),
            'year' => date('Y'),
            'source' => $source,
            'narration' => date('n') . ' ' . date('Y') . ' Loan Repayment',
            'created_by' => $user->id,
            'created_on' => date('Y-m-d H:i:s'),
            'status' => 'paid',
        ];
        $this->ignite->common->start_trans();
        if ($source == 1) {
            $this->ignite->common->add('wallet', $wallet);
        }

        $item_id = $this->ignite->common->add('loan_repayment', $loan_repayment_data);
        $this->ignite->utility->auto_post_to_general_ledger((object)$loan_repayment_data, $item_id, 'LOAR');
        $this->ignite->common->update_this('loans', ['id' => $loan->id], $loan_data);
        $this->ignite->common->finish_trans();

        if ($this->ignite->common->status_trans()) {
            $loan_type_name = $this->ignite->common->get_this('loan_types', [
                'coop_id' => $coop->id, 'id' =>  $loan_type->id
            ])->name;

            if ($coop->sms_notice == 'on') {
                $content = "Loan Repayment"
                . "\n" . "ST: " . $loan_type_name
                    . "\n" . "ID: " . $this->ignite->utility->shortend_str_len($user->username, 5, '***')
                    . "\n" . "DATE: " . $this->ignite->utility->just_date(date('Y-m-d H:i:s'), true)
                    . "\n" . "AMT: NGN" . number_format($amount, 2)
                    . "\n" . "LOAN.BAL: NGN" . number_format($splited_amount->total_remain, 2);
                $this->ignite->fivelinks->send_SMS($user->phone, $content, $coop);
            }
            return $response .= "Loan Repayment Successful";
        } else {
            return $response .= "Loan Repayment not Successful! Please retry";
        }
    }

    public function repay_credit_sales($user, $amount, $type) {
        $response = "Credit Sales Repayment \n";
        $coop = $this->ignite->common->get_this('cooperatives', ['id' => $user->coop_id]);
        $credit_sales_type = $this->resolve_type('credit_sales', $coop, $type);

        $credit_sales = $this->ignite->common->get_this('credit_sales', ['user_id' => $user->id, 'product_type_id' => $credit_sales_type->id, 'status' => 'disbursed']);

        if ($amount <= 0) {
            return $response .= "Invalid Amount";
        }
        if ($amount > $credit_sales->total_remain) {
            return $response .= "Repayment amount cannot be greater than balance";
        }

        $source = 1;
        //source == member wallet
        if ($source == 1) {
            $wallet_bal = $this->ignite->utility->get_wallet_bal($user->id, $coop->id);
            if ($amount > $wallet_bal) {
                return $response .= "Low wallet balance! Kindly load your wallet and retry.";
            }

            $wallet = [
                'tranx_ref' => $user->id . 'WAL' . date('Ymdhis'),
                'referrer_code' => $coop->referrer_code,
                'coop_id' => $coop->id,
                'user_id' => $user->id,
                'amount' => $amount,
                'tranx_type' => 'debit',
                'gate_way_id' => 0,
                'narration' => "Credit Sales Repayment",
                'status' => 'successful',
            ];
        } else {
            $wallet['tranx_ref'] = $user->id . 'DEF' . date('Ymdhis');
        }
        $splited_amount = $this->ignite->utility->split_repayment_amt($amount, $credit_sales);

        $credit_sales_data = [
            'principal_remain' => $splited_amount->principal_remain,
            'interest_remain' => $splited_amount->interest_remain,
            'total_remain' => $splited_amount->total_remain,
            'next_payment_date' => $this->ignite->utility->get_end_date(date('Y-m-d H:i:s'), $plus_a_month = 1, true),
        ];
        
        if ($splited_amount->total_remain == 0 && $splited_amount->principal_remain == 0 && $splited_amount->interest_remain == 0) {
            $credit_sales_data['status'] = 'finished';
        }

        $credit_sales_repayment_data = [
            'referrer_code' => $coop->referrer_code,
            'coop_id' => $coop->id,
            'tranx_ref' => $wallet['tranx_ref'],
            'user_id' => $user->id,
            'credit_sales_id' => $credit_sales->id,
            'product_type_id' => $credit_sales->product_type_id,
            'principal_repayment' => $splited_amount->principal_repayment,
            'interest_repayment' => $splited_amount->interest_repayment,
            'amount' => $splited_amount->amount,
            'principal_remain' => $splited_amount->principal_remain,
            'interest_remain' => $splited_amount->interest_remain,
            'amount_remain' => $splited_amount->total_remain,
            'month_id' => date('n'),
            'year' => date('Y'),
            'source' => $source,
            'narration' => date('n') . ' ' . date('Y') . ' Credit Sales Repayment',
            'created_by' => $user->id,
            'created_on' => date('Y-m-d H:i:s'),
            'status' => 'paid',
        ];

        $this->ignite->common->start_trans();
        if ($source == 1) {
            $this->ignite->common->add('wallet', $wallet);
        }
        $item_id = $this->ignite->common->add('credit_sales_repayment', $credit_sales_repayment_data);
        $this->ignite->utility->auto_post_to_general_ledger((object)$credit_sales_repayment_data, $item_id, 'CRSR');
        $this->ignite->common->update_this('credit_sales', ['id' => $credit_sales->id], $credit_sales_data);
        $this->ignite->common->finish_trans();

        if ($this->ignite->common->status_trans()) {
            $credit_sales_name = $this->ignite->common->get_this('product_types', [
                'coop_id' => $coop->id, 'id' =>  $credit_sales->product_type_id
            ])->name;

            if ($coop->sms_notice == 'on') {
                $content = "Credit Sales Repayment"
                    . "\n" . "TYPE: " . $credit_sales_name
                    . "\n" . "ID: " . $this->ignite->utility->shortend_str_len($user->username, 5, '***')
                    . "\n" . "DATE: " . $this->ignite->utility->just_date(date('Y-m-d H:i:s'), true)
                    . "\n" . "AMT: NGN" . number_format($amount, 2)
                    . "\n" . "Credit Sales.BAL: NGN" . number_format($splited_amount->total_remain, 2);
                $this->ignite->fivelinks->send_SMS($user->phone, $content, $coop);
            }
            return $response .= "Credit Sales Repayment Successful";
        } else {
            return $response .= "Credit Sales Repayment not Successful! Please retry";
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function load_wallet($user, $amount, $type) {
        $tranx_ref = "WALL".$user->id.date('dYmHis');
        $response = "Wallet Loading \n";
        $coop = $this->ignite->common->get_this('cooperatives', ['id' => $user->coop_id]);
        
        $wallet_data = [
            'coop_id'=>$coop->id,
            'user_id'=> $user->id,
            'tranx_ref'=> $tranx_ref,
            'referrer_code'=>$coop->referrer_code,
            'amount'=> $amount,
            'tranx_type'=> 'credit',
            'gate_way_id'=> 3,
            'narration'=> 'Load Wallet',
            'status'=> 'processing',
            'created_on'=> date('Y-m-d H:i:s'),
        ];
        if($type == '1'){
            if ($this->ignite->common->add('wallet', $wallet_data)) {
                if ($coop->sms_notice == 'on') {
                    $content = "Pending Member Wallet Loading"
                    . "\n" . "Use the REF as your narration"
                    . "\n" . "REF: " . $tranx_ref
                    . "\n" . "DATE: " . $this->ignite->utility->just_date(date('Y-m-d H:i:s'), true)
                    . "\n" . "AMT: NGN" . number_format($amount, 2);
                    $this->ignite->fivelinks->send_SMS($user->phone, $content, $coop);
                }
                $response .= "Wallet Loading processing \n";
                $response .= "Use this refrence as your narration: \n";
                return $response .= $tranx_ref;
            } else {
                return $response .= "Wallet Loading not Successful!";
            }
        }elseif($type == '2'){
            if ($this->ignite->common->add('agent_wallet', $wallet_data)) {
                if ($coop->sms_notice == 'on') {
                    $content = "Pending Agent Wallet Loading"
                    . "\n" . "Use the REF as your narration"
                    . "\n" . "REF: " . $tranx_ref
                    . "\n" . "DATE: " . $this->ignite->utility->just_date(date('Y-m-d H:i:s'), true)
                    . "\n" . "AMT: NGN" . number_format($amount, 2);
                    $this->ignite->fivelinks->send_SMS($user->phone, $content, $coop);
                }
                $response .= "Wallet Loading processing \n";
                $response .= "Use this refrence as your narration: \n";
                return $response .= $tranx_ref;
            } else {
                return $response .= "Wallet Loading not Successful!";
            }
        }
    }

    private function resolve_type($type, $coop, $input){
        $input -= 1; 
        if($type == 'savings'){
            $type = $this->ignite->common->get_all_these('savings_types', ['coop_id' => $coop->id]);
        }
        if($type == 'loan'){
            $type = $this->ignite->common->get_all_these('loan_types', ['coop_id' => $coop->id]);
        }

        if($type == 'credit_sales'){
            $type = $this->ignite->common->get_all_these('product_types', ['coop_id' => $coop->id]);
        }
        return $type[$input];

    }

}
