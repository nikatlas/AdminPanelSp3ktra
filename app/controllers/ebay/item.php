<?php

namespace controllers\ebay;
use core\view as View;

class Item extends \core\controller{
	
	public function __construct(){
		parent::__construct();
	}
	public function index(){
		$mod = new \models\ebay\item();
		$user = new \models\user\user();
		
		$user->checkProduct($_GET['id']);
						
		if ( !$mod->get($_GET['id']) ){
			$data['error'] = 1;
		}
		else{
			$data['items'] = $mod->items;
			$data['item'] = $mod->item;
		}

		
		$data['notes'] = $mod->getNotes($_GET['id']);
		
		View::rendertemplate('pagePrepare', $data);
		View::render('ebay/item',$data);
		View::rendertemplate('pageEnd', $data);
	}
	public function changeItemMargin(){
		$id = $_REQUEST['mid'];
		$margin = $_REQUEST['margin'];
		$mod = new \models\ebay\item();
		$mod->changeMargin($id , $margin);
		\helpers\url::previous();
	}
	public function changeItemQuantity(){
		$id = $_REQUEST['qid'];
		$margin = $_REQUEST['nquantity'];
		$mod = new \models\ebay\item();
		$mod->changeQuantity($id , $margin);
		\helpers\url::previous();
	}
	public function updatePrices($pid){
		$mod = new \models\ebay\item();
		$mod->updatePrices($pid);
		//\helpers\url::previous();
	}
	public function backToActive($id){
		$mod = new \models\ebay\item();
		$mod->restore($id);
	}
	public function sendToOutOfStock($id){
		$mod = new \models\ebay\item();
		$mod->outofstock($id);
	}
	public function listing($status="",$page=1){
		if( $status == "page" )$status = "";
		$user = new \models\user\user();
		if( !isset($_REQUEST['user']) )$_REQUEST['user'] = $user->id;
		$mod = new \models\ebay\item();
		$data['item'] = $mod->getProducts($status, $_REQUEST['user'] , $_REQUEST['title'] , $_REQUEST['itemid'], $_REQUEST['seller'],$page);
		if ( $data['item'] === false ) {
			$data['error'] = 1;	
		}
		$data['totalPages'] = $mod->getTotalPages($status, $_REQUEST['user'] , $_REQUEST['title'] , $_REQUEST['itemid'] , $_REQUEST['seller']);
		$data['results'] = $mod->getResultsNum($status, $_REQUEST['user'] , $_REQUEST['title'] , $_REQUEST['itemid'], $_REQUEST['seller']);
		$data['status'] = $status;
		
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
		if ( $status == "changed" )$data['alertPage'] = true;

		$data['startid'] = ($page-1)* \helpers\session::get('perPage');
		$data['users'] = $user->getAll();
		// TODO 
		View::rendertemplate('pagePrepare', $data);
		View::render('ebay/list',$data);
		View::rendertemplate('pageEnd', $data);
	}
	public function alerts($page=1){
		$mod = new \models\ebay\item();
		$user = new \models\user\user();
		if( !isset($_REQUEST['user']) )$_REQUEST['user'] = $user->id;
		$data['item'] = $mod->getAlertProducts('', $_REQUEST['user'] , $_REQUEST['title'] , $_REQUEST['itemid'], $_REQUEST['seller'],$page);
		if ( $data['item'] === false ) {
			$data['error'] = 1;	
		}
		$data['totalPages'] = $mod->getTotalAlertPages('', $_REQUEST['user'] , $_REQUEST['title'] , $_REQUEST['itemid'] , $_REQUEST['seller']);
		$data['alertPage'] = true;
		$data['results'] = $mod->getAlertsNum('', $_REQUEST['user'] , $_REQUEST['title'] , $_REQUEST['itemid'], $_REQUEST['seller']);
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
	
	public function addNew(){
		if( isset($_REQUEST['add']) ){
			if( $_REQUEST['add'] == 1 ){
					$this->fetch($_REQUEST['itemid']);
			}
		}
		
		View::rendertemplate('pagePrepare', $data);
		View::render('ebay/addnew',$data);
		View::rendertemplate('pageEnd', $data);
	}
		
	public function fetch($id){
		$mod = new \models\ebay\item();
		$mod->addItem($id);
		$mod->syncItemId($_REQUEST['itemid'] );
		\helpers\url::redirect("ebay/item?id=".$mod->id);
	}
	public function pin(){
		$mod = new \models\ebay\item();
		$mod->pin($_GET['id']);
		\helpers\url::previous();
	}
	public function delete(){
		$mod = new \models\ebay\item();
		$mod->delete($_GET['id']);
		\helpers\url::previous();
	}
	public function deleteAll(){
		$mod = new \models\ebay\item();
		$mod->deleteAll($_GET['id']);
		\helpers\url::redirect("ebay/list");
	}
	public function save(){
		$mod = new \models\ebay\item();
		if( $_POST['vat'] == 'on' )$vat = 1;
		else $vat = 0;
		if( $_POST['big'] == 'on' )$big = 1;
		else $big = 0;
		$rdata = array(
			'vat' => $vat,
			'big' => $big,
			'weight' => $_POST['fweight'],
			'profit' => $_POST['fprofit'],
			'insurancecost' => $_POST['finsurance']
		);
		$id = $_POST['id'];
		$mod->save($id, $rdata);
		
		\helpers\url::previous();
	}
	public function fetchToId(){
		$mod = new \models\ebay\item();
		foreach( (array)$_REQUEST['itemid'] as $itemid ){
			if( $itemid == "" ) continue;
			$mod->addItemIdtoItem($itemid,$_REQUEST['id']);
			$mod->syncItemId($itemid , false);
		}
		\helpers\url::redirect("ebay/item?id=".$_REQUEST['id']);
	}
	
}
