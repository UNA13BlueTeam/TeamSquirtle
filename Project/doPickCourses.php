<?php 
	include("includes/header.php");
	include("includes/db.php");
	// session_start();
	
	$link = mysqli_connect($host, $user, $pass, $db, $port);
    if(!$link)
	{
        die('cannot connect database'. mysqli_error($link));
    }
	
	// Get variables from input form
	$numCourses=$_POST['numCourses'];
	// $tos = date('YmdHi');
	$tos = time();
	
	$facultyUser = $_SESSION['username'];
	
	$yosQuery = "SELECT yos FROM faculty WHERE email = '$facultyUser'";
	$yosResults = mysqli_query($link, $yosQuery);
	$temp = mysqli_fetch_row($yosResults);
	$yos = $temp[0];
	
	$deadQuery = "SELECT deadline FROM users WHERE username = 'admin'";
	$deadResults = mysqli_query($link, $deadQuery);
	$temp = mysqli_fetch_row($deadResults);
	$deadline = $temp[0];


	echo "$facultyUser <br>";
	echo "$tos <br>";
	echo "$yos[0] <br>";
	echo "$numCourses <br>";
	// echo "$timePref <br>";
	
	if($tos<=$deadline)
	{
		// submit to query
		for($i=0; $i<$numCourses; $i++){
			if(isset($_POST['course'.$i]))
			{
				echo("looping<br>");
				$courseName = $_POST['course'.$i];
				$timePref = $_POST['time'.$i];
				$insertQuery = "INSERT INTO $db.preferences (facultyUser, timePref, yos, tos, courseName) VALUES ('$facultyUser', '$timePref', '$yos', '$tos', '$courseName')";
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
			
				echo "$insertQuery";
			}
		}
	}
	
	include("includes/footer.php"); 
?>