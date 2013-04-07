<?php include("includes/facultyheader.php"); ?>
  <h1>faculty homepage</h1>
  <p>Schedule</p>
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
<?php include ('includes/footer.php');
