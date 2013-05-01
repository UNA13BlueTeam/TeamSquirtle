<?php include("includes/header.php"); 

	$link = mysqli_connect($host, $user, $pass, $db, $port);
	
	$checkedCourses = $_POST['check'];
	if(empty($checkedCourses))
	{
		echo("You didn't select any courses.");
	}
	else
	{
		echo("These courses were deleted: <br>");
		for($i = 0; $i < count($checkedCourses); $i++)
		{
			$tempCourse = preg_split('/[-]/', $checkedCourses[$i]);
			// Check to see whether this is an internet course or not
			$checker = "SELECT * FROM scheduledCourses WHERE course = '".trim($tempCourse[0])."' and section = '".trim($tempCourse[1])."'";
			$result = mysqli_query($link, $checker);
			$row = mysqli_fetch_assoc($link, $result);
			
			// Insert into unscheduled courses 	
			if($row['roomName'] == "INTERNET")
			{
				$insert = "INSERT INTO $db.unscheduledCourses (course, section, internet) VALUES ('".trim($tempCourse[0])."', '".trim($tempCourse[1])."', '1')";
			}
			else
			{
				$insert = "INSERT INTO $db.unscheduledCourses (course, section, internet) VALUES ('".trim($tempCourse[0])."', '".trim($tempCourse[1])."', '0')";
			}
			// Delete from scheduled courses
			echo "$checkedCourses[$i] <br>";
			$delete = "DELETE FROM $db.scheduledCourses WHERE course = '".trim($tempCourse[0])."' and section = '".trim($tempCourse[1])."'";
			mysqli_query($link, $delete);
			
			mysqli_query($link, $insert);
		}
	}
	
	header("Location: manageSchedule.php");
	
include("includes/footer.php");?>