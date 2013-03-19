<?php include("header.php"); ?>

<h1>Manage Classes</h1><br />
<a href="addClass.php"><p style = "font-size:30px">Add Class Schedule</p></a><br />
<hr /><br />
<legend style="font-size:30px">Remove Course Schedule</legend>
<form name="removescheduleForm" method="post" action="removeschedule.php" onSubmit="return InputCheck(this)">
	<table  border = "2">
		<tr height="30%">
			<td><p style="font-size:30px">Course</p></td>
			<td><p style="font-size:30px">Days Sections</p></td>
			<td><p style="font-size:30px">Night Sections</p></td>
			<td><p style="font-size:30px">Internet Sections</p></td>
			<td><p style="font-size:30px">Class Size</p></td>
			<td><p style="font-size:30px">Room</p></td>
			<td><p style="font-size:30px">Remove</p></td>
		</tr>
		<tr>
			<td>AB100</td>
			<td>10</td>
			<td>1</td>
			<td>1</td>
			<td>40</td>
			<td>C</td>
			<td><input type="checkbox" name="r1"></td>
		</tr>
		<tr>
			<td>ABC101</td>
			<td>8</td>
			<td>2</td>
			<td>0</td>
			<td>24</td>
			<td>L</td>
			<td><input type="checkbox" name="r2"></td>
		</tr>
	</table>
	<p>
		<input type="submit" name="submit" value="Submit" />
		<input type="reset" name="reset" value="Reset"  />
	</p>
</form>

<?php include("footer.php"); ?>