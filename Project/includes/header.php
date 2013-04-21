<?php 
	session_start();
	include_once("php_error.php");
	\php_error\reportErrors();
 	include_once("db.php");
	include_once("global.php");
	// error_reporting(E_ERROR);
	date_default_timezone_set('America/Chicago');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link rel="stylesheet" href="css/main.css" type="text/css" />
		<link rel="icon" type="image/png" href="img/squirtle.png">
	</head>
	<body>
		<?php global $deptAbbrev;
			$auth = $_SESSION['permission'];
			if($auth == 1)
			{
				echo('
					<div class="nav">
						<nav>
							<a href="adminHome.php">UNA '.$deptAbbrev.'</a>
							<a href="adminHome.php" id="home">Home</a>
							<a href="manageTimeSlots.php" id="timeSlots">Manage Class Times</a> 
							<a href="manageRooms.php" id="building">Manage Rooms</a>
							<a href="manageClass.php" id="classes">Manage Classes</a>
							<a href="manageFaculty.php" id="faculty">Manage Faculty</a>
							<a href="help.php" id="help">Help</a>
							<a href="logout.php" id="logout">Log Out</a>
							<img src="img/unaLogo.png" alt="UNA Logo" id="logo" />
						</nav>
				');
			}elseif($auth == 2)
			{
				echo('
					<div class="nav">
						<nav>
							<a href="facultyHome.php">UNA '.$deptAbbrev.'</a>
							<a href="facultyHome.php" id="home">Home</a>
							<a href="viewschedule.php" id="timeSlots">View Schedules</a> 
							<a href="Pickcourses.php" id="building">Pick Courses</a>
							<a href="facultyHelp.php" id="help">Help</a>
							<a href="logout.php" id="logout">Log Out</a>
							<img src="img/unaLogo.png" alt="UNA Logo" id="logo" />
						</nav>
				');
			}else
			{
				echo('
					<div class="nav">
						<nav>
							<a href="adminHome.php">UNA </a>
							<a href="adminHome.php" id="home">Home</a>
							<a href="manageTimeSlots.php" id="timeSlots">Manage Class Times</a> 
							<a href="manageRooms.php" id="building">Manage Rooms</a>
							<a href="manageClass.php" id="classes">Manage Classes</a>
							<a href="manageFaculty.php" id="faculty">Manage Faculty</a>
							<a href="help.php" id="help">Help</a>
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
