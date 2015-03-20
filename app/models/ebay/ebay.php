<?php 
namespace models\ebay;


include_once ('keys.php');
require_once ('ebaySession.php');

/* TODO 
	-> Change/check site ID  = 0 = US !!!!!!!
	-> IMAGES 
//*/
class obj {
	public function __construct(){
		
	}
}
class Ebay {

    public function __construct( $appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID)
    {
        $this->devID = $devID;
        $this->appID= $appID;
        $this->certID= $certID;
        $this->compatabilityLevel= $compatabilityLevel;
        $this->siteID= $siteID;
		
        $this->userToken= $userToken;
        if( $userToken == "" && isset($_SESSION['userToken']) ){
			$this->userToken = $_SESSION['userToken'];
		}
		$this->serverUrl= $serverUrl;
        $this->runame= $RuName;
	
		$date = new \DateTime();
		$this->FetchAll = false;
		$this->StartTimeTo= $date->format('Y-m-dTH:i:s').'z';

		$d = 1;
		if( isset($_REQUEST['time_from_get']) ){
			$d = $_REQUEST['time_from_get'];
		} 
		$date->sub(new \DateInterval('P'.$d.'D'));
        $this->StartTimeFrom= $date->format('Y-m-d').'T'.$date->format('H:i:s.u3').'Z';
        
        $this->EntriesPerPage= 200;
        $this->timeTail = 'T21:59:59.005Z';
		$this->itemIds = array();
    }
    
    
    /**
     * returns an array from an xml string
     * 
     * @param \Cubet\Ebay\SimpleXMLElement $parent
     * @return array
     */
    function XML2Array($xmlSrting){
        $xml = simplexml_load_string($xmlSrting);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        return $array;
    }
	public function getStuff(){
			$this->FetchAll =true;
			$res =  $this->ebayManagement();
			if( !$res ) return false;

			$ebayItems = array();
			for ( $i=0;$i< $res['totalPages'];$i++ ){
				$doc = new \DOMDocument();
				$doc->loadXML($res['myeBaySellingXml'][$i]);
				$items = $doc->getElementsByTagName("ItemID");
				foreach( $items as $item ){
					array_push($ebayItems, $item->nodeValue);
				}	
			}
			$this->itemIds = $ebayItems;
			return true;
	}
	
	public function getNewStuff(){
			$res =  $this->ebayManagement();
			if( !$res ) return false;
			
			$ebayItems = array();
				$doc = new \DOMDocument();
				$doc->loadXML($res['sellerEventsXml']);
				$items = $doc->getElementsByTagName("ItemID");
				foreach( $items as $item ){
					array_push($ebayItems, $item->nodeValue);
				}	
			$this->itemIds = $ebayItems;
			return true;
	}
    
	// GET ALL ACTIVE STUFF
	public function getActiveStuff(){
			$this->FetchAll =true;
			$res =  $this->ebayManagement();
			if( !$res ) return false;

			$ebayItems = array();
			for ( $i=0;$i< $res['totalPages'];$i++ ){
				$doc = new \DOMDocument();
				$doc->loadXML($res['myeBaySellingXml'][$i]);
				$items = $doc->getElementsByTagName("SKU");
				//exit( sizeof( $items ) ."LLL");
				foreach( $items as $item ){
					array_push($ebayItems, $item->nodeValue);
				}	
				if( sizeof($ebayItems) == 0 ){			
					$items = $doc->getElementsByTagName("ItemID");
					foreach( $items as $item ){
						array_push($ebayItems, $item->nodeValue);
					}	
				}
			}
			$this->skus = $ebayItems;
			return true;
	}
    /**
     * Parse XML content to Object
     * 
     * @author Rahul P R <rahul.pr@cubettech.com>
     * @date 24-Jan-2014
     * @param type $xml
     * @return response Object
     */
    
    public function parseXml($responseXml){
        if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
                die('<P>Error sending request');
        //Xml string is parsed and creates a DOM Document object
        $responseDoc = new \DomDocument();
        $responseDoc->loadXML($responseXml);
        return $responseDoc;
    }	
	public function grabCategoryIDFromStore($doc1){
		if ( $doc1->getElementsByTagName("StoreOwner")->item(0)->nodeValue == "false" ){
					return $doc1->getElementsByTagName("CategoryID")->item(0)->nodeValue;
		}
		return $doc1->getElementsByTagName("StoreCategoryID")->item(0)->nodeValue;
	}
	public function grabCategoryFromStore($doc1){
			global $appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID;
			initKeys();
			session_start();
			if ( $doc1->getElementsByTagName("StoreOwner")->item(0)->nodeValue == "false" ){
					return $doc1->getElementsByTagName("CategoryName")->item(0)->nodeValue;
			}
			$reqID = $doc1->getElementsByTagName("StoreCategoryID")->item(0)->nodeValue;

			if( !isset($_SESSION['storeDoc1'])  ){
				$ebay = new Ebay($appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID);
				$xml = $ebay->GetStoreCategories(); 
				$_SESSION['storeDoc1'] = $xml;
				$doc = new \DOMDocument();
				$doc->loadXML($xml);
			}
			else{
				$xml = $_SESSION['storeDoc1'];		
				$doc = new \DOMDocument();
				$doc->loadXML($xml);
			}
			$found = false;
			
			$chcat = $doc->getElementsByTagName("CustomCategory");
			foreach( $chcat as $cat ){
				 if( $cat->getElementsByTagName("CategoryID")->item(0)->nodeValue  == $reqID ){
					$name = $cat->getElementsByTagName("Name")->item(0)->nodeValue;
					$found = true;
					break; 
				 }
			 }
			 if( !$found ){
				$chcat = $doc->getElementsByTagName("ChildCategory");
				foreach( $chcat as $cat ){
					 if( $cat->getElementsByTagName("CategoryID")->item(0)->nodeValue  == $reqID ){
						$name = $cat->getElementsByTagName("Name")->item(0)->nodeValue;
						$par=$cat->parentNode;
						while ( $par->nodeName == "ChildCategory" ){ 
							$name = $par->getElementsByTagName("Name")->item(0)->nodeValue.":".$name;	
							$par=$par->parentNode;
						}
						$name = $par->getElementsByTagName("Name")->item(0)->nodeValue.":".$name;	
						break; 
					 }
				 }
 			 }
			 return $name;
	}
	public function getItemData($itemId){
			 $xml = $this->getItem($itemId);			 
			 //echo $xml;die();
			 $doc = new \DOMDocument();
			 $desdoc = new \DOMDocument();
			 $doc->loadXML($xml);
			 
			 
			 error_reporting(E_ALL);
			 ini_set('display_errors', 1);
			 //echo $xml ;
			 if( $doc->getElementsByTagName("Ack")->item(0)->nodeValue != "Success" ){
				echo "ItemId doesnt exist on ebay! (ITEMID: ".$itemId.")<BR>Error:".$doc->getElementsByTagName("LongMessage")->item(0)->nodeValue;
				if( $doc->getElementsByTagName("ErrorCode")->item(0)->nodeValue  == 518 || $doc->getElementsByTagName("ErrorCode")->item(0)->nodeValue  == 18000 || $doc->getElementsByTagName("ErrorCode")->item(0)->nodeValue  == 218050 ){
					return -1;	
				}				
				return false;
			 }
			 

			 $item = new obj();
			 		 
			 $item->sku = $doc->getElementsByTagName("SKU")->item(0)->nodeValue;

			 $item->price = $doc->getElementsByTagName("CurrentPrice")->item(0)->nodeValue;
			 $item->currency = $doc->getElementsByTagName("CurrentPrice")->item(0)->attributes->getNamedItem("currencyID")->nodeValue;

			 $item->quantity = $doc->getElementsByTagName("Quantity")->item(0)->nodeValue;

	 		 $item->categoryId = $doc->getElementsByTagName(  "PrimaryCategoryID")->item(0)->nodeValue;
			 $item->categoryName = $doc->getElementsByTagName("PrimaryCategoryName")->item(0)->nodeValue;


			 $item->timeleft = $doc->getElementsByTagName("TimeLeft")->item(0)->nodeValue;
			 $item->endtime = $doc->getElementsByTagName("EndTime")->item(0)->nodeValue;			 

			 $item->imgurl = $doc->getElementsByTagName('PictureURL')->item(0)->nodeValue;

			 $item->storeurl = $doc->getElementsByTagName('StoreURL')->item(0)->nodeValue;
			 
			 $item->url = $doc->getElementsByTagName("ViewItemURL")->item(0)->nodeValue;
			 
			 $item->listingStatus = $doc->getElementsByTagName("ListingStatus")->item(0)->nodeValue;
			 $item->seller = $doc->getElementsByTagName('UserID')->item(0)->nodeValue;
			 $item->itemid = $itemId;
 			 
			 global $appID,$siteID;			 
			 //$query2 ='http://open.api.ebay.com/shopping?callname=GetShippingCosts&responseencoding=XML&appid='.$appid.'&siteid='.$globalid.'&version='.$version.'&ItemID='.$itemid.'&DestinationCountryCode=DE';
			 $query2 ='http://open.api.ebay.com/shopping?callname=GetShippingCosts&responseencoding=XML&appid='.$appID.'&siteid='.$siteID.'&version=889&ItemID='.$itemId.'&DestinationCountryCode=DE';
             $resp = simplexml_load_file($query2);
			 
			 $item->shippingcost = $resp->ShippingCostSummary->ListedShippingServiceCost;
			 
			 $pictures = $doc->getElementsByTagName("PictureURL");
			 $item->pictures = array();
			 foreach( $pictures as $pic ) {
				 array_push($item->pictures , $pic->nodeValue);
			 }
			 $item->name = $doc->getElementsByTagName("Title")->item(0)->nodeValue;
			 
			 return $item;
	}
    /**
     * Get get session id
     * 
     * @author Rahul P R <rahul.pr@cubettech.com>
     * @date 22-Jan-2014
     * @param \Cubet\Ebay\type $this->runame
     * @return type xml
     */
    
    protected function getSessionId($runame)
    {
            $session = new \eBaySession('GetSessionID',$this);
            //Build the request Xml string
            $requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
                                <GetSessionIDRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                                    <RuName>'.$runame.'</RuName>
                                </GetSessionIDRequest>';
            //Create a new eBay session with all details pulled in from included keys.php
            $responseXml = $session->sendHttpRequest($requestXmlBody);
            return $responseXml;
    }
    
    /**
     * Get get session id
     * 
     * @author Rahul P R <rahul.pr@cubettech.com>
     * @date 22-Jan-2014
     * @param \Cubet\Ebay\type $this->userToken
     * @return type xml
     */
    
    protected function GetUser($userToken)
    {
            $session = new \eBaySession('GetUser',$this);
            //Build the request Xml string
            $requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
                                <GetUserRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                                <RequesterCredentials>
                                <eBayAuthToken>'.$userToken.'</eBayAuthToken>
                                </RequesterCredentials>
                                </GetUserRequest>';

            //Create a new eBay session with all details pulled in from included keys.php
            $responseXml = $session->sendHttpRequest($requestXmlBody);
                        
            return $responseXml;
    }
    
    /**
     * Get Token Status
     * 
     * @author Rahul P R <rahul.pr@cubettech.com>
     * @date 22-Jan-2014
     * @param type $this->userToken
     * @return type xml
     */
    public function GetTokenStatus($userToken)
    {
        $session = new \eBaySession('GetTokenStatus',$this);
        $requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
                            <GetTokenStatusRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                                <RequesterCredentials>
                                    <eBayAuthToken>'.$userToken.'</eBayAuthToken>
                                </RequesterCredentials>
                            </GetTokenStatusRequest>';
	//Create a new eBay session with all details pulled in from included keys.php
	$responseXml = $session->sendHttpRequest($requestXmlBody);
        
        return $responseXml;
    }
    
    /**
     * Fetch User Token
     * 
     * @author Rahul P R <rahul.pr@cubettech.com>
     * @date 22-Jan-2014
     * @param type $sessionId
     * @return type xml
     */
    public function fetchToken($sessionId)
    {
        $session = new \eBaySession('FetchToken',$this);

        $requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
                            <FetchTokenRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                              <SessionID>'.$sessionId.'</SessionID>
                            </FetchTokenRequest>';
        //Create a new \eBay session with all details pulled in from included keys.php
        $responseXml = $session->sendHttpRequest($requestXmlBody);

        return $responseXml;
    }
    
    
    /**
     * returns sellers list
     * 
     * @author Rahul P R <rahul.pr@cubettech.com>
     * @date 22-Jan-2014
     * @param type $userToken
     * @param type $StartTimeFrom
     * @param type $StartTimeTo
     * @param type $EntriesPerPage
     * @return type xml
     */
    
    public function GetSellerList($userToken,$StartTimeFrom,$StartTimeTo,$EntriesPerPage,$pageNumber)
    {
            $session = new \eBaySession('GetSellerList',$this);
            //Build the request Xml string
            $requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
                                <GetSellerListRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                                <RequesterCredentials>
                                <eBayAuthToken>'.$userToken.'</eBayAuthToken>
                                </RequesterCredentials>
                                 <ErrorLanguage>en_US</ErrorLanguage>
                                  <WarningLevel>High</WarningLevel>
                                  <GranularityLevel>Fine</GranularityLevel>
                                  <StartTimeFrom>'.$StartTimeFrom.'</StartTimeFrom>
                                  <StartTimeTo>'.$StartTimeTo.'</StartTimeTo>
                                  <IncludeWatchCount>true</IncludeWatchCount>
                                  <Pagination>
                                    <EntriesPerPage>'.$EntriesPerPage.'</EntriesPerPage>
                                    <PageNumber>'.$pageNumber.'</PageNumber>    
                                  </Pagination>
                                </GetSellerListRequest>';
            
            //Create a new \eBay session with all details pulled in from included keys.php
            $responseXml = $session->sendHttpRequest($requestXmlBody);
            
            return $responseXml;
    }
    public function updateListingPrices($items , $cp , $sp){
		$mod = new \models\ebay\account();
		foreach( (array)$items as $item ){
			$session = $mod->getByName($item->seller);
			if( $session == false ) continue;
			
			$price = ($cp / ( 1 +   0.19 * intval($item->vat) ) + $sp + $item->weight + $item->profit + $item->insurancecost + 45*$item->big ) * ( 1.19 ) + $item->margin;// CHANGE 45 TODO
			$r = $this->updatePrice( $item->itemid , $price , $item->currency , $session , $item->nquantity);
			//echo $r."\r\n\r\n";
		}		
	}
	public function updateListingPricesEnd($items , $endprice){
		$mod = new \models\ebay\account();
		foreach( (array)$items as $item ){
			$session = $mod->getByName($item->seller);
			if( $session == false ) continue;
			$price = $endprice + $item->margin;
			$r = $this->updatePrice( $item->itemid , $price , $item->currency , $session , $item->nquantity);
			//echo $r."\r\n\r\n";
		}		
	}
	public function updatePrice($itemid , $price , $currency ,  $userToken ,$quantity =NULL){
            $session = new \eBaySession('ReviseItem',$this);


			$q = "";
			if ( $quantity != NULL ) {
				$q = '<Quantity>'.$quantity.'</Quantity>';
			}
			$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
								<ReviseItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
								  <RequesterCredentials>
									<eBayAuthToken>'.$userToken.'</eBayAuthToken>
								  </RequesterCredentials>
								  <ErrorLanguage>en_US</ErrorLanguage>
								  <WarningLevel>High</WarningLevel>
								  <Item>
									<ItemID>'.$itemid.'</ItemID>'.
									$q.'
									<StartPrice currencyID="'.$currency.'">'.\helpers\currency::convert($price, 'EUR' , $currency).'</StartPrice>
								  </Item>
								</ReviseItemRequest>';
			$responseXml = $session->sendHttpRequest($requestXmlBody);
			return $responseXml;
	}
	
	
    /**
     * Get my ebay selling details
     * 
     * @author Rahul P R <rahul.pr@cubettech.com>
     * @date 22-Jan-2014
     * @param type $userToken
     * @param type $EntriesPerPage
     * @return type xml
     */

    public function GetSellerEvents($userToken)
    {
            $session = new \eBaySession('GetSellerEvents',$this);
			
			//echo $this->StartTimeFrom;
			
            //Build the request Xml string
            $requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
							<GetSellerEventsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
							  <RequesterCredentials>
								<eBayAuthToken>'.$userToken.'</eBayAuthToken>
							  </RequesterCredentials>
							  <StartTimeFrom>'.$this->StartTimeFrom.'</StartTimeFrom>
							  <DetailLevel>ReturnAll</DetailLevel>
							  <OutputSelector>ItemID</OutputSelector>
							 </GetSellerEventsRequest>';

            //Create a new \eBay session with all details pulled in from included keys.php
            $responseXml = $session->sendHttpRequest($requestXmlBody);
            //echo $responseXml;
            return $responseXml;

    }	
	
    /**
     * Get my ebay selling details
     * 
     * @author Rahul P R <rahul.pr@cubettech.com>
     * @date 22-Jan-2014
     * @param type $userToken
     * @param type $EntriesPerPage
     * @return type xml
     */

    public function GetMyeBaySelling($userToken,$EntriesPerPage,$pageNumber)
    {
            $session = new \eBaySession('GetMyeBaySelling',$this);
            //Build the request Xml string
            $requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
                            <GetMyeBaySellingRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                              <RequesterCredentials>
                                <eBayAuthToken>'.$userToken.'</eBayAuthToken>
                              </RequesterCredentials>
                              <Version>'.$this->compatabilityLevel.'</Version>
                              <ActiveList>
                                <Sort>TimeLeft</Sort>
                                <Pagination>
                                  <EntriesPerPage>'.$EntriesPerPage.'</EntriesPerPage>
                                  <PageNumber>'.$pageNumber.'</PageNumber>  
                                </Pagination>
                              </ActiveList>
							  <OutputSelector>ItemID</OutputSelector>
							  <OutputSelector>SKU</OutputSelector>
							  <OutputSelector>PaginationResult</OutputSelector>
                            </GetMyeBaySellingRequest>';

            //Create a new \eBay session with all details pulled in from included keys.php
            $responseXml = $session->sendHttpRequest($requestXmlBody);
            
            return $responseXml;

    }	
	public function GetStoreCategories(){
            $session = new \eBaySession('GetStore',$this);
			$reqxml = '<?xml version="1.0" encoding="utf-8"?>
							<GetStoreRequest xmlns="urn:ebay:apis:eBLBaseComponents">
							  <RequesterCredentials>
								<eBayAuthToken>'.$this->userToken.'</eBayAuthToken>
							  </RequesterCredentials>
							  <CategoryStructureOnly>true</CategoryStructureOnly>
							</GetStoreRequest>';
			$responseXml = $session->sendHttpRequest($reqxml);
			return $responseXml;
	}
    public function GetMultipleItems($itemIds)
    {
            $session = new \eBaySession('GetMultipleItems',$this);
            //Build the request Xml string
            /*$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
								<GetSingleItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
								  <ItemID>'.$itemId.'</ItemID>
								</GetSingleItemRequest>';
			foreach($itemIds as $id ){
				$requestXmlBody .= '<ItemID>'.$id->itemid.'</ItemID>';
			}

			*/
			$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
								<GetMultipleItemsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
								<ItemID>261621195658</ItemID>
								</GetMultipleItemsRequest>';

            //Create a new \eBay session with all details pulled in from included keys.php
            $responseXml = $session->sendHttpRequest($requestXmlBody);
            echo $responseXml;
            return $responseXml;

    }
	public function GetItem($itemId)
    {
            $session = new \eBaySession('GetItem',$this);
            //Build the request Xml string
            /*$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
								<GetSingleItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
								  <ItemID>'.$itemId.'</ItemID>
								</GetSingleItemRequest>';
			*/
			$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
								<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
								  <RequesterCredentials>
									<eBayAuthToken>'.$this->userToken.'</eBayAuthToken>
								  </RequesterCredentials>
								  <Version>'.$this->compatabilityLevel.'</Version>
								  <IncludeItemSpecifics>true</IncludeItemSpecifics>
								  <IncludeTaxTable>true</IncludeTaxTable>
								  <IncludeWatchCount>true</IncludeWatchCount>
								  <ItemID>'.$itemId.'</ItemID>
								  <DetailLevel>ItemReturnDescription</DetailLevel>
								    <IncludeSelector>Description,ItemSpecifics</IncludeSelector>

								</GetItemRequest>';
			
            //Create a new \eBay session with all details pulled in from included keys.php
            $responseXml = $session->sendHttpRequest($requestXmlBody);
//            echo $responseXml;
            return $responseXml;

    }    
    public function ebayManagement($input=array())
    {
        $sellerList = array();
        $myeBaySelling = array();
        $getUser = array();
        $tokenStatus = "";
        $sessionId = "";
        $showLogin = true;
        $StartTimeFrom = $this->StartTimeFrom;
        $StartTimeTo = $this->StartTimeTo;
        $EntriesPerPage = $this->EntriesPerPage;
        $pageNumber = 1;
        $error = "";
        $formInput = array( 'StartTimeFrom' =>  $StartTimeFrom,
                            'StartTimeTo'   =>  $StartTimeTo,
                            'EntriesPerPage'=>  $EntriesPerPage,
                            'pageNumber'    =>  $pageNumber ) ;
        
		if (  (!isset($_SESSION['sesId']) || $_SESSION['sesId'] == "") && $this->userToken == '' ){
			$sessionIdXml = $this->getSessionId($this->runame) ;
			$sessionIdResponse = $this->parseXml($sessionIdXml);
			$sessionId = $sessionIdResponse->getElementsByTagName('SessionID')->item(0)->nodeValue;
			$_SESSION['sesId'] = $sessionId;
			echo '<a target="_new" href="https://signin.ebay.com/ws/eBayISAPI.dll?SignIn&RuName='.$this->runame.'&SessID='.$sessionId.'">Click Here To Link Your Ebay Account To Our Website</a>';
			return false;
		}
		// OPEN FETCHING
		else{
        // GET userToken
        // 
        // Check if usertoken is getting using the sessionId(passed to the ebay pop up form)
        // if success save that userToken to $this->userToken
        // else set $this->userToken to the token value stored in session
        if( !$this->userToken || $this->userToken == '' ){
			$fetchTokenXml = $this->fetchToken($_SESSION['sesId']) ;
			$fetchTokenResponse = $this->parseXml($fetchTokenXml);
			
			if($fetchTokenResponse->getElementsByTagName('Ack')->item(0)->nodeValue=='Success'){
				//echo '1.Success <br>';
				$this->userToken =  $fetchTokenResponse->getElementsByTagName('eBayAuthToken')->item(0)->nodeValue;
				$_SESSION['userToken'] = $fetchTokenResponse->getElementsByTagName('eBayAuthToken')->item(0)->nodeValue;
			} else {
				$_SESSION['sesId'] = "";
				echo 'FetchToken Fail <BR><br>';
				echo 'Refresh!';
				return false;
	
			}
		}
        if($this->userToken) {
            
            //get token Status
            $tokenStatusXml = $this->GetTokenStatus($this->userToken) ;
            $tokenStatusResponse = $this->parseXml($tokenStatusXml);
            $tokenStatus = $tokenStatusResponse->getElementsByTagName('Ack')->item(0)->nodeValue=='Success'
                            ? $tokenStatusResponse->getElementsByTagName('Status')->item(0)->nodeValue
                            : 'Inactive' ;

            $GetUserXml = $this->GetUser($this->userToken);
            $getUser = $this->XML2Array($GetUserXml);

            //  if form submitted
            if(isset($input['sellerListSubmit']) || true){
                
                //echo $input['pageNumber'];

                $StartTimeFrom = isset($input['StartTimeFrom']) && $input['StartTimeFrom']!=''
                                ? $input['StartTimeFrom']
                                :$StartTimeFrom ;
                $StartTimeTo = isset($input['StartTimeTo']) && $input['StartTimeTo']!=''
                                ? $input['StartTimeTo'] 
                                : $StartTimeTo;
                $EntriesPerPage = isset($input['EntriesPerPage']) && $input['EntriesPerPage']!=''
                                ? $input['EntriesPerPage'] 
                                : $EntriesPerPage;
                $pageNumber = isset($input['pageNumber']) && $input['pageNumber']!=''
                                ? $input['pageNumber'] 
                                : 1;
                
                $formInput = array( 'StartTimeFrom' =>  $StartTimeFrom,
                                    'StartTimeTo'   =>  $StartTimeTo,
                                    'EntriesPerPage'=>  $EntriesPerPage,
                                    'pageNumber'    =>  $pageNumber) ;
                //$sellerListXml = $this->GetSellerList($this->userToken, $StartTimeFrom, $StartTimeTo, $EntriesPerPage,$pageNumber);
                //$sellerList = $this->XML2Array($sellerListXml);
				$myeBaySellingXml = array();
				$myeBaySellingDocs = array();
				array_push($myeBaySellingXml , $this->GetMyeBaySelling($this->userToken,$EntriesPerPage,$pageNumber) );
        		$myeBaySellingDoc = new \DOMDocument();
		        $myeBaySellingDoc->loadXML($myeBaySellingXml[0]);
				//exit( $myeBaySellingXml[0] );
				array_push($myeBaySellingDocs , $myeBaySellingDoc );
				$myeBaySelling = $this->XML2Array($myeBaySellingXml[0]);
				$pages = $myeBaySellingDoc->getElementsByTagName("TotalNumberOfPages")->item(0)->nodeValue;
				$totalEntries = $myeBaySellingDoc->getElementsByTagName("TotalNumberOfEntries")->item(0)->nodeValue;
				if( $this->FetchAll == true ){
					for( $i=2;$i<=$pages;$i++){
						array_push($myeBaySellingXml , $this->GetMyeBaySelling($this->userToken,$EntriesPerPage,$i) );
						$myeBaySellingDoc = new \DOMDocument();
						$myeBaySellingDoc->loadXML($myeBaySellingXml[$i-1]);
						array_push($myeBaySellingDocs , $myeBaySellingDoc );
					}	
				}
				$sellerEvents = new \DOMDocument();
				$sellerEventsXml = $this->GetSellerEvents($this->userToken);
				$sellerEvents->loadXML($sellerEventsXml);
            }
            
        } else {
            echo ' no usertoken ';  
        }
        
        $_SESSION['passed4login'] = $_SESSION['sesId'];
        $_SESSION['userToken'] = $this->userToken;
        
        return array(   'sellerList'    =>  $sellerList,
                        'myeBaySelling' =>  $myeBaySelling,
						'myebaySellingDoc' => $myeBaySellingDocs,
						'totalEntries' => $totalEntries,
						'totalPages' => $pages,
						'sellerEventsXml' => $sellerEventsXml,
						'sellerEvents' => $sellerEvents,
						'myeBaySellingXml' => $myeBaySellingXml,
                        'tokenStatus'   =>  $tokenStatus,
                        'runame'        =>  $this->runame,
                        'sessionId'     =>  urlencode($_SESSION['passed4login']),
                        'userToken'     =>  $this->userToken,
                        'showLogin'     =>  $showLogin,
                        'formInput'     =>  $formInput,
                        'getUser'        => $getUser,
                        'error'         =>  $error
                    ) ;
		}// CLOSE OF FETCHING
    }

}
?>
