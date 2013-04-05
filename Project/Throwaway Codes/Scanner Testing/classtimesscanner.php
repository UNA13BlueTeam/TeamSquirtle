<html>
<body>

<?php

	//FLAGS
		$stillTesting = true;
		$startQuery = true;
		
	//FILE SETUP
		$testName = "Tests/classt/classt";
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
	$errorOnLine = false;
	$errorInFile = false;
	$REQUIREDITEMSONLINE = 2;	//there must be at least 2 items on a line, otherwise there is an error
	$listOfTimes = array();
	$listOfTimesIndex = 0;
	$readLine = " ";
	$retrievedNumber = 0;
	$retrievedDOW = " ";
	$retrievedSlash = " ";
	$retrievedTime = " ";
	
	while(!feof($readFile))
	{
	
		$listOfTimes = array();
		$listOfTimesIndex = 0;
		
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
		{ //if there is not at $REQUIREDITEMSONLINE(2) items on the line, something is wrong
			
			skipWhitespace($printLine, $printLineIndex);
			if(getNumber($printLine, $printLineIndex, $lineNumber, $retrievedNumber, $logFile) == false)
			{//invalid amount of minutes was encountered on the line
				echo "getNumber returned false" . "<br>";
				$errorOnLine = true; $errorInFile = true;
			}
			
			if($errorOnLine == false)
			{//valid amount of minutes was encountered on the line
				echo "getNumber returned true" . "<br>";
				if(verifyWhitespace($printLine, $printLineIndex, $lineNumber, $logFile) == false)
				{
					echo "whitespace error 1" . "<br>";
					$errorOnLine = true; $errorInFile = true;
				}
			}
				
			if($errorOnLine == false)
			{//start new query and add $retrievedNumber to it
				skipWhitespace($printLine, $printLineIndex);
				if(getDAYSOFWEEK($printLine, $printLineIndex, $lineNumber, $retrievedDOW, $logFile) == false)
				{
					echo "getDAYSOFWEEK returned false" . "<br>";
					$errorOnLine = true; $errorInFile = true;
				}
			}
			
			if($errorOnLine == false)
			{//add $retrievedDOW to query
				if(getSlash($printLine, $printLineIndex, $lineNumber, $retrievedSlash, $logFile) == false)
				{
					echo "getSlash returned false" . "<br>";
					$errorOnLine = true; $errorInFile = true;
				}
			}
			
			if($errorOnLine == false)
			{//add $retrievedSlash to query
				if(ctype_digit($printLine[$printLineIndex]) == false)
				{
					fputs($logFile, "Error on line $lineNumber at index $printLineIndex.  Time of day should immediately follow '/'" . PHP_EOL);
					$errorOnLine = true; $errorInFile = true;
				}
			}
			
			if($errorOnLine == false)
			{
				while(($printLineIndex < (strlen(trim($printLine)))) and ($errorOnLine == false))
				{//$printLineIndex == strlen(trim($printLine) means we are at the end of the current line				
					if(getTime($printLine, $printLineIndex, $lineNumber, $retrievedTime, $listOfTimes, $listOfTimesIndex, $logFile) == false)
					{
						echo "getTime returned false" . "<br>";
						$errorOnLine = true; $errorInFile = true;
					}
					else
					{
						//add $retrievedTime to query
						if(verifyWhitespace($printLine, $printLineIndex, $lineNumber, $logFile) == false)
						{
							echo "whitespace error between times of day" . "<br>";
							$errorOnLine = true; $errorInFile = true;
						}
						else
						{
							skipWhitespace($printLine, $printLineIndex);
						}
					}
				}
			}	
		}
		else 
		{
			if(strlen(trim($printLine)) != 0)
			{
				fputs($logFile, "Error on line $lineNumber at index $printLineIndex.  Each line in the file must have at least 2 items:
			         			Minutes DAYSOFWEEK_ForwardSlash_MilitaryTimeOfDay" . PHP_EOL);
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
		fputs($logFile, "No errors detected." . PHP_EOL);
	
	
	fclose($readFile);	
	fclose($logFile);
	
	$testNum += 1;
	if($testNum > "30")
		$stillTesting = false;
}
##################################################################################################

/*******************FUNCTIONS******************/

##################################################################################################
	function getTime($line, &$lineIndex, $lineNumber, &$retrievedTime, &$times, &$timesIndex, $logFile)
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
		if(in_array($retrievedTime, $times) == false)
		{
			$times[$timesIndex] = $retrievedTime;
			$timesIndex++;
			return true;
		}
		else
		{
			fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Duplicate time encountered on line." . PHP_EOL);
			return false;
		}
	}
	
##################################################################################################
	function getDAYSOFWEEK($line, &$lineIndex, $lineNumber, &$retrievedDOW, $logFile)
	{/*-----------------------------------------------------------------------------------------------
	********************** Function Prologue Comment: getDAYSOFWEEK ********************
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
	 * Preconditions:  Only called by getDAYSOFWEEK to track order of days of week,
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

	function getNumber($line, &$lineIndex, $lineNumber, &$retrievedNumber, $logFile)
	{/*-----------------------------------------------------------------------------------------------
	 ********************** Function Prologue Comment: getNumber ********************
	 * Preconditions:  Data exists on the line provided by file or user.
	 *
	 * Postconditions:  Valid time was provided
	 *
	 * Function Purpose:  Validates that the first item on a line of data is an
	 *					  integer that specifies the length of class time
	 *
	 * Input Expected:	$line = line of text read in from the test file
	 *					$lineIndex = current index for $line
	 *					$lineNumber = current line of file
	 *					$retrievedNumber = stores number gathered from the line to add to SQL query
	 *					$logFile = text file that errors are logged to
	 *
	 *
	 * Exceptions/Errors Thrown:  No number at start of line
	 *							  Number is less than 1
	 *
	 * Files Accessed:	$logFile - for reporting errors
	 *
	 * Function Pseudocode Author:  Jared Cox
	 *
	 * Function Author:	Jared Cox
	 *
	 * Date of Original Implementation:	April 3, 2013
	 *
	 * Tested by SQA Member (NAME and DATE):  Jared Cox, April 3, 2013
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
			fputs($logFile, "Error on line $lineNumber. Number expected at start of line." . PHP_EOL);
			return false;
		}
		elseif($numOnLine <= 0)
			{
				fputs($logFile, "Error on line $lineNumber.  class cannot be taught for 0 or less minutes." . PHP_EOL);
				return false;
			}
		else
		{
			$retrievedNumber = $numOnLine;
			return true;
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
		if(($line[$lineIndex] != " ") and ($line[$lineIndex] != "\r"))
		{
			fputs($logFile, "Error on line $lineNumber.  Whitespace must separate elements on line." . PHP_EOL);
		}
		else
		{
			return true;
		}
	}

##################################################################################################
?>