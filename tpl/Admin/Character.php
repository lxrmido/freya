<?php

namespace Tpl\Admin;
use Tpl\Config as TplConfig;
use Lib\Core\TPL as TPL;

class Character extends TplConfig{

	public function getConfig(){

		$config = TPL::extendConfig('Admin/Common', [
			'less' => [
				'admin/less/character'
			],
			'js' => [
				'admin/js/character'
			]
		]);

		return $config;
	}

}