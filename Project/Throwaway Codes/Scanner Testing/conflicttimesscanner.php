<html>
<body>

<?php
	/*-----------------------------------------------------------------------------------------------
	 ********************** Function Prologue Comment: conflicttimesscanner ********************
	 * Preconditions:  None
	 *
	 * Postconditions: None
	 *
	 * Function Purpose:  Validates the authenticity of a file containing conflict times
	 *					  for scheduled courses by analyzing the contents of each line
	 *					  in a file. 
	 *
	 * Input Expected:  Text input of the following format:
	 *						XYZ DoW/00:00 DoW/00:00
	 *						Where X must be 2 to 4 uppercase letters
	 *						Where Y must be a 3 digit number between 001-499
	 *						Where Z can be up to 2 uppercase letters
	 *						Where DoW can be any in-order substring of MTWRFS
	 *						Where 00:00 can be any valid military time specification
	 *						There can be any number of instances of DoW/00:00 provided
	 *							there are no duplicates
	 *
	 * Exceptions/Errors Thrown:  Course letters must be between 2 and 4 characters
	 *							  Course letters is not a part of the department
	 *							  Files must contain ONLY uppercase letters
	 *							  Invalid character encountered
	 *							  Course number must immediately follow course letters
	 *							  Course number must be exactly 3 digits
	 *							  Prerequisite is a higher level course than course requiring prerequisites
	 *							  Course number exceeds boundaries. Must be between 001 and 499
	 *							  String of characters following course number is too long
	 *							  Invalid character in string following course number
	 *							  Conflict time already defined in file
	 *							  Invalid time format error thrown if the time of day
	 *							  is not valid.
	 *							  Expected digit error thrown if a digit is not found
	 *							  where expected.
	 *							  Expected colon error thrown if a colon is not found
	 *							  where expected.
	 *							  Days of week out of order
	 *							  Duplicate found in days of week
	 *							  Character found does not represent day of week
	 *							  Lowercase character encountered
	 *							  No days of week specified
	 *							  Expected '/' not found
	 *
	 * Files Accessed:  Any file given to the program
	 *					$logFile for reporting errors
	 *
	 * Function Pseudocode Author:  Jared Cox
	 *
	 * Function Author:  Jared Cox
	 *
	 * Date of Original Implementation: April 4, 2013
	 *
	 * Tested by SQA Member (NAME and DATE):  Jared Cox, April 6, 2013
	 * 
	 ** Modifications by:
	 * Modified By (Name and Date):
	 * Modifications Description:
	 *
	 * Modified By (Name and Date):
	 * Modifications Description:
	 -------------------------------------------------------------------------------------------------*/ 		
	
	//FLAGS
		$stillTesting = true;
		$startQuery = true;
		
	//FILE SETUP
		$testName = "Tests/conflictt/CONFLICTT";
		$testNum = "1";
		
while($stillTesting == true)
{//loops through and tests each prereq test file in the current directory

	$fileName = $testName . $testNum;
	$log = $fileName . "Log";
	
	$readFile = fopen($fileName . ".txt", "r") or die("Unable to open $fileName");
	$logFile = fopen($log . ".log", "w") or die("Unable to open $log");
	
	echo "<br> <br> <br>" . "Test File:  $fileName" . "<br> <br> <br>";
	
	//VARIABLES
	$lineNumber = 0;	//used in listing errors
	$printLine = " ";	//line read in from file
	$printLineIndex = 0;	
	$currentCourse = "";		//string variable
	$listOfCourses = array();
	$listOfCoursesIndex = 0;
	$errorOnLine = false;
	$errorInFile = false;
	$REQUIREDITEMSONLINE = 2;	//there must be at least 2 items on a line, otherwise there is an error
	$listOfConflictTimes = array();
	$listOfConflictTimesIndex = 0;
	$conflictTime = " ";
	$readLine = " ";
	
	while(!feof($readFile))
	{
		$listOfConflictTimes = array();
		$listOfConflictTimesIndex = 0;
		
		$errorOnLine = false;
		$printLineIndex = 0;
		do
		{//this block will skip over any empty lines in a file
		  $printLine = fgets($readFile);
		  $lineNumber++;
		}while((strlen(trim($printLine)) == 0) and (!feof($readFile)));
		//0 means an empty line
		$printLine = $printLine . "\r"; //append a carriage return to the end of each line
										//otherwise, a line without a hard return at the end
										//will produce a whitespace error in this scanner
		
		
		$readLine = preg_split('/\s+/', trim($printLine));  //splits the line into an array of elements
														//each element will be a contiguous string of characters
														//all whitespace is ignored on line for this function due to " '/\s+/' "
		
		echo "length of line is " . strlen($printLine) . "<br>";
		echo "length of trimmed line is " . strlen(trim((string)$printLine)) . "<br>";
		echo "line number $lineNumber and line index $printLineIndex" . "<br>";
		
		if((count($readLine)) >= $REQUIREDITEMSONLINE)
		{//if there is not at $REQUIREDITEMSONLINE(2) items on the line, something is wrong
			
			skipWhitespace($printLine, $printLineIndex);
			if(getCourse($printLine, $printLineIndex, $lineNumber, $currentCourse, $logFile) == false)
			{
				echo "getCourse returned false" . "<br>";
				$errorOnLine = true; $errorInFile = true;
			}
			
			if($errorOnLine == false)
			{
				if(in_array($currentCourse, $listOfCourses) == true)
				{
					fputs($logFile, "Error on line $lineNumber.  Conflict time already defined in file." . PHP_EOL);
					$errorOnLine = true; $errorInFile = true;
				}
				else
				{
					$listOfCourses[$listOfCoursesIndex] = $currentCourse;
					$listOfCoursesIndex++;
					//start new query
				}
			}
			
			
			while(($printLineIndex < strlen(trim($printLine))) and ($errorOnLine == false))
			{//buildling the conflict time
				if($errorOnLine == false)
				{//add $currentCourse to sql query
					if(verifyWhitespace($printLine, $printLineIndex, $lineNumber, $logFile) == false)
					{
						echo "printline[printlineindex] at index $printLineIndex is $printLine[$printLineIndex]" . "<br>";
						echo "whitespace error 1" . "<br>";
						$errorOnLine = true; $errorInFile = true;
					}
				}
			
				if($errorOnLine == false)
				{
					skipWhitespace($printLine, $printLineIndex);
				}
				if($errorOnLine == false)
				{
					if(getDaysOfWeek($printLine, $printLineIndex, $lineNumber, $retrievedDOW, $logFile) == false)
					{
						echo "getDaysOfWeek returned false" . "<br>";
						$errorOnLine = true; $errorInFile = true;
					}
				}
				
				if($errorOnLine == false)
				{//add $retrievedDOW to sql query
					echo "getDaysOfWeek returned true" . "<br>";
					if(getSlash($printLine, $printLineIndex, $lineNumber, $retrievedSlash, $logFile) == false)
					{
						echo "getSlash returned false" . "<br>";
						$errorOnLine = true; $errorInFile = true;
					}
				}
				
				if($errorOnLine == false)
				{
					echo "getSlash returned true" . "<br>";
					if(getTime($printLine, $printLineIndex, $lineNumber, $retrievedTime, $logFile) == false)
					{
						echo "getTime returned false" . "<br>";
						$errorOnLine = true; $errorInFile = true;
					}
				}
				
				if($errorOnLine == false)
				{
					$conflictTime = $retrievedDOW . $retrievedSlash . $retrievedTime;
					if(in_array($conflictTime, $listOfConflictTimes) == false)
					{
						$listOfConflictTimes[$listOfConflictTimesIndex] = $conflictTime;
						$listOfConflictTimesIndex++;
					}
					else
					{
						fputs($logFile, "Error on line $lineNumber at index $printLineIndex. Conflict time already exists on line." . PHP_EOL);
						$errorOnLine = true; $errorInFile = true;
					}
				}
			}
		}
		else 
		{
			if(strlen(trim($printLine)) != 0)
			{
				fputs($logFile, "Error on line $lineNumber at index $printLineIndex.  Each line in the file must have at least 2 items:
			         			Minutes DAYSOFWEEK_ForwardSlash_MilitaryTimeOfDay DAYSOFWEEK_ForwardSlash_MilitaryTimeOfDay 
								... DAYSOFWEEK_ForwardSlash_MilitaryTimeOfDay" . PHP_EOL);
				$errorOnLine = true;  $errorInFile = true;
			}	
		}
		
		if($errorOnLine == false)
		{
			//submit query
			echo  "$lineNumber: $printLine" . "<br>";
		}
		else
		{
			echo $lineNumber . ": $printLine*" . "<br>";
		}
	}
	if($errorInFile == false)
	{
		fputs($logFile, "No errors detected." . PHP_EOL);
	}
	
	fclose($readFile);
	fclose($logFile);
	
	$testNum += 1;
	if($testNum > "22")
		$stillTesting = false;
	
}
##################################################################################################

/*******************FUNCTIONS******************/

##################################################################################################

	function getCourse($line, &$lineIndex, $lineNumber, &$currentCourse, $logFile)
	{/*-----------------------------------------------------------------------------------------------
	 ********************** Function Prologue Comment: getCourse ********************
	 * Preconditions:  Data exists on the line
	 *
	 * Postconditions: None
	 *
	 * Function Purpose:  Validates that the string of characters on a line represent
	 *					  a valid course.  A valid course is 2 to 4 uppercase characters
	 *					  concatenated with exactly 3 digits and can be further concatenated
	 *					  with up to 2 more uppercase characters.
	 *
	 * Input Expected:  $line = line of text read in from the test file
	 *					$lineIndex = current index for $line
	 *					$lineNumber = current line of file
	 *					$currentCourse = string that will store the course gathered from
	 *									 the line that will be added to the sql query
	 *					$currentCourseNumber = will store the number of the currently read course on
	 *										   the line and will be compared to $firstCourseNumber
	 *					$logFile = text file that errors are logged to
	 *
	 * Exceptions/Errors Thrown:  Course letters must be between 2 and 4 characters
	 *							  Course letters is not a part of the department
	 *							  Files must contain ONLY uppercase letters
	 *							  Invalid character encountered
	 *							  Course number must immediately follow course letters
	 *							  Course number must be exactly 3 digits
	 *							  Prerequisite is a higher level course than course requiring prerequisites
	 *							  Course number exceeds boundaries. Must be between 001 and 499
	 *							  String of characters following course number is too long
	 *							  Invalid character in string following course number
	 *
	 * Files Accessed:  None
	 *
	 * Function Pseudocode Author:  Jared Cox
	 *
	 * Function Author:  Jared Cox
	 *
	 * Date of Original Implementation: March 26, 2013
	 *
	 * Tested by SQA Member (NAME and DATE):  Jared Cox, March 26, 2013
	 * 
	 ** Modifications by:
	 * Modified By (Name and Date):
	 * Modifications Description:
	 *
	 * Modified By (Name and Date):
	 * Modifications Description:
	 -------------------------------------------------------------------------------------------------*/ 		
	
	 //function variables
	 $courseLetters = array();
	 $courseLettersIndex = 0;
	 $courseNumbers = array();
	 $courseNumbersIndex = 0;
	 $courseNumberInt = 0;
	 $tempCourse = array();
	 $endCourseName = array();
	 $endCourseNameIndex = 0;
		
		while(ctype_upper($line[$lineIndex]) == true)
		{	//while line[lineindex] is an uppercase character
			$courseLetters[$courseLettersIndex] = $line[$lineIndex];
			$lineIndex++;
			$courseLettersIndex++;
		}
		//ERROR HANDLING 
			if((count($courseLetters) < 2) or (count($courseLetters) > 4))
			{
				fputs($logFile, "Error on line $lineNumber at index $lineIndex. Course letters must be between 2 and 4 characters." . PHP_EOL);
				return false;
			}/*
			elseif($courseLetters not in Department Courses)
			{
				fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Course letters is not a part of the department." . PHP_EOL);
				return false;
			*/
			elseif(ctype_lower($line[$lineIndex]) == true)
			{	//line[lineindex] is lowercase
				fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Files must contain ONLY uppercase letters." . PHP_EOL);
				return false;
			}
			elseif(ctype_alnum($line[$lineIndex] == false))
			{	//line[lineindex] is not alphabetic or numeric
				fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Invalid character encountered." . PHP_EOL);
				return false;
			}
			elseif($line[$lineIndex] == " ")
			{
				fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Course number must immediately follow course letters." . PHP_EOL);
				return false;
			}
		//COURSE LETTERS ARE VALID AND IMMEDIATELY FOLLOWED BY NUMBERS
			else
			{
				while(ctype_digit($line[$lineIndex]) == true)
				{	//while line[lineindex] is a digit
					echo "line[lineindex] in ctype_digit is $line[$lineIndex] <br>";
					$courseNumbers[$courseNumbersIndex] = $line[$lineIndex];
					$lineIndex++;
					$courseNumbersIndex++;
				}
				
				if(count($courseNumbers) != 3)
				{
					fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Course number must be exactly 3 digits." . PHP_EOL);
					return false;
				}
				
				$courseNumberInt = intval(implode($courseNumbers));	//converts the integer array to a solid string
																	//and converts the string value to an integer
																	
				if(($courseNumberInt < 1) or ($courseNumberInt > 499))
				{
					fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Course number exceeds boundaries. Must be between 001 and 499." . PHP_EOL);
					return false;
				}
				else
				{//COURSE NUMBER IS VALID
				 //CHECK FOR LETTERS FOLLOWING COURSE NAME
					$tempCourse = array_merge($courseLetters, $courseNumbers);
					
					while(ctype_upper($line[$lineIndex]))
					{
						echo "went here <br>";
						$endCourseName[$endCourseNameIndex] = $line[$lineIndex];
						$lineIndex++;
						$endCourseNameIndex++;
					}
					if(count($endCourseName) > 2)
					{
						fputs($logFile, "Error on line $lineNumber at index $lineIndex.  String of characters following course number is too long." . PHP_EOL);
						return false;
					}
					elseif(($line[$lineIndex] !=  " ") and ($line[$lineIndex] != "\r") and ($line[$lineIndex] != "\t"))
					{//only whitespace or carriage return can immediately follow a course on line
						fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Invalid character in string following course number." . PHP_EOL);
						return false;
					}
					else
					{
						if(!empty($endCourseName))
						{
							$tempCourse = array_merge($tempCourse, $endCourseName);
						}
					}
				}
			}
		$currentCourse = implode($tempCourse);
		return true;
	}
	
##################################################################################################

	function getTime($line, &$lineIndex, $lineNumber, &$retrievedTime, $logFile)
	{/*-----------------------------------------------------------------------------------------------
	 ********************** Function Prologue Comment: getTime ********************
	 * Preconditions:  This function is not called unless contents of input have been
	 *				   correct up until a time of day is required
	 *
	 * Postconditions:  Input validation will either terminate in this function, if
	 *					invalid data is provided, or will advance validation if 
	 *					valid data is provided.
	 *
	 * Function Purpose:  This function validates times of day provided by
	 *					  file or user input.  A valid time follows the format
	 *					  XX:XX in military format.  
	 *
	 * Input Expected:	$line = line of text read in from the test file
	 *					$lineIndex = current index for $line
	 *					$lineNumber = current line of file
	 *					$retrievedTimer = time of day gathered from the line to add to SQL query
	 *					$times = list of times already found on line
	 *					$timeIndex = current index for $times
	 *					$logFile = text file that errors are logged to
	 *					  
	 * Exceptions/Errors Thrown:  Invalid time format error thrown if the time of day
	 *							  is not valid.
	 *							  Expected digit error thrown if a digit is not found
	 *							  where expected.
	 *							  Expected colon error thrown if a colon is not found
	 *							  where expected.
	 *
	 * Files Accessed:			  $logFile - to report errors to
	 *
	 * Function Pseudocode Author:  Jared Cox
	 *
	 * Function Author:			    Jared Cox
	 *
	 * Date of Original Implementation:  April 3, 2013
	 *
	 * Tested by SQA Member (NAME and DATE): Jared Cox, April 3, 2013
	 * 
	 ** Modifications by:
	 * Modified By (Name and Date):
	 * Modifications Description:
	 *
	 * Modified By (Name and Date):
	 * Modifications Description:
	 -------------------------------------------------------------------------------------------------*/ 
	
	echo "called getTime" . "<br> <br> <br>";
	//VARIABLES
		$timeString = array();
		$timeStringIndex = 0;
		$firstDigit = 0;
	
	////////////////////////////
	
		//retrieve first digit in time value
		if(ctype_digit($line[$lineIndex]) == false)
		{
			fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Expected a digit." . PHP_EOL);
			return false;
		}
		else
		{
			if(($line[$lineIndex] >= 0) && ($line[$lineIndex] <= 2))
			{
				$timeString[$timeStringIndex] = $line[$lineIndex];
				$firstDigit = $timeString[$timeStringIndex];
				$timeStringIndex++;
				$lineIndex++;
			}
			else
			{
				fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Invalid time encountered." . PHP_EOL);
				return false;
			}			
		}
		
		//second digit in time
		if(ctype_digit($line[$lineIndex]) == false)
		{
			fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Expected a digit." . PHP_EOL);
			return false;
		}
		else
		{
			if(($firstDigit == 2) && ($line[$lineIndex] > 3))
			{
				fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Invalid time encountered." . PHP_EOL);
				return false;
			}
			else
			{
				$timeString[$timeStringIndex] = $line[$lineIndex];
				$firstDigit = $timeString[$timeStringIndex];
				$timeStringIndex++;
				$lineIndex++;
			}
		}
		
		//check for ":"
		if($line[$lineIndex] != ':')
		{
			fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Expected a ':'." . PHP_EOL);
			return false;
		}
		else
		{
			$timeString[$timeStringIndex] = $line[$lineIndex];
			$firstDigit = $timeString[$timeStringIndex];
			$timeStringIndex++;
			$lineIndex++;
		}
		
		//third digit in time
		if(ctype_digit($line[$lineIndex]) == false)
		{
			fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Expected a digit." . PHP_EOL);
			return false;
		}
		else
		{
			if(($line[$lineIndex] < 0) or ($line[$lineIndex] > 5))
			{
				fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Invalid time encountered." . PHP_EOL);
				return false;
			}
			else
			{
				$timeString[$timeStringIndex] = $line[$lineIndex];
				$firstDigit = $timeString[$timeStringIndex];
				$timeStringIndex++;
				$lineIndex++;
			}
		}
		
		//fourth digit in time
		if(ctype_digit($line[$lineIndex]) == false)
		{
			fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Expected a digit." . PHP_EOL);
			return false;
		}
		else
		{
			if(($line[$lineIndex] < 0) or ($line[$lineIndex] > 9))
			{
				fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Invalid time encountered." . PHP_EOL);
				return false;
			}
			else
			{
				$timeString[$timeStringIndex] = $line[$lineIndex];
				$firstDigit = $timeString[$timeStringIndex];
				$timeStringIndex++;
				$lineIndex++;
			}
		}
		
		$retrievedTime = implode($timeString);
		return true;
	}
	
##################################################################################################

	function getDaysOfWeek($line, &$lineIndex, $lineNumber, &$retrievedDOW, $logFile)
	{/*-----------------------------------------------------------------------------------------------
	********************** Function Prologue Comment: getDaysOfWeek ********************
	* Preconditions:  This function is not called unless valid input has been provided
	*					leading up to it.
	*
	* Postconditions: After termination of this function, a forward slash ("/") is
	*					expected on the line.
	*
	* Function Purpose:  This function validates the days of the week a course will be
	*					 taught that have been provided by file or user.  Valid input
	*					 is any substring (or whole string, in order) of MTWRFS.
	*
	* Input Expected:	$line = line of text read in from the test file
	*					$lineIndex = current index for $line
	*					$lineNumber = current line of file
	*					$retrievedDOW = stores string gathered from the line to add to SQL query
	*					$logFile = text file that errors are logged to
	*
	* Exceptions/Errors Thrown:  Days of week out of order
	*							  Duplicate found in days of week
	*							  Character found does not represent day of week
	*							  Lowercase character encountered
	*							  No days of week specified
	*
	* Files Accessed:			  $logFile for reporting errors
	*
	* Function Pseudocode Author:  Jared Cox
	*
	* Function Author:	Jared Cox
	*
	* Date of Original Implementation:  April 3, 2013
	*
	* Tested by SQA Member (NAME and DATE): Jared Cox, April 3, 2013
	* 
	** Modifications by:
	* Modified By (Name and Date):
	* Modifications Description:
	*
	* Modified By (Name and Date):
	* Modifications Description:
	-------------------------------------------------------------------------------------------------*/ 
	//VARIABLES
		$daysFound = array();
		$daysFoundIndex = 0;
		$DAYSOFWEEK = array('M', 'T', 'W', 'R', 'F', 'S');
		$previousDays = array();
		
		while(ctype_upper($line[$lineIndex]) == true)
		{
			if(in_array($line[$lineIndex], $DAYSOFWEEK) == true)
			{
				if(in_array($line[$lineIndex], $daysFound) == false)
				{
					if(in_array($line[$lineIndex], $previousDays) == false)
					{
						$daysFound[$daysFoundIndex] = $line[$lineIndex];
						setPreviousDays($daysFound[$daysFoundIndex], $previousDays);
						$daysFoundIndex++;
						$lineIndex++;
					}
					else
					{
						fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Days of week out of order." . PHP_EOL);
						return false;
					}
				}
				else
				{
					fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Duplicate found in days of week." . PHP_EOL);
					return false;
				}
			}
			else
			{
				fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Character found is not one of MTWRFS." . PHP_EOL);
				return false;
			}
		}
		if(ctype_lower($line[$lineIndex]) == true)
		{
			fputs($logFile, "Error on line $lineNumber at index $lineIndex.  All characters must be uppercase." . PHP_EOL);
			return false;
		}
		$retrievedDOW = implode($daysFound);
		if(strlen(trim($retrievedDOW)) < 1)
		{
			fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Days of week not specified." . PHP_EOL);
			return false;
		}
		return true;
	}

##################################################################################################
	
	function setPreviousDays($dayOfWeek, &$previousDays)
	{/*-----------------------------------------------------------------------------------------------
	 ********************** Function Prologue Comment Template ********************
	 * Preconditions:  Only called by getDaysOfWeek to track order of days of week,
	 *				   and only if valid information has been provided up to this point
	 *
	 * Postconditions:  Maintains running list of days already specified in a line
	 *
	 * Function Purpose:  Using the input provided, this function maintains a list of
	 *					  days of the week occuring before those that have been listed
	 *
	 * Input Expected:	$dayOfWeek = character specifiying a (valid) day of the week
	 *					$previousDays = array that will contain days of the week prior
	 *					to that specified by $dayOfWeek
	 *
	 * Exceptions/Errors Thrown: None
	 *
	 * Files Accessed:	None
	 *
	 * Function Pseudocode Author:	Jared Cox
	 *
	 * Function Author:	Jared Cox
	 *
	 * Date of Original Implementation: April 3, 2013
	 *
	 * Tested by SQA Member (NAME and DATE): Jared Cox, April 3, 2013
	 * 
	 ** Modifications by:
	 * Modified By (Name and Date):
	 * Modifications Description:
	 *
	 * Modified By (Name and Date):
	 * Modifications Description:
	 -------------------------------------------------------------------------------------------------*/ 
		switch($dayOfWeek)
		{
			case 'S': $previousDays = array('M', 'T', 'W', 'R', 'F');
					  break;
			case 'F': $previousDays= array('M', 'T', 'W', 'R');
					  break;
			case 'R': $previousDays = array('M', 'T', 'W');
					  break;
			case 'W': $previousDays = array('M', 'T');
					  break;
			case 'T': $previousDays = array('M');
					  break;
			default:  break;
		}
	}
	
##################################################################################################

	function getSlash($line, &$lineIndex, $lineNumber, &$retrievedChar, $logFile)
	{/*-----------------------------------------------------------------------------------------------
	 ********************** Function Prologue Comment: getSlash ********************
	 * Preconditions:	Valid data has been provided up to this point
	 *
	 * Postconditions:  Times of day should follow this function
	 *
	 * Function Purpose:  Validates a forward slash ("/") separating days of the week
	 *					  from times of day
	 *
	 * Input Expected:  $line = line of text read in from the test file
	 *					$lineIndex = current index for $line
	 *					$lineNumber = current line of file
	 *					$retrievedChar = stores character gathered from the line to add to SQL query
	 *					$logFile = text file that errors are logged to
	 *
	 * Exceptions/Errors Thrown:  Expected '/' not found
	 *
	 * Files Accessed:  $logFile - for reporting errors
	 *
	 * Function Pseudocode Author:  Jared Cox
	 *
	 * Function Author:  Jared Cox
	 *
	 * Date of Original Implementation:  April 3, 2013
	 *
	 * Tested by SQA Member (NAME and DATE): Jared Cox, April 3, 2013
	 * 
	 ** Modifications by:
	 * Modified By (Name and Date):
	 * Modifications Description:
	 *
	 * Modified By (Name and Date):
	 * Modifications Description:
	 -------------------------------------------------------------------------------------------------*/ 	//VARIABLES
		$charOnLine = $line[$lineIndex];
	
	/////////////////////////////////////
	
		if($charOnLine != '/')
		{
			fputs($logFile, "Error on line $lineNumber at index $lineIndex. Expected '/'." . PHP_EOL);
			return false;
		}
		else
		{
			$retrievedChar = $charOnLine;
			$lineIndex++;
			return true;
		}
	}
##################################################################################################

	function skipWhitespace($line, &$lineIndex)
	{/*-----------------------------------------------------------------------------------------------
	 ********************** Function Prologue Comment Template ********************
	 * Preconditions:  Data exists on a line
	 *
	 * Postconditions: None
	 *
	 * Function Purpose:  Advances the index past continous strings of whitespace
	 *
	 * Input Expected:  $line = line of text read in from the test file
	 *					$lineIndex = current index for $line
	 *
	 * Exceptions/Errors Thrown:  None
	 *
	 * Files Accessed:  None
	 *
	 * Function Pseudocode Author:  Jared Cox
	 *
	 * Function Author:  Jared Cox
	 *
	 * Date of Original Implementation: March 26, 2013
	 *
	 * Tested by SQA Member (NAME and DATE):  Jared Cox, March 26, 2013
	 * 
	 ** Modifications by:
	 * Modified By (Name and Date):
	 * Modifications Description:
	 *
	 * Modified By (Name and Date):
	 * Modifications Description:
	 -------------------------------------------------------------------------------------------------*/ 		
	 while(($line[$lineIndex] == " ") or ($line[$lineIndex] == "\t"))
	 {
			$lineIndex++;
	 }
	}
	
##################################################################################################
	
	function verifyWhitespace($line, $lineIndex, $lineNumber, $logFile)
	{/*-----------------------------------------------------------------------------------------------
	 ********************** Function Prologue Comment Template ********************
	 * Preconditions:  Valid data exists on the line
	 *
	 * Postconditions: None
	 *
	 * Function Purpose:  Advances the index past a single character of whitespacee
	 *
	 * Input Expected:  $line = line of text read in from the test file
	 *					$lineIndex = current index for $line
	 *					$lineNumber = current line of file
	 *					$logFile = text file that errors are logged to
	 *
	 * Exceptions/Errors Thrown:  Whitespace missing where expected
	 *
	 * Files Accessed:  $logFile - for reporting errors
	 *
	 * Function Pseudocode Author:  Jared Cox
	 *
	 * Function Author:  Jared Cox
	 *
	 * Date of Original Implementation: March 26, 2013
	 *
	 * Tested by SQA Member (NAME and DATE):  Jared Cox, March 26, 2013
	 * 
	 ** Modifications by:
	 * Modified By (Name and Date):
	 * Modifications Description:
	 *
	 * Modified By (Name and Date):
	 * Modifications Description:
	 -------------------------------------------------------------------------------------------------*/ 		
		if(($line[$lineIndex] != " ") and ($line[$lineIndex] != "\r") and ($line[$lineIndex] != "\t"))
		{
			fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Whitespace must separate elements on line." . PHP_EOL);
		}
		else
		{
			return true;
		}
	}

##################################################################################################
