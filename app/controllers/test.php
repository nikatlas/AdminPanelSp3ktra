<?php

namespace controllers;
use core\view as View;

/*
 * Welcome controller
 *
 * @author David Carr - dave@daveismyname.com - http://www.daveismyname.com
 * @version 2.1
 * @date June 27, 2014
 */
class Test extends \core\controller{

	/**
	 * call the parent construct
	 */
	public function __construct(){
		parent::__construct();
	}

	/**
	 * define page title and load template files
	 */
	public function index(){
		View::rendertemplate('pagePrepare', $data);
		View::render('test/test', $data);
		View::rendertemplate('pageEnd', $data);
	}
	public function test(){
//		echo "!";
		echo \helpers\currency::convertTest();
	        
	}
	public function shell(){
		View::rendertemplate('pagePrepare', $data);
		View::render('shell', $data);
		View::rendertemplate('pageEnd', $data);	        
	}
}
