<?php

namespace Lib\User;

use Lib\Core\DB;
use Lib\Core\IO;

class Auth{

	public static function list_controllers(){
		return DB::all("SELECT * FROM `auth_controller` ORDER BY `controller` ASC, `action` ASC");
	}

	public static function list_controller_ignore(){
		return DB::all("SELECT * FROM `auth_controller_ignore`");
	}

	// 判断C/A是否免登陆验证
	public static function is_ca_ignored($c, $a){
		if(empty(
				DB::assoc("SELECT * FROM `auth_controller_ignore` WHERE `controller`=:controller AND `action`=:action", [
						'controller' => $c,
						'action'     => $a
					])
				)
			){
			return false;
		}
		return true;
	}

	public static function add_ca_ignore($c, $a){
		return DB::replace([
			'controller' => $c,
			'action'     => $a
		], 'auth_controller_ignore');
	}

	public static function del_ca_ignore($c, $a){
		return DB::query("DELETE FROM `auth_controller_ignore` WHERE `controller`=:controller AND `action`=:action", ['controller'=>$c, 'action'=>$a]);
	}

	public static function flush_controllers(){
		self::clear_controllers();
		self::scan_controllers(RUNTIME_DIR_CONTROLLER);
	}

	public static function clear_controllers(){
		return DB::query("DELETE FROM `auth_controller`");
	}

	// 扫描controller
	public static function scan_controllers($root){
		$root_dir = opendir($root);
		while(($md_dir = readdir($root_dir)) !== false){
			$md = $root . $md_dir;
			if(is_dir($md)){
				if($md_dir != '.' && $md_dir != '..'){
					self::scan_controllers($md . '/');
				}
			}else{
				self::add_controller($md_dir);
			}
		}
	}

	public static function add_controller($name){
		$className  = ucfirst(substr($name, 0, -4));
		$classRoute = "Controller\\{$className}";
		$controller = new $classRoute;
		$desc = $controller->getDesc();
		foreach($desc['actions'] as $k => $v){
			DB::insert([
				'controller'      => substr($className, 0, -10),
				'action'          => $k,
				'controller_desc' => $desc['desc'],
				'action_desc'     => $v
			], 'auth_controller');
		}
	}

	public static function add_character_ca($character_id, $controller, $action){
		return DB::replace([
			'character_id' => $character_id,
			'controller'   => $controller,
			'action'       => $action
		], 'auth_character_controller');
	}

	public static function del_character_ca($character_id, $controller, $action){
		return DB::query("DELETE FROM `auth_character_controller` WHERE `character_id`=:character_id AND `controller`=:controller AND `action`=:action", [
			'character_id' => $character_id,
			'controller'   => $controller,
			'action'       => $action
		]);
	}

	public static function list_character_controller($character_id){
		return DB::all("SELECT * FROM `auth_character_controller` WHERE `character_id`='$character_id'");
	}

	public static function is_character_permitted($character_id, $controller, $action){
		$r = DB::one("SELECT `character_id` FROM `auth_character_controller` WHERE `character_id`=:character_id AND `controller`=:controller AND `action`=:action", [
			'character_id' => $character_id,
			'controller'   => $controller,
			'action'       => $action
		]);
		return !empty($r);
	}

	public static function is_group_permitted($group_id, $controller, $action){
		$characters = self::group_avail_characters($group_id);
		foreach ($characters as $cid){
			if(self::is_character_permitted($cid, $controller, $action)){
				return true;
			}
		}
		return false;
	}

	public static function group_avail_characters($group_id){
		$rs = DB::all_one("SELECT `cid` FROM `user_group_character` WHERE `gid`=:group_id", ['group_id'=>$group_id]);
		return $rs;
	}

	public static function is_permitted($u, $controller, $action){
		if($u['id'] == 1){
			return true;
		}
		if(self::is_group_permitted($u['group'], $controller, $action)){
			return true;
		}
		return false;
	}

}