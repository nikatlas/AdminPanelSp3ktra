<?php

namespace controllers\ebay;
use core\view as View;

class Account extends \core\controller{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function add(){
		$name = $_REQUEST['name'];
		$session = $_REQUEST['session'];
		$mod = new \models\ebay\account();
		$mod->add($name, $session);
		\helpers\url::previous();
	}
	public function update(){
		$id = $_REQUEST['id'];
		$name = $_REQUEST['name'];
		$session = $_REQUEST['session'];
		$mod = new \models\ebay\account();
		$mod->update($id ,$name, $session);
		\helpers\url::previous();
	}
	public function delete($id){
		$mod = new \models\ebay\account();
		$mod->update($id);
		\helpers\url::previous();
	}
	public function index(){
		$mod = new \models\ebay\account();
		$data['accounts'] = $mod->getAll();		

		View::rendertemplate('pagePrepare', $data);
		View::render('ebay/account',$data);
		View::rendertemplate('pageEnd', $data);
	}
	
}