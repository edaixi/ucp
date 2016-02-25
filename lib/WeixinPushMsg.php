<?php
namespace Engine;
use Engine;

class WeixinPushMsg  extends  PushMsg{
	//微信纯文本消息JSON
	public function mkWxTextFields($req){
		$post['touser']= $req->from_user;
		$post['msgtype'] =  'text';
		$post['text'] = array('content' => $req->request_params['content']);
		$content = $this->JSON ( $post );
		return $content;
	}
	//微信纯文本消息JSON
	function getWxMediaID($val){
		$token = account_weixin_token();
		 $_url = '/cgi-bin/media/upload?access_token='.$token.'&type=image';
		 $_host = 'file.api.weixin.qq.com';
		 $errno = '';
		 $errstr = '';
		 $_fp = fsockopen($_host, 80, $errno,  $errstr, 15);
		 if($_fp){
	        // 设置分割标识
	        srand((double)microtime()*1000000);
	        $boundary = '---------------------------'.substr(md5(rand(0,32000)),0,10);
	        $data = '--'.$boundary."\r\n";
			$filedata = '';
			$filedata .= "content-disposition: form-data; name=\"".$val['name']."\"; filename=\"".$val['filename']."\"\r\n";
			$filedata .= "content-type: ".'image/jpeg'."\r\n\r\n";
			$filedata .= implode('', file($val['path']))."\r\n";
			$filedata .= '--'.$boundary."\r\n";
			
			 $data .= $filedata."--\r\n\r\n";
				$out = "POST ".$_url." http/1.1\r\n";
	        $out .= "host: ".$_host."\r\n";
	        $out .= "content-type: multipart/form-data; boundary=".$boundary."\r\n";
	        $out .= "content-length: ".strlen($data)."\r\n";
	        $out .= "connection: close\r\n\r\n";
	        $out .= $data;
			 fputs($_fp, $out);
			 // 读取返回数据
				$response = '';
				while($row = fread($_fp, 4096)){
						$response .= $row;
				}
				$pos = strpos($response, "\r\n\r\n");
				$response = substr($response, $pos+4);
				$res = json_decode($response,true);
				if(isset($res['media_id'])){
					return $res['media_id'];
				}else{
					return false;
				}
			}else{

				return false;
			}
	}
	public function mkWxImageFields($res){
		$post['touser']=  $res ['ToUserName'];
		$post['msgtype'] =  'image';
		$val = array(
		        'name' => 'media',
		        'filename' => 'test.jpg',
		        'path' => $res['content'],
	    	);
		$mediaId = $this->getWxMediaID( $val );
		// $media_id  = 'PXHlY3YIOAPnNctxF6DEdoX5rlltulBgz6MTkNk3CSXmvY_4wBya07jRdmGvX16P';
		$post['image'] = array('media_id' => $mediaId );
		$content = $this->JSON ( $post );
		return $content;
	}
	public function uploadWxMedia($file, $type){
		$token = account_weixin_token($_W['account']);
		$url = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=".$token."Q&type=".$type;
		// "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=65v5YTHowrD3DE-mLl9zcFmuknVPSdXv0MgReljh0K1pmymDI-2dhgZDqoAuQXHHq5ZDs1m76FNqzC1_MKJ9JVDnyLihIYFivCnCgtKDZNQ&type=image"
		$file_data = array('media' =>'@'.$file);
	}

	public function  mkWxNewsFields($reply){
		$new = array();
		$new['touser'] = $reply['ToUserName'];
		$new['msgtype'] =  'news';
		$article['title'] = $reply['title'];
		$article['description'] =   $reply['description'];
		$article['url'] = $reply['url'];
		$article['picurl'] =  $reply['picurl'];
		$articles = array($article);
		$new['news']['articles']  =  $articles;
		$content = $this->JSON ( $new );
		return $content;
	}
	//微信模板消息使用JSON方法 未测试
	public function mkWxTemplateData($params,$color_config){
	
		$data = array(
			'first'=>array(
				'color'=>$color_config['first'],
				'value'=>$params['first'],
			),
			'remark'=>array(
				'color'=>$color_config['remark'],
				'value'=>$params['remark'],
			),
			'OrderSn'=>array(
				'color'=> $color_config['fields_colors']['OrderNo'],
				'value'=> $params['params_valuse']['OrderNo'],
				),
			'OrderStatus'=>array(
				'color'=>$color_config['fields_colors']['OrderState'],
				'value'=> $params['params_valuse']['OrderState'],
				),
		);
		$s = '';		
		foreach ($params['orthers'] as $k =>$v){
			if (!empty($params['params_valuse'][$k])) {
				$s = $s.$v.':'.$params['params_valuse'][$k].';';
			}
		}
		if(!empty($s)){
				$s =  substr($s, 0,-1).'。';
				$remark = $s.$data['remark']['value'];
				$data['remark']['value'] = $remark;
		}
		
		// $data['remark']['value']=urlencode('查看订单');
		$url = $params['url'];
		$t_data = array(
			'url'=>$url,
			'topcolor'=>$color_config['topcolor'],
			'data'=>$data,
			);
		return $t_data;
	}
	//微信模板消息使用JSON方法 未测试
	public function mkWxTemplateContent($templateId,$touser,$template_data){
		$template_data['touser'] = $touser;
		$template_data['template_id'] = $templateId;
		$template_data_tojson = $this->JSON($template_data);
		return $template_data_tojson;
	}
	public function sendWxPost($poststr,$url){
		$token = account_weixin_token();
		// $token = '1xBVp_WUnrhHUYYWmgxvkbCgmhllRcM_F919d6p8lBhI68obvB4J5mFxHNFU6awgELn9yaAdOMSHkcDb92pknWBoUvYO3gikeIMSPM-mihY';
		$url = $url.$token;
		var_dump($token);
		$aop = new AopClient ();
		$result = $aop->curl2($url, $poststr);
		$res = json_decode( $result);
		if(($res->errcode == 42001) || ($res->errcode == 40001) ||($res->errcode == 40014)){
			refresh_access_token();
		}
		var_dump($res);
		return $res;
	}
}