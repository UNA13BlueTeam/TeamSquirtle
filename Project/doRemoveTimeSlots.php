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
			$timeRemoved = str_replace($newValue[2], "", $times);
			echo "$timeRemoved <br>";
			
			if(strlen(trim($timeRemoved)) != 0)
			{
				$query = "UPDATE timeSlots SET timesOfDay = '$timeRemoved' WHERE minutes = '$newValue[0]' AND daysOfWeek = '$newValue[1]'";
				mysqli_query($link, $query);
			}
			else
			{
				$delete = "DELETE FROM $db.timeSlots WHERE minutes = '$newValue[0]' AND daysOfWeek = '$newValue[1]'";
				mysqli_query($link, $delete);
			}
		}
	}
	header("Location: manageTimeSlots.php");
	
include("includes/footer.php");?>