<?php
	// session_start();
    include_once("db.php");
	include_once("php_error.php");
	// error_reporting(E_ERROR);

#################################

	global $host, $user, $pass, $db, $port;
	$link = mysqli_connect($host, $user, $pass, $db, $port);

	if(isset($_SESSION['loggedIn']))
	{
		if($_SESSION['loggedIn']===true)
		{
			$un = $_SESSION["username"];
			$infoQuery = "SELECT * FROM users WHERE username = '$un'";
			$infoResults = mysqli_query($link, $infoQuery);
			$userInfo = mysqli_fetch_assoc($infoResults);
			$deptName 	= "Computer Science and Information Systems";
			$deptAbbrev = "CSIS";
			$userName 	= $userInfo['title']." ".$userInfo['lastName'];
			$userTitle 	= "Admin";
		}
	}
?>