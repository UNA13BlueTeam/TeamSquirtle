<?php 
	include("includes/header.php");
	include("includes/global.php");
?>

<h1>Schedule</h1>

<div class="homeSchedule">
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
		<a  href="viewPDF.php"><button>View Schedule as PDF</button></a>
</div>

<div class="goldBox">
	<h2>Old Schedules</h2>
	<?php
		echo('<ul>');
		$dir = getcwd()."/generatedFiles/schedules/";
		$files = array();

		$files = scandir($dir);
		array_shift($files);
		array_shift($files);
		foreach($files as $file)
		{
			echo('<li><a href="generatedFiles/schedules/'.$file.'">'.$file.'</a></li>');
		}
		echo('</ul>');
	?>
</div>