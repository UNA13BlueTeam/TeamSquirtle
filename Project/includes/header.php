<?php 
	session_start();
	//include_once("php_error.php"); 
	//\php_error\reportErrors();
 	include_once("db.php");
	include_once("global.php");
	// error_reporting(E_ERROR);

	date_default_timezone_set('America/Chicago');
	if(isset($_SESSION['loggedIn']) == false or $_SESSION['loggedIn'] == false){
		header("Location: logout.php");
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link rel="stylesheet" href="css/main.css" type="text/css" />
		<link rel="icon" type="image/png" href="img/squirtle.png">
	</head>
	<body>
		<?php
			$auth = $_SESSION['permission'];
			if($auth == 1)
			{
				echo('
					<div class="nav">
						<nav>
							<!-- <a href="adminHome.php">UNA</a> -->
							<a href="adminHome.php" id="home">Home</a>&nbsp;|&nbsp;
							<a href="manageTimeSlots.php" id="timeSlots">Manage Class Times</a> &nbsp;|&nbsp;
							<a href="manageRooms.php" id="building">Manage Rooms</a>&nbsp;|&nbsp;
							<a href="manageClass.php" id="classes">Manage Classes</a>&nbsp;|&nbsp;
							<a href="manageFaculty.php" id="faculty">Manage Faculty</a>&nbsp;|&nbsp;
							<a href="includes/UserManual.pdf" id="help">Help</a>&nbsp;|&nbsp;
							<a href="adminActions.php" id="help">Options</a>&nbsp;|&nbsp;
							<a href="logout.php" id="logout">Log Out</a>
							<img src="img/unaLogo.png" alt="UNA Logo" id="logo" />
						</nav>
				');
			}elseif($auth == 2)
			{
				echo('
					<div class="nav">
						<nav>
							<!-- <a href="facultyHome.php">UNA</a> -->
							<a href="facultyHome.php" id="home">Home</a>&nbsp;|&nbsp;
							<a href="viewSchedule.php" id="timeSlots">View Schedules</a> &nbsp;|&nbsp;
							<a href="pickCourses.php" id="building">Pick Courses</a>&nbsp;|&nbsp;
							<a href="includes/FacultyHelp.pdf" id="help">Help</a>&nbsp;|&nbsp;
							<a href="logout.php" id="logout">Log Out</a>
							<img src="img/unaLogo.png" alt="UNA Logo" id="logo" />
						</nav>
				');
			}else
			{
				echo('
					<div class="nav">
						<nav>
							<!-- <a href="adminHome.php">UNA </a> -->
							<a href="adminHome.php" id="home">Home</a>
							<a href="manageTimeSlots.php" id="timeSlots">Manage Class Times</a> 
							<a href="manageRooms.php" id="building">Manage Rooms</a>
							<a href="manageClass.php" id="classes">Manage Classes</a>
							<a href="manageFaculty.php" id="faculty">Manage Faculty</a>
							<a href="includes/UserManual.pdf" id="help">Help</a>
							<a href="logout.php" id="logout">Log Out</a>
							<img src="img/unaLogo.png" alt="UNA Logo" id="logo" />
						</nav>
				');
			}
		?>
		<?php 
			global $host, $user, $pass, $db, $port;
			$test = mysqli_connect($host, $user, $pass, $db, $port);
			if($test){
				echo ('<div style="font-size:8pt; color:chartreuse;">DB Connected</div>');
				mysqli_close($test);
			}else{
				echo ('<div style="font-size:8pt; color:red;">DB Failed</div>');
			}
			if (mysqli_connect_errno())
			{
				printf("Connect failed: %s\n", mysqli_connect_error());
				exit();
			}
		?>
	</div>
	<div class="content">
