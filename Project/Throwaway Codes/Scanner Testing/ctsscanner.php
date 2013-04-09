<html>
<body>

<?php

	/*-----------------------------------------------------------------------------------------------
	 ********************** Function Prologue Comment: ctsscanner ********************
	 * Preconditions:  None
	 *
	 * Postconditions: None
	 *
	 * Function Purpose:  Validates the authenticity of a file containing information relating to
	 *					  courses that will be scheduled by analyzing the contents of each line in a file.
	 *
	 * Input Expected:  Text input of the following format:
	 *					XYZ #S #S #S #C & #H
	 *					Where X must be 2 to 4 uppercase letters
	 *					Where Y must be a 3 digit number between 001-499
	 *					Where Z can be up to 2 uppercase letters
	 *					Where #S is a positive integer >= 0
	 *					Where #C is a positive integer > 0
	 *					Where & is either C or L
	 *					Where #H is an integer between 1 and 12
	 *					
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
	 *							  Number expected at index $lineIndex
	 *							  Section count cannot be less than 0
	 *							  Class size must be between $CLASSSIZEMIN and $CLASSSIZEMAX
	 *							  Number of hours must be between $HOURSMIN and $HOURSMAX
	 *							  Unexpected error occurred on line $lineNumber at index $lineIndex
	 *							  Illegal character encountered.  Room type must be 'C' or 'L'
	 *							  Whitespace missing where expected
	 *
	 * Files Accessed:  Any file given to the program
	 *					$logFile for reporting errors
	 *
	 * Function Pseudocode Author:  Jared Cox
	 *
	 * Function Author:  Jared Cox
	 *
	 * Date of Original Implementation: March 28, 2013
	 *
	 * Tested by SQA Member (NAME and DATE):  Jared Cox, March 31, 2013
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
		$SECTIONSFLAG = 1;		//these three flags have integer values to distinguish between the
		$CLASSSIZEFLAG = 2;		//types of flags passed to the "getNumber" function
		$HOURSFLAG = 3;
	//FILE SETUP
		$testName = "Tests/cts/cts";		//used to create filenames to open
		$testNum = "1";		
		
while($stillTesting == true)
{//loops through and tests each prereq test file in the current directory

		$fileName = $testName . $testNum;
		$log = $fileName . "Log";
		

		$readFile=fopen($fileName . ".txt","r") or die("Unable to open $fileName");
		$logFile = fopen($log . ".log", "w") or die("Unable to open $log");
		
		echo "<br> <br> <br>" . "Test File: $fileName" . "<br> <br> <br>";
		
	
	
	//VARIABLES	
	$lineNumber = 0;	//used in listing errors
	$printLine = " ";	//line read in from file
	$printLineIndex = 0;	
	$currentCourse = "";		//string variable	
	$listOfCourses = array();	//used in detecting duplicate first courses in a file
	$listOfCoursesIndex = 0;
	$errorOnLine = false;
	$errorInFile = false;
	$REQUIREDITEMSONLINE = 7;	//there must be exactly 7 items on a line, otherwise there is an error
	$retrievedNumber = 0;
	
	while(!feof($readFile))
	{
		$errorOnLine = false;
		$printLineIndex = 0;
		do
		{//this block will skip over any empty lines in a file
		 $printLine = fgets($readFile);
		 $lineNumber++;
		}while((strlen(trim($printLine)) == 0) and (!feof($readFile)));
		
		$printLine = $printLine . "\r";	//append a carriage return to the end of each line
										//otherwise, a line without a hard return at the end
										//will produce a whitespace error in this scanner
		
	
			
		$readLine = preg_split('/\s+/', trim($printLine));	//splits the line into an array of elements
														//each element will be a contiguous string of characters
														//all whitespace is ignored on line for this function due to " '/\s+/' "
		
		while(($printLineIndex < (strlen(trim($printLine)))) and ($errorOnLine == false))
		{	//$printLineIndex == strlen(trim($printLine) means we are at the end of the current line
		
			echo "length of line is " . strlen($printLine) . "<br>";
			echo "length of trimmed line is " . strlen(trim((string)$printLine)) . "<br>";
			echo "line number $lineNumber and line index $printLineIndex" . "<br>";
			
			
			if((count($readLine)) == $REQUIREDITEMSONLINE)
			{	//if there is not $REQUIREDITEMSONLINE (7) items on the line, something is missing
				
				skipWhitespace($printLine, $printLineIndex); 
				if(getCourse($printLine, $printLineIndex, $lineNumber, $currentCourse, $logFile) == false)
				{//an invalid course format was encountered on the line
					echo "getCourse returned false" . "<br>";
					$errorOnLine = true;  $errorInFile = true;
				}
				
				if($errorOnLine == false)
				{
					if(in_array($currentCourse, $listOfCourses) == true)
					{
						fputs($logFile, "Error on line $lineNumber.  Course prerequisites already defined. All prerequisites for a course belong on the same line." . PHP_EOL);
						$errorOnLine = true; $errorInFile = true;
					}
					else
					{
						$listOfCourses[$listOfCoursesIndex] = $currentCourse;
						$listOfCoursesIndex++;
						$firstCourseOnLineFlag = false;
						//start new query
					}	
				}
				
				if($errorOnLine == false)
				{
					if(verifyWhiteSpace($printLine, $printLineIndex, $lineNumber, $logFile) == false)
					{   echo "whitespace error 1" . "<br>";
						echo $printLine[$printLineIndex] . "<br>";
						$errorOnLine = true; $errorInFile = true;
					}
				}
				
				if($errorOnLine == false)
				{
					skipWhitespace($printLine, $printLineIndex);
					if(getNumber($printLine, $printLineIndex, $lineNumber, $SECTIONSFLAG, $retrievedNumber, $logFile) == false)
					{//invalid day sections count was encountered
						echo "day sections false" . "<br>";
						$errorOnLine = true; $errorInFile = true;
					}
				}
				
				if($errorOnLine == false)
				{//valid day sections count was encountered
					echo "day sections true" . "<br>";
					if(verifyWhitespace($printLine, $printLineIndex, $lineNumber, $lineNumber, $logFile) == false)
					{	
						echo "whitespace error 2" . "<br>";
						$errorOnLine = true; $errorInFile = true;
					}
				}
				
				if($errorOnLine == false)
				{//add $retrievedNumber to query
					skipWhitespace($printLine, $printLineIndex);
					if(getNumber($printLine, $printLineIndex, $lineNumber, $SECTIONSFLAG, $retrievedNumber, $logFile) == false)
					{//invalid night sections count was encountered
						echo "night sections false"."<br>";
						$errorOnLine = true; $errorInFile = true;
					}
				}
				
				if($errorOnLine == false)
				{//valid night sections count was encounterd
					echo "night sections true"."<br>";
					if(verifyWhitespace($printLine, $printLineIndex, $lineNumber, $logFile) == false)
					{	echo "whitspace error 3" . "<br>";
						$errorOnLine = true; $errorInFile = true;
					}
				}
				
				if($errorOnLine == false)
				{
					//add $retrievedNumber to query
					skipWhitespace($printLine, $printLineIndex);
					if(getNumber($printLine, $printLineIndex, $lineNumber, $SECTIONSFLAG, $retrievedNumber, $logFile) == false)
					{//invalid internet sections count was encountered
						echo "internet sections false" . "<br>";
						$errorOnLine = true; $errorInFile = true;
					}
				}
				
				if($errorOnLine == false)
				{//valid internet sections count was encountered
					echo "internet sections true" . "<br>";
					if(verifyWhitespace($printLine, $printLineIndex, $lineNumber, $logFile) == false)
					{	echo "whitespace error 4" . "<br>";
						$errorOnLine = true; $errorInFile = true;
					}
				}
				
				if($errorOnLine == false)
				{//add $retrievedNumber to query
					skipWhitespace($printLine, $printLineIndex);
					if(getNumber($printLine, $printLineIndex, $lineNumber, $CLASSSIZEFLAG, $retrievedNumber, $logFile) == false)
					{//invalid class size count encountered
						echo "class size false" . "<br>";
						$errorOnLine = true; $errorInFile = true;
					}
				}
				
				if($errorOnLine == false)
				{//valid class size count encountered
					echo "class size true" . "<br>";
					if(verifyWhitespace($printLine, $printLineIndex, $lineNumber, $logFile) == false)
					{	
						echo "whitespace error 5" . "<br>";
						$errorOnLine = true; $errorInFile = true;
					}
				}
				
				if($errorOnLine == false)
				{//add $retrievedNumber to query
					skipWhitespace($printLine, $printLineIndex);
					if(getChar($printLine, $printLineIndex, $lineNumber, $retrievedChar, $logFile) == false)
					{//character was not C or L
						echo "getChar false" . "<br>";
						$errorOnLine = true; $errorInFile = true;
					}
				}

				if($errorOnLine == false)
				{//character was C or L
					echo "getChar true" . "<br>";
					if(verifyWhitespace($printLine, $printLineIndex, $lineNumber, $logFile) == false)
					{	echo "whitespace error 6" . "<br>";
						$errorOnLine = true; $errorInFile = true;
					}
				}

				if($errorOnLine == false)
				{//add $retrievedChar to query
					skipWhitespace($printLine, $printLineIndex);
					if(getNumber($printLine, $printLineIndex, $lineNumber, $HOURSFLAG, $retrievedNumber, $logFile) == false)
					{//invalid hours count encountered
						echo "hours false" . "<br>";
						$errorOnLine = true; $errorInFile = true;
					}
				}
				if($errorOnLine == false)
				{//valid hours count encountered
					echo "hours true" . "<br>";
					//add $retrievedNumber to query
					//submit query
				}		  
			}
			else
			{
				fputs($logFile, "Error on line $lineNumber at index $printLineIndex.  Each line in the file must have 7 items:
								Course	DaySections	NightSections	InternetSections	ClassSize	Room	Hours." . PHP_EOL);
				$errorOnLine = true;  $errorInFile = true;
			}
		}
		if($errorOnLine == false)
		{
			//submit query
			echo  "$lineNumber: $printLine" . "<br>";
		}
		else
			echo $lineNumber . ": $printLine*" . "<br>";
	}
	if($errorInFile == false)
		fputs($logFile, "No errors detected." . PHP_EOL);
		
	fclose($readFile);	
	fclose($logFile);
	
	$testNum += 1;
	if($testNum > "44")
		$stillTesting = false;
}

##################################################################################################

/*******************FUNCTIONS******************/

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
	 *					$firstCourseOnLineFlag = a flag designating whether or not the course
	 *											 pulled from the line is the first course on the line
	 *					$firstCourseNumber = will store the number of the first course on the line
	 *										 and will be used to validate prerequisites
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
	 $COURSELETTERSMIN = 2;
	 $COURSELETTERSMAX = 4;
	 $LENGTHOFCOURSENUM = 3;
	 $COURSENUMBERMIN = 1;
	 $COURSENUMBERMAX = 499;
	 $FOLLOWCOURSENUMBERMAX = 2;
		
		while(ctype_upper($line[$lineIndex]) == true)
		{	//while line[lineindex] is an uppercase character
			$courseLetters[$courseLettersIndex] = $line[$lineIndex];
			$lineIndex++;
			$courseLettersIndex++;
		}
		//ERROR HANDLING 
			if((count($courseLetters) < $COURSELETTERSMIN) or (count($courseLetters) > $COURSELETTERSMAX))
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
					$courseNumbers[$courseNumbersIndex] = $line[$lineIndex];
					$lineIndex++;
					$courseNumbersIndex++;
				}
				
				if(count($courseNumbers) != $LENGTHOFCOURSENUM)
				{
					fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Course number must be exactly 3 digits." . PHP_EOL);
					return false;
				}
				
				$courseNumberInt = intval(implode($courseNumbers));	//converts the integer array to a solid string
																	//and converts the string value to an integer
																	
				if(($courseNumberInt < $COURSENUMBERMIN) or ($courseNumberInt > $COURSENUMBERMAX))
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
						$endCourseName[$endCourseNameIndex] = $line[$lineIndex];
						$lineIndex++;
						$endCourseNameIndex++;
					}
					if(count($endCourseName) > $FOLLOWCOURSENUMBERMAX)
					{
						fputs($logFile, "Error on line $lineNumber at index $lineIndex.  String of characters following course number is too long." . PHP_EOL);
						return false;
					}
					elseif(($line[$lineIndex] !=  " ") and ($line[$lineIndex] != "\r") and ($line[$lineIndex] != "\r"))
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
	
	function getNumber($line, &$lineIndex, $lineNumber, $flag, &$retrievedNumber, $logFile)
	{/*-----------------------------------------------------------------------------------------------
	 ********************** Function Prologue Comment: getNumber ********************
	 * Preconditions:  Data exists on the line provided by file or user.
	 *
	 * Postconditions:  Valid number was provided based on the flag provided
	 *
	 * Function Purpose:  Validates that number of sections is 0 or greater;
	 *					  Class size is at least 1; or
	 *					  Credit hours is between 1 and 12						
	 *
	 * Input Expected:	$line = line of text read in from the test file
	 *					$lineIndex = current index for $line
	 *					$lineNumber = current line of file
	 *					$flag = used to distinguish between the number to look for
	 *							1 = number of sections
	 *							2 = class size
	 *							3 = credit hours
	 *					$retrievedNumber = stores number gathered from the line to add to SQL query
	 *					$logFile = text file that errors are logged to
	 *
	 *
	 * Exceptions/Errors Thrown:  Number expected at index $lineIndex
	 *							  Section count cannot be less than 0
	 *							  Class size must be between $CLASSSIZEMIN and $CLASSSIZEMAX
	 *							  Number of hours must be between $HOURSMIN and $HOURSMAX
	 *							  Unexpected error occurred on line $lineNumber at index $lineIndex
	 *
	 * Files Accessed:	$logFile - for reporting errors
	 *
	 * Function Pseudocode Author:  Jared Cox
	 *
	 * Function Author:	Jared Cox
	 *
	 * Date of Original Implementation:	March 28, 2013
	 *
	 * Tested by SQA Member (NAME and DATE):  Jared Cox, March 31, 2013
	 * 
	 ** Modifications by:
	 * Modified By (Name and Date):
	 * Modifications Description:
	 *
	 * Modified By (Name and Date):
	 * Modifications Description:
	 -------------------------------------------------------------------------------------------------*/ 		
		
	//VARIABLES
		$numString = array();
		$numStringIndex = 0;
		$numOnLine = 0;
		$CLASSSIZEMIN = 1;
		$CLASSSIZEMAX = 200;
		$HOURSMIN = 1;
		$HOURSMAX = 12;
	/////////////////////////////////////
	
		while(ctype_digit($line[$lineIndex]) == true)
		{
			$numString[$numStringIndex] = $line[$lineIndex];
			$lineIndex++;
			$numStringIndex++;
		}
		
		$numOnLine = intval(implode($numString));
		
		if(count($numString) == 0)
		{
			fputs($logFile, "Error on line $lineNumber. Number expected at index $lineIndex." . PHP_EOL);
			return false;
		}
		else
		{
			switch($flag)
			{
				case 1: //sections	
						if($numOnLine < 0)
						{
							fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Section count cannot be less than 0." . PHP_EOL);
							return false;
						}
						else
						{
							$retrievedNumber = $numOnLine;
							return true;
						}
				
				case 2: //class size
						if(($numOnLine < $CLASSSIZEMIN) or ($numOnLine > $CLASSSIZEMAX))
						{
							fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Class size must be between $CLASSSIZEMIN and $CLASSSIZEMAX." . PHP_EOL);
							return false;
						}
						else
						{
							$retrievedNumber = $numOnLine;
							return true;
						}
				case 3: //teacher credit hours
						if(($numOnLine < $HOURSMIN) or ($numOnLine > $HOURSMAX))
						{
							fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Number of hours must be between $HOURSMIN and $HOURSMAX." . PHP_EOL);
							return false;
						}
						else
						{
							$retrievedNumber = $numOnLine;
							return true;
						}
				default: fputs($logFile, "Unexpected error occurred on line $lineNumber at index $lineIndex." . PHP_EOL); break;
			}
		}
	}
	
##################################################################################################	
	
	function getChar($line, &$lineIndex, $lineNumber, &$retrievedChar, $logFile)
	{/*-----------------------------------------------------------------------------------------------
	 ********************** Function Prologue Comment: getSlash ********************
	 * Preconditions:	Valid data has been provided up to this point
	 *
	 * Postconditions:  Times of day should follow this function
	 *
	 * Function Purpose:  Validates a class type - C or L
	 *
	 * Input Expected:  $line = line of text read in from the test file
	 *					$lineIndex = current index for $line
	 *					$lineNumber = current line of file
	 *					$retrievedChar = stores character gathered from the line to add to SQL query
	 *					$logFile = text file that errors are logged to
	 *
	 * Exceptions/Errors Thrown:  Illegal character encountered.  Room type must be 'C' or 'L'
	 *
	 * Files Accessed:  $logFile - for reporting errors
	 *
	 * Function Pseudocode Author:  Jared Cox
	 *
	 * Function Author:  Jared Cox
	 *
	 * Date of Original Implementation:  March 28, 2013
	 *
	 * Tested by SQA Member (NAME and DATE): Jared Cox, March 31, 2013
	 * 
	 ** Modifications by:
	 * Modified By (Name and Date):
	 * Modifications Description:
	 *
	 * Modified By (Name and Date):
	 * Modifications Description:
	 -------------------------------------------------------------------------------------------------*/ 	//VARIABLES
	//VARIABLES
		$charOnLine = $line[$lineIndex];
	
	/////////////////////////////////////
	
		switch($charOnLine)
		{
			case 'C': $retrievedChar = $charOnLine;
					  $lineIndex++;
					  return true;
			case 'L': $retrievedChar = $charOnLine;
					  $lineIndex++;
					  return true;
			default :  fputs($logFile, "Error on line $lineNumber at index $lineIndex. Illegal character encountered.  Room type must be 'C' or 'L'." . PHP_EOL);
					   $lineIndex++;
					  return false;
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
		while($line[$lineIndex] == " ")
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
		if($line[$lineIndex] != " " and ($line[$lineIndex] != "\r") and ($line[$lineIndex] != "\t"))
		{
			fputs($logFile, "Error on line $lineNumber.  Whitespace must separate elements on line." . PHP_EOL);
		}
		else
		{
			return true;
		}
	}

##################################################################################################