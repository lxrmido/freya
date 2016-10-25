<?php
namespace Lib\User;

use Lib\Core\IO;
use Lib\Core\DB;

/**
 * 管理平台用户工具类
 */
define('CODE_USERNAME_EMPTY', -101);
define('CODE_USERNAME_NOT_VALID', -102);
define('CODE_EMAIL_EMPTY', -103);
define('CODE_EMAIL_NOT_VALID', -104);
define('CODE_PASSWORD_EMPTY', -105);
define('CODE_USERNAME_USED', -106);
define('CODE_EMAIL_USED', -107);

define('CODE_ADMIN_NOLOGIN', -201);

define('SUPER_ADMIN_GROUP', 1);


class User{
    
    /**
     * 最近使用的用户
     * @var [type]
     */
    public static $last;
    /**
     * 是否忽略用户的登录状态
     * @var boolean
     */
    public static $ignore = false;

    public static function auth(){
        return self::admin_check();
    }

    /**
     * 检查当前登陆的管理员
     * @return bool|array
     */
    public static function admin_check(){
        if(self::$ignore){
            return array(
                'id' => 0
            );
        }
        $u = self::get_login();
        if(!$u){
            global $FREYA_JSON_IO;
            if($FREYA_JSON_IO){
                IO::e(CODE_ADMIN_NOLOGIN, '请先登录');
            }else{
                \Lib\Core\Http::routeControllerAction('User', 'login', false);
            }
        }
        self::$last = self::get_details($u);

        global $_RG, $METHOD;

        // if($METHOD['is_method']){

        // }else{
        //     if(!self::is_super() && !UserGroup::check_priv_action($_RG['PG_C'], $_RG['PG_A'], $u['group'])){
        //         IO::e('您没有权限查看本页面！');
        //     }
        // }

        if($u['ban'] == 1){
            self::logout();
            IO::E('您已被禁止访问，请联系管理员');
        }

        return $u;
    }

    public static function get_details($u){
        $u['groups']     = [];
        $gid  = $u['group'];
        while(!empty($gid)){
            $g = UserGroup::get_group($gid);
            if(!$g){
                break;
            }
            $u['groups'][] = $g;
            $gid = $g['parent'];
        }
        if(!empty($u['group'])){
            $u['characters'] = Character::list_group_character($u['group']);
        }
        return $u;
    }

    /**
     * 是否超级管理组
     * @return bool
     */
    public static function is_super(){
        return (!empty(self::$last['group'])) && (intval(self::$last['group']) === SUPER_ADMIN_GROUP);
    }
    
    /**
     * 获取登陆状态
     * @return bool|array
     */
    public static function get_login(){
        if(empty($_SESSION[FREYA_SESSION_PREFIX.'user']) && !self::get_login_from_cookie()){
            return false;
        }
        self::$last = self::get_user_by_id($_SESSION[FREYA_SESSION_PREFIX.'user']['id']);
        // self::$last = self::update_fake(self::get_user_by_id($_SESSION['user']['id']));
        return self::$last;
    }

    /**
     * 从COOKIE登陆
     * @return bool
     */
    public static function get_login_from_cookie(){
        if(empty($_COOKIE['uid'])){
            return false;
        }
        $uid = intval($_COOKIE['uid']);
        if(empty($_COOKIE['time'])){
            return false;
        }
        $time = intval($_COOKIE['time']);
        if(time() - $time > 86400 * 30){
            return false;
        }
        if(empty($_COOKIE['snap'])){
            return false;
        }
        $snap = $_COOKIE['snap'];
        $u = self::get_user_by_id($uid);
        if($snap == self::make_snap($time, $u['password'], $u['salt'])){
            $_SESSION[FREYA_SESSION_PREFIX.'user'] = $u;
            return true;
        }else{
            return false;
        }
    }

    /**
     * 检查并更新fake值
     * @param  array &$u user_basic
     * @return array user_basic
     */
    public static function update_fake($u){
        $time = time();
        if(empty($u['fake_update']) || $time - $u['fake_update'] > 7200){
            $u['fake'] = rand(11111111, 99999999);
            $u['fake_update'] = $time;
            DB::update([
                'fake'        => $u['fake'],
                'fake_update' => $u['fake_update']
            ], 'user_basic', "`id`='{$u['id']}'");
            // Marnode::update_fake($u['id'], $u['fake']);
        }
        return $u;
    }

    /**
     * 获取其余用户信息
     * @param  array $u 用户基本信息
     * @return array
     */
    public static function get_full_info($u){
        $group = DB::assoc("SELECT * FROM `user_group` WHERE `id`='{$u['group']}'");
        $u['group_name'] = $group['name'];
        return $u;
    }
    
    /**
     * 检查密码
     * @param  string $account  邮箱或用户名
     * @param  string $password 密码
     * @return bool|array           
     */
    public static function check_login($account, $password){
        $u = DB::assoc("SELECT * FROM `user_basic` WHERE `username`='$account' OR `email`='$account'");
        if($u == null){
            return false;
        }
        if($u['password'] != self::make_pass($password, $u['salt'])){
            return false;
        }
        return $u;
    }

    /**
     * 检查密码
     * @param  int $uid     用户ID
     * @param  string $password 密码
     * @return bool|array         
     */
    public static function check_password_with_id($uid, $password){
        $u = DB::assoc("SELECT * FROM `user_basic` WHERE `id`='$uid'");
        if($u == null){
            return false;
        }
        if($u['password'] != self::make_pass($password, $u['salt'])){
            return false;
        }
        return $u;
    }

    /**
     * 通过用户ID获取用户
     * @param  int $id
     * @return bool|array
     */
    public static function get_user_by_id($id){
        $u = DB::assoc("SELECT * FROM `user_basic` WHERE `id`='$id'");
        if(empty($u['id'])){
            return false;
        }
        return $u;
    }

    /**
     * 通过用户名获取用户
     * @param  string $username
     * @return bool|array
     */
    public static function get_user_by_name($username){
        $u = DB::assoc("SELECT * FROM `user_basic` WHERE `username`='$username'");
        if(empty($u['id'])){
            return false;
        }
        return $u;
    }

    /**
     * 通过邮箱获取用户
     * @param  string $email
     * @return bool|array
     */
    public static function get_user_by_email($email){
        $u = DB::assoc("SELECT * FROM `user_basic` WHERE `email`='$email'");
        if(empty($u['id'])){
            return false;
        }
        return $u;
    }

    /**
     * 过滤用户数据中的安全信息
     * @param  array $u 
     * @return array
     */
    public static function low_safe($u){
        if(empty($u)){
            return false;
        }
        return [
            'id'       => intval($u['id']),
            'username' => $u['username'],
            'email'    => $u['email'],
            'fake'     => intval($u['fake'])
        ];
    }
    
    /**
     * 注销登陆状态
     * @return void
     */
    public static function logout(){
        unset($_SESSION[FREYA_SESSION_PREFIX.'user']);
        setcookie('uid', 0, 0);
        setcookie('time', 0, 0);
        setcookie('snap', 0, 0);
    }

    /**
     * 刷新SESSION
     * @return void
     */
    public static function refresh(){
        $_SESSION[FREYA_SESSION_PREFIX.'user'] = User::$last;
    }
    
    /**
     * 设置登陆状态
     * @param array  $user_data
     * @param bool $save_cookie 
     * @return   array
     */
    public static function set_login($user_data, $save_cookie = false){
        $_SESSION[FREYA_SESSION_PREFIX.'user'] = $user_data;
        $time = time();
        if($save_cookie){
            setcookie('uid', $user_data['id'], $time + 86400 * 30);
            setcookie('time', $time, $time + 86400 * 30);
            setcookie('snap', self::make_snap($time, $user_data['password'], $user_data['salt']), $time + 86400 * 30);
        }else{
            setcookie('uid', 0, 0);
            setcookie('time', 0, 0);
            setcookie('snap', 0, 0);
        }
        self::$last = $user_data;
        return $user_data;
    }

    /**
     * 创建COOKIE_SNAP
     * @param  int $time     
     * @param  string $password 
     * @param  string $salt     
     * @return return
     */
    public static function make_snap($time, $password, $salt){
        return md5($salt.md5($time.md5($password)));
    }
    
    /**
     * 插入新用户到数据库
     * @param  array $data 
     * @return bool
     */
    public static function insert($data){
        return DB::insert($data, 'user_basic');
    }

    /**
     * 更新数据库中的用户信息
     * @param  array $data
     * @param  int $id   
     * @return bool
     */
    public static function update($data, $id){
        return DB::update($data, 'user_basic', "`id`=:id", ['id'=>$id]);
    }

    /**
     * 从数据库中移除用户
     * @param  int $id 
     * @return bool
     */
    public static function remove($id){
        return DB::query("DELETE FROM `user_basic` WHERE `id`=:id", ['id'=>$id]);
    }

    /**
     * 更新数据库中的基本用户信息
     * @param  array $data
     * @param  int $id   
     * @return bool
     */
    public static function update_basic($data, $id){
        return DB::update($data, 'user_basic', "`id`=:id", ['id'=>$id]);
    }

    /**
     * 新增用户
     * @param array $data 
     * @return bool|array
     */
    public static function add_user($data){
        if(empty($data['salt'])){
            $data['salt'] = self::make_salt();
        }
        if(empty($data['regdate'])){
            $data['regdate'] = time();
        }
        if(empty($data['lastlogin'])){
            $data['lastlogin'] = time();
        }
        if(empty($data['logintimes'])){
            $data['logintimes'] = 1;
        }
        if(empty($data['lastip'])){
            $data['lastip'] = '';
        }
        $data['password'] = self::make_pass($data['password'], $data['salt']);
        if(!self::insert($data)){
            return false;
        }
        $data['id'] = DB::id();
        return $data;
    }
    
    /**
     * 新增检查
     * @param  array $data
     * @return bool
     */
    public static function check_insert($data){
        if(empty($data['username'])){
            return CODE_USERNAME_EMPTY;
        }
        if(DB::one("SELECT `id` FROM `user_basic` WHERE `username`=:username", ['username'=>$data['username']]) != null){
            return CODE_USERNAME_USED;
        }
        if(empty($data['email'])){
            return CODE_EMAIL_EMPTY;
        }
        if(DB::one("SELECT `id` FROM `user_basic` WHERE `email`=:email", ['email'=>$data['email']]) != null){
            return CODE_EMAIL_USED;
        }
        return 1;
    }
    
    /**
     * 创建盐
     * @return string
     */
    public static function make_salt(){
        return rand(11111111, 99999999);
    }
    
    /**
     * 产生加盐密码
     * @param  string $raw 
     * @param  string $salt
     * @return string      
     */
    public static function make_pass($raw, $salt){
        return md5(md5($raw).$salt);
    }

    /**
     * 获取用户列表
     * @param  int $offset
     * @param  int $count 
     * @return array      
     */
    public static function user_list($offset, $count){
        $amount = DB::one("SELECT COUNT(`id`) FROM `user_basic`");
        $list   = DB::all("SELECT `id`,`username`,`email`,`group`,`regdate`,`lastlogin`,`logintimes`,`lastip`,`ban` FROM `user_basic` LIMIT $offset,$count");
        return array($amount, $list);
    }

    public static function search_list($kw, $offset, $count){
        $amount = DB::one("SELECT COUNT(`id`) FROM `user_basic` WHERE `username` LIKE :kw", ['kw' => '%'.$kw.'%']);
        $list   = DB::all("SELECT `id`,`username`,`email`,`group`,`regdate`,`lastlogin`,`logintimes`,`lastip`,`ban` FROM `user_basic` WHERE `username` LIKE :kw LIMIT $offset,$count", ['kw' => '%'.$kw.'%']);
        return array($amount, $list);
    }

    /**
     * 写日志
     * @param  string $type
     * @param  array $data 
     * @return bool      
     */
    public static function log($type, $data = null){
        if($data == null){
            $data = '';
        }else{
            $data = addslashes(json_encode($data, JSON_UNESCAPED_UNICODE));
        }
        return DB::insert(array(
            'type' => $type,
            'uid'  => self::$last['id'],
            'data' => $data,
            'time' => time()
        ), 'log_user');
    }

    public static function errmsg($code){
        switch($code){
            case CODE_USERNAME_EMPTY:
                return '用户名空';
            case CODE_USERNAME_NOT_VALID:
                return '用户名不合法';
            case CODE_EMAIL_EMPTY:
                return '邮箱为空';
            case CODE_EMAIL_NOT_VALID:
                return '邮箱格式不合法';
            case CODE_PASSWORD_EMPTY:
                return '密码为空';
            case CODE_USERNAME_USED:
                return '用户名已被使用';
            case CODE_EMAIL_USED:
                return '邮箱已被使用';
            case CODE_ADMIN_NOLOGIN:
                return '需要管理员登陆';
            default:
                return '未知错误';
        }
    }
    
}