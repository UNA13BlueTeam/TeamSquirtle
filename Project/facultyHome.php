<?php 
	// session_start();
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
	echo("<h3> Faculty </h3>");
	echo "<h3>".$fetchInfo[1]."</h3>";
?>

<div class="homeSchedule">
	<h4>Schedule</h4>
	<?php
		$facultyUser = $_SESSION['username'];
 		$scheduledQuery = "SELECT * FROM scheduledCourses WHERE facultyUser = '$facultyUser' ORDER BY course ASC";
 		$results = mysqli_query($link, $scheduledQuery);
 		$scheduled = array();
		// $rows = count($scheduled); // define number of rows

		echo('<table class="schedule">
            <tr>
            	<th>Course</th>
            	<th>Time</th>
            	<th>Location</th>
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
				</tr>");
			}
	    ?>
		</table>
</div>
<div class="homeLinks" style="float:left;">
  <h4>Links!</h4>
  <ul>
    <li><a href="facultyHome.php" id="home">Home</a></li>
    <li><a href="viewschedule.php" id="timeSlots">View Schedules</a> </li>
    <li><a href="pickCourses.php" id="pickCourses">Pick Courses</a></li>
	<li><a href="facultyActions.php" id="changePassword">Change Password</a></li>
    <li><a href="includes/FacultyHelp.pdf" id="help">Help</a></li>
  </ul>
</div>
<?php include ('includes/footer.php');?>
