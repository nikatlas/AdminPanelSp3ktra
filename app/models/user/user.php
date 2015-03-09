<?php

namespace models\user;
use \helpers\session as Session;

class User extends \core\model {

	function __construct(){
		parent::__construct();
		$this->checkCreateTable();
		$this->isLoggedRedirect();
		$this->fetchData();
	}
	public function checkProduct($id){
		$q = $this->_db->select("SELECT *,".PREFIX."users.id AS uid FROM ".PREFIX."products_local INNER JOIN ".PREFIX."products ON ".PREFIX."products_local.productid=".PREFIX."products.id INNER JOIN ".PREFIX."users ON ".PREFIX."users.id=".PREFIX."products.user WHERE ".PREFIX."products_local.productid=:id",array(":id"=>$id));
		if( $q[0]->uid == $this->id || $q[0]->privs < $this->privs )return;
		\helpers\url::previous();
		return;  
	}
	public function checkPackage($id){
		$q = $this->_db->select("SELECT *,".PREFIX."users.id AS uid FROM ".PREFIX."packages INNER JOIN ".PREFIX."users ON ".PREFIX."users.id=".PREFIX."packages.user WHERE ".PREFIX."packages.id=:id",array(":id"=>$id));
		if( $q[0]->uid == $this->id || $q[0]->privs < $this->privs )return;
		\helpers\url::previous();
		return;  
	}
	public function getAll(){
		$r = $this->_db->select("SELECT * FROM ".PREFIX."users");
		return $r;
	}
	public function fetchData(){
		$id = Session::get("logged");
		$r = $this->_db->select("SELECT * FROM ".PREFIX."users WHERE id=:id", array(":id"=>$id));
		$this->id = $id;
		$this->username = $r[0]->username;
		$this->name = $r[0]->username;
		$this->privs = $r[0]->privs;
	}
	public function getPrivName(){
		$role = array( "User" , "2" , "3" , "SuperUser" , "Admin" ,"6" ,"7" ,"8" ,"9" ,"BOSS" ,"11");
		return $role[($this->privs-1)];	
	}
	public function isLoggedRedirect(){
			if( $_SERVER['REMOTE_ADDR'] == "5.9.119.101" )return true;
			if( !is_numeric(Session::get('logged')) ){
					if( $_SERVER['REQUEST_URI'] != "/login" ){
						\helpers\url::redirect('login');
					}
					return false;
			}
			return true;
	}
	
	public function getUsers(){
		$privs = $this->getPrivs();
		$q = $this->_db->select("SELECT * FROM ".PREFIX."users WHERE privs < :pr",array(":pr"=>$privs));
		if( !$q ){
			return -1;		
		}
		return $q;		
	}
	public function changePrivs($id, $privs){
		if( $this->getPrivs() <= $privs ) return -2;
		
		$where = array('id' => $id);
		$data = array(	'privs' => $privs	);
		$this->_db->update(PREFIX."users" , $data , $where );
		
		return 0;
	}
	public function delete($id){
		$r = $this->_db->select("SELECT * FROM ".PREFIX."users WHERE id=:id", array(":id"=>$id));
		$privs = $r[0]->privs;
		if( $this->getPrivs() <= $privs ) return -2;

		$where = array('id' => $id);
		$this->_db->delete(PREFIX."users" , $where );
		return 0;
	}
	public function getPrivs(){
		$this->isLoggedRedirect();
		return Session::get('privs');
	}
	public function getPrivsById($id){
		$r = $this->_db->select("SELECT * FROM ".PREFIX."users WHERE id=:id", array(":id"=>$id));
		return $r[0]->privs;
	}
	public function changePass($user , $oldpass ,$pass , $repass){
		if( $pass != $repass )return 1;

		$q = $this->_db->select("SELECT * FROM ".PREFIX."users WHERE id=:nm",array(":nm"=>$user));
		if( $q[0] == NULL ){
			exit( "!!!");
			return 1;		
		}
		$hash = $q[0]->pass;
   		if(\helpers\password::verify($oldpass,$hash)){
			$q = $this->_db->update(PREFIX."users" , array("pass" =>  \helpers\password::make($pass)) , array("id" => $user));
			return 2;
		}
		else{
			return 1;
		}
		return 0;
	}
	public function login($name , $pass){
		if( $this->isLoggedRedirect() ){
			\helpers\url::redirect('');	
		}
		if( strlen( $name ) < 4  || strlen( $pass ) < 4 )return -2;
		if( is_numeric(Session::get('logged')) )return 1; 
		$q = $this->_db->select("SELECT * FROM ".PREFIX."users WHERE username=:nm",array(":nm"=>$name));
		if( !$q ){
			return -1;		
		}
		$hash = $q[0]->pass;
   		if(\helpers\password::verify($pass,$hash)){
			Session::set('logged' , $q[0]->id);
			Session::set('username' , $q[0]->name);			
			Session::set('privs' , $q[0]->privs);
			Session::set('perPage' , DEFAULT_PER_PAGE );
			return 1;
		}
		else{
			return 0;	
		}	
	}
	public function logout(){
		if( is_numeric(Session::get('logged')) ){
			Session::pull('logged');	
		}
		\helpers\url::redirect("login");
	}
	public	function register($name , $pass , $privs){
		if( strlen( $name ) < 4  || strlen( $pass ) < 4 )return -2;

		$r = $this->_db->select("SELECT * FROM ".PREFIX."users WHERE username=:name",array(':name' => $name ));
		if ( sizeof($r) > 0 ){
			 return -1;
		}
		$hash = \helpers\password::make($pass);
		$data = array(
			'username' => $name,
			'pass' => $hash,
			'privs' => $privs,
			'time_created' => date("Y-m-d H:i:s")		
		);
		$q = $this->_db->insert(PREFIX."users",$data);
		return 1;
	}
	private function checkCreateTable(){
		$con = mysql_connect(DB_HOST, DB_USER,  DB_PASS) or die("Unable to connect to MySQL");
		$sdb = mysql_select_db(DB_NAME);

		$result = mysql_query("SHOW TABLES LIKE '".PREFIX."users'");
		$tableExists = mysql_num_rows($result) > 0;
		if( !$tableExists ){
			echo "[*] Creating `users` table... ";
			$result = mysql_query("CREATE TABLE ".PREFIX."users(
				id INT NOT NULL AUTO_INCREMENT,PRIMARY KEY(id),
				username TEXT,
				pass TEXT,
				privs INT,
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