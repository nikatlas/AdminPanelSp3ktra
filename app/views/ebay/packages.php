<h1 style="float:left">
Items
</h1>
<div>
<a href="/ebay/package/addnew" class="btn btn-primary" style="margin-bottom:2px; float: right;">Add new Package</a>
</div>
<p style="clear:both;"></p>
<?php 
if( $data['error'] == 1 ){
?>
<p>
<div class="alert alert-danger alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
      <strong>Error : </strong> There are no items on the database! Add some and come back here to view them! :)
</div>
</p>
<?php
}else{
?>
<script>
function show(id){
	$(".hide_"+id).toggle(500);
}
</script>
<style>
.link{
cursor: pointer;
}
.grey{
background:#F3F4CF;
}
table tbody tr td img{
	max-height:120px;
	max-width :120px;	
}
table tbody tr td div ul{
	right:0px !important; left:auto !important;
}
td{
	vertical-align:middle !important;
}
.pagi{
	text-align:center;
}
.bold{ font-weight:bold;}
.xlarge{font-size:20px}
.large{font-size:16px}
.small{font-size:12px}
.xsmall{font-size:9px}
.green{color:#01A95B}
</style>
<script type="text/javascript">
function backToActive(id){
		$.ajax({
		  type: "POST",
		  url: '/ebay/backtoactive/'+id,
		  success: function(){
			$("#line_"+id).hide(1000);  
		  }
		});
}
function deleteNote(id){
		$.ajax({
		  type: "POST",
		  url: '/notes/delete/'+id,
		  success: function(){
			$("#s"+id).hide(500);
			$("#f"+id).hide(500);
		  }
		});
}
function updateNote(id){
		$.ajax({
		  type: "POST",
		  url: '/notes/update/'+id,
		  data: { content : document.getElementById("c"+id).value , up : 1},
		  success: function(){
			$("#sc"+id).html($("#c"+id).val());
			$("#f"+id).hide(500);
			$("#s"+id).show(500);

		  }
		});
}
function createNote(id){
		$.ajax({
		  type: "POST",
		  url: '/notes/add/'+id,
		  data: { content : '' , add : 1 , ret : 'id'},
		  success: function(data){
			$("#notes"+id).append(
							'<span id="s'+data+'" style="display:none;"><a href="javascript:showUpdate('+data+')">Edit</a> <span style="color:#000;">Note by me: </span><span id="sc'+data+'"></span><a style="float:right;" href="javascript:deleteNote('+data+')">Delete</a></span>					  <span id="f'+data+'" ><a href="javascript:updateNote('+data+')" >Update</a> <span style="color:#000;">Note by me: </span><textarea id="c'+data+'"></textarea></span><br>'
			);
		  }
		});
}
function showUpdate(id){
		$("#s"+id).hide(500);
		$("#c"+id).val($("#sc"+id).html());
		$("#f"+id).show(500);
}
function gotoURL(url){
	var params = window.location.search;
	window.location.href = url + params;	
}
</script>
<div class="pagi">
<div style="float:left;">
<span>Page <?php echo $data['page']; ?> of <?php echo $data['totalPages']; ?></span>
<?php if ( $data['page'] > 1 ){ ?><a href="javascript:gotoURL('/ebay/packages/<?php echo $data['alert'];?><?php echo $data['page']-1;?>');">Previous</a><?php }?>
<?php if ( $data['page'] < $data['totalPages'] ){ ?><a href="javascript:gotoURL('/ebay/packages/<?php echo $data['alert'];?><?php echo $data['page']+1;?>');">Next</a><?php }?>
</div>

<div style="float:right;">
<span>Per page </span>
<select name="perpage" onchange="window.location.href='/user/changeperpage/'+this.value;">
<?php $opts = array ( 2,3,50,100,150,200);?>
    <?php foreach ( $opts as $opt ) {?>
	<option value="<? echo $opt;?>" <? if( \helpers\session::get('perPage') == $opt)echo 'selected';?>><? echo $opt;?></option>
	<?php }?>
</select>
</div>

<div>
<form action="/ebay/packages/<?php echo $data['alert'];?>1" method="get">
<input type="text" name="title" value="<?php echo $_REQUEST['title'];?>" placeholder="Title..." />
<input type="text" name="itemid" value="<?php echo $_REQUEST['itemid'];?>" placeholder="ItemID..." />
<select name="user">
		<option value="">All</option>
	<?php foreach( (array)$data['users'] as $user ){?>
    		<option value="<?php echo $user->id;?>" <?php if($user->id == $_REQUEST['user'])echo 'selected';?>><?php echo $user->username;?></option>
    <?php }?>
</select>

<input type="submit" value="Search" class="btn btn-primary"/>
</form>
</div>

</div>
<table class="table table-bordered table-hover">
<thead>
	<tr>
    	<th>#</th>
    	<th>Name</th>
    	<th>Status</th>
    	<th style="width:125px;">Price</th>    	
    	<th style="width:110px;">Actions</th>
    </tr>
</thead>
<tbody>
<?php 
$model = new \models\ebay\item();
$user = new \models\user\user();
$z = $data['startid'];
foreach ( $data['item'] as $item ){
?>
	<tr>
    	<td><?php echo ++$z;?></td>
    	<td>
			<a target="_new" href="/ebay/package/<?php echo $item->id;?>" class="bold"><?php echo $item->name;?></a>
            <span class="xsmall">(<?php echo $item->id; ?>)</span>
            <br />
            <br>User : <?php echo $item->username;?>        
        </td>
    	<td style="text-align:center">
			<?php echo ucfirst($item->listing);?><br>
            <?php if ( $item->oldprice != 0 && $item->currentprice != $item->oldprice ){
						if( $item->currentprice - $item->oldprice > 0){	?>
            				<img src="http://ebayreports.com/img/red.png" style="width:50px;"/><BR />
            <?php 		}else{ ?>
            				<img src="http://ebayreports.com/img/green.png" style="width:50px;"/><BR />
			<?php  		}?>
            			<span class="bold"><? echo $item->currentprice - $item->oldprice;?> € </span>
            <?php
				  }
			?>
        </td>
    	<td>
        	<span class="large bold"><?php echo $item->currentprice;?> €</span><br />
            <span class="xsmall">Old : <?php echo $item->oldprice;?> €</span><br />
			<span class="small">OurShip.: <?php echo $item->weight;?> €</span><br>
  			<span class="small">Profit  : <?php echo $item->profit;?> €</span><br>
        </td>
    	
    	<td>
        	<div class="btn-group">
            <?php 
			if ( $item->user == $user->id || $user->privs > 2){?>
              <button type="button" class="btn btn-info" onClick="window.location.href='/ebay/package/<?php echo $item->id;?>'">View</button>
              <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
                <span class="sr-only">Toggle Dropdown</span>
              </button>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#">View other items</a></li>
                <?php if( $data['alertPage'] ){?><li><a href="javascript:backToActive(<?php echo $item->productid;?>);">Back to Active</a></li><?php }?>
                <li><a href="/ebay/package/delete/<?php echo $item->id;?>">Delete</a></li>
              </ul>
              <?php }?>
            </div>
        </td>
    </tr>
	<!------------------------------------------------------------>
	<!------------------------------------------------------------>
	<!------------------------------------------------------------>
	<!------- FOREACH ITEM ADD THEIR Listings -------------------->
	<!------------------------------------------------------------>
	<!------------------------------------------------------------>
	<!------------------------------------------------------------>
	<!------------------------------------------------------------>
	<?php
	
	$arr = explode(',',$item->listingIds);

	for( $i=1;$i<sizeof($arr);$i++){
			$listing = $model->getByItemId($arr[$i]);
?>            
            <tr class="grey hide_<?php echo $item->productid;?>" style="display:none;height:100px;<?php echo (($listing->pinned == 1) ? 'background:#EEF1FF;' : '');?> ">
			<td>
				*
			</td>
			<td width="">
            	<a  target="_new" href="<?php echo $listing->url;?>" data-lightbox="<?php echo $listing->itemid;?>" title=""><img src="<?php echo $listing->imgurl;?>"></a>
                <?php if( $listing->pinned == 0 ) {?>      
               	<br />
				<a href="/ebay/item/makePinned/?id=<?php echo $listing->id;?>">Pin</a>    
                <? }?>
            </td>
			<td ><a target="_new" href="<?php echo $listing->url;?>" id="title" class="bold"><?php echo $listing->name;?></a><br>
			<span class="xsmall" id="code">(<?php echo $listing->itemid;?>)</span><br>
			<span class="bold" id="quantity">Quantity available : <?php echo $listing->quantity;?></span><br>
			<span id="quantity">User : <?php echo $model->getUsername($listing->productid);?></span><br>
			<span class="bold">Seller : <?php echo $listing->seller;?></span><br>
			<span class="small">Time Left: <?php $dt = new DateInterval($listing->timeleft);echo $dt->d." days ".$dt->h . " hours";?></span><br>
			</td>
            <td align="center">
            
            </td>
			<td align="center">
            
            </td>
			<td align="center">
            
            </td>
			<td style="width:100px">
			<br>
			<span id="costs">
			<table style="width:95%;" id="tab-min2">
			<tbody>
            <tr>
			<td valign="middle" style="text-align:<?php echo (($listing->pinned == 0)?'center':'right');?>">
				<span  <?php if($listing->pinned == 1 ) echo 'id="current_price"';else echo 'class="xlarge green"';?>><? echo $listing->currentprice." ".strtoupper($listing->currency);?></span>
				<? if ( strtoupper($listing->currency) != 'EUR' && $listing->pinned == 0) echo "<br><span class='small'>EUR: ".\helpers\currency::convert($listing->currentprice,$listing->currency , 'EUR')."</span>";?>
				<? if ( strtoupper($listing->currency) != 'USD' && $listing->pinned == 0) echo "<br><span class='small'>USD: ".\helpers\currency::convert($listing->currentprice,$listing->currency , 'USD')."</span>";?>
				<? if ( strtoupper($listing->currency) != 'GBP' && $listing->pinned == 0) echo "<br><span class='small'>GBP: ".\helpers\currency::convert($listing->currentprice,$listing->currency , 'GBP')."</span>";?>
			</td>
			</tr>
            <tr>
			</tr>
			<tr style="height:10px;">
			<td width="70%" valign="middle"></td>
			<td width="30%" valign="middle"></td>
			</tr>
			<tr class="plus">
			</tr>
			<tr style="height:10px;">
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
            </tr>
			
			</tbody></table>
			</span></span></td>
			<td align="center">
            
            </td>
			</tr>            
<?php }?>	
	<!------------------------------------------------------------>
	<!------------------------------------------------------------>
	<!------------------------------------------------------------>
	<!------------------------------------------------------------>
	<!------------------------------------------------------------>
	<!------------------------------------------------------------>
<?php }?>
</tbody>
</table>
<div class="pagi">
<span>Page <?php echo $data['page']; ?> of <?php echo $data['totalPages']; ?></span>

<?php if ( $data['page'] > 1 ){ ?><a href="javascript:gotoURL('/ebay/packages/<?php echo $data['alert'];?><?php echo $data['page']-1;?>');">Previous</a><?php }?>
<?php if ( $data['page'] < $data['totalPages'] ){ ?><a href="javascript:gotoURL('/ebay/packages/<?php echo $data['alert'];?><?php echo $data['page']+1;?>');">Next</a><?php }?>

<div style="float:right;">
<span>Per page </span>
<select name="perpage" onchange="window.location.href='/user/changeperpage/'+this.value;">
<?php $opts = array ( 2,3,50,100,150,200);?>
    <?php foreach ( $opts as $opt ) {?>
	<option value="<? echo $opt;?>" <? if( \helpers\session::get('perPage') == $opt)echo 'selected';?>><? echo $opt;?></option>
	<?php }?>
</select>
</div>
</div>

<?php }?>