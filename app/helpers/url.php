<?php namespace helpers;
/*
 * url Class
 *
 * @author David Carr - dave@daveismyname.com - http://www.daveismyname.com
 * @version 2.0
 * @date June 27, 2014
 */
class Url {

	public static function getPageURI(){
		$pattern = "/\/ebay\/list\/[A-Za-z]+/";
		$pattern2 = "/\/ebay\/list/";
		$uri = $_SERVER['REQUEST_URI'];
		if( preg_match($pattern,$uri,$matches)){
			return $matches[0];
		}
		else if(  preg_match($pattern2,$uri,$matches) ){
			return $matches[0]."/page";	
		}
		else{
			return "/ebay/list/page";	
		}
	}

	public static function getPage(){
		$pattern = "/\/ebay\/list\/[A-Za-z]+\/[0-9]+/";
		$pattern2 = "/\/ebay\/list/";
		$uri = $_SERVER['REQUEST_URI'];
		if( preg_match($pattern,$uri,$matches)){
			$a = explode('/' , $matches[0]);
			return ($a[(sizeof($a)-1)]);
		}
		else if(  preg_match($pattern2,$uri,$matches) ){
			return 1;
		}
		else{
			return 1;	
		}
	}	

	/**
	 * Redirect to chosen url
	 * @param  string  $url      the url to redirect to
	 * @param  boolean $fullpath if true use only url in redirect instead of using DIR
	 */
	public static function redirect($url = null, $fullpath = false){
		if($fullpath == false){
			$url = DIR . $url;
		}
		
		header('Location: '.$url);
		exit;
	}

	/**
	 * created the absolute address to the template folder
	 * @return string url to template folder
	 */
	public static function get_template_path(){
	    return DIR.'app/templates/'.Session::get('template').'/';
	}

	/**
	 * converts plain text urls into HTML links, second argument will be
	 * used as the url label <a href=''>$custom</a>
	 *
	 * @param  string $text   data containing the text to read
	 * @param  string $custom if provided, this is used for the link label
	 * @return string         returns the data with links created around urls
	 */
	public static function autolink($text,$custom = null) {
		$regex   = '@(http)?(s)?(://)?(([-\w]+\.)+([^\s]+)+[^,.\s])@';
		$replace = '';
		
		if ($custom === null) {
			$replace = '<a href="http$2://$4">$1$2$3$4</a>';
		} else {
			$replace = '<a href="http$2://$4">'.$custom.'</a>';
		}
	
		return preg_replace($regex, $replace, $text);
	}

	/**
	 * This function converts and url segment to an safe one, for example:
	 * `test name @132` will be converted to `test-name--123`
	 * Basicly it works by replacing every character that isn't an letter or an number to an dash sign
	 * It will also return all letters in lowercase
	 *
	 * @param $slug - The url slug to convert
	 *
	 * @return mixed|string
	 */
	public static function generateSafeSlug($slug) {
		$slug = preg_replace('/[^a-zA-Z0-9]/', '-', $slug);
		$slug = strtolower($slug);
		//Removing more than one dashes
		$slug = preg_replace('/\-{2,}/', '-', $slug);

		return $slug;
	}

	/**
         * Go to the previous url.
        */
        public static function previous() {
        	header('Location: '.$_SERVER['HTTP_REFERER']);
		exit;
    	}
}
