<?php

namespace Tpl\User;
use Tpl\Config as TplConfig;
use Lib\Core\TPL as TPL;

class Login extends TplConfig{

	public function getConfig(){

		return TPL::extendConfig('Core/Common', [

			'js'   => [
				'core/js/md5',
				'core/js/ui.vericode',
				'user/js/login',
			],
			'less' => [
				'core/less/ui.vericode',
				'user/less/login',
			]

		]);
	}

}