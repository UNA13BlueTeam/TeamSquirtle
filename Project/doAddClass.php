<?php include("includes/header.php");
	  
	$link = mysqli_connect($host, $user, $pass, $db, $port);
	
	$error1 = false;
	$error2 = false;
	$error3 = false;
	
    if(!$link)
	{
        die('cannot connect database'. mysqli_error($link));
    }
    if($_POST['flag']=="form")
	{
    	echo("<p>inserting</p>");
		// Get variables from input form
		$courseName = strtoupper($_POST['course']);
		$dsection = $_POST['dsection'];
		$nsection = $_POST['nsection'];
		$isection = $_POST['isection'];
		$classSize = $_POST['classSize'];
		$roomType = $_POST['roomType'];
		$hours = $_POST['hours'];
		$prereqs = strtoupper($_POST['prereq']);
		$conflicts = strtoupper($_POST['conflict']);
		
		$outForm = "$courseName $dsection $nsection $isection $classSize $roomType $hours";
		
		$outFile = fopen("formSubmissionFile.txt", "w");
		$outFileName = "formSubmissionFile.txt";
		fwrite($outFile, $outForm);
		fclose($outFile);
		
		$error1 = scanCTS($outFileName, $outFile);
		
		if($prereqs)
		{
			$outForm = "$courseName $prereqs";
		
			$outFile = fopen("formSubmissionFile.txt", "w");
			$outFileName = "formSubmissionFile.txt";
			fwrite($outFile, $outForm);
			fclose($outFile);
			
			$error2 = scanPrereqs($outFileName, $outFile);
		}
		if($conflicts)
		{
			$outForm = "$courseName $conflicts";
		
			$outFile = fopen("formSubmissionFile.txt", "w");
			$outFileName = "formSubmissionFile.txt";
			fwrite($outFile, $outForm);
			fclose($outFile);
			
			$error3 = scanConflicts($outFileName, $outFile);
		}
		
	}
	elseif($_POST['flag']=="file")
	{
		
		$classFile = $_FILES["classFile"]["tmp_name"];
		$classFileName = $_FILES["classFile"]["name"];
		
		$prereqFile = $_FILES["prereqFile"]["tmp_name"];
		$prereqFileName = $_FILES["prereqFile"]["name"];
		
		$conflictFile = $_FILES["conflictFile"]["tmp_name"];
		$conflictFileName = $_FILES["conflictFile"]["name"];
		
		if($classFile)
		{
			$error1 = scanCTS($classFile, $classFileName);
		}
		if($prereqFile)
		{
			$error2 = scanPrereqs($prereqFile, $prereqFileName);
		}
		if($conflictFile)
		{
			$error3 = scanConflicts($conflictFile, $conflictFileName);
		}
	}
	if(($error1 == false) and ($error2 == false) and ($error3 == false))
	{
		header("Location: addClass.php");
	}
	
	mysqli_close($link);
include("includes/footer.php");
?>

<!-- --------------------------****************------------------------------- -->
<!-- ------------**************************************************----------- -->
<!-- --------------------------CTS FILE SCANNER------------------------------ -->
<!-- ------------**************************************************----------- -->
<!-- --------------------------****************------------------------------- -->
<?php 
	function scanCTS($fileName, $prettyName)
	{/*-----------------------------------------------------------------------------------------------
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
	 * Modified By (Name and Date):	Jared Cox, April 9, 2013
	 * Modifications Description:	Added a check for empty line before uploading to database
	 *
	 * Modified By (Name and Date):
	 * Modifications Description:
	 -------------------------------------------------------------------------------------------------*/ 		
	
	//FLAGS
		$startQuery = true;
		$SECTIONSFLAG = 1;		//these three flags have integer values to distinguish between the
		$CLASSSIZEFLAG = 2;		//types of flags passed to the "getNumber" function
		$HOURSFLAG = 3;

			
			$readFile=fopen($fileName,"r") or die("Unable to open $fileName");
			echo("<h2>SCANNING COURSES TO SCHEDULE</h2><hr>");
			echo("<h3>Checking $prettyName for errors...</h3>");

			// echo "<br> <br> <br>" . "Test File: $fileName" . "<br> <br> <br>";
		global $link;		
		$predefQuery = "SELECT courseName FROM courses";
		$predefResult = mysqli_query($link, $predefQuery);
		$predef = array();
		while($row = mysqli_fetch_row($predefResult)){
			array_push($predef, $row[0]);
		}
		
		
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
			{	
				// $printLineIndex == strlen(trim($printLine) means we are at the end of the current line				
				
				if((count($readLine)) == $REQUIREDITEMSONLINE)
				{	//if there is not $REQUIREDITEMSONLINE (7) items on the line, something is missing
					
					skipWhitespace($printLine, $printLineIndex); 
					if(getCTS($printLine, $printLineIndex, $lineNumber, $currentCourse) == false)
					{//an invalid course format was encountered on the line
						$errorOnLine = true;  $errorInFile = true;
					}
					
					if($errorOnLine == false)
					{
						if(in_array($currentCourse, $listOfCourses) == true)
						{
							echo("Error on line $lineNumber.  Course already defined." . PHP_EOL);
							$errorOnLine = true; $errorInFile = true;
						}
						else
						{
							$listOfCourses[$listOfCoursesIndex] = $currentCourse;
							$listOfCoursesIndex++;
							$firstCourseOnLineFlag = false;
							$query = "INSERT INTO courses VALUES (NULL, '$currentCourse'";
						}	
					}
					
					if($errorOnLine == false)
					{
						if(verifyWhiteSpace($printLine, $printLineIndex, $lineNumber) == false)
						{   
							$errorOnLine = true; $errorInFile = true;
						}
					}
					
					if($errorOnLine == false)
					{
						skipWhitespace($printLine, $printLineIndex);
						if(getNumber($printLine, $printLineIndex, $lineNumber, $SECTIONSFLAG, $retrievedNumber) == false)
						{//invalid day sections count was encountered
							$errorOnLine = true; $errorInFile = true;
						}
					}
					
					if($errorOnLine == false)
					{//valid day sections count was encountered
						if(verifyWhitespace($printLine, $printLineIndex, $lineNumber, $lineNumber) == false)
						{	
							$errorOnLine = true; $errorInFile = true;
						}
					}
					
					if($errorOnLine == false)
					{//add $retrievedNumber to query
						$daySection = $retrievedNumber;
						skipWhitespace($printLine, $printLineIndex);
						if(getNumber($printLine, $printLineIndex, $lineNumber, $SECTIONSFLAG, $retrievedNumber) == false)
						{//invalid night sections count was encountered
							$errorOnLine = true; $errorInFile = true;
						}
					}
					
					if($errorOnLine == false)
					{//valid night sections count was encounterd
						if(verifyWhitespace($printLine, $printLineIndex, $lineNumber) == false)
						{	
							$errorOnLine = true; $errorInFile = true;
						}
					}
					
					if($errorOnLine == false)
					{
						$nightSection = $retrievedNumber;
						skipWhitespace($printLine, $printLineIndex);
						if(getNumber($printLine, $printLineIndex, $lineNumber, $SECTIONSFLAG, $retrievedNumber) == false)
						{//invalid internet sections count was encountered
							$errorOnLine = true; $errorInFile = true;
						}
					}
					
					if($errorOnLine == false)
					{//valid internet sections count was encountered
						if(verifyWhitespace($printLine, $printLineIndex, $lineNumber) == false)
						{	
							$errorOnLine = true; $errorInFile = true;
						}
					}
					
					if($errorOnLine == false)
					{//add $retrievedNumber to query
						$internetSection = $retrievedNumber;
						skipWhitespace($printLine, $printLineIndex);
						if(getNumber($printLine, $printLineIndex, $lineNumber, $CLASSSIZEFLAG, $retrievedNumber) == false)
						{//invalid class size count encountered
							$errorOnLine = true; $errorInFile = true;
						}
					}
					
					if($errorOnLine == false)
					{//valid class size count encountered
						if(verifyWhitespace($printLine, $printLineIndex, $lineNumber) == false)
						{	
							$errorOnLine = true; $errorInFile = true;
						}
					}
					
					if($errorOnLine == false)
					{//add $retrievedNumber to query
						$sizeForQuery = $retrievedNumber;
						skipWhitespace($printLine, $printLineIndex);
						if(getChar($printLine, $printLineIndex, $lineNumber, $retrievedChar) == false)
						{//character was not C or L
							$errorOnLine = true; $errorInFile = true;
						}
					}

					if($errorOnLine == false)
					{//character was C or L
						if(verifyWhitespace($printLine, $printLineIndex, $lineNumber) == false)
						{	
							$errorOnLine = true; $errorInFile = true;
						}
					}

					if($errorOnLine == false)
					{//add $retrievedChar to query
						$typeForQuery = $retrievedChar;
						skipWhitespace($printLine, $printLineIndex);
						if(getNumber($printLine, $printLineIndex, $lineNumber, $HOURSFLAG, $retrievedNumber) == false)
						{//invalid hours count encountered
							$errorOnLine = true; $errorInFile = true;
						}
					}
					if($errorOnLine == false)
					{//valid hours count encountered
						$hoursForQuery = $retrievedNumber;
						//submit query
					}		  
				}
				else
				{
					echo("Error on line $lineNumber at index $printLineIndex.  Each line in the file must have 7 items:
					// 				Course	DaySections	NightSections	InternetSections	ClassSize	Room	Hours." . PHP_EOL);
					$errorOnLine = true;  $errorInFile = true;
				}
			}
			if($errorOnLine == false)
			{
				if(strlen(trim($printLine)) != 0)
				{
					if(in_array($currentCourse, $predef))
					{
						$delete = "DELETE FROM courses WHERE courseName = '$currentCourse'";
						
						mysqli_query($link, $delete) or die("<h2>Delete failed</h2>");
					}
					
					$query = $query.", $daySection, $nightSection, $internetSection, $sizeForQuery, '$typeForQuery', $hoursForQuery)";
					$success = mysqli_query($link, $query);
					//echo("<p>No errors on line $lineNumber! Attempting to upload line.</p>");
					if($success)
					{
						//echo("<p>File uploaded successfully!</p>");
					}else
					{
						echo("<p class=\"warning\">There was a problem uploading the file, please try again. <br> If the problem persists, please contact your system administrator.</p>");
					}					
				}
				else
				{
					//echo "Line $lineNumber is empty. <br>";
				}
				echo "$lineNumber: $printLine" . "<br>";
			}
			else
			{
				echo $lineNumber . ": $printLine*" . "<br>";
				echo("<p class=\"error\"> Error discovered on line $lineNumber. Attempting to continue uploading file.</p>");
			}
		}
		if($errorInFile == false)
			echo("No errors detected." . PHP_EOL);
		echo("<hr>");
			
		fclose($readFile);	
		return $errorInFile;		
	}
##################################################################################################

/*******************FUNCTIONS******************/

	function getCTS($line, &$lineIndex, $lineNumber, &$currentCourse)
	{/*-----------------------------------------------------------------------------------------------
	 ********************** Function Prologue Comment: getCTS ********************
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
				echo("Error on line $lineNumber at index $lineIndex. Course letters must be between 2 and 4 characters." . PHP_EOL);
				return false;
			}/*
			elseif($courseLetters not in Department Courses)
			{
				echo("Error on line $lineNumber at index $lineIndex.  Course letters is not a part of the department." . PHP_EOL);
				return false;
			*/
			elseif(ctype_lower($line[$lineIndex]) == true)
			{	//line[lineindex] is lowercase
				echo("Error on line $lineNumber at index $lineIndex.  Files must contain ONLY uppercase letters." . PHP_EOL);
				return false;
			}
			elseif(ctype_alnum($line[$lineIndex] == false))
			{	//line[lineindex] is not alphabetic or numeric
				echo("Error on line $lineNumber at index $lineIndex.  Invalid character encountered." . PHP_EOL);
				return false;
			}
			elseif($line[$lineIndex] == " ")
			{
				echo("Error on line $lineNumber at index $lineIndex.  Course number must immediately follow course letters." . PHP_EOL);
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
					echo("Error on line $lineNumber at index $lineIndex.  Course number must be exactly 3 digits." . PHP_EOL);
					return false;
				}
				
				$courseNumberInt = intval(implode($courseNumbers));	//converts the integer array to a solid string
																	//and converts the string value to an integer
																	
				if(($courseNumberInt < $COURSENUMBERMIN) or ($courseNumberInt > $COURSENUMBERMAX))
				{
					echo("Error on line $lineNumber at index $lineIndex.  Course number exceeds boundaries. Must be between 001 and 499." . PHP_EOL);
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
						echo("Error on line $lineNumber at index $lineIndex.  String of characters following course number is too long." . PHP_EOL);
						return false;
					}
					elseif(($line[$lineIndex] !=  " ") and ($line[$lineIndex] != "\r") and ($line[$lineIndex] != "\t"))
					{//only whitespace or carriage return can immediately follow a course on line
						echo("Error on line $lineNumber at index $lineIndex.  Invalid character in string following course number." . PHP_EOL);
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
	
	function getNumber($line, &$lineIndex, $lineNumber, $flag, &$retrievedNumber)
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
			echo("Error on line $lineNumber. Number expected at index $lineIndex." . PHP_EOL);
			return false;
		}
		else
		{
			switch($flag)
			{
				case 1: //sections	
						if($numOnLine < 0)
						{
							echo("Error on line $lineNumber at index $lineIndex.  Section count cannot be less than 0." . PHP_EOL);
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
							echo("Error on line $lineNumber at index $lineIndex.  Class size must be between $CLASSSIZEMIN and $CLASSSIZEMAX." . PHP_EOL);
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
							echo("Error on line $lineNumber at index $lineIndex.  Number of hours must be between $HOURSMIN and $HOURSMAX." . PHP_EOL);
							return false;
						}
						else
						{
							$retrievedNumber = $numOnLine;
							return true;
						}
				default: echo("Unexpected error occurred on line $lineNumber at index $lineIndex." . PHP_EOL); break;
			}
		}
	}
	
##################################################################################################	
	
	function getChar($line, &$lineIndex, $lineNumber, &$retrievedChar)
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
			default :  echo("Error on line $lineNumber at index $lineIndex. Illegal character encountered.  Room type must be 'C' or 'L'." . PHP_EOL);
					   $lineIndex++;
					  return false;
		}
	}	
##################################################################################################

	function verifyWhitespace($line, $lineIndex, $lineNumber)
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
			echo("Error on line $lineNumber.  Whitespace must separate elements on line." . PHP_EOL);
		}
		else
		{
			return true;
		}
	}

##################################################################################################
?>
<!-- --------------------------****************------------------------------- -->

<!-- ----------------------**************************------------------------- -->
<!-- ------------**************************************************----------- -->
<!-- -----------------------PREREQUISITE FILE SCANNER------------------------- -->
<!-- ------------**************************************************----------- -->
<!-- ----------------------**************************------------------------- -->
<?php

function scanPrereqs($fileName, $prettyName)
{	/*-----------------------------------------------------------------------------------------------
	 ********************** Function Prologue Comment: scanPrereqs ********************
	 * Preconditions:  Data exists on the line
	 *
	 * Postconditions: None
	 *
	 * Function Purpose:  Validates the authenticity of a file containing information about
	 *					  prerequisites for specified courses in the file.
	 *
	 * Input Expected:  Text input of the following format:
	 *					XYZ ^ (2 - 4)
	 *					Where X must be 2 to 4 uppercase letters
	 *					Where Y must be a 3 digit number between 001-499
	 *					Where Z can be up to 2 uppercase letters
	 *					There must be at least 2 instances of XYZ per line, but no more than 4.
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
	 *							  Courses in file must contain between 1 and 3 prerequisites
	 *							  Whitespace must separate elements on the line
	 *							  
	 *
	 * Files Accessed:  Any file given to the program
	 *					$logFile for reporting errors
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
	 * Modified By (Name and Date):	Jared Cox, Michael Debs
	 *								April 10, 2013
	 * Modifications Description:	Fixed prerequisite scanner to check that the
	 *								course exists in the courses table in the database.
	 *								If it doesn't, we print out an appropriate message
	 *								saying the line scanned correctly, but the prereqs
	 *								were not uploaded to the database (because the course
	 *								requiring prereqs was not in the courses section of
	 *								the databse).  We suggest adding the course first
	 *								through form submission, and then adding the prereqs.
	 *
	 * Modified By (Name and Date): Jared Cox, April 10, 2013
	 * Modifications Description:	Moved the check for preexisting prerequisites to right
	 *								before the full query is submitted.  Otherwise, it was
	 *								deleting a course found on a wholy incorrect line.
	 *								ex.		AB101	AB98
	 *								AB101 already in prereqs, so we drop that record to 
	 *								overwrite. But an error is later found on the line, 
	 *								so a query is never submitted (thus, AB101 never gets
	 *								stored in the prereq table again.
	 -------------------------------------------------------------------------------------------------*/ 		
	
	global $link, $db;
	//FLAGS
	$firstCourseOnLineFlag = true;
	
	$predefCourses = array();	//of courses
	$predefCoursesQuery = "SELECT DISTINCT courseName FROM courses";
	$predefCoursesResult = mysqli_query($link, $predefCoursesQuery);
	while($row = mysqli_fetch_row($predefCoursesResult))
	{
		array_push($predefCourses, $row[0]);
	}
	$predef = array();
	$predefQuery = "SELECT DISTINCT course FROM prereqs";
	$predefResult = mysqli_query($link, $predefQuery);
	while($row = mysqli_fetch_row($predefResult))
	{
		array_push($predef, $row[0]);
	}
	
	// $predef = mysqli_fetch_all($predefResult, MYSQLI_NUM);
	
	$readFile=fopen($fileName,"r") or die("Unable to open $fileName");
	
	echo("<h1>SCANNING PREREQS</h1><br>");
	echo("<h3>Checking $prettyName for errors...</h3>");
			
		
		
	//VARIABLES	
	$lineNumber = 0;	//used in listing errors
	$printLine = " ";	//line read in from file
	$printLineIndex = 0;	
	$currentCourse = "";		//string		
	$errorOnLine = false;
	$listOfCourses = array();	//first courses on each line of a file
	$listOfPrereqs = array();	//used in detecting duplicate courses on a line
	$itemCount = 0;		//if there are more than 4  or less than 2 separate items on a line, there is an incorrect number of prerequisites
	$errorInFile = false;
	$firstCourseOnLine  = "";	//string used only for validation
	$firstCourseNumber = 0;	//used in checking if a prerequisiste course is higher than the course requiring prerequisites
	$REQUIREDITEMSMIN = 2;	//must be at least 2 courses on the line (a course and it's one prerequisite)
	$REQUIREDITEMSMAX = 4;	//no more than 4 courses on the line (a course and up to three prerequisites)
	
	
	while(!feof($readFile))
	{
		$errorOnLine = false;
		$printLineIndex = 0;
		$firstCourseOnLineFlag = true;
		do
		{//this block will skip over any empty lines in a file
		 $printLine = fgets($readFile);
		 $lineNumber++;
		}while((strlen(trim($printLine)) == 0) and (!feof($readFile)));
		
		$printLine = $printLine . "\r";	//append a carriage return to the end of each line
										//otherwise, a line without a hard return at the end
										//will produce a whitespace error in this scanner
		
		$listOfPrereqs = array();
		$firstCourseOnLine = "";
			
		$readLine = preg_split('/\s+/', trim($printLine));	//splits the line into an array of elements
														//each element will be a contiguous string of characters
														//all whitespace is ignored on line for this function due to " '/\s+/' "
		
		$fieldNum=0;	//used in SQL queries to determine which prereq to insert to
		
		while(($printLineIndex < (strlen(trim($printLine)))) and ($errorOnLine == false))
		{	//$printLineIndex == strlen(trim($printLine) means we are at the end of the current line
		
			if((count($readLine)) >= $REQUIREDITEMSMIN and (count($readLine) <= $REQUIREDITEMSMAX))
			{	//preg_split counts end of line as a nonwhitespace line element, so we check on boundaries
				//of 3 and 5 instead of 2 and 4
				
				skipWhitespace($printLine, $printLineIndex); 
				if(getCourse($printLine, $printLineIndex, $lineNumber, $currentCourse, $firstCourseOnLineFlag, $firstCourseNumber, $currentCourseNumber) == false)
				{//an invalid course format was encountered on the line
					$errorOnLine = true;  $errorInFile = true;
				}
				else
				{//a valid course format was encountered on the line
					if(in_array($currentCourse, $listOfPrereqs) == true)
					{
						echo("Error on line $lineNumber.  Duplicate course found on line." . PHP_EOL);
						$errorOnLine = true;  $errorInFile = true;
					}
					else
					{
						array_push($listOfPrereqs, $currentCourse);
					}
					if($firstCourseOnLineFlag == true)
					{	
						$firstCourseOnLine = $currentCourse;
						if(in_array($firstCourseOnLine, $listOfCourses))
						{
							echo "$firstCourseOnLine predefined in file. Put all prerequisites for a file on one line to avoid overwrite.<br>";
							$errorOnLine = true; $errorInFile = true;
						}
						else
						{
							
							array_push($listOfCourses, $firstCourseOnLine);
							
							//add course to $listOfPrereqs
							array_push($listOfPrereqs, $currentCourse);
							$insertQuery1= "INSERT INTO $db.prereqs (course";
							$insertQuery2= "VALUES ('$currentCourse'";
							$firstCourseOnLineFlag = false;
						}						
					}
					else
					{
						$insertQuery1 = $insertQuery1.", prereq".$fieldNum;
						$insertQuery2 = $insertQuery2.", '$currentCourse'";
					}
					if(($printLine[$printLineIndex] != " ") and ($printLine[$printLineIndex] != "\r") and ($printLine[$printLineIndex] != "\t"))
					{//only whitespace and end of line can immediately follow a course on the line
							$errorOnLine = true;  $errorInFile = true;
							echo("Error on line $lineNumber at index $printLineIndex.  Whitespace must separate elements on the line." . PHP_EOL);	
					}
				}
			}	
			else
			{
				echo("Error on line $lineNumber.  Courses in file must contain between 1 and 3 prerequisites.<br>" . PHP_EOL);
				$errorOnLine = true;  $errorInFile = true;
			}
			$fieldNum++;
		}
			
		if($errorOnLine == false)
		{
			if(strlen(trim($printLine)) != 0)
			{
				if(in_array(trim($firstCourseOnLine), $predef))
				{//if the course already has prereqs defined, we delete the course from
				 //the database and create a new record for the course
					$delete = "DELETE FROM $db.prereqs WHERE course = '$firstCourseOnLine'";
					
					mysqli_query($link, $delete) or die("<h2>Delete failed.</h2>");
				}
				if(in_array(trim($firstCourseOnLine), $predefCourses))
				{
					$insertQuery1 = $insertQuery1.") ";
					$insertQuery2 = $insertQuery2.")";
					$insertQuery = $insertQuery1.$insertQuery2;
					$insertion = mysqli_query($link, $insertQuery);
					//echo("No errors on line $lineNumber! Attempting to upload line.<br>");
					if($insertion)
					{
						//echo("File uploaded successfully!<br>");
					}
					else
					{
						echo("<p class=\"warning\">There was a problem uploading the file, please try again. <br> If the problem persists, please contact your system administrator.</p>");
					}
					echo  "$lineNumber: $printLine" . "<br>";
				}
				else
				{
					echo "<p class=\"warning\">Line $lineNumber is correct, but $firstCourseOnLine does not exist. (Try adding $firstCourseOnLine through form submission.)</p> <br>";
					echo "$lineNumber: $printLine <br>";
				}
			}
			else
			{
				//echo "Line $lineNumber is empty." . "<br>";
			}
		}
		else
		{
			echo $lineNumber . ": $printLine*" . "<br>";
			echo("<p class=\"error\"> Error discovered on line $lineNumber. Attempting to continue uploading file.</p>");
		}
		
	}
	if($errorInFile == false)
		echo("No errors detected." . PHP_EOL);
		
	fclose($readFile);	
	return $errorInFile;
}
##################################################################################################
	
	/**********FUNCTIONS*********/
function getCourse($line, &$lineIndex, $lineNumber, &$currentCourse, $firstCourseOnLineFlag, &$firstCourseNumber, &$currentCourseNumber){
	/*-----------------------------------------------------------------------------------------------
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
				echo("Error on line $lineNumber at index $lineIndex. Course letters must be between 2 and 4 characters." . PHP_EOL);
				return false;
			}
			elseif(ctype_lower($line[$lineIndex]) == true)
			{	//line[lineindex] is lowercase
				echo("Error on line $lineNumber at index $lineIndex.  Files must contain ONLY uppercase letters." . PHP_EOL);
				return false;
			}
			elseif(ctype_alnum($line[$lineIndex] == false))
			{	//line[lineindex] is not alphabetic or numeric
				echo("Error on line $lineNumber at index $lineIndex.  Invalid character encountered." . PHP_EOL);
				return false;
			}
			elseif($line[$lineIndex] == " ")
			{
				echo("Error on line $lineNumber at index $lineIndex.  Course number must immediately follow course letters." . PHP_EOL);
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
					echo("Error on line $lineNumber at index $lineIndex.  Course number must be exactly 3 digits." . PHP_EOL);
					return false;
				}
				
				$courseNumberInt = intval(implode($courseNumbers));	//converts the integer array to a solid string
																	//and converts the string value to an integer
				
				//SPECIFIC TO PREREQSCANNER. REMOVE IF REUSED ELSEWHERE
					if($firstCourseOnLineFlag == true)
					{
						$firstCourseNumber = $courseNumberInt;
					}
					elseif($courseNumberInt > $firstCourseNumber)
					{
						echo("Error on line $lineNumber at index $lineIndex.  Prerequisite is a higher level course than course requiring prerequisites." . PHP_EOL);
						return false;
					} 
																	
				if(($courseNumberInt < $COURSENUMBERMIN) or ($courseNumberInt > $COURSENUMBERMAX))
				{
					echo("Error on line $lineNumber at index $lineIndex.  Course number exceeds boundaries. Must be between 001 and 499." . PHP_EOL);
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
						echo("Error on line $lineNumber at index $lineIndex.  String of characters following course number is too long." . PHP_EOL);
						return false;
					}
					elseif(($line[$lineIndex] !=  " ") and ($line[$lineIndex] != "\r") and ($line[$lineIndex] != "\t"))
					{//only whitespace or carriage return can immediately follow a course on line
						echo("Error on line $lineNumber at index $lineIndex.  Invalid character in string following course number." . PHP_EOL);
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
	
function skipWhitespace($line, &$lineIndex)
{/*-----------------------------------------------------------------------------------------------
	 ********************** Function Prologue Comment: skipWhitespace ********************
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
?>
<!-- ----------------------**************************------------------------- -->











<!-- --------------------------****************------------------------------- -->
<!-- ------------**************************************************----------- -->
<!-- --------------------------CONFLICT FILE SCANNER------------------------------ -->
<!-- ------------**************************************************----------- -->
<!-- --------------------------****************------------------------------- -->
<?php 
	function scanConflicts($fileName, $prettyName)
{/*-----------------------------------------------------------------------------------------------
	 ********************** Function Prologue Comment: scanConflicts ********************
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
	 *
	 * Function Pseudocode Author:  Jared Cox
	 *
	 * Function Author:  Jared Cox
	 *
	 * Date of Original Implementation: April 4, 2013
	 *
	 * Tested by SQA Member (NAME and DATE): Jared Cox, April 6, 2013
	 * 
	 ** Modifications by:
	 * Modified By (Name and Date):	Michael Debs April 11, 2013
	 * Modifications Description: Integrated scanner to sync with database
	 *
	 * Modified By (Name and Date):	Jared Cox April 12, 2013
	 * Modifications Description:	Removed fputs and logFile parameters
	 *								Added check for empty lines
	 -------------------------------------------------------------------------------------------------*/

	
	// get global variables used
	global $link, $db;
	
	$predefCourses = array();	//of courses
	$predefCoursesQuery = "SELECT DISTINCT courseName FROM courses";
	$predefCoursesResult = mysqli_query($link, $predefCoursesQuery);
	while($row = mysqli_fetch_row($predefCoursesResult))
	{
		array_push($predefCourses, $row[0]);
	}
	
	$predef = array();
	$predefQuery = "SELECT DISTINCT course FROM conflicts";
	$predefResult = mysqli_query($link, $predefQuery);
	while($row = mysqli_fetch_row($predefResult))
	{
		array_push($predef, $row[0]);
	}
	
	
	$readFile=fopen($fileName,"r") or die("Unable to open $fileName");
	echo("<h1>SCANNING CONFLICTS</h1><br>");
	echo("<h3>Checking $prettyName for errors...</h3>");
	
	
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
		
		$allConflicts = "";
		
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
		
		if((count($readLine)) >= $REQUIREDITEMSONLINE)
		{//if there is not at $REQUIREDITEMSONLINE(2) items on the line, something is wrong
			
		
			skipWhitespace($printLine, $printLineIndex);
			if(getCTS($printLine, $printLineIndex, $lineNumber, $currentCourse) == false)
			{
				$errorOnLine = true; $errorInFile = true;
			}
			
			if($errorOnLine == false)
			{
				if(in_array($currentCourse, $listOfCourses) == true)
				{
					echo "Error on line $lineNumber.  Conflict time already defined in file. <br>";
					$errorOnLine = true; $errorInFile = true;
				}
				else
				{
					$listOfCourses[$listOfCoursesIndex] = $currentCourse;
					$listOfCoursesIndex++;
					
					// Add course to list of conflict times
					array_push($listOfConflictTimes, $currentCourse);
					
					//start new query
					$insertQuery1= "INSERT INTO $db.conflicts (course";
					$insertQuery2= "VALUES ('$currentCourse'";
				}
			}
			// Keeps track with the number of conflicts associated with this class
			$numberOfConflicts = 0;
			
			while(($printLineIndex < strlen(trim($printLine))) and ($errorOnLine == false))
			{//buildling the conflict time
				if($errorOnLine == false)
				{//add $currentCourse to sql query
					if(verifyWhitespace($printLine, $printLineIndex, $lineNumber) == false)
					{
						
						$errorOnLine = true; $errorInFile = true;
					}
				}
			
				if($errorOnLine == false)
				{
					skipWhitespace($printLine, $printLineIndex);
				}
				if($errorOnLine == false)
				{
					if(getDaysOfWeek($printLine, $printLineIndex, $lineNumber, $retrievedDOW) == false)
					{
						$errorOnLine = true; $errorInFile = true;
					}
				}
				
				if($errorOnLine == false)
				{//add $retrievedDOW to sql query
					if(getSlash($printLine, $printLineIndex, $lineNumber, $retrievedSlash) == false)
					{
						$errorOnLine = true; $errorInFile = true;
					}
				}
				
				if($errorOnLine == false)
				{
					if(getTime($printLine, $printLineIndex, $lineNumber, $retrievedTime) == false)
					{
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
						$allConflicts = $allConflicts." ".$conflictTime;
					}
					else
					{
						$errorOnLine = true; $errorInFile = true;
					}
				}
			}
		}
		else 
		{
			if(strlen(trim($printLine)) != 0)
			{
				echo "Error on line $lineNumber at index $printLineIndex.  Each line in the file must have at least 2 items:
			         			Minutes DAYSOFWEEK_ForwardSlash_MilitaryTimeOfDay DAYSOFWEEK_ForwardSlash_MilitaryTimeOfDay 
								... DAYSOFWEEK_ForwardSlash_MilitaryTimeOfDay <br>";
				$errorOnLine = true;  $errorInFile = true;
			}	
		}
		
		// If done gathering info and ready to submit to query
		if(($errorOnLine == false) and (strlen(trim($printLine)) != 0))
		{
			// If course already exist, then delete before submitting
			if(in_array(trim($currentCourse), $predef))
			{//if the course already has conflicts defined, we delete the course from
			 //the database and create a new record for the course
				$delete = "DELETE FROM $db.conflicts WHERE course = '$currentCourse'";
				mysqli_query($link, $delete) or die("<h2>Delete failed.</h2>");
			}
			
			//submit query
			if(in_array($currentCourse, $predefCourses))
			{
				$insertQuery1 = $insertQuery1.", times) ";
				$insertQuery2 = $insertQuery2.", '$allConflicts')";
				$insertQuery = $insertQuery1.$insertQuery2;
				//echo("No errors on line $lineNumber!  Attempting to upload line.<br>");
				$insertion = mysqli_query($link, $insertQuery);
				if($insertion)
				{
					//echo("File uploaded successfully!<br>");
				}
				else
				{
					echo("<p class=\"warning\">There was a problem uploading the file, please try again. <br> If the problem persists, please contact your system administrator.</p>");
				}
			}
			else
			{
				echo "<p class=\"warning\">Line $lineNumber is correct, but $currentCourse does not exist. (Try adding $currentCourse through form submission.)</p> <br>";
			}
			echo  "$lineNumber: $printLine" . "<br>";
		}
		else
		{
			if(strlen(trim($printLine)) == 0)
			{
				//echo "Line $lineNumber is empty. <br>";
			}
			else
			{
				echo $lineNumber . ": $printLine*" . "<br>";
				echo("<p class=\"error\"> Error discovered on line $lineNumber. Attempting to continue uploading file.</p>");
				echo("<p class=\"error\"> Error discovered on line $lineNumber. Attempting to continue uploading file.</p>");
				
			}
		}
	}
	if($errorInFile == false)
	{
		echo "No errors detected. <br>";
	}
	
	fclose($readFile);
	return $errorInFile;
}
	
	
	
	
	
	##################################################################################################

	function getTime($line, &$lineIndex, $lineNumber, &$retrievedTime)
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
			echo "Error on line $lineNumber at index $lineIndex.  Expected a digit. <br>";
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
				echo "Error on line $lineNumber at index $lineIndex.  Invalid time encountered. <br>";
				return false;
			}			
		}
		
		//second digit in time
		if(ctype_digit($line[$lineIndex]) == false)
		{
			echo "Error on line $lineNumber at index $lineIndex.  Expected a digit. <br>";
			return false;
		}
		else
		{
			if(($firstDigit == 2) && ($line[$lineIndex] > 3))
			{
				echo "Error on line $lineNumber at index $lineIndex.  Invalid time encountered. <br>";
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
			echo "Error on line $lineNumber at index $lineIndex.  Expected a ':'. <br>";
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
			echo "Error on line $lineNumber at index $lineIndex.  Expected a digit. <br>";
			return false;
		}
		else
		{
			if(($line[$lineIndex] < 0) or ($line[$lineIndex] > 5))
			{
				echo "Error on line $lineNumber at index $lineIndex.  Invalid time encountered. <br>";
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
			echo "Error on line $lineNumber at index $lineIndex.  Expected a digit. <br>";
			return false;
		}
		else
		{
			if(($line[$lineIndex] < 0) or ($line[$lineIndex] > 9))
			{
				echo "Error on line $lineNumber at index $lineIndex.  Invalid time encountered. <br>";
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

	function getDaysOfWeek($line, &$lineIndex, $lineNumber, &$retrievedDOW)
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
						echo "Error on line $lineNumber at index $lineIndex.  Days of week out of order. <br>";
						return false;
					}
				}
				else
				{
					echo "Error on line $lineNumber at index $lineIndex.  Duplicate found in days of week. <br>";
					return false;
				}
			}
			else
			{
				echo "Error on line $lineNumber at index $lineIndex.  Character found is not one of MTWRFS. <br>";
				return false;
			}
		}
		if(ctype_lower($line[$lineIndex]) == true)
		{
			echo "Error on line $lineNumber at index $lineIndex.  All characters must be uppercase. <br>";
			return false;
		}
		$retrievedDOW = implode($daysFound);
		if(strlen(trim($retrievedDOW)) < 1)
		{
			echo "Error on line $lineNumber at index $lineIndex.  Days of week not specified. <br>";
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

	function getSlash($line, &$lineIndex, $lineNumber, &$retrievedChar)
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
			echo "Error on line $lineNumber at index $lineIndex. Expected '/'. <br>";
			return false;
		}
		else
		{
			$retrievedChar = $charOnLine;
			$lineIndex++;
			return true;
		}
	}




			