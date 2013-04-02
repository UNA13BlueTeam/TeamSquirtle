<?php include("includes/header.php"); ?>
  <h1>admin homepage</h1>
	<p>Schedule</p>
	<?php
		$rows = 10; // define number of rows
		$cols = 4;// define number of columns
 
		echo "<table class='schedule'>"; 
 
		for($tr=1;$tr<=$rows;$tr++){ 
      
   			echo "<tr>"; 
        	for($td=1;$td<=$cols;$td++){ 
               echo "<td>row: ".$tr." column: ".$td."</td>"; 
        	} 
    		echo "</tr>"; 
		} 
 
		echo "</table>";
	?>
<?php include("includes/footer.php"); ?>
