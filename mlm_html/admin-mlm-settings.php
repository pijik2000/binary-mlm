<?php
error_reporting(0);
require_once("php-form-validation.php");
require_once("admin-mlm-general-settings.php");
require_once("admin-mlm-eligibility-settings.php");
require_once("admin-mlm-payout-settings.php");
require_once("admin-mlm-bonus-settings.php");
require_once("admin-create-first-user.php");
require_once("admin-mlm-payout-run.php");

function adminMLMSettings()
{	
	global $pagenow, $wpdb;
	$table_prefix = mlm_core_get_table_prefix();
	
	$sql = "SELECT COUNT(*) AS num FROM {$table_prefix}mlm_users";
	$num = $wpdb->get_var($sql);
	
	if($num == 0)
	{
		$tabs = array( 
						'createuser' => 'Create First User', 
						'general' => 'General', 
						'eligibility' => 'Eligibility', 
						'payout' => 'Payout', 
						'bonus' => 'Bonus'
						);
						
		$tabval = 'createuser';
		$tabfun = 'register_first_user';
	}
	else
	{
		$tabs = array(
						'general' => 'General', 
						'eligibility' => 'Eligibility', 
						'payout' => 'Payout', 
						'bonus' => 'Bonus'
					  ); 
					  
		$tabval = 'general';
		$tabfun = 'mlmGeneral';
	}
	
	if( $pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'createuser')
		$current = 'createuser';
	else if(  $pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'general')
		$current = 'general';
	else if($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'eligibility')
		$current = 'eligibility';
	else if( $pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'payout')
		$current = 'payout';
	else if( $pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'bonus')
		$current = 'bonus';
			
	else
 		$current = $tabval;
		
    $links = array();
	  
	echo '<div id="icon-themes" class="icon32"><br></div>';
	echo "<h1>MLM Settings</h1>";
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name )
	{
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=admin-settings&tab=$tab'>$name</a>";    
    }
    echo '</h2>';
	
	if($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'createuser')
			register_first_user();
	else if($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'general')
		 mlmGeneral();
	else if($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'eligibility')
		mlmEligibility();
	else if($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'payout')
		mlmPayout();
	else if($pagenow == 'admin.php' && $_GET['page'] == 'admin-settings' && $_GET['tab'] == 'bonus')
		mlmBonus();
	
	else
		 $tabfun();
}//end function






?>