<?php include("includes/header.php"); 

	$link = mysqli_connect($host, $user, $pass, $db, $port);
	
	$course = $_POST['course'];
	$instructor = $_POST['instructor'];
	
	$tempCourse = preg_split('/[-]/', $course);
	echo "$course   $instructor <br>";
	$insert = "INSERT INTO $db.scheduledCourses (course, section, timeSlot, facultyUser, roomName) VALUES ('".trim($tempCourse[0])."', '".trim($tempCourse[1])."', 'NA', '$instructor', 'INTERNET')";
	$insertion = mysqli_query($link, $insert);
	
	if($insertion)
	{
		$delete = "DELETE FROM $db.unscheduledCourses WHERE course = '".trim($tempCourse[0])."' and section = '".trim($tempCourse[1])."'";
		mysqli_query($link, $delete);
	}
	//header("Location: manageSchedule.php");
	
include("includes/footer.php");?>