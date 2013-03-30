<?php include("includes/header.php"); ?>

<h1>Manage Class Time</h1><br />

<legend style="font-size:30px">Add Class Time</legend>
<form name="AddClassTimeForm" method="post" action="addTimeSlotPHP.php" onSubmit="return InputCheck(this)">
<table border = "2">
<tr>
	<td><p style="font-size:30px">Minutes</p></td>
	<td><p style="font-size:30px">Days of Week</p></td>
	<td><p style="font-size:30px">Start Time</p></td>
</tr>
<tr>
	<td><input id="minutes" name="minutes" type="text" size="15" maxlength="6" style="height:50px; font-size:20px"/></td>
	<td>
		<label><input type="checkbox" name="Mon" value = "Mon" />Mon</label>
		<label><input type="checkbox" name="Tue" value = "Tue" />Tue</label>
		<label><input type="checkbox" name="Wed" value = "Wed" />Wed</label><br />
		<label><input type="checkbox" name="Thu" value = "Thu" />Thu</label>
		<label><input type="checkbox" name="Fri" value = "Fri" />Fri</label>
		<label><input type="checkbox" name="Sat" value = "Sat" />Sat</label>
	</td>
	<td>
		<input id="startTime" name="startTime" type="text" size="25" style="height:40px; font-size:20px"/>
		<p>00:00 Format separated by space</p>
	</td>
</tr>
</table>
<p>
	<input type="submit" name="submit" value="  submit  " />
	<input type="reset" name="submit" value="  reset  "  />
	</p>
</form>

<div class="goldBox">
	<form name="scheduleForm" method="post" action="addclasstimeFilePHP.php" onSubmit="return InputCheck(this)">
		<label for="upfile">File to upload:</label>
			<input type=file name=upfile> <br>
		<input type="submit" name="submit" value="  submit file " />
		<input type="reset" name="submit" value="  reset  "  />
	</form>
</div>

<?php include("includes/footer.php"); ?>
