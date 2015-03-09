<?php
namespace models\ebay;

class Account extends \core\model{

	public function __construct(){
		parent::__construct();
	}
	
	public function getByName($name){
		$q = $this->_db->select("SELECT * FROM ".PREFIX."accounts WHERE `name`=:name" , array( ":name" => $name ) );
		if( $q[0] == NULL ) return false;
		return $q[0]->session;
	}	
	public function getAll(){
		$q = $this->_db->select("SELECT * FROM ".PREFIX."accounts");		
		if( $q[0] == NULL ){
			return false;	
		}
		return $q;
	}
	
	public function get($id){
		$q = $this->_db->select("SELECT * FROM ".PREFIX."accounts WHERE id=:id",array(":id" => $id));		
		if( $q[0] == NULL ){
			return false;	
		}
		$this->data = $q[0];
		return $q[0];
	}
	public function add($name , $session){
		if( $from > $to ) return;
		$user = new \models\user\user();
		if ( $user->privs < 4 ) return;
		$postdata = array(
			'name'  => $name,
			'session' => $session
		);
		$q = $this->_db->insert(PREFIX."accounts" , $postdata);	
		return true;
	}
	public function update($id ,$name , $session){
		$user = new \models\user\user();
		if ( $user->privs < 4 ) return;

		$where = array(
			'id' => $id 
		);
		$up = array(
			'name' => $name,
			'session'  => $session
		);			
		$this->_db->update(PREFIX."accounts" , $up , $where);
		return true;
	}
	public function delete($id){
		$user = new \models\user\user();
		if ( $user->privs < 4 ) return;
		$where = array(
			'id' => $id 
		);
		$this->_db->delete(PREFIX."accounts" , $where);
		return true;
	}
}
?>