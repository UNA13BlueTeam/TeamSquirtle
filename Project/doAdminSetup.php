<?php 
	session_start();
	include("includes/db.php");
	global $host, $user, $pass, $db, $port;
	$link = mysqli_connect($host, $user, $pass, $db, $port);
	
	$deptName = $_POST['departmentName'];
	$semestName = $_POST['semesterName'];
	$user = $_SESSION['username'];
	
	$query = "UPDATE users SET deptName = '$deptName', semesterName = '$semestName', firstLogOn = '0' WHERE username = '$user'";
	$insert = mysqli_query($link, $query);
	header("Location: adminHome.php");
?>