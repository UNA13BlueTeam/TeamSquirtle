<html>
<body>

<?php
	//FLAGS
		$firstCourseOnLineFlag = true;
		
	//FILE SETUP
		$fileName = "prereq025";
		$log = $fileName . "Log";
		
		$readFile=fopen($fileName . ".txt","r") or die("Unable to open $fileName");
		$logFile = fopen($log . ".txt", "w") or die("Unable to open $log");
	
	//VARIABLES	
	$lineNumber = 0;
	$printLine;			//line read in from file
	$printLineIndex = 0;	
	$currentCourse = "";		//string		
	$errorOnLine = false;
	$listOfCourses = array();
	$listOfCoursesIndex = 0;
	$itemCount = 0;
	
	
	
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
		
		if(!feof($readFile))
		{	
			$readLine = preg_split('/\s+/', $printLine);
			while(($itemCount < count($readLine)) and ($errorOnLine == false))
			{
				echo "length of line is " . strlen($printLine) . "<br>";
				echo "line number $lineNumber and line index $printLineIndex" . "<br>";
				
				if((count($readLine)) >= 3 and (count($readLine) <= 5))
				{	
					skipWhitespace($printLine, $printLineIndex); 
					if(getCourse($printLine, $printLineIndex, $lineNumber, $firstCourseOnLineFlag, $currentCourse, $logFile) == false)
					{
						echo "getCourse returned false" . "<br>";
						$errorOnLine = true;
					}
					else
					{
						echo "getCourse returned true" . "<br>";
						$itemCount++;
						/*if($firstCourseOnLineFlag == true)
						{
							if $currentCourse is in $listOfCourses
							  {
								fputs($logFile, "Error on line $lineNumber.  Course previously defined in file." . PHP_EOL);
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
						if($printLine[$printLineIndex] != " ")
						{
							$errorOnLine = true;
							fputs($logFile, "Error on line $lineNumber.  Whitespace must separate elements on the line." . PHP_EOL);						
						}
					}
				}	
				else
				{
					fputs($logFile, "Error on line $lineNumber.  Courses in file must contain between 1 and 3 prerequisites." . PHP_EOL);
					$errorOnLine = true;
				}
				echo "\$printLine[\$printLineIndex] is $printLine[$printLineIndex]" . "<br>";
			}
			
			if($errorOnLine == false)
			{
				//submit query
				echo  "$lineNumber: $printLine" . "<br>";
			}
			else
				echo $lineNumber . ": $printLine*" . "<br>";
			
		}
	}
	fclose($readFile);	
	fclose($logFile);
	
	/**********FUNCTIONS*********/
	function getCourse($line, &$lineIndex, $lineNumber, &$firstCourseOnLineFlag, &$currentCourse, $logFile)
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
				fputs($logFile, "Error on line $lineNumber. Course letters must be between 2 and 4 characters." . PHP_EOL);
				return false;
			}
			elseif(ctype_lower($line[$lineIndex]) == true)
			{
				fputs($logFile, "Error on line $lineNumber.  Files must contain ONLY uppercase letters." . PHP_EOL);
				return false;
			}
			elseif(ctype_alnum($line[$lineIndex] == false))
			{
				fputs($logFile, "Error on line $lineNumber.  Invalid character encountered." . PHP_EOL);
				return false;
			}
			elseif($line[$lineIndex] == " ")
			{
				fputs($logFile, "Error on line $lineNumber.  Course number must immediately follow course letters." . PHP_EOL);
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
					fputs($logFile, "Error on line $lineNumber.  Course number must be exactly 3 digits." . PHP_EOL);
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
				if(($courseNumberInt < 1) or ($courseNumberInt > 499))
				{
					fputs($logFile, "Error on line $lineNumber.  Course number exceeds boundaries. Must be between 001 and 499." . PHP_EOL);
					return false;
				}
				else
				{//COURSE NUMBER IS VALID
					$tempCourse = array_merge($courseLetters, $courseNumbers);
					
					while(ctype_upper($line[$lineIndex]))
					{
						$endCourseName[$endCourseNameIndex] = $line[$lineIndex];
						$lineIndex++;
						$endCourseNameIndex++;
					}
					if(count($endCourseName) > 2)
					{
						fputs($logFile, "Error on line $lineNumber.  String of characters following course number is too long." . PHP_EOL);
						return false;
					}
					else
					{
						$tempCourse = array_merge($tempCourse, $endCourseName);
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