<?php
function mlmBonus()
{
	//get database table prefix
	$table_prefix = mlm_core_get_table_prefix();
	
	$error = '';
	$chk = 'error';
	
	//most outer if condition
	if(isset($_POST['mlm_bonus_settings']))
	{
		$bonus_criteria = sanitize_text_field( $_POST['bonus_criteria'] );
		$pair =  $_POST['pair'];
		$amount = $_POST['amount'];
		
		if ( checkInputField($bonus_criteria) ) 
			$error .= "\n Please Select bonus criteria.";
		
		for($i = 0; $i<count($pair); $i++)
		{
			if($pair[$i]=="" || $amount[$i] == "")
				$error .= "\n Your bonus slab data is wrong.";
		}
		
		//if any error occoured
		if(!empty($error))
			$error = nl2br($error);
		else
		{
			$chk = '';
			update_option('wp_mlm_bonus_settings', $_POST);
			$url = get_bloginfo('url')."/wp-admin/admin.php?page=admin-settings&tab=general";
			echo "<script>window.location='$url'</script>";
			$msg = "<span style='color:green;'>Your bonus has been successfully updated.</span>";
		}
	}// end outer if condition
	if($chk!='')
	{
		$mlm_settings = get_option('wp_mlm_bonus_settings');
?>
<p>&nbsp;</p>
<div class="helpmessage">
<p>In case you have a bonus option in your Network, use this tab to configure the bonus settings.</p>
<p><strong>Bonus Criteria - </strong>The bonus amount can be paid on the basis of Total number of Pairs in a members network or the Total number of members referred Personally by a member. Select the option that suits your network.</p>
<p><strong>Bonus Slabs -</strong> Specify the total number of pairs or members that a member needs to achieve and the corresponding bonus amount for the same. To add a new slab click the Add Row button. Specify the number of pairs / members and the corresponding amount for the next slab. When you are done creating the slabs click the Update Options button.</p>

<p><table width="200" border="0" cellspacing="10" cellpadding="1">
  <tr>
    <td colspan="2"><strong>Example Slab Figures</strong></td>
  </tr>
  <tr>
    <td>5</td>
    <td>10</td>
  </tr>
  <tr>
    <td>10</td>
    <td>20</td>
  </tr>
  <tr>
    <td>20</td>
    <td>40</td>
  </tr>  </p>
</table>

<p>This implies that a member is paid a commission of 10 on achieving 5 pairs or personal referrals.</p>
<p>On achieving the NEXT 10 pairs or personal referrals the member is paid a commission of 20. So now the member has either 15 pairs or 15 personal referrals in total.</p>
<p>On achieving the NEXT 20 pairs or personal referrals the member is paid a commission of 40. So now the member has either 35 pairs or 35 personal referrals in total.</p>


</div>
<div class="forms-ui">
<p><span style='color:red;'><?=$error?></span></p>
<?php
	if(empty($mlm_settings))
	{
?>
<form name="admin_bonus_settings" method="post" action="">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="form-table">
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-bonus');">
				<?php _e('Bonus Criteria <span style="color:red;">*</span>:');?> </a>
			</th>
			<td>
				<select name="bonus_criteria" id="bonus_criteria">
				<option value="">Select Bonus Criteria</option>
				<option value="pair" <?= $_POST['bonus_criteria']=='pair' ? 'selected':''?>>No. of Pairs</option>
				<option value="personal" <?= $_POST['bonus_criteria']=='personal' ? 'selected':''?>>No. of Personal Referrer</option>
				</select>
				<div class="toggle-visibility" id="admin-mlm-bonus"><?php _e('Please select bonus type.')?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-bonus-slab');">
				<?php _e('Bonus Slab <span style="color:red;">*</span>:')?></a>
			</th>
			<td>
				<INPUT type="button" value="Add Row" onclick="addRow('dataTable')" class='button-primary' />
    			<INPUT type="button" value="Delete Row" onclick="deleteRow('dataTable')" class='button-primary' />
				<div class="toggle-visibility" id="admin-mlm-bonus-slab"><?php _e('Add or remove bonus slab.')?></div>
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
	</table>
	<TABLE id="dataTableheading" cellspacing="5" cellpadding="5"  border="0" width="300">
		<TR>
			<TD align="center" width="20%"><strong>Select</strong></TD>
			<TD align="center" width="40%"> <strong>No. of Pairs</strong></TD>
			<TD align="center" width="40%"><strong>Amount</strong></TD>
		</TR>
	</TABLE>
	<br\>
	<TABLE id="dataTable"  cellspacing="0" cellpadding="0" border="0" width="300">
		<TR>
			<TD align="center" width="20%"><INPUT type="checkbox" name="chk[]"/></TD>
			<TD align="center" width="40%"> <INPUT type="text" name="pair[]" size="15" /> </TD>
			<TD align="center" width="40%"> <INPUT type="text" name="amount[]" size="15" /> </TD>
		</TR>
	</TABLE>
	
	<table border="0" width="100%">	
		<tr>
			<td>
		<p class="submit">
	<input type="submit" name="mlm_bonus_settings" id="mlm_bonus_settings" value="<?php _e('Update Options', 'mlm')?> &raquo;" class='button-primary' onclick="needToConfirm = false;">
	</p>
			</td>
		<tr>
	</table>
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
		
			<form name="admin_bouns_settings" method="post" action="">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="form-table">
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-bonus');">
				<?php _e('Bonus Criteria <span style="color:red;">*</span>:')?> </a>
			</th>
			<td>
				<select name="bonus_criteria" id="bonus_criteria">
				<option value="">Select Bonus Criteria</option>
				<option value="pair" <?= $mlm_settings['bonus_criteria']=='pair' ? 'selected':''?>>No. of Pairs</option>
			<option value="personal" <?= $mlm_settings['bonus_criteria']=='personal' ? 'selected':''?>>No. of Personal Referrer</option>
				</select>
				<div class="toggle-visibility" id="admin-mlm-bonus"><?php _e('Please select bonus type.')?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-bonus-slab');">
				<?php _e('Bonus Slab <span style="color:red;">*</span>:')?> </a>
			</th>
			<td>
				<INPUT type="button" value="Add Row" onclick="addRow('dataTable')" class='button-primary'/>
    			<INPUT type="button" value="Delete Row" onclick="deleteRow('dataTable')" class='button-primary'/>
				<div class="toggle-visibility" id="admin-mlm-bonus-slab"><?php _e('Add or remove bonus slab.')?></div>
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
	</table>
		<TABLE id="dataTable"  border="0" align="center">
		<?php
			$i = 0;
			while( $i<count($mlm_settings['pair']) )
			{
		?>
        	<TR>
           		<TD><INPUT type="checkbox" name="chk[]"/></TD>
				<TD> <INPUT type="text" name="pair[]" size="15" value="<?= $mlm_settings['pair'][$i]?>"/> </TD>
				<TD> <INPUT type="text" name="amount[]" size="15" value="<?= $mlm_settings['amount'][$i]?>"/> </TD>
        	</TR>    	
		<?php
				$i++;
			}
		?>
		</TABLE>
	
	<p class="submit">
	<input type="submit" name="mlm_bonus_settings" id="mlm_bonus_settings" value="<?php _e('Update Options', 'mlm')?> &raquo;" class='button-primary' onclick="needToConfirm = false;">
	</p>
</form>
</div>
<script language="JavaScript">
  populateArrays();
</script>
			<?php
		}
		
	} // end if statement
	else
		echo $msg;
} //end mlmBonus function
?>