<?php include("includes/header.php"); ?>

<h1>Manage Faculty</h1><br />
<a href="addFaculty.php"><p style = "font-size:30px">Add Faculty</p></a><br />
<hr /><br />
<legend style="font-size:30px">Remove Faculty</legend>
<form name="removefacultyForm" method="post" action="doRemoveFaculty.php">
	<table class="manage" id="manageFaculty">
		<tr>
			<th>Name</th>
			<th>Years of service</th>
			<th>Email</th>
			<th>Hours</th>
            <th>Remove</th>
		</tr>
		
		<?php
			$link = mysqli_connect($host, $user, $pass, $db, $port);
			
			// Declare arrays
			$predefNames = array();
			$predefYOS = array();
			$predefEmail = array();
			$predefHours = array();
			
			// Grabs all the faculty information in alphabetical order
			$predefQuery = "SELECT facultyName, yos, email, minHours FROM faculty ORDER BY facultyName ASC";
			$predefResult = mysqli_query($link, $predefQuery);
			while($row = mysqli_fetch_row($predefResult))
			{
				array_push($predefNames, $row[0]);
				array_push($predefYOS, $row[1]);
				array_push($predefEmail, $row[2]);
				array_push($predefHours, $row[3]);
			}
			
			// Print out the table of faculty in alphabetical order
			for($i = 0; $i < count($predefNames); $i++)
			{
				echo "<tr>";
				echo "<td>$predefNames[$i]</td>";
				echo "<td>$predefYOS[$i]</td>";
				echo "<td>$predefEmail[$i]</td>";
				echo "<td>$predefHours[$i]</td>";
				echo "<td><input type='checkbox' name='check[]' value='$predefEmail[$i]'/></td>";
				echo "<tr>";
			}	
		?>
	</table>
	<br>
	<div>
		<input type="submit" name="submit" value="Remove" />
		<input type="reset" name="reset" value="Reset"  />
	</div>
</form>

<?php include("includes/footer.php"); ?>
