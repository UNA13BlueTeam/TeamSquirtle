<?php 
	include("includes/header.php"); 
?>

<?php
	global $host, $user, $pass, $db, $port;
 	$link = mysqli_connect($host, $user, $pass, $db, $port);
	
	// Gets the department name and semester name from the database
	$query = "SELECT deptName, semesterName FROM users WHERE username = 'admin'";
	$result = mysqli_query($link, $query);
	$fetchInfo = mysqli_fetch_row($result);
	
	echo("<h1>".$fetchInfo[0]."</h1>");
	echo ("<h2>".$_SESSION['firstname']." ".$_SESSION['lastname']."</h2>");
	echo("<h3> Admin </h3>");
	echo "<h3>".$fetchInfo[1]."</h3>";
?>
<div class="homeLinks">
	<h4>Links!</h4>
		<ul>
			<li> <a href="manageTimeSlots.php" id="timeSlots">Manage Class Times</a> </li>
			<li> <a href="manageRooms.php" id="building">Manage Rooms</a> </li>
			<li> <a href="manageClass.php" id="classes">Manage Classes</a> </li>
			<li> <a href="manageFaculty.php" id="faculty">Manage Faculty</a> </li>
			<li> <a href="preSchedulingPage.php" id="schedule">Schedule Courses!</a> </li>
			<li> <a href="adminHome.php" id="deadline">Change Deadline</a></li>
            <li> <a href="viewPDF.php" id="pdf">View and Download Schedule in PDF</a></li>
			<li> <a href="help.php" id="deadline">Help</a></li>
		</ul>
</div>
<div id="homeSchedule">
	<h4>Schedule</h4>
	<?php
 		$scheduledQuery = "SELECT * FROM scheduledCourses ORDER BY course ASC";
 		$results = mysqli_query($link, $scheduledQuery);
 		$scheduled = array();

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
					");
				$adminUser = $row['facultyUser'];
				$getName = "SELECT lastName, firstName FROM users WHERE username = '$adminUser'";
				$resultGetName = mysqli_query($link, $getName);
				$name = mysqli_fetch_assoc($resultGetName);
					
				echo("
					<td>".$name['firstName']." ".$name['lastName']."</td>
				</tr>
				");
			}
	    ?>
		</table>
</div>
<?php include("includes/footer.php"); ?>
