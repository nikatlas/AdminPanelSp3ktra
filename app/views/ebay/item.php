<h1>
Ebay Item 
</h1>
<style>
.bold{ font-weight:bold;}
.xlarge{font-size:24px}
.large{font-size:16px}
.small{font-size:12px}
.xsmall{font-size:9px}
.green{color:#01A95B}
</style>
<?php
if( $data['error'] == 1 ){
?>
<p>
<div class="alert alert-danger alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
      <strong>Error : </strong> The product id you requested can't be found on the database!
</div>
</p>
<?php
}else{
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script>
function askToAdd(){
var id = prompt("Enter the itemId: " , '');
	if( id != null && id.length > 4 ){
		window.location.href = "/ebay/fetchtoid/?itemid="+id+"&id="+<?php echo $data['item'][0]->productid;?>;
	}
}
var cost = 0;
function changeSelect(){
var cp = parseFloat($("#current_price").html());
var shipping = parseFloat($("#fshipping").val());
var our_shipping = parseFloat($("select#fweight option:selected").attr('name'));
var pc = parseFloat($("#profit").val());
var insurance = parseFloat($("#finsurance option:selected").val());
var vat = 1.19;
var biig = 45 * document.getElementById('big').checked;
cost =  ( cp /  (1 + 0.19 * document.getElementById('vat').checked)  + shipping + our_shipping + pc + insurance + biig ) * vat;
cost = cost.toFixed(2);

$("#our_shipping").html(our_shipping);
$("#profit_cost").html(pc);
$("#rec").html(cost);

dollar = cost* $("input#dollar").val();
dollar = dollar.toFixed(2);
$("#convert_dollar").html(dollar);
gbp = cost* $("input#gbp").val();
gbp = gbp.toFixed(2);
$("#convert_gbp").html(gbp);

price_shipping = parseFloat(parseFloat($("#fshipping").val()) + parseFloat($("#current_price").html()));
price_shipping = price_shipping.toFixed(2);
$("#price_shipping").html(price_shipping);
}

$(document).ready(function(){
	dollar = cost* $("input#dollar").val();
	dollar = dollar.toFixed(2);
	$("#convert_dollar").html(dollar);
	gbp = cost* $("input#gbp").val();
	gbp = gbp.toFixed(2);
	$("#convert_gbp").html(gbp);
});

function verify_value()
{
	value = $("#profit").val();
	if(value.length == 0)
	{
	$("#profit").val("0.00");
	value = 0;
	}
	changeSelect();
}
function validate(evt) {
  var theEvent = evt || window.event;
  var key = theEvent.keyCode || theEvent.which;
  key = String.fromCharCode( key );
  var regex = /[0-9]|\./;
  if( !regex.test(key) ) {
    theEvent.returnValue = false;
    if(theEvent.preventDefault) theEvent.preventDefault();
  }
} 

$(document).ready(function(){
	changeSelect();
});
</script>
<style>
.bold{ font-weight:bold;}
.large{font-size:16px}
td img{
max-width: 120px;max-height: 120px;border:1px solid #C7C7C7;	
}
</style>

<table style="border:1px solid #C7C7C7;" class="items table">

		<tbody>
<?php
	$model = new \models\ebay\item();
	foreach($data['item'] as $item){
?>            
            <tr style="height:100px;<?php echo (($item->pinned == 1) ? 'background:#EEF1FF;' : '');?> ">
                <td width="18%">
                    <a  target="_new" href="<?php echo $item->url;?>" data-lightbox="<?php echo $item->itemid;?>" title=""><img src="<?php echo $item->imgurl;?>"></a>
                    <?php if( $item->pinned == 0 ) {?>      
                    <br />
                    <a href="/ebay/item/makePinned/?id=<?php echo $item->id;?>">Pin</a>    
                    <? }?>
                </td>
                <td colspan="3" width="54%"><a id="title" class="bold"><?php echo $item->name;?></a><br>
                    <span class="xsmall" id="code">(<?php echo $item->itemid;?>)</span><br>
                    <span class="bold" id="quantity">Quantity available : <?php echo $item->quantity;?></span><br>
                    <span id="quantity">User : <?php echo $model->getUsername($item->productid);?></span><br>
                    <span class="bold">Seller : <?php echo $item->seller;?></span><br>
                    <span class="small">Time Left: <?php //$dt = new DateInterval($item->timeleft);echo $dt->d." days ".$dt->h . " hours";?></span><br>
                    <?php if( $item->pinned == 1 ) {?>            
                    <span class="notes" style="width:500px;">
                    <?php	if( $data['notes'] != NULL ){ 
                                foreach ( (array)$data['notes'] as $note ){ ?>
                                    <a href="/notes/update/<?php echo $note->id;?>">Edit</a> <span style="color:#000;">Note by <? echo $note->username;?>: </span>
                                    <span><? echo $note->content;?></span><a style="float:right;" href="/notes/delete/<?php echo $note->id;?>">Delete</a><BR> 	
                        <?php	}
                            }?>
                    </span>
                    <?php }?>
                    <?php if ( $item->pinned == 0 ){?>
                        <form action="/ebay/item/margin" method="post" >
                            <input type="hidden" name="mid" value="<?php echo $item->id;?>"/>
                            <input type="text" name="margin" value="<?php if($item->margin != 0) echo $item->margin;?>" placeholder="Custom Difference..." />
                            <input type="submit" value="Save" class="btn btn-primary" />
                        </form> 
                        <form action="/ebay/item/quantityChange" method="post" >
                            <input type="hidden" name="qid" value="<?php echo $item->id;?>"/>
                            <input type="text" name="nquantity" value="<?php if($item->nquantity != NULL) echo $item->nquantity;?>" placeholder="Change Quantity..." />
                            <input type="submit" value="Save" class="btn btn-primary" />
                        </form> 
                    <?php }?>
                </td>
                <td align="center">
					<?php if($item->pinned == 1){$pndid=$item->itemid;?>
        
                    <?php if ( $item->oldprice != 0 ){?>
                     <span class="small">Old price: <? echo ($item->oldprice)." ".strtoupper($item->currency);?> </span><br>
                    <?php		if( $item->currentprice - $item->oldprice > 0){	?>
                                    <img src="http://ebayreports.com/img/red.png" style="width:50px; border:0"/><BR />
                    <?php 		}else{ ?>
                                    <img src="http://ebayreports.com/img/green.png" style="width:50px; border:0"/><BR />
                    <?php  		}?>
                                <span class="bold"><? echo ($item->currentprice - $item->oldprice)." ".strtoupper($item->currency);?> </span>
                    <?php
                          }
                        }
                    ?>
                </td>
				<td width="26%" rowspan="">
					<br>
					<span id="costs">
					<table style="width:95%;" id="tab-min2">
					<tbody>
        	    		<tr>
							<td valign="middle"><b style="font-size:15px;float:right;">Current Price </b>
                            </td>
							<td valign="middle" style="text-align:<?php echo (($item->pinned == 0)?'center':'right');?>">
								<span  <?php if($item->pinned == 1 ) echo 'id="current_price"';else echo 'class="xlarge green"';?>><? echo $item->currentprice." ".strtoupper($item->currency);?></span>
<? if ( strtoupper($item->currency) != 'EUR' && $item->pinned == 0) echo "<br><span class='xsmall'>EUR: ".\helpers\currency::convert($item->currentprice,$item->currency , 'EUR')."</span>";?>
<? if ( strtoupper($item->currency) != 'USD' && $item->pinned == 0) echo "<br><span class='xsmall'>USD: ".\helpers\currency::convert($item->currentprice,$item->currency , 'USD')."</span>";?>
<? if ( strtoupper($item->currency) != 'GBP' && $item->pinned == 0) echo "<br><span class='xsmall'>GBP: ".\helpers\currency::convert($item->currentprice,$item->currency , 'GBP')."</span>";?>
							</td>
							<td></td>
						</tr>
          				<tr>
							<?php if( $item->pinned == 1 ){ ?>
                            <td width="60%" valign="middle"><b style="font-size:15px;float:right;">Price inc. shipping:</b></td>
                            <td width="30%" valign="middle" style="text-align:right"><span  <?php if($item->pinned == 1 ) echo 'id="price_shipping"';else echo 'class="large bold"';?>><? echo \helpers\currency::convert($item->currentprice + $item->shippingcost,$item->currency , 'EUR'); ?></span>
           					</td>
							<td>€</td>
							<?php }?>
						</tr>
						<tr style="height:10px;">
                            <td width="70%" valign="middle"></td>
                            <td width="30%" valign="middle"></td>
                        </tr>
                        <tr class="plus">
							<?php if( $item->pinned == 1 ){ ?>
                            <td width="70%" valign="middle" style="text-align:right">Shipping : </td>
                            <td width="30%" valign="middle" style="text-align:right"> <?php echo $item->shippingcost ;?></td>
                            <td><?php echo strtoupper($item->currency);?></td>
                            <?php }?>
                        </tr>
						<?php if( $item->pinned == 1 ) {?>            
                        <tr class="plus">
                            <input id="fshipping" name="fshipping" type="text" value="<? echo $item->shippingcost;?>" style="visibility:hidden;width:0px;">
                            <td width="70%" valign="middle" style="text-align:right">Our Shipping:</td>
                            <td width="30%" valign="middle" style="text-align:right"> <span <?php if($item->pinned == 1 ) echo 'id="our_shipping"';?>>0.00</span></td>
                            <td><?php echo strtoupper($item->currency);?></td>
                        </tr>
                        <tr class="plus">
                            <td width="70%" valign="middle" style="text-align:right">Profit:</td>
                            <td width="30%" valign="middle" style="text-align:right"> <span <?php if($item->pinned == 1 ) echo 'id="profit_cost"';?>>0.00</span></td>
                            <td><?php echo strtoupper($item->currency);?></td>
                        </tr>
              
						<?php }else{?>
						<tr style="height:10px;">
						</tr>
						<tr>
          				  <td></td>
          				  <td><a href="/ebay/item/delete?id=<?php echo $item->id;?>" style="float:right" class="btn btn-danger"  onclick="return confirm('Are you sure you want to delete this item? ');"  >Delete</a></td>
         				  <td></td>
          			   </tr>
						<?php }?>
					</tbody>
           		 </table>
				</span>
             </td>
			</tr>            
<?php if( $item->pinned == 1 ) {?>            
			<tr style="height:50px;border:1px solid #CECECE;"> 
               <form action="/ebay/item/save" method="post">
                <input name="shippingcost" type="hidden" value="<? echo $item->shippingcost;?>">
                <input id="dollar" name="" type="hidden" value="<?php echo \helpers\currency::convert(1, "EUR" , "USD",5);?>">
                <input id="gbp" name="" type="hidden" value="<?php echo \helpers\currency::convert(1, "EUR" , "GBP",5);?>">
				<td width="18%" style="text-align:center">
          			    <div style="float:left;"> <span> - 19 %</span><br />
							<input type="checkbox" onchange="changeSelect();" name="vat" id="vat" <?php if( $item->vat == 1 ) echo 'checked';?>/>
            			</div>
                        <div style="float:left;"> <span> Big Dim.</span><br />
							<input type="checkbox" onchange="changeSelect();" name="big" id="big" <?php if( $item->big == 1 ) echo 'checked';?>/>
            			</div>
						<span style="color:#969C9C">Weight:</span>  <br>
						<div class="styled">
							<select id="fweight" name="fweight" style="visibility: visible;" onchange="changeSelect();">
                                <option name="0.00" value="0" <?php if($item->weight == 0 )echo 'selected=""';?>>0</option>
                                <option name="5.00" value="5" <?php if($item->weight == 5 )echo 'selected=""';?>>0 - 0.5 kg</option>
                                <option name="10.00" value="10" <?php if($item->weight == 10 )echo 'selected=""';?>>0.5 - 1kg</option>
                                <option name="20.00" value="20" <?php if($item->weight == 20 )echo 'selected=""';?>>1-2kg</option>
                                <option name="40.00" value="40" <?php if($item->weight == 40 )echo 'selected=""';?>>2-5kg</option>
                                <option name="60.00" value="60" <?php if($item->weight == 60 )echo 'selected=""';?>>5-10kg</option>
                                <option name="90.00" value="90" <?php if($item->weight == 90 )echo 'selected=""';?>>10-20kg</option>
                                <option name="120.00" value="120" <?php if($item->weight == 120 )echo 'selected=""';?>>20-30kg</option>
                           </select>
						</div>
				</td>
				<td width="18%" style="text-align:center">
					<span style="color:#969C9C">Profit:</span>  <br>
					<input type="text" name="fprofit" value="<?php echo intval($item->profit);?>" onfocus="this.value=''" onblur="verify_value()"  onkeypress="validate(event)" id="profit">	
				</td>
				<td width="18%" style="text-align:center"><span style="color:#969C9C">Insurance:</span>  <br>
        	    <div class="styled">
					<select id="finsurance" name="finsurance" style="visibility: visible;" onchange="changeSelect();">
						<option value="0" <?php if($item->insurancecost == 0 )echo 'selected=""';?>>0 €</option>
						<option value="1.5" <?php if($item->insurancecost == 1.5 )echo 'selected=""';?>>100 €-&gt; 1.5 €</option>
						<option value="3" <?php if($item->insurancecost == 3 )echo 'selected=""';?>>200 € -&gt; 3 €</option>
						<option value="4.5" <?php if($item->insurancecost == 4.5 )echo 'selected=""';?>>300 € -&gt; 4.5 €</option>
						<option value="6" <?php if($item->insurancecost == 6 )echo 'selected=""';?>>400 € -&gt; 6 €</option>
						<option value="7.5" <?php if($item->insurancecost == 7.5 )echo 'selected=""';?>>500 € -&gt; 7.5 €</option>
						<option value="9" <?php if($item->insurancecost == 9 )echo 'selected=""';?>>600 € -&gt; 9 €</option>
						<option value="10.5" <?php if($item->insurancecost == 10.5 )echo 'selected=""';?>>700 € -&gt; 10.5 €</option>
						<option value="12" <?php if($item->insurancecost == 12 )echo 'selected=""';?>>800 € -&gt; 12 €</option>
						<option value="13.5" <?php if($item->insurancecost == 13.5 )echo 'selected=""';?>>900 € -&gt; 13.5 €</option>
						<option value="15" <?php if($item->insurancecost == 15 )echo 'selected=""';?>>1000 € -&gt; 15 €</option>
						<option value="16.5" <?php if($item->insurancecost == 16.5 )echo 'selected=""';?>>1100 € -&gt; 16.5 €</option>
						<option value="18" <?php if($item->insurancecost == 18 )echo 'selected=""';?>>1200 € -&gt; 18 €</option>
						<option value="19.5" <?php if($item->insurancecost == 19.5 )echo 'selected=""';?>>1300 € -&gt; 19.5 €</option>
						<option value="21" <?php if($item->insurancecost == 21 )echo 'selected=""';?>>1400 € -&gt; 21 €</option>
						<option value="22.5" <?php if($item->insurancecost == 22.5 )echo 'selected=""';?>>1500 € -&gt; 22.5 €</option>
					</select>
				</div>
                </td>
   				<td width="18%" style="text-align:center">	
         	       	<input type="hidden" name="id" value="<?php echo $_GET['id'];?>"/>
                	<input type="submit" value="Save" class="btn btn-primary" style="position: relative;right: -40px;bottom: -5px;"/>
          		</td>
	            <td>
    	        </td>
        	    <td>
			
					<table style="width:260px;" id="tab-min2">
               			 <tbody>
              				  <tr style="height:10px;">
               					 <td width="70%" valign="middle"></td>
                           		 <td width="30%" valign="middle"></td>
                              </tr>
                				<tr>
                                    <td width="50%" valign="middle" style="text-align:right"><b style="font-size:17px;">Euro Total:</b></td>
                                    <td width="50%" valign="middle"><span id="rec">0.00</span></td>
                                </tr>
                                <tr style="height:10px;">
                                    <td width="70%" valign="middle"></td>
                                    <td width="30%" valign="middle"></td>
                                </tr>
                                <tr>
                                    <td width="70%" valign="middle" style="text-align:right;font-size:20px;">GBP:</td>
                                    <td width="30%" valign="middle"><span id="convert_gbp">0.00</span></td>
                                </tr>
                                <tr>
                                    <td width="70%" valign="middle" style="text-align:right;font-size:20px;">USD:</td>
                                    <td width="30%" valign="middle"><span id="convert_dollar">0.00</span></td>
                                </tr>			
               			 </tbody>
        		    </table>
				</td>
                </form>
			</tr>	
<?php }?>
<?php }?>
</tbody>

</table>
<br />
<br />
<a href="/ebay/item/deleteAll?id=<?php echo $_GET['id'];?>" onclick="return confirm('Are you sure you want to delete all these? ');" style="float:right" class="btn btn-danger" >Delete All</a>

<form action="/ebay/fetchtoid/" method="post">
<input type="hidden" name="id" value="<?php echo $_GET['id'];?>"/>

<!--<a href="#" onclick="askToAdd();" style="float:right" class="btn btn-primary" >Add Item</a>-->
<input type="submit" style="float:right" class="btn btn-primary" value="Add Items" />
<input type="text" class="form-control" style="float:right; width:140px;" placeholder="ItemId.." value="" name="itemid[]"/>
<input type="text" class="form-control" style="float:right; width:140px;" placeholder="ItemId.." value="" name="itemid[]"/>
<input type="text" class="form-control" style="float:right; width:140px;" placeholder="ItemId.." value="" name="itemid[]"/>
<input type="text" class="form-control" style="float:right; width:140px;" placeholder="ItemId.." value="" name="itemid[]"/>
</form>
<a href="/notes/add/<?php echo $_GET['id'];?>"  style="float:left" class="btn btn-primary" >Add note</a>
<a href="/ebay/revise/<?php echo $_GET['id'];?>"  style="float:left" class="btn btn-primary" >Revise Prices</a>
<a href="/ebay/packages?itemid=<?php echo $pndid;?>"  style="float:left" class="btn btn-primary" >Packages<span class="badge pull-right" style="position:relative;top:2px;"> <?php $p = new \models\ebay\package(); echo $p->countPackagesForItem($pndid);?></span></a>

<?php }?>