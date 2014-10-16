<?php 

namespace models\ebay;

class Item extends \core\model {
	
	function __construct(){
		parent::__construct();
	}

	function test(){
		return $this->checkCreateTables();
	}


















	function checkCreateTables(){
		$con = mysql_connect(DB_HOST, DB_USER,  DB_PASS) or die("Unable to connect to MySQL");
		$sdb = mysql_select_db(DB_NAME);

		$result = mysql_query("SHOW TABLES LIKE '".PREFIX."products'");
		$tableExists = mysql_num_rows($result) > 0;
		if( !$tableExists ){
			echo "[*] Creating `products` table... ";
			$result = mysql_query("CREATE TABLE ".PREFIX."products(
				id INT NOT NULL AUTO_INCREMENT,PRIMARY KEY(id),
				user INT,
				status varchar(25),
				time_created DATETIME
			)");
			if ( !$result ){
				echo "ERROR: ".mysql_error();
			}
			echo "<br>";
		}
		
		$result = mysql_query("SHOW TABLES LIKE '".PREFIX."products_local'");
		$tableExists = mysql_num_rows($result) > 0;
		if( !$tableExists ){
			echo "[*] Creating `products_local` table... ";
			$result = mysql_query("CREATE TABLE ".PREFIX."products_local(
				id INT NOT NULL AUTO_INCREMENT,PRIMARY KEY(id),
				productid INT,
				itemid TEXT,
				currency varchar(10),
				currentprice decimal(11,2),
				buynowprice decimal(11,2),
				shippingcost decimal(11,2),
				insurancecost decimal(11,2),
				profit       decimal(11,2),
				taxes        decimal(11,2),
				totalprice   decimal(11,2),
				recommendedprice decimal(11,2),			
				name LONGTEXT,
				seller TEXT,
				listing TEXT,
				quantity INT,
				timeleft TEXT,
				endtime DATETIME,
				url TEXT,
				weight TEXT,
				category TEXT,
				storecategory TEXT,
				changed INT,
				imgurl TEXT,
				date DATETIME,
				storeurl TEXT,
				time_created DATETIME
			)");
			if ( !$result ){
				echo "ERROR: ".mysql_error();
			}
			echo "<br>";
		}

		$result = mysql_query("SHOW TABLES LIKE '".PREFIX."products_remote'");
		$tableExists = mysql_num_rows($result) > 0;
		if( !$tableExists ){
			echo "[*] Creating `products_remote` table... ";
			$result = mysql_query("CREATE TABLE ".PREFIX."products_remote(
				id INT NOT NULL AUTO_INCREMENT,PRIMARY KEY(id),
				user INT,
				productid INT,
				itemid TEXT,
				lastsync DATETIME,
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
