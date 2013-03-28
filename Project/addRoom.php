<?php include("includes/header.php"); ?>

<h1>Manage Classrooms</h1><br />

<legend style="font-size:30px">Add Class Room</legend>
<form name="AddRoomForm" method="post" action="addRoomPHP.php" onSubmit="return InputCheck (this)">
<table height="200" border = "2">
<tr>
	<td><p style="font-size:30px">Type</p></td>
	<td><p style="font-size:30px">Size</p></td>
	<td><p style="font-size:30px">Room</p></td>
</tr>
<tr>
	<td><select name="roomType"/><option value="class">C</option><option value="lab">L</option></td>
	<td><input id="size" name="size" type="text" size="5" maxlength="3" style="height:40px; font-size:20px"/></td>
	<td><input id="roomName" name="roomName" type="text" size="" style="height:40px; font-size:20px"/></td>
</tr>
</table>
<p>
	<input type="submit" name="submit" value="  submit  " />
	<input type="reset" name="submit" value="  reset  "  />
</p>
</form>

<?php include("includes/footer.php"); ?>
