<?php 
  include("includes/header.php"); 
?>

<?php
	global $host, $user, $pass, $db, $port;
 	$link = mysqli_connect($host, $user, $pass, $db, $port);
	
	// Gets the department name and semester name from the database
	$query = "SELECT deptName, semesterName FROM users WHERE username = 'admin'";
	$result = mysqli_query($link, $query);
	$deptInfo = mysqli_fetch_row($result);
	
	echo("<h1>".$deptInfo[0]."</h1>");
	echo ("<h2>".$_SESSION['firstname']." ".$_SESSION['lastname']."</h2>");
	echo("<h3> Admin </h3>");
	echo "<h3>".$deptInfo[1]."</h3>";
?>
<div class="homeSchedule">
	<h4>Schedule</h4> 
	<a href="adminHome.php"><button>Admin View</button></a>
	<a href="roomSchedule.php"><button>Room View</button></a>
	<a href="scheduleHome.php"><button>Student View</button></a>
	<?php
 		$roomQuery = "SELECT * FROM users where permission = '2'";
 		$results = mysqli_query($link, $roomQuery);

		echo('<table class="schedule">
            <tr>
            	<th>Faculty</th>
            	<th>Information</th>
            </tr>
                ');
			// $row = mysqli_fetch_row($results, MYSQLI_BOTH);
		echo("<tr></tr>");
			while($row = mysqli_fetch_assoc($results))
			{
				echo("
				<tr>
					<td>".$row['firstName']." ".$row['lastName']."</td>
					<td></td>
					");
                                $courseQuery = "SELECT * FROM scheduledCourses WHERE facultyUser = '".$row['username']."'";
                                $subresults = mysqli_query($link, $courseQuery);
                                while($subrow = mysqli_fetch_assoc($subresults)){
                                    echo("
                                        <tr>
                                            <td></td>
                                            <td>".$subrow['course']."-".$subrow['section']."&nbsp;&nbsp;&nbsp;"
                                            .$subrow['timeSlot']."&nbsp;&nbsp;&nbsp;".$subrow['roomName']."</td>
                                        ");
                                
                                }
			}
	    ?>
		</table>
		<br>
		<button href="viewPDF.php">View Schedule as PDF</button> <button href="preSchedulingPage.php">Run Scheduling Algorithm</button>
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
			<li> <a href="help.php" id="deadline">Help</a></li>
		</ul>
</div>
<?php include("includes/footer.php"); ?>
