<?php

namespace Controller;
use Lib\Core\Vericode;
use Lib\Core\Http;
use Lib\Core\IO;


class CoreController extends BaseController{

	public function main(){
		echo 'main';
	}

	public function browser(){

	}

	public function debug(){
		// var_dump(Http::getUserIP());
		// \Lib\User\Auth::flush_controllers();
		var_dump(\Lib\User\Auth::list_controllers());
	}

	public function call(){
		$m = explode('.', IO::I('_m'));
		if(count($m) !== 2){
			IO::E('请求方法不合法');
		}

		global $FREYA_JSON_IO;
		global $FREYA_METHOD;

		$FREYA_JSON_IO = true;
		$FREYA_METHOD['is_method'] = true;
		$FREYA_METHOD['module'] = $m[0];
		$FREYA_METHOD['method'] = $m[1];

		Http::routeControllerAction(ucfirst($m[0]), $m[1]);
	}

}