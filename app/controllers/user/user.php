<?php

namespace controllers\user;
use core\view as View;

class User extends \core\controller{

	/**
	 * call the parent construct
	 */
	public function __construct(){
		parent::__construct();
	}
	public function login(){		
		$name = $_POST['name'];
		$pass = $_POST['pass'];
		
		$user = new \models\user\user();
		$r = $user->login($name,$pass);	
		if ( $r == 1 ){
			\helpers\url::redirect(""); 
		}
		else if ( isset($_POST['login']) ){
			$data['error'] = true;	
		}
		View::rendertemplate('pagePrepare', $data);
		View::render('user/login', $data);
		View::rendertemplate('pageEnd', $data);
	}
	public function logout(){		
		$user = new \models\user\user();
		$r = $user->logout();
	}
	public function users(){		
		$user = new \models\user\user();
		$data['privs'] = $user->getPrivs();
		$data['users'] = $user->getUsers();
		$data['myid'] = $user->id;
		View::rendertemplate('pagePrepare', $data);
		View::render('user/users', $data);
		View::rendertemplate('pageEnd', $data);
	}
	
	public function index(){		
		View::rendertemplate('pagePrepare', $data);
		View::rendertemplate('pageEnd', $data);
	}
	public function changePass(){
		$user = new \models\user\user();
		if( !isset($_REQUEST['user']) )\helpers\url::previous();
		if( $user->getPrivs() <= $user->getPrivsById($_REQUEST['user'])  && $user->id != $_REQUEST['user'] ){
			\helpers\url::previous();
		}
		if( $_REQUEST['change'] == 1 ){
			$data['error'] = $user->changePass($_REQUEST['user'] , $_REQUEST['oldpass'] ,$_REQUEST['pass'] , $_REQUEST['repass']);
		}
			
		View::rendertemplate('pagePrepare', $data);
		View::render('user/changepass', $data);
		View::rendertemplate('pageEnd', $data);
	}
	public function changePerPage($val){
		\helpers\session::set("perPage" , $val);
		\helpers\url::previous();	
	}
	public function changeprivs(){		
		$user = new \models\user\user();
		$id = intval($_POST['id']);
		$privs = intval($_POST['privs']);		
		echo $user->changePrivs($id , $privs);
	}
	public function delete(){		
		$user = new \models\user\user();
		$id = intval($_GET['id']);
		echo $user->delete($id);
		\helpers\url::previous();
	}

	public function register(){
		$user = new \models\user\user();
		$data['privs'] = $user->getPrivs();
		if( $user->getPrivs() < 4 ){
			$data['warning'] = true;
		}
		else if( isset($_POST['register']) ){
			$name = $_POST['name'];
			$pass = $_POST['pass'];
			$privs = $_POST['privs'];
			
			$r = $user->register($name,$pass,$privs);
			if( $r == -1 ){
				$data['error'] = true;
			}
			else{
				$data['success'] = true;
			}
			$data['name'] = $name;
			$data['uprivs'] = $privs;
		}

		View::rendertemplate('pagePrepare', $data);
		View::render('user/register', $data);
		View::rendertemplate('pageEnd', $data);
	}
}
