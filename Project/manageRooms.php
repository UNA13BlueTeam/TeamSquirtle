<?php include("includes/header.php"); ?>

<h1>Manage Classroom</h1><br />
<a href="addRoom.php"><p style = "font-size:30px">Add Classroom</p></a><br />
<hr /><br />
<legend style="font-size:30px">Remove Class Room</legend>
<form name="RemoveclassroomForm" method="post" action="removeclass.php">
	<table height="200" border = "2">
		<tr>
			<td><p style="font-size:30px">Type</p></td>
			<td><p style="font-size:30px">Size</p></td>
			<td><p style="font-size:30px">Room</p></td>
			<td><p style="font-size:30px">Remove</p></td>
		</tr>
		<tr>
			<td>C</td>
			<td>30</td>
			<td>AB125</td>
			<td><input type="checkbox" name="r1" /></td>
		</tr>
		<tr>
			<td>L</td>
			<td>40</td>
			<td>CD125</td>
			<td><input type="checkbox" name="r2" /></td>
		</tr>
	</table>
	<p>
		<input type="submit" name="submit" value="  remove  " />
		<input type="reset" name="submit" value="  reset  "  />
	</p>
</form>

<?php include("includes/footer.php"); ?>