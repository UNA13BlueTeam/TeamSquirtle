<?php 
	session_start();
	include("includes/db.php");
	global $host, $user, $pass, $db, $port;
	$link = mysqli_connect($host, $user, $pass, $db, $port);
	$password = $_POST['pass1'];
	$passCheck = $_POST['pass2'];
	$user = $_SESSION['username'];
	if($password === $passCheck)
	{
		// $password = crypt($password);
		$query = "UPDATE users SET password = '$password', firstLogOn = 0 WHERE username = '$username'";
		mysqli_query($link, $query);
		header("Location: facultyHome.php");
	}else{
		$_POST['invalid'] = true;
		header("Location: setup.php");
	}
?>