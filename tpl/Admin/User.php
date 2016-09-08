<?php

namespace Tpl\Admin;
use Tpl\Config as TplConfig;
use Lib\Core\TPL as TPL;

class User extends TplConfig{

	public function getConfig(){

		$config = TPL::extendConfig('Admin/Common', [
			'js'   => [
				'core/js/md5',
				'admin/js/user'
			],
			'less' => [
				'admin/less/user'
			]
		]);

		return $config;
	}

}