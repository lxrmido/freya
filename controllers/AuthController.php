<?php

namespace Controller;
use Lib\Core\Data;
use Lib\Core\Log;
use Lib\Core\DB;
use Lib\Core\IO;


class AuthController extends BaseController{

	public function __construct(){

	}

	/*
	 * 在此描述控制器信息及可被路由的方法
	 */
	public function getDesc(){
		return [
			'desc'    => '权限管理模块',
			'actions' => [
				'main'            => '主页面',
				'listCA'          => '获取CA权限列表',
				'addCAIgnore'     => '添加CA不验证登录',
				'delCAIgnore'     => '删除CA不验证登录',
				'listCharacterCA' => '列出角色CA',
				'addCharacterCA'  => '添加角色CA',
				'delCharacterCA'  => '删除角色CA',
			]
		];
	}

	public function listCA(){
		\Lib\User\Auth::flush_controllers();
		IO::O([
			'list'    => \Lib\User\Auth::list_controllers(),
			'ignored' => \Lib\User\Auth::list_controller_ignore()
		]);
	}

	public function addCAIgnore(){
		$c = IO::I('controller');
		$a = IO::I('action');

		if(
			(preg_match('/^[a-zA-Z0-9_]+$/', $c) !== 1) || 
			(preg_match('/^[a-zA-Z0-9_]+$/', $a) !== 1)  ){
			IO::E(-404, '路由不存在（1）');
		}
		$classRoute = "Controller\\{$c}Controller";
		if(!class_exists($classRoute)){
			IO::E(-404, '路由不存在（2）'.$classRoute);
		}
		$controller = new $classRoute; 
		if(!method_exists($controller, $a)){
			IO::E(-404, '路由不存在（3）');
		}

		\Lib\User\Auth::add_ca_ignore($c, $a);

		IO::O();
	}

	public function delCAIgnore(){
		$c = IO::I('controller');
		$a = IO::I('action');

		if(
			(preg_match('/^[a-zA-Z0-9_]+$/', $c) !== 1) || 
			(preg_match('/^[a-zA-Z0-9_]+$/', $a) !== 1)  ){
			IO::E(-404, '路由不存在（1）');
		}

		\Lib\User\Auth::del_ca_ignore($c, $a);

		IO::O();
	}

	public function listCharacterCA(){
		$cid = IO::I('cid', null, 'uint');
		IO::O([
			'list' => \Lib\User\Auth::list_character_controller($cid)
		]);
	}

	public function addCharacterCA(){
		$cid = IO::I('cid', null, 'uint');
		$controller = IO::I('controller');
		$action = IO::I('action');
		\Lib\User\Auth::add_character_ca($cid, $controller, $action);
		IO::O();
	}

	public function delCharacterCA(){
		$cid = IO::I('cid', null, 'uint');
		$controller = IO::I('controller');
		$action = IO::I('action');
		\Lib\User\Auth::del_character_ca($cid, $controller, $action);
		IO::O();
	}

	public function main(){
		
	}

}