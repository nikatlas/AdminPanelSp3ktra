<?php 
namespace models\ebay;

include_once ( "ebay.php" );
use \helpers\session as Session;


class Item extends \core\model {
	
	public function __construct(){
		parent::__construct();
		$user = new \models\user\user();
		$this->setPin = false;
	}
	public function getUsername($productId){
		$q = $this->_db->select("SELECT username FROM ".PREFIX."products INNER JOIN ".PREFIX."users ON ".PREFIX."users.id=".PREFIX."products.user  WHERE ".PREFIX."products.id=:id" , array( ":id" => $productId ) );
		if( $q[0] == NULL ){
			return "No such user!(ID:".$productId.")";	
		}
		return $q[0]->username;	
	}
	public function getUserPrivs($productId){
		$q = $this->_db->select("SELECT privs FROM ".PREFIX."products INNER JOIN ".PREFIX."users ON ".PREFIX."users.id=".PREFIX."products.user  WHERE ".PREFIX."products.id=:id" , array( ":id" => $productId ) );
		if( $q[0] == NULL ){
			return "No such user!(ID:".$productId.")";	
		}
		return $q[0]->privs;	
	}
	public function test(){
		return $this->checkCreateTables();
	}
	public function changeMargin($id , $margin){
		$data = array(
			'margin' => $margin
		);
		$where = array(
			'id' => $id
		);
		$q = $this->_db->update(PREFIX."products_local" , $data , $where);
	}
	public function changeQuantity($id , $margin){
		if( $margin == "" ) $margin = NULL;
		$data = array(
			'nquantity' => $margin
		);
		$where = array(
			'id' => $id
		);
		$q = $this->_db->update(PREFIX."products_local" , $data , $where);
	}
	public function restore($id){
		$user = new \models\user\user();
		$q = $this->_db->select("SELECT * FROM ".PREFIX."products_local WHERE productid=:id" , array( ":id" => intval($id) ) );
		if( $q[0] == NULL ){
			return false;
		}
		$data = array(
			'changed' => 0,
			'outofstock' => 0,
			'alert' => 0
		);
		$where = array(
			'productid' => $id
		);
		$q = $this->_db->update(PREFIX."products_local" , $data , $where);
	}
	public function outofstock($id){
		$user = new \models\user\user();
		$q = $this->_db->select("SELECT * FROM ".PREFIX."products_local WHERE productid=:id" , array( ":id" => intval($id) ) );
		if( $q[0] == NULL ){
			return false;
		}
		$data = array(
			'outofstock' => 1
		);
		$where = array(
			'productid' => $id
		);
		$q = $this->_db->update(PREFIX."products_local" , $data , $where);
	}

	public function getProducts($status="", $user='' , $title ='' , $itemid='',$seller='' , $page = 1){
		$perPage = DEFAULT_PER_PAGE;
		if( Session::get('perPage') != "" ) 
			$perPage = intval(Session::get('perPage'));
		//$perPage = 1;
		$start = ($page - 1) * $perPage;
		// Hardcoded statuses
		$out = 0;
		if ( $status == "outofstock" ){
			$out = 1;
			$status ="";
		}
		$changed = 0;
		if ( $status == "changed" ){
			$changed = 1;
			$status ="";
		}
		////////
		$q = $this->_db->select("SELECT * FROM ".PREFIX."products INNER JOIN( SELECT * FROM ".PREFIX."products_local WHERE pinned = 1 AND alert = 0  AND listing LIKE :status AND outofstock=:out AND changed=:changed  AND name LIKE :title AND itemid LIKE :itemid AND seller LIKE :seller  ) b ON b.productid = ".PREFIX."products.id INNER JOIN ".PREFIX."users ON ".PREFIX."products.user=".PREFIX."users.id INNER JOIN ( SELECT productid,GROUP_CONCAT(seller ORDER BY pinned DESC SEPARATOR ',') AS listings,GROUP_CONCAT(itemid ORDER BY pinned DESC SEPARATOR ',') AS listingIds FROM ".PREFIX."products_local GROUP BY productid) c ON c.productid=".PREFIX."products.id  LEFT JOIN (SELECT productid AS csid,GROUP_CONCAT(id SEPARATOR ',' ) AS notes FROM ".PREFIX."notes GROUP BY productid ) d ON d.csid=".PREFIX."products.id WHERE user LIKE :user LIMIT :start,:perPage" , array(
				":changed" => intval($changed),
				":out"    => intval($out),
				":status" =>'%'.$status,
				":itemid" =>'%'.$itemid,
				":title" =>'%'.$title.'%',
				":seller" =>'%'.$seller.'%',
				":user" =>'%'.$user,
				":start"  =>$start,
				":perPage"=>$perPage					
			 ) );
		
		if ( $q[0] == NULL ){return false;}		
		return $q;
	}
	public function getResultsNum($status="", $user='' , $title ='' , $itemid='',$seller=''){
		// Hardcoded statuses
		$out = 0;
		if ( $status == "outofstock" ){
			$out = 1;
			$status ="";
		}
		$changed = 0;
		if ( $status == "changed" ){
			$changed = 1;
			$status ="";
		}
		////////
		$q = $this->_db->select("SELECT *,COUNT(*) AS maniac FROM ".PREFIX."products INNER JOIN( SELECT * FROM ".PREFIX."products_local WHERE pinned = 1 AND listing LIKE :status AND outofstock=:out AND changed=:changed  AND name LIKE :title AND itemid LIKE :itemid AND seller LIKE :seller  ) b ON b.productid = ".PREFIX."products.id INNER JOIN ".PREFIX."users ON ".PREFIX."products.user=".PREFIX."users.id INNER JOIN ( SELECT productid,GROUP_CONCAT(seller ORDER BY pinned DESC SEPARATOR ',') AS listings,GROUP_CONCAT(itemid ORDER BY pinned DESC SEPARATOR ',') AS listingIds FROM ".PREFIX."products_local GROUP BY productid) c ON c.productid=".PREFIX."products.id  LEFT JOIN (SELECT productid AS csid,GROUP_CONCAT(id SEPARATOR ',' ) AS notes FROM ".PREFIX."notes GROUP BY productid ) d ON d.csid=".PREFIX."products.id WHERE user LIKE :user" , array(
				":changed" => intval($changed),
				":out"    => intval($out),
				":status" =>'%'.$status,
				":itemid" =>'%'.$itemid,
				":title" =>'%'.$title.'%',
				":seller" =>'%'.$seller.'%',
				":user" =>'%'.$user			
			 ) );
		if ( $q[0] == NULL ){return false;}		
		return $q[0]->maniac;
	}
	public function getAlertsNum($status="", $user='' , $title ='' , $itemid='',$seller=''){
		// Hardcoded statuses
		$out = 0;
		if ( $status == "outofstock" ){
			$out = 1;
			$status ="";
		}
		$changed = 0;
		if ( $status == "changed" ){
			$changed = 1;
			$status ="";
		}
		////////
		$q = $this->_db->select("SELECT *,COUNT(*) AS maniac FROM ".PREFIX."products INNER JOIN( SELECT * FROM ".PREFIX."products_local WHERE pinned = 1 AND alert=1 AND listing LIKE :status  AND name LIKE :title AND itemid LIKE :itemid AND seller LIKE :seller  ) b ON b.productid = ".PREFIX."products.id INNER JOIN ".PREFIX."users ON ".PREFIX."products.user=".PREFIX."users.id INNER JOIN ( SELECT productid,GROUP_CONCAT(seller ORDER BY pinned DESC SEPARATOR ',') AS listings,GROUP_CONCAT(itemid ORDER BY pinned DESC SEPARATOR ',') AS listingIds FROM ".PREFIX."products_local GROUP BY productid) c ON c.productid=".PREFIX."products.id  LEFT JOIN (SELECT productid AS csid,GROUP_CONCAT(id SEPARATOR ',' ) AS notes FROM ".PREFIX."notes GROUP BY productid ) d ON d.csid=".PREFIX."products.id WHERE user LIKE :user" , array(
				":status" =>'%'.$status,
				":itemid" =>'%'.$itemid,
				":title" =>'%'.$title.'%',
				":seller" =>'%'.$seller.'%',
				":user" =>'%'.$user			
			 ) );
		if ( $q[0] == NULL ){return false;}		
		return $q[0]->maniac;
	}
	public function getTotalPages($status="",$user='' , $title ='' , $itemid='' , $seller=''){
		$perPage = DEFAULT_PER_PAGE;
		$out = 0;
		if ( $status == "outofstock" ){
			$out = 1;
			$status ="";
		}
		$changed = 0;
		if ( $status == "changed" ){
			$changed = 1;
			$status ="";
		}
				if( Session::get('perPage') != "" ) 
			$perPage = intval(Session::get('perPage'));
		$q = $this->_db->select("SELECT COUNT(*) AS mx FROM ".PREFIX."products_local INNER JOIN ".PREFIX."products ON ".PREFIX."products.id=".PREFIX."products_local.productid INNER JOIN ".PREFIX."users ON ".PREFIX."users.id=".PREFIX."products.user WHERE pinned=1 AND outofstock=:out AND changed=:changed AND listing LIKE :status AND itemid LIKE :itemid AND name LIKE :title AND ".PREFIX."users.id LIKE :user AND seller LIKE :seller" , 
		array(   ":changed" => intval($changed),
				 ":out" => $out,
				 ":status" => '%'.$status,
				":itemid" =>'%'.$itemid,
				":title" =>'%'.$title.'%',
				":seller" =>'%'.$seller.'%',
				":user" =>'%'.$user,
		) );	

		$r = ceil($q[0]->mx / $perPage);
		return $r;
	}
	public function getAlertProducts($status="", $user='' , $title ='' , $itemid='',$seller='' , $page = 1){
		$perPage = DEFAULT_PER_PAGE;
		if( Session::get('perPage') != "" ) 
			$perPage = intval(Session::get('perPage'));
		$start = ($page - 1) * $perPage;

		$q = $this->_db->select("SELECT * FROM ".PREFIX."products INNER JOIN( SELECT * FROM ".PREFIX."products_local WHERE pinned = 1 AND listing LIKE :status  AND name LIKE :title AND itemid LIKE :itemid AND seller LIKE :seller  ) b ON b.productid = ".PREFIX."products.id INNER JOIN ".PREFIX."users ON ".PREFIX."products.user=".PREFIX."users.id INNER JOIN ( SELECT productid,GROUP_CONCAT(seller ORDER BY pinned DESC SEPARATOR ',') AS listings,GROUP_CONCAT(itemid ORDER BY pinned DESC SEPARATOR ',') AS listingIds FROM ".PREFIX."products_local GROUP BY productid) c ON c.productid=".PREFIX."products.id  LEFT JOIN (SELECT productid AS csid,GROUP_CONCAT(id SEPARATOR ',' ) AS notes FROM ".PREFIX."notes GROUP BY productid ) d ON d.csid=".PREFIX."products.id WHERE user LIKE :user AND alert = 1  LIMIT :start,:perPage" , array(
				":status" =>'%'.$status,
				":itemid" =>'%'.$itemid,
				":title" =>'%'.$title.'%',
				":seller" =>'%'.$seller.'%',
				":user" =>'%'.$user,
				":start"  =>$start,
				":perPage"=>$perPage					
			 ) );
		if ( $q[0] == NULL ){return false;}		
		return $q;
	}	
	public function getTotalAlertPages($status="",$user='' , $title ='' , $itemid='' , $seller=''){
		$perPage = DEFAULT_PER_PAGE;
		if( Session::get('perPage') != "" ) 
			$perPage = intval(Session::get('perPage'));
		$q = $this->_db->select("SELECT COUNT(*) AS mx FROM ".PREFIX."products_local INNER JOIN ".PREFIX."products ON ".PREFIX."products.id=".PREFIX."products_local.productid INNER JOIN ".PREFIX."users ON ".PREFIX."users.id=".PREFIX."products.user WHERE pinned=1 AND listing LIKE :status AND itemid LIKE :itemid AND name LIKE :title AND ".PREFIX."users.id LIKE :user AND seller LIKE :seller AND alert=1" , 
		array(  
				 ":status" => '%'.$status,
				":itemid" =>'%'.$itemid,
				":title" =>'%'.$title.'%',
				":seller" =>'%'.$seller.'%',
				":user" =>'%'.$user,
		) );	
		$r = ceil($q[0]->mx / $perPage);
		return $r;
	}
	public function save($id , $data){
		$user = new \models\user\user();

		$q = $this->_db->select("SELECT * FROM ".PREFIX."products_local WHERE productid=:id" , array( ":id" => intval($id) ) );
		if( $q[0] == NULL ){
			return false;
		}

		$where = array(
			'productid' => $id,
		);
		$this->_db->update(PREFIX."products_local" , $data , $where);
		
		return true;
	}
	public function delete($id){
		$user = new \models\user\user();
		$q = $this->_db->select("SELECT * FROM ".PREFIX."products_local WHERE id=:id" , array( ":id" => intval($id) ) );
		if( $q[0] == NULL ){
			return false;
		}
		$s = $this->_db->select("SELECT * FROM ".PREFIX."products_remote WHERE itemid=:id" , array( ":id" => intval($q[0]->itemid) ) );
		$where = array( 
			'id' => $id,
		);
		$this->_db->delete(PREFIX."products_local" , $where);
		$where = array( 
			'id' => $s[0]->id,
		);
		$this->_db->delete(PREFIX."products_remote" , $where);

		return true;
	}
	public function deleteAll($id){
		$user = new \models\user\user();
		$q = $this->_db->select("SELECT * FROM ".PREFIX."products_local WHERE productid=:id" , array( ":id" => intval($id) ) );
		if( $q[0] == NULL ){
			return false;
		}
		$where = array(
			'productid' => $id
		);
		$this->_db->delete(PREFIX."products_local" , $where);
		$this->_db->delete(PREFIX."products_remote" , $where);
		$where = array(
			'id' => $id
		);		
		$this->_db->delete(PREFIX."products" , $where);		
		return true;
	}
	public function pin($id){
		$user = new \models\user\user();

		$q = $this->_db->select("SELECT * FROM ".PREFIX."products_local WHERE id=:id" , array( ":id" => intval($id) ) );
		if( $q[0] == NULL ){
			return false;
		}
		
		$data = array(
			'pinned' => 0
		);
		$where = array(
			'productid' => $q[0]->productid,
		);
		$this->_db->update(PREFIX."products_local" , $data , $where);
		
		$data = array(
			'pinned' => 1
		);
		$where = array(
			'id' => $id,
		);
		$this->_db->update(PREFIX."products_local" , $data , $where);
		
		return true;
	}
	public function getNotes($id){
		$note = new \models\notes\notes();
		$q = $this->_db->select("SELECT * FROM ".PREFIX."notes WHERE productid=:id" , array( ":id" => $id ) );
		$notes = array();
		foreach( (array)$q as $id){
			array_push($notes ,$note->get($id->id));
		}	
		$this->notes = $notes;
		return $notes;
	}
	public function get($id){
		$user = new \models\user\user();		
		$q = $this->_db->select("SELECT * FROM ".PREFIX."products_local WHERE productid=:id  ORDER BY pinned DESC" , array( ":id" => $id ) );
		if( $q[0] == NULL ){
			return false;
		}
		$this->item = $q;
		$this->items = sizeof ($q);
		return true;
	}
	public function getByItemId($id){
		$user = new \models\user\user();
		$q = $this->_db->select("SELECT * FROM ".PREFIX."products_local WHERE itemid=:id" , array( ":id" => $id ) );
		if ( $q[0] == NULL ) return false;
		return $q[0];
	}
	public function addItem($itemId){
		$user = new \models\user\user();		
		$q = $this->_db->select("SELECT * FROM ".PREFIX."products_remote WHERE itemid=:id" , array( ":id" => $itemId ) );
		if( $q[0] != NULL ){
			$this->id = $q[0]->productid;
			return false;
		}
		$q = $this->_db->select("SELECT * FROM ".PREFIX."products_local WHERE itemid=:id" , array( ":id" => $itemId ) );
		if( $q[0] != NULL ){
			$this->id = $q[0]->productid;
			$rdata = array(
				'user' => $user->id,
				'productid'  => $this->id,
				'itemid'  => $itemId,
				'time_created'     => date("Y-m-d H:i:s")
			);
			$q = $this->_db->insert(PREFIX."products_remote" , $rdata);
			return false;
		}
		
		$postdata = array(
			'user' => $user->id,
			'status'  => 'active',
			'time_created'     => date("Y-m-d H:i:s")
		);
		$q = $this->_db->insert(PREFIX."products" , $postdata);
		
		$this->id = $this->_db->lastInsertId();
		
		$rdata = array(
			'user' => $user->id,
			'productid'  => $this->id,
			'itemid'  => $itemId,
			'time_created'     => date("Y-m-d H:i:s")
		);
		$q = $this->_db->insert(PREFIX."products_remote" , $rdata);
		return true;
	}
	public function addItemIdtoItem($itemId, $id){
		$user = new \models\user\user();
		$q = $this->_db->select("SELECT * FROM ".PREFIX."products_remote WHERE itemid=:id" , array( ":id" => $itemId ) );
		if( $q[0] != NULL ){
			$this->id = $q[0]->productid;
			$this->setPin = false;
			return false;
		}

		$rdata = array(
			'user' => $user->id,
			'productid'  => $id,
			'itemid'  => $itemId,
			'time_created'     => date("Y-m-d H:i:s")
		);
		$q = $this->_db->insert(PREFIX."products_remote" , $rdata);
	}
	public function checkRemoteItem($itemId){
		$q = $this->_db->select("SELECT * FROM ".PREFIX."products_remote WHERE itemid=:id", array( ':id' => $itemId ) );
		if( $q[0] == NULL ){
			return false;
		}
		return true;
	}
	public function getRemoteItem($itemId){
		$q = $this->_db->select("SELECT * FROM ".PREFIX."products_remote WHERE itemid=:id", array( ':id' => $itemId ) );
		if( $q[0] == NULL ){
			return NULL;
		}
		return $q[0];
	}
	public function isPinned($itemId){
		$q = $this->_db->select("SELECT * FROM ".PREFIX."products_local WHERE itemid=:id", array( ':id' => $itemId ) );
		if( $q[0] == NULL ){
			return false;
		}
		return ($q[0]->pinned==1);	
	}
	public function updatePrices($pid){
		global $appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID;
		initKeys();
		session_start();
	   	$ebay = new Ebay($appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID);
		
		$s = $this->_db->select("SELECT * FROM ".PREFIX."products_local WHERE productid=:pid AND pinned = 1" , array( ':pid' => $pid ) );	
		$q = $this->_db->select("SELECT * FROM ".PREFIX."products_local WHERE productid=:pid AND pinned != 1" , array( ':pid' => $pid ) );	
		$ebay->updateListingPrices($q , $s[0]->currentprice , $s[0]->shippingcost);
		$qqq=array();
		foreach( (array)$q as $item ){
			array_push($qqq , $item);
		}		
		$sss = $this->syncMultipleItemPrices($qqq , 0 , sizeof($qqq) );
		return $sss;
	}
	
	public function checkCreateLocalItem($itemId){
		$user = new \models\user\user();

		$q = $this->_db->select("SELECT * FROM ".PREFIX."products_local WHERE itemid=:id", array( ':id' => $itemId ) );
		if( $q[0] == NULL ){
			$e = $this->_db->select("SELECT *,COUNT(*) AS number FROM ".PREFIX."products_local WHERE productid=:id AND pinned=1" , array( ':id' => $this->getRemoteItem($itemId)->productid) );
			$rdata = array(
				'productid'  => $this->getRemoteItem($itemId)->productid,
				'itemid'  => $itemId,
				'vat' => 0,
				'big' => 0
			);
			if( $e[0]->number == 0 ){
				$rdata['pinned'] = 1;
			}
			else{
				$rdata['profit'] = $e[0]->profit;
				$rdata['insurancecost'] =$e[0]->insurancecost;
				$rdata['weight'] =$e[0]->weight;
				$rdata['vat'] =$e[0]->vat;
				$rdata['big'] =$e[0]->big;
			}
			$q = $this->_db->insert(PREFIX."products_local" , $rdata);
			return false;
		}
		return $q;
	}
	// ( $arr , $from , $to ) $arr -> itemids from $from to $to , $to excluded
	public function syncMultipleItemPrices($arr, $from, $to){
		$itemids = array();
		for($i = $from ; $i < $to && $i < sizeof($arr) ; $i ++ ){
			array_push( $itemids , $arr[$i]->itemid );
		}			
		// API request variables
		$endpoint = 'http://open.api.ebay.com/shopping?';  // URL to call
		$version = '837';  // API version supported by your application
		$appid = 'IancuAnd-91a6-479a-a73a-e7377631f212';  // Replace with your own AppID
		//if is evokt itemid change to 77
		$globalid = '0';  // Global ID of the eBay site you want to search (e.g., EBAY-DE)		
		// Construct the findItemsByKeywords HTTP GET call
		$apicall = "$endpoint";
		$apicall .= "callname=GetMultipleItems&responseencoding=XML";
		$apicall .= "&appid=$appid";
		$apicall .= "&siteid=$globalid";
		$apicall .= "&version=$version";
		$apicall .= "&ItemID=".implode(',', $itemids)."&IncludeSelector=Details";
		$z = 0;
		$resp = simplexml_load_file($apicall);  // load xml file from ebay api
		if( $resp->Ack == "PartialFailure" ){
			foreach( (array)$itemids as $itemid ){
					$this->syncItemId($itemid);
					$zz ++ ;
			}
			return;
		}
		else if ( $resp->Ack != "Success" ){
			//exit( " ERROR ON API CALL :".var_dump($resp));
			//var_dump($resp);
			return 33; // Ebay Error maybe limit has been reached
		}
		$margins = new \models\margin();
		$items = $resp->Item;
		foreach( $items as $item ){
			$changed = 0;
			$alert = 0;
			$flag = $this->checkCreateLocalItem($item->ItemID);
			if( $flag != false && $flag[0]->pinned == 1){
				$oldprice = $flag[0]->currentprice;
				// NEW LINE
				$name = $flag[0]->name;
				if ( $oldprice == $item->CurrentPrice && name == $item->Title){ //Title on ebay api name on us! ebay->GetItemDATA switch this by itself
					$oldflag = false;	
				}
				else{
					$oldflag = true;
					$changed = 1;
					$alert = 0;
					if( $item->CurrentPrice - $oldprice > $margins->getThreshold($oldprice) ){
						$alert = 1;
						$changed = 0;
					}
					//NEED TO GET NEW NAME HERE !
					if( $item->Title != $name ){
						$alert = 1;
						$postdata = array(
							'user' => 1,
							'productid'  => $flag[0]->productid,
							'content' => "Name changed!!!"			
						);
						$qq = $this->_db->insert(PREFIX."notes" , $postdata);
					}
				}
			}
			else{
				$oldprice = 0;	
			}
			$data = array(
				'currentprice' => $item->CurrentPrice,
				'quantity' => $item->Quantity - $item->QuantitySold,
				'listing' => $item->ListingStatus,
				'timeleft' => $item->TimeLeft
			);
			if ( $item->Quantity- $item->QuantitySold == 0 ){
				$alert = 1;
				$data['listing'] = "Completed";	
			}
			if( $oldflag ){
				$data['oldprice'] = $oldprice;
				$data['alert'] = $alert;	
				$data['changed'] = $changed;
			}
			if( $item->ListingStatus == "Completed" ){
					$data['alert'] = 0;	
					$data['changed'] = 0;
			}
			$where = array(
				'itemid' => $item->ItemID,
			);
			$this->_db->update(PREFIX."products_local" , $data , $where);

			$data = array(
				'lastsync' => date("Y-m-d H:i:s")
			);
			$where = array(
				'itemid' => $item->ItemID,
			);
			$this->_db->update(PREFIX."products_remote" , $data , $where);			
		}
		echo $z;
				
	}
	public function syncItemId($itemId,$pinned = false){
		global $appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID;
		initKeys();
		session_start();
	   	$ebay = new Ebay($appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID);
			
		$item = $ebay->getItemData($itemId);
		if( $item == false || $item == -1 ){//Item doesnt exists
					if( $item == false ){
						$where = array(
							'itemid' => $itemId
						);
						//$q = $this->_db->delete(PREFIX."products_remote" , $where);

						$tt = $this->_db->select("SELECT * FROM ".PREFIX."products_local WHERE itemid=:itemid",array(":itemid"=>$itemId));
						if( $tt[0] == NULL )return;
						if( $tt[0]->productid == NULL ) return;
						$rdata = array(
							'alert' => 1,
						);				
						$q = $this->_db->update(PREFIX."products_local" , $rdata , $where);

						$nq = $this->_db->select("SELECT * FROM ".PREFIX."notes WHERE productid=:pid AND user=:user",array(":user"=>1 , ":pid"=>$tt[0]->productid));
						if( $nq[0] == NULL ){
							$postdata = array(
								'user' => 1,
								'productid'  => $tt[0]->productid,
								'content' => "The Item Vanished!!!"			
							);
							$q = $this->_db->insert(PREFIX."notes" , $postdata);
						}
					}
					else{
						echo "LIMIT REACHED!";	
					}
		}
		else{
//TOOO
			$flag = $this->checkRemoteItem($itemId);
			if( !$flag ){return false;}
			$flag = $this->checkCreateLocalItem($itemId);
			if( $flag !== false ){
				$oldprice = $flag[0]->currentprice;
			}
			else{
				$oldprice = 0;	
			}
				
			$rdata = array(
				'lastsync' => date("Y-m-d H:i:s")
			);
			$where = array(
				'itemid' => $itemId,
			);
			$q = $this->_db->update(PREFIX."products_remote" , $rdata , $where);
	
			$a = $item->storeurl;
			$b = $item->url;
			preg_match("/ebay.[a-z.]+/" , $a , $matches );  
			$c = str_replace('ebay.de' , $matches[0] , $b); 
			
			$pnd = (($pinned)?'1':'0');
			$rdata = array(
				'currency' => $item->currency,//ITEM HERE IS FROM THE EBAY API!
				'oldprice' => $oldprice,
				'currentprice' => $item->price,
				'shippingcost' => $item->shippingcost,
				'name' => $item->name,
				'seller' => $item->seller,
				'listing' => $item->listingStatus,
				'sku' => ''.$item->sku,
				'quantity' => $item->quantity,
				'timeleft' => $item->timeleft,
				'endtime' => $item->endtime,
				'category' => $item->categoryName,
				'imgurl' => $item->imgurl,
				'url' => $c,
				'storeurl' => $item->storeurl
			);
			if( $this->setPin ){
				$rdata['pinned'] = $pnd;	
			}
		
			$where = array(
				'itemid' => $itemId,
			);
			$q = $this->_db->update(PREFIX."products_local" , $rdata , $where);		
		}
	}
	public function countItems(){
		$q = $this->_db->select("SELECT COUNT(*) AS max FROM ".PREFIX."products_local WHERE productId!='NULL' AND pinned=1");	
		if( $q[0] == NULL ){return -1;}
		return $q[0]->max;
	}
	public function countItemsByStatus($status){
		return $this->getResultsNum($status);
	}
	public function countAlerts(){
		$q = $this->_db->select("SELECT COUNT(*) AS max FROM ".PREFIX."products_local WHERE productId!='NULL' AND pinned = 1 AND outofstock=0 AND changed = 0 AND alert = 1");	
		if( $q[0] == NULL ){return -1;}
		return $q[0]->max;
	}
	public function countOutOfStock(){
		return $this->getResultsNum("outofstock");
		$q = $this->_db->select("SELECT COUNT(*) AS max FROM ".PREFIX."products_local WHERE productId!='NULL' AND pinned = 1 AND outofstock = 1");	
		if( $q[0] == NULL ){return -1;}
		return $q[0]->max;
	}
	public function countPriceChanges(){
		return $this->getResultsNum("changed");
		$q = $this->_db->select("SELECT COUNT(*) AS max FROM ".PREFIX."products_local WHERE productId!='NULL' AND pinned = 1 AND changed = 1");	
		if( $q[0] == NULL ){return -1;}
		return $q[0]->max;
	}
	private function checkCreateTables(){
		$con = mysql_connect(DB_HOST, DB_USER,  DB_PASS) or die("Unable to connect to MySQL");
		$sdb = mysql_select_db(DB_NAME);

		$result = mysql_query("SHOW TABLES LIKE '".PREFIX."products'");
		$tableExists = mysql_num_rows($result) > 0;
		if( !$tableExists ){
			echo "[*] Creating `products` table... ";
			$result = mysql_query("CREATE TABLE ".PREFIX."products(
				id INT NOT NULL AUTO_INCREMENT,PRIMARY KEY(id),
				user INT,
				status varchar(25),
				time_created DATETIME
			)");
			if ( !$result ){
				echo "ERROR: ".mysql_error();
			}
			echo "<br>";
		}
		
		$result = mysql_query("SHOW TABLES LIKE '".PREFIX."products_local'");
		$tableExists = mysql_num_rows($result) > 0;
		if( !$tableExists ){
			echo "[*] Creating `products_local` table... ";
			$result = mysql_query("CREATE TABLE ".PREFIX."products_local(
				id INT NOT NULL AUTO_INCREMENT,PRIMARY KEY(id),
				productid INT,
				itemid TEXT,
				sku varchar(22),
				currency varchar(10),
				currentprice decimal(11,2),
				buynowprice decimal(11,2),
				shippingcost decimal(11,2) DEFAULT '0',
				insurancecost decimal(11,2),
				profit       decimal(11,2) DEFAULT 0,
				taxes        decimal(11,2),
				totalprice   decimal(11,2),
				recommendedprice decimal(11,2),			
				name LONGTEXT,
				seller TEXT,
				listing TEXT,
				quantity INT,
				timeleft TEXT,
				endtime DATETIME,
				url TEXT,
				weight decimal(11,2) DEFAULT 0,
				category TEXT,
				storecategory TEXT,
				changed INT,
				imgurl TEXT,
				date DATETIME,
				storeurl TEXT,
				pinned INT,
				time_created DATETIME
			)");
			if ( !$result ){
				echo "ERROR: ".mysql_error();
			}
			echo "<br>";
		}

		$result = mysql_query("SHOW TABLES LIKE '".PREFIX."products_remote'");
		$tableExists = mysql_num_rows($result) > 0;
		if( !$tableExists ){
			echo "[*] Creating `products_remote` table... ";
			$result = mysql_query("CREATE TABLE ".PREFIX."products_remote(
				id INT NOT NULL AUTO_INCREMENT,PRIMARY KEY(id),
				user INT,
				productid INT,
				itemid TEXT,
				lastsync DATETIME,
				time_created DATETIME
			)");
			if ( !$result ){
				echo "ERROR: ".mysql_error();
			}
			echo "<br>";
		}
		
	}	
	
}


?>
