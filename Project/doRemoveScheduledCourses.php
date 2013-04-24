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
			echo "$checkedCourses[$i] <br>";
			$delete = "DELETE FROM $db.scheduledCourses WHERE course = '".trim($tempCourse[0])."' and section = '".trim($tempCourse[1])."'";
			mysqli_query($link, $delete);
			$insert = "INSERT INTO $db.unscheduledCourses (course, section) VALUES ('".trim($tempCourse[0])."', '".trim($tempCourse[1])."')";
			mysqli_query($link, $insert);
		}
	}
	
	header("Location: manageSchedule.php");
	
include("includes/footer.php");?>