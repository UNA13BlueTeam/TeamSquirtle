<?php include("includes/header.php"); ?>

<h1>Manage Faculty</h1><br />
<a href="addFaculty.php"><p style = "font-size:30px">Add Faculty</p></a><br />
<hr /><br />
<legend style="font-size:30px">Remove Course</legend>
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
			
			$predefNames = array();
			$predefQuery = "SELECT facultyName FROM faculty";
			$predefResult = mysqli_query($link, $predefQuery);
			while($row = mysqli_fetch_row($predefResult))
			{
				array_push($predefNames, $row[0]);
			}
			
			$predefYOS = array();
			$predefQuery = "SELECT yos FROM faculty";
			$predefResult = mysqli_query($link, $predefQuery);
			while($row = mysqli_fetch_row($predefResult))
			{
				array_push($predefYOS, $row[0]);
			}
			$predefEmail = array();
			$predefQuery = "SELECT email FROM faculty";
			$predefResult = mysqli_query($link, $predefQuery);
			while($row = mysqli_fetch_row($predefResult))
			{
				array_push($predefEmail, $row[0]);
			}
			$predefHours = array();
			$predefQuery = "SELECT minHours FROM faculty";
			$predefResult = mysqli_query($link, $predefQuery);
			while($row = mysqli_fetch_row($predefResult))
			{
				array_push($predefHours, $row[0]);
			}
			
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
