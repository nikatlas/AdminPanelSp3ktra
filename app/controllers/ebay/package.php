<?php

namespace controllers\ebay;
use core\view as View;

class Package extends \core\controller{
	
	public function __construct(){
		parent::__construct();
	}
	public function index($id){
		$mod = new \models\ebay\package();
		$user = new \models\user\user();
		
		$user->checkPackage($id);
						
		if ( !$mod->get($id) ){
			$data['error'] = 1;
		}
		else{
			$data['packages'] = $mod->packages;
			$data['package'] = $mod->package;
			$data['locals'] = $mod->locals;
			$data['local'] = $mod->local;
			$data['pack'] = $mod->getPackOnly($id);
		}
		
		$data['id'] = $id;
		View::rendertemplate('pagePrepare', $data);
		View::render('ebay/package',$data);
		View::rendertemplate('pageEnd', $data);
	}
	public function listing($page=1){
		$mod = new \models\ebay\package();
		$data['item'] = $mod->getProducts($_REQUEST['user'] , $_REQUEST['title'] , $_REQUEST['itemid'], $_REQUEST['seller'],$page);
		if ( $data['item'] === false ) {
			$data['error'] = 1;	
		}
		$data['totalPages'] = $mod->getTotalPages($_REQUEST['user'] , $_REQUEST['title'] , $_REQUEST['itemid'] , $_REQUEST['seller']);
		
		$data['startid'] = ($page-1)* \helpers\session::get('perPage');
		$user = new \models\user\user();
		$data['users'] = $user->getAll();
		$data['page'] = $page;
		// TODO 
		View::rendertemplate('pagePrepare', $data);
		View::render('ebay/packages',$data);
		View::rendertemplate('pageEnd', $data);
	}
	public function alert($page=1){
		$mod = new \models\ebay\package();
		$data['item'] = $mod->getAlertProducts($_REQUEST['user'] , $_REQUEST['title'] , $_REQUEST['itemid'], $_REQUEST['seller'],$page);
		if ( $data['item'] === false ) {
			$data['error'] = 1;	
		}
		$data['totalPages'] = $mod->getTotalAlertPages($_REQUEST['user'] , $_REQUEST['title'] , $_REQUEST['itemid'] , $_REQUEST['seller']);
		
		$data['startid'] = ($page-1)* \helpers\session::get('perPage');
		$user = new \models\user\user();
		$data['users'] = $user->getAll();
		$data['page'] = $page;
		$data['alert'] = "alert/";
		// TODO 
		View::rendertemplate('pagePrepare', $data);
		View::render('ebay/packages',$data);
		View::rendertemplate('pageEnd', $data);
	}
	public function addItems($id){
		$mod = new \models\ebay\package();
		foreach ( (array)$_POST['itemid'] as $item ){
			$mod->addItem($item , $id);
		}
		$mod->getCurrent($id , true);
		\helpers\url::previous();
	}
	public function addToPackage($itemid , $id){
		$mod = new \models\ebay\package();
		$mod->addToPackage($itemid , $id);
		$mod->getCurrent($id, true );
		\helpers\url::previous();
	}
	public function removeFromPackage($itemid , $id){
		$mod = new \models\ebay\package();
		$mod->removeFromPackage($itemid , $id);
		$mod->getCurrent($id, true );
		\helpers\url::previous();
	}
	public function removeItem($itemid , $id){
		$mod = new \models\ebay\package();
		$mod->removeItem($itemid , $id);
		\helpers\url::previous();
	}
	public function delete($id){
		$mod = new \models\ebay\package();
		$mod->delete($id);
		\helpers\url::redirect("ebay/packages");
	}
	public function updatePrices($pid){
		$mod = new \models\ebay\package();
		$mod->updatePrices($pid);
		\helpers\url::previous();
	}
	
	public function addNew(){
		if( isset($_REQUEST['add']) ){
			if( $_REQUEST['add'] == 1 && $_REQUEST['itemid'] != "" && $_REQUEST['name'] != ""){
					$this->fetch($_REQUEST['itemid'] , $_REQUEST['name']);
			}
		}
		View::rendertemplate('pagePrepare', $data);
		View::render('ebay/addnewpackage',$data);
		View::rendertemplate('pageEnd', $data);
	}
	public function fetch($id , $name){
		$mod = new \models\ebay\package();
		$pid = $mod->create($name);
		$mod->addItem($id , $pid);
		$mod->getCurrent($pid , true );
		\helpers\url::redirect("ebay/package/".$mod->id);
	}
	public function save($id){
		$mod = new \models\ebay\package();
		if( $_POST['vat'] == 'on' )$vat = 1;
		else $vat = 0;
		if( $_POST['big'] == 'on' )$big = 1;
		else $big = 0;
		$rdata = array(
			'vat' => intval($vat),
			'big' => intval($big),
			'weight' => intval($_POST['fweight']),
			'profit' => intval($_POST['fprofit']),
			'insurancecost' => intval($_POST['finsurance'])
		);
		$mod->save($id , $rdata);
		$mod->getCurrent($id , true);

		\helpers\url::previous();
	}
	
	public function alerts($page=1){
		$mod = new \models\ebay\item();
		$data['item'] = $mod->getAlertProducts($page);
		if ( $data['item'] === false ) {
			$data['error'] = 1;	
		}
		$data['totalPages'] = $mod->getTotalAlertPages();
		$data['alertPage'] = true;

		$user = new \models\user\user();
		$data['users'] = $user->getAll();

		$note = new \models\notes\notes();
		foreach( (array)$data['item'] as $item ){
			if( $item->notes != NULL ){
				$nids = explode( ',' , $item->notes );
				$item->notes = array();
				foreach($nids as $id){
					array_push($item->notes ,$note->get($id));
				}
			}
		}

		$data['startid'] = ($page-1)* \helpers\session::get('perPage');

		// TODO 
		View::rendertemplate('pagePrepare', $data);
		View::render('ebay/list',$data);
		View::rendertemplate('pageEnd', $data);
	}
	
}