<?php

require_once("php-form-validation.php");
function register_user_html_page()
{
	error_reporting(0);
	global $wpdb;
	$table_prefix = mlm_core_get_table_prefix();
	$error = '';
	$chk = 'error';
	global $current_user;
	get_currentuserinfo();
	
	if(is_user_logged_in())
	{
		$sponsor_name = $current_user->user_login;
		$readonly_sponsor = 'readonly';
	
	}else if(isset($_REQUEST['sp']) &&  $_REQUEST['sp'] != ''){
		
		$sponsorName = getusernamebykey($_REQUEST['sp']); 
			
		if(isset($sponsorName) && $sponsorName !='' )		
		{
			$readonly_sponsor = 'readonly';
			$sponsor_name = $sponsorName;
		}else{
			
			redirectPage(home_url(), array()); exit; 

		}
		
	}else{
		$readonly_sponsor = '';
	}
	
	
	//most outer if condition
	if(isset($_POST['submit']))
	{
		$firstname = sanitize_text_field( $_POST['firstname'] );
		$lastname = sanitize_text_field( $_POST['lastname'] );
		$username = sanitize_text_field( $_POST['username'] );
		$password = sanitize_text_field( $_POST['password'] );
		$confirm_pass = sanitize_text_field( $_POST['confirm_password'] );
		$email = sanitize_text_field( $_POST['email'] );
		$confirm_email = sanitize_text_field( $_POST['confirm_email'] );
		$address1 = sanitize_text_field( $_POST['address1'] );
		$address2 = sanitize_text_field( $_POST['address2'] );
		$sponsor = sanitize_text_field( $_POST['sponsor'] );
		$city = sanitize_text_field( $_POST['city'] );
		$state = sanitize_text_field( $_POST['state'] );
		$postalcode = sanitize_text_field( $_POST['postalcode'] );
		$telephone = sanitize_text_field( $_POST['telephone'] );
		$dob = sanitize_text_field( $_POST['dob'] );
		
		
		//Add usernames we don't want used
		$invalid_usernames = array( 'admin' );
		//Do username validation
		$username = sanitize_user( $username );
		
		if(!validate_username($username) || in_array($username, $invalid_usernames)) 
			$error .= "\n Username is invalid.";
			
		if ( username_exists( $username ) ) 
			$error .= "\n Username already exists.";
		
		if ( checkInputField($password) ) 
			$error .= "\n Please enter your password.";
			
		if ( confirmPassword($password, $confirm_pass) ) 
			$error .= "\n Please confirm your password.";
		
		if ( checkInputField($sponsor) ) 
			$error .= "\n Please enter your sponsor name.";
		
		if ( checkInputField($firstname) ) 
			$error .= "\n Please enter your first name.";
			
		if ( checkInputField($lastname) ) 
			$error .= "\n Please enter your last name.";
					
		if ( checkInputField($address1) ) 
			$error .= "\n Please enter your address.";
			
		if ( checkInputField($city) ) 
			$error .= "\n Please enter your city.";
			
		if ( checkInputField($state) ) 
			$error .= "\n Please enter your state.";
			
		if ( checkInputField($postalcode) ) 
			$error .= "\n Please enter your postal code.";
			
		if ( checkInputField($telephone) ) 
			$error .= "\n Please enter your contact number.";

		if ( checkInputField($dob) ) 
			$error .= "\n Please enter your date of birth.";
		
		//Do e-mail address validation
		if ( !is_email( $email ) )
			$error .= "\n E-mail address is invalid.";
			
		if (email_exists($email))
			$error .= "\n E-mail address is already in use.";
		
		if ( confirmEmail($email, $confirm_email) ) 
			$error .= "\n Please confirm your email address.";
		
		$sql = "SELECT COUNT(*) num, `user_key` 
				FROM {$table_prefix}mlm_users 
				WHERE `username` = '".$sponsor."'";
		$intro = $wpdb->get_row($sql);
		
		
		if($_GET['l']!='')
			$leg = $_GET['l']; 
		else
					
			$leg = $_POST['leg'];
			
		if($leg!='0')
		{
			if($leg!='1')
			{
				$error .= "\n You have enter a wrong placement.";
			}
		}
		
		//generate random numeric key for new user registration
		$user_key = generateKey();
		//if generated key is already exist in the DB then again re-generate key
		do
		{
			$check = $wpdb->get_var("SELECT COUNT(*) ck 
													FROM {$table_prefix}mlm_users 
													WHERE `user_key` = '".$user_key."'");
			$flag = 1;
			if($check == 1)
			{
				$user_key = generateKey();
				$flag = 0;
			}
		}while($flag==0);
		
		//check parent key exist or not
		
		if($_GET['k']!='')
		{
			if(!checkKey($_GET['k']))
				$error .= "\n Parent key does't exist.";
			// check if the user can be added at the current position
			$checkallow = checkallowed($_GET['k'],$leg);
			if($checkallow >=1)
				$error .= "\n You have enter a wrong placement.";
		}
		// outer if condition
		if(empty($error))
		{
			// inner if condition
			if($intro->num==1)
			{
				$sponsor = $intro->user_key;
				
				$sponsor1 = $sponsor;
				//find parent key
				if($_GET['k']!='')
				{
					$parent_key = $_GET['k'];
				}
				else
				{
					$readonly_sponsor = '';
					do
					{
						$sql = "SELECT `user_key` FROM {$table_prefix}mlm_users 
								WHERE parent_key = '".$sponsor1."' AND 
								leg = '".$leg."' AND banned = '0'";
						$spon = $wpdb->get_var($sql);
						$num = $wpdb->num_rows;
						if($num)
						{							
							$sponsor1 = $spon;
						}
					}while($num==1);
					$parent_key = $sponsor1;
				}
			
				$user = array
				(
					'user_login' => $username,
					'user_pass' => $password,
					'first_name' => $firstname,
					'last_name' => $lastname,
					'user_email' => $email
				);
				
				// return the wp_users table inserted user's ID
				$user_id = wp_insert_user($user);
				
				//get the selected country name from the country table
				$country = $_POST['country'];
				$sql = "SELECT name 
						FROM {$table_prefix}mlm_country
						WHERE id = '".$country."'";
				$country1 = $wpdb->get_var($sql);
				
				//insert the registration form data into user_meta table
				add_user_meta( $user_id, 'user_address1', $address1, FALSE );  //I have replace FALSE  to  $unique. 
				add_user_meta( $user_id, 'user_address2', $address2, FALSE );
				add_user_meta( $user_id, 'user_city', $city, FALSE );
				add_user_meta( $user_id, 'user_state', $state, FALSE );
				add_user_meta( $user_id, 'user_country', $country1, FALSE );
				add_user_meta( $user_id, 'user_postalcode', $postalcode, FALSE );
				add_user_meta( $user_id, 'user_telephone', $telephone, FALSE );
				add_user_meta( $user_id, 'user_dob', $dob, FALSE);
				
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
								'".$user_id."','".$username."', '".$user_key."', '".$parent_key."', '".$sponsor."', '".$leg."'
							)";
							
				// if all data successfully inserted
				if($wpdb->query($insert))
				{	//begin most inner if condition
					//entry on Left and Right Leg tables
					if($leg==0)
					{
						$insert = "INSERT INTO {$table_prefix}mlm_leftleg 
								   (
										pkey, ukey
									) 
									VALUES 
									(
										'".$parent_key."','".$user_key."'
									)";
						$insert = $wpdb->query($insert);
					}
					else if($leg==1)
					{
						$insert = "INSERT INTO {$table_prefix}mlm_rightleg
								   (
										pkey, ukey
									) 
									VALUES 
									(
										'".$parent_key."','".$user_key."'
									)";
						$insert = $wpdb->query($insert);
					}
					//begin while loop
					while($parent_key!='0')
					{
						$query = "SELECT COUNT(*) num, parent_key, leg 
								  FROM {$table_prefix}mlm_users 
								  WHERE user_key = '".$parent_key."'
								  AND banned = '0'";
						$result = $wpdb->get_row($query);
						if($result->num==1)
						{
							if($result->parent_key!='0')
							{
								if($result->leg==1)
								{
									$tbright = "INSERT INTO {$table_prefix}mlm_rightleg 
												(
													pkey,ukey
												) 
												VALUES
												(
													'".$result->parent_key."','".$user_key."'
												)";
									$tbright = $wpdb->query($tbright);
								}
								else
								{
									$tbleft = "INSERT INTO {$table_prefix}mlm_leftleg 
												(
													pkey, ukey
												) 
												VALUES
												(
													'".$result->parent_key."','".$user_key."'
												)";
									$tbleft = $wpdb->query($tbleft);
								}
							}
							$parent_key = $result->parent_key;
						}
						else
						{
							$parent_key = '0';
						}
					}//end while loop
					$chk = '';
					$msg = "<span style='color:green;'>Congratulations! You have successfully registered in the system.</span>";
				}//end most inner if condition
			} //end inner if condition
			else
				$error =  "\n Sponsor does not exist in the system.";
		}//end outer if condition
	}//end most outer if condition
	
	//if any error occoured
	if(!empty($error))
		$error = nl2br($error);
		
	if($chk!='')
	{
?>

 
<script type="text/javascript">
var popup1,popup2,splofferpopup1;
var bas_cal, dp_cal1,dp_cal2, ms_cal; // declare the calendars as global variables 
window.onload = function() {
	dp_cal1 = new Epoch('dp_cal1','popup',document.getElementById('dob'));  
};

function checkUserNameAvailability(str)
{
	//alert(url); return true; 
		
	if(isSpclChar(str, 'username')==false)
	{
		document.getElementById('username').focus();
		return false;
	}
	var xmlhttp;    
	if (str=="")
  	{
  		alert("Please enter the user name.");
		document.getElementById('username').focus();
		return false;
  	}
	
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	  }
	else
	  {// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
	xmlhttp.onreadystatechange=function()
	  {
	if (xmlhttp.status==200 && xmlhttp.readyState==4)
	{
	 document.getElementById("check_user").innerHTML=xmlhttp.responseText;
	 //alert(xmlhttp.responseText);
	}
	}   
	
	xmlhttp.open("GET", "<?= plugins_url().'/'.MLM_PLUGIN_NAME.'/ajax/check_username.php'?>"+"?action=username&q="+str,true);
	xmlhttp.send();
}



function checkReferrerAvailability(str)
{
	if(isSpclChar(str, 'sponsor')==false)
	{
		document.getElementById('sponsor').focus();
		return false;
	}
	var xmlhttp;    
	if (str=="")
  	{
  		alert("Please enter the sponsor name.");
		document.getElementById('sponsor').focus();
		return false;
  	}
	
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	  }
	else
	  {// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
	xmlhttp.onreadystatechange=function()
	  {
	if (xmlhttp.status==200 && xmlhttp.readyState==4)
	{
	 document.getElementById("check_referrer").innerHTML=xmlhttp.responseText;
	}
	}
	xmlhttp.open("GET", "<?= plugins_url().'/'.MLM_PLUGIN_NAME.'/ajax/check_username.php'?>"+"?action=sponsor&q="+str,true);
	xmlhttp.send();

}

</script>
<span style='color:red;'><?=$error?></span>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<form name="frm" method="post" action="" onSubmit="return formValidation();">
		<tr>
			<td><?php _e('Create Username','binary-mlm-pro');?><span style="color:red;">*</span> :</td>
			<td><input type="text" name="username" id="username" value="<?php if(!empty($_POST['username']))  _e( htmlentities($_POST['username'])); ?>" maxlength="20" size="37" onBlur="checkUserNameAvailability(this.value);"><br /><div id="check_user"></div></td>
		</tr>
		
		<tr><td colspan="2">&nbsp;</td></tr>
		
		<tr>
			<td><?php _e('Create Password','binary-mlm-pro') ?> <span style="color:red;">*</span> :</td>
			<td>	<input type="password" name="password" id="password" maxlength="20" size="37" >
				<br /><span style="font-size:12px; font-style:italic; color:#006633"><?php _e('Password length atleast 6 character','binary-mlm-pro');?></span>
			</td>
		</tr>
		
		<tr><td colspan="2">&nbsp;</td></tr>
		
		<tr>
			<td><?php _e('Confirm Password','binary-mlm-pro') ?>  <span style="color:red;">*</span> :</td>
			<td><input type="password" name="confirm_password" id="confirm_password" maxlength="20" size="37" ></td>
		</tr>
		
		<tr><td colspan="2">&nbsp;</td></tr>
		
		<tr>
			<?php
			if(isset($sponsor_name) && $sponsor_name!='')
			{
				$spon = $sponsor_name;
			}
			else if(isset($_POST['sponsor']))
				$spon =  htmlentities($_POST['sponsor']);
			?>
			<td><?php  _e('Sponsor Name','binary-mlm-pro');?> <span style="color:red;">*</span> :</td>
			<td>
			<input type="text" name="sponsor" id="sponsor" value="<?php if(!empty($spon)) _e($spon);?>" maxlength="20" size="37" onBlur="checkReferrerAvailability(this.value);" <?= $readonly_sponsor;?>>
			<br /><div id="check_referrer"></div>
			</td>
		</tr>
		
		<tr><td colspan="2">&nbsp;</td></tr>
		
		<tr>
			<td><?php  _e('Placement','binary-mlm-pro');?> <span style="color:red;">*</span> :</td>
			<?php
					
					
					if($_POST['leg']=='0'){$checked = 'checked'; }
					else if($_GET['l']=='0')
					{	$checked = 'checked';
						$disable_leg = 'disabled';
					}
					else $checked = '';
					
					if($_POST['leg']=='1'){$checked1 = 'checked'; }
					else if($_GET['l']=='1')
					{	
						$checked1 = 'checked';
						$disable_leg = 'disabled';
					}
					else
						$checked1 = '';
										
			?>

			<td>Left <input id="left" type="radio" name="leg" value="0" <?= $checked;?> <?php if(!empty($disable_leg)) _e($disable_leg);?>/>Right<input id="right" type="radio" name="leg" value="1" <?= $checked1;?> <?php if(!empty($disable_leg)) _e($disable_leg);?>/>

			

			</td>
		</tr>
		
		<tr><td colspan="2">&nbsp;</td></tr>
		
		<tr>
			<td><?php  _e('First Name','binary-mlm-pro');?> <span style="color:red;">*</span> :</td>
			<td><input type="text" name="firstname" id="firstname" value="<?php if(!empty($_POST['firstname']))  _e(htmlentities($_POST['firstname'])); ?>" maxlength="20" size="37" onBlur="return checkname(this.value, 'firstname');" ></td>
		</tr>
		
		<tr><td colspan="2">&nbsp;</td></tr>
		
		<tr>
			<td><?php  _e('Last Name','binary-mlm-pro');?> <span style="color:red;">*</span> :</td>
			<td><input type="text" name="lastname" id="lastname" value="<?php if(!empty($_POST['lastname'])) _e(htmlentities($_POST['lastname']));?>" maxlength="20" size="37" onBlur="return checkname(this.value, 'lastname');"></td>
		</tr>
		
		<tr><td colspan="2">&nbsp;</td></tr>
		
		<tr>
			<td><?php  _e('Address Line 1','binary-mlm-pro');?> <span style="color:red;">*</span> :</td>
			<td><input type="text" name="address1" id="address1" value="<?php if(!empty($_POST['address1'])) _e(htmlentities($_POST['address1']));?>"  size="37" onBlur="return allowspace(this.value,'address1');"></td>
		</tr>
		
		<tr><td colspan="2">&nbsp;</td></tr>
		
		<tr>
			<td><?php  _e('Address Line 2','binary-mlm-pro');?> :</td>
			<td><input type="text" name="address2" id="address2" value="<?php if(!empty($_POST['address2'])) _e(htmlentities($_POST['address2']));?>"  size="37" onBlur="return allowspace(this.value,'address2');"></td>
		</tr>
		
		<tr><td colspan="2">&nbsp;</td></tr>
		
		<tr>
			<td><?php  _e('City','binary-mlm-pro');?> <span style="color:red;">*</span> :</td>
			<td><input type="text" name="city" id="city" value="<?php if(!empty($_POST['city'])) _e(htmlentities($_POST['city']));?>" maxlength="30" size="37" onBlur="return allowspace(this.value,'city');"></td>
		</tr>
		
		<tr><td colspan="2">&nbsp;</td></tr>
		
		<tr>
			<td><?php  _e('State','binary-mlm-pro');?> <span style="color:red;">*</span> :</td>
			<td><input type="text" name="state" id="state" value="<?php if(!empty($_POST['state'])) _e(htmlentities($_POST['state']));?>" maxlength="30" size="37" onBlur="return allowspace(this.value,'state');"></td>
		</tr>
		
		<tr><td colspan="2">&nbsp;</td></tr>
		
		<tr>
			<td><?php  _e('Postal Code','binary-mlm-pro');?> <span style="color:red;">*</span> :</td>
			<td><input type="text" name="postalcode" id="postalcode" value="<?php if(!empty($_POST['postalcode']))  _e(htmlentities($_POST['postalcode']));?>" maxlength="20" size="37" onBlur="return numeric(this.value,'postalcode');"></td>
		</tr>
		
		<tr><td colspan="2">&nbsp;</td></tr>
		
		<tr>
			<td><?php  _e('Country','binary-mlm-pro');?> <span style="color:red;">*</span> :</td>
			<td>
				<?php
					$sql = "SELECT id, name
							FROM {$table_prefix}mlm_country
							ORDER BY name";
							
					$results = $wpdb->get_results($sql);
				?>
				<select name="country" id="country" >
					<option value="">Select Country</option>
				<?php
					
					foreach($results as $row)
					{
						if($_POST['country']==$row->id)
							$selected = 'selected';
						else
							$selected = '';
				?>
						<option value="<?= $row->id;?>" <?= $selected?>><?= $row->name;?></option>
				<?php
					}
				?>
				</select>
			</td>
		</tr>
		
		<tr><td colspan="2">&nbsp;</td></tr>
		
		<tr>
			<td><?php  _e('Email Address','binary-mlm-pro');?> <span style="color:red;">*</span> :</td>
			<td><input type="text" name="email" id="email" value="<?php if(!empty($_POST['email']))  _e(htmlentities($_POST['email']));?>"  size="37" ></td>
		</tr>
		
		<tr><td colspan="2">&nbsp;</td></tr><tr>
		
		<tr>
			<td><?php  _e('Confirm Email Address','binary-mlm-pro');?> <span style="color:red;">*</span> :</td>
			<td><input type="text" name="confirm_email" id="confirm_email" value="<?php if(!empty($_POST['confirm_email']))  _e(htmlentities($_POST['confirm_email']));?>" size="37" ></td>
		</tr>
		
		<tr><td colspan="2">&nbsp;</td></tr><tr>
		
		<tr>
			<td><?php  _e('Contact No','binary-mlm-pro');?>. <span style="color:red;">*</span> :</td>
			<td><input type="text" name="telephone" id="telephone" value="<?php if(!empty($_POST['telephone']))  _e(htmlentities($_POST['telephone']));?>" maxlength="20" size="37" onBlur="return numeric(this.value, 'telephone');" ></td>
		</tr>
		
		<tr><td colspan="2">&nbsp;</td></tr>
		
		<tr>
			<td><?php  _e('Date of Birth','binary-mlm-pro');?> <span style="color:red;">*</span> :</td>
			<td><input type="text" name="dob" id="dob" value="<?php if(!empty($_POST['dob']))  _e(htmlentities($_POST['dob']));?>" maxlength="20" size="22" ></td>
		</tr>
		
		<tr><td colspan="2">&nbsp;</td></tr>
		
		<tr>
			<td colspan="2"><input type="submit" name="submit" id="submit" value="<?php _e('Submit','binary-mlm-pro')?>" /></td>
		</tr>
	</form>
</table>
<?php
	}
	else
		 _e($msg);
}//function end

?>
