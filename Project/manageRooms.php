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
		$link = mysqli_connect($host, $user, $pass, $db, $port);
			
		$predefRoomTypes = array();
		$predefQuery = "SELECT roomType FROM rooms";
		$predefResult = mysqli_query($link, $predefQuery);
		while($row = mysqli_fetch_row($predefResult))
		{
			array_push($predefRoomTypes, $row[0]);
		}	
		
		$predefRoomSizes = array();
		$predefQuery = "SELECT size FROM rooms";
		$predefResult = mysqli_query($link, $predefQuery);
		while($row = mysqli_fetch_row($predefResult))
		{
			array_push($predefRoomSizes, $row[0]);
		}			
			
		$predefRoomNames = array();
		$predefQuery = "SELECT roomName FROM rooms";
		$predefResult = mysqli_query($link, $predefQuery);
		while($row = mysqli_fetch_row($predefResult))
		{
			array_push($predefRoomNames, $row[0]);
		}
		
		for($i = 0; $i < count($predefRoomNames); $i++)
		{
			echo "<tr>";
			echo "<td>$predefRoomTypes[$i]</td>";
			echo "<td>$predefRoomSizes[$i]</td>";
			echo "<td>$predefRoomNames[$i]</td>";
			echo "<td><input type='checkbox' name='check[]' value='$predefRoomNames[$i]'/></td>";
			echo "<tr>";
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