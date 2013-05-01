<?php include("includes/header.php"); ?>

<h1>Manage Classes</h1><br />
<hr /><br />
<legend style="font-size:30px">Remove Course</legend>
<form name="removescheduleForm" method="post" action="doRemoveCourses.php">
	<table class="manage" id="manageClass">
		<tr height="30%">
			<th>Course Name</th>
			<th>Days Sections</th>
			<th>Night Sections</th>
			<th>Internet Sections</th>
			<th>Class Size</th>
			<th>Room</th>
			<th>Hours</th>
			<th>Prerequisites</th>
			<th>Conflict Times</th>
		</tr>
		
		<?php
			$link = mysqli_connect($host, $user, $pass, $db, $port);
			set_time_limit(0);
			
			$NUMBEROFPREREQS = 3;
				
			$predefClasses = array();
			$predefDaySect = array();
			$predefNightSect = array();
			$predefInternetSect = array();
			$predefSize = array();
			$predefRoomType = array();
			$predefHours = array();
			
			$predefQuery = "SELECT courseName, dsection, nsection, isection, classSize, roomType, hours FROM courses ORDER BY courseName ASC";
			$predefResult = mysqli_query($link, $predefQuery);
			while($row = mysqli_fetch_row($predefResult))
			{
				array_push($predefClasses, $row[0]);
				array_push($predefDaySect, $row[1]);
				array_push($predefNightSect, $row[2]);
				array_push($predefInternetSect, $row[3]);
				array_push($predefSize, $row[4]);
				array_push($predefRoomType, $row[5]);
				array_push($predefHours, $row[6]);				
			}	
			
			
			for($i = 0; $i < count($predefClasses); $i++)
			{				
				echo "<tr>";
				echo "<td><input type='checkbox' name='check[]' value='$predefClasses[$i]'/> $predefClasses[$i]</td>";
				echo "<td>$predefDaySect[$i]</td>";
				echo "<td>$predefNightSect[$i]</td>";
				echo "<td>$predefInternetSect[$i]</td>";
				echo "<td>$predefSize[$i]</td>";
				echo "<td>$predefRoomType[$i]</td>";
				echo "<td>$predefHours[$i]</td>";
				
				//Get prereqs from the database per course
				echo "<td>";
				$query = "SELECT prereq1, prereq2, prereq3 FROM prereqs WHERE course = '$predefClasses[$i]'";
				$predefResult = mysqli_query($link, $query);
				$row = mysqli_fetch_row($predefResult);
				echo "$row[0] $row[1] $row[2]";
				echo "</td>";
				
					
				// Get conflict times from the database per course
				$query = "SELECT times FROM conflicts WHERE course = '$predefClasses[$i]'";
				$predefResult = mysqli_query($link, $query);
				$row = mysqli_fetch_row($predefResult);
				echo "<td>$row[0]</td>";
				
				echo "<tr>";
			}	
		?>
	</table>
	<br>
	<div>
		<input type="submit" name="submit" value="Remove" />
		<input type="reset" name="reset" value="Reset"  />
	</div>
</form>

<?php include("includes/footer.php"); ?>