<?php
	global $host, $user, $pass, $db, $port;
	$link = mysqli_connect($host, $user, $pass, $db, $port);
	$un = $_SESSION["username"];
	$infoQuery = "SELECT * FROM users WHERE username = '$un'";
	$infoResults = mysqli_query($link, $infoQuery);
	$userInfo = mysqli_fetch_assoc($infoResults);
	$deptName 	= "Computer Science and Information Systems";
	$deptAbbrev = "CSIS";
	$userName 	= $userInfo['title']." ".$userInfo['lastName'];
	$userPerm 	= "Admin";
?>