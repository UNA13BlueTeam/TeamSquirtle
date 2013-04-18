<?php include("includes/header.php"); ?>

<h1>Manage Classroom</h1><br />
<a href="addRoom.php"><p style = "font-size:30px">Add Classroom</p></a><br />
<hr /><br />
<legend style="font-size:30px">Remove Class Room</legend>
<form name="RemoveclassroomForm" method="post" action="doRemoveRooms.php">
	<table class="manage" id="manageRooms">
	<tr>
			<th>Type</th>
			<th>Size</th>
			<th>Room</th>
			<th>Remove</th>
	</tr>
	<?php
		global $host, $user, $pass, $db, $port;
		$link = mysqli_connect($host, $user, $pass, $db, $port);
			
		// Declare arrays
		$predefRoomTypes = array();
		$predefRoomSizes = array();
		$predefRoomNames = array();
		
		// Grabs all the faculty information in alphabetical order
		$predefQuery = "SELECT roomType, size, roomName FROM rooms ORDER BY roomName ASC";
		$predefResult = mysqli_query($link, $predefQuery);
		while($row = mysqli_fetch_row($predefResult))
		{
			array_push($predefRoomTypes, $row[0]);
			array_push($predefRoomSizes, $row[1]);
			array_push($predefRoomNames, $row[2]);
		}	
		
		// Print out the table of faculty in alphabetical order
		for($i = 0; $i < count($predefRoomNames); $i++)
		{
			echo "<tr>";
			echo "<td>$predefRoomTypes[$i]</td>";
			echo "<td>$predefRoomSizes[$i]</td>";
			echo "<td>$predefRoomNames[$i]</td>";
			echo "<td><input type='checkbox' name='check[]' value='$predefRoomNames[$i]'/></td>";
			echo "</tr>";
		}	
	?>
	</table>
	<br>
	<div>
		<input type="submit" name="submit" value="Remove" />
		<input type="reset" name="submit" value="Reset"  />
	</div>
</form>

<?php include("includes/footer.php"); ?>