<?php include("includes/header.php"); ?>

<h1>Manage Classes</h1><br />
<a href="addClass.php"><p style = "font-size:30px">Add Course</p></a><br />
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
			
			$NUMBEROFPREREQS = 3;
				
			$predefClasses = array();
			$predefQuery = "SELECT courseName FROM courses";
			$predefResult = mysqli_query($link, $predefQuery);
			while($row = mysqli_fetch_row($predefResult))
			{
				array_push($predefClasses, $row[0]);
			}	
			
			$predefDaySect = array();
			$predefQuery = "SELECT dsection FROM courses";
			$predefResult = mysqli_query($link, $predefQuery);
			while($row = mysqli_fetch_row($predefResult))
			{
				array_push($predefDaySect, $row[0]);
			}			
				
			$predefNightSect = array();
			$predefQuery = "SELECT nsection FROM courses";
			$predefResult = mysqli_query($link, $predefQuery);
			while($row = mysqli_fetch_row($predefResult))
			{
				array_push($predefNightSect, $row[0]);
			}
			
			$predefInternetSect = array();
			$predefQuery = "SELECT isection FROM courses";
			$predefResult = mysqli_query($link, $predefQuery);
			while($row = mysqli_fetch_row($predefResult))
			{
				array_push($predefInternetSect, $row[0]);
			}
			
			$predefSize = array();
			$predefQuery = "SELECT classSize FROM courses";
			$predefResult = mysqli_query($link, $predefQuery);
			while($row = mysqli_fetch_row($predefResult))
			{
				array_push($predefSize, $row[0]);
			}	
			
			$predefRoomType = array();
			$predefQuery = "SELECT roomType FROM courses";
			$predefResult = mysqli_query($link, $predefQuery);
			while($row = mysqli_fetch_row($predefResult))
			{
				array_push($predefRoomType, $row[0]);
			}	
			
			$predefHours = array();
			$predefQuery = "SELECT hours FROM courses";
			$predefResult = mysqli_query($link, $predefQuery);
			while($row = mysqli_fetch_row($predefResult))
			{
				array_push($predefHours, $row[0]);
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
				echo "<td>$predefHours[$i]</td>";
				/*
				echo "<td>";
				for($j = 0; $j < $NUMBEROFPREREQS; $j++)
				{
					$query = "SELECT prereq$i FROM conflicts WHERE course = '$predefClasses[$i]'";
					$predefResult = mysqli_query($link, $query);
					$row = mysqli_fetch_row($predefResult);
					echo "$row[0]";
				}
				echo "</td>";
				*/
					
				// Get conflict times from the database
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