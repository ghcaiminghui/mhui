<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MyhuiController extends Controller
{
    
    $token = 'caiminghui';

    //this is wei xin kai fa 
    public function myhui(Request $request)
    {
	//jie ru wei xin
	$signature = $request->input('signature');
	$timestamp = $request->input('timestamp');
	$nonce = $request->input('nonce');
	$dataarr=array($nonce,$timestamp,$this->token);
	
	//zi dian pai xu
	sort($dastaarr,SORT_STRING);

	//zhuan huan zi fu chuan
	$str = implode($dataarr);
	
	//jia mi
	$str1 = sha1($str);
	
	//bi dui
	if($signature == $str1){
		
		return true;
	}else{
		return false;
	}
    }

    public function myhui()
    {
	
    }
}
