<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        //Things to fix: Multiple errors, run FACULTY3.txt
        //                FACULTY4.txt 
        
        $lineNumber=1; 
        $facultyArray = array();
		 
         $fileName = 'FACULTY4.txt';
         $inputFile = fopen ($fileName,"r");
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
	    printf("Line = $line <br>");		
            skipWhiteSpace($line, $i);		
	    
            $value1=getName($line,$i,$lineNumber);
	    skipWhiteSpace($line, $i);	 
            //Need to check if $value1 is true before proceeding because if false we need to skip the rest.
            
            $value2=getYears($line,$i,$lineNumber);
	    skipWhiteSpace($line, $i);	 
	    //Need to check if $value2 is true before proceeding because if false we need to skip the rest.
            $value3=getEmail($line,$i,$lineNumber,$facultyArray);
	    skipWhiteSpace($line, $i);			 
	    //Need to check for duplicate emails either within the getEmail function call or after it.
            
            $value4=getMinHours($line,$i ,$lineNumber);
            $lineNumber= $lineNumber + 1;
            printf("<br><br>");
              
          }//end while loop  
        }//end else
        
        //*************************************
        //Functions
        //*************************************
   
         /*
         **-----------------------------------------------------------------------------------  
         * Purpose: Skips any white spaces. Updates the index so that it contains the first 
         *          non-blank index in the input line string.
         * Input:   The input line string that was read from the input file
         *          The current index position called $lineIndex.
         * Output: Nothing
         * Returns: Nothing
         **-----------------------------------------------------------------------------------  
         */
        function skipWhiteSpace($line, &$lineIndex)
        {
	      while(ord($line[$lineIndex]) == 32 || ord($line[$lineIndex])== 9)
	      {
		  $lineIndex++;
	      }
	}//end function
        
         /*
         **-----------------------------------------------------------------------------------  
         * Purpose: checks if there is a blank at the specified index that is passed in.
         * 
         * Input: The input line read from file called $line
         *        The index in the array to start reading from called $i
         *        The line number in the input file for error messages called $line number
         * 
         * Output:
         * Return: True if the index specified contains a blank space. Otherwise returns false. 
         **-----------------------------------------------------------------------------------  
         */        
        function isWhiteSpace ($line, &$lineIndex, $lineNumber)
        {
            if (ord($line[$lineIndex])== 32)
            {
                $lineIndex++;
                return true;
            }
            else
            {
                printf("Error on line %d : Expected White Space.<br>",$lineNumber);
                return false;
            }
        }//end function
        
         /*
         **-----------------------------------------------------------------------------------  
         * Purpose: Parses the input line to get the faculty first and last name. 
         * 
         * Input:The input line read from file called $line
         *        The index in the array to start reading from called $i
         *        The line number in the input file for error messages called $line number
         * 
         * Output: Error message is first or last name are invalid.
         * Return: True if both the first and last name conform to faculty name requirements. 
          *         False is returned if either one is invalid.
         **-----------------------------------------------------------------------------------  
         */
        function getName($line, &$i, $lineNumber)
        {
            $lastName = "";
	    $firstName = "";
            
            //Gets last name information
            while($line[$i] != ',' && strlen($lastName) <= 25)
            {
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
            if (isWhiteSpace($line,$i,$lineNumber) == true && strlen($lastName) <= 25)
	    {		
		
		while(ord($line[$i]) != 32 && ord($line[$i])!= 9 && strlen($firstName) <= (25 - strlen($lastName)) )
		{
		      $firstName = $firstName .$line[$i];
		      $i++;
		}
	   }
           if(strlen($lastName)==0)
           {
               //Invalid data
               printf("Error on line %d, missing last name <br>", $lineNumber);
               return false;
           }
	   printf("Name = $lastName, $firstName <br>");
            //To truncate remaining characters
           // while(ord($line[$i] !=  32))
             //   $i++;
            return true;
            
        }//end function
        
         /*
         **----------------------------------------------------------------------------------- 
         * Purpose: Parses the input line to get the years of service.
         * 
         * Input: The input line read from file called $line
         *        The index in the array to start reading from called $i
         *        The line number in the input file for error messages called $line number
         * 
         * Output: Error message if invalid data is found or if years of service does not meet
         *         the specified guidelines.
         * Return : True if the years of service data is correct. Returns false if not. 
         **-----------------------------------------------------------------------------------  
         */
        function getYears($line,&$i,$lineNumber)
        {
            $numString = "";
            while(ord($line[$i]) >= 48 && ord($line[$i]) <= 57) //is numeric
			{
                                $numString= $numString .$line[$i];
				$i++;
			}
            
            if(intval($numString) < 0 || intval($numString) > 60)
            {
                printf("Error on line %d, Years Of Service must be in the range of [0 to 60].",$lineNumber);
                return false;
            }
           
	    printf("Years = $numString <br>");
            return true;
        }//end function
        
         /*
         *----------------------------------------------------------------------------------- 
         * Purpose: Parses the line string to get and validate email address
         *          of the faculty member. If email address is valid it is inserted into the 
         *          faculty array to check for duplicates.
         * Input: The input line read from file called $line
         *        The index in the array to start reading from called $i
         *        The line number in the input file for error messages called $line number
         *        The array called $facultyArray that holds all valid email addresses already 
         *          identified.  
         * Output: An error message if the email information is not valid according to the 
         *         specified requirements/guidelines.
         * Return: True if the email address is valid. Otherwise returns false.
         *----------------------------------------------------------------------------------- 
         */
        function getEmail($line,&$i, $lineNumber,&$facultyArray)
        {
            //for($k=0; $k < strlen($line);$k++)
               // printf("k= %d %s--%d <br>",$k,$line[$k],ord($line[$k]));
            //printf("size of line= %d <br>",strlen($line) );
            $email= "";
            
            //Gets the email address except for the extension
            while($line[$i] != '@' && strlen($email)<=10)
            {
                $email= $email .$line[$i];
                $i++;
            }
            if(strlen($email)> 10)
            {
                printf("Error on line %d, Email Address must be less than 10 characters.",$lineNumber);
                return false;
            }
			
            //Gets the email extension; for example, @UNA.EDU
            while(ord($line[$i]) != 32 && ord($line[$i]) != 9)
            {
                $email= $email .$line[$i];
                $i++;
            }
			
	    printf("Email = $email <br>");
            return true;
        }//end function
        
         /*
         **----------------------------------------------------------------------------------- 
         * Purpose: Parses the line string to get the minimum hours teaching.
         * 
         * Input: The input line read from file called $line
         *        The index in the array to start reading from called $i
         *        The line number in the input file for error messages called $line number
         * 
         * Output: Error message if the minimum hours is invalid. 
         * Return: True if the minimum hours teaching is within the specified guidelines. Returns
         *         false if the hours is incorrect.
         **----------------------------------------------------------------------------------- 
         */
        function getMinHours ($line, &$i, $lineNumber)
        {
            $numString= "";
            $invalidMinHours=false;
            $endOfBuffer=false;
            //for($k=0; $k < strlen($line);$k++)
                //printf("k= %d %s--%d <br>",$k,$line[$k],ord($line[$k]));
            //printf("size of line= %d <br>",strlen($line) );
            while (ord($line[$i]) >=48 && ord($line[$i]) <=57)
            {
		$numString= $numString .$line[$i];
		$i++;
                //Exit condition
                if($i == strlen($line))
                {
                      $endOfBuffer=true;
                      break;
                }
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
            if(intval($numString) < 3)
            {
                printf("Error on line %d, Minimum number of hours to teach is 3.",$lineNumber);
                return false;
            }
            /*I did not think that there was a maximum number of hours that faculty can teach.
            else if ($numString > 12)
            {
                printf("Error on line %d, maximum number of hours is 12.",$lineNumber);
                return false;
            }*/
			
	    printf("Hours = $numString <br>");
            return true;
	
        }//end function
      

        ?>
    </body>
</html>
