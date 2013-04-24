<?php 
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
   	$roomQuery = "SELECT * FROM rooms";
 		$results = mysqli_query($link, $roomQuery);

		echo('<table class="schedule">
            <tr>
            	<th>Rome</th>
            	<th>Information</th>
            </tr>
                ');
			// $row = mysqli_fetch_row($results, MYSQLI_BOTH);
		echo("<tr></tr>");
			while($row = mysqli_fetch_assoc($results))
			{
				echo("
				<tr>
					<td>".$row['roomName']."</td>
					<td></td>
					");
                                $courseQuery = "SELECT * FROM scheduledCourses WHERE roomName = '".$row['roomName']."'";
                                $subresults = mysqli_query($link, $courseQuery);
                                while($subrow = mysqli_fetch_assoc($subresults)){
                                    echo("
                                        <tr>
                                            <td></td>
                                            <td>".$subrow['course']."-".$subrow['section']."&nbsp;&nbsp;&nbsp;"
                                            .$subrow['timeSlot']."&nbsp;&nbsp;&nbsp;
                                        ");
                                
				$adminUser = $subrow['facultyUser'];
				$getName = "SELECT lastName, firstName FROM users WHERE username = '".$adminUser."'";
				$resultGetName = mysqli_query($link, $getName);
				$name = mysqli_fetch_assoc($resultGetName);
                                	
				echo("
					".$name['firstName']." ".$name['lastName']."</td>
				</tr>
				");
       }
			}
?>
