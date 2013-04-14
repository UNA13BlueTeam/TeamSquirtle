<?php include("includes/header.php"); 

	$link = mysqli_connect($host, $user, $pass, $db, $port);
	
	$checkedCourses = $_POST['check'];
	if(empty($checkedCourses))
	{
		echo("You didn't select any rooms.");
	}
	else
	{
		echo("These courses were deleted: <br>");
		for($i = 0; $i < count($checkedCourses); $i++)
		{
			echo "$checkedCourses[$i] <br>";
			$delete = "DELETE FROM courses WHERE courseName = '$checkedCourses[$i]'";
			mysqli_query($link, $delete);

			$query = "SELECT times FROM conflicts WHERE course = '$checkedCourses[$i]'";
			$predefResult = mysqli_query($link, $query);
			if($predefResult)
			{
				$delete = "DELETE FROM conflicts WHERE course = '$checkedCourses[$i]'";
				mysqli_query($link, $delete);
			}
		}
	}
	
include("includes/footer.php");?>