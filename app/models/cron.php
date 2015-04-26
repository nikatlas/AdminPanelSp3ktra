<?php
namespace models;

class Cron extends \core\model{

	public function __construct(){
		parent::__construct();
		$this->maxPerReq = 20;
	}

	public function run(){
		if ( true || $_SERVER['REMOTE_ADDR'] == '5.9.119.101') {
			$con = mysql_connect(DB_HOST, DB_USER,  DB_PASS) or die("Unable to connect to MySQL");
	                $sdb = mysql_select_db(DB_NAME);
			
			$q = mysql_query("delete from `frame_products_local` WHERE productid not in (SELECT id as productid FROM `".PREFIX."products`)");
			$q = mysql_query("delete from `frame_products_local` WHERE productid is null");
        	        $q = mysql_query("delete from `frame_products_remote` WHERE productid is null");
			
			$q = $this->_db->select("SELECT * FROM ".PREFIX."products_remote WHERE itemid not in( SELECT itemid FROM ".PREFIX."products_local )");
			$model = new \models\ebay\item();	
			foreach( $q as $r ){
				$model->syncItemId($r->itemid, false);
			}
			$q = $this->_db->select("SELECT * FROM ".PREFIX."products_local WHERE outofstock=0 ORDER BY pinned DESC");

			$pages = ceil( (sizeof( $q ) / $this->maxPerReq) );
			for($i=0;$i < $pages;$i ++ ){
				$fflag = $model->syncMultipleItemPrices($q , $i*$this->maxPerReq ,($i+1)*$this->maxPerReq );
				if( $fflag == 33 )//check if ebay is out of API CALLS
					break;
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
