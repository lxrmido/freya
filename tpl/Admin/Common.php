<?php

namespace Tpl\Admin;
use Tpl\Config as TplConfig;
use Lib\Core\TPL;

class Common extends TplConfig{

	public function getConfig(){

		$config = TPL::extendConfig('Core/Common', [
            'header_tpl' => $this->dirTpl('Admin/Header'),
            'user'       => \Lib\User\User::$last,
            'frame'      => 'Admin/Common',
            'less'       => [
            	'admin/less/common'
            ],
            'js'         => [
            	'admin/js/common'
            ]
		]);

		return $config;
	}

}