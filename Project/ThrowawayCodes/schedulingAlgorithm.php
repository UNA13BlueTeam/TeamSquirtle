<?php 
/*-----------------------------------------------------------------------------------------------
 ********************** Scheduling Algorithm Function Prologue  ********************
 * Preconditions: A prepopulated database with the following tables: faculty, courses, preferences
 *				  prerequisitites, available rooms, an course times. Additionally the database may 
 *				  also contain a table called conflicts, but this table does not have to present to
 *				  run the scheduling algorithm. The information in each table includes: 
 *				1.faculty: faculty name, years-of-service(YOS), email, and minimum hours to teach.
 *				2.courses: course name, # of day-sections,# of night-sections, # of internet sections
 *							class size, room type, and the credit hours.
 *				3.preferences: faculty user name,time preference, years-of-service(YOS),course name
 *				4.prerequisties: course name, and up to three prerequisite course names.
 *				5.available rooms: room type, room size, and room name.
 *				6.course times: minutes, days-of-week,times-per-day.
 *				7.scheduled courses: course name, section number, time-slot,faculty user name, room name
 *			  (8).conflicts: course name and the days-of-week followed by the times that course cannot be
 *								scheduled.
 * Postconditions: Courses that were successfully scheduled will appear in the scheduled courses table in
 *					the database.Courses that were not scheduled will be logged to a file called unschedul-
 *					ed courses. This log will contain the faculty name, the course name, and a reason to why
 *                  the course was not scheduled.
 * Function Purpose: To schedule the minimum hours for an existing listing of faculty members. 
 *
 * Input Expected: No input is expected from the user in this algorithm. All input expected is only
 *					from the database (see preconditions).	
 * Exceptions/Errors Thrown: 
 * Files Accessed: 
 *
 * Function Pseudocode Author: Alla, Jared, Cody, and Michael
 *
 * Function Author: Alla, Jared, Cody, and Michael
 *
 * Date of Original Implementation: 4-13-2013
 *
 * Tested by SQA Member (NAME and DATE): 
 * 
 ** Modifications by:
 * Modified By (Name and Date): Michael Debs April 17, 1:27am
 * Modifications Description:	Now prints out a list of faculty members that didn't meet their 
 *								minimum required hours at the end of the algorithm
 *
 * Modified By (Name and Date): Jared Cox, Michael Debs  April 17, 4:59pm 
 * Modifications Description:	Selected course with no preference now correctly goes on
 *								unscheduled courses list
 *								Sections of the same course are not scheduled at the same time
 *								with a different professor and room.  They are now scheduled apart
 *								Order priority further by time of submission if years of service
 *								are the same, and vice versa
 *								Class times do not overlap when assigning to a professor or a room
 *									(e.g. a class will not be scheduled at 10:00 am in ROOM 12 if
 *									 there is class scheduled in ROOM 12 at 9:00 am for 75 minutes.)
 *								
 -------------------------------------------------------------------------------------------------*/
 
 
	$host = "127.0.0.1";
	$user = "leonie";
	$pass = "4EzvHW5C";
	$db = "teamsquirtle";
	$port = 45000;
	  
	$link = mysqli_connect($host, $user, $pass, $db, $port);
	mysqli_query($link, "TRUNCATE TABLE scheduledCourses");

	include ("classes.php");
	
	//Varaible declarations
	$sortByYOS = true;
	$sortByTOS = false;
	$ctsIndex = 0;    // Courses to schedule index
	$classTimesIndex = 0;   
	$missingConflictFile = false;
	$conflictExists = false;
	$scheduledSections = 0;
	$foundRoom = false;
	$scheduledSections = 0;
    $currentSectionNumber = 1;
	
	//put if stmt to check for missing conflict file.
	//Create array of unscheduled courses (listOfUnscheduledCourses)
	$unscheduledCourses = array();
	$unscheduledCourses2 = array();
	 
	//Create array of courses (coursesToSchedule)
	$coursesToSchedule = array();
	$predefQuery = "SELECT courseName, dsection, nsection, isection, classSize, roomType, hours FROM courses";
	$predefResult = mysqli_query($link, $predefQuery);
	while($row = mysqli_fetch_row($predefResult))
	{
		$addCourse = new Course($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6]);
		// Uncomment below to print out array of courses
		//$addCourse->printer();
		array_push($coursesToSchedule, $addCourse);
	}	
	
	//Create array of class times (classTimes)
	$classTimes = array();
	$classTimesQuery = "SELECT minutes, daysOfWeek, timesOfDay FROM timeSlots";
	$result = mysqli_query($link, $classTimesQuery);
	while($row = mysqli_fetch_row($result))
	{
		$addTimeSlot = new ClassTime($row[0], $row[1], $row[2]);
		// Uncomment below to print out array of courses
		//$addTimeSlot->printer();
		array_push($classTimes, $addTimeSlot);
	}
	
	//Create an array of rooms
	$arrayOfRooms = array();
	$roomsQuery = "SELECT roomType, size, roomName FROM rooms";
	$result = mysqli_query($link, $roomsQuery);
	while($row = mysqli_fetch_row($result))
	{
		$addRooms = new Room($row[0], $row[1], $row[2]);
		// Uncomment below to print out array of courses
		//$addRooms->printer();
		array_push($arrayOfRooms, $addRooms);
	}
	// NEED TO ALSO CREATE AN ARRAY OF FACULTY MEMBERS
	//THIS ARRAY WILL BE USED TO DETERMINE WHICH FACULTY NEEDS ADDITIONAL COURSES TO SATISFY THEIR MIN. HOURS TO TEACH.
	//DETAILS FOR PSEUDOCODE IS AT THE END OF THE ALGORITHM
	//Create an array of rooms
	$arrayOfFaculty = array();
	$factQuery = "SELECT email, minHours FROM faculty";
	$factResult = mysqli_query($link, $factQuery);
	while($row = mysqli_fetch_row($factResult))
	{
		$addFaculty = new FacultyMin($row[0], $row[1]);
		// Uncomment below to print out array of courses
		//$addRooms->printer();
		array_push($arrayOfFaculty, $addFaculty);
	}
	
	
	//Main loop to iterate through array of courses to schedule
	while ($ctsIndex < count($coursesToSchedule))
    {
		// Check if a conflict exists for this course
		$courseNamer = $coursesToSchedule[$ctsIndex]->name; //courseNamer should be courseName
		echo "$courseNamer  ";
		$conflictQuery = "SELECT course, times FROM conflicts WHERE course = '$courseNamer'";
		$missingConflict = mysqli_query($link, $conflictQuery);
		$row = mysqli_fetch_row($missingConflict);
		
        if ($row[0] != $courseNamer) //since we are doing a specific query for courseNamer,can we just check if query returns empty? if yes, no conflicts otherwise we have conflicts.
		{			
            $conflictFileExists = false;
			//echo "<br>No conflict found <br>";
        }
        else
        {
            $conflictFileExists = true;
			$conTimes = $row[1];
			$conflictTimes = preg_split('/\s+/', trim($conTimes)); //creates an array of conflict times from a single string
			//echo "<br>Conflict found <br>";
        }
        
        //Get day and night sections from course (as constants)
        
        $daySections = $coursesToSchedule[$ctsIndex]->daySections; 
        $nightSections= $coursesToSchedule[$ctsIndex]->nightSections;
		$scheduledSections = 0;
		
		$facultyPQ = array();
		$facultyPQIndex = 0;
		
		
		// Sort preferences table by years of service
		if($sortByYOS == true)
        {
			//Retrieve all faculty members that chose this course in their preferences
			$facultyQuery = "SELECT facultyUser, yos, tos, timePref FROM preferences WHERE courseName = '$courseNamer' ORDER BY yos DESC, CAST(tos AS SIGNED) ASC";
			$facultyResult = mysqli_query($link, $facultyQuery);
			
			while($row = mysqli_fetch_row($facultyResult))
			{
				// Call to get minimum hours for faculty
				$facultyQuery2 = "SELECT minHours FROM faculty WHERE email = '$row[0]'";
				$facultyResult2 = mysqli_query($link, $facultyQuery2);
				$row2 = mysqli_fetch_row($facultyResult2);
				
									//	 username   yos      tos     timePref   minHours   
				$addFaculty = new FacultyPref($row[0], $row[1], $row[2], $row[3], $row2[0]);
				$addFaculty->printer();
				array_push($facultyPQ, $addFaculty);
			}
		   
        }
		// Sort preference table by time of submission
		elseif($sortByTOS == true)
        {
			//Retrieve all faculty members that chose this course in their preferences
			$facultyQuery = "SELECT facultyUser, yos, tos, timePref FROM preferences WHERE courseName = '$courseNamer' ORDER BY CAST(tos AS SIGNED) ASC, yos DESC";
			$facultyResult = mysqli_query($link, $facultyQuery);
			while($row = mysqli_fetch_row($facultyResult))
			{
				// Call to get minimum hours for faculty
				$facultyQuery2 = "SELECT minHours FROM faculty WHERE email = '$row[0]'";
				$facultyResult2 = mysqli_query($link, $facultyQuery2);
				$row2 = mysqli_fetch_row($facultyResult2);
				
									//	 username   yos      tos     timePref   minHours   
				$addFaculty = new FacultyPref($row[0], $row[1], $row[2], $row[3], $row2[0]);
				$addFaculty->printer();
				array_push($facultyPQ, $addFaculty);
			}
        }
		
        if (count($facultyPQ) == 0)
        {
			// Put courseToSchedule[ctsIndex] on array of unscheduled courses “No faculty selected coursesToSchedule[ctsIndex].courseName”
			echo "<br>EMPTY PQ   $currentSectionNumber<br>";
			while($scheduledSections < ($daySections + $nightSections + $coursesToSchedule[$ctsIndex]->internetSections))
			{
				array_push($unscheduledCourses2, $courseNamer."-".$currentSectionNumber);
				$scheduledSections++;
				$currentSectionNumber++;
			}
			
        }
        else // we have a priority queue of faulcty member who wish to teach the current course
        {
		     //(declare variables for number of day and night sections left)
            $daySectionsRemaining = $daySections;
            $nightSectionsRemaining = $nightSections;
            $scheduledSections = 0;
            $currentSectionNumber = 1;
			
			$classTimesIndex = 0;
			
			echo "<br><h3> $daySections + $nightSections </h3><br>";
			
			//Iterate through the priority queue and begin the process of scheduling.
			while (($facultyPQIndex < count($facultyPQ)) and ($scheduledSections < $daySections + $nightSections) and ($classTimesIndex < count($classTimes)))
            {
				echo "Class Times Array: ".count($classTimes)."    Index: $classTimesIndex<br>";
				echo "<br><h3> Scheduled Sections = $scheduledSections </h3><br>";
				echo "<br><h3> Faculty Queue Index = $facultyPQIndex </h3><br>";
				
                //Check front of priority queue
                $facultyMember = $facultyPQ[$facultyPQIndex];
				
				
                //Check their time preference (verify with correct array early[], midday[], afternoon[], night[])    
				$arrayOfTimes = array();
				$arrayOfTimesIndex = 0;
				
				$dayType = false;
				$nightType = false;
				$noPreferenceFlag = false;
				
				//Puts the avialable times for the current faculty member's time preference into arrayOfTimes
				switch($facultyMember->timePref)
				{
					case  "early": $arrayOfTimes = $classTimes[$classTimesIndex]->early;
									$dayType = true;
									break;

					case "midDay": $arrayOfTimes = $classTimes[$classTimesIndex]->midDay;
									$dayType = true;
									break;

					case "lateAfternoon": $arrayOfTimes = $classTimes[$classTimesIndex]->lateAfternoon;
										$dayType = true;
										break;

					case "night": $arrayOfTimes = $classTimes[$classTimesIndex]->night;
									$nightType = true;
									break;
									
					case "noPreference": $noPreferenceFlag = true;
										break;
                }
				/*
				if($dayType == true)
				{
					echo "<br>DAY-TYPE FOUND <br>";
					print_r($arrayOfTimes);
					echo "<br>";
				}
				else if($nightType == true)
				{
					echo "<br>NIGHT-TYPE FOUND<br>";
					print_r($arrayOfTimes);
					echo "<br>";
				}
				*/
				//The following if statement checks to see if a faculty member's current preference is schedulable or if they even made a preference
                if(($dayType == true and $daySectionsRemaining == 0) OR ($nightType == true and $nightSectionsRemaining == 0) OR ($noPreferenceFlag == true))
                {
                    //add course and section number (currentSectionNumber) to listOfUnscheduledCourses 
                        //we were removing the faculty member from the queue here, but we don't need
                        //to do that HERE because we're popping them off at the end of this if/else
                        //down on line 254
						
					echo "<br><h2> ENTERED BAD STATEMENT </h2><br>";
					
					//array_push($unscheduledCourses, $courseNamer."-".$currentSectionNumber);
                    //$currentSectionNumber++;
                    //"No more sections available for preferred time chosen";
                    //$currentSectionNumber++;
                }
                else
                {
					$foundRoom = false;
					$arrayOfTimesIndex = 0;
					//Iterating through the list of times associated with the current faculty member's block of time preference (early, mid-day, afternoon, or night)
                    while($arrayOfTimesIndex < count($arrayOfTimes) and ($foundRoom == false))
                    {
						$foundRoom = false;
                        $conflictExists = false;
						$alreadyTeaching = false;		
						$alreadyInSession = false;
						
						// For debugging purposes!
						echo "<br>Array of Times: ".$arrayOfTimes[$arrayOfTimesIndex]."<br>";
						
						do
						{
							$alreadyInSession = false;
							$inSessionCheck = $classTimes[$classTimesIndex]->minutes." ".$classTimes[$classTimesIndex]->daysOfWeek."/";
							$inSessionCheck = $inSessionCheck.$arrayOfTimes[$arrayOfTimesIndex];
							
							
							
							if(!in_array($inSessionCheck, $coursesToSchedule[$ctsIndex]->inSession))// or ((count(array_intersect(str_split($inSessionCheck), str_split(trim($classTimes[$classTimesIndex]->daysOfWeek)))) == 0)))
							{
								$alreadyInSession = false;
							}
							else
							{
								$alreadyInSession = true;
								echo "<br>Class already in session at this time increment<br>";
								$arrayOfTimesIndex++;
							}
							
						}while(($alreadyInSession == true) and ($arrayOfTimesIndex < count($arrayOfTimes)));
						
                        if ($conflictFileExists == true)
                        {
                            do//needs documenting not sure what it is doing exactly. 
                            {
                                $conflictExists = false;
								$tempArrayOfTimes = $classTimes[$classTimesIndex]->daysOfWeek."/".$arrayOfTimes[$arrayOfTimesIndex];
								echo "<br><h3>TEMP OF TIMES = $tempArrayOfTimes</h3><br>";
								print_r($conflictTimes);
                                
                                if (in_array(trim($tempArrayOfTimes), $conflictTimes))
                                {
                                    $conflictExists = true;
									echo "<br><h2>Conflict Exists Increment</h2> <br>";
                                    $arrayOfTimesIndex++;
                                }
                            }while(($conflictExists == true) and ($arrayOfTimesIndex < count($arrayOfTimes)));
                        }
						
                        // Checks to make sure the above loop found a time that wasn't conflicted with the list of conflicts
                        if($conflictExists == false)
                        {
							// Loops through array of times to find a time slot where the faculty member isn't teaching
                            do
                            {								
								$facultyMember = $facultyPQ[$facultyPQIndex];
								$facultyName = $facultyMember->userName;
								
								// Query to get the timeslot where a faculty member is already teaching
                                $query = "SELECT timeSlot FROM scheduledCourses WHERE facultyUser = '$facultyName'";
								$queryResult = mysqli_query($link, $query);
								$row = mysqli_fetch_row($queryResult);
								
								
								//$row[0] and $timeTemp are the two times to check so we need the specific time (e.g. 10:00) from each string
								$timeTemp = $classTimes[$classTimesIndex]->minutes." ".$classTimes[$classTimesIndex]->daysOfWeek."/";
								$timeTemp = $timeTemp.$arrayOfTimes[$arrayOfTimesIndex];
								
								// Splits the times into arrays
								$timeToScheduleFaculty = preg_split('/[\s+\/]/', $timeTemp);
								$alreadyTeachingFaculty = preg_split('/[\s+\/]/', $row[0]);
								
								// Check to make sure a faculty is already teaching at a time
								if($row)
								{
									print_r($timeToScheduleFaculty);
									print_r($alreadyTeachingFaculty);
									
									// $difference = the amount of minutes between the time in array of times and the time they are already teaching
									$difference = round(abs(strtotime($timeToScheduleFaculty[2]) - strtotime($alreadyTeachingFaculty[2])) / 60,2);
									
									// Check to make sure the minutes don't overlap the time they are currently teaching
									if($difference > $alreadyTeachingFaculty[0])
									{
										$noTimeOverlapFaculty = true;
									}
									else
									{
										$noTimeOverlapFaculty = false;
									}
									
								}
								else	// The faculty member wasn't teaching at a time yet
								{
								 	$noTimeOverlapFaculty = true;								
								}
								
								echo "<br><h3>$timeTemp</h3><br>";
								
								// Checks to make sure the faculty member isn't already teaching at the same time on the same day
                                if(($row[0] != $timeTemp) and ((count(array_intersect(str_split($row[0]), str_split(trim($classTimes[$classTimesIndex]->daysOfWeek)))) == 0) 
										or ($noTimeOverlapFaculty == true)))
                                {
                                    $alreadyTeachingFlag = false;
                                }
                                else	// Falls here if a faculty member ran into a conflicting time
                                {
                                    $alreadyTeachingFlag = true;
									$conflictExist = true;
									echo "<br>Already Teaching increment<br>";
									$arrayOfTimesIndex++;
                                }
                            }while(($alreadyTeachingFlag == true) and ($arrayOfTimesIndex < count($arrayOfTimes)));
							
							// Check to make sure we found a time slot that does not conflict with that particular faculty member or a course
							// is not already in session
                            if(($alreadyTeachingFlag == false) and ($conflictExists == false) and ($alreadyInSession == false)) 
                            {
								// Loop to find a room where the faculty member can teach at the specified time
                                $aorIndex = 0;
                                while(($aorIndex < count($arrayOfRooms)) and ($foundRoom == false))
                                {									
								
                                    // Check to make sure the room type and size is correct for the current class size and time
                                    if (($coursesToSchedule[$ctsIndex]->classType == $arrayOfRooms[$aorIndex]->roomType) and ($coursesToSchedule[$ctsIndex]->classSize <= $arrayOfRooms[$aorIndex]->roomSize))
                                    {            
										// Grabs the array of unavailable times for the current room
                                        $unavailableTimesArray = $arrayOfRooms[$aorIndex]->unavailableTimes;
                                        
										// Debug statements
										echo "<br>Room name: ".$arrayOfRooms[$aorIndex]->roomName."<br>";
										echo "UnavailableTimesArray:    ";
										print_r($arrayOfRooms[$aorIndex]->unavailableTimes);
										echo "<br>";
										
										// Creates a temporary time variable of the current class time 
										$tempTime = $classTimes[$classTimesIndex]->minutes." ".$classTimes[$classTimesIndex]->daysOfWeek."/";
										$tempTime = $tempTime.$arrayOfTimes[$arrayOfTimesIndex];
										
										// Check to make sure the room is available at the current time
                                        if(!in_array($tempTime, $unavailableTimesArray))
                                        {    
											$roomAvailable = true;
											$noTimeOverlapRoom = true;
											// Loops through the preferred times by the faculty member to find if the room is available at a time
											for($i = 0; $i < count($unavailableTimesArray); $i++)
											{
												// Splits the times into arrays
												$timeToScheduleRoom = preg_split('/[\s+\/]/', $tempTime);
												$alreadyTeachingRoom = preg_split('/[\s+\/]/', $unavailableTimesArray[$i]);
												
												// Debugging statements
												print_r($timeToScheduleRoom);
												print_r($alreadyTeachingRoom);
												
												// $difference = the amount of minutes between the time in array of times 
															// and the time they are already teaching
												$difference = round(abs(strtotime($timeToScheduleRoom[2]) - strtotime($alreadyTeachingRoom[2])) / 60,2);
												// Checks to make sure the times don't overlap for the room
												if($difference > $alreadyTeachingRoom[0])
												{
													$noTimeOverlapRoom = true;
												}
												else
												{
													$noTimeOverlapRoom = false;
												}
												
												// Debugging statements
												echo "Difference of $timeToScheduleRoom[2] and $alreadyTeachingRoom[2] is: $difference<br>";
												echo "<br> ENTERED FOR LOOP :   ".$unavailableTimesArray[$i]."<br>";
												echo(count(array_intersect(str_split($unavailableTimesArray[$i]), str_split(trim($classTimes[$classTimesIndex]->daysOfWeek)))));
												
												// Check to make sure the room is not already being taught at the current time on the same day
												if((count(array_intersect(str_split($unavailableTimesArray[$i]), str_split(trim($classTimes[$classTimesIndex]->daysOfWeek)))) == 0) or ($noTimeOverlapRoom == true))
												{
													$roomAvailable = true;	// This must happen for every time in the list of unavailable times array
												}
												else	// Falls here if a the current time is unschedulable for this room and kicks out of the loop
												{
													$roomAvailable = false;
													$i = count($unavailableTimesArray);
												}
											}
                                        }
                                        else 		// Falls here the room is already scheduled for the current time
                                        {
                                            $roomAvailable = false;
                                        }
                                            
                                        if($roomAvailable == true)
                                        {
                                            $foundRoom = true;
											
											// Temporaries for insertion
											$tempCourse = $coursesToSchedule[$ctsIndex]->name;
											$tempFaculty = $facultyPQ[$facultyPQIndex]->userName;
											$tempRoom = $arrayOfRooms[$aorIndex]->roomName;
											
											// Insert the scheduled course into the database 
											$scheduleQuery1 = "INSERT INTO scheduledCourses (course, section, timeSlot, facultyUser, roomName) VALUES (";
											$scheduleQuery2 = "'$tempCourse', '$currentSectionNumber', '$tempTime', '$tempFaculty', '$tempRoom')";
											$scheduleQuery = $scheduleQuery1.$scheduleQuery2;
											$scheduleResult = mysqli_query($link, $scheduleQuery);
											
											// Debug statements
											if($scheduleResult)
											{
												echo "<br> Insertion Succeeded!!!!!!!!!!!!!!!! <br>";
											}
											else
											{
												echo "<br> insertion failed... <br>";
											}
											
											// Decrements the number of sections left depending on whether it is a day type or night type
											if($dayType == true)
											{
												$daySectionsRemaining--;
											}
											else if($nightType == true)
											{
												$nightSectionsRemaining--;												
											}
											
											// Add teaching hours to current teaching hours for faculty member teaching the course
											for($i = 0; $i < count($arrayOfFaculty); $i++)
											{
												if(strtolower($arrayOfFaculty[$i]->userName) == $facultyPQ[$facultyPQIndex]->userName)
												{
													$arrayOfFaculty[$i]->currentHours = trim($arrayOfFaculty[$i]->currentHours) + trim($coursesToSchedule[$ctsIndex]->creditHours);
												}
											}
											// Append time slot to list of unavailable time for the current room and course
											$arrayOfRooms[$aorIndex]->addUnavailableTimes($tempTime);
											$coursesToSchedule[$ctsIndex]->addInSessionTimes($tempTime);
											
											// Increments the section number and number of scheduled sections
											$scheduledSections++;
											$currentSectionNumber++;
                                        }
                                    }//endif
									
									// Increment to the next room
									$aorIndex++;
									
                                }//end rooms while
								
								// If we still haven't found a room, then go to the next time in the array of times
                                if($foundRoom == false)
                                {
                                    $arrayOfTimesIndex++;
                                }
							}//end if 
                        }//end if
						
                    }//end times while
					
					// If we still haven't found a room, then increment to the next list of times
					if($foundRoom == false)
					{
                        $classTimesIndex++;
					}
                }//endelse
				
				// This is where we increment the faculty member if we found a room and there are more faculty members that selected this course
				// THIS STATEMENT MIGHT BE ABLE TO BE CHANGED AND MOVED
                if(($facultyPQIndex < count($facultyPQ)) and (($foundRoom == true) OR ($noPreferenceFlag == true)))
                {
					// If a faculty member chose no preference, then we add them to the list of unscheduled courses here
					if($noPreferenceFlag == true)
					{
						$courseToPush = $coursesToSchedule[$ctsIndex]->name."-".$currentSectionNumber."  -  FACULTY MEMBER DIDN'T CHOOSE A PREFERENCE";
						array_push($unscheduledCourses, $courseToPush);
						$currentSectionNumber++;
						$scheduledSections++;
					}
                    $facultyPQIndex++;	
                }
				// I'm not sure about this statement, might be incorrect or unneccesary
				else if(($dayType == true and $daySectionsRemaining == 0) OR ($nightType == true and $nightSectionsRemaining == 0))
				{
					$facultyPQIndex++;
				}		
               
				
            }//endwhile
			
			// More debugging statements
			echo "<br><h3> EXITED WITH Scheduled Sections = $scheduledSections </h3><br>";
			echo "<br><h3> EXITED WITH Faculty queue index = $facultyPQIndex </h3><br>";
			echo "<br><h3> EXITED WITH Class Times index = $classTimesIndex </h3><br>";
			
			
        }//endelse
		
		//We reach this block because the priority queue is empty or we ran out of class times or we could have ran out of courses to schedule
		if(($scheduledSections < ($daySections + $nightSections + $coursesToSchedule[$ctsIndex]->internetSections)))
		{
			// Pushes the sections that haven't been scheduled into the list of unscheduled sections
			while($scheduledSections < ($daySections + $nightSections + $coursesToSchedule[$ctsIndex]->internetSections))
			{
				echo "<br><h3> ENTERED TEST LOOP </h3><br>";
				$courseToPush = $coursesToSchedule[$ctsIndex]->name."-".$currentSectionNumber;
				array_push($unscheduledCourses, $courseToPush);
				$currentSectionNumber++;
				$scheduledSections++;
			}
		}
		
		// Done and moving on to the next course
		$currentSectionNumber = 1;
		$ctsIndex++;
        //schedule next course
    }//end while


	echo "Unscheduled Courses: <br>";
	print_r($unscheduledCourses);
	for($i = 0; $i < count($unscheduledCourses); $i++)
	{
		echo "<br>".$unscheduledCourses[$i]."<br>";
	}
	echo "Unscheduled2 Courses: <br>";
	print_r($unscheduledCourses2);
	for($i = 0; $i < count($unscheduledCourses2); $i++)
	{
		echo "<br>".$unscheduledCourses2[$i]."<br>";
	}
	
	// Prints out the array of faculty members that have not met their minimum hours requirement
	for($i = 0; $i < count($arrayOfFaculty); $i++)
	{
		if($arrayOfFaculty[$i]->currentHours < $arrayOfFaculty[$i]->requiredMinHours)
		{
			echo "<br>".$arrayOfFaculty[$i]->userName." didn't meet their minimum hours of ".$arrayOfFaculty[$i]->requiredMinHours."<br>";
			echo "Currently at: ".$arrayOfFaculty[$i]->currentHours."<br>";
		}
	}
	
	
	// We would start scheduling the faculty members that haven't met their minimum hours requirement here
?>