<?php include("includes/header.php"); ?>

<h1>Manage Classes</h1><br />
<legend style="font-size:30px">Add Course</legend>

<div class="purpleBox">
	<form class="inputForm" id="scheduleForm" name="scheduleForm" method="post" action="doAddClass.php" onSubmit="return InputCheck(this)">
		<input type="hidden" name="flag" value="form">
		<div class="row">
			<label for="course">Course</label>
			<input id="course" name="course" type="text" maxlength="6"/>
		</div> <br> <hr>
		<div class="row">
			<label for="dsection">No. of Day Sections</label>
			<input id="dsection" name="dsection" type="text" maxlength="6"/>
		</div> <br> <hr>
		<div class="row">
			<label for="nsection">No. of Night Sections</label>
			<input id="nsection" name="nsection" type="text" maxlength="6"/>
		</div> <br> <hr>
		<div class="row">
			<label for="isection">No. of Internet Sections</label>
			<input id="isection" name="isection" type="text" maxlength="6"/>
		</div> <br> <hr>
		<div class="row">
			<label for="classSize">Class Size</label>
			<input id="classSize" name="classSize" type="text" maxlength="6"/>
		</div> <br> <hr>
		<div class="row">
			<label for="roomType">Class Type</label>
			<select name="roomType">
				<option value="C">Class</option>
				<option value="L">Lab</option>
			</select>
		</div> <br> <hr>
		<div class="row">
			<label for="hours">Credit Hours</label>
			<input id="hours" name="hours" type="text" maxlength="2"/>
		</div> <br> <hr>
		<div class="row">
			<label for="prereq">Prerequisites</label>
			<input id="prereq" name="prereq" type="text" maxlength="15"/>
		</div> <br> <hr>
		<div class="row">
			<label for="conflict">Conflicts</label>
			<input id="conflict" name="conflict" type="text" maxlength="15"/>
		</div> <br> <hr>
			<input id="button" type="submit" name="submit" value="Submit" />
			<input id="button" type="reset" name="reset" value="Reset"  />
	</form>
</div>

<div class="goldBox">
	<form name="scheduleForm" method="post" action="doAddClass.php" enctype="multipart/form-data" onSubmit="return InputCheck(this)">
		<input type="hidden" name="flag" value="file">
		<label for="classFile">Class File:</label>
			<input type="file" name="classFile"> <br /><br />
		<label for="prereqFile">Prequisite File:</label>
			<input type="file" name="prereqFile"><br><br>
		<input type="submit" name="submit" value="Submit File" />
		<input type="reset" name="submit" value="Reset"  />
	</form>
</div>

<?php include("includes/footer.php"); ?>
