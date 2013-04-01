<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        // put your code here
        
        $LineNumber=1;
         
        $FacultyArray = array();
		
		
         $InputFile = fopen ("FACULTY18.txt","r") or die ("Unable to open file");
		 
        while(!feof($InputFile))
        {
			$I=0;
            $Line = fgets($InputFile);
			$Line = trim($Line);
			
			printf("Line = $Line <br>");
			
            SkipWhitespace($Line, $I);
			
			$Value1=GetName($Line,$I,$LineNumber);
			SkipWhitespace($Line, $I);
			 
			$Value2=GetYears($Line,$I,$LineNumber);
			SkipWhitespace($Line, $I);
			 
			$Value3=GetEmail($Line,$I,$LineNumber);
			SkipWhitespace($Line, $I);
			 
			$Value4=GetMinHours($Line,$I ,$LineNumber);
             
             
            
            
            //GetInfo($Line,$LineNumber,$FacultyArray,$I);
            $LineNumber= $LineNumber + 1;
          
			printf("<br><br>");
           
        }
        
        //*************************************
        //Functions
        //*************************************
        
        function GetInfo($Line, $LineNumber, $RoomInfo)
        {
            
        }//end function
        
        function SkipWhitespace($Line, &$LineIndex)
		{
			 while(ord($Line[$LineIndex]) == 32 || ord($Line[$LineIndex])== 9)
			 {
				$LineIndex++;
			 }
		}//end function
        
        function IsWhiteSpace ($Line, &$LineIndex, $LineNumber)
        {
            if (ord($Line[$LineIndex])== 32)
            {
                $LineIndex++;
                return true;
            }
            else
            {
                printf("Error on line %d : Exprected White Space.<br>",$LineNumber);
                return false;
            }
        }//end function
        
        function GetName($Line, &$I, $LineNumber)
        {
            $LastName = "";
			$FirstName = "";
  
            while($Line[$I] != ',' && strlen($LastName) <= 25)
            {
                $LastName= $LastName .$Line[$I];
                $I++;
            }
            $I++;
            if (IsWhiteSpace($Line,$I,$LineNumber) == true && strlen($LastName) <= 25)
			{		
				//Do i have to reset index I before the following loop?
				while(ord($Line[$I]) != 32 && strlen($FirstName) <= (25 - strlen($LastName)) )
				{
					$FirstName = $FirstName .$Line[$I];
					$I++;
				}
			}
            
			printf("Name = $LastName, $FirstName <br>");
            //To truncate remaining characters
           // while(ord($Line[$I] !=  32))
             //   $I++;
            return true;
            
        }//end function
        
        function GetYears($Line,&$I,$LineNumber)
        {
            $NumString = "";
            while(ord($Line[$I]) >= 48 && ord($Line[$I]) <= 57) //is numeric
			{
                $NumString= $NumString .$Line[$I];
				$I++;
			}
            
            if(strlen($NumString) < 0)
            {
                printf("Error on line %d, Years Of Service is less than zero.",$LineNumber);
                return false;
            }
            if($NumString > 60)
            {
                printf("Error on line %d, 60 is the maximum number of years of service",$LineNumber);
                return false;
            }
			printf("Years = $NumString <br>");
            return true;
        }//end function
        
        function GetEmail($Line,&$I, $LineNumber)
        {
            $Email= "";
            while($Line[$I] != '@' && strlen($Email)<=10)
            {
                $Email= $Email .$Line[$I];
                $I++;
            }
            if(strlen($Email)> 10)
            {
                printf("Error on line %d, Email Address must be less than 10 characters.",$LineNumber);
                return false;
            }
			
            while(ord($Line[$I]) != 32)
            {
                $Email= $Email .$Line[$I];
                $I++;
            }
			
			printf("Email = $Email <br>");
        }//end function
        
        function GetMinHours ($Line, &$I, $LineNumber)
        {
            $NumString= "";
            while (ord($Line[$I]) >=48 && ord($Line[$I]) <=57)
            {
					$NumString= $NumString .$Line[$I];
					$I++;
            }
            if($NumString < 3)
            {
                printf("Error on line %d, Minimum number of hours to teach is 3.",$LineNumber);
                return false;
            }
            /*I did not think that there was a maximum number of hours that faculty can teach.
            else if ($NumString > 12)
            {
                printf("Error on line %d, maximum number of hours is 12.",$LineNumber);
                return false;
            }*/
			
			printf("Hours = $NumString <br>");
            return true;
			
			
			
        }//end function
        
        function GetOrdinalValue ($word)
          {
             //Varaiable declarations
              $OrdinalSum=0;
              for($I=0; $I < strlen($word);$I++)
                    $OrdinalSum= $OrdinalSum + ord($word[$I]);
              
              return $OrdinalSum;
          }//end function
         

        ?>
    </body>
</html>
