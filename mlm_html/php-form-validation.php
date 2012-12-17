<?php
function filter($data)
{
	$data = trim(htmlentities(strip_tags($data)));
	
	if(get_magic_quotes_gpc())
		$data = stripslashes($data);
	
	$data = mysql_real_escape_string($data);
	
	return $data;
}

function checkInputField($value)
{
	if($value=="")
		return true;
	else
		return false;
}

function confirmPassword($pass, $confirm)
{
	if($confirm != $pass)
		return true;
	else
		return false;
}

function confirmEmail($email, $confirm)
{
	if($confirm != $email)
		return true;
	else 
		return false;
}

function checkPair($pair1, $pair2)
{
	if($pair1 == "" || $pair2 == "" || $pair1 == 0 || $pair2 == 0)
		return true;
	else
		return false;
}

function checkInitial($initial)
{
	if($initial == "" || $initial == 0)
		return true;
	else
		return false;
}
?>