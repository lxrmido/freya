<?php

namespace Tpl\Admin;
use Tpl\Config as TplConfig;
use Lib\Core\TPL as TPL;

class UserGroup extends TplConfig{

	public function getConfig(){

		$config = TPL::extendConfig('Admin/Common', [
			'less' => [
				'admin/less/usergroup'
			],
			'js' => [
				'admin/js/usergroup'
			]
		]);

		return $config;
	}

}