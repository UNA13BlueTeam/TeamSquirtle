<?php include("includes/header.php"); ?>

<h1>Add Class Time</h1><br />

<legend style="font-size:30px">Add Class Time</legend>

<div class="purpleBox">
	<form class="inputForm" id="scheduleForm" name="scheduleForm" method="post" action="doAddTimeSlot.php">
		<input type="hidden" name="flag" value="form">
		<div class="row">
			<label for="hours">Minutes</label>
			<input id="minutes" name="minutes" type="text" size="15" maxlength="2"/>
		</div> <br> <hr>
		<div class="row">
			<label for="prereq">Days of Week</label>
			<input id="timesOfDay" name="daysOfWeek" type="text" size="15" maxlength="15"/>
		</div> <br> <hr>
		<div class="row">
			<label for="conflict">Start Time</label>
			<input id="timesOfDay" name="timesOfDay" type="text" size="15" maxlength="231"/>
		</div> <br> <hr>
		<div class="row">
			<input type="submit" name="submit" value="Submit" />
			<input type="reset" name="submit" value="Reset"  />
		</div>
	</form>
</div>


<div class="goldBox">
	<form class="fileForm" name="scheduleForm" method="post" action="doAddTimeSlot.php" enctype="multipart/form-data">
		<input type="hidden" name="flag" value="file">
		<div class="row">
			<label for="timeSlotFile">File to upload:</label>
			<input type="file" name="timeSlotFile">
		</div> 
		<br /><br />
		<input type="submit" name="submit" value="Submit File" />
		<input type="reset" name="submit" value="Reset"  />
	</form>
</div>

<?php include("includes/footer.php"); ?>
