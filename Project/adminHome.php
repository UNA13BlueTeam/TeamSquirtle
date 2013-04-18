<?php 
	// session_start();
	include("includes/header.php"); 
?>

<?php
	global $deptAbbrev, $deptName, $userName, $userTitle, $userInfo;
	echo("<h1>".$deptName."</h1>");
	//echo("<h2>".$userInfo['firstName']." ".$userInfo['lastName']."</h2>");
	echo ("<h2>".$_SESSION['firstname']." ".$_SESSION['lastname']."</h2>");
	echo("<h3> Admin </h3>");
?>
<div class="homeLinks">
	<h4>Links!</h4>
		<ul>
			<li> <a href="manageTimeSlots.php" id="timeSlots">Manage Class Times</a> </li>
			<li> <a href="manageRooms.php" id="building">Manage Rooms</a> </li>
			<li> <a href="manageClass.php" id="classes">Manage Classes</a> </li>
			<li> <a href="manageFaculty.php" id="faculty">Manage Faculty</a> </li>
			<li> <a href="manageConflicts.php" id="conflicts">Schedule Courses!</a> </li>
			<li> <a href="adminHome.php" id="deadline">Change Deadline</a></li>
			<li> <a href="help.php" id="deadline">Help</a></li>
		</ul>
</div>
<div id="homeSchedule">
	<h4>Schedule</h4>
	<?php
 		
 		global $host, $user, $pass, $db, $port;
 		$link = mysqli_connect($host, $user, $pass, $db, $port);
 		$scheduledQuery = "SELECT * FROM scheduledCourses";
 		$results = mysqli_query($link, $scheduledQuery);
 		$scheduled = array();
 		$cols = 4;
		// $rows = count($scheduled); // define number of rows

		echo('<table class="schedule">
            <tr>
            	<th>Course</th>
            	<th>Time</th>
            	<th>Location</th>
            	<th>Instructor</th>
            </tr>
			');
			// $row = mysqli_fetch_row($results, MYSQLI_BOTH);
		echo("<tr></tr>");
			while($row = mysqli_fetch_assoc($results))
			{
				echo("
				<tr>
					<td>".$row['course']."-".$row['section']."</td>
					<td>".$row['timeSlot']."</td>
					<td>".$row['roomName']."</td>
					<td>".$row['facultyUser']."</td>
				</tr>
				");
				// $row = mysqli_fetch_row($results, MYSQLI_BOTH);
			}
	    ?>
		</table>
</div>
<?php include("includes/footer.php"); ?>
