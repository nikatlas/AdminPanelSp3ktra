<h1>Add Note to <span style="color:red"><?php echo $data['title'];?></span></h1><br />

<form action="" method="post">
	<input type="hidden" name="add" value="1"/>
    
    <textarea name="content" style="width:500px; height:200px;" placeholder="Notes..."></textarea>
    <br>
	<input type="submit" class="btn btn-primary" value="Add" />
</form>