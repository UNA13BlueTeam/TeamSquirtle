<?php include("includes/header.php");
	  
	// Get variables from input form
	$flag = $_POST['flag'];

    $link = mysqli_connect ($host, $user, $pass, $db, $port);
	$error = false;
	
    if(!$link)
	{
        die('cannot connect database'. mysqli_error($link));
    }
    //mysqli_query ("INSERT INTO tablename (roomType, size, roomName )
    //VALUES (\'$type\',\'$size\', \'$name\')
    //");

	if($flag==="form")
	{	
		$type = $_POST['roomType'];
		$size = $_POST['size'];
		$name = $_POST['roomName'];
		
		$outForm = "$type $size $name";
		
		$outFile = fopen("formSubmissionFile.txt", "w");
		$outFileName = "formSubmissionFile.txt";
		fwrite($outFile, $outForm);
		fclose($outFile);
		
		$error = scanRooms($outFileName, $outFile);
		
		
	}
	elseif($flag==="file")
	{
		$roomFile = $_FILES["roomFile"]["tmp_name"];
		$roomFileName = $_FILES["roomFile"]["name"];
		$error = scanRooms($roomFile, $roomFileName);
	}
	
	if($error == false)
	{
		header("Location: addRoom.php");
	}
	mysqli_close($link);
	include("includes/footer.php");
?>

<?php	
    function scanRooms($fileName, $prettyName){
        
        $roomInfo = array ();
        $lineNumber=1;
        $i=0;
         
        $inputFile = fopen ($fileName,"r");
        if($inputFile == NULL)
        {
            echo("Error: Cannot open file.");     
        }
        
		echo("<h1>SCANNING ROOMS</h1><br>");
		echo("<h3>Checking $prettyName for errors...</h3>");
		
        //checks for empty file
        if(filesize($fileName)==0)
        {
            echo("Recognized an empty file.");
        }
        else
        {
	        while(!feof($inputFile))
	        {
	        	$line = fgets($inputFile);
	        	if (feof($inputFile))
	        	{
	        	    //Insert the carriage and new line charater if at the last line in file
	        	    $line= $line . chr(13);
	        	    $line= $line . chr(10);
	        	}           
	        	if(strlen($line)!= 2)
	        	{
	        	    getInfo($line,$lineNumber,$roomInfo,$i); //process line information to determine if it is in valid form.
	        	   $lineNumber= $lineNumber + 1;
	        	}
	        }//end while loop  
        }//end else
    }
    //************************************************************************************************
    // FUNCTIONS DEFINITIONS
    //************************************************************************************************
   
	 /*-----------------------------------------------------------------------------------------------
	 ********************** Function Prologue Comment: getInfo ********************
	 * Preconditions: Read data from file is in string variable called $line. 
	 *                The next available index to insert into the array $roomArray is in the variable $i.
	 * Postconditions: If the room name was previously identified on a prior input line in the file, then
	 *                 the array $roomArray will contain the name of the room and it will not be inserted
	 *                 in the array again. If it is not in the array it will be inserted into the array.
	 * Function Purpose: Parses the input line read and if the input data 
	 *                   conforms to standards it stores the identified room
	 *                   name and number into an array if it does not already
	 *                   exist. If at any time the input does not conform to the 
	 *                   expected standards, then the appropriate error is printed.
	 * Input Expected: A buffer with input data called $line.
	 *                 The line number used for logging errors.
	 *                 An array with the already identified room names called
	 *                 $roomArray.
	 *                 The next available index in the roomArray to insert into 
	 *                 called $i.
	 * Exceptions/Errors Thrown: Missing room type, or room type must be an upper-case character.
	 *                           Missing room size, or room size must be greater than 1 and less than or
	 *                           equal to 200. 
	 *                           Missing room name or, the room name must be six upper-case characters
	 *                           A-Z in length.
	 *                           Room number must all be digits and followed by nothing. 
	 * Files Accessed: None
	 *
	 * Function Pseudocode Author: Alla Salah
	 *
	 * Function Author: Alla Salah  
	 *
	 * Date of Original Implementation: 3-30-2013
	 *
	 * Tested by SQA Member (NAME and DATE): Alla Salah 3-30-2013 and 4-4-2013
	 * 
	 * Tested by SQA Member (NAME and DATE): Alla Salah 4-25-2013
	 ** Modifications by:
	 * Modified By (Name and Date): Alla Salah 4-25-2013
	 * Modifications Description: Scanner was considering anything following the room number as a valid
	 *								delimiter. Changed it so that the only valid delimiters allowed after
	 *								room number is a sapce, tab, carriage-return, new-line symbol. Regre-
	 *								ssion testing on same day.
	 * Modified By (Name and Date):
	 * Modifications Description:
	 -------------------------------------------------------------------------------------------------*/

    function  getInfo ($line,$lineNumber,&$roomArray,&$i)
    {
        //varaiable declarations and initialization
        $roomSize='';
        $roomName='';
        $roomNumber='';
        //$i=0;//used to keep track of where to insert into the array roomArray
        //boolean variables
        $invalidRoomType= false;
        $invalidRoomSize=false;
        $invalidRoomName=false;
        $invalidRoomNumber=false;   
        $invalidSpace=false;
		$errorInFile = false;
        $gotRoomType=false;
        $gotRoomSize=false;
        $gotRoomName=false;
        $gotRoomNumber=false;
    
        //Loop to get room type, size, name, and number
        for ($index=0; $index < strlen($line);$index= $index + 1)
        {
           //Loop until while $line[#index] is not a blank space '32' or a horizontal tab '9' 
           if((ord($line[$index]) != 32) &&  (ord($line[$index]) != 9))
           {    
              
              //Gets room type information
              if ($gotRoomType == false)
              {
                 if ( ($line[$index]!= 'L') && ($line[$index]!= 'C')) 
                 {
                    $invalidRoomType = true;
					$errorInFile = true;
                    break;
                 }
                 else
                 {
                     $roomType = $line[$index];
                     $gotRoomType=true;
                     $result = isWhiteSpace($index + 1 ,$line,$lineNumber);
                        if ($result == false)
                        {
                            $invalidSpace=true;
							$errorInFile = true;
                            break;
                        }
                 }
              }
              //Gets room size information
              else if (($invalidSpace == false) && ($gotRoomSize == false) && ($gotRoomType == true))
              {
				//Checking to see if portion of $line pertaining to room size includes anything besides numeric digits.
                if((ord($line[$index]) < 48) || (ord($line[$index]) > 57) ) // digit 0= '48' and 9 = '57'
                {
                    $invalidRoomSize= true;
					$errorInFile = true;
                    break;
                }
                else
                {
                    $roomSize= $roomSize .$line[$index];
                  
                    //Check if the next character in the string line is a digit
                    if ((ord($line[$index+1]) < 48) || (ord($line[$index+1]) > 57))// digit 0= '48' and 9 = '57'   
                    {
                        $gotRoomSize=true;						
                        $result = isWhiteSpace($index+1,$line,$lineNumber);
                        if ($result == false)
                        {
                            $invalidSpace=true;
							$errorInFile = true;
                            break;
                        }
                    }

                    if ((intval($roomSize)< 1) || (intval($roomSize) >200))//may need to change the 49 to 48 if room size can be 002, 001,...,etc
                    {
                        $invalidRoomSize=true;  
						$errorInFile = true;						
                        break;
                    }                       
                }
              }//end else if
              
              //Gets room name information
              else if (($invalidSpace == false) && ($gotRoomName == false) && ($gotRoomType==true) && ($gotRoomSize==true))       
              {
				  //Checking to see if the portion of $line pertaining to the room name included anything besides characters A to Z
                  if ((ord($line[$index]) < 65) || (ord($line[$index]) > 90)) //'A'= 65 and 'Z'=90
                  {                   
                      $invalidRoomName=true; 
					  $errorInFile = true;
                      break;
                  }
                  else
                  {
                      $roomName= $roomName .$line[$index];
                      
                      if (strlen($roomName)> 6) //checking to see if room name length is greater than six
                      {
                          $invalidRoomName= true;
						  $errorInFile = true;
                          break;
                      }
                      else if (strlen($roomName)== 6) //if room name length is already six characters check $line to see if more valid charcters follows
                      {
                          if ((ord($line[$index+1]) >= 65) && (ord($line[$index+1]) <= 90)) //'A'= 65 and 'Z'=90
                          {
							  //Found a valid room name but its length is greater than six characters. Essentially it becomes invalid.
                              $invalidRoomName=true;
							  $errorInFile = true;
                          }
                          else 
                          {
                              $gotRoomName= true;
                              $result = isWhiteSpace($index+1,$line,$lineNumber);
                              if ($result == false) //checking for valid space delimiter following room name.
                              {
                                 $invalidSpace=true;
								 $errorInFile = true;
                                 break;
                              }
                              
                          }
                          
                      }//end else if
                                
                   }//end else
             }//end else if
             
             //Gets room number information
             else if (($invalidSpace == false) && ($gotRoomNumber==false) && ($gotRoomType==true) && ($gotRoomSize==true) && ($gotRoomName==true))
             {
				 //checking to see if the portion of $line contains anything besides numeric digits.
				 if ((ord($line[$index]) < 48) || (ord($line[$index])>57)) // digit 0= '48' and 9 = '57' 
                 {
					
                     $invalidRoomNumber=true;
					 $errorInFile = true;
                     break;               
                 }
                 else
                 {
                     $roomNumber = $roomNumber.$line[$index];
					 //checking if the next item in line is a valid delimiter: sapce, tab, newline, or carriage return
					 //if it is any of the above, then we have reached the end of the line information data.
					 $temp = ord($line[$index+1]);
					 
                     if (  $temp == 9|| $temp == 10 || $temp == 13|| $temp == 32) // tab=9, carriage return=13 space=32 and newline= 10
                     {
                            $gotRoomNumber=true;         //reached the end of line.                                             
                     }   
					 else if ( $temp >= 48 and $temp <=57) // 0='48' and 9='57'
					 {
							continue;
					 }
					 else //we have an invalid item following room number.
					 {
							$invalidRoomNumber=true;
							$errorInFile=true;
							break;
					 }
                 }
             }//end else if
                  
         }//end if( (int)$line[$index] != 32 || (int)$line[$index] != 9)
           
        }//end for loop
       if(($invalidRoomType== false) && ($invalidRoomSize==false) && ($invalidRoomName==false) && ($invalidRoomNumber==false) && ($invalidSpace==false) )
        {
            //echo($roomType. " " .$roomSize." " .$roomName." " .$roomNumber. " input accepted.");
        }
      
        if ($invalidRoomType==true)
        {
              printf("Error on line %d:Missing room type, or room type must be an upper-case character 'C' or 'L' <br>",$lineNumber);
			  echo $lineNumber . ": $line*" . "<br>";
			  echo("<p class=\"error\"> Error discovered on line $lineNumber. Attempting to continue uploading file.</p>");
        }
        else if ($invalidRoomSize==true)
        {
              printf("Error on line %d:Missing room size, or room size must be greater than 1 and less than or equal to 200<br>",$lineNumber);
			  echo $lineNumber . ": $line*" . "<br>";
			  echo("<p class=\"error\"> Error discovered on line $lineNumber. Attempting to continue uploading file.</p>");
        }
        else if ($invalidRoomName==true)
        {
              printf("Error on line %d: Missing room name or, the room name must be six upper-case characters A-Z in length.<br>",$lineNumber);
			  echo $lineNumber . ": $line*" . "<br>";
			  echo("<p class=\"error\"> Error discovered on line $lineNumber. Attempting to continue uploading file.</p>");
        }
        else if ($invalidRoomNumber==true)
        {
              printf ("Error on line %d: Room number must all be digits and followed by nothing.<br>",$lineNumber);
			  echo $lineNumber . ": $line*" . "<br>";
			  echo("<p class=\"error\"> Error discovered on line $lineNumber. Attempting to continue uploading file.</p>");
        }
		else if ($invalidSpace == false)
		{
			$completeRoomName = $roomName." ".$roomNumber;
        	global $link, $db;
			
			$predef = array();
			$predefQuery = "SELECT DISTINCT roomName FROM rooms";
			$predefResult = mysqli_query($link, $predefQuery);
			while($row = mysqli_fetch_row($predefResult))
			{
				array_push($predef, $row[0]);
			}
			
			if(in_array(trim($completeRoomName), $predef))
			{
				$delete = "DELETE FROM $db.rooms WHERE roomName = '$completeRoomName'";
				
				mysqli_query($link, $delete);
			}
			
        	$insertQuery = "INSERT INTO $db.rooms (roomType, size, roomName) VALUES ('$roomType', '$roomSize', '$completeRoomName')";
        	$success = mysqli_query($link, $insertQuery);
        	if($success)
			{
        		//echo("<h3>File uploaded successfully!</h3>");
			}
        	else
        	{
        		echo("<p class=\"warning\">There was a problem uploading the file, please try again. <br> If the problem persists, please contact your system administrator.</p>");
        	}
			echo $lineNumber . ":$line" . "<br>";
        }
    }//end function    
      
	         
	     
	 /*---------------------------------------------------------------------------------------------------
	 ********************** Function Prologue Comment: isWhiteSpace ********************
	 * Preconditions: The variable $index contains an index value that is within the bounds of the string.
	 *
	 * Postconditions: Returns true if the specified index in the string is either a blank or tab. Returns
	 *                 false if not.
	 * Function Purpose: To check if the string variable $line at $index is either a space or tab.
	 *
	 * Input Expected: A variable called $index that hold an integer value.
	 *                 A variable called $line is checked to see if it contains a space or tab
	 *                 at the specified index value.
	 *                 The line number from the where the data was read from the input file.
	 * Exceptions/Errors Thrown: Expecting a white space or tab after %s on line.
	 * Files Accessed: None
	 *
	 * Function Pseudocode Author: Alla Salah
	 *
	 * Function Author: Alla Salah
	 *
	 * Date of Original Implementation: 3-30-2013
	 *
	 * Tested by SQA Member (NAME and DATE): Alla Salah 3-30-2013 and 4-4-2013
	 * 
	 ** Modifications by:
	 * Modified By (Name and Date):
	 * Modifications Description:
	 *
	 * Modified By (Name and Date):
	 * Modifications Description:
	 -------------------------------------------------------------------------------------------------*/

	    function isWhiteSpace($index,$line,$lineNumber)
	    {
	        if (ord($line[$index]) == 32 || ord($line[$index]) == 9)//white space= 32, and tab=9
	        {
	            return true;
	        }
	        else 
	        {
	            printf("Error: Expecting a white space or tab after %s on line %d <br>",$line[$index-1],$lineNumber);
	            return false;
	        }
	        
	    }//end function

?>
