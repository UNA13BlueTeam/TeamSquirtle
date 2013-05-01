<?php include("includes/header.php"); ?>

<h1>Manage Classrooms</h1><br />

<legend style="font-size:30px">Add Class Room</legend>

<div class="purpleBox">
	<form class="inputForm" id="scheduleForm" name="scheduleForm" method="post" action="doAddRoom.php">
		<input type="hidden" name="flag" value="form">
		<div class="row">
			<label for="roomType">Room Type</label>
			<select name="roomType">
				<option value="C">Class</option>
				<option value="L">Lab</option>
			</select>
		</div> <br> <hr>
		<div class="row">
			<label for="size">Room Size</label>
			<input id="size" name="size" type="text" size="15" maxlength="3"/>
		</div> <br> <hr>
		<div class="row">
			<label for="roomName">Room Name</label>
			<input id="roomName" name="roomName" type="text" placeholder="Ex. Keller&nbsp;&nbsp;210" size="15" maxlength="15"/>
			<br>
		</div><hr>
		<div class="row">
			<input type="submit" name="submit" value="Submit" />
			<input type="reset" name="submit" value="Reset"  />
		</div>
	</form>
	<br><br>
</div>

<div class="goldBox">
	<form class="fileForm" name="scheduleForm" method="post" action="doAddRoom.php" enctype="multipart/form-data">
		<input type="hidden" name="flag" value="file">
		<div class="row">
			<label for="roomFile">File to upload:</label>
			<input type="file" name="roomFile"> 
		</div> <br /><br /> <hr>
		<div class="row">
			<input type="submit" name="submit" value="Submit File" />
			<input type="reset" name="submit" value="Reset"  />
		</div>
	</form>
</div>

<?php include("includes/footer.php"); ?>
