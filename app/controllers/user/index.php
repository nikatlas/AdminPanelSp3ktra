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

	
	public function register(){		
		$name = $_POST['name'];
		$pass = $_POST['pass'];
		$privs = $_POST['privs'];

		$user = new \models\user\user();
		if( $user->getPrivs() < 4 )
			echo "-1";
		else{
			$r = $user->register($name,$pass,$privs);
			echo $r;
		}
	}

	public function index(){
	exit( "!" );
		$user = new \models\user\user();
		$data['privs'] = $user->getPrivs();
		View::rendertemplate('pagePrepare', $data);
		//View::render('user/register', $data);
		View::rendertemplate('pageEnd', $data);
	}
}
