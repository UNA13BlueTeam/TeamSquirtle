<?php 
	include("includes/header.php"); 
	
?>

<h3> Please choose how you would like to sort the faculty's priority </h3> <br />
	<form name="removeSlot" method="post" action="schedulingAlgorithm.php" >
			<input type="radio" name="sort" id="sort" value="years" checked/> Years of service <br />
			<input type="radio" name="sort" id="sort" value="times" /> Time of submission <br />
		
		<input type="submit" name="submit" value="Schedule!" />
		
	</form>
<?php include("includes/footer.php"); ?>