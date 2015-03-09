<?php
namespace models;

class Margin extends \core\model{

	public function __construct(){
		parent::__construct();
	}
	
	public function getThreshold($price){
		$q = $this->_db->select("SELECT * FROM ".PREFIX."margins WHERE :price>=`from` AND :price<`to`" , array( ":price" => $price ) );
		if( $q[0] == NULL ) return 0.1*$price;
		return $q[0]->threshold;
	}
	public function existsThreshold($price , $id = "%"){
		$q = $this->_db->select("SELECT * FROM ".PREFIX."margins WHERE :price>`from` AND :price<`to` AND id != :id" , array( ":price" => $price , ":id" => $id ) );
		if( $q[0] == NULL ) return false;
		return $q[0]->id;
	}
	
	public function getAll(){
		$q = $this->_db->select("SELECT * FROM ".PREFIX."margins");		
		if( $q[0] == NULL ){
			return false;	
		}
		return $q;
	}
	
	public function get($id){
		$q = $this->_db->select("SELECT * FROM ".PREFIX."margins WHERE id=:id",array(":id" => $id));		
		if( $q[0] == NULL ){
			return false;	
		}
		$this->data = $q[0];
		return $q[0];
	}
	public function add($from , $to , $thres){
		if( $from > $to ) return;
		if( $this->existsThreshold($from) || $this->existsThreshold($to) )return ;
		$user = new \models\user\user();
		if ( $user->privs < 3 ) return;
		$postdata = array(
			'from' => $from,
			'to'  => $to,
			'threshold' => $thres
		);
		$q = $this->_db->insert(PREFIX."margins" , $postdata);	
		return true;
	}
	public function update($id ,$from , $to , $thres){
		if( $from > $to ) return;
		if( $this->existsThreshold($from , $id) || $this->existsThreshold($to , $id) )return;
		$user = new \models\user\user();
		if ( $user->privs < 3 ) return;

		$where = array(
			'id' => $id 
		);
		$up = array(
			'from' => $from,
			'to'  => $to,
			'threshold' => $thres
		);			
		$this->_db->update(PREFIX."margins" , $up , $where);
		return true;
	}
	public function delete($id){
		$user = new \models\user\user();
		if ( $user->privs < 3 ) return;
		$where = array(
			'id' => $id 
		);
		$this->_db->delete(PREFIX."margins" , $where);
		return true;
	}
}
?>