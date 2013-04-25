<?php include("includes/facultyHeader.php"); 

	$link = mysqli_connect($host, $user, $pass, $db, $port);
	
	$checkedPrefs = $_POST['check'];
	$currentUser = $_SESSION['username'];
	
	for($i = 0; $i < count($checkedPrefs); $i++)
	{
		$value = preg_split("/\s+/", $checkedPrefs[$i]);
		$delete = "DELETE FROM preferences WHERE courseName = '$value[0]' AND facultyUser = '$currentUser' AND timePref = '$value[1]'";
		mysqli_query($link, $delete);
	}
	
	header("Location: removePreferences.php");
	
include("includes/footer.php");?>