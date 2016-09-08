<?php

namespace Tpl\Core;
use Tpl\Config as TplConfig;

class EmptyCommon extends TplConfig{

	function getConfig(){
        return [
        	'url_root'   => WEBSITE_URL_ROOT,
        	'url_static' => WEBSITE_URL_ROOT . '/' . RUNTIME_DIR_STATIC,
            'css'  => [
                
            ],
            'less' => [
                
            ],
            'js'   => [
                
            ],
            'navi'       => [],
            'header_tpl' => $this->dirTpl('Core/Header'),
            'static_tpl' => $this->dirTpl('Core/Static'),
            'tpl_basic'  => RUNTIME_DIR_TPL,
            'tpl'        => [
                
            ]
        ];
    }

}