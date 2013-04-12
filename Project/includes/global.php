<?php
    session_set_cookie_params((time()+60*60*24*30), "/");
    session_start(); 

#################################

    include_once("db.php");
	include_once("php_error.php");
	// error_reporting(E_ERROR);

#################################

	global $host, $user, $pass, $db, $port;
	$link = mysqli_connect($host, $user, $pass, $db, $port);

	if($_SESSION['loggedIn']===true)
	{
		echo("<h1>Hello, World</h1>");
		$un = $_SESSION["username"];
		$infoQuery = "SELECT * FROM users WHERE username = '$un'";
		$infoResults = mysqli_query($link, $infoQuery);
		$userInfo = mysqli_fetch_assoc($infoResults);
		$deptName 	= "Computer Science and Information Systems";
		$deptAbbrev = "CSIS";
		$userName 	= $userInfo['title']." ".$userInfo['lastName'];
		$userTitle 	= "Admin";
	}
?>