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
 * Modified By (Name and Date): Michael Debs April 20, 12:39am
 * Modifications Description:	Integrated with application
 *								Allows for administrator to choose how to prioritize faculty members
 *
 * Modified By (Name and Date): Michael Debs April 20, 4:37pm
 * Modifications Description:   Courses faculty chose with no preference given are now scheduled
 *
 * Modified By (Name and Date): Michael Debs, Jared Cox April 21, 7:29pm
 * Modifications Description:   Fixed severe flaw that allowed a teacher to teach two 
 *								different classes at the same time in two separate rooms
 *
 * Modified By (Name and Date): Jared Cox April 23, 8:19pm
 * Modifications Description:   Unscheduled Courses now push to a table in the database for
 *								later use, and internet sections are separated from non-
 *								internet sections.
 *								
 -------------------------------------------------------------------------------------------------*/
 
 
	$host = "127.0.0.1";
	$user = "leonie";
	$pass = "4EzvHW5C";
	$db = "teamsquirtle";
	$port = 45000;
	  
	$link = mysqli_connect($host, $user, $pass, $db, $port);
	mysqli_query($link, "TRUNCATE TABLE scheduledCourses");
	mysqli_query($link, "TRUNCATE TABLE unscheduledCourses");
	set_time_limit(0);
	include ("classes.php");
	
	//flags
	#############################
	//ONE OF THESE MUST BE TRUE//
		$sortByYOS = false;		//flag to sort by years of service
		$sortByTOS = false;		//flag to sort by time of submission
	//ONE OF THESE MUST BE TRUE//
	#############################
	
		
	$conflictExists = false;	/*flag noting if conflict exists for specific class time
									we are trying to schedule */
	$conflictFileExists = false; // flag noting if there is any conflicts at all for a course
	$foundRoom = false;			//flag noting if a room has been found to schedule a course in
	$roomAvailable = false;		//true if the room is available to be scheduled in at the specific time
	$dayType = false;			//flag noting if a preference belongs to a day time
	$nightType = false;			//flag noting if a preference belongs to a night time
	$noPreferenceFlag = false;	//a faculty member selected no preference for a course they want to teach
    
	$alreadyTeaching = false;   /*flag noting if a faculty member is already teaching during a time we are
									to schedule for that faculty member */
	$alreadyInSession = false;	/*flag noting if a course is already being taught in a room during a time
									we are trying to schedule a class for */
	$noTimeOverlapFaculty = false; /*flag noting if a class time to schedule overlaps with a class
									 time already scheduled to a faculty member*/
	$noTimeOverlapRoom = false; /*flag noting if a class time to schedule overlaps with a class
									 time already scheduled to a specific room*/
	$facultyMissingFlag = false;    //checks for existance of file
	$coursesMissingFlag = false;	//checks for existance of file
	$classTimesMissingFlag = false;	//checks for existance of file
	$roomsMissingFlag = false;		//checks for existance of file
	
	//Variable declarations
	$ctsIndex = 0;    // Courses to schedule index for 
	$classTimesIndex = 0;   //index for classTimes array
	$daySections;			//constant number of day sections for a course, defined later
    $nightSections;			//constant number of night sections for a course, defined later
	$scheduledSections = 0;	//number of scheduled sections throughout the algorithm
	$daySectionsRemaining;	//for a single course
	$nightSectionsRemaining; //for a single course
	$currentSectionNumber = 1;	//tracks the number of sections for each course when scheduled
	$addCourse;				//holds a course object to be added to coursesToSchedule array
	$coursesToSchedule;		//array of courses, defined later
	$unscheduledCourses;	//array of courses that weren't scheduled for various reasons, defined later
	$unscheduledCourses2;	/*courses that weren't scheduled because no faculty
						     member selected it when choosing preferences, defined later */
	$courseToPush;			//course to be put on array of unscheduledCourses
	$addTimeSlot;			//holds a class time object to be added to classTimes array
	$classTimes;			//array of class times, defined later
	$classTimesIndex = 0;	//index for classTimes array
	$addRooms;				//holds a room object to be added to arrayOfRooms
	$arrayOfRooms;			//array of rooms to schedule classes in, defined later
	$aorIndex = 0;			//index for arrayOfRooms
	$unavailableTimesArray; //array of times that a room is unavailable to teach in, defined later
	$overlapCheckRoom;		/*temporary variable storing a specific class time that is used
							  to check if it's length overlaps with a course being taught in
							  the room already*/
	$addFaculty;			//holds a faculty object to be added to arrayOfFaculty
	$arrayOfFaculty;		//array of faculty members to teach courses, defined later
	$alreadyTeachingTimes;	//array of times a faculty member is already teaching, define later
	$alreadyTeachingTimesIndex = 0; //index for alreadyTeachingTimes
	$conTimes;				//string of all conflict times that gets broken into an array later
	$conflictTimes;			/*array of conflict times that further determine when a course
							  can be scheduled, defined later */
	$conflictCheck;		    /*temporary variable storing a specific class time that is used
							  to check for conflicts at this time for a specific course*/
	$overlapCheckFaculty;	/*temporary variable storing a specific class time that is used
							  to check if it's length overlaps with a coruse being taught after it */
	$timeToScheduleFaculty = array(); /*array containing a broken up class time to be scheduled:  
										[0] is minutes  [1] is days of week  [2] is time of day in HH:MM */
	$alreadyTeachingFaculty = array(); /*array containing a broken up class time already scheduled to a
										faculty member. [0] is minutes  [1] is days of week  
												[2] is time of day in HH:MM */
	$timeToScheduleRoom; 	/*array containing a broken up class time to be scheduled:  
								[0] is minutes  [1] is days of week  [2] is time of day in HH:MM */
	$alreadyTeachingRoom;	 /*array containing a broken up class time already scheduled to a
								room. [0] is minutes  [1] is days of week  
									  [2] is time of day in HH:MM */
	$difference;			//difference in minutes of timeToScheduleFaculty[2] and alreadyTeachingFaculty[2]
	$facultyPQ;				//array of faculty member preferences treated as a priority queue for scheduling
	$facultyPQIndex = 0;	//index for facultyPQ array
	$facultyMember;			//object used to store current facultyPQ index for quick access
	$facultyName;			//derived from facultyMember for quick access
	$arrayOfTimes;			//array of class times available for a faculty's time preference
	$arrayOfTimesIndex = 0;	//index for arrayOfTimes
	
	$courseNamer;			//used in checking for conflict times for a specific course
	
	//SQL-related variables
	$row;					//used for mysqli_fetch_row statements throughout
	$row2;					//used for mysqli_fetch_row statements throughout
	$selectCoursesQuery;	//query to retrieve all courses in the database that need to be scheduled
	$selectCoursesResult;	//success or failure of selectCourseQuery
	$selectClassTimesQuery; //query to retrieve all class times in the database that courses can be scheduled at
	$selectClassTimesResult; //success or failure of selectClassTimesQuery
	$selectRoomsQuery;		 //query to retrieve all rooms in the database that courses can be scheduled in
	$selectRoomsResult;		 //success or failure of selectRoomsQuery
	$selectFacultyQuery;	 //query to retrieve all faculty members in the database to track how many hours they are scheduledto teach
	$selectFacultyResult;	 //success or failure of selectFacultyQuery
	$selectConflictQuery;	 //query to retrieve all conflicts for specific course "courseNamer"
	$selectConflictResult;	 //success or failure of selectConflictQuery
	$selectFacultyPreferenceQuery;	//query to retrieve all faculty members that chose this course in their preferences
	$selectFacultyPreferenceResult; //success or failure of selectFacultyPreferenceQuery
	$facultyQuery2;					//used in conjunction with selectFacultyPreferenceQuery
	$facultyResult2;				//success or failure of facultyQuery2
	$selectFacultyTeachingQuery;  	// Query to get the timeslot where a faculty member is already teaching
	$selectFacultyTeachingResult; 	//success or failure of selectFacultyTeachingQuery
	$tempCourse;
	$tempFaculty;
	$tempRoom;						//temporary variables used for 	SQL statements
	$scheduleQuery1;
	$scheduleQuery2;				/*query to insert scheduled courses to database.  we have 2 because
										the statement is lengthy, and we break it up to make it more readable*/
	$scheduleQuery;					//concatenation of scheduleQuery1 and 2 for readability
	$scheduleResult;				//success or failure of scheduleQuery
	$pushUnscheduledQuery;			//query to insert unscheduled courses to database
	$pushUnscheduledResult;			//success or failure of pushUnscheduledQuery
	
	
	

	
	
##################### BEGIN SCHEDULING #################################

	//Set flags for which priority to use for faculty
	$sorter = $_POST['sort'];
	if($sorter == "years")
	{
		$sortByYOS = true;
	}
	else if($sorter == "times")
	{
		$sortByTOS = true;
	}

	//Create array of courses (coursesToSchedule)
	$coursesToSchedule = array();
	
	
	//Create array of unscheduled courses (listOfUnscheduledCourses)
	$unscheduledCourses = array();		
	$unscheduledCourses2 = array();		
	 
	
	$selectCoursesQuery = "SELECT courseName, dsection, nsection, isection, classSize, roomType, hours FROM courses ORDER BY courseName DESC";
	$selectCoursesResult = mysqli_query($link, $selectCoursesQuery);
	
	while($row = mysqli_fetch_row($selectCoursesResult))
	{//add each course retrieved from database to coursesToSchedule array
	
								//name   //dsecs   //nsecs  //isecs  //size   //type   //hours
		$addCourse = new Course($row[0], $row[1],  $row[2], $row[3], $row[4], $row[5], $row[6]);
		// Uncomment below to print out array of courses
		//$addCourse->printer();
		array_push($coursesToSchedule, $addCourse);
	}	
	
	//Create array of class times (classTimes)
	$classTimes = array();
	
	
	$selectClassTimesQuery = "SELECT minutes, daysOfWeek, timesOfDay FROM timeSlots";
	$selectClassTimesResult = mysqli_query($link, $selectClassTimesQuery);
	
	while($row = mysqli_fetch_row($selectClassTimesResult))
	{//add each time slot retrieved from database to classTimes array)
	
									 //minutes  //daysofweek   //timesOfDay
		$addTimeSlot = new ClassTime($row[0],   $row[1],       $row[2]);
		// Uncomment below to print out array of time slots
		//$addTimeSlot->printer();
		array_push($classTimes, $addTimeSlot);
	}
	
	//Create an array of rooms
	$arrayOfRooms = array();
	
	
	$selectRoomsQuery = "SELECT roomType, size, roomName FROM rooms ORDER BY size ASC";
	$selectRoomsResult = mysqli_query($link, $selectRoomsQuery);
	
	while($row = mysqli_fetch_row($selectRoomsResult))
	{//add each room retrieved from database to arrayOfRooms array
	
							//room type	//size  //building#
		$addRooms = new Room($row[0], $row[1], $row[2]);
		// Uncomment below to print out array of courses
		$addRooms->printer();
		array_push($arrayOfRooms, $addRooms);
	}

	//Create an array of faculty members
	$arrayOfFaculty = array();
	
	//query to retrieve all faculty members in the database to track how many hours they are scheduledto teach
	$selectFacultyQuery = "SELECT email, minHours FROM faculty";
	$selectFacultyResult = mysqli_query($link, $selectFacultyQuery);
	
	while($row = mysqli_fetch_row($selectFacultyResult))
	{//add each faculty member to arrayOfFaculty array
	
		$addFaculty = new FacultyMin($row[0], $row[1]);
		// Uncomment below to print out faculty member's min hours
		//$addFaculty->printer();
		array_push($arrayOfFaculty, $addFaculty);
	}
	
	$facultyMissingFlag = false;
	$coursesMissingFlag = false;
	$classTimesMissingFlag = false;
	$roomsMissingFlag = false;
	
	if(empty($arrayOfFaculty))
	{
		$facultyMissingFlag = true;
	}
	if(empty($coursesToSchedule))
	{
		$coursesMissingFlag = true;
	}
	if(empty($classTimes))
	{
		$classTimesMissingFlag = true;
	}
	if(empty($arrayOfRooms))
	{
		$roomsMissingFlag = true;
	}
	
	if(($facultyMissingFlag == false) and ($coursesMissingFlag == false) and ($classTimesMissingFlag == false) and ($roomsMissingFlag == false))
	{
		//Main loop to iterate through array of courses to schedule
		$outFile = fopen("generatedFiles/unscheduled.txt", "w");
		while ($ctsIndex < count($coursesToSchedule))
		{//while there are courses left to schedule
		
			// Check if a conflict exists for this course
			$courseNamer = $coursesToSchedule[$ctsIndex]->name; //courseNamer should be courseName
			echo "$courseNamer  ";
			
			//query to retrieve all conflicts for specific course "courseNamer"
			$selectConflictQuery = "SELECT course, times FROM conflicts WHERE course = '$courseNamer'";
			$selectConflictResult = mysqli_query($link, $selectConflictQuery);
			
			$row = mysqli_fetch_row($selectConflictResult);
			
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
				$selectFacultyPreferenceQuery = "SELECT facultyUser, yos, tos, timePref FROM preferences WHERE courseName = '$courseNamer' ORDER BY yos DESC, CAST(tos AS SIGNED) ASC";
				$selectFacultyPreferenceResult = mysqli_query($link, $selectFacultyPreferenceQuery);
				
				while($row = mysqli_fetch_row($selectFacultyPreferenceResult))
				{
					// Call to get minimum hours for faculty
					$facultyQuery2 = "SELECT minHours FROM faculty WHERE email = '$row[0]'";
					$facultyResult2 = mysqli_query($link, $facultyQuery2);
					$row2 = mysqli_fetch_row($facultyResult2);
					
										//	 username   yos      tos     timePref   minHours   
					$addFaculty = new FacultyPref($row[0], $row[1], $row[2], $row[3], $row2[0]);
					//uncomment to print faculty preferences
					//$addFaculty->printer();
					array_push($facultyPQ, $addFaculty);
				}
			   
			}
			// Sort preference table by time of submission
			else if($sortByTOS == true)
			{
				//Retrieve all faculty members that chose this course in their preferences
				$selectFacultyPreferenceQuery = "SELECT facultyUser, yos, tos, timePref FROM preferences WHERE courseName = '$courseNamer' ORDER BY CAST(tos AS SIGNED) ASC, yos DESC";
				$selectFacultyPreferenceResult = mysqli_query($link, $selectFacultyPreferenceQuery);
				while($row = mysqli_fetch_row($selectFacultyPreferenceResult))
				{
					// Call to get minimum hours for faculty
					$facultyQuery2 = "SELECT minHours FROM faculty WHERE email = '$row[0]'";
					$facultyResult2 = mysqli_query($link, $facultyQuery2);
					$row2 = mysqli_fetch_row($facultyResult2);
					
										//	 username   yos      tos     timePref   minHours   
					$addFaculty = new FacultyPref($row[0], $row[1], $row[2], $row[3], $row2[0]);
					//uncomment to print faculty preferences
					//$addFaculty->printer();
					array_push($facultyPQ, $addFaculty);
				}
			}
			
			if (count($facultyPQ) == 0)
			{
				// Put courseToSchedule[ctsIndex] on array of unscheduled courses “No faculty selected coursesToSchedule[ctsIndex].courseName”
				
				echo "<br>EMPTY PQ   $currentSectionNumber<br>";
				while($scheduledSections < ($daySections + $nightSections))
				{
					array_push($unscheduledCourses2, $courseNamer."-".$currentSectionNumber);
					$output = "$courseNamer-$currentSectionNumber:  No faculty member selected this course in preferences.\r";
					fwrite($outFile, $output);
					$pushUnscheduledQuery = "INSERT INTO unscheduledCourses (course, section, internet) VALUES ('$courseNamer', '$currentSectionNumber', 0)";
					$pushUnscheduledResult = mysqli_query($link, $pushUnscheduledQuery);
					$scheduledSections++;
					$currentSectionNumber++;
				}
				for($i = 0; $i < $coursesToSchedule[$ctsIndex]->internetSections; $i++)
				{
					$output = "$courseNamer-$currentSectionNumber:  Internet section must be scheduled manually.\r";
					fwrite($outFile, $output);
					$pushUnscheduledQuery = "INSERT INTO unscheduledCourses (course, section, internet) VALUES ('$courseNamer', '$currentSectionNumber', 1)";
					$pushUnscheduledResult = mysqli_query($link, $pushUnscheduledQuery);
					$scheduledSections++;
					$currentSectionNumber++;
				}	
			}
			else // we have a priority queue of faculty members who wish to teach the current course
			{
				 //(declare variables for number of day and night sections left)
				$daySectionsRemaining = $daySections;
				$nightSectionsRemaining = $nightSections;
				$scheduledSections = 0;
				$currentSectionNumber = 1;
				
				$classTimesIndex = 0;
				
				echo "<br><h3> $daySections + $nightSections </h3><br>";
				
				shuffle($classTimes);
				
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
										
						case "noPreference": $arrayOfTimes = $classTimes[$classTimesIndex]->noPref;
											$noPreferenceFlag = true;
											break;
					}
					
					/*The following if statement checks to see if a faculty member's current preference is schedulable or if they even made a preference
					if(($dayType == true and $daySectionsRemaining == 0) OR ($nightType == true and $nightSectionsRemaining == 0))
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
					}*/
					if(!(($dayType == true and $daySectionsRemaining == 0) OR ($nightType == true and $nightSectionsRemaining == 0)))
					{
						$foundRoom = false;
						$arrayOfTimesIndex = 0;
						//Iterating through the list of times associated with the current faculty member's block of time preference (early, mid-day, afternoon, or night)
						while($arrayOfTimesIndex < count($arrayOfTimes) and ($foundRoom == false))
						{
							$foundRoom = false;
							$conflictExists = false;
							$alreadyTeachingFlag = false;		
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
									$conflictCheck = $classTimes[$classTimesIndex]->daysOfWeek."/".$arrayOfTimes[$arrayOfTimesIndex];
									echo "<br><h3>TEMP OF TIMES = $conflictCheck</h3><br>";
									print_r($conflictTimes);
									
									if (in_array(trim($conflictCheck), $conflictTimes))
									{/*if conflictCheck exists in conflict times, we try the next
									   time in arrayOfTimes*/
										$conflictExists = true;
										echo "<br><h2>Conflict Exists Increment</h2> <br>";
										$arrayOfTimesIndex++;
									}
								}while(($conflictExists == true) and ($arrayOfTimesIndex < count($arrayOfTimes)));
							}
							
							// Checks to make sure the above loop found a time that wasn't conflicted with the list of conflicts
							if($conflictExists == false)
							{
								
								$alreadyTeachingTimes = array();
								$facultyMember = $facultyPQ[$facultyPQIndex];
								$facultyName = $facultyMember->userName;
								$selectFacultyTeachingQuery = "SELECT timeSlot FROM scheduledCourses WHERE facultyUser = '$facultyName'";
								$selectFacultyTeachingResult = mysqli_query($link, $selectFacultyTeachingQuery);
								while($row = mysqli_fetch_row($selectFacultyTeachingResult))
								{
									if($row)
									{
										array_push($alreadyTeachingTimes, $row[0]);
									}
								}
								$alreadyTeachingTimesIndex = 0;
								// Loops through array of times to find a time slot where the faculty member isn't teaching
								if($alreadyTeachingTimes)
								{
									do
									{											
										$overlapCheckFaculty = $classTimes[$classTimesIndex]->minutes." ".$classTimes[$classTimesIndex]->daysOfWeek."/";
										$overlapCheckFaculty = $overlapCheckFaculty.$arrayOfTimes[$arrayOfTimesIndex];
										
										
										
										echo "ALREADY TEACHING ARRAYS BELOW FOR $facultyName: <br>";
										print_r($timeToScheduleFaculty);
										echo "<br>";
										print_r($alreadyTeachingFaculty);
										echo "<br>";
										print_r($alreadyTeachingTimes);
										echo "<br>";
										
										// Check to make sure a faculty is not already teaching at a time
										if(!in_array($overlapCheckFaculty, $alreadyTeachingTimes))
										{			
											for($i = 0; $i < count($alreadyTeachingTimes); $i++)
											{
												// Splits the times into arrays
												//$row[0] and $overlapCheckFaculty are the two times to check so we need the specific time (e.g. 10:00) from each string
												$timeToScheduleFaculty = preg_split('/[\s+\/]/', $overlapCheckFaculty);
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
																				
											
												echo "<br><h3>$overlapCheckFaculty</h3><br>";
												echo $alreadyTeachingTimes[$alreadyTeachingTimesIndex]."<br>";
												echo $classTimes[$classTimesIndex]->daysOfWeek."<br>";
												
												// Checks to make sure the faculty member isn't already teaching at the same time on the same day
												if($noTimeOverlapFaculty == true)
												{
													$alreadyTeachingFlag = false;
												}
												else	// Falls here if a faculty member ran into a conflicting time
												{
													if(count(array_intersect(str_split($alreadyTeachingTimes[$i]), str_split(trim($classTimes[$classTimesIndex]->daysOfWeek)))) == 0)
													{
														$alreadyTeachingFlag = false;
													}
													else
													{
														$alreadyTeachingFlag = true;
														$conflictExists = true;
														echo "<br>Already Teaching increment<br>";
														$alreadyTeachingTimesIndex++;
														if($alreadyTeachingTimesIndex >= count($alreadyTeachingTimes))
														{
															$arrayOfTimesIndex++;
															$alreadyTeachingTimesIndex = 0;
														}
														$i = count($alreadyTeachingTimes);
													}
												}
											}
										}
										else	// The faculty member wasn't teaching at a time yet
										{
											$alreadyTeachingFlag = true;
											$conflictExists = true;
											$alreadyTeachingTimesIndex++;
											if($alreadyTeachingTimesIndex >= count($alreadyTeachingTimes))
											{
												$arrayOfTimesIndex++;
												$alreadyTeachingTimesIndex = 0;
											}
										}
									}while(($alreadyTeachingFlag == true) and ($arrayOfTimesIndex < count($arrayOfTimes)));
								}	
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
											$overlapCheckRoom = $classTimes[$classTimesIndex]->minutes." ".$classTimes[$classTimesIndex]->daysOfWeek."/";
											$overlapCheckRoom = $overlapCheckRoom.$arrayOfTimes[$arrayOfTimesIndex];
											
											// Check to make sure the room is available at the current time
											if(!in_array($overlapCheckRoom, $unavailableTimesArray))
											{    
												$roomAvailable = true;
												$noTimeOverlapRoom = true;
												// Loops through the preferred times by the faculty member to find if the room is available at a time
												for($i = 0; $i < count($unavailableTimesArray); $i++)
												{
													// Splits the times into arrays
													$timeToScheduleRoom = preg_split('/[\s+\/]/', $overlapCheckRoom);
													$alreadyTeachingRoom = preg_split('/[\s+\/]/', $unavailableTimesArray[$i]);
													
													// Debugging statements
													print_r($timeToScheduleRoom);
													print_r($alreadyTeachingRoom);
													
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
													
													
													// Debugging statements
													echo "Difference of $timeToScheduleRoom[2] and $alreadyTeachingRoom[2] is: $difference<br>";
													echo "<br> ENTERED FOR LOOP :   ".$unavailableTimesArray[$i]."<br>";
													echo(count(array_intersect(str_split($unavailableTimesArray[$i]), str_split(trim($classTimes[$classTimesIndex]->daysOfWeek)))));
													
													// Check to make sure the room is not already being taught at the current time on the same day
													if((count(array_intersect(str_split($unavailableTimesArray[$i]), str_split(trim($classTimes[$classTimesIndex]->daysOfWeek)))) == 0) or ($noTimeOverlapRoom == true))
													{
														$roomAvailable = true;	// This must happen for every time in the list of unavailable times array
													}
													else	// Falls here if the current time is unschedulable for this room and kicks out of the loop
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
											{//this block can also go in place of line 612
												$foundRoom = true;
												
												// Temporaries for insertion
												$tempCourse = $coursesToSchedule[$ctsIndex]->name;
												$tempFaculty = $facultyPQ[$facultyPQIndex]->userName;
												$tempRoom = $arrayOfRooms[$aorIndex]->roomName;
												
												// Insert the scheduled course into the database 
												$scheduleQuery1 = "INSERT INTO scheduledCourses (course, section, timeSlot, facultyUser, roomName) VALUES (";
												$scheduleQuery2 = "'$tempCourse', '$currentSectionNumber', '$overlapCheckRoom', '$tempFaculty', '$tempRoom')";
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
												else if($noPreferenceFlag == true)
												{
													$splitTime = preg_split('/[:]/', trim($arrayOfTimes[$arrayOfTimesIndex]));
													$intTime = trim($splitTime[0]).trim($splitTime[1]);
													
													if(($intTime > 0) and ($intTime < 1800))
													{
														$daySectionsRemaining--;
													}
													else if(($intTime >= 1800) and ($intTime <= 2400))
													{
														$nightSectionsRemaining--;
													}
													
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
												$arrayOfRooms[$aorIndex]->addUnavailableTimes($overlapCheckRoom);
												$coursesToSchedule[$ctsIndex]->addInSessionTimes($overlapCheckRoom);
												
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
					if($facultyPQIndex < count($facultyPQ) and ($foundRoom == true))
					{
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
			
			//We reach this block because the priority queue is empty or we ran out of class times
			if(($scheduledSections < ($daySections + $nightSections + $coursesToSchedule[$ctsIndex]->internetSection)))
			{
				// Pushes the sections that haven't been scheduled into the list of unscheduled sections
				while($scheduledSections < ($daySections + $nightSections))
				{
					
					echo "<br><h3> ENTERED TEST LOOP </h3><br>";
					$courseToPush = $coursesToSchedule[$ctsIndex]->name."-".$currentSectionNumber;
					array_push($unscheduledCourses, $courseToPush);
					$output = "$courseToPush:  ";
					if($facultyPQIndex >= count($facultyPQ))
					{
						$output = $output . "Not enough faculty chose this course to fill in all sections.\r";
					}
					if($classTimesIndex >= count($classTimes))
					{
						$output = $output . "Unable to find time slot that was not in conflict.\r";
					}
					
					
					fwrite($outFile, $output);
					$pushUnscheduledQuery = "INSERT INTO unscheduledCourses (course, section, internet) VALUES ('$courseNamer', '$currentSectionNumber', 0)";
					$pushUnscheduledResult = mysqli_query($link, $pushUnscheduledQuery);
					$scheduledSections++;
					$currentSectionNumber++;
					
				}
				for($i = 0; $i < $coursesToSchedule[$ctsIndex]->internetSections; $i++)
				{
					$pushUnscheduledQuery = "INSERT INTO unscheduledCourses (course, section, internet) VALUES ('$courseNamer', '$currentSectionNumber', 1)";
					$output = "$courseToPush: Internet sections GARBLE must be scheduled manually.\r";
					fwrite($outFile, $output);
					$pushUnscheduledResult = mysqli_query($link, $pushUnscheduledQuery);
					$scheduledSections++;
					$currentSectionNumber++;
				}			
			}
			
			// Done and moving on to the next course
			
			$currentSectionNumber = 1;
			$ctsIndex++;
			//schedule next course
		}//end while
	}
	else
	{
		// Print out missing file statement
	}

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
	// Also prints to a file named facultyMinimumRequirements.txt in the generated files folder
	fclose($outFile);
	$outFile = fopen("generatedFiles/facultyMinimumRequirements.txt", "w");
	
	for($i = 0; $i < count($arrayOfFaculty); $i++)
	{
		if($arrayOfFaculty[$i]->currentHours < $arrayOfFaculty[$i]->requiredMinHours)
		{
			echo "<br>".$arrayOfFaculty[$i]->userName." didn't meet their minimum hours of ".$arrayOfFaculty[$i]->requiredMinHours."<br>";
			echo "Currently at: ".$arrayOfFaculty[$i]->currentHours."<br>";
			
			$output = "Username: ".strtolower($arrayOfFaculty[$i]->userName).", didn't meet their minimum hours of ".$arrayOfFaculty[$i]->requiredMinHours."\r";
			$output = $output."Currently at: ".$arrayOfFaculty[$i]->currentHours."\r\r";
			fwrite($outFile, $output);
		}
	}
	fclose($outFile);
	
	// Redirects back to the homepage. Comment to see debugging statements
	//header("Location: adminHome.php");
	// We would start scheduling the faculty members that haven't met their minimum hours requirement here
?>