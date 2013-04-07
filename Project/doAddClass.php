<?php include("includes/header.php");
	  
	$link = mysqli_connect($host, $user, $pass, $db, $port);
    if(!$link){
        die('cannot connect database'. mysqli_error($link));
    }
    if($_POST['flag']=="form"){
    	echo("<p>inserting</p>");
		// Get variables from input form
		$courseName = $_POST['course'];
		$dsection = $_POST['dsection'];
		$nsection = $_POST['nsection'];
		$isection = $_POST['isection'];
		$classSize = $_POST['classSize'];
		$roomType = $_POST['roomType'];
		$hours = $_POST['hours'];
		$prereq = $_POST['prereq'];
		$conflict = $_POST['conflict'];
		
		$query = "INSERT INTO courses values (NULL, '$courseName', $dsection, $nsection, $isection, $classSize, '$roomType', $hours);";

		$insertion = mysqli_query($link, $query);
		if($insertion)
			echo("insertion succeeded<br>");
		else{
			echo("insertion failed<br>");
			echo($query."<br>");
		}
		// Print out contents accepted
		// echo "You have successfully added this course information to the database! <br>";
		echo "Course Name: $courseName <br>";
		echo "Day Sections: $dsection <br>";
		echo "Night Sections: $nsection <br>";
		echo "Internet Sections: $isection <br>";
		echo "Class Size: $classSize <br>";
		echo "Room Type: $roomType <br>";
		echo "Hours: $hours <br>";
		echo "Prerequisites: $prereq <br>";
		echo "Conflicts: $conflict <br>";
	}elseif($_POST['flag']=="file"){
		echo ("I got files!<br>");
		// $classFile = $_POST['classFile'];
		// $prereqFile = $_POST['prereqFile'];
		$classFile 		= $_FILES["classFile"]["tmp_name"];
		$classFileName 	= $_FILES["classFile"]["name"];
		$prereqFile 	= $_FILES["prereqFile"]["tmp_name"];
		$prereqFileName = $_FILES["prereqFile"]["name"];
		// $success = move_uploaded_file($prereqFile, "uploads/prereqs.txt");
		// if(!$success)
		// echo("Move failed.<br>");
		// echo($prereqFile);
		scanPrereqs($prereqFile, $prereqFileName);
	}

	mysqli_close($link);
include("includes/footer.php");
?>

<!-- ------------------------************************------------------------- -->
<!-- ------------**************************************************----------- -->
<!-- -------------------------CLASS TIME FILE SCANNER------------------------- -->
<!-- ------------**************************************************----------- -->
<!-- ------------------------************************------------------------- -->
<?php
	/*-----------------------------------------------------------------------------------------------
	********************** Function Prologue Comment: classtimesscanner ********************
	* Preconditions:  None
	*
	* Postconditions:  None
	*
	* Function Purpose:  Validates authenticity of a file containing class times that 
	*					  a course may be scheduled at.
	*
	* Input Expected:	Text input of the following format:
	*						#M DoW/00:00 00:00
	*						Where #M is a positive integer > 0
	*						Where DoW can be any in-order substring of MTWRFS
	*						Where 00:00 can be any valid military time specification
	*						There can be any number of instances of 00:00 provided
	*							there are no duplicates
	*						
	*					  
	* Exceptions/Errors Thrown:  Invalid time format error thrown if the time of day
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
	*							  No number at start of line
	*							  Number is less than 1
	*							  Expected '/' not found
	*							  Whitespace  missing where expected
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
				if(getDaysOfWeek($printLine, $printLineIndex, $lineNumber, $retrievedDOW, $logFile) == false)
				{
					echo "getDaysOfWeek returned false" . "<br>";
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
			         			Minutes DAYSOFWEEK_ForwardSlash_MilitaryTimeOfDay1 MilitaryTimeOfDay2 ... MilitaryTimeOfDayN" . PHP_EOL);
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
			fputs($logFile, "Error on line $lineNumber.  Whitespace must separate elements on line." . PHP_EOL);
		}
		else
		{
			return true;
		}
	}

##################################################################################################
?>
<!-- ------------------------************************------------------------- -->


<!-- ----------------------**************************------------------------- -->
<!-- ------------**************************************************----------- -->
<!-- -----------------------PREREQUISITE FILE SCANNER------------------------- -->
<!-- ------------**************************************************----------- -->
<!-- ----------------------**************************------------------------- -->
<?php
function scanPrereqs($fileName, $prettyName){

	global $link;
	//FLAGS
	$firstCourseOnLineFlag = true;
	$stillTesting = true;
	
	$predef = array();
	$predefQuery = "SELECT DISTINCT course from prereqs";
	$predefResult = mysqli_query($link, $predefQuery);
	while($row = mysqli_fetch_row($predefResult))
	{
		array_push($predef, $row[0]);
	}
	// $predef = mysqli_fetch_all($predefResult, MYSQLI_NUM);
	print_r($predef);
	echo("<hr>");

	$readFile=fopen($fileName,"r") or die("Unable to open $fileName");

	echo("Scanning $prettyName - ".strftime('%c'));
	
	echo "<br> <br> <br>" . "Test File: $fileName" . "<br> <br> <br>";
			
		
		
	//VARIABLES	
	$lineNumber = 0;	//used in listing errors
	$printLine = " ";	//line read in from file
	$printLineIndex = 0;	
	$currentCourse = "";		//string		
	$errorOnLine = false;
	$listOfPrereqs = array();	//used in detecting duplicate courses on a line
	$listOfPrereqsIndex = 0;
	$itemCount = 0;		//if there are more than 4  or less than 2 separate items on a line, there is an incorrect number of prerequisites
	$errorInFile = false;
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
		$listOfPrereqsIndex = 0;
			
		$readLine = preg_split('/\s+/', $printLine);	//splits the line into an array of elements
														//each element will be a contiguous string of characters
														//all whitespace is ignored on line for this function due to " '/\s+/' "
		
		$fieldNum=0;	//used in SQL queries to determine which prereq to insert to
		
		while(($printLineIndex < (strlen(trim($printLine)))) and ($errorOnLine == false))
		{	//$printLineIndex == strlen(trim($printLine) means we are at the end of the current line
		
			echo "length of line is " . strlen($printLine) . "<br>";
			echo "length of trimmed line is " . strlen(trim((string)$printLine)) . "<br>";
			echo "line number $lineNumber and line index $printLineIndex" . "<br>";
			
			if((count($readLine)) >= $REQUIREDITEMSMIN and (count($readLine) <= $REQUIREDITEMSMAX))
			{	//preg_split counts end of line as a nonwhitespace line element, so we check on boundaries
				//of 3 and 5 instead of 2 and 4
				
				skipWhitespace($printLine, $printLineIndex); 
				if(getCourse($printLine, $printLineIndex, $lineNumber, $currentCourse, $firstCourseOnLineFlag, $firstCourseNumber, $currentCourseNumber) == false)
				{//an invalid course format was encountered on the line
					echo "getCourse returned false" . "<br>";
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
						$listOfPrereqs[$listOfPrereqsIndex] = $currentCourse;
						$listOfPrereqsIndex++;
					}
					echo "getCourse returned true" . "<br>";
					if($firstCourseOnLineFlag == true)
					{
						print_r($predef);
						echo("$currentCourse <br>");
						if (in_array(trim($currentCourse), $predef))
						{//if the course already has prereqs defined, we delete the course from
						 //the database and create a new record for the course
							$delete = "DELETE FROM prereqs WHERE course = '$currentCourse'";
							echo("<h1>DELETING</h1><h2>$delete</h2>");
							echo("Error on line $lineNumber.  Course prerequisites already defined. All prerequisites for a course belong on the same line." . PHP_EOL);
							mysqli_query($link, $delete);
						}
						//add course to $listOfPrereqs
						array_push($listOfPrereqs, $currentCourse);
						$insertQuery1= "INSERT INTO prereqs (course";
						$insertQuery2= "VALUES ('$currentCourse'";
						$firstCourseOnLineFlag = false;
						
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
				echo("Error on line $lineNumber at index $printLineIndex.  Courses in file must contain between 1 and 3 prerequisites." . PHP_EOL);
				$errorOnLine = true;  $errorInFile = true;
			}
			$fieldNum++;
		}
			
		if($errorOnLine == false)
		{
			$insertQuery1 = $insertQuery1.") ";
			$insertQuery2 = $insertQuery2.")";
			$insertQuery = $insertQuery1.$insertQuery2;
			$insertion = mysqli_query($link, $insertQuery);
			if($insertion)
 			{
 				echo("insertion succeeded<br>");
 			}
			else
			{
				echo("insertion failed<br>");
				echo($insertQuery."<br>");
			}
			echo  "$lineNumber: $printLine" . "<br>";
		}
		else
		{
			echo $lineNumber . ": $printLine*" . "<br>";
		}
		
	}
	if($errorInFile == false)
		echo("No errors detected." . PHP_EOL);
		
	fclose($readFile);	
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
				
				//SPECIFIC TO PREREQSCANNER. REMOVE IF REUSED ELSEWHERE
					if($firstCourseOnLineFlag == true)
					{
						$firstCourseNumber = $courseNumberInt;
					}
					elseif($courseNumberInt > $firstCourseNumber)
					{
						fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Prerequisite is a higher level course than course requiring prerequisites." . PHP_EOL);
						return false;
					} 
				///////////////////////////////////////////////////////
				
																	
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


