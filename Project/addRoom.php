<?php include("includes/header.php"); ?>

<h1>Manage Classrooms</h1><br />

<legend style="font-size:30px">Add Class Room</legend>

<div class="purpleBox">
	<form class="inputForm" id="scheduleForm" name="scheduleForm" method="post" action="doAddRoom.php">
		<div class="row">
			<label for="roomType">Room Type</label>
			<select name="roomType">
				<option value="C">Class</option>
				<option value="L">Lab</option>
			</select>
		</div> <br> <hr>
		<div class="row">
			<label for="hours">Room Size</label>
			<input id="hours" name="hours" type="text" size="15" maxlength="2"/>
		</div> <br> <hr>
		<div class="row">
			<label for="prereq">Room Name</label>
			<input id="prereq" name="prereq" type="text" size="15" maxlength="15"/>
		</div> <br> <hr>
		<div class="row">
			<input type="submit" name="submit" value="Submit" />
			<input type="reset" name="submit" value="Reset"  />
		</div>
	</form>
</div>

<div class="goldBox">
	<form class="fileForm" name="scheduleForm" method="post" action="addRoomFilePHP.php" enctype="multipart/form-data">
		<div class="row">
			<label for="upfile">File to upload:</label>
			<input type="file" name="upfile"> 
		</div> <br /><br /> <hr>
		<div class="row">
			<input type="submit" name="submit" value="Submit File" />
			<input type="reset" name="submit" value="Reset"  />
		</div>
	</form>
</div>

<?php include("includes/footer.php"); ?>
