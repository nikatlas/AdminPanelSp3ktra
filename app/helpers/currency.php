<?php 
namespace helpers;
class Currency {
	public static function http_request($uri, $time_out = 10, $headers = 0)
	{
		// Initializing
		$ch = curl_init();
	
		// Set URI
		curl_setopt($ch, CURLOPT_URL, trim($uri));
	
		curl_setopt($ch, CURLOPT_HEADER, $headers);
	
		// 1 - if output is not needed on the browser
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
		// Time-out in seconds
		curl_setopt($ch, CURLOPT_TIMEOUT, $time_out);
	
		// Executing
		$result = curl_exec($ch);
	
		// Closing the channel
		curl_close($ch);
	
		return $result;
	}
	public static function convert($price , $current , $to , $digits = 1){
		if( $current == NULL || $to == NULL ) return 0;
		if( $price == NULL || $price == "") return 0;
		if ( $current == $to || $price == 0 ) return $price;
		$mod = new \models\currency();
		return round( $mod->get($current , $to) * $price , $digits ); 
	}
	public static function convertTest(){
		$mod = new \models\currency();
		$mod->updateAll();
		$url = "http://rate-exchange.appspot.com/currency?from=dsadsaEUR&to=GBP&q=852";
		$cont = \helpers\currency::http_request($url);
		echo $cont;
	}
}
