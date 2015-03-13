<?php 
namespace models\ebay;

include_once ( "ebay.php" );
use \helpers\session as Session;


class Package extends \core\model {
	
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
	
	public function getProducts( $user='' , $title ='' , $itemid='',$seller='' , $page = 1){
		$perPage = DEFAULT_PER_PAGE;
		if( Session::get('perPage') != "" ) 
			$perPage = intval(Session::get('perPage'));
		//$perPage = 1;
		$start = ($page - 1) * $perPage;
		////////
		$q = $this->_db->select("SELECT ".PREFIX."packages.* ,".PREFIX."users.id AS uid ,".PREFIX."users.username ,".PREFIX."users.privs   FROM ".PREFIX."packages INNER JOIN ".PREFIX."users ON ".PREFIX."packages.user=".PREFIX."users.id WHERE ".PREFIX."packages.name LIKE :name AND ".PREFIX."users.id LIKE :user AND ".PREFIX."packages.packageids LIKE :itemid LIMIT :start,:perPage" , array(
				":name" => '%'.$title.'%',
				":user" => '%'.$user,
				":itemid" => '%'.$itemid.'%',
				":start"  =>$start,
				":perPage"=>$perPage					
			 ) );
		if ( $q[0] == NULL ){return false;}				
		return $q;
	}
	public function getTotalPages($user='' , $title ='' , $itemid='' , $seller=''){
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
		$q = $this->_db->select("SELECT ".PREFIX."packages.* , COUNT(*) AS mx,".PREFIX."users.id AS uid ,".PREFIX."users.username ,".PREFIX."users.privs   FROM ".PREFIX."packages INNER JOIN ".PREFIX."users ON ".PREFIX."packages.user=".PREFIX."users.id WHERE ".PREFIX."packages.name LIKE :name AND ".PREFIX."users.id LIKE :user AND ".PREFIX."packages.packageids LIKE :itemid" , array(
				":name" => '%'.$title.'%',
				":user" => '%'.$user,
				":itemid" => '%'.$itemid.'%'
			 ) );	

		$r = ceil($q[0]->mx / $perPage);
		return $r;
	}
	public function getAlertProducts( $user='' , $title ='' , $itemid='',$seller='' , $page = 1){
		$perPage = DEFAULT_PER_PAGE;
		if( Session::get('perPage') != "" ) 
			$perPage = intval(Session::get('perPage'));
		$start = ($page - 1) * $perPage;

		$q = $this->_db->select("SELECT ".PREFIX."packages.* ,".PREFIX."users.id AS uid ,".PREFIX."users.username ,".PREFIX."users.privs   FROM ".PREFIX."packages INNER JOIN ".PREFIX."users ON ".PREFIX."packages.user=".PREFIX."users.id WHERE ".PREFIX."packages.name LIKE :name AND ".PREFIX."users.id LIKE :user AND alert=1 LIMIT :start,:perPage" , array(
				":name" => '%'.$title.'%',
				":user" => '%'.$user,
				":start"  =>$start,
				":perPage"=>$perPage					
			 ) );
		if ( $q[0] == NULL ){return false;}		
		return $q;
	}	
	public function getTotalAlertPages($user='' , $title ='' , $itemid='' , $seller=''){
		$perPage = DEFAULT_PER_PAGE;
		if( Session::get('perPage') != "" ) 
			$perPage = intval(Session::get('perPage'));
		$q = $this->_db->select("SELECT ".PREFIX."packages.* , COUNT(*) AS mx,".PREFIX."users.id AS uid ,".PREFIX."users.username ,".PREFIX."users.privs   FROM ".PREFIX."packages INNER JOIN ".PREFIX."users ON ".PREFIX."packages.user=".PREFIX."users.id WHERE ".PREFIX."packages.name LIKE :name AND ".PREFIX."users.id AND alert=1 LIKE :user" , array(
						":name" => '%'.$title.'%',
						":user" => '%'.$user
					 ) );	
		$r = ceil($q[0]->mx / $perPage);
		return $r;
	}
	public function getCurrent($id,$update =false){
		$s = $this->_db->select("SELECT * FROM ".PREFIX."packages WHERE id=:id" , array( ':id' => $id ) );	
		if( $update==true ){
			$packageItems = explode(',' , $s[0]->packageids);
	
			$mod = new \models\ebay\item();
			$ss = 0;
			foreach( (array)$packageItems as $itemid ){
				$item = $mod->getByItemId($itemid);
				$ss += \helpers\currency::convert($item->currentprice + $item->shippingcost,$item->currency , 'EUR');
			}
			$endprice = ( $ss + $s[0]->weight + $s[0]->profit + $s[0]->insurancecost + $s[0]->big * 45 ) * (1.19 - 0.19 * $s[0]->vat) ;// TODO Change 45
	
			$this->_db->update(PREFIX."packages" , array ('currentprice' => $endprice ) , array('id' => $id) );
			return $endprice;
		}
		return $s[0]->currentprice;
	}
	public function updatePrices($id){
		global $appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID;
		initKeys();
		session_start();
	   	$ebay = new Ebay($appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID);
		
		$s = $this->_db->select("SELECT * FROM ".PREFIX."packages WHERE id=:id" , array( ':id' => intval($id) ) );	
		$packageItems = explode(',' , $s[0]->packageids);

		$mod = new \models\ebay\item();
		$ss = 0;
		foreach( (array)$packageItems as $itemid ){
			$item = $mod->getByItemId($itemid);
			$ss += \helpers\currency::convert($item->currentprice + $item->shippingcost,$item->currency , 'EUR');
		}
		$endprice = ( $ss + $s[0]->weight + $s[0]->profit + $s[0]->insurancecost + $s[0]->big * 45 ) * (1.19 - 0.19 * $s[0]->vat) ;// TODO Change 45
		
		$this->_db->update(PREFIX."packages" , array ('currentprice' => $endprice ) , array('id' => $id) );

		$q = $this->_db->select("SELECT * FROM ".PREFIX."products_local WHERE itemid IN (".mysql_real_escape_string($s[0]->localids).")");
		
		$ebay->updateListingPricesEnd($q ,$endprice);
		foreach( (array)$q as $item ){
			$this->syncItemId($item->itemid);
		}		
	}
	
	public function save($id , $data){
		$user = new \models\user\user();
		$q = $this->_db->select("SELECT * FROM ".PREFIX."packages WHERE id=:id" , array( ":id" => intval($id) ) );
		if( $q[0] == NULL ){
			return false;
		}
		$where = array(
			'id' => $id
		);
		$this->_db->update(PREFIX."packages" , $data , $where);
		return true;
	}
	public function delete($id){
		$user = new \models\user\user();
		$q = $this->_db->select("SELECT * FROM ".PREFIX."packages WHERE id=:id" , array( ":id" => intval($id) ) );
		if( $q[0] == NULL ){
			return false;
		}
		$where = array( 
			'id' => $id,
		);
		$this->_db->delete(PREFIX."packages" , $where);
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
	
	public function get($id){
		$user = new \models\user\user();		
		//$q = $this->_db->select("SELECT * FROM `".PREFIX."packages` INNER JOIN `".PREFIX."products_local` ON `".PREFIX."packages`.packageids LIKE  CONCAT('%',`".PREFIX."products_local`.itemid,'%') WHERE ".PREFIX."packages.id = :id" , array( ":id" => $id ) );
		$q = $this->_db->select("SELECT * FROM ".PREFIX."packages WHERE id = :id" , array(":id" => $id));
		if( $q[0] == NULL ){
			return false;
		}
		$k = explode( "," , $q[0]->packageids );
		$packs = array();
		foreach( $k as $itemid ){
			if( $itemid == "" )continue;
			$s = $this->_db->select("SELECT * FROM ".PREFIX."packages LEFT JOIN ".PREFIX."products_local ON ".PREFIX."products_local.itemid=:itemid WHERE ".PREFIX."packages.id = :id" , array(":id" => $id , ":itemid" => $itemid ));
			$packs = array_merge($packs , $s);
		}
		
		$this->package = $packs;
		$this->packages = sizeof($packs);
		
		$q = $this->_db->select("SELECT * FROM ".PREFIX."packages WHERE id = :id" , array(":id" => $id));
		if( $q[0] == NULL ){
			return false;
		}
		$k = explode( "," , $q[0]->localids );
		$packs = array();
		foreach( $k as $itemid ){
			if( $itemid == "" )continue;
			$s = $this->_db->select("SELECT * FROM ".PREFIX."packages LEFT JOIN ".PREFIX."products_local ON ".PREFIX."products_local.itemid=:itemid WHERE ".PREFIX."packages.id = :id" , array(":id" => $id , ":itemid" => $itemid ));
			$packs = array_merge($packs , $s);
		}
		
		$this->local = $packs;
		$this->locals = sizeof($packs);

		return true;
	}
	public function getPackOnly($id){
		$user = new \models\user\user();		
		$q = $this->_db->select("SELECT * FROM `".PREFIX."packages` WHERE id = :id" , array( ":id" => $id ) );
		if( $q[0] == NULL ){
			return false;
		}		
		return $q[0];
	}
	public function addToPackage($itemId , $id, $moreThanOnce = false){
		$user = new \models\user\user();		
		$q = $this->_db->select("SELECT * FROM ".PREFIX."packages WHERE id=:id" , array( ":id" => $id ) );
		if( $q[0] == NULL ) return false;
		
		if ( $q[0]->localids == "" ){
			$localids = array();
		}else{
			$localids = explode(',' , $q[0]->localids);
		}
		if ( $q[0]->packageids == "" ){
			$packageids = array();
		}else{
			$packageids = explode(',' , $q[0]->packageids);
		}
		
		$key = array_search($itemId , $localids);
		if( $key !== false && !$moreThanOnce ){
			unset($localids[$key]);
		}
		array_push($packageids , $itemId);						
		
		
		foreach( $localids as $key=>$iii ){
			if( strlen( $iii ) < 5 )unset($localids[$key]);
		}
		foreach( $packageids as $key=>$iii ){
			if( strlen( $iii ) < 5 )unset($packageids[$key]);
		}
		$localids = implode(',' ,$localids);
		$packageids = implode(',' ,$packageids);		
		$data = array(
			'localids' => $localids,
			'packageids' => $packageids			
		);
		$where = array(
			'id' => $id,
		);
		$this->_db->update(PREFIX."packages" , $data , $where);	
	}
	public function removeFromPackage($itemId , $id){
		$user = new \models\user\user();		
		$q = $this->_db->select("SELECT * FROM ".PREFIX."packages WHERE id=:id" , array( ":id" => $id ) );
		if( $q[0] == NULL ) return false;
		
		if ( $q[0]->packageids == "" ){
			$localids = array();
		}else{
			$localids = explode(',' , $q[0]->packageids);
		}
		if ( $q[0]->localids == "" ){
			$packageids = array();
		}else{
			$packageids = explode(',' , $q[0]->localids);
		}

		$key = array_search($itemId , $localids);
		if( $key !== false )
			unset($localids[$key]);
		array_push($packageids , $itemId);						
		
		foreach( $localids as $key=>$iii ){
			if( strlen( $iii ) < 5 )unset($localids[$key]);
		}
		foreach( $packageids as $key=>$iii ){
			if( strlen( $iii ) < 5 )unset($packageids[$key]);
		}

		$localids = implode(',' ,$localids);
		$packageids = implode(',' ,$packageids);		
		$data = array(
			'localids' => $packageids,
			'packageids' => $localids			
		);
		$where = array(
			'id' => $id,
		);
		$this->_db->update(PREFIX."packages" , $data , $where);	
	}
	public function removeItem($itemId , $id){
		$user = new \models\user\user();		
		$q = $this->_db->select("SELECT * FROM ".PREFIX."packages WHERE id=:id" , array( ":id" => $id ) );
		if( $q[0] == NULL ) return false;
		
		$localids = explode(',' , $q[0]->localids);
				
		$key = array_search($itemId , $localids);
		if( $key !== false )
			unset($localids[$key]);
		
		$localids = implode(',' ,$localids);
		$data = array(	'localids' => $localids		);
		$where = array(
			'id' => $id
		);
		$this->_db->update(PREFIX."packages" , $data , $where);	
	}
	public function create($name){
		$user = new \models\user\user();			
		$q = $this->_db->insert(PREFIX."packages" , array('user' => $user->id , 'name' => $name , 'currency' => 'EUR' ) ); // TODO CRITICAL HARDCODED EUR Package value
		$this->id = $this->_db->lastInsertId();
		return $this->id;
	}
	public function addItem($itemId , $id ){
		$user = new \models\user\user();		
		$q = $this->_db->select("SELECT * FROM ".PREFIX."products_remote WHERE itemid=:id" , array( ":id" => $itemId ) );
		if( $q[0] == NULL ){		
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
		}
		$this->syncItemId($itemId , false);

		$this->addToPackage($itemId , $id , true);
		
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
	
		$resp = simplexml_load_file($apicall);  // load xml file from ebay api
		if( $resp->Ack == "PartialFailure" ){
			foreach( (array)$itemids as $itemid ){
					$this->syncItemId($itemid);
			}
			return;
		}
		else if ( $resp->Ack != "Success" ){
			//exit( " ERROR ON API CALL :".var_dump($resp));
			return false;
		}
		$margins = new \models\margin();
		$items = $resp->Item;
		foreach( $items as $item ){
			$flag = $this->checkCreateLocalItem($item->ItemID);
			if( $flag != false ){
				$oldprice = $flag[0]->currentprice;
				if ( $oldprice == $item->CurrentPrice ){
					$oldflag = false;	
				}
				else{
					$oldflag = true;
					$changed = 1;
					if( $item->CurrentPrice - $oldprice > $margins->getThreshold($oldprice) ){
						$alert = 1;
					}
				}
			}
			else{
				$oldprice = 0;	
			}
			$data = array(
				'currentprice' => $item->CurrentPrice,
				'quantity' => $item->Quantity,
				'listing' => $item->ListingStatus,
				'timeleft' => $item->TimeLeft
			);
			if ( $item->Quantity == 0 ){
				$alert = 1;
				$data['listing'] = "Completed";	
				$data['outofstock'] = 1;				
			}
			else{
				$data['outofstock'] = 0;	
			}
			if( $oldflag ){
				$data['oldprice'] = $oldprice;
				$data['alert'] = $alert;	
				$data['changed'] = $changed;
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
		
				
	}
	public function syncItemId($itemId,$pinned = false){
		global $appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID;
		initKeys();
		session_start();
	   	$ebay = new Ebay($appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID);
			
		$item = $ebay->getItemData($itemId);
		if( $item == false ){//Item doesnt exists
					$where = array(
						'itemid' => $itemId
					);
					$q = $this->_db->delete(PREFIX."products_remote" , $where);
					$tt = $this->_db->select("SELECT * FROM ".PREFIX."products_local WHERE itemid=:itemid",array(":itemid"=>$itemId));
					if( $tt[0] == NULL )return;
					$rdata = array(
						'alert' => 1,
					);				
					$q = $this->_db->update(PREFIX."products_local" , $rdata , $where);
					$postdata = array(
						'user' => 1,
						'productid'  => $tt->productid,
						'content' => "The Item Vanished!!!"			
					);
					$q = $this->_db->insert(PREFIX."notes" , $postdata);
		}
		else{
			$flag = $this->checkRemoteItem($itemId);
			if( !$flag ){return false;}
			$flag = $this->checkCreateLocalItem($itemId);
			if( $flag !== false ){
				$oldprice = $q[0]->currentprice;
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
	public function countPackages(){
		$q = $this->_db->select("SELECT COUNT(*) AS max FROM ".PREFIX."packages");	
		if( $q[0] == NULL ){return -1;}
		return $q[0]->max;
	}
	public function countPackagesForItem($pids){
		$q = $this->_db->select("SELECT COUNT(*) AS max FROM ".PREFIX."packages WHERE packageids LIKE :pids" , array( ":pids" => '%'.$pids.'%' ) );	
		if( $q[0] == NULL ){return -1;}
		return $q[0]->max;
	}
	public function countAlerts(){
		$q = $this->_db->select("SELECT COUNT(*) AS max FROM ".PREFIX."packages WHERE alert = 1");	
		if( $q[0] == NULL ){return -1;}
		return $q[0]->max;
	}
	public function countPriceChanges(){
		$q = $this->_db->select("SELECT COUNT(*) AS max FROM ".PREFIX."packages WHERE changed = 1");	
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
	public function cron($id){
		    $q = $this->_db->select("SELECT * FROM ".PREFIX."packages WHERE id=:id", array(":id"=> $id ) );
			
			$margins = new \models\margin();
			foreach( (array)$q as $pack ){
				$oldprice = $pack->currentprice;
				$endprice = $this->getCurrent($pack->id);
				if( $endprice - $oldprice >  $margins->getThreshold($oldprice) ){
					$alert = 1;	
				}				
				else{
					$alert = 0;	
				}
				$changed = 0;
				if( round($endprice,1) != round($oldprice,1) ){
						$changed = 1;
				}
				$s = $this->_db->update(PREFIX."packages" , array(
																"oldprice" => $oldprice,
																"currentprice" => $endprice,
																"alert" => $alert,
																"changed" => $changed
															) , array("id"=>$pack->id));
			}
	}
	
}


?>
