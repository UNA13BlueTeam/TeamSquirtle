<?php include("includes/header.php"); ?>
  
<?php
  echo("<h1>".$deptName."</h1>");
  echo("<h2>".$userName."</h2>");
  echo("<h3>".$userTitle."</h3>");
?>
  <p>Schedule</p>
	<?php
		$rows = 6; // define number of rows
		$cols = 3;// define number of columns
 
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
