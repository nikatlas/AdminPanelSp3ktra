<?php
	if( is_array($data['users']) ){
?>

<script>
function submitChange(id , sel){
	$.ajax({
	    url: '/user/changeprivs',
		type: 'POST',
		data: { id : id , privs : sel.value },
		success: function (data){
			if( data == "0" ){
				
			}
			else{
				alert("Error : " + data);	
			}
		},
		error: function (data){
			alert("500 : Error udpating user privs!");
		}
	});
}

</script>
			<div class="table-responsive">
                <h1 class="margin-bottom-15">Users</h1>
                <table class="table table-striped table-hover table-bordered">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>Role</th>
                      <th>ChangePass</th>
                      <th>Delete</th>
                    </tr>
                  </thead>
                  <tbody>
<?php
	$i = 0;	
	foreach( $data['users'] as $user ){
?>
                    <tr>
                      <td><?php echo ++$i;?></td>
                      <td><?php echo $user->username;?></td>
                      <td>
                      	<select name="privs" onChange="submitChange(<?php echo $user->id;?> , this);">
                            <option value="1" <? echo (($user->privs == 1) ? "selected" :"");?>>User</option>
                            <?php if( $data['privs'] > 4 ){ ?>
                            <option value="4" <? echo (($user->privs == 4) ? "selected" :"");?>>Superuser</option>
                            <?php }if( $data['privs'] > 5 ){ ?>
                            <option value="5" <? echo (($user->privs == 5) ? "selected" :"");?>>Admin</option>
                            <?php } ?>
                        </select>
                      </td>
                      <td><a href="/user/change?user=<?php echo $user->id;?>">Change</a></td>
                      <td><a href="/user/delete?id=<?php echo $user->id;?>" class="btn btn-link">Delete</a></td>
                    </tr>   
<?php
	}
?>
                  </tbody>
                </table>
              </div>
              
<?php
	}else{
?>
	<p>
        <div class="alert alert-warning  alert-dismissible" role="alert">
              <strong>Warning!</strong> There are no users visible to you.
        </div>
    </p>
<?php
	}
?>

<div>
<h1>Create new user</h1>
<span class="btn btn-primary"><a href="/user/register">Register</a></span>
</div>
<div>
<h1>Change my pass</h1>
<span class="btn btn-primary"><a href="/user/change?user=<?php echo $data['myid'];?>">Change</a></span>
</div>