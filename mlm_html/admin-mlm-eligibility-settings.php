<?php
function mlmEligibility()
{
	//get database table prefix
	$table_prefix = mlm_core_get_table_prefix();
	
	$error = '';
	$chk = 'error';
	
	//most outer if condition
	if(isset($_POST['mlm_eligibility_settings']))
	{
		$direct_referral = sanitize_text_field( $_POST['direct_referral'] );
		$right_referral = sanitize_text_field( $_POST['right_referral'] );
		$left_referral = sanitize_text_field( $_POST['left_referral'] );
		
		if ( checkInputField($direct_referral) ) 
			$error .= "\n Please specify your direct active referrals.";
		if ( checkInputField($right_referral) ) 
			$error .= "\n Please specify your right leg active referrals.";
		if ( checkInputField($left_referral) ) 
			$error .= "\n Please specify your left leg active referrals.";
		//if any error occoured
		if(!empty($error))
			$error = nl2br($error);
		else
		{
			$chk = '';
			update_option('wp_mlm_eligibility_settings', $_POST);
			$url = get_bloginfo('url')."/wp-admin/admin.php?page=admin-settings&tab=payout";
			echo "<script>window.location='$url'</script>";
			$msg = "<span style='color:green;'>Your eligibility settings has been successfully updated.</span>";
		}
	}// end outer if condition
	if($chk!='')
	{
		$mlm_settings = get_option('wp_mlm_eligibility_settings');
		?>
		
			
		<div class="helpmessage">
	<p>Use this screen to define the eligibility criteria for a member to start earning commissions in the network.</p>
	<p><strong>No. of Direct Paid Referrals -</strong> The number of members that a member will need to directly and personally refer in the network before he can start earning commissions.</p>
	
	<p><strong>No. of paid referral(s) on right leg -</strong> The number of paid direct and personal referrals a member needs to introduce in this right leg before he can start earning commissions.</p>
	
	
	<p><strong>No. of paid referral(s) on left leg -</strong> The number of paid direct and personal referrals a member needs to introduce in this left leg before he can start earning commissions.</p>
	
	</div>
	
		<p>&nbsp;</p>
		
<?php
		if(empty($mlm_settings))
		{
?>
	
	<div class="forms-ui">
	<p><span style='color:red;'><?=$error?></span></p>
	<form name="admin_eligibility_settings" method="post" action="">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="form-table">
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('mlm_direct_referral');">
				<?php _e('No. of direct paid referral(s) <span style="color:red;">*</span>:');?> </a>
			</th>
			<td>
		<input type="text" name="direct_referral" id="direct_referral" size="10" value="<?= htmlentities($_POST['direct_referral']);?>">
				<div class="toggle-visibility" id="mlm_direct_referral"><?php _e('Please specify direct referral by you.')?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('mlm_right_referral');">
				<?php _e('No. of paid referral(s) on right leg <span style="color:red;">*</span>:');?> </a>
			</th>
			<td>
		<input type="text" name="right_referral" id="right_referral" size="10" value="<?= htmlentities($_POST['right_referral']);?>">
				<div class="toggle-visibility" id="mlm_right_referral"><?php _e('Please specify no. of paid referrals on right leg.')?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('mlm_left_referral');">
				<?php _e('No. of paid referral(s) on left leg <span style="color:red;">*</span>:');?> </a>
			</th>
			<td>
		<input type="text" name="left_referral" id="left_referral" size="10" value="<?= htmlentities($_POST['left_referral']);?>">
				<div class="toggle-visibility" id="mlm_left_referral"><?php _e('Please specify no. of paid referrals on left leg.')?></div>
			</td>
		</tr>
		</table>
		<p class="submit">
	<input type="submit" name="mlm_eligibility_settings" id="mlm_eligibility_settings" value="<?php _e('Update Options', 'mlm')?> &raquo;" class='button-primary' onclick="needToConfirm = false;">
	</p>
</form>
</div>
<?php
		}
		else if(!empty($mlm_settings))
		{
			?>
			<div class="forms-ui">
			<form name="admin_eligibility_settings" method="post" action="">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="form-table">
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('mlm_direct_referral');">
				<?php _e('No. of direct paid referral(s) <span style="color:red;">*</span>:');?> </a>
			</th>
			<td>
		<input type="text" name="direct_referral" id="direct_referral" size="10" value="<?= $mlm_settings['direct_referral'];?>">
				<div class="toggle-visibility" id="mlm_direct_referral"><?php _e('Please specify direct referral by you.')?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('mlm_right_referral');">
				<?php _e('No. of paid referral(s) on right leg <span style="color:red;">*</span>:');?> </a>
			</th>
			<td>
		<input type="text" name="right_referral" id="right_referral" size="10" value="<?= $mlm_settings['right_referral'];?>">
				<div class="toggle-visibility" id="mlm_right_referral"><?php _e('Please specify no. of paid referrals on right leg.')?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('mlm_left_referral');">
				<?php _e('No. of paid referral(s) on left leg <span style="color:red;">*</span>:');?> </a>
			</th>
			<td>
		<input type="text" name="left_referral" id="left_referral" size="10" value="<?= $mlm_settings['left_referral'];?>">
				<div class="toggle-visibility" id="mlm_left_referral"><?php _e('Please specify no. of paid referrals on left leg.')?></div>
			</td>
		</tr>
		</table>
		<p class="submit">
	<input type="submit" name="mlm_eligibility_settings" id="mlm_eligibility_settings" value="<?php _e('Update Options', 'mlm')?> &raquo;" class='button-primary' onclick="needToConfirm = false;" >
	</p>
</form>

<script language="JavaScript">
  populateArrays();
</script>
<?php
		}
		
	?>
	</div>
	<?php 	
	} // end if statement
	else
		echo $msg;

} //end mlmEligibility funtion
?>