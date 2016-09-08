<?php

namespace Tpl\<{$classRoute}>;
use Tpl\Config as TplConfig;
use Lib\Core\TPL as TPL;

class <{$className}> extends TplConfig{

	public function getConfig(){

		$config = TPL::extendConfig('Core/Common', [
			
		]);

		return $config;
	}

}