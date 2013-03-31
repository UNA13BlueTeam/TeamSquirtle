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
         $I=0;
        $FacultyArray = array();
         $InputFile = fopen ("FACULTY18.txt","r");
         if($InputFile == NULL)
         {
             echo("Error: Cannot open file.");     
         }
        //while(!feof($InputFile))
        //{
           
            $Line = fgets($InputFile);
            printf("Contents of line %d: %s <br>",$LineNumber,$Line);
            //printf("BEFORE I= %d <br>",$I);
            SkipWhitespace($Line, $I);
            //printf("AFTER I= %d <br>",$I);
            GetName($Line,$I,$LineNumber);
            
            
            //GetInfo($Line,$LineNumber,$FacultyArray,$I);
            $LineNumber= $LineNumber + 1;
            $I= $I + 1;
          
           
        //}
        
        //*************************************
        //Functions
        //*************************************
        
        function GetInfo($Line, $LineNumber, $RoomInfo)
        {
            
        }//end function
        
        function SkipWhitespace($Line, &$LineIndex)
	{
	     while(ord($Line[$LineIndex]) == 32 ||ord($Line[$LineIndex])== 9)
		$LineIndex++;
	}//end function
        function IsWhiteSpace ($Line,&$LineIndex,$LineNumber)
        {
            if (ord($Line[$LineIndex])== 32 || ord($Line[$LineIndex]))
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
        
        function GetName($Line, $Index,$LineNumber)
        {
            $I=0;
            $LastName='';
            $FirstName='';
            
            while($Line[$I] != ',' && strlen($LastName) <= 25)
            {
                $LastName= $LastName .$Line[$I];
                $I++;
            }
            $I++;
            $Result= IsWhiteSpace($Line,$Index,$LineNumber);
            
            //Do i have to reset index I before the following loop?
            while(ord($Line[$I]) != 32 && strlen($FirstName) <= (25-  strlen($LastName)) )
            {
                $FirstName = $FirstName .$Line[$I];
                $I++;
            }
            
            //To truncate remaining characters
            while(ord($Line[$I] !=  32))
                $I++;
            return true;
            
        }//end function
        
        function GetYears($Line,$LineNumber)
        {
            $NumString = '';
            $I=0;
            while(ord($Line[$I]) >= 48 && ord($Line[$I])<=57) //is numeric
                $NumString= $NumString .$Line[$I];
            
            if(strlen($NumString)<0)
            {
                printf("Error on line %d, Years Of Service is less than zero.",$LineNumber);
                return false;
            }
            $Actualalue= GetOrdinalValue($NumString);
            if($Actualalue > 102 )//'60'=102
            {
                printf("Error on line %d, 60 is the maximum number of years of service",$LineNumber);
                return false;
            }
            return true;
        }//end function
        
        function GetEmail($Line, $LineNumber)
        {
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
            //Do I have to reset I ?
            while(ord($Line[$I]) != 32)
            {
                $Email= $Email .$Line[$I];
                $I++;
            }
        }//end function
        
        function GetMinHours ($Line, $LineNumber)
        {
            while (ord($Line[$I]) >=48 && ord($Line[$I]) <=57)
            {
                    $NumString= $NumString .$Line[$I];
                    $I++;
            }      
            $Value= GetOrdinalValue($NumString);
            if($Value < 3)
            {
                printf("Error on line %d, Minimum number of hours to teach is 3.",$LineNumber);
                return false;
            }
            //I did not think that there was a maximum number of hours that faculty can teach.
            else if ($Value > 12)
            {
                printf("Error on line %d, maximum number of hours is 12. ");
                return false;
            }
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
