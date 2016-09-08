<?php

namespace Tpl\Admin;
use Tpl\Config as TplConfig;
use Lib\Core\TPL as TPL;

class Auth extends TplConfig{

	public function getConfig(){

		$config = TPL::extendConfig('Admin/Common', [
			'less' => [
				'admin/less/auth'
			],
			'js'   => [
				'admin/js/auth'
			]
		]);

		return $config;
	}

}