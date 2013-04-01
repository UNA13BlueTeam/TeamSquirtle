<?php include("includes/header.php"); ?>

<h1>Manage Classrooms</h1><br />

<legend style="font-size:30px">Add Class Room</legend>

<div class="purpleBox">
	<form class="inputForm" id="scheduleForm" name="scheduleForm" method="post" action="addRoomPHP.php" onSubmit="return InputCheck(this)">
		<label for="roomType">Room Type</label>
			<select name="roomType">
				<option value="C">Class</option>
				<option value="L">Lab</option>
			</select> <br>
		<label for="hours">Room Size</label>
			<input id="hours" name="hours" type="text" size="15" maxlength="2"/> <br>
		<label for="prereq">Room Name</label>
			<input id="prereq" name="prereq" type="text" size="15" maxlength="15"/> <br>
		<p>
			<input type="submit" name="submit" value="  submit  " />
			<input type="reset" name="submit" value="  reset  "  />
		</p>
	</form>
</div>

<div class="goldBox">
	<form name="scheduleForm" method="post" action="addRoomFilePHP.php" onSubmit="return InputCheck(this)">
		<label for="upfile">File to upload:</label>
			<input type="file" name="upfile"> <br /><br />
		<input type="submit" name="submit" value="  submit file " />
		<input type="reset" name="submit" value="  reset  "  />
	</form>
</div>

<?php include("includes/footer.php"); ?>
