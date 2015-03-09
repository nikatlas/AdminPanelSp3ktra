<h1>Change Pass</h1>
<?php 
if( $data['error'] == 1 ){
?>
<p>
<div class="alert alert-danger alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
      <strong>Error : </strong> Either the current password is wrong or the new passwords doesnt match!
</div>
</p>
<?php
} 
else if( $data['error'] == 2 ){
?>
<p>
<div class="alert alert-success alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
      <strong>Success : </strong> Password Changed
</div>
</p>
<?php
}
?>
<div>
<form action="" method="post">
<input type="hidden" name="user" value="<?php echo $_REQUEST['user'];?>" />
<input type="hidden" name="change" value="1" />
<input type="password" name="oldpass" value="" placeholder="Current Password" /><br>

<input type="password" name="pass" value="" placeholder="New Password" /><br>

<input type="password" name="repass" value="" placeholder="Retype new Password" /><br>
<input type="submit" class="btn btn-primary" value="Change"/>
</form>
</div>