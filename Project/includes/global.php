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
			echo($un);
			echo($infoQuery);
			$infoResults = mysqli_query($link, $infoQuery);
			$userInfo = mysqli_fetch_assoc($infoResults);
			echo($userInfo['lastName']);
			$deptName 	= "Computer Science and Information Blah";
			$deptAbbrev = "CSIS";
			echo($userName);
		}
	}
?>