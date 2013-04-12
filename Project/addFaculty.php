<?php include("includes/header.php"); ?>

<h1>Manage Faculty</h1><br />
<legend style="font-size:30px">Add Faculty</legend>

<div class="purpleBox">
  <form class="inputForm" id="facultyForm" name="facultyForm" method="post" action="doAddFaculty.php">
		<input type="hidden" name="flag" value="form">
		<div class="row">
			<label for="name">Name: </label>
			<input id="name" name="name" type="text" maxlength="25"/>
		</div> <br> <hr>
		<div class="row">
			<label for="yos">Years of Service: </label>
			<input id="yos" name="yos" type="text" maxlength="2"/>
		</div> <br> <hr>
		<div class="row">
			<label for="email">Email: </label>
			<input id="email" name="email" type="text" maxlength="10"/>
		</div> <br> <hr>
		<div class="row">
			<label for="hours">Hours: </label>
			<input id="hours" name="hours" type="text" maxlength="2"/>
		</div> <br> <hr>
			<input id="button" type="submit" name="submit" value="Submit" />
			<input id="button" type="reset" name="reset" value="Reset"  />
	</form>
</div>

<div class="goldBox">
	<form class="fileForm" name="facultyForm" method="post" action="doAddFaculty.php" enctype="multipart/form-data">
		<input type="hidden" name="flag" value="file">
		<label for="facultyFile">Faculty File: <br /></label>
			<input type="file" name="facultyFile"> <br /><br />
		<input type="submit" name="submit" value="Submit File" />
		<input type="reset" name="submit" value="Reset"  />
	</form>
</div>

<?php include("includes/footer.php"); ?>
