<?php
//Take user's key and return user's ID
function getuseridbykey($key)
{
	$table_prefix = mlm_core_get_table_prefix();
	$query = mysql_fetch_array(
								mysql_query("
												SELECT id 
												FROM {$table_prefix}mlm_users 
												WHERE `user_key` = '".$key."'
											")
								);
	return $query['id'];
}

//generate random key
function generateKey()
{
    /// Random characters
	$characters = array("0","1","2","3","4","5","6","7","8","9");

	// set the array
	$keys = array();

	// set length
	$length = 9;

	// loop to generate random keys and assign to an array
	while(count($keys) < $length) 
	{
		$x = mt_rand(0, count($characters)-1);
		if(!in_array($x, $keys)) 
       		$keys[] = $x;
	}

	// extract each key from array
	$random_chars='';
	foreach($keys as $key)
   		$random_chars .= $characters[$key];

	// display random key
	return $random_chars;
}

//return the logeed user's fa_user key
function get_current_user_key()
{
	$table_prefix = mlm_core_get_table_prefix();
	
	global $current_user;
	get_currentuserinfo();
	$username = $current_user->user_login;
	$row = mysql_fetch_array(
								mysql_query("
												SELECT user_key 
												FROM {$table_prefix}mlm_users 
												WHERE username = '".$username."'
											")
							);
	return $row['user_key'];
}

//return the logeed user's fa_user id
function getUserIdByUsername()
{
	$table_prefix = mlm_core_get_table_prefix();
	
	global $current_user;
	get_currentuserinfo();
	$username = $current_user->user_login;
	$row = mysql_fetch_array(
								mysql_query("
												SELECT id 
												FROM {$table_prefix}mlm_users 
												WHERE username = '".$username."'
											")
								);
	return $row['id'];
}

function checkKey($key)
{
	$table_prefix = mlm_core_get_table_prefix();
	$query = mysql_query("
							SELECT user_key 
						 	FROM {$table_prefix}mlm_users 
						  	WHERE `user_key` = '".$key."' 
						  	AND banned = '0'
						");
	if(mysql_num_rows($query)<1)
	{
		return false;
	}
	return true;
}

function checkallowed($key,$leg=NULL)
{
	$table_prefix = mlm_core_get_table_prefix();
	$query = mysql_query("
							SELECT username 
						  	FROM {$table_prefix}mlm_users 
						 	WHERE leg = '".$leg."' 
						  	AND parent_key = '".$key."'
						");
	$num = mysql_num_rows($query);
	return $num;
}

function totalLeftLegUsers($pkey)
{
	$table_prefix = mlm_core_get_table_prefix();
	$row = mysql_fetch_array(
								mysql_query("
												SELECT COUNT(*) AS num
												FROM {$table_prefix}mlm_leftleg
												WHERE pkey = '".$pkey."'
											")
							);
	return $row['num'];
}

function totalRightLegUsers($pkey)
{
	$table_prefix = mlm_core_get_table_prefix();
	$row = mysql_fetch_array(
								mysql_query("
												SELECT COUNT(*) AS num
												FROM {$table_prefix}mlm_rightleg
												WHERE pkey = '".$pkey."'
											")
							);
	return $row['num'];
}

function activeUsersOnLeftLeg($pkey)
{
	$table_prefix = mlm_core_get_table_prefix();

	$row = mysql_fetch_array(
								mysql_query("
												SELECT COUNT(*) AS num
												FROM {$table_prefix}mlm_users
												WHERE payment_status = '1'
												AND user_key IN
												(
													SELECT ukey 
													FROM {$table_prefix}mlm_leftleg
													WHERE pkey = '".$pkey."'
												)
											")
							);
	return $row['num'];
}

function activeUsersOnRightLeg($pkey)
{
	$table_prefix = mlm_core_get_table_prefix();

	$row = mysql_fetch_array(
								mysql_query("
												SELECT COUNT(*) AS num
												FROM {$table_prefix}mlm_users
												WHERE payment_status = '1'
												AND user_key IN
												(
													SELECT ukey 
													FROM {$table_prefix}mlm_rightleg
													WHERE pkey = '".$pkey."'
												)
											")
							);
	return $row['num'];
}

function totalMyPersonalSales($sponsor)
{
	$table_prefix = mlm_core_get_table_prefix();

	$row = mysql_fetch_array(
								mysql_query("
												SELECT COUNT(*) AS num
												FROM {$table_prefix}mlm_users
												WHERE sponsor_key = '".$sponsor."'
											")
							);
	return $row['num'];
}

function activeUsersOnPersonalSales($sponsor)
{
	$table_prefix = mlm_core_get_table_prefix();
	$row = mysql_fetch_array(
								mysql_query("
												SELECT COUNT(*) AS num
												FROM {$table_prefix}mlm_users
												WHERE sponsor_key = '".$sponsor."'
												AND payment_status = '1'
											")
							);
	return $row['num'];
}

function activeNotActive($status)
{
	if($status == '1')
		return 'Active';
	else
		return 'Not Active';
}

function myFiveLeftLegUsers($pkey)
{
	$table_prefix = mlm_core_get_table_prefix();
	$sql = "SELECT username, payment_status
			FROM {$table_prefix}mlm_users
			WHERE user_key IN
			(
				SELECT ukey 
				FROM {$table_prefix}mlm_leftleg
				WHERE pkey = '".$pkey."'
				ORDER BY id DESC
			)
			ORDER BY id DESC
			LIMIT 0,5";
	$sql = mysql_query($sql);
    $i = 0;
	if(mysql_num_rows($sql)>0)
	{
		while($row = mysql_fetch_array($sql))
		{
			$users[$i]['username'] = $row['username'];
			$users[$i]['payment_status'] = activeNotActive($row['payment_status']);
			$i++;
		}
	}
	else
	{
		$users[$i]['username'] = 'No Member Found';
		$users[$i]['payment_status'] = '';
	}
	return $users;
}


function myFiveRightLegUsers($pkey)
{
	$table_prefix = mlm_core_get_table_prefix();
	$sql = "SELECT username, payment_status
			FROM {$table_prefix}mlm_users
			WHERE user_key IN
			(
				SELECT ukey 
				FROM {$table_prefix}mlm_rightleg
				WHERE pkey = '".$pkey."'
				ORDER BY id DESC
			)
			ORDER BY id DESC
			LIMIT 0,5";
	$sql = mysql_query($sql);
    $i = 0;
	if(mysql_num_rows($sql)>0)
	{
		while($row = mysql_fetch_array($sql))
		{
			$users[$i]['username'] = $row['username'];
			$users[$i]['payment_status'] = activeNotActive($row['payment_status']);
			$i++;
		}
	}
	else
	{
		$users[$i]['username'] = 'No Member Found';
		$users[$i]['payment_status'] = '';
	}
	return $users;
}

function myFivePersonalUsers($pkey)
{
	$table_prefix = mlm_core_get_table_prefix();
	$sql = "SELECT username, payment_status
			FROM {$table_prefix}mlm_users
			WHERE sponsor_key = '".$pkey."'
			ORDER BY id DESC
			LIMIT 0,5";
	$sql = mysql_query($sql);
    $i = 0;
	if(mysql_num_rows($sql)>0)
	{
		while($row = mysql_fetch_array($sql))
		{
			$users[$i]['username'] = $row['username'];
			$users[$i]['payment_status'] = activeNotActive($row['payment_status']);
			$i++;
		}
	}
	else
	{
		$users[$i]['username'] = 'No Member Found';
		$users[$i]['payment_status'] = '';
	}
	return $users;
}

function getSponsorName($key)
{
	$table_prefix = mlm_core_get_table_prefix();
	$sql = "SELECT username
			FROM {$table_prefix}mlm_users
			WHERE user_key = '".$key."'";
	$sql = mysql_query($sql);
	$row = mysql_fetch_array($sql);
	return $row['username'];
}

function myTotalLeftLegUsers($pkey)
{
	$table_prefix = mlm_core_get_table_prefix();
	$sql = "SELECT username, payment_status, user_key, sponsor_key
			FROM {$table_prefix}mlm_users
			WHERE user_key IN
			(
				SELECT ukey 
				FROM {$table_prefix}mlm_leftleg
				WHERE pkey = '".$pkey."'
				ORDER BY id DESC
			)
			ORDER BY id DESC";
	$sql = mysql_query($sql);
    $i = 0;
	if(mysql_num_rows($sql)>0)
	{
		while($row = mysql_fetch_array($sql))
		{
			$users[$i]['username'] = $row['username'];
			$users[$i]['user_key'] = $row['user_key'];
			$users[$i]['sponsor_key'] = getSponsorName($row['sponsor_key']);
			$users[$i]['payment_status'] = activeNotActive($row['payment_status']);
			$i++;
		}
	}
	else
	{
		$users[$i]['username'] = 'No Member Found';
		$users[$i]['payment_status'] = '';
	}
	return $users;
}

function myTotalRightLegUsers($pkey)
{
	$table_prefix = mlm_core_get_table_prefix();
	$sql = "SELECT username, payment_status, user_key, sponsor_key
			FROM {$table_prefix}mlm_users
			WHERE user_key IN
			(
				SELECT ukey 
				FROM {$table_prefix}mlm_rightleg
				WHERE pkey = '".$pkey."'
				ORDER BY id DESC
			)
			ORDER BY id DESC";
	$sql = mysql_query($sql);
    $i = 0;
	if(mysql_num_rows($sql)>0)
	{
		while($row = mysql_fetch_array($sql))
		{
			$users[$i]['username'] = $row['username'];
			$users[$i]['user_key'] = $row['user_key'];
			$users[$i]['sponsor_key'] = getSponsorName($row['sponsor_key']);
			$users[$i]['payment_status'] = activeNotActive($row['payment_status']);
			$i++;
		}
	}
	else
	{
		$users[$i]['username'] = 'No Member Found';
		$users[$i]['payment_status'] = '';
	}
	return $users;
}

function myTotalPersonalUsers($pkey)
{
	$table_prefix = mlm_core_get_table_prefix();
	$sql = "SELECT username, payment_status, user_key
			FROM {$table_prefix}mlm_users
			WHERE sponsor_key = '".$pkey."'
			ORDER BY id DESC";
	$sql = mysql_query($sql);
    $i = 0;
	if(mysql_num_rows($sql)>0)
	{
		while($row = mysql_fetch_array($sql))
		{
			$users[$i]['username'] = $row['username'];
			$users[$i]['user_key'] = $row['user_key'];
			$users[$i]['payment_status'] = activeNotActive($row['payment_status']);
			$i++;
		}
	}
	else
	{
		$users[$i]['username'] = 'No Member Found';
		$users[$i]['payment_status'] = '';
	}
	return $users;
}

function legPlacement($leg)
{
	if($leg == 0)
		return 'Left';
	else
		return 'Right';
}

function totalSales($pkey)
{
	$table_prefix = mlm_core_get_table_prefix();
	$sql = "SELECT username, payment_status, user_key, sponsor_key, leg
			FROM {$table_prefix}mlm_users
			WHERE user_key IN
			(
				SELECT ukey 
				FROM {$table_prefix}mlm_rightleg
				WHERE pkey = '".$pkey."'
				ORDER BY id DESC
			)
			ORDER BY id DESC";
	$sql = mysql_query($sql);
    $i = 0;
	if(mysql_num_rows($sql)>0)
	{
		while($row = mysql_fetch_array($sql))
		{
			$rightUsers[$i]['username'] = $row['username'];
			$rightUsers[$i]['user_key'] = $row['user_key'];
			$rightUsers[$i]['sponsor_key'] = getSponsorName($row['sponsor_key']);
			$rightUsers[$i]['leg'] = legPlacement('1');
			$rightUsers[$i]['payment_status'] = activeNotActive($row['payment_status']);
			$i++;
		}
	}
	else
	{
		$rightUsers[$i]['username'] = 'No Member Found';
		$rightUsers[$i]['payment_status'] = '';
	}
	
	$sql = "SELECT username, payment_status, user_key, sponsor_key, leg
			FROM {$table_prefix}mlm_users
			WHERE user_key IN
			(
				SELECT ukey 
				FROM {$table_prefix}mlm_leftleg
				WHERE pkey = '".$pkey."'
				ORDER BY id DESC
			)
			ORDER BY id DESC";
	$sql = mysql_query($sql);
    $i = 0;
	if(mysql_num_rows($sql)>0)
	{
		while($row = mysql_fetch_array($sql))
		{
			$leftUsers[$i]['username'] = $row['username'];
			$leftUsers[$i]['user_key'] = $row['user_key'];
			$leftUsers[$i]['sponsor_key'] = getSponsorName($row['sponsor_key']);
			$leftUsers[$i]['leg'] = legPlacement('0');
			$leftUsers[$i]['payment_status'] = activeNotActive($row['payment_status']);
			$i++;
		}
	}
	else
	{
		$leftUsers[$i]['username'] = 'No Member Found';
		$leftUsers[$i]['payment_status'] = '';
	}
	$users = array($leftUsers, $rightUsers);
	
	return $users;
}

function show_message_after_plugin_activation() 
{
	$table_prefix = mlm_core_get_table_prefix();
	$check1 = mysql_fetch_array(
								mysql_query("
												SELECT COUNT(*) AS num 
												FROM {$table_prefix}mlm_users
											")
							 );
	
	$check2 = mysql_fetch_array(
								mysql_query("
												SELECT COUNT(*) AS num 
												FROM {$table_prefix}options
												WHERE option_name = 'wp_mlm_general_settings'
											")
							 );
	$check3 = mysql_fetch_array(
								mysql_query("
												SELECT COUNT(*) AS num 
												FROM {$table_prefix}options
												WHERE option_name = 'wp_mlm_eligibility_settings'
											")
							 );
	$check4 = mysql_fetch_array(
								mysql_query("
												SELECT COUNT(*) AS num 
												FROM {$table_prefix}options
												WHERE option_name = 'wp_mlm_payout_settings'
											")
							 );
	$check5 = mysql_fetch_array(
								mysql_query("
												SELECT COUNT(*) AS num 
												FROM {$table_prefix}options
												WHERE option_name = 'wp_mlm_bonus_settings'
											")
							 );
	//wp_mlm_general_settings
	//wp_mlm_eligibility_settings
	//wp_mlm_payout_settings
	//wp_mlm_bonus_settings
	$flag = 0;
	if($check1['num']==0)
	{
		$msg = "<div class='updated fade'><p><strong>Please create the first user account in the MLM Network and configure other MLM settings. Please </strong>";
		$tab = 'createuser';
		$flag = 1;
	}
	else if($check2['num'] == 0)
	{
		$msg = "<div class='updated fade'><p><strong>Please configure other MLM Settings. </strong>";
		$tab = 'general';
		$flag = 1;
	}
	else if($check3['num'] == 0)
	{
		$msg = "<div class='updated fade'><p><strong>Complete the MLM settings. Please </strong>";
		$tab = 'eligibility';
		$flag = 1;
	}
	else if($check4['num'] == 0)
	{
		$msg = "<div class='updated fade'><p><strong>Complete the MLM settings. Please </strong>";
		$tab = 'payout';
		$flag = 1;
	}
	else if($check5['num'] == 0)
	{
		$msg = "<div class='updated fade'><p><strong>Complete the MLM settings. Please </strong>";
		$tab = 'bonus';
		$flag = 1;
	}
	if($flag == 1)
		echo $msg."<a href='".get_bloginfo('url')."/wp-admin/admin.php?page=admin-settings&tab=$tab'>click here</a>.</p></div>";
}

remove_filter('the_content', 'wpautop');

/************** Here begin the code for calculate and distribute the commission***********************/
function mlmDistributeCommission()
{
	$table_prefix = mlm_core_get_table_prefix();

	//select all active users and give commision to their parents
	$query = mysql_query("
							SELECT user_key FROM {$table_prefix}mlm_users 
							WHERE payment_status= '1' 
							AND banned = '0'
							ORDER BY id
						");
	$num = mysql_num_rows($query);
	if($num)
	{
		while($result  = mysql_fetch_array($query))
		{
			if(mlmIsEligibleForCommission($result['user_key']))
				mlmCalculateCommission($result['user_key']);
				
		}
	}
	
	return "Commission";
}

function mlmIsEligibleForCommission($key)
{
	// get table's prefix used by your site database schema
	$table_prefix = mlm_core_get_table_prefix();
	
	//get the eligibility for commission and bonus
	$mlm_eligibility = get_option('wp_mlm_eligibility_settings');
	
	//rule : user must have x direct refererers , they must be activated and payed, 
	//must be y in left leg and z in right leg
	//where x = y + z 
	
	$leftusers = 0;
	$rightusers =0; 
	
	$query = mysql_query("
							SELECT user_key 
							FROM {$table_prefix}mlm_users 
							WHERE banned = '0' 
							AND payment_status = '1' 
							AND sponsor_key = '".$key."'
			  			");
	$num = mysql_num_rows($query);
	
	if($num)
	{
		while($result = mysql_fetch_array($query))
		{
			$sql = mysql_query("
									SELECT COUNT(*) AS lactive 
									FROM {$table_prefix}mlm_leftleg 
									WHERE ukey = '".$result['user_key']."' 
									AND pkey = '".$key."'
							  ");
			$lactive = mysql_fetch_array($sql);
			
			if($lactive['lactive'] >= 1)
				$leftusers++;		
					
			$sql = mysql_query("
									SELECT COUNT(*) AS ractive 
									FROM {$table_prefix}mlm_rightleg 
									WHERE ukey = '".$result['user_key']."' 
									AND pkey = '".$key."'
							");
			$ractive = mysql_fetch_array($sql);
			
			if($ractive['ractive'] >= 1)
				$rightusers++;
		} //end while loop
		
		//total direct referral including left and right
		$total_referral = $leftusers + $rightusers;
		
		if($leftusers >= $mlm_eligibility['left_referral'] && $rightusers >= $mlm_eligibility['right_referral'] && $total_referral >= $mlm_eligibility['direct_referral'])
			return true;
	} // end if condition
	return false;
}
	
function mlmCalculateCommission($pkey)
{
	// get table's prefix used by your site database schema
	$table_prefix = mlm_core_get_table_prefix();
	
	//get the eligibility for commission
	$mlm_payout = get_option('wp_mlm_payout_settings');
	
	$pair1 = $mlm_payout['pair1'];
	$pair2 = $mlm_payout['pair2'];
	$initial_pair = $mlm_payout['initial_pair'];			
	$initial_amount = $mlm_payout['initial_amount'];
	$further_amount = $mlm_payout['further_amount'];
	
	$childs='';
	$rgtno=0;
	$leftno=0;
				
	//check users from left leg table 
	$leftquery = mysql_query("
								  SELECT  ukey 
								  FROM {$table_prefix}mlm_leftleg, {$table_prefix}mlm_users 
								  WHERE user_key = ukey 
								  AND pkey = '".$pkey."' 
								  AND commission_status = '0' 
								  AND payment_status = '1'  
								  ORDER BY {$table_prefix}mlm_users.id 
								  LIMIT {$pair1}
							");
								  
	$leftno = mysql_num_rows($leftquery);
	if($leftno >= $pair1)
	{
		$rightquery = mysql_query("
									   SELECT ukey 
									   FROM {$table_prefix}mlm_rightleg, {$table_prefix}mlm_users 
									   WHERE user_key = ukey 
									   AND pkey = '".$pkey."' 
									   AND commission_status = '0' 
									   AND payment_status = '1'  
									   ORDER BY {$table_prefix}mlm_users.id 
									   LIMIT {$pair2}
								");
								   
		$rgtno = mysql_num_rows($rightquery);
		//check users from rgt leg table 
		if($rgtno >= $pair2)
		{
			//mark users as paid and update commission table with child ids
			$childs='';
			while($leftresult = mysql_fetch_array($leftquery))
			{
				$leftupdate = mysql_query("
												UPDATE {$table_prefix}mlm_leftleg 
												SET commission_status = '1' 
										   		WHERE pkey = '".$pkey."' 
										   		AND ukey = '".$leftresult['ukey']."' 
										   		LIMIT 1
											");
											
				$childs .= mlmGetUserNameByKey($leftresult['ukey']).",";
			}
			
			//mark users as paid and update commission table with child ids
			while($rightresult = mysql_fetch_array($rightquery))
			{
				$rightupdate = mysql_query("
												UPDATE {$table_prefix}mlm_rightleg SET commission_status = '1' 
												WHERE pkey = '".$pkey."' 
												AND ukey = '".$rightresult['ukey']."' 
												LIMIT 1
											");
											
				$childs .= mlmGetUserNameByKey($rightresult['ukey']).",";
			}
			
			//give commission and mark users as paid
			$date = date("Y-m-d H:i:s");
			$parent_id = getuseridbykey($pkey);
			
			$num = mysql_fetch_array(mysql_query("
													SELECT COUNT(*) AS num 
													FROM {$table_prefix}mlm_commission 
													WHERE parent_id = $parent_id
												 ")
									);
			
			if($num['num'] >= $initial_pair)
				$amount = $further_amount;
			else
				$amount = $initial_amount;
				
			$child_ids = $childs;
			
			//deduct service charge and tds
			$insert = mysql_query("
									INSERT INTO {$table_prefix}mlm_commission 
									(
										id, date_notified, parent_id, child_ids, amount
									) 
									VALUES 
									(
										NULL, '".$date."', '".$parent_id."', '".$child_ids."', '".$amount."'
										
									)
								");	
		}
	}

	$childs='';
	$rgtno=0;
	$leftno=0;				

	//check users from rgt leg table
	$rightquery = mysql_query("
								   SELECT ukey 
								   FROM {$table_prefix}mlm_rightleg, {$table_prefix}mlm_users
								   WHERE user_key = ukey 
								   AND pkey = '".$pkey."' 
								   AND commission_status = '0' 
								   AND payment_status = '1' 
								   ORDER BY {$table_prefix}mlm_users.id 
								   LIMIT {$pair1}
							 "); 
							   
	$rgtno = mysql_num_rows($rightquery);
										
	if($rgtno >= $pair1)
	{	
		//check users from rgt leg table 
		$leftquery = mysql_query("
									  SELECT ukey
									  FROM {$table_prefix}mlm_leftleg, {$table_prefix}mlm_users
									  WHERE user_key = ukey 
									  AND pkey = '".$pkey."' 
									  AND commission_status = '0' 
									  AND payment_status = '1' 
									  ORDER BY {$table_prefix}mlm_users.id
									  LIMIT {$pair2}
								");
								  
		$leftno = mysql_num_rows($leftquery);
					
		if($leftno >= $pair2)
		{
			//mark users as paid and update commission table with child ids
			$childs='';
			while($rightresult = mysql_fetch_array($rightquery))
			{
				$rightupdate = mysql_query("
												UPDATE {$table_prefix}mlm_rightleg 
												SET commission_status = '1' 
												WHERE pkey = '".$pkey."' 
												AND ukey = '".$rightresult['ukey']."' 
												LIMIT 1
											");
											
				$childs .= mlmGetUserNameByKey($rightresult['ukey']).",";
			}
			//mark users as paid and update commission table with child ids
										
			while($leftresult = mysql_fetch_array($leftquery))
			{
				$leftupdate = mysql_query("
												UPDATE {$table_prefix}mlm_leftleg 
												SET commission_status = '1' 
										   		WHERE pkey = '".$pkey."' 
												AND ukey = '".$leftresult['ukey']."' 
												LIMIT 1
											");
											
				$childs .= mlmGetUserNameByKey($leftresult['ukey']).",";
			}
			//give commission and mark users as paid
			$date = date("Y-m-d H:i:s");
			$parent_id = getuseridbykey($pkey);
			
			$num = mysql_fetch_array(
										mysql_query("
														SELECT COUNT(*) AS num 
														FROM {$table_prefix}mlm_commission 
														WHERE parent_id = $parent_id
												 	")
									);
			
			if($num['num'] >= $initial_pair)
				$amount = $further_amount;
			else
				$amount = $initial_amount;

			$child_ids = $childs;
			
			$insert = mysql_query("
									INSERT INTO {$table_prefix}mlm_commission 
									(
										id, date_notified, parent_id, child_ids, amount
									) 
									VALUES
									(
										NULL, '".$date."', '".$parent_id."', '".$child_ids."', '".$amount."'
									)
								");
								
								
		}
	}
	
}

function mlmGetUserNameByKey($key)
{
	// get table's prefix used by your site database schema
	$table_prefix = mlm_core_get_table_prefix();
	
	$username = mysql_fetch_array(
									mysql_query("
													SELECT username 
			  									 	FROM {$table_prefix}mlm_users 
			   									 	WHERE user_key = '".$key."'
												")
								);
		return $username['username'];
}
/************** Here end the code for calculating and distributing the commission ***********************/


/************************* Here begin of the code for calculate  and distribute the bonus *****************************************/
function mlmDistributeBonus()
{
	$table_prefix = mlm_core_get_table_prefix();
	
	//select all active users and give commision to their parents
	$query = mysql_query("
							SELECT user_key FROM {$table_prefix}mlm_users 
							WHERE payment_status= '1' 
							AND banned = '0'
							ORDER BY id
						");
	$num = mysql_num_rows($query);
	
	if($num)
	{
		while($result  = mysql_fetch_array($query))
		{
			if(mlmIsEligibleForCommission($result['user_key']))
				mlmCalculateBonus($result['user_key']);
				
		}
	}
	return "Bonus";
}

function mlmCalculateBonus($key)
{
	
	$table_prefix = mlm_core_get_table_prefix();
	
	//get the eligibility for bonus
	$mlm_bonus = get_option('wp_mlm_bonus_settings');
	
	if($mlm_bonus['bonus_criteria'] == 'personal')
	{
		//count total direct referrals
		$query = mysql_fetch_array(
									mysql_query("
													SELECT COUNT(*) AS num 
													FROM {$table_prefix}mlm_users
													WHERE sponsor_key = '".$key."'
													AND payment_status = '1'
													AND banned = '0'
												")
									);
		$bonus_slab = $query['num'];
	}
	else if($mlm_bonus['bonus_criteria'] == 'pair')
	{		
		//count total active users on left leg
		$leftcount = mysql_fetch_array(
										mysql_query("
														SELECT COUNT(*) AS num 
														FROM {$table_prefix}mlm_leftleg, {$table_prefix}mlm_users
														WHERE user_key = ukey
														AND pkey = '".$key."'
														AND payment_status = '1'
														AND banned = '0'
												")
									  );
		//count total active users on right leg							  
		$rightcount = mysql_fetch_array(
										mysql_query("
														SELECT COUNT(*) AS num 
														FROM {$table_prefix}mlm_rightleg, {$table_prefix}mlm_users
														WHERE user_key = ukey
														AND pkey = '".$key."'
														AND payment_status = '1'
														AND banned = '0'
												")
									  );
		//count total numbers of active pair							  
		$paircase1 = getPair($leftcount['num'], $rightcount['num']);
		$paircase2 = getPair($rightcount['num'], $leftcount['num']);
		
		if($paircase1['pair'] >= $paircase2['pair'])
			$bonus_slab = $paircase1['pair'];
		else
			$bonus_slab = $paircase2['pair'];
	}
		
	$slabpair = $mlm_bonus['pair'];

	$slabamount = $mlm_bonus['amount'];
	
	//count total slabs defined for bouns
	$totalslabs = count($slabpair);
		
	//get mlm user id
	$mlm_user_id = getuseridbykey($key);
		
	$flag = 1;
	while($flag)
	{
		$num = distributeBonusSlab($mlm_user_id);
		if($bonus_slab >= $slabpair[$num] && ($num < $totalslabs))
		{
			insertBonusSlab($mlm_user_id, $slabamount[$num]);
		}
		else
			$flag = 0;
	}

	
	
}

function getPair($leftcount, $rightcount)
{
	$mlm_payout = get_option('wp_mlm_payout_settings');
	$pair1 = $mlm_payout['pair1'];
	$pair2 = $mlm_payout['pair2'];
	
	$leftpair = (int)($leftcount/$pair1);
	$rightpair = (int)($rightcount/$pair2);
	
	if($leftpair <= $rightpair)
		$pair = $leftpair;
	else
		$pair = $rightpair;
		
	$leftbalance = $leftcount - ($pair * $pair1);
	$rightbalance = $rightcount - ($pair * $pair2);
	
	$array['leftbal'] = $leftbalance;
	$array['rightbal'] = $rightbalance; 
	$array['pair'] = $pair;
	
	return $array;
}

function distributeBonusSlab($mlm_user_id)
{
	$table_prefix = mlm_core_get_table_prefix();
	//count how many times bonus have been paid by the system previously
	
	$cb = mysql_fetch_array(
								mysql_query("
												SELECT COUNT(*) AS num
												FROM {$table_prefix}mlm_bonus
												WHERE mlm_user_id = '".$mlm_user_id."'
											")
							);
	return $cb['num'];
}

function insertBonusSlab($mlm_user_id, $amount)
{
	$table_prefix = mlm_core_get_table_prefix();
	$date = date('Y-m-d H:i:s');
	
	//deduct service charge and tds
	//$payable_amount_array = calculateTdsAndServiceCharge($amount);
	
	$insert = mysql_query("
							INSERT INTO {$table_prefix}mlm_bonus
							(
								id, date_notified, mlm_user_id, amount
							)
							VALUES
							(
								NULL, '".$date."', '".$mlm_user_id."', '".$amount."'
							)
							
						 ");
}
/*********************** Here end the code of calculating and distributing bonus ******************************************/

function calculateTdsAndServiceCharge($amount)
{
	$mlm_payout = get_option('wp_mlm_payout_settings');
	//first calculate tds
	if($mlm_payout['tds'] != "")
	{
		$tds =  $amount * ($mlm_payout['tds'] / 100);
		//$amount = $amount - $tds;
	}
	else
		$tds = 0;
	//calculate service charge	
	if($mlm_payout['service_charge'] != "")
	{
		//$amount = $amount - $mlm_payout['service_charge'];
		$array['service_charge'] = $mlm_payout['service_charge'];
	}
	else
		$array['service_charge'] = 0.00;
		
		
	$array['amount'] = $amount;
	$array['tds'] = $tds;
	
	return $array;
}

//this function REGISTER THE SHORTCODE when plugin is activated
function register_shortcodes()
{
   //1st agru is the name of the shortcode and second is function name which is called when shortcode is triggered
   add_shortcode(MLM_REGISTRATIN_SHORTCODE, 'register_user_html_page');
   add_shortcode(MLM_VIEW_NETWORK_SHORTCODE, 'viewBinaryNetwork');
   add_shortcode(MLM_VIEW_GENEALOGY_SHORTCODE, 'viewBinaryNetwork');
   add_shortcode(MLM_NETWORK_DETAILS_SHORTCODE, 'mlmNetworkDetails');
   add_shortcode(MLM_LEFT_GROUP_DETAILS_SHORTCODE, 'myLeftGroupDetails');
   add_shortcode(MLM_RIGHT_GROUP_DETAILS_SHORTCODE, 'myRightGroupDetails');
   add_shortcode(MLM_PERSONAL_GROUP_DETAILS_SHORTCODE, 'myPersonalGroupDetails');
   add_shortcode(MLM_MY_CONSULTANT_SHORTCODE, 'myConsultantTotalSales');
   add_shortcode(MLM_UPDATE_PROFILE_SHORTCODE, 'mlm_update_profile');
   add_shortcode(MLM_CHANGE_PASSWORD_SHORTCODE, 'mlm_change_password');
   add_shortcode(MLM_MY_PAYOUTS_SHORTCODE, 'mlm_my_payout_page');
   add_shortcode(MLM_MY_PAYOUT_DETAILS_SHORTCODE, 'mlm_my_payout_details_page');
   
   
   
   /**** create pages for run payout and bonus ***/
   add_shortcode(MLM_DISTRIBUTE_COMMISSION_SHORTCODE, 'mlmDistributeCommission');
   add_shortcode(MLM_DISTRIBUTE_BONUS_SHORTCODE, 'mlmDistributeBonus');
   /***** end code for distributing payout and bonus*****/
			   
}

function mlm_admin_menu() 
{
		/*
		1st argument: Title of the page
		2nd argument: Name of the menu
		3rd argument: The capability required for this menu to be displayed to the user.
		if 3rd arugment value is zero then this menu also accessible at user interface
		4ht argument: pass to the URL (name of the page)
		5th argument: function name (which function to be called) 
		*/
		
	//add_menu_page() function add the new menu item
	//add_menu_page('WP-MLM-Settings', 'WP-MLM', 1,'register-first-user', 'register_first_user');
	//add_submenu_page('register-first-user', 'Admin Settings', 'Admin Settings', 1,'admin-settings', 'adminMLMSettings');
	
	$icon_url =  plugins_url()."/mlm/images/mlm_tree.png";
	add_menu_page('WP-MLM-Settings', 'Binary MLM', 1,'admin-settings', 'adminMLMSettings', $icon_url);
	add_submenu_page('admin-settings','Settings','Settings','1','admin-settings','adminMLMSettings');
	add_submenu_page('admin-settings','Run Payouts','Run Payouts','administrator','mlm-payout','adminMLMPayout');
	
	
	
}

// get_post_id function return the inserted post_id's
function get_post_id($page)
{
	$table_prefix = mlm_core_get_table_prefix();
	$sql = "SELECT post_id 
			FROM {$table_prefix}postmeta 
			WHERE meta_key = '".$page."' 
			AND meta_value = '".$page."'";
	$sql = mysql_query($sql);
	$post_id = mysql_fetch_object($sql);
	return $post_id->post_id;
}

//register_page function register the page and postID
function register_page($title, $content)
{
	$post_array = array(
							'post_title'    =>  $title,
							'post_content'	 => "[".$content."]",
							'post_status'   =>  'publish',
							'post_type'	 =>  'page',
							'comment_status'=>  'close',
							'ping_status'	 =>  'close'
						);
	// Insert the post into the wp_posts table
	$post_id = wp_insert_post( $post_array );
	return $post_id;
}

function createTheMlmMenu()
{
	//assign the mlm page title to the array
	$page_title = array();
	$page_title['registration'][] = MLM_REGISTRATION_TITLE;
	
	$page_title['network'][] = MLM_VIEW_NETWORK_TITLE;
	$page_title['network'][] = MLM_VIEW_GENEALOGY_TITLE;
	$page_title['network'][] = MLM_NETWORK_DETAILS_TITLE;
	$page_title['network'][] = MLM_LEFT_GROUP_DETAILS_TITLE;
	$page_title['network'][] = MLM_RIGHT_GROUP_DETAILS_TITLE;
	$page_title['network'][] = MLM_PERSONAL_GROUP_DETAILS_TITLE;
	$page_title['network'][] = MLM_MY_CONSULTANT_TITLE;
	$page_title['network'][] = MLM_MY_PAYOUTS;
	
	$page_title['profile'][] = MLM_UPDATE_PROFILE_TITLE;
	$page_title['profile'][] = MLM_CHANGE_PASSWORD_TITLE;
	
	//$page_title['commission'][] = MLM_DISTRIBUTE_COMMISSION_TITLE;
//	$page_title['commission'][] = MLM_DISTRIBUTE_BONUS_TITLE;
	
	//name of the menu
	$name = MENU_NAME;
	
    //create the menu
    $menu_id = wp_create_nav_menu($name);
	
	//get the term id
 	$menu = get_term_by( 'name', $name, 'nav_menu' );
	
 	foreach($page_title as $value)
	{
		//get the post_id by the page title
		$myPage = get_page_by_title($value[0]);
		
		//build the menu item array
		$args = array();
		$args['menu-item-db-id'] = 0;
		$args['menu-item-object-id'] = $myPage->ID;
		$args['menu-item-object'] = 'page';
		$args['menu-item-parent-id'] = 0;
		$args['menu-item-position'] ='';
		$args['menu-item-type'] = 'post_type';
		$args['menu-item-title'] = $value[0];
		$args['menu-item-description'] = '';
		$args['menu-item-status'] = 'publish';
		$args['menu-item-attr-title'] = '';
		$args['menu-item-target'] = '';
		$args['menu-item-classes'] = '';
		$args['menu-item-xfn'] = '';
		
		//create the menu item
		$menu_item_id = wp_update_nav_menu_item($menu->term_id, 0, $args);
		
		if(count($value) > 1)
		{
			for($i = 1; $i < count($value); $i++)
			{
				//get the post_id by the page title
				$myPage = get_page_by_title($value[$i]);
				
				//build the menu item array
				$args = array();
				$args['menu-item-db-id'] = 0;
				$args['menu-item-object-id'] = $myPage->ID;
				$args['menu-item-object'] = 'page';
				$args['menu-item-parent-id'] = $menu_item_id;
				$args['menu-item-position'] ='';
				$args['menu-item-type'] = 'post_type';
				$args['menu-item-title'] = $value[$i];
				$args['menu-item-description'] = '';
				$args['menu-item-status'] = 'publish';
				$args['menu-item-attr-title'] = '';
				$args['menu-item-target'] = '';
				$args['menu-item-classes'] = '';
				$args['menu-item-xfn'] = '';
				
				//create the menu item
				$item_id = wp_update_nav_menu_item($menu->term_id, 0, $args);
			}
		}
	}
	update_option('menu_check', true);
	
	$primary_menu = array
					(
						"nav_menu_locations" => array
							(
								"primary" => $menu_id,
								"primary_".PLUGIN_NAME => 0
							)
					
					);
	$theme_slug = get_option( 'stylesheet' );
	update_option("theme_mods_$theme_slug", $primary_menu);
}


function add_payment_status_column_value( $value, $column_name, $user_id ){
	//$user = get_userdata( $user_id );
		
	/***************************/
	if ( 'payment_status' == $column_name)
	{
		
		$table_prefix = mlm_core_get_table_prefix();
		/*check that it is mlm user or not */
		$sql = mysql_query("SELECT user_id, payment_status FROM {$table_prefix}mlm_users WHERE user_id = '".$user_id."'");
		$html = '';
		
		if(mysql_num_rows($sql)>0)
		{
			$res = mysql_fetch_array($sql);
			
			$path = "'".plugins_url()."'";
			
			$currStatus = $res['payment_status'];
			global $paymenntStatusArr; 
			
			$html .= '<select name="payment_status_'.$user_id.'" id="payment_status_'.$user_id.'" onchange="update_payment_status('.$path.','.$user_id.',this.value)">';
			
			foreach($paymenntStatusArr AS $row=>$val) :	
			
			if($row == $currStatus ){
				$sel = 'selected="selected"';
			}else{
				$sel ='';
			}
			
			$html .= '<option value="'.$row.'" '.$sel.'>'.$val.'</option>';
			endforeach; 
			$html .= '</select><span id="resultmsg_'.$user_id.'"></span>';
			return $html;
		
		
		}else{
			return "Not a MLM User";
		}	
		
	}
	
}


function load_javascript() {
	wp_enqueue_script( 'custom-script', plugins_url( '/js/ajax.js', __FILE__ ));
	wp_enqueue_script( 'custom-script-epoch-classes', plugins_url( '/js/epoch_classes.js', __FILE__ ));
	wp_enqueue_script( 'custom-script-form-validation', plugins_url( '/js/form-validation.js', __FILE__ ));
	wp_enqueue_style('custom-css-epoch-classes', plugins_url( '/css/epoch_styles.css', __FILE__ ));
	wp_enqueue_style('custom-css-mlm', plugins_url( '/css/mlm.css', __FILE__ ));
}

/*function to display the payment status column in the users table*/
add_filter('manage_users_columns', 'add_payment_status');
add_filter('manage_users_custom_column',  'add_payment_status_column_value', 10, 3);

function add_payment_status( $columns){
    $columns['payment_status'] = __('Payment Status', 'payment_status');
    return $columns;
}
 
?>