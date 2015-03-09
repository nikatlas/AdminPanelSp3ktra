<h1>Update Note</h1><br />
<span>Note submitted by <?php echo $data['note']->username;?></span><br>
<form action="" method="post">
	<input type="hidden" name="up" value="1"/>
    
    <textarea name="content" style="width:500px; height:200px;"><? echo $data['note']->content;?></textarea>
    <br>
	<input type="submit" class="btn btn-primary" value="Update" />
</form>