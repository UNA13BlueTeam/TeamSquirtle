<?php
	// session_start();
    include_once("db.php");
	//include_once("php_error.php");
	// error_reporting(E_ERROR);

#################################

	global $host, $user, $pass, $db, $port;
	$link = mysqli_connect($host, $user, $pass, $db, $port);

	$un = $_SESSION["username"];
	$infoQuery = "SELECT * FROM users WHERE username = '$un'";
	$infoResults = mysqli_query($link, $infoQuery);
	$userInfo = mysqli_fetch_assoc($infoResults);
	$deptName 	= $userInfo['deptName'];
	$semesterName = $userInfo['semesterName'];
	$deadline = $userInfo['deadline'];

################################################################
// Table Clearing Functions
################################################################

	function clearFaculty()
	{
		global $link;
		$query = "TRUNCATE TABLE faculty";
		$delete = "DELETE FROM users WHERE permission != 1";
		mysqli_query($link, $query);
		mysqli_query($link, $delete);
	}

	function clearClasses()
	{
		global $link;
		$query = "TRUNCATE TABLE courses";
		mysqli_query($link, $query);
	}

	function clearClassTimes()
	{
		global $link;
		$query = "TRUNCATE TABLE timeSlots";
		mysqli_query($link, $query);
	}

	function clearRooms()
	{
		global $link;
		$query = "TRUNCATE TABLE rooms";
		mysqli_query($link, $query);
	}

	function clearPrereqs()
	{
		global $link;
		$query = "TRUNCATE TABLE prereqs";
		mysqli_query($link, $query);
	}

	function clearPrefs()
	{
		global $link;
		$query = "TRUNCATE TABLE preferences";
		mysqli_query($link, $query);
	}

	function clearConflicts()
	{
		global $link;
		$query = "TRUNCATE TABLE conflicts";
		mysqli_query($link, $query);
	}

	function clearSchedule()
	{
		global $link;
		$query = "TRUNCATE TABLE scheduledCourses";
		mysqli_query($link, $query);
	}

################################################################
// File Generators
################################################################	

	function generateFaculty()
	{
		global $link;
		$outFile = fopen("generatedFiles/faculty.txt", "w");
		$query = "SELECT * FROM faculty";
		$results = mysqli_query($link, $query);
		while($row = mysqli_fetch_assoc($results))
		{
			$output = $row['facultyName']." ".$row['yos']." ".$row['email']."@UNA.EDU ".$row['minHours']."\r\n";
			fwrite($outFile, $output);
		}
		fclose($outFile);
	}

	function generateClasses()
	{
		global $link;
		$outFile = fopen("generatedFiles/courses.txt", "w");
		$query = "SELECT * FROM courses";
		$results = mysqli_query($link, $query);
		while($row = mysqli_fetch_assoc($results))
		{
			$output = $row['courseName']." ".$row['dsection']." ".$row['nsection']." ".$row['isection']." ".$row['classSize']." ".$row['roomType']." ".$row['hours']."\r\n";
			fwrite($outFile, $output);
		}
		fclose($outFile);
	}

	function generateClassTimes()
	{
		global $link;
		echo(dirname("."));
		$outFile = fopen("generatedFiles/classTimes.txt", "w");
		$query = "SELECT * FROM timeSlots";
		$results = mysqli_query($link, $query);
		while($row = mysqli_fetch_assoc($results))
		{
			$output = $row['minutes']." ".$row['daysOfWeek']."/".$row['timesOfDay']."\r\n";
			fwrite($outFile, $output);
		}
		fclose($outFile);
	}

	function generateRooms()
	{
		global $link;
		$outFile = fopen("generatedFiles/rooms.txt", "w");
		$query = "SELECT * FROM rooms";
		$results = mysqli_query($link, $query);
		while($row = mysqli_fetch_assoc($results))
		{
			$output = $row['roomType']." ".$row['size']." ".$row['roomName']."\r\n";
			fwrite($outFile, $output);
		}
		fclose($outFile);
	}

	function generatePrereqs()
	{
		global $link;
		$outFile = fopen("generatedFiles/prereqs.txt", "w");
		$query = "SELECT * FROM prereqs";
		$results = mysqli_query($link, $query);
		while($row = mysqli_fetch_assoc($results))
		{
			$output = $row['course']." ".$row['prereq1']." ".$row['prereq2']." ".$row['prereq3']."\r\n";
			fwrite($outFile, $output);
		}
		fclose($outFile);
	}

	function generateConflicts()
	{
		global $link;
		$outFile = fopen("generatedFiles/conflicts.txt", "w");
		$query = "SELECT * FROM conflicts";
		$results = mysqli_query($link, $query);
		while($row = mysqli_fetch_assoc($results))
		{
			$output = $row['course']." ".$row['times']."\r\n";
			fwrite($outFile, $output);
		}
		fclose($outFile);
	}

?>