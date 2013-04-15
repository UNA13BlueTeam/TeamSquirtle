<?php 
/*-----------------------------------------------------------------------------------------------
 ********************** Scheduling Algorithm Function Prologue  ********************
 * Preconditions: 
 *
 * Postconditions: 
 *                  
 * Function Purpose: 
 *
 * Input Expected: 
 *
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
 * Modified By (Name and Date):
 * Modifications Description:
 *
 * Modified By (Name and Date):
 * Modifications Description:
 -------------------------------------------------------------------------------------------------*/
 
 
	$host = "127.0.0.1";
	$user = "leonie";
	$pass = "4EzvHW5C";
	$db = "teamsquirtle";
	$port = 45000;
	  
	$link = mysqli_connect($host, $user, $pass, $db, $port);

	include ("classes.php");
	
	//Varaible declarations
	$sortByYOS = true;
	$sortByTOS = false;
	$ctsIndex = 0;    // Courses to schedule index
	$classTimesIndex = 0;   
	$missingConflictFile = false;
	$conflictExists = false;
	$scheduledSections = 0;
	
	//put if stmt to check for missing conflict file.
	//Create array of unscheduled courses (listOfUnscheduledCourses)
	$unscheduledCourses = array();
	 
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
	
	
	while ($ctsIndex < count($coursesToSchedule))
    {
		// Check if a conflict exists for this course
		$courseNamer = $coursesToSchedule[$ctsIndex]->name;
		echo "$courseNamer  ";
		$conflictQuery = "SELECT course FROM conflicts WHERE course = '$courseNamer'";
		$missingConflict = mysqli_query($link, $conflictQuery);
		$row = mysqli_fetch_row($missingConflict);
		
        if ($row[0] != $courseNamer)
		{			
            $conflictFileExists = false;
			//echo "No conflict found <br>";
        }
        else
        {
            $conflictFileExists = true;
			//echo "Conflict found <br>";
        }
        
        //Get day and night sections from course (as constants)
        
        $daySections = $coursesToSchedule[$ctsIndex]->daySections; 
        $nightSections= $coursesToSchedule[$ctsIndex]->nightSections;
		
		
		$facultyPQ = array();
		
		// Sort preferences table by years of service
		if($sortByYOS == true)
        {
			//Retrieve all faculty members that chose this course in their preferences
			$facultyQuery = "SELECT facultyUser, yos, tos, timePref FROM preferences WHERE courseName = '$courseNamer' ORDER BY yos DESC";
			$facultyResult = mysqli_query($link, $facultyQuery);
			
			while($row = mysqli_fetch_row($facultyResult))
			{
				// Call to get minimum hours for faculty
				$facultyQuery2 = "SELECT minHours FROM faculty WHERE email = '$row[0]'";
				$facultyResult2 = mysqli_query($link, $facultyQuery2);
				$row2 = mysqli_fetch_row($facultyResult2);
				
									//	 username   yos      tos     timePref   minHours   
				$addFaculty = new Faculty($row[0], $row[1], $row[2], $row[3], $row2[0]);
				$addFaculty->printer();
				array_push($facultyPQ, $addFaculty);
			}
		   
        }
		// Sort preference table by time of submission
		elseif($sortByTOS == true)
        {
			//Retrieve all faculty members that chose this course in their preferences
			$facultyQuery = "SELECT facultyUser, yos, tos, timePref FROM preferences WHERE courseName = '$courseNamer' ORDER BY tos ASC";
			$facultyResult = mysqli_query($link, $facultyQuery);
			while($row = mysqli_fetch_row($facultyResult))
			{
				// Call to get minimum hours for faculty
				$facultyQuery2 = "SELECT minHours FROM faculty WHERE email = '$row[0]'";
				$facultyResult2 = mysqli_query($link, $facultyQuery2);
				$row2 = mysqli_fetch_row($facultyResult2);
				
									//	 username   yos      tos     timePref   minHours   
				$addFaculty = new Faculty($row[0], $row[1], $row[2], $row[3], $row2[0]);
				$addFaculty->printer();
				array_push($facultyPQ, $addFaculty);
			}
        }
		
		
		
        if (count($facultyPQ) == 0)
        {
			// Put courseToSchedule[ctsIndex] on array of unscheduled courses “No faculty selected coursesToSchedule[ctsIndex].courseName”
			array_push($unscheduledCourses, $courseNamer);
        }
        else //(declare variables for number of day and night sections left)
        {
            $daySectionsRemaining = $daySections;
            $nightSectionsRemaining = $nightSections;
            $scheduledSections = 0;
            $currentSectionNumber = 1;
			
			while ((count($facultyPQ) != 0) and ($scheduledSections < $daySections + $nightSections))
            {
                //Check front of priority queue
                $facultyMember = $facultyPQ[0];
				
                //Check their time preference (verify with correct array early[], midday[], afternoon[], night[])    
				$arrayOfTimes = array();
				$arrayOfTimesIndex = 0;
				
				$dayType = false;
				$nightType = false;
				
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
                }
				
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
				
                if(($dayType== true and $daySectionsRemaining == 0) OR ($nightType == true and $nightSectionsRemaining == 0))
                {
                    //add course and section number (currentSectionNumber) to listOfUnscheduledCourses 
                        //we were removing the faculty member from the queue here, but we don't need
                        //to do that HERE because we're popping them off at the end of this if/else
                        //down on line 254
					array_push($unscheduledCourses, $courseNamer."-".$currentSectionNumber);
                        
                    //"No more sections available for preferred time chosen";
                    $currentSectionNumber++;
                }
				
				$scheduledSections++;	// <- TAKE THIS OUT WHEN YOU MOVE ON
				
				/*
                else
                {
                    while($arrayOfTimesIndex < count($arrayOfTimes))
                    {
                        $conflictExists = false;
                        if ($conflictFileExists == true)
                        {
                            do
                            {
                                $conflictExists = false;
                                //SELECT conflictString FROM conflicts WHERE courseName = coursesToSchedule[ctsIndex]
                                //$arrayOfConflicts = explode(trim(conflictString returned from above statement))
                                
                                if ($arrayOfTimes[$arrayOfTimesIndex] in $arrayOfConflicts)
                                {
                                    $conflictExists = true;
                                    $arrayOfTimesIndex++;
                                }
                            }while($conflictExists == true) and ($arrayOfTimesIndex < count($arrayOfTimes))
                        }
                        // Loops through array of times to find a time the faculty member is not teaching at
                        if($conflictExists == false)
                        {
                            do
                            {
                                //Check to see if arrayOfTimes[arrayOfTimesIndex] is in database in “Scheduled Courses Table” WHERE facultyName = facultyMember.name   (alreadyTeaching == result)
                                //SELECT DISTINCT classTime FROM scheduleCourses WHERE facultyEmail = facultyMember.facultyEmail
                                if(above statement returns null)
                                {
                                    $alreadyTeaching = false;
                                }
                                else
                                {
                                    $alreadyTeaching = true;
                                }
                                
                                if $arrayOfTimes[$arrayOfTimesIndex] and $facultyMember.name already exists together ($alreadyTeaching == true)
                                {
                                    $arrayOfTimesIndex++
                                }
                            }while($alreadyTeaching == true) and ($arrayOfTimesIndex < count($arrayOfTimes))
                        
                            if($alreadyTeaching == false) //we found a time slot that does not conflict with that particular faculty member
                            {
                            
                                //conflictExists == false
                                //Find room
                                $arrayOfRooms = array();
                                //(SELECT DISTINCT FROM Rooms)
								// Create a Rooms object for each row returned from above statement
                                
								//To find a room for the selected time slot (Scheduled Courses Table)
                                for ($aorIndex = 0; $aorIndex < count($arrayOfRooms) and ($foundRoom = false); $aorIndex++)
                                {
                                    //check if room type is ok
                                    if ($coursesToSchedule[$ctsIndex].$classType == $arrayOfRooms[$aorIndex].$roomType) and 
                                       ($coursesToSchedule[$ctsIndex].$classSize <= $arrayOfRooms[$aorIndex].$roomSize)
                                    {                                                                                
                                        //$unanvailableTimesArray = split arrayofRooms[aorIndex].unavailableTimes into an array of times // preg_split function
                                        
                                        if(!in_array($arrayOfTimes[$arrayOfTimesIndex], $unanvailableTimesArray))
                                        {
                                            $roomAvailable = true;
                                        }
                                        else
                                        {
                                            $roomAvailable = false;
                                        }
                                            
                                        if($roomAvailable == true)
                                        {
                                            $foundRoom = true;
                                            //add courseName-sectionNumber to scheduledCourses table
                                            /*
                                                courseName
                                                course section number
                                                facultyMember
                                                classTime
                                                room
                                                time slot
                                            *   
                                            $scheduledSections++
                                            //append time slot to arrayofRooms[aorIndex].unavailableTimes     
                                            break; //exit for loop                                                                                          
                                        }
                                    }//endif
                                }//endfor
                                if($foundRoom == true)
                                {
                                    $currentSectionNumber++
                                    $scheduledSection++
                                }
                                else
                                {
                                    $arrayOfTimesIndex++
                                }
                            }//end else
                        }//end if
                    }//end while
                    if($foundRoom == false)
                    {
                        //"Unable to find an available room for courseName-currentSectionNumber during selected time preference"
                        //Put course-sectionNumber on list of unscheduled courses
                        $classTimesIndex++
                    }
                }//endelse
                if($facultyPQ.isEmpty()== false and $foundRoom == true)
                {
                    //pop off top faculty member
                    $temp= $facultyPQ.extract(); //do nothing with temp
                    $currentSectionNumber++
                    $scheduledSection++
                }
                else
                {
                    //We reach this block because the priority queue is empty
                    //scheduledSections can still be less than daySections + nightSections
                    //So at this point we probably need to repopulate our priority queue in
                    //order to continue scheduling the rest of the sections
                    
                    //Or we could allow each faculty member to select the number of
                    //sections of a course they want to teach, which would put them on
                    //the priority queue for the course multiple times. Then if the priority
                    //queue is empty with sections remaining, they just go unscheduled due
                    //to no professor selecting to teach them
                    
                    while($scheduledSections < $day + $night)
                    {
                        //put courseName-currentSectionNumber on list of unscheduled courses
                        $scheduledSection++;
                        $currentSectionNumber++;
                    }
                }
				*/
            }//endwhile
        }//endelse
		
        $ctsIndex++;
        //schedule next course
    }//end while


	echo "Unscheduled Courses: <br>";
	print_r($unscheduledCourses);
















?>
 
