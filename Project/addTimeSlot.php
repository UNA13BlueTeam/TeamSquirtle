<?php include("includes/header.php"); ?>

<h1>Add Class Time</h1><br />

<legend style="font-size:30px">Add Class Time</legend>

<div class="purpleBox">
	<form class="inputForm" id="scheduleForm" name="scheduleForm" method="post" action="doAddTimeSlot.php">
		<div class="row">
			<label for="hours">Minutes</label>
			<input id="hours" name="hours" type="text" size="15" maxlength="2"/>
		</div> <br> <hr>
		<div class="row">
			<label for="prereq">Days of Week</label>
			<input id="prereq" name="prereq" type="text" size="15" maxlength="15"/>
		</div> <br> <hr>
		<div class="row">
			<label for="conflict">Start Time</label>
			<input id="conflict" name="conflict" type="text" size="15" maxlength="15"/>
		</div> <br> <hr>
		<div class="row">
			<input type="submit" name="submit" value="Submit" />
			<input type="reset" name="submit" value="Reset"  />
		</div>
	</form>
</div>


<div class="goldBox">
	<form class="fileForm" name="scheduleForm" method="post" action="addTimeSlotFilePHP.php" enctype="multipart/form-data">
		<div class="row">
			<label for="upfile">File to upload:</label>
			<input type="file" name="upfile">
		</div> 
		<br /><br />
		<input type="submit" name="submit" value="Submit File" />
		<input type="reset" name="submit" value="Reset"  />
	</form>
</div>

<?php include("includes/footer.php"); ?>
