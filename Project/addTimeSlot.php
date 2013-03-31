<?php include("includes/header.php"); ?>

<h1>Add Class Time</h1><br />

<legend style="font-size:30px">Add Class Time</legend>

<div class="purpleBox">
	<form class="inputForm" id="scheduleForm" name="scheduleForm" method="post" action="addTimeSlotPHP.php" onSubmit="return InputCheck(this)">
		<label for="hours">Minutes</label>
			<input id="hours" name="hours" type="text" size="15" maxlength="2"/> <br>
		<label for="prereq">Days of Week</label>
			<input id="prereq" name="prereq" type="text" size="15" maxlength="15"/> <br>
		<label for="conflict">Start Time</label>
			<input id="conflict" name="conflict" type="text" size="15" maxlength="15"/> <br>
		<p>
			<input type="submit" name="submit" value="  submit  " />
			<input type="reset" name="submit" value="  reset  "  />
		</p>
	</form>
</div>


<div class="goldBox">
	<form name="scheduleForm" method="post" action="addTimeSlotFilePHP.php" onSubmit="return InputCheck(this)">
		<label for="upfile">File to upload:</label>
			<input type="file" name="upfile"> <br /><br />
		<input type="submit" name="submit" value="  submit file " />
		<input type="reset" name="submit" value="  reset  "  />
	</form>
</div>

<?php include("includes/footer.php"); ?>
