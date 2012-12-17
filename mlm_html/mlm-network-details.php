<?php
function mlmNetworkDetails()
{
	//get loged user's key
	$key = get_current_user_key();
	
	//Total Users on My left leg
	$leftLegUsers = totalLeftLegUsers($key);
	
	//Total users on my right leg
	$rightLegUsers = totalRightLegUsers($key);
	
	//paid users on my left leg
	$leftLegActiveUsers = activeUsersOnLeftLeg($key);
	
	//paid users on my right leg
	$rightLegActiveUsers = activeUsersOnRightLeg($key);
	
	//Total my personal sales
	$personalSales = totalMyPersonalSales($key);
	
	//Total my personal sales active users
	$activePersonalSales = activeUsersOnPersonalSales($key);
	
	//show five users on left leg
	$fiveLeftLegUsers = myFiveLeftLegUsers($key);
	
	//show five users on right leg
	$fiveRightLegUsers = myFiveRightLegUsers($key);
	
	//show five users on personal sales
	$fivePersonalUsers = myFivePersonalUsers($key);
?>
	<!-- Begin for left leg users -->
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>Total on Left Leg: <?= $leftLegUsers?></td>
			<td>Active: <?= $leftLegActiveUsers?></td>
		</tr>
		<?php
		foreach($fiveLeftLegUsers as $key => $value)
		{
			echo "<tr>";
			foreach($value as $k=>$val)
			{
				echo "<td>".$val."</td>";
			}
			echo "</tr>";
		}
		?>
		<tr>
		<td colspan="2"><a href="?page_id=<?= get_post_id('mlm_left_group_details_page');?>" style="text-decoration: none">View All</a></td>
		</tr>
	</table>
	<!-- end for left leg users -->	
	
	<!-- Begin for Right leg users -->
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>Total on Right Leg: <?= $rightLegUsers?></td>
			<td>Active: <?= $rightLegActiveUsers?></td>
		</tr>
		<?php
		foreach($fiveRightLegUsers as $key => $value)
		{
			echo "<tr>";
			foreach($value as $k=>$val)
			{
				echo "<td>".$val."</td>";
			}
			echo "</tr>";
		}
		?>
		<tr>
		<td colspan="2"><a href="?page_id=<?= get_post_id('mlm_right_group_details_page');?>" style="text-decoration: none">View All</a></td>
		</tr>
	</table>
	<!-- end for Right leg users -->
	
	<!-- Begin for Personal users -->
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>My Personal Sales: <?= $personalSales?></td>
			<td>Active: <?= $activePersonalSales?></td>
		</tr>
		<?php
		foreach($fivePersonalUsers as $key => $value)
		{
			echo "<tr>";
			foreach($value as $k=>$val)
			{
				echo "<td>".$val."</td>";
			}
			echo "</tr>";
		}
		?>
		<tr>
		<td colspan="2"><a href="?page_id=<?= get_post_id('mlm_personal_group_details_page');?>" style="text-decoration: none">View All</a></td>
		</tr>
	</table>
	<!-- end for Personal leg users -->	
<?php
}
?>