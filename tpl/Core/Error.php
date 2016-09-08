<?php

namespace Tpl\Core;
use Tpl\Config as TplConfig;
use Lib\Core\TPL as TPL;

class Error extends TplConfig{

	public function getConfig(){

		$config = TPL::extendConfig('Core/EmptyCommon', [
			'css' => [
				'core/css/error'
			],
			'header_tpl' => $this->dirTpl('Core/EmptyHeader'),
            'static_tpl' => $this->dirTpl('Core/StaticCssInLast'),
		]);

		return $config;
	}

}