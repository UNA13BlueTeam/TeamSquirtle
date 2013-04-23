<?php 
	include("includes/header.php"); 
	doActions();
	include("includes/global.php");
?>

<h1>Admin Actions</h1>
<?php
	global $host, $user, $pass, $db, $port, $deptName;
 	$link = mysqli_connect($host, $user, $pass, $db, $port);
 	echo("<h3>Department Name</h3>");
 	echo('<form action="adminActions.php" method="POST"><input type="text" name="department" placeholder="'.$deptName.'" size="'.(strlen($deptName)+3).'"><input type="submit" value="Change"></form>');
 	echo('<br style="clear:both"> <br style="clear:both">');
 	echo("<h3>Administrator Name</h3>");
 	echo('
 			<form action="adminActions.php" method="POST">
 				<input type="text" name="firstName" placeholder="'.$_SESSION['firstname'].'" size="'.(strlen($_SESSION['firstname'])+2).'">
 				<input type="text" name="lastName"  placeholder="'.$_SESSION['lastname'].'"  size="'.(strlen($_SESSION['lastname'])+2).'">
 				<input type="hidden" name="nameFlag" value="true" />
 				<input type="submit" value="Change">
 		  	</form>
 		');
 	echo('<br style="clear:both"> <br style="clear:both">');
 	printForm();
 	printConflicts();
 	printDownloads();

 	function doActions()
 	{
 		global $host, $user, $pass, $db, $port, $deptName;
 		$link = mysqli_connect($host, $user, $pass, $db, $port);
 		if(isset($_POST)){
 			if(isset($_POST['department']))
 			{
 				$query = "UPDATE users SET deptName = '".$_POST['department']."'";
 				mysqli_query($link, $query);
 			}elseif(isset($_POST['nameFlag']))
 			{
 				global $un;
 				$query = "UPDATE users SET firstName = '".$_POST['firstName']."', lastName = '".$_POST['lastName']."' WHERE username = '".$un."'";
 				mysqli_query($link, $query);
 			}elseif(isset($_POST['clear']))
 			{
 			// 	if($_POST['newSemester'])
				// {
 			// 		startNewSemester();
 			// 	}
 				if(isset($_POST['classes']) and $_POST['classes'])
				{
 					clearClasses();
 				}
 				if(isset($_POST['classTimes']) and $_POST['classTimes'])
				{
 					clearClassTimes();
 				}
 				if(isset($_POST['rooms']) and $_POST['rooms'])
				{
 					clearRooms();
 				}
 				if(isset($_POST['prereqs']) and $_POST['prereqs'])
				{
 					clearPrereqs();
 				}
				if(isset($_POST['faculty']) and $_POST['faculty'])
				{
 					clearFaculty();
 				}
 				if(isset($_POST['prefs']) and $_POST['prefs'])
				{
 					clearPrefs();
 				}
 				if(isset($_POST['schedule']) and $_POST['schedule'])
				{
 					clearSchedule();
 				}
 			}else if(isset($_POST['generate']))
 			{
				if(isset($_POST['updateTimes']) and $_POST['updateTimes'])
				{
					echo("<h1>Generating Class Times</h1>");
 					generateClassTimes();
 				}
				if(isset($_POST['updateCourses']) and $_POST['updateCourses'])
				{
 					generateCourses();
 				}
				if(isset($_POST['updateRooms']) and $_POST['updateRooms'])
				{
 					generateRooms();
 				}
				if(isset($_POST['updateFaculty']) and $_POST['updateFaculty'])
				{
 					generateFaculty();
 				}
				if(isset($_POST['updatePrereqs']) and $_POST['updatePrereqs'])
				{
 					generatePrereqs();
 				}
				if(isset($_POST['updateConflicts']) and $_POST['updateConflicts'])
				{
 					generateConflicts();
 				}
 			}
 		}
 	}

	function printForm()
	{
		?>
		<div class="purpleBox" id="actions">
			<h2>Clear Data</h2>
			<form action="adminActions.php" method="POST" id="clearForm">
				<div class="row"><input type="checkbox" name="classes">		<label for="classes">		Clear Classes				</label></div><hr>
				<div class="row"><input type="checkbox" name="classTimes">	<label for="classTimes">	Clear Class Times			</label></div><hr>
				<div class="row"><input type="checkbox" name="rooms">		<label for="rooms">			Clear Rooms					</label></div><hr>
				<div class="row"><input type="checkbox" name="prereqs">		<label for="prereqs">		Clear Prerequisites			</label></div><hr>
				<div class="row"><input type="checkbox" name="faculty">		<label for="faculty">		Clear Faculty				</label></div><hr>
				<div class="row"><input type="checkbox" name="prefs">		<label for="prefs">			Clear Faculty's Preferences	</label></div><hr>
				<div class="row"><input type="checkbox" name="conflicts">	<label for="conflicts">		Clear Conflict Times		</label></div><hr>
				<div class="row"><input type="checkbox" name="schedule">	<label for="schedule">		Clear Entire Schedule		</label></div><hr>
				<div class="row"><input type="submit" value="Submit"><input type="reset" value="Reset"><input type="hidden" name="clear" value="true"></div>
			</form>
		</div>
		<?php
	}

	function printConflicts()
	{
		?>
		<div class="goldBox" id="actions">
			<h2>Generate New Input Files</h2>
			<form action="adminActions.php" method="POST" id="generatorForm">
				<div class="row"><input type="checkbox" name="updateTimes">		<label for="updateTimes">		Update Class Times File		</label></div><hr>
				<div class="row"><input type="checkbox" name="updateCourses">	<label for="updateCourses">		Update Courses File			</label></div><hr>
				<div class="row"><input type="checkbox" name="updateRooms">		<label for="updateRooms">		Update Rooms File			</label></div><hr>
				<div class="row"><input type="checkbox" name="updateFaculty">	<label for="updateFaculty">		Update Faculty File			</label></div><hr>
				<div class="row"><input type="checkbox" name="updatePrereqs">	<label for="updatePrereqs">		Update Prerequisites File	</label></div><hr>
				<div class="row"><input type="checkbox" name="updateConflicts">	<label for="updateConflicts">	Update Conflicts File		</label></div><hr>
				<div class="row"><input type="submit" value="Submit"><input type="reset" value="Reset"><input type="hidden" name="generate" value="true"></div>
			</form>
		</div>
		<?php
	}

	function printDownloads()
	{
		?>
		<div class="purpleBox" id="downloads">
			<h2>Downloads</h2>
			<ul>
					<li><a href="generatedFiles/classTimes.txt">Class Times</a></li>
					<li><a href="generatedFiles/courses.txt">Courses</a></li>
					<li><a href="generatedFiles/rooms.txt">Rooms</a></li>
					<li><a href="generatedFiles/faculty.txt">Faculty</a></li>
					<li><a href="generatedFiles/prereqs.txt">Prerequisites</a></li>
					<li><a href="generatedFiles/conflicts.txt">Conflicts</a></li>
					<li><a href="generatedFiles/schedule.pdf">Schedule</a></li>
			</ul>
			<p>Downloads will not work unless a file has been generated by using the form on the left.</p>
		</div>
		<?php
	}
?>

<?php include("includes/footer.php"); ?>