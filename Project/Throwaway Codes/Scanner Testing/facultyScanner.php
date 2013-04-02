<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        // put your code here
        
        $lineNumber=1; 
        $facultyArray = array();
        $inputFile = fopen ("FACULTY18.txt","r") or die ("Unable to open file");
		 
        while(!feof($inputFile))
        {
	    $i=0;
            $line = fgets($inputFile);
            $line = trim($line);		
	    printf("Line = $line <br>");		
            skipWhiteSpace($line, $i);		
	    
            $value1=getName($line,$i,$lineNumber);
	    skipWhiteSpace($line, $i);	 
	    
            $value2=getYears($line,$i,$lineNumber);
	    skipWhiteSpace($line, $i);	 
	    
            $value3=getEmail($line,$i,$lineNumber);
	    skipWhiteSpace($line, $i);			 
	    
            $value4=getMinHours($line,$i ,$lineNumber);
            $lineNumber= $lineNumber + 1;
            printf("<br><br>");
           
        }
        
        //*************************************
        //Functions
        //*************************************
        
        
         /*
         **-----------------------------------------------------------------------------------  
         * Purpose:
         * 
         * Input:
         * 
         * Output:
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
         * Purpose:
         * 
         * Input:
         * 
         * Output:
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
                printf("Error on line %d : Exprected White Space.<br>",$lineNumber);
                return false;
            }
        }//end function
        
         /*
         **-----------------------------------------------------------------------------------  
         * Purpose:
         * 
         * Input:
         * 
         * Output:
         **-----------------------------------------------------------------------------------  
         */
        function getName($line, &$i, $lineNumber)
        {
            $lastName = "";
			$firstName = "";
  
            while($line[$i] != ',' && strlen($lastName) <= 25)
            {
                $lastName= $lastName .$line[$i];
                $i++;
            }
            $i++;
            if (isWhiteSpace($line,$i,$lineNumber) == true && strlen($lastName) <= 25)
			{		
				//Do i have to reset index I before the following loop?
				while(ord($line[$i]) != 32 && strlen($firstName) <= (25 - strlen($lastName)) )
				{
					$firstName = $firstName .$line[$i];
					$i++;
				}
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
            
            if(strlen($numString) < 0)
            {
                printf("Error on line %d, Years Of Service is less than zero.",$lineNumber);
                return false;
            }
            if($numString > 60)
            {
                printf("Error on line %d, 60 is the maximum number of years of service",$lineNumber);
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
         * Output: An error message if the email information is not valid according to the 
         *         specified requirements/guidelines.
         * Return: True if the email address is valid. Otherwise returns false.
         *----------------------------------------------------------------------------------- 
         */
        function getEmail($line,&$i, $lineNumber)
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
            //for($k=0; $k < strlen($line);$k++)
               // printf("k= %d %s--%d <br>",$k,$line[$k],ord($line[$k]));
            //printf("size of line= %d <br>",strlen($line) );
            while (ord($line[$i]) >=48 && ord($line[$i]) <=57)
            {
					$numString= $numString .$line[$i];
					$i++;
                                        //Exit condition
                                        if($i == strlen($line))
                                        {
                                            break;
                                        }
            }
            if($numString < 3)
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
        
         /*
         **----------------------------------------------------------------------------------- 
         * Purpose:
         * 
         * Input:
         * 
         * Output:
         **----------------------------------------------------------------------------------- 
         */
        function getOrdinalValue ($word)
          {
             //Varaiable declarations
              $ordinalSum=0;
              for($i=0; $i < strlen($word);$i++)
                    $ordinalSum= $ordinalSum + ord($word[$i]);
              
              return $ordinalSum;
          }//end function
         

        ?>
    </body>
</html>
