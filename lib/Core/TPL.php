<?php

namespace Lib\Core;

class TPL{

    public function __construct(){

    }

    public function url($params){
        $c  = $params['c'];
        unset($params['c']);
        if(empty($params['a'])){
            $a = 'main';
        }else{
            $a = $params['a'];
            unset($params['a']);
        }
        return Http::makeURL($c, $a, $params);
    }

    public function duration($params){
        $time = intval($params['time']);
        $ms = 0;
        if(isset($params['ms'])){
            $ms   = $time - intval($time / 1000) * 1000;
            $time = intval($time / 1000);
        }
        $h = 0;
        $m = 0;
        if($time > 3600){
            $h = intval($time / 3600);
            $time -= $h * 3600;
        }
        if($time > 60){
            $m = intval($time / 60);
            $time -= $m * 60;
        }
        $s = sprintf('%02s:%02s:%02s', $h, $m, $time);
        if($ms > 0){
            $s .= '.' . $ms;
        }
        return $s;
    }

    public function filesize($params){
        $size = intval($params['size']);
        if($size > 1024 * 1024){
            return number_format($size / (1024 * 1024), 2) . 'MB';
        }
        if($size > 1024){
            return number_format($size / 1024, 2) . 'KB';
        }
        return $size . 'Bytes';
    }

    public function datadir($params){
        return WEBSITE_URL_DATA . $params['folder'];
    }

    public static function show($tpl_name, $config = []){

        $default_config = [
            'website_url_root' => WEBSITE_URL_ROOT,
            'title'       => TPL_DEFAULT_TITLE,
            'js'          => [],
            'css'         => [],
            'less'        => [],
            'default_js'  => [],
            'default_css' => [],
            'ext_js'      => [],
            'ext_css'     => [],
            'plugin'      => [],
            'frame'       => 'Core/Common',
            'static_version' => STATIC_VERSION
        ];

                
        $tpl_config = self::loadConfig($tpl_name, $config);
        
        if($tpl_config != false){
            foreach($tpl_config as $key => $value){
                $default_config[$key] = $value;
            }
        }
                
        foreach($default_config as $key => $value) {
            if(!isset($config[$key])){
                $config[$key] = $default_config[$key];
            }
        }
        
        # JS
        foreach ($config['default_js'] as $jsf) {
            $config['js'][] = $jsf;
        }
        $config['js'] = ($config['js']);

        # CSS
        foreach ($config['default_css'] as $csf) {
            $config['css'][] = $csf;
        }
        $config['css'] = ($config['css']);
        
        $tpl = new TPL();

        $s = new \Smarty;

        $s->setLeftDelimiter('<{');
        $s->setRightDelimiter('}>');
        $s->setTemplateDir(RUNTIME_DIR_TPL);
        $s->setCompileDir(RUNTIME_DIR_CACHE);

        $s->registerObject('tpl', $tpl);
        
        global $_RG;

        if(!empty($_RG['user']['salt'])){
            $_RG['user'] = User::low_safe($_RG['user']);
        }

        $s->assign($config);
        $s->assign('_RG', $_RG);
        $s->assign('_RG_JSON', json_encode($_RG, JSON_UNESCAPED_UNICODE));
        if(FREYA_DEBUG){
            $s->assign('_CF_JSON', json_encode($config, JSON_UNESCAPED_UNICODE));
        }else{
            $s->assign('_CF_JSON', "null");
        }
        $s->assign('COMPILE_LESS', COMPILE_LESS);
        $s->assign('tpl_name', $tpl_name . '.html');
        $s->display(self::getDir($config['frame']));
        die();
    }
    
    public static function extendConfig($origin, $added){

        if(is_string($origin)){
            $origin = self::loadConfig($origin);
        }

        if(is_string($added)){
            $added = self::loadConfig($added);
        }


        foreach($added as $key => $value){
            if(!isset($origin[$key])){
                $origin[$key] = $value;
            }else{
                if(is_array($value)){
                    foreach($value as $v){
                        $origin[$key][] = $v;
                    }
                }else{
                    $origin[$key] = $value;
                }
            }
        }
        return $origin;
    }
    
    public static function loadConfig($tpl_name, $config = null){
        $class_name = 'Tpl\\'.str_replace('/', '\\', $tpl_name);
        $config_class = new $class_name;
        if($config === null){
            return $config_class->getConfig();
        }else{
            return $config_class->getConfig($config);
        }
    }

    public static function getDir($dir){
        return RUNTIME_DIR_TPL . $dir . '.html';
    }
}