<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        
         $roomInfo = array ();
         $lineNumber=1;
          $i=0;
          
         $fileName = 'ROOMS30.txt';
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
            $line = fgets($inputFile);
            if (feof($inputFile))
            {
                //Insert the carriage and new line charater if at the last line in file
                $line= $line . chr(13);
                $line= $line . chr(10);
            }           
            if(strlen($line)!= 2)
            {
                getInfo($line,$lineNumber,$roomInfo,$i);
               $lineNumber= $lineNumber + 1;
            }
          }//end while loop  
        }//end else
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
 * Tested by SQA Member (NAME and DATE): 3-30-2013 and 4-4-2013
 * 
 ** Modifications by:
 * Modified By (Name and Date):
 * Modifications Description:
 *
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
            $gotRoomType=false;
            $gotRoomSize=false;
            $gotRoomName=false;
            $gotRoomNumber=false;
            //printf("Size of line= %d <br>",strlen($line));
           //for($k=0; $k < strlen($line);$k++)
             // printf("k= %d --%s = %d <br>",$k, $line[$k],ord($line[$k]));
            
            //Loop to get room type, size, name, and number
            for ($index=0; $index < strlen($line);$index= $index + 1)
            {
                
               
               if( ord($line[$index]) != 32  &&  ord($line[$index]) != 9)
               {    
                  //printf("Inside loop, line[%d]= %s-->%d<br>",$index,$line[$index],ord($line[$index])); 
                  //Gets room type information
                  if ($gotRoomType == false)
                  {
                     if ( $line[$index]!= 'L' && $line[$index]!= 'C') //'C' = 67 and 'L'=76
                     {
                         //printf("Room type is not L or C, when index= %d<br>",$index);
                        $invalidRoomType = true;
                        break;
                     }
                     else
                     {
                         $roomType = $line[$index];
                         //printf("RoomType = %s, when index= %d<br>",$RoomType,$index);
                         $gotRoomType=true;
                         $result = isWhiteSpace($index+1,$line,$lineNumber);
                            if ($result == false)
                            {
                                $invalidSpace=true;
                                break;
                            }
                     }
                  }
                  //Gets room size information
                  else if ($gotRoomSize == false && $gotRoomType == true)
                  {
                    if(ord($line[$index]) < 48 || ord($line[$index]) > 57 )
                    {
                        $invalidRoomSize= true;
                        break;
                    }
                    else
                    {
                        $roomSize= $roomSize .$line[$index];
                        //printf("ord(RoomSize) = %d, when index= %d <br>",ord($RoomSize),$index);
                        //printf("RoomSize= %s<br>",$roomSize);
                        
                        //Check if the next character in the string line is a digit
                        if ( ord($line[$index+1]) < 48 || ord($line[$index+1]) > 57)//checking next value     
                        {
                            $gotRoomSize=true;
                            $result = isWhiteSpace($index+1,$line,$lineNumber);
                            if ($result == false)
                            {
                                $invalidSpace=true;
                                break;
                            }
                        }

                        if (intval($roomSize)< 1 || intval($roomSize) >200)//may need to change the 49 to 48 if room size can be 002, 001,...,etc
                        {
                            $invalidRoomSize=true;
                           // printf("intval(roomSize)= %d<br>",intval($roomSize));
                            //printf("invalidRoomSize= %d <br>",$invalidRoomSize);
                            break;
                        }                       
                    }
                  }//end else if
                  
                  //Gets room name information
                  else if ($gotRoomName == false && $gotRoomType==true && $gotRoomSize==true)       
                  {
                      if (ord($line[$index]) < 65 || ord($line[$index]) > 90)
                      {
                          //printf("invalid room, index= %d<br>",$index);
                          $invalidRoomName=true;                     
                          break;
                      }
                      else
                      {
                          $roomName= $roomName .$line[$index];
                          
                          if (strlen($roomName)> 6)
                          {
                              $invalidRoomName= true;
                              break;
                          }
                          else if (strlen($roomName)== 6)
                          {
                              if (ord($line[$index+1]) >= 65 && ord($line[$index+1]) <= 90)
                              {
                                  $invalidRoomName=true;
                              }
                              else 
                              {
                                  $gotRoomName= true;
                                  $result = isWhiteSpace($index+1,$line,$lineNumber);
                                  if ($result == false)
                                  {
                                     $invalidSpace=true;
                                     break;
                                  }
                                  
                              }
                              //printf("RoomName=%s, length of= %d<br>",$RoomName,strlen($RoomName));
                          }//end else if
                                    
                       }//end else
                 }//end else if
                 
                 //Gets room number information
                 else if ($gotRoomNumber==false && $gotRoomType==true && $gotRoomSize==true && $gotRoomName==true)
                 {
                     if (ord($line[$index]) < 48 || ord($line[$index])>57)
                     {
                         $invalidRoomNumber=true;
                         break;               
                     }
                     else
                     {
                         $roomNumber= $roomNumber .$line[$index];
                         if (  ord($line[$index+1]) < 48 || ord($line[$index+1]) > 57 )
                         {
                                $gotRoomNumber=true;                                                       
                         }                    
                     }
                 }//end else if
                      
             }//end if( (int)$line[$index] != 32 || (int)$line[$index] != 9)
               
            }//end for loop
            
            
            if($invalidRoomType== false && $invalidRoomSize==false && $invalidRoomName==false && $invalidRoomNumber==false && $invalidSpace==false )
            {
                echo("RoomType= ".$roomType);
                echo(", RoomSize= ".$roomSize);
                echo(", RoomName= ".$roomName );
                echo(", RoomNumber= ".$roomNumber);
                 $CompleteRoomName= $roomName .$roomNumber;
            printf("<br>CompleteRoomName= %s <br>",$CompleteRoomName);
            }
          $completeRoomName= $roomName .$roomNumber;
           
           
            if ($invalidRoomType==true)
            {
                  printf("Error on line %d:Missing room type, or room type must be an upper-case character 'C' or 'L' <br>",$lineNumber);
            }
            else if ($invalidRoomSize==true)
            {
                  printf("Error on line %d:Missing room size, or room size must be greater than 1 and less than or equal to 200<br>",$lineNumber);
            }
            else if ($invalidRoomName==true)
            {
                  printf("Error on line %d: Missing room name or, the room name must be six upper-case characters A-Z in length.<br>",$lineNumber);
            }
            else if ($invalidRoomNumber==true)
            {
                  printf ("Error on line %d: Room number must all be digits and followed by nothing.<br>",$lineNumber);
            }
            echo("__________________________________________________________<br>");
            // Now the valid data is taken and put into an array.
            /*$result = insert ($completeRoomName,$roomArray,$i);
            if ($result == true)
                 $i= $i + 1; */
            //IF result == false, then don't change value of i.
          }//end function    
          
         
/*-----------------------------------------------------------------------------------------------
 ********************** Function Prologue Comment: insert ********************
 * Preconditions: The variable $completeRoomName contains the room name concatenated with the room
 *                room number with no space. The variable $i is the next available index to insert 
 *                into the array.
 * Postconditions: Returns true if insertion into the roomArray is performed. Otherwise, it returns false.                 
 * Function Purpose: Checks to see if the currently identified room name is
 *                   is already in the roomArray. If it is not then it will
 *                   insert it into the array. If it is it will not insert.
 *                   The goal is to identify duplicate rooms in the file.
 * Input Expected: the room name and number called completeRoomName an array with the already identified 
 *                 called roomArray
 * Exceptions/Errors Thrown: None
 * Files Accessed: None
 *
 * Function Pseudocode Author: Alla Salah
 *
 * Function Author: Alla Salah
 *
 * Date of Original Implementation: 3-30-2013
 *
 * Tested by SQA Member (NAME and DATE): 3-30-2013 and 4-4-2013
 * 
 ** Modifications by:
 * Modified By (Name and Date):
 * Modifications Description:
 *
 * Modified By (Name and Date):
 * Modifications Description:
 -------------------------------------------------------------------------------------------------*/

        /* function insert ($completeRoomName,&$roomArray,&$i)
          {
              //Varaible Declarations
              $duplicateFound=false;
              printf("i= %d<br>",$i);
              
              if ($i == 0)
              {
                  //Array is empty. Just insert into it
                  $roomArray[$i]= $completeRoomName;
                  return true;
              }
              else 
              {
                  //Array is first searched to make sure the room is not already in there
                  for($count=0; $count < $i;$count++)
                  {
           
                      
                      if (strcmp($roomArray[$i],$completeRoomName)==0)
                      {
                          //Set flag to true; indictating that the roomName is already in the array
                          $duplicateFound= true;
                      }
                  }//end for
                  
                  if ($duplicateFound==false)
                  {
                      //Then room name identified is not in the array. So just insert it.
                      $roomArray[$i]= $completeRoomName;
                      return true;
                  }
                  //If duplicateFound is true, then don't insert it into the array
                  else
                  {
                      return false;
                  }
              }//end else
              
              //just to check the contents of roomArray
              for($k=0; $k < $i; $k++)
                    printf("k= %d-- %s <br>",$k,$roomArray[$k]);
              
          }//end function   */
         
        /*------------------------------------------------------------
         Purpose: This function check the index in the buffer line to see
                  if it a white space or tab.
         Input: buffer with input data called $line
                the line number used for logging errors
                
         Return: true if line[index] is a blank or tab. Otherwise returns
                 false.
         --------------------------------------------------------------*/
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
    </body>
</html>
