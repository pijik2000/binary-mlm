<?php
function mlmGeneral()
{
	global $wpdb;	
	//get database table prefix
	$table_prefix = mlm_core_get_table_prefix();
	
	$error = '';
	$chk = 'error';
	
	//most outer if condition
	if(isset($_POST['mlm_general_settings']))
	{
		$currency = sanitize_text_field( $_POST['currency'] );

		if ( checkInputField($currency) ) 
			$error .= "\n Please Select your currency type.";
		
		//if any error occoured
		if(!empty($error))
			$error = nl2br($error);
		else
		{
			$chk = '';
			update_option('wp_mlm_general_settings', $_POST);
			$url = get_bloginfo('url')."/wp-admin/admin.php?page=admin-settings&tab=eligibility";
			echo "<script>window.location='$url'</script>";
			$msg = "<span style='color:green;'>Your general settings has been successfully updated.</span>";
		}
	}// end outer if condition
	
	if($chk!='')
	{
		$mlm_settings = get_option('wp_mlm_general_settings');
?>
		<p>&nbsp;</p>

		<div class="helpmessage">Please select the base currency of your MLM Network. This option is very important as all calculations will be performed in this base currency. Once this currency is chosen and saved, it CANNOT be changed later. The entire network will need to be reset if you decide to change the currency at a later date.</div>
		
		<div class="forms-ui">
		<p><span style='color:red;'><?=$error?></span></p>
<?php
		if(empty($mlm_settings))
		{
?>
	<form name="admin_general_settings" method="post" action="">
	<table border="0" cellpadding="0" cellspacing="0" width="60%" class="form-table">
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('admin-mlm-currency');"><?php _e('Currency <span style="color:red;">*</span>:');?> </a>
			</th>
			<td>
			<?php
				$sql = "SELECT iso3, currency 
											FROM {$table_prefix}mlm_currency 
											ORDER BY iso3";
				$results = $wpdb->get_results($sql);
			?>
				<select name="currency" id="currency" >
					<option value="">Select Currency</option>
				<?php
					
					foreach($results as $row)
					{
						if($_POST['currency']==$row->iso3)
							$selected = 'selected';
						else
							$selected = '';
				?>
						<option value="<?= $row->iso3;?>" <?= $selected?>><?= $row->iso3." - ".$row->currency;?></option>
				<?php
					}
				?>
				</select>
				<div class="toggle-visibility" id="admin-mlm-currency"><?php _e('Select your currency which will you use.');?></div>
			</td>
		</tr>
	</table>
	<p class="submit">
	<input type="submit" name="mlm_general_settings" id="mlm_general_settings" value="<?php _e('Update Options', 'mlm')?> &raquo;" class='button-primary' onclick="needToConfirm = false;">
	</p>
	</form>
	</div>
	<script language="JavaScript">
  populateArrays();
</script>
<?php
		}
		else if(!empty($mlm_settings))
		{
		?>
			<form name="admin_general_settings" method="post" action="">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="form-table">
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('admin-mlm-currency');"><?php _e('Currency <span style="color:red;">*</span>:')?> </a>
			</th>
			<td>
			<?php
				$sql = "SELECT iso3, currency 
						FROM {$table_prefix}mlm_currency
						WHERE iso3 = '".$mlm_settings['currency']."'
						ORDER BY iso3";
				//$sql = mysql_fetch_array(mysql_query($sql));
			?>
				<input type="text" name="currency" id="currency" value="<?= $mlm_settings['currency']?>" readonly />
				<div class="toggle-visibility" id="admin-mlm-currency"><?php _e('You can not change the currency.')?></div>
			</td>
		</tr>
		</table>
		<p class="submit">
	<input type="submit" name="mlm_general_settings" id="mlm_general_settings" value="<?php _e('Update Options', 'mlm')?> &raquo;" class='button-primary' onclick="needToConfirm = false;">
	</p>
	</form>
	</div>
		<?php
		}
	} // end if statement
	else
		echo $msg;
} //end mlmGeneral function
?>