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
			// echo("loggedIn = true <br>");
			
			$un = $_SESSION["username"];
			$infoQuery = "SELECT * FROM users WHERE username = '$un'";
			$infoResults = mysqli_query($link, $infoQuery);
			$userInfo = mysqli_fetch_assoc($infoResults);
			$deptName 	= $userInfo['deptName'];
			$deptAbbrev = "CSIS";
		}else
		{
			// echo("LoggedIn = false <br>");
		}
	}else
	{
		// echo("loggedIn unset. <br>");
	}

	function clearUsers(){
		$query = "TRUNCATE TABLE faculty; DELETE * FROM users WHERE permission != 1;";
		mysqli_query($link, $query);
	}

	function clearClasses(){
		$query = "TRUNCATE TABLE courses";
		mysqli_query($link, $query);
	}

	function clearClassTimes(){
		$query = "TRUNCATE TABLE timeSlots";
		mysqli_query($link, $query);
	}

	function clearRooms(){
		$query = "TRUNCATE TABLE rooms";
		mysqli_query($link, $query);
	}

	function clearPrereqs(){
		$query = "TRUNCATE TABLE prereqs";
		mysqli_query($link, $query);
	}

	function clearPrefs(){
		$query = "TRUNCATE TABLE preferences";
		mysqli_query($link, $query);
	}

	function clearSchedule(){
		$query = "TRUNCATE TABLE scheduledCourses";
		mysqli_query($link, $query);
	}
?>