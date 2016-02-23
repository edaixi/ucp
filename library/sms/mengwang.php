<?php

class Mengwang{
  private static $instance;

  private $user_id;
  private $password;

  private function __construct($user_id, $password){
    $this->user_id = $user_id;
    $this->password = $password;
  }

  public static function get_instance($config){
    if(null === self::$instance){
      self::$instance = new Mengwang($config['sms']['mengwang']['user_id'], $config['sms']['mengwang']['password']);
    }
    return self::$instance;
  }

  public function send_message($phone, $content, $headers = array(), $options = array()){
    $payload = array(
      'userId' => $this->user_id,
      'password' => $this->password,
      'pszMobis' => $phone,
      'pszMsg' => $content,
      'iMobiCount' => '1',
      'pszSubPort' => '*'
      );

    $param_str = $this->url_encode($payload);
    $url = "http://61.145.229.29:9006/MWGate/wmgw.asmx/MongateCsSpSendSmsNew";
    $response = Requests::get($url.'?'.$param_str, $headers, $options);
    $codes = simplexml_load_string($response->body);
    return $codes;
  }

  private function url_encode($payload) {
    $arg  = "";
    while (list ($key, $val) = each ($payload)) {
      $arg.=$key."=".urlencode($val)."&";
    }
    $arg = substr($arg,0,count($arg)-2);
    if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
    return $arg;
  }
}