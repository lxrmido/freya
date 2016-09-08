<?php

namespace Tpl\Admin;
use Tpl\Config as TplConfig;
use Lib\Core\TPL as TPL;

class Main extends TplConfig{

	public function getConfig(){

		$config = TPL::loadConfig('Admin/Common');

		return $config;
	}

}