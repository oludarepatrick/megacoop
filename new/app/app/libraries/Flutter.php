<?php 
class Flutter{

    const MAX_CHARGES = 2000;
    const CHARGES_ON_MAX_CHAGES = 30;
    const FLAT_CHARGS = 0;
    const RATE = 0.014;

    public function __construct() {
        $this->ignite = & get_instance();
        // $this->ignite->load->model('common_model');
    }

    public function get_amount_flutter($amount) {
        if (!$amount or empty($amount)) {
            return false;
        }

        $flutter_charges = ($amount * self::RATE);

        if ($amount < 2000) {
            $amount_topay = $amount + $flutter_charges;
            return round($amount_topay + self::CHARGES_ON_MAX_CHAGES);
        } elseif ($flutter_charges >= self::MAX_CHARGES) {
            $amount_topay = $amount + self::MAX_CHARGES;
            return $amount_topay;
        } else {
            $amount_topay = $amount + $flutter_charges + self::CHARGES_ON_MAX_CHAGES + self::FLAT_CHARGS;
            return round($amount_topay);
        }
    }

    public function initialize_flutter($post_data, $flutter_private) {
        $result = array();
        $url = "https://api.flutterwave.com/v3/payments";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));  //Post Fields
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            "Authorization: Bearer {$flutter_private}",
            'Content-Type: application/json',
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //should bo removed before taking the app to live

        $response = curl_exec($ch);
        // var_dump($response);exit;
        curl_close($ch);
        if ($response) {
            $result = json_decode($response, true);
            
            if ($result['status'] == 'success') {
                header('Location: ' . $result['data']['link']);
                exit;
            }
        }
    }

    //this method is called by flutter callback url
    public function verify_flutter_payment($flutter_private, $transaction_id =false) {
        if($transaction_id == false){
            $transaction_id = isset($_GET['transaction_id']) ? $_GET['transaction_id'] : '';
            if (!$transaction_id) {
                die('No transaction_id supplied ddd');
            }
        }

        $url = "https://api.flutterwave.com/v3/transactions/{$transaction_id}/verify/";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
                $ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$flutter_private}"]
        );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //should be removed before taking the app life
        $response2 = curl_exec($ch);
        curl_close($ch);

        if ($response2) {
            return json_decode($response2, true);
        }
    }
}
