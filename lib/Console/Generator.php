<?php

namespace Lib\Console;
use Lib\Core\IO as Io;

class Generator{

	public static function controller($name){
		$className = ucfirst($name) . 'Controller';
		$filename  = './controllers/' . $className . '.php';
		if(file_exists($filename)){
			IO::E("Controller file existed.");
		}
		$fileContent = self::compileTemplate('controller.php', [
			'className' => $className
		]);
		if(!self::writeFile($filename, $fileContent)){
			IO::E('Failed to create controller file.');
		}
		return true;
	}

	public static function front($dir){
		$paths = explode('/', $dir);
		$name  = array_pop($paths);
		foreach ($paths as &$p) {
			$p = strtolower($p);
		}
		$path  = implode('/', $paths);
		$route = implode('\\', $paths);
		if(!file_exists('./static/' . $path . '/js')){
			mkdir('./static/' . $path . '/js', 0755, true);
		}
		if(!file_exists('./static/' . $path . '/less')){
			mkdir('./static/' . $path . '/less', 0755, true);
		}
		$resName      = strtolower($name);

		$contentJS    = self::compileTemplate('static.js');
		$contentLess  = self::compileTemplate('static.less');

		$filenameJS   = './static/' . $path . '/js/' . $resName . '.js';
		$filenameLess = './static/' . $path . '/less/' . $resName . '.less';

		if(file_exists($filenameJS) || file_exists($filenameLess)){
			IO::E("File existed.");
		}

		if(self::writeFile($filenameJS, $contentJS)){
			if(self::writeFile($filenameLess, $contentLess)){
				return true;
			}else{
				IO::E('Failed to create less file.');
			}
		}else{
			unlink($filenameJS);
			IO::E('Failed to create js file.');
		}
	}

	public static function tpl($dir){
		$paths = explode('/', $dir);
		$name  = array_pop($paths);
		foreach ($paths as &$p) {
			$p = ucfirst($p);
		}
		$path  = implode('/', $paths);
		$route = implode('\\', $paths);
		if(!file_exists('./tpl/' . $path)){
			mkdir('./tpl/' . $path, 0755, true);
		}
		$className       = ucfirst($name);

		$contentConfig   = self::compileTemplate('tpl.php', [
			'className'  => $className,
			'classRoute' => $route
		]);
		$contentTpl      = self::compileTemplate('tpl.html');

		$filenameConfig  = './tpl/' . $path . '/' . $className . '.php';
		$filenameTpl     = './tpl/' . $path . '/' . $className . '.html';

		if(file_exists($filenameConfig) || file_exists($filenameTpl)){
			IO::E("File existed.");
		}

		if(self::writeFile($filenameConfig, $contentConfig)){
			if(self::writeFile($filenameTpl, $contentTpl)){
				return true;
			}else{
				IO::E('Failed to create tpl file.');
			}
		}else{
			unlink($filenameConfig);
			IO::E('Failed to create config file.');
		}
	}

	public static function writeFile($filename, $fileContent){
		return file_put_contents($filename, $fileContent);
	}


	public static function compileTemplate($name, $data = []){
		$content = self::getTemplate($name);
		foreach ($data as $key => $value) {
			$content = str_replace('<{$'.$key . '}>', $value, $content);
		}
		return $content;
	}

	public static function getTemplate($name){
		$filename = "./dev/templates/$name";
		if(file_exists($filename)){
			return file_get_contents($filename);
		}else{
			IO::E("模板不存在");
		}
	}

}