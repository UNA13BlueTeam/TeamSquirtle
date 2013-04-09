<?php include_once("global.php"); ?>
<!-- <?php include_once("php_error.php"); ?>  -->
<?php include_once("db.php"); ?>
<?php error_reporting(E_ERROR); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link rel="stylesheet" href="css/main.css" type="text/css" />
		<link rel="icon" type="image/png" href="img/squirtle.png">
	</head>
	<body>
	<div class="nav">
		<nav>
			<a href="index.php">UNA <?php echo($deptAbbrev);?></a>
			<a href="index.php" id="home">Home</a>
			<a href="manageTimeSlots.php" id="timeSlots">Manage Class Times</a> 
			<a href="manageRooms.php" id="building">Manage Rooms</a>
			<a href="manageClass.php" id="classes">Manage Classes</a>
			<a href="help.php" id="help">Help</a>
			<a href="logout.php" id="logout">Log Out</a>
			<img src="img/unaLogo.png" alt="UNA Logo" id="logo" />
		</nav>
		<?php 
			global $host, $user, $pass, $db, $port;
			$test = mysqli_connect($host, $user, $pass, $db, $port);
			if($test){
				echo ('<div style="font-size:8pt; color:chartreuse;">DB Connected</div>');
				mysqli_close($test);
			}else{
				echo ('<div style="font-size:8pt; color:red;">DB Failed</div>');
			}
		?>
	</div>
		<div class="content">
		
