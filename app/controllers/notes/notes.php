<?php

namespace controllers\notes;
use core\view as View;

class Notes extends \core\controller{
	
	public function __construct(){
		parent::__construct();
	}
	public function add( $pid ){
		$mod = new \models\notes\notes();
		$item = new \models\ebay\item();
		$item->get($pid);
		
		if( $_REQUEST['add'] == 1 ){
			$content = $_REQUEST['content'];	
			$mod->createNote($content,$pid);
			if( $_REQUEST['ret'] == "id" ){
				echo $mod->id;
			}else{
				\helpers\url::redirect("ebay/item?id=".$pid);
			}
			return;
		}

		$data['title'] = $item->item[0]->name;
		$data['itemid'] = $item->item[0]->id;
		
		View::rendertemplate('pagePrepare', $data);
		View::render('notes/add',$data);
		View::rendertemplate('pageEnd', $data);
	}
	public function update( $nid ){
		$mod = new \models\notes\notes();
		if( $_REQUEST['up'] == 1 ){
			$content = $_REQUEST['content'];	
			$mod->update($nid , $content);
			\helpers\url::redirect("ebay/item?id=".$mod->data->productid);
			return;
		}
		$data['note'] = $mod->get($nid);
		View::rendertemplate('pagePrepare', $data);
		View::render('notes/update',$data);
		View::rendertemplate('pageEnd', $data);
	}
	public function delete( $nid ){
		$mod = new \models\notes\notes();
		$mod->delete($nid);
		\helpers\url::previous();
	}
}