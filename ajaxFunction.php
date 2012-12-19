<?php 
require_once('../../../wp-config.php');
$g_criteria = ""; 
$g_criteria1 = ""; 
$g_criteria2 = ""; 
$g_criteria3 = "";
 
if(isset($_REQUEST['do'])) {
	$g_criteria1 = trim($_REQUEST['do']);
}

if(isset($_REQUEST['event'])) {
	$g_criteria2 = trim($_REQUEST['event']);
}


switch($g_criteria1)
{
	
	case "statuschange": 
		updatePaymentStatus();		
	break;
	
}


function updatePaymentStatus()
{
	global $wpdb;
	if(isset($_REQUEST['userId']) && isset($_REQUEST['status']))
	{
		$table_prefix = mlm_core_get_table_prefix();
				
		$sql = "UPDATE 
								 {$table_prefix}mlm_users 
				      SET 
					     payment_status = '".$_REQUEST['status']."'
				      WHERE 
					     user_id = '".$_REQUEST['userId']."'";
			
		$rs = $wpdb->query($sql);
		if(!$rs){
			echo "<span class='error' style='color:red'>Updating Fail</span>";
		} 		 
		 
	}
	
}



?>