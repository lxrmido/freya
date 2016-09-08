<?php

namespace Controller;
use Lib\Core\Data;
use Lib\Core\Log;
use Lib\Core\DB;
use Lib\Core\IO;

class <{$className}> extends BaseController{

	public function __construct(){

	}

	/*
	 * 在此描述控制器信息及可被路由的方法
	 */
	public function getDesc(){
		return [
			'desc'    => '<{$className}>模块',
			'actions' => [
				'main' => '主页面'
			]
		];
	}

	public function main(){
		
	}

}