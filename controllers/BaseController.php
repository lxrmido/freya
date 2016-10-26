<?php


namespace Controller;

use Lib\Core\IO;
use Lib\Core\TPL;
use Lib\Core\Http;


class BaseController{


	/**
	 * 显示模板页面
	 * @param  string $tplName 模板名称
	 * @param  array  $config  参数列表
	 * @return [type]          [description]
	 */
	public function show($tplName, $config = []){
		return TPL::show($tplName, $config);
	}

	/**
	 * 输出对应URL
	 * @param  string $c    控制器
	 * @param  string $a    动作
	 * @param  array  $args 参数列表
	 * @return string       URL
	 */
	public function url($c, $a, $args = []){
		return Http::makeURL($c, $a, $args);
	}

	/**
	 * 跳转页面
	 * @param  string $c    控制器
	 * @param  string $a    动作
	 * @param  array  $args 参数列表
	 * @return [type]       [description]
	 */
	public function routeTo($c, $a, $args = []){
		header('Location:' . $this->url($c, $a, $args));
		die();
	}

	/** 
	 * 获取$_REQUEST中的变量
	 * @param string $var_name 变量名, $_REQUEST[$var_name]
	 * @param var    $pre_set  预设值，当此值不为null且$_REQUEST[$var_name]不为空时返回预设值
	 * @param string $var_type 变量类型，可为：'string'、'uint'、'bool'、'int'、'date'、'html'
	 *                                   string : 返回为字符串
	 *                                   int    : 返回为整数
	 *                                   date   : 将字符串或者整数规格化为时间戳
	 * @return $var
	 */
	public function i($var_name, $pre_set = null, $var_type = 'string'){
		return IO::I($var_name, $pre_set, $var_type);
	}

	/**
	 * 返回信息
	 * 当此方法在AJAX响应中被调用时，将抛出JSON编码的返回信息
	 * 被直接访问时，以HTML格式输出返回信息
	 * 在IO::M()中调用时，将返回PHP变量
	 *
	 * @param int $code 错误码 大于0时表示正确返回，不大于0时表示出错返回
	 * @param var $args 返回参数，当错误码不大于0时，此参数为表示出错原因的字符串;
	 *                            当错误码大于0时，此参数为包含返回值的一个array()
	 */
	public function o($code = 1, $args = []){
		return IO::O($code, $args);
	}

	/**
	 * 抛出错误信息
	 * @param  integer $code    错误码
	 * @param  string  $message 错误消息
	 * @return [type]           [description]
	 */
	public static function e($code = -1, $message = '出错！'){
		return IO::E($code, $message);
	}

	public function getDesc(){
		return [
			'desc'    => 'Default',
			'actions' => [

			]
		];
	}

}