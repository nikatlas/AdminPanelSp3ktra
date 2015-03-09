<h1>Accounts</h1>
<table class="table">
	<tr>
    	<th>Name</th>
    	<th>Session</th>
      	<th>Update</th>        
    </tr>
<?php 
	foreach( (array)$data['accounts'] as $acc ){
?>
	<tr>
    <form action="/ebay/account/update" method="post">
    		<input type="hidden" name="id" value="<?php echo $acc->id;?>"/>
    	<td><input type="text" name="name" value="<?php echo $acc->name;?>"/></td>
    	<td><input type="text" name="session" value="<?php echo $acc->session;?>"/></td>
    	<td><input type="submit" value="Update"/></td>
    </form>
    </tr>

<?php }?>

</table><br />

<h4>Add more accounts</h4><br>
<form action="/ebay/account/add" method="post">
<table class="table">
	<tr>
    	<th>Name</th>
    	<th>Session</th>
        <th>Add</th>     
    </tr>
    <tr>
		<td><input type="text" name="name" value="" placeholder="Seller Name..."/></td>
		<td><input type="text" name="session" value="" placeholder="Session Token..."/></td>
		<td><input type="submit" name="submit" value="Add" /></td>
	</tr>
</table>
</form>