<h1>
Register new User
</h1>
<?php
	if( $data['warning'] == 1 ){
?>
<p>
<div class="alert alert-danger alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
      <strong>*</strong> You don't have the privileges to create new Users!
</div>
</p>
<?php
	}else{
?>
<?php 
if ( $data['success']==1 ) {
	$role = array( "User" , "" , "" , "SuperUser" , "Admin");
?>
<p>
<div class="alert alert-success alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
      <strong>Success!</strong> User `<?php echo $data['name'];?>` has been created with `<?php echo $role[$data['uprivs']-1]; ?>` role.
</div>
</p>
<?php }?>
<?php 
if ( $data['error']==1 ) {
?>
<p>
<div class="alert alert-danger alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
      <strong>Error!</strong> User `<?php echo $data['name'];?>` exists!
</div>
</p>
<?php }?>
<form action="register" method="post">
<input type="hidden" name="register" value="register" />
<p>
<input type="text" name="name"  class="form-control" style="width:140px;" value="" placeholder="Username" />
</p>
<p>
<input type="password" name="pass" class="form-control" style="width:140px;" value="" placeholder="Password" />
<p>
<select name="privs">
	<option value="1">User</option>
    <?php if( $data['privs'] > 4 ){ ?>
    <option value="4">Superuser</option>
    <option value="5">Admin</option>
	<?php } ?>
</select>
</p>
<p>
<input class="btn btn-primary" type="submit" value="Register" />
</p>
</form>
<?php }?>