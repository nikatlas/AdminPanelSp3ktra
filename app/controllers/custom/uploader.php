<?php

namespace controllers\custom;
use core\view as View;

class Uploader extends \core\controller{
	
	public function __construct(){
		parent::__construct();
	}
	public function index(){
		View::render('custom/index',$data);		
	}
	public function process(){
		View::render('custom/processupload',$data);				
	}
	
}