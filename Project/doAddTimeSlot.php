<?php include("includes/header.php");

	// Get variables from input form
	$minutes = $_POST['minutes'];
	$mon = $_POST['Mon'];
	$tue = $_POST['Tue'];
	$wed = $_POST['Wed'];
	$thu = $_POST['Thu'];
	$fri = $_POST['Fri'];
	$sat = $_POST['Sat'];
	$startTime = $_POST['startTime'];
	
	//$days = ARRAY(0 => $mon, 1 => $tue, 2 => $wed);
	
	
	// Print out contents accepted
	echo "You have successfully added this course information to the database! <br>";
	echo "Room Type: $minutes <br>";
	echo "Days of the week: $mon  $tue  $wed  $thu  $fri  $sat <br>";
	echo "Start Times: $startTime <br>";
	
	
	

include("includes/footer.php");
?>