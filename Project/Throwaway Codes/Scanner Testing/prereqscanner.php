<html>
<body>

<?php
	
	
	//FLAGS
		$firstCourseOnLineFlag = true;
		$stillTesting = true;
		
	//FILE SETUP
		$testName = "Tests/prereq";
		$testNum = "1";
while($stillTesting == true)
{
		$fileName = $testName . $testNum;
		$log = $fileName . "Log";
		

		$readFile=fopen($fileName . ".txt","r") or die("Unable to open $fileName");
		$logFile = fopen($log . ".log", "w") or die("Unable to open $log");
		
		echo "<br> <br> <br>" . "Test File: $fileName" . "<br> <br> <br>";
		
	
	
	//VARIABLES	
	$lineNumber = 0;
	$printLine = " ";	//line read in from file
	$printLineLength = 255;
	$printLineIndex = 0;	
	$currentCourse = "";		//string		
	$errorOnLine = false;
	$listOfCourses = array();
	$listOfCoursesIndex = 0;
	$itemCount = 0;
	$errorInFile = false;
	$firstLine = true;
	$firstCourseNumber = 0;
	
	
	while(!feof($readFile))
	{
		$itemCount = 0;
		$errorOnLine = false;
		$printLineIndex = 0;
		$firstCourseOnLineFlag = true;
		do
		{$printLine = fgets($readFile);
		 $lineNumber++;
		}while((strlen(trim($printLine)) == 0) and (!feof($readFile)));
		
		$printLine = $printLine . "\r";
		$listOfCourses = array();
		$listOfCoursesIndex = 0;
			
			$readLine = preg_split('/\s+/', $printLine);
			while(($printLineIndex < (strlen(trim($printLine)))) and ($errorOnLine == false))
			{
				echo "length of line is " . strlen($printLine) . "<br>";
				echo "length of trimmed line is " . strlen(trim((string)$printLine)) . "<br>";
				echo "line number $lineNumber and line index $printLineIndex" . "<br>";
				
				
				if((count($readLine)) >= 3 and (count($readLine) <= 5))
				{	
					skipWhitespace($printLine, $printLineIndex); 
					if(getCourse($printLine, $printLineIndex, $lineNumber, $currentCourse, $firstCourseOnLineFlag, $firstCourseNumber, $currentCourseNumber, $logFile) == false)
					{
						echo "getCourse returned false" . "<br>";
						$errorOnLine = true;  $errorInFile = true;
						$itemCount++;
					}
					else
					{
						if(in_array($currentCourse, $listOfCourses) == true)
						{
							fputs($logFile, "Error on line $lineNumber.  Duplicate course found on line." . PHP_EOL);
							$errorOnLine = true;  $errorInFile = true;
						}
						else
						{
							$listOfCourses[$listOfCoursesIndex] = $currentCourse;
							$listOfCoursesIndex++;
						}
						if($firstCourseOnLineFlag == true)
							$firstCourseOnLineFlag = false;
						echo "getCourse returned true" . "<br>";
						$itemCount++;
						/*if($firstCourseOnLineFlag == true)
						{
							if $currentCourse in COURSES database already has defined prerequisites
							  {
								fputs($logFile, "Error on line $lineNumber.  Course prerequisites already defined. All prerequisites for a course belong on the same line." . PHP_EOL);
								errorOnLine = true;
							  }
							  else
							  {
								//add course to $listOfCourses
								//start query "INSERT INTO ... "
								$firstCourseOnLineFlag = false;
							  }
							
						}
						else
						{
							//append to current query
							// $sqlQuery . $currentCourse
						}*/
						if(($printLine[$printLineIndex] != " ") and ($printLine[$printLineIndex] != "\r"))
						{
							
							
								$errorOnLine = true;  $errorInFile = true;
								fputs($logFile, "Error on line $lineNumber at index $printLineIndex.  Whitespace must separate elements on the line." . PHP_EOL);						
							
						}
					}
				}	
				else
				{
					fputs($logFile, "Error on line $lineNumber at index $printLineIndex.  Courses in file must contain between 1 and 3 prerequisites." . PHP_EOL);
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
			
		//}
		$firstLine = false;
	}
	if($errorInFile == false)
		fputs($logFile, "No errors detected." . PHP_EOL);
		
	fclose($readFile);	
	fclose($logFile);
	
	$testNum += 1;
	if($testNum > "45")
		$stillTesting = false;
}
	
	
	/**********FUNCTIONS*********/
	function getCourse($line, &$lineIndex, $lineNumber, &$currentCourse, $firstCourseOnLineFlag, &$firstCourseNumber, &$currentCourseNumber, $logFile)
	{//function description
	
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
		{	
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
			{
				fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Files must contain ONLY uppercase letters." . PHP_EOL);
				return false;
			}
			elseif(ctype_alnum($line[$lineIndex] == false))
			{
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
				{
					$courseNumbers[$courseNumbersIndex] = $line[$lineIndex];
					$lineIndex++;
					$courseNumbersIndex++;
				}
				
				if(count($courseNumbers) != 3)
				{
					fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Course number must be exactly 3 digits." . PHP_EOL);
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
						fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Prerequisite is a higher level course than course requiring prerequisites." . PHP_EOL);
						return false;
					} 
				///////////////////////////////////////////////////////
				
				
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
						$endCourseName[$endCourseNameIndex] = $line[$lineIndex];
						$lineIndex++;
						$endCourseNameIndex++;
					}
					if(count($endCourseName) > 2)
					{
						fputs($logFile, "Error on line $lineNumber at index $lineIndex.  String of characters following course number is too long." . PHP_EOL);
						return false;
					}
					elseif(($line[$lineIndex] !=  " ") and ($line[$lineIndex] != "\r"))
					{
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
	{
		while($line[$lineIndex] == " ")
			$lineIndex++;
	}
?>

</body>
</html>