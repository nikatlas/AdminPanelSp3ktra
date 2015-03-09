<?php

namespace models\notes;
use \helpers\session as Session;

class Notes extends \core\model {

	function __construct(){
		parent::__construct();
	}
	public function getItemNotes($itemid){
		$q = $this->_db->select("SELECT * FROM ".PREFIX."notes WHERE productid=:id", array( ':id' => $itemid ) );
		if( $q[0] == NULL )return false;
		return $q;
	}
	public function get($id){
		$q = $this->_db->select("SELECT * FROM ".PREFIX."notes INNER JOIN(SELECT id as uid,privs,username FROM ".PREFIX."users)b ON b.uid=".PREFIX."notes.user  WHERE id=:id ", array( ':id' => $id ) );
		if( $q[0] == NULL )return false;
		$this->data = $q[0];
		return $q[0];
	}
	public function createNote($content , $product){
		$user = new \models\user\user();

		$postdata = array(
			'user' => $user->id,
			'productid'  => $product,
			'content' => $content			
		);
		$q = $this->_db->insert(PREFIX."notes" , $postdata);
		$this->id = $this->_db->lastInsertId();		
	}
	public function update($id, $content){
		$user = new \models\user\user();
		$r = $this->get($id);	
		if( $r->user == $user->id || $r->privs < $user->privs ){
			$where = array(
				'id' => $id 
			);
			$up = array(
				'content' => $content
			);			
			$this->_db->update(PREFIX."notes" , $up , $where);
			
			return true;
		}
		return false;
	}
	public function delete($id){
		$user = new \models\user\user();
		$r = $this->get($id);
		if( $r->user == $user->id || $r->privs < $user->privs ){
			$where = array(
				'id' => $id 
			);
			$this->_db->delete(PREFIX."notes" , $where);
		}

	}

}

?>