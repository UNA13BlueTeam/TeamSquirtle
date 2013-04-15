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

<?php
include ("classes.php");
//Varaible declarations
 $sortByYOS = false;
 $sortTimeOfSubmission=false;
 $ctsIndex = 0;    // Courses to schedule index
 $classTimesIndex = 0;   
 $missingConflictFile=false;
 $conflictExists=false;
 $facultyPreferenceQueue= new SplPriorityQueue();
 $scheduledSections=0;
 $facultyMember = new Faculty;
 //put if stmt to heck for missing conflict file.
 //Create array of unscheduled courses (listOfUnscheduledCourses)
 
 //Create array of courses (coursesToSchedule)
 
 //Create array of class times (classTimes)

While ($ctsIndex < count($coursesToSchedule))
    {
        if ($missingConflictFile == true ) 
        {//if((SELECT * FROM conflicts WHERE courseName = coursesToSchedule[ctsIndex].courseName) returns null)
            $conflictFileExists=false; // (no conflicts for coursesToSchedule[ctsIndex].courseName)
        }
        else
        {
            $conflictFileExists=true; //(conflicts exist for coursesToSchedule[ctsIndex].courseName)
        }
        
        //Get day and night sections from course (as constants)
        
        $daySections = $courseToSchedule[$ctsIndex].$daySections; 
        $nightSections= $coursesToSchedule[$ctsIndex].$nightSections;
     
        //Retrieve all faculty members that chose coursesToSchedule[ctsIndex].courseName in their preferences
        {
            //SELECT * FROM preferences WHERE courseName = coursesToSchedule[ctsIndex].courseName                
        }
        if($sortByYOS ==true)
        {
           //generate priority queue using years of service as priority in insert(facultyPreference.facultyName, yearsOfService)
               //using faculty members retrieved from SQL statement above      (facultyPreference.PreferenceQueue)
        }
        elseif($sortByTOS ==true)
        {
            //generate priority queue using time of submission as priority in insert(facultyPreference.facultyName, timeOfSubmission)
                //using faculty members retrieved from SQL statement above    (facultyPreferenceQueue)
        }
        
        if ($facultyPreferenceQueue.isEmpty()==true )
        {
           // Put courseToSchedule[ctsIndex] on array of unscheduled courses “No faculty selected coursesToSchedule[ctsIndex].courseName”
            $ctsIndex++;
        }
        else //(declare variables for number of day and night sections left)
        {
            $daySectionsRemaining = $daySections;
            $nightSectionsRemaining = $nightSections;
            $scheduledSections = 0;
            $currentSectionNumber = 1;
        }
         while (($facultyPreferenceQueue.isEmpty() == false) and ($scheduledSections < $daySections + $nightSections))
            {
                //Check front of priority queue
                $facultyMember.$name = $facultyPreferenceQueue.top();
            
                //Check their time preference (verify with correct array early[], midday[], afternoon[], night[])    
                 $arrayOfTimes = array();
                 $arrayOfTimesIndex = 0;
                     
                 switch($facultyMember.$timePref)
                 {
                     case  "early":  $arrayOfTimes = $classTimes[$classTimesIndex].$early;
                                          $dayType = true;
                                          break;
                        
                     case "midday": $arrayOfTimes =  $classTimes[$classTimesIndex].$midday;
                                          $dayType = true;
                                          break;
                        
                     case "late after": $arrayOfTimes =  $classTimes[$classTimesIndex].$lateAfter;
                                          $dayType = true;
                                          break;
                        
                     case "night":  $arrayOfTimes = $classTimes[$classTimesIndex].$night;
                                          $nighType = true;
                                          break;
                     }
                
                if(($dayType== true and $daySectionsRemaining == 0) OR ($nightType == true and $nightSectionsRemaining == 0))
                {
                    //add course and section number (currentSectionNumber) to listOfUnscheduledCourses 
                        //we were removing the faculty member from the queue here, but we don't need
                        //to do that HERE because we're popping them off at the end of this if/else
                        //down on line 254
                        
                    "No more sections available for preferred time chosen";
                    $currentSectionNumber++;
                }
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
                                            */    
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
                if($facultyPreferenceQueue.isEmpty()== false and $foundRoom == true)
                {
                    //pop off top faculty member
                    $temp= $facultyPreferenceQueue.extract(); //do nothing with temp
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
            }//endwhile        
        }//endelse
        $ctsIndex++;
        //schedule next course
    }//end while


























?>
 
