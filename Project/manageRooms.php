<?php include("includes/header.php"); ?>

<h1>Manage Classroom</h1><br />
<a href="addRoom.php"><p style = "font-size:30px">Add Classroom</p></a><br />
<hr /><br />
<legend style="font-size:30px">Remove Class Room</legend>
<form name="RemoveclassroomForm" method="post" action="removeclass.php">
	<table class="manage" id="manageRooms">
		<tr>
			<th>Type</th>
			<th>Size</th>
			<th>Room</th>
			<th>Remove</th>
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
	<br>
	<div>
		<input type="submit" name="submit" value="Remove" />
		<input type="reset" name="submit" value="Reset"  />
	</div>
</form>

<?php include("includes/footer.php"); ?>