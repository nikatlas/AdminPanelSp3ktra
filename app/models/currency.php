<?php
namespace models;

class Currency extends \core\model{

	public function __construct(){
		parent::__construct();
	}
	
	public function get($from , $to , $value = 1){
		$q = $this->_db->select("SELECT * FROM ".PREFIX."currency WHERE `from`=:frm AND `to`=:t",array(":frm" => $from , ":t"=>$to ));		
		if( $q[0] == NULL ){
			$this->add($from , $to);
		}
		return $q[0]->value * $value;
	}
	public function exists($from , $to ){
		$q = $this->_db->select("SELECT * FROM ".PREFIX."currency WHERE `from`=:frm AND `to`=:t",array(":frm" => $from , ":t"=>$to ));		
		if( $q[0] == NULL ){
			return false;
		}
		return true;	
	}
	public function add($from , $to ){
		if( $this->exists($from , $to ) ){
			$this->update($from , $to);
			return;	
		}
		$url = "http://rate-exchange.appspot.com/currency?from=".$from."&to=".$to;
		$cont = \helpers\currency::http_request($url);
		$r = json_decode($cont);	
		$val = $r->rate;
		$postdata = array(
			'from' => ''.$from,
			'to'  => $to,
			'value' => $val
		);
		$q = $this->_db->insert(PREFIX."currency" , $postdata);	
		return true;
	}
	public function update($from , $to ){
		$url = "http://rate-exchange.appspot.com/currency?from=".$from."&to=".$to;
		$cont = \helpers\currency::http_request($url);
		$r = json_decode($cont);	
		$val = $r->rate;
		if ( !is_numeric($val) || $val == 0 )return false;
		$where = array(
			'from' => $from,
			'to' => $to
		);
		$up = array(
			'value' => $val
		);			
		$this->_db->update(PREFIX."currency" , $up , $where);
		return true;
	}
	public function updateAll(){
		$q = $this->_db->select("SELECT * FROM ".PREFIX."currency WHERE 1");		
		foreach( (array)$q as $item ){
			$this->update($item->from , $item->to);			
		}
		return true;
	}
}
?>