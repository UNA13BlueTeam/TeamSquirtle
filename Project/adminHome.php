<?php include("includes/header.php"); ?>

<?php
	global $deptAbbrev, $deptName, $userName, $userTitle, $userInfo;
	echo($userName);
	echo($userInfo['lastName']);
	echo("<h1>".$deptName."</h1>");
	echo("<h2>".$_SESSION['firstName']." ".$_SESSION['lastName']."</h2>");
?>
<div class="homeLinks">
	<h4>Links!</h4>
	<ul>
		<li> <a href="manageTimeSlots.php" id="timeSlots">Manage Class Times</a> </li>
		<li> <a href="manageRooms.php" id="building">Manage Rooms</a> </li>
		<li> <a href="manageClass.php" id="classes">Manage Classes</a> </li>
		<li> <a href="manageConflicts.php" id="conflicts">Conflicts</a> </li>
		<li> <a href="adminHome.php" id="deadline">Change Deadline</a></li>
		<li> <a href="help.php" id="deadline">Help</a></li>
	</ul>
</div>
<div id="homeSchedule">
	<h4>Schedule</h4>
	<?php
		$rows = 6; // define number of rows
		$cols = 2;// define number of columns
 
		echo "<table class='schedule'>";
                echo"<tr>";
                for($th=1;$th<=$cols;$th++){
                    echo"<th></th>";
                }
 
		for($tr=2;$tr<=$rows;$tr++)
		{ 
   			echo "<tr>"; 
        	for($td=1;$td<=$cols;$td++)
        	{ 
                echo "<td></td>"; 
        	} 
  		echo "</tr>"; 
		} 

		echo "</table>";
	
        ?>        
</div>
<?php include("includes/footer.php"); ?>
