<?php


namespace Controller;

use Lib\Core\TPL;
use Lib\Core\Http;


class BaseController{



	public function show($tplName, $config = []){

		return TPL::show($tplName, $config);

	}

	public function url($c, $a, $args = []){
		return Http::makeURL($c, $a, $args);
	}

	public function routeTo($c, $a, $args = []){
		header('Location:' . $this->url($c, $a, $args));
	}

	public function getDesc(){
		return [
			'desc'    => 'Default',
			'actions' => [

			]
		];
	}

}