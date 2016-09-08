<?php

namespace Tpl\Core;
use Tpl\Config as TplConfig;

class Common extends TplConfig{

	function getConfig(){
        return [
        	'url_root'   => WEBSITE_URL_ROOT,
        	'url_static' => WEBSITE_URL_ROOT . '/' . RUNTIME_DIR_STATIC,
            'css'  => [
                'core/css/font-awesome',
                'core/css/font-entypo',
                'core/css/font-elico',
            ],
            'less' => [
                'core/less/ui'
            ],
            'js'   => [
                'core/js/less',
                'core/js/moment',
                'core/js/jquery',
                'core/js/ui',
                'core/js/config',
                'core/js/global',
                'core/js/common'
            ],
            'navi'       => [],
            'header_tpl' => $this->dirTpl('Core/Header'),
            'static_tpl' => $this->dirTpl('Core/Static'),
            'tpl_basic'  => RUNTIME_DIR_TPL,
            'tpl'        => [
                'Core/UI'
            ]
        ];
    }

}