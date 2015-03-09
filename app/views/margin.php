<h1>Margins</h1>
<table class="table">
	<tr>
    	<th>From</th>
    	<th>To</th>
      	<th>Threshold</th>        
      	<th>Update</th>        
    </tr>
<?php 
	foreach( (array)$data['margins'] as $margin ){
?>
	<tr>
    <form action="/margins/update" method="post">
    		<input type="hidden" name="id" value="<?php echo $margin->id;?>"/>
    	<td><input type="text" name="from" value="<?php echo $margin->from;?>"/></td>
    	<td><input type="text" name="to" value="<?php echo $margin->to;?>"/></td>
    	<td><input type="text" name="threshold" value="<?php echo $margin->threshold;?>"/></td>
    	<td><input type="submit" value="Update"/></td>
    </form>
    </tr>

<?php }?>

</table><br />

<h4>Add more margins</h4><br>
<form action="/margins/add" method="post">
<table class="table">
	<tr>
    	<th>From</th>
    	<th>To</th>
      	<th>Threshold</th>   
        <th>Add</th>     
    </tr>
    <tr>
		<td><input type="text" name="from" value="" placeholder="From..."/></td>
		<td><input type="text" name="to" value="" placeholder="To..."/></td>
		<td><input type="text" name="threshold" value="" placeholder="Threshold... "/></td>
		<td><input type="submit" name="submit" value="Add" /></td>
	</tr>
</table>
</form>