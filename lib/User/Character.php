<?php

namespace Lib\User;

use Lib\Core\DB;

class Character{

	public static function get_character($id){
		$r = DB::assoc("SELECT * FROM `character` WHERE `id`=:id", ['id'=>$id]);
		if(empty($r)){
			return false;
		}
		return $r;
	}

	public static function list_characters(){
		return DB::all("SELECT * FROM `character` ORDER BY `id` ASC");
	}

	public static function add_character($name){
		return DB::insert([
			'name' => $name
		], 'character');
	}

	public static function del_character($id){
		return DB::query("DELETE FROM `character` WHERE `id`='$id'");
	}

	public static function rename_character($id, $name){
		DB::update([
			'name' => $name
		], 'character', "`id`='$id'");
	}

	public static function list_user_character($uid){
		return DB::all_one("SELECT `cid` FROM `user_character` WHERE `uid`='$uid'");
	}	

	public static function add_user_character($uid, $cid){
		return DB::replace([
			'uid' => $uid,
			'cid' => $cid
		], 'user_character`');
	}

	public static function del_user_character($uid, $cid){
		return DB::query("DELETE FROM `user_character` WHERE `uid`=:uid AND `cid`=:cid", [
			'uid' => $uid,
			'cid' => $cid
		]);
	}

	public static function list_group_character($gid){
		return DB::all_one("SELECT `cid` FROM `user_group_character` WHERE `gid`='$gid'");
	}	

	public static function add_group_character($gid, $cid){
		if(DB::one("SELECT `gid` FROM `user_group_character` WHERE `gid`=:gid AND `cid`=:cid", ['gid'=>$gid,'cid'=>$cid])){
			return false;
		}
		return DB::insert([
			'gid' => $gid,
			'cid' => $cid
		], 'user_group_character');
	}

	public static function del_group_character($gid, $cid){
		return DB::query("DELETE FROM `user_group_character` WHERE `gid`=:gid AND `cid`=:cid", [
			'gid' => $gid,
			'cid' => $cid
		]);
	}
	
}