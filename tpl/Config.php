<?php

namespace Tpl;

class Config{

	public function getConfig(){
		return [];
	}

	public function dirTpl($dir){
		return RUNTIME_DIR_TPL . $dir . '.html';
	}

}