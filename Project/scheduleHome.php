<?php
    //include("includes/header.php");
    require("includes/db.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <link rel="stylesheet" href="css/main.css" type="text/css" />
        <link rel="icon" type="image/png" href="img/squirtle.png">
        <title>Welcome</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body> 
    <?php 
        global $host, $user, $pass, $db, $port;
   $link = mysqli_connect($host, $user, $pass, $db, $port);
        $scheduledQuery = "SELECT distinct course FROM scheduledCourses ORDER BY course ASC";
        $results = mysqli_query($link, $scheduledQuery);
        echo('<table class="schedule">
            <tr>
            	<th>Course</th>
            	<th>Information</th>
            </tr>
             ');
        		echo("<tr></tr>");
			while($row = mysqli_fetch_assoc($results))
			{
				echo("
				<tr>
					<td>".$row['course']."</td>
					<td></td>
					");
                                $courseQuery = "SELECT * FROM scheduledCourses WHERE course = '".$row['course']."'";
                                $subresults = mysqli_query($link, $courseQuery);
                                while($subrow = mysqli_fetch_assoc($subresults)){
                                    echo("
                                        <tr>
                                            <td id = 'section'>".$subrow['section']."</td>
                                            <td>".$subrow['timeSlot']."&nbsp;&nbsp;&nbsp;
                                        ");
                                
				$adminUser = $subrow['facultyUser'];
				$getName = "SELECT lastName, firstName FROM users WHERE username = '".$adminUser."'";
				$resultGetName = mysqli_query($link, $getName);
				$name = mysqli_fetch_assoc($resultGetName);
                                	
				echo("
					".$name['firstName']." ".$name['lastName']."&nbsp;&nbsp;&nbsp;".$subrow['roomName']."</td>
				</tr>
				");
                                }
			}
    ?>
    </body>
</html>
