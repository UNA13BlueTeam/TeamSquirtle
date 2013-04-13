<?php include("includes/header.php");
	  
	$link = mysqli_connect($host, $user, $pass, $db, $port);
    if(!$link)
	{
        die('cannot connect database'. mysqli_error($link));
    }
	// Form submission
    if($_POST['flag']=="form")
	{
    	echo("<p>inserting</p>");
		// Get variables from input form
		$name = $_POST['name'];
		$yos = $_POST['yos'];
		$email = $_POST['email'];
		$minHours = $_POST['hours'];
		
		$query = "INSERT INTO $db.faculty VALUES ('$name', '$yos', '$email', '$minHours');";

		$insertion = mysqli_query($link, $query);
		if($insertion)
			echo("insertion succeeded<br>");
		else{
			echo("insertion failed<br>");
			echo($query."<br>");
		}
		// Print out contents accepted
		// echo "You have successfully added this course information to the database! <br>";
		echo "Faculty Name: $name <br>";
		echo "Years of Service: $yos <br>";
		echo "Email: $email <br>";
		echo "Minimum Hours: $minHours <br>";
		
	}
	
	// File Submission
	elseif($_POST['flag']=="file")
	{
		echo ("I got files!<br>");
		
		$facultyFile = $_FILES["facultyFile"]["tmp_name"];
		$facultyFileName = $_FILES["facultyFile"]["name"];
		
		
		if($facultyFile)
		{
			scanFaculty($facultyFile, $facultyFileName);
		}
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
	 * Tested by SQA Member (NAME and DATE): 
	 * 
	 ** Modifications by:
	 * Modified By (Name and Date):	Michael Debs April 12, 2013
	 * Modifications Description: Integrated scanner to sync with database
	 *
	 * Modified By (Name and Date):
	 * Modifications Description:
	 -------------------------------------------------------------------------------------------------*/

		echo("<h1>SCANNING FACULTY</h1><br>");
		
		// get global variables used
		global $link, $db;
		
		$readFile=fopen($fileName,"r") or die("Unable to open $fileName");

		echo("Scanning $prettyName - ".strftime('%c'));
		
		echo "<br> <br> <br>" . "Test File: $fileName" . "<br> <br> <br>";
		
		$lineNumber = 0; 
		
		while(!feof($readFile))
		{
			$facultyName = "";
			$yos = "";
			$email = "";
			$minHours = "";	
		
			$lineIndex = 0;
			$line = fgets($readFile);
			$line = trim($line);	
			$errorFlag = false;
			$lineNumber++;
			
			
			if(strlen($line)!= 0)
			{
				echo "Line = $line <br>";
				$errorFlag = false;
				skipWhiteSpace($line, $lineIndex);
				$errorFlag = getName($line, $lineIndex, $lineNumber, $facultyName);

				if ($errorFlag == true)
				{
				   skipWhiteSpace($line, $lineIndex, $lineNumber);
				   $errorFlag = getYears($line, $lineIndex, $lineNumber, $yos);

				   if($errorFlag == true)
				   {
					   skipWhiteSpace($line, $lineIndex);
					   $errorFlag = getEmail($line, $lineIndex, $lineNumber, $email);

					   if($errorFlag == true)
					   {
							skipWhiteSpace($line, $lineIndex);
							$errorFlag = getMinHours($line, $lineIndex ,$lineNumber, $minHours);
					   }                           
				   }          
				}
			}  
			
			if($errorFlag == true)
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
					echo("<h2>Faculty member already defined in database on line $lineNumber.  Attempting to overwrite... </h2><br>");
					$delete = "DELETE FROM $db.faculty WHERE email = '$email'";
					echo("<h1>DELETING</h1><h2>$delete</h2>");
					
					mysqli_query($link, $delete);
				}
				// submit to query
				$insertQuery= "INSERT INTO $db.faculty (facultyName, yos, email, minHours) VALUES ('$facultyName', '$yos', '$email', '$minHours')";
				echo "$insertQuery";
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
			}
			
			printf("<br><br>");				
		}//end while loop  
	}
	
	
	
	
	
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
	* Tested by SQA Member (NAME and DATE):
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
			else if ( ord($line[$lineIndex])>= 97 && ord($line[$lineIndex] <= 122))
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
		
		printf("Name = $lastName, $firstName <br>");
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
	* Tested by SQA Member (NAME and DATE):
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
	   
		printf("Years = $years <br>");
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
	*
	* Files Acessed: None
	*
	* Function Pseudocode Author: Michael Debs
	*
	* Function Author: Michael Debs
	*
	* Date of Original Implementation: April 5, 2013
	*
	* Tested by SQA Member (NAME and DATE):
	* 
	** Modifications by:
	* Modified By (Name and Date): Michael Debs April 12, 2013
	* Modifications Description: Integrated to work with application
	*
	* Modified By (Name and Date):
	* Modifications Description:
	-------------------------------------------------------------------------------------------------*/ 
	function getEmail($line, &$lineIndex, $lineNumber, &$email)
	{	
		//Gets the email address except for the extension
		while($line[$lineIndex] != '@' && strlen($email)<=10)
		{
			$email = $email.$line[$lineIndex];
			$lineIndex++;
			if($lineIndex == strlen($line) )
			{
				printf("Error on line %d, missing email extension. <br>",$lineNumber);
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
			$email= $email.$line[$lineIndex];
			$lineIndex++;
			if($lineIndex == strlen($line) )
			{
				break;
			}
		}
		
		printf("Email = $email <br>");
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
	* Tested by SQA Member (NAME and DATE):
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
		
		printf("Hours = $minHours <br>");
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