<?php 
function mlm_my_payout_details_page()
{

$detailArr = my_payout_details_function();


	if(count($detailArr)>0)
	{

		//echo "<pre>";print_r($detailArr); exit; 
		$memberId = $detailArr['memberId']; 
		$payoutId = $detailArr['payoutId'];
		$comissionArr = getCommissionByPayoutId($memberId,$payoutId );
		$bonusArr = getBonusByPayoutId($memberId,$payoutId );
		//echo "<pre>";print_r($bonusArr); exit; 
		
		$mlm_settings = get_option('wp_mlm_general_settings');
		?>
		
		<table width="100%" border="0" cellspacing="10" cellpadding="1">
		  <tr>
			<td width="40%" valign="top">
				<table width="100%" border="0" cellspacing="10" cellpadding="1">
				  <tr>
					<td colspan="2"><strong>Personal Information</strong></td>
				  </tr>
				  <tr>
					<td scope="row">Title</td>
					<td>Details</td>
				  </tr>
				  <tr>
					<td scope="row">Name</td>
					<td><?=$detailArr['name'] ?></td>
				  </tr>
				  <tr>
					<td scope="row">ID</td>
					<td><?=$detailArr['userKey'] ?></td>
				  </tr>
				  <tr>
					<td scope="row">Payout ID</td>
					<td><?=$detailArr['payoutId'] ?></td>
				  </tr>
				  <tr>
					<td scope="row">Date</td>
					<td><?=$detailArr['payoutDate'] ?></td>
				  </tr>
				</table>
			</td>
			<td width="40%">
				<table width="100%" border="0" cellspacing="10" cellpadding="1">
				  <tr>
					<td><strong>Payout Details</strong></td>
				  </tr>
				   <tr>
					<td>
						<table width="100%" border="0" cellspacing="10" cellpadding="1">
							<tr>
								<td colspan="2"><strong>Commission</strong></td>
							</tr>
							
							<tr>
								<td>User Name</td>
								<td>Amount</td>
							</tr>
							<?php foreach($comissionArr as $comm ) :?>
							
							<tr>
								<td><?= $comm['child_ids'] ?></td>
								<td><?= $mlm_settings['currency'].' '.$comm['amount'] ?></td>
							</tr>
							
							<?php endforeach; ?>
							
							
						</table>
					</td>
				  </tr>
				   <?php if(count($bonusArr)>0) : ?>
				   <tr>
					<td>
						<table width="100%" border="0" cellspacing="10" cellpadding="1">
							<tr>
								<td colspan="2"><strong>Bonus</strong></td>
							</tr>
							<?php foreach($bonusArr as $bonus ) :?>
							<tr>
								<td><?= $bonus['bonusDate'] ?> </td>
								<td><?= $mlm_settings['currency'].' '.$bonus['amount'] ?></td>
							</tr>
							<?php endforeach; ?>
							
						</table>
					</td>
				  </tr>
				  <?php endif;?>
				  
				</table>
			</td>
		  </tr>
		</table>
		
		
		<table width="100%" border="0" cellspacing="10" cellpadding="1" class="payout-summary">
			<tr>
				<td colspan="2"><strong>Payout Summary</strong></td>
			</tr>
			<tr>
				<td width="50%">Commission Amount</td>
				<td width="50%" class="right"><?= $mlm_settings['currency'].' '.$detailArr['commamount'] ?></td>
			</tr>
			<tr>
				<td width="50%">Bonus Amount</td>
				<td width="50%" class="right" ><?= $mlm_settings['currency'].' '.$detailArr['bonusamount'] ?></td>
			</tr>
			<tr>
				<td width="50%">Sub-Total</td>
				<td width="50%" class="right"><?=$mlm_settings['currency'].' '.$detailArr['subtotal'] ?></td>
			</tr>
			<tr>
				<td width="50%">Service Charge</td>
				<td width="50%" class="right"><?=$mlm_settings['currency'].' '.$detailArr['servicecharges'] ?></td>
			</tr>
			<tr>
				<td width="50%">Tax	</td>
				<td width="50%" class="right"><?=$mlm_settings['currency'].' '.$detailArr['tax'] ?></td>
			</tr>
			<tr>
				<td width="50%"><strong>Net Amount</strong>	</td>
				<td width="50%" class="right"><strong><?=$mlm_settings['currency'].' '.$detailArr['netamount'] ?></strong></td>
			</tr>
			
		</table>			
			
			
		<?php
	}else{
	
		echo "<div class='notfound'>It Seems some error. Please contact adminisistrator ".get_settings('admin_email')." for this issue.</div>";
	
	}	
		 
}

function my_payout_details_function()
{
	if ( is_user_logged_in() && isset($_REQUEST['pid']))
	{
	
		global $table_prefix;
		global $wpdb;
		global $current_user;
    	get_currentuserinfo();
		
		$userId = $current_user->ID; 	
		
		$sql = "SELECT {$table_prefix}mlm_users.id AS id , {$table_prefix}mlm_users.user_key FROM {$table_prefix}users,{$table_prefix}mlm_users WHERE {$table_prefix}mlm_users.username = {$table_prefix}users.user_login AND {$table_prefix}users.ID = '".$userId."'"; 
		$res = $wpdb->get_results($sql, ARRAY_A); 
		$mlm_user_id = $res[0]['id'];
		$mlm_user_key = $res[0]['user_key'];
	
		
		$sql = 	"SELECT 
					id, user_id, DATE_FORMAT(`date`,'%d %b %Y') as payoutDate, payout_id, commission_amount, 
					bonus_amount, banktransfer_code, cheque_no, 
					cheque_date, bank_name, user_bank_name, user_bank_account_no, 
					tax, service_charge, dispatch_date, courier_name, awb_no 
				FROM 
					{$table_prefix}mlm_payout 
				WHERE 
					payout_id = '".$_REQUEST['pid']."' AND 
					user_id = '".$mlm_user_id."'";
		
		 $rs = mysql_query($sql);
		 $payoutDetail = array(); 
		 
		 if(mysql_num_rows($rs)>0)
		 {
		 	$row = mysql_fetch_array($rs);
	
			$payoutDetail['memberId'] = $mlm_user_id;
			$payoutDetail['name'] = $current_user->user_firstname.' '.$current_user->user_lastname; 
			$payoutDetail['userKey'] = $mlm_user_key;
			
			$payoutDetail['payoutId'] = $_REQUEST['pid']; 
			$payoutDetail['payoutDate'] = $row['payoutDate'];
			
			/*Conmmission*/	
			$payoutDetail['commamount'] = $row['commission_amount'];
			$payoutDetail['bonusamount'] = $row['bonus_amount'];
			$payoutDetail['subtotal'] = $row['commission_amount'] + $row['bonus_amount'];
			$payoutDetail['servicecharges'] = $row['service_charge'];
			$payoutDetail['tax'] = $row['tax'];
			$netPayable =  $row['commission_amount'] + $row['bonus_amount'] -($row['service_charge'] + $row['tax'] );
			$payoutDetail['netamount'] = number_format( $netPayable, 2); 		

		 
		 }
		 
		return $payoutDetail;	 
		 
	}	else{
	
		return null;
	}

}

function getCommissionByPayoutId($memberId,$payoutId )
{
	global $table_prefix;
	global $wpdb;
	if(isset($memberId) && isset($payoutId))
	{
		$sql = "SELECT 
					id, date_notified, parent_id, child_ids, amount, payout_id 
				FROM 
					{$table_prefix}mlm_commission 
				WHERE 
					parent_id = '".$memberId."' AND 
					payout_id = '".$payoutId."' 
				";
				
		$myrows = $wpdb->get_results($sql, ARRAY_A );
			
		return $myrows;
	
	}else
	return null;

}

function getBonusByPayoutId($memberId,$payoutId )
{
	global $table_prefix;
	global $wpdb;
	if(isset($memberId) && isset($payoutId))
	{
		$sql = "SELECT 
					 id, DATE_FORMAT(`date_notified`,'%d %b %Y') as bonusDate, mlm_user_id, amount, payout_id 
				FROM 
					{$table_prefix}mlm_bonus 
				WHERE 
					mlm_user_id = '".$memberId."' AND 
					payout_id = '".$payoutId."' 
				";
				
		$myrows = $wpdb->get_results($sql, ARRAY_A );
			
		return $myrows;
	
	}else
	return null;

}
?>