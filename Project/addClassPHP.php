<?php include("includes/header.php");
	$link = mysqli_connect($host, $user, $pass, $db, $port);
	// Get variables from input form
	$courseName = $_POST['course'];
	$dsection = $_POST['dsection'];
	$nsection = $_POST['nsection'];
	$isection = $_POST['isection'];
	$classSize = $_POST['classSize'];
	$roomType = $_POST['roomType'];
	$hours = $_POST['hours'];
	$prereq = $_POST['prereq'];
	$conflict = $_POST['conflict'];
	
	$query = "INSERT INTO courses $courseName, $dsection, $nsection, $isection, $classSize, $roomType, $hours";

	mysqli_query($link, $query);
	// Print out contents accepted
	echo "You have successfully added this course information to the database! <br>";
	echo "Course Name: $courseName <br>";
	echo "Day Sections: $dsection <br>";
	echo "Night Sections: $nsection <br>";
	echo "Internet Sections: $isection <br>";
	echo "Class Size: $classSize <br>";
	echo "Room Type: $roomType <br>";
	echo "Hours: $hours <br>";
	echo "Prerequisites: $prereq <br>";
	echo "Conflicts: $conflict <br>";

	mysqli_close($link);
include("includes/footer.php");
?>