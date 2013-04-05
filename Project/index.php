<?php include("includes/header.php");
	  include_once("includes/db.php");?>

<?php
	echo("<h1>".$deptName."</h1>");
	echo("<h2>".$userName."</h2>");
	echo("<h3>".$userTitle."</h3>");
	$test = mysqli_connect($host, $user, $pass, $db, $port);
	if($test){
		echo ("DB Connected!");
	}else{
		echo ("DB Failed.");
	}
	mysqli_close($test);
?>

<div class="homeSchedule">
	Schedule!
</div>
<div class="homeLinks">
	Links!
	<ul>
		<li> <a href="timeSlots.php" id="timeSlots">Manage Class Times</a> </li>
		<li> <a href="manageRooms.php" id="building">Manage Rooms</a> </li>
		<li> <a href="manageClass.php" id="classes">Manage Classes</a> </li>
		<li> <a href="conflicts.php" id="conflicts">Conflicts</a> </li>
		<li> <a href="index.php" id="deadline">Change Deadline</a></li>
		<li> <a href="index.php" id="deadline">Help</a></li>
	</ul>
</div>
<br style="clear:both;">
<?php include("includes/footer.php"); ?>