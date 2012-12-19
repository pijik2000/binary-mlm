<?php 
function adminMLMPayout()
{
	$msg = '';
	
	if(isset($_REQUEST['distribute_commission_bonus']))
	{
		$msg .= mlmDistributeCommission();
		$msg .= ' & '; 
		$msg .= mlmDistributeBonus();
		$msg .= '&nbsp;Distributed Successfully';
		
		 
	}
	
		
	if(isset($_REQUEST['pay_cycle']))
	{	
		$msg = wpmlm_run_pay_cycle(); 
	}


?>
<div class="userslist">
	<div class="heading">
		<div id="icon-users" class="icon32"></div>
		<h1>MLM Payout</h1>
	</div>
	
	<div class="helpmessage">
		<p>	Use this screen to run the Payout routine for your network. While testing the plugin use the Distribute Commission and Bonus button below after adding a few members to the network. On a live site the following URL needs to be scheduled (cron job) to run every hour for the commission and bonus routines.
		
		</p>
		
		<p><?= plugins_url()."/".MLM_PLUGIN_NAME?>/cronjobs/commission-bonus.php</p>
		
		<p>The commission and bonus routines would simply keep distributing the commission and bonus amounts in the member accounts. They would not show up in their account till the time the Payout Routine is not run. This script can be run manually once every week, every fortnight or every month depending on the payout cycle of the network. Alternately, please schedule (cron job) the following URL as per the frequency of the payout cycle.</p>
		
		<p><?= plugins_url()."/".MLM_PLUGIN_NAME?>/cronjobs/paycycle.php</p>
		
		
	</div>
	<div class="forms-ui">
		<p><h2 style="color:#0000CC;"><?= $msg ?></h2></p>
		<br />

		
		<form name="frm" method="post" action="">
		
			<div class="payout-run">
				<input class="button-primary" type="submit" name="distribute_commission_bonus" value="<?php _e('Distribute Commission and Bonus'); ?>" id="distribute_commission_bonus" /> 
			</div>
				
			<div class="payout-run">
				<input class="button-primary" type="submit" name="pay_cycle" value="<?php _e('Run Payout Routine'); ?>" id="pay_cycle" /> 
			</div>
			
			<div style="clear:both;"></div>	
	
		</form>
	</div>	
</div>
<?php 
}
?>