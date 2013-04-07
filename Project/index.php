<?php include("includes/header.php"); ?>

<?php
	echo("<h1>".$deptName."</h1>");
	echo("<h2>".$userName."</h2>");
	echo("<h3>".$userTitle."</h3>");
?>
<div class="homeLinks">
	Links!
	<ul>
		<li> <a href="timeSlots.php" id="timeSlots">Manage Class Times</a> </li>
		<li> <a href="manageRooms.php" id="building">Manage Rooms</a> </li>
		<li> <a href="manageClass.php" id="classes">Manage Classes</a> </li>
		<li> <a href="conflicts.php" id="conflicts">Conflicts</a> </li>
		<li> <a href="index.php" id="deadline">Change Deadline</a></li>
		<li> <a href="index.php" id="deadline">Help</a></li>
	</ul>
</div>
<div id="homeSchedule">
	<?php
		$rows = 6; // define number of rows
		$cols = 2;// define number of columns
 
		echo "<table class='schedule'>";
                echo"<tr>";
                for($th=1;$th<=$cols;$th++){
                    echo"<th></th>";
                }
 
		for($tr=2;$tr<=$rows;$tr++){ 
      
   			echo "<tr>"; 
        	for($td=1;$td<=$cols;$td++){ 
                echo "<td></td>"; 
        	} 
  		echo "</tr>"; 
		} 

		echo "</table>";
	
        ?>        
</div>

<br style="clear:both;">
<?php include("includes/footer.php"); ?>
