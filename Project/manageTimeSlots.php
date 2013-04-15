<?php include("includes/header.php"); ?>

<h1>Manage Class Time</h1><br />
<a href="addTimeSlot.php"><p style = "font-size:30px">Add Class time</p></a><br />
<hr /><br />
<legend style="font-size:30px">Remove Class Time</legend>
<form name="removeSlot" method="post" action="doRemoveTimeSlots.php" >
	<table class="manage" id="manageTimeSlots">
		<tr>
			<th>Minutes</th>
			<th>Days of Week</th>
			<th>Start Time</th>
		</tr>
		<?php
			$link = mysqli_connect($host, $user, $pass, $db, $port);
				
			$predefMinutes = array();
			$predefQuery = "SELECT minutes FROM timeSlots";
			$predefResult = mysqli_query($link, $predefQuery);
			while($row = mysqli_fetch_row($predefResult))
			{
				array_push($predefMinutes, $row[0]);
			}	
			
			$predefDOW = array();
			$predefQuery = "SELECT daysOfWeek FROM timeSlots";
			$predefResult = mysqli_query($link, $predefQuery);
			while($row = mysqli_fetch_row($predefResult))
			{
				array_push($predefDOW, $row[0]);
			}		
			
			$predefTimes = array();
			$predefQuery = "SELECT timesOfDay FROM timeSlots";
			$predefResult = mysqli_query($link, $predefQuery);
			while($row = mysqli_fetch_row($predefResult))
			{
				array_push($predefTimes, $row[0]);
			}	
			
			for($i = 0; $i < count($predefMinutes); $i++)
			{
				echo "<tr>";
				echo "<td>$predefMinutes[$i]</td>";
				echo "<td>$predefDOW[$i]</td>";
				echo "<td>";
				
				$times = preg_split('/\s+/', trim($predefTimes[$i]));
				
				for($j = 0; $j < count($times); $j++)
				{
					$submittedValue = $predefMinutes[$i]." ".$predefDOW[$i]." ".$times[$j];
					echo "<input type='checkbox' name='check[]' value='$submittedValue'/> $times[$j] ";			
				}
				echo "</td>";
				echo "<tr>";
			}	
		
		?>	
		
	</table>
	<br>
	<div>
		<input type="submit" name="submit" value="Remove" />
		<input type="reset" name="submit" value="Reset"  />
	</div>
</form>

<?php include("includes/footer.php"); ?>