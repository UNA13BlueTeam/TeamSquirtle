<?php include("includes/header.php");
	  
	$link = mysqli_connect($host, $user, $pass, $db, $port);
	$error = false;
    if(!$link)
	{
        die('cannot connect database'. mysqli_error($link));
    }
	// Form submission
    if($_POST['flag']=="form")
	{
    	echo("<p>inserting</p>");
		// Get variables from input form
		$name = strtoupper($_POST['name']);
		$yos = $_POST['yos'];
		$email = strtoupper($_POST['email']);
		$minHours = $_POST['hours'];
		
		$outForm = "$name $yos $email $minHours";
		
		$outFile = fopen("formSubmissionFile.txt", "w");
		$outFileName = "formSubmissionFile.txt";
		fwrite($outFile, $outForm);
		fclose($outFile);
		
		$error = scanFaculty($outFileName, $outFile);
		
	}
	
	// File Submission
	elseif($_POST['flag']=="file")
	{
		
		$facultyFile = $_FILES["facultyFile"]["tmp_name"];
		$facultyFileName = $_FILES["facultyFile"]["name"];
		
		
		if($facultyFile)
		{
			$error = scanFaculty($facultyFile, $facultyFileName);
		}
	}
	
	if($error == false)
	{
		header("Location: addFaculty.php");
	}
	
	mysqli_close($link);
include("includes/footer.php");
?>



<!-- --------------------------****************------------------------------- -->
<!-- ------------**************************************************----------- -->
<!-- --------------------------FACULTY FILE SCANNER------------------------------ -->
<!-- ------------**************************************************----------- -->
<!-- --------------------------****************------------------------------- -->
<?php 
	function scanFaculty($fileName, $prettyName)
	{/*-----------------------------------------------------------------------------------------------
	 ********************** Function Prologue Comment: ctsscanner ********************
	 * Preconditions:  None
	 *
	 * Postconditions: None
	 *
	 * Function Purpose:  Validates the authenticity of a file containing faculty member information
	 *					  by analyzing the contents of each line in a file.
	 *
	 * Input Expected:  Text input of the following format:
	 *						
	 *
	 * Exceptions/Errors Thrown: Insertion failed
	 *
	 * Files Accessed:  Any file given to the program
	 *
	 * Function Pseudocode Author:  Michael Debs
	 *
	 * Function Author:  Michael Debs
	 *
	 * Date of Original Implementation: April 4, 2013
	 *
	 * Tested by SQA Member (NAME and DATE): Alla Salah 4-21-2013
	 * 
	 ** Modifications by:
	 * Modified By (Name and Date):	Michael Debs April 12, 2013
	 * Modifications Description: Integrated scanner to sync with database
	 *
	 * Modified By (Name and Date):
	 * Modifications Description: 
	 -------------------------------------------------------------------------------------------------*/

		
		
		// get global variables used
		global $link, $db;
		
		$readFile=fopen($fileName,"r") or die("Unable to open $fileName");
		echo("<h1>SCANNING FACULTY</h1><br>");
		echo("<h3>Checking $prettyName for errors...</h3>");
		
		
		$lineNumber = 0; 
		
		$errorInFile = false;
		while(!feof($readFile))
		{
			$facultyName = "";
			$yos = "";
			$email = "";
			$minHours = "";	
			
			$lineIndex = 0;
			$line = fgets($readFile);
			$line = trim($line);	
			$errorFlag = true;
			
			
			$lineNumber++;
				
				
			if(strlen($line)!= 0)
			{
				$errorFlag = false;
				skipWhiteSpace($line, $lineIndex);
				$errorFlag = getName($line, $lineIndex, $lineNumber, $facultyName);
				if($errorFlag == false)
				{
					$errorInFile = true;
				}

				if ($errorFlag == true)
				{
					  skipWhiteSpace($line, $lineIndex, $lineNumber);
					  $errorFlag = getYears($line, $lineIndex, $lineNumber, $yos);
					  if($errorFlag == false)
					  {
						$errorInFile = true;
					  }
					  if($errorFlag == true)
					  {
						  skipWhiteSpace($line, $lineIndex);
						  $errorFlag = getEmail($line, $lineIndex, $lineNumber, $email);
						  if($errorFlag == false)
						  {
						    $errorInFile = true;
						  }
						  if($errorFlag == true)
						  {
							skipWhiteSpace($line, $lineIndex);
							$errorFlag = getMinHours($line, $lineIndex ,$lineNumber, $minHours);
							if($errorFlag == false)
							{
								$errorInFile = true;
							}
						  }                           
					  }          
				}
			}  
				
			if(($errorFlag == true) && (strlen($line) != 0))
			{
				// If faculty already exist, then delete before submitting
					
				$predef = array();
				$predefQuery = "SELECT DISTINCT email FROM faculty";
				$predefResult = mysqli_query($link, $predefQuery);
				while($row = mysqli_fetch_row($predefResult))
				{
					array_push($predef, $row[0]);
				}
					
				if(in_array(trim($email), $predef))
				{
					$delete = "DELETE FROM $db.faculty WHERE email = '$email'";
					$delete2 = "DELETE FROM $db.users WHERE username = '$email'";
						
					// Delete from faculty and users table
					mysqli_query($link, $delete);
					mysqli_query($link, $delete2);
				}
				// submit to faculty table
				$insertQuery = "INSERT INTO $db.faculty (facultyName, yos, email, minHours) VALUES ('$facultyName', '$yos', '$email', '$minHours')";
				$insertion = mysqli_query($link, $insertQuery);
				if($insertion)
				{
					//echo("File uploaded successfully!<br>");
				}
				else
				{
					echo("<p class=\"warning\">There was a problem uploading the file, please try again. <br> If the problem persists, please contact your system administrator.</p>");
				}
				// submit to users table
					
				$temp = preg_split('/[,]/', $facultyName);
				$last = $temp[0];
				$first = trim($temp[1]);
					
					
				$first = ucfirst(strtolower($first));
				$last = ucfirst(strtolower($last));
				$email = strtolower($email);
					
				//$password = crypt('password');
				$password = 'master1!';
				$insertQuery = "INSERT INTO $db.users (username, permission, password, firstName, lastName) VALUES ('$email', '2', '$password', '$first', '$last')";
				$insertion = mysqli_query($link, $insertQuery);
					
				if($insertion)
				{
					//echo("File uploaded successfully!<br>");
					
				}
				else
				{
					echo("<p class=\"warning\">There was a problem uploading the file, please try again. <br> If the problem persists, please contact your system administrator.</p>");
				}
				echo $lineNumber . ": $line" . "<br>";
			}
			else
			{
				if(strlen($line) != 0)
				{
					echo $lineNumber . ": $line*" . "<br>";
					echo("<p class=\"error\"> Error discovered on line $lineNumber. Attempting to continue uploading file.</p>");
				}
			}						
		}//end while loop  
		return $errorInFile;
	}//end function
	
	/*-----------------------------------------------------------------------------------------------
	********************** getName ********************
	* Preconditions: Line index $lineIndex is currently on the first character of the line
	*
	* Postconditions: The first and last name are retrieved and sent to the database (once connected to it)
	*
	* Function Purpose: To retrieve and validate the name of a faculty member
	*
	* Input Expected: None
	*
	* Exceptions/Errors Thrown:
	*	Missing commma between first and last name
	*	Only uppercase characters allowed'
	*	Missing last name
	* 	Blank must follow a comma
	*
	* Files Acessed: None
	*
	* Function Pseudocode Author: Michael Debs
	*
	* Function Author: Michael Debs
	*
	* Date of Original Implementation: April 5, 2013
	*
	* Tested by SQA Member (NAME and DATE): Alla Salah 4-21-2013
	* 
	** Modifications by:
	* Modified By (Name and Date): Michael Debs April 12, 2013
	* Modifications Description: Integrated to work with application
	*
	* Modified By (Name and Date):
	* Modifications Description:
	-------------------------------------------------------------------------------------------------*/ 
	function getName($line, &$lineIndex, $lineNumber, &$facultyName)
	{
		$MAXNAMELENGTH = 25;
		$lastName = "";
		$firstName = "";
		
		//Gets last name information
		while($line[$lineIndex] != ',' && strlen($lastName) <= $MAXNAMELENGTH)
		{
			if ($line[$lineIndex] == " ")
			{
				printf("Error on line $lineNumber, Last name should have a comma following it <br>");
				return false;
			}
			else if ( ord($line[$lineIndex])>= 97 && ord($line[$lineIndex] <= 122)) //checking for lower-case letters
			{
				printf("Error on line $lineNumber, only upper-case characters allowed <br>");
				return false;
			}
			$lastName = $lastName.$line[$lineIndex];
			$lineIndex++;
		}
		
		if(strlen($lastName)==0)
		{
			//Invalid data
			printf("Error on line %d, missing last name <br>",$lineNumber);
			return false;
		}
		$lineIndex++;
		
		//Gets first name information
		if (ord($line[$lineIndex]) == 32 || ord($line[$lineIndex]) == 9)
		{		
			$lineIndex++;
			while(ord($line[$lineIndex]) != 32 && ord($line[$lineIndex])!= 9 && strlen($firstName) <= ($MAXNAMELENGTH - strlen($lastName)) )
			{
				if( ( ord($line[$lineIndex])>= 97 && ord($line[$lineIndex] <= 122)))
				{
					printf("Error on line %d, only upper-case characters allowed <br>",$lineNumber);
					return false;
				}
				$firstName = $firstName .$line[$lineIndex];
				$lineIndex++;
			}
		}
		else
		{
			printf("Error on line %d, a blank must follow a comma <br>",$lineNumber);
			return false;
		}
		if(strlen($lastName) == 0)
		{
			//Invalid data
			printf("Error on line %d, missing last name <br>", $lineNumber);
			return false;
		}
		
		$facultyName = $lastName.", ".$firstName;
		
		//To truncate remaining characters
		while(ord($line[$lineIndex]) !=  32 && ord($line[$lineIndex]) && strlen($firstName.$lastName) >= $MAXNAMELENGTH) 
		{
			$lineIndex++;
		}
		return true;
		
	}//end function
        
	/*-----------------------------------------------------------------------------------------------
	********************** getYears ********************
	* Preconditions: $lineIndex is positioned at the first number of the years after skipping whitespace
	*
	* Postconditions: $lineIndex will be positioned at the first whitespace character after 
	* 					getting the number of years
	*
	* Function Purpose: To retrieve and validate the number of years a faculty member has been employed
	*
	* Input Expected: None
	*
	* Exceptions/Errors Thrown: Years Of Service must be in the range of [0 to 60].
	*							invalid years of service
	*
	* Files Acessed: None
	*
	* Function Pseudocode Author: Michael Debs
	*
	* Function Author: Michael Debs
	*
	* Date of Original Implementation: April 5, 2013
	*
	* Tested by SQA Member (NAME and DATE): Alla Salah 4-21-2013
	* 
	** Modifications by:
	* Modified By (Name and Date): Michael Debs April 12, 2013
	* Modifications Description: Integrated to work with application
	*
	* Modified By (Name and Date):
	* Modifications Description:
	-------------------------------------------------------------------------------------------------*/ 
	function getYears($line, &$lineIndex, $lineNumber, &$yos)
	{
		$years = "";
		
		while(ord($line[$lineIndex]) >= 48 && ord($line[$lineIndex]) <= 57) //is numeric
		{
			$years= $years .$line[$lineIndex];
			$lineIndex++;
		}            
		if(intval($years) < 0 || intval($years) > 60 || strlen($years) == 0)
		{
			printf("Error on line %d, Years Of Service must be in the range of [0 to 60]. <br>",$lineNumber);
			return false;
		}
		// Blank must follow before email address
		else if (ord($line[$lineIndex]) != 32 && ord($line[$lineIndex]) != 9)
		{
			printf("Error on line %d, invalid years of service <br>", $lineNumber);
			return false;
		}
	   
		$yos = $years;
		return true;
	}//end function
        
	/*-----------------------------------------------------------------------------------------------
	********************** getEmail ********************
	* Preconditions: $lineIndex is on the first element of the email
	*
	* Postconditions: $lineIndex will be on the first whitespace character after 
	* 					getting the email
	*
	* Function Purpose: To retrieve and validate the email address of a faculty member
	*
	* Input Expected: None
	*
	* Exceptions/Errors Thrown: Missing email extension.
	*							Email Address must between 3 and 10 characters in length.
	*							Email address cannot include any lower-case characters.
	*							Email extension cannot include any lower-case characters.
	*
	* Files Acessed: None
	*
	* Function Pseudocode Author: Michael Debs and Alla Salah
	*
	* Function Author: Michael Debs
	*
	* Date of Original Implementation: April 5, 2013
	*
	* Tested by SQA Member (NAME and DATE): Alla Salah 4-22-2013
	* 
	** Modifications by:
	* Modified By (Name and Date): Michael Debs April 12, 2013
	* Modifications Description: Integrated to work with application
	*
	* Modified By (Name and Date): Alla Salah 4-22-2013
	* Modifications Description: Added code to check if email address and extension contains any lower-
	*							case characters.
	-------------------------------------------------------------------------------------------------*/ 
	function getEmail($line, &$lineIndex, $lineNumber, &$email)
	{	
		$ext = "";

		
		//Gets the email address except for the extension
		while($line[$lineIndex] != '@' && strlen($email) <= 10)
		{
			$email = $email.$line[$lineIndex];
			$lineIndex++;
			if($lineIndex == strlen($line))
			{
				printf("Error on line %d, missing email extension. <br>",$lineNumber);
				return false;
			}
			else if (ord($line[$lineIndex])>= 97 && ord($line[$lineIndex] <= 122)) //checking for lower-case letters
			{
				printf("Error on line $lineNumber, only upper-case characters allowed <br>");
				return false;
			}
		}//end while
		
		if(strlen($email)> 10 || strlen($email) < 3)
		{
			printf("Error on line %d, Email Address must between 3 and 10 characters in length. <br>",$lineNumber);
			return false;
		}
		
		//Gets the email extension; for example, @UNA.EDU
		while(ord($line[$lineIndex]) != 32 && ord($line[$lineIndex]) != 9 )
		{
			$ext= $ext.$line[$lineIndex];
			$lineIndex++;
			if($lineIndex == strlen($line) )
			{
				break;
			}
			else if (ord($line[$lineIndex])>= 97 && ord($line[$lineIndex] <= 122)) //checking for lower-case letters
			{
				printf("Error on line $lineNumber, only upper-case characters allowed <br>");
				return false;
			}
		}
		
		return true;
	}//end function
        
	/*-----------------------------------------------------------------------------------------------
	********************** getMinHours ********************
	* Preconditions: $lineIndex is on the first element of the minimum hours
	*
	* Postconditions: $lineIndex will be on the first whitespace character after 
	* 					getting the email
	*
	* Function Purpose: To retrieve and validate the minimum number of hours 
						a faculty member must teach
	*
	* Input Expected: None
	*
	* Exceptions/Errors Thrown:
	*
	* Files Acessed: None
	*
	* Function Pseudocode Author: Michael Debs
	*
	* Function Author: Michael Debs
	*
	* Date of Original Implementation: April 5, 2013
	*
	* Tested by SQA Member (NAME and DATE): Alla Salah 4-21-2013
	* 
	** Modifications by:
	* Modified By (Name and Date): Michael Debs April 12, 2013
	* Modifications Description: Integrated to work with application
	*
	* Modified By (Name and Date):
	* Modifications Description:
	-------------------------------------------------------------------------------------------------*/ 
	function getMinHours ($line, &$lineIndex, $lineNumber, &$minHours)
	{
		$invalidMinHours=false;
		$endOfBuffer=false;
		
		if($lineIndex != strlen($line))
		{
			while (ord($line[$lineIndex]) >=48 && ord($line[$lineIndex]) <=57)
			{
				$minHours= $minHours .$line[$lineIndex];
				$lineIndex++;
				//Exit condition
				if($lineIndex == strlen($line))
				{
					$endOfBuffer=true;
					break;
				}
			}
		}
		else
		{
			printf("Error on line %d, invalid minimum faulty hours.  <br>",$lineNumber);
			return false;
		}
		
		if($endOfBuffer==false)
		{
			//To detect any invalid data in file
			if(ord($line[$lineIndex] <48 || ord($line[$lineIndex]>57)))
			{
				//Then found invalid symbol
				printf("Error on line %d, invalid minimum faulty hours.  <br>",$lineNumber);
				return false;
			}
		}
		
		if(intval($minHours) < 3)
		{
			printf("Error on line %d, Minimum number of hours to teach is 3. <br>",$lineNumber);
			return false;
		}
		
		return true;

	}//end function
	
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