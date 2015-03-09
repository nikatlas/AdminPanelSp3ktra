<h1>
Login
</h1>
<?php 
if ( $data['error']==1 ) {
?>
<p>
<div class="alert alert-danger alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
      <strong> * </strong> Credentials you provided was wrong!
</div>
</p>
<?php }?>
<form action="/login" method="post">
<input type="hidden" name="login" value="login" />
<p>
<input type="text" name="name" class="form-control" style="width:140px;"  value="" placeholder="Username" />
</p>
<p>
<input type="password" name="pass" class="form-control" style="width:140px;"  value="" placeholder="Password" />
<p>
<input class="btn btn-success" type="submit" value="Login" />
</p>
</form>