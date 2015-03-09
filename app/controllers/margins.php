<?php

namespace controllers;
use core\view as View;

class Margin extends \core\controller{
	
	public function __construct(){
		parent::__construct();
	}

	public function add($id , $from , $to , $thres){
		$mod = new \models\margin();
		$mod->add($id , $from , $to , $thres);
		\helpers\url::previous();
	}
	public function update($from , $to , $thres){
		$mod = new \models\margin();
		$mod->update($from , $to , $thres);
		\helpers\url::previous();
	}
	public function delete($id ){
		$mod = new \models\margin();
		$mod->update($id );
		\helpers\url::previous();
	}
	public function index(){
		exit("!");
		$mod = new \models\margin();

		$data['margins'] = $mod->getAll();		
				
		View::rendertemplate('pagePrepare', $data);
		View::render('margins',$data);
		View::rendertemplate('pageEnd', $data);
	}
}

?>