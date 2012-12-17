<?php
function mlmPayout()
{
	//get database table prefix
	$table_prefix = mlm_core_get_table_prefix();
	
	$error = '';
	$chk = 'error';
	//most outer if condition
	if(isset($_POST['mlm_payout_settings']))
	{
		$pair1 = sanitize_text_field( $_POST['pair1'] );
		$pair2 = sanitize_text_field( $_POST['pair2'] );
		$initial_pair = sanitize_text_field( $_POST['initial_pair'] );
		$initial_amount = sanitize_text_field( $_POST['initial_amount'] );
		$further_amount = sanitize_text_field( $_POST['further_amount'] );
		
		if ( checkPair($pair1, $pair2) ) 
			$error .= "\n Your pair ratio is wrong.";
			
		if ( checkInputField($initial_pair) ) 
			$error .= "\n Your initial pair value is wrong.";
			
		if ( checkInputField($initial_amount) ) 
			$error .= "\n Your initial amount value is wrong.";
		
		if ( checkInitial($further_amount) ) 
			$error .= "\n Your further amount value is wrong.";
			
		//if any error occoured
		if(!empty($error))
			$error = nl2br($error);
		else
		{
			$chk = '';
			update_option('wp_mlm_payout_settings', $_POST);
			$url = get_bloginfo('url')."/wp-admin/admin.php?page=admin-settings&tab=bonus";
			echo "<script>window.location='$url'</script>";
			$msg = "<span style='color:green;'>Your payout settings has been successfully updated.</span>";
		}
	}// end outer if condition
	if($chk!='')
	{
		$mlm_settings = get_option('wp_mlm_payout_settings');
		?>
<p>&nbsp;</p>

<div class="helpmessage">
<p>Use this screen to define the basic parameters of your pay plan.</p>
<p><strong>Pair - </strong>How many paid members in the left and right leg individually will make 1 pair for calculating commissions.</p>

<p><strong>Initial Pairs</strong> - To incentivise members in the initial stages the amount paid for initial pairs is slightly higher than the regular payout amount. Specify the number of initial pairs for which you would like to pay a higher payout amount.</p>

<p><strong>Initial Pair Amount - </strong>This is the per pair amount that is paid for the each Initial Pair.</p>

<p><strong>Further Pair Amount -</strong> This is the payout amount for every Pair after the Initial Pairs.</p>

<p><strong>Service Charges -</strong> An amount that is deducted from each Payout paid to the member as a fixed Service Charge. eg. $2 as processing fee for each payout.</p>

<p><strong>Tax Deduction- </strong>Some countries have a legislation of deducting Income Tax at source while making commission payments to your members. In case there is a tax deduction required in your country you can specify the tax % here.</p>

</div>

<div class="forms-ui">

<p><span style='color:red;'><?=$error?></span></p>
<?php
	if(empty($mlm_settings))
	{
?>
		<form name="admin_payout_settings" method="post" action="">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="form-table">
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('admin-mlm-pair');"><?php _e('Pair <span style="color:red;">*</span>:');?> </a>
			</th>
			<td>
				<input type="text" name="pair1" id="pair1" size="2" value="<?= htmlentities($_POST['pair1']);?>"> : 
				<input type="text" name="pair2" id="pair2" size="2" value="<?= htmlentities($_POST['pair2']);?>">
				<div class="toggle-visibility" id="admin-mlm-pair"><?php _e('Please mention here pair ratio.');?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-initial-pair');">
				<?php _e('Initial Pairs <span style="color:red;">*</span>:');?> </a>
			</th>
			<td>
				<input type="text" name="initial_pair" id="initial_pair" size="2" value="<?= htmlentities($_POST['initial_pair']);?>">
				<div class="toggle-visibility" id="admin-mlm-initial-pair"><?php _e('Please mention here initial pair.');?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
			<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-initial-amount');">
			<?php _e('Initial Pair Amount <span style="color:red;">*</span>:');?> </a>
			</th>
			<td>
			<input type="text" name="initial_amount" id="initial_amount" size="10" value="<?= htmlentities($_POST['initial_amount']);?>">
				<div class="toggle-visibility" id="admin-mlm-initial-amount"><?php _e('Please mention here initial amount.');?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-further-amount');">
				<?php _e('Further Pair Amount <span style="color:red;">*</span>:');?> </a>
			</th>
			<td>
			<input type="text" name="further_amount" id="further_amount" size="10" value="<?= htmlentities($_POST['further_amount']);?>">
				<div class="toggle-visibility" id="admin-mlm-further-amount"><?php _e('Please mention here further pair amount.')?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-service-charege');">
				<?php _e('Service Charge (If any):');?> </a>
			</th>
			<td>
			<input type="text" name="service_charge" id="service_charge" size="10" value="<?= htmlentities($_POST['service_charge']);?>">
				<div class="toggle-visibility" id="admin-mlm-service-charege"><?php _e('Please specify service charge.')?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-tds');">
				<?php _e('Tax Deduction :');?> </a>
			</th>
			<td>
			<input type="text" name="tds" id="tds" size="10" value="<?= htmlentities($_POST['tds']);?>">&nbsp;%
				<div class="toggle-visibility" id="admin-mlm-tds"><?php _e('Please specify TDS.')?></div>
			</td>
		</tr>
		</table>
		<p class="submit">
	<input type="submit" name="mlm_payout_settings" id="mlm_payout_settings" value="<?php _e('Update Options', 'mlm')?> &raquo;" class='button-primary' onclick="needToConfirm = false;">
	</p>
	</form>

	<?php
		}
		else if(!empty($mlm_settings))
		{
			?>
			<form name="admin_payout_settings" method="post" action="">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="form-table">
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('admin-mlm-pair');"><?php _e('Pair <span style="color:red;">*</span>:')?> </a>
			</th>
			<td>
				<input type="text" name="pair1" id="pair1" size="2" value="<?= $mlm_settings['pair1'];?>"> : 
				<input type="text" name="pair2" id="pair2" size="2" value="<?= $mlm_settings['pair2'];?>">
				<div class="toggle-visibility" id="admin-mlm-pair"><?php _e('Please mention here pair ratio.')?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-initial-pair');">
				<?php _e('Initial Pair <span style="color:red;">*</span>:')?> </a>
			</th>
			<td>
				<input type="text" name="initial_pair" id="initial_pair" size="2" value="<?= $mlm_settings['initial_pair'];?>">
				<div class="toggle-visibility" id="admin-mlm-initial-pair"><?php _e('Please mention here initial pair.')?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
			<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-initial-amount');">
			<?php _e('Initial Amount <span style="color:red;">*</span>:')?> </a>
			</th>
			<td>
			<input type="text" name="initial_amount" id="initial_amount" size="10" value="<?= $mlm_settings['initial_amount'];?>">
				<div class="toggle-visibility" id="admin-mlm-initial-amount"><?php _e('Please mention here initial amount.')?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-further-amount');">
				<?php _e('Further Pair Amount <span style="color:red;">*</span>:')?> </a>
			</th>
			<td>
			<input type="text" name="further_amount" id="further_amount" size="10" value="<?= $mlm_settings['further_amount'];?>">
				<div class="toggle-visibility" id="admin-mlm-further-amount"><?php _e('Please mention here further pair amount.')?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-service-charege');">
				<?php _e('Service Charge (If any):');?> </a>
			</th>
			<td>
			<input type="text" name="service_charge" id="service_charge" size="10" value="<?= $mlm_settings['service_charge'];?>">
				<div class="toggle-visibility" id="admin-mlm-service-charege"><?php _e('Please specify service charge.')?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-tds');">
				<?php _e('TDS:');?> </a>
			</th>
			<td>
			<input type="text" name="tds" id="tds" size="10" value="<?= $mlm_settings['tds'];?>">&nbsp;%
				<div class="toggle-visibility" id="admin-mlm-tds"><?php _e('Please specify TDS.')?></div>
			</td>
		</tr>
		</table>
		<p class="submit">
	<input type="submit" name="mlm_payout_settings" id="mlm_payout_settings" value="<?php _e('Update Options', 'mlm')?> &raquo;" class='button-primary' onclick="needToConfirm = false;">
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
		
		
} //end mlmPayout function
?>
