<?php
namespace models;

class Cron extends \core\model{

	public function __construct(){
		parent::__construct();
		$this->maxPerReq = 20;
	}

	public function run(){
		if ( true || $_SERVER['REMOTE_ADDR'] == '5.9.119.101') {
			$q = $this->_db->select("SELECT * FROM ".PREFIX."products_remote WHERE itemid not in( SELECT itemid FROM ".PREFIX."products_local )");
			$model = new \models\ebay\item();	
			foreach( $q as $r ){
				$model->syncItemId($r->itemid, false);
			}
			$q = $this->_db->select("SELECT * FROM ".PREFIX."products_local");

			$pages = ceil( (sizeof( $q ) / $this->maxPerReq) );
			for($i=0;$i < $pages;$i ++ ){
				$model->syncMultipleItemPrices($q , $i*$this->maxPerReq ,($i+1)*$this->maxPerReq );
			}
			
			$q = $this->_db->select("SELECT * FROM ".PREFIX."packages");
			$m = new \models\ebay\package();
			foreach($q as $t){
				$m->cron($t->id);
			}
			$mc = new \models\currency();
			$mc->updateAll();
		echo "0";
		}

	}

}
?>