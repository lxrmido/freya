<?php

namespace Controller;

use Lib\User\User;

class AdminController extends BaseController{

	public function __construct(){
		
	}

	public function main(){
		$this->show('Admin/Main');
	}

	public function user(){
		$this->show('Admin/User');
	}

	public function auth(){
		$this->show('Admin/Auth');
	}

	public function usergroup(){
		$this->show('Admin/UserGroup');
	}

	public function character(){
		$this->show('Admin/Character', [
			'ca_json' => json_encode(\Lib\User\Auth::list_controllers()),
			'ca_ignore_json' => json_encode(\Lib\User\Auth::list_controller_ignore()),
		]);
	}

	public function getDesc(){
		return [
			'desc'    => '管理模块',
			'actions' => [
				'main'      => '概述',
				'user'      => '用户管理',
				'auth'      => '权限列表',
				'usergroup' => '用户组管理',
				'character' => '角色定义'
			]
		];
	}

}