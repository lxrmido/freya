<?php

namespace Controller;

use Lib\Core\Vericode;
use Lib\Core\Http;
use Lib\Core\Data;
use Lib\Core\IO;
use Lib\Core\DB;
use Lib\User\UserGroup;
use Lib\User\User;
use Lib\User\Auth;
use Lib\User\Character;


class UserController extends BaseController{

	public function __construct(){

	}

	public function getDesc(){
		return [
			'desc'    => '用户模块',
			'actions' => [
				'login'              => '登陆',
				'logined'            => '登陆成功',
				'logout'             => '注销',
				'checkLogin'         => '检查登陆信息',
				'groupList'          => '获取用户组列表',
				'addGroup'           => '添加用户组',
				'renameGroup'        => '重命名用户组',
				'moveGroup'          => '移动用户组',
				'setGroupCharacter'  => '设置用户组角色',
				'removeGroup'        => '删除用户组',
				'userList'           => '获取用户列表',
				'searchList'         => '搜索用户列表',
				'addUser'            => '添加用户',
				'editUser'           => '修改用户信息',
				'moveUser'           => '移动用户组',
				'removeUser'         => '删除用户',
				'banUser'            => '封禁用户',
				'listCharacter'      => '列出角色',
				'addCharacter'       => '增加角色',
				'delCharacter'       => '删除角色',
				'renameCharacter'    => '角色重命名',
				'listUserCharacter'  => '列出用户角色',
				'addUserCharacter'   => '增加用户角色',
				'delUserCharacter'   => '删除用户角色',
				'listGroupCharacter' => '列出用户组角色',
				'addGroupCharacter'  => '增加用户组角色',
				'delGroupCharacter'  => '删除用户组角色',
			]
		];
	}

	public function main(){
		
	}

	// 登录页面
	public function login(){
		$this->show('user/login');
	}

	// 登陆成功的页面
	public function logined(){
		$account  = IO::I('account');
		$password = IO::I('password');
		$code     = IO::I('code');
		$keep     = IO::I('keep', false, 'bool');

		if(!Vericode::check_code($code)){
			IO::E(-1, '验证码不正确！');
		}

		$u = User::check_login($account, $password);

		if($u == false){
			IO::E(-2, '用户名或密码输入有误');
		}

		User::set_login($u, $keep);
		User::update_basic([
			'lastlogin' => time(),
			'lastip'    => Http::getUserIP()
		], $u['id']);

		Vericode::flush_code();

		$this->routeTo('admin', 'main');
	}

	public function logout(){
		User::logout();
		$this->routeTo('user', 'login');
	}


	public function checkLogin(){
		$account  = IO::I('account');
		$password = IO::I('password');
		$code     = IO::I('code');
		$keep     = IO::I('keep', false, 'bool');

		if(!Vericode::check_code($code)){
			IO::E(-1, '验证码不正确！');
		}

		$u = User::check_login($account, $password);

		if($u == false){
			IO::E(-2, '用户名或密码输入有误');
		}

		IO::O();
	}

	public function register(){

	}

	public function groupList(){
		IO::O([
			'list' => UserGroup::group_list()
		]);
	}

	public function addGroup(){
		$name       = IO::I('name');		
		$parent     = IO::I('parent', null, 'uint');
		$data = UserGroup::insert([
			'name'   => $name,
			'parent' => $parent
		]);
		IO::O([
			'inserted' => $data
		]);
	}

	public function renameGroup(){
		$id   = IO::I('id', null, 'uint');
		$name = IO::I('name');
		UserGroup::update_name($id, $name);
		IO::O();
	}

	public function moveGroup(){
		$id     = IO::I('id', null, 'uint');
		$parent = IO::I('parent', null, 'uint');
		if(UserGroup::has_parent($parent, $id)){
			IO::E('将要移动的用户组是目标用户组的父用户组节点，不能移动。');
		}
		UserGroup::update_parent($id, $parent);
		IO::O();
	}
	public function setGroupCharacter(){

	}
	public function removeGroup(){
		$id      = IO::I('id', null, 'uint');
		$move_to = IO::I('move_to', null, 'uint');
		UserGroup::remove($id, $move_to);
		IO::O();
	}

	public function userList(){
		$group  = IO::I('group',  null, 'uint');
		$offset = IO::I('offset', null, 'uint');
		$count  = IO::I('count',  null, 'uint');

		if($group === 0){
			list($total, $list) = User::user_list($offset, $count);
		}else{
			list($total, $list) = UserGroup::user_list($group, $offset, $count);
		}


		IO::O([
			'total' => $total,
			'list'  => $list
		]);
	}

	public function searchList(){
		$offset = IO::I('offset', null, 'uint');
		$count  = IO::I('count',  null, 'uint');
		$kw     = IO::I('kw');
		list($total, $list) = User::search_list($kw, $offset, $count);
		IO::O([
			'total' => $total,
			'list'  => $list
		]);
	}

	public function addUser(){
		$data = [
			'username' => IO::I('username'),
			'password' => IO::I('password'),
			'email'    => IO::I('email'),
			'group'    => IO::I('group', null, 'uint'),
		];
		if(($r = User::check_insert($data)) !== 1){
			IO::E($r, User::errmsg($r));
		}
		$u = User::add_user($data);
		if($u){
			IO::O();
		}
		IO::E();
	}

	public function editUser(){
		$data = [
			'username' => IO::I('username'),
			'email'    => IO::I('email'),
			'group'    => IO::I('group', null, 'uint'),
		];
		$uid      = IO::I('uid', null, 'uint');
		$u = User::get_user_by_id($uid);
		if(!$u){
			IO::E('用户不存在');
		}
		$password = IO::I('password');
		if(!empty($password)){
			$data['password'] = User::make_pass($password, $u['salt']);
		}
		if(User::update($data, $uid)){
			IO::O();
		}
		IO::E();
	}

	public function moveUser(){
		$group = IO::I('group', null, 'uint');
		$uid   = IO::I('uid', null, 'uint');
		$u = User::get_user_by_id($uid);
		if(!$u){
			IO::E('用户不存在');
		}
		if(User::update(['group'=>$group], $uid)){
			IO::O();
		}
		IO::E();
	}

	public function removeUser(){
		$uid = IO::I('uid', null, 'uint');
		$u   = User::get_user_by_id($uid);
		if(!$u){
			IO::E('用户不存在');
		}
		if(User::remove($uid)){
			IO::O();
		}
		IO::E();
	}

	public function banUser(){
		$ban = IO::I('ban', null, 'uint');
		$uid = IO::I('uid', null, 'uint');
		$u = User::get_user_by_id($uid);
		if(!$u){
			IO::E('用户不存在');
		}
		if(User::update(['ban'=>$ban], $uid)){
			IO::O();
		}
		IO::E();
	}

	public function listCharacter(){
		IO::O([
			'list' => Character::list_characters()
		]);
	}

	public function addCharacter(){
		$name = IO::I('name');
		Character::add_character($name);
		IO::O();
	}

	public function delCharacter(){
		$id = IO::I('id', null, 'uint');
		Character::del_character($id);
		IO::O();
	}

	public function renameCharacter(){
		$name = IO::I('name');
		$id   = IO::I('id', null, 'uint');
		Character::rename_character($id, $name);
		IO::O();
	}

	public function listUserCharacter(){
		$uid = IO::I('uid', null, 'uint');
		IO::O([
			'list' => Character::list_user_character($uid)
		]);
	}

	public function addUserCharacter(){
		$uid = IO::I('uid', null, 'uint');
		$cid = IO::I('cid', null, 'uint');
		Character::add_user_character($uid, $cid);
		IO::O();
	}

	public function delUserCharacter(){
		$uid = IO::I('uid', null, 'uint');
		$cid = IO::I('cid', null, 'uint');
		Character::del_user_character($uid, $cid);
		IO::O();
	}

	public function listGroupCharacter(){
		$gid = IO::I('gid', null, 'uint');
		IO::O([
			'list' => Character::list_group_character($gid),
			'char' => Character::list_characters()
		]);
	}

	public function addGroupCharacter(){
		$gid = IO::I('gid', null, 'uint');
		$cid = IO::I('cid', null, 'uint');
		if(!Character::get_character($cid)){
			IO::E('角色不存在');
		}
		if(!UserGroup::get_group($gid)){
			IO::E('用户组不存在');
		}
		Character::add_group_character($gid, $cid);
		IO::O();
	}

	public function delGroupCharacter(){
		$gid = IO::I('gid', null, 'uint');
		$cid = IO::I('cid', null, 'uint');
		Character::del_group_character($gid, $cid);
		IO::O();
	}

}