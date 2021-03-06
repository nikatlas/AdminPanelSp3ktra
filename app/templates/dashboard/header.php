<!DOCTYPE html>
<head>
  <meta charset="utf-8">
  <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
  <title>Dashboard, Free HTML5 Admin Template</title>
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="viewport" content="width=device-width">
  <link rel="stylesheet" href="<? echo \helpers\url::get_template_path();?>css/templatemo_main.css">
</head>
<body>
  <!-- HEADER -->
  <div class="navbar navbar-inverse" role="navigation">
      <div class="navbar-header" >
        <div class="logo"><h1>EbayReports </h1></div>
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <div style="float:right;padding: 5px 10px;">
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
        
      </div>
    </div>
  <!-- END OF HEADER -->
  <!-- MAIN BODY GOES IN THIS DIV -->
  <div class="template-page-wrapper">
  <!-- INCLUDE MENU AND CONTENT -->
