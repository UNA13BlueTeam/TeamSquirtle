<?php include("includes/header.php"); 

	$link = mysqli_connect($host, $user, $pass, $db, $port);
	
	$checkedRooms = $_POST['check'];
	if(empty($checkedRooms))
	{
		echo("You didn't select any rooms.");
	}
	else
	{
		echo("These rooms were deleted: <br>");
		for($i = 0; $i < count($checkedRooms); $i++)
		{
			echo "$checkedRooms[$i] <br>";
			$delete = "DELETE FROM $db.rooms WHERE roomName = '$checkedRooms[$i]'";
			mysqli_query($link, $delete);
		}
	}
	header("Location: manageRooms.php");
	
include("includes/footer.php");?>