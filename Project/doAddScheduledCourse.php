<?php include("includes/header.php"); 

	$link = mysqli_connect($host, $user, $pass, $db, $port);
	
	$course = $_POST['course'];
	$instructor = $_POST['instructor'];
	$time = $_POST['times'];
	$room = $_POST['rooms'];
	
	$tempCourse = preg_split('/[-]/', $course);
	$course = trim($tempCourse[0]);
	$section = trim($tempCourse[1]);
	
	$errorFlag = false;
	
	
	$alreadyTeachingTimes = array();
	$query = "SELECT * FROM scheduledCourses WHERE facultyUser = '$instructor'";
	$result = mysqli_query($link, $query);
	while($row = mysqli_fetch_assoc($result))
	{
		if($row)
		{
			array_push($alreadyTeachingTimes, $row['timeSlot']);
		}
	}
	
	$alreadyTeachingRooms = array();
	$query = "SELECT * FROM scheduledCourses WHERE roomName = '$room'";
	$result = mysqli_query($link, $query);
	while($row = mysqli_fetch_assoc($result))
	{
		if($row)
		{
			array_push($alreadyTeachingRooms, $row['timeSlot']);
		}
	}
	
	
	$noTimeOverlapFaculty = false;
	$alreadyTeachingFlag = true;
	$alreadyTeachingTimesIndex = 0;
	
	
	// CHECK 
	if(!in_array($time, $alreadyTeachingTimes))
	{			
		for($i = 0; $i < count($alreadyTeachingTimes); $i++)
		{
			// Splits the times into arrays
			//$row[0] and $time are the two times to check so we need the specific time (e.g. 10:00) from each string
			$timeToScheduleFaculty = preg_split('/[\s+\/]/', $time);
			$alreadyTeachingFaculty = preg_split('/[\s+\/]/', $alreadyTeachingTimes[$i]);
		
			// $difference = the amount of minutes between the time in array of times and the time they are already teaching
			$difference = round(abs(strtotime($timeToScheduleFaculty[2]) - strtotime($alreadyTeachingFaculty[2])) / 60,2);
			
			// Check to make sure the minutes don't overlap the time they are currently teaching
			if(strtotime($timeToScheduleFaculty[2]) > strtotime($alreadyTeachingFaculty[2]))
			{
				if($difference > $alreadyTeachingFaculty[0])
				{
					$noTimeOverlapFaculty = true;
				}
				else
				{
					$noTimeOverlapFaculty = false;
				}
			}
			else
			{
				if($difference > $timeToScheduleFaculty[0])
				{
					$noTimeOverlapFaculty = true;
				}
				else
				{
					$noTimeOverlapFaculty = false;
				}
			}
			
			// Checks to make sure the faculty member isn't already teaching at the same time on the same day
			if($noTimeOverlapFaculty == true)
			{
				$alreadyTeachingFlag = false;
			}
			else	// Falls here if a faculty member ran into a conflicting time
			{
				if(count(array_intersect(str_split($alreadyTeachingTimes[$i]), str_split(trim($timeToScheduleFaculty[1])))) == 0)
				{
					$alreadyTeachingFlag = false;
				}
				else
				{
					echo "Could not schedule time because faculty member is already teaching in the specified time interval $time";
					$errorFlag = true;
					$i = count($alreadyTeachingTimes);
				}
			}
		}
	}
	else	// The faculty member wasn't teaching at a time yet
	{
		echo "Could not schedule time because faculty member is already teaching in the specified time interval $time";
		$errorFlag = true;
	}
	
	
	
	//CHECK ROOM IS AVAILABLE IN THE TIME INTERVAL
	if($errorFlag == false)
	{
		if(!in_array($time, $alreadyTeachingRooms))
		{    
			$roomAvailable = false;
			$noTimeOverlapRoom = false;
			// Loops through the preferred times by the faculty member to find if the room is available at a time
			for($i = 0; $i < count($alreadyTeachingRooms); $i++)
			{
				// Splits the times into arrays
				$timeToScheduleRoom = preg_split('/[\s+\/]/', $time);
				$alreadyTeachingRoom = preg_split('/[\s+\/]/', $alreadyTeachingRooms[$i]);
				
				// $difference = the amount of minutes between the time in array of times 
							// and the time they are already teaching
				$difference = round(abs(strtotime($timeToScheduleRoom[2]) - strtotime($alreadyTeachingRoom[2])) / 60,2);
				// Checks to make sure the times don't overlap for the room
				if(strtotime($timeToScheduleRoom[2]) > strtotime($alreadyTeachingRoom[2]))
				{
					if($difference > $alreadyTeachingRoom[0])
					{
						$noTimeOverlapRoom = true;
					}
					else
					{
						$noTimeOverlapRoom = false;
					}
				}
				else
				{
					if($difference > $timeToScheduleRoom[0])
					{
						$noTimeOverlapRoom = true;
					}
					else
					{
						$noTimeOverlapRoom = false;
					}
				}
				
				// Check to make sure the room is not already being taught at the current time on the same day
				if((count(array_intersect(str_split($alreadyTeachingRooms[$i]), str_split(trim($timeToScheduleRoom[1])))) == 0) or ($noTimeOverlapRoom == true))
				{
					$roomAvailable = true;	// This must happen for every time in the list of unavailable times array
				}
				else	// Falls here if the current time is unschedulable for this room and kicks out of the loop
				{
					$roomAvailable = false;
					echo "Could not schedule time because room $room is already taken during the specified time interval $time";
					$errorFlag = true;
					$i = count($alreadyTeachingRooms);
				}
			}
		}
		else 		// Falls here the room is already scheduled for the current time
		{
			echo "Could not schedule time because room $room is already taken during the specified time interval $time";
			$errorFlag = true;
			$roomAvailable = false;
		}
	}
	
	
	if($errorFlag == false)
	{
		$insertQuery = "INSERT INTO scheduledCourses (course, section, timeSlot, facultyUser, roomName) ";
		$insertQuery = $insertQuery."VALUES ('$course', '$section', '$time', '$instructor', '$room')";
		mysqli_query($link, $insertQuery);
		$removeQuery = "DELETE FROM unscheduledCourses WHERE course = '$course' AND section = '$section'";
		mysqli_query($link, $removeQuery);
		header("Location: manageSchedule.php");
	}
	
include("includes/footer.php");?>