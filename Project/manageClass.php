<?php include("includes/header.php"); ?>

<h1>Manage Classes</h1><br />
<a href="addClass.php"><p style = "font-size:30px">Add Course</p></a><br />
<hr /><br />
<legend style="font-size:30px">Remove Course</legend>
<form name="removescheduleForm" method="post" action="removeschedule.php" onSubmit="return InputCheck(this)">
	<table class="manage" id="manageClass">
		<tr height="30%">
			<th>Course</th>
			<th>Days Sections</th>
			<th>Night Sections</th>
			<th>Internet Sections</th>
			<th>Class Size</th>
			<th>Room</th>
			<th>Remove</th>
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
	<br>
	<div>
		<input type="submit" name="submit" value="Submit" />
		<input type="reset" name="reset" value="Reset"  />
	</div>
</form>

<?php include("includes/footer.php"); ?>