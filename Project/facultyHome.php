<?php include("includes/header.php"); ?>
  
<?php
  global $deptAbbrev, $deptName, $userName, $userTitle;
  echo("<h1>".$deptName."</h1>");
  echo("<h2>".$userName."</h2>");
  echo("<h3>".$userTitle."</h3>");
?>

<div class="homeLinks">
  <h4>Links!</h4>
  <ul>
    <li><a href="index.php" id="home">Home</a></li>
    <li><a href="viewschedule.php" id="timeSlots">View Schedules</a> </li>
    <li><a href="Pickcourses.php" id="building">Pick Courses</a></li>
    <li><a href="help.php" id="help">Help</a></li>
  </ul>
</div>
<div id="homeSchedule">
  <h4>Schedule</h4>
	<?php
		$rows = 6; // define number of rows
		$cols = 3;// define number of columns
 
		echo "<table class='schedule'>";
		echo"<tr>";
		
		for($th=1;$th<=$cols;$th++)
		{
			echo"<th></th>";
		}
 
		for($tr=2;$tr<=$rows;$tr++)
		{ 
   			echo "<tr>"; 
        	for($td=1;$td<=$cols;$td++)
			{ 
                echo "<td></td>"; 
        	} 
  		echo "</tr>"; 
		}
		echo "</table>";
  ?>
</div>
<?php include ('includes/footer.php');?>
