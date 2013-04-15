<?php include("includes/header.php"); 

	$link = mysqli_connect($host, $user, $pass, $db, $port);
	
	$checkedFaculty = $_POST['check'];
	if(empty($checkedFaculty))
	{
		echo("You didn't select any rooms.");
	}
	else
	{
		echo("These rooms were deleted: <br>");
		for($i = 0; $i < count($checkedFaculty); $i++)
		{
		  echo "$checkedFaculty[$i] <br>";
		  $delete = "DELETE FROM faculty WHERE email = '$checkedFaculty[$i]'";
		  mysqli_query($link, $delete);
		}
	}
	
include("includes/footer.php");?>