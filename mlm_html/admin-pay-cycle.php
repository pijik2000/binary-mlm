<?php 
function wpmlm_run_pay_cycle()
{
	
	$returnVar  = wpmlm_run_pay_cycle_functions();	
	return $returnVar; 
	
}

function wpmlm_run_pay_cycle_functions()
{
	

	$payoutMasterId = createPayoutMaster(); 
	
	global $table_prefix, $wpdb; 
	$sql=  "SELECT 
				id, date_notified, parent_id, child_ids, amount, SUM(amount) AS commission  
			FROM 
				{$table_prefix}mlm_commission 
			WHERE 
				payout_id = 0
			GROUP BY 
				parent_id	
			";
	
	//echo $sql; exit; 
	
		
	$rs = $wpdb->get_results($sql); 
	if($wpdb->num_rows > 0)
	{
		foreach($rs as $row)
		{
			
			$userId = $row->parent_id;
			$commissionAmt = $row->commission;
			$bonusAmt = getBonusAmountById($userId); 
			$payout_settings = get_option('wp_mlm_payout_settings');
			
			$tax = $payout_settings['tds'];
			$taxAmt = round(($commissionAmt + $bonusAmt)* $tax/100, 2);
						
			$service_charge = $payout_settings['service_charge'];	
					
			/***********************************************************
			INSERT INTO PAYOUT TABLE
			***********************************************************/ 
			$sql_payout = "INSERT INTO 
							{$table_prefix}mlm_payout
							(
								user_id, date, payout_id, commission_amount, 
								bonus_amount, tax, service_charge
							) 
							VALUES 					
							(
								'".$userId."', '".date('Y-m-d H:i:s')."', '".$payoutMasterId."', '".$commissionAmt."', 
								'".$bonusAmt."', '".$taxAmt."', '".$service_charge."'
							)";
					
			$rs_payout = mysql_query($sql_payout);
			$insert_id = mysql_insert_id();
			
			/***********************************************************
			Update Commission table Payout Id
			***********************************************************/ 
			if(isset($insert_id) && $insert_id >0)
			{
				$sql_comm = "UPDATE {$table_prefix}mlm_commission 
								SET 
									payout_id= '".$payoutMasterId."'
								WHERE 
									parent_id = '".$userId."' AND 
									payout_id = '0'
								";
				$rs_comm = mysql_query($sql_comm); 					
			
			}
			/***********************************************************
			Update Bonus table Payout Id
			***********************************************************/ 
			if(isset($insert_id) && $insert_id >0)
			{
				$sql_bon = "UPDATE {$table_prefix}mlm_bonus 
								SET 
									payout_id= '".$payoutMasterId."'
								WHERE 
									mlm_user_id = '".$userId."' AND 
									payout_id = '0'
								";
				$rs_bon = mysql_query($sql_bon); 					
			
			}
						
		}	
	
	}
	
	return "Payout Run Successfully";
	
}



function createPayoutMaster()
{
	global $table_prefix; 
	$sql = "INSERT INTO {$table_prefix}mlm_payout_master(date) VALUES ('".date('Y-m-d H:i:s')."')"; 
	$res = mysql_query($sql);
	$pay_master_id = mysql_insert_id();
	
	return $pay_master_id; 
}

function getBonusAmountById($userId)
{
	global $table_prefix; 
	$sql = "SELECT 
				amount, SUM(amount) AS bonus, payout_id 
			FROM 
				wp_mlm_bonus 
			WHERE 
				mlm_user_id ='".$userId."' 
			GROUP BY 
				mlm_user_id 			
		";
	
	$rs = mysql_query($sql);
	
	if(mysql_num_rows($rs)>0)
	{
		$row = mysql_fetch_array($rs); 
		
		$bonus = $row['bonus']; 
		
	}	
	
	return $bonus;
 
}



?>