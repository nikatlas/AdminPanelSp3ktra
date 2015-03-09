<!DOCTYPE html>
<head>
  <meta charset="utf-8">
  <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
  <title>EbayReports</title>
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="viewport" content="width=device-width">
  <link rel="stylesheet" href="<? echo \helpers\url::get_template_path();?>css/templatemo_main.css">
  <style>
  .templatemo-content{
	margin-left:0px !important;  
  }
  .menu{
		display: inline-block;
		vertical-align: bottom;
		width: 100%;
		text-align: center;
		bottom: 0px;
		position: absolute;
		left: 0px;
  }
  .menu a div{
	display: inline-block;
	width: 100px;
	height: 50px;
	background: rgba(66, 139, 202, 0.43);
	margin: 0px -2px;
	border-left: 1px solid #666;
	line-height: 50px;
	text-align: center;
	padding: 0 8px;
	color:#CCC;
	font-weight:bold;
  }
  .menu a div:hover{
	background: rgba(66, 139, 202, 1);
	color:#FFF
  }
  .menu a.selected div{
	background: rgba(66, 139, 202, 1);
	color:#FFF
  }
  .menu a div:last-child{
	border-right: 1px solid #666;
  }
  .bmenu{ 
	    display: inline-block;
		vertical-align: bottom;
		width: 100%;
		text-align: center;
		position: absolute;
		bottom: -30px;
		left: 0px;
		background: #E4E4E4;
  }
  .bmenu a div{
	display: inline-block;
	width: 145px;
	height: 30px;
	background: rgba(66, 139, 202, 0.83);
	margin: 0px -2px;
	border-left: 1px solid #666;
	border-top: 1px solid #666;	
	line-height: 30px;
	text-align: center;
	padding: 0 8px;
	color:#CCC;
	font-weight:bold;
  }
  .bmenu a.selected div{
	background: rgba(66, 139, 202, 1);
	color:#FFF
  }
  .bmenu a div:hover{
	background: rgba(66, 139, 202, 1);
	color:#FFF
  }
  .bmenu a div:last-child{
	border-right: 1px solid #666;
  }
  </style>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
</head>
<body>
  <!-- HEADER -->
  <div class="navbar navbar-inverse" role="navigation">
      <div class="navbar-header" >
        <div class="logo"><h1>EbayReports </h1></div>
        <div class="menu">
        				<a href="/" <?php if( strlen($_SERVER['REQUEST_URI']) < 3 ) echo "class='selected'";?>><div>Home</div></a>
        	        	<a href="/ebay/list" <?php if(strpos($_SERVER['REQUEST_URI'],"ebay/list")!==false && strpos($_SERVER['REQUEST_URI'],"/ebay/list/alerts")===false)echo 'class="selected"';?> ><div>Items<span class="badge pull-right" style="position:relative;top:17px;"><?php $m = new \models\ebay\item(); echo $m->countItems();?></span></div></a>
        	        	<a href="/ebay/packages" <?php if(strpos($_SERVER['REQUEST_URI'],"ebay/packages")!==false && strpos($_SERVER['REQUEST_URI'],"/ebay/packages/alert")===false)echo 'class="selected"';?> ><div>Packs<span class="badge pull-right" style="position:relative;top:17px;"><?php $p = new \models\ebay\package(); echo $p->countPackages();?></span></div></a>
        	        	<a href="/ebay/item/addnew" <?php if(strpos($_SERVER['REQUEST_URI'],"ebay/item/addnew")!==false)echo 'class="selected"';?> ><div>Add New</div></a>
        	        	<a href="/ebay/list/alerts" <?php if(strpos($_SERVER['REQUEST_URI'],"/ebay/list/alerts")!==false)echo 'class="selected"';?> ><div>Alerts<span class="badge pull-right" style="position:relative;top:17px;"><?php  echo $m->countAlerts();?></span></div></a>
                        <a href="/ebay/packages/alert" <?php if(strpos($_SERVER['REQUEST_URI'],"/ebay/packages/alert")!==false)echo 'class="selected"';?> ><div style="font-size:12px !important">P.Alerts<span class="badge pull-right" style="position:relative;top:17px;"><?php  echo $p->countAlerts();?></span></div></a>
        	        	<a href="/user/users" <?php if(strpos($_SERVER['REQUEST_URI'],"user/users")!==false)echo 'class="selected"';?> ><div>Users</div></a>
        	        	<?php   $user = new \models\user\user();
							if( $user->privs > 3 ){ ?>
                        <a href="/margins" <?php if(strpos($_SERVER['REQUEST_URI'],"margins")!==false)echo 'class="selected"';?> ><div>Margins</div></a>
        	        	<a href="/ebay/account" <?php if(strpos($_SERVER['REQUEST_URI'],"ebay/account")!==false)echo 'class="selected"';?> ><div>Accounts</div></a>
                        <?php }?>
         				<a href="javascript:;" data-toggle="modal" data-target="#confirmModal"><div><i class="fa fa-sign-out"></i>Sign Out</div></a>
        </div>
        

        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <div style="padding: 5px 10px;position: absolute;right: 1px;top: 0px;">
        <?php 
			$user = new \models\user\user();
			if( $user->isLoggedRedirect() ){
		?>	
        	<span style="font-size:14px;">
            	Logged in as <strong><?php echo ucfirst($user->username);?></strong>
            </span><br>
            <span style="font-size:10px">
            	 <? echo $user->getPrivName();?> privilleges  <a href="/logout" >Logout</a>
            </span>
        <?php 
			}
		?>
        </div>
        <div class="bmenu">
        				
        	        	<a href="/ebay/list/Active" <?php if(strpos($_SERVER['REQUEST_URI'],"ebay/list/Active")!==false)echo 'class="selected"';?>><div>Active<span class="badge pull-right" style="position:relative;top:5px;"><?php echo $m->countItemsByStatus("Active");?></span></div></a>
        	        	<a href="/ebay/list/Completed" <?php if(strpos($_SERVER['REQUEST_URI'],"ebay/list/Completed")!==false)echo 'class="selected"';?>><div>Ended<span class="badge pull-right" style="position:relative;top:5px;"><?php echo $m->countItemsByStatus("Completed");?></span></div></a>
        	        	<a href="/ebay/list/changed" <?php if(strpos($_SERVER['REQUEST_URI'],"ebay/list/changed")!==false)echo 'class="selected"';?>><div style="font-size:13px;">Price Changes<span class="badge pull-right" style="position:relative;top:5px;"><?php echo $m->countPriceChanges();?></span></div></a>
        	        	<a href="/ebay/list/outofstock" <?php if(strpos($_SERVER['REQUEST_URI'],"ebay/list/outofstock")!==false)echo 'class="selected"';?>><div>Out of Stock<span class="badge pull-right" style="position:relative;top:5px;"><?php echo $m->countOutOfStock();?></span></div></a>
        </div>
      </div>
    </div>
  <!-- END OF HEADER -->
  <!-- MAIN BODY GOES IN THIS DIV -->
  <div class="template-page-wrapper">
  <!-- INCLUDE MENU AND CONTENT -->
