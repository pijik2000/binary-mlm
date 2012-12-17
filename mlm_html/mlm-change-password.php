<?php
error_reporting(0);
require_once("php-form-validation.php");
function mlm_change_password()
{ 
	$error = '';
	global $current_user;
	get_currentuserinfo();
	$sponsor_name = $current_user->user_login;
	
	//most outer if condition
	if(isset($_POST['submit']))
	{
		$password = sanitize_text_field( $_POST['password'] );
		$confirm_pass = sanitize_text_field( $_POST['confirm_password'] );

		if ( checkInputField($password) ) 
			$error .= "\n Please enter your new password.";
			
		if ( confirmPassword($password, $confirm_pass) ) 
			$error .= "\n Your confirm password does not match.";

		// inner if condition
		if(empty($error))
		{
				$user = array
				(
					'ID' => $current_user->ID,
					'user_pass' => $password,
				);
				
				// return the wp_users table inserted user's ID
				$user_id = wp_update_user($user);

				$msg = "<span style='color:green;'>Congratulations! Your password has been successfully updated.</span>";
		}//end inner if condition
	}//end most outer if condition
	
	//if any error occoured
	if(!empty($error))
		$error = nl2br($error);
		
	echo $msg;
?>
<script type="text/javascript" src="<?= plugins_url().'/mlm/js/form-validation.js'?>"></script>

<span style='color:red;'><?=$error?></span>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<form name="frm" method="post" action="" onSubmit="return updatePassword();">
		<tr>
			<td>New Password <span style="color:red;">*</span> :</td>
			<td>	<input type="password" name="password" id="password" maxlength="20" size="37" >
				<br /><span style="font-size:12px; font-style:italic; color:#006633">Password length atleast 6 character</span>
			</td>
		</tr>
		
		<tr><td colspan="2">&nbsp;</td></tr>
		
		<tr>
			<td>Type Again<span style="color:red;">*</span> :</td>
			<td>	<input type="password" name="confirm_password" id="confirm_password" maxlength="20" size="37" >
			</td>
		</tr>
		
		<tr><td colspan="2">&nbsp;</td></tr>
		
		<tr>
			<td colspan="2"><input type="submit" name="submit" id="submit" value="Submit" /></td>
		</tr>
	</form>
</table>
<?php
}//function end
?>