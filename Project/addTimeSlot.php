<?php include("header.php"); ?>

<h1>Manage Class Time</h1><br />

<legend style="font-size:30px">Add Class Time</legend>
<form name="AddclassForm" method="post" action="addclass.php" onSubmit="return InputCheck(this)">
<table border = "2">
<tr>
	<td><p style="font-size:30px">minutes</p></td>
	<td><p style="font-size:30px">Days of Week</p></td>
	<td><p style="font-size:30px">Start Time</p></td>
</tr>
<tr>
	<td><input id="minates" name="minates" type="text" size="15" maxlength="6" style="height:50px; font-size:20px"/></td>
	<td>
		<label><input type="checkbox" name="Mon" />Mon</label>
		<label><input type="checkbox" name="Tue" />Tue</label>
		<label><input type="checkbox" name="Wed" />Wed</label><br />
		<label><input type="checkbox" name="Thu" />Thu</label>
		<label><input type="checkbox" name="Fri" />Fri</label>
		<label><input type="checkbox" name="Sat" />Sat</label>
	</td>
	<td>
		<input id="starttime" name="starttime" type="text" size="25" style="height:40px; font-size:20px"/>
		<p>00:00 Format separated by space</p>
	</td>
</tr>
</table>
<p>
	<input type="submit" name="submit" value="  submit  " />
	<input type="reset" name="submit" value="  reset  "  />
	</p>
</form>

<?php include("footer.php"); ?>