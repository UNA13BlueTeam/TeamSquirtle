<?php include("includes/header.php"); ?>

<h1>Admin Actions</h1>
	<?php 
		global $host, $user, $pass, $db, $port, $deptName;
	 	$link = mysqli_connect($host, $user, $pass, $db, $port);

	 	echo("<h3>Department Name</h3>");
	 	echo('<form action="." method="POST"><input type="text" value="'.$deptName.'" size="'.(strlen($deptName)+5).'"><input type="submit" value="Change"></form>');
	 	echo('<br style="clear:both"> <br style="clear:both">');
	 	printForm();
	 	printConflicts();
	?>

<?php
	function printForm()
	{
		?>
		<div class="purpleBox" id="actions">
			<h2>Clear Data</h2>
			<form action="." method="POST" id="actionForm">
				<div class="row"><input type="checkbox" value="users" name="users"><label for="users">					Clear Users				</label></div><hr>
				<div class="row"><input type="checkbox" value="classes" name="classes"><label for="classes">			Clear Classes			</label></div><hr>
				<div class="row"><input type="checkbox" value="classTimes" name="classTimes"><label for="classTimes">	Clear Class Times		</label></div><hr>
				<div class="row"><input type="checkbox" value="rooms" name="rooms"><label for="rooms">					Clear Rooms				</label></div><hr>
				<div class="row"><input type="checkbox" value="users" name="users"><label for="users">					Clear Prerequisites		</label></div><hr>
				<div class="row"><input type="submit" value="Clear Tables"><input type="reset" value="Reset">											</div>
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
					// Print Conflicts
				}
		echo('</div>');
	}
?>

<?php include("includes/footer.php"); ?>