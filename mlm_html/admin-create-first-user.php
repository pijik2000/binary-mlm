<?php
error_reporting(0);
require_once("php-form-validation.php");
function register_first_user()
{ 
	global $wpdb;	
	//get database table prefix
	$table_prefix = mlm_core_get_table_prefix();
	
	$error = '';
	$chk = 'error';
	
	//most outer if condition
	if(isset($_POST['submit']))
	{

		$username = sanitize_text_field( $_POST['username'] );
		$password = sanitize_text_field( $_POST['password'] );
		$confirm_pass = sanitize_text_field( $_POST['confirm_password'] );
		$email = sanitize_text_field( $_POST['email'] );
		$confirm_email = sanitize_text_field( $_POST['confirm_email'] );
				
		//Add usernames we don't want used
		$invalid_usernames = array( 'admin' );
		
		//Do username validation
		$username = sanitize_user( $username );
		
		if(!validate_username($username) || in_array($username, $invalid_usernames)) 
			$error .= "\n Username is invalid.";

		if ( username_exists( $username ) ) 
			$error .= "\n Username already exists.";
		
		if ( checkInputField($username) ) 
			$error .= "\n Please enter your username.";
		
		if ( checkInputField($password) ) 
			$error .= "\n Please enter your password.";
			
		if ( confirmPassword($password, $confirm_pass) ) 
			$error .= "\n Please confirm your password.";
					
		//Do e-mail address validation
		if ( !is_email( $email ) )
			$error .= "\n E-mail address is invalid.";
			
		if (email_exists($email))
			$error .= "\n E-mail address is already in use.";
			
		if ( confirmEmail($email, $confirm_email) ) 
			$error .= "\n Please confirm your email address.";

		//generate random numeric key for new user registration
		$user_key = generateKey();

		// outer if condition
		if(empty($error))
		{
				$user = array
				(
					'user_login' => $username,
					'user_pass' => $password,
					'user_email' => $email
				);
				
				// return the wp_users table inserted user's ID
				$user_id = wp_insert_user($user);
				
				/*Send e-mail to admin and new user - 
				You could create your own e-mail instead of using this function*/
				wp_new_user_notification($user_id, $password);
				
				//insert the data into fa_user table
				$insert = "INSERT INTO {$table_prefix}mlm_users
						   					(
																user_id, username, user_key, parent_key, sponsor_key, leg
													) 
													VALUES
													(
															'".$user_id."','".$username."', '".$user_key."', '0', '0', '0'
													)";
							
				// if all data successfully inserted
				if($wpdb->query($insert))
				{
					$chk = '';
					//$msg = "<span style='color:green;'>Congratulations! You have successfully registered in the system.</span>";
				}
		}//end outer if condition
	}//end most outer if condition
	
	//if any error occoured
	if(!empty($error))
		$error = nl2br($error);
		
	if($chk!='')
	{
?>
<p>&nbsp;</p>
<div class="forms-ui">
<span style='color:red;'><?=$error?></span>
<p>&nbsp;</p>
<form name="frm" method="post" action="" onSubmit="return adminFormValidation();">
<table border="0" cellpadding="0" cellspacing="0" width="100%"  class="form-table">
	<tr>
		<th scope="row" class="admin-settings">
			<a style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('create-first-user');">
			<?php _e('Create Username <span style="color:red;">*</span>:');?> </a>
		</th>
		<td>
			<input type="text" name="username" id="username" value="<?= htmlentities($_POST['username'])?>" maxlength="20" size="37">
			<div class="toggle-visibility" id="create-first-user"><?php _e('Please create the first user of the your network.');?></div>
		</td>
	</tr>
		
	<tr>
		<th scope="row" class="admin-settings">
			<a style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('create-password');">
			<?php _e('Create Password <span style="color:red;">*</span>:');?> </a>
		</th>
		<td><input type="password" name="password" id="password" maxlength="20" size="37" >
			<div class="toggle-visibility" id="create-password"><?php _e('Password length is atleast 6 char.');?></div>
		</td>
	</tr>

	<tr>
		<th scope="row" class="admin-settings">
		<a style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('confirm-password');">
			<?php _e('Confirm Password <span style="color:red;">*</span>:');?> </a>
		</th>
		<td>
			<input type="password" name="confirm_password" id="confirm_password" maxlength="20" size="37" >
			<div class="toggle-visibility" id="confirm-password"><?php _e('Please confirm your password.');?></div>
		</td>
	</tr>

	<tr>
		<th scope="row" class="admin-settings">
		<a style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('email-address');">
			<?php _e('Email Address <span style="color:red;">*</span>:');?> </a>
		</th>
		<td>
			<input type="text" name="email" id="email" value="<?= htmlentities($_POST['email']);?>"  size="37" >
			<div class="toggle-visibility" id="email-address"><?php _e('Please specify your email address.');?></div>
		</td>
	</tr>
		
	<tr>
	<th>
		<a style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('confirm-address');">
			<?php _e('Confirm Email Address <span style="color:red;">*</span>:');?> </a>
		</th>
		<td>
		<input type="text" name="confirm_email" id="confirm_email" value="<?= htmlentities($_POST['confirm_email']);?>" size="37" >
		<div class="toggle-visibility" id="confirm-address"><?php _e('Please confirm your email address.');?></div>
		</td>
		</tr>
</table>
	<p class="submit">
		<input type="submit" name="submit" id="submit" value="Submit" class='button-primary' onclick="needToConfirm = false;"/>
	</p>
	</form>
</div>	
	<script language="JavaScript">
  populateArrays();
</script>
<?php
	}
	else
		echo "<script>window.location='admin.php?page=admin-settings&tab=general&msg=s'</script>";
}//function end

?>