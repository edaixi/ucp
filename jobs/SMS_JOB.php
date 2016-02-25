<?php
class SMS_JOB{

  public function perform(){
    fwrite(STDOUT, 'Start job! !!!!!-> ');
    // $config = require APP_ROOT . '/config/Config.php';
    $config = new Config;
    $this->config = $config['sms'];
    $fun = 'send'.$this->getChannel();
    $this->$fun();
  }
  private function  getChannel(){
      $channels = array('Mengwang');
      return $channels[0];
  }
  private function sendMengwang(){

      $reply['userId']  = $this->config['mengwang']['userId'];
      $reply['password']  = $this->config['mengwang']['password'];
      $reply['pszMobis'] = $this->args['phone']; 
      $reply['pszMsg'] = $this->args['content']; 
      $reply['iMobiCount'] = '1';
      $reply['pszSubPort'] = '*';
      $param_str = $this->createLinkstringUrlencode($reply);
      $url = "http://61.145.229.29:9006/MWGate/wmgw.asmx/MongateCsSpSendSmsNew";
      $headers = array();
      $options = array();
      $resp = Requests::get($url.'?'.$param_str, $headers, $options);
      var_dump($resp);
  }
  
    private function createLinkstringUrlencode($para) {
         $arg  = "";
         while (list ($key, $val) = each ($para)) {
           $arg.=$key."=".urlencode($val)."&";
         }
         $arg = substr($arg,0,count($arg)-2);
         if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
         return $arg;
    }
}
