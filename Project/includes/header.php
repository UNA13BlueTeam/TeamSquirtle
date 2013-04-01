<?php include_once("global.php"); ?>
<?php include_once("db.php"); ?>
<?php error_reporting(E_ALL); ?>

<html>
	<head>
		<link rel="stylesheet" href="css/main.css" type="text/css" />
		<link rel="icon" type="image/png" href="img/squirtle.png">
	</head>
	<body>
	<div class="nav">
		<nav>
			<a href="index.php">UNA-Dept. Name</a>
			<a href="index.php" id="home">Home</a>
			<a href="timeSlots.php" id="timeSlots">Manage Class Times</a> 
			<a href="manageRooms.php" id="building">Manage Rooms</a>
			<a href="manageClass.php" id="classes">Manage Classes</a>
			<a href="conflicts.php" id="conflicts">Conflicts</a>
			<a href="help.php" id="help">Help</a>
			<a href="logout.php" id="logout">Log Out</a>
			<img src="img/unaLogo.png" alt="UNA Logo" id="logo" />
		</nav>
	</div>
		<div class="content">
		