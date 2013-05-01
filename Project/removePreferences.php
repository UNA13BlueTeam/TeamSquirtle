<?php include("includes/header.php"); ?>

<h1>Remove Preferences</h1><br />
<hr /><br />
<form name="RemovePreferencesForm" method="post" action="doRemovePreferences.php">
	<table class="manage" id="managePreferences">
	<tr>
			<th>Remove</th>
			<th>Course</th>
			<th>Preference</th>
	</tr>
	<?php
		global $host, $user, $pass, $db, $port;
		$link = mysqli_connect($host, $user, $pass, $db, $port);
		
		$currentUser = $_SESSION['username'];
		
		// Declare arrays
		$predefTimePref = array();
		$predefCourseName = array();
		
		// Grabs all the faculty information in alphabetical order
		$predefQuery = "SELECT * FROM preferences WHERE facultyUser = '$currentUser'";
		$predefResult = mysqli_query($link, $predefQuery);
		while($row = mysqli_fetch_assoc($predefResult))
		{
			array_push($predefTimePref, $row['timePref']);
			array_push($predefCourseName, $row['courseName']);
		}	
		
		// Print out the table of faculty in alphabetical order
		for($i = 0; $i < count($predefCourseName); $i++)
		{
			echo "<tr>";
			echo "<td><input type='checkbox' name='check[]' value='$predefCourseName[$i] $predefTimePref[$i]'/></td>";
			echo "<td>$predefCourseName[$i]</td>";
			echo "<td>$predefTimePref[$i]</td>";
			echo "</tr>";
		}	
	?>
	</table>
	<br>
	<div>
		<input type="submit" name="submit" value="Remove" />
		<input type="reset" name="submit" value="Reset"  />
	</div>
</form>

<?php include("includes/footer.php"); ?>