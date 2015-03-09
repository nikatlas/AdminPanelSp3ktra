<?php

namespace controllers;
use core\view as View;

class Margin extends \core\controller{
	
	public function __construct(){
		parent::__construct();
	}

	public function add(){
		$from = $_REQUEST['from'];
		$to = $_REQUEST['to'];
		$thres = $_REQUEST['threshold'];
		$mod = new \models\margin();
		$mod->add($from , $to , $thres);
		\helpers\url::previous();
	}
	public function update(){
		$id = $_REQUEST['id'];
		$from = $_REQUEST['from'];
		$to = $_REQUEST['to'];
		$thres = $_REQUEST['threshold'];
		$mod = new \models\margin();
		$mod->update($id ,$from , $to , $thres);
		\helpers\url::previous();
	}
	public function delete($id ){
		$mod = new \models\margin();
		$mod->update($id );
		\helpers\url::previous();
	}
	public function index(){
		$mod = new \models\margin();
		$data['margins'] = $mod->getAll();		
				
		View::rendertemplate('pagePrepare', $data);
		View::render('margin',$data);
		View::rendertemplate('pageEnd', $data);
	}
}

?>