<?php

namespace Lib\Core;

class Http{

	/**
	 * 生成控制器->方法对应的URL
	 */
	public static function makeURL($c, $a, $args = []){
		if(RUNTIME_REWRITE){
			$url = WEBSITE_URL_ROOT . '/' . $c . '/' . $a;
			if(!empty($args)){
				$f = true;
				foreach ($args as $key => $value) {
					if($f){
						$f = false;
						$url .= '?' . $key . '=' . $value;
					}else{
						$url .= '&' . $key . '=' . $value;
					}
				}
			}
		}else{
			$url = WEBSITE_URL_ROOT . '/?c=' . $c . '&a=' . $a;
			if(!empty($args)){
				foreach ($args as $key => $value) {
					$url .= '&' . $key . '=' . $value;
				}
			}
		}
		return $url;
	}

	public static function routeControllerAction($c = false, $a = false, $auth = true){
		if(!$c){
			$c = ucfirst(IO::I('c', 'index'));
		}
		if(!$a){
			$a = IO::I('a', 'main');
		}
		if(
			(preg_match('/^[a-zA-Z0-9_]+$/', $c) !== 1) || 
			(preg_match('/^[a-zA-Z0-9_]+$/', $a) !== 1)  ){
			IO::E(-404, '路由不存在（1）');
		}

		if($auth && (!\Lib\User\Auth::is_ca_ignored($c, $a))){
			\Lib\User\User::auth();
		}

		global $_RG;
		$_RG['controller']      = $c;
		$_RG['action']          = $a;
		$_RG['navi_controller'] = $c;
		$_RG['navi_action']     = $a;
		
		$classRoute = "Controller\\{$c}Controller";
		if(!class_exists($classRoute)){
			IO::E(-404, '路由不存在（2）'.$classRoute);
		}
		$controller = new $classRoute; 
		if(!method_exists($controller, $a)){
			IO::E(-404, '路由不存在（3）');
		}
		
		return $controller->$a();
	}

	public static function getUserIP(){
		return empty($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["REMOTE_ADDR"] : $_SERVER["HTTP_X_FORWARDED_FOR"];
	}

}