<?php include("includes/header.php"); ?>

<h1>Admin Actions</h1>
<?php
	doActions();
	global $host, $user, $pass, $db, $port, $deptName;
 	$link = mysqli_connect($host, $user, $pass, $db, $port);
 	echo("<h3>Department Name</h3>");
 	echo('<form action="adminActions.php" method="POST"><input type="text" name="department" value="'.$deptName.'" size="'.(strlen($deptName)+2).'"><input type="submit" value="Change"></form>');
 	echo('<br style="clear:both"> <br style="clear:both">');
 	echo("<h3>Administrator Name</h3>");
 	echo('
 			<form action="adminActions.php" method="POST">
 				<input type="text" name="firstName" value="'.$_SESSION['firstname'].'" size="'.(strlen($_SESSION['firstname'])+2).'">
 				<input type="text" name="lastName"  value="'.$_SESSION['lastname'].'"  size="'.(strlen($_SESSION['lastname'])+2).'">
 				<input type="hidden" name="nameFlag" value="true" />
 				<input type="submit" value="Change">
 		  	</form>
 		');
 	echo('<br style="clear:both"> <br style="clear:both">');
 	printForm();
 	printConflicts();

 	function doActions()
 	{
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
 				if($_POST['newSemester'])
				{
 					startNewSemester();
 				}
 				if($_POST['classes'])
				{
 					clearClasses();
 				}
 				if($_POST['classTimes'])
				{
 					clearClassTimes();
 				}
 				if($_POST['rooms'])
				{
 					clearRooms();
 				}
 				if($_POST['prereqs'])
				{
 					clearPrereqs();
 				}
				if($_POST['faculty'])
				{
 					clearFaculty();
 				}
 				if($_POST['prefs'])
				{
 					clearPrefs();
 				}
 				if($_POST['schedule'])
				{
 					clearSchedule();
 				}
				if($_POST['updateTimes'])
				{
 					updateTimes();
 				}
				if($_POST['updateCourses'])
				{
 					updateCourses();
 				}
				if($_POST['updateRooms'])
				{
 					updateRooms();
 				}
				if($_POST['updateFaculty'])
				{
 					updateFaculty();
 				}
				if($_POST['updatePrereqs'])
				{
 					updatePrereqs();
 				}
				if($_POST['updateConflicts'])
				{
 					updateConflicts();
 				}
 			}
 		}
 	}

	function printForm()
	{
		?>
		<div class="purpleBox" id="actions">
			<h2>Clear Data</h2>
			<form action="adminActions.php" method="POST" id="actionForm">
				<div class="row"><input type="checkbox" name="newSemester">		<label for="users">		Start New Semester			</label></div><hr>
				<div class="row"><input type="checkbox" name="classes">		<label for="classes">		Clear Classes				</label></div><hr>
				<div class="row"><input type="checkbox" name="classTimes">	<label for="classTimes">	Clear Class Times			</label></div><hr>
				<div class="row"><input type="checkbox" name="rooms">		<label for="rooms">			Clear Rooms					</label></div><hr>
				<div class="row"><input type="checkbox" name="prereqs">		<label for="prereqs">		Clear Prerequisites			</label></div><hr>
				<div class="row"><input type="checkbox" name="faculty">		<label for="faculty">		Clear Faculty				</label></div><hr>
				<div class="row"><input type="checkbox" name="prefs">		<label for="prefs">			Clear Faculty's Preferences	</label></div><hr>
				<div class="row"><input type="checkbox" name="schedule">	<label for="schedule">		Clear Entire Schedule		</label></div><hr>
				
				<div class="row"><input type="checkbox" name="updateTimes">	<label for="updateTimes">		Update Class Times File		</label></div><hr>
				<div class="row"><input type="checkbox" name="updateCourses"><label for="updateCourses">	Update Courses File		</label></div><hr>
				<div class="row"><input type="checkbox" name="updateRooms">	<label for="updateRooms">		Update Rooms File		</label></div><hr>
				<div class="row"><input type="checkbox" name="updateFaculty"><label for="updateFaculty">	Update Faculty File		</label></div><hr>
				<div class="row"><input type="checkbox" name="updatePrereqs"><label for="updatePrereqs">	Update Prerequisites File	</label></div><hr>
				<div class="row"><input type="checkbox" name="updateConflicts"><label for="updateConflicts">Update Conflicts File	</label></div><hr>
				<div class="row"><input type="submit" value="Submit">	<input type="reset" value="Reset"><input type="hidden" name="clear" value="true"></div>
			</form>
		</div>
		<?php
	}

	function printConflicts()
	{
		echo('
			<div class="goldBox">
				<h2>Conflicts</h2>
		'); 
				$getConflicts = "SELECT * FROM conflicts";
				$conflictResults = mysqli_query($link, $getConflicts);
				if(!$conflictResults)
				{
					echo("<p>No conflicting courses!</p>");
				}else
				{
					while($row = mysqli_fetch_assoc($conflictResults))
					{
						echo('<div class="row">'.$row['course'].'-'.$row['times'].'</div><br><hr>');
					}
				}
		echo('</div>');
	}
	
	function clearClasses()
	{
		mysqli_query($link, "TRUNCATE TABLE courses");
	}
	
	function clearClassTimes()
	{
		mysqli_query($link, "TRUNCATE TABLE timeSlots");
	}
	
	function clearRooms()
	{
		mysqli_query($link, "TRUNCATE TABLE rooms");
	}
	
	function clearPrereqs()
	{
		mysqli_query($link, "TRUNCATE TABLE prereqs");
	}
	
	function clearFaculty()
	{
		$queryResult = mysqli_query($link, "SELECT * FROM faculty");
		while($row = mysqli_fetch_assoc($queryResulty))
		{
		  $delete = "DELETE FROM faculty WHERE email = '$row['email']'";
		  $delete2 = "DELETE FROM users WHERE userName = '$row['email']'";
		  mysqli_query($link, $delete);
		  mysqli_query($link, $delete2);
		}
	}
	
	function clearPrefs()
	{
		mysqli_query($link, "TRUNCATE TABLE preferences");
	}
	
	function clearSchedule()
	{
		mysqli_query($link, "TRUNCATE TABLE scheduledCourses");
	}
	
	function updateTimes()
	{
		
	}
?>

<?php include("includes/footer.php"); ?>