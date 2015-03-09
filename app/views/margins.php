<h1>Margins</h1>
<table>
	<tr>
    	<th>From</th>
    	<th>To</th>
      	<th>Threshold</th>        
    </tr>
<?php 
	foreach( (array)$data['margins'] as $margin ){
?>
	<tr>
    	<td><?php echo $margin->from;?></td>
    	<td><?php echo $margin->to;?></td>
    	<td><?php echo $margin->threshold;?></td>
    </tr>

<?php }?>

</table>