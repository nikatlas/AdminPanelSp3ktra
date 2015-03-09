<?php
namespace controllers;

class Cron extends \core\controller{

	public function __construct(){
		parent::__construct();
	}

	public function run(){
		$model = new \models\user\user();
		$model = new \models\cron();
		$model->run();
	}

}
?>
