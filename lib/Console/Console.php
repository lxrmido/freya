<?php

namespace Lib\Console;

use Lib\Core;

class Console{

	public $argv;

	public function __construct(&$argv){

		if(count($argv) < 2){
			echo 	'freya commands:', "\n",
					'controller $controllerName', "\n",
					'front $frontName', "\n",
					'tpl $tplName', "\n";
			die();
		}

		$func = $argv[1];

		if(method_exists($this, $func)){
			$this->$func($argv);
		}

	}

	public function controller(&$argv){
		if(count($argv) < 3){
			return;
		}
		if(Generator::controller($argv[2])){
			echo 'Controller generated.';
		}
	}

	public function tpl(&$argv){
		if(count($argv) < 3){
			return;
		}
		if(Generator::tpl($argv[2])){
			echo 'Tpl generated.';
		}
	}

	public function front(&$argv){
		if(count($argv) < 3){
			return;
		}
		if(Generator::front($argv[2])){
			echo 'Front files generated.';
		}
	}

	public static function run(&$argv){
		return new Console($argv);
	}

}