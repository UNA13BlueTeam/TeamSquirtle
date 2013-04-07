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
		$prereqFile = $_FILES["prereqFile"]["tmp_name"];
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
	$listOfCourses = array();	//used in detecting duplicate courses on a line
	$listOfCoursesIndex = 0;
	$itemCount = 0;		//if there are more than 4  or less than 2 separate items on a line, there is an incorrect number of prerequisites
	$errorInFile = false;
	$firstCourseNumber = 0;	//used in checking if a prerequisiste course is higher than the course requiring prerequisites
	
	
	while(!feof($readFile))
	{
		$itemCount = 0;
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
		
		$listOfCourses = array();		
		$listOfCoursesIndex = 0;
			
		$readLine = preg_split('/\s+/', $printLine);	//splits the line into an array of elements
														//each element will be a contiguous string of characters
														//all whitespace is ignored on line for this function due to " '/\s+/' "
		
		$fieldNum=0;
		while(($printLineIndex < (strlen(trim($printLine)))) and ($errorOnLine == false))
		{	//$printLineIndex == strlen(trim($printLine) means we are at the end of the current line
		
			echo "length of line is " . strlen($printLine) . "<br>";
			echo "length of trimmed line is " . strlen(trim((string)$printLine)) . "<br>";
			echo "line number $lineNumber and line index $printLineIndex" . "<br>";
			
			if((count($readLine)) >= 3 and (count($readLine) <= 5))
			{	//preg_split counts end of line as a nonwhitespace line element, so we check on boundaries
				//of 3 and 5 instead of 2 and 4
				
				skipWhitespace($printLine, $printLineIndex); 
				if(getCourse($printLine, $printLineIndex, $lineNumber, $currentCourse, $firstCourseOnLineFlag, $firstCourseNumber, $currentCourseNumber) == false)
				{//an invalid course format was encountered on the line
					echo "getCourse returned false" . "<br>";
					$errorOnLine = true;  $errorInFile = true;
					$itemCount++;
				}
				else
				{//a valid course format was encountered on the line
					if(in_array($currentCourse, $listOfCourses) == true)
					{
						echo("Error on line $lineNumber.  Duplicate course found on line." . PHP_EOL);
						$errorOnLine = true;  $errorInFile = true;
					}
					else
					{
						$listOfCourses[$listOfCoursesIndex] = $currentCourse;
						$listOfCoursesIndex++;
					}
					// if($firstCourseOnLineFlag == true)
					// 	$firstCourseOnLineFlag = false;
					echo "getCourse returned true" . "<br>";
					$itemCount++;
					if($firstCourseOnLineFlag == true)
					{
						print_r($predef);
						echo("$currentCourse <br>");
						if (in_array(trim($currentCourse), $predef))
						{
							$delete = "DELETE FROM prereqs WHERE course = '$currentCourse'";
							echo("<h1>DELETING</h1><h2>$delete</h2>");
							echo("Error on line $lineNumber.  Course prerequisites already defined. All prerequisites for a course belong on the same line." . PHP_EOL);
							// $errorOnLine = true;
							mysqli_query($link, $delete);
						}
						//add course to $listOfCourses
						array_push($listOfCourses, $currentCourse);
						$insertQuery1= "INSERT INTO prereqs (course";
						$insertQuery2= "VALUES ('$currentCourse'";
						$firstCourseOnLineFlag = false;
						
					}
					else
					{
						$insertQuery1 = $insertQuery1.", prereq".$fieldNum;
						$insertQuery2 = $insertQuery2.", '$currentCourse'";
					}
					if(($printLine[$printLineIndex] != " ") and ($printLine[$printLineIndex] != "\r"))
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
			echo $lineNumber . ": $printLine*" . "<br>";
		
	}
	if($errorInFile == false)
		echo("No errors detected." . PHP_EOL);
		
	fclose($readFile);	
}

function getCourse($line, &$lineIndex, $lineNumber, &$currentCourse, $firstCourseOnLineFlag, &$firstCourseNumber, &$currentCourseNumber){
	/*************************************************************************************
	|	Function Name:  getCourse
	|	Input Parameters:
	|  	$line = line of text read in from the test file
	|		$lineIndex = current index for $line
	|		$lineNumber = current line number for test file
	|		$currentCourse = will hold a string containing the course built
	|						  from the test file (if course is valid)
	|		$firstCourseOnLineFlag = flag denoting whether or not the course being
	|								 inspected is the first course on $line
	|		$firstCourseNumber = will hold an integer containing the course number of
	|							 the first course on the line.  It is used to determine
	|							 if a prerequisite on the line is higher than the first
	|							 course on the line
	|		$currentCourseNumber = will hold an integer to be compared against $firstCourseNumber
	|		$logFile = text file that errors are logged to
	|					
	|
	 ************************************************************************************/
	
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
		
		if(count($courseNumbers) != 3)
		{
			echo("Error on line $lineNumber at index $lineIndex.  Course number must be exactly 3 digits." . PHP_EOL);
			return false;
		}
		for($i=0; $i<count($courseNumbers); $i++)
		{	
			switch($i)
			{
				case 0: $courseNumberInt += ($courseNumbers[$i] * 100);
						break;
				case 1: $courseNumberInt += ($courseNumbers[$i] * 10);
						break;
				case 2: $courseNumberInt += ($courseNumbers[$i]);
						break;
			}
		}
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
		///////////////////////////////////////////////////////
		
		
		if(($courseNumberInt < 1) or ($courseNumberInt > 499))
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
			if(count($endCourseName) > 2)
			{
				echo("Error on line $lineNumber at index $lineIndex.  String of characters following course number is too long." . PHP_EOL);
				return false;
			}
			elseif(($line[$lineIndex] !=  " ") and ($line[$lineIndex] != "\r"))
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
{
	while($line[$lineIndex] == " ")
		$lineIndex++;
}

?>
