<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
		// Global Constants
		$NUMBEROFFILES = 25;
		$MAXNAMELENGTH = 25;
		
    for ($fileNumber = 1; $fileNumber <= $numberOfFiles; $fileNumber++)
	{
        $fileName = 'FACULTY'.$fileNumber;
		$fileName = $fileName.'.txt';
		
        $inputFile = fopen ($fileName,"r");
		
		printf("<br><br><br><br>File: $fileName <br><br>");
        
		$lineNumber=1; 
        $facultyArray = array();
		 
         
        if($inputFile == NULL)
        {
            echo("Error: Cannot open file.");     
        }
         
        //checks for empty file
        if(filesize($fileName)==0)
        {
             echo("Recognized an empty file.");
        }
        else
        {
			while(!feof($inputFile))
			{
				$i=0;
				$line = fgets($inputFile);
				$line = trim($line);	
				if(strlen($line)!= 0)
				{
					printf("Line = $line <br>");
					$errorFlag = false;
					skipWhiteSpace($line, $i);
					$errorFlag=getName($line,$i,$lineNumber);

					if ($errorFlag == true)
					{
					   skipWhiteSpace($line,$i,$lineNumber);
					   $errorFlag=getYears($line,$i,$lineNumber);

					   if($errorFlag == true)
					   {
						   skipWhiteSpace($line, $i);
						   $errorFlag=getEmail($line,$i,$lineNumber,$facultyArray);

						   if($errorFlag == true)
						   {
								skipWhiteSpace($line, $i);
								$errorFlag=getMinHours($line,$i ,$lineNumber);
						   }                           
					   }          
					}
				}  
				$lineNumber= $lineNumber + 1;
				printf("<br><br>");				
			}//end while loop  
        }//end else
    }  
	//*************************************
	//Functions
	//*************************************

	/*-----------------------------------------------------------------------------------------------
	********************** skipWhiteSpace ********************
	* Preconditions: $index is currently at a white space
	*
	* Postconditions: $index will be on the next non-white space character
	*
	* Function Purpose: To skip all white space until something else is found
	*
	* Input Expected: None
	*
	* Exceptions/Errors Thrown: None
	*
	* Files Acessed: None
	*
	* Function Pseudocode Author: Michael Debs
	*
	* Function Author: Alla Salah
	*
	* Date of Original Implementation:
	*
	* Tested by SQA Member (NAME and DATE):
	* 
	** Modifications by:
	* Modified By (Name and Date):
	* Modifications Description:
	*
	* Modified By (Name and Date):
	* Modifications Description:
	-------------------------------------------------------------------------------------------------*/  
	function skipWhiteSpace($line, &$lineIndex)
	{	 
		if($lineIndex != strlen($line))
		{
			while( ord($line[$lineIndex]) == 32 || ord($line[$lineIndex])== 9 )
			{
				$lineIndex++;
			}
		}
	}//end function
        
	/*-----------------------------------------------------------------------------------------------
	********************** getName ********************
	* Preconditions: Line index $i is currently on the first character of the line
	*
	* Postconditions: The first and last name are retrieved and sent to the database (once connected to it)
	*
	* Function Purpose: To retrieve and validate the name of a faculty member
	*
	* Input Expected: 
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
	* Function Author: Michael Debs, Alla Salah
	*
	* Date of Original Implementation:
	*
	* Tested by SQA Member (NAME and DATE):
	* 
	** Modifications by:
	* Modified By (Name and Date):
	* Modifications Description:
	*
	* Modified By (Name and Date):
	* Modifications Description:
	-------------------------------------------------------------------------------------------------*/ 
	function getName($line, &$i, $lineNumber)
	{
		$lastName = "";
		$firstName = "";
		
		//Gets last name information
		while($line[$i] != ',' && strlen($lastName) <= $MAXNAMELENGTH)
		{
			if ($line[$i] == " ")
			{
				printf("Error on line $lineNumber, Last name should have a comma following it <br>");
				return false;
			}
			else if ( ord($line[$i])>= 97 && ord($line[$i] <= 122))
			{
				printf("Error on line $lineNumber, only upper-case characters allowed <br>");
				return false;
			}
			$lastName= $lastName .$line[$i];
			$i++;
		}
		
		if(strlen($lastName)==0)
		{
			//Invalid data
			printf("Error on line %d, missing last name <br>",$lineNumber);
			return false;
		}
		$i++;
		
		//Gets first name information
		if (ord($line[$i]) == 32 || ord($line[$i]) == 9)
		{		
			$i++;
			while(ord($line[$i]) != 32 && ord($line[$i])!= 9 && strlen($firstName) <= ($MAXNAMELENGTH - strlen($lastName)) )
			{
				if( ( ord($line[$i])>= 97 && ord($line[$i] <= 122)))
				{
					printf("Error on line %d, only upper-case characters allowed <br>",$lineNumber);
					return false;
				}
				$firstName = $firstName .$line[$i];
				$i++;
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
		
		//To truncate remaining characters
		while(ord($line[$i]) !=  32 && ord($line[$i]) && strlen($firstName.$lastName) >= $MAXNAMELENGTH) 
		{
			$i++;
		}
		return true;
		
	}//end function
        
	/*-----------------------------------------------------------------------------------------------
	********************** getYears ********************
	* Preconditions:
	*
	* Postconditions:
	*
	* Function Purpose: To retrieve and validate the number of years a faculty member has been employed
	*
	* Input Expected:
	*
	* Exceptions/Errors Thrown:
	*
	* Files Acessed:
	*
	* Function Pseudocode Author:
	*
	* Function Author:
	*
	* Date of Original Implementation:
	*
	* Tested by SQA Member (NAME and DATE):
	* 
	** Modifications by:
	* Modified By (Name and Date):
	* Modifications Description:
	*
	* Modified By (Name and Date):
	* Modifications Description:
	-------------------------------------------------------------------------------------------------*/ 
	function getYears($line,&$i,$lineNumber)
	{
		$years = "";
		
		while(ord($line[$i]) >= 48 && ord($line[$i]) <= 57) //is numeric
		{
			$years= $years .$line[$i];
			$i++;
		}            
		if(intval($years) < 0 || intval($years) > 60 || strlen($years) == 0)
		{
			printf("Error on line %d, Years Of Service must be in the range of [0 to 60]. <br>",$lineNumber);
			return false;
		}
		// Blank must follow before email address
		else if (ord($line[$i]) != 32 && ord($line[$i]) != 9)
		{
			printf("Error on line %d, invalid years of service <br>", $lineNumber);
			return false;
		}
	   
		printf("Years = $years <br>");
		return true;
	}//end function
        
	/*-----------------------------------------------------------------------------------------------
	********************** getEmail ********************
	* Preconditions: $index is on the first element of the email
	*
	* Postconditions: Email is retrieved and sent to the database (once connected to it)
	*
	* Function Purpose: To retrieve and validate the email address of a faculty member
	*
	* Input Expected:
	*
	* Exceptions/Errors Thrown:
	*
	* Files Acessed:
	*
	* Function Pseudocode Author:
	*
	* Function Author:
	*
	* Date of Original Implementation:
	*
	* Tested by SQA Member (NAME and DATE):
	* 
	** Modifications by:
	* Modified By (Name and Date):
	* Modifications Description:
	*
	* Modified By (Name and Date):
	* Modifications Description:
	-------------------------------------------------------------------------------------------------*/ 
	function getEmail($line,&$i, $lineNumber,&$facultyArray)
	{
		$email= "";
		
		//Gets the email address except for the extension
		while($line[$i] != '@' && strlen($email)<=10)
		{
			$email= $email .$line[$i];
			$i++;
			if($i == strlen($line) )
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
		while(ord($line[$i]) != 32 && ord($line[$i]) != 9 )
		{
			$email= $email .$line[$i];
			$i++;
			if($i == strlen($line) )
			{
				break;
			}
		}
		
		printf("Email = $email <br>");
		return true;
	}//end function
        
	/*-----------------------------------------------------------------------------------------------
	********************** getMinHours ********************
	* Preconditions:
	*
	* Postconditions:
	*
	* Function Purpose: To retrieve and validate the minimum number of hours 
						a faculty member must teach
	*
	* Input Expected:
	*
	* Exceptions/Errors Thrown:
	*
	* Files Acessed:
	*
	* Function Pseudocode Author:
	*
	* Function Author:
	*
	* Date of Original Implementation:
	*
	* Tested by SQA Member (NAME and DATE):
	* 
	** Modifications by:
	* Modified By (Name and Date):
	* Modifications Description:
	*
	* Modified By (Name and Date):
	* Modifications Description:
	-------------------------------------------------------------------------------------------------*/ 
	function getMinHours ($line, &$i, $lineNumber)
	{
		$minHours= "";
		$invalidMinHours=false;
		$endOfBuffer=false;
		
		if($i != strlen($line))
		{
			while (ord($line[$i]) >=48 && ord($line[$i]) <=57)
			{
				$minHours= $minHours .$line[$i];
				$i++;
				//Exit condition
				if($i == strlen($line))
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
			if(ord($line[$i] <48 || ord($line[$i]>57)))
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
      

        ?>
    </body>
</html>
