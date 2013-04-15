<?php include("includes/facultyHeader.php");
	include("includes/db.php");
	session_start();
	
	$link = mysqli_connect($host, $user, $pass, $db, $port);
    if(!$link)
	{
        die('cannot connect database'. mysqli_error($link));
    }
	
	// Get variables from input form
	$courseName = $_POST['course'];
	$timePref = $_POST['time'];
	$tos = date('YmdHi');
	
	$facultyUser = $_SESSION['username'];
	
	$retrieval = "SELECT yos FROM faculty WHERE email = '$facultyUser'";
	$results = mysqli_query($link, $retrieval);
	$row = mysqli_fetch_row($results);
	$yos = $row[0];
	
	echo "$facultyUser <br>";
	echo "$tos <br>";
	echo "$yos[0] <br>";
	echo "$courseName <br>";
	echo "$timePref <br>";
	
	// submit to query
	$insertQuery = "INSERT INTO $db.preferences (facultyUser, timePref, yos, tos, courseName) VALUES ('$facultyUser', '$timePref', '$yos', '$tos', '$courseName')";
	echo "$insertQuery";
	$insertion = mysqli_query($link, $insertQuery);
	
	if($insertion)
	{
		echo("insertion succeeded<br>");
	}
	else
	{
		echo("insertion failed<br>");
		echo($insertQuery."<br>");
	}
	
	
include("includes/footer.php"); ?>