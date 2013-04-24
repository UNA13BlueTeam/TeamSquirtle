<?php include("includes/header.php"); 
	global $host, $user, $pass, $db, $port;
	$link = mysqli_connect($host, $user, $pass, $db, $port);
?>


<h1>Manage Schedule</h1><br />

<legend style="font-size:30px">Add Scheduled Course</legend>
<div class="purpleBox">
	<form class="inputForm" id="scheduleForm" name="scheduleForm" method="post" action="doAddScheduledCourse.php">
		<input type="hidden" name="flag" value="form">
		<div class="row">
			<label for="course">Course</label>
			<select name="course">
				<?php
					$query = "SELECT * FROM unscheduledCourses WHERE internet = 0";
					$result = mysqli_query($link, $query);
					while($row = mysqli_fetch_assoc($result))
					{
						echo "<option value='".$row['course']."-".$row['section']."'>".$row['course']."-".$row['section']."</option>";
					}
				?>
			</select>
		</div> <br> <hr>
		<div class="row">
			<label for="instructor">Instructor</label>
			<select name="instructor">
				<?php
					$query = "SELECT * FROM users WHERE permission = 2";
					$result = mysqli_query($link, $query);
					while($row = mysqli_fetch_assoc($result))
					{
						echo "<option value='".$row['username']."'>".$row['firstName']." ".$row['lastName']."</option>";
					}
				?>
			</select>
		</div> <br> <hr>
		<div class="row">
			<label for="times">Time Slot</label>
			<select name="times">
				<?php
					$query = "SELECT * FROM timeSlots";
					$result = mysqli_query($link, $query);
					while($row = mysqli_fetch_assoc($result))
					{
						$times = preg_split('/\s+/', trim($row['timesOfDay']));
						for($i = 0; $i < count($times); $i++)
						{
							echo "<option value='".$row['minutes']." ".$row['daysOfWeek']."/"."'>".$row['minutes']." ".$row['daysOfWeek']."/".$times[$i]."</option>";
						}
					}
				?>
			</select>
		</div> <br> <hr>
		<div class="row">
			<label for="rooms">Room</label>
			<select name="rooms">
				<?php
					$query = "SELECT * FROM rooms";
					$result = mysqli_query($link, $query);
					while($row = mysqli_fetch_assoc($result))
					{
						echo "<option value='".$row['roomName']."'>".$row['roomName']."</option>";
					}
				?>
			</select>
		</div> <br> <hr>
		<div class="row">
			<input type="submit" name="submit" value="Submit" />
			<input type="reset" name="submit" value="Reset"  />
		</div>
	</form>
</div>


<legend style="font-size:30px">Add Internet Course</legend>
<div class="purpleBox">
	<form class="inputForm" id="scheduleForm" name="scheduleForm" method="post" action="doAddInternetCourse.php">
		<input type="hidden" name="flag" value="form">
		<div class="row">
			<label for="course">Course</label>
			<select name="course">
				<?php
					$query = "SELECT * FROM unscheduledCourses WHERE internet = 1";
					$result = mysqli_query($link, $query);
					while($row = mysqli_fetch_assoc($result))
					{
						echo "<option value='".$row['course']."-".$row['section']."'>".$row['course']."-".$row['section']."</option>";
					}
				?>
			</select>
		</div> <br> <hr>
		<div class="row">
			<label for="instructor">Instructor</label>
			<select name="instructor">
				<?php
					$query = "SELECT * FROM users WHERE permission = 2";
					$result = mysqli_query($link, $query);
					while($row = mysqli_fetch_assoc($result))
					{
						echo "<option value='".$row['username']."'>".$row['firstName']." ".$row['lastName']."</option>";
					}
				?>
			</select>
		</div> <br> <hr>
		<div class="row">
			<input type="submit" name="submit" value="Submit" />
			<input type="reset" name="submit" value="Reset"  />
		</div>
	</form>
</div>

<form name="RemoveScheduleForm" method="post" action="doRemoveScheduledCourses.php">
<div class="homeSchedule">
	<h4>Schedule</h4>
	<?php
		$scheduledQuery = "SELECT * FROM scheduledCourses ORDER BY course ASC";
 		$results = mysqli_query($link, $scheduledQuery);
 		$scheduled = array();

		echo('<table class="schedule">
            <tr>
				<th>Remove</th>
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
					<td><input type='checkbox' name='check[]' value='".$row['course']."-".$row['section']."'/></td>
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
		<div>
			<input type="submit" name="submit" value="Remove" />
			<input type="reset" name="submit" value="Reset"  />
		</div>
</div>

<?php include("includes/footer.php"); ?>