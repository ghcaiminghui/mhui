<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MyhuiController extends Controller
{
    
    public $token = 'caiminghui';

    //微信开发 
    public function myhui(Request $request)
    {
		//获取微信服务器get参数
		$signature = $request->input('signature');
		$timestamp = $request->input('timestamp');
		$nonce = $request->input('nonce');

		//判断
		if($this->checkSignature($nonce,$timestamp,$signature)){

			//把随机的字符串原模原样返回->接入网址成功
			$echostr = $request->input('echostr');

			if($echostr){

				echo $echostr;
				exit;
			}
		}

		//接收用户发送过来的消息->微信后台->用户的客户端
		$data = file_get_contents("php://input");

		if(!empty($data)){

			//解析微信服务器传过来的xml格式消息，转换成对象的格式
			$object = simplexml_load_string($data,"SimpleXMLElement",LIBXML_NOCDATA);

			//获取里面的基本信息
			$ToUserName = $object->ToUserName; //用于回复的发送方
			$FromUserName = $object->FromUserName; //用于回复xml数据的接收方
			$MsgType = $object->MsgType;  //类型
			switch ($MsgType) {
				case 'text':

					//文本信息，封装回复信息
					$Content = $object->Content;  //获取回复的信息

					//触发笑话的功能
					if($Content == "笑话"){

						//初始化curl
						$cn = curl_init();

						//准备接口
						$url = "http://www.kuitao8.com/api/joke";

						curl_setopt($cn,CURLOPT_URL,$url);

						curl_setopt($cn,CURLOPT_RETURNTRANSFER,1);

						$result = curl_exec($cn);

						curl_close($cn);

						$data = json_decode($result,true);

						$Content =  $data['content'];

					}

					//封装回复的xml包
					$msgXml="<xml>
								<ToUserName><![CDATA[%s]]></ToUserName>
								<FromUserName><![CDATA[%s]]></FromUserName>
								<CreateTime>%s</CreateTime>
								<MsgType><![CDATA[text]]></MsgType>
								<Content><![CDATA[%s]]></Content>
							</xml>";
					break;
				
			}

			//用变量替换通配符%s,然后发送xml包给客户端
			$result = sprintf($msgXml,$FromUserName,$ToUserName,time(),$Content);
			echo $result;
			

		}else{

			echo '';
			exit;
		}

    }


    //比对微信服务器的信息，确认是否是微信后台发送的
    public function checkSignature($nonce,$timestamp,$signature)
    {
		//把token $timestamp $nonce 存储在数组里
		$dataArr=array($nonce,$timestamp,$this->token);
		
		//字典排序 SORT_STRING 快速排序
		sort($dataArr,SORT_STRING);

		//转换字符
		$str = implode($dataArr);
		
		//加密字符串 sha1相当于md5加密
		$str = sha1($str);
		
		//对比加密签名和加密字符串
		if($signature == $str){
			
			return true;
		}else{
			return false;
		}
    }

    public function access_token()
    {

    	//初始化
		$cn = curl_init();

		//基本信息
		$appid = "wxc6fb93f638f00a58";
		$secret = "bfdf517e7ef50b8a2a2f9467bdb66811";

		//准备接口
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";

		curl_setopt($cn,CURLOPT_URL,$url);
		curl_setopt($cn,CURLOPT_RETURNTRANSFER,1);

		$result = curl_exec($cn);

		curl_close($cn);

		$data = json_decode($result,true);

		$access_token = $data['access_token'];

		echo $access_token;
	}
}
