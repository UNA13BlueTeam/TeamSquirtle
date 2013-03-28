<?php include("includes/header.php");

	// Get variables from input form
	$type = $_POST['roomType'];
	$size = $_POST['size'];
	$name = $_POST['roomName'];
	
	// Print out contents accepted
	echo "You have successfully added this course information to the database! <br>";
	echo "Room Type: $type <br>";
	echo "Room Size: $size <br>";
	echo "Room Name: $name <br>";






include("includes/footer.php");
?>