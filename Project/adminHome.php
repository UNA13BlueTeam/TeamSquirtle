<?php 
	include("includes/header.php");
	include_once("includes/global.php");
?>

<?php
	global $host, $user, $pass, $db, $port, $deptName;
 	$link = mysqli_connect($host, $user, $pass, $db, $port);
	
	// Gets the department name and semester name from the database
	$query = "SELECT deptName, semesterName FROM users WHERE username = 'admin'";
	$result = mysqli_query($link, $query);
	$deptInfo = mysqli_fetch_row($result);
	
	echo("<h1>".$deptName."</h1>");
	echo ("<h2>".$_SESSION['firstname']." ".$_SESSION['lastname']."</h2>");
	echo("<h3> Admin </h3>");
	echo "<h3>".$deptInfo[1]."</h3>";
?>
<div class="homeSchedule">
	<h4>Schedule</h4> 
	<a href="facultySchedule.php"><button>Faculty View</button></a>
	<a href="roomSchedule.php"><button>Room View</button></a>	
	<a href="scheduleHome.php"><button>Student View</button></a>
	<?php
 		$scheduledQuery = "SELECT * FROM scheduledCourses ORDER BY course ASC, section ASC";
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
				$getName = "SELECT lastName, firstName FROM users WHERE username = '".$adminUser."'";
				$resultGetName = mysqli_query($link, $getName);
				$name = mysqli_fetch_assoc($resultGetName);
					
				echo("
					<td>".$name['firstName']." ".$name['lastName']."</td>
				</tr>
				");
			}
	    ?>
		</table>
		<br>
		<a href="viewPDF.php"><button>View Schedule as PDF</button></a> <a href="preSchedulingPage.php"><button>Run Scheduling Algorithm</button></a>
</div>
<div class="homeLinks" style="float:left;">
	<h4>Links!</h4>
		<ul>
			<li> <a href="manageTimeSlots.php" id="timeSlots">Manage Class Times</a> </li>
			<li> <a href="manageRooms.php" id="building">Manage Rooms</a> </li>
			<li> <a href="manageClass.php" id="classes">Manage Classes</a> </li>
			<li> <a href="manageFaculty.php" id="faculty">Manage Faculty</a> </li>
			<li> <a href="preSchedulingPage.php" id="schedule">Schedule Courses!</a> </li>
			<li> <a href="manageSchedule.php" id="deadline">Manage Scheduled Courses</a></li>
            <li> <a href="viewPDF.php" id="deadline">View and Download Schedule in PDF</a></li>
			<li> <a href="includes/UserManual.pdf" id="deadline">Help</a></li>
		</ul>
</div>
<?php include("includes/footer.php"); ?>
