<?php

namespace Tpl\Core;
use Tpl\Config as TplConfig;
use Lib\Core\TPL as TPL;

class Bp extends TplConfig{

	public function getConfig(){

		$config = TPL::extendConfig('Core/EmptyCommon', [
			'css' => [
				'core/css/b-p'
			],
			'header_tpl' => $this->dirTpl('Core/EmptyHeader'),
            'static_tpl' => $this->dirTpl('Core/StaticCssInLast'),
		]);

		return $config;
	}

}