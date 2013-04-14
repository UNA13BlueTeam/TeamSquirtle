<?php include("includes/header.php"); 

	$link = mysqli_connect($host, $user, $pass, $db, $port);
	
	$checkedTimeSlots = $_POST['check'];
	if(empty($checkedTimeSlots))
	{
		echo("You didn't select any rooms.");
	}
	else
	{
		echo("These times were deleted: <br>");
		for($i = 0; $i < count($checkedTimeSlots); $i++)
		{
			$timeRemoved = "";
			$times = "";
			$newValue = array();
			
			echo "$checkedTimeSlots[$i] <br>";
			$newValue = explode(" ", $checkedTimeSlots[$i]);
			echo "$newValue[0] <br>";
			echo "$newValue[1] <br>";
			echo "$newValue[2] <br>";

			$query = "SELECT DISTINCT timesOfDay FROM $db.timeSlots WHERE minutes = '$newValue[0]' AND daysOfWeek = '$newValue[1]'";
			$result = mysqli_query($link, $query);
			$row = mysqli_fetch_row($result);
			$times = $row[0];
			echo "$times <br>";
			
			
			// PATTERN MATCHING ISSUE HERE
			$timeRemoved = substr_replace($times, " ", $newValue[2], 6);
			echo "$timeRemoved <br>";
			
			$delete = "DELETE FROM $db.timeSlots WHERE minutes = '$newValue[0]' AND daysOfWeek = '$newValue[1]'";
			mysqli_query($link, $delete);
			
			$query = "INSERT INTO $db.timeSlots (minutes, daysOfWeek, timesOfDay) VALUES ('$newValue[0]', '$newValue[1]', '$timeRemoved')";
			mysqli_query($link, $query);
		}
	}
	
include("includes/footer.php");?>