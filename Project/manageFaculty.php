<?php include("includes/header.php"); ?>

<h1>Manage Faculty</h1><br />
<a href="addFaculty.php"><p style = "font-size:30px">Add Faculty</p></a><br />
<hr /><br />
<legend style="font-size:30px">Remove Course</legend>
<form name="removefacultyForm" method="post" action="removefaculty.php" onSubmit="return InputCheck(this)">
	<table class="manage" id="manageFaculty">
		<tr>
			<th>Name</th>
			<th>Years of service</th>
			<th>Email</th>
			<th>Hours</th>
                        <th>Remove</th>
		</tr>
		<tr>
			<td>Dr Roden</td>
			<td>30</td>
			<td>roden@una.edu</td>
			<td>15</td>
			<td><input type="checkbox" name="r1"></td>
		</tr>
	</table>
	<br>
	<div>
		<input type="submit" name="submit" value="Submit" />
		<input type="reset" name="reset" value="Reset"  />
	</div>
</form>

<?php include("includes/footer.php"); ?>
