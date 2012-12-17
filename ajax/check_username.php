<?php 
include("../../../../wp-config.php");

$action = $_REQUEST['action'];

$table_prefix = mlm_core_get_table_prefix();
 
if($action == 'username')
{
	$q = $_GET['q'];
	$select = mysql_query("SELECT username FROM wp_mlm_users WHERE username = '$q'");
	$num = mysql_num_rows($select);
	if($num)
		echo "<span class='errormsg'>Sorry! The specified username is not available for registration.</span>";
	else
		echo "<span class='msg'>Congratulations! The username is available.</span>";
}
else if($action == 'sponsor')
{
	
	$q = $_GET['q'];
	$select = mysql_query("SELECT username FROM {$table_prefix}mlm_users WHERE `username` = '$q'");
	$num = mysql_num_rows($select);
	$row = mysql_fetch_array($select); 
	if(!$num)
		echo "<span class='errormsg'>Sorry! The specified sponsor is not available for registration.</span>";
	//else
		//echo "<span class='msg'>Congratulations! Your sponsor is <b> ".ucwords(strtolower($row['user_name']))."</b> .</span>";
} 
?>