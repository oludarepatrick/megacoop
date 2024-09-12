<?php 
class Paystack{

    const MAX_CHARGES = 2000;
    const CHARGES_ON_MAX_CHAGES = 30;
    const FLAT_CHARGS = 100;
    const RATE = 0.015;

    public function __construct() {
        $this->ignite = & get_instance();
        // $this->ignite->load->model('common_model');
    }

    public function get_amount_paystack($amount) {
        if (!$amount or empty($amount)) {
            return false;
        }

        $paystack_charges = ($amount * self::RATE);

        if ($amount < 2000) {
            $amount_topay = $amount + $paystack_charges;
            return round($amount_topay + self::CHARGES_ON_MAX_CHAGES);
        } elseif ($paystack_charges >= self::MAX_CHARGES) {
            $amount_topay = $amount + self::MAX_CHARGES;
            return $amount_topay;
        } else {
            $amount_topay = $amount + $paystack_charges + self::CHARGES_ON_MAX_CHAGES + self::FLAT_CHARGS;
            return round($amount_topay);
        }
    }

    public function initialize_paystack($post_data, $paystack_private) {
        $result = array();
        $url = "https://api.paystack.co/transaction/initialize";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));  //Post Fields
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            "Authorization: Bearer {$paystack_private}",
            'Content-Type: application/json',
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //should bo removed before taking the app to live

        $response = curl_exec($ch);
        curl_close($ch);
        if ($response) {
            $result = json_decode($response, true);
            
            if ($result['status']) {
                header('Location: ' . $result['data']['authorization_url']);
                exit;
            }
        }
    }

    //this method is called by paystack callback url
    public function verify_paystack_payment($paystack_private, $reference=false) {
        if($reference == false){
            $reference = isset($_GET['reference']) ? $_GET['reference'] : '';
            if (!$reference) {
                die('No reference supplied');
            }
        }

        $url = 'https://api.paystack.co/transaction/verify/' . rawurlencode($reference);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
                $ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$paystack_private}"]
        );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //should be removed before taking the app life
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            return json_decode($response, true);
        }
    }
}