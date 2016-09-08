<?php

namespace Controller;
use Lib\Core\Vericode;
use Lib\Core\Data;
use Lib\Core\Log;
use Lib\Core\DB;
use Lib\Core\IO;

class VericodeController extends BaseController{

	public function __construct(){

	}

	public function getDesc(){
		return [
			'desc'    => '验证码模块',
			'actions' => [
				'simple' => '获取验证码',
				'check'  => '检查验证码'
			]
		];
	}

	public function main(){
		
	}

	public function simple(){
		Vericode::new_login_code();
	}

	public function check(){
		$code = IO::I('code');
		if(Vericode::check_code($code)){
			IO::O([
				'correct' => true
			]);
		}else{
			IO::E('验证码不正确');
		}
	}

}