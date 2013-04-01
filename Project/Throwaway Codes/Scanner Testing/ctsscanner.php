<html>
<body>

<?php

	//FLAGS
		$stillTesting = true;
		$startQuery = true;
		$sectionsFlag = 1;		//these three flags have integer values to distinguish between the
		$classSizeFlag = 2;		//types of flags passed to the "getNumber" function
		$hoursFlag = 3;
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
	$errorOnLine = false;
	$errorInFile = false;
	$requiredItemsOnLine = 7;	//there must be exactly 7 items on a line, otherwise there is an error
	
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
		
	
			
		$readLine = preg_split('/\s+/', trim($printLine));	//splits the line into an array of elements
														//each element will be a contiguous string of characters
														//all whitespace is ignored on line for this function due to " '/\s+/' "
		
		while(($printLineIndex < (strlen(trim($printLine)))) and ($errorOnLine == false))
		{	//$printLineIndex == strlen(trim($printLine) means we are at the end of the current line
		
			echo "length of line is " . strlen($printLine) . "<br>";
			echo "length of trimmed line is " . strlen(trim((string)$printLine)) . "<br>";
			echo "line number $lineNumber and line index $printLineIndex" . "<br>";
			
			
			if((count($readLine)) == $requiredItemsOnLine)
			{	//if there is not $requiredItemsOnLine (7) items on the line, something is missing
				
				skipWhitespace($printLine, $printLineIndex); 
				if(getCourse($printLine, $printLineIndex, $lineNumber, $currentCourse, $logFile) == false)
				{//an invalid course format was encountered on the line
					echo "getCourse returned false" . "<br>";
					$errorOnLine = true;  $errorInFile = true;
				}
				else
				{//valid course was encountered
				
					/*if $currentCourse already established in COURSES database
					  {
						fputs($logFile, "Error on line $lineNumber.  Course already exists in database." . PHP_EOL);
						$errorOnLine = true; $errorInFile = true;
					  }
					  else
					  {
						//add course to $listOfCourses
						//start query "INSERT INTO ... "
						*/
						if(verifyWhiteSpace($printLine, $printLineIndex, $lineNumber, $logFile) == false)
						{   echo "whitespace error 1" . "<br>";
							echo $printLine[$printLineIndex] . "<br>";
							$errorOnLine = true; $errorInFile = true;
						}
						else
						{
							skipWhitespace($printLine, $printLineIndex);
							if(getNumber($printLine, $printLineIndex, $lineNumber, $sectionsFlag, $retrievedNumber, $logFile) == false)
							{//invalid day sections count was encountered
								echo "day sections false" . "<br>";
								$errorOnLine = true; $errorInFile = true;
							}
							else
							{//valid day sections count was encountered
								echo "day sections true" . "<br>";
								if(verifyWhitespace($printLine, $printLineIndex, $lineNumber, $lineNumber, $logFile) == false)
								{	echo "whitespace error 2" . "<br>";
									$errorOnLine = true; $errorInFile = true;
								}
								else
								{
									//add $retrievedNumber to query
									skipWhitespace($printLine, $printLineIndex);
									if(getNumber($printLine, $printLineIndex, $lineNumber, $sectionsFlag, $retrievedNumber, $logFile) == false)
									{//invalid night sections count was encountered
										echo "night sections false"."<br>";
										$errorOnLine = true; $errorInFile = true;
									}
									else
									{//valid night sections count was encounterd
										echo "night sections true"."<br>";
										if(verifyWhitespace($printLine, $printLineIndex, $lineNumber, $logFile) == false)
										{	echo "whitspace error 3" . "<br>";
											$errorOnLine = true; $errorInFile = true;
										}
										else
										{
											//add $retrievedNumber to query
											skipWhitespace($printLine, $printLineIndex);
											if(getNumber($printLine, $printLineIndex, $lineNumber, $sectionsFlag, $retrievedNumber, $logFile) == false)
											{//invalid internet sections count was encountered
												echo "internet sections false" . "<br>";
												$errorOnLine = true; $errorInFile = true;
											}
											else
											{//valid internet sections count was encountered
												echo "internet sections true" . "<br>";
												if(verifyWhitespace($printLine, $printLineIndex, $lineNumber, $logFile) == false)
												{	echo "whitespace error 4" . "<br>";
													$errorOnLine = true; $errorInFile = true;
												}
												else
												{	
													//add $retrievedNumber to query
													skipWhitespace($printLine, $printLineIndex);
													if(getNumber($printLine, $printLineIndex, $lineNumber, $classSizeFlag, $retrievedNumber, $logFile) == false)
													{//invalid class size count encountered
														echo "class size false" . "<br>";
														$errorOnLine = true; $errorInFile = true;
													}
													else
													{//valid class size count encountered
														echo "class size true" . "<br>";
														if(verifyWhitespace($printLine, $printLineIndex, $lineNumber, $logFile) == false)
														{	echo "whitespace error 5" . "<br>";
															$errorOnLine = true; $errorInFile = true;
														}	
														else
														{	//add $retrievedNumber to query
															skipWhitespace($printLine, $printLineIndex);
															if(getChar($printLine, $printLineIndex, $retrievedChar, $logFile) == false)
															{//character was not C or L
																echo "getChar false" . "<br>";
																$errorOnLine = true; $errorInFile = true;
															}
															else
															{//character was C or L
																echo "getChar true" . "<br>";
																if(verifyWhitespace($printLine, $printLineIndex, $lineNumber, $logFile) == false)
																{	echo "whitespace error 6" . "<br>";
																	$errorOnLine = true; $errorInFile = true;
																}
																else
																{	//add $retrievedChar to query
																	
																	skipWhitespace($printLine, $printLineIndex);
																	if(getNumber($printLine, $printLineIndex, $lineNumber, $hoursFlag, $retrievedNumber, $logFile) == false)
																	{//invalid hours count encountered
																		echo "hours false" . "<br>";
																		$errorOnLine = true; $errorInFile = true;
																	}
																	else
																	{//valid hours count encountered
																		echo "hours true" . "<br>";
																		//add $retrievedNumber to query
																		//submit query
																	}
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
			    	#}
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
	{/*************************************************************************************
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
	 |	Output:
	 |		Returns true of the course encountered is valid and acceptable.
	 |		Returns false otherwise.
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
	{/*************************************************************************************
	 |	Function Name:  getNumber
	 |	Input Parameters:
	 |  	$line = line of text read in from the test file
	 |		$lineIndex = current index for $line
	 |		$flag = an integer representing one of three possible values:
	 |			1 - day/night/internet sections count
	 |			2 - class size count
	 |			3 - hours count
	 |		$retrievedNumber = number gathered from the line to add to SQL query
	 |		$logFile = text file that errors are logged to
	 |
	 |	Output:
	 |		Modified $lineIndex, and a number ($retrievedNumber) to add to SQL query
	 |					
	 |
	  ************************************************************************************/
		
	//VARIABLES
		$numString = array();
		$numStringIndex = 0;
		$numOnLine = 0;
		$classSizeMin = 1;
		$classSizeMax = 200;
		$hoursMin = 1;
		$hoursMax = 12;
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
						if(($numOnLine < $classSizeMin) or ($numOnLine > $classSizeMax))
						{
							fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Class size must be between $classSizeMin and $classSizeMax." . PHP_EOL);
							return false;
						}
						else
						{
							$retrievedNumber = $numOnLine;
							return true;
						}
				case 3: //hours
						if(($numOnLine < $hoursMin) or ($numOnLine > $hoursMax))
						{
							fputs($logFile, "Error on line $lineNumber at index $lineIndex.  Number of hours must be between $hoursMin and $hoursMax." . PHP_EOL);
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
	
	function getChar($line, &$lineIndex, &$retrievedChar, $logFile)
	{/*************************************************************************************
	 |	Function Name:  getNumber
	 |	Input Parameters:
	 |  	$line = line of text read in from the test file
	 |		$lineIndex = current index for $line
	 |		$retrievedChar = character gathered from the line to add to SQL query
	 |		$logFile = text file that errors are logged to
	 |
	 |	Output:
	 |		Modified $lineIndex, and a characdter ($retrievedChar) to add to SQL query
	 |					
	 |
	  ************************************************************************************/
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
			default :  fputs($logFile, "Illegal character encountered.  Room type must be 'C' or 'L'." . PHP_EOL);
					   $lineIndex++;
					  return false;
		}
	}
##################################################################################################

	function skipWhitespace($line, &$lineIndex)
	{/*************************************************************************************
	 |	Function Name:  skipWhitespace
	 |	Input Parameters:
	 |  	$line = line of text read in from the test file
	 |		$lineIndex = current index for $line
	 |
	 |	Output:
	 |		Modified $lineIndex
	 |					
	 |
	  ************************************************************************************/
		while($line[$lineIndex] == " ")
		{
			$lineIndex++;
		}
	}
	
##################################################################################################

	function verifyWhitespace($line, $lineIndex, $lineNumber, $logFile)
	{/*************************************************************************************
	 |	Function Name:  verifyWhitespace
	 |	Input Parameters:
	 |  	$line = line of text read in from the test file
	 |		$lineIndex = current index for $line
	 |		$logFile = text file that errors are logged to
	 |
	 |	Output:
	 |		Returns true if the current position of $line is whitespace
	 |		Returns false otherwise
	 |					
	 |
	  ************************************************************************************/
		if($line[$lineIndex] != " ")
		{
			fputs($logFile, "Error on line $lineNumber.  Whitespace must separate elements on line." . PHP_EOL);
		}
		else
		{
			return true;
		}
	}

##################################################################################################