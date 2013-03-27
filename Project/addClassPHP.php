<?php include("header.php");

	// Get variables from input form
	$courseName = $_POST['course'];
	$dsection = $_POST['dsection'];
	$nsection = $_POST['nsection'];
	$isection = $_POST['isection'];
	$classSize = $_POST['size'];
	$roomType = $_POST['type'];
	$hours = $_POST['hours'];
	$prereq = $_POST['prereq'];
	$conflict = $_POST['conflict'];
	
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

include("footer.php");
?>