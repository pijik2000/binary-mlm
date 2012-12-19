<?php 
include("../../../../wp-config.php");

$action = $_REQUEST['action'];

$table_prefix = mlm_core_get_table_prefix();
 
 global $wpdb;
if($action == 'username')
{
	$q = $_GET['q'];
	$uname = $wpdb->get_var("SELECT username FROM wp_mlm_users WHERE username = '$q'");
	if($uname)
		echo "<span class='errormsg'>Sorry! The specified username is not available for registration.</span>";
	else
		echo "<span class='msg'>Congratulations! The username is available.</span>";
}
else if($action == 'sponsor')
{
	
	$q = $_GET['q'];
	$sname = $wpdb->get_var("SELECT username FROM {$table_prefix}mlm_users WHERE `username` = '$q'");
	
	if(!$sname)
		echo "<span class='errormsg'>Sorry! The specified sponsor is not available for registration.</span>";
	//else
		//echo "<span class='msg'>Congratulations! Your sponsor is <b> ".ucwords(strtolower($sname))."</b> .</span>";
}
		
?>