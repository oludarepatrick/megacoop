<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
  
class Fivelinks{

    public function __construct() {
        $this->ignite = & get_instance();
    }

    private function process_phone_num($phone_nos){
        $num_arr = explode(',',$phone_nos);
        $new_mum = [];
        foreach($num_arr as $num){
            if($num =='' or $num ==' '){
                continue;
            }
            if(!is_numeric($num)){
                continue;
            }
            
            $first = ltrim($num, '234');
            $new_mum[] = '234' . ltrim($first, '0');
        }
        
        return $new_mum;
    }

    private function process_sms_fee($content, $coop){
        $unit = ceil(count($content) / $coop->sms_price_per_unit);
        $price = $coop->sms_price_per_unit * $unit;
        return (object)[
            "price"=>$price,
            "unit"=>$unit
        ];
    }
    
    public function send_SMS($phone_no, $content, $broadcast = false){
        if($this->ignite->app_settings->config == 0){ //default
            $this->send_SMS_termii($phone_no, $content, $broadcast);
        }else{
            $this->send_SMS_fivelinks($phone_no, $content, $broadcast);
        }
    }

    public function send_SMS_fivelinks($phone_no, $content, $broadcast = false) {
        $api_token = base64_encode($this->ignite->coop->beta_sms_username . ':' . $this->ignite->coop->beta_sms_pass);
        $sms_fee = $this->process_sms_fee($content, $this->ignite->coop);
        $post_data = [
            'from'=>$this->ignite->coop->sms_sender,
            'to'=>$this->process_phone_num($phone_no),
            'text'=> $content
        ];
         
        $url = 'http://api.messaging-service.com/sms/1/text/single';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));  //Post Fields
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            "Authorization: Basic ".$api_token,
            'Content-Type: application/json',
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //should bo removed before taking the app to live

        if (!$broadcast) {
            $user = $this->ignite->common->get_this('users', ['coop_id' => $this->ignite->coop->id,'phone' => $phone_no]);
            if($user->sms_notice =='true'){
                $response = curl_exec($ch);
                $this->sms_log($user, $sms_fee);
            }
        }else{
            $user = $this->ignite->common->get_all_these('users', ['coop_id' => $this->ignite->coop->id]);
            foreach($user as $u){
                $this->sms_log($u, $sms_fee);
            }
            $response = curl_exec($ch);
        }
        curl_close($ch);

        if ($response) {
            $result = json_decode($response, true);
            if(isset($result['messages'])){
                return true;
            }
        }
        return false;
    }

    public function send_SMS_termii($phone_no, $content, $broadcast = false) {
        $sms_fee = $this->process_sms_fee($content, $this->ignite->coop);
        $numbers = $this->process_phone_num($phone_no);
        $post_data = [
            'from'=>$this->ignite->coop->sms_sender,
            'to'=> (count($numbers) > 1 ) ? $numbers:$numbers[0],
            'sms'=> $content,
            'type'=>"plain",
            'channel'=> "generic",
            'api_key'=> $this->ignite->coop->sms_api_key,
        ];
        $type = "";
        if(count($numbers) > 1){
            $type = "/bulk";
        }
         
        $url = 'https://api.ng.termii.com/api/sms/send'.$type;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));  //Post Fields
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            'Content-Type: application/json',
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //should bo removed before taking the app to live
        foreach ($numbers as $n) {
            $n = '0'.ltrim($n, '234');
            $user = $this->ignite->common->get_this('users', ['coop_id' => $this->ignite->coop->id, 'phone'=>$n]);
            $this->sms_log($user, $sms_fee);
        }

        $response = curl_exec($ch);

        curl_close($ch);

        if ($response) {
            $result = json_decode($response, true);
            if($result['code'] == 'ok'){
                return true;
            }
        }
        return false;
    }


    private function sms_log($user, $sms_fee){
        $data = [
            'coop_id'=>$this->ignite->coop->id,
            'user_id'=>$user->id,
            'price'=>$sms_fee->price,
            'unit'=>$sms_fee->unit,
        ];
        $this->ignite->common->add('sms_log', $data);
    }
}