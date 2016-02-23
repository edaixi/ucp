<?php

class SmsTest_Mengwang extends PHPUnit_Framework_TestCase {
  public function test_get_instance() {
    $config = array(
      'sms' => array(
        'mengwang' => array('user_id' => 'dumb_user_id', 'password' => 'dumb_password')));
    $inst = MengWang::get_instance($config);
    $this->assertEquals($inst, MengWang::get_instance($config));
  }

  public function test_send_message(){
    $transport = $this->getMockBuilder('Requests_Transport')->getMock();

    $response =
    "HTTP/1.0 200 OK\r\n".
    "Content-Type: text/plain\r\n".
    "Connection: close\r\n\r\n".
    "<string></string>\r\n";

    $transport->method('request')->with('http://61.145.229.29:9006/MWGate/wmgw.asmx/MongateCsSpSendSmsNew?userId=dumb_user_id&password=dumb_password&pszMobis=test_phone&pszMsg=test+content&iMobiCount=1&pszSubPort=%2A')->willReturn($response);
    $config = array(
      'sms' => array(
        'mengwang' => array('user_id' => 'dumb_user_id', 'password' => 'dumb_password')));
    $inst = MengWang::get_instance($config);
    $inst->send_message('test_phone', 'test content', array(), array('transport' => $transport));
  }

  public function test_send_message_with_wrong_account(){
    $transport = new MockTransport();
    $transport->body = '<?xml version="1.0" encoding="utf-8"?><string xmlns="http://tempuri.org/">-10001</string>';
    $transport->chunked = true;

    $config = array(
      'sms' => array(
        'mengwang' => array('user_id' => 'dumb_user_id', 'password' => 'dumb_password')));
    $inst = MengWang::get_instance($config);
    $code = $inst->send_message('test_phone', 'test content', array(), array('transport' => $transport));
    $this->assertEquals('-10001', $code);
  }

  public function test_send_message_with_success_response(){
    $transport = new MockTransport();
    $transport->body = '<?xml version="1.0" encoding="utf-8"?><string xmlns="http://tempuri.org/">3150561236279167210</string>';
    $transport->chunked = true;

    $config = array(
      'sms' => array(
        'mengwang' => array('user_id' => 'dumb_user_id', 'password' => 'dumb_password')));
    $inst = MengWang::get_instance($config);
    $code = $inst->send_message('test_phone', 'test content', array(), array('transport' => $transport));
    $this->assertEquals('3150561236279167210', $code);
  }
}