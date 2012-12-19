<?php 
function mlm_my_payout_page()
{

$detailsArr =  my_payout_function();
//echo "<pre>";print_r($detailsArr); exit; 
$page_id = get_post_id('mlm_my_payout_details_page');

if(count($detailsArr)>0){
$mlm_settings = get_option('wp_mlm_general_settings');
	?>
	<table width="100%" border="0" cellspacing="10" cellpadding="1" id="payout-page">
		<tr>
			<td>Date</td>
			<td>Amount</td>
			<td>Action</td>
		</tr>
		<?php foreach($detailsArr as $row) :  
		
		$amount = $row->commission_amount + $row->bonus_amount - $row->tax - $row->service_charge;
		?>
		<tr>
			<td><?= $row->payoutDate ?></td>
			<td><?= $mlm_settings['currency'].' '.$amount ?></td>
			<td><a href="<?php bloginfo('url')?>/?page_id=<?= $page_id?>&pid=<?= $row->payout_id?>">View</a></td>
			
		</tr>
		
		<?php endforeach; ?>
		
	</table>
	<?php 
	}else{

	?>
	<div class="no-payout"> You have not earned any commisssions yet. </div>
	
	<?php 
	}
}

function my_payout_function()
{
	
	global $table_prefix;
	
	global $wpdb;
	global $current_user;
    get_currentuserinfo();
	
	$userId = $current_user->ID; 
	//$mlm_user_id = $wpdb->get_results( );
	
	$sql = "SELECT {$table_prefix}mlm_users.id AS id FROM {$table_prefix}users,{$table_prefix}mlm_users WHERE {$table_prefix}mlm_users.username = {$table_prefix}users.user_login AND {$table_prefix}users.ID = '".$userId."'"; 
	
	$res = $wpdb->get_results($sql, ARRAY_A); 
	
	$mlm_user_id = $res[0]['id']; 
	
			
	if ( is_user_logged_in())
	{
	
		$sql = "SELECT id, user_id, DATE_FORMAT(`date`,'%d %b %Y') as payoutDate, payout_id, commission_amount, bonus_amount, tax, service_charge FROM {$table_prefix}mlm_payout WHERE user_id = '".$mlm_user_id."'";

		$myrows = $wpdb->get_results($sql);
	
	}
	
	return $myrows; 

}

?>