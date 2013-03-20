<?php include("header.php"); ?>

<h1>Manage Classes</h1><br />
<legend style="font-size:30px">Add Course</legend>
<form name="scheduleForm" method="post" action="schedule.php" onSubmit="return InputCheck(this)">
<table height="300" border = "2">
<tr height="30%">
	<td><p style="font-size:30px">Course</p></td>
	<td><p style="font-size:30px">Days Sections</p></td>
	<td><p style="font-size:30px">Night Sections</p></td>
	<td><p style="font-size:30px">Internet Sections</p></td>
	<td><p style="font-size:30px">Class Size</p></td>
	<td><p style="font-size:30px">Room</p></td>
	<td><p style="font-size:30px">Course Hours</p></td>
	<td><p style="font-size:30px">Prerequisites</p></td>

</tr>
<tr>
	<td><input id="course" name="course" type="text" size="15" maxlength="6" style="height:50px; font-size:20px"/></td>
	<td><input id="dsection" name="dsection" type="text" size="15" maxlength="6" style="height:50px; font-size:20px"/></td>
	<td><input id="nsection" name="nsection" type="text" size="15" maxlength="6" style="height:50px; font-size:20px"/></td>
	<td><input id="isection" name="isection" type="text" size="15" maxlength="6" style="height:50px; font-size:20px"/></td>
	<td><input id="size" name="size" type="text" size="15" maxlength="6" style="height:50px; font-size:20px"/></td>
	<td><select name="type"/><option value="class">C</option><option value="lab">L</option></td>
	<td><input id="hours" name="hours" type="text" size="15" maxlength="2" style="height:50px; font-size:20px"/></td>
	<td><input id="prereq" name="prereq" type="text" size="15" maxlength="15" style="height:50px; font-size:20px"/></td>

</tr>
</table>
<p>
	<input type="submit" name="submit" value="  submit  " />
	<input type="reset" name="submit" value="  reset  "  />
</p>
</form>

<?php include("footer.php"); ?>
