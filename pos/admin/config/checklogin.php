<?php
function check_login()
{
if (empty($_SESSION['admin_id']))
	{
		$host = $_SERVER['HTTP_HOST'];
		$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		$extra="index.php";
		$_SESSION["admin_id"]="";
		header("Location: http://$host$uri/$extra");
		exit;
	}
}
?>
